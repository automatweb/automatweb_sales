<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_tyybid.aw,v 1.7 2008/02/17 21:13:01 kristo Exp $
// expp_tyybid.aw - Expp tyybid 
/*

@classinfo syslog_type=ST_EXPP_TYYBID relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_tyybid extends class_base implements main_subtemplate_handler
{
	const AW_CLID = 984;

	function expp_tyybid() {
		$this->init(array(
			"tpldir" => "automatweb/menuedit",
			"clid" => CL_EXPP_TYYBID
		));
		lc_site_load( "expp", $this );
	}

	function show($arr) {

		global $lc_expp;

		$retHTML = '';
		$this->read_template("main.tpl");

		$this->db_query("SELECT id,nimi FROM expp_tyybid ORDER BY sort");
		while ($row = $this->db_next())
		{
			$_lc_key = 'LC_EXPP_DB_'.strtoupper($row['nimi']);
			$_tyyp_wnimi = ( isset( $lc_expp[$_lc_key] ) ? $lc_expp[$_lc_key] : $row['nimi'] );

			$this->vars(array(
				'link' => aw_ini_get("tell_dir").urlencode(strtolower($row['nimi'])),
				'text' => $_tyyp_wnimi,
			));
			$retHTML .= $this->parse('VAIKE_TYYP');
		}
		return $retHTML;
	}

	function on_get_subtemplate_content($arr) {
		$arr["inst"]->vars(array(
			"VAIKE_TYYP" => $this->show( $arr )
		));
	}
}
?>
