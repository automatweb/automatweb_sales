<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_basket_short.aw,v 1.8 2008/02/17 21:13:01 kristo Exp $
// expp_basket_short.aw - Expp short basket 
/*

@classinfo syslog_type=ST_EXPP_BASKET_SHORT relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_basket_short extends class_base implements main_subtemplate_handler
{
	function expp_basket_short()
	{
		$this->init(array(
			"tpldir" => "automatweb/menuedit",
			"clid" => CL_EXPP_BASKET_SHORT
		));
		lc_site_load( "expp", $this );
	}
	function show($arr) {
		global $lc_expp;

		if (strpos($_SERVER['REQUEST_URI'], 'remotemakse') !== false)
		{
			return '';
		}

		$retHTML = '';
		$this->read_template("main.tpl");
		$sql = "SELECT count(*) as arv FROM expp_korv WHERE session='".session_id()."'";
		$row = $this->db_fetch_row( $sql );
		$_kogus = intval( $row['arv'] );
		if( $_kogus > 0 ) {
			$_text = sprintf( $lc_expp['LC_EXPP_ITEMS'], $_kogus );
		} else {
			$_text = $lc_expp['LC_EXPP_EMPTY'];
		}
 		$this->vars(array(
			'link' => aw_ini_get("tell_dir").'korv',
			'text' => $_text,
		));
		return $this->parse('VAIKE_KORV');
	}

	function on_get_subtemplate_content($arr)
	{
		$arr["inst"]->vars(array(
			"VAIKE_KORV" => $this->show( $arr )
		));
	}
	//-- methods --//
}
?>
