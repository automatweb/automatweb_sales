<?php
/*
@classinfo syslog_type=ST_OBJECT_IMPORT_EXCEPTION relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general

@groupinfo exc caption="Erandid"
@default group=exc

@property exc type=table store=no no_caption=1

*/


class object_import_exception extends class_base
{
	function object_import_exception()
	{
		$this->init(array(
			"tpldir" => "admin/object_import_exception",
			"clid" => CL_OBJECT_IMPORT_EXCEPTION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "exc":
				$this->do_exc_table($arr);
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
			case "exc":
				$this->save_exc_table($arr);
				break;
		}
		return $retval;
	}	

	private function save_exc_table($arr)
	{
		$awa = new aw_array($arr["request"]["exc"]);
		$dat = array();
		foreach($awa->get() as $id => $d)
		{
			if ($d["from"] != "")
			{
				$dat[] = $d;
			}
		}

		$arr["obj_inst"]->set_meta("exc", $dat);
	}

	private function _init_exc_table(&$t)
	{
		$t->define_field(array(
			"name" => "from",
			"caption" => t("Mis asendada")
		));

		$t->define_field(array(
			"name" => "to",
			"caption" => t("Millega asendada")
		));

		$t->set_sortable(false);
	}

	private function do_exc_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_exc_table($t);
		
		$mid = 0;
		$awa = new aw_array($arr["obj_inst"]->meta("exc"));
		foreach($awa->get() as $id => $dat)
		{
			$t->define_data(array(
				"from" => html::textbox(array(
					"name" => "exc[$id][from]",
					"value" => $dat["from"]
				)),
				"to" => html::textbox(array(
					"name" => "exc[$id][to]",
					"value" => $dat["to"]
				)),
			));
			$mid = max($id, $mid);
		}

		for($i = 1; $i < 5; $i++)
		{
			$id = $mid+$i;
			$t->define_data(array(
				"from" => html::textbox(array(
					"name" => "exc[$id][from]",
					"value" => ""
				)),
				"to" => html::textbox(array(
					"name" => "exc[$id][to]",
					"value" => ""
				)),
			));
		}
	}

	/** Applies the changes defined in the exception
		@attrib api=1 params=pos

		@param ex_id required type=oid
			The exception object id

		@param text required type=string
			The text to replace texts in

		@returns
			The text with the exceptions replaced
	**/
	function do_replace($ex_id, $text)
	{
		$o = obj($ex_id);
		$awa = new aw_array($o->meta("exc"));
		foreach($awa->get() as $idx => $d)
		{
			if (substr($d["from"], -1) == "%")
			{
				if (substr(strtolower($text), 0, strlen($d["from"])) == strtolower(substr($d["from"],0,-1)))
				{
					$text = $d["to"];
				}
			}
			else
			{
				if (strtolower($text) == strtolower($d["from"]))
				{
					$text = $d["to"];
				}
			}
		}

		return $text;
	}
}
?>
