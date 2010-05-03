<?php

namespace automatweb;
// messenger_v2.aw - Messenger V2
/*
HANDLE_MESSAGE(MSG_USER_LOGIN, on_user_login)

@classinfo syslog_type=ST_MESSENGER relationmgr=yes maintainer=tarvo

@default table=objects
@default group=settings

@property name type=textbox
@caption Nimi

@property status type=status
@caption Staatus

@default field=meta
@default method=serialize

property identity type=relpicker reltype=RELTYPE_MAIL_IDENTITY
caption Identiteet

@property fromname type=relpicker reltype=RELTYPE_FROMNAME
@caption Kellelt

@property config type=relpicker reltype=RELTYPE_MAIL_CONFIG
@caption Konfiguratsioon

@property msg_outbox type=relpicker reltype=RELTYPE_FOLDER
@caption Saadetud kirjad

@property msg_drafts type=relpicker reltype=RELTYPE_FOLDER
@caption Mustandite kataloog

@property page_reload type=textbox size=5
@caption Kirjade v&auml;rskendamine (sekundites)

@property mail_rte type=chooser
@caption Kirjade RTE

@property num_attachments type=select field=meta method=serialize group=advanced default=1
@caption Manuste arv

@property contact_text type=textarea cols=60 rows=5 group=advanced
@caption Kontaktinfo

@property grouping type=checkbox ch_value=1 default=0 field=meta method=serialize group=grouping
@caption Kirjade ajaline grupeerimine

@groupinfo main_view caption="Kirjad" submit=no
@default group=main_view

	@property mail_toolbar type=toolbar no_caption=1 store=no
	@caption Msg. toolbar

	@layout pane_split type=hbox width=10%:70%:20%

		@layout left_pane type=vbox parent=pane_split
			@layout msg_tree type=vbox parent=left_pane closeable=1 area_caption=Kaustad
				@property treeview type=text parent=msg_tree no_caption=1
				@caption Folderid


		@layout middle_pane type=vbox parent=pane_split
// lets make some wierd layouts here.. haha

			@layout msg_contents type=vbox parent=middle_pane closeable=1 area_caption=Kiri
				@property message_contents type=text no_caption=1 store=no parent=msg_contents


			@layout msg_list type=vbox parent=middle_pane
				@property message_list type=table no_caption=1 parent=msg_list no_caption=1
				@caption Kirjad

			@layout msg_new type=vbox parent=middle_pane closeable=1 area_caption=Kirja&nbsp;sisu
				@property new_mail_toolbar type=toolbar parent=msg_new submit=no no_caption=1
				@caption uue maili tuulbar

				@property message_info type=releditor reltype=RELTYPE_MAIL_MESSAGE parent=msg_new props=mfrom,mto,cc,name,add_contacts,message,msgrid
				@caption Maili andmed

			@layout msg_new2 type=vbox parent=middle_pane closeable=1 area_caption=Uus&nbsp;kiri
				@property msg_new2_from type=select parent=msg_new2 store=no
				@caption Kellelt

				@property msg_new2_to type=textbox parent=msg_new2 store=no
				@caption Kellele

				@property msg_new2_cc type=textbox parent=msg_new2 store=no
				@caption Koopia

				@property msg_new2_subject type=textbox parent=msg_new2 store=no
				@caption Teema

				@property msg_new2_msg type=textarea parent=msg_new2 store=no
				@caption Sisu

			@layout msg_search type=vbox parent=middle_pane closeable=1 area_caption=Otsing
				@property tmp type=text no_caption=1 store=no parent=msg_search

		@layout right_pane type=vbox parent=pane_split

			@layout msg_address_book type=vbox parent=right_pane closeable=1 area_caption=Aadressiraamat
				@property msg_ab_contents type=text no_caption=1 store=no parent=msg_address_book
				@caption Aadressiraamat

			@layout msg_calendar type=vbox parent=right_pane closeable=1 area_caption=Kalender
				@property msg_calendar type=text parent=msg_calendar no_caption=1
				@caption Kalender

@default group=search

@property s_toolbar type=toolbar no_caption=1
@caption Otsingu toolbar

@property s_from type=textbox store=no
@caption From

@property s_subject type=textbox store=no
@caption Subject

@property s_submit type=submit
@caption Otsi

@property no_reforb type=hidden value=1
@caption lolo

@property s_results type=table no_caption=1
@caption Tulemused

@property imap type=releditor reltype=RELTYPE_MAIL_SOURCE rel_id=first props=server,port,user,password,use_ssl group=imap
@caption IMAP

@default group=rules_settings

@property testfilters type=text
@caption Testi filtreid

@property rule_editor type=releditor reltype=RELTYPE_RULE mode=manager group=rules_editor table_fields=id,rule_from,rule_subject props=rule_from,rule_subject,target_folder,on_server
@caption Reeglid

@property add_rule type=releditor reltype=RELTYPE_RULE group=rules_add table_fields=id,rule_from,rule_subject props=rule_from,rule_subject,target_folder,on_server
@caption Lisa reegel

@property contact_lists type=select group=book_add store=no
@caption Aadressiraamat

@property contact_name type=textbox group=book_add store=no
@caption Nimi

@property contact_mail type=textbox group=book_add store=no
@caption E-Mail

@property contact_list type=table group=book_view no_caption=1
@caption Aadressiraamat

@groupinfo settings caption="Seaded" parent=general
@groupinfo advanced caption="Lisaks" parent=general
@groupinfo grouping caption="Grupeerimine" parent=general
@groupinfo imap caption="IMAP" parent=general
@groupinfo search caption=Otsing submit=no submit_action=change submit_method=GET
@groupinfo rules caption=Reeglid submit=no
@groupinfo rules_add caption="Lisa reegel" parent=rules
@groupinfo rules_editor caption="Reeglid" submit=no parent=rules
@groupinfo rules_settings caption="Seaded"
@groupinfo a_book caption="Aadressiraamat" submit=no
@groupinfo book_add caption="Lisa" parent=a_book
@groupinfo book_view caption="Aadressid" parent=a_book submit=no

@reltype MAIL_IDENTITY value=1 clid=CL_MESSENGER_IDENTITY
@caption messengeri identiteet

@reltype MAIL_SOURCE value=2 clid=CL_PROTO_IMAP
@caption mailikonto

@reltype MAIL_CONFIG value=3 clid=CL_MESSENGER_CONFIG
@caption messengeri konfiguratsioon

@reltype FOLDER value=4 clid=CL_MENU
@caption kataloog

@reltype ADDRESS value=5 clid=CL_ML_LIST
@caption adressaat

@reltype RULE value=6 clid=CL_MAIL_RULE
@caption maili ruul

@reltype MESSENGER_OWNERSHIP value=7 clid=CL_USER
@caption Omanik

@reltype FROMNAME value=8 clid=CL_ML_MEMBER
@caption Saatja

@reltype BUGTRACKER value=9 clid=CL_BUG_TRACKER
@caption Bugtrack

@reltype CONTACT_LIST value=10 clid=CL_CONTACT_LIST
@caption Aadressiraamat

@reltype MAIL_MESSAGE value=11 clid=CL_MESSAGE
@caption Maili andmed

*/

class messenger_v2 extends class_base
{
	const AW_CLID = 227;

	function messenger_v2()
	{
		$this->init(array(
			"tpldir" => "applications/messenger",
			"clid" => CL_MESSENGER_V2
		));
		$this->connected = false;
		$this->outbox = "INBOX.Sent-mail";
	}

	/**
		@attrib name=my_messages params=name is_public="1" caption="Minu kirjad" all_args="1"
		@returns
		@comment
	**/
	function my_messages($arr)
	{
		$msgr_id = $this->get_messenger_for_user();
		if (empty($msgr_id))
		{
			return t("kulla mees, sa pole omale default messengeri ju valinud?");
		};
		$arr["id"] = $msgr_id;
		$arr["group"] = "main_view";
		$arr["action"] = "change";
		return $this->change($arr);


	}

	/** called by aw message
		@attrib params=name
		@param uid required type=string
		@comment
		gets called on user login and checks if user has messenger
	**/
	function on_user_login($arr = array())
	{
		$tmp = $this->get_messenger_for_user($arr)?$this->get_messenger_for_user($arr):"false";
		$_SESSION["current_user_has_messenger"] = $tmp;
	}

	/** returns messenger oid for user
	**/
	function get_messenger_for_user($arr = array())
	{
		$uid = $arr["uid"];
		if (empty($uid))
		{
			$uid = aw_global_get("uid");
		};
		$users = new users();
		$user = new object($users->get_oid_for_uid($uid));

		$conns = $user->connections_to(array(
			"type" => "RELTYPE_MESSENGER_OWNERSHIP",
			"from.class_id" => CL_MESSENGER_V2,
		));
		if (sizeof($conns) == 0)
		{
			return false;
		};
		list(,$conn) = each($conns);
		$obj_id = $conn->prop("from");
		return $obj_id;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			// calendar
			case "msg_calendar":
				$prop["value"] = "kalender";
				break;
			// address book
			case "msg_ab_contents":
				$cl = new contact_list();
				$cls = array();
				$cls = $cl->get_contact_lists_for_messenger($arr["obj_inst"]->id());
				$adds = $cl->get_addresses($cls);
				foreach($adds as $add)
				{
					$obj = new object($k);
					$add["name"] = split("[ ]", $add["name"]);
					foreach($add["name"] as $k => $name)
					{
						$add["name"][$k] = ucfirst(strtolower($name));
					}
					$prop["value"] .= join(" ", $add["name"])."&nbsp;&nbsp;(".$add["mail"].")<br/>";
				}
				break;
			// others
			case "num_attachments":
				$prop["options"] = array(
					0 => 0,
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
				);
				break;

			case "message_list":
				$retval = $this->gen_message_list($arr);
				break;

			case "mail_toolbar":
				$prop["value"] = $this->gen_mail_toolbar($arr);
				break;

			case "treeview":
				$prop["value"] = $this->make_folder_tree($arr);
				break;

			case "mailbox":
				$prop["value"] = $this->use_mailbox;
				break;

			case "autofilter_delay":
				$prop["options"] = array("0" => "--","3" => "3","5" => "5","10" => "10");
				break;

			case "rule_editor":
				break;
			case "rule_view":
				break;
			case "target_folder":
				$this->_connect_server(array("msgr_id" => $arr["obj_inst"]->id()));
				$flds = $this->drv_inst->list_folders();
				foreach($flds as $fld)
				{
					$options[$fld["fullname"]] = $fld["name"];
				}
				$prop["options"] = $options;
				break;

			case "testfilters":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("test_filters",array("id" => $arr["obj_inst"]->id())),
					"caption" => $prop["caption"],
				));
				break;

			case "currentfolder":
				$prop["value"] = $this->use_mailbox;
				break;

			case "s_from":
				$prop["value"] = $arr["request"]["s_from"];
				break;

			case "s_subject":
				$prop["value"] = $arr["request"]["s_subject"];
				break;

			case "s_results":
				$this->do_search($arr);
				break;

			case "s_toolbar":
				$t = &$prop["toolbar"];
				$t->add_button(array(
					"name" => "delete",
					"img" => "delete.gif",
					"confirm" => t("Kustutada?"),
					"tooltip" => t("Kustuta m&auml;rgitud kirjad"),
					"action" => "delete_search_results",
				));
				break;
			case "contact_list":
				$prop["vcl_inst"]->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
				));
				$arr["prop"]["vcl_inst"]->define_field(array(
					"name" => "email",
					"caption" => t("E-Mail"),
				));

				$cl = new contact_list();
				$cls = $cl->get_contact_lists_for_messenger($arr["obj_inst"]->id());
				if(!$cls)
				{
					$obj = new object();
					$obj->set_class_id(CL_CONTACT_LIST);
					$obj->set_name(aw_global_get("uid").".aadressiraamat");
					$obj->set_parent($arr["obj_inst"]->id());
					$cls = array($obj->save_new());
					$arr["obj_inst"]->connect(array(
						"type" => "RELTYPE_CONTACT_LIST",
						"to" => $obj->id(),
					));
				}

				$adds = $cl->get_addresses($cls);
				foreach($adds as $add)
				{
					$obj = new object($k);
					$arr["prop"]["vcl_inst"]->define_data(array(
						"name" => $add["name"],
						"email" => $add["mail"],
					));
				}

				break;
			case "contact_lists":
				$cl = new contact_list();
				$cls = $cl->get_contact_lists_for_messenger($arr["obj_inst"]->id());
				if(!$cls)
				{
					$obj = new object();
					$obj->set_class_id(CL_CONTACT_LIST);
					$obj->set_name(aw_global_get("uid").".aadressiraamat");
					$obj->set_parent($arr["obj_inst"]->id());
					$cls = array($obj->save_new());
				}

				foreach($cls as $cl)
				{
					$obj = new object($cl);
					$cl_names[$obj->id()] = $obj->name();

				}
				$prop["options"] = $cl_names;
				break;
			case "new_mail_toolbar":
				// gen sendmail toolbar
				$prop["vcl_inst"]->add_button(array(
					"name" => "send_mail",
					"tooltip" => t("Saada kiri"),
					"img" => "mail_send.gif",
					"onClick" => "msgr_sendmail();",
					"url" => "#",
				));
				break;

			/* mail view_group */
			case "message_contents":
				// generating toolbar
				$tb = new toolbar();

				if(!strlen($arr["request"]["msgid"]))
				{
					$enum = aw_global_get("table_enum");
					$arr["request"]["msgid"] = $enum[0];
				}
				if(!strlen($arr["request"]["mailbox"]))
				{
					$arr["request"]["mailbox"] = "INBOX";
				}
				$move = $this->_msg_move($arr);
				$url = $this->mk_my_orb("change", array(
					"group" => $arr["request"]["group"],
					"msgrid" => $arr["request"]["msgrid"],
					"mailbox" => $arr["request"]["mailbox"],
					"id" => $arr["request"]["id"],
				));
				$tb->add_button(array(
					"name" => "next",
					"tooltip" => t("J&auml;rgmine kiri"),
					"img" => "up_r_arr.png",
					"url" => "javascript:void();",
					"onClick" => "javascript:msgr_load('middle_pane_outer', 'msg_contents', true, '".$url."&msgid=".$move["next"]."');",
				));
				$tb->add_cdata(sprintf(t("%s of %s"), $move["cur"], $move["total"]));
				$tb->add_button(array(
					"name" => "prev",
					"tooltip" => t("Eelmine kiri"),
					"img" => "down_r_arr.png",
					"url" => "#",
					"onClick" => "javascipt:msgr_load('middle_pane_outer', 'msg_contents', true, '".$url."&msgid=".$move["prev"]."');",
				));
				$cur = get_current_company();
				$tb->add_button(array(
					"name" => "save_to_mf",
					"tooltip" => t("Salvesta"),
					"img" => "save.gif",
					"url" => $this->mk_my_orb("new", array(
						"msgrid" => $arr["request"]["msgrid"],
						"mailbox" => $arr["request"]["mailbox"],
						"msgid" => $arr["request"]["msgid"],
						"parent" => $cur->id(),
						"save" => "mf",
					),CL_MESSAGE),
				));
				$tb_html = $tb->get_toolbar();
				$ms_html = $this->_fetch_message_contents($arr);
				$prop["value"] = $this->parse();
				$prop["value"] = $tb_html.$ms_html;
				break;

			case "mail_view_tree":
				$prop["value"] = $this->make_folder_tree($arr);
				break;

			case "fromname":
				$name = $arr["obj_inst"]->prop("fromname.name");
				$prop["options"][$prop["value"]] = $name;
				break;

			case "msg_new2_from":
				$prop["options"] = array($arr["obj_inst"]->prop("fromname.mail") => $arr["obj_inst"]->prop("fromname.name"));
				break;

			case "mail_rte":
				$prop["options"] = array(
					0 => t("Ei kuva"),
					2 => t("FCKeditor"),
				);

		};
		return $retval;
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "autofilter_delay":
				$this->schedule_filtering($arr);
				break;
		}
		return $retval;
	}

	/** sets default inbox if not set
	**/
	function callback_pre_edit($arr)
	{
		$mailbox = isset($arr["request"]["mailbox"]) ? $arr["request"]["mailbox"] : "INBOX";
		$name = $arr["obj_inst"]->name();
		aw_global_set("title_action",$name);
		$this->use_mailbox = $mailbox;
		/*
		print "preedit handler<br>";
		print "this is where we can validate data for messenger $id<br>";
		*/


	}

	/**
		@attrib name=_catch_mail_send all_args=1
	**/
	function _catch_mail_send($arg)
	{
		// uh, see on siin selleks, et miksip2rast ajax post requestist tulnud asjad siia $arg'i ei j6udnud. who knows why...
		$arg = array_merge($arg, $_POST);
		// drafti tegemise peab siia t&otilde;stma.. muidu tehakse iga kord uue maili tab'iga uus m&otilde;tetu draft?!?
		$mm = new message();
		$arg["message_info"]["msgrid"] = $arg["id"];
		$arg["msgid"] = $mm->create_draft(array(
			"msgrid" => $arg["message_info"]["msgrid"],
		));
		// this sucks.. but right now, with this heat.. i can't do any better
		$msgobj = obj($arg["msgid"]);
		$msgobj->set_prop("mfrom", mb_convert_encoding($arg["message_info"]["mfrom"], "ISO-8859-1", "UTF-8"));
		$msgobj->set_prop("add_contacts", mb_convert_encoding($arg["message_info"]["add_contacts"], "ISO-8859-1", "UTF-8"));
		$msgobj->set_prop("mto", mb_convert_encoding($arg["message_info"]["mto"], "ISO-8859-1", "UTF-8"));
		$msgobj->set_prop("cc", mb_convert_encoding($arg["message_info"]["cc"], "ISO-8859-1", "UTF-8"));
		$msgobj->set_prop("name", mb_convert_encoding($arg["message_info"]["name"], "ISO-8859-1", "UTF-8"));
		$msgobj->set_prop("message", mb_convert_encoding($arg["message"], "ISO-8859-1", "UTF-8"));
		$msgobj->set_name(mb_convert_encoding($arg["message_info"]["name"], "ISO-8859-1", "UTF-8"));
		$msgobj->save();

		$arg = array_merge($arg, $arg["message_info"]);
		$arg["msgrid"] = $arg["message_info"]["msgrid"];
		$mm->mail_send(
			$arg
		);
		// well.. this thingie goes thru ajax now, so we dont need to return url
	}

	/** fetches message contents for mail_view
	**/
	function _fetch_message_contents(&$arg = array())
	{
		if($arg["request"]["msgid"] && $arg["request"]["mailbox"])
		{
			$inst = new message();
			$inst->msgr = $this;
			$ret = $inst->formatted_message(array(
				"msgid" => $arg["request"]["msgid"],
				"msgrid" => $arg["request"]["id"],
				"mailbox" => $arg["request"]["mailbox"]
			));
		}
		return  $ret;
	}

	function callback_mod_layout($arr)
	{
		$layouts_to_be_ignored = array("msg_contents", "msg_address_book", /*"msg_tree", "msg_list",*/ "msg_new", "msg_new2", "msg_search", "msg_calendar");
		foreach($layouts_to_be_ignored as $layout)
		{
			if($arr["name"] == $layout && $arr["request"]["view_layout"] != $layout)
			{
				return false;
			}
		}
		return true;
	}

	function callback_generate_scripts($arr)
	{
		load_javascript("autocomplete.js");
		load_javascript("autocomplete_lib.js");

		$sendmail_url = $this->mk_my_orb("_catch_mail_send", array(
			"id" => $arr["obj_inst"]->id(),
		));
		$v1 = "Palun oodake kuni soovitud andmeid laetakse";
		$v2 = "<img src=\"".aw_ini_get("baseurl")."/automatweb/images/LoadingClock.gif\">";
		$new_pane_url = $this->mk_my_orb("change", $arr["request"]);
		$js = "
			// this holds loading message
			//temp_layout = '<div id=\"vbox\"><div class=\"pais\"><div class=\"caption\">Laadin ...</div><div class=\"closer\"></div></div><div class=\"sisu3\">".$v2."</div><div>';
			temp_layout = '<div id=\"vbox\" style=\"margin:50 auto; position: absolute; width: 200px; height: 100px;\">".$v2."<div>';
			ajax_loader_div = '<div style=\"padding:10px 20px; width:200px; left: 50%; margin-left: -100px; height:200; top:50%; margin-top:-100; background-color:white; border:1px solid silver; position:absolute; text-align:center; color:gray; font-size:12px; display:none;\" id=\"ajax_loader_div\"><img src=\"".aw_ini_get("baseurl")."/automatweb/images/ajax-loader.gif\"><br/><br/>Laadin...</div>';
			$(\"body\").append(ajax_loader_div);

			//
			loading = false;
			// info about layouts. in which area, which layout lies
			layouts = new Array();
			layouts['left_pane_outer'] = 'msg_tree';

			//
			ourl = '".$new_pane_url."';
			msgr = ".(($arr["request"]["group"] == "main_view")?'true':'false').";
			// triggers messenger initializer
			// holds active folder
			var active_folder = 'INBOX';
			// to where the layout is being currently loaded
			var async_location = false;
			// holds last loaded layouts name
			last_loaded_layout = false;

			// sends mail & switches new mail form with mailbox list
			function msgr_sendmail()
			{
				show_ajax_loader();
				surl = '".$sendmail_url."';
				//document.getElementById('right_pane_outer').display = 'none';
				params = '&message_info[mfrom]=' + Url.encode(document.getElementById('message_info[mfrom]').value) +'&message_info[add_contacts]=' + Url.encode(document.getElementById('message_info[add_contacts]').value) + '&message_info[mto]=' + Url.encode(document.getElementById('message_info_mto_').value) + '&message_info[cc]=' + Url.encode(document.getElementById('message_info_cc_').value) + '&message_info[name]=' + Url.encode(document.getElementById('message_info_name_').value) + '&message=' + Url.encode(document.getElementById('message').value);
				aw_post_url_contents(surl, params);
				msgr_load('middle_pane_outer', 'msg_list', false, ourl);

			}

			// loads given folder contents to right pane
			function msgr_show_folder(folder, page)
			{
				if(!page)
				{
					page = 0;
				}
				msgr_load('middle_pane_outer', 'msg_list', false, ourl + '&mailbox=' + folder + '&ft_page=' + page);
				active_folder = folder;
			}

			function msgr_collect_checkboxes(elem)
			{
				var retval = new String();
				for(i=0; i < elem.length; i++){
					if(elem[i].type == 'checkbox' && elem[i].checked){
						retval = retval + '&'+ elem[i].name + '=' +elem[i].value;
					}
				}
				return retval;
			}


			function _msgr_handle_loaded()
			{
				if(async_location)
				{
					add = last_loaded_type?document.getElementById(async_location).innerHTML:'';
					document.getElementById(async_location).innerHTML = (last_loaded_type=='PRE')?(req.responseText + add):(req.responseText + add);
					hide_ajax_loader();
					async_location = false;
					$.getScript('/automatweb/js/fckeditor/2.6.3/fckeditor.js', function(){
						_msgr_load_fck('message');
					});
					_msgr_load_autocomplete();
					//f('Loading messenger layout '+ last_loaded_layout +' (' + aw_timer('msgr::load_layout') + 'ms)');
				}
			}

			function _msgr_load_fck(name)
			{
				var oFCKeditor = new FCKeditor(name);
				oFCKeditor.BasePath = '/automatweb/js/fckeditor/2.6.3/';
				oFCKeditor.Width = '600px';
				oFCKeditor.Height = '500px';
				oFCKeditor.Config['AutoDetectLanguage'] = false;
				oFCKeditor.Config['DefaultLanguage'] = 'et';
				oFCKeditor.ReplaceTextarea();
				oFCKeditor.Config['CustomConfigurationsPath'] = '/automatweb/orb.aw?class=fck_editor&action=get_fck_config' + ( new Date() * 1 ) ;
			}

			function _msgr_load_autocomplete(name)
			{
				autocomplete = \"var awAc_message_info_mto_ = new awActb(document.getElementsByName('message_info[mto]')[0]);\";
				autocomplete += \"awAc_message_info_mto_.actb_setOptionUrl('http://robert.dev.struktuur.ee/automatweb/orb.aw?class=mail_message&action=get_autocomplete');\";
				autocomplete += \"awAc_message_info_mto_.actb_setParams(new Array ('message_info[mto]'));\";
				eval(autocomplete);
			}

			function msgr_close(subject)
			{
				el=document.getElementById(subject);
				im=document.getElementById(subject + '_closer_img');
				if (el.style.display == 'none')
				{
					el.style.display = 'block';
					im.src = '/automatweb/images/aw06/closer_up.gif';	aw_get_url_contents('/automatweb/orb.aw?class=user&action=open_layer&u_class=messenger_v2&u_group=main_view&u_layout=' + subject);
				}
				else
				{
					el.style.display = 'none';
					im.src = '/automatweb/images/aw06/closer_down.gif';	aw_get_url_contents('/automatweb/orb.aw?class=user&action=close_layer&u_class=messenger_v2&u_group=main_view&u_layout=' + subject);
				}
			}

			function msgr_load(to, content, add, ovr_url)
			{
				//aw_timer('msgr::load_layout');
				last_loaded_layout = content;
				last_loaded_type = add;
				from_url = ((ovr_url)?ovr_url:ourl) + '&view_layout=' + content;
				if(add)
				{
					element = (document.getElementById(content + '_outer') == null)?false:document.getElementById(content + '_outer');
					if(layouts[content] && element.parentNode && element.parentNode.removeChild)
					{
						element.parentNode.removeChild(element);
					}
					async_location = to;
					aw_do_xmlhttprequest(from_url, _msgr_handle_loaded);
					show_ajax_loader();
					//document.getElementById(to).innerHTML = temp_layout;
					//document.getElementById(to).innerHTML = document.getElementById(to).innerHTML + aw_get_url_contents(from_url);
				}
				else
				{
					for(x in layouts)
					{
						if(layouts[x] == content)
						{
							layouts[x] = false;
						}
					}
					async_location = to;
					aw_do_xmlhttprequest(from_url, _msgr_handle_loaded);
					show_ajax_loader();
					//document.getElementById(to).innerHTML = temp_layout;
					//document.getElementById(to).innerHTML = aw_get_url_contents(from_url);
				}
				layouts[content] = to;
			}

			function show_ajax_loader()
			{
				document.getElementById('ajax_loader_div').style.display = 'block';
			}
			function hide_ajax_loader()
			{
				document.getElementById('ajax_loader_div').style.display = 'none';
			}

			function msgr_onLoad()
			{
				if(msgr)
				{
					//msgr_load('left_pane_outer', ourl + '&view_layout=msg_tree');
					//msgr_load('left_pane_outer', 'msg_address_book', true);
					msgr_show_folder(active_folder);
				}
			}

			//document.onLoad = msgr_onLoad();
		";
		return $js;
	}

	/** saves for example address book values
	**/
	function callback_pre_save($arr)
	{
		$request = $arr["request"];
		if(strlen($request["contact_mail"]) && strlen($request["contact_lists"]))
		{
			$mail = new object();
			$mail->set_parent($request["contact_lists"]);
			$mail->set_class_id(CL_ML_MEMBER);
			$mail->set_prop("name", $request["contact_name"]);
			$mail->set_prop("mail", $request["contact_mail"]);
			$mail->save_new();
		}
	}

	function _connect_server($arr)
	{

		if (!$this->connected || $arr["force_reconnect"])
		{
			global $awt;

			if (!extension_loaded("imap"))
			{
				$this->connect_errors = t("IMAP extension not available");
				return false;
			};
			$this->msgobj = new object($arr["msgr_id"]);
			$conns = $this->msgobj->connections_from(array("type" => "RELTYPE_MAIL_SOURCE"));


			// right now it only deals with a single server.
			list(,$_sdat) = each($conns);
			//$_sdat =$conns[0];
			if (empty($_sdat))
			{
				$this->connect_errors = t("IMAP sissep&auml;&auml;s on konfigureerimata");
				return false;
			};
			$sdat = new object($_sdat->to());

			$this->_name = $sdat->prop("name");

			$this->drv_inst = new imap();
			$this->drv_inst->set_opt("use_mailbox",$this->use_mailbox);
			$this->drv_inst->set_opt("outbox",$this->outbox);
			$awt->start("imap-server-connect");
			$errors = $this->drv_inst->connect_server(array(
				"obj_inst" => $_sdat->to(),
				"arr" => $arr,
			));
			if ($errors)
			{
				$this->connect_errors = $errors;
				return false;
			}
			$awt->stop("imap-server-connect");
			$this->drv_inst->set_opt("messenger_id",$arr["msgr_id"]);

			$this->mbox = $this->drv_inst->get_opt("mbox");
			$this->servspec = $this->drv_inst->get_opt("servspec");
			$this->mboxspec = $this->drv_inst->get_opt("mboxspec");

			$this->connected = true;

			$msg_cfg = $this->msgobj->prop("config");
			if (!empty($msg_cfg))
			{
				$msg_cfg_obj = new object($msg_cfg);
				$this->perpage = $msg_cfg_obj->prop("msgs_on_page");
			};
			$awt->start("imap-list-folders");
			if (!$arr["no_folders"])
			{
				$this->mailboxlist = $this->drv_inst->list_folders();
			};
			$awt->stop("imap-list-folders");
		};
		return true;
	}


	/** locates next/prev msg id's for mail_view plus current and total
	**/
	function _msg_move($arg)
	{
		$enum = aw_unserialize(cache::file_get($this->drv_inst->mbox_msg_list_cache_id));
		$key = array_search($arg["request"]["msgid"], $enum);
		if($key !== false)
		{
			$move["next"] = ($key==0)?$enum[$key]:$enum[$key-1];
			$move["prev"] = (($key+1) == count($enum))?$enum[$key]:$enum[$key+1];
			$move["cur"] = $key+1;
			$move["total"] = count($enum);
		}
		else
		{
			$move["next"] = $arg["request"]["msgid"];
			$move["prev"] = $arg["request"]["msgid"];
		}
		return $move;
	}

	/** defines fields for main_view table (gen_message_list uses it)
	**/
	function _mk_mb_table(&$t, $obj)
	{
		$t->define_field(array(
			"name" => "answered",
			"caption" => t("Vast."),
			"talign" => "center",
			"align" => "center",
			"width" => 20,
			"nowrap" => 1,
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "attach",
			"caption" => t("A"),
			"talign" => "center",
			"align" => "center",
			"width" => 10,
			"nowrap" => 1,
		));
		$t->define_field(array(
			"name" => "from",
			"caption" => t("Kellelt"),
			"talign" => "center",
			"sortable" => 1,
			"nowrap" => 1,
		));
		$t->define_field(array(
			"name" => "subject",
			"caption" => t("Teema"),
			"talign" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"talign" => "center",
			"nowrap" => 1,
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "H:i d-M-Y",
			"smart" => "1",
		));
		$t->define_field(array(
			"name" => "size",
			"caption" => t("KB"),
			"talign" => "center",
			"nowrap" => 1,
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
		));
		if($obj->prop("grouping"))
		{
			$t->define_field(array(
				"name" => "time_group",
				"caption" => t("G"),
			));
		}
		$t->define_chooser(array(
			"name" => "mark",
			"field" => "id",
		));
	}

	/** generates message list table for main_view
	**/
	function gen_message_list(&$arr)
	{
		global $awt;
		$awt->start("msgr::gen_message_list/connect_server");
		$this->_connect_server(array(
			"msgr_id" => $arr["obj_inst"]->id(),
			"no_folders" => true,
			"gen_mess_list_srat" => "1",
		));
		$awt->stop("msgr::gen_message_list/connect_server");

		if ($this->connect_errors)
		{
			$arr["prop"]["error"] = t("Login failed, check whether server name, user and password are correct.<br>") . $this->connect_errors;
			return PROP_ERROR;
		};

		$perpage = empty($this->perpage) ? 25 : $this->perpage;

		$ft_page = (int)$arr["request"]["ft_page"];

		global $awt;
		$awt->start("msgr::gen_message_list/list-folder-contents");
		$awt->count("msgr::gen_message_list/list-folder-contents");
		$contents = $this->drv_inst->get_folder_contents(array(
			"to" => ($perpage * ($ft_page + 1)) - 1,
			"from" => $perpage * $ft_page,
			"from_filter" => $arr["request"]["quicksearch"],
		));
		$count = $this->drv_inst->folder_count(array(
			"use_mailbox" => $this->use_mailbox,
		));
		$t = &$arr["prop"]["vcl_inst"];

		if(key($count) > $this->perpage)
		{
			$pages = ceil(key($count) / $this->perpage);
			if($ft_page != 0 && key($count) > 1)
			{
				$ps[] = html::href(array(
					"caption" => "&lt;&lt;",
					"url" => "#",
				));
			}

			$nth = false;
			for($i = 0; $i < $pages ; $i++)
			{
				if(($i > 2 && $i+2 < $ft_page) || ($i < $pages - 2 && $i > $ft_page + 2))
				{
					$nth = true;
					continue;
				}
				else
				{
					if($nth)
					{
						$ps[] = "...";
					}
					$nth = false;

				}
				$ps[] = html::href(array(
					"caption" => ($ft_page == $i)?sprintf("<b>%s</b>", ($i + 1)): ($i + 1) ,
					"url" => "#",
					"onClick" => "javascript:msgr_show_folder(\"".$this->use_mailbox."\", ".$i.");",
				));
			}
			if($ft_page != key($count) - 1  && key($count) > 1)
			{
				$ps[] = html::href(array(
					"caption" => "&gt;&gt;",
					"url" => "#",
				));
			}
			$t->set_header("<div width=\"100%\" align=\"right\">".join("&nbsp;", $ps)."</div>");
		}
		$awt->stop("msgr::gen_message_list/list-folder-contents");

		$this->_mk_mb_table($t, $arr["obj_inst"]);


		$fldr = $this->use_mailbox;


		if($grouping = $arr["obj_inst"]->prop("grouping"))
		{
			// calculate some periods
			$today = mktime(0,0,0, date("m"), date("d"), date("Y"));
			$week_before = $today - (60*60*24*7);
			// end calc
			$t->set_rgroupby(array(
				"time_group" => "time_group",
			));
		}

		$awt->start("msgr::gen_message_list/loop-over-contents");
		foreach($contents as $key => $message)
		{
			if($grouping)
			{
				if($message["tstamp"] >= $today)
				{
					$period = "Today";
				}
				elseif($message["tstamp"] < $today && $message["tstamp"] >= $week_before)
				{
					$period = "Last&nbsp;week";
				}
				else
				{
					$period = "Old&nbsp;mails";
				}
			}
			$seen = $message["seen"];
			$fromline = "";
			if (!empty($message["fromn"]))
			{
				$fromline = html::href(array(
					"url" => "javascript:void();",
					"title" => $message["froma"],
					"caption" => substr($message["fromn"],0,1),
				)) . substr($message["fromn"], 1);
			}
			else
			{
				$fromline = $message["from"];
			};

			// this should be unique enough
			$wname = "msgr" . $key;

			$url = "#";
			$onClick = $this->mk_my_orb("change", array(
				"group" => "main_view",
				"msgrid" => $arr["obj_inst"]->id(),
				"id" => $arr["obj_inst"]->id(),
				"mailbox" => $this->use_mailbox,
				"msgid" => $key,
				"view_layout" => "msg_contents",
			));
			$new_link = "<a href=\"".$url."\" onClick=\"javascript:msgr_load('middle_pane_outer', 'msg_contents', 'PRE', '".$onClick."');\">".$this->_format(parse_obj_name($message["subject"]), $seen, 20)."</a>";

			$t->define_data(array(
				"time_group" => $period,
				"id" => $key,
				"from" => $this->_format($fromline, $seen),
				"subject" => $new_link,
				"date" => $message["tstamp"],
				"size" => $this->_format(sprintf("%d",$message["size"]/1024),$seen),
				"answered" => $this->_format($this->_conv_stat($message["answered"]),$seen),
				"attach" => $message["has_attachments"] ? html::img(array("url" => $this->cfg["baseurl"] . "/automatweb/images/attach.gif")) : "",
			));

		};
		$awt->stop("msgr::gen_message_list/loop-over-contents");


		$t->set_default_sortby("date");
		$t->set_default_sorder("desc");
		$t->set_final_enum();
		return PROP_OK;
	}


	function _format($str,$flag, $len = false)
	{
		$str = ($flag) ? $str : "<strong>$str</strong>";
		return $len?substr($str, 0, $len)."...":$str;
	}

	function _conv_stat($code)
	{
		return ($code == 0) ? t("ei") : t("jah");
	}

	////
	// !Returns full name from the address
	function _conv_addr($addr)
	{
		if (preg_match("/(.*)</",$addr,$m))
		{
			return $m[1];
		}
		else
		{
			return $addr;
		};
	}

	/**
		@attrib params=name
		@param mailbox
		mailbox name (INBOX.Sent, ..)
		@param messenger
		messenger id

		@comment
		counts unread/total number of messages for given folder. messenger id is given instead of imap stream.
	**/
	function count_fld_contents($arg)
	{
		if($arg["mailbox"] == "INBOX.Sent-mail")
			return $ret[1] = 4;
		$imap = new proto_imap();

		$back = $this->use_mailbox;
		$this->use_mailbox = $arg["mailbox"];
		$err = $this->_connect_server(array(
			"msgr_id" => $arg["messenger"],
			"force_reconnect" => true,
			"count_contents" => "1",
		));
		if($err)
		{
			return array();
		}
		$mboxinf = imap_mailboxmsginfo($this->mbox);

		$ret = array($mboxinf->Nmsgs => $mboxinf->Unread);
		if(trim($this->use_mailbox) != trim($back))
		{
			$this->use_mailbox = $back;
			$this->_connect_server(array(
				"msgr_id" => $arg["messenger"],
				"force_reconnect" => true,
				"backup" => "kax",
				"box" => $this->use_mailbox,
			));
		}
		return $ret;
	}

	/** generates mailbox tree
	**/
	function make_folder_tree($arr)
	{
		$conn = $this->_connect_server(array(
			"msgr_id" => $arr["obj_inst"]->id(),
			"make_fld_tree_start" => "1",
		));

		if (!$conn)
		{
			return false;
		};

		$rv = "";

		// I have to enumerate those mailboxes, because the current DHTML
		// trees uses names as unique identifiers for tree branches ..
		// having special characters in them breaks javascript syntax
		$enum = array();

		$tree = new treeview();
		$tree->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "msgr_tree", // what if there are multiple messengers?
			"persist_state" => 1,
			"js_bold_nodes" => 1,
		));

		$tree->add_item(0,array(
			"name" => "Local folders",
			"id" => "local_root",
		));

		$this->localfolders = array();
		$conns = $this->msgobj->connections_from(array("type" => "RELTYPE_FOLDER"));
		foreach($conns as $folder_item)
		{
			$sdat = new object($folder_item->to());
			$tree->add_item("local_root",array(
				"name" => $sdat->prop("name"),
				"id" => "local_".$sdat->id(),
				"url" => $this->mk_my_orb("change",array(
					"id" => $arr["obj_inst"]->id(),
					"group" => $arr["prop"]["group"],
					"localmailbox" => $sdat->id(),
				)),
			));
			$this->localfolders[$sdat->id()] = $sdat->prop("name");
		};

		$i = 0;

		foreach($this->mailboxlist as $key => $val)
		{
			$i++;
			$enum[$val["name"]] = $i;

			// kui mailboxi nimi ei sisalda punkti, siis on tegemist esimese taseme folderiga
			// tegelikult .. eraldaja m&auml;&auml;ratakse &auml;ra namespacega, ilmselt v&otilde;ib olla see ka
			// midagi muud kui punkt. aga praegu piisab punktist.
			if (strpos($val["name"],".") === false)
			{
				$parent = 0;
				$name = $val["name"];
			}
			else
			{
				$parent = $enum[substr($val["name"],0,strrpos($val["name"],"."))];
				$name = substr($val["name"],strrpos($val["name"],".")+1);
			};

			if ($val["name"] == $this->use_mailbox)
			{
				$name = "<strong>$name</strong>";
			};

			$count = $this->drv_inst->folder_count(array(
				"use_mailbox" => $val["fullname"],
			));
			$tree->add_item($parent,array(
				"name" => $name . " (".key($count)." / ".current($count).")",
				"id" => $i,
				"onClick" => "javascript:msgr_show_folder('".$val["name"]."');",
				"url" => "#"
			));

		}

		$res .= $tree->finalize_tree();

		$rv .= $res;
		return $rv;
	}

	/** generates main_view toolbar
	**/
	function gen_mail_toolbar($arr)
	{
		$rv = $this->_connect_server(array(
			"msgr_id" => $arr["obj_inst"]->id(),
		));

		if ($this->connect_errors)
		{
			$arr["prop"]["error"] = t("Login failed, check whether server name, user and password are correct.<br>") . $this->connect_errors;
			return PROP_ERROR;
		}

		$drafts = $this->msgobj->prop("msg_drafts");
		$toolbar = $arr["prop"]["vcl_inst"];

		$url = get_ru()."&view_layout=msg_new";
		$toolbar->add_button(array(
			"name" => "newmessage",
			"tooltip" => t("Uus kiri"),
			"url" => "#",
			"onClick" => "msgr_load('middle_pane_outer', '".$url."');",
			"img" => "new.gif"

		));

		$delurl = $this->mk_my_orb("delete_messages");
		$toolbar->add_button(array(
			"name" => "removemessage",
			"tooltip" => t("Kustuta valitud kirjad"),
			"url" => "#",
			"onClick" => "aw_post_url_contents('".$delurl."', '&mailbox='+active_folder+'&id=".$arr["obj_inst"]->id()."'+msgr_collect_checkboxes(document.changeform.elements));msgr_show_folder(active_folder);",
			"img" => "delete.gif"

		));


		$toolbar->add_separator();
		$toolbar->add_cdata("<div id=\"loading_progress\"></div>");
		$toolbar->add_separator();

		if ($rv == false)
		{
			return false;
		}
	}

	function parse_alias($args = array())
	{
		extract($args);
		return $this->show(array("id" => $alias["target"]));
	}

	function schedule_filtering($arr)
	{
		if ($arr["prop"]["value"] > 0)
		{
			$sched = new scheduler();
			$sched->add(array(
				"event" => $this->mk_my_orb("run_filters", array("id" => $arr["obj_inst"]->id()), "", false, true),
				"time" => time()+($arr["prop"]["value"] * 60),
			));
		};
	}

	/** called from ORB/scheduler, runs all the filter on INBOX

		@attrib name=run_filters params=name nologin="1"
		@param id required type=int
		@returns
		@comment

	**/
	function run_filters($arr)
	{
		$msgr_obj = new object($arr["id"]);
		if ($msgr_obj->prop("autofilter_delay") > 0)
		{
			$this->_connect_server(array(
				"msgr_id" => $arr["id"],
			));

			$this->preprocess_filters();

			$rv = $this->do_filters();

			print $rv;

			// reschedule
			$sched = new scheduler();
			$sched->add(array(
				"event" => $this->mk_my_orb("run_filters", array("id" => $arr["id"]), "", false, true),
				"time" => time()+($msgr_obj->prop("autofilter_delay") * 60),
			));

		};
		// stop processing, will ya?
		die();

	}

	/**

		@attrib name=test_filters params=name
		@param id required type=int
	**/
	function test_filters($arr)
	{
		$this->_connect_server(array(
			"msgr_id" => $arr["id"],
		));

		$this->preprocess_filters();

		$rv = $this->do_filters(array("dryrun" => 1));

		if ($this->done === false)
		{
			$rv = t("&uuml;kski kiri INBOXis ei matchinud &uuml;hegi ruuliga<br>");
		};
		return "<pre>" . $rv . "</pre>";

	}

	////
	// !Creates hash tables from connected filter objects to make any following
	// processing of messages easier.
	function preprocess_filters()
	{
		$conns = $this->msgobj->connections_from(array(
			"type" => "RELTYPE_RULE",
		));

		if (sizeof($conns) == 0)
		{
			return t("&uuml;htegi ruuli pole seostatud");
		};

		$rv = "";

		$this->subjrules = $this->fromrules = $this->targets = array();

		foreach($conns as $item)
		{
			$filter_obj = new object($item->to());

			if (1 == $filter_obj->prop("on_server"))
			{
				continue;
			};

			$from_rule = $filter_obj->prop("rule_from");
			$subj_rule = $filter_obj->prop("rule_subject");
			$target_folder = $filter_obj->prop("target_folder");
			$id = $filter_obj->id();

			if (!empty($from_rule))
			{
				$this->fromrules[$id] = $from_rule;
			}

			if (!empty($subj_rule))
			{
				$this->subjrules[$id] = $subj_rule;
			};

			if (!empty($target_folder))
			{
				$this->targets[$id] = $target_folder;
			}
		}
	}

	function do_filters($arr = array())
	{
		// now I need read the messages
		$contents = $this->drv_inst->get_folder_contents(array(
			"from" => 1,
			"to" => "*",
		));

		$rv = "";

		$this->done = false;

		$move_ops = array();

		foreach($this->subjrules as $key => $val)
		{
			$matches = $this->drv_inst->search_folder(sprintf('SUBJECT "%s"',$val));
			$target = $this->targets[$key];
			if (is_array($matches))
			{
				$move_ops[$target] = $matches;
				$this->done = true;
			};
		};

		foreach($this->fromrules as $key => $val)
		{
			$matches = $this->drv_inst->search_folder(sprintf('FROM "%s"',$val));
			$target = $this->targets[$key];
			if (is_array($matches))
			{
				$move_ops[$target] = $matches;
				$this->done = true;
			};
		};

		if (empty($arr["dryrun"]) && sizeof($move_ops) > 0)
		{
			foreach($move_ops as $folder => $keys)
			{
				$rv .= $this->drv_inst->move_messages(array(
					"id" => $keys,
					"to" =>  $folder,
				));
			};
		};
		return $rv;
	}

	/** Deletes messages from server
		@attrib name=delete_messages all_args=1

	**/
	function delete_messages($arr)
	{
		$marked = is_array($arr["mark"]) && sizeof($arr["mark"]) > 0 ? $arr["mark"] : false;
		if (is_array($marked))
		{
			$this->_connect_server(array(
				"msgr_id" => $arr["id"],
			));
			// why the hell is this change folder here?
			// bite my legs of if it's necessary...

			// shit on me... this is actually necessary .. :S
			$this->drv_inst->change_folder(array(
				"use_mailbox" => $arr["mailbox"],
			));
			$this->drv_inst->delete_msgs_from_folder(array_keys($marked));
		};

		// those have to return links, and how do I do that?
		return $this->mk_my_orb("change",array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"ft_page" => $arr["ft_page"],
			"mailbox" => $arr["mailbox"],
		));
	}

	/** Deletes messages from server

		@attrib name=delete_search_results all_args="1"

	**/
	function delete_search_results($arr)
	{
		$marked = is_array($arr["mark"]) && sizeof($arr["mark"]) > 0 ? $arr["mark"] : false;
		if (is_array($marked))
		{
			$this->_connect_server(array(
				"msgr_id" => $arr["id"],
			));
			$this->drv_inst->delete_msgs_from_folder(array_keys($marked));
		};

		// those have to return links, and how do I do that?
		return $this->mk_my_orb("change",array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"s_subject" => $arr["s_subject"],
			"s_from" => $arr["s_from"],

		));
	}

	/** Moves messages to another server

		@attrib name=move_messages

	**/
	function move_messages($arr)
	{
		$marked = is_array($arr["mark"]) && sizeof($arr["mark"]) > 0 ? $arr["mark"] : false;
		if ($arr["move_to_folder"] !== 0 && is_array($marked))
		{
			$this->_connect_server(array(
				"msgr_id" => $arr["id"],
			));

			$rv = $this->drv_inst->move_messages(array(
				"id" => array_keys($marked),
				"to" =>  $arr["move_to_folder"],
			));
		};
		return $this->mk_my_orb("change",array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"ft_page" => $arr["ft_page"],
			"mailbox" => $arr["mailbox"],
		));
	}

	/**
		@attrib name=test_tree all_args="1"

	**/
	function test_tree($arr)
	{
		$this->_connect_server(array(
			"msgr_id" => $arr["id"],
		));
		$list = imap_getmailboxes($this->drv_inst->mbox,$this->drv_inst->servspec,"*");
		foreach($list as $folder_o)
		{
			$fn = $folder_o->name;
			$status = imap_status($this->drv_inst->mbox,$folder_o->name,SA_ALL);
			print "fn = $fn";

		}
		print "all done<br>";
	}

	function callback_mod_retval($arr)
	{
		$args = &$arr["args"];
		if (!empty($arr["request"]["ft_page"]))
		{
			$args["ft_page"] = $arr["request"]["ft_page"];
		};
	}

	function _get_identity_list($arr)
	{
		if (!$this->can("view", $arr["id"]))
		{
			return array();
		}
		$msgrobj = new object($arr["id"]);
		$rv = array();
		$frm = $msgrobj->prop("fromname");
		if(is_oid($frm) && $this->can("view", $frm))
		{
			$frm = obj($frm);
			$rv[$frm] = $frm->prop("mail");
		}
		$conns = $msgrobj->connections_from(array(
			"type" => "RELTYPE_FROMNAME",
		));
		foreach($conns as $conn)
		{
			$obj = new object($conn->to());
			$rv[$obj->id()] = htmlspecialchars($obj->prop("name")." <".$obj->prop("mail").">");
		}
		return $rv;
	}

	function _gen_address_list($arr)
	{
		$msgrobj = new object($arr["id"]);
		$rv = array();
		$conns = $msgrobj->connections_from(array(
			"type" => "RELTYPE_ADDRESS",
		));
		foreach($conns as $conn)
		{
			$obj = new object($conn->to());
			$rv[$obj->id()] = $obj->prop("name");
		};
		return $rv;
	}

	/**
		@attrib name=quicksearch
	**/
	function quicksearch($arr)
	{
		return $this->mk_my_orb("change",array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"ft_page" => $arr["ft_page"],
			"mailbox" => $arr["mailbox"],
			"quicksearch" => $arr["search_field"],
		));
	}

	function do_search($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_mk_mb_table($t, $arr["obj_inst"]);
		$from = $arr["request"]["s_from"];
		$subj = $arr["request"]["s_subject"];
		$this->_connect_server(array(
			"msgr_id" => $arr["obj_inst"]->id(),
			"no_folders" => true,
		));


		if (!empty($subj) || !empty($from))
		{
			$str = array();
			if (!empty($subj))
			{
				$str[] = sprintf('SUBJECT "%s"',$subj);
			};
			if (!empty($from))
			{
				$str[] = sprintf('FROM "%s"',$from);
			};
			$fldrs = $this->drv_inst->list_folders();

			// have to do all dha crap for every folder, actually all this sucks a bit, or even a bit more than a bit
			foreach($fldrs as $fld)
			{
				$this->use_mailbox = $fld["name"];
				$this->_connect_server(array(
					"msgr_id" => $arr["obj_inst"]->id(),
					"force_reconnect" => true,
				));
				$matches = $this->drv_inst->search_folder(join(" ",$str));
				if (is_array($matches))
				{
					foreach($matches as $msg_uid)
					{
						$message = $this->drv_inst->fetch_headers(array(
							"msgid" => $msg_uid,
							"arr" => 1,
						));
						$seen = ($message->Unseen != "U");
						$fromline = "";

						$addrinf = $this->drv_inst->_extract_address($message->fromaddress);
						$fromn = $this->drv_inst->MIME_decode($addrinf["name"]);
						if (!empty($fromn))
						{
							$fromline = html::href(array(
								"url" => "javascript:void();",
								"title" => $message->fromaddress,
								"caption" => substr($fromn,0,1),
							)) . substr($fromn,1);
						}
						else
						{
							$fromline = $message->from;
						};

						// this should be unique enough
						$wname = "msgr" . $key;
						$t->define_data(array(
							"id" => $msg_uid,
							"from" => $this->_format($fromline,$seen),
							"subject" => html::href(array(
								"url" => "javascript:aw_popup_scroll(\"" . $this->mk_my_orb("change",array(
										"msgrid" => $arr["obj_inst"]->id(),
										"msgid" => $msg_uid,
										"form" => "showmsg",
										"cb_part" => 1,
										"mailbox" => $this->use_mailbox,
								),"mail_message",false,true) . "\",\"$wname\",800,600)",
								"caption" => $this->_format(parse_obj_name($this->drv_inst->_parse_subj($message->subject)),$seen),
							)),
							"date" => strtotime($message->date),
							"size" => $this->_format(sprintf("%d",$message->Size/1024),$seen),
							"answered" => $this->_format($this->_conv_stat($message->answered),$seen),
							"attach" => $message->has_attachments ? html::img(array("url" => $this->cfg["baseurl"] . "/automatweb/images/attach.gif")) : "",
						));
					}
				}
			}
		}
	}

	/**
		@attrib name=get_mailbox_js
		@param id required
	**/
	function get_mailbox_js($arr)
	{
		$this->read_template("mailbox.js");
		$msgobj = new object($arr["id"]);
		$this->vars(array(
			"server" => $this->mk_my_orb("imap",array("id" => $msgobj->id())),
			"message" => $this->mk_my_orb("change",array(
				"msgrid" => $msgobj->id(),
				"form" => "showmsg",
				"cb_part" => 1,
			),"mail_message") . "&",
		));
		die($this->parse());
	}

	/**
		@attrib name=get_comm
	**/
	function get_comm()
	{
		$this->read_template("subetha_sensomatic.js");
		die($this->parse());

	}

	/**
		@attrib name=v3
		@param id required
	**/
	function v3($arr)
	{
		// vata see asi peab mul n&uuml;&uuml;d v&auml;ljastama selle serveriv&auml;rgi
		$msgobj = new object($arr["id"]);
		$this->read_template("ui.tpl");
		$this->vars(array(
			"u1" => $this->mk_my_orb("get_comm",array()),
			"u2" => $this->mk_my_orb("get_mailbox_js",array("id" => $msgobj->id())),
		));
		die($this->parse());
	}

	/**
		@attrib name=imap all_args=1
	**/
	function imap($arr)
	{
		$msgobj = new object($arr["id"]);
		$conns = $msgobj->connections_from(array("type" => "RELTYPE_MAIL_SOURCE"));


		// right now it only deals with a single server.
		list(,$_sdat) = each($conns);
		if (empty($_sdat))
		{
			print "IMAP sissep&auml;&auml;s on konfigureerimata";
			return false;
		}
		$obj = $_sdat->to();
		$server = $obj->prop("server");
		$port = $obj->prop("port");
		$user = $obj->prop("user");
		$password = $obj->prop("password");


		//  cert validating could probably be made an option later on
		$mask = (1 == $obj->prop("use_ssl")) ? "{%s:%d/ssl/novalidate-cert}" : "{%s:%d}";

		$server = sprintf($mask,$server,$port);
		include("imap.php");
		die();

	}

	/**
		@attrib name=get_msg_attachment params=name
		@param msgr_id required type=oid
		@param msg_id required type=int
		@param attach_id required type=int
	**/
	function get_msg_attachment($arr)
	{
		$mm = new message();
		$attach = $mm->get_attachment($arr);
		header("Content-Type:".$attach["content_type"]);
		header("Expires:".gmdate("D, d M Y H:i:s")." GMT");
		header('Content-Disposition:attachment; filename="'.$attach["filename"].'"');
		Header("Content-Disposition-type: attachment");
		Header("Content-Transfer-Encoding: binary");
		Header("Content-Length: ".$attach["size"]);
		echo $attach["body"];
		die();
	}
}
?>
