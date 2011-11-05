<?php
include(aw_ini_get("classdir").aw_ini_get("site_impl_dir")."site_header".AW_FILE_EXT);
$o = new orb();
$result = $o->handle_rpc_call(array(
	"method" => "xmlrpc"
));

if (strlen($result))
{
	automatweb::$result->set_data($result);
}
