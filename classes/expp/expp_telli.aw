<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_telli.aw,v 1.8 2007/12/12 10:28:15 dragut Exp $
// expp_telli.aw - Expp telli 
/*

@classinfo syslog_type=ST_EXPP_TELLI relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/

/*
DROP TABLE IF EXISTS `expp_korv`;
CREATE TABLE `expp_korv` (
  `id` int(11) NOT NULL auto_increment,
  `session` varchar(63) NOT NULL default '',
  `pindeks` int(11) NOT NULL default '0',
  `eksemplar` int(11) NOT NULL default '0',
  `algus` varchar(6) NOT NULL default '',
  `leping` varchar(4) NOT NULL default '',
  `pikkus` int(11) NOT NULL default '0',
  `kogus` int(11) NOT NULL default '0',
  `time` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `session` (`session`),
  KEY `pindeks` (`pindeks`)
) TYPE=MyISAM;

*/
class expp_telli extends class_base {

	var $cy;
	var $cp;
	var $post_errors = array();
	var $lang;

	function expp_telli() {
		$this->lang = aw_global_get("admin_lang_lc");

		$this->init(array(
			"tpldir" => "expp",
			"clid" => CL_EXPP_TELLI
		));
		$this->cy = get_instance( CL_EXPP_JAH );
		$this->cp = get_instance( CL_EXPP_PARSE );
		lc_site_load( "expp", $this );
		$GLOBALS['expp_show_footer'] = 1;
	}

	function show($arr) {
		global $lc_expp;
		$retHTML = '';
		if( empty( $this->cp->pids )) return $retHTML;
		if( isset( $GLOBALS['HTTP_POST_VARS']['tyhista'] ) || isset( $GLOBALS['HTTP_POST_VARS']['tyhista_y'] )) {
			$this->returnPost();
		}
		if( isset( $GLOBALS['HTTP_POST_VARS']['salvesta'] ) || isset( $GLOBALS['HTTP_POST_VARS']['salvesta_y'] )) {
			$this->parsePost();
		}

		$_aid = $this->cp->getPid( 2 );
		if( empty( $_aid )) return $retHTML;

//		$_pikkus = intval( $this->cp->getVal('pikkus'));
		$_pikkus = $this->cp->getVal('pikkus');
		$_leping = $this->cp->getVal( 'leping' );
		$_temp = explode( '_', $_pikkus );
		$_p = intval( $_temp[0] );
// KIRJELDUS
		$_vanne = array();
		if( $this->lang != 'et' ) {
			$sql = "SELECT v.pindeks, v.toimetus, v.toote_nimetus, v.kampaania, v.veebi_kirjeldus, tr1.nimetus as lang_va, tr2.nimetus as lang_toode"
					." FROM expp_valjaanne v"
					." LEFT JOIN expp_translate tr1 ON tr1.pindeks = v.pindeks AND tr1.tyyp = 'va' AND tr1.lang = '{$this->lang}'"
					." LEFT JOIN expp_translate tr2 ON tr2.pindeks = v.pindeks AND tr2.tyyp = 'toode' AND tr2.lang = '{$this->lang}'"
					." WHERE v.valjaande_nimetus='{$_aid}'";
		} else {
			$sql = "SELECT v.pindeks, v.toimetus, v.toote_nimetus, v.kampaania, v.veebi_kirjeldus"
					." FROM expp_valjaanne v"
					." WHERE v.valjaande_nimetus='{$_aid}'";
		}
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() == 0 ) return $retHTML;

		$_ch_logo = str_replace( '%', '#', urlencode( stripslashes( $row['toote_nimetus'] ))); // ."_logo";
		$cl = get_instance( CL_EXPP_SITE_LOGO );
		$cl->register( $_ch_logo );
		if( $this->lang != 'et' ) {
			$_laid = ( isset( $row['lang_toode'] ) && !empty( $row['lang_toode'] ) ? $row['lang_toode'] : $_aid );
		} else {
			$_laid = $_aid;
		}

		$myURL = $this->cp->addYah( array(
				'link' => 'telli/'.urlencode( $_aid ),
				'text' => $lc_expp['LC_EXPP_TELLIMINE'].': '.$_laid,
			));

		$this->read_template("expp_periood.tpl");

		$this->vars(array(
			'TEXT' => stripslashes( $row['veebi_kirjeldus'] ),
		));
		$_kirjeldus	= $this->parse( 'KIRJELDUS' );
		$_pindeks	= $row['pindeks'];
		$_toimetus	= $row['toimetus'];
		$_pari_veel	= 1;
		if( isset( $_SESSION['expp_kampaania']) && !empty( $_SESSION['expp_kampaania'] )) {
			$__kampaania = addslashes( $_SESSION['expp_kampaania'] );
			$sql = "SELECT h.* FROM"
					." expp_hind h, expp_valjaanne v"
					.( $_p > 0 ? ", expp_hind hh " : '' )
					." WHERE h.pindeks=v.pindeks"
					." AND h.kampaania = '{$__kampaania}'"
					.( $_p > 0 ? " AND hh.hinna_liik = h.hinna_liik AND hh.pindeks = h.pindeks AND hh.id = '{$_p}'" : '' )
					." AND h.lubatud = 'jah'"
					." AND (h.algus+0 = 0 OR h.algus <= now())"
					." AND (h.lopp+0 = 0 OR h.lopp >= now())"
					." AND h.hinna_liik in ( 'SALAJANE_OKHIND', 'SALAJANE_TAVAHIND' )"
					." AND v.valjaande_nimetus='{$_aid}'";
			$this->db_query( $sql );
			if( $this->num_rows() > 0 ) $_pari_veel = 0;
		}
		if( $_pari_veel == 1 ) {
			$sql = "SELECT h.* FROM"
					." expp_hind h, expp_valjaanne v"
					.( $_p > 0 ? ", expp_hind hh " : '' )
					." WHERE h.pindeks=v.pindeks"
					.( $_p > 0 ? " AND hh.hinna_liik = h.hinna_liik AND hh.pindeks = h.pindeks AND h.id = '{$_p}'" : '' )
					." AND h.lubatud = 'jah'"
					." AND (h.algus+0 = 0 OR h.algus <= now())"
					." AND (h.lopp+0 = 0 OR h.lopp >= now())"
					.( $_p == 0 ? " AND h.hinna_liik in ( 'OKHIND', 'TAVAHIND' )" : " AND h.hinna_liik in ( 'OKHIND', 'AVALIK_OKHIND', 'TAVAHIND', 'AVALIK_TAVAHIND' )" )
					." AND v.valjaande_nimetus='{$_aid}'";
			$this->db_query( $sql );
		}
		if( $this->num_rows() == 0 ) return $retHTML;

		$this->cp->log( get_class($this), "show", $_pindeks, $_toimetus, $_aid );

		$my_otsek 	= array();
		$my_tavah	= array();
		$_kampaania = '';
		while ( $row = $this->db_next()) {
			$_pindeks	= $row['pindeks'];
			if( !empty( $row['hinna_kirjeldus'] ) && empty( $_kampaania ) ) {
				$_kampaania = $row['hinna_kirjeldus'];
			}
			$row['baashind']  = sprintf( "%1.0f", $row['baashind'] );
			switch( $row["hinna_liik"] ) {
				case 'OKHIND':
				case 'AVALIK_OKHIND':
				case 'SALAJANE_OKHIND':
					$my_otsek[]	= $row;
					if( $_pikkus > 0 && $_pikkus == $row['id'] ) $_leping = 'ok';
					break;
/*
				case 'OK_TAVA':
					$my_otsek[]	= $row;
*/
				case 'TAVAHIND':
				case 'AVALIK_TAVAHIND':
				case 'SALAJANE_TAVAHIND':
				default:
					$my_tavah[]	= $row;
					if( $_pikkus > 0 && $_pikkus == $row['id'] ) $_leping = 'tel';
			}	// switch
		}
		$kogus		= intval($GLOBALS["HTTP_POST_VARS"]["kogus"]?$GLOBALS["HTTP_POST_VARS"]["kogus"]:$GLOBALS["HTTP_GET_VARS"]["kogus"]?$GLOBALS["HTTP_GET_VARS"]["kogus"]:1);
		if( $kogus < 1 ) $kogus = 1;


		$_action			= $this->cy->getURL().urlencode( $_aid );
		$_pealkiri		= stripslashes( $_aid );;

// TINGIMUSED
		$_link = "";
		$this->vars(array(
			'LINK' => $_link,
		));
		$_tingimused   = $this->parse( 'TINGIMUSED' );

// VEAD
		if( !empty( $this->post_errors )) {
			$_viga = "";
			foreach( $this->post_errors as $_text ) {
				$this->vars(array(
					'TEXT' => $_text,
				));
				$_viga .= $this->parse( 'VIGA' );
			}
			$this->vars(array(
				'VIGA' => $_viga,
			));
			$_vead = $this->parse( 'VEAD' );
		} else {
			$_vead = '';
		}

// KUUPAEV
		$_kuup_options = array(
			'' => '-------------------------'
		);
		if ( strncmp ( $_aid, 'Postimees', 9 ) != 0 ) {
		
			// Vastvaalt v2ljaande koodile kontrollin ilmumisgraafikust, kas j2rgmine number ilmub v2hem kui 25 p2eva
			// p2rast
			$valjaande_kood = $this->db_fetch_field("select * from expp_valjaanne where pindeks = ".$_pindeks, "valjaanne");
			$kaks_numbrit = $this->db_fetch_array("select * from expp_ilmumisgraafik where valjaanne = '$valjaande_kood' order by ilmumiskpv desc limit 2");
			if (strtotime($kaks_numbrit[0]['ilmumiskpv']) < (strtotime($kaks_numbrit[1]['ilmumiskpv']) + (25 * 24 * 60 * 60)) )
			{
				$_kuup_options['ASAP'] = $lc_expp['LC_EXPP_ASAP'];
			}

		}

		$_kuup_options['CONT'] = $lc_expp['LC_EXPP_CONT'];
		if ( date("d",mktime(0,0,0,date("m"),date("d")+14,date("Y")))< 15 ) { 
			$jn = 1;
		}
		else {
			$jn = 0;
		}

		for ( $in=1;$in<13;$in++ ) {
			$ajut = mktime(0,0,0,date("m")+$in+$jn,1,date("Y"));
			$year = date("Y",$ajut);
			$month = date("m",$ajut);
			$_value = date("Ym",$ajut);
			$_text = aw_locale::get_lc_month(intval($month))." $year";
			$_kuup_options[$_value] = $_text;
		}	// for
// KAMPAANIA
// dragut h2kib
		if (!empty($_SESSION['expp_kampaania']))
		{

			$kampaaniad_sql = "
				select 
					* 
				from 
					expp_kampaania 
				where 
					nimetus = '".$_SESSION['expp_kampaania']."' 
			";

			$kampaania_info = $this->db_fetch_array($kampaaniad_sql);
			if (!empty($kampaania_info))
			{
				$kampaania_info = reset($kampaania_info);
				$algus_aeg = $kampaania_info['tellimine_algus'];
				if ($algus_aeg != -1)
				{
					$_kuup_options = array(date('Ymd', $algus_aeg) => date('d.m.Y', $algus_aeg));
				}
				$_SESSION['expp_kampaania_email_noutud'] = $kampaania_info['email_noutud'];
			}
			
			
		
		}
	/*
		if ($_pikkus == "218372")
		{
			$_kuup_options = array('20070402' =>  "02.04.2007");
		}
	*/	
		$_kuupaev = html::select( array(
			'name' => 'algus',
			'options' => $_kuup_options,
			'selected' => $this->cp->getVal('algus'),
			'class' => 'formElement',
		));

//	kui on olemas otsekorraldusega hinnad
// OTSEKORRALDUS
		if( count( $my_otsek ) && $_leping != 'tel' ) {
			$_hind = $my_otsek[0]["baashind"];
			switch( $my_otsek[0]["hinna_tyyp"] ) {
				case 0: $_periood = $my_otsek[0]["kestus"].($my_otsek[0]["kestus"]>1?t(" kuud "):t(" kuu "));
					break;
				case 1:	$_periood = $my_otsek[0]["kestus"]." n&auml;dal";
					break;
				case 2:	$_periood = $my_otsek[0]["kestus"]." p&auml;ev";
					break;
				case 3:	$_periood = $my_otsek[0]["kestus"]." number";
					break;
				case 4:	$_periood = $my_otsek[0]["kestus"]." aasta";
					break;
				case 5:	$_periood = $my_otsek[0]["kestus"]." poolkuu";
			}	// switch

			$_checked = ( $_leping == "ok" || !count($my_tavah) || $my_otsek[0]["id"] == $_pikkus ?"checked":"");
			$_okpikkus= $my_otsek[0]["id"];
			$_oktekst = (isset( $lc_expp['LC_EXPP_OKTEKST_'.$_toimetus] )? 'LC_EXPP_OKTEKST_'.$_toimetus : 'LC_EXPP_OKTEKST' );
			$this->vars(array(
				'CHECKED' => $_checked,
				'OKPIKKUS'=> $_okpikkus,
				'PERIOOD' => $_periood,
				'HIND'    => $_hind,
				'OKTEKST' => sprintf( $lc_expp[$_oktekst], $_periood, $_hind ),
			));
			$_otsekorraldus= $this->parse( 'OTSEKORRALDUS' );
		} else {
			$_otsekorraldus= "";
		}

// TELLIMUS
		if( count( $my_tavah ) && $_leping != 'ok' ) {
			$_per_options = array(
				'' => '-------------------------'
			);
			$_check = 0;
			reset( $my_tavah );
			$_temp = current( $my_tavah );
// vb on vaja juurdekasvu kaudu arvutada?
			if( $_temp["hinna_tyyp"] == 3 && $_temp['kestus'] == 1 ) {
//			if ( strncmp( $_aid, "Eesti Ekspress", 14 ) == 0 ) {
				for( $in=1;$in<13;$in++) {
					$_per_options[$_temp["id"]."_$in"] = $in." ".(($in>1)?t("kuud"):t("kuu"));
				}
			} else {
				for( $in=0;$in<count($my_tavah);$in++) {
					if( $my_tavah[$in]["id"] == $_pikkus ) $_check = 1;
					$_kestus = $my_tavah[$in]["kestus"];
					switch( $my_tavah[$in]["hinna_tyyp"] ) {
						case 0: $_kestus.= ($my_tavah[$in]["kestus"]>1)?t(" kuud "):t(" kuu ");
							break;
						case 1:	$_kestus.= ($my_tavah[$in]["kestus"]>1)?t(" n&auml;dalat "):t(" n&auml;dal ");
							break;
						case 2:	$_kestus.= ($my_tavah[$in]["kestus"]>1)?t(" p&auml;eva "):t(" p&auml;ev ");
							break;
						case 3:	$_kestus.= ($my_tavah[$in]["kestus"]>1)?t(" numbrit "):t(" number ");
							break;
						case 4:	$_kestus.= ($my_tavah[$in]["kestus"]>1)?t(" aastat "):t(" aasta ");
							break;
						case 5:	$_kestus.= ($my_tavah[$in]["kestus"]>1)?t(" poolkuud "):t(" poolkuu ");
					}	// switch
					$_kestus.= $my_tavah[$in]["baashind"];
					$_per_options[$my_tavah[$in]["id"]] = $_kestus;
				}	// for
			} // if


			$_periood = html::select( array(
				'name' => 'pikkus',
				'options' => $_per_options,
				'selected' => $_pikkus,
				'class' => 'formElement',
			));
//			if (( (int)count($my_tavah) > 1) || (strncmp( $aid, "Eesti Ekspress", 14 ) == 0)) {
//			if (( (int)count($my_tavah) > 1) || ($_temp["hinna_tyyp"] == 3)) {
				$_yxper	= $this->parse( 'YXPER' );
				$_mituper= '';
/*
			} else {
				$_yxper = '';
				$_kogus = $this->cp->getVal('kogus');
				$this->vars(array(
					'KOGUS'  => ($_kogus == 0? 1 : $_kogus),
				));
				$_mituper = $this->parse( 'MITUPER' );
			}
*/
			$_checked = ($_leping == "tel" || !count($my_otsek) || $_check == 1 ?"checked":"");

			$this->vars(array(
				'CHECKED' => $_checked,
				'PERIOOD' => $_periood,
				'YXPER'   => $_yxper,
				'MITUPER' => $_mituper,
			));			
			$_tellimus     = $this->parse( 'TELLIMUS' );
		} else {
			$_tellimus     = "";
		}

		$_eksemplar	= intval( $this->cp->getVal( 'eksemplar' ));
		if( $_eksemplar < 1 ) $_eksemplar = 1;

		if( !empty( $_kampaania )) {
			$this->vars(array(
				'TEXT' => stripslashes( $_kampaania ),
			));
			$_kampaania	= $this->parse( 'KAMPAANIA' );
		}

		$this->vars(array(
			'ACTION'			=> $_action,
			'PEALKIRI'     => $_pealkiri,
			'TINGIMUSED'   => $_tingimused,
			'KIRJELDUS'    => $_kirjeldus,
			'KAMPAANIA'    => $_kampaania,
			'VEAD'         => $_vead,
			'KUUPAEV'      => $_kuupaev,
			'OTSEKORRALDUS'=> $_otsekorraldus,
			'TELLIMUS'     => $_tellimus,
			'EKSEMPLAR'    => $_eksemplar,
			'PINDEKS'		=> $_pindeks,
		));
		return $this->parse();
	}
	//-- methods --//
	function parsePost() {
		global $lc_expp;
		$vead = array(
			"rid"			=> '',
			"eksemplar"	=>	'LC_EXPP_EKSEMPLAR',
			"algus"		=>	'LC_EXPP_ALGUS',
			"leping"		=>	'LC_EXPP_LEPING',
//			"kogus"		=>	'LC_EXPP_KOGUS',
			"tingimused"=> 'LC_EXPP_TINGIMUSED',
		);
		foreach( $vead as $key => $val ) {
			if( !isset( $GLOBALS['HTTP_POST_VARS'][$key] ) || empty( $GLOBALS['HTTP_POST_VARS'][$key] )) {
				$this->post_errors[] = $lc_expp[$val];
			}
			$name = "_$key";
			$$name = addslashes( $GLOBALS['HTTP_POST_VARS'][$key] );
		}

//		$sql = "SELECT v.pindeks, v.valjaanne, hkkood, hinna_tyyp, 


		$_eksemplar = intval( $_eksemplar);
		switch( $_leping ) {
			case "tel" :
				$_pikkus = $GLOBALS['HTTP_POST_VARS']['pikkus'];
				$_kogus = $GLOBALS['HTTP_POST_VARS']['kogus'];
				break;
			case "ok" :
				$_pikkus = $GLOBALS['HTTP_POST_VARS']['okpikkus'];
				$_kogus = 1;
				break;
		}
		if( strpos($_pikkus, '_') !== false ) {
			$_temp = explode( '_', $_pikkus );
			$_pikkus = intval( $_temp[0] );
			$_ptemp = intval( $_temp[1] );
			$sql = "select h.hinna_tyyp, h.kestus, v.valjaanne, v.toimetus, v.toimtunnus, v.valjaande_nimetus from expp_hind h, expp_valjaanne v where h.id = '{$_pikkus}' AND h.pindeks = '{$_rid}' AND h.lubatud = 'jah' AND v.pindeks = h.pindeks";
			$row = $this->db_fetch_row($sql);
			$_va = $row['valjaande_nimetus'];
			$_tt = $row['toimetus'];
			if( $this->num_rows() > 0 ) {
				$_va = $row['valjaanne'];
				switch( $_algus ) {
					case "ASAP":
					case "CONT":
						$_talgus = date( "Y-m-d", mktime( 0,0,0, date("m"), date("d") + 14, date("Y")));
						$_tlopp = date( "Y-m-d", mktime( 0,0,0, date("m")+$_ptemp, date("d") + 13, date("Y")));
						break;
					default:
						$_talgus = date( "Y-m-d", mktime( 0,0,0, intval(substr($_algus,4,2)), 1, intval(substr($_algus,0,4))));
						$_tlopp = date( "Y-m-d", mktime( 0,0,0, intval(substr($_algus,4,2))+$_ptemp, 0, intval(substr($_algus,0,4))));
				}
				$sql = "select count(*) as arv from expp_ilmumisgraafik where valjaanne = '{$_va}' and ilmumiskpv between '{$_talgus}' and '$_tlopp'";
				$row = $this->db_fetch_row($sql);
				$_kogus = $row['arv'];
			} else {
				$_pikkus = 0;
			}
		} else {
			$sql = "select  v.toimetus, v.toimtunnus, v.valjaande_nimetus from expp_valjaanne v where v.pindeks = '{$_rid}'";
			$row = $this->db_fetch_row($sql);
			$_va = $row['valjaande_nimetus'];
			$_tt = $row['toimetus'];
		}
		$_pikkus = intval( $_pikkus );
		if ( $_pikkus == 0 ) {
			$this->post_errors[] = "viga:".$lc_expp['LC_EXPP_PIKKUS'];
		}
		$_kogus = intval( $_kogus );
		if ( $_kogus == 0 ) {
			$this->post_errors[] = "viga:".$lc_expp['LC_EXPP_KOGUS'];
		}
		if( !empty( $this->post_errors )) 
		return;

		$sql = "INSERT INTO expp_korv SET session='".session_id()."'"
			.", pindeks='{$_rid}'"
			.", eksemplar='{$_eksemplar}'"
			.", algus='{$_algus}'"
			.", leping='{$_leping}'"
			.", pikkus='{$_pikkus}'"
			.", kogus='{$_kogus}'"
			.", time=now()";
		$this->db_query( $sql );

		$this->cp->log( get_class($this), "lisa_korvi", $_rid, $_tt, $_va );
// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
if ($_SERVER['REMOTE_ADDR'] == '62.65.36.186')
{

/*
//	Eesti Ekspress special
//	if (( $rid == 69830 ) and ( $leping == "tel" )) {
	if ( strncmp( $aid, "Eesti Ekspress", 14 ) == 0 and ( $leping == "tel" )) {
$query=<<<query
SELECT id
FROM hinnad
WHERE
	pindeks='$rid' AND
	ok_tava_hind='2'
query;
	$result	= @mysql_db_query( $db_base, $query, $dbh );
if ( @mysql_num_rows( $result ) > 0 ) {
		$row = @mysql_fetch_array( $result );
		switch ( $algus ) {
			case "ASAP":
				$my_begin	= mktime( 0,0,0,date("m"),date("d")+14,date("Y"));
				$my_end		= mktime( 0,0,0,date("m")+$pikkus,date("d")+14,date("Y"));
				break;
			default:
				if (strlen($algus) == 8)
				{
					$my_begin       = mktime( 0,0,0,(int)substr($algus,4,2),(int)substr($algus,6,2),(int)substr($algus,0,4));
					$my_end         = mktime( 0,0,0,$pikkus+(int)substr($algus,4,2),0,(int)substr($algus,0,4));
				}
				else
				{
					$my_begin	= mktime( 0,0,0,(int)substr($algus,4,2),1,(int)substr($algus,0,4));
					$my_end		= mktime( 0,0,0,$pikkus+(int)substr($algus,4,2),0,(int)substr($algus,0,4));
				}
		}
		//die(dbg::dump(date("d.m.Y", $my_begin)));
		$first_day	= (date( "w",$my_begin)>4)?date( "w",$my_begin)-7:date( "w",$my_begin);
		$kogus = ($my_end - $my_begin)/60/60/24+1;
		$kogus = floor(($kogus+$first_day+2) / 7);
		$pikkus = $row["id"];
	} else {
		$pid = algus;
		header( "location: index.php3".make_argc( "id", "pid" ));
		exit;
	}
}	//	Eesti Ekspress special
*/

} 
// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx


		header( "Location: ".aw_ini_get("baseurl")."/tellimine/korv/" );
		exit;
	}
	
	function returnPost() {
		$_aid = $this->cp->getPid( 2 );
		$sql = "SELECT v.toote_nimetus"
				." FROM expp_valjaanne v"
				." WHERE v.valjaande_nimetus='{$_aid}'";
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() > 0 ) {
			header( "Location: ".aw_ini_get("baseurl").'/tellimine/'.urlencode( $row['toote_nimetus'] ).'/' );
		} else {
			header( "Location: ".aw_ini_get("baseurl").'/tellimine/' );
		}
		exit;
	}
}
?>
