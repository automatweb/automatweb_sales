<?php

namespace automatweb;

class protocol_finder
{
	/**
	@attrib api=1 params=pos
	@param url required type=string
		message oid or mailinglist oid
	@returns http or ftp class
	@errors
		ERR_NO_PROTOCOL - if there is no protocol implemented for url
	@examples
		$proto_find = get_instance("protocols/protocol_finder");
		$proto_inst = $proto_find->inst($data["value"]);
		$str = $proto_inst->get($data["value"]);
	**/
	function inst($url)
	{
		$data = parse_url($url);
		switch($data["scheme"])
		{
			case "":
			case "http":
				return get_instance("protocols/file/http");

			case "ftp":
				return get_instance("protocols/file/ftp");
		}
		error::raise(array(
			"id" => ERR_NO_PROTOCOL,
			"msg" => sprintf(t("protocol_fnider::inst(%s): no protocol implemented for url"), $url)
		));
	}
}

interface protocol_interface
{
	// protocol name string
	function name();
}

?>
