<?php

class infrastructure_import_obj extends _int_object
{
	public function invoke()
	{
		$countries_parent = $this->prop("countries_parent");
		if(!is_oid($countries_parent) || !$this->can("add", $countries_parent))
		{
			die("Ei saa riikide kausta alla salvestada!");
		}

		$jdata = file_get_contents($this->prop("countries_json.file"));
		$data = json_decode($jdata);
		foreach($data as $o)
		{
			if($o->model == "places.country")
			{
				$names[$o->fields->code]["et"] = iconv("UTF-8", aw_global_get("charset"), $o->fields->et_name);
				$names[$o->fields->code]["en"] = iconv("UTF-8", aw_global_get("charset"), $o->fields->en_name);
				$estnames[$o->fields->code] = $names[$o->fields->code]["et"];
			}
		}
		$ol = new object_list(array(
			"class_id" => CL_CRM_COUNTRY,
			"name" => $estnames,
			"lang_id" => array(),
			"site_id" => array(),
		));
		$name_to_object = array_flip($ol->names());

		foreach($names as $name)
		{
			if(isset($name_to_object[$name["et"]]))
			{
				$o = obj($name_to_object[$name["et"]]);
			}
			else
			{
				$o = obj();
				$o->set_class_id(CL_CRM_COUNTRY);
				$o->set_parent($countries_parent);
				$o->set_name($name["et"]);
			}
			$m = $o->meta("translations");
			$m[2]["name"] = $name["en"];
			$o->set_meta("trans_2_status", 1);
			$o->set_meta("translations", $m);
			$o->save();
		}

		die("DONE");
	}
}

?>
