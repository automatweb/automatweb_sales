<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/party.aw,v 1.13 2008/05/09 11:22:25 robert Exp $
// party.aw - Pidu 
/*

@classinfo syslog_type=ST_PARTY relationmgr=yes no_status=1 r2=yea maintainer=kristo

@default table=objects
@default group=general

@tableinfo planner index=id master_table=objects master_index=brother_of
@default table=planner

@property start1 type=datetime_select field=start 
@caption Algus

@property end type=datetime_select
@caption L&otilde;pp

@property image type=releditor reltype=RELTYPE_FLYER rel_id=first use_form=emb
@caption Flyer

@property content type=textarea cols=60 rows=20 field=description
@caption Sisu

@property from_artist type=select multiple=1 table=objects field=meta method=serialize
@caption V&otilde;ta esineja objektist &uuml;rituse

@property relman type=aliasmgr no_caption=1 store=no
@caption Seostehaldur

@groupinfo artists caption="Esinejad" submit=no

@property artists_toolbar type=toolbar no_caption=1 group=artists
@caption Esinejate toolbar

@property artists type=table no_caption=1 group=artists
@caption Esinejad

@groupinfo projects caption="Projektid"

@property project_selector type=project_selector store=no group=projects all_projects=1
@caption Projektid

@reltype FLYER value=1 clid=CL_FLYER
@caption Flaier

@reltype ARTIST value=2 clid=CL_CRM_PERSON
@caption Esineja

*/

class party extends class_base
{
	const AW_CLID = 914;

	/**
		@attrib name=remove_artist
		@param id required type=int acl=edit
		@param group optional
		@param sel optional
	**/
	function remove_artist($arr)
	{
		$obj = obj($arr["id"]);
		foreach(safe_array($arr["sel"]) as $sel)
		{
			$obj->disconnect(array(
				"from" => $sel,
				"reltype" => "RELTYPE_ARTIST",
				"errors" => false,
			));
		}
		return $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => $arr["group"],
		));
	}
	function party()
	{
		$this->init(array(
			"clid" => CL_PARTY,
			"tpldir" => "applications/calendar",
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "from_artist":
				$prop["options"] = array(
					0 => t("--vali--"),
					"image" => t("Pilt"),
					"content" => t("Sisu"),
				);
				break;
			
			case "artists":
				$this->artists_tbl($arr);
				break;
				
			case "artists_toolbar":
				$this->artists_tb($arr);
				break;
		};
		return $retval;
	}
	
	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "artists":
				$arr["obj_inst"]->set_meta("artists", array(
					"ord" => $arr["request"]["ord"],
					"profession" => $arr["request"]["profession"],
				));
				$arr["obj_inst"]->save();
				break;
		}
		return $retval;
	}
	
	function artists_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"tooltip" => t("Lisa uus esineja"),
			"url" => $this->mk_my_orb("new", array("parent" => $arr["obj_inst"]->parent(), "return_url" => get_ru(), "reltype" => 2, "alias_to" => $arr["obj_inst"]->id()), CL_CRM_PERSON),
			"img" => "new.gif",
		));
		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "",
			"img" => "save.gif",
		));
		$tb->add_search_button(array(
			"pn" => "set_artist",
			"clid" => CL_CRM_PERSON,
			"multiple" => 1
		));
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Eemalda esineja"),
			"action" => "remove_artist",
			"confirm" => t("Oled kindel, et soovid valitud esinejad eemaldada?"),
			"img" => "delete.gif",
		));
	}
	
	function artists_tbl($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		$tb->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"sortable" => 1,
		));
		$tb->define_field(array(
			"name" => "profession",
			"caption" => t("Amet"),
			"sortable" => 1,
		));
		$tb->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
		$meta = $arr["obj_inst"]->meta("artists");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ARTIST")) as $art)
		{
			$artist = $art->to();
			$id = $artist->id();
			$data = array(
				"id" => $id,
				"name" => html::get_change_url($id, array(
					"return_url" => get_ru(),
				), $artist->name()),
				"ordx" => $meta["ord"][$id],
				"ord" => html::textbox(array(
					"name" => "ord[$id]",
					"value" => $meta["ord"][$id],
					"size" => 4,
				)),
			);
			$ranks = $artist->connections_from(array("type" => "RELTYPE_RANK"));
			if(count($ranks) > 1)
			{
				$options = array();
				foreach($ranks as $rank)
				{
					$options[$rank->prop("to")] = $rank->prop("to.name");
				}
				$data["profession"] = html::select(array(
					"name" => "profession[$id]",
					"options" => $options,
					"value" => $meta["profession"][$id],
					"multiple" => 1,
				));
			}
			else
			{
				$rank = reset($ranks);
				if($rank)
				{
					$data["profession"] = $rank->prop("to.name");
				}
			}
			$tb->define_data($data);
		}
		$tb->set_default_sortby("ordx");
		$tb->sort_by();
	}
	
	function request_execute($obj)
	{
		$this->read_template("party_show.tpl");
		$objs = new object_list(array(
			"brother_of" => $obj->brother_of(),
		));
		$evt = $obj->properties();
		$fa = safe_array($obj->prop("from_artist"));
		$proj = array();
		foreach($objs->arr() as $obz)
		{
			$obx = obj($obz->parent());
			if($obx->class_id() == CL_PROJECT)
			{
				$doc_id = $obx->prop("doc_id");
				$nm = $obx->name();
				if(is_oid($doc_id) && $this->can("view", $doc_id))
				{
					$nm = html::href(array(
						"caption" => $nm,
						"url" => obj_link($doc_id),
					));
				}
				$proj[] = $nm;
			}
		}
		$evt["project"] = implode(", ", $proj);
		$meta = $obj->meta("artists");
		unset($fa[0]);
		if(count($fa) > 0 && ($artist = $obj->get_first_obj_by_reltype("RELTYPE_ARTIST")))
		{
			if($fa["content"])
			{
				$evt["content"] = $artist->prop("notes");
			}
			if($fa["image"])
			{
				$evt["image"] = $artist->prop("picture");
			}
		}
		else
		{
			$art = array();
			foreach($artists = $obj->connections_from(array("type" => "RELTYPE_ARTIST")) as $artist)
			{
				$id = $artist->prop("to");
				$art[] = array(
					"id" => $id,
					"name" => $artist->prop("to.name"),
					"ord" => $meta["ord"][$id],
					"profession" => $meta["profession"][$id],
				);
			}
			uasort($art, array($this, "__sort_by_ord"));
			$xz = array();
			foreach($art as $a)
			{
				$x = html::href(array(
					"url" => obj_link($a["id"]),
					"caption" => $a["name"],
				));
				/*
				if(count($a["profession"]) > 0)
				{
					$profs = array();
					foreach($a["profession"] as $prof)
					{
						if(is_oid($prof) && $this->can("view", $prof))
						{
							$ob = obj($prof);
							$profs[] = $ob->name();
						}
					}
					$x .= " - ".implode(", ", $profs);
				}
				*/
				$xz[] = $x;
			}
			$evt["artist"] = implode(", ", $xz);
		}
		if($image = $obj->get_first_obj_by_reltype("RELTYPE_FLYER"))
		{
			$flyer_i = get_instance(CL_FLYER);
			$evt["image"] = $flyer_i->show($image);
			$evt["image_url"] = $flyer_i->image->get_url($image->prop('file1'));
		}
		$al = get_instance("alias_parser");
		$al->parse_oo_aliases($obj->id(), $evt["content"]);
		$this->vars($evt);
		return $this->parse();
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["set_artist"] = "0";
	}

	function callback_post_save($arr)
	{
		$ps = new popup_search();
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["set_artist"], 2);
	}
}
?>
