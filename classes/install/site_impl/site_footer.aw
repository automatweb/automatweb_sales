<?php

$sf = new aw_template();
$sf->tpl_init();
$sf->read_template(!empty($index_template) ? $index_template : "index.tpl");
$_t = aw_global_get("act_period");
$sf->vars(array(
	"content" => $content,
	"per_string" => $_t["description"],
	"date" => $sf->time2date(time(),2),
	"charset" => aw_global_get("charset"),
	"sel_charset" => aw_global_get("charset"),
	"title_action" => aw_global_get("title_action"),
));


if (aw_global_get("uid"))
{
	$sf->vars(array(
		"login" => "",
		"uid"  => aw_global_get("uid")
	));
	$sf->vars(array("logged" => $sf->parse("logged")));
}
else
{
	$sf->vars(array("logged" => ""));
	$sf->vars(array("login" => $sf->parse("login")));
}

$a_plugins = array();
if ( aw_ini_get("plugin.jquery") )
{
	$a_plugins["automatweb/js/jquery_latest.aw"] = array("type" => "js");
}

if ( aw_ini_get("plugin.thickbox") )
{
	$a_plugins["automatweb/js/jquery_latest.aw"] = array("type" => "js");
	$a_plugins["automatweb/css/thickbox.css"] = array("type" => "css");
	$a_plugins["automatweb/js/jquery/plugins/thickbox.js"] = array("type" => "js");
}

if ( aw_ini_get("plugin.protect_emails") )
{
	$a_plugins["automatweb/js/jquery_latest.aw"] = array("type" => "js");
	$a_plugins["automatweb/js/jquery/plugins/jquery_protect_email.js"] = array("type" => "js");
}

if (aw_ini_get("menuedit.protect_emails") == 1 || aw_ini_get("plugin.protect_emails"))
{
	$i = new mail_protector();
	$str = $i->protect($sf->parse());
	if ( aw_ini_get("plugin.protect_emails") )
	{
		$a_plugins["automatweb/js/jquery_latest.aw"] = array("type" => "js");
		$a_plugins["automatweb/js/jquery/plugins/jquery_protect_email.js"] = array("type" => "js");
	}
}
else
{
	$str = $sf->parse();
}

if (aw_ini_get("content.doctype") === "html" )
{
	$str = str_replace  ( array("<br />", "<br/>"), "<br>", $str);
}
else if (aw_ini_get("content.doctype") === "xhtml" )
{
	$str = str_replace  ( array("<br>", "<BR>"), "<br />", $str);
}

// include the javascripts
$s_plugins = "";
foreach ($a_plugins as $key => $var )
{
	if ( $var["type"] === "js" )
	{
		$s_plugins .= '<script type="text/javascript" src="'.aw_ini_get("baseurl").$key.'"></script>';
	}
	else
	{
		$s_plugins .= '<link href="'.aw_ini_get("baseurl").$key.'" rel="stylesheet" type="text/css" media="screen" />';
	}
}
$str = preg_replace  ( "/<\/head>/imsU", $s_plugins."</head>\n" , $str);

// this will add google analytics code to html if id is set in ini
if (aw_ini_get("ga_id"))
{
	$sf->read_template("applications/google_analytics/tracking_code.tpl");
	$gpn = "";
	if (strlen(aw_global_get("ga_page_name")) > 1 )
	{
		$gpn = "\"".aw_global_get("ga_page_name")."\"";
	}
	$sf->vars(array(
		"ga_id" => aw_ini_get("ga_id"),
		"ga_page_name" => $gpn,
	));
	$s_code = $sf->parse();
	$str = preg_replace  ( "/<\/body>.*<\/html>/imsU", $s_code."</body>\n</html>" , $str);
}

if ( aw_ini_get("menuedit.protect_emails") || aw_ini_get("plugin.protect_emails") )
{
	if ( aw_ini_get("plugin.protect_emails") )
	{
		$s_protect_emails = '<script type="text/javascript">jQuery.protect_email();</script>';
		$str = preg_replace  ( "/<\/body>.*<\/html>/imsU", $s_protect_emails."</body>\n</html>" , $str);
	}
}

// search for swfobject from html
if (strpos($str, "var aw_flash_"))
{
	$s_swfobject = '<script type="text/javascript" src="'.aw_ini_get("baseurl").'automatweb/js/swfobject.js"></script>';
	$str = str_replace ( "</head>" , $s_swfobject."\n</head>", $str);
}

if (isset($_GET["TPL"]) and $_GET["TPL"] === "1")
{
	// fix for logged out users - dint show templates after page refresh
	if (aw_global_get("uid")=="")
	{
		if (strlen(cache::file_get("tpl_equals_1_cache_".aw_global_get("section")))==0)
		{
			cache::file_set("tpl_equals_1_cache_".aw_global_get("section"), aw_global_get("TPL=1"));
		}
		else
		{
			aw_global_set("TPL=1", cache::file_get("tpl_equals_1_cache_".aw_global_get("section")));
		}
	}
	else
	{
		cache::file_set("tpl_equals_1_cache_".aw_global_get("section"), aw_global_get("TPL=1"));
	}

	$sf->read_template("debug/tpl_equals_1.tpl");
	eval (aw_global_get("TPL=1"));
	$tmp = "";
	foreach ($_aw_tpl_equals_1 as $key=>$var)
	{
		$count = 0;
		foreach($_aw_tpl_equals_1_counter as $var2)
		{
			if ($key == $var2)
			{
				$count++;
			}
		}

		$sf->vars(array(
			"text" => $key,
			"link" => $var["link"],
			"count" => $count,
		));
		$tmp .= $sf->parse("TEMPLATE");
	}

	$sf->vars(array(
		"TEMPLATE" => $tmp
	));
	aw_global_set("TPL=1", $sf->parse());
	$str = preg_replace("/<body.*>/imsU", "\\0".aw_global_get("TPL=1"), $str);
}

// if (false && aw_ini_get("content.compress") === "1")
// {
	// ob_start( 'ob_gzhandler' );
	// echo $str;
// }
// else
// {
	// ob_start();
	echo $str;
	// ob_end_flush();
// }

aw_shutdown();

// do a cache clean every hour
if (filectime(aw_ini_get("cache.page_cache")."/temp/lmod") < (time() - 3600))
{
	$m = new maitenance();
	$m->cache_update(array());

	$m = new scheduler();
	$m->static_sched(array());
}
