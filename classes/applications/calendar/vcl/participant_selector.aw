<?php
/*
@classinfo maintainer=kristo
*/
class participant_selector extends core
{
	function participant_selector()
	{
		$this->init("");
	}


	function callb_human_name($arr)
	{
		return html::href(array(
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["id"],
				"return_url" => $this->request_uri,
				),CL_CRM_PERSON),
			"caption" => $arr["name"],
		));
	}

	function init_vcl_property($arr)
	{
		$table = new vcl_table();
		$datas = $arr["obj_inst"]->connections_to(array(
			"from.class_id" => CL_CALENDAR_REGISTRATION_FORM,
			"type" => 3 // regform.RELTYPE_DATA
		));
		$f = 0;
		if(count($datas) == 0)
		{
			$f = 1;
			$table->define_field(array(
				"name" => "name",
				"caption" => t("Nimi"),
				"sortable" => 1,
				"callback" => array($this, "callb_human_name"),
				"callb_pass_row" => true,
			));
			$table->define_field(array(
				'name' => 'phone',
				'caption' => t('Telefon'),
				'sortable' => '1',
			));

			$table->define_field(array(
				'name' => 'email',
				'caption' => t('E-post'),
				'sortable' => '1'
			));
			$table->define_field(array(
				'name' => 'rank',
				'caption' => t('Ametinimetus'),
				'sortable' => '1'
			));
		}
		$darr = array();
		$fields = array();
		$cff = new cfgform();
		$clsfs = array();

		$no_show = array("submit", "button", "text", "reset");
		foreach($datas as $d_c)
		{
			$to = $d_c->from();
			$darr[$to->prop("person_id")] = $to;

			if (!is_oid($to->meta("cfgform_id")) || !$this->can("view", $to->meta("cfgform_id")))
			{
				$ps = $to->get_property_list();
			}
			else
			{
				$ps = $cff->get_props_from_cfgform(array(
					"id" => $to->meta("cfgform_id")
				));
			}

			foreach($ps as $pn => $pv)
			{
				if (!isset($ps[$pn]) || in_array($pv["type"], $no_show))
				{
					continue;
				}

				if (trim($pv) != "" && $ps[$pn]["type"] !== "hidden")
				{
					$fields[$pn] = $ps[$pn]["caption"];
				}

				if ($ps[$pn]["type"] === "classificator")
				{
					$clsfs[$pn] = 1;
				}
			}
		}

		foreach($fields as $fld => $fld_c)
		{
			$table->define_field(array(
				"name" => $fld,
				"caption" => $fld_c,
				"sortable" => 1,
				"align" => "center"
			));
		}

		if (1 != automatweb::$request->arg("get_csv_file"))
		{
			$table->define_field(array(
				"name" => "change",
				"caption" => t("Muuda"),
				"align" => "center"
			));
			$table->define_chooser(array(
				"name" => "check",
				"field" => "id"
			));
		}

		$table->set_sortable(false);
		$conns = $arr['obj_inst']->connections_to(array());
		$cls = new classificator();
		$person = new crm_person();

		foreach($conns as $conn)
		{
			if($conn->prop("from.class_id") == CL_CRM_PERSON)
			{
				$dat = array();
				if (($_tmp = $darr[$conn->prop("from")]) && $f == 0)
				{
					$dat["id"] = $_tmp->id();
					$dat["change"] = html::get_change_url($_tmp->id(), array(), "Muuda");
					foreach($_tmp->properties() as $pn => $pv)
					{
						if (!isset($dat[$pn]))
						{
							if ($clsfs[$pn] == 1)
							{
								$pv = $_tmp->prop_str($pn);
							}
							if($ps[$pn]["type"] === "date_select")
							{
								$pv = $pv["day"].".".$pv["month"].".".$pv["year"];
							}
							$dat[$pn] = $pv;
						}
					}
				}
				elseif(($data = $person->fetch_person_by_id(array("id" => $conn->prop("from")))) && $f == 1)
				{
					$dat = array(
						"id" => $conn->prop("from"),
						"name" => $data["name"],
						"phone" => $data["phone"],
						"email" => $data["email"],
						"rank" => $data["rank"],
						"reg_data" => $regd,
						"change" => html::get_change_url($conn->prop("from"), array(), "Muuda")
					);
				}
				else
				{
					continue;
				}
				$table->define_data($dat);
			}
		}

		$propname = $arr["prop"]["name"];

		$tbl = $arr["prop"];
		$tbl["type"] = "table";
		$tbl["vcl_inst"] = $table;

		if (automatweb::$request->arg("get_csv_file"))
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-disposition: inline; filename=".t("osalejad.xls").";");
			$table->sort_by();
			die($table->draw());
		}

		return array(
			$propname => $tbl
		);
	}
}

?>
