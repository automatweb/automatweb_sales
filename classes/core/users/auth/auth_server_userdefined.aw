<?php

namespace automatweb;
// auth_server_userdefined.aw - Autentimisserver kasutajadefineeritud
/*

@classinfo syslog_type=ST_AUTH_SERVER_USERDEFINED relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@property auto_create_user type=checkbox ch_value=1 field=meta method=serialize
@caption Kas lastakse sisse logida kasutajatel, keda kohalikus s&uuml;steemis pole

@property no_save_pwd type=checkbox ch_value=1 field=meta method=serialize
@caption &Auml;ra salvesta AW'sse kasutaja parooli


@property code type=textarea rows=50 cols=100 field=meta method=serialize
@caption Kood
*/

class auth_server_userdefined extends class_base
{
	const AW_CLID = 1191;

	function auth_server_userdefined()
	{
		$this->init(array(
			"tpldir" => "core/users/auth/auth_server_userdefined",
			"clid" => CL_AUTH_SERVER_USERDEFINED
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function check_auth($server, $credentials, &$conf)
	{
		$code = $server->prop("code");
		eval($code);

		if ($res[0] == true)
		{
			if ($conf->check_local_user($server->id(), $credentials))
			{
				return $res;
			}
		}
		return array(false, "", false);
	}
}
?>
