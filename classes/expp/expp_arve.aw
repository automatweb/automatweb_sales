<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_arve.aw,v 1.8 2007/11/23 07:18:28 dragut Exp $
// expp_arve.aw - Expp arve 
/*

@classinfo syslog_type=ST_EXPP_ARVE relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_arve extends class_base {
	var $cp;
	var $valjad = array(
		'nimi'		=> 'nimi',
		'enimi'		=> 'enimi',
		'email'		=> 'email',
		'tanav'		=> 'tanav',
		'maja'		=> 'maja',
		'korter'		=> 'korter',
		'telefon'	=> 'telefon',
		'mobiil'		=> 'mobiil',
		'faks'		=> 'fax',
		'indeks'		=> 'indeks',
		'aadress'	=> 'aadress',
		'isikukood'	=> 'isikukood',
	);
	function expp_arve() {
		$this->init(array(
			"tpldir" => "expp",
			"clid" => CL_EXPP_ARVE
		));
		$this->cp = get_instance( CL_EXPP_PARSE );
		lc_site_load( "expp", $this );
	}

	function show($arr) {
		global $lc_expp;
		if( isset( $GLOBALS['HTTP_POST_VARS']['tagasi'] ) || isset( $GLOBALS['HTTP_POST_VARS']['tagasi_y'] )) {
			$this->returnPost();
		}
		if( isset( $GLOBALS['HTTP_POST_VARS']['edasi'] ) || isset( $GLOBALS['HTTP_POST_VARS']['edasi_y'] )) {
			$this->parsePost();
		}
		$_action = $this->cp->addYah( array(
				'link' => 'arve',
				'text' => t('Arve koostamine'),
			));

		$sql = "SELECT * FROM expp_tellija WHERE session='".session_id()."' AND staatus='tellija' ORDER BY time DESC LIMIT 1";
		$row = $this->db_fetch_row( $sql );
		if( $this->num_rows() < 1 ) {
			header( "Location: ".aw_ini_get("baseurl")."/tellimine/korv/" );
			exit;
		}
		$this->read_template("expp_arve.tpl");

		$_kood	= ($row['tyyp']=="firma")?"Registri nr. <b>".$row["isikukood"]."</b>":"Isikukood <b>".$row["isikukood"]."</b>";
		$_isik1	= $this->getIsik( $row );
		$this->vars( array(
			'PEALKIRI' => t('Tellija andmed:'),
			'SISU' => $_isik1,
		));
		$_isik = $this->parse( 'ISIK' );
		
		if( $row['toimetus'] != 'sama' ) {
			$sql = "SELECT * FROM expp_tellija WHERE session='".session_id()."' AND staatus='saaja' ORDER BY time DESC LIMIT 1";
			$row = $this->db_fetch_row( $sql );
			if( $this->num_rows() < 1 ) {
				header( "Location: ".aw_ini_get("baseurl")."/tellimine/tellija/" );
				exit;
			}
			$_isik1 = $this->getIsik( $row );
		}
		$this->vars( array(
			'PEALKIRI' => t('Saaja andmed:'),
			'SISU' => $_isik1,
		));
		$_isik .= $this->parse( 'ISIK' );


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
		." FROM expp_korv k ,expp_valjaanne t, expp_hind h"
		." WHERE k.session='".session_id()."' AND k.pindeks=t.pindeks AND k.pikkus=h.id"
		." ORDER BY k.leping DESC, t.valjaande_nimetus ASC";
		$this->db_query($sql);
		if( $this->num_rows() == 0 ) {
			header( "Location: ".aw_ini_get("baseurl")."/tellimine/korv/" );
			exit;
		}

		$this->cp->log( get_class($this), "show" );

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
			'ISIK' => $_isik,
		));
		return $this->parse();
	}
	
	function getIsik( $row ) {
		$content = '';
		if ( $row['tyyp'] == "firma" )	$content.=stripslashes( $row["firmanimi"])."<br>\n";
		$content.=stripslashes( $row["eesnimi"]." ".$row["perenimi"])."<br>\n";
		if ( !empty( $row["email"] ))	$content.=t("e-post: ").stripslashes( $row["email"])."<br>\n";
		if ( !empty( $row["telefon"] ))	$content.=t("tel: ").stripslashes( $row["telefon"])."<br>\n";
		if ( !empty( $row["mobiil"] ))	$content.=t("mobiil: ").stripslashes( $row["mobiil"])."<br>\n";
		if ( !empty( $row["faks"] ))	$content.=t("faks: ").stripslashes( $row["faks"])."<br>\n";
		$content.="<br>";
		if ( !empty( $row["tanav"] ))	$content.=stripslashes( $row["tanav"]);
		if ( !empty( $row["maja"] ))	$content.=" ".stripslashes( $row["maja"]);
		if ( !empty( $row["korter"] ))	$content.="-".stripslashes( $row["korter"]);
		$content.="<br>\n";
		if ( !empty( $row["linn"] ))	$content.=stripslashes( $row["linn"])."<br>\n";
		if ( !empty( $row["indeks"] ))	$content.=stripslashes( $row["indeks"])." ";
		if ( !empty( $row["maakond"] ))	$content.=stripslashes( $row["maakond"]);
		return $content;
	}

	function returnPost() {
		header( "Location: ".aw_ini_get("baseurl")."/tellimine/" );
		exit;
	}

	function getNextArve() {
		$this->save_handle();
//		$sql	= "LOCK TABLES expp_arvenr WRITE";
//		$this->db_query($sql);
		$sql	= "UPDATE expp_arvenr SET arvenr=arvenr+1 WHERE id=1";
		$this->db_query($sql);
		$sql	= "SELECT arvenr FROM expp_arvenr WHERE id=1";
		$arow = $this->db_fetch_row( $sql );
//		$sql	= "UNLOCK TABLES expp_arvenr";
//		$this->db_query($sql);
		$retVal = sprintf("%08s",$arow["arvenr"]);
		$this->restore_handle();

		$this->cp->log( get_class($this), "uusArve", $retVal );

		return $retVal;
	}

	function getNextTell() {
		$this->save_handle();
//		$sql	= "LOCK TABLES expp_tellnr WRITE";
//		$this->db_query($sql);
		$sql	= "UPDATE expp_tellnr SET tellnr=tellnr+1 WHERE id=1";
		$this->db_query($sql);
		$sql	= "SELECT tellnr FROM expp_tellnr WHERE id=1";
		$arow = $this->db_fetch_row( $sql );
//		$sql	= "UNLOCK TABLES expp_tellnr";
//		$this->db_query($sql);
		$retVal = sprintf("%08s",$arow["tellnr"]);
		$this->restore_handle();

		$this->cp->log( get_class($this), "uusTellimus", $retVal );

		return $retVal;
	}

	function leia_731( $S ) {
		$Kontroll = "";
		$L = strlen( $S );
		for ( $i = 0; $i < $L ; $i ++)
			if (( $S[ $i ] != " " ) and ( $S[ $i ] != "-" ))
				$Kontroll.= (integer)$S[$i];
		$S = $Kontroll;
		$m1 = 7;
		$K = 0;
		$L = strlen( $S );
		for ( $i = ($L - 1); $i > -1; $i--) {
			$K+= ( $S[ $i ] * $m1 );
			switch ( $m1 ) {
				case 7 : $m1 = 3; break;
				case 3 : $m1 = 1; break;
				case 1 : $m1 = 7; break;
			}
		}
		$K1 = ( $K - ( $K % 10 ) + 10 );
		$K2 = $K1 - $K;
		if ( $K2 > 9 ) $K2 -= 10;
		return $K2;
	}

	function parsePost() {

		$this->cp->log( get_class($this), "salvestaArve" );

		$sqls = array();
		$sql = "SELECT * FROM expp_tellija WHERE session='".session_id()."' AND staatus='tellija' ORDER BY time DESC LIMIT 1";
		$_tellija = $this->db_fetch_row( $sql );
		if( $this->num_rows() < 1 ) {
			header( "Location: ".aw_ini_get("baseurl")."/tellimine/tellija/" );
			exit;
		}
		$_SESSION['tellnr'] = $this->getNextTell();
		if ( $_tellija['tyyp'] == "firma" ) {
			$_tellija['nimi'] = $_tellija["firmanimi"];
			$_tellija['enimi'] = $_tellija["eesnimi"]." ".$_tellija["perenimi"];
		} else {
			$_tellija['nimi'] = $_tellija["eesnimi"]." ".$_tellija["perenimi"];
			$_tellija['enimi'] = '';
		}
		$_tellija['aadress'] = $_tellija['linn'].','.$_tellija['maakond'];
		if( $_tellija['toimetus'] == 'sama' ) {
			$_saaja = $_tellija;
		} else {
			$sql = "SELECT * FROM expp_tellija WHERE session='".session_id()."' AND staatus='saaja' ORDER BY time DESC LIMIT 1";
			$_saaja = $this->db_fetch_row( $sql );
			if( $this->num_rows() < 1 ) {
				header( "Location: ".aw_ini_get("baseurl")."/tellimine/saaja/" );
				exit;
			}
			if ( $_saaja['tyyp'] == "firma" ) {
				$_saaja['nimi'] = $_saaja["firmanimi"];
				$_saaja['enimi'] = $_saaja["eesnimi"]." ".$_saaja["perenimi"];
			} else {
				$_saaja['nimi'] = $_saaja["eesnimi"]." ".$_saaja["perenimi"];
				$_saaja['enimi'] = '';
			}
			$_saaja['aadress'] = $_saaja['linn'].','.$_saaja['maakond'];
		}
		$sql1 = '';
		foreach( $this->valjad as $key => $val ) {
			$sql1 .= " s{$val}='".$_saaja[$key]."',";
			$sql1 .= " m{$val}='".$_tellija[$key]."',";
		}
		$sql1 .= " vvotja='TK',"
			." kanal='WEB',"
			." tyyp='U'";
		$sql = "SELECT k.*"
			.", t.toimtunnus"
			.", h.baashind"
			.", h.hkkood"
			.", h.kampaania"
		." FROM expp_korv k ,expp_valjaanne t, expp_hind h"
		." WHERE k.session='".session_id()."' AND k.pindeks=t.pindeks AND k.pikkus=h.id"
		." AND k.leping = 'ok'"
		." ORDER BY k.leping DESC, t.valjaande_nimetus ASC";
		$this->db_query($sql);
		while ($row = $this->db_next()) {
			$_arve = $this->getNextArve();
			$_viitenr = "205".$row["toimtunnus"].$_arve;
			$_viitenr.= $this->leia_731( $_viitenr );
			if ( $row["algus"] == "ASAP" ) {
				$algus	= mktime( 0,0,0,date("m"),date("d"),date("Y"));
				$lopp	= mktime( 0,0,0,date("m")+1,date("d")-1,date("Y"));
				$lisarida	= addslashes( "Nii ruttu, kui v&otilde;imalik" );
			} else if ( $row["algus"] == "CONT" ) {
				$algus	= mktime( 0,0,0,date("m"),date("d"),date("Y"));
				$lopp	= mktime( 0,0,0,date("m")+1,date("d")-1,date("Y"));
				$lisarida	= addslashes( "Kehtiva tellimuse l&otilde;pust" );
			} else {
				$algus		= mktime(0,0,0,(int)substr( $row["algus"],4,2),1,(int)substr( $row["algus"],0,4));
				$lopp		= mktime(0,0,0,1+(int)substr( $row["algus"],4,2),0,(int)substr( $row["algus"],0,4));
				$lisarida	= "";
			}

			$sqls[] = "INSERT INTO expp_arved SET $sql1, tellkpv='".date("d.m.Y")."',"
				." arvenr='{$_arve}',"
				." vaindeks='".$row["pindeks"]."',"
				." algus='".date("d.m.Y",$algus)."',"
				." lopp='".date("d.m.Y",$lopp)."',"
				." lisarida='{$lisarida}',"
				." eksempla='".$row["eksemplar"]."',"
				." rhkkood='".$row["hkkood"]."',"
				." kampaania='".$row['kampaania']."',"
				." maksumus='".($row["baashind"]*(int)$row["eksemplar"]*(int)$row["kogus"])."',"
				." leping='".$row["leping"]."',"
				." trykiarve='0',"
				." trykiokpakkumine='0',"
				." viitenumber='{$_viitenr}',"
				." session='".session_id().'-'.$_SESSION['tellnr']."',"
				." time=NOW()";
//			$this->db_query($sql);
//			$my_ok += @mysql_affected_rows( $dbh );
		}
// ----------------------------------------
//							arvega asjad
		$sql = "SELECT k.*"
			.", t.toimetus"
			.", t.toimtunnus"
			.", t.valjaanne"
			.", h.kampaania"
			.", h.baashind"
			.", h.kestus"
			.", h.hinna_tyyp"
			.", h.hkkood"
		." FROM expp_korv k ,expp_valjaanne t, expp_hind h"
		." WHERE k.session='".session_id()."' AND k.pindeks=t.pindeks AND k.pikkus=h.id"
		." AND k.leping = 'tel'"
		." ORDER BY k.leping DESC, t.valjaande_nimetus ASC";

		$this->db_query($sql);
		$_nr = $this->num_rows();
		if( $_nr > 0 ) {
			$_arve = $this->getNextArve();

			if ( $_nr > 1 ) {
				$_viitenr = "10599{$_arve}";
				$_viitenr.= $this->leia_731( $_viitenr );
			}

			while ($row = $this->db_next()) {
				if ( $_nr == 1 ) {
					$_viitenr = "105".$row["toimtunnus"].$_arve;
					$_viitenr.= $this->leia_731( $_viitenr );
				}
				$kestus	= (int)$row["kestus"]*(int)$row["kogus"];
				switch( $row["hinna_tyyp"] ) {
					case 0:	//	kuu
						$lisarida	=( $kestus== 1 )?"kuu":"kuud";
						$my_m		= (int)$row["kestus"]*(int)$row["kogus"];
						$my_d		= 0;
						$my_y		= 0;
						break;
					case 1:	//	nädal
						$lisarida	=( $kestus== 1 )?"n&auml;dal":"n&auml;dalat";
						$my_m		= 0;
						$my_d		= (int)$row["kestus"]*7*(int)$row["kogus"];
						$my_y		= 0;
						break;
					case 3:	//	number
						$lisarida	=( $kestus== 1 )?"number":"numbrit";
						$my_m		= 0;
						$my_d		= (int)$row["kestus"]*7*(int)$row["kogus"];
						$my_y		= 0;
						break;
					case 2:	//	päev
						$lisarida	=( $kestus== 1 )?"p&auml;ev":"p&auml;eva";
						$my_m		= 0;
						$my_d		= (int)$row["kestus"]*(int)$row["kogus"];
						$my_y		= 0;
						break;
					case 4:	//	aasta
						$lisarida	=( $kestus== 1 )?"aasta":"aastat";
						$my_m		= 0;
						$my_d		= 0;
						$my_y		= (int)$row["kestus"]*(int)$row["kogus"];
						break;
		 			case 5:	//	poolkuud
						$lisarida	= ( $kestus== 1 )?"poolkuu":"poolkuud";
						$my_m		= (int)$row["kestus"]*(int)$row["kogus"];
						$my_d		= 0;
						$my_y		= 0;
						break;
				}
				if ( $row["algus"] == "ASAP" ) {
					$algus		= mktime( 0,0,0,date("m"),date("d"),date("Y"));
					$lopp		= mktime( 0,0,0,date("m")+$my_m,date("d")+$my_d-1,date("Y")+$my_y);
					$lisarida	= "Nii ruttu, kui v&otilde;imalik<br><b>$kestus</b> $lisarida";
				} else if ( $row["algus"] == "CONT" ) {
					$algus		= mktime( 0,0,0,date("m"),date("d"),date("Y"));
					$lopp		= mktime( 0,0,0,date("m")+$my_m,date("d")+$my_d-1,date("Y")+$my_y);
					$lisarida	= "Kehtiva tellimuse l&otilde;pust<br><b>$kestus</b> $lisarida";
				} else if ( $row["hinna_tyyp"] != 3 ) {
					$algus		= mktime(0,0,0,(int)substr( $row["algus"],4,2),1,(int)substr( $row["algus"],0,4));
					$lopp		= mktime(0,0,0,(int)substr( $row["algus"],4,2)+$my_m,$my_d,(int)substr( $row["algus"],0,4)+$my_y);
					$lisarida	= "";
//				} else if ( $row["hinna_tyyp"] == 3 && $row['kestus'] > 1 ) {
				} else {	// Eesti ekspress jpt.
					$algus		= mktime(0,0,0,(int)substr( $row["algus"],4,2),1,(int)substr( $row["algus"],0,4));
					$sql = "select UNIX_TIMESTAMP( ilmumiskpv ) as kuup from expp_ilmumisgraafik where valjaanne = '".$row['valjaanne']."' AND ilmumiskpv >= '".date('Y-m-d', $algus)."' ORDER BY ilmumiskpv ASC LIMIT ".(($row["kogus"]*$row['kestus'])-1).",1";
					$this->save_handle();
					$row1 =& $this->db_fetch_row( $sql );
					$lopp = $row1['kuup'];
					$this->restore_handle();
					$lisarida	= "<b>".($row["kogus"]*$row['kestus'])."</b> $lisarida";
				}
/*
				} else if ( $row["pindeks"] != "69830" ) {
					$algus		= mktime(0,0,0,(int)substr( $row["algus"],4,2),1,(int)substr( $row["algus"],0,4));
					$lopp		= mktime(0,0,0,(int)substr( $row["algus"],4,2)+$my_m,$my_d,(int)substr( $row["algus"],0,4)+$my_y);
					$lisarida	= "";
				} else {	// Eesti ekspress
					$algus		= mktime(0,0,0,(int)substr( $row["algus"],4,2),1,(int)substr( $row["algus"],0,4));
					$ajut		= date("w", $algus);
					$ajut		= ($ajut <= 4)?(4-$ajut):(11-$ajut);
					$algus		= mktime(0,0,0,(int)substr( $row["algus"],4,2),1+$ajut,(int)substr( $row["algus"],0,4));
					$lopp		= mktime(0,0,0,(int)substr( $row["algus"],4,2)+$my_m,$my_d+$ajut-6,(int)substr( $row["algus"],0,4)+$my_y);
					$lisarida	= "";
				}
*/
				$sqls[] = "INSERT INTO expp_arved SET {$sql1}, tellkpv='".date("d.m.Y")."',"
					." arvenr='{$_arve}',"
					." vaindeks='".$row["pindeks"]."',"
					." algus='".date("d.m.Y",$algus)."',"
					." lopp='".date("d.m.Y",$lopp)."',"
					." lisarida='".addslashes($lisarida)."',"
					." eksempla='".$row["eksemplar"]."',"
					." rhkkood='".$row["hkkood"]."',"
					." kampaania='".$row['kampaania']."',"
					." maksumus='".($row["baashind"]*(int)$row["eksemplar"]*(int)$row["kogus"])."',"
					." leping='".$row["leping"]."',"
					." trykiarve='0',"
					." trykiokpakkumine='0',"
					." viitenumber='{$_viitenr}',"
					." session='".session_id().'-'.$_SESSION['tellnr']."',"
					." time=NOW()";
//				$this->db_query($sql);
//				$my_ok += @mysql_affected_rows( $dbh );
			}
		}
		$sqls[] = "UPDATE expp_korv SET session='".session_id().'-'.$_SESSION['tellnr']."' WHERE session='".session_id()."'";
		foreach( $sqls as $sql ) {
			$this->db_query($sql);
		}
		$this->sendEmail();
/*
if ( $my_ok == 0 ) {
	$pid = korv;
	header( "location: index.php3".make_argc( "id", "pid" ));
	exit;
}
*/

/*$query = "UPDATE tellija SET session='".date("Ymd - ").session_id()."' WHERE session='".session_id()."'";
@mysql_db_query( $db_base, $query, $dbh );
$query = "UPDATE korv SET session='".date("Ymd - ").session_id()."' WHERE session='".session_id()."'";
@mysql_db_query( $db_base, $query, $dbh );*/

		header( "Location: ".aw_ini_get("baseurl")."/tellimine/makse/" );
		exit;
	}
	function sendEmail() {
		global $lc_expp;

		$retEmail = '';

		$sql = "SELECT * FROM expp_tellija WHERE session='".session_id()."' AND staatus='tellija' ORDER BY time DESC LIMIT 1";
		$row = $this->db_fetch_row( $sql );
		$to_email = $row['email'];
		if( $this->num_rows() < 1  || empty( $to_email)) {
			return;
		}
		$this->read_template("expp_arve_email.tpl");

		$_kood	= ($row['tyyp']=="firma")?"Registri nr. <b>".$row["isikukood"]."</b>":"Isikukood <b>".$row["isikukood"]."</b>";
		$_isik1	= $this->getIsik( $row );
		$this->vars( array(
			'PEALKIRI' => 'Tellija andmed:',
			'SISU' => $_isik1,
		));
		$_isik = $this->parse( 'ISIK' );
		
		if( $row['toimetus'] != 'sama' ) {
			$sql = "SELECT * FROM expp_tellija WHERE session='".session_id()."' AND staatus='saaja' ORDER BY time DESC LIMIT 1";
			$row = $this->db_fetch_row( $sql );
			if( $this->num_rows() < 1 ) {
				return;
			}
			$_isik1 = $this->getIsik( $row );
		}
		$this->vars( array(
			'PEALKIRI' => 'Saaja andmed:',
			'SISU' => $_isik1,
		));
		$_isik .= $this->parse( 'ISIK' );

		$sql = "SELECT a.arvenr, a.viitenumber, a.maksumus, a.algus, a.lisarida, t.toote_nimetus, t.valjaande_nimetus"
			." FROM expp_arved a LEFT JOIN expp_valjaanne t ON a.vaindeks=t.pindeks"
			." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='ok'";
		$this->db_query( $sql );
		$_ok_count = $this->num_rows();
		if( $_ok_count > 0 ) {
			$_okline = '';
			$_oklink	= '';
			$_hansacase = '';
			while( $row = $this->db_next()) {
				$this->vars(array(
					'OKLEPINGUNR'	=> $row["arvenr"],
					'OKVIITENR'		=> $row["viitenumber"],
					'TOODE'			=> stripslashes($row["valjaande_nimetus"]),
					'LEPING'			=> ($row["algus"]==date("d.m.Y")?"<b>".$row["lisarida"]."</b>":"alates <b>".$row["algus"]."</b>"),
					'HIND'			=> $row["maksumus"],
				));
				$_okline .= $this->parse( 'OKLINE' );
			}	//	while
			if( $_ok_count > 1 ) {
				$_hansacase = $this->parse( 'HANSACASE' );
			}
			foreach( $this->lingid as $key => $val ) {
				$this->vars(array(
					'url'		=> $val['url2'],
//					'target'
					'text'	=> $lc_expp[$val['text']],
				));
				$_oklink .= $this->parse( 'OKLINK' );
			}
			$this->vars(array(
				'HANSACASE'	=> $_hansacase,
				'OKLINE'		=> $_okline,
				'OKLINK'		=> $_oklink,
			));
			$_okleping .= $this->parse( 'OTSEKORRALDUS' );
		}
		$sql = "SELECT a.arvenr, a.viitenumber, a.maksumus, a.algus, a.lopp, a.lisarida, t.toote_nimetus, t.valjaande_nimetus"
			." FROM expp_arved a LEFT JOIN expp_valjaanne t ON a.vaindeks=t.pindeks"
			." WHERE session='".session_id().'-'.$_SESSION['tellnr']."' AND leping='tel'";
		$this->db_query( $sql );
		$_tel_count = $this->num_rows();
		if( $_tel_count > 0 ) {
			$_summa = 0;
			$_teline = '';
			$_tellep = '';
			$_old_arve = '';
			$_old_viide = '';
			while( $row = $this->db_next()) {
				if( $_old_arve != $row['arvenr'] ) {
					if( !empty( $_teline )) {
						$this->vars(array(
							'TELLINE' => $_teline,
							'SUMMA'	 => $_summa,
						));
						$_tellep .= $this->parse( 'TELLEP' );
					}
					$this->vars(array(
						'LEPINGUNR'	=> $row['arvenr'],
						'VIITENR'	=> $row["viitenumber"],
					));
					$_old_arve	= $row['arvenr'];
					$_teline 	= '';
					$_summa		= 0;
				}
				$_summa	+= $row['maksumus'];
				$this->vars(array(
					'TOODE'	=> stripslashes($row["valjaande_nimetus"]),
					'LEPING'	=> ( $row["algus"]==date("d.m.Y")?$row["lisarida"]:"<b>".$row["algus"]."</b> - <b>".$row["lopp"]."</b>"),
					'HIND'	=> $row["maksumus"],
				));
				$_teline .= $this->parse( 'TELLINE' );
			}
			if( !empty( $_teline )) {
				$this->vars(array(
					'TELLINE' => $_teline,
					'SUMMA'	 => $_summa,
				));
				$_tellep .= $this->parse( 'TELLEP' );
			}
			$_pangad = html::select(array(
						'name' => 'maksan',
						'options' => $this->pangad,
						'selected' => '',
						'class' => 'formElement',
					));
			$this->vars(array(
				'TELLEP' => $_tellep,
				'makse_meetod' =>	$_pangad,
			));
			$_teleping = $this->parse( 'TAVALEPING' );
		}
		if( $_tel_count < 1 && $_ok_count < 1 ) {
			return;
		}

		$_aid = $this->cp->getPid( 2 );
		$myURL = $this->cp->addYah( array(
				'link' => 'makse',
				'text' => $lc_expp['LC_EXPP_TITLE_MAKSMINE'],
			));

//		$this->read_template("expp_maksevalik.tpl");
		$this->vars(array(
			'ACTION'			=> $myURL,
			'OTSEKORRALDUS'=> $_okleping,
			'TAVALEPING'	=> $_teleping,
			'ISIK' => $_isik,
		));
		$retHTML .= $this->parse();

		$i = get_instance("protocols/mail/aw_mail");
		$i->create_message(array(		
			"froma" => 'tellimine@tellimine.ee',
			"fromn" => 'www.tellimine.ee', 
			"subject" => 'Uus tellimus www.tellimine.ee',
			"to" => "{$to_email}",
			"body" => 'Tere,

Täname tellimuse eest!
Teie tellimus jõudis AS Express Post klienditeenindusse.
Tellimus jõustub Teie poolt valitud kuupäeval või hiljemalt 10 päeva jooksul peale makse laekumist või otsekorralduse lepingu sõlmimist.

Lugupidamisega,
AS Express Post 

tellimine@expresspost.ee
www.tellimine.ee
6662535

AS Express Posti klienditeenindus on avatud E-R 8.00-20.00 L 8.00-13.00',
		));

// lisame html sisu
		$i->htmlbodyattach( array("data" => $retHTML ));

		// saadame meili teele
		$i->gen_mail();
	}
}
?>
