<?php
/*===========================================================================*/
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_import.aw,v 1.6 2007/11/23 07:18:28 dragut Exp $
// expp_import.aw - Expp import 
/*

@classinfo syslog_type=ST_EXPP_IMPORT relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general
*/
/*===========================================================================*/
class expp_import extends class_base
{
	var $errorMsg	= '';
	var $fileCont	= '';
	var $values		= array();
	var $tags		= array();
	var $imp_arr	= array();

	var $va_table = 'expp_valjaanne';
	var $va_temp;

	var $ilm_table = 'expp_ilmumisgraafik';
	var $ilm_temp;
	
	var $hind_table = 'expp_hind';
	var $hind_temp;

	var $lva_table = 'expp_va_liik';
	var $lva_temp;
	
	var $conv_table = array(
		'valjaanne',
		'toimetus',
		'toote_nimetus',
		'valjaande_nimetus',
		'kampaania',
		'ajaleht',
		'toimetus',
		'hinna_liik',
		'veebi_kirjeldus',
		'valjaande_kirjeldus',
	);

	var $va_trans_sql = '';
	var $va_trans_table = array(
		'pindeks' => 'pindeks',
		'valjaanne' => 'valjaanne',
		'valjaande_nimetus' => 'valjaande_nimetus',
		'toote_nimetus' => 'toote_nimetus',
		'ajaleht' => 'toote_tyyp',
		'toimetus' => 'toimetus',
		'toimtunnus' => 'toimtunnus',
	//	'kampaania' => 'kampaania',
		'valjaande_kirjeldus' => 'veebi_kirjeldus',
	//	'tyyp' => 'hinna_tyyp'
	);

	var $ilm_trans_sql = '';
	var $ilm_trans_table = array(
		'valjaanne' => 'valjaanne',
		'number' => 'number',
		'ilmumiskpv' => 'ilmumiskpv'
	);

	var $hind_trans_sql = '';
	var $hind_trans_table = array( 
		'pindeks'		=> 'pindeks',
		'hkkood'       => 'hkkood',
		'hinna_liik'	=> 'hinna_liik',
		'tyyp'			=> 'hinna_tyyp',
		'algus'			=> 'algus',
		'lopp'			=> 'lopp',	
		'hinna_kirjeldus' => 'hinna_kirjeldus',
		'kampaania'		=> 'kampaania',
		'kestus'			=> 'kestus',
		'baashind'		=> 'baashind',
		'juurdekasv'	=> 'juurdekasv',
	);

	var $ok_tyybid = array(
		'OKHIND'				=> 'OKHIND',
		'AVALIK_OKHIND'	=>	'AVALIK_OKHIND',
		'SALAJANE_OKHIND'	=> 'SALAJANE_OKHIND',
	);

	var $tel_tyybid = array(
				'TAVAHIND'				=> 'TAVAHIND',
				'AVALIK_TAVAHIND'		=>	'AVALIK_TAVAHIND',
				'SALAJANE_TAVAHIND'	=> 'SALAJANE_TAVAHIND',
	);
	
	var $all_tyybid = array();
	var $lang;
/*===========================================================================*/
	function expp_import() {
		$this->lang = aw_global_get("admin_lang_lc");

		$sl = get_instance(CL_AUTH_SERVER_LOCAL);
		list($success, $msg) = $sl->check_auth(NULL, array(
			"uid" => $_SERVER['PHP_AUTH_USER'],
			"password" => $_SERVER['PHP_AUTH_PW']
		));
		if ($success) {
			$u = get_instance("users");
			$u->login(array("uid" => $_SERVER['PHP_AUTH_USER'], "password" => $_SERVER['PHP_AUTH_PW']));
		} else {
		   header('WWW-Authenticate: Basic realm="Juurdep@@s piiratud"');
		   header('HTTP/1.0 401 Unauthorized');
		   echo '<h1>Autoriseerimine puudulik!</h1><br>Siis saavad ligi ainult &otilde;igustega kasutajad<br>'.$msg.'<br>Only for allowed access';
		   exit;
		}
 		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "expp",
			"clid" => CL_EXPP_IMPORT
		));
		$this->va_trans_sql = implode( ' , ', $this->va_trans_table );
		$this->ilm_trans_sql = implode( ' , ', $this->ilm_trans_table );
		$this->hind_trans_sql = implode( ' , ', $this->hind_trans_table );
		$this->all_tyybid = ($this->tel_tyybid + $this->ok_tyybid);
	}
/*===========================================================================*/
//////
// class_base classes usually need those, uncomment them if you want to use them
	function get_property( $arr )
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}
/*===========================================================================*/
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//

		}
		return $retval;
	}	
/*===========================================================================*/
	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
/*===========================================================================*/
////
// !this will be called if the object is put in a document by an alias and the document is being shown
// parameters
//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}
/*===========================================================================*/
////
// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
/**  
	
	@attrib name=show is_public="1" caption="Expp" nologin="0" default="1" all_args="1"
	
	@param id type=int
	
	@returns
	
	@comment

**/
	function show($arr)
	{
		$ob = new object($arr["id"]);
/*
		if( isset( $P['Salvesta'] ) && !empty( $P['Salvesta'] )) {
*/
			if(  $this->postX() === true ) {
/*
				$this->read_template("expp_import_ok.tpl");
				$this->vars(array(
					"name" => $ob->prop("name")
				));
*/
				echo 'OK';
				exit;
			}
/*
		}
*/
		if( isset( $this->errorMsg ) && !empty( $this->errorMsg )) {
			foreach( $this->errorMsg as $_err ) {
				echo $_err."<br />\n";
			}
		}
		$this->read_template("expp_import_show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
	      'reforb' => $this->mk_reforb("submit", array("action"=>"show")),
		));
		return $this->parse();
	}
/*===========================================================================*/
	function postX()
	{
		$this->fileCont = '';
		$upload_dir =  $this->cfg["site_basedir"].'/upload/';

		$allowed_extensions = array( 'xml' );
		$packed_extensions = array( 'zip' );
		$P = $GLOBALS['HTTP_POST_VARS'];
		$F = $GLOBALS['HTTP_POST_FILES'];
/*
		if( !isset( $P['Salvesta'] ) || empty( $P['Salvesta'] )) {
			return false;
		} 
*/
		$File	= $F['tootefail']['name'];
		$Tmp	= $F['tootefail']['tmp_name'];
		if( !is_uploaded_file($Tmp)) {
			$this->errorMsg[] = 'Fail puudub';
			return false;
		}
		if( !move_uploaded_file( $Tmp, $upload_dir.$File ))	{
			$this->errorMsg[] = 'Faili ei suudetud kopeerida';
			return false;
		}
		$ext = substr( $File, strrpos( $File, '.' ) + 1 );
		if( in_array( $ext, $packed_extensions ) === true ) {
			shell_exec( "unzip -o '$upload_dir$File' -d '$upload_dir'" );
			unlink( $upload_dir.$File );
		}		
		// get whole upload dir
		$d = dir( $upload_dir );
		while (($File = $d->read()) !== false ) {
			if ( strpos( $File, '.' ) === 0 ) continue;
			if( filesize( $upload_dir.$File ) > 0 ) {
				$this->sendFile( $File );
				// get extension
				$ext = substr( $File, strrpos( $File, '.' ) + 1 );
				if( in_array( $ext, $allowed_extensions ) === false ) {
					$this->errorMsg[] = 'Ei ole õige faili tüüp -> '.$File;
				} else {
					$this->getFile( $upload_dir.$File );
					if( empty( $this->fileCont )) {
						return false;
					}
					$this->do_import();
					$this->save_data();
				}
			} else {
				$this->errorMsg[] = 'T&uuml;hi fail -> '.$File;
			}
			unlink( $upload_dir.$File );
		}
		return true;
	}
/*===========================================================================*/
	function getFile( $fName ) {
		$this->is_utf32 = chr(255).chr(254).chr(0).chr(0);
		$this->fileSize = filesize( &$fName );
		if( $this->fileSize < 1 )
			return;
		$fh = fopen( &$fName, 'rb' );
		$this->fileCont = fread( $fh, $this->fileSize );
		fclose( $fh );
		if( strncmp( $this->fileCont, $this->is_utf32, 4 ) == 0 ) {
			$this->parse_utf32();
		}
//		return utf8_encode( $this->fileCont );
		return;
	}
/*===========================================================================*/
	function parse_utf32()	{
		$newCont = '';
		for( $i = 4; $i < $this->fileSize; $i+=4 ) {
			$temp = rtrim( substr( $this->fileCont, $i,4 ), "\0" );
			$tlen = strlen( $temp );
			if( $tlen == 0 ) continue;
			if( $tlen == 1 ) {
				$newCont .= $temp;
			} else {
				for( $j = 0; $j < $tlen; $j++ ) {
					$newCont .= '&#'.sprintf( "%02d", ord( $temp{$j}));
				}
			}
		}
		$this->fileCont = $newCont;
	}
/*===========================================================================*/
	function do_import() {
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parse_into_struct($parser,&$this->fileCont,&$this->values,&$this->tags);
		if (xml_get_error_code($parser)) {
			$this->error_and_die( $parser );
			return false;
		};
		xml_parser_free($parser);
		reset( $this->values );
		$val = current( $this->values );
		$this->imp_arr = $this->make_array($val['tag']);
		return true;
	}
/*===========================================================================*/
	function error_and_die( &$parser ) {
		$err = xml_error_string(xml_get_error_code($parser));
		$b_idx = xml_get_current_byte_index($parser);
		$frag = substr( $this->fileCont, $b_idx - 100, 200);
		$pref = htmlspecialchars(substr($frag,0,100));
		$suf = htmlspecialchars(substr($frag,101));
		$offender = htmlspecialchars(substr($frag,100,1));
		$this->errorMsg[] = "Viga lähteandmetes<br /><font color='red'><strong>$err</strong></font><br />\nTekstifragment: <pre>$pref<font color='red'><strong> ---&gt;&gt;$offender&lt;&lt;---</strong></font>$suf</pre>";
	}
/*===========================================================================*/
	function make_array( $old_tag ) {
		$temp_arr = array();
		$val = current( $this->values );
		if( $val['type'] == 'complete' ) {
			$temp_arr = $val['value'];
			return $temp_arr;
		}
		while( $val = next( $this->values )) {
			$attrib_arr = array();
			if ( isset( $val['attributes'] )) {
				foreach( $val['attributes'] as $key => $attrib ) {
					$attrib_arr[$key] = $attrib;
				}
			}
			switch ( $val['tag'] ) {
				case $old_tag:
					if ( $val['type'] != 'close' ) {
						continue 2;
					}
					break 2;
				default:
					$temp_temp = $this->make_array( $val['tag'] );
					if ( !empty( $attrib_arr )) {
						if( !is_array( $temp_temp )) {
							$temp_temp = array( $temp_temp );
						}
						$temp_temp = array_merge( $attrib_arr, $temp_temp );
					}
					if( isset($temp_arr[$val['tag']])) {
						if( is_array($temp_arr[$val['tag']]) ){
							$temp_arr[$val['tag']][] = $temp_temp;
						} else {
							$temp_arr[$val['tag']] = array(
									$temp_arr[$val['tag']],
									$temp_temp
								);
						}
					} else if( $temp_temp && count( $temp_temp ) > 1 ){
						$temp_arr[$val['tag']][] = $temp_temp;
					} else {
						$temp_arr[$val['tag']] = $temp_temp;
					}
			}
		}
		return $temp_arr;
	}
/*===========================================================================*/
	function save_data() {
		if( empty( $this->imp_arr )) {
			echo "Nothing to do!";
			return;
		}
// hinnad
		$temp_arr = $this->imp_arr['hinnad']['hind'];
		unset( $this->imp_arr['hinnad'] );
		if ( isset( $temp_arr ) && !empty( $temp_arr )) {
			$this->parse_hinnad( &$temp_arr );
		}
// ilmumisgraafikud
		$temp_arr = $this->imp_arr['ilmumisgraafikud']['ilmumisgraafik'];
		unset( $this->imp_arr['ilmumisgraafikud'] );
		if ( isset( $temp_arr ) && !empty( $temp_arr )) {
			$this->parse_graafik( &$temp_arr );
		}
	}
/*===========================================================================*/
	function parse_liigid( $pindeks, $liigid, $tyyp ) {
		if( empty( $liigid )) {
			return;
		}
		if( !is_array( $liigid )) {
			$liigid = array( $liigid );
		}
		$sqh = "replace into {$this->lva_temp} values ( '%s' , '%s' )";
		foreach( $liigid as $key=>$val ) {
			$val = $this->convert_unicode( $val );
			$sql = "select id from expp_liigid where liik = '$val' and tyyp_id = '{$tyyp}'";
			$row =& $this->db_fetch_row( $sql );
			$id = $row['id'];
			if( empty( $id )) {
				$sql = "insert into expp_liigid set liik = '$val', tyyp_id = '{$tyyp}' , esilehel='1'";
				$this->db_query( $sql );
				$id = $this->db_last_insert_id();
			}
			$sql = sprintf( $sqh, $pindeks, $id );
			$this->db_query( $sql );
		}
	}
/*===========================================================================*/
	function parse_hinnad( $temp_arr ) {
		$this->va_temp = $this->create_temp_table( $this->va_table );
		$this->hind_temp = $this->create_temp_table( $this->hind_table );
		$this->lva_temp = $this->create_temp_table( $this->lva_table );

		$sql1 = "replace {$this->va_temp} ( {$this->va_trans_sql} ) values ( '";

		foreach( $temp_arr as $key=>$val ) {
			if( !in_array( $val['hinna_liik'], $this->all_tyybid )) {
				echo "Vigane hinna liik! => ".$val['hinna_liik']."<br>\n";
				continue;
			}
			$val['toote_nimetus'] = $val['toode']['nimetus'];
			if( is_array( $val['toote_nimetus'] )) {
				echo "Vigane nimetus! => ".$this->convert_unicode( implode( '; ', $val['toote_nimetus'] ))."<br>\n";
				continue;
			}
			unset( $val['toode'] );
			foreach( $this->conv_table as $from ) {
				if( isset( $val[$from] )) {
					$val[$from] = addslashes( $this->convert_unicode( trim( $val[$from] )));
				}
			}

			$this->parse_liigid( $val['toote_nimetus'], $val['valjaande_liik']['liik'], $val['ajaleht'] );
			unset( $val['valjaande_liik'] );

			$val['algus'] = ( isset( $val['algus'] ) && !empty( $val['algus'] ) ? str_replace( 'T', ' ', $val['algus'] ) : 'NULL' );
			$val['lopp'] = ( isset( $val['lopp'] ) && !empty( $val['lopp'] ) ? str_replace( 'T', ' ', $val['lopp'] ) : 'NULL' );
			$ins_arr = array();
			foreach( $this->va_trans_table as $from => $to ) {
				$ins_arr[] = ( isset( $val[$from] )? $val[$from]: "" );
			}
			if( !empty( $ins_arr )) {
				$sql = $sql1.implode( "','", $ins_arr )."' )";
				$this->db_query( $sql );
			}
			$this->parse_hind( $val );
			$this->looSeosed( $val['toimetus'], $val['toote_nimetus'], $val['valjaande_nimetus'], $val['pindeks'] );
	 	}
// merge temp & orig
		$sql_tags = array();
		$sql = "SELECT DISTINCT pindeks FROM ".$this->va_temp." ORDER BY pindeks";
		$this->db_query( $sql );
		while( $row = $this->db_next()) {
			$sql_tags[] = $row['pindeks'];
		}
		$str1 = implode( "','", $sql_tags );

		$sql_tags = array();
		$sql = "select distinct toote_nimetus from {$this->lva_temp}";
		$this->db_query( $sql );
		while( $row = $this->db_next()) {
			$sql_tags[] = $row['toote_nimetus'];
		}
		$str2 = implode( "','", $sql_tags );
		$sql_tags = array();
		$sql_tags[] = "DELETE FROM {$this->lva_table} WHERE toote_nimetus in ( '{$str2}' )";
		$sql_tags[] = "DELETE FROM ".$this->va_table." WHERE pindeks in ( '{$str1}' )";
		$sql_tags[] = "UPDATE ".$this->hind_table." SET lubatud = 'ei', changetime=now() WHERE pindeks in ( '{$str1}' )";
		$sql_tags[] = "REPLACE {$this->lva_table} SELECT * FROM {$this->lva_temp}";
		$sql_tags[] = "REPLACE ".$this->va_table." SELECT * FROM ".$this->va_temp;
// impordi ajal korvis olnud asjad kaduma ei läheks
		$sql_tags[] = "DELETE FROM ".$this->hind_table." WHERE lubatud = 'ei' AND changetime < ( DATE_SUB(now(),INTERVAL 1 DAY)+0 )";
		$sql_tags[] = "REPLACE INTO ".$this->hind_table." ( {$this->hind_trans_sql},lubatud ) SELECT {$this->hind_trans_sql},'jah' FROM ".$this->hind_temp;
		$sql_tags[] = "DROP TABLE IF EXISTS ".$this->va_temp;
		$sql_tags[] = "DROP TABLE IF EXISTS ".$this->hind_temp;
		$sql_tags[] = "DROP TABLE IF EXISTS ".$this->lva_temp;

		if( !empty( $sql_tags )) {
			foreach( $sql_tags as $sql ) {
				$this->db_query( $sql );
			}
		}
		$ch = get_instance("cache");
		$ch->full_flush();
	}
/*===========================================================================*/
	function parse_graafik( $temp_arr ) {
		$this->ilm_temp = $this->create_temp_table( $this->ilm_table );

		$str1 = implode( ' , ', $this->ilm_trans_table );
		$sql1 = "REPLACE ".$this->ilm_temp." ( $str1 ) values ";

		$sql_ilm = "( '%s', '%d', '%s' )";
		foreach( $temp_arr as $key=>$val ) {
			$sql_tags = array();
			$valjaanne = $this->convert_unicode( $val['valjaanne'] );
			foreach( $val['ilmumisinfo'] as $key2 => $val2 ) {
				$sql_tags[] = sprintf( $sql_ilm, $valjaanne, $val2['number'], $val2['ilmumiskpv'] );
			}
			if( !empty( $sql_tags )) {
				$sql = $sql1.implode( ',', $sql_tags );
				$this->db_query( $sql );
			}
	 	}
// merge temp & orig
		$sql_tags = array();
		$sql1 = "DELETE FROM ".$this->ilm_table." WHERE valjaanne = '%s' AND ilmumiskpv BETWEEN '%s' and '%s'";
		$sql = "select valjaanne, min( ilmumiskpv ) as vahim, max( ilmumiskpv ) as suurim from ".$this->ilm_temp." group by valjaanne";
		$this->db_query( $sql );
		while( $row =& $this->db_next()) {
			$sql_tags[] = sprintf( $sql1, $row['valjaanne'], $row['vahim'], $row['suurim'] );
		}
		$sql_tags[] = "REPLACE ".$this->ilm_table." SELECT * FROM ".$this->ilm_temp;
		$sql_tags[] = "DROP TABLE IF EXISTS ".$this->ilm_temp;
		if( !empty( $sql_tags )) {
			foreach( $sql_tags as $sql ) {
				$this->db_query( $sql );
			}
		}
	}
/*===========================================================================*/
	function create_temp_table( $table ) {
		$sql = "SHOW CREATE TABLE $table";
		$row =& $this->db_fetch_row( $sql );
		$dt = $row["Create Table"];
		preg_match("/CREATE TABLE `(\w*)` \(/",$dt,$m);
		$tablename = $m[1];
		$tempname = "temp_" . $table;
		$sql = "DROP TABLE IF EXISTS $tempname";
		$this->db_query( $sql );
		$sql = preg_replace("/(CREATE TABLE `)(\w*)(` \()/","\\1temp_\\2\\3",$dt);
		$this->db_query( $sql );
		return $tempname;
	}
/*===========================================================================*/
	function convert_unicode($source) {
		// utf8_decode doesn't work here
		$retval = str_replace(chr(0xC3). chr(0xB5),"õ",$source);
		$retval = str_replace(chr(0xC3). chr(0xBC),"ü",$retval);
		$retval = str_replace(chr(0xC3). chr(0xB6),"ö",$retval);
		$retval = str_replace(chr(0xC3). chr(0xA4),"ä",$retval);
		$retval = str_replace(chr(0xC3). chr(0x96),"Ö",$retval);
		$retval = str_replace(chr(0xC3). chr(0x95),"Õ",$retval);
		$retval = str_replace(chr(0xC3). chr(0xB4),"õ",$retval);
		$retval = str_replace(chr(0xC3). chr(0x84),"Ä",$retval);
		$retval = str_replace(chr(0xC3). chr(0x9C),"Ü",$retval);
		$retval = str_replace(chr(0xC5). chr(0xA0),"&#0352;",$retval);
		$retval = str_replace(chr(0xC3). chr(0xA9),"&#0233;",$retval);
		$retval = str_replace(chr(0xC5). chr(0xA1),"&#0353;",$retval);
		$retval = str_replace(chr(0xC5). chr(0xBD),"&#381;",$retval);
		$retval = str_replace(chr(0xC5). chr(0xBE),"&#382;",$retval);
//		$retval = utf8_decode( $retval );
		
		return $retval;
	}
/*===========================================================================*/
	function array_fill( $start, $count, $filler ) {
		$retVal = array();
		for ( $i = 0; $i < $count; $i++ ) {
			$retVal[$i + $start] = $filler;
		}
		return $retVal;
	}
/*===========================================================================*/
	function looSeosed( $toimetus, $seksioon, $toode, $pindeks ) {
// kui toode on olemas, siis ilmselt on kogu see jama tehtud ning rohkem edasi ei uuri! = performance
		$co_ol = new object_list(array(
			"class_id" => CL_EXPP_PUBLICATION,
			"name" => $toode,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ( $co_ol->count() > 0 )
			return;

		// kasutajagrupid
		$ugParent = aw_ini_get("groups.tree_root");
// Saadan lubatud nimekirja vajalikest objektidest, mida tuleb impordi käigus luua:

		// menüü Kirjastused
		$co_ol = new object_list(array(
			"class_id" => CL_MENU,
			"name" => "Kirjastused",
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ($co_ol->count() > 0) {
			$_to = $co_ol->begin();
			$mnParent = $_to->id();
		} else {
			$mnParent = aw_ini_get("sitemap.rootmenu");
		}

// 1. Kasutajagrupp ja Organisatsioon iga Toimetuse (nt Ajakirjade Kirjastus) kohta
		$toimetusGrupp = $this->createGroup( $toimetus, $ugParent );
		$toimetusOrg	= $this->createOrg( $toimetus, $mnParent );

// 2. Kasutajagrupp ja Üksus iga Toimetuse alla kuuluva Väljaande (nt Anne) kohta
		$toodeGrupp		= $this->createGroup( $seksioon, $ugParent );
		$toodeSeksioon	= $this->createSection( $seksioon, $mnParent, $toimetus );

// 3. Kasutaja Väljaanne.Toimetaja iga Väljaande Kasutajagrupi sisse
		$toodeName 		= $seksioon.".toimetus";
		$toodeUser		= $this->createUser( $toodeName, $ugParent, array( $toodeGrupp->id()));

// 4. Toimetuse nimeline Kaust (nt Ajakirjade Kirjastus) iga Toimetuse kohta (parentiks ID=765,  Väljaannete haldusobjektid)
		$toodeKaust		= $this->createMenu( $toimetus, 765 );

// 5. Väljaannete haldusobjekt iga Toimetuse alla kuuluva Väljaande (nt Anne) kohta, parentiks vastava Toimetuse nimeline Kaust
		$tooteHaldus	= $this->createCRM( $seksioon, $toodeKaust->id());

// 6. Õigus tüüpi seos Organisatsiooni ning iga Väljaande Kasutajagrupi vahel kõigi õigustega

		// organisatsiooni obj: $toimetusOrg
		// väljaande kasutaja grupp: $toodeGrupp -- kasutajagrupid: $ugParent all

		$toimetusOrg->acl_set($toodeGrupp, array(
			"can_add" => 1,
			"can_edit" => 1,
			"can_delete" => 1,
			"can_view" => 1
		));
		
// 7. Õigus tüüpi seos Väljaannete haldusobjekti ning vastava Väljaande Kasutajagrupi vahel kõigi õigustega

		// väljaannete haldus - $tooteHaldus
		// vastava väljaande kasutajagrupp
		$tooteHaldus->acl_set($toodeGrupp, array(
			"can_add" => 1,
			"can_edit" => 1,
			"can_delete" => 1,
			"can_view" => 1
		));

// 8. Väljaande objekt iga alamVäljaande kohta, seostatuna Väljaannete haldusobjektiga (seose tüübiga  Väljaanne). Vt Anne Seksieri
// http://andrus.dev.struktuur.ee/automatweb/orb.aw?class=expp_journal_management&action=list_aliases&id=735
// http://andrus.dev.struktuur.ee/automatweb/orb.aw?class=expp_publication&action=change&id=998&parent=765
// Selle objekti comment välja võiks panna publikatsiooni unikaalse ID, mis Reggyst tuleb, mille aluse vältida topeltobjektide loomist ja seoste säilimist.
		$tooteObjekt	= $this->createPub( $toode, $toodeKaust->id(), $pindeks);

		$tooteHaldus->connect(array(
			"to" => $tooteObjekt->id(),
			"type" => "RELTYPE_PUBLICATION"
		));

// 9. Toote Grupile määratakse rootmenüüks tooteHalduse objekt:
		$toodeGrupp->connect(array(
			"to" => $tooteHaldus->id(),
			"type" => "RELTYPE_ADMIN_ROOT",
		));

/*
		$g = get_instance(CL_GROUP);
		$g->add_user_to_group( $us, $ug1 );
		$g->add_user_to_group( $us, $ug2 );

		$us->connect(array(
			"to" => $se,
			"reltype" => "RELTYPE_PERSON"
		));
*/
	}

	function parse_hind( $arr_in ) {
		$sql1 = "insert into {$this->hind_temp} ( {$this->hind_trans_sql} ) values ( '";
		$sql2	= array(
				'pindeks'		=> $arr_in['pindeks'],
				'hkkood'       => $arr_in['hkkood'],
				'hinna_liik'	=> $arr_in['hinna_liik'],
				'hinna_tyyp'	=> $arr_in['tyyp'],
				'algus'			=> $arr_in['algus'],
				'lopp'			=> $arr_in['lopp'],
				'hinna_kirjeldus' => $arr_in['veebi_kirjeldus'],
				'kampaania'		=> $arr_in['kampaania'],
		);
		$row1 = $arr_in['hinnagraafikud']['hinnagraafik'];
		$row2 = array();
		foreach( $row1 as $val ) {
			$val['kestus']		= intval( $val['kestus'] );
			$val['baashind']	= trim( $val['baashind'] );
			$val['juurdekasv']= trim( $val['juurdekasv'] );
			
			$row2[$val['kestus']] = array(
				'kestus'		=> $val['kestus'],
				'baashind'	=> $val['baashind'],
				'juurdekasv'=> $val['juurdekasv']
			);

			if ( $val["juurdekasv"] > 0 && $arr_in['tyyp'] == 0 && !in_array( $arr_in['hinna_liik'], $this->ok_tyybid )) {
				for( $ii = $val["kestus"]+1; $ii < 13; $ii++ ){
					$row2[$ii] = array(
						'kestus'		=> $ii,
						'baashind'	=> sprintf( "%1.0f", $val["baashind"]+$val["juurdekasv"]*($ii-$val["kestus"])),
						'juurdekasv'=> $val['juurdekasv']
					);
				}
			}
		}
		foreach( $row2 as $val ) {
			$sql3	= $sql2 + array(
				'kestus'		=> $val['kestus'],
				'baashind'	=> $val['baashind'],
				'juurdekasv'=> $val['juurdekasv']
			);
			$sql = $sql1.implode( "','", $sql3)."' )";
			$this->db_query( $sql );
		}
	}
/*===========================================================================*/
	function createUser( $name, $parent, $group ) {
		$uid = substr( str_replace( ' ', '_', $name ), 0 , 50 );
		$co_ol = new object_list(array(
			"class_id" => CL_USER,
//			"name" => $name,
			"uid" => $uid,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ($co_ol->count() == 0) {
			$usi = get_instance(CL_USER);
			$us = $usi->add_user(array(
				"uid" => $uid,
//				"password" => $pass
				"use_md5_passwords" => true,
				"join_grp" => $group,
			));
//			arr( "User > $name > new > $parent" );
			$us->set_name( $name );
			$us->set_parent( $parent );
			$us->save();
		} else {
//			arr( "User > $name > old" );
			$us = $co_ol->begin();
		}
		return $us;
	}
/*===========================================================================*/
	function createGroup( $name, $parent ) {
		$co_ol = new object_list(array(
			"class_id" => CL_GROUP,
			"name" => $name,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ($co_ol->count() == 0) {
//			arr( "UserGroup > $name > new > $parent" );
			$ug = obj();
			$ug->set_class_id( CL_GROUP );
			$ug->set_parent( $parent );
			$ug->set_name( $name );
			$ug->set_prop( "name", $name );
			$ug->save();
		} else {
//			arr( "UserGroup > $name > old" );
			$ug = $co_ol->begin();
		}
		return $ug;
	}
/*===========================================================================*/
	function createOrg( $name, $parent ) {
		$co_ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"reg_nr" => $name,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ($co_ol->count() == 0) {
//			arr( "Company > $name > new > $parent" );
			$co = obj();
			$co->set_class_id(CL_CRM_COMPANY);
			$co->set_parent( $parent );
			$co->set_name( $name );
			$co->set_prop( 'reg_nr', $name );
			$co->save();
		} else {
//			arr( "Company > $name > old" );
			$co = $co_ol->begin();
		}
		return $co;
	}
/*===========================================================================*/
	function createSection( $name, $parent, $ext ) {
		$co_ol = new object_list(array(
			"class_id" => CL_CRM_SECTION,
			"name" => $name,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ($co_ol->count() == 0) {
//			arr( "Section > $name > new > $parent" );
			$se = obj();
			$se->set_class_id( CL_CRM_SECTION );
			$se->set_parent( $parent );
			$se->set_name( $name );
			$se->set_prop( "ext_id", $ext );
			$se->save();
		}
		else
		{
//			arr( "Section > $name > old" );
			$se = $co_ol->begin();
		}
		return $se;
	}
/*===========================================================================*/
	function createMenu( $name, $parent ) {
		$co_ol = new object_list(array(
			"class_id" => CL_MENU,
			"name" => $name,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ($co_ol->count() == 0) {
//			arr( "Menu > $name > new > $parent" );
			$se = obj();
			$se->set_class_id( CL_CRM_SECTION );
			$se->set_parent( $parent );
			$se->set_name( $name );
			$se->save();
		}
		else
		{
//			arr( "Menu > $name > old" );
			$se = $co_ol->begin();
		}
		return $se;
	}
/*===========================================================================*/
	function createCRM( $name, $parent ) {
		$co_ol = new object_list(array(
			"class_id" => CL_EXPP_JOURNAL_MANAGEMENT,
			"name" => $name,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ($co_ol->count() == 0) {
//			arr( "CRM > $name > new > $parent" );
			$se = obj();
			$se->set_class_id( CL_EXPP_JOURNAL_MANAGEMENT );
			$se->set_parent( $parent );
			$se->set_name( $name );
			
			// kood peab olema urlencode()-detud ja % asendatakse
			// #-dega
			$code = str_replace( "%", "#", urlencode( $name ) );
			$se->set_prop( "code", $code );
			
			// väljaande nimetus peaks siis olema ka see $name
			// nagu ma aru saan, nii et paneb selle ka siis:
			//
			// and i save the value to meta field manually, take it
			// out in management object directly from meta and set 
			// the publications_name value to it, the purpose of it
			// is, that as long as i change the property type to text
			// the property value will be deleted after the property is
			// saved - some aw weirdness --dragut
			$se->set_meta( "publications_name_value", $name );
			$se->save();
		}
		else
		{
//			arr( "CRM > $name > old" );
			$se = $co_ol->begin();
		}
		return $se;
	}
/*===========================================================================*/
	function createPerm(  )
	{

	}
/*===========================================================================*/
	function createPub( $name, $parent, $pindeks ) {
		$co_ol = new object_list(array(
			"class_id" => CL_EXPP_PUBLICATION,
			"name" => $name,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if ($co_ol->count() == 0) {
//			arr( "Pub > $name > new > $parent" );
			$se = obj();
			$se->set_class_id( CL_EXPP_PUBLICATION );
			$se->set_parent( $parent );
		//	$se->set_prop( "comment", $pindeks );
		// kommentaari sättimiseks on objektil meetod set_comment()
			$se->set_comment($pindex);
			$se->set_name( $name );
			$se->save();
		} else {
//			arr( "Pub > $name > old" );
			$se = $co_ol->begin();
		}
		return $se;
	}
/*===========================================================================*/
	function log( $class, $action = '', $pindeks = '', $toimetus = '', $va = '' ) {
		$ip = aw_global_get("HTTP_X_FORWARDED_FOR");
		if (!inet::is_ip($ip)) {
			$ip = aw_global_get("REMOTE_ADDR");
		}
		$sql = "INSERT DELAYED INTO expp_log SET"
			."  ip = '{$ip}'"
			.", lang = '{$this->lang}'"
			.", action = '".addslashes($action)."'"
			.", class = '".addslashes($class)."'"
			.", url = '".addslashes($GLOBALS['REQUEST_URI'])."'"
			.", pindeks = '".addslashes($pindeks)."'"
			.", toimetus = '".addslashes($toimetus)."'"
			.", valjaanne = '".addslashes($va)."'"
			.", sid = '".session_id()."'"
			.", time = now()";
		$this->save_handle();
		$this->db_query($sql);
		$this->restore_handle();
	}
/*===========================================================================*/
	function sendFile( $File ) {
		$this->log( get_class($this), "import", '', '', $File );
		$i = get_instance("protocols/mail/aw_mail");

		$upload_dir =  $this->cfg["site_basedir"].'/upload/';
		$attachment = fread($fp = fopen( $upload_dir.$File, 'r'), filesize($upload_dir.$File));
		fclose($fp);

		// lisame teksti kujul sisu ja saaja/saatja
		$i->create_message(array(		
			"froma" => "tellimine@tellimine.ee",
			"fromn" => "Faili import", 
			"subject" => "expp import",
			"to" => "andrus@hv.ee",
			"body" => "tuli selline fail",
		));
		// lisame html sisu
		//	$i->htmlbodyattach(array("data" => preg_replace("/<script(.*)>(.*)<\/script>/imsU", "", $app)));

		// liasme faili attachi
		$i->fattach(array(
			"content" => $attachment,
			"filename" => $File,
			"contenttype" => "application/octet-stream",
			"name" => $File
		));

		// saadame meili teele
		$i->gen_mail();
	}
}
?>
