<?php
// industrial_design_add.aw - T88stusdisainilahenduse veebist lisamine
/*

@classinfo syslog_type=ST_INDUSTRIAL_DESIGN_ADD relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property procurator_menu type=relpicker reltype=RELTYPE_PROCURATOR_MENU
	@caption Volinike kaust

	@property bank_payment type=relpicker reltype=RELTYPE_BANK_PAYMENT
	@caption Pangamakse objekt

	@property trademarks_menu type=relpicker reltype=RELTYPE_TRADEMARK_MENU
	@caption T&ouml;&ouml;stusdisainilahenduse taotluste kaust

	@property series type=relpicker reltype=RELTYPE_SERIES
	@caption Numbriseeria

@reltype BANK_PAYMENT value=11 clid=CL_BANK_PAYMENT
@caption Pangalingi objekt

@reltype PROCURATOR_MENU value=8 clid=CL_MENU
@caption Volinike kaust

@reltype TRADEMARK_MENU value=9 clid=CL_MENU
@caption T&ouml;&ouml;stusdisaini taotluste kaust

@reltype SERIES clid=CL_CRM_NUMBER_SERIES value=3
@caption Numbriseeria


*/

class industrial_design_add extends class_base
{
	function industrial_design_add()
	{
		$this->init(array(
			"tpldir" => "applications/patent",
			"clid" => CL_INDUSTRIAL_DESIGN_ADD
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/**
		@attrib name=parse_alias is_public="1" caption="Change"
	**/
	function parse_alias($arr)
	{
		$tm_inst = get_instance(CL_INDUSTRIAL_DESIGN);
		return $tm_inst->parse_alias($arr);
	}

	function get_folders_as_object_list($o, $level, $parent)
	{
		$ol = new object_list();
		if(isset($_GET["data_type"]) or isset($_GET["trademark_id"]))
		{
			$links = $o->meta("meaningless_sh__");

			if(is_array($links) && sizeof($links) == 8)
			{
				foreach($links as $link)
				{
					if($this->can("view", $link))
					{
						$ol->add($link);
					}
					else break;
				}
			}

			if(!is_array($links) or count($links) !== 8 or $ol->count() != 8)
			{
				$ol = new object_list();
				$o1 = new object();
				$o1->set_name("Taotleja andmed");
				$o1->set_class_id(CL_MENU);
				$o1->set_parent($o->id());
				$o1->save();
				$ol->add($o1);

				$o2 = new object();
				$o2->set_name("Autori andmed");
				$o2->set_class_id(CL_MENU);
				$o2->set_parent($o->id());
				$o2->save();
				$ol->add($o2);

				$o3 = new object();
				$o3->set_name("T&ouml;&ouml;stusdisainilahendus");
				$o3->set_class_id(CL_MENU);
				$o3->set_parent($o->id());
				$o3->save();
				$ol->add($o3);

				$o4 = new object();
				$o4->set_name("Prioriteedin".chr(245)."ue");
				$o4->set_class_id(CL_MENU);
				$o4->set_parent($o->id());
				$o4->save();
				$ol->add($o4);

				$o41 = new object();
				$o41->set_name("Menetluse peatamise n".chr(245)."ue");
				$o41->set_class_id(CL_MENU);
				$o41->set_parent($o->id());
				$o41->save();
				$ol->add($o41);

				$o51 = new object();
				$o51->set_name("Reproduktsioonid");
				$o51->set_class_id(CL_MENU);
				$o51->set_parent($o->id());
				$o51->save();
				$ol->add($o51);

				$o5 = new object();
				$o5->set_name("Riigil".chr(245)."iv");
				$o5->set_class_id(CL_MENU);
				$o5->set_parent($o->id());
				$o5->save();
				$ol->add($o5);

				$o6 = new object();
				$o6->set_name("Andmete kontroll/edastamine");
				$o6->set_class_id(CL_MENU);
				$o6->set_parent($o->id());
				$o6->save();
				$ol->add($o6);

				$o->set_meta("meaningless_sh__" , $ol->ids());
				$o->save();
			}
		}
		return $ol;
	}

	function make_menu_link($o, $ref = NULL)
	{
		if($this->can("view" , $_SESSION["patent"]["id"]))
		{
			$tr_inst = get_instance(CL_INDUSTRIAL_DESIGN);
			$res = $tr_inst->is_signed($_SESSION["patent"]["id"]);
			if($res["status"] == 1)
			{
				return aw_url_change_var()."#";
			}
		}

		static $jrk;

		if (empty($jrk))
		{
			$jrk = 0;
		}

		$item = industrial_design::$level_index[$jrk];

		if($jrk === 0)
		{
			$url = $_SERVER["SCRIPT_URI"]."?data_type=0" . (!empty($_GET["section"]) ? ("&section=".$_GET["section"]) : "");
		}
		elseif (in_array($item, $_SESSION["patent"]["checked"]))
		{
			$url = aw_url_change_var("data_type", $item);
			$url = aw_url_change_var("new_application" , null , $url);
		}
		else
		{
			$url = aw_url_change_var("new_application" , null) . "#";
		}

		++$jrk;
		$url = aw_url_change_var("new_application" , null , $url);
		return $url;
	}
}

?>
