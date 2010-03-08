<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_korv.aw,v 1.7 2007/11/23 07:18:28 dragut Exp $
// expp_korv.aw - Expp tootekorv 
/*

@classinfo syslog_type=ST_EXPP_KORV relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_korv extends class_base {

	var $cy;
	var $cp;

	function expp_korv() {
		$this->init(array(
			"tpldir" => "expp",
			"clid" => CL_EXPP_KORV
		));
		$this->cy = get_instance( CL_EXPP_JAH );
		$this->cp = get_instance( CL_EXPP_PARSE );
		lc_site_load( "expp", $this );
		$GLOBALS['expp_show_footer'] = 1;
	}

	function show($arr) {
		global $lc_expp;
		if( isset( $GLOBALS['HTTP_POST_VARS']['tagasi'] ) || isset( $GLOBALS['HTTP_POST_VARS']['tagasi_y'] )) {
			$this->returnPost();
		}
		if( isset( $GLOBALS['HTTP_POST_VARS']['edasi'] ) || isset( $GLOBALS['HTTP_POST_VARS']['edasi_y'] )) {
			$this->parsePost();
		}
		$_SESSION['expp_kampaania'] = '';

		$_kid = intval( $this->cp->getVal('kustuta'));
		if( $_kid > 0 ) {
			$this->kustuta( $_kid );
		}

		$_action = $this->cp->addYah( array(
				'link' => 'korv',
				'text' => $lc_expp['LC_EXPP_TITLE'],
			));

		$this->cp->log( get_class($this), "show" );

		$this->read_template("expp_korv.tpl");

		$sql = "SELECT k.id"
			.", k.eksemplar"
			.", k.algus"
			.", k.leping"
			.", k.kogus"
			.", t.valjaande_nimetus"
			.", h.kestus"
			.", h.hinna_tyyp"
			.", h.baashind"
			.", h.juurdekasv"
		." FROM expp_korv k ,expp_valjaanne t left join expp_hind h on k.pikkus=h.id AND h.pindeks=k.pindeks"
		." WHERE k.session='".session_id()."' AND k.pindeks=t.pindeks"
		." ORDER BY k.leping DESC, t.valjaande_nimetus ASC";
		$this->db_query($sql);
		if( $this->num_rows() == 0 ) {
			$this->vars(array(
				'TEXT' => $lc_expp['LC_EXPP_EMPTY'],
			));
			$this->vars(array(
				'ACTION' => $_action,
				'TYHI' => $this->parse('TYHI'),
			));
			return $this->parse();
		}

		$_out_rows = array();
		$_sum_rows = array();
		while ($row = $this->db_next()) {
			$_toode = stripslashes( $row["valjaande_nimetus"] );
			$_algus = ($row["algus"] == "ASAP")?$lc_expp['LC_EXPP_ASAP']."<br />": (($row["algus"] == "CONT")?$lc_expp['LC_EXPP_CONT']."<br />" :	aw_locale::get_lc_month(intval(substr( $row["algus"],4,2)))." ".substr( $row["algus"],0,4));
			if (!isset( $_out_rows[$row["leping"]]))		$_out_rows[$row["leping"]] = "";
			$_eksemplar	= intval( $row["eksemplar"] );
			$_kogus		= intval( $row["kogus"] );
			$_kestus		= intval( $row["kestus"] );
			$_hind		= $_eksemplar*(float)$row["baashind"];
			if ( $_kogus > 1 )
				$_hind += ($_kogus- 1)*(float)$row["juurdekasv"]*$_kestus*$_eksemplar;
			if (!isset( $_sum_rows[$row["leping"]]))
				$_sum_rows[$row["leping"]] = $_hind;
			else
				$_sum_rows[$row["leping"]]+= $_hind;
			$_kestus = $_kogus* $_kestus;
			switch( $row["hinna_tyyp"] ) {
				case 0: $_hinnatyyp=( $_kestus== 1 ?t("kuu"):t("kuud"));
					break;
				case 1:	$_hinnatyyp=( $_kestus== 1 ?t("n&auml;dal"):t("n&auml;dalat"));
					break;
				case 2:	$_hinnatyyp=( $_kestus== 1 ?t("p&auml;ev"):t("p&auml;eva"));
					break;
				case 3:	$_hinnatyyp=( $_kestus== 1 ?t("number"):t("numbrit"));
					break;
				case 4:	$_hinnatyyp=( $_kestus== 1 ?t("aasta"):t("aastat"));
					break;
	 			case 5:	$_hinnatyyp=( $_kestus== 1 ?t("poolkuu"):t("poolkuud"));
			}
			$_hind = sprintf( "%1.0d", $_hind );
			$this->vars(array(
				'TOODE'  => $_toode,
				'KOGUS'  => $_eksemplar,
				'LEPING' => "{$_algus} ({$_kestus} {$_hinnatyyp})",
				'HIND'   => $_hind,
				'LINK'   => $_action.'?kustuta='.intval($row['id']),
			));
			$_out_rows[$row['leping']] .= $this->parse( 'RIDA' );
		}
		$_leping = '';
		foreach( $_out_rows as $key => $_rida ) {
			if( $key != 'ok' ) {
				$this->vars(array(
					'KOKKU'	  => sprintf( "%1.2d EEK", $_sum_rows[$key] ),
				));
				$_summa = $this->parse( 'SUMMA' );
			} else {
				$_summa = '';
			}
			$this->vars(array(
				'PEALKIRI'	=> ( $key == 'ok' ? $lc_expp['LC_EXPP_OK'] : $lc_expp['LC_EXPP_TEL'] ),
				'RIDA'		=> $_rida,
				'SUMMA'		=> $_summa,
			));
			$_leping .= $this->parse( 'LEPING' );
		}
		$this->vars(array(
			'ACTION' => $_action,
			'LEPING'	=> $_leping,
		));
		return $this->parse();
	}
	
	function kustuta( $id ) {
		$sql = "select v.pindeks, v.toimetus, v.valjaande_nimetus from expp_valjaanne v, expp_korv k where k.id = '$id' AND k.session='".session_id()."' AND v.pindeks = k.pindeks";
		$row = $this->db_fetch_row($sql);
		$this->cp->log( get_class($this), "kustuta_korvist", $row['pindeks'], $row['toimetus'], $row['valjaande_nimetus'] );

		$sql = "DELETE FROM expp_korv WHERE id = '$id' AND session='".session_id()."'";
		$this->db_query($sql);

		header( "Location: ".aw_ini_get("baseurl")."/tellimine/korv/" );
		exit;
	}

	function returnPost() {
		header( "Location: ".aw_ini_get("baseurl")."/tellimine/" );
		exit;
	}

	function parsePost() {
		header( "Location: ".aw_ini_get("baseurl")."/tellimine/tellija/" );
		exit;
	}
}
?>
