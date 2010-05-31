<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_implementor_center.aw,v 1.5 2007/12/06 14:33:50 kristo Exp $
// procurement_implementor_center.aw - Hanngete keskkond pakkujale 
/*

@classinfo syslog_type=ST_PROCUREMENT_IMPLEMENTOR_CENTER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@tableinfo aw_procurement_implementor_centers index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property owner type=text store=no 
	@caption Omanik

@default group=p

	@property p_tb type=toolbar no_caption=1 store=no
	
	@layout p_l type=hbox width=30%:70%
		
		@property p_tr type=treeview no_caption=1 store=no parent=p_l

		@property p_tbl type=table no_caption=1 store=no parent=p_l

@default group=team

	@property team_tb type=toolbar store=no no_caption=1

	@property team_table type=table store=no no_caption=1

@groupinfo p caption="Hanked" submit=no
@groupinfo team caption="Meeskond" submit=no

@reltype MANAGER_CO value=1 clid=CL_CRM_COMPANY
@caption Haldaja firma

@reltype TEAM_MEMBER value=2 clid=CL_CRM_PERSON
@caption Meeskonna liige
*/

class procurement_implementor_center extends class_base
{
	const AW_CLID = 1068;

	function procurement_implementor_center()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_implementor_center",
			"clid" => CL_PROCUREMENT_IMPLEMENTOR_CENTER
		));

		$this->model = get_instance("applications/procurement_center/procurements_model");
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "owner":
				$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
				if (!$o)
				{
					return PROP_IGNORE;
				}
				$prop["value"] = html::obj_change_url($o);
				break;

			case "team_tb":
				$this->_team_tb($arr);
				break;

			case "team_table":
				$this->_team_table($arr);
				break;

			case "p_tb":
				$this->_p_tb($arr);
				break;

			case "p_tr":
				$this->_p_tr($arr);
				break;

			case "p_tbl":
				$this->_p_tbl($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "team_tb":
				if ($this->can("view", $arr["request"]["add_member"]))
				{
					$arr["obj_inst"]->connect(array("to" => $arr["request"]["add_member"], "type" => "RELTYPE_TEAM_MEMBER"));
				}
				$arr["obj_inst"]->set_meta("team_prices", $arr["request"]["prices"]);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["add_member"] = "0";
	}

	function _p_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$parent = $arr["request"]["p_id"];
		$po = obj($parent);
		if ($parent && $po->prop("state") == PROCUREMENT_PUBLIC)
		{
			$tb->add_menu_button(array(
				'name'=>'add_item',
				'tooltip'=> t('Uus')
			));
	
			$tb->add_menu_item(array(
				'parent'=>'add_item',
				'text'=> t('Pakkumine'),
				'link'=> html::get_new_url(CL_PROCUREMENT_OFFER, $parent, array("return_url" => get_ru(), "proc" => $parent))
			));
		}

		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud pakkumised'),
			'action' => 'delete_procurement_offers',
			'confirm' => t("Kas oled kindel et soovid valitud pakkumised kustudada?")
		));
	}

	function _p_tr($arr)
	{
		classload("core/icons");

		$ol = $this->model->get_my_procurements();
		foreach($ol->arr() as $o)
		{
			$arr["prop"]["vcl_inst"]->add_item(0, array(
				"id" => $o->id(),
				"name" => $arr["request"]["p_id"] == $o->id() ? "<b>".$o->name()."</b>" : $o->name(),
				"url" => aw_url_change_var("p_id", $o->id()),
			));
		}
	}

	function _init_p_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _p_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);

		$parent = $arr["request"]["p_id"];
		if (!$parent)
		{
			return;
		}

		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_OFFER,
			"procurement" => $parent,
			"lang_id" => array(),
			"site_id" => array()
		));
		$t->data_from_ol($ol, array("change_col" => "name"));
	}

	function _team_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa isik'),
		));

		$co = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
		// get sections and jobs from co
		$this->_crea_team_add_menu($tb, $co, "new");

		$url = $this->mk_my_orb("do_search", array(
			"clid" => CL_CRM_PERSON,
			"pn" => "add_member",
			"s" => array("search_co" => $co->name(), "show_vals" => array("def" => "def"))
		), "applications/crm/crm_participant_search");

		$tb->add_button(array(
			'name' => 'search',
			'img' => 'search.gif',
			'tooltip' => t('Otsi isikuid isikud meeskonnast'),
			'url' => "javascript:aw_popup_scroll('$url','Otsing',550,500)"
		));
		$tb->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'url' => "javascript:submit_changeform()"
		));
		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta isikud meeskonnast'),
			'action' => 'delete_team_members',
			'confirm' => t("Kas oled kindel et soovid isikud meeskonnast eemaldada?")
		));
		
	}

	function _crea_team_add_menu(&$tb, $co, $parent)
	{
		$cnt = 0;
		foreach($co->connections_from(array("type" => "RELTYPE_SECTION")) as $c)
		{
			$sub_cnt = $this->_crea_team_add_menu($tb, $c->to(), $c->prop("to"));
			$cnt++;

			if ($sub_cnt)
			{
				$tb->add_sub_menu(array(
					"parent" => $parent,
					"name" => $c->prop("to"),
					"text" => $c->prop("to.name")
				));
			}
			else
			{
				$link = $this->mk_my_orb("create_new_person", array(
					"unit" => $c->prop("to"),
					"parent" => $c->prop("to"),
					"alias_to" => $c->prop("to"),
					"reltype" => 2,
					"return_url" => get_ru()
				), CL_CRM_COMPANY);

				$tb->add_menu_item(array(
					"parent" => $parent,
					"text" => $c->prop("to.name"),
					"link" => $link,
					"title" => $c->prop("to.name")
				));
			}
		}
		foreach($co->connections_from(array("type" => "RELTYPE_PROFESSIONS")) as $c)
		{
			$cnt++;

			$link = $this->mk_my_orb("create_new_person", array(
				"profession" => $c->prop("to"),
				"unit" => $parent,
				"cat" => $c->prop("to"),
				"parent" => $parent,
				"alias_to" => $parent,
				"reltype" => 2,
				"return_url" => get_ru()
			), CL_CRM_COMPANY);

			$tb->add_menu_item(array(
				"parent" => $parent,
				"text" => $c->prop("to.name"),
				"link" => $link,
				"title" => $c->prop("to.name")
			));
		}
		return $cnt;
	}

	function _init_team_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
	        	'name' => 'phone',
			'caption' => t('Telefon'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'email',
			'caption' => t('E-post'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'section',
			'caption' => t('&Uuml;ksus'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'rank',
			'caption' => t('Ametinimetus'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'price',
			'caption' => t('Tunnihind'),
			'sortable' => '1',
		));

		$t->define_chooser(array(
			'name'=>'check',
			'field'=>'id',
		));
		$t->set_default_sortby("name");
	}

	function _team_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_team_t($t);

		$prices = $arr["obj_inst"]->meta("team_prices");	
		$co = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
		$cc = get_instance(CL_CRM_COMPANY);
		$sections = $cc->get_all_org_sections($co);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_TEAM_MEMBER")) as $c)
		{
			$section = $rank = "";
			$p = $c->to();

			$conns = $p->connections_to(array(
				"from.class_id" => CL_CRM_SECTION,
				"from" => $sections
			));
			if (count($conns))
			{
				$con = reset($conns);
				$section = $con->prop("from");
			}

			$t->define_data(array(
				"name" => html::obj_change_url($p),
				"phone" => html::obj_change_url($p->prop("phone")),
				"email" => html::obj_change_url($p->prop("email")),
				"section" => html::obj_change_url($section),
				"rank" => html::obj_change_url($p->get_first_obj_by_reltype("RELTYPE_RANK")),
				"id" => $p->id(),
				"price" => html::textbox(array(
					"name" => "prices[".$p->id()."]",
					"value" => $prices[$p->id()],
					"size" => 5
				))
			));
		}
	}
}
?>
