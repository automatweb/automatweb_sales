<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_remote_makse.aw,v 1.9 2007/11/27 12:30:57 dragut Exp $
// expp_remote_makse.aw - expp remote makse 
/*

@classinfo syslog_type=ST_EXPP_REMOTE_MAKSE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_remote_makse extends class_base {
	var $cy;
	var $cp;

var $lingid = array(
	'hansapank' => array(
		'url'		=> "javascript:oppwin('/tellimine/makse/okhansa');",
		'url2'	=> "javascript:oppwin('/tellimine/makse/okhansa');",
		'pank'	=> '767',
		'text'	=> "LC_EXPP_HANSAPANK",
		'okurl'	=> "https://www.hanza.net/cgi-bin/hanzanet",
	),
	'yhispank'	=> array(
		'url'		=> "javascript:oppwin('/tellimine/makse/okseb');",
		'url2'	=> "javascript:oppwin('/tellimine/makse/okseb');",
		'pank'	=> '401',
		'text'	=> "LC_EXPP_YHISPANK",
		'okurl'	=> "http://www.seb.ee/static/https_www.seb.ee/index.html",
	),
	'sampopank'	=> array(
		'url'		=> "javascript:oppwin('/tellimine/makse/ok/sampopank');",
		'url2'	=> "javascript:oppwin('/tellimine/makse/oksampo');",
		'pank'	=> '720',
		'text'	=> "LC_EXPP_SAMPOPANK",
		'okurl'	=> "https://www.sampo.ee/cgi-bin/login?lang=est",
	),
	'krediidipank'	=> array(
		'url'		=> "javascript:oppwin('/tellimine/makse/ok/krediidipank');",
		'url2'	=> "javascript:oppwin('/tellimine/makse/okkrediidi');",
		'pank'	=> '742',
		'text'	=> "LC_EXPP_KREDIIDIPANK",
		'okurl'	=> "https://i-pank.krediidipank.ee/",
	),
	'nordeapank'	=> array(
		'url'		=> "javascript:oppwin('/tellimine/makse/ok/nordeapank');",
		'url2'	=> "javascript:oppwin('/tellimine/makse/oknordea');",
		'pank'	=> '780',
		'text'	=> "LC_EXPP_NORDEAPANK",
		'okurl'	=> "https://www.arved.ee/epay/arch_login.jsp?PARTNER=MERP&GROUP=EIS&SERVICE=EIS",
	),
	'postiga'	=> array(
		'url'		=> "javascript:oppwin('/tellimine/makse/ok/post');",
		'url2'	=> "/tellimine/makse/tanudok/",
		'text'	=> "LC_EXPP_POSTIGA",
	),
);
var $pangad = array(
	""					=> "--- Vali makse meetod ---",
	"hanza.net"		=> "tasun hanza.net-is - Hansapanga internetipank",
	"U-net"			=> "tasun U-net-is - SEB Eesti &Uuml;hispanga internetipank",
	"samponet"		=> "tasun S&#64;mpo Internetipangas",
	"nordeapank"	=> "tasun Solo Internetis - Nordea Internetipangas",
	"krediidipank"	=> "tasun Krediidipanga i-pangas",
	"kontor"			=> "m&auml;rgin &uuml;les arve andmed ja tasun nende alusel pangakontoris",
	"kodu"			=> "tellin arve postiga ja tasun selle alusel",
);
	var $burl = "";

	function expp_remote_makse()
	{
		$this->init(array(
			"tpldir" => "expp/expp_remote_makse",
			"clid" => CL_EXPP_REMOTE_MAKSE
		));
		$this->lang = aw_global_get("admin_lang_lc");
		$this->cy = get_instance( CL_EXPP_JAH );
		$this->cp = get_instance( CL_EXPP_PARSE );
		lc_site_load( "expp", $this );
		$this->burl = str_replace( "http://", "https://", aw_ini_get("baseurl"));
	}

	function show($arr) {
		global $lc_expp;
		$retHTML		= '';
		$_okleping	= '';
		$_teleping	= '';
		if( empty( $this->cp->pids )) return $retHTML;

		if( isset( $GLOBALS['HTTP_POST_VARS']['edasi'] ) || isset( $GLOBALS['HTTP_POST_VARS']['edasi_y'] )) {
			$retHTML = &$this->parsePost();
			if( !empty( $retHTML )) {
				return;
			}
		};
		$_pid = $this->cp->getPid( 2 );
		switch( $_pid ) {
			case "seb":
				$retHTML = &$this->maksaSEB();
				break;
			case "hansapank":
				$retHTML = &$this->maksaHansapank();
				break;
			case "okhansa":
				$retHTML = &$this->okHansapank();
				break;
			case "sampopank":
				$retHTML = &$this->maksaSampo();
				break;
			case "nordeapank":
				$retHTML = &$this->maksaNordea();
				break;
			case "krediidipank":
				$retHTML = &$this->maksaKrediidipank();
				break;
			case "tanudok":
				$sql = "UPDATE expp_arved SET trykiokpakkumine='1' WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='ok'";
				$this->db_query( $sql );
			case "tanudarve":
				$retHTML = &$this->maksaArve();
				break;
			case "tanud":
				$retHTML = &$this->maksaTanud();
				break;
			case "okseb":
				$retHTML = &$this->okPank( 'yhispank' );
				break;
			case "oksampo":
				$retHTML = &$this->okPank( 'sampopank' );
				break;
			case "oknordea":
				$retHTML = &$this->okPank( 'nordeapank' );
				break;
			case "okkrediidi":
				$retHTML = &$this->okPank( 'krediidipank' );
				break;
			case "ok":
				$retHTML = &$this->maksaOK();
				break;
		}
		if( !empty( $retHTML )) {
			return $retHTML;
		}

		$this->cp->log( get_class($this), "show" );

		$this->read_template("expp_maksevalik.tpl");

		$sql = "SELECT a.arvenr, a.viitenumber, a.maksumus, a.algus, a.lisarida, t.toote_nimetus, t.valjaande_nimetus"
			." FROM expp_arved a LEFT JOIN expp_valjaanne t ON a.vaindeks=t.pindeks"
			." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='ok'";
		$this->db_query( $sql );
		$_ok_count = $this->num_rows();
		if( $_ok_count > 0 ) {
			$_okline = '';
			$_oklink	= '';
			$_hansacase = '';
			while( $row = $this->db_next()) {
				$this->vars(array(
					'OKLEPINGUNR'	=> $row["arvenr"],
					'OKVIITENR'		=> $row["viitenumber"],
					'TOODE'			=> stripslashes($row["valjaande_nimetus"]),
					'LEPING'			=> ($row["algus"]==date("d.m.Y")?"<b>".$row["lisarida"]."</b>":t("alates")." <b>".$row["algus"]."</b>"),
					'HIND'			=> $row["maksumus"],
					'INPUT'			=> ($_ok_count > 1 ? 'radio' : 'hidden' ),
				));
				$_okline .= $this->parse( 'OKLINE' );
			}	//	while
			if( $_ok_count > 1 ) {
				$_hansacase = $this->parse( 'HANSACASE' );
			}
			foreach( $this->lingid as $key => $val ) {
				$this->vars(array(
					'url'		=> $val['url2'],
//					'target'
					'text'	=> $lc_expp[$val['text']],
				));
				$_oklink .= $this->parse( 'OKLINK' );
			}
			$this->vars(array('options' => ($this->lang != 'et') ? $lc_expp['LC_EXPP_PANGAD'] : $this->pangad,
				'HANSACASE'	=> $_hansacase,
				'OKLINE'		=> $_okline,
				'OKLINK'		=> $_oklink,
			));
			$_okleping .= $this->parse( 'OTSEKORRALDUS' );
		}
		$sql = "SELECT a.arvenr, a.viitenumber, a.maksumus, a.algus, a.lopp, a.lisarida, a.vaindeks, t.toote_nimetus, t.valjaande_nimetus"
			." FROM expp_arved a LEFT JOIN expp_valjaanne t ON a.vaindeks=t.pindeks"
			." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel'";
		$this->db_query( $sql );
		$_tel_count = $this->num_rows();
		if( $_tel_count > 0 ) {
			$_summa = 0;
			$_teline = '';
			$_tellep = '';
			$_old_arve = '';
			$_old_viide = '';
			while( $row = $this->db_next()) {
				if( $_old_arve != $row['arvenr'] ) {
					if( !empty( $_teline )) {
						$this->vars(array(
							'TELLINE' => $_teline,
							'SUMMA'	 => $_summa,
						));
						$_tellep .= $this->parse( 'TELLEP' );
					}
					$this->vars(array(
						'LEPINGUNR'	=> $row['arvenr'],
						'VIITENR'	=> $row["viitenumber"],
					));
					$_old_arve	= $row['arvenr'];
					$_teline 	= '';
					$_summa		= 0;
				}
				$_summa	+= $row['maksumus'];
				$this->vars(array(
				//	'TOODE'	=> stripslashes($row["valjaande_nimetus"]),
					'TOODE'	=> $_SESSION['expp_remote_valjaanded'][$row['vaindeks']],
					'LEPING'	=> ( $row["algus"]==date("d.m.Y")?$row["lisarida"]:"<b>".$row["algus"]."</b> - <b>".$row["lopp"]."</b>"),
					'HIND'	=> $row["maksumus"],
				));
				$_teline .= $this->parse( 'TELLINE' );
			}
			if( !empty( $_teline )) {
				$this->vars(array(
					'TELLINE' => $_teline,
					'SUMMA'	 => $_summa,
				));
				$_tellep .= $this->parse( 'TELLEP' );
			}
			$_pangad = html::select(array(
						'name' => 'maksan',
					//	'options' => $this->pangad,
						'options' => ($this->lang != 'et') ? $lc_expp['LC_EXPP_PANGAD'] : $this->pangad,
						'selected' => '',
						'class' => 'formElement',
					));
			$this->vars(array(
				'TELLEP' => $_tellep,
				'makse_meetod' =>	$_pangad,
			));
			$_teleping = $this->parse( 'TAVALEPING' );
		}
		if( $_tel_count < 1 && $_ok_count < 1 ) {
			return $retHTML;
		}

		$_aid = $this->cp->getPid( 2 );
		$myURL = $this->cp->addYah( array(
				'link' => 'remotemakse',
				'text' => $lc_expp['LC_EXPP_TITLE_MAKSMINE'],
			));

//		$this->read_template("expp_maksevalik.tpl");
		$this->vars(array(
			'ACTION'			=> $myURL,
			'OTSEKORRALDUS'=> $_okleping,
			'TAVALEPING'	=> $_teleping,
		));
		$retHTML .= $this->parse();
		return $retHTML;
	}

	function parsePost() {

		$_maksan = $GLOBALS['HTTP_POST_VARS']['maksan'];
		$sql1 = '';
		$pid = '';
		switch( $_maksan ) {
			case "U-net":
				$pid		= "seb";
				$sql1		= ",pank='401'";
				break;
			case "hanza.net":
				$pid		= "hansapank";
				$sql1		= ",pank='767'";
				break;
			case "samponet":
				$pid		= "sampopank";
				$sql1		= ",pank='720'";
				break;
			case "nordeapank":
				$pid		= "nordeapank";
				$sql1		= ",pank='801'";
				break;
			case "krediidipank":
				$pid		= "krediidipank";
				$sql1		= ",pank='742'";
				break;
			case "kodu":
				$trykiarve	= "checked";
			case "kontor":
				$pid		= "tanudarve";
				break;
			default:
				$pid		= "tanud";
		}
//		$this->cp->log( get_class($this), "go-".$pid );

		$sql = "UPDATE expp_arved SET trykiarve='".(isset($trykiarve)and($trykiarve=="checked")?"1":"0")."' {$sql1}"
				." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel'";
		$this->db_query( $sql );
		$_aid = $this->cp->getPid( 1 );

		file_get_contents("http://www.raamat24.ee/kasutaja.php?tellimus=".$tellimus."&arve=".$_SESSION['arvenr']);

		header( "Location: ".aw_ini_get("baseurl").'/tellimine/'.urlencode( $_aid ).'/'.urlencode( $pid ).'/' );
		exit;
	}

	function maksaHansapank() {

		global $lc_expp;
		$sql = "SELECT arvenr, viitenumber, SUM( maksumus ) AS summa FROM expp_arved WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel' GROUP BY arvenr ORDER BY arvenr DESC LIMIT 0,1";
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() == 0 ) return '';

		$this->cp->log( get_class($this), "formHansa", $row["arvenr"], $row["viitenumber"] );

		$_action = $this->cp->addYah( array(
				'link' => 'hansapank',
				'text' => 'Hansapank',
			));

		$VK_SERVICE = "1002";
		$VK_VERSION	= "008";
		$VK_SND_ID	= "EXPRPOST";
		$VK_STAMP	= $row["arvenr"];
		$VK_AMOUNT	= $row["summa"];
		$VK_CURR		= "EEK";
		$VK_REF		= $row["viitenumber"];
		$VK_MSG		= "Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
		$VK_message = sprintf("%03d",strlen($VK_SERVICE)).$VK_SERVICE;
		$VK_message.= sprintf("%03d",strlen($VK_VERSION)).$VK_VERSION;
		$VK_message.= sprintf("%03d",strlen($VK_SND_ID)).$VK_SND_ID;
		$VK_message.= sprintf("%03d",strlen($VK_STAMP)).$VK_STAMP;
		$VK_message.= sprintf("%03d",strlen($VK_AMOUNT)).$VK_AMOUNT;
		$VK_message.= sprintf("%03d",strlen($VK_CURR)).$VK_CURR;
		$VK_message.= sprintf("%03d",strlen($VK_REF)).$VK_REF;
		$VK_message.= sprintf("%03d",strlen($VK_MSG)).$VK_MSG;
		$VK_signature = "";
		$fp = fopen( $this->cfg["site_basedir"]."/pank/expp.key.key", "r");
		$priv_key = fread($fp, 2048);
		fclose($fp);
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign( $VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);
		$VK_RETURN	= $this->burl."/tellimine/makse/tanud/";	//	60	URL, kuhu vastatakse edukal tehingu sooritamisel
		$VK_CANCEL	= $this->burl."/tellimine/makse/";	//	60	URL, kuhu vastatakse ebaõnnestunud tehingu puhul
		$VK_LANG = "EST";

		$this->read_template("expp_pank.tpl");

		$this->vars(array(
			"VK_SERVICE"	=> $VK_SERVICE,
			"VK_VERSION"	=> $VK_VERSION,
			"VK_SND_ID"		=> $VK_SND_ID,
			"VK_STAMP"		=> $VK_STAMP,
			"VK_AMOUNT"		=> $VK_AMOUNT,
			"VK_CURR"		=> $VK_CURR,
			"VK_REF"			=> $VK_REF,
			"VK_MSG"			=> $VK_MSG,
			"VK_MAC" 		=> $VK_MAC,
			"VK_RETURN"		=> $VK_RETURN,
			"VK_CANCEL"		=> $VK_CANCEL,
			"VK_LANG" 		=> $VK_LANG,
			'image'	=> "/img/hansapank.gif",
			'alt'		=> $lc_expp['LC_EXPP_ALT_HP'],
			'pank'	=> $lc_expp['LC_EXPP_PANK_HP'],
			'link'	=> "https://www.hanza.net/cgi-bin/hanza/pangalink.jsp",
		));
		return $this->parse();
	}

	function maksaSEB() {
		global $lc_expp;
		$sql = "SELECT arvenr, viitenumber, SUM( maksumus ) AS summa FROM expp_arved WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel' GROUP BY arvenr ORDER BY arvenr DESC LIMIT 0,1";
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() == 0 ) return '';

		$this->cp->log( get_class($this), "formSEB", $row["arvenr"], $row["viitenumber"] );

		$_action = $this->cp->addYah( array(
				'link' => 'seb',
				'text' => 'SEB &Uuml;hispank',
			));

		$VK_SERVICE = "1002";
		$VK_VERSION	= "008";
		$VK_SND_ID	= "expost"; 					//	15	Päringu koostaja ID (Kaupluse ID)
		$VK_STAMP	= $row["arvenr"];
		$VK_AMOUNT	= $row["summa"];
		$VK_CURR		= "EEK";
		$VK_REF		= $row["viitenumber"];
		$VK_MSG		= "Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
		$VK_message = sprintf("%03d",strlen($VK_SERVICE)).$VK_SERVICE;
		$VK_message.= sprintf("%03d",strlen($VK_VERSION)).$VK_VERSION;
		$VK_message.= sprintf("%03d",strlen($VK_SND_ID)).$VK_SND_ID;
		$VK_message.= sprintf("%03d",strlen($VK_STAMP)).$VK_STAMP;
		$VK_message.= sprintf("%03d",strlen($VK_AMOUNT)).$VK_AMOUNT;
		$VK_message.= sprintf("%03d",strlen($VK_CURR)).$VK_CURR;
		$VK_message.= sprintf("%03d",strlen($VK_REF)).$VK_REF;
		$VK_message.= sprintf("%03d",strlen($VK_MSG)).$VK_MSG;
		$VK_signature = "";
		$fp = fopen( $this->cfg["site_basedir"]."/pank/expp.key.key", "r");
		$priv_key = fread($fp, 2048);
		fclose($fp);
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign( $VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);
		$VK_RETURN	= $this->burl."/tellimine/makse/tanud/";	//	60	URL, kuhu vastatakse edukal tehingu sooritamisel
		$VK_CANCEL	= $this->burl."/tellimine/makse/";	//	60	URL, kuhu vastatakse ebaõnnestunud tehingu puhul
		$VK_LANG = "EST";

		$this->read_template("expp_pank.tpl");

		$this->vars(array(
			"VK_SERVICE"	=> $VK_SERVICE,
			"VK_VERSION"	=> $VK_VERSION,
			"VK_SND_ID"		=> $VK_SND_ID,
			"VK_STAMP"		=> $VK_STAMP,
			"VK_AMOUNT"		=> $VK_AMOUNT,
			"VK_CURR"		=> $VK_CURR,
			"VK_REF"			=> $VK_REF,
			"VK_MSG"			=> $VK_MSG,
			"VK_MAC" 		=> $VK_MAC,
			"VK_RETURN"		=> $VK_RETURN,
			"VK_CANCEL"		=> $VK_CANCEL,
			"VK_LANG" 		=> $VK_LANG,
			'image'	=> "/img/logo_seb.gif",
			'alt'		=> $lc_expp['LC_EXPP_ALT_SEB'],
			'pank'	=> $lc_expp['LC_EXPP_PANK_SEB'],
//			'link'	=> "https://unet.eyp.ee/cgi-bin/unet3.sh/un3min.r",
			'link'	=> "https://www.seb.ee/cgi-bin/unet3.sh/un3min.r",
		));
		return $this->parse();
	}

	function maksaSampo() {
		global $lc_expp;
		$sql = "SELECT arvenr, viitenumber, SUM( maksumus ) AS summa FROM expp_arved WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel' GROUP BY arvenr ORDER BY arvenr DESC LIMIT 0,1";
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() == 0 ) return '';

		$this->cp->log( get_class($this), "formSampo", $row["arvenr"], $row["viitenumber"] );

		$_action = $this->cp->addYah( array(
				'link' => 'sampo',
				'text' => 'Sampopank',
			));

		$VK_SERVICE = "1002";
		$VK_VERSION	= "008";
		$VK_SND_ID	= "EXPRPOST";
		$VK_STAMP	= $row["arvenr"];
		$VK_AMOUNT	= $row["summa"];
		$VK_CURR		= "EEK";
		$VK_REF		= $row["viitenumber"];
		$VK_MSG		= "Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
		$VK_message = sprintf("%03d",strlen($VK_SERVICE)).$VK_SERVICE;
		$VK_message.= sprintf("%03d",strlen($VK_VERSION)).$VK_VERSION;
		$VK_message.= sprintf("%03d",strlen($VK_SND_ID)).$VK_SND_ID;
		$VK_message.= sprintf("%03d",strlen($VK_STAMP)).$VK_STAMP;
		$VK_message.= sprintf("%03d",strlen($VK_AMOUNT)).$VK_AMOUNT;
		$VK_message.= sprintf("%03d",strlen($VK_CURR)).$VK_CURR;
		$VK_message.= sprintf("%03d",strlen($VK_REF)).$VK_REF;
		$VK_message.= sprintf("%03d",strlen($VK_MSG)).$VK_MSG;
		$VK_signature = "";
		$fp = fopen( $this->cfg["site_basedir"]."/pank/expp.key.key", "r");
		$priv_key = fread($fp, 2048);
		fclose($fp);
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign( $VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);
		$VK_RETURN	= $this->burl."/tellimine/makse/tanud/";	//	60	URL, kuhu vastatakse edukal tehingu sooritamisel
		$VK_CANCEL	= $this->burl."/tellimine/makse/";	//	60	URL, kuhu vastatakse ebaõnnestunud tehingu puhul
		$VK_LANG = "EST";

		$this->read_template("expp_pank.tpl");

		$this->vars(array(
			"VK_SERVICE"	=> $VK_SERVICE,
			"VK_VERSION"	=> $VK_VERSION,
			"VK_SND_ID"		=> $VK_SND_ID,
			"VK_STAMP"		=> $VK_STAMP,
			"VK_AMOUNT"		=> $VK_AMOUNT,
			"VK_CURR"		=> $VK_CURR,
			"VK_REF"			=> $VK_REF,
			"VK_MSG"			=> $VK_MSG,
			"VK_MAC" 		=> $VK_MAC,
			"VK_RETURN"		=> $VK_RETURN,
			"VK_CANCEL"		=> $VK_CANCEL,
			"VK_LANG" 		=> $VK_LANG,
			'image'	=> "/img/logo_sampo.gif",
			'alt'		=> $lc_expp['LC_EXPP_ALT_SAMPO'],
			'pank'	=> $lc_expp['LC_EXPP_PANK_SAMPO'],
			'link'	=> "https://www2.sampo.ee/ibank/pizza/pizza",
		));
		return $this->parse();
	}

	function maksaNordea() {
		global $lc_expp;
		$sql = "SELECT arvenr, viitenumber, SUM( maksumus ) AS summa FROM expp_arved WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel' GROUP BY arvenr ORDER BY arvenr DESC LIMIT 0,1";
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() == 0 ) return '';

		$this->cp->log( get_class($this), "formNordea", $row["arvenr"], $row["viitenumber"] );

		$_action = $this->cp->addYah( array(
				'link' => 'nordea',
				'text' => 'Nordea pank',
			));

		$SOLOPMT_VERSION  	= "0002";            // 1.    Payment Version   SOLOPMT_VERSION   "0002"   AN 4  M
		$SOLOPMT_STAMP    	= $row["arvenr"];    // 2.    Payment Specifier    SOLOPMT_STAMP  Code specifying the payment   N 20  M 
		$SOLOPMT_RCV_ID    	= "10354213";          // 3.    Service Provider ID  SOLOPMT_RCV_ID    Customer ID (in Nordea's register)  AN 15    M 
		$SOLOPMT_RCV_ACCOUNT	= "";               // 4.    Service Provider's Account    SOLOPMT_RCV_ACCOUNT  Other than the default account   AN 15    O
		$SOLOPMT_RCV_NAME 	= "";              // 5.    Service Provider's Name    SOLOPMT-RCV_NAME  Other than the default name   AN 30    O 
		$SOLOPMT_LANGUAGE 	= "4";               // 6.    Payment Language  SOLOPMT_LANGUAGE  1 = Finnish 2 = Swedish 3 = English    N 1   O 
		$SOLOPMT_AMOUNT		= $row["summa"];     // 7.    Payment Amount    SOLOPMT_AMOUNT    E.g. 990.00    AN 19    M 
		$SOLOPMT_REF			= $row["viitenumber"];  // 8.    Payment Reference Number   SOLOPMT_REF    Standard reference number  AN 20    M 
		$SOLOPMT_DATE			= 'EXPRESS';         // 9.    Payment Due Date  SOLOPMT_DATE   "EXPRESS" or "DD.MM.YYYY"  AN 10    M 
		$SOLOPMT_MSG			= "Ajakirjade tellimus. Arve nr. ".$row["arvenr"];  // 10.   Payment Message   SOLOPMT_MSG    Service user's message  AN 234   O 
		$SOLOPMT_RETURN		= $this->burl."/tellimine/makse/tanud/"; // 11.   Return Address    SOLOPMT_RETURN    Return address following payment    AN 60    M 
		$SOLOPMT_CANCEL		= $this->burl."/tellimine/makse/"; // 12.   Cancel Address    SOLOPMT_CANCEL    Return address if payment is cancelled    AN 60    M 
		$SOLOPMT_REJECT      = $this->burl."/tellimine/makse/"; // 13.   Reject Address    SOLOPMT_REJECT    Return address for rejected payment    AN 60    M 
		      // 14.   Solo Button OR Solo Symbol    SOLOPMT_ BUTTON SOLOPMT_IMAGE    Constant    Constant    O 
		      // $SOLOPMT_ BUTTON SOLOPMT_IMAGE   Constant    Constant    O 
		$SOLOPMT_MAC      = '';             // 15.   Payment MAC    SOLOPMT_MAC    MAC   AN 32    O 
		$SOLOPMT_CONFIRM  = 'NO';              // 16.   Payment Confirmation    SOLOPMT_CONFIRM   YES or NO   A 3   O 
		$SOLOPMT_KEYVERS  = '0001';            // 17.   Key Version    SOLOPMT_KEYVERS   E.g. 0001   N 4   O 
		$SOLOPMT_CUR      = "EEK";          // 18.   Currency Code  SOLOPMT_CUR    EUR   A 3   O 

		$VK_message       = $SOLOPMT_VERSION.'&';
		$VK_message       .= $SOLOPMT_STAMP.'&';
		$VK_message       .= $SOLOPMT_RCV_ID.'&';
		$VK_message       .= $SOLOPMT_AMOUNT.'&';
		$VK_message       .= $SOLOPMT_REF.'&';
		$VK_message       .= $SOLOPMT_DATE.'&';
		$VK_message       .= $SOLOPMT_CUR.'&';
		$VK_message       .= 'g94z7e7KgP6PM8av7kIF7bwX8YNZ7eFX'.'&';
		$SOLOPMT_MAC      = strtoupper(md5( $VK_message ));

		$this->read_template("expp_pank_nordea.tpl");
		$this->vars(array(
			"SOLOPMT_VERSION"     => $SOLOPMT_VERSION,
			"SOLOPMT_STAMP"       => $SOLOPMT_STAMP,
			"SOLOPMT_RCV_ID"      => $SOLOPMT_RCV_ID,
			"SOLOPMT_RCV_ACCOUNT" => $SOLOPMT_RCV_ACCOUNT,
			"SOLOPMT_RCV_NAME"    => $SOLOPMT_RCV_NAME,
			"SOLOPMT_LANGUAGE"    => $SOLOPMT_LANGUAGE,
			"SOLOPMT_AMOUNT"      => $SOLOPMT_AMOUNT,
			"SOLOPMT_REF"         => $SOLOPMT_REF,
			"SOLOPMT_DATE"        => $SOLOPMT_DATE,
			"SOLOPMT_MSG"         => $SOLOPMT_MSG,
			"SOLOPMT_RETURN"      => $SOLOPMT_RETURN,
			"SOLOPMT_CANCEL"      => $SOLOPMT_CANCEL,
			"SOLOPMT_REJECT"      => $SOLOPMT_REJECT,
			"SOLOPMT_MAC"         => $SOLOPMT_MAC,
			"SOLOPMT_CONFIRM"     => $SOLOPMT_CONFIRM,
			"SOLOPMT_KEYVERS"     => $SOLOPMT_KEYVERS,
			"SOLOPMT_CUR"         => $SOLOPMT_CUR,
			'image'	=> "/img/logo_solo.gif",
			'alt'		=> $lc_expp['LC_EXPP_ALT_NDEA'],
			'pank'	=> $lc_expp['LC_EXPP_PANK_NDEA'],
		//	'link'	=> "https://solo3.merita.fi/cgi-bin/SOLOPM01",
			'link'	=> "https://netbank.nordea.com/pnbepay/epay.jsp", // e-makse lingi vahetus by dragut 20.09.2007
		));
		return $this->parse();
	}

	function maksaKrediidipank() {
		global $lc_expp;
		$sql = "SELECT arvenr, viitenumber, SUM( maksumus ) AS summa FROM expp_arved WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel' GROUP BY arvenr ORDER BY arvenr DESC LIMIT 0,1";
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() == 0 ) return '';

		$this->cp->log( get_class($this), "formKrediidi", $row["arvenr"], $row["viitenumber"] );

		$_action = $this->cp->addYah( array(
				'link' => 'krediidipank',
				'text' => 'Krediidipank',
			));

		$VK_SERVICE = "1002";
		$VK_VERSION	= "008";
		$VK_SND_ID	= "EXPRPOST";
		$VK_STAMP	= $row["arvenr"];
		$VK_AMOUNT	= $row["summa"];
		$VK_CURR		= "EEK";
		$VK_REF		= $row["viitenumber"];
		$VK_MSG		= "Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
		$VK_message = sprintf("%03d",strlen($VK_SERVICE)).$VK_SERVICE;
		$VK_message.= sprintf("%03d",strlen($VK_VERSION)).$VK_VERSION;
		$VK_message.= sprintf("%03d",strlen($VK_SND_ID)).$VK_SND_ID;
		$VK_message.= sprintf("%03d",strlen($VK_STAMP)).$VK_STAMP;
		$VK_message.= sprintf("%03d",strlen($VK_AMOUNT)).$VK_AMOUNT;
		$VK_message.= sprintf("%03d",strlen($VK_CURR)).$VK_CURR;
		$VK_message.= sprintf("%03d",strlen($VK_REF)).$VK_REF;
		$VK_message.= sprintf("%03d",strlen($VK_MSG)).$VK_MSG;
		$VK_signature = "";
		$fp = fopen( $this->cfg["site_basedir"]."/pank/expp.key.key", "r");
		$priv_key = fread($fp, 2048);
		fclose($fp);
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign( $VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);
		$VK_RETURN	= $this->burl."/tellimine/makse/tanud/";	//	60	URL, kuhu vastatakse edukal tehingu sooritamisel
		$VK_CANCEL	= $this->burl."/tellimine/makse/";	//	60	URL, kuhu vastatakse ebaõnnestunud tehingu puhul
		$VK_LANG = "EST";

		$this->read_template("expp_pank.tpl");

		$this->vars(array(
			"VK_SERVICE"	=> $VK_SERVICE,
			"VK_VERSION"	=> $VK_VERSION,
			"VK_SND_ID"		=> $VK_SND_ID,
			"VK_STAMP"		=> $VK_STAMP,
			"VK_AMOUNT"		=> $VK_AMOUNT,
			"VK_CURR"		=> $VK_CURR,
			"VK_REF"			=> $VK_REF,
			"VK_MSG"			=> $VK_MSG,
			"VK_MAC" 		=> $VK_MAC,
			"VK_RETURN"		=> $VK_RETURN,
			"VK_CANCEL"		=> $VK_CANCEL,
			"VK_LANG" 		=> $VK_LANG,
			'image'	=> "/img/logo_krediidipank.gif",
			'alt'		=> $lc_expp['LC_EXPP_ALT_KRED'],
			'pank'	=> $lc_expp['LC_EXPP_PANK_KRED'],
			'link'	=> "https://i-pank.krediidipank.ee/teller/maksa",
		));
		return $this->parse();
	}
	function maksaArve() {
		global $lc_expp;

		$this->read_template("expp_tanudarve.tpl");

		$sql = "SELECT MAX(trykiarve) as trykiarve FROM expp_arved"
				." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel'";
		$row = $this->db_fetch_row($sql);
		$_trykiarve = $row['trykiarve'];

		$sql = "SELECT MAX( trykiokpakkumine ) as trykiok FROM expp_arved"
				." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='ok'";
		$row = $this->db_fetch_row($sql);
		$_trykiok = $row['trykiok'];

		$sql = "SELECT a.arvenr, a.viitenumber, a.maksumus, a.algus, a.lisarida, t.toote_nimetus, t.valjaande_nimetus"
			." FROM expp_arved a LEFT JOIN expp_valjaanne t ON a.vaindeks=t.pindeks"
			." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='ok'";
		$this->db_query( $sql );
		$_ok_count = $this->num_rows();
		if( $_ok_count > 0 ) {
			$_okline = '';
			$_oklink	= '';
			$_hansacase = '';
			while( $row = $this->db_next()) {
				$this->vars(array(
					'OKLEPINGUNR'	=> $row["arvenr"],
					'OKVIITENR'		=> $row["viitenumber"],
					'TOODE'			=> stripslashes($row["valjaande_nimetus"]),
					'LEPING'			=> ($row["algus"]==date("d.m.Y")?"<b>".$row["lisarida"]."</b>":t("alates")." <b>".$row["algus"]."</b>"),
					'HIND'			=> $row["maksumus"],
					'INPUT'			=> ($_ok_count > 1 ? 'radio' : 'hidden' ),
				));
				$_okline .= $this->parse( 'OKLINE' );
			}	//	while
			if( $_ok_count > 1 ) {
				$_hansacase = $this->parse( 'HANSACASE' );
			}
			foreach( $this->lingid as $key => $val ) {
				$this->vars(array(
					'url'		=> $val['url'],
//					'target'
					'text'	=> $lc_expp[$val['text']],
				));
				$_oklink .= $this->parse( 'OKLINK' );
			}
			$this->vars(array(
				'HANSACASE'	=> $_hansacase,
				'OKLINE'		=> $_okline,
				'OKLINK'		=> $_oklink,
			));
			$_okleping .= $this->parse( 'OTSEKORRALDUS' );
		}
		$sql = "SELECT a.arvenr, a.viitenumber, a.maksumus, a.algus, a.lopp, a.lisarida, a.vaindeks, t.toote_nimetus, t.valjaande_nimetus"
			." FROM expp_arved a LEFT JOIN expp_valjaanne t ON a.vaindeks=t.pindeks"
			." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel'";
		$this->db_query( $sql );
		$_tel_count = $this->num_rows();
		if( $_tel_count > 0 ) {
			$_summa = 0;
			$_teline = '';
			$_tellep = '';
			$_old_arve = '';
			$_old_viide = '';
			while( $row = $this->db_next()) {
				if( $_old_arve != $row['arvenr'] ) {
					if( !empty( $_teline )) {
						$this->vars(array(
							'TELLINE' => $_teline,
							'SUMMA'	 => $_summa,
						));
						$_tellep .= $this->parse( 'TELLEP' );
					}
					$this->vars(array(
						'LEPINGUNR'	=> $row['arvenr'],
						'VIITENR'	=> $row["viitenumber"],
					));
					$_old_arve	= $row['arvenr'];
					$_teline 	= '';
					$_summa		= 0;
				}
				$_summa	+= $row['maksumus'];
				$this->vars(array(
				//	'TOODE'	=> stripslashes($row["valjaande_nimetus"]),
					'TOODE' => $_SESSION['expp_remote_valjaanded'][$row['vaindeks']],
					'LEPING'	=> ( $row["algus"]==date("d.m.Y")?$row["lisarida"]:"<b>".$row["algus"]."</b> - <b>".$row["lopp"]."</b>"),
					'HIND'	=> $row["maksumus"],
				));
				$_teline .= $this->parse( 'TELLINE' );
			}
			if( !empty( $_teline )) {
				$this->vars(array(
					'TELLINE' => $_teline,
					'SUMMA'	 => $_summa,
				));
				$_tellep .= $this->parse( 'TELLEP' );
			}
			$_pangad = html::select(array(
						'name' => 'maksan',
						'options' => $this->pangad,
						'selected' => '',
						'class' => 'formElement',
					));
			$this->vars(array(
				'TELLEP' => $_tellep,
				'makse_meetod' =>	$_pangad,
			));
			$_teleping = $this->parse( 'TAVALEPING' );
		}
		if( $_tel_count < 1 && $_ok_count < 1 ) {
			return $retHTML;
		}

		$_aid = $this->cp->getPid( 2 );
		$myURL = $this->cp->addYah( array(
				'link' => 'tanudarve',
				'text' => $lc_expp['LC_EXPP_TITLE_TANUD'],
			));

		if( $_trykiarve > 0 ) {
			$_trykiarve = $this->parse( 'TRYKIARVE' );
		} else {
			$_trykiarve = '';
		}

		if( $_trykiok > 0 ) {
			$_trykiok = $this->parse( 'TRYKIOK' );
		} else {
			$_trykiok = '';
		}

		$this->vars(array(
			'url'				=> aw_ini_get("baseurl")."/tellimine/",
			'TRYKIARVE'		=> $_trykiarve,
			'TRYKIOK'		=> $_trykiok,
			'ACTION'			=> $myURL,
			'OTSEKORRALDUS'=> $_okleping,
			'TAVALEPING'	=> $_teleping,
		));
		$retHTML .= $this->parse();
		return $retHTML;

	}
	function maksaTanud() {
		global $lc_expp;

		$this->read_template("expp_tanud.tpl");
		$_aid = $this->cp->getPid( 2 );
		$myURL = $this->cp->addYah( array(
				'link' => 'tanud',
				'text' => $lc_expp['LC_EXPP_TITLE_TANUD'],
			));

		$this->vars(array(
			'url'				=> aw_ini_get("baseurl")."/tellimine/"
		));
		$retHTML .= $this->parse();
		return $retHTML;
	}

	function okHansapank() {
		global $lc_expp;
		$ref = $GLOBALS['HTTP_GET_VARS']['ref'];
//		if ( $ref ) {
			$this->updatePank( 'ok', $ref, $this->lingid['hansapank']['pank'] );
//		}
		

		$this->read_template("expp_ok_hansa.tpl");

		$this->vars(array(
			'REF_NO'	=> $ref,
			'image'	=> "/img/hansapank.gif",
			'alt'		=> $lc_expp['LC_EXPP_ALT_HP'],
			'pank'	=> $lc_expp['LC_EXPP_PANK_HP'],
			'link'	=> $this->lingid['hansapank']['okurl'],
		));
		return $this->parse();
	}

	function okPank( $pank ){
		$url = $this->lingid[$pank]['okurl'];
		if( empty( $url )) {
			$url = 'http://www.tellimine.ee/tellimine/';
		} else {
			$this->updatePank( 'ok', '', $this->lingid[$pank]['pank'] );
		}
		header( "Location: {$url}" );
		exit;
	}

	function updatePank( $lep, $ref, $pank ) {
		$sql = "UPDATE expp_arved SET pank='{$pank}'"
				." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='{$lep}'";
		$this->db_query( $sql );
	}
/*
	function maksaOK() {
		$retHTML = '';
		$_ok = $this->cp->getPid( 3 );
		$_cell = $this->lingid[$_ok];
		if( is_array( $_cell ) && !empty( $_cell )) {
			header( "Location :".$_cell['url2'] );
			exit;
		}
		return $retHTML;
	}
*/

	function remoteTellimus() {
		$tellimus = $this->cp->getPid( 2 );
		if (empty($tellimus)) {
			return false;
		}

		$arved = $this->db_fetch_field("select count(*) as nr from expp_arved where tlkood='".$tellimus."' and staatus='".uus."'", 'nr');
		if ($arved > 0)
		{
			header('Location:'.aw_url_change_var('ok', '0', $_SERVER['HTTP_REFERER']));
			exit();
		}

		$expp_arve = get_instance( CL_EXPP_ARVE );

		$arvenr = $expp_arve->getNextArve();

		$_SESSION['tellnr'] = $expp_arve->getNextTell();
		$_SESSION['arvenr'] = $arvenr;

		$viitenumber = "10605".$arvenr;
		$viitenumber .= $expp_arve->leia_731( $viitenumber );

		$_SESSION['expp_remote_valjaanded'] = array(); 

		$tellimus_url_xml = "http://www.raamat24.ee/kasutaja.php?tellimus=".$tellimus."&eksport=xml&ak=1";
		$tellimus_raw_xml = file_get_contents($tellimus_url_xml);
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $tellimus_raw_xml, $values, $index);
		xml_parser_free($xml_parser);

		$tellimused = array();
		foreach ($values as $value)
		{
			if ( ($value['tag'] == 'TOODE') && ($value['type'] == 'open') )
			{
				$tellimus = array();
			}

			if ($value['type'] == 'complete')
			{
				$tellimus[$value['tag']] = $value['value'];
			}

			if ( ($value['tag'] == 'TOODE') && ($value['type'] == 'close') )
			{
				$tellimused[] = $tellimus;
			}
		}

		foreach ($tellimused as $tell)
		{
			// viitenumber:

			$_SESSION['expp_remote_valjaanded'][$tell['VAINDEKS']] = $tell['AK_TOOTENIMI'];

			// I need change the date format for expp_arved db table:
			$tellkpv_osad = explode('-', $tell['TELLKPV']);
			$tellkpv = $tellkpv_osad[2].'.'.$tellkpv_osad[1].'.'.$tellkpv_osad[0];

			$alguskpv_osad = explode('-', $tell['ALGUS']);
			$alguskpv = $alguskpv_osad[2].'.'.$alguskpv_osad[1].'.'.$alguskpv_osad[0];

			$loppkpv_osad = explode('-', $tell['LOPP']);
			$loppkpv = $loppkpv_osad[2].'.'.$loppkpv_osad[1].'.'.$loppkpv_osad[0];

			$sql = "
				insert into
					expp_arved
				set
					snimi = '".$tell['SNIMI']."',
					senimi = '".$tell['SENIMI']."',
					semail = '".$tell['SEMAIL']."',
					stanav = '".$tell['STANAV']."',
					smaja = '".$tell['SMAJA']."',
					skorter = '".$tell['SKORTER']."',
					stelefon = '".$tell['STELEFON']."',
					smobiil = '".$tell['SMOBIIL']."',
					sfax = '".$tell['SFAX']."',
					sindeks = '".$tell['SINDEKS']."',
					saadress = '".$tell['SAADRESS']."',
					sisikukood = '".$tell['SISIKUKOOD']."',
					
					mnimi = '".$tell['MNIMI']."',
					mtanav = '".$tell['MTANAV']."',
					mmaja = '".$tell['MMAJA']."',
					mkorter = '".$tell['MKORTER']."',
					mtelefon = '".$tell['MTELEFON']."',
					mmobiil = '".$tell['MMOBIIL']."',
					mfax = '".$tell['MFAX']."',
					mindeks = '".$tell['MINDEKS']."',
					maadress = '".$tell['MAADRESS']."',
					menimi = '".$tell['MENIMI']."',
					misikukood = '".$tell['MISIKUKOOD']."',
					memail = '".$tell['MEMAIL']."',

					leping = 'tel',
					vvotja = '".$tell['VVOTJA']."',
					kanal = '".$tell['KANAL']."',
					tlkood = '".$tell['TLKOOD']."',
					arvenr = '".$arvenr."',
					vaindeks = '".$tell['VAINDEKS']."',
					algus = '".$alguskpv."',
					lopp = '".$loppkpv."',
					eksempla = '".$tell['EKSEMPLA']."',
					tellkpv = '".$tellkpv."',
					maksumus = '".$tell['MAKSUMUS']."',
					rhkkood = '',
					
					kampaania = '".$tell['RKMKOOD']."',
					tyyp = '".$tell['TYYP']."',
					trykiarve = ".(int)$tell['TRYKIARVE'].",
					trykiokpakkumine = ".(int)$tell['TRYKIOKPAKKUMINE'].",
					viitenumber = '".$viitenumber."',
					session = '".session_id().'-'.$_SESSION['tellnr']."',

					pank = ".(int)$tell['PANK'].",
					time = NOW(),
					staatus = 'uus'
			";
			$this->db_query($sql);
		}
		header("Location:".aw_ini_get("baseurl")."/tellimine/remotemakse");
		exit();
	}
}
?>
