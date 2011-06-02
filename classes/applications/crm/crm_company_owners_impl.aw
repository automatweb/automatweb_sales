<?php

// tf parameter format is $docs_folder_oid|$selected_item_id. $docs_folder_oid|$docs_folder_oid when no subitem selected.

class crm_company_owners_impl extends class_base
{
	public function crm_company_owners_impl()
	{
		$this->init();
	}

	public function _get_owners_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_delete_button();

		return PROP_OK;
	}

	public function _get_owners_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol($arr["obj_inst"]->get_ownerships(), array("owner", "share_percentage"), crm_company_ownership_obj::CLID);

		return PROP_OK;
	}
}
