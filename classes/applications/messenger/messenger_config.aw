<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/messenger/messenger_config.aw,v 1.4 2007/12/06 14:33:40 kristo Exp $
// messenger_config.aw - Messengeri config 
/*

@classinfo syslog_type=ST_MESSENGER_CONFIG relationmgr=yes maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property msgs_on_page type=select 
@caption Kirju lehel


*/

/*
	Omadused:
		* Messenger avatakse eraldi aknas (msg_window). Kui see on valitud,
		siis kuvatakse objekt ilma saidi "raamita".

		* Vaikimisi avatav leht (msg_default_page). Siit valitud leht
		avatakse messengeri poole pöördumisel (default tab)

		* Peida menüüriba (msg_hide_menubar). Ilmselt peidab selle ülemise
		navigeerimise menüü

		* Uue kirja formaat kirjutamisel (msg_default_format) 
		(autodetect, richtext, plaintext): ehk millist editori kasutatakse.

		* Mitu kirja lehel (numbri valik)(msg_on_page) - kirjade näitamisel,
		mitu kirja ühel lehel on

		* Filtreerida meiliaadress "Kellelt" valjal? (msg_filter_address)
		I'm not so sure about that

		* Font kirja vaatamisel: (msg_font, msg_font_size)

		* Tekstivaljade laius (msg_field_width)

		* Tekstikasti mõõtmed (msg_box_width, msg_box_height)

		* Loetud kirjad liigutada (msg_move_read) (checkbox)

		* Loetud kirjade folder (msg_move_read_folder) (folderi valik)

		* Salvestada saadetud kirjad Outboxi (msg_store_sent)

		* Kustutatud kirjad kustutakse/viiaks trash folderisse (msg_ondelete)

		* Küsida kinnitust kirja saatmisel (msg_confirm_send)
		See on selleks, et näpuinvaliidid kogemata kirja ära ei saadaks

		* Vaikimisi prioriteet uue kirja kirjutamisel (msg_default_pri)
		 on miski tag kirja headeris

               * Kvootimismärk (msg_quotechar)

	       * Kirjale lisatavate attachide arv (msg_cnt_att)


*/


class messenger_config extends class_base
{
	function messenger_config()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. the default folder does not actually exist, 
		// it just points to where it should be, if it existed
		$this->init(array(
			"tpldir" => "messenger/messenger_config",
			"clid" => CL_MESSENGER_CONFIG
		));
	}

	function get_property($args)
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "msgs_on_page":
				$data["options"] = array(25 => 25,50 => 50,100 => 100);
				break;

		};
		return $retval;
	}

	/*
	function set_property($args = array())
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
	}	
	*/




}
?>
