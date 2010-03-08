<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_isik.aw,v 1.8 2007/11/23 07:18:28 dragut Exp $
// expp_isik.aw - Expp isik 
/*

@classinfo syslog_type=ST_EXPP_ISIK relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/
class expp_isik extends class_base {

	var $cy;
	var $cp;
	var $post_errors = array();
	var $post_arr = array();
	var $sql_sent = array();

var $maakonnad = array(
	""									=> "----------------------",
	"Harjumaa"						=>	"Harjumaa",               
	"Hiiumaa"						=>	"Hiiumaa",                
	"Ida-Virumaa"					=>	"Ida-Virumaa",            
	"J&otilde;gevamaa"			=>	"J&otilde;gevamaa",       
	"J&auml;rvamaa"				=>	"J&auml;rvamaa",          
	"L&auml;&auml;nemaa"			=>	"L&auml;&auml;nemaa",     
	"L&auml;&auml;ne-Virumaa"	=>	"L&auml;&auml;ne-Virumaa",
	"P&otilde;lvamaa"				=>	"P&otilde;lvamaa",        
	"P&auml;rnumaa"				=>	"P&auml;rnumaa",          
	"Raplamaa"						=>	"Raplamaa",               
	"Saaremaa"						=>	"Saaremaa",               
	"Tartumaa"						=>	"Tartumaa",               
	"Valgamaa"						=>	"Valgamaa",               
	"Viljandimaa"					=>	"Viljandimaa",            
	"V&otilde;rumaa"				=>	"V&otilde;rumaa"          
);

var $telliitems =	array (
	"tyyp"	=> array(
		"ok_era"	=> 1,
		"ok_ari"	=> 1,
		"mysql" => "tyyp",
		"error"	=> "LC_EXPP_ERR_TYYP",
	),
	"firmanimi"	=> array(
		"ok_era"	=> 0,
		"ok_ari"	=> 1,
		"mysql" => "firmanimi",
		"error"	=> "LC_EXPP_ERR_FIRMANIMI",
	),
	"eesnimi"	=> array(
		"ok_era"	=> 1,
		"ok_ari"	=> 1,
		"mysql" => "eesnimi",
		"error"	=> "LC_EXPP_ERR_EESNIMI",
	),
	"perenimi"	=>  array(
		"ok_era"	=> 1,
		"ok_ari"	=> 1,
		"mysql" => "perenimi",
		"error"	=> "LC_EXPP_ERR_PERENIMI",
	),
	"isikukood"	=>  array(
		"ok_era"	=> 1,
		"ok_ari"	=> 1,
		"mysql" => "isikukood",
		"error"	=> "LC_EXPP_ERR_ISIKUKOOD1",
		"error1"	=> "LC_EXPP_ERR_ISIKUKOOD2",
	),
	"epost"		=>  array(
		"ok_era"	=> 0,
		"ok_ari"	=> 1,
		"mysql" => "email",
		"error"	=> "LC_EXPP_ERR_EPOST1",
		"error1"=> "LC_EXPP_ERR_EPOST2",
		"error2"=> "LC_EXPP_ERR_EPOST3",
	),
	"telefon"	=>  array(
		"ok_era"	=> 0,
		"ok_ari"	=> 1,
		"mysql" => "telefon",
		"error"	=> "LC_EXPP_ERR_TELEFON1",
		"error1"=> "LC_EXPP_ERR_TELEFON2",
		"error2"=> "LC_EXPP_ERR_TELEFON3",
	),
	"mobiil"	=>  array(
		"ok_era"	=> 0,
		"ok_ari"	=> 0,
		"mysql" => "mobiil",
		"error"	=> "LC_EXPP_ERR_MOBIIL1",
		"error1"=> "LC_EXPP_ERR_MOBIIL2",
		"error2"=> "LC_EXPP_ERR_MOBIIL3",
	),
	"faks"		=>  array(
		"ok_era"	=> 0,
		"ok_ari"	=> 0,
		"mysql" => "faks",
		"error"	=> "LC_EXPP_ERR_FAKS1",
		"error1"=> "LC_EXPP_ERR_FAKS2",
		"error2"=> "LC_EXPP_ERR_FAKS3",
	),
	"tanav"		=>  array(
		"ok_era"	=> 1,
		"ok_ari"	=> 1,
		"mysql" => "tanav",
		"error"	=> "LC_EXPP_ERR_TANAV",
	),
	"maja"		=>  array(
		"ok_era"	=> 0,
		"ok_ari"	=> 0,
		"mysql" => "maja",
		"error"	=> "LC_EXPP_ERR_MAJA",
	),
	"korter"	=>  array(
		"ok_era"	=> 0,
		"ok_ari"	=> 0,
		"mysql" => "korter",
		"error"	=> "LC_EXPP_ERR_KORTER",
	),
	"indeks"	=>  array(
		"ok_era"	=> 0,
		"ok_ari"	=> 0,
		"mysql" => "indeks",
		"error"	=> "LC_EXPP_ERR_INDEKS",
	),
	"linn"		=>  array(
		"ok_era"	=> 1,
		"ok_ari"	=> 1,
		"mysql" => "linn",
		"error"	=> "LC_EXPP_ERR_LINN",
	),
	"maakond"	=>  array(
		"ok_era"	=> 1,
		"ok_ari"	=> 1,
		"mysql" => "maakond",
		"error"	=> "LC_EXPP_ERR_MAAKOND",
	),
	"toimetus"	=>  array(
		"ok_era"	=> 1,
		"ok_ari"	=> 1,
		"mysql" => "toimetus",
		"error"	=> "LC_EXPP_ERR_TOIMETUS",
	)
);

	function expp_isik() {
		$this->init(array(
			"tpldir" => "expp",
			"clid" => CL_EXPP_ISIK
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
		$_tellija = $this->cp->getPid( 1 );

		$myURL = $this->cp->addYah( array(
				'link' => $_tellija,
			//	'text' => ucfirst( $_tellija )." andmed"
				'text' => $lc_expp['LC_EXPP_'.strtoupper($_tellija).'_ANDMED']
			));

// ----------------------------------------
//	palju on tellimusi korvis
// ----------------------------------------
		$sql = "SELECT count(leping) AS okarv FROM expp_korv WHERE session='".session_id()."' AND leping='ok'";
		$row = $this->db_fetch_row( $sql );
		$_ok_count = $row['okarv'];
		if ( $_ok_count < 1 ) $this->telliitems["isikukood"]["ok_era"] = 0;
		if ( $_tellija != 'tellija' ) {
			$this->telliitems["toimetus"]["ok_era"] = 0;
			$this->telliitems["toimetus"]["ok_ari"] = 0;
			$this->telliitems["isikukood"]["ok_era"] = 0;
			$this->telliitems["isikukood"]["ok_ari"] = 0;
		}
		if( isset( $GLOBALS['HTTP_POST_VARS']['tagasi'] ) || isset( $GLOBALS['HTTP_POST_VARS']['tagasi_y'] )) {
			$this->returnPost();
		}
		if( isset( $GLOBALS['HTTP_POST_VARS']['edasi'] ) || isset( $GLOBALS['HTTP_POST_VARS']['edasi_y'] )) {
			$this->parsePost( $_tellija );
		} else {
			$this->getDBRows( $_tellija );
		}

		$this->cp->log( get_class($this), "form".ucfirst($_tellija) );

		$this->read_template("expp_isik.tpl");


		switch( $this->post_arr['tyyp'] ) {
			case 'eraisik' :
				$_ck = 'ok_era';
				break;
			case 'firma':
			default:
				$_ck = 'ok_ari';
		}
		foreach( $this->telliitems as $key => $var ) {
			$this->post_arr['bold_'.$key] = ($var[$_ck]?'cbold':'cnormal');
		}

		$this->post_arr['KES'] = $lc_expp['LC_EXPP_'.strtoupper( $_tellija )];
		$this->post_arr['ERAISIK_CH'] = ($this->post_arr['tyyp'] == 'eraisik' ? 'checked' : '');
		$this->post_arr['FIRMA_CH'] = ($this->post_arr['tyyp'] == 'firma' ? 'checked' : '');
		$this->post_arr['MAAKOND_SEL'] = html::select(array(
			'name' => 'maakond',
			'options' => $this->maakonnad,
			'selected' => htmlentities( $this->post_arr['maakond'] ),
			'class' => 'formElement',
		));
		if( $_tellija == 'tellija' ) {
			$this->vars(array(
				'SAMA_CH' => ($this->post_arr['toimetus'] == 'sama' ? 'checked' : ''),
				'ERINEV_CH' => ($this->post_arr['toimetus'] == 'erinev' ? 'checked' : ''),
			));
			$_aadress = $this->parse( 'AADRESS' );
		} else {
			$_aadress = '';
		}
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
		$this->post_arr['VEAD'] = $_vead;
		$this->post_arr['AADRESS'] = $_aadress;
		$this->vars( $this->post_arr );
		return $this->parse();
	}

	function getDBRows( $_tellija ) {
		$sql = "SELECT * FROM expp_tellija WHERE session='".session_id()."' AND staatus='{$_tellija}'";
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() < 1 ) {
			$sql = "INSERT INTO expp_tellija SET session='".session_id()."', tyyp='', staatus='{$_tellija}', time=NOW()";
			$this->db_query( $sql );
			return;
		}
		$_ck = $_tellija;
		switch( $_ck ) {
			case 'eraisik' :
				$_ck = 'ok_era';
				break;
			case 'firma':
			default:
				$_ck = 'ok_ari';
		}
		foreach( $this->telliitems as $key => $val ) {
			$in = $row[$val['mysql']];
			if( empty( $in )) {
				if( $val[$_ck] ) {
//					$this->post_errors[] = $val['error'];
				}
			} else {
				$this->post_arr[$key] = stripslashes( $in );
			}
		}
	}
	
	function returnPost() {
		$_aid = $this->cp->getPid( 1 );
		if( $_aid == 'saaja' ) {
			$_aid = 'tellija';
		} else {
			$_aid = 'korv';
		}
		header( "Location: ".aw_ini_get("baseurl")."/tellimine/{$_aid}" );
		exit;
	}
	
	function parsePost( $_aid ) {
		global $lc_expp;
		if( $_aid != 'tellija' && $_aid != 'saaja' ) {
			return 'tellija';
		}
		$_ck = $GLOBALS['HTTP_POST_VARS']['tyyp'];
		switch( $_ck ) {
			case 'eraisik' :
				$_ck = 'ok_era';
				break;
			case 'firma':
			default:
				$_ck = 'ok_ari';
		}
/*
		$_dir =  $this->cfg["site_basedir"].'/failid/';
		$fname	= "isik.txt";
		$fp = fopen( $_dir.$fname, "a");
*/
		foreach( $this->telliitems as $key => $val ) {
			$in = $GLOBALS['HTTP_POST_VARS'][$key];
/*
			fwrite ( $fp, $key."=>".$in."=>".preg_match( '/&#\d{4};/', $in )."\n" );
*/
			if( empty( $in )) {
				if( $val[$_ck] ) {
					$this->post_errors[] = $lc_expp[$val['error']];
				}
			} else if( preg_match( '/&#\d{4};/', $in )) { // Vene keele filter ???
				$this->post_errors['LC_EXPP_ERR_CHARS'] = $lc_expp['LC_EXPP_ERR_CHARS'];
			} else {
				$this->post_arr[$key] = $in;
			}
		}
		fclose( $fp );

		$this->specialChecks();
		if( !empty( $this->post_errors )) {
			return;
		}
		$sqls = array();
		foreach( $this->telliitems as $key => $val ) {
			$sqls[] = $val['mysql']."='".addslashes( $this->post_arr[$key] )."'";
		}
		$sql = "UPDATE expp_tellija SET ".implode( ',', $sqls ).", time=NOW() WHERE session='".session_id()."' AND staatus='{$_aid}'";
		$this->db_query( $sql );		

		$this->cp->log( get_class($this), "save".ucfirst($_aid), $this->post_arr['toimetus'], $this->post_arr['tyyp'] );

		if( $_aid == 'tellija' && $this->post_arr['toimetus'] == 'sama' || $_aid == 'saaja' ) {
			header( "Location: ".aw_ini_get("baseurl")."/tellimine/arve/" );
		} else if( $_aid == 'tellija' ) {
			header( "Location: ".aw_ini_get("baseurl")."/tellimine/saaja/" );
		} else {
			header( "Location: ".aw_ini_get("baseurl")."/tellimine/tellija/" );
		}
		exit;
	}

	function specialChecks() {
		global $lc_expp;
// ---[ telefon ]--------------------------
		if ( isset( $this->post_arr['telefon'] ) && !empty( $this->post_arr['telefon'] )) {
			$telefon1 = $this->post_arr['telefon'];
			if( $this->check_tel( &$telefon1 )) {
				$this->post_errors[] = $lc_expp[$this->telliitems['telefon']['error1']];
			} else {
				$telefon1 = $this->format_tel( $telefon1 );
			}
			if( strcmp( $this->post_arr['telefon'], $telefon1 ) != 0 ) {
				$this->post_errors[] = $lc_expp[$this->telliitems['telefon']['error2']];
				$this->post_arr['telefon'] = $telefon1;
			}
		}
// ---[ mobiil ]---------------------------
		if ( isset( $this->post_arr['mobiil'] ) && !empty( $this->post_arr['mobiil'] )) {
			$mobiil1 = $this->post_arr['mobiil'];
			if( $this->check_tel( &$mobiil1 )) {
				$this->post_errors[] = $lc_expp[$this->telliitems['mobiil']['error1']];
			} else {
				$mobiil1 = $this->format_tel( $mobiil1 );
			}
			if( strcmp( $this->post_arr['mobiil'], $mobiil1 ) != 0 ) {
				$this->post_errors[] = $lc_expp[$this->telliitems['mobiil']['error2']];
				$this->post_arr['mobiil'] = $mobiil1;
			}
		}
// ---[ faks ]-----------------------------
		if ( isset( $this->post_arr['faks'] ) && !empty( $this->post_arr['faks'] )) {
			$faks1 = $this->post_arr['faks'];
			if( $this->check_tel( &$faks1 )) {
				$this->post_errors[] = $lc_expp[$this->telliitems['faks']['error1']];
			} else {
				$faks1 = $this->format_tel( $faks1 );
			}
			if( strcmp( $this->post_arr['faks'], $faks1 ) != 0 ) {
				$this->post_errors[] = $lc_expp[$this->telliitems['faks']['error2']];
				$this->post_arr['faks'] = $faks1;
			}
		}
// ---[ e-post ]---------------------------
		if ( isset( $this->post_arr['epost'] ) && !empty( $this->post_arr['epost'] )) {
			$epost1 = $this->post_arr['epost'];
			if( $this->check_email( &$epost1 )) {
				$this->post_errors[] = $lc_expp[$this->telliitems['epost']['error1']];
			}
			if( strcmp( $this->post_arr['epost'], $epost1 ) != 0 ) {
				$this->post_errors[] = $lc_expp[$this->telliitems['epost']['error2']];
				$this->post_arr['epost'] = $epost1;
			}
		}	
// ---[ isikukood ]------------------------
		if ( isset( $this->post_arr['isikukood'] ) && $this->post_arr['tyyp'] == "eraisik" && $this->IsikuKoodVigane( $this->post_arr['isikukood'] )) {
			$this->post_errors[] = $lc_expp[$this->telliitems['isikukood']['error1']];
		}
	}
	
	function check_tel( $number_i ) {
		$error	= 0;
		$retVal = "";
		$number = trim( $number_i );
		$number = ereg_replace('[ .()/-]', '', $number );
		$number_a = split('([,;]|või)', $number );
		$arv2 = count( $number_a );
		for( $j = 0; $j < $arv2; $j++ ) {
			$number_b = ereg_replace('^(\+372|372|\+0|o)', '0', $number_a[$j] );
			$number_b = ereg_replace('^\+', '00', $number_b );
	//		$kontroll = '^((6|06)[0-9]{6}|(04[3-8]|03[23589]|07[6-9]|(7|07)[^6-9])[0-9]{5}|05[0-35-8][0-9]{5,6}|0[89]00[0-9]{4,7}|00[0-9]{4,20}|puudub)$';
			$kontroll = '^(0?6[0-9]{6}|0?(4[3-8]|3[23589]|7[6-9]|7[^6-9])[0-9]{5}|0?5[0-35-8][0-9]{5,6}|0?[89]0[0-1][0-9]{4,7}|00[0-9]{4,20}|puudub)$';
			if ( !ereg( $kontroll, $number_b ) ) {
				$error++;
				$retVal .= ($j>0?';':'').$number_a[$j];
			} else {
				$retVal .= ($j>0?';':'').$number_b;
			}
		}
		$number_i = $retVal;
		return $error;
	}

	function format_tel( $number_i ) {
		$kontroll = array();
		$replace = array();

		$kontroll[0]	= "^(00)([0-9]{3})";
		$replace[0]		= "\\1 \\2 ";
		$kontroll[1]	= "^0?(6[0-9]{2})";
		$replace[1]		= "\\1 ";
		$kontroll[2]	= "^0?((4[3-8]|3[23589]|7[6-9])[0-9]{1})";
		$replace[2]		= "\\1 ";
		$kontroll[3]	= "^0?7([^6-9][0-9]{1})";
		$replace[3]		= "7\\1 ";
		$kontroll[4]	= "^0?(5[0-35-8][0-9]{1})";
		$replace[4]		= "\\1 ";
		$kontroll[5]	= "^0?([89])(0[0-1])";
		$replace[5]		= "\\1 \\2 ";

		$number_a = split(';', $number_i );
		$arv2 = count( $number_a );
		for( $j = 0; $j < $arv2; $j++ ) {
			$number_b = $number_a[$j];
			for( $i = 0; $i < count( $kontroll ); $i++) {
				$number_b	= ereg_replace( $kontroll[$i], $replace[$i], $number_b);
			}
			$retVal .= ($j>0?';':'').$number_b;
		}
		return trim( $retVal );
	}

	function check_email( $email_i ) {
		$error	= 0;
		$retVal = "";
		$email_a = split('[;])', $email_i );
		$arv2 = count( $email_a );
		for( $j = 0; $j < $arv2; $j++ ) {
			$email_b = trim( $email_a[$j] );
			$kontroll = '^(([0-9a-z]+)([\._\-]([0-9a-z]+))*@([0-9a-z]+)([\._\-]([0-9a-z]+))*\.([0-9a-zA-Z]){2,3}([0-9a-zA-Z])?|puudub)$';
			if ( !eregi( $kontroll, $email_b ) ) {
				$error++;
				$retVal .= ($j>0?';':'').$email_a[$j];
			} else {
				$retVal .= ($j>0?';':'').$email_b;
			}
		}
		$email_i = $retVal;
		return $error;
	}

	function IsikuKontrollNumber( $S) {
		$M1 = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 1);
		$M2 = array( 3, 4, 5, 6, 7, 8, 9, 1, 2, 3);
		$K1 = 0;
		$K2 = 0;
		for( $I = 0; $I<10; $I++ )
		{	$J = $S[ $I ];
			$K1 = $K1 + $M1[ $I ] * $J;
			$K2 = $K2 + $M2[ $I ] * $J;
		}
		$K1 = $K1 % 11;
		if ( $K1 == 10 )
			$K1 = $K2 % 11;
		if ( $K1==10 )
			$K1 = 0;
		return ( $K1 == $S[ 10 ] );
	}

	function IsikukoodVigane( $S ) {
		$my_error = 0;
		if ( strlen( $S ) != 11 )
			$my_error+=1;
		if ( ! $this->IsikuKontrollNumber( $S ))
			$my_error+=1;
		$M = (integer)substr( $S, 3, 2);
		$D = (integer)substr( $S, 5, 2);
		$Y1  = (integer)substr( $S, 0, 1);
		$Y2  = (integer)substr( $S, 1, 2);
		$Y3 = (17 + ($Y1 + ($Y1 % 2)) / 2) * 100 + $Y2;
		if ( $Y3 < 1800 )
			$my_error+=1;
		return $my_error;
	}
}
?>
