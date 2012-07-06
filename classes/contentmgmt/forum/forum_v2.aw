<?php
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_FORUM_V2, on_connect_menu)

	@classinfo syslog_type=ST_FORUM relationmgr=yes

@default table=objects


	@groupinfo general_sub caption="&Uuml;ldine" parent=general
	@default group=general_sub

		@property name type=textbox rel=1 trans=1
		@caption Nimi
		@comment Objekti nimi

		@property comment type=textbox
		@caption Kommentaar
		@comment Vabas vormis tekst objekti kohta

		@property status type=status trans=1 default=1
		@caption Aktiivne
		@comment Kas objekt on aktiivne


@default field=meta
@default method=serialize
		@property topic_folder type=relpicker reltype=RELTYPE_TOPIC_FOLDER
		@caption Teemade kaust
		@comment Sellest kaustast v&uuml;etakse foorumi teemad

		@property address_folder type=relpicker reltype=RELTYPE_ADDRESS_FOLDER
		@caption Listiliikmete kaust
		@comment Sellesse kausta paigutatakse "listi liikmete" objektid

		@property images_folder type=relpicker reltype=RELTYPE_IMAGES_FOLDER
		@caption Piltide kaust
		@comment Kui piltide kaust on valitud, siis foorumisse pandud piltide objektid salvestatakse selle kausta alla, muidu pannakse selle objekti alla, mille k&uuml;lge pilt pannakse

		@property faq_folder type=relpicker reltype=RELTYPE_FAQ_FOLDER
		@caption KKK kaust
		@comment Sellesse kausta paigutatakse KKK dokumendid

	@groupinfo mail_settings caption="Meiliseaded" parent=general
	@default group=mail_settings

		@property answers_to_mail type=checkbox ch_value=1
		@caption Soovi korral vastused meiliga

#		@property mail_from type=textbox field=meta method=serialize
#		@caption Kellelt
#		comment Default on kommenteerija nimi

		@property mail_from type=textbox
		@caption E-mail kellelt

		@property mail_address type=textbox
		@caption E-maili aadress kellelt
		comment Default - Kommenteerija e-mail

		@property mail_subject type=textbox
		@caption Maili subject
		@comment Kui m&auml;&auml;ramata, siis foorumi topic

        @groupinfo required_fields caption="Kohustuslikud v&auml;ljad" parent=general
        @default group=required_fields

                @property required_fields type=chooser multiple=1 orient=vertical
                @caption Kohustuslikud v&auml;ljad

	@groupinfo users caption="Kasutajad" parent=general
	@default group=users

		@property show_logged type=checkbox ch_value=1
		@caption Luba kasutajal oma andmeid muuta

		@property limit_anonymous_posting type=checkbox ch_value=1
		@caption Anon&uuml;&uuml;mse postitamise piiramine

		@property post_name type=chooser
		@caption Postituste autori nimi


@groupinfo look caption="V&auml;limus"
	@groupinfo styles caption="Stiilid" parent=look
	@default group=styles

		@property style_general_subtitle type=text store=no subtitle=1
		@caption &Uuml;ldine

			@property style_donor type=relpicker reltype=RELTYPE_STYLE_DONOR
			@caption Stiilidoonor

			@property style_caption type=relpicker reltype=RELTYPE_STYLE
			@caption Tabeli pealkirja stiil

			@property style_forum_yah type=relpicker reltype=RELTYPE_STYLE
			@caption Foorumi asukohariba stiil

		@property thread_folder_subtitle type=text store=no subtitle=1
		@caption Teema kaust

			@property style_l1_folder type=relpicker reltype=RELTYPE_STYLE
			@caption Teema kausta stiil

			@property style_folder_caption type=relpicker reltype=RELTYPE_STYLE
			@caption Teema kausta pealkirja stiil

			@property style_folder_topic_count type=relpicker reltype=RELTYPE_STYLE
			@caption Teema kausta arvu stiil

			@property style_folder_comment_count type=relpicker reltype=RELTYPE_STYLE
			@caption Teema kausta vastuste arvu stiil

			@property style_folder_last_post type=relpicker reltype=RELTYPE_STYLE
			@caption Teema kausta viimase vastuse stiil

		@property thread_styles_subtitle type=text store=no subtitle=1
		@caption Teema

			@property style_new_topic_row type=relpicker reltype=RELTYPE_STYLE
			@caption Teema lisamise stiil

			@property style_topic_caption type=relpicker reltype=RELTYPE_STYLE
			@caption Teema pealkirja stiil

			@property style_topic_replies type=relpicker reltype=RELTYPE_STYLE
			@caption Teema vastuste arvu stiil

			@property style_topic_author type=relpicker reltype=RELTYPE_STYLE
			@caption Teema autori stiil

			@property style_topic_last_post type=relpicker reltype=RELTYPE_STYLE
			@caption Teema viimase vastuse stiil

			@property style_comment_creator type=relpicker reltype=RELTYPE_STYLE
			@caption Teema autori stiil

		@property answer_style_subtitle type=text store=no subtitle=1
		@caption Vastus

			@property style_comment_count type=relpicker reltype=RELTYPE_STYLE
			@caption Vastuste arvu stiil

			@property style_comment_user type=relpicker reltype=RELTYPE_STYLE
			@caption Vastuse autori stiil

			@property style_comment_time type=relpicker reltype=RELTYPE_STYLE
			@caption Vastuse aja stiil

			@property style_comment_text type=relpicker reltype=RELTYPE_STYLE
			@caption Vastuse teksti stiil

		@property input_form_subtitle type=text store=no subtitle=1
		@caption Sisestusvorm

			@property style_form_caption type=relpicker reltype=RELTYPE_STYLE
			@caption Sisestusvormi pealkirja stiil

			@property style_form_text type=relpicker reltype=RELTYPE_STYLE
			@caption Sisestusvormi teksti stiil

			@property style_form_element type=relpicker group=styles reltype=RELTYPE_STYLE
			@caption Sisestusvormi elemendi stiil

	@groupinfo jrks caption=J&auml;rjekorrad parent=look
	@default group=jrks

		@property jrks type=callback callback=callback_gen_jrks no_caption=1

	@groupinfo topic_form_fields caption="Teema vormi v&auml;ljad" parent=look
	@default group=topic_form_fields

		@property topic_form_fields type=chooser multiple=1 orient=vertical
		@caption Teema lisamise vormi v&auml;ljad

@groupinfo settings caption="Sisu seaded"

	@groupinfo topic_selector caption=Teemad parent=settings
	@default group=topic_selector

		@property topics_on_page type=textbox
		@caption Teemasid lehel

		@property topics_sort_order type=select
		@caption Teemade j&auml;rjekord

		@property activation type=checkbox ch_value=1
		@caption Teemade aktiveerimine



		@property comments_on_page type=textbox
		@caption Kommentaare lehel

		@property topics_editable type=hidden
		@property comments_editable type=hidden
		@property posts_editable type=chooser multiple=1 store=no
		@caption Postitused muudetavad
		@comment Kehtib ainult autori kohta

		@property topic_depth type=select default=0
		@caption Teemade s&uuml;gavus

		@property topic_last_author type=select default=1
		@caption Viimane autor
		@comment Teemade nimekirjas viimase lisajana n&auml;idata, kes lisas teema v&otilde;i vastuse

		@property link_max_len type=textbox
		@caption Lingi maksimaalne pikkus
		@comment Linkide kuvamisel maksimaalne lingi teksti pikkus

		@property show_last_posts_count type=textbox default=3
		@caption Viimaseid postitusi kuva
		@comment Mitut n&auml;idata viimaste postituste vaates

		@property topic_selector type=table no_caption=1
		@caption Teemade tasemed

		@property image_upload_subtitle type=text store=no subtitle=1
		@caption Pildi &uuml;leslaadimise v&auml;li

		@property show_image_upload_in_add_topic_form type=checkbox ch_value=1
		@caption Teema lisamise vormis

		@property show_image_upload_in_add_comment_form type=checkbox ch_value=1
		@caption Kommentaari lisamise vormis

	@groupinfo image_verification caption=Kontrollpilt parent=settings
	@default group=image_verification

		@property use_image_verification type=checkbox ch_value=1
		@caption Kasuta kontrollpilti

		@property verification_image type=releditor use_form=emb reltype=RELTYPE_IMAGE_VERIFICATION rel_id=first
		@caption Kontrollpilt

		@property verification_image_oid type=text
		@caption Kontrollpildi ID

	@groupinfo import caption=Import parent=settings
	@default group=import

		@property import_xml_file type=fileupload store=no
		@caption Vali XML fail

	@groupinfo actv_settings caption=Aktiiverimine parent=settings
	@default group=actv_settings

		@property add_admin_mail type=checkbox ch_value=1
		@caption Uued teemad adminile meiliga

		@property a_mail_from type=textbox
		@caption E-mail kellelt

		@property a_mail_address type=textbox
		@caption E-maili aadress kellelt

		@property a_mail_subject type=textbox
		@caption Maili subject

		@property mail_vars type=text
		@caption Maili sisu muutujad

		@property a_mail_body type=textarea cols=50 rows=10
		@caption Maili sisu

	@groupinfo views caption="Vaated" submit=no
	@groupinfo contents caption="Eelvaade" submit=no parent=views
	@default group=contents
		@property topic type=hidden store=no
		@caption Topic ID (sys)

		@property show type=callback callback=callback_gen_contents store=no no_caption=1
		@caption Foorumi sisu

	@groupinfo last_posts caption="Viimased postitused" submit=no parent=views
	@default group=last_posts
		@property last_posts type=table store=no no_caption=1
		@caption Foorumi viimased postitused

	@groupinfo search caption="Otsing" submit=no
	@default group=search

		@property show2 type=callback callback=callback_gen_search store=no no_caption=1
		@caption Otsing

// ---------------- RELTYPES ------------------

	@reltype TOPIC_FOLDER value=1 clid=CL_MENU
	@caption Teemade kaust

	@reltype ADDRESS_FOLDER value=2 clid=CL_MENU
	@caption Listiliikmete kaust

	@reltype STYLE value=3 clid=CL_CSS
	@caption Stiil

	@reltype STYLE_DONOR value=4 clid=CL_FORUM_V2
	@caption Stiilidoonor

	@reltype FORUM_ADMIN value=5 clid=CL_USER,CL_GROUP
	@caption Administraator

	@reltype FAQ_FOLDER value=6 clid=CL_MENU
	@caption KKK kaust

	@reltype IMAGES_FOLDER value=7 clid=CL_MENU
	@caption Piltide kaust

	@reltype EMAIL value=8 clid=CL_ML_MEMBER
	@caption Meiliaadress

	@reltype IMAGE_VERIFICATION value=9 clid=CL_IMAGE_VERIFICATION
	@caption Kontrollpilt

	@reltype ICON value=10 clid=CL_IMAGE
	@caption Teema ikoon

*/


//DEPRECATED constants
define('TOPICS_SORT_ORDER_NEWEST_TOPICS_FIRST', 1);  define('TOPICS_SORT_ORDER_ALPHABET', 2); define('TOPICS_SORT_ORDER_NEWEST_COMMENTS_FIRST', 3); define('TOPICS_SORT_ORDER_MOST_COMMENTED_FIRST', 4); define('TOPICS_SORT_ORDER_OBJ_ORD', 5); define('TOPICS_LAST_REPLY_AUTHOR', 1); define('TOPICS_LAST_TOPIC_AUTHOR', 2);


// forum data structure:
// folders (cl_menu) (containing other folders if depth > 1) contain topics
// (cl_msgboard_topic) which in turn contain comments (cl_comment) to topics.


class forum_v2 extends class_base implements site_search_content_group_interface
{
	const TOPICS_SORT_ORDER_NEWEST_TOPICS_FIRST = 1;
	const TOPICS_SORT_ORDER_ALPHABET = 2;
	const TOPICS_SORT_ORDER_NEWEST_COMMENTS_FIRST = 3;
	const TOPICS_SORT_ORDER_MOST_COMMENTED_FIRST = 4;
	const TOPICS_SORT_ORDER_OBJ_ORD = 5;

	const TOPICS_LAST_REPLY_AUTHOR = 1;
	const TOPICS_LAST_TOPIC_AUTHOR = 2;

	var $topics_sort_order = array();

	private $topic_id = 0;
	private $style_donor_obj;
	private $style_data = array();

	function forum_v2()
	{
		$this->init(array(
			"tpldir" => "forum",
			"clid" => CL_FORUM_V2,
		));

		$this->topics_sort_order = array(
			self::TOPICS_SORT_ORDER_NEWEST_TOPICS_FIRST => t('Uuemad teemad eespool'),
			self::TOPICS_SORT_ORDER_ALPHABET => t('T&auml;hestikulises j&auml;rjekorras (A-Z)'),
			self::TOPICS_SORT_ORDER_NEWEST_COMMENTS_FIRST => t('Viimati kommenteeritud eespool'),
			self::TOPICS_SORT_ORDER_MOST_COMMENTED_FIRST => t('Enim kommenteeritud eespool'),
			self::TOPICS_SORT_ORDER_OBJ_ORD => t('Objektide jrk. nr. j&auml;rgi')
		);

		$this->topic_last_author = array(
			self::TOPICS_LAST_REPLY_AUTHOR => t("vastus"),
			self::TOPICS_LAST_TOPIC_AUTHOR => t("teema")
		);

		$this->link_def_max_len = 50;

		lc_site_load("forum", $this);

		$this->comment_fields = array(
			"name" => t("Pealkiri"),
			"uname" => t("Autor"),
			"pname" => t("Autori nimi"),
			"uemail" => t("Autori e-mail"),
			"commtext" => t("Kommentaar"),
		);

		$this->topic_fields = array(
			"name" => t("Pealkiri"),
			"author_name" => t("Autori nimi"),
			"author_email" => t("Autori e-mail"),
			"answers_to_mail" => t("Vastused e-mailile"),
			"comment" => t("Kommentaar"),
		);

	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
		//	case "topics_on_page":
		//	case "comments_on_page":
		//		$data["options"] = array(5 => 5,10 => 10,15 => 15,20 => 20,25 => 25,30 => 30);
		//		break;
			case "post_name":
				$data["options"] = array(
					0 => "Kasutaja nimi",
					1 => "Isiku nimi",
				);
				break;
			case "required_fields":
				$data["options"] = $this->comment_fields;
				break;

			case "topic_form_fields":
				if (empty($data['value']))
				{
					$data['value'] = array_combine(array_keys($this->topic_fields), array_keys($this->topic_fields));
				}
				$data["options"] = $this->topic_fields;
				break;

			case "topics_sort_order":
				$data['options'] = $this->topics_sort_order;
				break;

			case "topic_last_author":
				$data["options"] = $this->topic_last_author;
				break;

			case "posts_editable":
				$data["options"] = array(
					"topics_editable" => t("teemad"),
					"comments_editable" => t("vastused"),
				);
				$data["value"] = array(
					"topics_editable" => $arr["obj_inst"]->prop("topics_editable")?1:0,
					"comments_editable" => $arr["obj_inst"]->prop("comments_editable")?1:0,
				);
				break;

			case "topic_depth":
				$data["options"] = array("0" => "0","1" => "1","2" => "2","3" => "3","4" => "4","5" => "5");
				break;

			case "topic_selector":
				$topic_folder = $arr["obj_inst"]->prop("topic_folder");
				$depth = $arr["obj_inst"]->prop("topic_depth");
				// hide topic_selector if it doesn't make any sense
				if (0 == $depth)
				{
					$retval = PROP_IGNORE;
				}
				else if (!is_oid($topic_folder))
				{
					$retval = PROP_ERROR;
					$data["error"] = t("Teemade kaust on valimata");
				}
				else
				{
					$this->get_topic_selector(&$arr);
				};
				break;

			case "topic":
				if (!empty($arr["request"]["topic"]))
				{
					$data["value"] = $arr["request"]["topic"];
				}
				else
				{
					$retval = PROP_IGNORE;
				};
				break;
			case "mail_vars":
				$data["value"] = "Link teemale: [link]<br /> Teema nimi: [name]<br />Teema autor: [author]";
				break;
			case "link_max_len":
				$data["value"] = ($val = $data["value"])? $val: $this->link_def_max_len;
				break;
			case "verification_image_oid":
				$image_verification_obj = $arr['obj_inst']->get_first_obj_by_reltype("RELTYPE_IMAGE_VERIFICATION");
				if (!empty($image_verification_obj))
				{
					$data['value'] = $image_verification_obj->id();
				}
				break;
			default:
				$tmp = explode("_", $data["name"]);
				if($tmp[0] == "text" || $tmp[0] == "icon")
				{
					$data["value"] = $arr["obj_inst"]->meta($data["name"]);
				}
				break;
		};
		return $retval;

	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		$this_o = $arr["obj_inst"];

		switch($data["name"])
		{
			case "topic_selector":
				$this->update_topic_selector($arr);
				break;

			case "posts_editable":
				$this_o->set_prop("topics_editable", (int)(bool) $data["value"]["topics_editable"]);
				$this_o->set_prop("comments_editable", (int)(bool) $data["value"]["comments_editable"]);
				break;

			case "container":
				$this->update_contents($arr);
				break;

			case "import_xml_file":
				$tmpname = $_FILES["import_xml_file"]["tmp_name"];
				if (is_uploaded_file($tmpname))
				{
					$contents = aw_unserialize(file_get_contents($tmpname));
					$forumdata = $contents["forum"];
					$topicdata = $contents["topics"];
					$commentdata = $contents["comments"];
					if (is_array($forumdata) && is_array($topicdata) && is_array($commentdata))
					{
						$this->obj_inst = $arr["obj_inst"];
						$this->create_forum_from_comments($forumdata,$topicdata,$commentdata);
					};
				};
				break;

			case "show":
				$this->process_contents($arr);
				break;

			case "jrks":
				foreach($arr["request"] as $var => $val)
				{
					$tmp = explode("_", $var);
					if($tmp[0] == "text" || $tmp[0] == "icon")
					{
						$arr["obj_inst"]->set_meta($var, $val);
					}
				}
				break;
		}
		return $retval;
	}

	function process_contents($arr)
	{
		if (isset($arr["request"]["delete_selected_topics"]))
		{
			$topics_to_delete = new aw_array($arr["request"]["sel_topic"]);
			foreach($topics_to_delete->get() as $topic_id => $foo)
			{
				if ($this->can("delete",$topic_id))
				{
					$topic_obj = new object($topic_id);
					$topic_obj->delete();
				};
			};
		};
		if(isset($arr["request"]["save_jrk"]))
		{
			$data = new aw_array($arr["request"]["jrk"]);
			foreach($data->get() as $topic_id => $jrk)
			{
				$to = obj($topic_id);
				$to->set_ord($jrk);
				$to->save();
			}
		}
		if (isset($arr["request"]["locktoggle_selected_topics"]))
		{
			$topic_list = new aw_array($arr["request"]["sel_topic"]);
			foreach($topic_list->get() as $topic_id => $foo)
			{
				if ($this->can("edit",$topic_id))
				{
					$topic_obj = new object($topic_id);
					$topic_obj->set_prop("locked",!$topic_obj->prop("locked"));
					$topic_obj->save();
				}
			}
		}
		return class_base::PROP_OK;
	}

	function create_forum_from_comments($forumdata,$topicdata,$commentdata)
	{
		$ol = new object_list(array(
			"class_id" => CL_FORUM_V2,
			"name" => $forumdata["name"],
		));
		$id = $this->obj_inst->id();
		$o = $this->obj_inst;

		print "creating forum object with name " . $forumdata["name"] . "<br>";
		print "creating folder for topics " . $forumdata["name"] . " teemad" . "<br>";
		print "creating topics<br>";

		$o->set_name($forumdata["name"]);
		$o->set_comment($forumdata["comment"]);
		$o->set_prop("topics_on_page",$forumdata["topics_on_page"]);
		$o->set_prop("comments_on_page",$forumdata["comments_on_page"]);

		// does this forum have a topic folder?
		$folder_conns = $o->connections_from(array(
			"type" => "RELTYPE_TOPIC_FOLDER",
		));

		if (sizeof($folder_conns) == 0)
		{
			// create the folder then!
			print "creating folder for topics<br>";
			$mn = new object();
			$mn->set_class_id(CL_MENU);
			$mn->set_parent($o->parent());
			$mn->set_status(STAT_ACTIVE);
			$mn->set_name($forum_data["name"] . " teemad");
			$mn->save();
			$topic_folder = $mn->id();

			$o->connect(array(
				"to" => $topic_folder,
				"reltype" => "RELTYPE_TOPIC_FOLDER",
			));

			$o->set_prop("topic_folder",$topic_folder);
			print "connecting<br>";
		}
		else
		{
			$topic_folder = $o->prop("topic_folder");
		};

		$o->save();

		// first, create a list of all topics in this folder
		$topic_list = new object_list(array(
			"parent" => $topic_folder,
			"class_id" => CL_MSGBOARD_TOPIC,
		));

		$existing_topics = array();

		foreach ($topic_list->arr() as $to)
		{
			// each imported topic has its unique id in metadata
			$import_id = $to->meta("import_id");
			if (!empty($import_id))
			{
				$existing_topics[$import_id] = 1;
			};
		};

		// there is a shitload of topics with no name, I need to take those into account
		foreach($topicdata as $topic_id => $topic_data)
		{
			if ($existing_topics[$topic_id])
			{
				print "topic exists, not creating object<br>";
				$comment_parent = $topic_id;
			}
			else
			{
				print "creating topic $topic_id / " . $topic_data["subject"] . "<br>";
				//arr($topic_data);
				$topic_obj = new object();
				$topic_obj->set_class_id(CL_MSGBOARD_TOPIC);
				$topic_obj->set_parent($topic_folder);
				$topic_obj->set_name($topic_data["subject"]);
				$topic_obj->set_comment($topic_data["comment"]);
				// XXX: HACK: can't modify created, but need it. this is the workaround
				$topic_obj->set_subclass($topic_data["time"]);
				// XXX: HACK: can't modify created, but need it. this is the workaround
				$topic_obj->set_prop("author_name",$topic_data["author"]);
				$topic_obj->set_prop("author_email",$topic_data["email"]);
				$topic_obj->set_status(STAT_ACTIVE);
				$topic_obj->set_meta("import_id",$topic_id);
				$topic_obj->save();
				$comment_parent = $topic_obj->id();
			};

			if (is_array($commentdata[$topic_id]))
			{
				$existing_comments = array();
				// first, create a list of all topics in this folder
				$comm_list = new object_list(array(
					"parent" => $comment_parent,
					"class_id" => CL_COMMENT,
				));

				foreach ($comm_list->arr() as $co)
				{
					// each imported topic has its unique id in metadata
					$import_id = $co->meta("import_id");
					if (!empty($import_id))
					{
						$existing_comments[$import_id] = 1;
					};
				};

				foreach($commentdata[$topic_id] as $comm_id => $comments)
				{
					if ($existing_comments[$comm_id])
					{
						print "not creating existing comment<br>";
					}
					else
					{
						print "creating comment ";
						arr($comments);
						print "<br>";
						$comm = new object();
						print "cp is $comment_parent<br>";
						$comm->set_parent($comment_parent);
						$comm->set_class_id(CL_COMMENT);
						$comm->set_name($comments["subject"]);
						$comm->set_prop("ip",$comments["ip"]);
						$comm->set_status(STAT_ACTIVE);
						$comm->set_prop("uname",$comments["name"]);
						$comm->set_prop("uemail",$comments["email"]);
						$comm->set_prop("commtext",$comments["comment"]);
						$comm->set_meta("import_id",$comm_id);
						// XXX: HACK: can't modify created, but need it. this is the workaround
						$comm->set_subclass($comments["time"]);
						// XXX: HACK: can't modify created, but need it. this is the workaround
						$comm->save();
					};
				};
			};

			print "topic loading finished<br>";
		}
		print "forum import finished<br>";
	}

	function callback_pre_edit($arr)
	{
		$this->rel_id = aw_global_get("section");
	}

	function callback_gen_jrks($arr)
	{
		$folders = $this->get_search_folders($arr);
		$fids = array();
		foreach($folders as $fid=>$foo)
		{
			$fids[] = $fid;
		}
		$odl = new object_data_list(array(
				"lang_id" => array(),
				"site_id" => array(),
			),
			array(CL_MSGBOARD_TOPIC => array(new obj_sql_func(OBJ_SQL_UNIQUE, "jrk", "jrk")))
		);
		$jrks = array();
		foreach($odl->arr() as $od)
		{
			if($od["jrk"] == "")
			{
				$od["jrk"] = 0;
			}
			$jrks[$od["jrk"]] = $od["jrk"];
		}
		$props = array();
		foreach($jrks as $jrk)
		{
			$props["icon_".$jrk] = array(
				"name" => "icon_".$jrk,
				"type" => "relpicker",
				"reltype" => "RELTYPE_ICON",
				"caption" => "Ikoon (".$jrk.")",
			);
			$props["text_".$jrk] = array(
				"name" => "text_".$jrk,
				"type" => "textbox",
				"caption" => "Tekst (".$jrk.")",
			);
		}
		return $props;
	}

	function callback_gen_search($arr)
	{
		if ($arr["request"]["searchv"])
		{
			$rv["contents"] = array(
				"type" => "text",
				"name" => "contents",
				"value" => $this->search_results($arr),
				"no_caption" => 1,
			);
		}
		else
		{
			$rv = $this->get_search_form($arr);
		};
		return $rv;
	}

	function callback_gen_contents($arr)
	{
		$this->style_data = array();
		$this->obj_inst = $arr["obj_inst"];
		$style_donor = $this->obj_inst->prop("style_donor");
		if (is_oid($style_donor))
		{
			$this->style_donor_obj = new object($style_donor);
		};
		$this->_add_style("style_caption");

		$rv = array();

		if (is_oid($arr["request"]["topic"]))
		{
			$rv = $this->draw_topic($arr);
		}
		elseif (is_oid($arr["request"]["folder"]))
		{
			$rv["contents"] = array(
				"type" => "text",
				"store" => "class_base",
				"name" => "contents",
				"value" => $this->draw_folder($arr),
				"no_caption" => 1,
			);
		}
		else
		{
			// default view, used when the user first views the forum
			// shows all folders
			$rv["contents"] = array(
				"type" => "text",
				"store" => "class_base",
				"name" => "contents",
				"value" => $this->draw_all_folders($arr),
				"no_caption" => 1,
			);
		}

		//$prop = $arr["prop"];
		//$prop["value"] = $retval;
		//return array($prop);
		return $rv;
	}

	function _last_posts_tbl($arr)
	{
		$this_o = $arr["obj_inst"];

		// init table
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "comment",
		));

		// get last posts
		$last_posts_count = (int) $this_o->prop("show_last_posts_count");
		$last_posts_count = 1 > $last_posts_count ? 3 : $last_posts_count;
		$last_posts = array();
		$parents = new object_tree(array(
			"parent" => $this_o->prop("topic_folder"),
			"class_id" => array(CL_MSGBOARD_TOPIC, CL_MENU)
		));
		$parents = $parents->ids();//!!! ajutine. kui palju teemasid siis muu lahendus vajalik
		$comments = new object_list(array(
			"parent" => $parents,
			"class_id" => CL_COMMENT,
			"sort_by" => "objects.created DESC",
			"limit" => $last_posts_count
		));

		// fill table
		$comments->begin();
		$this->read_template("comment_in_last_comments.tpl");

		while ($comment = $comments->next())
		{
			$this->vars(array(
				"name" => $comment->name(),
				"createdby" => $comment->createdby(),
				"created" => $this->time2date($comment->created(), 2),
			));
			$t->define_data(array(
				"comment" => $this->parse()
			));
		}

		$t->set_sortable(false);
	}

	function draw_all_folders($args = array())
	{
		extract($args);

		$this->read_template("forum.tpl");

		$c = "";

		$this->_add_style("style_new_topic_row");
		$this->_add_style("style_l1_folder");
		$this->_add_style("style_folder_caption");
		$this->_add_style("style_folder_topic_count");
		$this->_add_style("style_folder_comment_count");
		$this->_add_style("style_folder_last_post");
		$this->vars($this->style_data);

		// so now I need a function that gives me all folders .. hm .... can I use object_tree
		// for that then? no, obviously not.

		// it is important to know that comments may only be at the lowest level

		$depth = $args["obj_inst"]->prop("topic_depth");
		if (empty($depth) && $depth != 0)
		{
			$depth = 1;
		}

		$this->depth = $depth;

		// forum allows turning off of certain folders, this deals with it.
		$this->exclude = $args["obj_inst"]->meta("exclude");
		$this->exclude_subs = $args["obj_inst"]->meta("exclude_subs");

		$this->level = 1;
		$this->group = $args["request"]["group"];

		$conns = $args["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_TOPIC_FOLDER",
		));

		$c = "";
		$first = 0;
		foreach($conns as $conn)
		{
			// ideaalis v6iks saada teemasid teha ka otse foorumi sisse.
			// see t4hendab siis seda, et kui kataloogi pole m44ratud, siis
			// tulevad teemad by default kohe foorum sisse. Ja nii ongi...
			if ($this->depth == 0)
			{
				$args["request"]["folder"] = $conn->prop("to");
				$c .= $this->draw_folder($args);
			}
			else
			{
				$c .= $this->_draw_one_level(array(
					"parent" => $conn->prop("to"),
					"id" => $args["obj_inst"]->id(),
					"obj_inst" => $args["obj_inst"]
				));
			}
		}

		$this->vars(array(
			"forum_contents" => $c,
			"search_url" => $this->mk_my_orb("search",array(
				"id" => $args["obj_inst"]->id(),
				"group" => $arr["group"],
				"section" => aw_global_get("section"),
			)),
		));

		$rv = $this->parse();
		return $rv;
	}

	function _draw_one_level($arr)
	{
		if ($this->level == $this->depth)
		{
			$c .= $this->_draw_last_level(array(
				"parent" => $arr["parent"],
				"id" => $arr["id"],
				"obj_inst" => $arr["obj_inst"]
			));
		}
		else
		{
			// this shit doesn't even draw the first level, even if explicitly specify that one should exist
			// why???
			$folder_list = new object_list(array(
				"parent" => $arr["parent"],
				"class_id" => CL_MENU,
				"status" => STAT_ACTIVE,
				"sort_by" => "objects.jrk ASC"
			));
			foreach ($folder_list->arr() as $folder_obj)
			{
				$this->vars(array(
					"name" => $folder_obj->name(),
					"comment" => $folder_obj->comment(),
					"open_l1_url" => $this->mk_my_orb("change",array(
						"id" => $arr["id"],
						"c" => $folder_obj->id(),
						"group" => $this->group,
						"section" => $this->rel_id,
						"_alias" => get_class($this),
					)),
				));
//				$tplname = "L" . $this->level . "_FOLDER";

				$tplname = "FOLDER";
				$this->vars(array(
					"spacer" => str_repeat("&nbsp;",6*($this->level-1)),
				));

				if (empty($this->exclude[$folder_obj->id()]))
				{
					$c .= $this->parse($tplname);
				};

				$this->level++;
				$c .= $this->_draw_one_level(array(
					"parent" => $folder_obj->id(),
					"id" => $arr["id"],
					"obj_inst" => $arr["obj_inst"],
				));
				$this->level--;
			}
		};
		return $c;
	}

	// needs at least one argument .. the parent
	function _draw_last_level($arr)
	{
		$sub_folder_list = new object_list(array(
			"parent" => $arr["parent"],
			"class_id" => CL_MENU,
			"status" => STAT_ACTIVE,
			"sort_by" => "objects.jrk ASC"
		));
		$topic_last_author = $arr["obj_inst"]->prop("topic_last_author");

		// for each second level folder, figure out the amount of topics
		// and posts
		list($topic_counts,$topic_list) = $this->get_topic_list(array(
			"parents" => $sub_folder_list->ids(),
			"obj_inst" => $arr["obj_inst"],
		));

		// ja iga alamtopicu jaoks on mul vaja teada, mitu
		// teemat seal on.
		$folder_counter = 0;
		foreach ($sub_folder_list->arr() as $sub_folder_obj)
		{
			list(,$comment_count) = $this->get_comment_counts(array(
				"parents" => $topic_list[$sub_folder_obj->id()],
			));
			$last = (TOPICS_LAST_REPLY_AUTHOR == $topic_last_author) ? $this->get_last_comments(array('parents' => $topic_list[$sub_folder_obj->id()])) :  $this->get_last_topic($sub_folder_obj->id());


			$mdate = $last["created"];
			$datestr = empty($date) ? "" : $this->time2date($mdate,2);

			$lv = $this->level - 2;
			if ($lv < 0)
			{
				$lv = 0;
			};

			$this->vars(array(
				"name" => $sub_folder_obj->name(),
				"comment" => $sub_folder_obj->comment(),
				"topic_count" => (int)$topic_counts[$sub_folder_obj->id()],
				"comment_count" => (int)$comment_count,
				"last_createdby" => $last["uname"],
				"last_date" => $datestr,
				"spacer" => str_repeat("&nbsp;",6*($lv)),
				"open_topic_url" => $this->mk_my_orb("change",array(
					"id" => $arr["id"],
					"folder" => $sub_folder_obj->id(),
					"group" => $this->group,
					"section" => $this->rel_id,
					"_alias" => get_class($this),
				)),
			));
			$c .= $this->parse("LAST_LEVEL");
			if ($folder_counter % 2 == 0)
			{
				$c .= $this->parse("LAST_LEVEL_EVEN");
			}
			else
			{
				$c .= $this->parse("LAST_LEVEL_ODD");
			}
			$folder_counter++;
		};
		return $c;
	}

/* UNUSED METHODS
	function get_folder_tree($arr)
	{
		$this->tree = array();
	}

	////
	// !antakse ette parent ja sygavus ja siis tehakse lotsa t88d
	function _rec_folder_tree($arr)
	{
		$folder_list = new object_list(array(
			"parent" => $arr["parent"],
			"class_id" => CL_MENU,
		));
		foreach ($folder_list->arr() as $folder_obj)
		{
			$this->tree[$folder_obj->parent()][$folder_obj->id()] = $folder_obj->name();
		};
	}
*/

	function _get_fp_link($arr)
	{
		return html::href(array(
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["id"],
				"group" => $arr["group"],
				"section" => aw_global_get("section"),
				"_alias" => get_class($this),
			)),
			"caption" => $arr["name"],
		));
	}

	////
	// !Draws the contents of a single folder
	function draw_folder($args = array())
	{
		extract($args);

		$topics_on_page = $args["obj_inst"]->prop("topics_on_page");

		if (empty($topics_on_page))
		{
			$topics_on_page = 5;
		}

		$oid = $args["obj_inst"]->id();

		$topic_obj = new object($args["request"]["folder"]);

		$this->read_template("folder.tpl");

		$obj_chain = $topic_obj->path();
		$obj_chain = array_reverse($obj_chain);

		$path = array();
		$path[] = $this->_get_fp_link(array(
			"id" => $oid,
			"group" => $args["request"]["group"],
			"name" => $args["obj_inst"]->name(),
		));

		$stop = false;
		foreach($obj_chain as $o)
		{
			if ($stop)
			{
				continue;
			};
			if ($o->id() == $topic_obj->id())
			{
				// this creates the link back to the front page
				// of the topic and stops processing
				$name = html::href(array(
					"url" => $this->mk_my_orb("change",array(
						"id" => $oid,
						"group" => $args["request"]["group"],
						"section" => $this->rel_id,
						"folder" => $o->id(),
						"_alias" => get_class($this),
					)),
					"caption" => $o->name(),
				));
				$stop = true;
			}
			else
			{
				// this is used for all other levels
				$name = html::href(array(
					"url" => $this->mk_my_orb("change",array(
						"id" => $oid,
						"c" => $key,
						"group" => $args["request"]["group"],
						"section" => $this->rel_id,
						"_alias" => get_class($this),
					)),
					"caption" => $o->name(),
				));


			}
			$path[] = $name;
		}

		$this->_add_style("style_topic_caption");
		$this->_add_style("style_topic_replies");
		$this->_add_style("style_topic_author");
		$this->_add_style("style_topic_last_post");
		$this->_add_style("style_forum_yah");
		$this->vars($this->style_data);

		$topics_sort_order = $args['obj_inst']->prop('topics_sort_order');
		$topics_list_params = array(
			'parent' => $topic_obj->id(),
			'class_id' => CL_MSGBOARD_TOPIC,
			'status' => object::STAT_ACTIVE
		);
		if($args["obj_inst"]->prop("activation"))
		{
			$topics_list_params["active"] = 1;
		}
		$is_sorted = true;

		if ( !empty($topics_sort_order) )
		{
			// if topics can be sorted via object_list, then we are going to do it:
			switch ( $topics_sort_order )
			{
				case TOPICS_SORT_ORDER_ALPHABET:
					$topics_list_params['sort_by'] = 'objects.name ASC';
					break;
				case TOPICS_SORT_ORDER_NEWEST_TOPICS_FIRST:
					$topics_list_params['sort_by'] = 'objects.created DESC';
					break;
				case TOPICS_SORT_ORDER_OBJ_ORD:
					$topics_list_params['sort_by'] = 'objects.jrk ASC';
					break;
				default:
					// if topics list can't be sorted via object_list, then we mark, that topics are not sorted:
					$is_sorted = false;
			}
		}
		else
		{
			// if the topics sort order is not set at all, then by default we sort it by creation time:
			$topics_list_params['sort_by'] = 'objects.created DESC';
		}

		$topics_ol = new object_list($topics_list_params);
		if (!$is_sorted)
		{
			$topics_ol->sort_by(array(
				"prop" => "jrk",
				"order" => "asc"
			));
		}
		$topics_list_ids = $topics_ol->ids();

		list($comment_counts, ) = $this->get_comment_counts(array('parents' => $topics_list_ids));

		// some kind of age check
		$age_check = false;
		$c_date = 0;
		$user_id = aw_global_get("uid_oid");
		if(!empty($user_id))
		{
			$user_obj = obj($user_id);
			$u_date = $user_obj->meta("topic_age");
			if(is_array($u_date))
			{
				if(!empty($u_date[$oid]))
				{
					$c_date = strtotime("-".$u_date[$oid]." days");
					$age_check = true;
				}
			}
		}

		$topics_list = array();
		foreach ($topics_ol->arr() as $topic)
		{
			$topic_oid = $topic->id();
			$topic_name = $topic->name();
			/*if($args["obj_inst"]->prop("activation") && !$topic->prop("active"))
			{
				continue;
			}*/
			// data of latest comment:
			$last_comment = $this->get_last_comments(array('parents' => array($topic_oid)));
			if ( $age_check === true && $last_comment['created'] < $c_date )
			{
				continue;
			}
			$jrk = $topic->prop("jrk");
			$text = $args["obj_inst"]->meta("text_".$jrk);
			$icon = $args["obj_inst"]->meta("icon_".$jrk);
			$ii = get_instance(CL_IMAGE);
			$icon = $ii->get_url_by_id($icon);
			$topics_list[$topic_oid] = array(
				'name' => ( 1 == $topic->prop('locked') ) ? '[L] '.htmlspecialchars($topic->name()) : htmlspecialchars($topic->name()),
				'author' => htmlspecialchars($topic->prop('author_name')),
				'comment_count' => (int)$comment_counts[$topic_oid],
				'last_date' => ( empty($last_comment['created']) ) ? $topic->created() : $last_comment['created'],
				'last_createdby' => htmlspecialchars($last_comment['uname']),
				'topic_id' => $topic_oid,
				'jrk' => $jrk,
				'jrk_text' => $text,
				'icon_url' => $icon,
			);
		}

		// if the topics list is marked not sorted, then we have to sort it now:
		if ($is_sorted === false)
		{
			switch ($topics_sort_order)
			{
				case TOPICS_SORT_ORDER_NEWEST_COMMENTS_FIRST:
					uasort($topics_list, array($this, '__sort_topics_newest_comments_first'));
					break;
				case TOPICS_SORT_ORDER_MOST_COMMENTED_FIRST:
					uasort($topics_list, array($this, '__sort_topics_most_commented_first'));
					break;

			}
		}

		$c = $pager = "";

		$tcount = sizeof($topics_list_ids);
		$num_pages = (int)(($tcount / $topics_on_page) + 1);
		$selpage = (int)$args["request"]["page"];
		if ($selpage == 0)
		{
			$selpage = 1;
		};
		if ($selpage > $num_pages)
		{
			$selpage = $num_pages;
		};

		$from = ($selpage - 1) * $topics_on_page + 1;
		$to = $from + $topics_on_page - 1;
		$cnt = 0;


		// each topic can have its own ACL (I highly doubt that this is ever going
		// to happen though) and DELETE_ACTION subtemplate is parsed only if any of
		// the topics can actually be deleted
		$delete_action = false;
		$section = aw_global_get("section");
		$can_admin = $this->_can_admin(array("forum_id" => $args["obj_inst"]->id()));


		foreach($topics_list as $topic)
		{
			$cnt++;
			if(!between($cnt, $from, $to))
			{
				continue;
			};

			$topic['last_date'] = ( !empty($topic['last_date']) ) ? $this->time2date($topic['last_date'], 2) : '';

			$topic['open_topic_url'] = $this->mk_my_orb("change",array(
				'id' => $oid,
				'group' => $args['request']['group'],
				'topic' => $topic['topic_id'],
				'section' => $section,
				'_alias' => get_class($this),
			));

			$this->vars($topic);
			$tmp = $this->parse("ICON");
			$this->vars(array("ICON" => $tmp));

			$del = "";
			if ($can_admin && $this->can("delete", $topic['topic_id']))
			{
				$delete_action = true;

				// add_faq_url - it is the matter of template to actually show the link or not
				$this->vars(array(
					"add_faq_url" => $this->mk_my_orb("add_faq", array(
						"topic" => $st_oid,
						"id" => $oid,
						"section" => $section,
					)),
				));
				$del = $this->parse("ADMIN_BLOCK");
			}

			$this->vars(array(
				"ADMIN_BLOCK" => $del,
			));

			$c .= $this->parse("SUBTOPIC");
			if ($cnt % 2 == 0)
			{
				$c .= $this->parse("SUBTOPIC_EVEN");
			}
			else
			{
				$c .= $this->parse("SUBTOPIC_ODD");
			}
		}

		$page_count = 0;

		// draw pager
		for ($i = 1; $i <= $num_pages; $i++)
		{
			$page_count++;
			$this->vars(array(
				"num" => $i,
				"url" => $this->mk_my_orb("change",array(
					"id" => $oid,
						"folder" => $topic_obj->id(),
						"page" => $i,
						"group" => $args["request"]["group"],
						"section" => $section,
						"_alias" => get_class($this),
				)),
			));
			$pager .= $this->parse($selpage == $i ? "active_page" : "page");
		};

		if ($this->is_template("PAGER") && $page_count > 1)
		{
			$this->vars(array(
				"active_page" => $pager,
			));
			$pager = $this->parse("PAGER");
			$this->vars(array(
				"PAGER" => $pager,
			));
			$pager = "";
		};


		$this->vars(array(
			"SUBTOPIC" => $c,
			"name" => $topic_obj->name(),
			"path" => join(" &gt; ",$path),
			"active_page" => $pager,
			"add_topic_url" => $this->mk_my_orb("add_topic",array(
				"id" => $oid,
				"section" => aw_global_get("section"),
				"folder" => $args["request"]["folder"],
				"_alias" => get_class($this),
				"in_popup" => "1"
			)),
			"search_url" => $this->mk_my_orb("search",array(
				"id" => $args["obj_inst"]->id(),
				"group" => $arr["group"],
				"section" => aw_global_get("section"),
			)),
		));
		if ($can_admin)
		{
			if ($delete_action)
			{
				$this->vars(array(
					"DELETE_ACTION" => $this->parse("DELETE_ACTION"),
				));
			};

			$this->vars(array(
				"LOCK_ACTION" => $this->parse("LOCK_ACTION"),
			));
		};
		return $this->parse();
	}

	function draw_topic($args = array())
	{
		$fld = $args["fld"];
		$this->read_template("topic.tpl");

		if (!$this->can("view", $args["request"]["topic"]))
		{
			return "";
		}
		$topic_obj = new object($args["request"]["topic"]);

		$this->_add_style("style_comment_user");
		$this->_add_style("style_comment_creator");
		$this->_add_style("style_forum_yah");
		$this->_add_style("style_comment_count");
		$this->_add_style("style_comment_time");
		$this->_add_style("style_comment_text");
		$this->vars($this->style_data);

		$this->link_max_len = ($ml = $args["obj_inst"]->prop("link_max_len")) ? $ml : $this->link_def_max_len;

		$comments_on_page = $args["obj_inst"]->prop("comments_on_page");
		if (empty($comments_on_page))
		{
			$comments_on_page = 5;
		};

		$t = get_instance(CL_COMMENT);
		$comments = $t->get_comment_list(array("parent" => $topic_obj->id()));

		$c = $pager = "";

		$tcount = sizeof($comments);
		$num_pages = (int)(($tcount / $comments_on_page));
		if ($tcount % $comments_on_page)
		{
			$num_pages++;
		}

		$selpage = (int)$args["request"]["page"];
		if ($selpage == 0)
		{
			$selpage = 1;
		}

		if ($selpage > $num_pages)
		{
			$selpage = $num_pages;
		}

		$from = ($selpage - 1) * $comments_on_page + 1;
		$to = $from + $comments_on_page - 1;
		$cnt = 0;

		$oid = $args["obj_inst"]->id();

		$can_delete = $this->_can_admin(array("forum_id" => $oid));
		if (is_array($comments))
		{
			foreach($comments as $comment)
			{
				$cnt++;
				if (!between($cnt,$from,$to))
				{
					continue;
				}

				$changed = "";
				$change_link = "";
				$ca = $this->_can_admin(array(
					"forum_id" => $oid,
					"uid" => aw_global_get("uid")
				));
				if ($args["obj_inst"]->prop("comments_editable") || $ca)
				{
					if ($comment["created"] < $comment["modified"])
					{
						$this->vars(array(
							"modified" => $this->time2date($comment["modified"], 2)
						));
						$changed = $this->parse("CHANGED");
					}

					if (aw_global_get("uid") === $comment["createdby"] || $ca)
					{
						$url = $this->mk_my_orb("edit_post", array(
							"section" => aw_global_get("section"),
							"id" => $oid,
							"post" => $comment["oid"],
							"ru" => get_ru(),
						));
						$this->vars(array(
							"change_url" => $url,
						));
						$change_link = $this->parse("CHANGE_LINK");
					}
				}
				$pdata = $this->get_user_data($comment["createdby"]);
				if($pdata)
				{
					foreach($pdata as $var => $val)
					{
						if($val)
						{
							$this->vars(array($var => $val));
							$tmp = $this->parse("C".strtoupper($var));
							$this->vars(array("C".strtoupper($var) => $tmp));
						}
					}
				}
				$this->vars(array(
					"id" => $comment["oid"],
					"name" => htmlspecialchars($comment["name"]),
					"commtext" => $this->_filter_output($comment["commtext"]),
					"date" => $this->time2date($comment["created"],2),
					"createdby" => htmlspecialchars($comment["createdby"]),
					"uname" => htmlspecialchars($comment["uname"]),
					"pname" => htmlspecialchars($comment["pname"]),
					"uemail" => $comment["uemail"],
					"ip" => $comment["ip"],
					"comment_image1" => $this->get_image_tag(array("id" => $comment['oid'])),
					"ADMIN_POST" => "",
					"HAS_EMAIL" => "",
					"HAS_NOT_EMAIL" => "",
					"IMAGE" => '',
					"CHANGED" => $changed,
					"CHANGE_LINK" => $change_link,
				));

				// if there is set an email
				if (empty($comment['uemail']))
				{
					$this->vars(array(
						"HAS_NOT_EMAIL" => $this->parse("HAS_NOT_EMAIL"),
					));
				}
				else
				{
					$this->vars(array(
						"HAS_EMAIL" => $this->parse("HAS_EMAIL"),
					));
				}
				$group_picture = $this->_get_group_image_for_user($comment['createdby']);
				if ( $group_picture )
				{
					$image_inst  = get_instance(CL_IMAGE);
					$this->vars(array(
						'image_url' => $image_inst->get_url_by_id($group_picture->id()),
					));
					$this->vars(array(
						'IMAGE' => $this->parse('IMAGE')
					));
				}
				// have to check if the comment creator is admin or not
				if ($this->_can_admin(array(
					"forum_id" => $oid,
					"uid" => $comment['createdby'],
				)))
				{
					$this->vars(array(
						"ADMIN_POST" => $this->parse("ADMIN_POST"),
					));
				}

				if ($can_delete)
				{
					$this->vars(array(
						"ADMIN_BLOCK" => $this->parse("ADMIN_BLOCK"),
					));
				};

				$c .= $this->parse("COMMENT");
				if ($cnt % 2 == 0)
				{
					$c .= $this->parse("COMMENT_EVEN");
				}
				else
				{
					$c .= $this->parse("COMMENT_ODD");
				}
			};
		};

		$section = aw_global_get("section");

		// draw pager
		for ($i = 1; $i <= $num_pages; $i++)
		{
			$this->vars(array(
				"num" => $i,
				"url" => $this->mk_my_orb("change",array(
					"id" => $oid,
					"topic" => $topic_obj->id(),
					"page" => $i,
					"group" => $args["request"]["group"],
					"section" => $section,
					"_alias" => get_class($this),
				)),
			));
			$pager .= $this->parse($selpage == $i ? "active_page" : "page");
		};

		// path drawing starts
		$path = array();
		$fld = $topic_obj->parent();
		$obj_chain = array_reverse($topic_obj->path());

		$show = true;
		foreach($obj_chain as $_to)
		{

			if ($_to->id() == $section)
			{
				$show = false;
			}

			if ($_to->id() == $args["obj_inst"]->prop("topic_folder"))
			{
				$show = false;
			};

			if (!$show)
			{
				continue;
			}

			$obj = $_to;
			$clid = $obj->class_id();
			if ($clid == CL_MENU)
			{
				$name = html::href(array(
					"url" => $this->mk_my_orb("change",array(
						"id" => $oid,
						"group" => $args["request"]["group"],
						"folder" => $obj->id(),
						"section" => $section,
						"_alias" => get_class($this),
					)),
					"caption" => htmlspecialchars($obj->name()),
				));
			}
			elseif ($clid == CL_MSGBOARD_TOPIC)
			{
				$name = html::href(array(
					"url" => $this->mk_my_orb("change",array(
						"id" => $oid,
						"group" => $args["request"]["group"],
						"topic" => $_to->id(),
						"section" => $section,
						"_alias" => get_class($this),
					)),
					"caption" => htmlspecialchars($obj->name()),
				));
			};


			//};
			array_unshift($path,$name);
			//$path[] = $name;
		};

		$fp = $this->_get_fp_link(array(
			"id" => $oid,
			"group" => $args["request"]["group"],
			"name" => $args["obj_inst"]->name(),
		));

		array_unshift($path,$fp);

		// path drawing ends .. sucks
		$this->vars(array(
			"ADMIN_TOPIC" => "",
			"IMAGE" => '',
		));

		$topic_creator = $topic_obj->createdby();
		$group_picture = $this->_get_group_image_for_user($topic_creator);
		if ( $group_picture )
		{
			$image_inst  = get_instance(CL_IMAGE);
			$this->vars(array(
				'image_url' => $image_inst->get_url_by_id($group_picture->id()),
			));
			$this->vars(array(
				'IMAGE' => $this->parse('IMAGE')
			));

		}

		if ($this->_can_admin(array(
			"forum_id" => $oid,
			"uid" => $topic_creator
		)))
		{
			$this->vars(array(
				"ADMIN_TOPIC" => $this->parse("ADMIN_TOPIC"),
			));
		}

		$changed = "";
		$change_link = "";

		$ca = $this->_can_admin(array(
			"forum_id" => $oid,
			"uid" => aw_global_get("uid")
		));

		if ($args["obj_inst"]->prop("topics_editable") || $ca)
		{
			if ($topic_obj->created() < $topic_obj->modified())
			{
				$this->vars(array(
					"modified" => $this->time2date($topic_obj->modified(), 2)
				));
				$changed = $this->parse("CHANGED");
			}

			if (aw_global_get("uid") === $topic_obj->createdby() || $ca)
			{
				$url = $this->mk_my_orb("edit_post", array(
					"post" => $topic_obj->id(),
					"id" => $oid,
					"ru" => get_ru(),
				));
				$this->vars(array(
					"change_url" => $url,
				));
				$change_link = $this->parse("CHANGE_LINK");
			}
		}

		$pdata = $this->get_user_data($topic_obj->createdby());
		if($pdata)
		{
			foreach($pdata as $var => $val)
			{
				if($val)
				{
					$this->vars(array($var => $val));
					$tmp = $this->parse(strtoupper($var));
					$this->vars(array(strtoupper($var) => $tmp));
				}
			}
		}
		$this->vars(array(
			"active_page" => $pager,
			"name" => htmlspecialchars($topic_obj->name()),
			"createdby" => htmlspecialchars($topic_obj->prop("author_name")),
			"author_email" => $topic_obj->prop("author_email"),
			"date" => $this->time2date($topic_obj->created(),2),
			"comment" => $this->_filter_output($topic_obj->comment()),
			"topic_image1" => $this->get_image_tag(array("id" => $topic_obj->id())),
			"topic_ip" => $topic_obj->prop("ip"),
			"COMMENT" => $c,
			"path" => join(" &gt; ",$path),
			"CHANGED" => $changed,
			"CHANGE_LINK" => $change_link,
		));

		if ($num_pages > 1)
		{
			$this->vars(array(
				"PAGER" => $this->parse("PAGER"),
			));
		};

		if ($can_delete)
		{
			$this->vars(array(
				"DELETE_ACTION" => $this->parse("DELETE_ACTION"),
			));
		};

		$rv = $this->parse();

		if (0 == $topic_obj->prop("locked"))
		{
			aw_session_set('no_cache', 1);
			$this->read_template("add_comment.tpl");
			$this->reforb_action = "submit_comment";
			$this->_add_style("style_form_caption");
			$this->_add_style("style_form_text");
			$this->_add_style("style_form_element");
			$this->vars($this->style_data);
			//return $rv . $this->parse();

			$retval = array();


			if (false === strpos(aw_global_get("REQUEST_URI"),"class="))
			{
				$embedded = true;
			}

			if ($embedded)
			{
				$retval["_alias"] = array(
					"type" => "hidden",
					"name" => "_alias",
					"value" => 1,
				);
			};

			$uid = aw_global_get("uid");
			$add = "";

			if(!empty($uid))
			{
				$uid_oid = users::get_oid_for_uid($uid);
				$user_obj = new object($uid_oid);
				$cl_users = get_instance(CL_USER);
				$p_oid = $cl_users->get_person_for_user($user_obj);

				if ($this->can("view", $p_oid))
				{
					$p_o = obj($p_oid);
					$pname = $p_o->name();
				}
				else
				{
					$pname = $uid;
				}
				if($args["obj_inst"]->prop("post_name"))
				{
					$this->vars(array("author" => $pname));
				}
				else
				{
					$this->vars(array("author" => $uid));
				}
				$this->vars(array(
					"author_pname" => $pname,
					"author_emaili" => $user_obj->prop("email"),
				));
			}

			// if user tries to add comment, and he has an error during the submitting
			// then this one here should keep the data user already submitted and
			// puts it back into comment form --dragut

			$this->vars(array(
				'title' => '',
				'commtext' => ''
			));
			if ( !empty( $_SESSION['forum_comment_error']['submit_values'] ) )
			{
				$this->vars($_SESSION['forum_comment_error']['submit_values']);
			}

			if ($this->obj_inst->prop("show_logged") == 1)
			{
				$add = "_logged";
			}
			$this->vars(array(
				"a_name" => $this->parse("a_name".$add),
				"a_email" => $this->parse("a_email".$add),
			));

			if ($_SESSION['forum_comment_error'])
			{
				$error_msg = "";
				if ( $_SESSION['forum_comment_error']['verification_code'] )
				{
					$error_msg .= t('Sisestatud kontrollnumber on vale! <br />');
				}
				if ( $_SESSION['forum_comment_error']['name'] )
				{
					$error_msg .= t('Pealkirja v&auml;li peab olema t&auml;idetud! <br />');
				}
				if ( $_SESSION['forum_comment_error']['author'] )
				{
					$error_msg .= t('Nime v&auml;li peab olema t&auml;idetud! <br />');
				}
				if ( $_SESSION['forum_comment_error']['email'] )
				{
					$error_msg .= t('E-mail ei ole korrektne! <br />');
				}
				if ( $_SESSION['forum_comment_error']['commtext'] )
				{
					$error_msg .= t('Kommentaari v&auml;li peab olema t&auml;idetud!');
				}
				if ( $_SESSION['forum_comment_error']['ip_limit'] )
				{
					$error_msg .= t('Anon&uuml;&uuml;mselt saab postitada ainult Eesti IP-lt');
				}

				$this->vars(array(
					'error_message' => $error_msg
				));
				$this->vars(array(
					"ERROR" => $this->parse("ERROR"),
				));
				unset($_SESSION['forum_comment_error']);
			}

			if ( $args['obj_inst']->prop('show_image_upload_in_add_comment_form') == 1 )
			{
				$this->vars(array(
					'IMAGE_UPLOAD_FIELD' => $this->parse('IMAGE_UPLOAD_FIELD'),
				));
			}

			if ( $args['obj_inst']->prop('use_image_verification') )
			{
				$image_verification = $args['obj_inst']->get_first_obj_by_reltype('RELTYPE_IMAGE_VERIFICATION');
				if ( !empty($image_verification) )
				{
					$this->vars(array(
						'image_verification_url' => aw_ini_get('baseurl').'/'.$image_verification->id(),
						'image_verification_width' => $image_verification->prop('width'),
						'image_verification_height' => $image_verification->prop('height'),
					));
					$this->vars(array(
						'IMAGE_VERIFICATION' => $this->parse('IMAGE_VERIFICATION')
					));
				}
			}

			$rv .= $this->parse();
		};

		$retval["contents"] = array(
			"type" => "text",
			"name" => "contents",
			"value" => $rv,
			"no_caption" => 1,
		);
		return $retval;

	}

	function get_user_data($uid)
	{
		$ui = get_instance(CL_USER);
		$pid = $ui->get_person_for_uid($uid);
		if(is_oid($pid))
		{
			$p = obj($pid);
			$adrid = $p->prop("address");
			if(is_oid($adrid) && $this->can("view", $adrid))
			{
				$adro = obj($adrid);
				if($t = $adro->prop("linn"))
				{
					$to = obj($t);
					$result["location"] = $to->name();
				}
			}
			if(($bd = $p->prop("birthday")) > 10)
			{
				$result["age"] = floor((time() - strtotime($bd))/31556926);
			}
			if($pic = $p->prop("picture"))
			{
				$ii = get_instance(CL_IMAGE);
				$result["avatar"] = $ii->get_url_by_id($pic);
			}
		}
		return $result;
	}

	function callback_gen_add_topic($args = array())
	{
		$t = get_instance(CL_MSGBOARD_TOPIC);
		$t->init_class_base();
		$emb_group = "general";
		if ($this->event_id && $args["request"]["cb_group"])
		{
			$emb_group = $args["request"]["cb_group"];
		};
		$all_props = $t->get_property_group(array(
			"group" => $emb_group,
		));

		$t->request = $args["request"];

		$all_props[] = array("type" => "hidden","name" => "class","value" => "forum_topic");
		$all_props[] = array("type" => "hidden","name" => "action","value" => "submit");
		$all_props[] = array("type" => "hidden","name" => "group","value" => $emb_group);
		$all_props[] = array("type" => "hidden","name" => "parent","value" => $args["request"]["folder"]);

		return $t->parse_properties(array(
			"properties" => $all_props,
			"name_prefix" => "emb",
		));
	}

	function callback_gen_add_comment($args = array())
	{
		$t = get_instance(CL_COMMENT);
		$t->init_class_base();
		$emb_group = "general";
		if ($this->event_id && $args["request"]["cb_group"])
		{
			$emb_group = $args["request"]["cb_group"];
		};

		$all_props = $t->get_property_group(array(
			"group" => $emb_group,
		));

		$all_props[] = array("type" => "hidden","name" => "class","value" => "forum_comment");
		$all_props[] = array("type" => "hidden","name" => "action","value" => "submit");
		$all_props[] = array("type" => "hidden","name" => "group","value" => $emb_group);
		$all_props[] = array("type" => "hidden","name" => "parent","value" => $args["request"]["topic"]);
		return $t->parse_properties(array(
			"properties" => $all_props,
			"name_prefix" => "emb",
		));
	}

	function callback_mod_retval($args = array())
	{
		$req = $args["request"];
		if ($this->topic_id)
		{
                	$emb = $args["request"]["emb"];
			$rv_args = &$args["args"];
			$rv_args["folder"] = $emb["parent"];
			$rv_args["topic"] = $this->topic_id;
			$rv_args["group"] = "contents";
			$rv_args["page"] = $args["request"]["page"];
		}
		else
		{
			$rv_args = &$args["args"];
			if ($req["folder"])
			{
				$rv_args["folder"] = $req["folder"];
			};
			if ($req["section"])
			{
				$rv_args["_alias"] = get_class($this);
			};
		};
		if($args['args']["group"] == "search")
		{
			$args['args']['word'] = ($args['request']['word']);
			$args['args']['start'] = ($args['request']['start']);
			$args['args']['end'] = ($args['request']['end']);
			$args['args']['folder'] = ($args['request']['folder']);
			$args['args']['author'] = ($args['request']['author']);
			$args['args']['com_once'] = ($args['request']['com_once']);
			$args['args']['topcom'] = ($args['request']['topcom']);
			$args['args']['searchv'] = ($args['request']['searchv']);
		}
	}

	function callback_mod_tab(&$arr)
	{
		if($arr["id"] == "actv_settings" && !$arr["obj_inst"]->prop("activation"))
		{
			return false;
		}
		return true;
	}

	function get_topic_list($args = array())
	{
		$topic_count = $tlist = array();
		if (sizeof($args["parents"]) != 0)
		{
			$params = array(
				"parent" => $args["parents"],
				"class_id" => CL_MSGBOARD_TOPIC,
				"status" => STAT_ACTIVE,
			);
			if(is_object($args["obj_inst"]) && $args["obj_inst"]->prop("activation"))
			{
				$params["active"] = 1;
			}
			$topic_list = new object_list($params);
			foreach ($topic_list->arr() as $topic)
			{
				$parent = $topic->parent();
				$topic_count[$parent]++;
				$tlist[$parent][] = $topic->id();
			};
		};
		return array($topic_count,$tlist);
	}

	function get_comment_counts($args = array())
	{
		$comment_count = array();
		$grand_total = 0;
		if (sizeof($args["parents"]) != 0)
		{
			$q = sprintf("SELECT count(*) AS cnt,parent FROM objects WHERE parent IN (%s) AND class_id = '%d'
					AND status != 0 GROUP BY parent",join(",",$args["parents"]),CL_COMMENT);
			$this->db_query($q);
			while($row = $this->db_next())
			{
				$comment_count[$row["parent"]] = $row["cnt"];
				$grand_total += $row["cnt"];
			};
		};
		return array($comment_count,$grand_total);
	}

	function get_last_comments($args = array())
	{
		$retval = array();
		if (sizeof($args["parents"]) != 0)
		{
			// hm, but this does not work at all with multiple parents
			$q = sprintf("SELECT parent,created,createdby,forum_comments.uname as uname FROM objects LEFT JOIN forum_comments ON (objects.oid = forum_comments.id) WHERE parent IN (%s) AND class_id = '%d'
				AND status != 0 ORDER BY created DESC",join(",",$args["parents"]),CL_COMMENT);
			$this->db_query($q);
			$retval = $this->db_next();
		};
		return $retval;
	}

	function get_last_topic($parent)
	{
		$retval = array();
		if (is_oid($parent))
		{
			$q = "SELECT forum_topics.author_name as uname,created,createdby FROM objects LEFT JOIN forum_topics ON (objects.oid = forum_topics.aw_oid) WHERE parent=" . $parent . " AND class_id = " . CL_MSGBOARD_TOPIC . " AND status != 0 ORDER BY created DESC LIMIT 1";
			$this->db_query($q);
			$retval = $this->db_next();
		}
		return $retval;
	}

	function _add_style($name)
	{
		// this right now takes data from the currently loaded object
		if (is_object($this->style_donor_obj))
		{
			$st_data = $this->style_donor_obj->prop($name);
		}
		else
		{
			$st_data = $this->obj_inst->prop($name);
		}

		if ($st_data)
		{
			active_page_data::add_site_css_style($st_data);
			$this->style_data[$name] = "st" . $st_data;
		}
	}

	function get_topic_selector($arr)
	{
		$topic_folder = $arr["obj_inst"]->prop("topic_folder");
		//$depth = $arr["obj_inst"]->prop("topic_folder");
		$depth = $topic_folder;
		$this->rv = "";

		$ot = new object_tree(array(
			   "parent" => $topic_folder,
			   "class_id" => CL_MENU,
		));

		$this->ot = $ot;

		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "spacer",
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Teema"),
		));

		$t->define_field(array(
			"name" => "exclude",
			"caption" => t("J&auml;ta v&auml;lja"),
			"align" => "center",
			"width" => 100,
		));

		$t->define_field(array(
			"name" => "exclude_subs",
			"caption" => t("k.a. alamkaustad"),
			"align" => "center",
			"width" => 100,
		));

		$this->t = &$t;

		$this->exclude = $arr["obj_inst"]->meta("exclude");
		$this->exclude_subs = $arr["obj_inst"]->meta("exclude_subs");

		$this->_do_rec_topic(array(
			"parent" => $topic_folder,
		));

		return $this->parse();
	}


	// so now, how do I do the consolidation?
	function _do_rec_topic($arr)
	{
		static $level = 0;
		$litems = $this->ot->level($arr["parent"]);
		foreach($litems as $item)
		{
			$id = $item->id();
			$this->t->define_data(array(
				"name" => $item->name(),
				"spacer" => str_repeat("&nbsp;",$level*3),
				"exclude" => html::checkbox(array(
					"name" => "exclude[${id}]",
					"checked" => $this->exclude[$id],
				)),
				"exclude_subs" => html::checkbox(array(
					"name" => "exclude_subs[${id}]",
					"checked" => $this->exclude_subs[$id],
				)),
			));
			$level++;
			$this->_do_rec_topic(array("parent" => $id));
			$level--;
		};
	}

	function callback_mod_reforb($arr,$request)
	{
		if (!empty($this->reforb_action))
		{
			$arr["action"] = $this->reforb_action;
		};
		if (isset($request['page']) && is_numeric($request['page']))
		{
			$arr["page"] = $request["page"];
		};
		if (isset($request['folder']) && is_oid($request['folder']))
		{
			$arr["folder"] = $request["folder"];
		};
	}

	function update_topic_selector($arr)
	{
		$arr["obj_inst"]->set_meta("exclude",$arr["request"]["exclude"]);
		$arr["obj_inst"]->set_meta("exclude_subs",$arr["request"]["exclude_subs"]);
	}

        function request_execute($o)
        {
                return $this->parse_alias(array(
			"id" => $o->id(),
			"req_args" => array(
				"action" => $_REQUEST["change"],
				"group" => $_REQUEST["group"],
			),
			"alias" => array(
				"target" => $o->id(),
			),
		));
        }

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($args)
	{
		extract($args);
		$this->classconfig = array(
			"hide_tabs" => 1,
			"relationmgr" => false,
		);
		$this->inst->embedded = true;
		$this->embedded = true;

		// XXX: temporary workaround to make embedded forum work correctly
		parse_str(aw_global_get("REQUEST_URI"),$req_args);
		$act = isset($req_args["action"]) ? $req_args["action"] : "change";
		$group = isset($req_args["group"]) ? $req_args["group"] : "contents";


		if (method_exists($this, $act))
		{
			$args = array(
				"id" => $alias["target"],
				"action" => $act,
				"rel_id" => $args["alias"]["relobj_id"],
				"folder" => $req_args["folder"],
				"topic" => $req_args["topic"],
				"page" => $req_args["page"],
				"c" => $req_args["c"],
				"cb_part" => 1,
				"form_embedded" => 1,
				"fxt" => 1,
				"group" => $group,
			);
			return $this->$act($args);
		}
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		extract($arr);
		$ob = new object($id);

		$this->classconfig = array(
			"hide_tabs" => 1,
			"relationmgr" => false,
		);

		$this->read_template('show.tpl');

		$this->vars(array(
			'name' => $ob->name(),
		));

		return $this->parse();
	}


	/**
	@attrib name=edit_post all_args=1 nologin=1
	**/
	function edit_post($arr)
	{
		$uid = aw_global_get("uid");
		if(is_oid($arr["id"]) && is_oid($arr["post"]) && $uid)
		{
			$this->obj_inst = obj($arr["id"]);
			$topic = obj($arr["post"]);
			$t_edit = ($topic->class_id()==CL_MSGBOARD_TOPIC)? $this->obj_inst->prop("topics_editable"): $this->obj_inst->prop("comments_editable");
			$ca = $this->_can_admin(array("uid" => $uid, "forum_id" => $arr["id"]));
			if(($t_edit && aw_global_get("uid") == $topic->createdby()) || $ca)
			{

				$htmlc = null;

				if ($topic->class_id() == CL_MSGBOARD_TOPIC && $this->read_site_template('edit_topic.tpl', 1))
				{
					$htmlc = get_instance("cfg/htmlclient",array("tpldir" => "forum", "template" => "edit_topic.tpl"));
				}

				if ($topic->class_id() == CL_COMMENT && $this->read_site_template('edit_comment.tpl', 1))
				{
					$htmlc = get_instance("cfg/htmlclient",array("tpldir" => "forum", "template" => "edit_comment.tpl"));
				}

				if ($htmlc == null)
				{
					$htmlc = get_instance("cfg/htmlclient",array("template" => "webform.tpl"));
				}

				$htmlc->start_output();

				$htmlc->add_property(array(
					"name" => "caption",
					"caption" => ($topic->class_id()==CL_MSGBOARD_TOPIC)?t("Teema muutmine"):t("Kommentaari muutmine"),
					"type" => "text",
					"subtitle" => 1,
				));

				$cfgu = get_instance("cfg/cfgutils");
				$props = $cfgu->load_class_properties(array(
					"clid" => ($topic->class_id()==CL_MSGBOARD_TOPIC)?CL_MSGBOARD_TOPIC:CL_COMMENT,
				));
				if($topic->class_id()==CL_MSGBOARD_TOPIC)
				{
					$use_props[0] = "name";
					if($ca)
					{
						$use_props[] = "active";
						$props['active']["options"] = array(1 => "Jah", 0 => "Ei");
						$props['active']["value"] = $topic->prop("active");
						$use_props[] = "jrk";
						$props['jrk']['value'] = $topic->prop("jrk");
					}
					$use_props[] = "author_name";
					$use_props[] = "author_email";
					$use_props[] = "comment";
					$use_props[] = "answers_to_mail";
					$props['author_email']['value'] = $topic->prop("author_email");

					$props['author_name']['value'] = $topic->prop("author_name");
					$props['author_name']['type'] = "text";
					$props['author_email']['type'] = "text";

					$props["name"]["value"] = $topic->name();
					$props["answers_to_mail"]["value"] = $topic->prop("answers_to_mail");
					$props["comment"]["value"] = $topic->comment();
				}
				else
				{
					$use_props = array("name","uname","uemail","commtext");
					$props["commtext"]["value"] = $topic->prop("commtext");
					$props["name"]["value"] = $topic->name();
					$props["uemail"]["value"] = $topic->prop("uemail");
					$props["uemail"]["type"] = "text";
					$props["uname"]["value"] = $topic->prop("uname");
					$props["uname"]["type"] = "text";
					$htmlc->vars(array(
						'commtext' => $topic->prop('commtext'),
						'name' => $topic->name(),
						'uemail' => $topic->prop('uemail'),
						'uname' => $topic->prop('uname'),
						));
				}
				$cb_values = aw_global_get("cb_values");
				aw_session_del("cb_values");

				foreach($use_props as $key)
				{
					$propdata = $props[$key];
					if (isset($cb_values[$key]["error"]))
					{
						$propdata["error"] = $cb_values[$key]["error"];
					};
					if (isset($cb_values[$key]["value"]))
					{
						$propdata["value"] = $cb_values[$key]["value"];
					};
					$htmlc->add_property($propdata);
				};

				if ($this->obj_inst->prop("show_image_upload_in_add_topic_form"))
				{
					$htmlc->add_property(array(
						"name" => "uimage",
						"caption" => t("Pilt"),
						"type" => "fileupload",
					));
				}
				aw_session_set('no_cache', 1);

				$htmlc->add_property(array(
					"name" => "sbt",
					"caption" => t("Muuda"),
					"type" => "submit",
				));

				$class = aw_global_get("class");
				// XXX: are we embedded? I know, this sucks :(
				$form_handler = "";
				if (empty($_GET["class"]))
				{
					$form_handler = aw_ini_get("baseurl") . "/" . aw_global_get("section");
				};

				$htmlc->finish_output(array("data" => array(
						"class" => get_class($this),
						"section" => aw_global_get("section"),
						"action" =>"submit_post_edit",
						"id" => $arr["id"],
						"post" => $arr["post"],
						"ru" => $arr["ru"],
					),
					"form_handler" => $form_handler,
				));

				$html = $htmlc->get_result(array(
					"form_only" => 1
				));

				return $html;
			}
		}
	}

	/**
		@attrib name=search all_args=1 nologin=1
	**/
	function forum_search($arr)
	{
		if(is_oid($arr["id"]) && $this->can("view", $arr["id"]))
		{
			$arr["obj_inst"] = obj($arr["id"]);
			$htmlc = get_instance("cfg/htmlclient",array("template" => "webform.tpl"));
			$htmlc->start_output();

			$props = $this->get_search_form($arr);
			foreach($props as $prop)
			{
				$htmlc->add_property($prop);
			}

			$htmlc->finish_output(array("data" => array(
					"class" => get_class($this),
					"section" => aw_global_get("section"),
					"action" =>"search_results",
					"id" => $arr["id"],
					"post" => $arr["post"],
					"ru" => $arr["ru"],
				),
				"method" => "GET",
				"form_handler" => "index.aw",
			));

			$html = $htmlc->get_result(array(
				"form_only" => 1
			));
			return $html;
		}
	}

	/**
	@attrib name=search_results all_args=1 nologin=1
	**/
	function search_results($arr)
	{
		$arr["obj_inst"] = $arr["obj_inst"]?$arr["obj_inst"]:obj($arr["id"]);


		$t = new vcl_table;

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Pealkiri"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "user",
			"caption" => t("Autor"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Aeg"),
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.y H:i"
		));
		if($arr["request"])
		{
			extract($arr["request"]);
		}
		else
		{
			extract($arr);
		}
		if($topcom == "topics")
		{
			$params["class_id"] = array(CL_MSGBOARD_TOPIC);
		}
		elseif($topcom == "comms")
		{
			$params["class_id"] = array(CL_COMMENT);
		}
		else
		{
			$params["class_id"] = array(CL_MSGBOARD_TOPIC, CL_COMMENT);
		}
		if($word)
		{
			$params["name"] = "%".$word."%";
		}
		$start = mktime($start["hour"], $start["minute"], 0, $start["month"], $start["day"], $start["year"]);
		$end = mktime($end["hour"], $end["minute"], 0, $end["month"], $end["day"], $end["year"]);
		if($start && $end)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end);
		}
		elseif($start)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_GREATER,$start);
		}
		elseif($end)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_LESS,$end);
		}
		if($folder)
		{
			foreach($folder as $f)
			{
				$folders[$f] = $f;
			}
		}
		if($arr["obj_inst"]->prop("activation"))
		{
			$params[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"active" => 1,
					"class_id" => CL_COMMENT,
				),
			));
		}
		if($author)
		{
			$params[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"author_name" => $author,
					"uname" => $author,
				),
			));
		}
		$ol = new object_list($params);
		foreach($ol->arr() as $o)
		{
			$tp = null;
			if($folder)
			{
				$ok = 0;
				$path = $o->path();
				foreach($path as $po)
				{
					if($folders[$po->id()])
						$ok = 1;
				}
				if(!$ok)
				{
					continue;
				}
			}
			if($o->class_id() == CL_COMMENT)
			{
				$tp = obj($o->parent());
				if($com_once)
				{
					if($usedcoms[$tp->id()])
					{
						continue;
					}
					else
					{
						$usedcoms[$tp->id()] = $o->id();
					}
				}
				if($arr["obj_inst"]->prop("activation") && !$tp->prop("active"))
				{
					continue;
				}
				$data["user"] = $o->prop("uname");
				$data["type"] = t("Kommentaar");
			}
			else
			{
				$data["user"] = $o->prop("author_name");
				$data["type"] = t("Teema");
			}
			$data["name"] = html::href(array(
				"url" => $this->mk_my_orb("change",array(
					"topic" => ($tp)?$tp->id():$o->id(),
					"id" => $arr["obj_inst"]->id(),
					"folder" => ($tp)?$tp->parent():$o->parent(),
					"section" => aw_global_get("section"),
					"_alias" => get_class($this),
					"group" => "contents",
				)),
				"caption" => $o->name()
			));
			$data["date"] = $o->created();
			$t->define_data($data);
		}
		$t->set_default_sortby("date");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->define_pageselector(array(
			"type" => "lb",
			"records_per_page" => 20,
			"position" => "bottom"
		));
		$html = "Otsingu tulemused: ".$t->get_html();
		return $html;
	}

	function get_search_form($arr)
	{
		$props["word"] = array(
			"type" => "textbox",
			"name" => "word",
			"caption" => t("Otsingus&otilde;na"),
			"store" => "no",
		);
		$props["start"] = array(
			"type" => "datetime_select",
			"name" => "start",
			"caption" => t("Kuup&auml;ev alates"),
			"store" => "no",
			"value" => -1
		);
		$props["end"] = array(
			"type" => "datetime_select",
			"name" => "end",
			"caption" => t("Kuup&auml;ev kuni"),
			"store" => "no",
			"value" => -1
		);
		$props["folder"] = array(
			"type" => "select",
			"multiple" => 1,
			"size" => 4,
			"name" => "folder",
			"caption" => t("Teema"),
			"store" => "no",
		);
		$props["folder"]["options"] = $this->get_search_folders($arr);
		$props["author"] = array(
			"type" => "textbox",
			"name" => "author",
			"caption" => t("Autor"),
			"store" => "no",
		);
		$props["com_once"] = array(
			"type" => "checkbox",
			"name" => "com_once",
			"caption" => t("&Uuml;he teema postitusi ei korrata"),
			"store" => "no",
		);
		$props["topcom"] = array(
			"type" => "select",
			"name" => "topcom",
			"caption" => t("Otsitakse"),
			"options" => array(
				"all" => t("K&otilde;iki"),
				"topics" => t("Teemasid"),
				"comms" => t("Kommentaare"),
			),
			"store" => "no",
		);
		$props["button"] = array(
			"type" => "submit",
			"name" => "button",
			"caption" => t("Otsi"),
			"store" => "no",
		);
		$props["searchv"] = array(
			"type" => "hidden",
			"name" => "searchv",
			"value" => 1,
			"store" => "no",
		);
		return $props;
	}

	function get_search_folders($arr)
	{
		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_TOPIC_FOLDER",
		));
		$this->search_folders = array();
		foreach($conns as $conn)
		{
			$flevel = 0;
			$folder = $conn->prop("to");
			$fo = obj($folder);
			$this->search_folders_recur($folder, $flevel);
		}
		return $this->search_folders;
	}

	function search_folders_recur($f, $flevel)
	{
		$fo = obj($f);
		for($i=0;$i<$flevel;$i++)
		{
			$slashes .= "--";
		}
		$nflevel = $flevel+1;
		$this->search_folders[$fo->id()] = $slashes.$fo->name();

		$folder_list = new object_list(array(
			"parent" => $f,
			"class_id" => CL_MENU,
			"status" => STAT_ACTIVE,
			"sort_by" => "objects.jrk ASC"
		));
		foreach($folder_list->arr() as $fo)
		{
			$this->search_folders_recur($fo->id(), $nflevel);
		}
	}

	/**
		@attrib name=add_topic params=name all_args=1 nologin=1
	**/
	function add_topic($arr)
	{
		$this->obj_inst = new object($arr["id"]);
		$arr['obj_inst'] = $this->obj_inst;

		if ($this->read_site_template('add_topic.tpl', 1))
		{
			$htmlc = get_instance("cfg/htmlclient",array("tpldir" => "forum", "template" => "add_topic.tpl"));
		}
		else
		{
			$htmlc = get_instance("cfg/htmlclient",array("template" => "webform.tpl"));
		}

		$htmlc->start_output();

		$htmlc->vars(array(
			"path" => implode(" &gt; ",$this->_get_yah_path($arr)),
		));

		$htmlc->add_property(array(
			"name" => "caption",
			"caption" => t("Uus teema"),
			"type" => "text",
			"subtitle" => 1,
		));

		$cfgu = new cfgutils();
		$props = $cfgu->load_class_properties(array(
			"clid" => CL_MSGBOARD_TOPIC,
		));

		$use_props = $this->obj_inst->prop('topic_form_fields');
		// if there is no topic fields selected, then assume that all should be displayed, it should maintain backward compatibility and in my opinion is sensible anyway --dragut@08.07.2008
		if (empty($use_props))
		{
			$use_props = array_keys($this->topic_fields);
		}

		// if user is logged in,
		$uid = aw_global_get("uid");
		if (!empty($uid))
		{
			$uid_oid = users::get_oid_for_uid($uid);
			$user_obj = new object($uid_oid);
			$props['author_email']['value'] = $user_obj->prop("email");

			$cl_users = get_instance(CL_USER);
			$p_oid = $cl_users->get_person_for_user($user_obj);

			if ($this->can("view", $p_oid))
			{
				$p_o = obj($p_oid);
				$pname = $p_o->name();
			}
			else
			{
				$pname = $uid;
			}

			$props['author_name']['value'] = $pname;

			if ($this->obj_inst->prop("show_logged") != 1)
			{
				$props['author_name']['type'] = "text";
				$props['author_email']['type'] = "text";
			}
		}

		$cb_values = aw_global_get("cb_values");
		aw_session_del("cb_values");

		foreach($use_props as $key)
		{
			$propdata = $props[$key];
			if (isset($cb_values[$key]["error"]))
			{
				$propdata["error"] = $cb_values[$key]["error"];
			};
			if (isset($cb_values[$key]["value"]))
			{
				$propdata["value"] = $cb_values[$key]["value"];
			};
			$htmlc->add_property($propdata);
		};

		if ($this->obj_inst->prop("show_image_upload_in_add_topic_form"))
		{
			$htmlc->add_property(array(
				"name" => "uimage",
				"caption" => t("Pilt"),
				"type" => "fileupload",
			));
		}
		aw_session_set('no_cache', 1);
		if ($this->obj_inst->prop('use_image_verification'))
		{
			$image_verification = $this->obj_inst->get_first_obj_by_reltype('RELTYPE_IMAGE_VERIFICATION');
			if (!empty($image_verification))
			{
				$htmlc->add_property(array(
					'name' => 'image_verification',
					'caption' => t('Kontrollnumber'),
					'type' => 'text',
					'value' => html::img(array(
						'url' => aw_ini_get('baseurl').'/'.$image_verification->id(),
						'width' => $image_verification->prop('width'),
						'height' => $image_verification->prop('height')
					)).html::textbox(array(
						'name' => 'ver_code',
						'size' => 20
					)),
					'error' => ($cb_values['image_verification']) ? $cb_values['image_verification']['error'] : ''
				));
				// if the template doesn't have the ability to display htmlclients properties
				// then lets add there the variable to be able to show image verification directly --dragut@16.10.2008
				$htmlc->vars(array(
					'image_verification_url' => aw_ini_get('baseurl').'/'.$image_verification->id(),
					'image_verification_width' => $image_verification->prop('width'),
					'image_verification_height' => $image_verification->prop('height')
				));
				$htmlc->vars(array(
					'IMAGE_VERIFICATION' => $htmlc->parse('IMAGE_VERIFICATION')
				));
			}
		}

		////
		// hack some more this topic adding method, so the custom add_topic.tpl would be more supported dragut@29.10.2008
		$uid = aw_global_get('uid');
		if(!empty($uid))
		{
			$uid_oid = users::get_oid_for_uid($uid);
			$user_obj = new object($uid_oid);
			$cl_users = get_instance(CL_USER);
			$p_oid = $cl_users->get_person_for_user($user_obj);

			if ($this->can("view", $p_oid))
			{
				$p_o = obj($p_oid);
				$pname = $p_o->name();
			}
			else
			{
				$pname = $uid;
			}
			if($this->obj_inst->prop("post_name"))
			{
				$htmlc->vars(array("author" => $pname));
			}
			else
			{
				$htmlc->vars(array("author" => $uid));
			}
			$htmlc->vars(array(
				"author_pname" => $pname,
				"author_emaili" => $user_obj->prop("email"),
			));
		}
		if ($this->obj_inst->prop("show_logged") == 1)
		{
			$add = "_logged";
		}
		$htmlc->vars(array(
			"a_name" => $htmlc->parse("a_name".$add),
			"a_email" => $htmlc->parse("a_email".$add),
		));
		// end hacking for custom add_topic.tpl

		$htmlc->add_property(array(
			"name" => "sbt",
			"caption" => t("Lisa"),
			"type" => "submit",
		));

		$class = aw_global_get("class");
		// XXX: are we embedded? I know, this sucks :(
		$form_handler = "";
		if (empty($_GET["class"]))
		{
			$form_handler = aw_ini_get("baseurl") . "/" . aw_global_get("section");
		};
		$htmlc->finish_output(array("data" => array(
				"class" => get_class($this),
				"section" => aw_global_get("section"),
				"action" => "submit_topic",
				"folder" => $arr["folder"],
				"id" => $arr["id"],
				"edit" => 1,
				"section" => aw_global_get("section"),
			),
			"form_handler" => $form_handler,
                ));
		$html = $htmlc->get_result(array(
			"form_only" => 1
		));
		return $html;
	}

	/**
		@attrib name=submit_post_edit all_args=1 nologin=1
	**/
	function submit_post_edit($arr)
	{
		if(is_oid($arr["id"]) && $this->can("view", $arr["id"]))
		{
			$obj_inst = obj($arr["id"]);
		}
		else
		{
			return $arr["ru"];
		}
		if(is_oid($arr["post"]) && $this->can("view", $arr["post"]))
		{
			$topic = obj($arr["post"]);
			if($topic->class_id()==CL_MSGBOARD_TOPIC)
			{
				$topic->set_name($arr["name"]);
				$topic->set_comment($arr["comment"]);
				$topic->set_prop("answers_to_mail",$arr["answers_to_mail"]);
				if(isset($arr["active"]))
				{
					$topic->set_prop("active", $arr["active"]);
					$topic->set_prop("jrk", $arr["jrk"]);
				}
			}
			else
			{
				$topic->set_name($arr["name"]);
				$topic->set_prop("commtext", $arr["commtext"]);
			}
			$image_inst = get_instance(CL_IMAGE);
			// figure out the images parent:
			$images_folder_id = $obj_inst->prop("images_folder");

			if (!empty($images_folder_id))
			{
				// if there is images_folder set, then put images there:
				$image_parent = $images_folder_id;
			}
			else
			{
				// else lets put it under the object where the image is added:
				$image_parent = $topic->id();
			}

			// if there is image uploaded:
			$upload_image = $image_inst->add_upload_image("uimage", $image_parent);

			if ($upload_image !== false && is_oid($topic->id()))
			{
				$con = $topic->connections_from(array("type" => "RELTYPE_FORUM_IMAGE"));
				foreach($con as $c)
				{
					$topic->disconnect(array("from" => $c->prop("to")));
				}
				$topic->connect(array(
					"to" => $upload_image['id'],
					"reltype" => "RELTYPE_FORUM_IMAGE",
				));
				$image_inst->do_apply_gal_conf(obj($upload_image['id']));
			}
			$topic->save();
		}
		return $arr["ru"];
	}


	/**
		@attrib name=submit_topic params=name all_args=1 nologin=1
	**/
	function submit_topic($arr)
	{
		$t = get_instance(CL_MSGBOARD_TOPIC);
		if(is_oid($arr["id"]) && $this->can("view", $arr["id"]))
		{
			$obj_inst = obj($arr["id"]);
			$uid = aw_global_get("uid");

			if($obj_inst->prop("show_logged") != 1 && !empty($uid))
			{
				$user = obj(aw_global_get("uid_oid"));
				if($obj_inst->prop("post_name"))
				{
					$ui = get_instance(CL_USER);
					$p = obj($ui->get_current_person());
					$arr["author_name"] = $p->name();
				}
				else
				{
					$arr["author_name"] = $uid;
				}
				$arr["author_email"] = $user->prop("email");
				// if the logged in user hasn't set his/her email
				// and it is set, that user can't change his/her data
				// then i have to make sure something goes to forum_topic
				// class in author_email field, just so it still passes
				// forum_topic's empty field checks
				$arr['author_email'] = (empty($arr['author_email'])) ? "none" : $arr['author_email'];

			}
		}

		$emb = $arr;
		$emb["parent"] = $arr["folder"];
		$emb["forum_id"] = $arr["id"];
		$arr["group"] = "contents";
		$emb["status"] = STAT_ACTIVE;
		$emb["return"] = "id";
		unset($emb["id"]);
		$emb['required_fields'] = $obj_inst->prop('required_fields');
		$this->topic_id = $t->submit($emb);

		$image_inst = get_instance(CL_IMAGE);
		// figure out the images parent:
		$images_folder_id = $obj_inst->prop("images_folder");

		if (!empty($images_folder_id))
		{
			// if there is images_folder set, then put images there:
			$image_parent = $images_folder_id;
		}
		else
		{
			// else lets put it under the object where the image is added:
			$image_parent = $this->topic_id;
		}

		// if there is image uploaded:
		$upload_image = $image_inst->add_upload_image("uimage", $image_parent);

		if ($upload_image !== false && is_oid($this->topic_id) && $this->can("view", $this->topic_id))
		{
			$topic_obj = new object($this->topic_id);
			$topic_obj->connect(array(
				"to" => $upload_image['id'],
				"reltype" => "RELTYPE_FORUM_IMAGE",
			));
			$image_inst->do_apply_gal_conf(obj($upload_image['id']));
		}

		$cb_values = $t->cb_values;
		// ma pean tagasi suunama siin
		if (is_array($cb_values) && sizeof($cb_values) > 0)
		{
			return $this->abort_action($arr);
		}
		$arr["topic"] = $this->topic_id;
		// see bloody finish_action kalab :(

		aw_session_set("no_cache", 1);

		$topic_url = $this->finish_action($arr);

		if ($obj_inst->connections_from(array("type" => "RELTYPE_EMAIL")))
		{
			$t->mail_subscribers(array(
				'forum_id' => $obj_inst->id(),
				'id' => $arr['id'],
				'message' => $arr['comment'],
				'topic_url' => $topic_url
			));
		}

		if($obj_inst->prop("activation") && $obj_inst->prop("add_admin_mail"))
		{
			$this->send_admin_mail(array(
				"forum_id" => $obj_inst->id(),
				"id" => $this->topic_id,
				"section" => $arr["section"]
			));
		}

		return $topic_url;
	}

	/** Creates a new comment object for a topic

		@attrib name=submit_comment params=name all_args="1" nologin="1"

	**/
	function submit_comment($arr)
	{

		$errors = array();

		if(is_oid($arr["id"]) && $this->can("view", $arr["id"]))
		{
			$obj_inst = obj($arr["id"]);

			// so, if image verification has to be passed, then lets make it happen:
			if ( $obj_inst->prop('use_image_verification') )
			{
				$image_verification_inst = get_instance('core/util/image_verification/image_verification');
				if ( !$image_verification_inst->validate($arr['ver_code']) )
				{
					$errors['verification_code'] = 1;
				}
			}

			$uid = aw_global_get("uid");

			if ( $obj_inst->prop('limit_anonymous_posting') && empty($uid) )
			{
				$ip_locator = get_instance('core/util/ip_locator/ip_locator');
				$country = $ip_locator->search($_SERVER['REMOTE_ADDR']);
				if ($country['country_code3'] != 'EST')
				{
					$errors['ip_limit'] = 1;
				}
			}

			if($obj_inst->prop("show_logged") != 1 && !empty($uid))
			{
				$uid_oid = users::get_oid_for_uid($uid);
				$user_obj = new object($uid_oid);

				$arr["pname"] = $user_obj->name();
				if($obj_inst->prop("post_name"))
				{
					$ui = get_instance(CL_USER);
					$p = obj($ui->get_current_person());
					$arr["uname"] = $p->name();
				}
				else
				{
					$arr["uname"] = $uid;
				}
				$arr["uemail"] = $user_obj->prop("email");
			}
		}

                $req_f = $obj_inst->prop("required_fields");
                $conv = array(
                        "name" => "name",
                        "commtext" => "commtext",
                        "uname" => "author",
                        "uemail" => "email",
                );
                foreach($req_f as $field)
                {
                        if ($conv[$field] == 'email')
                        {
                                if(isset($arr[$field]) && !is_email($arr[$field]))
                                {
                                        $errors[$conv[$field]] = 1;
                                }
                        }
                        else
                        {
                                if(isset($arr[$field]) && empty($arr[$field]))
                                {
                                        $errors[$conv[$field]] = 1;
                                }
                        }
                }

		if ( !empty($errors) )
		{
			$_SESSION['forum_comment_error'] = $errors;
			$_SESSION['forum_comment_error']['submit_values'] = array(
				'title' => $arr['name'],
				'commtext' => $arr['commtext'],
				'author' => $arr['uname'],
				'author_email' => $arr['uemail']
			);
			return $this->finish_action($arr);
		}

		$t = get_instance(CL_COMMENT);
		$topic = get_instance(CL_MSGBOARD_TOPIC);
		$image_inst = get_instance(CL_IMAGE);

		$emb = $arr;
		$t->id_only = true;
		unset($emb["id"]);
		$emb["parent"] = $arr["topic"];
		$emb["status"] = STAT_ACTIVE;
		if (!$this->can("add", $emb["parent"]))
		{
			aw_session_set("no_cache", 1);
			return $this->finish_action($arr);
		}
		$this->comm_id = $t->submit($emb);
		// figure out the images parent:
		$images_folder_id = $obj_inst->prop("images_folder");
		if (!empty($images_folder_id))
		{
			// if there is image_folder set, then put images there
			$image_parent = $images_folder_id;
		}
		else
		{
			// else lets put it under the object where the image is added:
			$image_parent = $this->comm_id;
		}
		// if there is image which should be uploaded
		$upload_image = $image_inst->add_upload_image("uimage", $image_parent);
		if ($upload_image !== false && is_oid($this->comm_id) && $this->can("view", $this->comm_id))
		{

			$comment_obj = new object($this->comm_id);
			$comment_obj->connect(array(
				"to" => $upload_image['id'],
				"reltype" => "RELTYPE_FORUM_IMAGE",
			));
			$image_inst->do_apply_gal_conf(obj($upload_image['id']));
		}

		$return_url = $this->finish_action($arr);

		$topic->mail_subscribers(array(
			"id" => $arr["topic"],
			"message" => $arr["commtext"],
			"title" => $arr["name"],
			"forum_id" => $arr["id"],
			"topic_url" => $return_url,
		));
		return $return_url;
	}

	/**
		@attrib name=delete_comments

	**/
	function delete_comments($arr)
	{
		// _can_admin requires reltypes defined in class header, creating an instance
		// of the object loads them
		$forum_obj = new object($arr["id"]);
		if ($this->_can_admin(array("forum_id" => $arr["id"])) && sizeof($arr["del"]) > 0)
		{
			$to_delete = new object_list(array(
				"oid" => $arr["del"],
				"parent" => $arr["topic"],
				"class_id" => CL_COMMENT,
			));

			$to_delete->delete();
		};
		return $this->finish_action($arr);
	}

	/**
		@attrib name=change params=name all_args="1" nologin="1"

		@param id optional type=int
		@param group optional
		@param period optional
		@param alias_to optional
		@param return_url optional

                @returns

                @comment

	**/
	function change($arr)
	{
		if (!is_admin())
		{
			$arr["fxt"] = 1;
			$arr["group"] = "contents";
		}
		return parent::change($arr);
	}

	function send_admin_mail($arr)
	{
		$forum_obj = &obj($arr["forum_id"]);
		$topic_obj = &obj($arr["id"]);

		if($forum_obj->prop("a_mail_subject"))
		{
			$subject = $forum_obj->prop("a_mail_subject");
		}
		else
		{
			$subject = $topic_obj->name();
		}

		if($forum_obj->prop("a_mail_address") || $forum_obj->prop("a_mail_from"))
		{
			$from = "From:".$forum_obj->prop("a_mail_from")."<".$forum_obj->prop("a_mail_address").">\n";
		}
		else
		{
			$from = "From: automatweb@automatweb.com\n";
		}

		// composing the message:
		$message = $forum_obj->prop("a_mail_body");
		$message = str_replace("[link]", $this->mk_my_orb("edit_post", array(
			"id" => $arr["forum_id"],
			"post" => $arr["id"],
			"ru" => aw_ini_get("baseurl")."/section=".$arr["section"]."&alias=forum_v2&action=change&id=".$arr["forum_id"]."&group=contents",
		)),$message);
		$message = str_replace("[name]", $topic_obj->name(),$message);
		$message = str_replace("[author]", $topic_obj->prop("author_name"),$message);
		$targets = array();
		$c = new connection();
		$conns = $c->find(array(
			"from" => $arr['forum_id'],
			"type" => 5
		));
		foreach($conns as $c)
		{
			$adm_obj = obj($c["to"]);
			if($adm_obj->class_id()==CL_USER)
			{
				$targets[] = $adm_obj;
			}
			else
			{
				$gi = get_instance(CL_GROUP);
				$targets = $gi->get_group_members($adm_obj);
			}
		}
		foreach($targets as $target)
		{
			send_mail($target->prop("email"),$subject,$message,$from);
		};
	}


	/**
		@comment
			checks whether the remote user can admin the forum
	**/
	function _can_admin($arr)
	{
		// admin can be either CL_USER or CL_GROUP, check for both
		// checking if uid comes through function params ($arr)
		// if it doesn't, then use logged in user
		if (!isset($arr['uid']))
		{
			$uid_oid = aw_global_get("uid_oid");
			$gids = aw_global_get("gidlist_oid");

		}
		else
		if (!empty($arr['uid']))
		{
			$uid_oid = users::get_oid_for_uid($arr['uid']);
			$user_inst = get_instance(CL_USER);
			$user_groups = $user_inst->get_groups_for_user($arr['uid']);
			if (!$user_groups)
			{
				$gids = array();
			}
			else
			{
				$gids = $this->make_keys($user_groups->ids());
			}

		}

		if (empty($uid_oid) || empty($arr['forum_id']))
		{
			return false;
		}

		$check_ids = array($uid_oid) + $gids;
		$c = new connection();
		$conns = $c->find(array(
			"from" => $arr['forum_id'],
			"to" => $check_ids,
			"type" => 5 //RELTYPE_FORUM_ADMIN,
		));

		return sizeof($conns) > 0;

	}


	function on_connect_menu($arr)
	{
		$conn = &$arr["connection"];
		if ($conn->prop("reltype") == 1) //RELTYPE_TOPIC_FOLDER
		{
			// now I need to grant certian privileges
			$group = obj(group::get_non_logged_in_group());

			$target_object = new object($conn->prop("to"));
			$target_object->acl_set($group, array("can_add" => 1, "can_view" => 1));
			$target_object->save();
		}
	}

	function _filter_output($text)
	{
		$text = htmlspecialchars($text);
		if (false !== strpos($text,"#php#"))
		{
			$text = preg_replace("/(#php#)(.+?)(#\/php#)/esm","highlight_string(stripslashes('<'.'?php'.'\$2'.'?'.'>'),true)",$text);
		};

		if(!$this->link_max_len)
		{
			$text = create_links($text);
		}
		else
		{
			$text = preg_replace("/((\W|^))((http(s?):\/\/)|(www\.))([^\s\)]+)/ime", "'\\2<a href=\"http\\5://\\6\\7\" target=\"_blank\">'.substr('\\4\\6\\7', 0, \$this->link_max_len).((strlen('\\4\\6\\7')>\$this->link_max_len)?'...':'').'</a>'", $text);
			if (!aw_ini_get("menuedit.protect_emails"))
			{
				$text = preg_replace("/([\w*|\.|\-]*?)@([\w*|\.]*?)/imsU","<a href='mailto:$1@$2'>$1@$2</a>",$text);
			}
		}
		$text = preg_replace("/\r([^<])/m","<br />\n\$1",$text);
		//$text = nl2br($text);
		return $text;
	}

	function callback_post_save($arr)
	{
		if ($arr["request"]["new"])
		{
			// create folders and set props
			$topic_folder = obj();
			$topic_folder->set_parent($arr["obj_inst"]->parent());
			$topic_folder->set_name($arr["obj_inst"]->name().t(" teemade kaust"));
			$topic_folder->set_class_id(CL_MENU);
			$topic_folder->save();
			$arr["obj_inst"]->set_prop("topic_folder", $topic_folder->id());

			$address_folder = obj();
			$address_folder->set_parent($arr["obj_inst"]->parent());
			$address_folder->set_name($arr["obj_inst"]->name().t(" aadresside kaust"));
			$address_folder->set_class_id(CL_MENU);
			$address_folder->save();
			$arr["obj_inst"]->set_prop("address_folder", $address_folder->id());

			$arr["obj_inst"]->save();
		}
	}

	/**
		@attrib name=add_faq nologin=1
		@param id required type="int" acl="edit"
		@param topic required type="int" acl="edit"
		@param section optional
	**/
	function add_faq($arr)
	{

		$forum_obj = new object($arr['id']);
		$faq_folder_id = $forum_obj->prop("faq_folder");
		if (!empty($faq_folder_id))
		{

			$topic_obj = new object($arr['topic']);

			$comment_inst = get_instance(CL_COMMENT);
			$comments = $comment_inst->get_comment_list(array("parent" => $topic_obj->id()));
			$comments_str = "";
			foreach ($comments as $comment)
			{
				$comments_str .= $comment['name']."<br />\n";
				$comments_str .= "-------------------------------------------------------<br />\n";
				$comments_str .= $comment['commtext']."<br /><br />\n\n";
			}

			$faq_document = new object();
			$faq_document->set_class_id(CL_DOCUMENT);
			$faq_document->set_parent($faq_folder_id);
			$topic_obj_name = $topic_obj->name();
			$faq_document->set_name($topic_obj_name);
			$faq_document->set_status(STAT_ACTIVE);
			$faq_document->set_prop("title", $topic_obj_name);
			$faq_document->set_prop("lead", $this->_filter_output($topic_obj->comment()));
			$faq_document->set_prop("content", $this->_filter_output($comments_str));
			$faq_document->save();

		}
		return $this->mk_my_orb("change", array(
				"id" => $forum_obj->id(),
				"section" => $arr['section'],
				"group" => "contents",
				"_alias" => get_class($this),
			)
		);
	}

	function get_image_tag($arr)
	{
		$retval = "";
		if ( is_oid($arr['id']) && $this->can("view", $arr['id']) )
		{
			$obj = new object($arr['id']);
			$image_obj = $obj->get_first_obj_by_reltype("RELTYPE_FORUM_IMAGE");
			if (!empty($image_obj))
			{
				$image_inst = get_instance(CL_IMAGE);
				$retval = $image_inst->make_img_tag_wl($image_obj->id());
			}
		}
		return $retval;
	}

	function _get_group_image_for_user($username)
	{
		if ( empty($username) )
		{
			return false;
		}
		$user_inst = get_instance(CL_USER);
		$post_creator_groups = $user_inst->get_groups_for_user($username);
		if (!$post_creator_groups)
		{
			return false;
		}
		$post_creator_groups->sort_by(array(
			"prop" => "priority",
			"order" => "desc"
		));
		foreach ( $post_creator_groups->arr() as $post_creator_group )
		{
			$pic = $post_creator_group->get_first_obj_by_reltype('RELTYPE_PICTURE');
			if ( !empty($pic) )
			{
				return $pic;
			}
		}

		return false;
	}

	function __sort_topics_newest_comments_first($a, $b)
	{
		if ($a['last_date'] == $b['last_date'])
		{
			return 0;
		}
		return ( $a['last_date'] < $b['last_date'] ) ? 1 : -1;
	}

	function __sort_topics_most_commented_first($a, $b)
	{
		if ($a['comment_count'] == $b['comment_count'])
		{
			return 0;
		}
		return ( $a['comment_count'] < $b['comment_count'] ) ? 1 : -1;
	}

	function scs_get_search_results($arr)
	{
		extract($arr);
		$forum = obj($group);
		$params["class_id"] = array(CL_MSGBOARD_TOPIC, CL_COMMENT);
		$params[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => array(
				"comment" => "%".$str."%",
				"commtext" => "%".$str."%",
				"name" => "%".$str."%",
			),
		));
		if($forum->prop("activation"))
		{
			$params[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"active" => 1,
					"class_id" => CL_COMMENT,
				),
			));
		}
		$start = $date["from"];
		$end = $date["to"];
		if($start > 0 && $end > 0)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end);
		}
		elseif($start > 0)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_GREATER,$start);
		}
		elseif($end > 0)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_LESS,$end);
		}
		$parents = array_keys($this->get_search_folders(array("obj_inst" => $forum)));
		$params[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"parent" => $parents,
					"class_id" => CL_COMMENT,
				),
			));
		$params["site_id"] = array();
		$params["lang_id"] = array();
		$ol = new object_list($params);
		$grpcfg = $obj->meta("grpcfg");
		$sorder = $grpcfg["sorder"][$group];
		$i = get_instance(CL_SITE_SEARCH_CONTENT);
		switch($sorder)
		{
			case S_ORD_TIME:
				$sb = array(
					"prop" => "created",
					"order" => "desc",
				);
				break;
			case S_ORD_TIME_ASC:
				$sb = array(
					"prop" => "created",
					"order" => "asc",
				);
				break;
			default:
				$sb = array(
					"prop" => "name",
					"order" => "asc",
				);
				break;
		}
		$ol->sort_by($sb);

		foreach($ol->arr() as $o)
		{
			if($o->class_id() == CL_COMMENT)
			{
				$tp = obj($o->parent());
				if($forum->prop("activation") && !$tp->prop("active"))
				{
					continue;
				}
				$path = $o->path();
				$cont = 1;
				foreach($path as $po)
				{
					if(array_search($po->id(), $parents))
					{
						$cont = 0;
						break;
					}
				}
				if($cont)
				{
					continue;
				}
			}
			$ids[$o->id()] = $o->id();
		}
		return $ids;
	}

	function scs_display_search_results($arr)
	{
		$this->read_template("scs_results.tpl");
		$ob = obj($arr["group"]);
		$conn = $ob->connections_to(array(
			"type" => 0,
			"from.class_id" => CL_DOCUMENT
		));
		foreach($conn as $c)
		{
			$section = $c->prop("from");
		}
		if(count($arr["results"]))
		{
			$tmp = "";
			foreach($arr["results"] as $oid)
			{
				$o = obj($oid);
				$add = "";
				$tp = null;
				if($o->class_id() == CL_COMMENT)
				{
					$tp = obj($o->parent());
					$add = "#c".$o->id();
				}
				$url = $this->mk_my_orb("change",array(
					"topic" => ($tp)?$tp->id():$o->id(),
					"id" => $ob->id(),
					"folder" => ($tp)?$tp->parent():$o->parent(),
					"section" => $section,
					"_alias" => get_class($this),
					"group" => "contents",
				));
				$url .= $add;
				$name = $o->name()?$o->name():t("Nimetu");
				$this->vars(array(
					"link" => $url,
					"name" => $name,
				));
				$tmp .= $this->parse("ROW");
			}
			$this->vars(array(
				"ROW" => $tmp,
			));
		}
		return $this->parse();
	}

	function _get_yah_path($args)
	{
		$path = array();
		$path[] = $this->_get_fp_link(array(
			"id" => $oid,
			"group" => $args["request"]["group"],
			"name" => $args["obj_inst"]->name(),
		));

		$stop = false;
		foreach($obj_chain as $o)
		{
			if ($stop)
			{
				continue;
			};
			if ($o->id() == $topic_obj->id())
			{
				// this creates the link back to the front page
				// of the topic and stops processing
				$name = html::href(array(
					"url" => $this->mk_my_orb("change",array(
						"id" => $oid,
						"group" => $args["request"]["group"],
						"section" => $this->rel_id,
						"folder" => $o->id(),
						"_alias" => get_class($this),
					)),
					"caption" => $o->name(),
				));
				$stop = true;
			}
			else
			{
				// this is used for all other levels
				$name = html::href(array(
					"url" => $this->mk_my_orb("change",array(
						"id" => $oid,
						"c" => $key,
						"group" => $args["request"]["group"],
						"section" => $this->rel_id,
						"_alias" => get_class($this),
					)),
					"caption" => $o->name(),
				));


			}
			$path[] = $name;
		}

		return $path;
	}
}
