<?php

namespace automatweb;


class crm_company_customer_data_generator_obj extends _int_object
{
	const AW_CLID = 1507;

	public function generate($arr)
	{
		$sample_oid = $this->prop("sample_object");
		if(!$this->can("view", $sample_oid) || obj($sample_oid)->class_id() != CL_CRM_COMPANY_CUSTOMER_DATA)
		{
			print t("You need to set the sample object with class_id CL_CRM_COMPANY_CUSTOMER_DATA!");
			return $arr["return_url"];
		}

		$prop = $this->prop("seller_vs_buyer");
		if(!$prop)
		{
			print t("You need to set whether the organisation is saved to 'buyer' or 'seller' property.");
			return $arr["return_url"];
		}

		$ol = $this->get_orgs();
		$ol->remove($this->get_done_orgs());

		$seller = $buyer = obj($sample_oid)->prop(($prop == "seller" ? "buyer" : "seller").".name");

		print t("<b>GENERATING CUSTOMER DATA OBJECTS STARTED</b><br \><br \>");
		flush();

		foreach($ol->names() as $id => $name)
		{
			$$prop = $name;

			$cd_oid = obj($sample_oid)->save_new();
			$cd = obj($cd_oid);
			$cd->set_prop("name", sprintf(t("Kliendisuhe %s => %s"), $seller, $buyer));
			$cd->set_prop($prop, $id);
			$cd->save();
			$this->mark_org_done($id);
			printf(t("Customer data object generated for %s!<br \>"), $name);
			flush();
		}
		print t("<br \><b>GENERATING CUSTOMER DATA OBJECTS ENDED</b><br \><br \>");
		flush();

		return $arr["return_url"];
	}

	public function get_orgs()
	{
		$parent = $this->prop("dirs");

		if(count($parent) == 0)
		{
			return new object_list();
		}
		if($this->prop("subdirs"))
		{
			foreach($parent as $p)
			{
				$ot = new object_tree(array(
					"class_id" => CL_MENU,
					"parent" => $p,
					"lang_id" => array(),
					"site_id" => array(),
				));
				$parent += $ot->ids();
			}
		}

		return new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"parent" => $parent,
			"lang_id" => array(),
			"site_id" => array(),
		));
	}

	public function get_done_orgs()
	{
		$retval = array();

		if(file_exists(aw_ini_get("site_basedir")."/files/crm_customer_data_generator_".$this->id().".txt"))
		{
			$rd = file_get_contents(aw_ini_get("site_basedir")."/files/crm_customer_data_generator_".$this->id().".txt");
			$retval = strlen($rd) > 0 ? aw_unserialize($rd) : array();
		}

		return $retval;
	}

	public function mark_org_done($id)
	{
		$retval = $this->get_done_orgs();
		$retval[$id] = $id;
		$f = fopen(aw_ini_get("site_basedir")."/files/crm_customer_data_generator_".$this->id().".txt", "w");
		fwrite($f, aw_serialize($retval));
		fclose($f);
	}
}

?>
