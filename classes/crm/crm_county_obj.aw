<?php

namespace automatweb;


class crm_county_obj extends _int_object
{
	const AW_CLID = 140;

	/** Returns object list of personnel_management_job_offer objects that are connected to the county.

		@attrib name=get_job_offers params=name api=1

		@param parent optional type=oid,array(oid) acl=view

		@param status optional type=int
			The status of the personnel_management_job_offer objects.

		@param childs optional type=boolean default=true
			If set true all objects that's parent is either the parent folder or subfolder of the parent folder is returned. Has effect only if parent folder is set. (folder = CL_MENU object)

		@param props optional type=array
			You can add here filters for the object list.

	**/
	function get_job_offers($arr)
	{
		$this->prms($arr);

		$ol_prms = array(
			"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
			"CL_PERSONNEL_MANAGEMENT_JOB_OFFER.RELTYPE_COUNTY" => parent::id(),
			"status" => $arr["status"],
			"parent" => $arr["parent"],
			"lang_id" => array(),
			"site_id" => array(),
		);

		if(is_array($arr["props"]) && count($arr["props"]) > 0)
		{
			$ol_prms += $arr["props"];
		}

		return new object_list($ol_prms);
	}

	/**
		@attrib name=get_residents api=1 params=name

		@param parent optional type=oid,array(oid)
			The oid(s) of the parent(s) of the crm_person objects.

		@param status optional type=int
			The status of the crm_person objects.

		@param by_jobwish optional type=bool

		@param childs optional type=boolean default=true
			If set true all objects that's parent is either the parent folder or subfolder of the parent folder is returned. Has effect only if parent folder is set. (folder = CL_MENU object)

		@param props optional type=array
			The second parameter for object_data_list. Used if return_as_odl is set.

		@param return_as_odl optional type=bool default=false
			If this is set, object_data_list is returned instead of object_list.

	**/
	function get_residents($arr)
	{
		$this->prms($arr);

		$ol_prms = array(
			"class_id" => CL_CRM_PERSON,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"parent" => $arr["parent"],
					"CL_CRM_PERSON.RELTYPE_PERSONNEL_MANAGEMENT" => $arr["personnel_management"],
				)
			)),
			"status" => $arr["status"],
		);
		if(!$arr["by_jobwish"])
		{
			$ol_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_ADDRESS.RELTYPE_MAAKOND" => parent::id(),
					// Do we really need 'em both?? Reltypes, I mean.
					"CL_CRM_PERSON.RELTYPE_CORRESPOND_ADDRESS.RELTYPE_MAAKOND" => parent::id(),
				),
			));
		}
		else
		{
			$ol_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					//"CL_CRM_PERSON.RELTYPE_WORK_WANTED.location_text" = "%".parent::name()."%",
					"CL_CRM_PERSON.RELTYPE_WORK_WANTED.RELTYPE_LOCATION" => parent::id(),
					"CL_CRM_PERSON.RELTYPE_WORK_WANTED.RELTYPE_LOCATION2" => parent::id(),
				),
			));
		}

		if($arr["return_as_odl"] && is_array($arr["props"]))
		{
			return new object_data_list($ol_prms, $arr["props"]);
		}
		else
		{
			return new object_list($ol_prms);
		}
	}

	function prms(&$arr)
	{
		$arr["parent"] = !isset($arr["parent"]) ? array() : $arr["parent"];
		if(!is_array($arr["parent"]))
		{
			$arr["parent"] = array($arr["parent"]);
		}
		$arr["status"] = !isset($arr["status"]) ? array() : $arr["status"];
		$arr["childs"] = !isset($arr["childs"]) ? true : $arr["childs"];

		if($arr["childs"] && (!is_array($arr["parent"]) || count($arr["parent"]) > 0))
		{
			$pars = $arr["parent"];
			foreach($pars as $par)
			{
				$ot = new object_tree(array(
					"class_id" => CL_MENU,
					"status" => $arr["status"],
					"parent" => $par,
					"lang_id" => array(),
				));
				foreach($ot->ids() as $oid)
				{
					$arr["parent"][] = $oid;
				}
			}
		}
	}
}
?>
