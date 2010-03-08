<?php
// $Header: /home/cvs/automatweb_dev/classes/export/menu_export.aw,v 1.9 2008/01/31 13:54:28 kristo Exp $
// menu_export.aw - helper class for exporting menus
/*
@classinfo  maintainer=kristo
*/
class menu_export
{
	function menu_export()
	{


	}
	////
	// !exports menu $id and all below it
	// if $ret_data is true, then the export arr is returned, not output
	function export_menus($arr)
	{
		extract($arr);
		if (!is_array($ex_menus) || count($ex_menus) == 0)
		{
			return;
		}

		$menus = array("0" => $id);

		// ok. now we gotta figure out which menus the user wants to export. 
		// he can select just the lower menus and assume that the upper onec come along with them.
		// biyaatch 

		$sels = array();
		$ol = new object_list(array(
			"oid" => $ex_menus,
			"site_id" => array(),
			"lang_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$sels[$o->id()] = $o;
			$pt = $o->path();
			foreach($pt as $p_o)
			{
				if ($p_o->id() == $id)
				{
					break;
				}
				$sels[$p_o->id()] = $p_o;
			}
		}
		
		// so now we have a complete list of menus to fetch.
		// so fetchemall
		foreach($sels as $o)
		{
			$row = $this->_get_row($o);
			if ($allactive)
			{
				$row["status"] = 2;
			}
			$this->append_exp_arr($row,&$menus,$ex_icons);
		}

		if ($ret_data)
		{
			return $menus;
		}

		/// now all menus are in the array with all the other stuff, 
		// so now export it.
		header("Content-type: x-automatweb/menu-export");
		header("Content-Disposition: filename=awmenus.txt");
		echo serialize($menus);
		die();
	}

	function append_exp_arr($db, $menus,$ex_icons)
	{
		$ret = array();
		$ret["db"] = $db;
		if (!is_array($menus[$db["parent"]]))
		{
			$menus[$db["parent"]] = array();
		}
		$menus[$db["parent"]][] = $ret;
	}

	function _get_row($o)
	{
		return array(
			"parent" => $o->parent(),
			"oid" => $o->id(),
			"comment" => $o->comment(),
			"name" => $o->name(),
			"created" => $o->created(),
			"createdby" => $o->createdby(),
			"modified" => $o->modified(),
			"modifiedby" => $o->modifiedby(),
			"status" => $o->status(),
			"jrk" => $o->ord(),
			"alias" => $o->prop("alias"),
			"class_id" => $o->class_id(),
			"brother_of" => $o->brother_of(),
			"metadata" => aw_serialize($o->meta()),
			"periodic" => $o->prop("periodic"),
			"type" => $o->prop("type"),
			"link" => $o->prop("link"),
			"clickable" => $o->prop("clickable"),
			"target" => $o->prop("target"),
			"ndocs" => $o->prop("ndocs"),
			"admin_feature" => $o->prop("admin_feature")
		);
	}
};
?>
