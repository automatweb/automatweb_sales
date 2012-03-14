<?php

// persons_webview.aw - Kliendihaldus
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property company type=relpicker reltype=RELTYPE_COMPANY
@caption Ettev&otilde;te

@property departments type=relpicker multiple=1 reltype=RELTYPE_DEPARTMENT
@caption Osakonnad

//----------------------------------------------
@groupinfo view caption=N&auml;itamine
@default group=view

@property show_only_public_phones type=checkbox ch_value=1
@caption N&auml;ita ainult avalikke numbreid

- Isikute j2rjestamisprintsiip (saab valida mitu, peale salvestamist tekib uus valik), Listbox Omadus (perenimi, ametinimetuse jrk, isiku jrk), mille k6rval Listbox J2rjestamine (V2iksem enne/Suurem enne)
@property persons_principe type=callback callback=callback_get_persons_principe
@caption Isikute j&auml;rjestamisprintsiip

property persons_principe_property type=select
caption Isikute jrjestamisprintsiibi omadus

property persons_principe_direct type=select
caption .

- Grupeerimine osakonna j2gi (m2rkeruut)
@property department_grouping type=checkbox ch_value=1
@caption Grupeerimine osakonna j&auml;rgi

- Grupeerimise jarjestamisprintsiip (analoogne isikutega, osakonna jrk/osakonna nimi alusel)
@property grouping_principe type=callback callback=callback_get_grouping_principe
@caption Grupeerimise j&auml;rjestamisprintsiip

- Vaadete tabel (salvestamisel uus rida)
@property view callback=callback_get_view_table
@caption Vaadete tabel

-- Vaade 1 (tekst)
property view1 type=textbox
caption Vaade 1

-- templeit (tekstikast, viide failisysteemis olevale templeidile, kataloog on eeldefineeritud)
property template type=textbox
caption Template

-- osakondade tasemeid (tekstikast, mitu osakondade taset sellel lehel sisse on vaja lugeda), v6ib olla ka kujul 1-2 (ehk loetakse sisse ning kuvatakse 1 ja 2 taseme osakonnad, kui on kirjutatud 1, siis kuvatakse ainult Osakonnad propertys valitud osakonnad)
property department_levels type=textbox
caption Osakondade tasemed

-- raadionupud (ainult osakonnad/koos isikutega) - selleks, et ei loetaks tingimata sisse nende osakondade isikuid, kui on aja naidata ainult osakondade andmeid.
property with_without_persons type=chooser orient=vertical store=yes method=serialize
caption Sisse lugeda

-- tulpade arv (tekstikast) - mitmes tulbas kuvatakse inimeste andmeid
property columns type=textbox
caption Tulpade arv

-- read ametinimetuste alusel (markeruut) - sama ametinimega isikuid yritatakse sama rea peale paigutada
property rows_by type=checkbox ch_value=1
caption Read ametinimetuse alusel

-- min tulpade arv (tekstikast) - erineva tasemega ametinimetusele vastavad isikud v6ib ka k6rvuti panna, kui min tulpade arv mingis reas ei ole saavutatud.
property min_cols type=textbox
caption Minimaalne tulpade arv

@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Registri andmed

@reltype DEPARTMENT value=2 clid=CL_CRM_SECTION,CL_CRM_COMPANY
@caption Osakond
*/
class persons_webview extends class_base
{
	function persons_webview()
	{
		$this->init(array(
			"tpldir" => "crm/persons_webview",
			"clid" => CL_PERSONS_WEBVIEW
		));

		$this->persons_sort_order = array(
			0 => "",
			"last_name" => t("perenimi"),
			"profession" => t("ametinimetuse jrk"),
			"jrk" => t("isiku jrk"),
		);

		$this->department_sort_order = array(
			0 => "",
			"jrk" => t("osakonna jrk"),
			"name" => t("osakonna nimi"),
		);

		$this->order = array (
			"ASC" => t("Kasvav"),
			"DESC" => t("Kahanev"),
		);

		$this->education = array("options" => array(
			0 => t("-- vali --"),
			1 => t("p&otilde;hi"),
			2 => t("kesk"),
			3 => t("kesk-eri"),
			4 => t("k&otilde;rgem"),
		));

		$this->phone_types = array("phone" => "");//see on selleks, et lihtsalt telefoni tyybid kirja panna ja jargmistel inimestel vastavad muutujad ara nullida, muidu v6tab eelmiselt isikult

		$this->help = nl2br(htmlentities(t("
			Osakondade tasemed, mida n&auml;idataks, saab m&auml;rkida kujul '1,2,3' v&otilde;i '1-2' v&otilde;i '3'
			juhul , kui on vaja kuvada inimesi &uuml;ksteise k&otilde;rval, siis teplates peaks olema subid umbes kujul:
			<!-- SUB: DEPARTMENT -->
			{VAR:department_name}
			<table>
			<!-- SUB: LINE -->
				<tr>
				<!-- SUB: WORKER -->
					<td>
					<p align=bottom><center>
						{VAR:photo} <br>
						<b>{VAR:name} </b><br>
						{VAR:rank} <br>
					</center></p>
					</td>
				<!-- END SUB: WORKER -->
				</tr>
			<!-- END SUB: LINE -->
			</table>
			<!-- SUB: DEPARTMENT -->

			kui igal real on 1 inimene, siis v&otilde;ib SUB: LINE vahelt &auml;ra j&auml;tta

			Kui viimaseks vaateks on &uuml;ks konkreetne isik, siis template sees &uuml;htegi SUBi ei tohiks olla, kasutada saab samu muutujaid, mis muidu sub'is worker

			endine variant ka igaks juhuks t&ouml;&ouml;tab veel

			juhul kui miski taseme osakonda oleks vaja teistmoodi n&auml;idata, siis tuleks <!-- SUB: DEPARTMENT --> sisse teha <!-- SUB: LEVEL4DEPARTMENT --> (vastavalt taseme numbrile) , mis oleks muidu sama struktuuriga nagu DEPARTMENT

			muutujad mida saab kasutada:
			DEPARTMENT sub'is: department_name, address , phone , fax , email , next_level_link (link n&auml;gemas antud osakonda uues vaates).
			sub'is worker :
			name , name_with_email , email , emails, photo, contact,
			profession , profession_with_directive , professions , directive (ametijuhend), wage_doc , wage_doc_exist (palgaandmete dokument, kui on olemas),
			education (haridustase) , school , subject_field(valdkond) , speciality ,
			phone, phones , home_phone, home_phones, mobile_phone, mobile_phones, skype_phone, skype_phones, short_phone, short_phones, work_phone, work_phones, extension_phone, extension_phones,
			next_level_link (link j'gmise taseme vaatesse... kui tegu siis antud inimesega),
			company, section,
			url, urls,
			ta1 - ta5 (kasutajadefineeritud muutujad).

			Kui lisada objekt men&uuml;&uuml;sse, siis esimeseks vaate infoks tuleb men&uuml;&uuml;s olev.
			Template'ide t&otilde;lkimiseks kasutada faili persons_web_view.aw.
			")));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "with_without_persons":
				$prop["options"] = array(
					0 => t("ainult osakonnad"),
					1 => t("koos isikutega"),
				);
				break;
			case "departments":
				if(is_oid($arr["obj_inst"]->prop("company")) && $this->can("view" , $arr["obj_inst"]->prop("company")))
				{
					$company = obj($arr["obj_inst"]->prop("company"));
					$comp = new crm_company();
					foreach($comp->get_all_org_sections($company) as $section_id)
					{
						$section = obj($section_id);
						$prop["options"][$section_id] = $section->trans_get_val("name");
					}
				}
				break;
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
			case "persons_principe":
				$this->submit_meta($arr);
				break;
		}
		return $retval;
	}

	function submit_meta($arr = array())
	{
		$arr["obj_inst"]->set_meta("persons_principe", array($arr["request"]["persons_principe"]));
 		$arr["obj_inst"]->set_meta("grouping_principe", array($arr["request"]["grouping_principe"]));
 		$arr["obj_inst"]->set_meta("view", array($arr["request"]["view"]));
 	}

	function callback_get_persons_principe($arr)
	{
		$principe = $arr["obj_inst"]->meta("persons_principe");
		$count = sizeof($principe);
		if($count > 0 && !$principe[$count-1]["principe"])$count--;
		if($count > 0 && !$principe[$count-1]["principe"])$count--;
		$ret = array();

		for($i = 0; $i < $count+1; $i++)
		{
			$nm = "persons_principe[".$i."][principe]";
			$ret[$nm] = array(
				"name" => $nm,
				"caption" => ($i + 1).". ".t("Isikute j&auml;rjestamisprintsiip"),
				"type" => "text",
				"value" => html::select(array(
					"name" => "persons_principe[".$i."][principe]",
					"options" => $this->persons_sort_order,
					"value" => $principe[$i]["principe"],
				)).html::select(array(
					"name" => "persons_principe[".$i."][order]",
					"options" => $this->order,
					"value" => $principe[$i]["order"],
				)),
//				"type" => "select",
//				"options" => $this->persons_sort_order,
//				"value" => $principe[$i]["principe"]
				);
//			$nm = "persons_principe[".$i."][order]";
//			$ret[$nm] = array("name" => $nm,  "type" => "select", "options" => $this->order, "value" => $principe[$i]["order"]);
		}
		return $ret;
	}

	function callback_get_grouping_principe($arr)
	{
		$principe = $arr["obj_inst"]->meta("grouping_principe");
		$count = sizeof($principe);
		if($count > 0 && !$principe[$count-1]["principe"])$count--;
		if($count > 0 && !$principe[$count-1]["principe"])$count--;
		$ret = array();
		for($i = 0; $i < $count+1; $i++)
		{
			$nm = "grouping_principe[".$i."][principe]";
			$ret[$nm] = array(
				"name" => $nm,
				"caption" => ($i + 1).". ".t("Osakondade j&auml;rjestamisprintsiip"),
				"type" => "text",
				"value" => html::select(array(
					"name" => $nm,
					"options" => $this->department_sort_order,
					"value" => $principe[$i]["principe"],
				)).html::select(array(
					"name" => "grouping_principe[".$i."][order]",
					"options" => $this->order,
					"value" => $principe[$i]["order"],
				)),
			);
	//		$nm = "grouping_principe[".$i."][order]";
	//		$ret[$nm] = array("name" => $nm,  "type" => "select", "options" => $this->order, "value" => $principe[$i]["order"]);
		}
		return $ret;
	}

	function callback_get_view_table($arr)
	{
		$view = $arr["obj_inst"]->meta("view");
		$count = sizeof($view);
		if($count > 0 && !$view[$count-1]["template"])$count--;
		if($count > 0 && !$view[$count-1]["template"])$count--;
		$ret = array();

		$t = new aw_table(array(
			"layout" => "generic"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Vaade"),
		));
		$t->define_field(array(
			"name" => "template",
			"caption" => t("Templeit"),
		));
		$t->define_field(array(
			"name" => "department_levels",
			"caption" => t("Osakondade tasemeid"),
		));
		$t->define_field(array(
			"name" => "with_persons",
			"caption" => t("Koos inimkoosseisuga"),
		));
		$t->define_field(array(
			"name" => "columns",
			"caption" => t("Tulpade arv"),
		));
		$t->define_field(array(
			"name" => "rows_by",
			"caption" => t("Read ametinimetuste alusel"),
		));
		$t->define_field(array(
			"name" => "min_cols",
			"caption" => t("Minimaalne tulpade arv"),
		));
		$nm = "view";
		for($i = 0; $i < $count+1; $i++)
		{
			if($i==0) $caption = "Vaadete tabelid";
			else $caption = "";
			$t->define_data(array(
				"name" => ($i + 1),
				"template" => html::textbox(array(
						"name" => "view[".$i."][template]",
						"value" => $view[$i]["template"],
						"size" => "10",
				)),
				"department_levels" => html::textbox(array(
						"name" => "view[".$i."][department_levels]",
						"value" => $view[$i]["department_levels"],
						"size" => "10",
				)),
				"with_persons"  => html::checkbox(array(
						"value" => 1,
						"checked" => $view[$i]["with_persons"],
						"name" => "view[".$i."][with_persons]",
				)),
				"columns" => html::textbox(array(
						"name" => "view[".$i."][columns]",
						"value" => $view[$i]["columns"],
						"size" => "1",
				)),
				"rows_by" => html::checkbox(array(
						"value" => 1,
						"checked" => $view[$i]["rows_by"],
						"name" => "view[".$i."][rows_by]",
				)),
				"min_cols"  => html::textbox(array(
						"name" => "view[".$i."][min_cols]",
						"value" => $view[$i]["min_cols"],
						"size" => "1",
				)),
			));
		}
		$ret[$nm] = array(
			"name" => $nm,
			"caption" => t($caption),
			"type" => "text",
			"value" => $t->draw().$this->help,
		);
		return $ret;
	}

	function get_folders_as_object_list($o, $level, $parent)
	{
		$_SESSION["persons_webview"] = $o->id();
		$_SESSION["company"] = $o->prop("company");
		$this->view_obj = $o;
		$this->meta = $this->view_obj->meta();
		$this->view = $this->meta["view"][0];
		$company_id = $this->view_obj->prop("company");
		if(is_oid($company_id))	$company = obj($company_id);
		$departments = $this->view_obj->prop("departments");
		$this->set_levels(0);//teeb siis erinevatest tasemetest massiivi, mida yldse kuvada ja paneb selle muutujasse $this->levels
		$this->jrks = array();
		$sections = $this->get_sections(array("section" => $company , "jrk" => 0));
		if(in_array(0,$this->levels)) $sections = array_merge(array($company) ,$sections); //v6ibolla tahetakse ka asutust naha
		$ol = new object_list();
		foreach($sections as $section)
		{
			if((!in_array($section->id(), $this->view_obj->prop("departments")) || !sizeof($this->view_obj->prop("departments"))>0) && (!$section->id() == $company_id)) continue;
			$ol->add($section);
		}
		return $ol;
	}

	function make_menu_link($o, $ref = NULL)
	{
		return $this->mk_my_orb("parse_alias",
			array(
				"id" => $_SESSION["persons_webview"],
				"section_o" => $o->id(),
				"view" => 1,
				"level" => $this->jrks[$o->id()],
				"company_id" => $_SESSION["company"],
		),
		CL_PERSONS_WEBVIEW);
	}

	/*
	function sort_by($args)
	{
		extract($args);
		switch($orderby)
		{
			case "last_name":
				foreach($workers as $worker)
				{
					$workers_tmp[] = array("sort" => $worker["worker"]->prop("lastname"), "data" => $worker);
				}
				break;
			case "profession":
				foreach($workers as $worker)
				{
					$jrk = 0;
					if(is_oid($worker["worker"]->prop("rank")))
					{
						$profession_obj = obj($worker["worker"]->prop("rank"));
						$jrk = $profession_obj->prop("jrk");
					}
					$workers_tmp[] = array("sort" => $jrk, "data" => $worker);
				}
				break;
			case "jrk":
				return $workers;
// 				foreach($workers as $worker)
// 				{
// 					$workers_tmp[] = array("sort" => $worker["worker"]->prop("lastname"), "data" => $worker);
// 				}
// 				break;
		}
		foreach ($workers_tmp as $key => $row) {
			$data[$key]  = $row['data'];
			$sort[$key] = $row['sort'];
		}
		if($sort_order == "ASC") $sort_order = SORT_ASC;
		else $sort_order = SORT_DESC;
		array_multisort($sort, $sort_order, $workers_tmp);

		$workers = array();
		foreach ($workers_tmp as $data)// teeb massiivi vanale kujule tagasi
		{
			$workers[] = $data["data"];
		}
		return $workers;
	}*/

	function do_person_sort($a, $b)
	{
		foreach($this->principe as $key => $val)
		{
			switch($val["principe"])
			{
				case "last_name":
					$res = strcmp($a["worker"]->prop("lastname"),$b["worker"]->prop("lastname"));
					if($res)
					{
						if($val["order"] == "ASC")
						{
							return $res;
						}
						return -$res;
					}
					continue;

				case "profession":
					$awr = $this->get_relation_profession_ord($a["worker"]);
					$bwr = $this->get_relation_profession_ord($b["worker"]);
					if($awr || $bwr)
					{
						$res = $awr-$bwr;
					}
					else
					{
						$res = $a["worker"]->prop("rank.jrk") - $b["worker"]->prop("rank.jrk");
					}
					if($res)
					{
						if($val["order"] == "ASC")
						{
							$this->set_proffession_principe_order("ASC");
							return $res;
						}
						$this->set_proffession_principe_order("DESC");
						return -$res;
					}
					continue;

				case "jrk":
					$res = $a["jrk"] - $b["jrk"];
					if($res)
					{
						if($val["order"] == "ASC")
						{
							return $res;
						}
						return -$res;
					}
					continue;
			}
		}
		return 0;
	}

	//hiljem v2ljatrykil j2rjekorra m22ramiseks vaja
	private function set_proffession_principe_order($ord)
	{
		if(!$this->proffession_principe_order)
		{
			$this->proffession_principe_order = $ord;
		}
	}

	private function get_relation_profession_ord($o)
	{
		$ret = 0;
		foreach($o->connections_from(array("clid" => "CL_CRM_PERSON_WORK_RELATION")) as $c)
		{
			$rel = $c->to();
			if($this->section && $rel->prop("section") == $this->section->id())
			{
				if($rel->prop("profession.jrk") > $ret)
				{
					$ret = $rel->prop("profession.jrk");
				}
			}
		}
		return $ret;
	}

	function person_sort($workers)
	{
		$this->principe = $this->view_obj->prop("persons_principe");
		uasort($workers, array(&$this, "do_person_sort"));

/*		$count = sizeof($principe);
		while($count>=0)
		{
			if($principe[$count]["principe"])
			{
				$workers = $this->sort_by(array(
					"workers" => $workers,
					"orderby" => $principe[$count]["principe"],
					"sort_order" => $principe[$count]["order"],
				));
			}
			$count--;
		}
*/
		return ($workers);
	}
	function sort_sections($sections)
	{
		if(sizeof($sections) < 2)
		{
			return $sections;
		}
		$principe = $this->view_obj->prop("grouping_principe");
		$count = sizeof($principe);
		while($count>=0)
		{
			if($principe[$count]["principe"])
			{
				$sections = $this->section_sort_by(array(
					"sections" => $sections,
					"orderby" => $principe[$count]["principe"],
					"sort_order" => $principe[$count]["order"],
				));
			}
			$count--;
		}

		return ($sections);
	}

	function section_sort_by($args)
	{
		extract($args);
		switch($orderby)
		{
			case "name":
 				foreach($sections as $section)
 				{
 					$sections_tmp[] = array("sort" => $section->trans_get_val("name"), "data" => $section);
 				}
 				break;
			case "jrk":
 				foreach($sections as $section)
 				{
 					$sections_tmp[] = array("sort" => $section->prop("jrk"), "data" => $section);
 				}
 				break;
		}

		foreach ($sections_tmp as $key => $row) {
			$data[$key]  = $row['data'];
			$sort[$key] = $row['sort'];
		}
		if($sort_order == "ASC") $sort_order = SORT_ASC;
		else $sort_order = SORT_DESC;
		array_multisort($sort, $sort_order, $sections_tmp);
		$sections = array();
		foreach ($sections_tmp as $data)// teeb massiivi vanale kujule tagasi
		{
			$sections[] = $data["data"];
		}
		return $sections;
	}

	//tekitab nimekirja tasemetest mida n2idatakse...
	function set_levels($level)
	{
		$levels = $this->view["department_levels"];
		$possible_levels = explode("," , $levels);
		if(sizeof($possible_levels) > 1)
		{
			$levels = array();
			foreach ($possible_levels as $val)
			{
				$levels[$val - $level] = $val - $level;
			}
		}
		else
		{
			$from_to = explode("-" , $levels);
			$possible_levels = array();

			if(!empty($from_to[1]))
			{
				while($from_to[0] <= $from_to[1])
				{
					$possible_levels[] = $from_to[0] - $level;
					$from_to[0]++;
				}
			}
			if(sizeof($possible_levels)>0) $levels = $possible_levels;
			else $levels = array($levels - $level);
		}
		$this->levels = $levels;
	}

	//seda vist siiski ei l2he vaja seekord
	function request_execute ($this_object)
	{
		return $this->parse_alias (array (
			"alias" => array("to" => $this_object->id()),
		));
	}

	/** parse alias
		@attrib name=parse_alias is_public="1" nologin="1"
	**/
	function parse_alias($arr = array())
	{
		extract($_GET);
		//global $view , $id, $section, $level, $company_id, $section_id;
		if(!empty($id) && is_oid($section_o)) // juhul kui asi pole dokumendi sees vaid tulev kuskiklt urlist
		{
			$this->view_obj = obj($id);
		}
		else
		{
			$this->view_obj = obj($arr["alias"]["to"]); // dokumendis aliasena
		}
		if(!empty($company_id) && is_oid($company_id)) $this->company = obj($company_id);
		if(!empty($section_id) && is_oid($section_id)) $this->section = obj($section_id);

		$this->meta = $this->view_obj->meta();
		$this->view_no = empty($view) ? 0 : $view;
		if(empty($view))
		{
			 $this->view = $this->meta["view"][0]; // algul paneb siis metasse esimese (default) taseme vaate,...
		}
		else
		{
			$this->view = $this->meta["view"][$view]; // juhul kui tuleb kuskilt urlist miski tase,...
		}

		if(!empty($section_o) && $this->can("view" , $section_o)){
			$section_obj = obj($section_o);
			if($section_obj->class_id() == CL_CRM_PERSON)
			{
				$template = $this->view["template"];
				if(!strlen($template))
				{
					// Default
					$template = "persons_webview.tpl";
				}
				lc_site_load("persons_web_view",$this);
				$this->read_template($template);
				$this->parse_worker($section_obj);
				return $this->parse();
			}
			if(($section_obj->class_id() == CL_CRM_SECTION)  || ($section_obj->class_id() == CL_CRM_COMPANY))
			{
				$company = $section_obj;
			}
		}

		if(empty($company))
		{
			$company_id = $this->view_obj->prop("company");
			if(!is_oid($company_id)) return t("pole asutust valitud");
			$company = obj($company_id);
		}

		//miskis lambikohas v6ib asutust ka vaja minna
		if($company->class_id() == CL_CRM_COMPANY) $this->company=$company;
		if(empty($level))$level = 0;
		$this->level = $level;
		$this->set_levels($level);//teeb siis erinevatest tasemetest massiivi, mida yldse kuvada ja paneb selle muutujasse $this->levels

		if(!empty($arr["search_results"])) //sel juhul tuleb otsingust
		{
			$this->sw = $arr["search_results"];
		}
		else
		{
			$this->sw = null;
		}

		return $this->parse_company($company);
	}

	function parse_company($company)
	{
		$departments = $this->view_obj->prop("departments");
		$template = $this->view["template"];
		$this->read_template($template);
		lc_site_load("persons_web_view",$this);
		if($this->view_obj->prop("department_grouping"))
		{
			if($this->is_template("DEPARTMENT"))
			{
				$this->jrks = array();
				if(in_array((0) , $this->levels) && (sizeof($this->levels) > 0)) $sections = array_merge(array($company) , $this->get_sections(array("section" => $company , "jrk" => 0)));
				else $sections = $this->get_sections(array("section" => $company , "jrk" => 0));
			foreach($sections as $section)
				{
					$this->section = $section; // eks seda l2heb vast mujal ka vaja... ametinimetuses n2iteks
					if(!(in_array($section->id(), $this->view_obj->prop("departments")))
						&& sizeof($this->view_obj->prop("departments"))>0 && array_sum($this->view_obj->prop("departments")) > 0) continue;
					if($this->view["with_persons"])
					{
						$workers = $this->get_workers($section);
						$this->parse_persons($workers);
					}
					//if(sizeof($workers) > 0)
					$this->parse_section($section);
					if($this->is_template("LEVEL".$this->jrks[$section->id()]."DEPARTMENT"))
						$department .= $this->parse("LEVEL".$this->jrks[$section->id()]."DEPARTMENT");
					else $department .= $this->parse("DEPARTMENT");
				}
				$this->vars_safe(array("DEPARTMENT" => $department));
			}
		}
		else //juhul kui osakondade j2rgi pole grupeeritud, siis saab veidi lihtsamini template jne teha
		{
			if($this->view["with_persons"])
			{
				$workers = $this->get_workers($company);
				$this->parse_persons($workers);
			}
			if($this->is_template("DEPARTMENT"))//juhuks kui DEPARTMENT sub sisse on j22nud... mida tegelt pole vaja
			{
				$department .= $this->parse("DEPARTMENT");
				$this->vars_safe(array("DEPARTMENT" => $department));
			}
		}
		$this->vars_safe(array(
			"name" => $company->prop("name"),
		));
                $ret = $this->parse();
		$this->vars["DEPARTMENT"] = "";
                return $ret;
	}

	function parse_section($section)
	{
		$secvars = array(
			"department_name" => $section->trans_get_val("name"),
			"document" => $section->prop("link_document"),
		);

		if(is_oid($section->prop("phone_id")) && $this->can("view", $section->prop("phone_id")))
		{
			$phone_obj = obj($section->prop("phone_id"));
		}
		else
		{
			 $phone_obj = $section->get_first_obj_by_reltype("RELTYPE_PHONE");
		}
		if(is_object($phone_obj))
		{
			$secvars["phone"] = $phone_obj->name();
			$secvars["phone_comment"] = $phone_obj->trans_get_val("comment");
		}

		if(is_oid($section->prop("email_id")))
		{
			$email_obj = obj($section->prop("email_id"));
		}
		else
		{
			$email_obj = $section->get_first_obj_by_reltype("RELTYPE_EMAIL");
		}
		if(is_object($email_obj))
		{
			$secvars["email"] = $email_obj->prop("mail");
		}

		if(is_oid($section->prop("telefax_id")))
		{
			$fax_obj = obj($section->prop("telefax_id"));
		}
		else
		{
			$fax_obj = $section->get_first_obj_by_reltype("RELTYPE_TELEFAX");
		}
		if(is_object($fax_obj))
		{
			$secvars["fax"] = $fax_obj->name();
		}

		$secvars["next_level_link"] = $this->mk_my_orb("parse_alias",
			array(
				"id" => $this->view_obj->id(),
				"section_o" => $section->id(),
				"view" => (1 + $this->view_no),
				"level" => $this->jrks[$section->id()],
				"company_id"	=> $this->company->id(),
		),
		CL_PERSONS_WEBVIEW);

		$secvars["address"] = $section->prop("contact.name");
		$this->vars_safe($secvars);
	}


	function get_workers($section)
	{
		$workers_list = $section->get_workers();
		//------------------------sorteerib k6vemad vennad ette;
		foreach($workers_list->arr() as $worker)
		{
			if($this->sw && !in_array($worker->id() , $this->sw))//kui on otsing, kuid tulemustes pole
			{
				continue;
			}
			$jrk = 0;
			if($this->can("view", $worker->prop("rank")))
			{
				$profession_obj = obj($worker->prop("rank"));
				$jrk = $profession_obj->prop("jrk");
			}
			$workers[] = array("worker" => $worker, "jrk" => $jrk);
		}
		foreach ($workers as $key => $row)
		{
			$person[$key]  = $row['worker'];
			$jrk_[$key] = $row['jrk'];
		}
		array_multisort($jrk_, SORT_DESC, $person, SORT_DESC, $workers);
		$principe = $this->view_obj->prop("persons_principe");
		if($principe[0]["principe"])
		{
			$workers = $this->person_sort($workers);
		}

		return $workers;
	}

	function get_sections($args)
	{
		extract($args);
		$sections = array();
		$section_list = new object_list($section->connections_from (array (
			"type" => "RELTYPE_SECTION",
		)));
		$section_arr = $this->sort_sections($section_list->arr());
		foreach($section_arr as $sec)
		{
			if(in_array(($jrk + 1) , $this->levels) && (sizeof($this->levels) > 0) && !$this->jrks[$sec->id()])
			{
				$sections[] = $sec;
			}
			$sections = array_merge($sections , $this->get_sections(array("section" => $sec, "jrk" => ($jrk+1))));
			$this->jrks[$sec->id()] = $jrk + 1;
		}
		return $sections;
	}

	function parse_profession($worker)
	{
		$profession = $directive = $directive_link= "";
		$profession_obj = $worker->get_first_obj_by_reltype("RELTYPE_RANK");
		//k6ik ametid mis tyybil on
		$conns = $worker->connections_from(array(
			"type" => "RELTYPE_RANK",
		));

		$professions = "";
		//nyyd oleks vaja siis kindlaks teha, et kus ameti all tyyp antud sektsioonis asub
		//kui siit midagi asjalikku ei leia, siis j22b algul leitud amet.
		foreach($conns as $conn)
		{
			$tmp_profession_obj = obj($conn->prop("to"));
			if($professions != "") $professions .= ",";
			$professions .= $tmp_profession_obj->name();
			$section_conns = $profession_obj->connections_to(array(
				"type" => 3,
			));
			foreach($section_conns as $section_conn)
			{
				if($this->section && $section_conn->prop("from") == $this->section->id()) $profession_obj = $tmp_profession_obj;
			}
		}

		if(is_object($profession_obj))
		{
			$profession = $profession_obj->name();
			$directive_link = $profession_obj->prop("directive_link");
			if(is_oid($profession_obj->prop("directive")) && $this->can("view", $profession_obj->prop("directive")))
			{
				$directive = $profession_obj->prop("directive");
			}
			else
			{
				$directive_obj = $profession_obj->get_first_obj_by_reltype("RELTYPE_DESC_FILE");
				if(is_object($directive_obj))
				$directive = $directive_obj->id();
			}
		}
		$profession_with_directive = $profession;
		if(is_oid($directive) && $this->can("view" , $directive ))
		{
			$file_inst = new file();
			$directive_obj = obj($directive);
			$directive_obj->trans_get_val("name");
			$profession_with_directive = '<a href ="'.$file_inst->get_url($directive , $directive_obj->trans_get_val("name")).'"  target=_new> '. $profession_with_directive.' </a>';
		}
		//kirjutab yle k6ik juhendi muutujad, kui on viide olemas
		if($directive_link)
		{
			$profession_with_directive = '<a href ="'.$directive_link.'"  target=_new> '. $profession.' </a>';
		}

		$professions = join (", " , $worker->get_profession_selection($this->company->id() , ($this->section ? array($this->section->id()) : null)));

		$this->vars_safe(array(
			"profession" => $profession,
			"professions" => $professions,
			"directive" => $directive,
			"profession_with_directive" => $profession_with_directive,
			"rank" => $profession,
		));
	}

	function parse_persons($workers)
	{
		$this->count = 0;
		$col = 0;
		$this->max_col = $col_num = $max_col = $this->view["columns"];
		$column = "";
		$row = "";
		$row_num = 0;
		$this->min_col = $this->view["min_cols"];
		$this->calculated=0;
		$this->order_array=array();
		if($this->is_template("ROW") && $this->is_template("COL"))
		{
			foreach($workers as $val)
			{
				$worker = $val["worker"];
				if(!empty($this->view["rows_by"]))//ametinimede kaupa grupeerimise porno, et erinevale reale 6ige arv tuleks jne
				{
					if(!$this->order_array) $this->make_order_array($workers);
					if(!$this->calculated) $col_num = $this->get_cols_num($row_num);
				}
				$c = "";
				if($this->is_template("worker"))
				{
					$this->parse_worker($worker);
					$c .= $this->parse("worker");
				}
				$this->vars_safe(array(
					"worker" => $c,
				));
				$column .= $this->parse("COL");
				$col++;
				$parsed = 0;
				if($col == $col_num)
				{
					$this->vars_safe(array(
						"COL" => $column,
					));
					$column = "";
					$row .= $this->parse("ROW");
					$col = 0;
					$parsed = 1;
					$col_num = $max_col;;
					$this->calculated = 0;
					$row_num++;
				}
			$this->count++;
			}
			if(!$parsed)//viimane rida v6ib olla tegemata
			{
				$this->vars_safe(array(
					"COL" => $column,
				));
				$column = "";
				$row .= $this->parse("ROW");
				$col = 0;
				$parsed = 1;
			}
			$this->vars_safe(array(
				"ROW" => $row,
			));

		}
		elseif($this->is_template("LINE"))
		{
			$c = "";
			foreach($workers as $val)
			{
				$worker = $val["worker"];
				if($this->view["rows_by"])//ametinimede kaupa grupeerimise porno, et erinevale reale 6ige arv tuleks jne
				{
					if(!$this->order_array) $this->make_order_array($workers);
					if(!$this->calculated) $col_num = $this->get_cols_num($row_num);
				}

				if($this->is_template("WORKER"))
				{
					$this->parse_worker($worker);
					$c .= $this->parse("WORKER");
				}
				$col++;
				$parsed = 0;
				if($col == $col_num)
				{
					$this->vars_safe(array(
						"WORKER" => $c,
					));
					$c = "";
					$row .= $this->parse("LINE");
					$col = 0;
					$parsed = 1;
					$col_num = $max_col;;
					$this->calculated = 0;
					$row_num++;
				}
			$this->count++;
			}
			if(!$parsed)//viimane rida v6ib olla tegemata
			{
				$this->vars_safe(array(
					"WORKER" => $c,
				));
				$column = "";
				$row .= $this->parse("LINE");
				$col = 0;
				$parsed = 1;
			}
			$this->vars_safe(array(
				"LINE" => $row,
			));
		}
		else
		{
			$c = "";
			foreach($workers as $val)
			{
				$worker = $val["worker"];
				if($this->is_template("WORKER"))
				{
					$this->parse_worker($worker);
					$c .= $this->parse("WORKER");
				}
			}
			$this->vars_safe(array(
				"WORKER" => $c,
			));
		}
	}

	function parse_worker($worker)
	{
		if(empty($this->section) && !$this->view_obj->prop("department_grouping"))
		{
			$this->section = $worker->get_first_obj_by_reltype("RELTYPE_SECTION");
		}
		if(!$this->section && !$this->view_obj->prop("department_grouping"))
		{
			$this->section = $worker->get_first_obj_by_reltype("RELTYPE_COMPANY");
		}

		//amet
		$this->parse_profession($worker);

		$vars = array(
			"name" => str_replace("¹" , "&#154;" , str_replace("©" , "&#138;" , $worker->name())),
			"wage_doc" => $worker->prop("wage_doc"),
			"ta1" => $worker->prop("udef_ta1"),
			"ta2" => $worker->prop("udef_ta2"),
			"ta3" => $worker->prop("udef_ta3"),
			"ta4" => $worker->prop("udef_ta4"),
			"ta5" => $worker->prop("udef_ta5"),
			"comment" => $worker->prop("comment"),
			"reception" => $worker->prop("work_hrs"),
		);

		//pilt 
		$photo="";
		$image_inst = new image();
		if(false && is_oid($worker->prop("picture")) && $this->can("view", $worker->prop("picture")))
		{
			$photo = $image_inst->make_img_tag_wl($worker->prop("picture"), NULL, NULL, array(), array("show_title" => false));
			$vars["photo_url"] = $image_inst->get_url_by_id($worker->prop("picture"));
		}
		else
		{
			$photo_obj = $worker->get_first_obj_by_reltype("RELTYPE_PICTURE");
			if(is_object($photo_obj))
			{
				$photo = $image_inst->make_img_tag_wl($photo_obj->id(), NULL, NULL, array(), array("show_title" => false));
				$vars["photo_url"] = $image_inst->get_url_by_id($photo_obj->id());
			}
		}


		//igast telefoninumbrid
		$phone_array = $this->phone_types;
		$phone = $phone_obj = $phones= $home_phone= $work_phone=$short_phone=$cell_phone=$in_phone=$skype="";

		$phone_obj = $worker->get_first_obj_by_reltype("RELTYPE_PHONE");

		if(is_object($phone_obj))
		{
			$phone = $phone_obj->name();
		}

		/*$phone_list = new object_list($worker->connections_from (array (
			"type" => "RELTYPE_PHONE",
		)));*/
		$phone_list = $worker->phones();
		foreach($phone_list->arr() as $phone_obj)
		{
			if(!$this->view_obj->prop("show_only_public_phones") || $phone_obj->prop("is_public"))
			{
				if(strlen($phone_obj->name()) > 2)
				{
					$phone_array[$phone_obj->prop("type")."_phones"][] = $phone_obj->name();
					$phone_array[$phone_obj->prop("type")."_phone"][0] = $phone_obj->name(); //need siis muutujad vastavalt erinevate telefonityypidele
					$phone_array["phone"][0] = $phone_obj->name();
					$phone_array["phones"][] = $phone_obj->name();
					$this->phone_types[$phone_obj->prop("type")."_phone"] = "";
				}
			}
		}

		//url
		$url = $url_obj = $urls = "";
		$url_obj = $worker->prop("url");
		if(!is_object($url_obj))
		{
			$url_obj = $worker->get_first_obj_by_reltype("RELTYPE_URL");
		}
		if(is_object($url_obj))
		{
			$url = $url_obj->prop("url");
		}
		$url_list = new object_list($worker->connections_from (array (
			"type" => "RELTYPE_URL",
		)));
		foreach($url_list->arr() as $url_obj)
		{
			if(strlen($urls) > 0 ) $urls .= ', ';
			if(strlen($urls) > 0 ) $urls .= $url_obj->prop("url");
		}

		//mail
		$email = $email_obj = $emails = "";
		$email_obj = $worker->prop("email");
		if(!is_object($email_obj))
		{
			$email_obj = $worker->get_first_obj_by_reltype("RELTYPE_EMAIL");
		}
		$name_with_email = $worker->name();
		if(is_object($email_obj)) $email_obj->prop("mail");
		if(strlen($email) > 3)
		{
			$name_with_email = '<a href =mailto:'.$email.'> '. $name_with_email.' </a>';
		}
		$mail_list = new object_list($worker->connections_from (array (
			"type" => "RELTYPE_EMAIL",
		)));
		foreach($mail_list->arr() as $mail_obj)
		{
			if(strlen($emails) > 0 ) $emails .= ', ';
			$emails .= $mail_obj->prop("mail");
		}

		//haridus
		$speciality = $school = $subject_field = "";
		$speciality_obj = $worker->get_first_obj_by_reltype("RELTYPE_EDUCATION");
		if(is_object($speciality_obj))
		{
			$speciality = $speciality_obj->prop("speciality");
			$school = $speciality_obj->prop("school");
			$subject_field = $speciality_obj->prop("field");
		}

		//palk
		$wage_doc_exist = "";
		if(is_oid($worker->prop("wage_doc")))
		{
			$wage_doc_exist = '<a href ='.$worker->prop("wage_doc").'> '. t("Palk").' </a>';
		}

		$url_params = array(
			"id" => $this->view_obj->id(),
			"section_o" => $worker->id(),
			"view" => (1 + $this->view_no),
			"company_id" => $this->company->id(),
		//	"section_id" => $this->section->id()
		);

		if(!empty($this->section))
		{
			$url_params["section_id"] = $this->section->id();
		}

		$next_level_link = $this->mk_my_orb("parse_alias",
			$url_params,
		CL_PERSONS_WEBVIEW);
		if(!$this->company)
		{
			$this->company = $worker->get_first_obj_by_reltype("RELTYPE_COMPANY");
		}
		$company = $this->company->name();

		$person_inst = new crm_person();
		$contact = $person_inst->get_short_description($worker->id());


		foreach($phone_array as $key => $val)
		{
			$this->vars_safe(array(
				$key => join(" ," , $val),
			));
		}

		//haridus
		$education = "";
		if($this->is_template("EDUCATION_SUB"))
		{
			$this->education_options = array(
				"pohiharidus" => t("P&otilde;hiharidus"),
				"keskharidus" => t("Keskharidus"),
				"keskeriharidus" => t("Kesk-eriharidus"),
				"diplom" => t("Diplom"),
				"bakalaureus" => t("Bakalaureus"),
				"magister" => t("Magister"),
				"doktor" => t("Doktor"),
				"teadustekandidaat" => t("Teaduste kandidaat"),
			);

			$ed_inst = new crm_person_education();
			$education_list = new object_list($worker->connections_from (array (
				"type" => "RELTYPE_EDUCATION",
			)));
			foreach($education_list->arr() as $edu)
			{
				$ip = "";
				if($edu->prop("in_progress"))
				{
					$ip = $this->parse("IN_PROGRESS");
				}
				$this->vars_safe(array(
					"degree" => $this->education_options[$edu->prop("degree")],
					"field" => $edu->prop("field.name"),
					"school" => $edu->prop("school"),
					"speciality" => $edu->prop("speciality"),
					"IN_PROGRESS" => $ip,
				));
				$education.= $this->parse("EDUCATION_SUB");
			}
		}

		//peatatud toosuhe
		$stopped = "";
		if($this->is_template("STOPPED"))
		{
			$stopped_reason = $subsitute = $stopped = "";
			$prev_list = new object_list($worker->connections_from (array (
				"type" => "RELTYPE_PREVIOUS_JOB",
			)));
			foreach($prev_list->arr() as $prev)
			{
//				if($prev->prop("end") > 1 && $prev->prop("end") < time())
//				{

					$stops = new object_list($prev->connections_from (array (
						"type" => "RELTYPE_CONTRACT_STOP",
					)));

					$sub_o = $prev->get_first_obj_by_reltype("RELTYPE_SUBSITUTE");
					if(is_object($sub_o))
					{
						$subsitute = $sub_o->name();
					}
					if(sizeof($stops->ids()))
					{
						foreach($stops->arr() as $stop)
						{
							$stopped_reasons.=" ".$stop->name();
						}
						$this->vars_safe(array(
							"stopped_reason" => $stopped_reasons,
							"subsitute" => $subsitute,
						));
						$stopped = $this->parse("STOPPED");
					}
//				}
			}
		}

		//praeguse toosuhte info
		$or = $worker->get_first_obj_by_reltype("RELTYPE_CURRENT_JOB");
		if(is_object($or))
		{
			$vars["org_rel_comment"] = $or->prop("comment");
			$vars["room"] = $or->prop("room");
			$vars["profession"] = $or->trans_get_val("profession.name");
		}

		//kraad
		$degree = $worker->get_first_obj_by_reltype("RELTYPE_DEGREE");
		if(is_object($degree))
		{
			$vars["degree_name"] = $degree->name();
			$vars["degree_subject"] = $degree->prop("subject");
		}

		//cv
		if($worker->prop("cv_doc"))
		{
			$file_inst = new file();
			$vars["cv_doc"] = $file_inst->get_url($worker->prop("cv_doc"), $worker->prop("cv_doc.name"));
		}
		$vars["cv_link"] = $worker->prop("cv_link");
		$vars["photo"] = $photo;
		$vars["phone"] = $phone;
		$vars["phones"] = $phones;
		$vars["contact"] = $contact;
		$vars["email"] = $email;
		$vars["emails"] = $emails;
		$vars["education"] = !$worker->prop("edulevel") ? "" : $person_inst->edulevel_options[$worker->prop("edulevel")];
		$vars["speciality"] = $speciality;
		$vars["name_with_email"] = $name_with_email;
		$vars["wage_doc_exist"] = $wage_doc_exist;
		$vars["next_level_link"] = $next_level_link;
		$vars["company"] = $company;
		if(!empty($this->section))
		{
			$vars["section"] = $this->section->trans_get_val("name");
		}
		$vars["url"] = $url;
		$vars["urls"] = $urls;
		$vars["school"] = $school;
		$vars["subject_field"] = $subject_field;
		$vars["EDUCATION_SUB"] = $education;
		$vars["STOPPED"] = $stopped;
		$vars = $this->to_ent($vars);
		$this->vars_safe($vars);

		$subs = array();
		foreach($vars as $key => $val)
		{
			$sub_name = strtoupper($key)."_SUB";
			if($this->is_template($sub_name))
			{
				if($val)
				{
					$subs[$sub_name] = $this->parse($sub_name);
				}
				else
				{
					$subs[$sub_name] = "";
				}
			}
		}
		$this->vars_safe($subs);

		$this->vars_safe(array(
			"EDU_SUB" => $this->parse("EDU_SUB")
		));
	}

	function get_cols_num($row)
	{
//		if(!$this->order_array) $this->make_order_array($workers);
		$this->calculated = 1;
		return sizeof($this->order_array[$row]);
	}

	//hull keemia....
	function make_order_array($workers)
	{
		$this->order_array = array();
		$x = 0;
		$cols = 0;
		$jrk = $workers[0]["jrk"];
		foreach($workers as $data)
		{
			if($data["jrk"] != $jrk || $cols >= $this->max_col )
			{
				$cols = 0;
				$jrk = $data["jrk"];
				$x++;
			}
			$this->order_array[$x][] = $data;
			$cols++;
		}
		$x = 0;
		while($x < 10)//miski suht random arv moment, et mitu korda ikka l2bi k2ia nimekiri et siis iga sammuga tasandab maksimaalselt yhe v6rra
		{
			$small_rows = 0;
			$row_num = 0;
			foreach($this->order_array as $key => $row)
			{
				if(sizeof($this->order_array[$row_num]) < $this->min_col){
					$small_rows++;
					if((sizeof($this->order_array[$row_num-1]) > 0)
						&& (sizeof($this->order_array[$row_num-1]) < $this->max_col)
						&& (sizeof($this->order_array[$row_num-1])+ sizeof($this->order_array[$row_num]) <= $this->max_col)
						&& $x<2)
					{
						$this->order_array[$row_num-1] = array_merge($this->order_array[$row_num-1] , $this->order_array[$row_num]);
						unset($this->order_array[$row_num]);
					}
					elseif((sizeof($this->order_array[$row_num+1]) > 0)
						&& (sizeof($this->order_array[$row_num+1]) < $this->max_col)
						&& (sizeof($this->order_array[$row_num+1])+ sizeof($this->order_array[$row_num]) <= $this->max_col)
						&& $x<2)
					{
						$this->order_array[$row_num+1] = array_merge($this->order_array[$row_num+1] , $this->order_array[$row_num]);
						unset($this->order_array[$row_num]);
					}
					elseif(((sizeof($this->order_array[$row_num-1])+ sizeof($this->order_array[$row_num]))/2 <= $this->max_col)
						&& $x>1
						&& $this->order_array[$row_num-1][0]["jrk"] == $this->order_array[$row_num][0]["jrk"]
					)
					{
						$this->order_array[$row_num] = array_merge(array(0 => $this->order_array[$row_num-1][sizeof($this->order_array[$row_num-1])-1]) , $this->order_array[$row_num]);
						unset($this->order_array[$row_num-1][sizeof($this->order_array[$row_num-1])-1]);
					}
				}
				$row_num++;
			}
			if($small_rows == 0) break;
			$x++;
			}
		$tmp = array();
		foreach ($this->order_array as $data)
		{
			if(sizeof($data) > 0) $tmp[] = $data;
		}
		$this->order_array = $tmp;
	}

	function show($arr)
	{
		return $this->parse_alias();
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		lc_site_load("persons_web_view",$this);
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
}
