<?php
/*
@classinfo  maintainer=markop
*/

define("PROCUREMENT_NEW", 0);
define("PROCUREMENT_PUBLIC", 1);
define("PROCUREMENT_INPROGRESS", 2);
define("PROCUREMENT_DONE", 3);
define("PROCUREMENT_CLOSED", 4);

class procurements_model extends class_base
{
	function procurements_model()
	{
		$this->init();
	}

	/**
		@attrib api=1
	**/
	function get_my_procurements()
	{
		$co = get_current_company();
		return new object_list(array(
			"class_id" => array(CL_PROCUREMENT),
			"lang_id" => array(),
			"site_id" => array(),
			"offerers" => $co,
		//	"state" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, PROCUREMENT_PUBLIC)
		));
	}

	/**
		@attrib api=1
	**/
	function get_procurements_for_co($co)
	{
		return new object_list(array(
			"class_id" => array(CL_PROCUREMENT),
			"lang_id" => array(),
			"site_id" => array(),
			"offerers" => $co
		));
	}

	/**
		@attrib api=1
	**/
	function get_requirements_from_procurement($p)
	{
		$ot = new object_tree(array(
			"class_id" => CL_MENU,
			"parent" => $p->id(),
			"lang_id" => array(),
			"site_id" => array()
		));
		$pts = $ot->ids() + array($p->id() => $p->id());
		return new object_list(array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT,
			"parent" => $pts,
			"lang_id" => array(),
			"site_id" => array()
		));
	}

	/**
		@attrib api=1
	**/
	function get_all_offers_for_procurement($p)
	{
		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_OFFER,
			"procurement" => $p->id(),
			"lang_id" => array(),
			"site_id" => array()
		));
		return $ol;
	}

	/**
		@attrib api=1
	**/
	function get_impl_center_for_co($co)
	{
		$conns = $co->connections_to(array(
			"from.class_id" => CL_PROCUREMENT_IMPLEMENTOR_CENTER,
			"type" => "RELTYPE_MANAGER_CO"
		));
		if (count($conns))
		{
			$con = reset($conns);
			$center = $con->from();
			return $center;
		}
		return false;
	}

	/**
		@attrib api=1
	**/
	function get_proc_center_for_co($co)
	{
		$conns = $co->connections_to(array(
			"from.class_id" => CL_PROCUREMENT_CENTER,
			"type" => "RELTYPE_MANAGER_CO"
		));
		if (count($conns))
		{
			$con = reset($conns);
			$center = $con->from();
			return $center;
		}
		return false;
	}

	/**
		@attrib api=1

		@returns array team member oid => team member price
	**/
	function get_team_from_center($center)
	{
		$ret = $center->meta("team_prices");
		foreach($center->connections_from(array("type" => "RELTYPE_TEAM_MEMBER")) as $c)
		{
			if (!isset($ret[$c->prop("to")]))
			{
				$ret[$c->prop("to")] = 0;
			}
		}
		return $ret;
	}

	/**
		@attrib api=1
	**/
	function get_pris_for_requirement($req)
	{	
		$proc = $this->get_procurement_from_requirement($req);
		return $this->get_pris_from_procurement($proc);
	}

	/**
		@attrib api=1
	**/
	function get_pris_from_procurement($proc)
	{
		if (!$proc)
		{
			return array();
		}
		$ret = array();
		foreach($proc->connections_from(array("type" => "RELTYPE_PRI")) as $c)
		{
			$pri = $c->to();
			$ret[$pri->prop("pri")] = $pri->name();
		}
		return $ret;
	}

	/**
		@attrib api=1
	**/
	function get_procurement_from_requirement($req)
	{
		$pt = $req->path();
		foreach($pt as $pi)
		{
			if ($pi->class_id() == CL_PROCUREMENT)
			{
				return $pi;
			}
		}
		return false;
	}

	/**
		@attrib api=1
	**/
	function get_team_from_procurement($proc)
	{
		$ret = array();
		foreach($proc->connections_from(array("type" => "RELTYPE_TEAM_MEMBER")) as $c)
		{
			$ret[$c->prop("to")] = $c->prop("to");
		}
		return $ret; 
	}
}
?>
