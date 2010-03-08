<?php
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_section_webside.aw,v 1.13 2008/01/31 13:54:16 kristo Exp $
// crm_section_webside.aw - ÃÃœksus weebis 
/*

@classinfo syslog_type=ST_CRM_SECTION_WEBSIDE relationmgr=yes maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

property section_picker type=relpicker reltype=RELTYPE_SECTION method=serialize field=meta table=objects
caption Üksus

@property section_picker type=select field=meta method=serialize
@caption Üksus

@property show_sub_sections type=checkbox ch_value=1
@caption Võta alamüksustest

@property cols type=textbox size=1
@caption Tulpasid

@default group=order

@property persons_order_table type=table no_caption=1 store=no

@default group=view
@default field=meta
@default method=serialize

@property show_label type=text store=1 subtitle=1
@caption Milliseid alljärgnevaid omadusi näidata? 


@property show_name type=checkbox ch_value=1 default=1
@caption Nimi

@property show_rank type=checkbox ch_value=1 default=1
@caption Ametikoht

@property show_picture type=checkbox ch_value=1 default=1
@caption Pilt

@property show_phone type=checkbox ch_value=1 default=1
@caption Telefon

@property show_email type=checkbox ch_value=1 default=1
@caption E-mail

@groupinfo order caption="Järjekord" 
@groupinfo view caption="Näitamine"

@reltype SECTION value=1 clid=CL_CRM_SECTION
@caption &uuml;ksus

@reltype ORG value=2 clid=CL_CRM_COMPANY
@caption Organisatsioon

*/

class crm_section_webside extends class_base
{
	function crm_section_webside()
	{
		$this->init(array(
			"tpldir" => "crm/crm_section_webside",
			"clid" => CL_CRM_SECTION_WEBSIDE
		));		
		$this->submerge=1;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "persons_order_table":
				$this->do_persons_order_table($arr);
			break;
			
			case "section_picker":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return PROP_IGNORE;
				}
				$org = $arr["obj_inst"]->get_first_obj_by_reltype('RELTYPE_ORG');
				if($org)
				{
					$org_inst = get_instance(CL_CRM_COMPANY);
					$sections = $org_inst->get_all_org_sections($org);
					
					$ol =& new object_list(array(
						"oid" => $sections,
					));
					
					foreach($ol->arr() as $tmp)
					{
						$prop["options"][$tmp->id()] = $tmp->prop("name"); 
					}
				}
			break;
			
			case "view":
				$prop["options"] = array(
					0 => t("Piltidega vaade"),
					1 => t("Tabelina"),
				);
			break;
			case "cols":
				if(!$prop["value"])
				{
					$prop["value"] = 2;
				}
			break;
		};
		return $retval;
	}
	
	function callb_ord($arr)
	{
		return html::textbox(array(
			"name" => "ord[".$arr['person_id']."]",
			"value" => $arr['ord'],
			"size" => 3
		));
	}
	

	
	function do_persons_order_table(&$arr)
	{
		$table = &$arr["prop"]["vcl_inst"];

		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,	
		));
		
		$table->define_field(array(
			"name" => "ord",
			"caption" => t("Järjekord"),
			"sortable" => 1,
			"callback" => array(&$this, "callb_ord"),
			"numeric" => 1,
			"callb_pass_row" => true,
			"align" => "center",
		));
		
		$section = get_instance(CL_CRM_SECTION);
		$workers = $section->get_section_workers($arr["obj_inst"]->id(), true);
		$orderinfo = $arr["obj_inst"]->meta("order");
		
		foreach ($workers->arr() as $worker)
		{
			$table->define_data(array(
				"name" => html::get_change_url($worker->id(), array() , $worker->name()),
				"ord" => $orderinfo[$worker->id()],
				"person_id" => $worker->id(),
			));	
		}
		$table->set_default_sortby("ord");
	}
	
	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/
	
	function parse_person(&$person, &$ob)
	{
		if($ob->prop("cols") == 1)
		{
			$this->read_template("person_one_col.tpl");
		}
		else
		{
			$this->read_template("person.tpl");
		}
		$this->sub_merge = 1;
		$this->vars(array(
			"name" => "",
			"rank" => "",
			"phone" => "",
			"email" => "",
			"photo" => "",
		));
		
		
		if($ob->prop("show_picture"))
		{
			$img_inst = get_instance(CL_IMAGE);
			$img = $person->get_first_obj_by_reltype("RELTYPE_PICTURE");
			
			if($img)
			{
				$this->vars(array(
					"photo" => html::img(array(
						"url" => $img_inst->get_url_by_id($img->id()),
					)),
				));
			}
			$retval.= $this->parse("SHOW_PHOTO");
		}
		
		$retval .= $this->parse("header");	
		if($ob->prop("show_name"))
		{
			if($doc = $person->get_first_obj_by_reltype("RELTYPE_DESCRIPTION_DOC"))
			{
				$this->vars = array(
					"name" => html::href(array(
						"caption" => $doc->prop("name"),
						"url" => $doc->id(),
					)),
				);
			}
			else
			{
				$this->vars = array(
					"name" => $person->prop("name"),
				);
			}
			
			$retval .= $this->parse("SHOW_NAME");
		}
			
		if($ob->prop("show_rank"))
		{
			if($person->prop("rank"))
			{
				$rank = &obj($person->prop("rank"));
				$this->vars(array(
					"rank" => $rank->name(),
				));
			}
			$retval.= $this->parse("SHOW_RANK");
		}
		
		
		if($ob->prop("show_phone"))
		{
			$phone = &obj($person->prop("phone"));
			$this->vars(array(
				"phone" => $phone->name(),
			));
			
			$retval.= $this->parse("SHOW_PHONE");
		}
		
		if($ob->prop("show_email"))
		{
			if($this->can("view", $person->prop("email")))
			{ 
				$email = &obj($person->prop("email"));
				$this->vars(array(
					"email" => $email->prop("mail"),
				));
			}
			$retval.= $this->parse("SHOW_EMAIL");
			
		}
		$retval .= $this->parse("footer");

		
		$this->sub_merge = 0;
		
		if($ob->prop("cols") == 1)
		{
			$this->read_template("frame_one_col.tpl");
		}
		else
		{
			$this->read_template("frame.tpl");
		}
		
		//$this->read_template("frame.tpl");
		
		return $retval;
	}
	
	function callback_post_save($arr)
	{
		$section = get_instance(CL_CRM_SECTION);
		$workers = $section->get_section_workers($arr["obj_inst"]->id(), true);
		
		if(is_array($arr["request"]["ord"]))
		{
			$arr["obj_inst"]->set_meta("order" ,$arr["request"]["ord"]);
			$arr["obj_inst"]->save();
		}
	}
	
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}
	
	function show($arr)
	{
		$ob = &obj($arr["id"]);
		$section = get_instance(CL_CRM_SECTION);
		
		if($ob->prop("show_sub_sections"))
		{
			$workers = $section->get_section_workers($ob->prop("section_picker"), true);
		}
		else
		{
			$workers = $section->get_section_workers($ob->prop("section_picker"), false);
		}
 		
		
		$cols = $ob->prop("cols");
		$workers_count = $workers->count();
		$workers = array_values($workers->arr());
		$rows = ceil($workers_count/$cols);
		
		$orderdata = $ob->meta("order");
		foreach ($workers as $worker)
		{
			$workers2[$orderdata[$worker->id()]][$worker->prop("lastname").$worker->name()] = $worker;
		}
		ksort($workers2);
		$workers = array();
		foreach ($workers2 as $workers_array)
		{
			ksort($workers_array);
			$workers += $workers_array;
		}
		$workers = array_values($workers);
		if($ob->prop("cols") == 1)
		{
			$this->read_template("frame_one_col.tpl");
		}
		else
		{
			$this->read_template("frame.tpl");
		}
		//This is amazing... i wrote this and I dont understand how it works, but it does
		for($i=0; $i<$workers_count; $i++)
		{
			
			$this->vars(array(
				"person" => $this->parse_person($workers[$i], $ob), 
			));
			$persondata[] = $this->parse("persons");
			$cur_row = ceil(($i + 1)/$cols);
			//It is time to parse separ ator now
			
			if((($i + 1)%$cols == 0))
			{
				if(!($cur_row == $rows))
				{
					$persondata[] = $this->parse("separator");
				}
			}
		}
		$this->vars(array(
			"personinfo" => join($persondata),
		));
		
		return $this->parse();	
		
	}
}
?>
