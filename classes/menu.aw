<?php

// menu.aw - adding/editing/saving menus and related functions

/*
	// stuff that goes into the objects table
	@default table=objects

	@classinfo trans=1

	@groupinfo general_sub caption="&Uuml;ldine" parent=general

		@property name type=textbox rel=1 trans=1 group=general_sub
		@caption Nimi
		@comment Objekti nimi

		@property comment type=textbox group=general_sub
		@caption Kommentaar
		@comment Vabas vormis tekst objekti kohta

		@property status type=status trans=1 default=1 group=general_sub
		@caption Aktiivne
		@comment Kas objekt on aktiivne

		@property status_recursive type=checkbox ch_value=1 default=0 group=general_sub store=no editonly=1
		@caption Aktiveeri/deaktiveeri ka alamkaustad
		@comment Suure hulga alamkaustade olemasolul on see operatsioon ajamahukas

		@property alias_ch type=checkbox ch_value=1 default=1 group=general_sub field=meta method=serialize
		@caption Genereeri alias automaatselt

		@property alias type=textbox group=general_sub
		@caption Alias

		@property jrk type=textbox size=4 group=general_sub
		@caption Jrk


	@groupinfo advanced_settings caption="S&uuml;vaseaded" parent=general

		@property type type=select group=advanced_settings table=menu field=type
		@caption Men&uuml;&uuml; t&uuml;&uuml;p

		@property pmethod_properties type=callback callback=callback_get_pmethod_options group=advanced_settings store=no
		@caption Avaliku meetodi seaded

		@property admin_feature warning=0 type=select group=advanced_settings table=menu field=admin_feature
		@caption Vali programm

		@property add_tree_conf type=relpicker reltype=RELTYPE_ADD_TREE_CONF field=meta method=serialize group=advanced_settings
		@caption Objekti lisamise puu konff

		@property cfgmanager type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize group=advanced_settings
		@caption Konfiguratsioonivorm

		@property default_image_folder type=relpicker reltype=RELTYPE_DEFAULT_IMAGE_FOLDER field=meta method=serialize group=advanced_settings
		@caption Vaikimisi piltide kataloog

		@property short_alias type=checkbox ch_value=1 field=meta method=serialize group=advanced_settings
		@caption Struktuurist s&otilde;ltumatu alias

		@property default_image_folder_is_inherited type=checkbox ch_value=1 field=meta method=serialize group=advanced_settings
		@caption Vaikimisi piltide kataloog p&auml;ritav

		@property change_time type=select field=meta method=serialize group=advanced_settings
		@caption Sisu uuendamise sagedus

		@property change_pri type=textbox field=meta method=serialize group=advanced_settings
		@caption Sisu t&auml;htsus (0-1.0)

	@groupinfo import_export caption=Eksport submit=no parent=general

		@property no_export type=checkbox group=import_export ch_value=1 field=meta method=serialize table=objects
		@caption &Auml;ra n&auml;ita ekspordis

		@property export type=callback callback=callback_get_export_options group=import_export store=no
		@caption Eksport


	@groupinfo users caption="Kasutajad" parent=general

		@property users_only type=checkbox field=meta method=serialize group=users ch_value=1
		@caption Ainult sisselogitud kasutajatele

	@groupinfo stats caption="Statistika" parent=general

		@property stats_from type=date_select field=meta method=serialize group=stats default=-1
		@caption Alates

		@property stats_to type=date_select field=meta method=serialize group=stats default=-1
		@caption Kuni

		@property stats_disp type=text field=meta method=serialize group=stats
		@caption Vaatamisi ja muutmisi


@groupinfo show caption=N&auml;itamine

	@groupinfo show_sub caption="N&auml;itamine" parent=show


		@property link_behaviour type=chooser store=no multiple=1 group=show_sub
		@caption Lingi iseloom

		@property target type=checkbox group=show_sub ch_value=1 search=1 table=menu
		@caption Uues aknas

		@property clickable type=checkbox group=show_sub ch_value=1 default=1 table=menu
		@caption Klikitav


		@property link type=textbox group=show_sub table=menu
		@caption Men&uuml;&uuml; link


		@property show_restrictions type=chooser store=no multiple=1 group=show_sub
		@caption N&auml;itamine

		@property frontpage type=checkbox table=objects field=meta method=serialize group=show_sub ch_value=1
		@caption Esilehel

		@property mid type=checkbox group=show_sub ch_value=1 table=menu
		@caption Paremal


		@property show_conditions type=chooser store=no multiple=1 group=show_sub
		@caption Tingimused

		@property hide_noact type=checkbox ch_value=1 group=show_sub table=menu
		@caption Peida &auml;ra, kui selle kausta all aktiivseid dokumente ei ole

		@property no_menus type=checkbox group=show_sub ch_value=1 table=menu
		@caption Ilma men&uuml;&uuml;deta


		@property panes type=chooser store=no multiple=1 group=show_sub
		@caption Paanid

		@property left_pane type=checkbox  ch_value=1 default=1 group=show_sub table=menu
		@caption Vasak paan

		@property right_pane type=checkbox ch_value=1 default=1 group=show_sub table=menu
		@caption Parem paan


		@property width type=textbox size=5 group=show_sub table=menu
		@caption Laius

	@groupinfo doc_show caption="Dokumentide kuvamine" parent=show

		@property show_lead type=checkbox field=meta method=serialize group=doc_show ch_value=1
		@caption N&auml;ita ainult leadi

		@property sort_by_name type=checkbox field=meta method=serialize group=doc_show ch_value=1
		@caption Sorteeri nime j&auml;rgi

	@groupinfo doc_ord  caption="Dokumentide j&auml;rjestamine" parent=show

		@property sorter type=table group=doc_ord table=menu
		@caption Dokumentide j&auml;rjestamine

		property sort_by type=select table=objects field=meta method=serialize group=doc_ord
		caption Dokumente j&auml;rjestatakse

		property sort_ord type=select table=objects field=meta method=serialize group=doc_ord

		@property doc_ord_apply_to_admin type=checkbox table=objects field=meta method=serialize group=doc_ord
		@caption Kehtib ka adminis

	@groupinfo ip caption="IP piirangud" parent=show

		@property ip type=table store=no group=ip no_caption=1

@groupinfo look caption="V&auml;limus"

	@groupinfo look_sub caption="V&auml;limus" parent=look

		@property color type=colorpicker field=meta method=serialize group=look_sub
		@caption Men&uuml;&uuml; v&auml;rv

		@property color2 type=colorpicker field=meta method=serialize group=look_sub
		@caption Men&uuml;&uuml; v&auml;rv 2

		@property icon type=icon field=meta method=serialize group=look_sub
		@caption Ikoon

		@property sel_icon type=relpicker reltype=RELTYPE_ICON table=objects field=meta method=serialize group=look_sub
		@caption Vali ikoon

	@groupinfo templates caption=Kujundusp&otilde;hjad parent=look

		@property tpl_dir table=objects type=select field=meta method=serialize group=templates
		@caption Template set

		@property tpl_dir_applies_to_all table=objects type=checkbox ch_value=1 field=meta method=serialize group=templates
		@caption Kasuta template setist k&otilde;iki templatesid

		@property show_lead_template type=select field=meta method=serialize group=templates
		@caption Leadi template

		@property tpl_view type=select group=templates table=menu
		@caption Template dokumendi n&auml;itamiseks (pikk)

		@property tpl_view_no_inherit type=checkbox group=templates table=menu ch_value=1
		@caption Ei ole p&auml;ritav

		@property tpl_lead type=select group=templates table=menu
		@caption Template dokumendi n&auml;itamiseks (l&uuml;hike)

		@property tpl_lead_no_inherit type=checkbox group=templates table=menu ch_value=1
		@caption Ei ole p&auml;ritav

		@property show_layout type=relpicker reltype=RELTYPE_SHOW_AS_LAYOUT group=templates field=meta method=serialize table=objects
		@caption Kasuta n&auml;itamiseks layouti

	@groupinfo presentation caption=Pildid parent=look

		@property images_from_menu type=relpicker reltype=RELTYPE_PICTURES_MENU group=presentation field=meta method=serialize
		@caption V&otilde;ta pildid men&uuml;&uuml; alt

		@property img_timing type=textbox size=3 field=meta method=serialize group=presentation
		@caption Viivitus piltide vahel (sek.)

		@property imgrelmanager type=relmanager reltype=RELTYPE_IMAGE store=no group=presentation
		@caption Vali pilte

		@property img_act type=relpicker reltype=RELTYPE_IMAGE field=meta method=serialize group=presentation
		@caption Aktiivse men&uuml;&uuml; pilt

		@property menu_images type=table field=meta method=serialize group=presentation store=no
		@caption Men&uuml;&uuml; pildid

@groupinfo menus caption="Sisu seaded"

	@groupinfo menus_sub caption="Sisu seaded" parent=menus

		@property submenus_from_obj type=relpicker reltype=RELTYPE_SUBMENUS table=objects  field=meta method=serialize group=menus_sub
		@caption Alammen&uuml;&uuml;d objektist

		@property get_content_from type=relpicker reltype=RELTYPE_CONTENT_FROM field=meta method=serialize group=menus_sub
		@caption Sisu objektist

		@property submenus_from_cb type=checkbox ch_value=1 group=menus_sub field=meta method=serialize default=0
		@caption Alammen&uuml;&uuml;d objekti AW liidesest

		@property submenus_from_menu type=relpicker reltype=RELTYPE_SHOW_SUBFOLDERS_MENU group=menus_sub table=objects field=meta method=serialize table=objects
		@caption V&otilde;ta alammen&uuml;&uuml;d men&uuml;&uuml; alt

		@property show_object_tree type=relpicker reltype=RELTYPE_OBJ_TREE group=menus_sub field=meta method=serialize table=objects
		@caption Kasuta alammen&uuml;&uuml;de n&auml;itamiseks objektide nimekirja

		@property use_target_audience type=checkbox ch_value=1 group=menus_sub field=meta method=serialize table=objects
		@caption Kasuta sihtr&uuml;hmap&otilde;hist kuvamist

		@property select_target_audience type=relpicker multiple=1 reltype=RELTYPE_TARGET_AUDIENCE group=menus_sub store=connect field=meta method=serialize table=objects
		@caption Vali sihtr&uuml;hmad

		@property content_all_langs type=checkbox ch_value=1 group=menus_sub field=meta method=serialize table=objects
		@caption Sisu k&otilde;ikidest keeltest

		@property set_doc_content_type type=chooser group=menus_sub table=menu field=set_doc_content_type
		@caption M&auml;&auml;ra sisu t&uuml;&uuml;p

	@groupinfo advanced_ctx caption=Kontekst parent=menus

		@property has_ctx type=checkbox ch_value=1 table=objects field=meta method=serialize group=advanced_ctx
		@caption Kuva alamkaustu kontekstip&otilde;hiselt

		@property default_ctx type=select table=objects field=meta method=serialize group=advanced_ctx
		@caption Default kontekst

		@property ctx type=releditor reltype=RELTYPE_CTX field=meta method=serialize mode=manager props=name,status table_fields=name,status table_edit_fields=name,status group=advanced_ctx
		@caption Kontekstid

	@groupinfo relations caption="Vaata lisaks" parent=menus

		@property sa_manager type=relmanager reltype=RELTYPE_SEEALSO group=relations store=no
		@caption Seosehaldur

		@property seealso type=table group=relations store=no
		@caption Men&uuml;&uuml;d, mille all see men&uuml;&uuml; on "vaata lisaks" men&uuml;&uuml;
		@comment Nende men&uuml;&uuml;de lisamine ja eemaldamine k&auml;ib l&auml;bi seostehalduri

		@property seealso_order type=textbox group=relations size=3 table=objects field=meta method=serialize
		@caption J&auml;rjekorranumber (vaata lisaks)

	@groupinfo brothers caption=Vennastamine parent=menus

		@property sections type=table store=no group=brothers
		@caption Vennad

	@groupinfo docs_from caption="Sisu asukoht" parent=menus

		@property sss_tb type=toolbar store=no no_caption=1 group=docs_from

		@property ndocs type=textbox size=3 group=doc_show,docs_from table=menu
		@caption Mitu viimast dokumenti

		@property sss type=table store=no group=docs_from no_caption=1
		@caption Men&uuml;&uuml;d, mille alt viimased dokumendid v&otilde;etakse

		@property sss_exclude type=table store=no group=docs_from no_caption=1
		@caption &Auml;ra v&otilde;ta dokumente men&uuml;&uuml; alt

	@groupinfo seealso_docs caption="Vaata lisaks dokumendid" parent=menus

		@property seealso_docs_tb type=toolbar group=seealso_docs no_caption=1 store=no
		@caption Vaatalisaks dokumendid toolbar

		@property seealso_docs_t type=table group=seealso_docs no_caption=1 table=menu
		@caption Vaatalisaks dokumendid

	@groupinfo periods caption="Perioodid" parent=menus

		@property periodic type=checkbox group=periods ch_value=1
		@caption Perioodiline

		@property pers type=relpicker multiple=1 size=5 table=objects field=meta method=serialize group=periods reltype=RELTYPE_PERIOD
		@caption Perioodid, mille alt dokumendid v&otilde;etakse

		@property all_pers type=checkbox ch_value=1 table=objects field=meta method=serialize group=periods
		@caption K&otilde;ikide perioodide alt

		@property docs_per_period type=textbox size=3 group=periods table=objects field=meta method=serialize
		@caption Dokumente perioodist

		@property show_periods type=checkbox ch_value=1 group=periods table=objects field=meta method=serialize
		@caption N&auml;ita perioode

		@property show_period_count type=textbox size=4 group=periods table=objects field=meta method=serialize
		@caption Mitu viimast perioodi

	@groupinfo keywords caption=V&otilde;tmes&otilde;nad parent=menus

		@property kw_tb type=toolbar no_caption=1 store=no group=keywords

		property grkeywords type=select size=10 multiple=1 field=meta method=serialize group=keywords
		caption AW M&auml;rks&otilde;nad

		@property grkeywords2 type=keyword_selector field=meta method=serialize group=keywords reltype=RELTYPE_KEYWORD
		@caption AW M&auml;rks&otilde;nad

		@property keywords type=textbox field=meta method=serialize group=keywords
		@caption META keywords

		@property description type=textbox field=meta method=serialize group=keywords
		@caption META description

		@property page_title type=textbox field=meta method=serialize group=keywords
		@caption Lehe pealkiri


@groupinfo acl caption=&Otilde;igused
@default group=acl

	@groupinfo acl_main caption=&Otilde;igused parent=acl
	@default group=acl_main

		@property acl type=acl_manager store=no
		@caption &Otilde;igused

	@groupinfo acl_views caption=Vaatamised parent=acl
	@default group=acl_views

		@property acl_views type=table store=no no_caption=1

	@groupinfo acl_edits caption=Muutmised parent=acl
	@default group=acl_edits

		@property acl_edits type=table store=no no_caption=1

@groupinfo timing caption="Ajaline aktiivsus"
@default group=timing

	@property timing type=timing store=no
	@caption Ajaline aktiivsus

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


	@classinfo relationmgr=yes
	@classinfo objtable=menu
	@classinfo objtable_index=id
	@classinfo syslog_type=ST_MENU



	@tableinfo menu index=id master_table=objects master_index=oid

	@reltype PICTURES_MENU value=1 clid=CL_MENU
	@caption v&otilde;ta pildid men&uuml;&uuml;lt

	@reltype SHOW_SUBFOLDERS_MENU value=2 clid=CL_MENU
	@caption v&otilde;ta alamkasutad men&uuml;&uuml;lt

	@reltype SHOW_AS_CALENDAR value=3 clid=CL_PLANNER
	@caption v&otilde;ta objekte kalendrist

	@reltype SHOW_AS_LAYOUT value=4 clid=CL_LAYOUT
	@caption kasuta saidi n&auml;itamisel layouti

	@reltype SEEALSO value=5 clid=CL_MENU
	@caption vaata lisaks

	@reltype IP value=6 clid=CL_IPADDRESS
	@caption IP aadress ligip&auml;&auml;su piiramiseks

	@reltype ACL_GROUP value=7 clid=CL_GROUP
	@caption Kasutajagrupp

	@reltype OBJ_TREE value=8 clid=CL_OBJECT_TREE,CL_OBJECT_TREEVIEW_V2
	@caption objektide nimekiri

	@reltype DOCS_FROM_MENU value=9 clid=CL_MENU
	@caption v&otilde;ta dokumente men&uuml;&uuml; alt

	@reltype PERIOD value=10 clid=CL_PERIOD
	@caption v&otilde;ta dokumente perioodi alt

	@reltype ADD_TREE_CONF value=12 clid=CL_ADD_TREE_CONF
	@caption lisamise puu konfiguratsioon

	@reltype CFGFORM value=13 clid=CL_CFGFORM
	@caption konfiguratsioonivorm

	@reltype IMAGE value=14 clid=CL_IMAGE
	@caption pilt

	@reltype SUBMENUS value=16 clid=CL_SHOP_ORDER_CENTER,CL_CRM_SECTION,CL_OBJECT_TREEVIEW_V2,CL_ABSTRACT_DATASOURCE,CL_CRM_COMPANY_WEBVIEW,CL_PERSONS_WEBVIEW,CL_CFGFORM,CL_TRADEMARK_ADD,CL_PATENT_ADD,CL_UTILITY_MODEL_ADD,CL_INDUSTRIAL_DESIGN_ADD,CL_EURO_PATENT_ET_DESC_ADD
	@caption alammen&uuml;&uuml;d objektist

	@reltype CONTENT_FROM value=17 clid=CL_PROJECT
	@caption Sisu objektist

	@reltype SEEALSO_DOC value=18 clid=CL_DOCUMENT
	@caption vaata lisaks dokument

	@reltype ICON value=19 clid=CL_IMAGE
	@caption ikoon

	@reltype TIMING value=20 clid=CL_TIMING
	@caption Aeg

	@reltype CTX value=21 clid=CL_FOLDER_CONTEXT
	@caption Kontekst

	@reltype LANG_REL value=22 clid=CL_MENU
	@caption Keeleseos

	@reltype KEYWORD value=23 clid=CL_KEYWORD
	@caption V&otilde;tmes&otilde;na

	@reltype NO_DOCS_FROM_MENU value=24 clid=CL_MENU
	@caption &auml;ra v&otilde;ta dokumente men&uuml;&uuml; alt

	@reltype DEFAULT_IMAGE_FOLDER value=25 clid=CL_MENU
	@caption Vaikimisi piltide kataloog

	@reltype TARGET_AUDIENCE value=26 clid=CL_TARGET_AUDIENCE
	@caption Sihtr&uuml;hm
*/

define("IP_ALLOWED", 1);
define("IP_DENIED", 2);

class menu extends class_base implements main_subtemplate_handler
{
	var $sel_section = 0; //TODO:scope?
	
	protected $menu_images_done;

	function menu($args = array())	{
		$this->init(array(
			"tpldir" => "automatweb/menu",
			"clid" => CL_MENU,
		));

		$this->trans_props = array(
			"name", "comment","alias", "link","keywords","description","page_title"
		);
	}

	/** Generate a form for adding or changing an object
		@attrib name=new params=name all_args="1" is_public="1" caption="Lisa"
	**/
	function new_change($args)
	{
		return $this->change($args);
	}

	/**
		@attrib name=change params=name all_args="1" is_public="1" caption="Muuda"
	**/
	function change($args = array())
	{
		return parent::change($args);
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		$ob = $arr["obj_inst"];
		switch($data["name"])
		{
			case "seealso_docs_tb":
				$this->_seealso_docs_tb($arr);
				break;

			case "stats_disp":
				$m = new stats_model();
				$from = ($f = $arr["obj_inst"]->prop("stats_from")) ? $f : -1;
				$to = ($t = $arr["obj_inst"]->prop("stats_to")) ? $t : -1;
				$data["value"] = $m->get_simple_count_for_obj($arr["obj_inst"]->id(), $from, $to);
				break;

			case "sss_tb":
				$this->_get_sss_tb($arr);
				break;

			case "alias_ch":
				if(!aw_ini_get("menu.automatic_aliases"))
				{
					return PROP_IGNORE;
				}
				break;
			case "change_time":
				$arr = array(
					"always" => t("Alati muutuv"),
					"hourly" => t("Tunnis korra"),
					"daily" => t("P&auml;evas korra"),
					"weekly" => t("N&auml;dalas korra"),
					"monthly" => t("Kuus korra"),
					"yearly" => t("Aastas korra"),
					"never" => t("Ei muudeta (arhiveeritud)"),
				);
				$data["options"] = $arr;
				break;
			case "set_doc_content_type":
				$ol = new object_list(array("class_id" => CL_DOCUMENT_CONTENT_TYPE));
				$data["options"] = array("" => t("Vali t&uuml;hjaks")) + $ol->names();
				break;

			case "jrk":
				if (!empty($arr["new"]) && !empty($arr["request"]["ord_after"]))
				{
					$oa = obj($arr["request"]["ord_after"]);
					$mlp = new object_list(array(
						"class_id" => CL_MENU,
						"parent" => $oa->parent(),
						"sort_by" => "jrk"
					));
					foreach($mlp->arr() as $id => $menu)
					{
						if ($get_next)
						{
							$next_ord = $menu->ord();
							$get_next = false;
						}
						if ($id == $oa->id())
						{
							$get_next = true;
						}
					}
					if (!isset($next_ord))
					{
						$next_ord = $oa->ord() + 100;
					}
					$data["value"] = ($oa->ord() + $next_ord) / 2;
				}
				break;

			case "kw_tb":
				$this->kw_tb($arr);
				break;

			case "left_pane":
			case "right_pane":
				$retval = PROP_IGNORE;
				break;

			case "panes":
				$data["options"] = array(
					"left_pane" => t("Vasak"),
					"right_pane" => t("Parem"),
				);
				$data["value"]["left_pane"] = $ob->prop("left_pane");
				$data["value"]["right_pane"] = $ob->prop("right_pane");
				break;


			case "target":
			case "clickable":
				$retval = PROP_IGNORE;
				break;

			case "link_behaviour":
				$data["options"] = array(
					"target" => t("Uues aknas"),
					"clickable" => t("Klikitav"),
				);
				$data["value"]["target"] = $ob->prop("target");
				$data["value"]["clickable"] = $ob->prop("clickable");
				break;


			case "frontpage":
			case "mid":
				$retval = PROP_IGNORE;
				break;

			case "show_restrictions":
				$data["options"] = array(
					"frontpage" => t("Esilehel"),
					"mid" => t("Paremal"),
				);
				$data["value"]["frontpage"] = $ob->prop("frontpage");
				$data["value"]["mid"] = $ob->prop("mid");
				break;


			case "hide_noact":
			case "no_menus":
				$retval = PROP_IGNORE;
				break;

			case "show_conditions":
				$data["options"] = array(
					 "hide_noact" => t("Peida &auml;ra, kui selle kausta all aktiivseid dokumente ei ole"),
					"no_menus" => t("Ilma men&uuml;&uuml;deta"),
				);
				$data["value"]["hide_noact"] = $ob->prop("hide_noact");
				$data["value"]["no_menus"] = $ob->prop("no_menus");
				break;



			case "type":
				$data["options"] = $this->get_type_sel();
				break;

			case "tpl_lead":
				$tplmgr = new templatemgr();
				$data["options"] = $tplmgr->get_template_list(array("type" => 1, "menu" => $ob->id()));
				break;

			case "tpl_view":
				$tplmgr = new templatemgr();
				$data["options"] = $tplmgr->get_template_list(array("type" => 2, "menu" => $ob->id()));
				break;

			case "tpl_dir":
				$template_sets = aw_ini_get("menuedit.template_sets");
				$data["options"] = array_merge(array("" => t("kasuta parenti valikut")),$template_sets);
				break;

			case "sections":
				$this->get_brother_table($arr);
				break;

			case "sss":
				$this->get_sss_table($arr);
				break;

			case "sss_exclude":
				$this->get_sss_exclude_table($arr);
				break;

			case "grkeywords":
				$kwds = new keyword();
				$data["options"] = $kwds->get_keyword_picker();
				$data["selected"] = $this->get_menu_keywords($ob->id());
				break;

			case "icon":
				$ext = $this->cfg['ext'];
				if (is_oid($ob->meta("sel_icon")) && $this->can("view", $ob->meta("sel_icon")))
				{
					$fi = new image();
					$icon = html::img(array(
						"url" => $fi->get_url_by_id($ob->meta("sel_icon"))
					));
				}
				else
				if ($ob->prop("admin_feature"))
				{
					classload("core/icons");
					$icon = html::img(array(
						"url" => icons::get_feature_icon_url($ob->prop("admin_feature")),
					));
				}
				else
				{
					$icon = t("(no icon set)");
				};
				$data["value"] = $icon;
				break;

			case "admin_feature":
				// only show the program selector, if the menu has the correct type
				if ($ob->prop("type") == MN_ADMIN1)
				{
					$data["options"] = $this->get_feature_sel();
				}
				else
				{
					$retval = PROP_IGNORE;
				};
				break;

			case "sorter":
				// okey, how do I do this?
				// by default I show only one line ....
				// if something gets selected, then I'll add another..
				// salvestame metainfosse
				$sort_fields = new aw_array($arr["obj_inst"]->meta("sort_fields"));
				$sort_order = new aw_array($arr["obj_inst"]->meta("sort_order"));
				$t = &$data["vcl_inst"];
				$t->define_field(array(
					"name" => "fubar",
				));
				$t->define_field(array(
					"name" => "field",
					"caption" => t("V&auml;li"),
				));
				$t->define_field(array(
					"name" => "order",
					"caption" => t("J&auml;rjekord"),
				));

				$fields = array(
					'' => "",
					'objects.jrk' => t("J&auml;rjekorra j&auml;rgi"),
					'objects.created' => t("Loomise kuup&auml;eva j&auml;rgi"),
					'objects.modified' => t("Muutmise kuup&auml;eva j&auml;rgi"),
					'documents.modified' => t("Dokumenti kirjutatud kuup&auml;eva j&auml;rgi"),
					'planner.start' => t("Kalendris valitud aja j&auml;rgi"),
					'objects.name' => t("Nime j&auml;rgi"),
				);

				$orders = array(
					'DESC' => t("Suurem (uuem) enne"),
					'ASC' => t("V&auml;iksem (vanem) enne"),
				);

				$idx = 1;

				$morder = $arr["obj_inst"]->meta("sort_order");

				foreach($sort_fields->get() as $key => $val)
				{
					$t->define_data(array(
						"fubar" => $idx,
						"field" => html::select(array(
							"name" => "sort_fields[$idx]",
							"options" => $fields,
							"value" => $val,
						)),

						"order" => html::select(array(
							"name" => "sort_order[$idx]",
							"options" => $orders,
							"value" => $morder[$key],
						)),
					));
					$idx++;
				}

				$t->define_data(array(
					"fubar" => $idx,
					"field" => html::select(array(
						"name" => "sort_fields[$idx]",
						"options" => $fields,
					)),
					"order" => html::select(array(
						"name" => "sort_order[$idx]",
						"options" => $orders,
					)),
				));
				$t->sort_by(array("field" => "fubar","sorder" => "desc"));
				break;

			case "sort_by":
				$data['options'] = array(
					'' => "",
					'objects.jrk' => t("J&auml;rjekorra j&auml;rgi"),
					'objects.created' => t("Loomise kuup&auml;eva j&auml;rgi"),
					'objects.modified' => t("Muutmise kuup&auml;eva j&auml;rgi"),
					'documents.modified' => t("Dokumenti kirjutatud kuup&auml;eva j&auml;rgi"),
					'planner.start' => t("Kalendris valitud aja j&auml;rgi"),
					'RAND()' => t("Random"),
				);
				break;

			case "sort_ord":
				$data['options'] = array(
					'DESC' => t("Suurem (uuem) enne"),
					'ASC' => t("V&auml;iksem (vanem) enne"),
				);
				break;

			case "seealso":
				$t = &$arr["prop"]["vcl_inst"];
				$t->define_field(array(
					"name" => "id",
					"caption" => t("OID"),
					"type" => "int",
					"talign" => "center",
				));

				$t->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
				));

				$see_also_conns = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_SEEALSO",
				));

				foreach($see_also_conns as $conn)
				{
					$t->define_data(array(
						"id" => $conn->prop("to"),
						"name" => $conn->prop("to.name"),
					));
				};
				break;

			case "ip":
				$t = &$arr["prop"]["vcl_inst"];
				$t->define_field(array(
					"name" => "ip_name",
					"caption" => t("IP Nimi"),
					"sortable" => 1,
					"align" => "center"
				));
				$t->define_field(array(
					"name" => "ip",
					"caption" => t("IP Aadress"),
					"sortable" => 1,
					"align" => "center"
				));
				$t->define_field(array(
					"name" => "allowed",
					"caption" => t("Lubatud"),
					"sortable" => 0,
					"align" => "center"
				));
				$t->define_field(array(
					"name" => "denied",
					"caption" => t("Keelatud"),
					"sortable" => 0,
					"align" => "center"
				));

				$allow = $ob->meta("ip_allow");
				$deny = $ob->meta("ip_deny");

				$conn = $ob->connections_from(array(
					"type" => "RELTYPE_IP",
				));
				foreach($conn as $c)
				{
					$c_o = $c->to();

					$t->define_data(array(
						"ip_name" => $c_o->name(),
						"ip" => $c_o->prop("addr"),
						"allowed" => html::radiobutton(array(
							"name" => "ip[".$c_o->id()."]",
							"checked" => $allow[$c_o->id()] == 1,
							"value" => IP_ALLOWED
						)),
						"denied" => html::radiobutton(array(
							"name" => "ip[".$c_o->id()."]",
							"checked" => $deny[$c_o->id()] == 1,
							"value" => IP_DENIED
						))
					));
				}
				break;

			case "menu_images":
				$data["value"] = $this->_get_images_table($arr);
				break;

			case "show_lead_template":
				if ($arr["obj_inst"]->prop("show_lead") == 0)
				{
					return PROP_IGNORE;
				}
				$ol = new object_list(array(
					"class_id" => CL_CONFIG_AW_DOCUMENT_TEMPLATE,
					"type" => 1 // lead
				));
				$data["options"] = array("" => "") +$ol->names();
				break;

			case "seealso_docs_t":
				$this->_do_seealso_docs_t($arr);
				break;

			case "default_ctx":
				// gather contexts from submenus
				$ol = new object_list(array(
					"class_id" => CL_MENU,
					"parent" => $arr["obj_inst"]->id()
				));
				$opts = array("" => "");
				foreach($ol->arr() as $o)
				{
					foreach($o->connections_from(array("type" => "RELTYPE_CTX")) as $c)
					{
						$opts[$c->prop("to")] = $c->prop("to.name");
					}
				}
				$data["options"] = $opts;
				break;

			case "link":
				$this->_get_linker($data, $arr["obj_inst"]);
				break;

			case "acl_views":
				$this->_get_acl_views($arr);
				break;

			case "acl_edits":
				$this->_get_acl_edits($arr);
				break;
		};
		return $retval;
	}

	private function _get_images_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_get_images_table_cols($t);

		$cnt = aw_ini_get("menu.num_menu_images");
		$imdata = $arr["obj_inst"]->meta("menu_images");

		$imgrels = array(0 => t("Vali pilt.."));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_IMAGE")) as $conn)
		{
			$imgrels[$conn->prop("to")] = $conn->prop("to.name");
		}

		$k=0;
		for($i = 0; $i <  $cnt; $i++)
		{
			// image preview
			$url = "";
			$imi = new image();
			if (true || $imdata[$i]["image_id"])
			{
				$url = $imi->get_url_by_id($imdata[$i]["image_id"]);
				if ($url)
				{
					// kix will burn me alive for this html in here
					$url =  "<div id='preview_div_".$k."'>".html::img(array(
						"url" => $url,
						"id" => "preview_img_".$k
					));
					$url .= " <br> ".html::href(array(
						"url" => $this->mk_my_orb("change",	array("id" => $imdata[$i]["image_id"],"return_url" => get_ru()),"image"),
						"caption" => t("Muuda"),
						"target" => "_blank",
						"id" => "preview_url_".$k,
					))." </div> ";

					$rel = html::select(array(
						"name" => "img[$k]",
						"options" => $imgrels,
						"selected" => $imdata[$i]["image_id"],
						"onchange" => "menu_images_update_image (this, $k)",
					));

					$t->define_data(array(
						"nr" => "$k",
						"ord" => html::textbox(array(
							"name" => "img_ord[$k]",
							"value" => $imdata[$k]["ord"],
							"size" => 3
						)),
						"preview" => $url,
						"rel" => $rel,
						"del" => html::checkbox(array(
							"ch_value" => 1,
							"name" => "img_del[$k]"
						)),
						"up" => html::fileupload(array(
							"name" => "mimg_$k",
							"onchange" => "menu_images_add_row(this);",
						))
					));
					$k++;
				}
			}
		}

		if ($k<$cnt)
		{
			$url = "<div id='preview_div_".$k."'></div>";

			$rel = html::select(array(
				"name" => "img[$k]",
				"options" => $imgrels,
				"onchange" => "menu_images_add_row(this); menu_images_update_image (this, $k);",
			));

			$t->define_data(array(
				"nr" => "$k",
				"ord" => html::textbox(array(
					"name" => "img_ord[$k]",
					"value" => $imdata[$k]["ord"],
					"size" => 3
				)),
				"preview" => $url,
				"rel" => $rel,
				"del" => html::checkbox(array(
					"ch_value" => 1,
					"name" => "img_del[$k]"
				)),
				"up" => html::fileupload(array(
					"name" => "mimg_$k",
					"onchange" => "menu_images_add_row(this)",
				))
			));
		}
		$t->set_default_sortby("nr");
		$t->sort_by();
	}

	function callback_generate_scripts ()
	{
		$output = "";
		if ($this->use_group === "presentation")
		{
			$id = $_GET["id"];
			$menu_images_cnt = $this->menu_images_get_cnt(array("id"=>$id));

			$output = '

			var menu_images_cnt = '.$menu_images_cnt.'
			// get imgrels array for making select menu
			'.$this->menu_images_get_imgrels(array(
					"id"=>$id
			)).'

			// get connected images as js array into menu_images
			'.$this->menu_images_get(array(
					"id"=>$id,
					"cnt"=>'.$menu_images_cnt.'
			)).'

			function menu_images_add_row (that)
			{
				row_clicked = that.parentNode.parentNode.childNodes[0].innerHTML*1.0;
				if ((menu_images_cnt*1.0+1) <'.aw_ini_get("menu.num_menu_images").' && row_clicked == (menu_images_cnt*1.0)  )
				{
					menu_images_cnt++;

					$("<tr class=awmenuedittablerow>"+
						"<td align=center class=awmenuedittabletext>"+menu_images_cnt+"</td>"+
						"<td align=center><div id=preview_div_"+menu_images_cnt+"></div></td>"+
						"<td align=center class=awmenuedittabletext>"+create_select ("img["+(menu_images_cnt*1.0)+"]", imgrels)+"</td>"+
						"<td align=center><input type=file name=mimg_"+(menu_images_cnt*1.0)+" onchange=\'row_nr = this.parentNode.parentNode.childNodes[0].innerHTML*1.0;menu_images_add_row (this);\'></td>"+
					"</tr>").appendTo(that.parentNode.parentNode.parentNode);
				}
			}

			function create_select (name, options)
			{
				s_html = "<select  name=\'"+name+"\' onchange=\'row_nr = this.parentNode.parentNode.childNodes[0].innerHTML*1.0;menu_images_add_row (this);	menu_images_update_image (this, row_nr); ;\'>";

				for (key in options)
				{
					if (options[key].length > 0)
					{
						s_html += "<option value="+key+">"+options[key]+"</option>";
					}
				}
				s_html += "</select>";
				return s_html;
			}

			function menu_images_update_image (that, row_nr)
			{
					sel_img_id = that.value;
					if ( document.getElementById("preview_img_"+row_nr) )
					{
						if (sel_img_id>0)
						{
							$("#preview_div_"+row_nr).css("display", "block");
							$("#preview_img_"+row_nr).attr("src", menu_images[sel_img_id]["img"].src);
							$("#preview_url_"+row_nr).attr("href", menu_images[sel_img_id]["change_url"]);
							$("#preview_div_"+row_nr).css("height", $("#preview_img_"+row_nr).height()+20);
						}
						else if (sel_img_id == 0)
						{
							$("#preview_div_"+row_nr).css("display", "none");
						}
					}else
						{
						$("#preview_div_"+row_nr).html("");
						$("<img src="+menu_images[sel_img_id]["img"].src+" id=preview_img_"+row_nr+"><br><a id=preview_url_"+row_nr+" href="+menu_images[sel_img_id]["change_url"]+" target=_blank>'.t("Muuda").'</a>").appendTo("#preview_div_"+row_nr);
						$("#preview_div_"+row_nr).css("height", $("#preview_img_"+row_nr).height()+20);
					}
			}';
		}

		return $output;
	}

	/**
	@attrib name=ajax_menu_images_new_row
	@param menuid required type=int

	@comment
	**/
	function ajax_menu_images_new_row ($arr)
	{
		classload("vcl/table");
		$t =  new aw_table();
		$this -> _get_images_table_cols ($t);

		$menu_obj = obj($arr["menuid"]);
		$imdata = $menu_obj->meta("menu_images");

		$imgrels = array(0 => t("Vali pilt.."));
		foreach($menu_obj->connections_from(array("type" => "RELTYPE_IMAGE")) as $conn)
		{
			$imgrels[$conn->prop("to")] = $conn->prop("to.name");
		}

		$output = '

		';

		die($output);
	}

	private function menu_images_get_cnt ($arr)
	{
		classload("vcl/table");
		$t =  new aw_table();
		$this -> _get_images_table_cols ($t);

		$menu_obj = obj($arr["id"]);
		$imdata = $menu_obj->meta("menu_images");

		$i = 0;
		foreach ($imdata as $img)
		{
			if ($img["image_id"] > 0)
				$i++;
		}

		return $i;
	}

	private function menu_images_get_imgrels ($arr)
	{
		classload("vcl/table");
		$t =  new aw_table();
		$this -> _get_images_table_cols ($t);

		$menu_obj = obj($arr["id"]);
		$imdata = $menu_obj->meta("menu_images");

		$imgrels = array(0 => t("Vali pilt.."));
		foreach($menu_obj->connections_from(array("type" => "RELTYPE_IMAGE")) as $conn)
		{
			$imgrels[$conn->prop("to")] = $conn->prop("to.name");
		}

		$output = 'var imgrels = new Array();';
		foreach ($imgrels as $key => $value)
		{
			$output .= 'imgrels['.$key.'] = "'.$value.'";';
		}

		return $output;
	}

	private function menu_images_get ($arr)
	{
		classload("vcl/table");
		$t =  new aw_table();
		$this -> _get_images_table_cols ($t);

		$menu_obj = obj($arr["id"]);
		$imdata = $menu_obj->meta("menu_images");

		$imgrels = array(0 => t("Vali pilt.."));
		foreach($menu_obj->connections_from(array("type" => "RELTYPE_IMAGE")) as $conn)
		{
			$imgrels[$conn->prop("to")] = $conn->prop("to.name");
		}

		$output = 'var menu_images = new Array();';
		foreach ($imgrels as $key => $value)
		{

			if ($key>0)
			{
				$imi = new image();
				$url = $imi -> get_url_by_id ($key);

				$output .= '
				img = document.createElement ("img");
				img.src = "'.$url.'";
				menu_images['.$key.'] = new Array;
				menu_images['.$key.']["img"] = img;
				menu_images['.$key.']["change_url"] = "'.$this->mk_my_orb("change",	array("id" => $key),"image").'";
				';
			}
		}

		return  $output;
	}


	private function _get_images_table_cols(&$t)
	{
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("Pildi number"),
			"talign" => "center",
			"align" => "center",
		));
		/*$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"talign" => "center",
			"align" => "center",
		));*/
		$t->define_field(array(
			"name" => "preview",
			"caption" => t("Eelvaade"),
			"talign" => "center",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "rel",
			"caption" => t("Vali Pilt"),
			"talign" => "center",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "up",
			"caption" => t("Uploadi Pilt"),
			"talign" => "center",
			"align" => "center",
		));
/*		$t->define_field(array(
			"name" => "del",
			"caption" => t("Kustuta"),
			"talign" => "center",
			"align" => "center",
		));*/
	}

	function callback_get_export_options($arr = array())
	{
		$submenus = $this->get_menu_list(false,false,$arr["obj_inst"]->id());
		$nodes = array();
		$tmp = array(
			"type" => "select",
			"multiple" => 1,
			"size" => 15,
			"name" => "ex_menus",
			"caption" => t("Vali men&uuml;&uuml;d"),
			"options" => $submenus,
			// this selects all choices
			"selected" => array_flip($submenus),
		);
		$nodes[] = $tmp;
		$tmp = array(
			"type" => "checkbox",
			"name" => "allactive",
			"value" => 1,
			"caption" => t("M&auml;rgi k&otilde;ik men&uuml;&uuml;d aktiivseks"),
		);
		$nodes[] = $tmp;
		$tmp = array(
			"type" => "checkbox",
			"name" => "ex_icons",
			"value" => 1,
			"caption" => t("Ekspordi ikoonid"),
		);
		$nodes[] = $tmp;
		$tmp = array(
			"type" => "submit",
			"value" => t("Ekspordi"),
			"name" => "do_export",
		);
		$nodes[] = $tmp;
		return $nodes;
	}

	function callback_get_pmethod_options($arr = array())
	{
		if ($arr["obj_inst"]->prop("type") != MN_PMETHOD)
		{
			return PROP_IGNORE;
		};

		$nodes = array();

		$nodes[] = array(
			"type" => "select",
			"name" => "pclass",
			"caption" => t("Vali meetod"),
			"options" => array(),
			"selected" => $arr["obj_inst"]->meta("pclass"),
			"options" => $this->get_pmethod_sel(),
		);
		$pclass = $arr["obj_inst"]->meta("pclass");
		list($class_name, $tmp) = explode("/", $pclass);
		if($class_name == "method")
		{
			$class_id = clid_for_name($class_name);
			$nodes[] = array(
				"type" => "select",
				"name" => "pobject",
				"caption" => t("Vali meetodiga seotud objekt"),
				"selected" => $arr["obj_inst"]->meta("pobject"),
				"options" => $this->get_pobjects($class_id),
			);
		}
		$nodes[] = array(
			"type" => "checkbox",
			"name" => "pm_url_admin",
			"value" => 1,
			"caption" => t("Meetod viitab adminni"),
			"ch_value" => $arr["obj_inst"]->meta("pm_url_admin"),
		);

		$nodes[] = array(
			"type" => "checkbox",
			"name" => "pm_url_menus",
			"value" => 1,
			"caption" => t("Meetodi v&auml;ljundi kuvamisel n&auml;idatakse men&uuml;&uuml;sid"),
			"ch_value" => $arr["obj_inst"]->meta("pm_url_menus"),
		);

		$nodes[] = array(
			"type" => "textbox",
			"name" => "pm_extra_params",
			"value" => $arr["obj_inst"]->meta("pm_extra_params"),
			"caption" => t("Meetodi lisaparameetrid"),
		);

		return $nodes;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$ob = $arr["obj_inst"];
		$retval = PROP_OK;
		switch($data["name"])
		{
/*			case "submenus_from_cb":
				if($data["value"])
				{
					if($this->can("view" , $arr["obj_inst"]->prop("submenus_from_obj")))
					{
						$sub_obj = obj($arr["obj_inst"]->prop("submenus_from_obj"));
						if($sub_obj->class_id() == CL_CFGFORM) break;
					}
					$cfgo = new object();
					$cfgo->set_class_id(CL_CFGFORM);
					$cfgo->set_name($arr["obj_inst"]->name()." ".t("konfi vorm"));
					$cfgo->set_parent($arr["obj_inst"]->id());
					$cfgo->save();
					$arr["obj_inst"]->set_prop("submenus_from_obj" , $cfgo->id());
					$arr["obj_inst"]->connect(array("to"=> $cfgo->id(), "type" => "RELTYPE_SUBMENUS"));
				}
				break;*/
			case "transl":
				$this->write_trans_aliases($arr);
				$this->trans_save($arr, $this->trans_props);
				break;

			// grkeywords just triggers an action, nothing should
			// be saved into the objects table
			case "grkeywords":
				if ($ob->id())
				{
					$this->save_menu_keywords($data["value"],$ob->id());
				}
				$retval = PROP_IGNORE;
				break;

			case "icon":
				$retval = PROP_IGNORE;
				break;

			case "left_pane":
			case "right_pane":
				$retval = PROP_IGNORE;
				break;

			case "panes":
				$ob->set_prop("left_pane",isset($data["value"]["left_pane"]) ? 1 : 0);
				$ob->set_prop("right_pane",isset($data["value"]["right_pane"]) ? 1 : 0);
				break;

			case "target":
			case "clickable":
				$retval = PROP_IGNORE;
				break;

			case "link_behaviour":
				$ob->set_prop("target",isset($data["value"]["target"]) ? 1 : 0);
				$ob->set_prop("clickable",isset($data["value"]["clickable"]) ? 1 : 0);
				break;


			case "frontpage":
			case "mid":
				$retval = PROP_IGNORE;
				break;

			case "show_restrictions":
				$ob->set_prop("frontpage",isset($data["value"]["frontpage"]) ? 1 : 0);
				$ob->set_prop("mid",isset($data["value"]["mid"]) ? 1 : 0);
				break;


			case "hide_noact":
			case "no_menus":
				$retval = PROP_IGNORE;
				break;

			case "show_conditions":
				$ob->set_prop("hide_noact",isset($data["value"]["hide_noact"]) ? 1 : 0);
				$ob->set_prop("no_menus",isset($data["value"]["no_menus"]) ? 1 : 0);
				break;



			case "sections":
				$dar = new aw_array($arr["request"]["erase"]);
				foreach($dar->get() as $erase)
				{
					$e_o = obj($erase);
					$e_o->delete();
				}
				break;

			case "sss":
				$arr["obj_inst"]->set_meta("section_include_submenus",$arr["request"]["include_submenus"]);
				break;

			case "sss_exclude":
				$arr["obj_inst"]->set_meta("section_no_include_submenus",$arr["request"]["include_submenus"]);
				break;

			case "type":
				$request = &$arr["request"];
				if ($request["type"] != MN_ADMIN1)
				{
					$ob->set_prop("admin_feature",0);
				};
				if ($request["type"] != MN_PMETHOD)
				{
					$ob->set_meta("pclass","");
					$ob->set_meta("pobject", "");
					$ob->set_meta("pgroup", "");
					$ob->set_meta("pm_url_admin","");
					$ob->set_meta("pm_url_menus","");
					$ob->set_meta("pm_extra_params","");
				};
				break;

			case "menu_images":
				if (!$this->menu_images_done)
				{
					$arr["obj_inst"]->set_meta("menu_images",$this->update_menu_images(array(
						"id" => $ob->id(),
						"img_del" => $arr["request"]["img_del"],
						"img_ord" => $arr["request"]["img_ord"],
						"img" => $arr["request"]["img"],
						"meta" => $arr["obj_inst"]->meta(),
						"obj_inst" => $arr["obj_inst"]
					)));
					$this->menu_images_done = 1;
				};
				break;

			case "sorter":
				$request = &$arr["request"];
				$save_fields = array();
				$save_orders = array();
				$fields = new aw_array($request["sort_fields"]);
				$str = array();
				foreach($fields->get() as $key => $val)
				{
					if ($val)
					{
						$save_fields[] = $val;
						$str[] = $val . " " . $request["sort_order"][$key];
						$save_orders[] = $request["sort_order"][$key];
					}
				}
				$ob->set_meta("sort_fields",$save_fields);
				$ob->set_meta("sort_order",$save_orders);
				$ob->set_meta("sort_by",join(",",$str));
				$ob->set_meta("sort_ord","");
				break;

			case "pmethod_properties":
				$request = &$arr["request"];
				$ob->set_meta("pclass", $request["pclass"]);
				$ob->set_meta("pobject", $request["pobject"]);
				$ob->set_meta("pgroup", $request["pgroup"]);
				$ob->set_meta("pm_url_menus", $request["pm_url_menus"]);
				$ob->set_meta("pm_url_admin",$request["pm_url_admin"]);
				$ob->set_meta("pm_extra_params",$request["pm_extra_params"]);
				break;

			case "ip":
				$allow = array();
				$deny = array();

				$ar = new aw_array($arr["request"]["ip"]);
				foreach($ar->get() as $ipid => $ipv)
				{
					if ($ipv == IP_ALLOWED)
					{
						$allow[$ipid] = 1;
					}
					else
					if ($ipv == IP_DENIED)
					{
						$deny[$ipid] = 1;
					}
				}
				$arr["obj_inst"]->set_meta("ip_allow",$allow);
				$arr["obj_inst"]->set_meta("ip_deny",$deny);
				break;

			case "alias":
				return $this->_check_alias($arr);

			case "seealso_docs_t":
				$arr["obj_inst"]->set_meta("sad_opts", $arr["request"]["sad_opts"]);
				break;
		}
		return $retval;
	}

	private function get_object_groups($class_name)
	{
		$cfg = new cfgutils();
		$cfg->load_class_properties(array("clid" => $class_name));
		$groups = $cfg->get_groupinfo();
		//arr($groups);
		$rval = array();
		foreach($groups as $key => $value)
		{
			$rval[$key] = $value["caption"] ? $value["caption"]." ($key)" : $key;
		}
		return $rval;
	}

	private function get_pobjects($class_id)
	{
		$objects = new object_list(array(
			"class_id" => $class_id,
			"limit" => 100,
			"site_id" => aw_ini_get("site_id"),
			"lang_id" => aw_global_get("lang_id")
		));
		return array(0 => t("-- vali --")) + $objects->names();
	}

	private function update_menu_images($args = array())
	{
		extract($args);
		$imgar = $meta["menu_images"];

		// rewire the uploaded images as connected and selected
		foreach(safe_array($_FILES) as $name => $upf)
		{
			if (substr($name, 0, 4) == "mimg" && is_uploaded_file($upf["tmp_name"]))
			{
				$nm = substr($name, 5);
				$im = new image();
				$imd = $im->add_upload_image($name, $args["obj_inst"]->id(), $imgar[$nm]["image_id"]);
				$args["obj_inst"]->connect(array(
					"to" => $imd["id"],
					"type" => "RELTYPE_IMAGE"
				));
				$img[$nm] = $imd["id"];
			}
		}
		$num_menu_images = $this->cfg["num_menu_images"];
		$t = new image();

		for ($i=0; $i < $num_menu_images; $i++)
		{
			if ($img_del[$i] == 1)
			{
				unset($imgar[$i]);
			}
			else
			{
				$imgar[$i]["image_id"] = $img[$i];
				$imgar[$i]["ord"] = $img_ord[$i];
			}
		}

		$timgar = array();
		$cnt = 0;
		for ($i=0; $i < $num_menu_images; $i++)
		{
			if (true || $imgar[$i]["id"] || $imgar[$i]["ord"] || $imgar[$i]["image_id"])
			{
				$timgar[$cnt++] = $imgar[$i];
			}
		}

		// now sort the image array
		//usort($timgar,array($this,"_menu_img_cmp"));
		return $timgar;
	}

	function _menu_img_cmp($a,$b)
	{
		if ($a["ord"] == $b["ord"]) return 0;
		return ($a["ord"] < $b["ord"]) ? -1 : 1;
	}


	function callback_post_save($arr)
	{
		$request = &$arr["request"];
		if(
			("general_sub" === $this->use_group || "general" === $this->use_group || empty($this->use_group)) &&
			aw_ini_get("menu.automatic_aliases")  &&
			$arr["obj_inst"]->prop("alias_ch") == 1 &&
			!strlen($arr["obj_inst"]->alias())
		)
		{
			$arr["obj_inst"]->set_alias($this->gen_nice_alias($request["name"], $arr["obj_inst"]->id()));
			$arr["obj_inst"]->save();
		}

		if (!empty($arr["request"]["status_recursive"]))
		{
			$r = $arr["obj_inst"]->set_status_recursive();
			if (!$r)
			{
				$this->show_error_text(t("Alamkaustade staatuse muutmine eba&otilde;nnestus."));
			}
		}

		$ps = new popup_search();
		$rels = array(
			"_set_sss" => 9, /* RELTYPE_DOCS_FROM_MENU */
			"_set_no_sss" => 24, /* RELTYPE_NO_DOCS_FROM_MENU */
			"sad_s" => 18, /* RELTYPE_SEEALSO_DOCUMENT */
		);
		foreach($rels as $rel_key => $rel_type)
		{
			if(!empty($arr["request"]["_set_sss"]))
			{
				$ps->do_create_rels($arr["obj_inst"], $arr["request"][$rels_key], $rel_type);
			}
		}
	}

	function callback_pre_save($arr)
	{
		$request = &$arr["request"];
		if ("import_export" === $this->use_group)
		{
			$menu_export = new menu_export();
			$menu_export->export_menus(array(
				"id" => $arr["obj_inst"]->id(),
				"ex_menus" => $request["ex_menus"],
				"allactive" => $request["allactive"],
				"ex_icons" => $request["ex_icons"],
			));
		}

		if (isset($arr["request"]["link_pops"]) && $this->can("view", $arr["request"]["link_pops"]))
		{
			$arr["obj_inst"]->set_meta("linked_obj", $arr["request"]["link_pops"]);
		}
	}

	/** genrates a suitable alias from string
		@attrib api=1 params=pos

		@param name required type=string
			The string to generate the alias from

		@param oid optional type=oid
			The oid of the object whose name we are converting, used to check for alias uniqueness

		@comment
			Generates a string that is suitable for an url - replaces spaces and other non-ascii characters with ones suitable for an url

		@returns
			The given sring, converted so that it can be used as an object alias

	**/
	function gen_nice_alias($name, $oid = false)
	{
		$name = strtolower($name);
		$name = trim($name);
		$to_replace = array("&auml;","&ouml;","&uuml;","&otilde;", " ", "&Auml;","&Ouml;","&Uuml;","&Otilde;");
		$tmp ="";
		for($i = 0; $i < strlen($name); $i++)
		{
			if ( ord($name{$i}) == 184 ||ord($name{$i}) == 180 ) // zcaron or Zcaron
			{
				$tmp .= "z";
			}
			else if (ord($name{$i}) == 168 ||ord($name{$i}) == 166) // scaron or Zcaron
			{
				$tmp .= "s";
			}
			else if (ord($name{$i}) == 233 || ord($name{$i}) == 201 || ord($name{$i}) == 235)//e with acute
			{
				$tmp .= "e";
			}
			else
			{
				$tmp .= $name[$i];
			}
		}
		$name = $tmp;
		$replace_with = array("a","o","u","o","-","a","o","u","o");
		$str = "!\"@#.¤$%&/()[]={}?\+-`'|,;:";
		$name = str_replace(preg_split("//", $str, -1 , PREG_SPLIT_NO_EMPTY), "", $name);
		$name = str_replace($to_replace, $replace_with, htmlentities($name, ENT_QUOTES, aw_global_get("charset")));
		$name = str_replace("--", "-", $name);
		return $this->_check_alias_name(strtolower(substr($name,0, 50)), $oid);
	}

	private function _check_alias_name($name, $oid)
	{
		$nr = 0;
		$orig_name = $name;
		while(true)
		{
			$filt = array();
			$filt["alias"] = $name;
			if(aw_ini_get("menuedit.recursive_aliases") && is_oid($oid))
			{
				$o = obj($oid);
				$filt["parent"] = $o->parent();
			}
			$ol = new object_list($filt);
			if($ol->count() > 0)
			{
				$name = $orig_name."_".$nr;
				$nr++;
			}
			else
			{
				break;
			}
		}
		return $name;
	}

	////
	// !tagastab array adminni featuuridest, mida sobib ette s88ta aw_template->picker funxioonile
	private function get_feature_sel()
	{
		$ret = array("0" => t("--vali--"));
		$prog = aw_ini_get("programs");
		reset($prog);
		while (list($id,$v) = each($prog))
		{
			// only show stuff with names
			if ($v["name"])
			{
				$ret[$id] = $v["name"];
			};
		}
		return $ret;
	}

	////
	// !Tagastab nimekirja avalikest meetodidest. Arvatavasti tuleb see anyway ymber kirjutada,
	// sest kui neid meetodeid saab olema palju, siis on neid sitt selectist valida
	private function get_pmethod_sel()
	{
		$orb = new orb();
		return array("0" => t("--vali--")) + $orb->get_classes_by_interface(array("interface" => "public"));
	}

	/** Returns a list of keywords for the menu and all its submenus
		@attrib api=1 params=pos

		@param id required type=oid
			The id of the menu to return keywords for

		@returns
			array { keyword_id => keyword_id }
	**/
	function get_menu_keywords($id)
	{
		// get menu and all submenus and all kw rels from those
		$ot = new object_tree(array(
			"class_id" => CL_MENU,
			"parent" => $id,
			"status" => STAT_ACTIVE,
			"site_id" => aw_ini_get("site_id"),
			"lang_id" => aw_global_get("lang_id")
		));
		$ids = $ot->ids();
		$ids[] = $id;

		$c = new connection();
		$conns = $c->find(array(
			"from" => $ids,
			"from.class_id" => CL_MENU,
			"type" => "RELTYPE_KEYWORD"
		));
		$ret = array();
		foreach($conns as $con)
		{
			$ret[$con["to"]] = $con["to"];
		}
		return $ret;
	}

	private function save_menu_keywords($keywords,$id)
	{
		$old_kwds = $this->get_menu_keywords($id);
		if (is_array($keywords))
		{
			// check if the kwywords have actually changed - if not, we souldn't do this, as this
			// can be quite time-consuming
			$update = false;
			foreach($keywords as $koid)
			{
				if ($old_kwds[$koid] != $koid)
				{
					$update = true;
				}
			}

			if (count($old_kwds) != count($keywords))
			{
				$update = true;
			}

			if (!$update)
			{
				return;
			}
		}
		else
		{
			if (count($old_kwds) < 1)
			{
				return;
			}
		}
		$this->db_query("DELETE FROM keyword2menu WHERE menu_id = $id");

		if (is_array($keywords))
		{
			$has_kwd_rels = 1;
			foreach($keywords as $koid)
			{
				$this->db_query("INSERT INTO keyword2menu (menu_id,keyword_id) VALUES('$id','$koid')");
			}
		}
		else
		{
			$has_kwd_rels = 0;
		};

		$tmp = obj($id);
		$tmp->set_meta("has_kwd_rels",$has_kwd_rels);
		$tmp->save();
	}


	function callback_on_submit_relation_list($args)
	{
		$obj =& obj($args["id"]);
		$co = $obj->connections_from(array(
			"type" => "RELTYPE_IP",
		));

		$_allow = $obj->meta("ip_allow");
		$_deny = $obj->meta("ip_deny");

		$lut = array();
		foreach($co as $c)
		{
			$lut[$c->prop("to")] = $c->prop("to");
		}

		$allow = array();
		$deny = array();
		foreach($allow as $ipa => $one)
		{
			if (isset($lut[$ipa]))
			{
				$allow[$ipa] = $one;
			}
		}
		foreach($deny as $ipa => $one)
		{
			if (isset($lut[$ipa]))
			{
				$deny[$ipa] = $one;
			}
		}

		$obj->set_meta("ip_allow", $allow);
		$obj->set_meta("ip_deny", $deny);
		$obj->save();
	}

	private function get_brother_table($arr)
	{
		$obj = $arr["obj_inst"];

		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
		));
/*		$t->define_field(array(
			"name" => "check",
			"caption" => t("kustuta"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));*/

		$ol = new object_list(array(
			"site_id" => aw_ini_get("site_id"),
			"lang_id" => aw_global_get("lang_id"),
			"brother_of" => $obj->id()
		));

		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($o->id() == $obj->id())
			{
				continue;
			}
			$t->define_data(array(
				"id" => $o->id(),
				"name" => $o->path_str(array(
					"max_len" => 3
				)),
				"check" => html::checkbox(array(
					"name" => "erase[".$o->id()."]",
					"value" => $o->id(),
					"checked" => false,
				)),
			));
		}
	}

	private function get_sss_table($arr)
	{
		$obj = $arr["obj_inst"];

		$section_include_submenus = $obj->meta("section_include_submenus");

		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
		));
		$t->define_field(array(
			"name" => "check",
			"caption" => t("k.a. alammen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
			"width" => "10"
		));

		$t->set_caption(t("Men&uuml;&uuml;d, mille alt viimased dokumendid v&otilde;etakse"));

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_DOCS_FROM_MENU",
		));

		foreach($conns as $c)
		{
			$o = $c->to();

			$t->define_data(array(
				"id" => $o->id(),
				"name" => $o->path_str(array(
					"max_len" => 3
				)),
				"check" => html::checkbox(array(
					"name" => "include_submenus[".$o->id()."]",
					"value" => $o->id(),
					"checked" => $section_include_submenus[$o->id()],
				)),
			));
		}
	}

	private function get_sss_exclude_table($arr)
	{
		$obj = $arr["obj_inst"];

		$section_include_submenus = $obj->meta("section_no_include_submenus");

		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
		));
		$t->define_field(array(
			"name" => "check",
			"caption" => t("k.a. alammen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
			"width" => "10"
		));

		$t->set_caption(t("&Auml;ra v&otilde;ta dokumente men&uuml;&uuml; alt"));

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_NO_DOCS_FROM_MENU",
		));

		foreach($conns as $c)
		{
			$o = $c->to();

			$t->define_data(array(
				"id" => $o->id(),
				"name" => $o->path_str(array(
					"max_len" => 3
				)),
				"check" => html::checkbox(array(
					"name" => "include_submenus[".$o->id()."]",
					"value" => $o->id(),
					"checked" => $section_include_submenus[$o->id()],
				)),
			));
		}
	}

	function parse_alias($args = array())
	{
		extract($args);
		$f = $alias;
		if (!$f["target"])
		{
			return "";
		}
		$target = $f;

		if (!$this->can("view", $target["to"]))
		{
			return "";
		}

		$o = obj($target["to"]);

		if ($o->prop("link") != "")
		{
			$link = $o->trans_get_val("link");
		}
		else
		{
			$link = $this->cfg["baseurl"]."/".$target["to"];
		}

		$ltarget = "";
		if ($o->prop("target"))
		{
			$ltarget = "target='_blank'";
		}

		if (aw_global_get("section") == $target["to"])
		{
			$ret = sprintf("<a $ltarget class=\"sisutekst-sel\" href='$link'>%s</a>",$o->trans_get_val("name"));
		}
		else
		{
			$ret = sprintf("<a $ltarget class=\"sisutekst\" href='$link'>%s</a>",$o->trans_get_val("name"));
		}
		return $ret;
	}

	////
	// !this must set the content for subtemplates in main.tpl
	// params
	//	inst - instance to set variables to
	//	content_for - array of templates to get content for
	//	currently handles SEEALSO_DOCUMENT only
	function on_get_subtemplate_content($arr)
	{
		$str = array();
		$sect = obj(aw_global_get("section"));
		if ($sect->class_id() != CL_MENU)
		{
			$sect = obj($sect->parent());
		}
		$sad_opts = $sect->meta("sad_opts");
		foreach($sect->connections_from(array("type" => "RELTYPE_SEEALSO_DOC")) as $c)
		{
			$tpl = isset($sad_opts[$c->prop("to")]["tpl"]) ? $sad_opts[$c->prop("to")]["tpl"] : "SEEALSO_DOCUMENT";
			if ($c->prop("to.lang_id") == aw_global_get("lang_id"))
			{
				$str[$tpl][$c->prop("to")] = $c->prop("to.jrk");
			}
		}

		// also parents
		if (aw_ini_get("ini_rootmenu"))
		{
			$tmp = aw_ini_get("rootmenu");
			aw_ini_set("rootmenu", aw_ini_get("ini_rootmenu"));
		}
		$pt = $sect->path();
		if (aw_ini_get("ini_rootmenu"))
		{
			aw_ini_set("rootmenu", $tmp);
		}

		foreach($pt as $o)
		{
			if ($o->id() == $sect->id())
			{
				continue;
			}

			$sad_opts = $o->meta("sad_opts");
			foreach(safe_array($sad_opts) as $docid => $dat)
			{
				if ($this->can("view", $docid) && $dat["submenus"] == $docid)
				{
					$tpl = isset($dat["tpl"]) ? $dat["tpl"] : "SEEALSO_DOCUMENT";
					if ($dat["ovr_parent"])
					{
						$str[$tpl] = array();
					}
					$doco = obj($docid);
					if ($doco->lang_id() == aw_global_get("lang_id"))
					{
						if ($o->is_connected_to(array("to" => $doco->id())))
						{
							$str[$tpl][$docid] = $doco->ord();
						}
					}
				}
			}
		}

		$tmp = array();
		foreach($str as $tpl => $dat)
		{
			asort($dat);

			foreach($dat as $did => $ord)
			{
				$d_tpl = "seealso_document.tpl";
				if (!empty($this->cfg["seealso_doc_tpl_names"][$tpl]))
				{
					$d_tpl = $this->cfg["seealso_doc_tpl_names"][$tpl];
				}
				$d = new document();
				$iv = aw_ini_get("document.lead_splitter");
				aw_ini_set("document.lead_splitter", "");
				$ttt = $d->gen_preview(array(
					"docid" => $did,
					"tpl" => $d_tpl,
					"no_doc_lead_break" => 1
				));
				aw_ini_set("document.lead_splitter", $iv);
				$tmp[$tpl] .= $ttt;
			}
		}
		foreach($tmp as $tpl => $docs)
		{
			$arr["inst"]->vars(array(
				$tpl => $docs
			));
		}
	}

	private function _init_seealso_docs_t(&$t)
	{
		$t->define_field(array(
			"name" => "doc",
			"caption" => t("Dokument"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "doc_subs",
			"caption" => t("ka alammen&uuml;&uuml;de juures"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "overwrite_parent_settings",
			"caption" => t("&Auml;ra kasuta &uuml;lemiste men&uuml;&uuml;de dokumente"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "tpl",
			"caption" => t("Vali asukoht"),
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	private function _do_seealso_docs_t($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_seealso_docs_t($t);

		$sad_opts = $arr["obj_inst"]->meta("sad_opts");
		$tpls = aw_ini_get("menu.seealso_doc_tpls");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SEEALSO_DOC")) as $c)
		{
			$cto = $c->prop("to");
			$t->define_data(array(
				"doc" => html::get_change_url($cto, array(), $c->prop("to.name")),
				"doc_subs" => html::checkbox(array(
					"name" => "sad_opts[".$cto."][submenus]",
					"value" => $cto,
					"checked" => $sad_opts[$cto]["submenus"]
				)),
				"overwrite_parent_settings" => html::checkbox(array(
					"name" => "sad_opts[".$cto."][ovr_parent]",
					"value" => $cto,
					"checked" => $sad_opts[$cto]["ovr_parent"]
				)),
				"tpl" => html::select(array(
					"name" => "sad_opts[".$cto."][tpl]",
					"options" => $tpls,
					"selected" => $sad_opts[$cto]["tpl"]
				)),
				"oid" => $cto
			));
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	private function kw_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "new_kw",
			"tooltip" => t("M&auml;rks&otilde;na"),
			"url" => html::get_new_url(CL_KEYWORD, $arr["obj_inst"]->id(), array("return_url" => get_ru())),
			"img" => "new.gif",
		));
	}

	/** toggles site editing display
		@attrib name=toggle_site_editing is_public=1 caption="N&auml;ita &auml;ra n&auml;ita saidi muutmise linke"
	**/
	function toggle_site_editing($arr)
	{
		$_SESSION["no_display_site_editing"] = !$_SESSION["no_display_site_editing"];
		return aw_ini_get("baseurl");
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		switch($field)
		{
			case "set_doc_content_type":
			case "tpl_view_no_inherit":
			case "tpl_lead_no_inherit":
				$this->db_query("ALTER TABLE menu add {$field} int");
				return true;
				break;
		}
	}

	/** Outputs the google sitemap xml file	for the site
		@attrib api=1
	**/
	function get_sitemap()
	{
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";

		$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';
/*
		$ot = new object_tree(array(
			"class_id" => CL_MENU,
			"parent" => aw_ini_get("site_rootmenu"),
		));
		$ol = $ot->to_list();
*/
		$mt = new menu_tree();
		if($this->can("view", $_id = $mt->get_sysdefault()))
		{
			$mt_obj = obj($_id);
			$ol = $mt_obj->sitemap_menulist();
		}
		else
		{
			$ol = new object_list();
		}

		$si = new site_show();
		$arr = array();
		$si->_init_path_vars($arr);
		$si->sel_section_obj = obj($this->sel_section);
		$l = new languages();
		$l_list = $l->get_list(array("all_data" => true));
		foreach($ol->arr() as $item)
		{
			$ct = $item->prop("change_time");
			$cpri = $item->prop("change_pri");
			if (!$ct)
			{
				foreach($item->path() as $path_item)
				{
					$ct = $path_item->prop("change_time") != "" ? $path_item->prop("change_time") : $ct;
					$cpri = $path_item->prop("change_pri") != "" ? $path_item->prop("change_pri") : $cpri;
				}
			}

			foreach($l_list as $lid => $ldat)
			{
				$tmp = str_replace("&", "&amp;", $si->make_menu_link($item, $ldat["acceptlang"]))."</loc><lastmod>".date("Y-m-d", $item->created())."</lastmod>";
				if ($tmp{0} == "/")
				{
					$tmp = aw_ini_get("baseurl").substr($tmp, 1);
				}
				if (strpos($tmp, "://") === false)
				{
					$tmp = aw_ini_get("baseurl").$tmp;
				}
				$xml .= "<url><loc>".$tmp."</loc><lastmod>".date("Y-m-d", $item->created())."</lastmod>";
				$xml .= "<changefreq>".$ct."</changefreq><priority>".((double)$cpri)."</priority></url>\n";
			}
		}
		$xml .= "</urlset>";
		header("Content-Type: text/html");
		header("Content-Encoding: x-gzip");
		echo gzencode($xml);
		die();
	}

	private function _get_sss_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"name" => "search",
			"img" => "search.gif"
		));

		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("V&otilde;ta viimaseid dokumente"),
			"link" => "javascript:aw_popup_scroll('".$this->mk_my_orb("do_search", array("pn" => "_set_sss", "clid" => CL_MENU), "popup_search")."','".t("Otsi")."',550,500)",
		));
		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("&Auml;ra v&otilde;ta viimaseid dokumente"),
			"link" => "javascript:aw_popup_scroll('".$this->mk_my_orb("do_search", array("pn" => "_set_no_sss", "clid" => CL_MENU), "popup_search")."','".t("Otsi")."',550,500)",
		));

		$tb->add_delete_rels_button();

	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["_set_sss"] = "0";
		$arr["_set_no_sss"] = "0";
		$arr["sad_s"] = "0";
		$arr["link_pops"] = "0";
		if (isset($request["group"]) && $request["group"] === "relationmgr")
		{
			$arr["return_url"] = $request["return_url"];
		}
		else
		{
			$arr["post_ru"] = post_ru();
		}
	}

	private function _get_linker(&$p, $o)
	{
		$ps = new ct_linked_obj_search();
		if ($this->can("view", $o->meta("linked_obj")))
		{
			$p["post_append_text"] = sprintf(t("Valitud objekt: %s /"), html::obj_change_url($o->meta("linked_obj")));
			$p["post_append_text"] .= " ".html::href(array(
				"url" => $this->mk_my_orb("remove_linked", array("id" => $o->id(), "ru" => get_ru())),
				"caption" => html::img(array("url" => aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif", "border" => 0))
			))." / ";
		}
		$p["post_append_text"] = isset($p["post_append_text"]) ? $p["post_append_text"] : "";
		$p["post_append_text"] .= t(" Otsi uus objekt: ").$ps->get_popup_search_link(array(
			"pn" => "link_pops",
			"clid" => array(doc_obj::CLID,link_fix::CLID)
		));
	}

	private function _init_stats_table($t)
	{
		$t->define_field(array(
			"name" => "tm",
			"align" => "center",
			"caption" => t("Aeg"),
			"type" => "time",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "person",
			"align" => "center",
			"caption" => t("Isik"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "uid",
			"align" => "center",
			"caption" => t("Kasutaja"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "ugroup",
			"align" => "center",
			"caption" => t("Kasutajagrupp"),
			"sortable" => 1
		));
	}

	private function _get_acl_views($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_stats_table($t);

		$u = new user();

		$this->db_query("SELECT tm,uid FROM syslog WHERE oid = ".$arr["obj_inst"]->id()." AND act_id = " .
		 19 /* SA_PAGEVIEW */ . " ORDER BY id DESC LIMIT 50 ");
		while ($row = $this->db_next())
		{
			$p = $u->get_person_for_uid($row["uid"]);
			$g = $u->get_highest_pri_grp_for_user($row["uid"], true);
			$t->define_data(array(
				"tm" => $row["tm"],
				"person" => $p ? $p->name() : "",
				"uid" => $row["uid"],
				"ugroup" => $g ? $g->name() : ""
			));
		}
	}

	private function _get_acl_edits($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_stats_table($t);

		$u = new user();

		$this->db_query("SELECT tm,uid FROM syslog WHERE oid = ".$arr["obj_inst"]->id()." AND act_id = " . 1 /* SA_CHANGE */ . " ORDER BY id DESC LIMIT 50 ");
		while ($row = $this->db_next())
		{
			$p = $u->get_person_for_uid($row["uid"]);
			$g = $u->get_highest_pri_grp_for_user($row["uid"], true);
			$t->define_data(array(
				"tm" => $row["tm"],
				"person" => $p ? $p->name() : "",
				"uid" => $row["uid"],
				"ugroup" => $g ? $g->name() : ""
			));
		}
	}

	/**
		@attrib api=1
	**/
	public function write_trans_aliases($arr)
	{
		$o = $arr["obj_inst"];
		$l = new languages();
		$ll = $l->get_list(array("all_data" => true, "set_for_user" => true));
		foreach($ll as $lid => $lang)
		{
			if ($lid == $o->lang_id())
			{
				continue;
			}
			if ($arr["request"]["act_".$lid])
			{
				$this->db_query("DELETE FROM aw_alias_trans WHERE menu_id = ".$o->id(). " AND lang_id = ".$lid);
				$str = $arr["request"]["trans_".$lid."_alias"];
				// No spaces in the end/beginning! -kaarel 6.02.2009
				$nv = trim(iconv("UTF-8", $lang["charset"], $str));
				$this->quote($nv);
				$this->db_query("INSERT INTO aw_alias_trans(menu_id,lang_id,alias)
					VALUES(".$o->id().", $lid, '$nv')");
			}
		}
	}

	private function _seealso_docs_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(doc_obj::CLID), $arr["obj_inst"]->id(), 18 /* RELTYPE_SEEALSO_DOCUMENT */);
		$tb->add_search_button(array(
			"pn" => "sad_s",
			"clid" => doc_obj::CLID
		));
		$tb->add_delete_rels_button();
	}

	/**
		@attrib name=remove_linked
		@param id required type=int
		@param ru required
	**/
	function remove_linked($arr)
	{
		$o = obj($arr["id"]);
		$o->set_meta("linked_obj", null);
		$o->save();
		return $arr["ru"];
	}

	////
	// !Tagastab nimekirja erinevatest mentpidest
	private function get_type_sel()
	{
		return array(
			"70" => t("Sektsioon"),
			"69" => t("Klient"),
			"71" => t("Adminni men&uuml;&uuml;"),
			"75" => t("Kataloog"),
			"77" => t("Avalik meetod"),
		);
	}

	/**
	@attrib name=get_menu_open caption="Ava Aktiivne" is_public=1
	**/
	function get_menu_open($arr)
	{
		$p = obj($_GET['parent']);
		foreach($p->path() as $po)
		{
			if($po->class_id() == CL_MENU)
			{
				$parent = $po->id();
			}
		}
		return admin_if::get_link_for_obj($parent);
	}

	private function _check_alias($arr)
	{
		if ($arr["prop"]["value"] != "")
		{
			$filt = array(
				"class_id" => CL_MENU,
				"alias" => $arr["prop"]["value"],
				"site_id" => aw_ini_get("site_id")
			);
			if (is_oid($arr["obj_inst"]->id()))
			{
				$filt["oid"] = new obj_predicate_not($arr["obj_inst"]->id());
			}

			if (aw_ini_get("menuedit.recursive_aliases") == 1)
			{
				// if this menu is the first one in it's path then list all menus with the same alias and check if
				// their parents also have none and if so, then we fail.
				if ($this->_object_has_parent_aliases($arr["obj_inst"]))
				{
					$filt["parent"] = $arr["obj_inst"]->parent();
				}
				else
				{
					$ol = new object_list($filt);
					$has_no_parent_als = false;
					foreach($ol->arr() as $other_menu)
					{
						if (!$this->_object_has_parent_aliases($other_menu))
						{
							$has_no_parent_als = true;
						}
					}

					if ($has_no_parent_als)
					{
						$arr["prop"]["error"] = t("Selline alias on juba olemas!");
						return PROP_FATAL_ERROR;
					}
					return PROP_OK;
				}
			}

			$ol = new object_list($filt);
			if (count($ol->ids()))
			{
				$arr["prop"]["error"] = t("Selline alias on juba olemas!");
				return PROP_FATAL_ERROR;
			}
		}
		return PROP_OK;
	}

	private function _object_has_parent_aliases($o)
	{
		if (!is_oid($o->id()))
		{
			$o = obj($o->parent());
		}
		foreach($o->path() as $path_item)
		{
			if ($path_item->alias() != "")
			{
				return true;
			}
		}
		return false;
	}
}
