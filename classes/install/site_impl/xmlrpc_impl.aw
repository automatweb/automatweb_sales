<?php
include(aw_ini_get("classdir").aw_ini_get("site_impl_dir")."site_header".AW_FILE_EXT);
$o = new orb();
echo $o->handle_rpc_call(array(
	"method" => "xmlrpc"
));

