<?php
$start = get_tm();
$mailbox = isset($_REQUEST["mailbox"]) ? $_REQUEST["mailbox"] : "INBOX";
$mboxspec = $server . $mailbox;
$mbox = @imap_open($mboxspec, $user, $password);
$err = imap_last_error();

if ($err)
{
	print "[";
	print "{ type: 'error', 'txt': 'Cannot connect to IMAP server: $err' }\n";
	print "];";
	die();
};

$act = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";

// see on siis see asi, mis kirjade flagidega tegeleb. aga hmm .. mis sorti argumente ma sinna kaasa
// saan panna?
if ($act == "move")
{
	// this is too fucking easy
	$seq = join(",",array_keys($_POST["msgmark"]));
	$res = imap_mail_move($mbox,$seq,$_POST["moveto"],CP_UID);
	imap_expunge($mbox);
	print "[\n";
	print describe_folder($mbox,$server,$_POST["mailbox"]);
	print describe_folder($mbox,$server,$_POST["moveto"]);
	print "];";
	/*
	print $seq;
	print $_POST["moveto"];
	print $res;
	*/
}
else
if ($act == "flags")
{
	if (isset($_POST["msgmark"]))
	{
		$seq = join(",",array_keys($_POST["msgmark"]));
	}
	$flag = $_POST["flag"];
	if ("read" == $flag)
	{
		imap_setflag_full($mbox,$seq,"\\Seen",ST_UID);
	}
	if ("unread" == $flag)
	{
		imap_clearflag_full($mbox,$seq,"\\Seen",ST_UID);
	}

	print "[\n";
	
	if (isset($_REQUEST["msgids"]))
	{
		$ovr = imap_fetch_overview($mbox,$_REQUEST["msgids"],FT_UID);
		foreach($ovr as $mkey => $msgobj)
		{
			printf("{'type': 'mflag', 'msgid': '%d', 'seen': %d, 'answered': %d},\n",
				$msgobj->uid,$msgobj->seen,$msgobj->answered);
		}	
	}
	
	$mboxinf = imap_status($mbox, $mboxspec, SA_ALL);

	$msg_total = $mboxinf->messages;
	$msg_unread = $mboxinf->unseen;
	printf("{'type': 'mbox', 'caption': '%s', 'name': '%s', 'total': %d, 'unread': %d},\n",get_folder_caption($mailbox),$mailbox,$msg_total,$msg_unread);

	print "];";

	// edasi .. see asi peab tagastama packeti iga mailboxis olnud kirja kohta, sest mõni neist võib olla
	// muutunud loetuks mõnel muul moel .. s.t. siis query_server peab tagastama kõik kirjad,
	// aga lugema struktuuri ainult nende kohta, mis on muutunud
}
else if ($act == "delete")
{
	if (isset($_POST["msgmark"]))
	{
		foreach($_POST["msgmark"] as $pkey => $val)
		{
			imap_delete($mbox,$pkey,FT_UID);
			//print "Deleting $pkey from $server\n";
		}
	}
	imap_expunge($mbox);

	// now I want to get read/unread marking ..
}
elseif ($act == "folders")
{
	$folders = imap_list($mbox,$server,"*");
	print "[\n";
	sort($folders);
	// rsk kyll, aga inbox peaks ju ikka ja alati olema esimene ja mitte teisiti
	foreach($folders as $folder)
	{
		$realname = substr($folder,strlen($server));
		$caption = get_folder_caption($realname);
		$status = imap_status($mbox,$folder,SA_ALL);
		// dunno, sometimes that shit is in the folder name
		$caption = str_replace(chr(0),"",$caption);
		printf("{'type': 'mbox', 'caption': '%s', 'name': '%s', 'total': %d, 'unread': %d},\n",
			$caption,$realname,$status->messages,$status->unseen);
	}
	print "];";
}
else
{
	

	$msgpage = isset($_POST["msgpage"]) ? $_POST["msgpage"] : 0;
	$onpage = 50;

	$fo = imap_sort($mbox,SORTARRIVAL,1,SE_UID && SE_NOPREFETCH);

	$_from = $msgpage * $onpage;

	$interesting = array_slice($fo,$_from,$onpage);

	$msgids = array();
	print "[\n";

	$flagsonly = array();


	// asterisk is used as a separator for msgids
	//$_POST["msgids"] = "333*334*335*29330*29316";
	//$_POST["msgids"] = "333*334*335*29330";
	//$_POST["msgids"] = "333*334*335*";
	//$_POST["msgids"] = "333*334*335*29469";

	if (!empty($_REQUEST["msgids"]))
	{
		$msgstr = $_REQUEST["msgids"];
		// cut the last char if it's a separator, this simplifies things later.
		// why is it at the end anyway? because that makes client side code easier.
		if ("," == substr($msgstr,-1)) $msgstr = substr($msgstr,0,-1);
		$msgids = explode(",",$msgstr);


		// issue delete orders for everything in the msgids array, but not in the 
		// interesting array

		$delete_from_client = array_diff($msgids,$interesting);	
		foreach($delete_from_client as $msg_key => $ft_uid)
		{
			if ($ft_uid) {
				printf("{'type': 'msgdel', 'msgid': %d},\n",$ft_uid);
				unset($msgids[$msg_key]);
			};
		}
	}

	// now I have 2 arrays, one with msgids from the client, the other with latest message ft_uids
	// get a list of uids that are not on the client, so only the minimal amount of processing 
	// has to be done.
	$new_messages = array_diff($interesting,$msgids);

	$mkeys = array();
	if (sizeof($msgids) > 0)
	{
		$mkeys = array_values($msgids);
	};

	$first_on_client = $last_on_client = false;
	
	$x1 = $x2 = false;

	if (sizeof($mkeys) > 0)
	{
		$first_on_client = $mkeys[0];
		$last_on_client = $mkeys[sizeof($mkeys)-1];

		$x1 = array_search($first_on_client,$interesting);
		$x2 = array_search($last_on_client,$interesting)+1;
	}

	$to_insert = array_reverse(array_slice($interesting,0,$x1));
	$to_append = array_slice($interesting,$x2);

	// now, for each message I need to know whether it should be inserted or appended
	// it seems to me (no hard data though) that doing index checks on arrays is faster
	// than doing a search in values
	$flags_insert = array_flip($to_insert);
	$flags_append = array_flip($to_append);

	$message_orders = array();

	$send_to_client = array_merge($to_insert,$to_append);
	//$uids = join(",",$send_to_client);
	$uids = join(",",$interesting);

	// for some messages we only send flags
	$flagsonly = array_diff($interesting,$send_to_client);

	// $interesting has things in the correct order
	$ovr = imap_fetch_overview($mbox,$uids,FT_UID);

	$today = date("Ymd");

	// create JSON structure
	foreach($ovr as $mkey => $msgobj)
	{
		// by default addime, see juhtub näiteks esimesel vaatamisel
		if (in_array($msgobj->uid,$flagsonly))
		{
			printf("{'type': 'mflag', 'msgid': '%d', 'seen': %d, 'answered': %d},\n",
				$msgobj->uid,$msgobj->seen,$msgobj->answered);
			//print "brrr";
			continue;
		};
		$append = 1;
		if (isset($flags_insert[$msgobj->uid])) $append = 0;
		$mx = imap_fetchstructure($mbox,$msgobj->uid,FT_UID);

		$tstamp = strtotime($msgobj->date);
		$dstamp = date("Ymd",$tstamp);

		if ($dstamp == $today)
		{
			$mask = "H:i";
		}
		else
		{
			$mask = "d-M";
		}
		$msgdate = date($mask,$tstamp);

		$dt = date("Ymd",$tstamp);

		$addr = _extract_address($msgobj->from);

		$from = $addr["name"];
	
		$message_orders[$msgobj->uid] = sprintf("{from: '%s', subject: '%s', id: '%d', date: '%s', seen: %d, answered: %d, append: %d, attach: %d, size: '%s' },\n",
			qp_decode($from),qp_decode($msgobj->subject),$msgobj->uid,$msgdate,$msgobj->seen,$msgobj->answered,$append,$mx->type == 1 ? 1 : 0,$msgobj->size);
		
	}

	if (sizeof($flagsonly) > 0)
	{

	}
	foreach($send_to_client as $key => $uid)
	{
		print $message_orders[$uid];
	}
	/***
	// vata ega ma ei taha küll seda asja iga kord kaasa panna. See peaks tulema ikka ainult siis, kui vaja
	// on ja edaspidi kolima eraldi threadi sisse
	**/

	$mboxinf = imap_status($mbox, $mboxspec, SA_ALL);

	$msg_total = $mboxinf->messages;
	$msg_unread = $mboxinf->unseen;
	printf("{'type': 'mbox', 'caption': '%s', 'name': '%s', 'total': %d, 'unread': %d},\n",get_folder_caption($mailbox),$mailbox,$msg_total,$msg_unread);

	// I might still want to do this to get faster response time you know
	/*
	$folders = imap_list($mbox,$server,"*");
	foreach($folders as $folder)
	{
		$realname = substr($folder,strlen($server));
		$realname = imap_utf7_decode($realname);
		// dunno, sometimes that shit is in the folder name
		$realname = str_replace(chr(0),"",$realname);
		if ($realname == $mailbox)
			printf("{'type': 'mbox', 'name': '%s', 'total': %d, 'unread': %d},\n",$realname,$msg_total,$msg_unread);
		else
			printf("{'type': 'mbox', 'name': '%s', },\n",$realname);
	}
	*/
	print "];";
}

exit;

$end = get_tm();
print "aega kulus : " . ($end-$start) . "<br>";

exit;

print "<pre>";
//print_r($mbox);
var_export($mboxinf);
var_export($fo);
#var_export($mboxinf2);
print_r($err);
print "</pre>";

function get_tm()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function qp_decode($str)
{
	$elements = imap_mime_header_decode($str);
	$rv = "";
	for($i=0; $i<count($elements); $i++)
	{
		$rv .= ' ' . $elements[$i]->text;
	};
	return str_replace("'","\'",$rv);
}

function _extract_address($arg)
{
	$rv = array("name" => $arg,"addr" => "");
	if (preg_match("/(.*)<(.*)>/",$arg,$m))
	{
		$rv["name"] = $m[1];
		$rv["addr"] = $m[2];
	};
	return $rv;
}

function get_folder_caption($realname)
{
	$spaces = 0;
	$last = $realname;
	if (strpos($realname,".") !== false)
	{
		$last = substr($realname,strrpos($realname,".")+1);
		$spaces = substr_count($realname,".");
	}
	return str_repeat("&nbsp;&nbsp;",$spaces) . imap_utf7_decode($last);
}

function describe_folder($mbox,$server,$folder) 
{
	$mboxinf = imap_status($mbox, $server . $folder, SA_ALL);

	$msg_total = $mboxinf->messages;
	$msg_unread = $mboxinf->unseen;
	return sprintf("{'type': 'mbox', 'caption': '%s', 'name': '%s', 'total': %d, 'unread': %d},\n",
				get_folder_caption($folder),$folder,$msg_total,$msg_unread);
}
// imap_status:
/*
class stdClass {
  var $flags = 31;
  var $messages = 1192;
  var $recent = 0;
  var $unseen = 172;
  var $uidnext = 29506;
  var $uidvalidity = 1058122526;
}
*/

// imap_mailboxmsginfo: (slow!)
/*
class stdClass {
  var $Unread = 172;
  var $Deleted = 0;
  var $Nmsgs = 1192;
  var $Size = 16770085;
  var $Date = 'Sat, 22 Oct 2005 12:21:44 +0300 (EEST)';
  var $Driver = 'imap';
  var $Mailbox = '{mail.struktuur.ee:8143/imap/user="duke"}INBOX';
  var $Recent = 0;
}
*/

// imap_fetch_overview:
/*
class stdClass {
    var $subject = '3 Vead moderator request(s) waiting';
    var $from = 'vead-bounces@lists.struktuur.ee';
    var $to = 'vead-owner@lists.struktuur.ee';
    var $date = 'Sat, 22 Oct 2005 08:00:02 +0300';
    var $message_id = '';
    var $size = 2129;
    var $uid = 29501;
    var $msgno = 1188;
    var $recent = 0;
    var $flagged = 0;
    var $answered = 0;
    var $deleted = 0;
    var $seen = 0;
    var $draft = 0;
}
*/

// dammit .. do I really-really have to use fetch_structure on every single message just to figure out
// whether the bloody message has attachments? I guess so :(

// okey .. now .. the next thing I need to implement is to let the mailbox now what it has in it. 
// so that it can delete old shit and replace it with new one .. this is needed if I want to get this
// think working fluently .. of course, right now it works as well, but I really need to get this working
// properly. I mean .. I will be able to select things and if the msgbox is refreshed while I'm doing that
// then all selections will be lost. And this is never a good thing. But then again, it can cause a real
// confusion to the user, if things move around. but hey, what do you expect if you deal with 2 mailboxes
// at once.

// ja kuidas korrektseid linke teha? noh, ma lihtsalt serveerin mailboxi skripti läbi AW .. and that 
// will take care of adding the correct base url. Nothing easier really.

/*
var myJSONObject = {"bindings": [
        {"ircEvent": "PRIVMSG", "method": "newURI", "regex": "^http://.*"},
        {"ircEvent": "PRIVMSG", "method": "deleteURI", "regex": "^delete.*"},
        {"ircEvent": "PRIVMSG", "method": "randomURI", "regex": "^random.*"}
    ]
};
*/





?>
