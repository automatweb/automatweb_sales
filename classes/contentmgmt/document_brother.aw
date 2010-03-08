<?php

/*

@classinfo trans=1 syslog_type=ST_DOCUMENT maintainer=kristo

@default table=documents
@default group=general

@property navtoolbar type=toolbar no_caption=1 store=no
@caption Toolbar

@property active type=checkbox ch_value=2 table=objects field=status
@caption Aktiivne

@property nobreaks type=hidden table=documents

@property plugins type=callback callback=callback_get_doc_plugins table=objects field=meta method=serialize
@caption Pluginad

@property title type=textbox size=60 trans=1
@caption Pealkiri

@property subtitle type=textbox size=60 trans=1
@caption Alapealkiri

@property author type=textbox size=60 trans=1
@caption Autor

@property photos type=textbox size=60 trans=1
@caption Fotode autor

@property alias type=textbox size=60 table=objects field=alias
@caption Alias

@property keywords type=textbox size=60 trans=1
@caption V&otilde;tmes&otilde;nad

@property names type=textbox size=60 trans=1
@caption Nimed

@property lead type=textarea richtext=1 cols=60 rows=10 trans=1
@caption Lead

@property content type=textarea richtext=1 cols=60 rows=30 trans=1
@caption Sisu

@property moreinfo type=textarea richtext=1 cols=60 rows=5 trans=1
@caption Lisainfo

@property link_text type=textbox size=60
@caption URL

@property is_forum type=checkbox ch_value=1
@caption Foorum

@property showlead type=checkbox ch_value=1 default=1
@caption N&auml;ita leadi

@property show_modified type=checkbox ch_value=1
@caption N&auml;ita muutmise kuup&auml;eva

//---------------
@property no_right_pane type=checkbox ch_value=1 group=settings
@caption Ilma parema paanita

@property no_left_pane type=checkbox ch_value=1 group=settings
@caption Ilma vasaku paanita

@property title_clickable type=checkbox ch_value=1 group=settings
@caption Pealkiri klikitav

@property clear_styles type=checkbox ch_value=1 store=no
@caption T&uuml;hista stiilid

@property link_keywords type=checkbox ch_value=1 store=no
@caption Lingi v&otilde;tmes&otilde;nad

@property esilehel type=checkbox ch_value=1 group=settings
@caption Esilehel

@property frontpage_left type=checkbox ch_value=1
@caption Esilehel tulbas

@property dcache type=checkbox store=no
@caption Cache otsingu jaoks

@property dcache_save type=checkbox ch_value=1 group=settings table=objects field=meta method=serialize trans=1
@caption Cache otsingu jaoks (salvestub)

@property dcache_content type=hidden field=dcache 
@property rating type=hidden 
@property num_ratings type=hidden 


@property show_title type=checkbox ch_value=1
@caption N&auml;ita pealkirja

@property no_search type=checkbox ch_value=1
@caption J&auml;ta otsingust v&auml;lja

@property cite type=textarea cols=60 rows=10
@caption Tsitaat

@property tm type=textbox size=20
@caption Kuup&auml;ev

@property show_print type=checkbox ch_value=1 table=objects field=meta method=serialize default=1
@caption 'Prindi' nupp

@property sections type=select multiple=1 size=20 group=vennastamine store=no
@caption Sektsioonid

@property aliasmgr type=aliasmgr store=no editonly=1 group=relationmgr trans=1
@caption Aliastehaldur

@property start type=date_select table=planner group=calendar
@caption Algab (kp)

@property start1 type=datetime_select field=start table=planner group=calendar
@caption Algab 

@property createdby table=objects field=createdby group=general type=text
@caption Kes tegi

@property user1 table=documents group=general type=textbox size=60
@caption Kasutaja defineeritud 1

@property user2 table=documents group=general type=textarea rows=2 cols=60
@caption Kasutaja defineeritud 2

@property user3 table=documents group=general type=textarea rows=2 cols=60
@caption Kasutaja defineeritud 3

@property user4 table=documents group=general type=textarea rows=2 cols=60
@caption Kasutaja defineeritud 4

@property user5 table=documents group=general type=textarea rows=2 cols=60
@caption Kasutaja defineeritud 5

@property user6 table=documents group=general type=textarea rows=5 cols=60
@caption Kasutaja defineeritud 6

@property language type=text group=general type=text store=no
@caption Keel

@property duration type=time_select field=end table=planner group=calendar
@caption Kestab

@property doc_modified type=hidden table=documents field=modified
@caption Dok. modified

@property link_calendars type=callback store=no callback=callback_gen_link_calendars group=calendar
@caption Vali kalendrid, millesse see s&uuml;ndmus veel salvestatakse.

@property calendar_relation type=select field=meta method=serialize group=general table=objects
@caption P&otilde;hikalender

@property gen_static type=checkbox store=no
@caption Genereeri staatiline

@property no_last type=checkbox ch_value=1 group=settings trans=1
@caption &Auml;ra arvesta muutmist

@property show_last_changed type=checkbox ch_value=1 group=settings trans=1 table=objects field=meta method=serialize
@caption Muutmise kuupaev dokumendi sees


@property sbt type=submit value=Salvesta store=no 

@groupinfo calendar caption=Kalender
@groupinfo vennastamine caption=Vennastamine
@groupinfo settings caption=Seadistused
@groupinfo relationmgr caption=Seostehaldur submit=no

@tableinfo documents index=docid master_table=objects master_index=brother_of
@tableinfo planner index=id master_table=objects master_index=brother_of

@classinfo trans=1

*/


classload("document");
class document_brother extends document
{
	function document_brother()
	{
		$this->document();
	}

	/**  
		
		@attrib name=new params=name is_public="1" default="0"
		
		@param parent required
		@param period optional
		@param s_name optional
		@param s_content optional
		
		@returns
		
		
		@comment

	**/
	function add($arr)
	{
		extract($arr);
		$this->mk_path($parent, LC_DOCUMENT_BROD_DOC);
		$this->read_template("search_doc.tpl");
		$SITE_ID = $this->cfg["site_id"];
		$period = aw_global_get("period");
		if ($s_name != "" || $s_content != "")
		{
			load_vcl("table");
			$t = new aw_table(array(
				"layout" => "generic"
			));
			$t->define_field(array(
				"name" => "name",
				"caption" => t("Nimetus"),
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "parent",
				"caption" => t("Asukoht"),
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "createdby",
				"caption" => t("Looja"),
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "modified",
				"caption" => t("Viimati muudetud"),
				"type" => "time",
				"format" => "d.m.Y / H:i",
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "pick",
				"caption" => t("Vali see"),
			));
			$se = array();
			if ($s_name != "")
			{
				$this->quote(&$s_name);
				$se[] = " name LIKE '%".$s_name."%' ";
			}
			if ($s_content != "")
			{
				$this->quote(&$s_content);
				$se[] = " content LIKE '%".$s_content."%' ";
			}
			/* AND (objects.site_id = $SITE_ID OR objects.site_id IS NULL) */
			$this->db_query("
				SELECT 
					documents.title as name,
					objects.oid,
					objects.createdby,
					objects.modified
				FROM 
					objects 
					LEFT JOIN documents ON documents.docid=objects.oid 
				WHERE 
					objects.status != 0  AND 
					(
						objects.class_id = ".CL_DOCUMENT." OR 
						objects.class_id = ".CL_PERIODIC_SECTION." 
					) AND 
					".join("AND",$se));
			while ($row = $this->db_next())
			{
				if (!$this->can("view", $row["oid"]))
				{
					continue;
				}
				$row["name"] = html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $row["oid"])),
					"caption" => $row["name"]
				));
				$row["pick"] = html::href(array(
					"url" => $this->mk_my_orb("create_bro", array("parent" => $parent, "id" => $row["oid"], "s_name" => $s_name, "s_content" => $s_content,"period" => $period)),
					"caption" => t("Vali see")
				));
				$o = obj($row["oid"]);
				$row["parent"] = $o->path_str(array(
					"max_len" => 4
				));
				$t->define_data($row);
			}
			$t->set_default_sortby("name");
			$t->sort_by();
			$this->vars(array("LINE" => $t->draw()));
		}
		else
		{
			$s_name = "%";
			$s_content = "%";
		}
		$this->vars(array(
			"reforb" => $this->mk_reforb("new", array("reforb" => 0,"parent" => $parent)),
			"s_name"	=> $s_name,
			"s_content"	=> $s_content
		));
		return $this->parse();
	}

	/** creates a brother of document $id under menu $parent 
		
		@attrib name=create_bro params=name default="0"
		
		@param parent required
		@param id required
		@param s_name optional
		@param s_content optional
		
		@returns
		
		
		@comment

	**/
	function create_bro($arr)
	{
		extract($arr);
		$o = obj($id);
		$noid = $o->create_brother($parent);
		if ($no_header)
		{
			return $noid;
		}
		else
		{
			header("Location: ".$this->mk_my_orb("new", array("parent" => $parent, "s_name" => $s_name,"s_content" => $s_content), "document_brother"));
		}
	}
}
?>
