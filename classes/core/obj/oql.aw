<?php
/*
@classinfo  maintainer=kristo
*/

class oql
{
	/** executes an oql query 

		SELECT 
			prop1, prop2
		FROM
			CL_FOO
		WHERE
			((CL_FOO.prop1.RELTYPE_BOOYAH.foo * CL_FOO.prop1.RELTYPE_BOOYAH.foo) + 4) > 2
	**/
	public static function compile_query($oql)
	{
		return $GLOBALS["object_loader"]->ds->compile_oql_query($oql);
	}

	public static function execute_query($oql, $params)
	{
		$rv =  $GLOBALS["object_loader"]->ds->execute_oql_query(vsprintf($oql, $params));
		$d = array();

		// set acldata to memcache
		if (aw_ini_get("acl.use_new_acl"))
		{
			foreach($rv as $a_oid => $a_dat)
			{
				$tmp = safe_array(aw_unserialize($a_dat["acldata"]));
				$tmp["status"] = $a_dat["status"];
				$GLOBALS["__obj_sys_acl_memc"][$a_oid] = $tmp;
			}
		}

		foreach($rv as $oid => $dat)
		{
			if ($GLOBALS["object_loader"]->ds->can("view", $oid))
			{
				$d[$oid] = $rv[$oid];
			}
		}
		return $d;
	}
}