<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/mailinglist/ml_list.aw,v 1.161 2009/08/22 20:08:06 markop Exp $
// ml_list.aw - Mailing list
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_MENU, on_mconnect_to)
@classinfo syslog_type=ST_MAILINGLIST relationmgr=yes no_status=1 r2=yes maintainer=markop allow_rte=2

@default table=objects
@default field=meta
@default method=serialize

@default group=general

------------------------------------------------------------------------

@property choose_menu type=relpicker reltype=RELTYPE_MEMBER_PARENT editonly=1 multiple=1 delete_rels_popup_button=1
@caption Kaustad millega liituda

@property sources_data_table type=table store=no no_caption=1
@caption Allikate tabel

@property choose_languages type=select multiple=1 field=meta method=serialize
@caption Keeled millega v&otilde;ib liituda

@property multiple_folders type=checkbox ch_value=1
@caption Lase liitumisel/lahkumisel kausta valida

@property multiple_languages type=checkbox ch_value=1
@caption Lase liitumisel valida keelt

@property msg_folder type=relpicker reltype=RELTYPE_MSG_FOLDER
@caption Kirjade asukoht

@property sub_form_type type=select rel=1
@caption Vormi t&uuml;&uuml;p

@property file_separator type=textbox 
@caption Failis nime ja mailiaadressi eraldaja (tab=/t)

@property redir_obj type=relpicker reltype=RELTYPE_REDIR_OBJECT rel=1
@caption Dokument millele suunata

@property redir_unsubscribe_obj type=relpicker reltype=RELTYPE_REDIR_OBJECT rel=1
@caption Dokument millele suunata lahkujad

@property member_config type=relpicker reltype=RELTYPE_MEMBER_CONFIG rel=1
@caption Listi liikmete seadetevorm

@property senders type=relpicker reltype=RELTYPE_SENDER store=connect multiple=1
@caption V&otilde;imalikud saatjad

@property register_data_cgfform_id type=relpicker reltype=RELTYPE_RD_CONFIG
@caption Registri andmete seadetevorm

@property default_bounce type=textbox
@caption Default bounce

@property classinfo_allow_rte type=chooser field=meta method=serialize
@caption RTE

@property no_mails_to_base type=checkbox ch_value=1
@caption Mitte kirjutada k&otilde;iki maile baasi

@property personnel_management type=relpicker reltype=RELTYPE_PERSONNEL_MANAGEMENT store=connect
@caption Personalikeskkond

@property subscribe_mail type=relpicker reltype=RELTYPE_SUBSCRIBE_MAIL store=connect
@caption Liitumise kirja templeit

@property unsubscribe_mail type=relpicker reltype=RELTYPE_UNSUBSCRIBE_MAIL store=connect
@caption Lahkumise kirja templeit

@groupinfo membership caption=Liikmed 
------------------------------------------------------------------------
@groupinfo search caption=Nimekiri parent=membership
@default group=search
	@property search_tb type=toolbar no_caption=1 store=no
	@caption Otsingu toolbar

	@layout search_lay type=hbox width=20%:80%
		@layout search_left_lay type=vbox parent=search_lay
			@property search_menu type=text captionside=top parent=search_left_lay
			@caption Kaustad kust otsida

			property req_members_s type=text store=no parent=search_left_lay captionside=top
			caption Otsi ka alamobjektide alt

			@property search_mail type=textbox store=no parent=search_left_lay captionside=top size=20
			@caption E-mail
			
			@property search_name type=textbox store=no parent=search_left_lay captionside=top size=20
			@caption Nimi
			
			@property search_submit type=submit store=no no_caption=1 parent=search_left_lay
			@caption Otsi

		@layout list_lay closeable=1 type=vbox parent=search_lay
			@property search_table type=table store=no no_caption=1 parent=list_lay
			@caption Liimete nimekiri

@groupinfo export_members caption=Eksport parent=membership
@default group=export_members

@property export_folders type=relpicker reltype=RELTYPE_MEMBER_PARENT editonly=1 multiple=1 delete_rels_popup_button=1
@caption Kaustad, mida eksportida

property req_members_e type=text
caption Otsi ka alamobjektide alt

@property export_type type=chooser orient=vertical store=no
@caption Formaat

@property export_from_date type=date_select store=no default=-1
@caption Alates kuup&auml;evast

@property exp_sbt type=submit
@caption Ekspordi

------------------------------------------------------------------------

@groupinfo export_to_file caption="Eksport faili" parent=membership
@default group=export_to_file

@property expf_path type=textbox 
@caption Kataloog serveris

@property expf_num_per_day type=textbox size=5
@caption Mitu korda p&auml;evas eksport teha

@property expf_next_time type=text store=no
@caption Millal j&auml;rgmine eksport toimub

-----------------------------------------------------------------------
@groupinfo subsc_parent caption=Liitumine
@groupinfo subscribing caption=Liitumine parent=subsc_parent
@default group=subscribing

	@property admin_subscribe_folders type=text store=no
	@caption Kaustad,&nbsp;kuhu&nbsp;liituda

	@property confirm_subscribe type=checkbox ch_value=1 
	@caption Liitumiseks on vaja kinnitust

	@property confirm_subscribe_msg type=relpicker reltype=RELTYPE_ADM_MESSAGE
	@caption Liitumise kinnituseks saadetav kiri
	
	@property import_textfile type=fileupload store=no
	@caption Impordi liikmed tekstifailist
	
	@property mass_subscribe type=textarea rows=25 store=no
	@caption Massiline liitumine <br>(Iga liituja eraldi real, nimi ja aadress komaga eraldatud)
	@comment Iga liituja eraldi real, nimi ja aadress komaga eraldatud

------------------------------------------------------------------------

@groupinfo unsubscribing caption=Lahkumine parent=subsc_parent
@default group=unsubscribing

	@property admin_unsubscribe_folders type=text store=no
	@caption Kaustad, kust lahkuda
	
	@property confirm_unsubscribe type=checkbox ch_value=1 
	@caption Lahkumiseks on vaja kinnitust
	
	@property confirm_unsubscribe_msg type=relpicker reltype=RELTYPE_ADM_MESSAGE 
	@caption Lahkumise kinnituseks saadetav kiri
	
	@property delete_textfile type=fileupload store=no
	@caption Kustuta tekstifailis olevad aadressid
	
	@property mass_unsubscribe type=textarea rows=25 store=no 
	@caption Massiline&nbsp;kustutamine <br> (Aadressid eraldi real)
------------------------------------------------------------------------


@groupinfo raports caption=Kirjad

@groupinfo list_status caption="Saadetud kirjad" parent=raports submit=no

@default group=list_status

@property list_status_tb type=toolbar store=no no_caption=1
@caption Listi staatuse toolbar

@property list_status_table type=table store=no no_caption=1
@caption Listi staatus

@layout status_bottom type=hbox

@property send_button type=button store=no no_caption=1 parent=status_bottom
@caption Saada kohe

@property delete_old_button type=button store=no no_caption=1  parent=status_bottom
@caption Kustuta vanad kirjad

@property db_mail_count type=text store=no no_caption=1  parent=status_bottom
@caption Vanu kirju andmebaasis

------------------------------------------------------------------------

@groupinfo unsent caption="Saatmata kirjad" parent=raports submit=no
@default group=unsent

@property unsent_tb type=toolbar store=no no_caption=1
@caption Listi staatuse toolbar

@property unsent_table type=table store=no no_caption=1
@caption Listi staatus

------------------------------------------------------------------------

@groupinfo write_mail caption="Saada kiri" parent=raports 
@default group=write_mail

@property mail_toolbar type=toolbar no_caption=1
@caption Maili toolbar

@layout mail_top type=hbox closeable=1 area_caption=Seaded 
//width=20%:20%:30%:30%
	@property send_away type=checkbox ch_value=1 store=no parent=mail_top 
	@caption Saada peale salvestamist &auml;ra
	
	@property save_as_new type=checkbox ch_value=1 parent=mail_top 
	@caption Salvesta uue kirjana
	
	@property no_fck type=checkbox ch_value=1 parent=mail_top 
	@caption Maili kirjutamine plaintext vaates

	property take_out_adrr type=checkbox ch_value=1 parent=mail_top 
	caption Enne saatmist lase m&otilde;ningad aadressid v&auml;lja praakida

@layout write_message_layout type=hbox width=40%:60% 
	@layout wml type=vbox parent=write_message_layout area_caption=&nbsp;
	
		@property write_user_folder type=relpicker reltype=RELTYPE_MEMBER_PARENT editonly=1 multiple=1  delete_rels_popup_button=1 parent=wml captionside=top
		@caption Grupid kellele kiri saata
		
		property req_members_m type=text parent=wml captionside=top
		caption Otsi ka alamobjektide alt
		
		@property bounce type=textbox parent=wml captionside=top
		@caption Bounce aadress
		
		@property mfrom_name type=textbox store=no parent=wml captionside=top
		@caption Saatja nimi
		
		@property mfrom type=select store=no parent=wml captionside=top
		@caption Saatja e-maili aadress
		
		@property subject type=textbox  size=50 store=no parent=wml captionside=top
		@caption Teema

		
		@property template_selector type=select store=no parent=wml captionside=top
		@caption Vali template
		
		@property html_mail type=checkbox ch_value=1 parent=wml captionside=top no_caption=1
		@caption HTML kiri
		
		@property register_data type=text parent=wml
		@caption Registri andmed:
		
		@property copy_template type=select store=no parent=wml captionside=top
		@caption Vali template mille sisusse kopeerida
		
		property write_mail type=callback callback=callback_gen_write_mail store=no no_caption=1 parent=wml captionside=top
		caption Maili kirjutamine

		@property legend type=text store=no parent=wml captionside=top no_caption=1
		@caption Legend
			
	@layout wmr type=vbox parent=write_message_layout area_caption=&nbsp;

		@property message type=textarea cols=70 rows=60 store=no parent=wmr no_caption=1
		@caption Sisu

@property message_id type=hidden store=no parent=wmr no_caption=1
@caption Kirja id

property aliasmgr type=aliasmgr store=no editonly=1 group=relationmgr trans=1
caption Aliastehaldur

------------------------------------------------------------------------

@groupinfo mail_report caption="Kirja raport" parent=raports submit=no
@default group=mail_report

@property mail_subject type=text store=no 
@caption Teema

@property mail_percentage type=text store=no 
@caption Saadetud

@property mail_start_date type=text store=no 
@caption Saatmise algus

@property mail_last_batch type=text store=no 
@caption Viimane kiri saadeti

@property list_source type=text store=no
@caption Saadetud liikmete allikas

@property mail_report table type=table store=no no_caption=1
@caption Meili raport

------------------------------------------------------------------------

@groupinfo show_mail caption="Listi kiri" parent=raports submit=no
@default group=show_mail
@property show_mail_subject type=text store=no
@caption Teema

@property show_mail_from type=text store=no
@caption Kellelt

@property show_mail_message type=text store=no no_caption=1
@caption Sisu


------------------------------------------------------------------------

@reltype MEMBER_PARENT value=1 clid=CL_MENU,CL_GROUP,CL_USER,CL_FILE,CL_CRM_SELECTION,CL_CRM_SECTOR,CL_PERSONNEL_MANAGEMENT_CV_SEARCH_SAVED,CL_CRM_CATEGORY
@caption Listi liikmete allikas

@reltype REDIR_OBJECT value=2 clid=CL_DOCUMENT
@caption &Uuml;mbersuunamine

@reltype ADM_MESSAGE value=3 clid=CL_MESSAGE
@caption administratiivne teade 

@reltype TEMPLATE value=4 clid=CL_MESSAGE_TEMPLATE
@caption kirja template

@reltype MEMBER_CONFIG value=5 clid=CL_CFGFORM
@caption Listi liikme seadetevorm

@reltype MSG_FOLDER value=6 clid=CL_MENU
@caption Kirjade kaust

@reltype LANGUAGE value=7 clid=CL_LANGUAGE
@caption Keeled

@reltype SENDER value=8 clid=CL_ML_MEMBER
@caption Saatja

@reltype RELTYPE_REGISTER_DATA value=9 clid=CL_REGISTER_DATA
@caption Registri andmed

@reltype RELTYPE_RD_CONFIG value=10 clid=CL_CFGFORM
@caption Registri andmete seadete vorm

@reltype PERSONNEL_MANAGEMENT value=12 clid=CL_PERSONNEL_MANAGEMENT
@caption Personalikeskkond

@reltype SUBSCRIBE_MAIL value=13 clid=CL_MESSAGE_TEMPLATE
@caption Liitumise kirja templeit

@reltype UNSUBSCRIBE_MAIL value=14 clid=CL_MESSAGE_TEMPLATE
@caption Lahkumise kirja templeit


*/

define("ML_EXPORT_CSV",1);
define("ML_EXPORT_NAMEADDR",2);
define("ML_EXPORT_ADDR",3);
define("ML_EXPORT_ALL", 4);

class ml_list extends class_base
{
	function ml_list()
	{
		$this->init(array(
			"tpldir" => "automatweb/mlist",
			"clid" => CL_ML_LIST,
		));
		lc_load("definition");
		lc_site_load("ml_list", &$this);
	}

	function callback_mod_layout(&$arr)
	{
		switch($arr["name"])
		{
			case "list_lay":
				$arr["area_caption"] = sprintf(t("Mailinglisti liikmed"), $arr["obj_inst"]->name());
				break;
			case "search_left_lay":
				$arr["area_caption"] = sprintf(t("Liikmete otsingu parameetrid"), $arr["obj_inst"]->name());
				break;
		}
		return true;
	}

	function callback_pre_edit($arr)
	{
		if(($arr["group"] == "write_mail") && (!$arr["obj_inst"]->prop("no_fck")))
		{
			$arr["classinfo"]["allow_rte"] = $arr["obj_inst"]->prop("classinfo_allow_rte");
		}
		if($arr["obj_inst"]->prop("no_fck"))
		{
			$arr["classinfo"]["allow_rte"] = 0;
		}
	}

	function on_mconnect_to($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("from.class_id") == CL_ML_LIST)
		{
			if($con->prop("reltype") == 6)
			{
				$obj = $con->from();
				$fld = $obj->prop("msg_folder");
				if(empty($fld))
				{
					$obj->set_prop("msg_folder", $con->prop("to"));
					$obj->save();
				}
			}
			elseif($con->prop("reltype") == 1)
			{
				$to = $con->to();
				$group = obj(group::get_non_logged_in_group());
				$to->acl_set($group, array("can_view" => 1, "can_add" => 1));
				$to->save();
			}
		}
	}

	function choose_addresses($arr)
	{/*
		$members = $this->get_members(array("id" => $arr["id"]));
		classload("vcl/table");
		$t = new vcl_table();
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));

		$t->define_field(array(
			"caption" => t("Nimi"),
			"name" => "name"
		));

		$t->define_field(array(
			"caption" => t("Mailiaadress"),
			"name" => "email"
		));

		$t->define_field(array(
			"caption" => t("Organisatsioon"),
			"name" => "org"
		));

		$t->define_field(array(
			"caption" => t("Ametinimetus"),
			"name" => "pro"
		));
		$t->define_field(array(
			"caption" => t("Osakond"),
			"name" => "sec"
		));
		$t->set_header(t("Praagi isikud kirja saajate nimekirjast v&auml;lja"));
		foreach($members as $member)
		{
			$t->define_data(array(
				"oid" => $member["oid"],
				"name" => $member["name"],
				"email" => $member["mail"],
				"org" => get_name($member["co"]),
				"pro" => get_name($member["pro"]),
				"sec" => get_name($member["section"]),
			));
		}

		classload("cfg/htmlclient");
		$htmlc = new htmlclient(array(
			'template' => "default",
		));
		$htmlc->start_output();



		$htmlc->add_property(array(
			"name" => "table",
			"type" => "text",
			"value" => $t->draw(),
		));

		$htmlc->add_property(array(
			"name" => "submitb",
			"type" => "submit",
			"value" => "Saada",
			"class" => "sbtbutton"
		));

		$htmlc->finish_output(array(
			"action" => "submit_choose_addresses",
			"method" => "POST",
			"data" => array(
				"id" => $arr["id"],
				"list_id" => $arr["list_id"],
				"mfrom" => $mfrom,
				"orb_class" => "ml_list",
				"reforb" => 1
			)
		));
		$html = $htmlc->get_result();

		$tp = get_instance("vcl/tabpanel");
		$tp->add_tab(array(
			"active" => true,
			"caption" => t("Kirja saatmine"),
			"link" => aw_global_get("REQUEST_URI")
		));
		return $tp->get_tabpanel(array(
			"content" => $html
		));

*/
	}


	/** 
		@attrib name=submit_choose_addresses all_args=1
		
	**/
	function submit_choose_addresses($args)
	{
		extract($args);
		$mail = obj($args["id"]);
		$mail->set_meta("chosen_before_sent" , $args["sel"]);
		$mail->set_meta("let_choose_addresses" , 0);
		$mail->save();
		$url = $this->mk_my_orb("post_message" , array(
			"id" =>  $args["id"],
			"targets" => $args["list_id"],
		));
		die("<script language='javascript'>
			window.location = \"".$url."\";

		</script>");
	}


	/** saadab teate $id listidesse $targets(array stringidest :listinimi:grupinimi)
		@attrib name=post_message
		@param id required 
		@param targets optional 
		
	**/
	function post_message($args)
	{
		extract($args);

		if($to_post)
		{
			return $this->submit_post_message(array(
				"list_id" => $mto,
				"id" => $targets,
			));
		}

		$id = (int)$id;//teate id
		$listinfo = new object($targets);
		if(is_oid($id))
		{
			$msg = obj($id);
			$mfrom = $msg->prop("mfrom");
//			if($msg->meta("let_choose_addresses"))
//			{
//				return $this->choose_addresses(array(
//					"list_id" => $targets,
//					"id" => $id,
//				));
//			}
		}

		$this->mk_path(0, "<a href='".aw_global_get("route_back")."'>".t("Tagasi")."</a>&nbsp;/&nbsp;".t("Saada teade"));
		classload("cfg/htmlclient");
		$htmlc = new htmlclient(array(
			'template' => "default",
		));
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "start_at",
			"type" => "datetime_select",
			"caption" => t("Millal saata"),
			"value" => time() - 13
		));
		$htmlc->add_property(array(
			"name" => "patch_size",
			"type" => "textbox",
			"value" => 100,
			"caption" => t("Mitu teadet korraga"),
		));
		$htmlc->add_property(array(
			"name" => "delay",
			"type" => "textbox",
			"value" => 0,
			"caption" => t("Saatmiste vahel oota (min)"),
		));

		$htmlc->add_property(array(
			"name" => "submitb",
			"type" => "submit",
			"value" => "Saada",
			"class" => "sbtbutton"
		));

		$htmlc->finish_output(array(
			"action" => "submit_post_message",
			"method" => "POST",
			"data" => array(
				"id" => $id,
				"list_id" => $listinfo->id(),
				"mfrom" => $mfrom,
				"orb_class" => "ml_list",
				"reforb" => 1
			)
		));
		$html = $htmlc->get_result();

		$tp = get_instance("vcl/tabpanel");
		$tp->add_tab(array(
			"active" => true,
			"caption" => t("Kirja saatmine"),
			"link" => aw_global_get("REQUEST_URI")
		));
		return $tp->get_tabpanel(array(
			"content" => $html
		));
	}

	/** See handleb juba oiget postitust, siis kui on valitud saatmise ajavahemikud
		@attrib name=submit_post_message
	**/
	function submit_post_message($args)
	{
		extract($args);
		$id = (int)$id;
		$gid = (int)$gid;
		load_vcl('date_edit');
		unset($aid);//umm?
		$list_id = (int)$args["list_id"];
		$_start_at = date_edit::get_timestamp($start_at);
		$_delay = $delay * 60;
		$_patch_size = (int)$patch_size;
		$count = 0;
		$this->list_id = $list_id;
		$this->get_members(array("id" => $id, "no_return" => 1));
		
		$mail_obj = obj($id);
		$ll = get_instance("languages");
		$mail_obj->set_meta("charset" , $ll->get_charset());
		
		$count = $this->member_count;
		// mark the queue as "processing" - 5
		
		$this->db_query("INSERT INTO ml_queue (lid,mid,gid,uid,aid,status,start_at,last_sent,patch_size,delay,position,total)
			VALUES ('$list_id','$id','$gid','".aw_global_get("uid")."','$aid','5','$_start_at','0','$_patch_size','$_delay','0','$count')");
		$qid = $this->db_fetch_field("SELECT max(qid) as qid FROM ml_queue", "qid");
		$mail_obj = obj($id);
		$mail_obj -> set_meta("mail_data" , array(
			"mail_id" => $id,
			"list_id" => $list_id,
			"qid" => $qid,
			"mfrom" => $mfrom,
		));
		$mlq = get_instance("applications/mailinglist/ml_mail_gen");
		$mlq->bg_control(array("id" => $id, "do" => "start"));

		$this->_log(ST_MAILINGLIST, SA_SEND, sprintf(t("saatis meili %s listi %s:%s"),$id, $v["name"], $gname) ,$lid);
		return aw_global_get("route_back");
	}

	/** (un)subscribe an address from(to) a list 
		@attrib name=subscribe nologin="1" 
		@param id required type=int 
		@param rel_id required type=int 
	**/
	function subscribe($args = array())
	{
		$list_id = $args["id"];
		$rel_id = $args["rel_id"];

		$list_obj = new object($list_id);
		// I have to check whether subscribing requires confirmation, and if so, send out the confirm message
		// subscribe confirm works like this - we still subscribe the member to the list, but make
		// her status "deactive" and generate her a confirmation code
		// confirm code is added to the metad
		$ml_member = get_instance(CL_ML_MEMBER);
		$allow = false;
		$use_folders = array();
		$choose_menu = $list_obj->prop("choose_menu");	
		// need to check those folders
		foreach($choose_menu as $menu_id => $menu)
		{
			if(!is_oid($menu) || !$this->can("add" , $menu))
			{
				unset($choose_menu[$menu_id]);	
			}
		}
		
		if ($list_obj->prop("multiple_folders") == 1)
		{
			if (!empty($args["subscr_folder"]))
			{
				// check the list of selected folders against the actual connections to folders
				// and ignore ones that are not connected - e.g. don't take candy from strangers
				foreach($args["subscr_folder"] as $ml_connect=>$ml_id)
				{ 
//					if (in_array($ml_connect , $choose_menu))
//					{
						$use_folders[] = $ml_connect;
//					}
				}
			}
			if (sizeof($use_folders) > 0)
			{
				$allow = true;
			}
		}
		else
		{
			$use_folders = $list_obj->prop("choose_menu");
			$allow = true;
		};
		if(is_array($args["subscr_lang"]))
		{
//			$lang_id = $list_obj->lang_id();
			$lang_id = aw_global_get("lang_id");
			$temp_use_folders = array();
			foreach ($args["subscr_lang"] as $user_lang => $user_lang_id)
			{
				foreach ($use_folders as $folder_id => $val)
				{
					if ($user_lang == $lang_id)
					{
						$temp_use_folders[] = $val;
					}
					else
					{
						$o = obj($val);
						$conns = $o->connections_from(array(
							"type" => "RELTYPE_LANG_REL",
							"to.lang_id" => $user_lang,
						));
						
						if(count($conns)<1)
						{
							$conns_to_orig = $o->connections_to(array(
							"type" => 22,
					//		"from.lang_id" => $user_lang,
							));
						}
						
						foreach($conns_to_orig as $conn)
						{
							if($conn->prop("from.lang_id") == $user_lang)
							{
							 $temp_use_folders[] = $conn->prop("from");
							}
							else {
								$from_obj = obj($conn->prop("from"));
								$conns = $from_obj->connections_from(array(
									"type" => "RELTYPE_LANG_REL",
									"to.lang_id" => $user_lang,
								));
								foreach($conns as $conn)
								{
									$temp_use_folders[] = $conn->prop("to");
								}			
							}
						}						
						foreach($conns as $conn)
						{
							$temp_use_folders[] = $conn->prop("to");
						}			
					}
				}
			}
			$use_folders = $temp_use_folders;
		}
		if (empty($args["email"]))
		{
			$args["email"] = $args["mail"];
		}
		if (empty($args["mail"]))
		{
			$args["mail"] = $args["email"];
		}
		if(!is_email($args["mail"]))
		{
			$args["op"] = 0 ;
		}
		$request = $args;
		if (is_array($args["udef_txbox"]))
		{
			foreach($args["udef_txbox"] as $key => $val)
			{
				$request["udef_txbox" . $key] = $val;
			}
		}

		$cfgform = $list_obj->prop("member_config");
		$errors = $ml_member->validate_data(array(
			"request" => $request,
			"cfgform_id" => $cfgform,
		));
		
		foreach($use_folders as $key => $folder)
		{
			$members = $this->get_all_members(array($folder));
			if(in_array($args["mail"], $members) || in_array($args["email"], $members))
			{
				unset($use_folders[$key]);
			}
		}
	
		$erx = array();
		
		if(count($use_folders) < 1 && $args["op"] == 1)
		{
			$allow = false;
			$args["op"] = 0;
			//$erx["XXX"]["msg"] = t("Sellise aadressiga inimene on juba valitud listidega liitunud");
		}
		
		if(empty($args["name"])){
			$args["name"] = $args["firstname"].' '.$args["lastname"];
		}
		
		if(empty($args["name"]) && empty($args["firstname"]) && empty($args["lastname"]))
		{
			$allow = false;
			$erx["XXX"]["msg"] = t("Liitumisel vaja ka nime");
		}
		
		if(empty($args["email"]))
		{
			$allow = false;
			$erx["XXX"]["msg"] = t("Liitumisel vaja t&auml;ita aadressi v&auml;li");
		}
		
		if (sizeof($errors) > 0 || (!$allow && $args["op"] == 1))
		{
			$errors = $errors + $erx;
			$errmsg = "";
			foreach($errors as $errprop)
			{
				$errmsg .= $errprop["msg"] . "<br>";
			}

			aw_session_set("no_cache", 1);
			//arr($errmsg);
			//* fsck me plenty
			$request["mail"] = $_POST["mail"];
			aw_session_set("cb_reqdata", $request);
			aw_session_set("cb_errmsg", $errmsg);
			aw_session_set("cb_errmsgs", $errors);
			//die();
			return aw_global_get("HTTP_REFERER");
		};
		
		$udef_fields["textboxes"] = $args["udef_txbox"];
		$udef_fields["textareas"] = $args["udef_txtarea"];
		$udef_fields["checkboxes"] = $args["udef_checkbox"];
		$udef_fields["classificators"] = $args["udef_classificator"];
		$udef_fields["date1"] = $args["udef_date1"];

		if ($allow === true)
		{
			if ($args["op"] == 1)
			{
				$retval = $ml_member->subscribe_member_to_list(array(
					"firstname" => $args["firstname"],
					"lastname" => $args["lastname"],
					"name" => $args["name"],
					"email" => $args["mail"],
					"use_folders" => $use_folders,
					"list_id" => $list_obj->id(),
					"confirm_subscribe" => $list_obj->prop("confirm_subscribe"),
					"confirm_message" => $list_obj->prop("confirm_subscribe_msg"),
					"udef_fields" => $udef_fields,
				));
				
			//	$msg_to_admin = $args["name"].' , aadressiga '.$args["email"].' liitus mailinglistiga kaustadesse :<br>';
			//	foreach ($use_folders as $folder_to_send)
			//	{
			//		$folder = obj($folder_to_send);
			//		$msg_to_admin = $msg_to_admin.($folder->name()).'<br>';
			//	}
			
			}
		}
		
		if ($args["op"] == 2)
		{
			$retval = $ml_member->unsubscribe_member_from_list(array(
				"use_folders" => array_keys($args["subscr_folder"]),
				"email" => $args["email"],
				"list_id" => $list_obj->id(),
				"confirm_message" => $list_obj->prop("confirm_unsubscribe_msg"),
			));
			if($this->can("view", $list_obj->prop("redir_unsubscribe_obj")))
			{
				$retval = $this->cfg["baseurl"] . "/" . $list_obj->prop("redir_unsubscribe_obj");
			}
		}

		$relobj = new object($rel_id);

		$mx1 = $relobj->meta("values");
		$mx = $mx1["CL_ML_LIST"];

		// XXX: need to give some kind of feedback to the user, if subscribing did not succeed
		if (!empty($mx["redir_obj"]))
		{
			$retval = $this->cfg["baseurl"] . "/" . $mx["redir_obj"];
		}
		else
		if  (is_oid($list_obj->prop("redir_obj")) && $this->can("view", $list_obj->prop("redir_obj")))
		{
			$ro = obj($list_obj->prop("redir_obj"));
			
			$langid = aw_global_get("lang_id");
			$doc_langid = $ro -> lang_id();
			if($doc_langid != $langid)
			{
				$documents = $ro->connections_from(array(
					"type" => "RELTYPE_LANG_REL",
					"to.lang_id" => $langid,
				));
				if(count($documents) > 0)
				{
					foreach ($documents as $doc_conn)
					{
						$new_doc_id = $doc_conn->prop("to");
						$ro = obj($new_doc_id);
					}
				}
			}
			$retval = $this->cfg["baseurl"] . "/" . $ro->id();
		}
		return $retval;
	}
	
	/** previews a mailing list message 
		
		@attrib name=msg_preview  
		@param id required type=int 
		@param msg_id required type=int 
		
	**/
	function msg_preview($arr)
	{
		extract($arr);
		$msg_obj = new object($arr["msg_id"]);
		$message = $msg_obj->prop("message");
		if(!$msg_obj->prop("html_mail")) $message = nl2br($message);
		$al = get_instance("alias_parser");
		$al->parse_oo_aliases($msg_obj->id(), &$message);
		
		$c_title = $msg_obj->prop("msg_contener_title");
		$c_content = $msg_obj->prop("msg_contener_content");
		if(!$msg_obj->prop("html_mail")) $c_content = nl2br($c_content);
		
		$message = str_replace("#username#", t("Kasutajanimi"), $message);
		$message = str_replace("#name#", t("Nimi Perenimi"), $message);
		$message = str_replace("#email#", t("e-mail"), $message);
		$message = preg_replace("#\#pea\#(.*?)\#/pea\##si", '<div class="doc-title">\1</div>', $message);
		$message = preg_replace("#\#ala\#(.*?)\#/ala\##si", '<div class="doc-titleSub">\1</div>', $message);
		$message = str_replace("#subject#", $msg_obj->name(), $message);
		$message = str_replace("#traceid#", "?t=".md5(uniqid(rand(), true)), $message);
		$tpl_sel = $msg_obj->meta("template_selector");
		if (is_oid($tpl_sel) && $this->can("view", $tpl_sel))
		{
			$tpl_obj = new object($tpl_sel);
			$tpl_content = $tpl_obj->prop("content");
			$tpl_content = str_replace("#title#", $c_title, $tpl_content);
			$tpl_content = str_replace("#content#", $message, $tpl_content);
			$tpl_content = str_replace("#container#", $c_content, $tpl_content);	
			echo $tpl_content;
		}
		else
		{
			echo $message;
		}
		die();
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "admin_unsubscribe_folders":
			case "admin_subscribe_folders":
				$sources = $arr["obj_inst"]->get_menu_sources();
				$prop["value"] = "";
				if(!$sources->count())
				{
					$prop["value"].= t("Tuleb valida mailinglisti liikmete allikaks ka m&otilde;ni kaust!");
				}
				foreach($sources->arr() as $source)
				{
					$prop["value"].= html::checkbox(array(
						"name" => "admin_subscribe_folders[".$source->id()."]",
						"value" => 1,
						"checked" => $arr["request"]["admin_subscribe_folders"][$source->id()]? 1 : 0,
					)). " " . $source->name()." <br>";
				}
				break;

			case "sources_data_table":
				$this->_get_sources_data_table($arr);
				break;
			case "search_mail":
			case "search_name":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "search_menu":
				$sources = $arr["obj_inst"]->get_sources();
				$prop["value"] = "";
				foreach($sources->arr() as $source)
				{
					$prop["value"].= html::checkbox(array(
						"name" => "search_from_source[".$source->id()."]",
						"value" => 1,
						"checked" => $arr["request"]["search_from_source"][$source->id()]? 1 : 0,
					)). " " . $source->name()." <br>";
				}
				break;

			case "register_data":
				if(is_object($rd = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_REGISTER_DATA")))
				{
					$prop["value"] = $rd->name();
				}
				else
				{
					$prop["value"] = t("Registri andmed");
				}
/*				if(is_oid($arr["request"]["msg_id"]) && $this->can("view" , $arr["request"]["msg_id"]))
				{
					$mail_obj = obj($arr["request"]["msg_id"]);
					foreach($mail_obj->connections_from(array("type" => "RELTYPE_REGISTER_DATA")) as $c)
					{
						$rd = $c->to();
						$prop["options"] = $prop["options"]+array($rd->id() => $rd->name);
					}
				}*/
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("register_data" , array("ml" => $arr["obj_inst"]->id(), "ru" => get_ru())),
					"caption" => $prop["value"],
				));
				break;

			case "legend":
				$prop["caption"] = t("Asenduste legend");
				$prop["value"] = t("Meili sisus on v&otilde;imalik kasutada j&auml;rgnevaid asendusi:<br /><br />
					#username# - AutomatWebi kasutajanimi<br />
					#organisatsioon# - liikme organisatsioon<br />
					#isik# - Isiku nimi<br />
					#osakond# - Osakond<br />
					#ametinimetus# - ametinimetus<br />
					#name# - Listi liikme nimi<br />
					#e-mail# - Listi liikme e-mail<br/>
					#subject# - Kirja teema<br />
					#pea#(pealkiri)#/pea# - (pealkiri) asemele kirjutatud tekst muutub 1. taseme pealkirjaks<br />
					#ala#(pealkiri)#/ala# - (pealkiri) asemele kirjutatud tekst muutub 2. taseme pealkirjaks<br />
					#lahkumine# - link, millel klikkides inimene saab listist lahkuda<br />
					<br />
					Kui soovid kirja pandava lingi puhul teada saada, kas saaja sellele ka klikkis, lisa lingi aadressi l&otilde;ppu #traceid#
					N&auml;iteks: http://www.struktuur.ee/aw#traceid#
				");
				break;

			case "copy_template":
				$templates = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_TEMPLATE",
				));
				$options = array(0 => t(" - vali - "));
				$tm = get_instance("templatemgr");
				$options = $tm->template_picker(array(
					"folder" => "automatweb/mlist"
				));
				foreach($templates as $template)
				{
					$options[$template->prop("to")] = $template->prop("to.name");
				}
				$prop["options"] = $options;
				$msg_obj = $this->_get_mail_message_object($arr);
				$prop["value"] = $msg_obj->meta("copy_template");
				break;
			
			case "template_selector":
				$templates = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_TEMPLATE",
				));
				foreach($templates as $template)
				{
					$options[$template->prop("to")] = $template->prop("to.name");
				}
				// insert a template selector, if there are any templates available
				if (sizeof($templates) > 0 )
				{
					$options = array(0 => t(" - vali - "));
					foreach($templates as $template)
					{
						$options[$template->prop("to")] = $template->prop("to.name");
					}
					$prop["options"] = $options;
					$msg_obj = $this->_get_mail_message_object($arr);
					$prop["value"] = $msg_obj->meta("template_selector");
				}
				break;

			case "message_id":
				$msg_obj = $this->_get_mail_message_object($arr);
				$prop["value"] = $msg_obj->id();
				break;
			case "mfrom_name":
				$msg_obj = $this->_get_mail_message_object($arr);
				$prop["value"] = $msg_obj->meta("mfrom_name");
				break;
			case "message":
				$msg_obj = $this->_get_mail_message_object($arr);
/*		if(aw_global_get("uid") == "struktuur")
		{
			$ol = new object_list(array(
				"class_id" => CL_MESSAGE,
				"lang_id" => array(),
				"site_id" => array(),
			));
			foreach($ol-> arr() as $o)
			{$md = $o->meta("mail_data");$qid = $md["qid"];
	if($qid){			
	$row = $this->db_fetch_row("SELECT * FROM ml_sent_mails WHERE qid = ".$qid);
				if(!$o->prop("message"))
				{
					$o->set_prop("message" , $row["message"]);
					$o -> save();
				}
				arr($row);
}
			}
		}*/
//arr($msg_obj->properties());
				$prop["value"] = $msg_obj->prop("message");
				$prop["richtext"] = $arr["obj_inst"]->prop("no_fck")? 0 : $arr["obj_inst"]->prop("classinfo_allow_rte");
				//allow_rte
				break;
			case "html_mail":
				$msg_obj = $this->_get_mail_message_object($arr);
				//$prop["value"] = 1;
				$prop["checked"] = $msg_obj->prop("html_mail");
				break;

			case "html_mail":
				$msg_obj = $this->_get_mail_message_object($arr);
				$prop["value"] = $msg_obj->prop("html_mail");
				break;
	
			case "mfrom":
				$objs = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_SENDER",
				));
				$opts = safe_array($prop["options"]);
				foreach($objs as $obj)
				{
					if(in_array(array_keys($opts), $obj->prop("to")))
					{
						continue;
					}
					$opts[$obj->prop("to")] = $obj->prop("to.name");
				}
				$prop["options"] = $opts;
				$msg_obj = $this->_get_mail_message_object($arr);
				$prop["value"] = $msg_obj->prop("mfrom");
				break;

			case "subject":
				$msg_obj = $this->_get_mail_message_object($arr);
				$prop["value"] = $msg_obj->name();
				break;

			case "search_tb":
				$toolbar = &$prop["vcl_inst"];
/*				$toolbar->add_button(array(
					"name" => "new",
					"img" => "new.gif",
					"tooltip" => t("Lisa uus"),
					"url" => aw_url_change_var("group", "subscribing" , $args["return_to"]),
				));
*/
				$toolbar->add_save_button();

				$toolbar->add_button(array(
					"name" => "delete",
					"tooltip" => t("Kustuta"),
					"action" => "delete_members",
					"confirm" => t("Kustutan valitud tegelased listist?"),
					"img" => "delete.gif",
				));
				break;
			case "classinfo_allow_rte":
				$prop["options"] = array(
					0 => t("Ei kuva"),
					1 => t("AW RTE"),
					2 => t("FCKeditor"),
				);
				$prop["type"] = "select";
				break;
/*			case "req_members_s": 
				$source_prop = "search_menu";
			case "req_members_e":
				if(!$source_prop) $source_prop = "export_folders";
			case "req_members_m":
				if(!$source_prop) $source_prop = "write_user_folder";
			case "req_members":
				if(!$source_prop) $source_prop = "choose_menu";
				if(!(is_array($arr["obj_inst"]->prop($source_prop)) && sizeof($arr["obj_inst"]->prop($source_prop))))
				{
					return PROP_IGNORE;
				}
				$_SESSION["submembers_source_prop_value"] = $arr["obj_inst"]->prop($source_prop);
				$ret = "";
				foreach($arr["obj_inst"]->prop($source_prop) as $menu)
				{
					if(is_oid($menu) && $this->can("view" , $menu))
					{
						$menu_o = obj($menu);
						if($menu_o->class_id() == CL_MENU || $menu_o->class_id() == CL_CRM_SECTOR)
						{
							$ret.=html::checkbox(array("name" => "req_members[".$menu."]" , "value" => 1, "checked" => $menu_o->meta("req_members")));
							$ret.=" ".$menu_o->name()."\n";
						}
					}
				}
				$prop["value"] = $ret;
				if(!$prop["value"])
				{
					return PROP_IGNORE;
				}
				break;*/
			case "send_button":
				$prop["value"] = t("Saada!");
				$prop["onclick"] = "javascript:window.open(
					'".$this->mk_my_orb("process_queue", array(), "ml_queue", false, true)."',
					'',
					'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600');";
				break;
				
			case "delete_old_button":
				$prop["value"] = t("Kustuta vanad kirjad!");
				$prop["onclick"] = "javascript:window.open(
					'".$this->mk_my_orb("delete_old", array("ml" => $arr["obj_inst"]->id()), "ml_list", false, true)."',
					'',
					'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600');";
				break;
			
			case "db_mail_count":
				return PROP_IGNORE;//see teeb paljudes saitides aeglaseks nimekirja naitamise... kui miski mahakeeramine variant teha, siis voib seda jalle naidata
				$list_id = $arr["obj_inst"]->id();
				$time = time()-3*30*3600;
				$row = $this->db_fetch_row("SELECT count(*) as cnt FROM ml_sent_mails WHERE lid = '$list_id' AND mail_sent = 1 and tm < '$time'");
				$prop["value"] = t("Vanu kirju andmebaasis").": ".$row["cnt"];
				if($row["cnt"] == 0)
				{
					return PROP_IGNORE;
				}
				break;
				
			/*
			case "msg_folder":
				if(empty($prop["value"]) && !$arr["new"])
				{
					$obj = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MSG_FOLDER");
					if(is_object($obj))
					{
						$arr["obj_inst"]->set_prop("msg_folder", $obj->id());
						$prop["value"] = $obj->id();
					}
				}
				break;
			*/
			case "no_fck":
				if($arr["obj_inst"]->prop("classinfo_allow_rte") == 0)
				{
					return PROP_IGNORE;
				}
				if($prop["value"]) $this->set_classinfo(array("allow_rte" => 0));
				else $this->set_classinfo(array("allow_rte" => $arr["obj_inst"]->prop("classinfo_allow_rte")));
			
			case "sub_form_type":
				$prop["options"] = array(
					"0" => t("liitumine"),
					"1" => t("lahkumine"),
				);
				break;

			case "search_table":
				$this->member_search_table($arr);
				break;
	
			case "separator_legend":
				$prop["value"] = "tab'i eraldajana kasutamiseks m&auml;rgi tekstikasti /t";
				break;
			case "list_status_table":
				$tbl = $this->db_get_table("ml_sent_mails");
/*				if (!isset($tbl["fields"]["oid"]))
				{
					$this->db_add_col("ml_sent_mails", array(
						"name" => "oid",
						"type" => "int"
					));
				}*/
				$this->gen_list_status_table($arr);
				break;
				
			case "unsent_table":
				$this->gen_unsent_table($arr);
				break;
				
			case "unsent_tb":
				$this->gen_unsent_tb($arr);
				break;
				
			case "mail_report":
				$this->gen_mail_report_table($arr);
				break;

			case "mail_percentage":
				$prop["value"] = $this->gen_percentage($arr);
				break;

			case "mail_subject":
				$prop["value"] = $this->gen_mail_subject($arr);
				break;
			
			case "bounce":
				if(is_oid($arr["request"]["msg_id"]) && $this->can("view" , $arr["request"]["msg_id"]))
				{
					$message_object = obj($arr["request"]["msg_id"]);
					$prop["value"] = $message_object->meta("bounce");
				}
				if(!$prop["value"])
				{
					$prop["value"] = $arr["obj_inst"]->prop("default_bounce");
				}
				break;
			
			case "mail_toolbar":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "save",
					"img" => "save.gif",
					"tooltip" => t("Salvesta"),
					"action" => "submit",
				));
				$msg = $arr["request"]["msg_id"];
				if (is_oid($msg) && $this->can("view", $msg))
				{
					$link = $this->mk_my_orb("msg_preview",array(
						"id" => $arr["obj_inst"]->id(),
						"msg_id" => $msg,
					), $this->clid, false, true);

					$tb->add_button(array(
						"name" => "preview",
						"img" => "preview.gif",
						"tooltip" => t("Eelvaade"),
						"url" => $link,
						"target" => "_blank",
					));
				};
				/*
				$tb->add_separator();
				$tb->add_button(array(
					"name" => "send",
					"img" => "mail_send.gif",
					"tooltip" => t("Saada"),
					"confirm" => t("Saata kiri &auml;ra?"),
				));
				*/
				break;
				
			case "list_source":
				$m_id = $arr["request"]["mail_id"];
				if(is_oid($m_id) && $this->can("view", $m_id))
				{
					$msg = obj($m_id);
					$s = new aw_array($msg->meta("list_source"));
					$val = array();
					foreach($s->get() as $s_id)
					{
						if(is_oid($s_id) && $this->can("view", $s_id))
						{
							$source = obj($s_id);
							$val[] = $source->name();
						}
					}
					$prop["value"] = implode(", ", $val);
				}
				break;
				
			case "mail_start_date":
			case "mail_last_batch":
				$list_id = $arr["obj_inst"]->id();
				$mail_id = (int)$arr["request"]["mail_id"];
				$row = $this->db_fetch_row("SELECT * FROM ml_queue WHERE lid = ${list_id} ANd mid = ${mail_id}");
				if ($prop["name"] == "mail_start_date")
				{
					$prop["value"] = $this->time2date($row["start_at"],2);
				}
				else
				{
					if ($row["last_sent"] == 0)
					{
						$prop["value"] = t("Midagi pole veel saadetud");
					}
					else
					{
						$prop["value"] = $this->time2date($row["last_sent"],2);
					}
				}
				break;
		
			case "list_status_tb":
				$this->gen_list_status_tb($arr);
				break;

			case "export_type":
				$prop["options"] = array(
					ML_EXPORT_CSV => t("nimi,aadress"),
					ML_EXPORT_NAMEADDR => t("nimi &lt;aadress&gt;"),
					ML_EXPORT_ADDR => t("aadress"),
					ML_EXPORT_ALL => t("K&otilde;ik andmed"),
				);
				$prop["value"] = 1;
				break;


			case "choose_languages":
				$lg = get_instance("languages");
				$langdata = array();
				$prop["options"] = $lg->get_list();
				break;

// 			case "write_mail":
// 				break;

			case "show_mail_subject":
			case "show_mail_from":
			case "show_mail_message":
				$prop["value"] = $this->gen_ml_message_view($arr);
				break;

			case "expf_next_time":
				$url = str_replace("automatweb/", "", $this->mk_my_orb("exp_to_file", array("id" => $arr["obj_inst"]->id())));
				$sc = get_instance("scheduler");
				$exp = safe_array($sc->find(array(
					"event" => $url
				)));
				if (count($exp) == 0)
				{
					return PROP_IGNORE;
				}
				$event = reset($exp);
				$prop["value"] = date("d.m.Y H:i", $event["time"]);
				break;
		}
		return $retval;
	}
	

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "search_table":
				$this->_set_member_search_table($arr);
			break;
			/*
			case "msg_folder":
				if(empty($prop["value"]) && !$arr["new"])
				{
					$obj = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MSG_FOLDER");
					if(is_object($obj))
					{
						$prop["value"] = $obj->id();
					}
				}
				break;
			*/
			case "sources_data_table":
				$this->_set_sources_data_table($arr);
				break;
			case "list_status_table":
				foreach($arr["request"]["pach_sizes"] as $qid => $val)
				{
					$qid = (int)$qid;
					if($val > 0 && $val < 5001)
					{
						$this->db_fetch_row("UPDATE ml_queue SET patch_size='$val' where qid='$qid'");
					}
				}
				break;
			case "no_fck":
				if($prop["value"]) $this->set_classinfo(array("allow_rte" => 0));
				else $this->set_classinfo(array("allow_rte" => $arr["obj_inst"]->prop("classinfo_allow_rte")));
				break;
			case "import_textfile":
				$imp = $_FILES["import_textfile"]["tmp_name"];
				if (!is_uploaded_file($imp))
				{
					return PROP_OK;
				}
				$contents = file_get_contents($imp);
				if(!$arr["obj_inst"]->mass_subscribe(array(
					"menus" => array_keys($arr["request"]["admin_subscribe_folders"]),
					"text" => $contents,
					"debug" => 1,
				)))
				{
					$prop["error"] = t("Liitumiseks peab olema valitud m&otilde;ni kaust kuhu liituda");
					return PROP_FATAL_ERROR;
				}
				break;

			case "delete_textfile":
				$imp = $_FILES["delete_textfile"]["tmp_name"];
				if (!is_uploaded_file($imp))
				{
					return PROP_OK;
				}
				$contents = file_get_contents($imp);

				if(!$arr["obj_inst"]->mass_unsubscribe(array(
					"menus" =>array_keys($arr["request"]["admin_subscribe_folders"]),
					"text" => $contents,
					"debug" => 1,
				)))
				{
					$prop["error"] = t("Lahkumiseks peab olema valitud m&otilde;ni kaust kust lahkuda");
					return PROP_FATAL_ERROR;
				}
				break;
				
			case "mass_subscribe":
				if(!$arr["obj_inst"]->mass_subscribe(array(
					"menus" => array_keys($arr["request"]["admin_subscribe_folders"]),
					"text" => $prop["value"],
					"debug" => 1,
				)))
				{
					$prop["error"] = t("Liitumiseks peab olema valitud m&otilde;ni kaust kuhu liituda");
					return PROP_FATAL_ERROR;
				}
				break;

			case "mass_unsubscribe":
				if(!$arr["obj_inst"]->mass_unsubscribe(array(
					"menus" => array_keys($arr["request"]["admin_subscribe_folders"]),
					"text" => $prop["value"],
					"debug" => 1,
				)))
				{
					$prop["error"] = t("Lahkumiseks peab olema valitud m&otilde;ni kaust kust lahkuda");
					return PROP_FATAL_ERROR;
				}
				break;

			case "message":
				$this->submit_write_mail($arr);
				break;

			case "exp_sbt":
				$this->do_export = true;
				$this->export_type = $arr["request"]["export_type"];
				break;

			case "bounce":
				if($this->can("view" , $arr["request"]["message_id"]))
				{
					$mail_object = obj($arr["request"]["message_id"]);
					$mail_object -> set_meta("bounce" , $prop["value"]);
				}
				return PROP_IGNORE;

		}
		return $retval;
	}


	function callback_mod_retval($arr)
	{
		$object = obj($arr["args"]["id"]);
		if($object->prop["no_rtf"]) $this->set_classinfo(array("allow_rte" => 0));
		else $this->set_classinfo(array("allow_rte" => $object->prop("classinfo_allow_rte")));

		$arr["args"]["search_mail"] = $_POST["search_mail"];
		$arr["args"]["search_name"] = $_POST["search_name"];
		$arr["args"]["search_from_source"] = $_POST["search_from_source"];
		$arr["args"]["admin_subscribe_folders"] = $_POST["admin_subscribe_folders"];

		$arr["args"]["search_from_subfolders"] = $_POST["search_from_subfolders"];

		if (isset($this->do_export))
		{
			$arr["action"] = "export_members";
			$arr["args"]["filename"] = "members.txt";
			$arr["args"]["export_type"] = $this->export_type;
			$arr["args"]["export_date"] = date_edit::get_timestamp($arr["request"]["export_from_date"]);
		}
		if (isset($this->edit_msg))
		{
			$arr["args"]["msg_id"] = $this->edit_msg;
		}
	}
	
	function get_all_members($id)
	{
		$member_list = array();
		$mem_list = new object_list(array(
			"parent" => $id,
			"class_id" => CL_ML_MEMBER,
		));
		foreach($mem_list->arr() as $mem)
		{
			$member_list[$mem->prop("name")] = $mem->prop("mail");
		}
		return $member_list;
	}
	
	////
	// !Imports members from a text file / or text block
	// text(string) - member list, comma separated
	// list_id(id) - which list?
	function mass_subscribe($arr)
	{
	//def_user_folder
		aw_global_set("no_cache_flush", 1);
		$lines = explode("\n", $arr["text"]);
		$list_obj = new object($arr["list_id"]);
		arr($arr);
		$fld = new aw_array($list_obj->prop("admin_subscribe_folders"));
		foreach($fld->get() as $fold)
		{
			if(!is_oid($fold) || !$this->can("add", $fold))
			{
				continue;
			}
			$fld_obj = new object($fold);
			if($fld_obj->class_id() != CL_MENU)
			{
				echo "Liituda saab ainult kaustadesse... ".$fld_obj->name()." ei ole kaust<br />";
				continue;
			}
			$members = $this->get_all_members($fold);
			$name = $fld_obj->name();
			echo "Impordin kasutajaid kataloogi $fold / $name... <br />";
			aw_set_exec_time(AW_LONG_PROCESS);
			$ml_member = get_instance(CL_ML_MEMBER);
			$cnt = 0;
			if (sizeof($lines) > 0)
			{
				foreach($lines as $line)
				{
					$line = trim($line);
					if (strlen($line) == 0)
					{
						continue;
					}
					if (strpos($line,",") !== false)
					{
						list($name,$addr) = explode(",", $line);
					}
					elseif (strpos($line,";") !== false)
					{
						list($name,$addr) = explode(";",$line);
					}
					else
					{
						$name = "";
						$addr = $line;
					}
					$name = trim($name);
					$addr = trim($addr);
	
					if (is_email($addr) && !in_array($addr, $members))
					{
						print "OK - nimi: $name, aadress: $addr<br />";
						flush();
						$cnt++;
						$retval = $ml_member->subscribe_member_to_list(array(
							"name" => $name,
							"email" => $addr,
							"list_id" => $list_obj->id(),
							"use_folders" => $fold,
						));
//						usleep(500000);
						$members[] = $addr;
					}
					elseif(in_array($addr, $members))
					{
						print "Juba olemas listis - nimi: $name, aadress: $addr<br />";
						flush();
					}
					else
					{
						print "Vale aadress - nimi: $name, aadress: $addr<br />";
						flush();
					}
				}
				$c = get_instance("cache");
				$c->file_clear_pt("menu_area_cache");
				$c->file_clear_pt("storage_search");
				$c->file_clear_pt("storage_object_data");
			}
			print "Importisin $cnt aadressi<br>";
		}
		return true;
	}
	
	////
	// !Returns redir_unsubscribe_obj if defined ... section, if not

	/** unsubscribe
		@attrib name=unsubscribe no_login=1 all_args=1
		@param usr required type=int
		@param list_source optional
		@param list required type=int
	**/
	function unsubscribe($arr)
	{
		$ml_object = obj($arr["list"]);
		$ml_member = get_instance(CL_ML_MEMBER);
		if(is_oid($arr["usr"]) && $this->can("view", $arr["usr"]))
		{
			$member = obj($arr["usr"]);
			$email = $member->prop("mail");
		}
		if(!is_array($arr["list_source"]))
		{
			$list_source = array();
			foreach($arr as $key => $val)
			{
				if(substr_count($val["mail"], $key))
				{
					$list_source[$key] = $val;
				}
			}
		}
		else
		{
			$list_source = $arr["list_source"];
		}
		$retval = $ml_member->unsubscribe_member_from_list(array(
			"email" => $email,
			"list_id" => $arr["list"],
			"ret_status" => true,
			"use_folders" => $arr["list_source"],
		));
		if(is_oid($ml_object->prop("redir_unsubscribe_obj")) && $this->can("view", $ml_object->prop("redir_unsubscribe_obj")))
		{
			return aw_ini_get("baseurl")."/".$ml_object->prop("redir_unsubscribe_obj");
		}
		else
		{
			return $retval;
		}
	}
	
	function gen_unsent_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"type" => "time",
			"format" => "H:i d-M-y",
			"numeric" => 1,
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
		));
		$t->set_default_sortby("created");
		$t->set_default_sorder("desc");
		$q = "SELECT DISTINCT m.mail FROM ml_sent_mails m LEFT JOIN objects ON (objects.oid = m.mail) WHERE objects.status != 0";
		$fld = $arr["obj_inst"]->prop("msg_folder");
		$mls = array();
		$this->db_query($q);
		while($w = $this->db_next())
		{
			$mls[] = $w["mail"];
		}
		$mails = new object_list(array(
			"class_id" => CL_MESSAGE,
			"site_id" => array(),
			"parent" => !empty($fld) ? $fld : $arr["obj_inst"]->parent(),
		));
		foreach($mails->arr() as $mail)
		{
			if(!in_array($mail->id(), $mls))
			{
				$t->define_data(array(
					"id" => $mail->id(),
					"name" => html::get_change_url($arr["obj_inst"]->id(), array(
						"group" => "write_mail",
						"msg_id" => $mail->id(),
					), ($mail->name() ? $mail->name() : t("(pealkiri puudub)"))),
					"created" => $mail->created(),
				));
			}
		}
		$t->sort_by();
	}
	
	function gen_unsent_tb($arr)
	{
		$toolbar = &$arr["prop"]["toolbar"];
		$toolbar->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"action" => "delete_mails",
			"confirm" => t("Eemaldan valitud kirjad?"),
			"img" => "delete.gif",
		));
	}

	/** Exports list members as a plain text file
		@attrib name=export_members
		@param id required type=int 
		@param filename optional
		@param export_type optional type=int
		@param export_date optional type=int
	**/
	function export_members($arr)
	{
		$arr["obj_inst"] = &obj($arr["id"]);
		if($arr["obj_inst"]->prop("export_folders"))
		{
			$srcs = $arr["obj_inst"]->prop("export_folders");
			$members = $this->get_members(array(
				"id" => $arr["id"],
				"src" => $srcs,
			));
		}
		else
		{
			$members = $this->get_members(array("id" => $arr["id"]));
		}

		$ml_member_inst = get_instance(CL_ML_MEMBER);
		$ser = "";
		
		if($arr["obj_inst"]->prop("member_config"))
		{
			$config_obj = &obj($arr["obj_inst"]->prop("member_config"));
			$config_data = array();
			$config_data = $config_obj->meta("cfg_proplist");
			uasort($config_data, array($this,"__sort_props_by_ord"));
		}
		$imported = array();
		foreach($members as $key => $val)
		{
			if($val["file_name"]){
				;
				continue;
			}
			list($mailto,$memberdata) = $ml_member_inst->get_member_information(array(
				"lid" => $arr["id"],
				"member" => $val["oid"],
			));
			if(!$mailto)
			{
				$mailto = $val["mail"];
			}
			if(!in_array($mailto, $imported))
			{
				$imported[] = $mailto;
	
				$member = &obj($memberdata["id"]);
				if($member->created() > $arr["export_date"] || ($arr["export_date"] < 100))
				{
					switch($arr["export_type"])
					{
						case ML_EXPORT_ADDR:
							$ser .= $mailto;
							break;
		
						case ML_EXPORT_NAMEADDR:
							$ser .= $memberdata["name"] . " <" . $mailto . ">";
							break;
						
						case ML_EXPORT_ALL:
							$ser .= $memberdata["name"] . ";" . $mailto . ";";
							foreach ($config_data as $key2 => $value)
							{
								if(strpos($key2, "def_"))
								{
									if(strpos($key2, "def_date"))
									{
										$ser .= get_lc_date($member->prop($key2));
									}
									else
									{
										$ser .= $member->prop($key2);
									}
									$ser .= ";";
								}
							}
							break;
						default:
							$ser .= $memberdata["name"] . "," . $mailto;
							break;
					}
					$ser .= "\n";
				}
			}
		}
		if ($arr["ret"] == true)
		{
			return $ser;
		}

		header("Content-Type: text/plain");
		header("Content-length: " . strlen($ser));
		header("Content-Disposition: filename=members.txt");
		print $ser;
		exit;
	}
/*
	function gen_member_list($arr)
	{/*
		if(aw_global_get("uid") == "marko")
		{		//$GLOBALS["DUKE"] = 1;

			$ol = new object_list(array(
				"class_id" => CL_ML_MEMBER,
				"mail" => "%&quot;%",
				"lang_id" => array(),
				"site_id" => array(),
				//"created" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 1228111200),
			));

foreach($ol->arr() as $o)
		{




			$mailaddress = str_replace("&quot;" , "" , $o->prop("mail"));

			$q = "UPDATE ml_users SET mail = '".$mailaddress."' WHERE id = '".$o->id()."'";
			//arr($q); 
//$this->db_query($q);

		}arr($ol->count());


		}*//*
		$perpage = 100;
	/*	$ft_page = (int)$GLOBALS["ft_page"];
		$ml_list_members = $this->get_members(array(
			"id" 	=> $arr["obj_inst"]->id(),
			"from"	=> $perpage * $ft_page ,
			"to"	=> $perpage * ($ft_page + 1),
			"all"	=> 1,
		));
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"width" => 50,
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("Aadress"),
			"sortable" => 1,
		));
		
		$t->define_field(array(
			"name" => "source",
			"caption" => t("Allikas"),
			"sortable" => 1,
		));

		if($this->show_extra_cols)
		{
			$t->define_field(array(
				"name" => "co",
				"caption" => t("Organisatsioon"),
				"sortable" => 1,
			));
			$t->define_field(array(
				"name" => "section",
				"caption" => t("Osakond"),
				"sortable" => 1,
			));
			$t->define_field(array(
				"name" => "pro",
				"caption" => t("Ametinimetus"),
				"sortable" => 1,
			));
		}

		$t->define_field(array(
			"name" => "joined",
			"caption" => t("Liitunud"),
			"sortable" => 1,
			"type" => "time",
			"format" => "H:i d-m-Y",
			"smart" => 1,
		));
		$t->set_default_sortby("id");
		$t->set_default_sorder("desc");
		$cfg = $arr["obj_inst"]->prop("member_config");
		if(is_oid($cfg) && $this->can("view", $cfg))
		{
			$config_obj = &obj($cfg);
			
			$config_data = $config_obj->meta("cfg_proplist");
			uasort($config_data, array($this,"__sort_props_by_ord"));
			
			foreach($config_data as $key => $item)
			{
				strpos($key, "def_txbox");
				if(strpos($key, "def_txbox"))
				{
					$t->define_field(array(
						"name" => $item["name"],
						"caption" => $item["caption"],
						"sortable" => 1,
					));
				}
				
				if(strpos($key, "def_date"))
				{
					$t->define_field(array(
						"name" => $item["name"],
						"caption" => $item["caption"],
						"sortable" => 1,
					));	
				}
			}
		}
		$member_config = $arr["obj_inst"]->prop("member_config");
		if(!empty($member_config))
		{
			$t->define_field(array(
				"name" => "others",
				"caption" => t("Liitumisinfo"),
			));
		}
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));

		$ml_member_inst = get_instance(CL_ML_MEMBER);

		if($this->show_extra_cols)
		{
			$co_array = array();
			$pro_array = array();
			$sec_array = array();
			foreach($ml_list_members as $key => $val)
			{
				if($val["co"]) $co_array[$val["co"]] = $val["co"];
				if($val["pro"]) $pro_array[$val["pro"]] = $val["pro"];
				if($val["section"]) $sec_array[$val["section"]] = $val["section"];
			}
			$pros = new object_list();
			$pros->add($pro_array);
			$pros = $pros->names();
			$cos = new object_list();
			$cos->add($co_array);
			$cos = $cos->names();
			$secs = new object_list();
			$secs->add($sec_array);
			$secs = $secs->names();
		}

		if (is_array($ml_list_members))
		{
			foreach($ml_list_members as $key => $val)
			{
				$is_oid = 0;
				if(is_oid($val["oid"]))
				{
					$is_oid = 1;
				}
				if(!(strlen($val["name"]) > 0))
				{
					$val["name"] = "(nimetu)";
				}
				$parent_obj = obj($val["parent"]);
				$parent_name = $parent_obj->name();
				if($is_oid)
				{
					list($mailto,$memberdata) = $ml_member_inst->get_member_information(array(
						"lid" => $arr["obj_inst"]->id(),
						"member" => $val["oid"],
						"from_user" => true,
					));
					$joined = $memberdata["joined"];
						$source = ($parent_obj->class_id() == $parent_obj->prop("parent.class_id") ? html::href(array(
							"url" => admin_if::get_link_for_obj($parent_obj->parent()),
							"caption" => $parent_obj->prop("parent.name"),
						))."->" : "" ). html::href(array(
							"url" => admin_if::get_link_for_obj($val["parent"]),
							"caption" => $parent_name,
						));
					$others = html::href(array(
						"caption" => t("Vaata"),
						"url" => $this->mk_my_orb("change", array(
							"id" => $val["oid"],
							"group" => "udef_fields",
							"cfgform" => $arr["obj_inst"]->prop("member_config"),
							), CL_ML_MEMBER),
					));
					$name = html::get_change_url($val["oid"], array("return_url" => get_ru()), $val["name"]);
					$oid = $val["oid"];
				}
				else
				{
					$source = $parent_name;
					$name = $val["name"];
					$oid = $val["parent"]."_row_count_".$val["row_cnt"];
				}
				$tabledata = array(
					"id" => $val["oid"],
					"email" => $val["mail"],
					"joined" => $memberdata["joined"],
					"source" => $source,
					"others" => $others,
					"name" => $name,
					"oid" => $oid,
				);
				if($val["co"])
				{
					$tabledata["co"] = $cos[$val["co"]];
				}
				if($val["pro"])
				{
					$tabledata["pro"] = $pros[$val["pro"]];
				}
				if($val["section"])
				{
					$tabledata["section"] = $secs[$val["section"]];
				}

				if(is_oid($is_oid))
				{
					$member_obj = &obj($val["oid"]);
					for($i = 0; $i < 10; $i++)
					{
						$tabledata["udef_txbox$i"] = $member_obj->prop("udef_txbox$i");
						if($member_obj->prop("udef_date$i"))
						{
							$tabledata["udef_date$i"] = get_lc_date($member_obj->prop("udef_date$i"));
						}
					}
				}

				if (isset($tabledata_arr[$tabledata["id"]]))
				{
					$tabledata_arr[$tabledata["id"]]["source"] .= ", ".$tabledata["source"];
				}
				else
				{
					$tabledata_arr[$tabledata["id"]] = $tabledata;
				}
			}

			foreach($tabledata_arr as $row)
			{
				$t->define_data($row);	
 			}
		}

		if($this->member_count > $perpage)
		{
			$t->define_pageselector (array (
				"type" => "lb",
				"d_row_cnt" => $this->member_count,
				"records_per_page" => $perpage,
			));
		}
		$t->sort_by();
	}*/

	function __sort_props_by_ord($el1,$el2)
	{
		if (empty($el1["ord"]) && empty($el2["ord"]))
		{
			if(empty($el1["tmp_ord"]) || empty($el2["tmp_ord"]))
			{
				return 0;
			}
			return (int)($el1["tmp_ord"] - $el2["tmp_ord"]);
			//return 0;
		}
		return (int)($el1["ord"] - $el2["ord"]);
	}
	
	function gen_list_status_tb($arr)
	{
		/*
		$sched = get_instance("scheduler");
		$sched->do_events(array(
			"event" => $this->mk_my_orb("process_queue", array(), "", false, true),
			"time" => time()-120,
		));
		*/
		$toolbar = &$arr["prop"]["toolbar"];
		$toolbar->add_button(array(
			"name" => "new",
			"tooltip" => t("Uus kiri"),
			"url" => $this->mk_my_orb("change", array(
					"id" => $arr["obj_inst"]->id(),
					"group" => "write_mail",
				)),
			"img" => "new.gif",
		));

		$toolbar->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"action" => "submit",
		));

		$toolbar->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"action" => "delete_queue_items",
			"img" => "delete.gif",
			"confirm" => t("Oled kindel, et soovid valitud kirjad kustutada?"),
		));
	}

	function _gen_ls_table(&$t)
	{
		$t->define_field(array(
			"name" => "qid",
			"caption" => t("#"),
			"talign" => "center",
			"sortable" => 1,
			"type" => "int",
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "subject",
			"caption" => t("Kiri"),
			"talign" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Staatus"),
			"talign" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "start_at",
			"caption" => t("Algus"),
			"talign" => "center",
			"type" => "time",
			"format" => "H:i d-m-Y",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "last_sent",
			"caption" => t("Viimane"),
			"talign" => "center",
			"type" => "time",
			"format" => "H:i:s",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "perf",
			"caption" => t("Perf"),
			"talign" => "center",
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "patch_size",
			"caption" => t("Korraga"),
			"talign" => "center",
			"sortable" => 1,
			"type" => "int",
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "delay",
			"caption" => t("Oota"),
			"talign" => "center",
			"sortable" => 1,
			"type" => "int",
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "protsent",
			"caption" => t("Valmis"),
			"talign" => "center",
			"sortable" => 1,
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "qid",
		));
		$t->set_default_sortby("last_sent");
		$t->set_default_sorder("desc");
	}

	function gen_list_status_table($arr)
	{
		/*
		$sched = get_instance("scheduler");
		$sched->add(array(
			"event" => $this->mk_my_orb("process_queue", array(), "", false, true),
			"time" => time()+120, // every 2 minutes
		));
		*/
		$sched = get_instance("scheduler");
		$mq = get_instance("applications/mailinglist/ml_queue");
		$t = &$arr["prop"]["vcl_inst"];
		$this->_gen_ls_table($t);
		$q = "SELECT ml_queue.* FROM ml_queue LEFT JOIN objects ON (ml_queue.mid = objects.oid) WHERE objects.status != 0 AND lid = " . $arr["obj_inst"]->id() . " ORDER BY start_at DESC";
		$this->db_query($q);
		while ($row = $this->db_next())
		{
			$mail_obj = new object($row["mid"]);
			$status = $row["status"];
			/*
			if ($row["status"] != 2)
			{
				$stat_str = $mq->a_status[$row["status"]];
				$status_str = "<a href='javascript:remote(0,450,270,\"".$this->mk_my_orb("queue_change", array("id"=>$row["qid"]))."\");'>$stat_str</a>";
			}
			else
			{
			*/
				$status_str = $mq->a_status[$row["status"]];
			//};
			
			//monele pole subjekti pandud
			if(!strlen($mail_obj->name()) > 0) $mail_name = t("(Nimetu)");
			else $mail_name = $mail_obj->name();
			 
			 $row["subject"] = html::get_change_url($arr["obj_inst"]->id(), array(
				"group" => "write_mail",
				"msg_id" => $mail_obj->id(),
				"status" => $row["status"],
			), $mail_name);
			
			//$row["mid"] = $mail_obj->name();
			if (!$row["patch_size"])
			{
				$row["patch_size"] = t("k&otilde;ik");
			};
			if($row["status"] != 2 && $row["patch_size"] > 1)
			{
				$row["patch_size"] = html::textbox(array(
					"name" => "pach_sizes[".$row["qid"]."]",
					"value" => $row["patch_size"],
					"size" => 3,
				));
			}
			$row["delay"]/=60;
			$row["status"] = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"group" => "mail_report", 
					"id" => $arr["obj_inst"]->id(),
					"mail_id" => $row['mid'],
					"qid" => $row["qid"],
				)),
				"caption" => $status_str,
			));
			$row["protsent"] = $this->queue_ready_indicator($row["position"], $row["total"]);
			if($status == 5)
			{
				$this->save_handle();
				$row["protsent"].= $this->gen_ready_indicator($row);
				$this->restore_handle();
			
				$is_in_ched = 0;
				foreach($in_scheduler as $key=> $val)
				{
					if(substr_count($val["event"], $row["mid"]) > 0)
					{
						$is_in_ched = 1;
						break;
					}
				}
				if(!$is_in_ched)
				{
					$row["status"].="<br>\n".html::button(array(
						"onclick" => "javascript:window.open(
							'".$this->mk_my_orb("add_gen_sched", array("mid" => $row["mid"]), "ml_list", false, true)."',
							'',
							'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600');",
						"value" => t("Pane genereerima"),
						"confirm" => t("bljaad"),
					));
				}
			}
			$row["perf"] = sprintf("%.2f", $row["total"] / ($row["last_sent"] - $row["start_at"]) * 60);
			$t->define_data($row);
		};
		$t->sort_by();
	}
	
	/**
		@attrib name=add_gen_sched
		@param mid required type=mail id
	**/
	function add_gen_sched($arr)
	{
		extract($arr);
		$mlq = get_instance("applications/mailinglist/ml_mail_gen");
		$mlq->bg_control(array("id" => $mid, "do" => "start",));
		print "<script language='javascript'>window.opener.location.reload();;
			window.close();
		</script>";
	}
	
	/** removes queue items 
		
		@attrib name=delete_queue_items 
		
		@param id required type=int 
		
	**/
	function delete_queue_items($arr)
	{
		foreach($arr["sel"] as $key => $val)
		{
			$arr["sel"][$key] = (int)$val;
		}
		if (is_array($arr["sel"]))
		{
			$q = sprintf("DELETE FROM ml_queue WHERE qid IN (%s)",join(",",$arr["sel"]));
			$this->db_query($q);
		};
		return $this->mk_my_orb("change",array("id" => $arr["id"],"group" => "raports"));
	}

	// --------------------------------------------------------------------
	// messengerist saatmise osa
	////
	//! Messenger kutsub valja kui on valitud liste targetiteks
	// vajab targets ja id
	function route_post_message($args = array())
	{
		extract($args);
		$url = $this->mk_my_orb("post_message", array("id" => $id, "targets" => $targets), "", 1);
		$sched = get_instance("scheduler");
		$sched->add(array(
			"event" => $this->mk_my_orb("process_queue", array(), "ml_queue", false, true),
			"time" => time() + 120,	// every 2 minutes
		));
		return $url;
	}

	function get_members_from_sector($args)
	{
		extract($args);
		$ala = obj($id);
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"CL_CRM_COMPANY.RELTYPE_TEGEVUSALAD" => $id,
			"site_id" => array(),
			"lang_id" => array(),
		));
		foreach($ol->arr() as $o)
		{
			if(!$args["all"] && $this->ignore_member($ala , $o->id()))
			{
				continue;
			}
			$mail = $o->prop("email_id.mail");
			if (!$mail)
			{
				// try to get first conn
				$mail_o = $o->get_first_obj_by_reltype("RELTYPE_EMAIL");
				if ($mail_o)
				{
					$mail = $mail_o->prop("mail");
				}
			}
			if(!$no_return)
			{
				if(!(array_key_exists($mail , $this->already_found)))
				{
					if(!($to > 1) || (!($this->member_count < $from) && ($to > $this->member_count)))
					{
						$name = $o->name();
						if($comb)
						{
							$name = "".$name." &lt;".$mail."&gt;";
						}
						$this->ml_members[]  = array(
							"parent" => $ala->id(),
							"name" => $name,
							"mail" => $mail,
							"parent_name" => $ala->name(),
							"oid" => $o->id(),
						);
					}
					$this->member_count++;
				}
			}
			if(!$all) 
			{
				$this->already_found[$mail] = $mail;
			}
		}
		return $this->ml_members;
	}

	private function ignore_member($source_obj, $member)
	{
		if(!(is_object($source_obj) && $this->can("view" , $member)))
		{
			return false;
		}

		$ignore_list = $source_obj->meta("mail_ignore_list");
		if(is_array($ignore_list) && isset($ignore_list[$member]))
		{
			return true;
		}
		return false;
	}

	//"source_data" - array("usable_data" => array("orgs" , "pers" , "work"))
	private function get_members_from_category($args)
	{
		$GLOBALS["mailinglist_show_org_column"] = 1;
		extract($args);
		$o = obj($id);
		$customers = $o->get_category_customers();
		if(is_array($source_data["usable_data"]) && sizeof($source_data["usable_data"]))
		{
			$use = $source_data["usable_data"];
		}
		foreach($customers->arr() as  $co)
		{
			$people = new object_list();
			if($co->class_id() == CL_CRM_PERSON)
			{
				if(!$use || in_array("pers" , $use))
				{
					$people->add($co);
				}
			}
			else
			{
				if(!$use || in_array("work" , $use))
				{
					$GLOBALS["mailinglist_show_person_columns"] = 1;	
					$people = $co->get_workers();
				}
				if(!$use || in_array("orgs", $use))
				{
					$people->add($co);
				}
			}
			foreach($people->arr() as $worker)
			{
				if(!$args["all"] && $this->ignore_member($o , $worker->id()))
				{
					continue;
				}
			
				if($worker->class_id() == CL_CRM_COMPANY)
				{
					$mail = $worker->get_mail();
					if(!$mail)
					{
						continue;
					}
					if(!$no_return)
					{
						if(!(array_key_exists($mail , $this->already_found)))
						{
							if(!($to > 1) || (!($this->member_count < $from) && ($to > $this->member_count)))
							{
								$name = $worker->name();
								if($comb)
								{
									$name = "".$name." &lt;".$mail."&gt;";
								}
								$this->ml_members[]  = array(
									"co" => $co->id(),
									"parent" => $o->id(),
									"name" => $name,
									"mail" => $mail,
									"parent_name" => $o->name(),
									"oid" => $worker->id(),
								);
							}
							$this->member_count++;
						}
					}
				}
				else
				{
					$mail = $worker->get_mail($co->id());
					if(!$mail)
					{
						continue;
					}
					if(!$no_return)
					{
						if(!(array_key_exists($mail , $this->already_found)))
						{
							if(!($to > 1) || (!($this->member_count < $from) && ($to > $this->member_count)))
							{
								$name = $worker->name();
								if($comb)
								{
									$name = "".$name." &lt;".$mail."&gt;";
								}
								$this->ml_members[]  = array(
									"co" => $co->id(),
									"pro" => $worker->get_rank($co->id()),
									"section" => $worker->get_section_id($co->id()),
									"parent" => $o->id(),
									"name" => $name,
									"mail" => $mail,
									"parent_name" => $o->name(),
									"oid" => $worker->id(),
								);
							}
							$this->member_count++;
						}
					}
				}
				if(!$all) 
				{
					$this->already_found[$mail] = $mail;
				}
			}
		}
		return $this->ml_members;
	}

	function get_members_from_file($args)
	{
		extract($args);
		$file = get_instance(CL_FILE);
		$file_data = $file->get_file_by_id($id);
		$rows = explode("\n" , $file_data["content"]);
		if(is_oid($this->list_id))
		{
			$list_obj = obj($this->list_id);
			$separator = $list_obj->prop("file_separator");
		}
		if(!$separator) $separator=",";
		if($separator[0] == "/")
		{
			$separator = str_replace("/t", "\t" ,$separator);
		}
		$row_count = 0;
		foreach($rows as $row)
		{
			$column = explode($separator , $row);
			if(!(strlen($column[1]) > 5))
			{
				continue;
			}
			$mail = trim($column[1]);
			if(!$no_return)
			{
				if(!(array_key_exists($mail , $this->already_found)))
				{
					if(!($to > 1) || (!($this->member_count < $from) && ($to > $this->member_count)))
					{
						$name = trim($column[0]);
						if($comb)
						{
							$name = "".$name." &lt;".$mail."&gt;";
						}
						$this->ml_members[]  = array(
							"parent" => $file_data["id"],
							"name" => $name,
							"mail" => $mail,
							"parent_name" => $file_data["name"],
							"row_cnt" => $row_count,
						);
					}
					$this->member_count++;
				}
			}
			if(!$all)
			{
				$this->already_found[$mail] = $mail;
			}
			$row_count++;
		}
		return $this->ml_members;
	}

	function un_format_name($arr)
	{
		$data = explode('&lt;' , $arr["name"]);
		if($arr["result"] == "name") return trim($data[0]);
		if($arr["result"] == "email") return trim($data[1], "&gt;");
	}

	/**
	@attrib api=1 params=name
	@param id required type=oid
		message oid or mailinglist oid
	@param all optional type=int
		if 1, then return all member dublicates also
	@param no_return optional type=int
		if 1, then returns nothing... if you need only a number of members
	@param src optional type=array
		List member source
	@returns array
	@comment
		if the source is file, then parent_name is set
		else oid is set
	@examples
		$ml_list_inst = get_instance(CL_ML_LIST);
		$data = $ml_list_inst->get_members($list_id);
		//data = Array(
			[0] => Array(
				[parent] => 7375
				[name] => keegi
				[mail] => keegi@normaalne.ee
				[parent_name] => mailinglist.txt
			)
			[1] => Array(
				[oid] => 7500
				[parent] => 580
				[name] => inimene
				[mail] => inimene@mail.ee
		))
	**/
	function get_members($args)
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		ini_set("memory_limit", "800M");
		extract($args);
		$this->ml_members = array();
		$this->member_count = 0;
		$obj = obj($id);
		$this->already_found = array();
		if($obj->class_id() == CL_MESSAGE)
		{;
			$src = $obj->meta("list_source");
			$m_data = $obj->meta("mail_data");
			if(!$this->list_id) $this->list_id = $m_data["list_id"];
		}
		if(!(sizeof($src) > 0))
		{
			$src = $obj->prop("choose_menu");
			if(!$this->list_id) $this->list_id = $id;
		}
		if(!empty( $this->list_id) && $this->can("view" , $this->list_id))
		{
			$mailinglist = obj($this->list_id);
		}
		else
		{
			$mailinglist = $obj;
		}
		$src = $mailinglist ->add_minions($src);
		$fld = new aw_array($src);
		$sources_data = $mailinglist ->get_sources_data();


		foreach($fld->get() as $folder_id)
		{
			if(!is_oid($folder_id) || !$this->can("view", $folder_id))
			{
				continue;
			}
			$source_obj = obj($folder_id);
			if($source_obj->class_id() == CL_MENU)
			{
				$odl_prms = array(
					"class_id" => CL_ML_MEMBER,
					"parent" => $folder_id,
					"lang_id" => array(),
					"site_id" => array(),
				);
				if($to > 1)
				{
					if(($from - $this->member_count) < 0) $from_q = 0;
					else $from_q = $from - $this->member_count;
					if(($to - $from_q - $this->member_count) < 0) $to_q = 0;
					else $to_q = $to - $from_q - $this->member_count;

					$odl_prms["limit"] = $from_q.", ".$to_q;
				}

				$odl = new object_data_list(
					$odl_prms,
					array(
						CL_ML_MEMBER => array("name", "mail"),
					)
				);

				if($to > 1)
				{
					$cnt_all = $odl->count();
					$this->member_count = $this->member_count + $cnt_all;
				}

				foreach($odl->arr() as $oid => $od)
				{
					if(!$args["all"] && $this->ignore_member($source_obj , $oid))
					{
						continue;
					}
					if(!(array_key_exists($od["mail"] , $this->already_found)))
					{
						$this->ml_members[] = array(
							"oid" 		=> $oid,
							"parent"	=> $folder_id,
							"name"		=> $od["name"],
							"mail"		=> $od["mail"]
						);
						if(!($to > 1))$this->member_count++;
					}
					if(!$all) $this->already_found[$od["mail"]] = $od["mail"];
				}
			}
			elseif ($source_obj->class_id() == CL_GROUP)
			{
				$members = $source_obj->connections_from(array(
					"type" => "RELTYPE_MEMBER",
				));
				foreach ($members as $member)
				{
					$member = $member->to();
					if(!$args["all"] && $this->ignore_member($source_obj , $member->id()))
					{
						continue;
					}
					$email = $member->get_first_obj_by_reltype("RELTYPE_EMAIL");
					if(!$email)
					{
						continue;
					}
					if($email->prop("name")) $name = $email->prop("name");
					else $name = $member->name();
					if(!(array_key_exists($email->prop("mail") , $this->already_found)))
					{
						if(!($to > 1) || (!($this->member_count < $from) && ($to > $this->member_count)))
						$this->ml_members[] = array(
							"oid" 		=> $member->id(),
							"parent"	=> $folder_id,
							"name"		=> $name,
							"mail"		=> $email->prop("mail"),
						);
						$this->member_count++;
					}
					if(!$all) $this->already_found[$email->prop("mail")] = $email->prop("mail");
				}
			}
			elseif($source_obj->class_id() == CL_USER)
			{
				if($email = $source_obj->get_first_obj_by_reltype("RELTYPE_EMAIL"))
				{
					if(!(array_key_exists($email->prop("mail") , $this->already_found)))
					{
						if(!($to > 1) || (!($this->member_count < $from) && ($to > $this->member_count)))
						$this->ml_members[] = array(
							"oid" 		=> $folder_id,
							"parent" 	=> $folder_id,
							"name"		=> $name,
							"mail"		=> $email->prop("mail"),
						);
						$this->member_count++;
					}
					if(!$all) $this->already_found[$email->prop("mail")] = $email->prop("mail");
				}
			}
			elseif($source_obj->class_id() == CL_FILE)
			{
				$this->get_members_from_file(array(
					"id" => $source_obj->id(),
					"all" => $all ,
					"from" => $from ,
					"to" => $to,
					"no_return" => $no_return));
			}
			else
			if ($source_obj->class_id() == CL_CRM_SELECTION)
			{
				$si = get_instance(CL_CRM_SELECTION);
				$this->ml_members = array();
				foreach($si->get_selection($source_obj->id()) as $selection_item)
				{
					if (!$this->can("view", $selection_item["object"]))
					{
						continue;
					}
					$co = obj($selection_item["object"]);
					if ($this->can("view", $co->prop("email_id")))
					{
						$eml = obj($co->prop("email_id"));
						$this->ml_members[] = array(
							"oid" => $eml->id(),
							"parent" => $source_obj->id(),
							"name" => $eml->prop("name"),
							"mail" => $eml->prop("mail")
						);
					}
				}
				$this->member_count += count($this->ml_members);
			}
			elseif ($source_obj->class_id() == CL_CRM_SECTOR)
			{
				$this->get_members_from_sector(array(
					"id" => $source_obj->id(),
					"all" => $all ,
					"from" => $from ,
					"to" => $to,
					"no_return" => $no_return));
			}
			elseif ($source_obj->class_id() == CL_CRM_CATEGORY)
			{
				$this->get_members_from_category(array(
					"id" => $source_obj->id(),
					"all" => $all ,
					"from" => $from ,
					"to" => $to,
					"source_data" => $sources_data[$source_obj->id()],
					"no_return" => $no_return));
			}
			elseif($source_obj->class_id() == CL_PERSONNEL_MANAGEMENT_CV_SEARCH_SAVED)
			{
				$list = obj($this->list_id);
				$pm = $list->personnel_management;
				if($this->can("view", $pm) && is_oid($pm))
				{
					$cv_saved_search = $source_obj;
					$personnel_management = obj($pm);
					$ol_prms = $personnel_management->instance()->cv_search_filter($personnel_management, $cv_saved_search->meta());
					$ol = new object_list($ol_prms);
					foreach($ol->arr() as $o)
					{
						$ml = $o->emails()->begin();
						if(is_object($ml) && strlen($ml->mail))
						{
							$this->ml_members[] = array(
								"oid" => $ml->id(),
								"parent" => $folder_id,
								"name" => $ml->name(),
								"mail" => $ml->mail,
							);
							$this->member_count++;
							if(!$all) $this->already_found[$ml->prop("mail")] = $ml->prop("mail");
						}
					}

				}
			}
		}
		if(!$all)
		{
			$this->member_count = sizeof($this->already_found);
		}
		return $this->ml_members;
	}

	function parse_alias($args = array())
	{
		$targ = obj($args["alias"]["target"]);
		enter_function("ml_list::parse_alias");
		$cb_errmsg = aw_global_get("cb_errmsg");
		$cb_errmsgs = aw_global_get("cb_errmsgs");
		$cb_reqdata = aw_global_get("cb_reqdata");
		aw_session_del("cb_errmsg", "");
		aw_session_del("cb_errmsgs", "");
		aw_session_del("cb_reqdata", "");
		$tobj = new object($args["alias"]["target"]);
		$sub_form_type = $tobj->prop("sub_form_type");
		if (!empty($args["alias"]["relobj_id"]))
		{
			$relobj = new object($args["alias"]["relobj_id"]);
			$meta = $relobj->meta("values");
			if (!empty($meta["CL_ML_LIST"]["sub_form_type"]))
			{
				$sub_form_type = $meta["CL_ML_LIST"]["sub_form_type"];
			}
		}
		$tpl = ($sub_form_type == 0) ? "subscribe.tpl" : "unsubscribe.tpl";
		$this->read_template($tpl);
		lc_site_load("ml_list", &$this);
		if ($this->is_template("FOLDER") && $tobj->prop("multiple_folders") == 1 && $sub_form_type == 0)
		{
			$langid = aw_global_get("lang_id");
			$c = "";
			$choose_menu = $targ->prop("choose_menu");	
			foreach($choose_menu as $folder)
			{
				$folder_obj = obj($folder);
				$folders = $folder_obj->connections_from(array(
					"type" => "RELTYPE_LANG_REL",
					"to.lang_id" => $langid,
				));

				if($langid == $folder_obj -> lang_id())
				{
					$this->vars(array(
						"folder_name" => $folder_obj -> trans_get_val("name"),
						"folder_id" => $folder_obj -> id(),
					));
					$c .= $this->parse("FOLDER");
				}
				
				else
				{
					if(count($folders)>1)
					{
						foreach($folders as $folder_conn)
						{
							$conn_fold_obj = obj($folder_conn->prop("to"));
							if(($langid == $conn_fold_obj->lang_id()) && ($folder_conn->prop("from") == $folder))
							{
								$this->vars(array(
									"folder_name" => $conn_fold_obj->trans_get_val("name"),
//									"folder_name" => $folder_conn->prop("to.name"),
									"folder_id" => $folder_conn->prop("to"),
								));
								$c .= $this->parse("FOLDER");
							}
						}
					}
					else
					{
						$conns_to_orig = $folder_obj->connections_to(array(
							"type" => 22,
//							"to.lang_id" => $langid,
						));
						foreach($conns_to_orig as $conn)
						{
							if($conn->prop("from.lang_id") == $langid)
							{
								$this->vars(array(
								"folder_name" => $conn->prop("from.name"),
								"folder_id" => $conn->prop("from"),
								));
								$c .= $this->parse("FOLDER");
							}
							else 
							{
								$from_obj = obj($conn->prop("from"));
								$conns = $from_obj->connections_from(array(
									"type" => "RELTYPE_LANG_REL",
									"to.lang_id" => $user_lang,
								));
								foreach($conns as $conn)
								{
									$this->vars(array(
									"folder_name" => $conn_fold_obj->trans_get_val("name"),
//"folder_name" => $conn->prop("to.name"),
									"folder_id" => $conn->prop("to"),
									));
									$c .= $this->parse("FOLDER");
								}			
							}
						}						
					}
				}
			}
			$this->vars(array(
				"FOLDER" => $c,
			));	
		}
		if ($this->is_template("LANGFOLDER") && $tobj->prop("multiple_languages") == 1)
		{
			$lg = get_instance("languages");
			$langdata = array();
			$langdata = $lg->get_list();
			$c = "";
			$choose_languages = $targ->prop("choose_languages");
			foreach($langdata as $id => $lang)
			{
				if(in_array($id, $choose_languages))
				{	
					$this->vars(array(
						"lang_name" => $lang,
						"lang_id" => $id,
					));
					$c .= $this->parse("LANGFOLDER");
				}	
			}			
			$this->vars(array(
				"LANGFOLDER" => $c,
			));	
		}
		
		if ($this->is_template("FOLDER") && $tobj->prop("multiple_folders") == 1 && $sub_form_type == 1)
		{
			$this->parse_unsubscribe($tobj);
		}
		// this is sl8888w and otto needs to be fffffaaassssttt
		/*
		$classificator_inst = get_instance(CL_CLASSIFICATOR);
		
		for ($i = 1; $i <= 5; $i++)
		{
			$options = $classificator_inst->get_options_for(array(
				"clid" => CL_ML_MEMBER,
				"name" => "udef_classificator$i",
			));
			
			$this->vars(array(
				"classificator$i" => html::select(array(
					"name" => "udef_classificator[$i]",
					"options" => $options,
				)),
			));
		}
		*/

		if (is_array($cb_reqdata))
		{
			$this->vars($cb_reqdata);
		};

		if(is_array($cb_errmsgs) && count($cb_errmsgs))
		{
			foreach($cb_errmsgs as $errprop => $msg)
			{
				$this->vars(array(
					"err_".$errprop => $msg["msg"],
				));
			}
		}

		$this->vars(array(
			"listname" => $tobj->name(),
			"cb_errmsg" => $cb_errmsg,
			"reforb" => $this->mk_reforb("subscribe",array(
				"id" => $targ->id(),
				"rel_id" => $relobj->id(),
				"section" => aw_global_get("section"),
			)),
		));
		exit_function("ml_list::parse_alias");
		return $this->parse();

	}

	function parse_unsubscribe($obj)
	{
		$c = "";
		$folders = $obj->prop("choose_menu");
		foreach($folders as $folder)
		{
			$folder_obj = obj($folder);
			$this->vars(array(
				"folder_name" => $folder_obj -> trans_get_val("name"),
				"folder_id" => $folder_obj -> id(),
			));
			$c .= $this->parse("FOLDER");
		}
		$this->vars(array(
			"FOLDER" => $c,
		));	
	}

	//! teeb progress bari
	// tegelt saax seda pitidega teha a siis tekib iga progress bari kohta oma query <img src=
	// see olex overkill kui on palju queue itemeid
	function queue_ready_indicator($osa,$kogu)
	{
		if (!$kogu)
		{
			$p = 100;
		}
		else
		{
			$p = (int)((int)$osa * 100 / (int)$kogu);
		}
		$not_p = 100 - $p;
		// tekst pane sinna, kus on rohkem ruumi.
		if ($p > $not_p)
		{
			$p1t = "<span Style='font-size:10px;font-face:verdana;'><font color='white'>".$p."%</font></span>";
		}
		else
		{
			$p2t = "<span Style='font-size:10px;font-face:verdana;'><font color='black'>".$p."%</font></span>";
		}
		// kommentaar on selleks, et sorteerimine tootaks (hopefully)
		return "<!-- $p --><table bgcolor='#CCCCCC' Style='height:12;width:100%'><tr><td width=\"$p%\" bgcolor=\"blue\">$p1t</td><td width=\"$not_p%\">$p2t</td></tr></table>";
	}
	
	//! teeb genereerimise progressi bari
	function gen_ready_indicator($p)
	{
		extract($p);
		$qid = (int)$qid;
		$q = "SELECT count(*) as cnt FROM ml_sent_mails WHERE qid = '$qid'";
		$this->db_query($q);
		while($w = $this->db_next())
		{
			if (!$total)
			{
				$p = 100;
			}
			else
			{
				$p = (int)((int)$w["cnt"] * 100 / (int)$total);
			}
			$not_p = 100 - $p;
			if ($p > $not_p)
			{
				$p1t = "<span Style='font-size:10px;font-face:verdana;'><font color='white'>".$p."%</font></span>";
			}
			else
			{
				$p2t = "<span Style='font-size:10px;font-face:verdana;'><font color='black'>".$p."%</font></span>";
			}
			return "<!-- $p --><table bgcolor='#CCCCCC' Style='height:12;width:100%'><tr><td width=\"$p%\" bgcolor=\"green\">$p1t</td><td width=\"$not_p%\">$p2t</td></tr></table>";
		}
		return "";
	}
	
	////
	// !This will generate a raport for a single mail sent to a list.
	// Ungh, shouldn't this be a separate class then?
	function gen_mail_report_table($arr)
	{
		$perpage = 100;
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "member",
			"caption" => t("Kellele"),
		));
		$t->define_field(array(
			"name" => "tm",
			"caption" => t("Millal"),
			"talign" => "center",
			"type" => "time",
			"format" => "H:i d-m-Y",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "clicked",
			"caption" => t("Klikitud"),
		));
		$_mid = (int)$arr["request"]["mail_id"];
		$qid = (int)$arr["request"]["qid"];
		$id = $arr["obj_inst"]->id();
		$q1 = "SELECT COUNT(*) as cnt FROM ml_sent_mails WHERE lid = '$id' AND mail='$_mid' AND qid = '$qid' AND mail_sent = 1";
		$cnt = $this->db_fetch_field($q1, "cnt");
		$q = "SELECT target, tm, subject, id, vars 
			FROM ml_sent_mails
			WHERE lid = '$id' AND mail = '$_mid' AND qid = '$qid' AND mail_sent = 1 ORDER BY tm DESC LIMIT ".(100*$arr["request"]["ft_page"]).", 100";
		$this->db_query($q);
		while ($row = $this->db_next())
		{
			$tgt = htmlspecialchars($row["target"]);
			$row["member"] = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $id, 
					"group" => "show_mail", 
					"mail_id" => $arr["request"]["mail_id"], 
					"s_mail_id" => $row["id"],
				)),
				"caption" => $tgt,
			));
			$row["clicked"] = ($row["vars"] == 1 ? t("jah") : t("ei"));
			$t->define_data($row);
		}
// 		$q = "SELECT target 
// 			FROM ml_sent_mails
// 			WHERE lid = '$id' AND mail = '$_mid' AND qid = '$qid' AND mail_sent = 1";
// 		$this->db_query($q);		
// 		while ($row = $this->db_next())
// 		{
// 			echo $row["target"].'<br>';
// 		}
		
		$t->d_row_cnt = $cnt;
		$t->set_header($t->draw_text_pageselector(array(
			"records_per_page" => $perpage,
		)));
		$t->sort_by();
	}

	function gen_mail_subject($arr)
	{
		$mail_id = $arr["request"]["mail_id"];
		$mail_obj = new object($mail_id);
		return $mail_obj->name();
	}

	function gen_percentage($arr)
	{
		$list_id = $arr["obj_inst"]->id();
		$mail_id = (int)$arr["request"]["mail_id"];
		
		// how many members does this list have?
		$row = $this->db_fetch_row("SELECT total,qid,position,status FROM ml_queue WHERE lid = '$list_id' AND mid = '$mail_id'");
		$member_count = $row["total"];
		$qid = (int)$row["qid"];
		
		// how many members have been served?
		$served_count = 0;
//		$served_count = $this->db_fetch_row("SELECT count(*) AS cnt FROM ml_sent_mails WHERE lid = '$list_id' AND mail = '$mail_id' AND qid = '$qid'");
//		$row2 = $this->db_fetch_row("SELECT position,status FROM ml_queue WHERE lid = '$list_id' AND mid = '$mail_id'");
		$served_count = $row["position"];
		$url = $_SERVER["REQUEST_URI"];
	
		if (!headers_sent() && $served_count < $member_count)
		{
			$refresh_rate = 30;
			header("Refresh: $refresh_rate; url=$url");
			$str = ", v&auml;rskendan iga ${refresh_rate} sekundi j&auml;rel";
		}
		$ret = sprintf(t("Liikmeid: %s, saadetud: %s %s"), $member_count, $served_count, $str);
		if($row["status"] == 5)
		{
			$ret.= "<br>\n";
			$row3 = $this->db_fetch_row("SELECT count(*) as cnt FROM ml_sent_mails WHERE qid = '$qid' ");
			$ret.= t("Genereeritud")." : ".$row3["cnt"];
		}
		return $ret;
	}

	function callback_mod_tab($arr)
	{
		// hide it, if no mail report is open
		if ($arr["id"] == "mail_report" && empty($arr["request"]["mail_id"]))
		{
			return false;
		}
		if ($arr["id"] == "show_mail" && empty($arr["request"]["s_mail_id"]))
		{
			return false;
		}
		if ($arr["id"] == "mail_report")
		{
			$arr["link"] .= "&mail_id=" . $arr["request"]["mail_id"].'&qid='.$arr["request"]["qid"];
		}
		if ($arr["id"] == "write_mail" && $arr["request"]["group"] != "write_mail")
		{
			return false;
		}
	}

	function gen_ml_message_view($arr)
	{
		$mail_id = (int)$arr["request"]["s_mail_id"];
		if (!is_array($this->msg_view_data))
		{
			$this->msg_view_data = $this->db_fetch_row("SELECT * FROM ml_sent_mails WHERE id = '$mail_id'");
		}

		$rv = "";

		switch($arr["prop"]["name"])
		{
			case "show_mail_from":
				$rv = htmlspecialchars($this->msg_view_data["mfrom"]);
				break;

			case "show_mail_subject":
				$rv = $this->msg_view_data["subject"];
				break;

			case "show_mail_message":
				$rv = nl2br($this->msg_view_data["message"]);
				break;
		}
		return $rv;
	}

	function _get_mail_message_object($arr)
	{
		if(is_object($this->msg_obj))
		{
			return $this->msg_obj;
		}
		$writer = get_instance(CL_MESSAGE);
		$writer->init_class_base();
		$all_props = $writer->get_property_group(array(
			"group" => "general",
		));
		if (is_oid($arr["request"]["msg_id"]))
		{
			$this->msg_obj = new object($arr["request"]["msg_id"]);
			$arr["obj_inst"]->set_prop("write_user_folder", $this->msg_obj->meta("list_source"));
		}
		else
		{
			$arr["obj_inst"]->set_prop("write_user_folder", null);
			$this->msg_obj = new object();
			$this->msg_obj->set_class_id(CL_MESSAGE);
			$folder = $arr["obj_inst"]->prop("msg_folder");
			$this->msg_obj->set_parent((!empty($folder) ? $folder : $arr["obj_inst"]->parent()));
			$this->msg_obj->save();
		};
		return $this->msg_obj;
	}

//seda funktsiooni nyyd enam vaja pole ma loodan... a igaks juhuks jatab praegu alles
/*
	function callback_gen_write_mail($arr)
	{
		$writer = get_instance(CL_MESSAGE);
		$writer->init_class_base();
		$all_props = $writer->get_property_group(array(
			"group" => "general",
		));
		if (is_oid($arr["request"]["msg_id"]))
		{
			$msg_obj = new object($arr["request"]["msg_id"]);
			$arr["obj_inst"]->set_prop("write_user_folder", $msg_obj->meta("list_source"));
		}
		else
		{
			$arr["obj_inst"]->set_prop("write_user_folder", null);
			$msg_obj = new object();
			$msg_obj->set_class_id(CL_MESSAGE);
			$folder = $arr["obj_inst"]->prop("msg_folder");
			$msg_obj->set_parent((!empty($folder) ? $folder : $arr["obj_inst"]->parent()));
			//$msg_obj->save();
		};
		$templates = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_TEMPLATE",
		));
		$filtered_props = array();
		
		if (!strlen($msg_obj->prop("message")) > 0)
		{
			$options = array(0 => t(" - vali - "));
			$tm = get_instance("templatemgr");
			$options = $tm->template_picker(array(
				"folder" => "automatweb/mlist"
			));
			foreach($templates as $template)
			{
				$options[$template->prop("to")] = $template->prop("to.name");
			}
			$filtered_props["copy_template"] = array(
				"type" => "select",
				"name" => "copy_template",
				"options" => $options,
				"caption" => t("Vali template mille sisusse kopeerida"),
				"value" => $msg_obj->meta("copy_template"),
			);
		}
		
		// insert a template selector, if there are any templates available
		if (sizeof($templates) > 0 )
		{
			$options = array(0 => t(" - vali - "));
			foreach($templates as $template)
			{
				$options[$template->prop("to")] = $template->prop("to.name");
			}
			$filtered_props["template_selector"] = array(
				"type" => "select",
				"name" => "template_selector",
				"options" => $options,
				"caption" => t("Vali template"),
				"value" => $msg_obj->meta("template_selector"),
			);
		}
		
		$filtered_props["send_away"] = array(
			"name" => "send_away",
			"type" => "checkbox",
			"ch_value" => 1,
			"caption" => t("Saada peale salvestamist &auml;ra"),
		);

		// narf, can I make this work better perhaps? I really do hate callback ..
		// and I want to embed a new object. And I have to functionality in form
		// of releditor. So why can't I use _that_ to write a new mail. Eh?
		
		// would be nice to have some other and better method to do this
		

		
		$prps = array("name", "html_mail", "msg_contener_title", "msg_contener_content" , "mfrom",  "mfrom_name"); 
		foreach($all_props as $id => $prop)
		{
			if (in_array($id, $prps))
			{
				if ($id == "mfrom")
				{
					$prop["caption"] = t("Saatja e-maili aadress");
				}
				elseif($id == "mfrom_name")
				{
					$prop["caption"] = t("Saatja nimi");
					$prop["type"] = "textbox";
				}
				elseif($id == "message")
				{
					$prop["richtext"] = 1;
					$filtered_props["legend"] = array(
						"type" => "text",
						"name" => "legend",
						"caption" =>  t("Asenduste legend"),
						"value" => t("Meili sisus on v&otilde;imalik kasutada j&auml;rgnevaid asendusi:<br /><br />
							#username# - AutomatWebi kasutajanimi<br />
							#name# - Listi liikme nimi<br />
							#e-mail# - Listi liikme e-mail<br/>
							#subject# - Kirja teema<br />
							#pea#(pealkiri)#/pea# - (pealkiri) asemele kirjutatud tekst muutub 1. taseme pealkirjaks<br />
							#ala#(pealkiri)#/ala# - (pealkiri) asemele kirjutatud tekst muutub 2. taseme pealkirjaks<br />
							#lahkumine# - link, millel klikkides inimene saab listist lahkuda<br />
							<br />
							Kui soovid kirja pandava lingi puhul teada saada, kas saaja sellele ka klikkis, lisa lingi aadressi l&otilde;ppu #traceid#
							N&auml;iteks: http://www.struktuur.ee/aw#traceid#
							"),
					);
				}
				$filtered_props[$id] = $prop;
			}
		}
		$filtered_props["id"] = array(
			"name" => "id",
			"type" => "hidden",
			"value" => $msg_obj->id(),
		);
		
		$filtered_props["save_as_new"] = array(
			"name" => "save_as_new",
			"type" => "checkbox",
			"ch_value" => 1,
			"caption" => t("Salvesta uue kirjana"),
		);
		
		$filtered_props["aliasmgr"] = array(
			"name" => "aliasmgr",
			"type" => "aliasmgr",
			"editonly" => 1,
			"trans" => 1,
		);
		if($msg_obj)
		{
			if(!$filtered_props["message"]["value"])$filtered_props["message"]["value"] = $msg_obj->prop("message"); 
			$xprops = $writer->parse_properties(array(
				"obj_inst" => $msg_obj,
				"properties" => $filtered_props,
		//		"name_prefix" => "emb",
				"classinfo" => array("allow_rte" => $arr["obj_inst"]->prop("classinfo_allow_rte")),
			));
		}
//		if(is_oid($xprops["emb_mfrom"]["value"]))
//		{
//		$email_obj = obj($xprops["emb_mfrom"]["value"]);
//		$mailto = $email_obj->prop("mail");
//		//$xprops["emb_mfrom"]["value"] = $mailto;
//		}
//
//		arr($xprops);
		$xprops["message"]["value"] = $msg_obj->prop("message");
		$xprops["message"]["type"] = "textarea";
		$xprops["message"]["cols"] = 80;
		$xprops["message"]["rows"] = 40;
		$xprops["message"]["origin_type"] = "textarea";
		return $xprops;
	}
*/

	function submit_write_mail($arr)//+-+-
	{
		$img_inst = get_instance(CL_IMAGE);
		$msg_data = $arr["request"];
		$msg_data["id"] = (int)$arr["request"]["message_id"];
		$msg_data["name"] = $arr["request"]["subject"];
		if(!$msg_data["html_mail"])
		{
			$msg_data["html_mail"] = "0";
		}else $msg_data["html_mail"] = 1024;
		unset($msg_data["rawdata"]);

		$msg_data["mto"] = $arr["obj_inst"]->id();
		$folder = $arr["obj_inst"]->prop("msg_folder");
		$mail_id = $msg_data["id"];
		if(is_oid($msg_data["id"]) && $this->can("view", $msg_data["id"]))
		{
			if(!$arr["request"]["save_as_new"])
			{
				$status = $this->db_fetch_row("SELECT status FROM ml_queue WHERE lid = ".$arr["obj_inst"]->id()." ANd mid = ".$msg_data["id"]);
				if(!$status["status"])
				{
					$msg_obj = obj($msg_data["id"]);
					$new = 1;
				}
			}
		}
		if(!$new)
		{
			$msg_obj = obj();
			$msg_obj->set_parent((!empty($folder) ? $folder : $arr["obj_inst"]->parent()));
			$msg_obj->set_class_id(CL_MESSAGE);
			$msg_obj->save();
			if(is_oid($msg_data["id"]) && $this->can("view" , $msg_data["id"]))
			{
				$ex_mess = obj($msg_data["id"]);
				$ex_conns = $ex_mess->connections_from(array());
				if(count($ex_conns) > 0)
				{
					foreach ($ex_conns as $ex_conn)
					{
						$exid = $ex_conn->prop("to");
						$msg_obj->connect(array(
							"to" => $exid,
							"reltype" => $ex_conn->prop("reltype"),
						));
					}
				}

			}
			$msg_data["id"] = $msg_obj->id();
		}
		$tpl = $msg_data["template_selector"];
		$msg_obj->set_meta("list_source", $arr["obj_inst"]->prop("write_user_folder"));
//arr($msg_data);arr(strlen($msg_data["message"]));die();
		if((!strlen(trim(str_replace("<br>" , "" , $msg_data["message"]))) > 0 ) && $msg_data["copy_template"])
		{
			if(is_oid($msg_data["copy_template"]))
			{
				$template_obj = obj($msg_data["copy_template"]);
				$this->use_template($template_obj->prop("content"));
			}
			else
			{
				$this->read_site_template($msg_data["copy_template"]);
			}
			if(is_object($ro = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_REGISTER_DATA")))
			{
				$c = obj($ro->meta("cfgform_id"));
				foreach($c->get_property_list() as $key => $val)
				{
					$this->vars(array($key => $c->prop($key)));
				}
				foreach($ro->get_property_list() as $key => $val)
				{
					$this->vars(array($key => $ro->prop($key)));
				}
				foreach($c->get_property_list() as $key => $val)
				{
					if($c->prop($key))
					{
						$this->vars(array(strtoupper($key) => $this->parse(strtoupper($key))));
					}
				}
				foreach($ro->get_property_list() as $key => $val)
				{
					if($ro->prop($key))
					{
						$this->vars(array(strtoupper($key) => $this->parse(strtoupper($key))));
					}
				}
				$asd = 1;
				while($asd < 6)
				{
					if(is_object($x = $ro->get_first_obj_by_reltype("RELTYPE_IMAGE".$asd)))
					{
					$this->vars(array("userim".$asd => $img_inst->make_img_tag_wl($x->id())));
					}
					$asd++;
					
				}
			}
			$msg_data["message"] = $this->parse();
		}

		//et igasugu urlid saaks kas urli objektist k"tte v6i salvestaks v2hemalt baseurli ette.
		$msg_data["message"] = $arr["obj_inst"]->make_fck_urls_good($msg_data["message"]);

		//uhh......minuarust on selle koigega ikka miski jama
		//toenaoliselt on varsti jalle miski probleem selle kohaga...a noh
		//no kui kirja kirjutad html vaates ja ei pane html kirjaks, siis on ikka enda viga kui miski vigane vark ara saadetakse
		//eks ma praegu kommenteerin selle koha valja... niikuinii on kellelgi varsti jalle vaja seda tagasi
/*		if((!$msg_obj->prop("html_mail")) && $msg_data["send_away"])
		{
			//$msg_data["message"] = str_replace("\n", "", $msg_data["message"]);
			$msg_data["message"] = str_replace("<br />", "\n", $msg_data["message"]);
			$msg_data["message"] = strip_tags($msg_data["message"]);
			$msg_data["message"] = html_entity_decode($msg_data["message"]);
		}
*/		$writer = get_instance(CL_MESSAGE);
		//$writer->init_class_base();
		//$message_id = $writer->submit($msg_data);die();

		foreach($msg_data as $prop => $val)
		{
			if($msg_obj->is_property($prop))
			{
				$msg_obj->set_prop($prop , $val);
			}
		}
		if($arr["request"]["take_out_adrr"])
		{
			$msg_obj->set_meta("let_choose_addresses" , 1);
		}
		$msg_obj->save();
		$message_id = $msg_obj->id();
		$writer->id = $msg_obj->id();
/*
		arr($msg_obj->prop("message"));
		$msg_obj->set_prop("message" , $msg_data["message"]);
		$msg_obj->save();
arr($msg_obj->prop("message"));
*/

		$sender = $msg_obj->prop("mfrom");
		if($this->can("view", $sender))
		{
			$arr["obj_inst"]->connect(array(
				"to" => $sender,
				"reltype" => "RELTYPE_SENDER",
			));
		}
		//arr($msg_data["message"]);
		//$msg_obj->set_prop("message" , $msg_data["message"]);
		//$msg_obj->save();

		if ($this->can("view", $tpl))
		{
			$msg_obj->set_meta("template_selector", $tpl);
			$tpl_obj = new object($tpl);
			if ($tpl_obj->prop("is_html") == 1)
			{
				$msg_obj->set_prop("html_mail", 1024);
			}	
			$msg_obj->save();
		}
		else
		{
			$msg_obj->set_meta("template_selector", null);
			$msg_obj->save();
		}
		if ($msg_data["send_away"] == 1)
		{
			// XXX: work out a way to save the message and not send it immediately
			$writer->send_message(array(
				"id" => $message_id,
				"to_post" => $arr["to_post"],
				"mfrom"	=> $msg_data["mfrom"],
			));
		}
		else
		{
			$this->edit_msg = $message_id;
		}
	}
	
	// this one can be used to send a message to mailinglist from code
	// following parameters can be used
        //      Array
        //      (
        //              [send_away] => 1 // sends mesage away (int)
        //              [mfrom_name] => sender name (string)
        //              [mfrom] => name@mail.ee (string)
        //              [name] => topic (string)
        //              [message] => message content (string)
        //              [msg_contener_title] => have no idea what for (string)
        //              [msg_contener_content] => have no idea what for (string)
        //              [id] => I assume that this is message oid (int)
        //              [mto] => mailing list oid (int) (required)
        //              [return] => id  // if this is set, it gives back created object id
        //                              // else it gives back the redirection url
	//		[submit_post_message] => 0|1 (int) // if 0, then the message wouldn't
	//						   // be sent right away
	//						   // submit_post_message method will not be called
        //      )

	//teeb fck igasugu urlid mailile kohaseks, et mujal maailmas ka aru saaks kuhu asi viitab
	function make_fck_urls_good($msg)
	{
		$x = 0;
		$regs = array();
		if ($asd = preg_match_all("/\/[0-9]+/", $msg, $regs)) 
		{
			if($x > 100) break;
			foreach($regs[0] as $reg)
			{
				$reg = substr($reg , 1);
				if($this->can("view" , $reg))
				{
					$link = obj($reg);
					if($link->class_id() == CL_EXTLINK)
					{
						$msg = str_replace('href="/'.$link->id().'"', 'href="'.$link->prop("url").'"' , $msg);
						$msg = str_replace("href='/".$link->id()."'", "href='".$link->prop("url")."'" , $msg);
						$msg = str_replace('href="'.aw_ini_get("baseurl").'/'.$link->id().'"', 'href="'.$link->prop("url").'"' , $msg);
						$msg = str_replace("href='".aw_ini_get("baseurl")."/".$link->id()."'", 'href="'.$link->prop("url").'"' , $msg);
						continue;
					}
				}
			}
			if($reg > 1)
			{
				$msg = str_replace('href="/'.$reg.'"', 'href="'.aw_ini_get("baseurl").'/'.$reg.'"' , $msg);
				$msg = str_replace("href='/".$reg."'", "href='".aw_ini_get("baseurl")."/".$reg."'" , $msg);
			}
 			$x++;
		}
/*
		$x = 0;//igaks juhuks, et mingi valemiga tsyklisse ei l2heks
		while (ereg('^(.*href=\"/([0-9]+)\".*)+$', $msg, $regs)) 
		{
			if($x > 100) break;
			foreach($regs as $reg)
			{
				if($this->can("view" , $reg))
				{
					$link = obj($reg);
					if($link->class_id() == CL_EXTLINK)
					{
						$msg = str_replace('href="/'.$link->id().'"', 'href="'.$link->prop("url").'"' , $msg);
						continue;
					}
				}
				if($reg > 1)
				{
					$msg = str_replace('href="/'.$reg.'"', 'href="'.aw_ini_get("baseurl").'/'.$reg.'"' , $msg);
				}
			}
			$x++;
		}

		$x = 0;
		while (ereg("^(.*href=\'/([0-9]+)\'.*)+$", $msg, $regs)) 
		{
			if($x > 100) break;
			foreach($regs as $reg)
			{
				if($this->can("view" , $reg))
				{
					$link = obj($reg);
					if($link->class_id() == CL_EXTLINK)
					{
						$msg = str_replace("href='/".$link->id()."'", "href='".$link->prop("url")."'" , $msg);
						continue;
					}
				}
				if($reg > 1)
				{
					$msg = str_replace("href='/".$reg."'", "href='".aw_ini_get("baseurl")."/".$reg."'" , $msg);
				}
			}
 			$x++;
		}
*/

		return $msg;
	}

	function send_message($arr)
	{
		$mailinglist_obj = obj($arr["mto"]);
		// mail messages folder:
		$folder = $mailinglist_obj->prop("msg_folder");
		$msg_obj = obj();
		$msg_obj->set_parent((!empty($folder) ? $folder : $mailinglist_obj->parent()));
		$msg_obj->set_class_id(CL_MESSAGE);
		$msg_obj->save();
		$arr["id"] = $msg_obj->id();
		if($arr["submit_post_message"] == 1)
		{
			$sched = get_instance("scheduler");
			$sched->add(array(
				"event" => $this->mk_my_orb("process_queue", array(), "ml_queue", false, true),
				"time" => time() + 120,	// every 2 minutes
			));
			$time = time();
			$this->submit_post_message(array(
				"mfrom" => $mfrom,
				"list_id" => $arr["mto"],
				"id" => $arr["id"],
				"start_at" => array(
					"day" => date("d", $time),
					"month" => date("m", $time),
					"year" => date("Y", $time),
					"hour" => date("H", $time),
					"minute" => date("i", $time),
				),
			));
		}
		$this->submit_write_mail(array(
			"request" => array($arr),
			"obj_inst" => obj($arr["mto"]),
		));
	}

	/** delete mails
		@attrib name=delete_mails 
		@param id required type=int 
		@param group optional
	**/
	function delete_mails($arr)
	{
		foreach(safe_array($arr["sel"]) as $member_id)
		{
			if(is_oid($member_id) && $this->can("delete", $member_id))
			{
				$member_obj = new object($member_id);
				if($member_obj->class_id() == CL_MESSAGE)
				{
					$member_obj->delete();
				}
			}
		}
		return $this->mk_my_orb("change", array("id" => $arr["id"], "group" => $arr["group"]));
	}
		
	/** delete members from list
		@attrib name=delete_members 
		@param id required type=int 
	**/
	function delete_members($arr)
	{
		$del_from_file = array();
		foreach(safe_array($arr["sel"]) as $member_id)
		{
			if(is_oid($member_id) && $this->can("delete", $member_id))
			{
				$member_obj = new object($member_id);
				if($member_obj->class_id() == CL_ML_MEMBER)
				{
					$member_obj->delete();
				}
				//teistele allikatele kustutamine peaks olema seose eemaldamine v6i muu sarnane
			}
			else $del_from_file[] = $member_id;
		}
		if(sizeof($del_from_file))
		{
			$f = array();
			foreach($del_from_file as $d)
			{
				$asd = explode("_row_count_", $d);
				$row = $asd[1];
				$file = $asd[0];
				$f[$file][$row] =1;
			}
			$file_inst = get_instance(CL_FILE);
			foreach($f as $file => $data)
			{
				$file_data = $file_inst->get_file_by_id($file);
				$rows = explode("\n" , $file_data["content"]);
				foreach($data as $key => $val)
				{
					if($val)
					{
						unset($rows[$key]);
					}
				}
				$file_data["content"] = join("\n" , $rows);
				$file_data["file_id"] = $file;

				$file_inst->save_file($file_data);
			}
		}

		return $this->mk_my_orb("change", array("id" => $arr["id"], "group" => "search"));
	}

	function callback_post_save($arr)
	{
		$sc = get_instance("scheduler");
		$url = str_replace("automatweb/", "", $this->mk_my_orb("exp_to_file", array("id" => $arr["obj_inst"]->id())));
		$sc->remove(array(
			"event" => $url
		));

		if ($arr["obj_inst"]->prop("expf_path") != "" && $arr["obj_inst"]->prop("expf_num_per_day") > 0)
		{
			$this->_add_expf_sched($arr["obj_inst"]);
		}
	}

	function _add_expf_sched($o)
	{
		$sc = get_instance("scheduler");
		$url = str_replace("automatweb/", "", $this->mk_my_orb("exp_to_file", array("id" => $o->id())));
		$sc->remove(array(
			"event" => $url
		));

		// get start of day
		$time = time() - (time() % (24*3600));

		// get num of hours between exports
		$numh = 24 / $o->prop("expf_num_per_day");

		// get num secs
		$nums = $numh * 3600;

		// make next time
		while( $time < time())
		{
			$time += $nums;
		}

		$sc->add(array(
			"event" => $url,
			"time" => $time
		));
	}

	/** exports list members to textfile

		@attrib name=exp_to_file

		@param id required type=int acl=view
	**/
	function exp_to_file($arr)
	{
		$last_time = $this->get_cval("ml_list::exp_to_file::".$arr["id"]."::time");

		$ser = $this->export_members(array(
			"ret" => true,
			"id" => $arr["id"],
			"export_type" => ML_EXPORT_ALL,
			"export_date" => $last_time
		));

		// get file name
		$l = obj($arr["id"]);
		$num = 0;
		do {
			$num++;
			$fn = $l->prop("expf_path")."/".date("Y")."-".date("m")."-".date("d")."-".$num.".csv";
		} while(file_exists($fn));

		$this->put_file(array(
			"file" => $fn,
			"content" => $ser
		));

		$this->set_cval("ml_list::exp_to_file::time", $last_time);

		// add to scheduler
		$this->_add_expf_sched($l);
		die(t("all done"));
	}
		
	/**
		@attrib api=1 params=name name=delete_old
		@param ml required type=oid
		ml_list oid
	**/
	function delete_old($arr)
	{
		extract($arr);
		$time = time()-3*30*3600;
		$ml = (int)$ml;
		$row = $this->db_fetch_row("DELETE FROM ml_sent_mails WHERE lid = '$ml' AND mail_sent = 1 and tm < '$time'");
		print "<script language='javascript'>window.opener.location.reload();;
			window.close();
		</script>";
		arr("kustutab saasta");
	}
	
	/**
	@attrib api=1 params=name name=register_data
	@param ml required type=oid
		ml_list oid
	@param ru optional type=string
		return url
	**/
	function register_data($arr)
	{
		$arr["obj_inst"] = obj($arr["ml"]);
		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_REGISTER_DATA");
		if(!is_object($o))
		{
			$o = new object();
			$o->set_parent($arr["obj_inst"]->id());
			$o->set_class_id(CL_REGISTER_DATA);
			$o->set_name($arr["obj_inst"]->name()." ".t("kirjade registri andmed"));
			$o->save();
			if($arr["obj_inst"]->prop("register_data_cgfform_id"))
			{
				$o->set_meta("cfgform_id", $arr["obj_inst"]->prop("register_data_cgfform_id"));
			}
			$o->save();
			$arr["obj_inst"]->connect(array(
				"to" => $o->id(),
				"reltype" => "RELTYPE_REGISTER_DATA",
			));
		}
		return html::get_change_url($o->id(), array("return_url" => $arr["ru"]));
	}
	
	private function _init_members_table(&$t, $list)
	{
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"width" => 50,
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("Aadress"),
			"sortable" => 1,
		));
		
		$t->define_field(array(
			"name" => "source",
			"caption" => t("Allikas"),
			"sortable" => 1,
		));

		if(isset($GLOBALS["mailinglist_show_org_column"]))
		{
			$t->define_field(array(
				"name" => "co",
				"caption" => t("Organisatsioon"),
				"sortable" => 1,
			));
		}
		
		if(isset($GLOBALS["mailinglist_show_person_columns"]))
		{
			$t->define_field(array(
				"name" => "section",
				"caption" => t("Osakond"),
				"sortable" => 1,
			));

			$t->define_field(array(
				"name" => "profession",
				"caption" => t("Ametinimetus"),
				"sortable" => 1,
			));
		}
			
		$t->define_field(array(
			"name" => "joined",
			"caption" => t("Liitunud"),
			"sortable" => 1,
			"type" => "time",
			"format" => "H:i d-m-Y",
			"smart" => 1,
		));
			
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));

		$cfg = $list->prop("member_config");
		if(is_oid($cfg) && $this->can("view", $cfg))
		{
			$config_obj = &obj($cfg);
			$config_data = $config_obj->meta("cfg_proplist");
			uasort($config_data, array($this,"__sort_props_by_ord"));
				
			foreach($config_data as $key => $item)
			{
				strpos($key, "def_txbox");
				if(strpos($key, "def_txbox"))
				{
					$t->define_field(array(
						"name" => $item["name"],
						"caption" => $item["caption"],
						"sortable" => 1,
					));
				}
					
				if(strpos($key, "def_date"))
				{
					$t->define_field(array(
						"name" => $item["name"],
						"caption" => $item["caption"],
						"sortable" => 1,
					));	
				}
			}
		}

		if(!empty($cfg))
		{
			$t->define_field(array(
				"name" => "others",
				"caption" => t("Liitumisinfo"),
			));
		}
		$t->define_field(array(
			"name" => "ignore",
			"caption" => t("Ignoreeri"),
		));
	}

	function member_search_table($arr)
	{
		$ml_member_inst = get_instance(CL_ML_MEMBER);
		$search_name = $arr["request"]["search_name"];
		$search_mail = $arr["request"]["search_mail"];

		$t = &$arr["prop"]["vcl_inst"];
		$t->set_default_sortby("id");
		$t->set_default_sorder("desc");

		$this->list_id = $arr["obj_inst"]->id();

		if(isset($arr["request"]["search_from_source"]) && is_array($arr["request"]["search_from_source"]) && sizeof($arr["request"]["search_from_source"]))
		{
			$members = $arr["obj_inst"]->get_members(array(
				"sources" => array_keys($arr["request"]["search_from_source"]),
				"mail" => $search_mail,
				"name" => $search_name,
				"all" => 1,
			));
		}
		else
		{
			$members = array();
		}

		$this->_init_members_table($t, $arr["obj_inst"]);

		//hangib organisatsioonide, ametinimetuste ja osakondade nimed
		if(!empty($GLOBALS["mailinglist_show_org_column"]))
		{
			$co_array = array();
			$pro_array = array();
			$sec_array = array();
			foreach($members as $key => $val)
			{
				if($val["co"]) $co_array[$val["co"]] = $val["co"];
				if($val["pro"]) $pro_array[$val["pro"]] = $val["pro"];
				if($val["section"]) $sec_array[$val["section"]] = $val["section"];
			}
			$pros = new object_list();
			$pros->add($pro_array);
			$pros = $pros->names();
			$cos = new object_list();
			$cos->add($co_array);
			$cos = $cos->names();
			$secs = new object_list();
			$secs->add($sec_array);
			$secs = $secs->names();
		}

		foreach($members as $key => $val)
		{
			$is_oid = 0;
			if(is_oid($val["oid"]))
			{
				$is_oid = 1;
			}
			if(!(strlen($val["name"]) > 0))
			{
				$val["name"] = "(nimetu)";
			}
			$parent = obj($val["parent"]);
			$parent_name = $parent->name();;
			if($is_oid)
			{
				list($mailto,$memberdata) = $ml_member_inst->get_member_information(array(
					"lid" => $arr["obj_inst"]->id(),
					"member" => $val["oid"],
					"from_user" => true,
				));
				$joined = $memberdata["joined"];
				$source = ($parent->class_id() == $parent->prop("parent.class_id") ? html::href(array(
					"url" => admin_if::get_link_for_obj($parent->parent()),
					"caption" => $parent->prop("parent.name"),
				))."->" : "" ). html::href(array(
					"url" => admin_if::get_link_for_obj($val["parent"]),
					"caption" => $parent_name,
				));
				$others = html::href(array(
					"caption" => t("Vaata"),
					"url" => $this->mk_my_orb("change", array(
						"id" => $val["oid"],
						"group" => "udef_fields",
						"cfgform" => $arr["obj_inst"]->prop("member_config"),
						), CL_ML_MEMBER),
				));
				$name = html::get_change_url($val["oid"], array("return_url" => get_ru()), $val["name"]);
				$oid = $val["oid"];
			}
			else
			{
				$source = $parent_name;
				$name = $val["name"];
				$oid = $val["parent"]."_row_count_".$val["row_cnt"];
			}

			$tabledata = array(
				"id" => $val["oid"],
				"email" => $val["mail"],
				"joined" => $memberdata["joined"],
				"source" => $source,
				"others" => $others,
				"name" => $name,
				"oid" => $oid,
			);

			$tabledata["ignore"] = html::checkbox(array(
				"name" => "ignore_members[".$val["parent"]."][".$val["oid"]."]",
				"checked" => $this->ignore_member(obj($val["parent"]) , $val["oid"]) ? 1: 0,
				"value" => 1,
			)).html::hidden(array(
				"name" => "ignore_members_count[".$val["parent"]."][".$val["oid"]."]",
				"value" => 1,
			));

			if($val["co"])
			{
				$tabledata["co"] = $cos[$val["co"]];
			}
			if($val["pro"])
			{
				$tabledata["pro"] = $pros[$val["pro"]];
			}
			if($val["section"])
			{
				$tabledata["section"] = $secs[$val["section"]];
			}
			$t->define_data($tabledata);
		}


/*		$t->define_pageselector(array(
			"type" => "text",
			"records_per_page" =>  100,
			"d_row_cnt" =>  sizeof($members),
			"position" => "both",
		));
*/
		if(sizeof($members) > 100)
		{
			$t->d_row_cnt = sizeof($members);
			$t->records_per_page = 100;
			$t->pageselector = "lbtxt";
			$t->set_header($t->draw_lb_pageselector());
		}
		else
		{
			$t->set_header("1 - ".sizeof($members));
		}
//		$t->d_row_cnt = sizeof($members);arr($t->d_row_cnt);
//		$t->set_header($t->draw_text_pageselector(array(
//			"records_per_page" => $perpage,
//		)));
		$t->sort_by();
	}

	function _set_member_search_table($arr)
	{
		foreach($arr["request"]["ignore_members_count"] as $source => $members)
		{
			$source = obj($source);
			$ignore_list = $source->meta("mail_ignore_list");
			foreach($members as $id => $val)
			{
				if($arr["request"]["ignore_members"][$source->id()][$id])
				{
					$ignore_list[$id] = $val;
				}
				elseif(isset($ignore_list[$id]))
				{
					unset($ignore_list[$id]);
				}
			}
			$source->set_meta("mail_ignore_list" , $ignore_list);
			$source->save();//ei saa aru miks ta kurat peale salvestamist 2ra kaob
		}
	}

	function _set_sources_data_table($arr)
	{
		$arr["obj_inst"]->set_sources_data($arr["request"]["sources_data"]);
	}

	function _get_sources_data_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
		));
		$sources = $arr["obj_inst"]->get_sources();
		$categorys = $menus = 0;
		$sources_data = $arr["obj_inst"]->get_sources_data();
		foreach($sources->arr() as $source)
		{
			$source_data = array();
			$source_data["id"] = $source->id();
			$source_data["name"] = $source->name();
			$source_data["type"] = $source->class_id();
			if($source->class_id() == CL_CRM_CATEGORY)
			{
				$categorys = 1;
				$usable_data_options = array(
					"orgs" => t("Organisatsioonid"),
					"pers" => t("Isikud"),
					"work" => t("T&ouml;&ouml;tajad"),
				);
				$source_data["usable_data"] = html::select(array(
					"name" => "sources_data[".$source->id()."][usable_data]",
					"multiple" => 1,
					"options" => $usable_data_options,
					"value" => is_array($sources_data) ? $sources_data[$source->id()]["usable_data"] : "",
				));
			}
			if($source->class_id() == CL_MENU)
			{
				$menus = 1;
				$source_data["use_minions"] = html::checkbox(array(
					"name" => "sources_data[".$source->id()."][use_minions]",
					"value" => 1,
					"checked" => isset($sources_data[$source->id()]["use_minions"]) && $sources_data[$source->id()]["use_minions"] ? 1:0,
				));
			}
			$t->define_data($source_data);
		}

		if($categorys)
		{
			$t->define_field(array(
				"name" => "usable_data",
				"caption" => t("Kasuta"),
			));
		}

		if($menus)
		{
			$t->define_field(array(
				"name" => "use_minions",
				"caption" => t("Kasuta ka alamkaustu"),
			));
		}
	}

	/**
		@attrib name=send_subscription_mail params=name

		@param ml_list required type=int acl=view
			The OID of the mailing list object to take the mail template from.
		@param dir required type=array/oid acl=view
			The OID or array of OIDs of the directories joined.
		@param to_mail required type=string
			The e-mail address to send the mail to.
		@param froma optional type=string
			The e-mail address the mail is sent from.
		@param fromn optional type=string
			The name of the sender.

		@returns False if the subscribe_mail property of given mailing list object is not set, otherwise returns true.
	**/
	public function send_subscription_mail($arr)
	{
		$o = obj($arr["ml_list"]);
		if(!is_oid($o->subscribe_mail) || !$this->can("view", $o->subscribe_mail))
		{
			return false;
		}

		$m = obj($o->subscribe_mail);

		$dirs = is_oid($arr["dir"]) ? (array)$arr["dir"] : safe_array($arr["dir"]);
		$this->do_send_un_subscribe_mail($m, $arr["to_mail"], $dirs, $arr);
		return true;
	}

	/**
		@attrib name=send_unsubscription_mail params=name

		@param ml_list required type=int acl=view
			The OID of the mailing list object to take the mail template from.
		@param dir required type=array/oid acl=view
			The OID or array of OIDs of the directories joined.
		@param to_mail required type=string
			The e-mail address to send the mail to.
		@param froma optional type=string
			The e-mail address the mail is sent from.
		@param fromn optional type=string
			The name of the sender.

		@returns False if the unsubscribe_mail property of given mailing list object is not set, otherwise returns true.
	**/
	public function send_unsubscription_mail($arr)
	{
		$o = obj($arr["ml_list"]);
		if(!is_oid($o->unsubscribe_mail) || !$this->can("view", $o->unsubscribe_mail))
		{
			return false;
		}

		$m = obj($o->unsubscribe_mail);

		$dirs = is_oid($arr["dir"]) ? (array)$arr["dir"] : safe_array($arr["dir"]);
		$this->do_send_un_subscribe_mail($m, $arr["to_mail"], $dirs, $arr);
		return true;
	}

	private function do_send_un_subscribe_mail($m, $to_mail, $dirs, $arr)
	{
		$inst = get_instance("protocols/mail/aw_mail");
		$t = get_instance("aw_template");

		$t->use_template($m->content);
		$LIST = "";
		if(count($dirs) > 0)
		{
			$dir_names_ol = new object_list(array(
				"oid" => $dirs,
				"lang_id" => array(),
				"site_id" => array(),
			));
			$dir_names = $dir_names_ol->names();
			foreach($dir_names as $dir_name)
			{
				$t->vars(array(
					"list_name" => $dir_name,
				));
				$LIST .= $t->parse("LIST");
			}
		}
		$t->vars(array(
			"LIST" => $LIST,
		));
		$message = $t->parse();
		$subject = $m->subject;
		$froma = isset($arr["froma"]) ? $arr["froma"] : "";
		$fromn = isset($arr["fromn"]) ? $arr["fromn"] : "";

		$inst->set_header("Content-Type","text/plain; charset=\"".aw_global_get("charset")."\"");
		if($m->is_html)
		{
			$inst->create_message(array(
				"froma" => $froma,
				"fromn" => $fromn,
				"subject" => $subject,
				"to" => $to_mail,
				"body" => t("Kahjuks sinu kirjalugeja ei oska kuvada HTML-formaadis kirju."),
			));
			$inst->htmlbodyattach(array(
				"data" => $message,
			));
		}
		else
		{
			$inst->create_message(array(
				"froma" => $froma,
				"fromn" => $fromn,
				"subject" => $subject,
				"to" => $to_mail,
				"body" => $message,
			));
		}
		$inst->gen_mail();
	}

}
?>
