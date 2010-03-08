<?php
/*
@classinfo  maintainer=kristo
*/
class menu_site_admin extends core
{
	function menu_site_admin()
	{
		$this->init();
	}

	/**
		@attrib name=hide_menu 
		@param id required type=int acl=edit
		@param ru required
	**/
	function hide_menu($arr)
	{
		$o = obj($arr["id"]);
		if ($o->status() == STAT_ACTIVE && $o->class_id() == CL_MENU)
		{
			$o->set_status(STAT_NOTACTIVE);
			$o->save();
		}
		return $arr["ru"];
	}

	/**
		@attrib name=cut_menu 
		@param id required type=int acl=edit
		@param ru required
	**/
	function cut_menu($arr)
	{
		$_SESSION["site_admin"]["cut_menu"] = $arr["id"];
		return $arr["ru"];
	}

	/**
		@attrib name=paste_menu 
		@param after required type=int acl=edit
		@param ru required
	**/
	function paste_menu($arr)
	{
		$o = obj($arr["after"]);
		if ($this->can("view", $_SESSION["site_admin"]["cut_menu"]))
		{
			$cut = obj($_SESSION["site_admin"]["cut_menu"]);

			$mlp = new object_list(array(
				"class_id" => CL_MENU,
				"parent" => $o->parent(),
				"sort_by" => "jrk"
			));
			foreach($mlp->arr() as $id => $menu)
			{
				if ($get_next)
				{
					$next_ord = $menu->ord();
					$get_next = false;
				}
				if ($id == $o->id())
				{
					$get_next = true;
				}
			}
			if (!isset($next_ord))
			{
				$next_ord = $o->ord() + 100;
			}

			if ($cut->parent() != $o->parent())
			{
				// cut between menu areas
				$cut->set_parent($o->parent());
			}

			$cut->set_ord(($o->ord() + $next_ord) / 2);
			$cut->save();
		}
		$_SESSION["site_admin"]["cut_menu"] = null;
		return $arr["ru"];
	}


	/**
		@attrib name=hide_doc
		@param id required type=int acl=edit
		@param ru required
	**/
	function hide_doc($arr)
	{
		$o = obj($arr["id"]);
		if ($o->status() == STAT_ACTIVE && $o->class_id() == CL_DOCUMENT)
		{
			$o->set_status(STAT_NOTACTIVE);
			$o->save();
		}
		return $arr["ru"];
	}

	/**
		@attrib name=cut_doc 
		@param id required type=int acl=edit
		@param ru required
	**/
	function cut_doc($arr)
	{
		$_SESSION["site_admin"]["cut_doc"] = $arr["id"];
		return $arr["ru"];
	}

	/**
		@attrib name=paste_doc 
		@param after required type=int acl=edit
		@param ru required
	**/
	function paste_doc($arr)
	{
		$o = obj($arr["after"]);
		if ($this->can("view", $_SESSION["site_admin"]["cut_doc"]))
		{
			$cut = obj($_SESSION["site_admin"]["cut_doc"]);

			$mlp = new object_list(array(
				"class_id" => CL_DOCUMENT,
				"parent" => $o->parent(),
				"sort_by" => "jrk"
			));
			foreach($mlp->arr() as $id => $menu)
			{
				if ($get_next)
				{
					$next_ord = $menu->ord();
					$get_next = false;
				}
				if ($id == $o->id())
				{
					$get_next = true;
				}
			}
			if (!isset($next_ord))
			{
				$next_ord = $o->ord() + 100;
			}

			if ($cut->parent() != $o->parent())
			{
				// cut between menu areas
				$cut->set_parent($o->parent());
			}

			$cut->set_ord(($o->ord() + $next_ord) / 2);
			$cut->save();
		}
		$_SESSION["site_admin"]["cut_doc"] = null;
		return $arr["ru"];
	}
}
?>
