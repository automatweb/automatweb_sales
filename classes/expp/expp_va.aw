<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_va.aw,v 1.17 2007/12/12 10:30:22 dragut Exp $
// expp_va.aw - Expp väljaanne 
/*

@classinfo syslog_type=ST_EXPP_VA relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_va extends class_base {
	const AW_CLID = 989;


	var $cy;
	var $cp;
	var $ch;
	var $lang;
	var $clURL;

	function expp_va() {
		$this->lang = aw_global_get("admin_lang_lc");

		$this->init(array(
			"tpldir" => "expp",
			"clid" => CL_EXPP_VA
		));
//		$this->cy = get_instance( CL_EXPP_JAH );
		$this->cp = get_instance( CL_EXPP_PARSE );
		$this->ch = get_instance("cache");

		lc_site_load( "expp", $this );
		$GLOBALS['expp_show_footer'] = 1;
	}

	function show($arr) {
		$_SESSION['expp_kampaania'] = '';
		$retHTML = '';
		if( empty( $this->cp->pids )) {
			return $retHTML;
		}
		$_kampaania = $this->cp->nextPid();
		if( $_kampaania == 'kampaania' ) {
			$retHTML = $this->showKampaania();
			if ( !empty( $retHTML )) {
				return $retHTML;
			}
			header( "Location: ".aw_ini_get("baseurl")."/tellimine/" );
			exit;
		}

		$this->clURL = '/tellimine/';

		$this->cp->pidpos = 1;
		$retHTML =& $this->showValjaanne();
		if ( !empty( $retHTML )) return $retHTML;

		$this->cp->pidpos = 1;
		$retHTML =& $this->showLiikList();
		if ( !empty( $retHTML )) return $retHTML;

		$this->cp->pidpos = 1;
		$retHTML =& $this->showTyypList();
		if ( !empty( $retHTML )) return $retHTML;


// viimne valik
		$this->cp->pidpos = 1;
		return $this->showLiigid();
	}

	//-- methods --//
	function showTyypList() {

		global $lc_expp;

		$retHTML = '';
		$_tyyp = $this->cp->nextPid();
		if( empty( $_tyyp )) return $retHTML;
		if( is_numeric( $_tyyp )) {
			$sql = "SELECT id,nimi FROM expp_tyybid WHERE id = '{$_tyyp}' ORDER by sort ASC";
		} else {
			$_tyyp = addslashes( urldecode( $_tyyp ));
			$sql = "SELECT id,nimi FROM expp_tyybid WHERE nimi = '{$_tyyp}'  ORDER by sort ASC";
		}
		$row = $this->db_fetch_row($sql);
		if (!is_array($row)) {
			return $retHTML;
		}

		$_tyyp_nimi = $row['nimi'];
		$_tyyp_id = $row['id'];

		$_lc_key = 'LC_EXPP_DB_'.strtoupper($_tyyp_nimi);
		$_tyyp_wnimi = ( isset( $lc_expp[$_lc_key] ) ? $lc_expp[$_lc_key] : $_tyyp_nimi );
		$_tyyp_id = $row['id'];
//		$this->clURL .= urlencode( $_tyyp_nimi ).'/';
		$myURL = $this->cp->addYah( array(
				'link' => urlencode( $_tyyp_nimi ),
				'text' => $_tyyp_wnimi
			));

		$_tmp = $this->cp->pidpos;

		$retHTML =& $this->showValjaanne();
		if ( !empty( $retHTML )) return $retHTML;
		$this->cp->pidpos = $_tmp;

		$retHTML =& $this->showLiikList();
		if ( !empty( $retHTML )) return $retHTML;
		$this->cp->pidpos = $_tmp;

		$this->cp->log( get_class($this), "show" );

		$_cache_name = urlencode( $this->lang.'_va_tyyplist_'.$_tyyp_nimi );
		$retHTML = $this->ch->file_get_ts( $_cache_name, time() - 24*3600);
		if( !empty( $retHTML )) {
			return $retHTML;
		}

		$retArr = array();
		$htmlArr = array();
		$coutArr = array();
		$this->read_template("expp_tyyp_list.tpl");

		if( $this->lang != 'et' ) {
			$sql = "SELECT DISTINCT"
				." v.toote_nimetus, l.liik, tr2.nimetus as lang_toode"
				." FROM expp_valjaanne v, expp_liigid l, expp_va_liik vl"
				." LEFT JOIN expp_translate tr2 ON tr2.pindeks = v.pindeks AND tr2.tyyp = 'toode' AND tr2.lang = '{$this->lang}'"
				." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
				." WHERE v.toote_tyyp = '{$_tyyp_id}'"
				." AND h.pindeks is not null"
				." AND l.tyyp_id = '{$_tyyp_id}'"
				." AND vl.toote_nimetus = v.toote_nimetus"
				." AND vl.liik_id = l.id"
				." ORDER BY l.sort ASC, v.toote_nimetus ASC";
		} else {
			$sql = "SELECT DISTINCT"
				." v.toote_nimetus, l.liik"
				." FROM expp_valjaanne v, expp_liigid l, expp_va_liik vl"
				." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
				." WHERE v.toote_tyyp = '{$_tyyp_id}'"
				." AND h.pindeks is not null"
				." AND l.tyyp_id = '{$_tyyp_id}'"
				." AND vl.toote_nimetus = v.toote_nimetus"
				." AND vl.liik_id = l.id"
				." ORDER BY l.sort ASC, v.toote_nimetus ASC";
		}
		$this->db_query($sql);
		while ($row = $this->db_next()) {
			$retArr[$row['liik']][] = $row;
		}
		$_row_count = max( count($retArr), 1);
		$colspan = min($_row_count,aw_ini_get('colspan.tyyp'));
		$this->vars(array(
				'WIDTH'	=> floor( 100 / $colspan )."%",
		));

		for( $i = 0; $i< $colspan; $i++ ) {
			$countArr[$i] = 0;
			$htmlArr[$i] = '';
		}
		foreach( $retArr as $key => $val ) {
			$_idx = 0;
			for( $i = 0; $i< $colspan; $i++ ) {
				if( $countArr[$i] < $countArr[$_idx] ) {
					$_idx = $i;
				}
			}
/*
			ksort( $countArr );
			asort( $countArr );
			reset( $countArr );
			$_idx = key( $countArr );
*/
			$countArr[$_idx] += count($val);
			$_tmp = '';
			foreach( $val as $key1 => $val1 ) {
				if( $this->lang != 'et' ) {
					$_laid = ( isset( $val1['lang_toode'] ) && !empty( $val1['lang_toode'] ) ? $val1['lang_toode'] : htmlentities($val1['toote_nimetus']) );
				} else {
					$_laid = $val1['toote_nimetus'];
				}
				$this->vars(array(
					'VAHE' => ($key1 == 0 ? '' : $this->parse( 'VAHE' )),
					'url' => $myURL.urlencode( $val1['toote_nimetus'] ),
					'text' => $_laid,
				));
				$_tmp .= $this->parse('LINE');
			}
			$_lc_key = 'LC_EXPP_DB_'.strtoupper($key);
			$_lc_key = str_replace(' ', '_', $_lc_key);
			$_lc_key = htmlentities($_lc_key);
			$_wnimi = ( isset( $lc_expp[$_lc_key] ) ? $lc_expp[$_lc_key] : $key );

			$this->vars(array(
				'text' => $_wnimi,
				'url' => $myURL.urlencode( $key ),
				'LINE' => $_tmp
			));
			$htmlArr[$_idx] .= $this->parse('SISU_BOX');
		}
		$_tmp = '';
		foreach( $htmlArr as $val ) {
			$this->vars(array(
				'SISU_BOX' => $val,
			));
			$_tmp .= $this->parse('VISU_BOX');
		}
		$this->vars(array(
			'tyyp' => $_tyyp_nimi,
			'colspan' => $colspan,
			'VISU_BOX' => $_tmp
		));
		$retHTML = $this->parse();
		$this->ch->file_set( $_cache_name, $retHTML );
		return $retHTML;
	}

	function showLiikList() {

		global $lc_expp;
		$retHTML = '';

		$_liik = $this->cp->nextPid();

		if( empty( $_liik )) return $retHTML;

		if( is_numeric( $_liik )) {
		$sql = "SELECT DISTINCT l.id, l.liik, l.tyyp_id FROM expp_liigid l"
			." LEFT JOIN expp_va_liik vl ON vl.liik_id = l.id"
			." left join expp_valjaanne v ON v.toote_nimetus = vl.toote_nimetus"
			." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
			." WHERE vl.liik_id is not null and v.toote_nimetus is not null and h.pindeks is not null and"
			." l.esilehel = 1 AND l.id = '{$_liik}' ORDER BY l.sort ASC, l.id";
//			$sql = "SELECT id,tyyp_id,liik FROM expp_liigid WHERE id = '{$_liik}' ORDER BY sort asc";
		} else {
			$_liik = addslashes(urldecode( $_liik ));
//			$sql = "SELECT id,tyyp_id,liik FROM expp_liigid WHERE liik = '{$_liik}' ORDER BY sort asc";

// kommenteeris v2lja Rain Viigipuu 03.08.2007 parandamaks liikide kattumise probleem erinevate tyypide all
/*
			$sql = "SELECT DISTINCT l.id, l.liik, l.tyyp_id FROM expp_liigid l"
				." LEFT JOIN expp_va_liik vl ON vl.liik_id = l.id"
				." left join expp_valjaanne v ON v.toote_nimetus = vl.toote_nimetus"
				." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
				." WHERE vl.liik_id is not null and v.toote_nimetus is not null and h.pindeks is not null and"
				." l.esilehel = 1 AND l.liik = '{$_liik}' ORDER BY l.sort ASC, l.id";
*/

			// lisas Rain Viigipuu 03.08.2007, parandamaks liikide kattumise probleem erinevate tyypide all:
			$_tyyp = addslashes(urldecode( $this->cp->pids[2] )); 
			$sql = "SELECT DISTINCT l.id, l.liik, l.tyyp_id, expp_tyybid.nimi FROM expp_liigid l"
				." LEFT JOIN expp_va_liik vl ON vl.liik_id = l.id"
				." left join expp_valjaanne v ON v.toote_nimetus = vl.toote_nimetus"
				." left join expp_tyybid on expp_tyybid.id = l.tyyp_id"
				." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
				." WHERE vl.liik_id is not null and v.toote_nimetus is not null and h.pindeks is not null and"
				." l.esilehel = 1 AND l.liik = '{$_liik}' AND expp_tyybid.nimi = '{$_tyyp}' ORDER BY l.sort ASC, l.id";

		}

		$row = $this->db_fetch_row($sql);
		if (!is_array($row)) {
			return $retHTML;
		}

		$_liik_nimi = $row['liik'];

		$_lc_key = 'LC_EXPP_DB_'.str_replace( ' ', '_', htmlentities( strtoupper($_liik_nimi) ) );
		$_liik_wnimi = ( isset( $lc_expp[$_lc_key] ) ? $lc_expp[$_lc_key] : $_liik_nimi );
		$_liik_id = $row['id'];
		$_tyyp_id = $row['tyyp_id'];

//		$this->clURL .= urlencode( $_liik_nimi ).'/';
		$myURL = $this->cp->addYah( array(
				'link' => urlencode( $_liik_nimi ),
				'text' => $_liik_wnimi
			));
		$_tmp = $this->cp->pidpos;
		$retHTML =& $this->showValjaanne();
		if ( !empty( $retHTML )) return $retHTML;
		$this->cp->pidpos = $_tmp;

		$this->cp->log( get_class($this), "show" );

		// lisas Rain Viigipuu 03.08.2007 et cache faili nime kokku panemisel peetaks meeles ka tyypi:
		$_cache_name = urlencode( $this->lang.'_va_liiklist_'.$_tyyp_id.'_'.$_liik_nimi );
		$retHTML = $this->ch->file_get_ts( $_cache_name, time() - 24*3600);
		if( !empty( $retHTML )) {
			return $retHTML;
		}

		$retArr = array();
		$htmlArr = array();
		$coutArr = array();

		$this->read_template("expp_liik_list.tpl");


		if( $this->lang != 'et' ) {
			$sql = "SELECT DISTINCT"
				." v.toote_nimetus, tr2.nimetus as lang_toode"
				." FROM expp_valjaanne v, expp_va_liik vl"
				." LEFT JOIN expp_translate tr2 ON tr2.pindeks = v.pindeks AND tr2.tyyp = 'toode' AND tr2.lang = '{$this->lang}'"
				." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
				." WHERE vl.liik_id = '{$_liik_id}'"
				." AND h.pindeks is not null"
				." AND vl.toote_nimetus = v.toote_nimetus"
				." ORDER BY v.toote_nimetus ASC";
		} else {
			$sql = "SELECT DISTINCT"
				." v.toote_nimetus"
				." FROM expp_valjaanne v, expp_va_liik vl"
				." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
				." WHERE vl.liik_id = '{$_liik_id}'"
				." AND h.pindeks is not null"
				." AND vl.toote_nimetus = v.toote_nimetus"
				." ORDER BY v.toote_nimetus ASC";
		}
		$this->db_query($sql);
		$_row_count = max( $this->num_rows(), 1);
		$colspan = min( $_row_count, aw_ini_get('colspan.liik') );
		$this->vars(array(
				'WIDTH'	=> floor( 100 / $colspan )."%",
		));
		$_row_count = ceil( $_row_count / $colspan );
		for( $i = 0; $i< $colspan; $i++ ) {
			$htmlArr[$i] = '';
		}
		$_idx = 0;
		$i = 0;
		while ($row = $this->db_next()) {
			if( $this->lang != 'et' ) {
				$_laid = ( isset( $row['lang_toode'] ) && !empty( $row['lang_toode'] ) ? $row['lang_toode'] : htmlentities($row['toote_nimetus']) );
			} else {
				$_laid = $row['toote_nimetus'];
			}
			$this->vars(array(
				'url' => $myURL.urlencode($row['toote_nimetus']),
				'text' => $_laid,
			));
			$htmlArr[$_idx] .= $this->parse('LINE');
			$i++;
			if( $i >= $_row_count ) {
				$_idx++;
				$i = 0;
			}
		}
		$_tmp = '';
		foreach( $htmlArr as $val ) {
			$this->vars(array(
				'SISU_BOX' => $val
			));
			$_tmp .= $this->parse('VISU_BOX');
		}
		$this->vars(array(
			'liik' => $_liik_nimi,
			'colspan' => $colspan,
			'VISU_BOX' => $_tmp
		));

		$retHTML = $this->parse();
		$this->ch->file_set( $_cache_name, $retHTML );
		return $retHTML;
	}
	
	function showValjaanne() {

		global $lc_expp;

		$retHTML = '';
		$__aid = $this->cp->nextPid();

//		$_ch_site = str_replace( '%', '#', urlencode( $__aid ))."_site";
		$_ch_logo = str_replace( '%', '#', urlencode( $__aid ));
		if( empty( $__aid )) return $retHTML;
// $this->ch->full_flush(); 

		$cl = get_instance( CL_EXPP_SITE_LOGO );
		$cl2 = get_instance( CL_EXPP_SITE_CONTENT );
		$cl->register( $_ch_logo );
		$cl2->register( $_ch_logo );

		$_aid = addslashes( $__aid );
		$_vanne = array();
		if( $this->lang != 'et' ) {
			$sql = "SELECT distinct v.pindeks, v.valjaande_nimetus, v.veebi_kirjeldus, tr1.nimetus as lang_va, tr2.nimetus as lang_toode"
					." FROM expp_valjaanne v"
					." LEFT JOIN expp_translate tr1 ON tr1.pindeks = v.pindeks AND tr1.tyyp = 'va' AND tr1.lang = '{$this->lang}'"
					." LEFT JOIN expp_translate tr2 ON tr2.pindeks = v.pindeks AND tr2.tyyp = 'toode' AND tr2.lang = '{$this->lang}'"
					." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
					." WHERE v.toote_nimetus='{$_aid}'"
					." and h.pindeks is not null"
					." ORDER BY v.valjaande_nimetus ASC";
		} else {
			$sql = "SELECT distinct v.pindeks, v.valjaande_nimetus, v.veebi_kirjeldus"
					." FROM expp_valjaanne v"
					." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
					." WHERE v.toote_nimetus='{$_aid}'"
					." and h.pindeks is not null"
					." ORDER BY v.valjaande_nimetus ASC";
		}
		$this->db_query( $sql );
		if( $this->num_rows() == 0 ) return $retHTML;
		while ( $row = $this->db_next()) {
			$_vanne[$row['pindeks']] = $row;
		}

		if( $this->lang != 'et' ) {
			$_laid = reset( $_vanne );
			$_laid = ( isset( $_laid['lang_toode'] ) && !empty( $_laid['lang_toode'] ) ? $_laid['lang_toode'] : htmlentities($__aid) );
		} else {
			$_laid = $__aid;
		}
		$this->cp->log( get_class($this), "show", '', '', $__aid );

		$_cache_name = urlencode( $this->lang.'_va_valjaanne_'.$__aid );
		$retHTML = $this->ch->file_get_ts( $_cache_name, time() - 24*3600);
		if( !empty( $retHTML )) {
//			$this->clURL .= urlencode( $__aid ).'/';
			$myURL = $this->cp->addYah( array(
					'link' => urlencode( $__aid ),
					'text' => $_laid
				));
			return $retHTML;
		}


		////
		// added h.algus DESC sorting rule to this query, it should fix the problem, when there are multiple prices and the wrong one is showed
		// in prices view. --dragut 12.12.2007
		$sql = "SELECT v.pindeks, h.id, h.hinna_tyyp, h.kestus, h.baashind, h.juurdekasv, h.hinna_liik, h.hinna_kirjeldus"
			." FROM expp_valjaanne v, expp_hind h"
			." WHERE v.pindeks = h.pindeks"
			." AND h.lubatud = 'jah'"
			." AND v.toote_nimetus='{$_aid}'"
			." AND (h.algus+0 = 0 OR h.algus <= now())"
			." AND (h.lopp+0 = 0 OR h.lopp >= now())"
//			." AND h.hinna_liik not like '%SALAJANE%'"
			." AND h.hinna_liik in ( 'OKHIND', 'AVALIK_OKHIND', 'TAVAHIND', 'AVALIK_TAVAHIND' )"
			." ORDER BY v.valjaande_nimetus ASC, h.hinna_liik ASC, h.kestus ASC";
		$this->db_query( $sql );

		if( $this->num_rows() == 0 ) return $retHTML;
//		$this->clURL .= urlencode( $__aid ).'/';
		$myURL = $this->cp->addYah( array(
				'link' => urlencode( $__aid ),
				'text' => $_laid
			));
/*
		if( $this->lang != 'et' ) {
			$_laid = reset( $_vanne );
			$_laid = ( isset( $_laid['lang_toode'] ) && !empty( $_laid['lang_toode'] ) ? $_laid['lang_toode'] : $__aid );
		} else {
			$_laid = $__aid;
		}
		$myURL = $this->cp->addYah( array(
				'link' => urlencode( $__aid ),
				'text' => $_laid,
			));
*/

		$myURL = aw_ini_get("tell_dir").'telli/';
		$this->read_template("expp_va.tpl");
		$_vad			= array();
		$_reklaam	= '';
		while ( $row = $this->db_next()) {
			$_kamp = str_replace( array('OKHIND','TAVAHIND', '_'), '', $row["hinna_liik"] );
			$_temp_liik = $row["pindeks"].'_'.$_kamp;
			$row['baashind']  = sprintf( "%1.0f", $row['baashind'] );
			if( !isset( $_vad[$_temp_liik]))	{
				$_vad[$_temp_liik] = array();
				$_vad[$_temp_liik]["ht"] = 0;
				$_vad[$_temp_liik]["pindeks"] = $row["pindeks"];
				$_vad[$_temp_liik]["kamp"] = (empty( $_kamp )?'':'KAMP');
				$_vad[$_temp_liik]["hinna_kirjeldus"] = stripslashes( $row["hinna_kirjeldus"] );
			}
			$_vad[$_temp_liik]["ht"] += $row["hinna_tyyp"];
//			$_vad[$_temp_liik]["kampaania"] = $row["kampaania"];
			switch( $row["hinna_tyyp"] ) {
				case 0:
						if ( $this->lang != 'et' ) {
							$_text = $lc_expp['LC_EXPP_VA_KUU'];
							$_textn = $lc_expp['LC_EXPP_VA_KUUD'];
						} else {
							$_text = 'kuu';
							$_textn = 'kuud';
						}
					break;
				case 3:
						if ( $this->lang != 'et' ) {
							$_text = $lc_expp['LC_EXPP_VA_NUMBRI_HIND'];
							$_textn = $lc_expp['LC_EXPP_VA_NUMBRI_HIND'];
						} else {
							$_text = ' numbri hind';
							$_textn = ' numbri hind';
						}
					break;
				default:
					echo $row["hinna_tyyp"];
					continue 2;
			}
			switch( $row["hinna_liik"] ) {
				case 'OKHIND':
				case 'AVALIK_OKHIND':
				case 'SALAJANE_OKHIND':
					if ( $this->lang != 'et' ) {
						$lc_otsekorraldus = $lc_expp['LC_EXPP_VA_OTSEKORRALDUS'];
					} else {
						$lc_otsekorraldus = 'otsekorraldus';
					}
					$_vad[$_temp_liik]['hinnad'][0] = array(
							'h' => $row["baashind"],
							'id' => $row["id"],
							'head' => $lc_otsekorraldus
						);
					break;
/*
				case 'OK_TAVA':
					$_vad[$row["pindeks"]]['hinnad'][0] = array(
							'h' => $row["baashind"],
							'id' => $row["id"],
							'head' => 'otsekorraldus'
						);
*/
				case 'TAVAHIND':
				case 'AVALIK_TAVAHIND':
				case 'SALAJANE_TAVAHIND':
				default:
					$_vad[$_temp_liik]['hinnad'][$row["kestus"]] = array(
							'h' => $row["baashind"],
							'id' => $row["id"],
							'head' => ( $row["kestus"] == 1 ? $_text : $_textn )
						);
/*
					if ( $row["juurdekasv"] > 0 ) {
						for( $ii = $row["kestus"]+1; $ii < 13; $ii++ ){
							$_vad[$row["pindeks"]]['hinnad'][$ii] = array(
								'h' => sprintf( "%1.0f", $row["baashind"]+$row["juurdekasv"]*($ii-$row["kestus"])),
								'id' => $row["id"].'-'.$ii,
								'head' => ( $ii == 1 ? $_text : $_textn )
							);
						}
					}
*/
			}
		}
		$_cell = array();
		foreach( $_vad as $__pindeks => $val ) {
			$_pindeks = $val['pindeks'];
			$_kamp = $val['kamp'];
/*
			if( empty( $_reklaam )) {
//				$_reklaam = stripslashes( $_vanne[$_pindeks]["veebi_kirjeldus"] );
//				$_reklaam = stripslashes( $_vanne[$_pindeks]["kampaania"] );
				$_reklaam = stripslashes( $_vanne[$_pindeks]['veebi_kirjeldus'] );
			}
*/
			$_url = urlencode( $_vanne[$_pindeks]['valjaande_nimetus'] );
			$_title = ( isset( $_vanne[$_pindeks]['lang_va'] ) && !empty( $_vanne[$_pindeks]['lang_va'] ) ? $_vanne[$_pindeks]['lang_va'] : htmlentities($_vanne[$_pindeks]['valjaande_nimetus']) );
			$_price1 = '';
			$_price2 = '';
			if( !empty( $val['hinnad'] )) {
				ksort( $val['hinnad'] );
				$_count = count( $val['hinnad'] );
				foreach( $val['hinnad'] as $_kuu => $val1 ) {
					$this->vars(array(
						'hinna_text' => ( $_kuu == 0? ($_count > 13 ? substr( $val1['head'], 0, 2 ) : $val1['head'] ) : ( $_count > 13 ? $_kuu.substr( $val1['head'], 0, 1 ) : $_kuu.$val1['head'] ))
					));
					$_price1 .= $this->parse( $_kamp.'PRICE1' );

					$this->vars(array(
						'hinna_link' => $myURL.$_url.'?pikkus='.$val1['id'],
						'hinna_sum' => $val1['h'],
					));
					$_price2 .= $this->parse( $_kamp.'PRICE2' );
				}
			}
			$_kirjeldus = htmlentities( stripslashes( $_vanne[$_pindeks]['veebi_kirjeldus'] ) );
			if( isset( $val['hinna_kirjeldus'] )) {
				if( !empty( $_kirjeldus )) {
					$_kirjeldus .= '<br />';
				}
				$_kirjeldus .= stripslashes( nl2br( $val['hinna_kirjeldus'] ) );
			}

			$this->vars(array(
				'nimi' => $_title,
//				'kirjeldus' => $_vanne[$_pindeks]['kampaania'],
				'kirjeldus' => $_kirjeldus,
				$_kamp.'PRICE1' => $_price1,
				$_kamp.'PRICE2' => $_price2
			));
			$_cell[(empty($_kamp)?1:0)] .= $this->parse($_kamp.'CELL');
		}

////
// reklaamikast
/*
		if( !empty( $_reklaam )) {
			$this->vars(array(
				'text' => $_reklaam
			));
			$_reklaam = $this->parse('REKLAAM');
		}
*/
////
// ??? mida teha kirjeldustega?
		$_desc = '';
////
// lõpuks viimane jupats

		$this->vars(array(
			'colspan' => $_colspan,
			'toode' => $_GET['id'],
			'REKLAAM' => $_reklaam,
			'CELL' => $_cell[0].$_cell[1],
			'DESC' => $_desc,
		));
		$preview = $this->parse();

		if( empty( $preview )) return $preview;

		$_preview = '';
/**/
		$cl = new object_list(array(
			"class_id" => CL_DOCUMENT,
			"name" => urlencode( $__aid ).".site",
			"lang_id" => array(),
			"site_id" => array(),
		));

		if( $cl->count() > 0 ) {
			$cd = $cl->begin();
			$dcx = get_instance("doc_display");
			$this->read_tpl(array(
				$dcx->gen_preview(array(
					"docid" => $cd->id(),
	//				"leadonly" => 1
				))
			));
			if( $this->template_has_var("site")) {
				$this->vars( array(
					"site" => $preview,
				));
				$_preview = $this->parse();
			}
			$retHTML = $dcx->gen_preview(array(
					"docid" => $cd->id(),
					"leadonly" => 1
				));
			if( !empty( $retHTML )) {
				$this->ch->file_set( $_ch_logo, $retHTML );
			} else {
				$this->ch->file_invalidate( $_ch_logo );
			}
		}
/**/

		$retHTML = ( empty( $_preview )? $preview : $_preview );
		$this->ch->file_set( $_cache_name, $retHTML );
		return $retHTML;
	}
	
	function showLiigid() {

		global $lc_expp;

		$retHTML = '';

		$cy = get_instance( CL_EXPP_JAH );
//		$myURL = $cy->getURL();

		$this->cp->log( get_class($this), "show" );

		$_cache_name = urlencode( $this->lang.'_va_liigid' );
		$retHTML = $this->ch->file_get_ts( $_cache_name, time() - 24*3600);

		if( !empty( $retHTML )) {
			return $retHTML;
		}

		$this->read_template("expp_liigid.tpl");
		$retLiik = '';
		$tempLiik = '';
		$retTyyp = '';
		$lastTyyp = '';
		$isCount = 0;
		$sql = "SELECT DISTINCT l.id, l.liik, t.nimi as tyyp FROM expp_liigid l, expp_tyybid t"
			." LEFT JOIN expp_va_liik vl ON vl.liik_id = l.id"
			." left join expp_valjaanne v ON v.toote_nimetus = vl.toote_nimetus"
			." left join expp_hind h ON h.pindeks = v.pindeks AND h.hinna_liik in ('TAVAHIND', 'AVALIK_TAVAHIND', 'OKHIND', 'AVALIK_OKHIND' ) AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now()) AND h.lubatud = 'jah'"
			." WHERE vl.liik_id is not null and v.toote_nimetus is not null and h.pindeks is not null and"
			." l.tyyp_id = t.id AND l.esilehel = 1 ORDER BY t.sort ASC, t.id, l.sort ASC, l.id";
		$this->db_query( $sql );
		while ($row = $this->db_next()) {
			if( $lastTyyp != $row['tyyp'] ) {
				if( !empty( $lastTyyp ) && !empty( $tempLiik )) {
					if( $this->lang != 'et' ) {
						$_lc_key = 'LC_EXPP_DB_'.strtoupper($lastTyyp);
						$_tyyp_wnimi = ( isset( $lc_expp[$_lc_key] ) ? $lc_expp[$_lc_key] : $lastTyyp );
					} else {
						$_tyyp_wnimi = $lastTyyp;
					}

					$this->vars(array(
						'VAHE' => ($isCount == 0?'':$this->parse('VAHE')),
						'text' => $_tyyp_wnimi
					));
					$retTyyp .= $this->parse('TYYP');

					$this->vars(array(
						'LINE' => $tempLiik
					));

					$retLiik .= $this->parse('SISU');
					$isCount++;
				}
				$lastTyyp = $row['tyyp'];
				$tempLiik = '';
			}
			if ( $this->lang != 'et' ) {
				$_lc_key = 'LC_EXPP_DB_'.strtoupper($row['liik']);
				$_lc_key = str_replace(' ', '_', $_lc_key);
				$_lc_key = htmlentities($_lc_key);
				$_liik_wnimi = ( isset( $lc_expp[$_lc_key] ) ) ? $lc_expp[$_lc_key] : $row['liik'];
			} else {
				$_liik_wnimi = $row['liik'];
			}
			$this->vars(array(
				'url' => $this->clURL.urlencode($row['tyyp']).'/'.urlencode($row['liik']),
			//	'text' => $row['liik']
				'text' => $_liik_wnimi
			));
			$tempLiik .= $this->parse('LINE');
		}
		if( !empty( $lastTyyp ) && !empty( $tempLiik )) {
			if( $this->lang != 'et' ) {
				$_lc_key = 'LC_EXPP_DB_'.strtoupper($lastTyyp);
				$_tyyp_wnimi = ( isset( $lc_expp[$_lc_key] ) ? $lc_expp[$_lc_key] : $lastTyyp );
			} else {
				$_tyyp_wnimi = $lastTyyp;
			}
			$this->vars(array(
				'VAHE' => (empty($lastTyyp)?'':$this->parse('VAHE')),
				'text' => $_tyyp_wnimi
			));
			$retTyyp .= $this->parse('TYYP');
			$this->vars(array(
				'LINE' => $tempLiik
			));
			$retLiik .= $this->parse('SISU');
		}

		$this->vars(array(
			'TYYP' => $retTyyp,
			'SISU' => $retLiik
		));

		$retHTML = $this->parse();
		$this->ch->file_set( $_cache_name, $retHTML );
		return $retHTML;
	}

	function showKampaania() {
		global $lc_expp;

		$retHTML = '';
		$_kampaania = addslashes( $this->cp->nextPid());
		if( empty( $_kampaania )) return $retHTML;

		if( $this->lang != 'et' ) {
			$sql = "SELECT distinct v.pindeks, v.valjaande_nimetus, v.veebi_kirjeldus, v.toote_nimetus, tr1.nimetus as lang_va, tr2.nimetus as lang_toode"
					." FROM expp_hind h, expp_valjaanne v"
					." LEFT JOIN expp_translate tr1 ON tr1.pindeks = v.pindeks AND tr1.tyyp = 'va' AND tr1.lang = '{$this->lang}'"
					." LEFT JOIN expp_translate tr2 ON tr2.pindeks = v.pindeks AND tr2.tyyp = 'toode' AND tr2.lang = '{$this->lang}'"
					." WHERE h.kampaania = '{$_kampaania}'"
					." and h.pindeks = v.pindeks"
					." AND h.hinna_liik in ('SALAJANE_TAVAHIND', 'SALAJANE_OKHIND')"
					." AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now())"
					." AND h.lubatud = 'jah'"
					." ORDER BY v.valjaande_nimetus ASC";
		} else {
			$sql = "SELECT distinct v.pindeks, v.valjaande_nimetus, v.veebi_kirjeldus, v.toote_nimetus"
					." FROM expp_hind h, expp_valjaanne v"
					." WHERE h.kampaania = '{$_kampaania}'"
					." and h.pindeks = v.pindeks"
					." AND h.hinna_liik in ('SALAJANE_TAVAHIND', 'SALAJANE_OKHIND')"
					." AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now())"
					." AND h.lubatud = 'jah'"
					." ORDER BY v.valjaande_nimetus ASC";
		}
		$_vanne = array();
		$this->db_query( $sql );
		if( $this->num_rows() == 0 ) return $retHTML;
		$_SESSION['expp_kampaania'] = $_kampaania;
		while ( $row = $this->db_next()) {
			$_vanne[$row['pindeks']] = $row;
		}
		$_laid = reset( $_vanne );
		$_aid = $_laid['toote_nimetus'];
		$__aid = stripslashes( $_aid );
		if( $this->lang != 'et' ) {
			$_laid = ( isset( $_laid['lang_toode'] ) && !empty( $_laid['lang_toode'] ) ? $_laid['lang_toode'] : $__aid );
		} else {
			$_laid = $__aid;
		}
		$_ch_logo = str_replace( '%', '#', urlencode( $__aid ));

		$cl = get_instance( CL_EXPP_SITE_LOGO );
		$cl2 = get_instance( CL_EXPP_SITE_CONTENT );
		$cl->register( $_ch_logo );
		$cl2->register( $_ch_logo );

		$myURL = $this->cp->addYah( array(
				'link' => urlencode( $__aid ),
				'text' => $_laid
			));

		$this->cp->log( get_class($this), "show_{$_kampaania}", '', '', $__aid );

		$_cache_name = urlencode( $this->lang.'_va_valjaanne_'.$_kampaania );
//		$retHTML = $this->ch->file_get_ts( $_cache_name, time() - 24*3600);
		if( !empty( $retHTML )) {
			return $retHTML;
		}

		$sql = "SELECT v.pindeks, h.id, h.hinna_tyyp, h.kestus, h.baashind, h.juurdekasv, h.hinna_liik, h.hinna_kirjeldus"
			." FROM expp_valjaanne v, expp_hind h"
			." WHERE h.kampaania = '{$_kampaania}'"
			." and h.pindeks = v.pindeks"
			." AND h.hinna_liik in ('SALAJANE_TAVAHIND', 'SALAJANE_OKHIND')"
			." AND (h.algus+0 = 0 OR h.algus <= now()) AND (h.lopp+0 = 0 OR h.lopp >= now())"
			." AND h.lubatud = 'jah'"
			." AND v.toote_nimetus='{$_aid}'"
			." ORDER BY v.valjaande_nimetus ASC, h.hinna_liik ASC, h.kestus ASC";
		$this->db_query( $sql );
		if( $this->num_rows() == 0 ) return $retHTML;

		$myURL = aw_ini_get("tell_dir").'telli/';
		$this->read_template("expp_va.tpl");
		$_vad			= array();
		$_reklaam	= '';
		while ( $row = $this->db_next()) {
			$_kamp = str_replace( array('OKHIND','TAVAHIND', '_'), '', $row["hinna_liik"] );
			$_temp_liik = $row["pindeks"].'_'.$_kamp;

			$row['baashind']  = sprintf( "%1.0f", $row['baashind'] );
			if( !isset( $_vad[$_temp_liik]))	{
				$_vad[$_temp_liik] = array();
				$_vad[$_temp_liik]["ht"] = 0;
				$_vad[$_temp_liik]["pindeks"] = $row["pindeks"];
				$_vad[$_temp_liik]["kamp"] = (empty( $_kamp )?'':'KAMP');
				$_vad[$_temp_liik]["hinna_kirjeldus"] = stripslashes( $row["hinna_kirjeldus"] );
			}
			$_vad[$_temp_liik]["ht"] += $row["hinna_tyyp"];
			switch( $row["hinna_tyyp"] ) {
				case 0:
						$_text = t('kuu');
						$_textn = t('kuud');
					break;
				case 1:
						$_text = t(' n&auml;dal');
						$_textn = t(' n&auml;dalat');
					break;
				case 3:
						$_text = ' numbri hind';
						$_textn = ' numbri hind';
					break;
				default:
					echo $row["hinna_tyyp"];
					continue 2;
			}
			switch( $row["hinna_liik"] ) {
				case 'OKHIND':
				case 'AVALIK_OKHIND':
				case 'SALAJANE_OKHIND':
					$_vad[$_temp_liik]['hinnad'][0] = array(
							'h' => $row["baashind"],
							'id' => $row["id"],
							'head' => 'otsekorraldus'
						);
					break;
				case 'TAVAHIND':
				case 'AVALIK_TAVAHIND':
				case 'SALAJANE_TAVAHIND':
				default:
					$_vad[$_temp_liik]['hinnad'][$row["kestus"]] = array(
							'h' => $row["baashind"],
							'id' => $row["id"],
							'head' => ( $row["kestus"] == 1 ? $_text : $_textn )
						);
			}
		}
		$_cell = '';
		foreach( $_vad as $__pindeks => $val ) {
			$_pindeks = $val['pindeks'];
			$_kamp = $val['kamp'];

			$_url = urlencode( $_vanne[$_pindeks]['valjaande_nimetus'] );
			$_title = ( isset( $_vanne[$_pindeks]['lang_va'] ) && !empty( $_vanne[$_pindeks]['lang_va'] ) ? $_vanne[$_pindeks]['lang_va'] : $_vanne[$_pindeks]['valjaande_nimetus'] );
			$_price1 = '';
			$_price2 = '';
			if( !empty( $val['hinnad'] )) {
				ksort( $val['hinnad'] );
				$_count = count( $val['hinnad'] );
				foreach( $val['hinnad'] as $_kuu => $val1 ) {
					$this->vars(array(
						'hinna_text' => ( $_kuu == 0? ($_count > 13 ? substr( $val1['head'], 0, 2 ) : $val1['head'] ) : ( $_count > 13 ? $_kuu.substr( $val1['head'], 0, 1 ) : $_kuu.$val1['head'] ))
					));
					$_price1 .= $this->parse( $_kamp.'PRICE1' );

					$this->vars(array(
						'hinna_link' => $myURL.$_url.'?pikkus='.$val1['id'], // .'&kampaania='.urlencode( $_kampaania ),
						'hinna_sum' => $val1['h'],
					));
					$_price2 .= $this->parse( $_kamp.'PRICE2' );
				}
			}
			$_kirjeldus = stripslashes( $_vanne[$_pindeks]['veebi_kirjeldus'] );
			if( isset( $val['hinna_kirjeldus'] )) {
				if( !empty( $_kirjeldus )) {
					$_kirjeldus .= '<br />';
				}
				$_kirjeldus .= stripslashes( $val['hinna_kirjeldus'] );
			}

			$this->vars(array(
				'nimi' => $_title,
				'kirjeldus' => $_kirjeldus,
				$_kamp.'PRICE1' => $_price1,
				$_kamp.'PRICE2' => $_price2
			));
			$_cell .= $this->parse($_kamp.'CELL');
		}
		$_desc = '';
	
		////
		// kampaania kohta k2iv lisainfo, lisatakse expp_kampaania objekti kaudu --dragut
		$kampaania_info = $this->db_fetch_row("select * from expp_kampaania where nimetus = '".$_kampaania."'");

		if (!empty($kampaania_info))
		{
			$this->vars(array(
				'kampaania_kirjeldus' => $kampaania_info['kirjeldus'],
				'kampaania_pilt_url' => $kampaania_info['pilt']
			));
			$kampaania_str = $this->parse('KAMPAANIA');
		}
////
// lõpuks viimane jupats
	
		$this->vars(array(
			'colspan' => $_colspan,
			'toode' => $_GET['id'],
			'REKLAAM' => $_reklaam,
			'CELL' => $_cell,
			'DESC' => $_desc,
			'KAMPAANIA' => $kampaania_str,
		));
		$preview = $this->parse();
		if( empty( $preview )) return $preview;

		$_preview = '';

		$retHTML = ( empty( $_preview )? $preview : $_preview );
		$this->ch->file_set( $_cache_name, $retHTML );
		return $retHTML;
	}
}
?>
