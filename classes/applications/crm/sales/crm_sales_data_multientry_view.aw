<?php

class crm_sales_data_multientry_view
{
	const DATA_CONTAINER_ID = "crm_me_data";

	// reference to crm_sales workspace owner object
	private static $owner_organization;
	private static $forms_options_cache = array();

	public static function _get_multientry_toolbar(&$arr)
	{
		$toolbar = $arr["prop"]["vcl_inst"];
		$toolbar->add_save_button();
	}

	public static function _set_multientry_input_table(&$arr)
	{
		$r = class_base::PROP_OK;
		self::$owner_organization = $arr["obj_inst"]->prop("owner");
		$count = 0;

		foreach ($arr["request"][self::DATA_CONTAINER_ID] as $id => $data)
		{
			$data["id"] = $id;
			$count += self::_save_multientry_row($data);
		}

		class_base::show_success_text(sprintf(t("%s uut klienti lisatud"), $count));
		return $r;
	}

	private static function _save_multientry_row($data)
	{
		$r = false;

		if (isset($data["name"]) and strlen($data["name"]) > 1)
		{
			$form_id = null;
			try
			{
				if (!empty($data["form"]))
				{
					$form = obj($data["form"], array(), CL_CRM_CORPFORM);
					$form_id = $form->id();
				}
			}
			catch (Exception $e)
			{
			}

			// create customer object
			$customer = new object();
			$customer->set_class_id(crm_company_obj::CLID);
			$customer->set_name($data["name"]);
			$customer->set_parent(self::$owner_organization->id());
			$customer->set_prop("ettevotlusvorm" , $form_id);

			if (!empty($data["regcode"]))
			{
				$customer->set_prop("reg_nr", $data["regcode"]);
			}

			$customer->save();

			// add as customer, set contact info
			$customer_relation = self::$owner_organization->create_customer_relation(crm_company_obj::CUSTOMER_TYPE_BUYER, $customer);

			// add customer releation comment
			if (!empty($data["comment"]))
			{
				$customer_relation->set_comment($data["comment"]);
			}

			// contact data
			$customer->add_mail($data["email"], false);
			$customer->add_phone($data["phone"], false);

			// add contact people
			if (!empty($data["contact_person_ceo_name"]))
			{
				// parse name
				$name = explode(" ", $data["contact_person_ceo_name"], 2);
				if (count($name) === 1)
				{
					$firstname = "";
					$lastname = trim($name[0]);
				}
				else
				{
					$firstname = trim($name[0]);
					$lastname = trim($name[1]);
				}

				// parse and add position
				if (!empty($data["contact_person_ceo_position"]))
				{
					$ceo_position = $customer->add_profession(null, trim($data["contact_person_ceo_position"]));
				}

				// create person object
				$contact_ceo = new object();
				$contact_ceo->set_class_id(crm_person_obj::CLID);
				$contact_ceo->set_parent(self::$owner_organization->id());
				$contact_ceo->set_prop("firstname" , $firstname);
				$contact_ceo->set_prop("lastname" , $lastname);
				$contact_ceo->save();


				// add phone
				if (!empty($data["contact_person_ceo_phone"]))
				{
					$contact_ceo->set_phone($data["contact_person_ceo_phone"], "", false);
				}

				// add email
				if (!empty($data["contact_person_ceo_email"]))
				{
					$contact_ceo->set_email($data["contact_person_ceo_email"], false);
				}

				// add ceo position, create work rel, set company ceo prop
				$customer->add_employee($ceo_position, $contact_ceo);
				$customer->set_prop("firmajuht" , $contact_ceo->id());

				// save changes
				$contact_ceo->save();

				// add contact person to customer relation
				$customer_relation->set_prop("buyer_contact_person", $contact_ceo->id());
				$customer_relation->connect(array("to" => $contact_ceo, "type" => "RELTYPE_CONTACT_PERSON"));
			}

			if (!empty($data["contact_person_other_name"]))
			{
				// parse name
				$name = explode(" ", $data["contact_person_other_name"], 2);
				if (count($name) === 1)
				{
					$firstname = "";
					$lastname = trim($name[0]);
				}
				else
				{
					$firstname = trim($name[0]);
					$lastname = trim($name[1]);
				}

				// parse and add position
				if (!empty($data["contact_person_other_position"]))
				{
					$contact_position = $customer->add_profession(null, trim($data["contact_person_other_position"]));
				}

				// create person object
				$contact_other = new object();
				$contact_other->set_class_id(crm_person_obj::CLID);
				$contact_other->set_parent(self::$owner_organization->id());
				$contact_other->set_prop("firstname" , $firstname);
				$contact_other->set_prop("lastname" , $lastname);
				$contact_other->save();


				// add phone
				if (!empty($data["contact_person_other_phone"]))
				{
					$contact_other->set_phone($data["contact_person_other_phone"], "", false);
				}

				// add email
				if (!empty($data["contact_person_other_email"]))
				{
					$contact_other->set_email($data["contact_person_other_email"], false);
				}

				// create work rel
				$customer->add_employee($contact_position, $contact_other);

				// save changes
				$contact_other->save();

				// add contact person to customer relation
				$customer_relation->set_prop("buyer_contact_person2", $contact_other->id());
				$customer_relation->connect(array("to" => $contact_other, "type" => "RELTYPE_CONTACT_PERSON"));
			}

			// save changes to customer company and customer relateion objects
			$customer_relation->save();
			$customer->save();
			$r = true;
		}
		return $r;
	}

	public static function _get_multientry_input_table(&$arr)
	{
		$r = class_base::PROP_OK;
		$table = $arr["prop"]["vcl_inst"];
		self::_multientry_input_table_header($arr);
		$i = $arr["obj_inst"]->prop("multientry_rows") or $i = 15;
		$arr[self::DATA_CONTAINER_ID]["action"] = "new";
		self::$forms_options_cache = crm_company_obj::get_company_forms("array_abbreviations");

		while ($i--)
		{
			$arr[self::DATA_CONTAINER_ID]["id"] = $i;
			$table->define_data(array("customer_entry" => self::_multientry_row($arr)));
		}

		return $r;
	}

	private static function _multientry_row($arr)
	{
		$i = $arr[self::DATA_CONTAINER_ID]["id"];

		// insert a table in contacts edit cell
		$entry_table = new aw_table();
		$entry_table->set_titlebar_display(false);
		$entry_table->define_field(array(
			"name" => "customer_info"
		));
		$entry_table->define_field(array(
			"name" => "customer_co_form"
		));
		$entry_table->define_field(array(
			"name" => "customer_contacts"
		));
		$entry_table->define_field(array(
			"name" => "customer_additional_info"
		));
		$entry_table->define_field(array(
			"name" => "contact_name"
		));
		$entry_table->define_field(array(
			"name" => "contact_position"
		));
		$entry_table->define_field(array(
			"name" => "contact_email"
		));
		$entry_table->define_field(array(
			"name" => "contact_phone"
		));

		// add first row, ceo edit row
		$entry_table->define_data(array(
			"customer_info" => t("Ettev&otilde;tte nimi*: ") . self::name_edit($arr),
			"customer_co_form" => t("Vorm: ") . self::form_edit($arr),
			"customer_contacts" => t("E-post: ") . self::email_edit($arr),
			"customer_additional_info" => t("Kommentaar: ") . self::comment_edit($arr),

			// ceo
			"contact_name" => html::span(array("content" => html::bold(t("Kontakt 1 (juht) ")) . t("Nimi: "), "nowrap" => true)) . html::textbox(array(
				"name" => self::DATA_CONTAINER_ID . "[{$i}][contact_person_ceo_name]",
				"size" => 15
			)),

			"contact_position" => t("Amet: ") . html::textbox(array(
				"name" => self::DATA_CONTAINER_ID . "[{$i}][contact_person_ceo_position]",
				"size" => 10
			)),

			"contact_email" => t("E-post: ") . html::textbox(array(
				"name" => self::DATA_CONTAINER_ID . "[{$i}][contact_person_ceo_email]",
				"size" => 15
			)),

			"contact_phone" => t("Tel: ") . html::textbox(array(
				"name" => self::DATA_CONTAINER_ID . "[{$i}][contact_person_ceo_phone]",
				"size" => 10
			)),
		));

		// add second row, other contact person edit row
		$entry_table->define_data(array(
			"customer_info" => t("Reg. nr.: ") . self::regcode_edit($arr),
			"customer_co_form" => "",
			"customer_contacts" => t("Telefon: ") . self::phone_edit($arr),
			"customer_additional_info" => "",

			// contact person
			"contact_name" => html::span(array("content" => html::bold(t("Kontakt 2 ")) . t("Nimi: "), "nowrap" => true)) . html::textbox(array(
				"name" => self::DATA_CONTAINER_ID . "[{$i}][contact_person_other_name]",
				"size" => 15
			)),

			"contact_position" => t("Amet: ") . html::textbox(array(
				"name" => self::DATA_CONTAINER_ID . "[{$i}][contact_person_other_position]",
				"size" => 10
			)),

			"contact_email" => t("E-post: ") . html::textbox(array(
				"name" => self::DATA_CONTAINER_ID . "[{$i}][contact_person_other_email]",
				"size" => 15
			)),

			"contact_phone" => t("Tel: ") . html::textbox(array(
				"name" => self::DATA_CONTAINER_ID . "[{$i}][contact_person_other_phone]",
				"size" => 10
			)),
		));

		$entry_table->set_sortable(false);
		return $entry_table->draw();
	}

	public static function name_edit($arr)
	{
		$i = $arr[self::DATA_CONTAINER_ID]["id"];
		return html::textbox(array(
			"name" => self::DATA_CONTAINER_ID . "[{$i}][name]",
			"size" => 20
		));
	}

	public static function regcode_edit($arr)
	{
		$i = $arr[self::DATA_CONTAINER_ID]["id"];
		return html::textbox(array(
			"name" => self::DATA_CONTAINER_ID . "[{$i}][regcode]",
			"size" => 20
		));
	}

	public static function comment_edit($arr)
	{
		$i = $arr[self::DATA_CONTAINER_ID]["id"];
		return html::textbox(array(
			"name" => self::DATA_CONTAINER_ID . "[{$i}][comment]",
			"size" => 25
		));
	}

	public static function form_edit($arr)
	{
		$i = $arr[self::DATA_CONTAINER_ID]["id"];
		return html::select(array(
			"name" => self::DATA_CONTAINER_ID . "[{$i}][form]",
			"options" => self::$forms_options_cache
		));
	}

	public static function email_edit($arr)
	{
		$i = $arr[self::DATA_CONTAINER_ID]["id"];
		return html::textbox(array(
			"name" => self::DATA_CONTAINER_ID . "[{$i}][email]",
			"size" => 15
		));
	}

	public static function phone_edit($arr)
	{
		$i = $arr[self::DATA_CONTAINER_ID]["id"];
		return html::textbox(array(
			"name" => self::DATA_CONTAINER_ID . "[{$i}][phone]",
			"size" => 15
		));
	}

	private static function _multientry_input_table_header($arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$table->set_layout("cool");
		$table->define_field(array(
			"name" => "customer_entry",
			"valign" => "top",
			"caption" => t("Klientide sisestamine")
		));
		$table->set_sortable(false);
	}

	public static function _get_multientry_last_entries_table(&$arr)
	{
		$r = class_base::PROP_OK;
		$table = $arr["prop"]["vcl_inst"];
		self::_multientry_last_entries_table_header($arr);
		$limit = $arr["obj_inst"]->prop("tables_rows_per_page");

		// search buyers added by current user, limit ..
		$search = new crm_sales_contacts_search();
		$search->seller = $arr["obj_inst"]->prop("owner");
		$search->createdby = aw_global_get("uid");
		$search->set_sort_order("created-desc");
		$oids = $search->get_customer_relation_oids(new obj_predicate_limit($limit));

		if(count($oids))
		{
			foreach ($oids as $data)
			{
				$cro = new object($data["oid"]);
				$table->define_data(array(
					"name" => html::obj_change_url($cro->prop("buyer"), ($cro->prop("buyer.name") ? $cro->prop("buyer.name") : t("[Nimetu]"))),
					"created" => $cro->created()
				));
			}
		}

		return $r;
	}

	private static function _multientry_last_entries_table_header($arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")
		));
		$table->define_field(array(
			"name" => "created",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i",
			"caption" => t("Sisestatud")
		));
	}
}
