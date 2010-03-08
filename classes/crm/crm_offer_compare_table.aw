<?php
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_offer_compare_table.aw,v 1.3 2008/01/31 13:54:13 kristo Exp $
// crm_offer_compare_table.aw - Pakkumise v&otilde;rdlustabel
/*

@classinfo syslog_type=ST_CRM_OFFER_COMPARE_TABLE relationmgr=yes no_comment=1 no_status=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@default group=columns

	@property columns type=releditor reltype=RELTYPE_COLUMN mode=manager props=name,ord,divs,div_headers table_fields=name,ord,divs,div_headers table_edit_fields=name,ord,divs,div_headers
	@caption Tulbad

@default group=rows

	@property rows type=releditor reltype=RELTYPE_ROW mode=manager props=name,ord,divs,div_headers table_fields=name,ord,divs,div_headers table_edit_fields=name,ord,divs,div_headers
	@caption Read

@default group=content

	@property content type=table no_caption=1

@default group=preview

	@property preview type=text no_caption=1

@groupinfo columns caption="Tulbad"
@groupinfo rows caption="Read"
@groupinfo content caption="Sisu"
@groupinfo preview caption="Eelvaade" submit=no

@reltype COLUMN value=1 clid=CL_CRM_OFFER_COMPARE_TABLE_COLUMN
@caption tulp

@reltype ROW value=2 clid=CL_CRM_OFFER_COMPARE_TABLE_ROW
@caption rida

*/

class crm_offer_compare_table extends class_base
{
	function crm_offer_compare_table()
	{
		$this->init(array(
			"tpldir" => "crm/crm_offer_compare_table",
			"clid" => CL_CRM_OFFER_COMPARE_TABLE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "content":
				$this->_content($arr);
				break;

			case "preview":
				$prop["value"] = $this->show(array("id" => $arr["obj_inst"]->id()));
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
			case "content":
				$this->_save_content($arr);
				break;
		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _init_content_t(&$t, $o)
	{
		$columns = array();

		$t->define_field(array(
			"name" => "desc",
			"caption" => t("&nbsp;"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "desc_divs",
			"caption" => t("&nbsp;"),
			"align" => "center"
		));

		// get columns
		$ol = new object_list($o->connections_from(array("type" => "RELTYPE_COLUMN")));
		$ol->sort_by(array("prop" => "ord"));
		foreach($ol->arr() as $o)
		{
			$t->define_field(array(
				"name" => $o->id(),
				"caption" => $o->name(),
				"align" => "center"
			));

			// subcolumns, if any
			if ($o->prop("divs") > 1)
			{
				$headers = explode(",", $o->prop("div_headers"));
				for($i = 0; $i < $o->prop("divs"); $i++)
				{
					$t->define_field(array(
						"name" => $o->id()."_".$i,
						"caption" => $headers[$i],
						"align" => "center",
						"parent" => $o->id()
					));
					$columns[] = $o->id()."_".$i;
				}
			}
			else
			{
				$columns[] = $o->id();
			}
		}
		return $columns;
	}

	function _draw($o, &$t, $content_cb)
	{
		$cols = $this->_init_content_t($t, $o);

		$data = $o->meta("data");
		$ol = new object_list($o->connections_from(array("type" => "RELTYPE_COLUMN")));
		$ol->sort_by(array("prop" => "ord"));
		$idx = 1000;
		$cnt = 0;
		foreach($ol->arr() as $o)
		{
			$idx--;
			$hdrs = explode(",", $o->prop("div_headers"));
			for($i = 0; $i < max($o->prop("divs"),1); $i++)
			{
				$dat = array(
					"desc" => $o->name(),
					"desc_divs" => $hdrs[$i],
					"counter" => $cnt++,
					"desc_counter" => $idx
				);
				foreach($cols as $col)
				{
					$dat[$col] = $content_cb[0]->$content_cb[1]($o, $i, $col, $data);
				}

				$t->define_data($dat);
			}
		}

		$t->sort_by(array(
			"field" => "counter",
			"sorder" => "asc",
			"vgroupby" => array("desc" => "desc_counter")
		));
		$t->set_sortable(false);
	}

	function _edit_content_cb($o, $i, $col, &$data)
	{
		return html::textbox(array(
			"name" => "dat[".$o->id()."][$i][$col]",
			"value" => $data[$o->id()][$i][$col],
			"size" => 20
		));
	}

	function _content($arr)
	{
		$this->_draw($arr["obj_inst"], $arr["prop"]["vcl_inst"], array(&$this, "_edit_content_cb"));
	}

	function _save_content($arr)
	{
		$arr["obj_inst"]->set_meta("data", $arr["request"]["dat"]);
	}

	function _draw_content_cb($o, $i, $col, &$data)
	{
		return $data[$o->id()][$i][$col];
	}

	function show($arr)
	{
		$t = new aw_table(array("layout" => "generic"));
		$this->_draw(obj($arr["id"]), $t, array(&$this, "_draw_content_cb"));
		return $t->draw();
	}

	function generate_html($o, $item)
	{
		return $this->show(array("id" => $item->id()));
	}
}
?>
