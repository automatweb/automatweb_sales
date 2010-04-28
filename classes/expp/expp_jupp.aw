<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_jupp.aw,v 1.9 2007/11/23 07:18:28 dragut Exp $
// expp_jupp.aw - Expp Jupp 
/*

@classinfo syslog_type=ST_EXPP_JUPP relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_jupp extends class_base
{
	const AW_CLID = 983;

	var $valjad	= array(
			"algus"			=>	"algus",
			"arvenr"			=>	"arvenr",
			"eksempla"		=>	"eksempla",
			"lopp"			=>	"lopp",
			"maadress"		=>	"maadress",
			"maksumus"		=>	"maksumus",
			"rhkkood"		=>	"rhkkood",
			"rkmkood"		=> "kampaania",
			"memail"			=>	"memail",
			"menimi"			=>	"menimi",
			"mfax"			=>	"mfax",
			"mindeks"		=>	"mindeks",
			"misikukood"	=>	"misikukood",
			"mkorter"		=>	"mkorter",
			"mmaja"			=>	"mmaja",
			"mnimi"			=>	"mnimi",
			"mtanav"			=>	"mtanav",
			"mtelefon"		=>	"mtelefon",
			"mmobiil"		=>	"mmobiil",
			"saadress"		=>	"saadress",
			"semail"			=>	"semail",
			"senimi"			=>	"senimi",
			"sfax"			=>	"sfax",
			"sindeks"		=>	"sindeks",
			"sisikukood"	=>	"sisikukood",
			"skorter"		=>	"skorter",
			"smaja"			=>	"smaja",
			"snimi"			=>	"snimi",
			"stanav"			=>	"stanav",
			"stelefon"		=>	"stelefon",
			"smobiil"		=>	"smobiil",
			"tellkpv"		=>	"tellkpv",
			"tlkood"			=>	"id",
			"trykiarve"		=>	"trykiarve",
			"trykiokpakkumine"	=>	"trykiokpakkumine",
			"tyyp"			=>	"tyyp",
			"vaindeks"		=>	"vaindeks",
			"vvotja"			=>	"vvotja",
			"kanal"			=>	"kanal",
			"viitenumber"	=>	"viitenumber",
			"pank"			=> "pank"
		);

		var $to_replace = array( "\t", "\r", "\n", "\0", "\x0B" );
		var $lang;
	function expp_jupp()
	{
		$this->lang = aw_global_get("admin_lang_lc");

		$this->init(array(
			"tpldir" => "expp",
			"clid" => CL_EXPP_IMPORT
		));
	}

	/**  
		
		@attrib name=show is_public="1" caption="Expp" nologin="1" default="1" all_args="1"
		
		@param id type=int
		
		@returns
		
		@comment

	**/
	function show($arr){
		$i = get_instance("protocols/mail/aw_mail");

		// lisame html sisu
		// $i->htmlbodyattach(array("data" => preg_replace("/<script(.*)>(.*)<\/script>/imsU", "", $app)));

		$_dir =  $this->cfg["site_basedir"].'/failid/';


		$piiraeg	= date( "YmdHis", mktime( date("H")-1,date("i"),date("s"),date("m"),date("d"),date("Y")));
		$fname	= "{$piiraeg}.uus.txt";

		$sql = "UPDATE expp_arved SET staatus='saatmisel' WHERE staatus='uus' AND time<'$piiraeg'";
		$this->db_query( $sql );

		$sql			= "SELECT ".implode( ',', $this->valjad )." FROM expp_arved WHERE staatus='saatmisel'";
		$my_head		= implode( "\t", array_keys( $this->valjad ))."\n";
		$my_write	= 0;
		$this->db_query( $sql );
		if( $this->num_rows() > 0 ) {

			$this->log( get_class($this), "eksport", '', '', $filename );

			$fp = fopen( $_dir.$fname, "w");
			fwrite ( $fp, $my_head );
			while ( $row = $this->db_next()) {
				$row['mtelefon']	= $this->check_tel( $row['mtelefon'] );
				$row['mmobiil']	= $this->check_tel( $row['mmobiil'] );
				$row['mfax']	 	= $this->check_tel( $row['mfax'] );
				$row['stelefon']	= $this->check_tel( $row['stelefon'] );
				$row['smobiil']	= $this->check_tel( $row['smobiil'] );
				$row['sfax']		= $this->check_tel( $row['sfax'] );
				$row['pank']		= intval($row['pank']);
				if( $row['pank'] == 0 ) $row['pank'] = "";
				$first_row	= 1;
				$my_head	= "";
				foreach( $this->valjad as $key => $val ) {
					if ( $first_row )	$first_row = 0;
					else {
						$my_head.= "\t";
					}
					$my_head.= trim( str_replace( $this->to_replace, " ", $row[$val] ));
				}
				$my_head	.= "\n";
				$my_write += 1;
				fwrite ( $fp, $my_head );
			}
			fclose( $fp );
			// lõpp
			$sql="UPDATE expp_arved SET staatus='saadetud' WHERE staatus='saatmisel'";
			$this->db_query( $sql );
			if ( $my_write ) {
				$text = "Korras!\n\nExporditi fail nimega $fname\n\nwww.tellimine.ee exporter\nPalun ära vasta sellele kirjale!!!\n";

				$i->create_message(array(		
					"froma" => 'tellimine@tellimine.ee',
					"fromn" => 'www.tellimine.ee', 
					"subject" => 'veebitellimus : '.$piiraeg,
					"to" => "andrus@rae.ee,veebitellimus@expresspost.ee",
					"body" => $text,
				));

				$attachment = fread($fp = fopen( $_dir.$fname, 'r'), filesize($_dir.$fname));
				fclose($fp);

				// liasme faili attachi
				$i->fattach(array(
					"content" => $attachment,
					"filename" => $fname,
					"contenttype" => "application/octet-stream",
					"name" => $fname
				));

/*
	        $my_date = date("Ymd000000",mktime(0,0,0,date("m")-4,date("d"),date("Y")));
				$query="DELETE FROM arved WHERE staatus='saadetud' AND arved.time <'$my_date'";
				@mysql_db_query( $db_base, $query, $dbh );
				$query="DELETE FROM korv WHERE korv.time <'$my_date'";
				@mysql_db_query( $db_base, $query, $dbh );
				$query="DELETE FROM tellija WHERE tellija.time <'$my_date'";
				@mysql_db_query( $db_base, $query, $dbh );
*/
			}
		} else {
			$this->log( get_class($this), "eksport", '', '', 'tyhi' );
			// lisame teksti kujul sisu ja saaja/saatja
			$i->create_message(array(		
				"froma" => 'tellimine@tellimine.ee',
				"fromn" => 'www.tellimine.ee', 
				"subject" => 'veebitellimus : '.$piiraeg,
				"to" => "andrus@rae.ee,veebitellimus@expresspost.ee",
				"body" => "pole andmeid",
			));
		}
		// saadame meili teele
		$i->gen_mail();

//		$mail->send('', 'veebitellimus@expresspost.ee', 'www.tellimine.ee', 'tellimine@centrum.neti.ee', $piiraeg);
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
			$number_b = ereg_replace('(^puudub$|;puudub$|^puudub;|;puudub;)', '', $number_b );
			$number_b = ereg_replace('^\+', '00', $number_b );
			$kontroll = '^((6|06)[0-9]{6}|(04[3-8]|03[23589]|07[6-9]|(7|07)[^6-9])[0-9]{5}|05[0-35-8][0-9]{5,6}|0[89]00[0-9]{4,7}|00[0-9]{4,20}|puudub)$';
			if ( !ereg( $kontroll, $number_b ) ) {
				$error++;
				$retVal .= ($j>0?';':'').$number_a[$j];
			} else {
				$retVal .= ($j>0?';':'').$number_b;
			}
		}
		return $retVal;
	}

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

}

?>
