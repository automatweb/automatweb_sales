<?php

namespace automatweb;

/*
@classinfo  maintainer=robert
*/

class bug_comment_obj extends _int_object
{
	const AW_CLID = 1058;

	function save($exclusive = false, $previous_state = null)
	{
		//miskit sellise nimelist propi on vaja, et otsinguid teha jne
		if($this->prop("parent.class_id") == CL_BUG)
		{
			$this->set_prop("bug" , $this->parent());
		}
		else
		{
			$bugs = new object_list(array(
				"CL_BUG.RELTYPE_COMMENT" => $this->id(),
				"lang_id" => array(),
				"class_id" => CL_BUG,
				"site_id" => array(),
				//"limit" => 1,
			));
			$bug = reset($bugs->ids());
			$this->set_prop("bug" , $bug);
		}
		return parent::save($exclusive, $previous_state);
	}

	function set_prop($name,$value)
	{
		parent::set_prop($name,$value);
	}
}
