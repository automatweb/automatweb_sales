<?php
/*
@classinfo  maintainer=kristo
*/

class object_tree extends _int_obj_container_base
{
	/////////////////////////////////////////////
	// private variables
	var $tree;	// array - tree structure, 2 level - level one is parent, level 2 is oid => object
	var $tree_names;

	////////////////////////////////////////////
	// public functions

	/** constructs and optionally initializes the object tree. the tree always contains folders and if you don't filter them out, also other objects under the folders
		@attrib api=1

		@param param optional type=array
			array of properties that are used as a filter when creating the tree. the parent
			  member is required in the filter, if it is not present, tree will not be initialized.

		@comment
			in addition to the member functions described here, this class also implements all the object class mutator functions, only in this case they apply for all objects in the tree, for instance #php# $ot->set_name("fish"); #/php# will change all the objects names in the tree to "fish". you must also call #php# $ot->save() #/php# to save the objects, because they are not saved automatically

		@errors
			none

		@returns
			none

		@examples
			// creates a tree that contains all folders below object 666, the tree will contain
			// all folders below it
			// and all objects in those folders
			$ot = new object_tree(array(
				"parent" => 666,
				"class_id" => CL_FILE
			));
	**/
	function object_tree($param = NULL)
	{
		if ($param != NULL && !is_array($param))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_tree:constructor(): if you specify a parameter, it must be a filter array!")
			));
		}

		$this->_int_init_empty();

		if (is_array($param))
		{
			$this->_int_load($param);
		}
	}

	/** filters the object tree, or creates a new tree from the filter
		@attrib api=1

		@param filter required type=array
			- array of filter parameters, according to which to filter the object tree
			filter parameters are documented in $AWROOT/docs/tutorials/object_list.txt

		@param new optional type=bool
			if set to true, new tree is made, if false, current tree is filtered, defaults to true

		@errors
			- if creating a new tree, no parent parameter is passed, error is thrown

		@returns
			none

		@examples
			$ot = new object_tree(array(
				"parent" => 5,
				"name" => "%foo%",
				"class_id" => CL_BAR,
				"cool_property" => true
			));

			$ot->filter(array(
				"status" => STAT_ACTIVE
			)), false); // filters the current tree for all objects that are active

			$ot->filter(array(
				"parent" => 90,
				"status" => STAT_ACTIVE
				)), true); // creates a new tree of all the objects below folder 90 that are active
	**/
	function filter($filter, $new = true)
	{
		if (!is_array($filter))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_tree::filter(): filter parameter must be an array!")
			));
		}

		if ($new)
		{
			$this->_int_load($filter);
		}
		else
		{
			$this->_int_filter_current($filter);
		}
	}

	/** returns an object_list instance that contains all the objects in the current tree
		@attrib api=1 params=name

		@param add_root optional type=bool
			wheather to add root item to list or not
		@errors
			none

		@returns
			object list instance that contains all the objects in the current tree

		@examples
			$ot = new object_tree(array(
				"parent" => 5,
				"name" => "%foo%",
				"class" => CL_BAR,
				"cool_property" => true
			));
			$ol = $ot->to_list();
	**/
	function to_list($arr = array())
	{
		$root = isset($arr["add_root"])?$arr["add_root"]:false;
		$ol = new object_list();
		foreach($this->tree as $pt => $objs)
		{
			if($root)
			{
				$ol->list[$pt] = $pt;
			}
			foreach($objs as $oid => $o)
			{
				$ol->list[$oid] = $oid;
				$ol->list_objdata[$oid] = $this->tree_objdata[$pt][$oid];
			}
		}
		$ol->list_names = $this->tree_names;
		return $ol;
	}

	/** iterates over all objects in the tree and calls the specified object member function for each object in the tree with the specified parameters.
		@attrib api=1 params=name

		@param func required type=text
			object class function name to call for each object in the tree

		@param params optional
			array of parameters to pass to the member function

		@param save optional type=bool
			whether to also call the save method on all objects, optional, defaults true

		@errors
			- error is thrown if no member function with the specified name exists in the object class
			- acl error is thrown if the user has no change access for any object in the tree


		@returns
			none

		@examples
			$ot = new object_tree(array(
				"parent" => 90,
				"status" => STAT_ACTIVE
			));
			$ot->foreach_o(array(
				"func" => "set_name",
				"params" => array("kala"),
				"save" => true
			));
	**/
	function foreach_o($param)
	{
		if (!is_array($param))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_tree::foreach_o(): parameter must be an array")
			));
		}

		if (!isset($param["save"]))
		{
			$param["save"] = true;
		}

		$func = $param["func"];

		if (!$GLOBALS["object_loader"]->is_object_member_fun($func))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => sprintf(t("object_tree::foreach_o(): %s is not a member function of the object class!"), $func)
			));
		}

		$cnt = 0;
		foreach($this->tree as $_pt => $level)
		{
			foreach($level as $_oid => $o)
			{
				$cnt++;

				$tmp = obj($_oid);
				if (isset($param["params"]))
				{
					call_user_func_array(array(&$o, $func), $param["params"]);
				}
				else
				{
					$o->$func();
				}

				if ($param["save"])
				{
					$o->save();
				}
			}
		}

		return $cnt;
	}

	/** iterates over the tree, calling the specified user function with each object as the parameter
		@attrib api=1 params=name

		@param func required
			 the name of the function to call, the function gets the object reference as the parameter
			type: text (global function name) , array (array(&$this, "func") - class member function name)

		@param param optional
			single param that can optionally passed to the function, optional, type: any

		@param save optional type=bool
			 - whether to also call the save method on all objects, optional, defaults to false

		@errors
			- error is thrown, if the specified user function does not exist
			- acl error is thrown if the user has no change access for any object in the tree

		@returns
			none

		@examples
			function ot_cb(&$o, $parent)
			{
				$o->set_parent($parent);
			}

			$ot->foreach_cb(array(
				"func" => "ot_cb",
				"param" => 666,
				"save" => true
			));
	**/
	function foreach_cb($param)
	{
		if (!is_array($param))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_tree::foreach_cb(): parameter must be an array!")
			));
		}

		if (!isset($param["save"]))
		{
			$param["save"] = true;
		}

		if (is_array($param["func"]))
		{
			if (!method_exists($param["func"][0], $param["func"][1]))
			{
				error::raise(array(
					"id" => ERR_PARAM,
					"msg" => sprintf(t("object_tree::foreach_cb(): callback method %s does not exist in class %s!"), $param["func"][1], get_class($param["func"][1]))
				));
			}
		}
		else
		if ($param["func"] == "" || !function_exists($param["func"]))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => sprintf(t("object_tree::foreach_cb(): callback function %s does not exist!"), $param["func"][1])
			));
		}

		$cnt = 0;
		$param["param"] = isset($param["param"]) ? $param["param"] : null;
		foreach($this->tree as $_pt => $level)
		{
			foreach($level as $_oid => $o)
			{
				$o = obj($_oid);
				if (is_array($param["func"]))
				{
					$meth = $param["func"][1];
					isset($param["param"]) ? $param["func"][0]->$meth($o, $param["param"]) : $param["func"][0]->$meth($o);
				}
				else
				{
					isset($param["param"]) ? $param["func"]($o, $param["param"]) : $param["func"]($o);
				}

				if (!empty($param["save"]))
				{
					$o->save();
				}
				$cnt++;
			}
		}

		return $cnt;
	}

	/** returns an array of objects under a certain object in the tree
		@attrib api=1

		@param param required
			parent oid, required
			type: oid / string / object instance

		@errors
			none

		@returns
			array, key is object id, value is object class instance

		@examples
			function walker(&$ot, $parent)
			{
				foreach($ot->level($parent) as $o)
				{
					echo "o = ".$o->name()."\n";
					walker($ot, $o->parent());
				}
			}
			$ot = new object_tree(array(
				"parent" => 1
			));
			walker($ot, 1); // prints out the names of all the objects in the list #/php#
	**/
	function level($param)
	{
		$oid = $GLOBALS["object_loader"]->param_to_oid($param);
		$tmp =  (isset($this->tree[$oid]) && is_array($this->tree[$oid]) ? $this->tree[$oid] : array());
		$ret = array();
		foreach($tmp as $oid => $a)
		{
			$ret[$oid] = obj($oid);
		}
		return $ret;
	}

	/** returns an instance of the object tree class that contains a subtree of the current tree, starting at the specified level
		@attrib api=1

		@param param required
			- parent object oid, to start the tree from
			type: oid / string / object instance

		@errors
			none

		@returns
			instance of the object_tree class that contains the subtree starting at the specified parent

		@examples
			$ot = new object_tree(array(
				"parent" => 9
			));
			$ot2 = $ot->subtree(90);
	**/
	function subtree($param)
	{
		$oid = $GLOBALS["object_loader"]->param_to_oid($param);
		return $this->_int_subtree($oid);
	}

	/** adds an object or objects to the tree.
		@attrib api=1

		@param param required
			object to add, required.
			type: integer (object id), string (object alias), object instance, object_list instance, object_tree instance

		@errors
			none

		@returns
			number of objects actually inserted in the tree

		@examples
			$ot = new object_tree(array(
				"parent" => 90,
				"name" => "tunafish",
				));
				$ot->add(new object_list(array("status" => STAT_ACTIVE)));

			//now ot contains all objects that are under folder 90 and are named tunafish or are active
	**/
	function add($param)
	{
		$cnt = 0;
		$oids = $GLOBALS["object_loader"]->param_to_oid_list($param);
		foreach($oids as $oid)
		{
			$o = new object($oid);
			$this->tree[$o->parent()][$o->id()] = $o;
			$cnt++;
		}
		return $cnt;
	}

	/** deletes all objects in the tree and clears the tree
		@attrib api=1

		@errors
			- acl error is thrown if user has no delete access for any object in the tree

		@returns
			the number of objects deleted

		@examples
			$ot = new object_tree(array(
				"parent" => 90,
				"name" => "tunafish",
			));
			$ot->delete();
	**/
	function delete($full = null)
	{
		return parent::delete($full);
	}

	/** removes the specified object(s) from the tree and all objects under those
		@attrib api=1

		@param param required
			object to remove
			type: integer (object id), string (object alias), object instance, object_list instance, object_tree instance

		@errors
			none

		@returns
			the actual number of objects removed

		@examples
			$ot = new object_tree(array(
				"parent" => 90,
				"name" => "tunafish",
			));
			$ot->remove(new object_list(array(
				"status" => STAT_ACTIVE
			)));

			//now the tree contains all objects that are under folder 90 are named "tunafish" and are not active
	**/
	function remove($param)
	{
		$cnt = 0;
		$oids = $GLOBALS["object_loader"]->param_to_oid_list($param);
		foreach($oids as $oid)
		{
			$o = new object($oid);
			$this->_int_req_remove($o, $cnt);
		}
		return $cnt;
	}

	/** calls the save method on all the members of the tree
		@attrib api=1

		@errors
			- throws acl error, if user has no write access to any object in the tree

		@returns
			count of objects saved

		@examples
			$ot = new object_tree(array(
				"parent" => 90
			));
			$ot->foreach_o(array(
				"func" => "set_name",
				"params" => array("automatweb"),
				"save" => false
			));
			$ot->save();
	**/
	function save()
	{
		return parent::save();
	}

	/** removes all objects from the tree
		@attrib api=1

		@errors
			none

		@returns
			none

		@examples
			$ot = new object_tree(array(
				"parent" => "90"
			));
			$ot->remove_all();
	**/
	function remove_all()
	{
		$this->_int_init_empty();
	}

	/** returns all object id's in the current tree
		@attrib api=1

		@errors
			none

		@returns
			array of oid's that are currently in the tree

		@examples
			$ot = new object_tree(array(
            	"parent" => 90
		));
		$ids = $ot->ids();
	**/
	function ids()
	{
		$ret = array();
		foreach($this->tree as $_pt => $level)
		{
			foreach($level as $oid => $o)
			{
				$ret[] = $oid;
			}
		}

		return $ret;
	}

	/** returns array of arrays of arrays of ... keys being OIDs
		@attrib api=1

		@errors
			none

		@returns
			array of arrays of arrays of ... keys being OIDs

		@examples
			$ot = new object_tree(array(
            	"parent" => 90
		));
		$hierarchy = $ot->ids_hierarchy();

		$hierachy = array(
			[lvl_1_child_1_oid] => array(
				[lvl_2_child_1_oid] => array(
					[lvl_3_child_1_oid] => array(
						...
					)
				),
				[lvl_2_child_2_oid] => array(),
			)
			[lvl_1_child_2_oid] => array()
			...
		);
	**/
	public function ids_hierarchy()
	{
		$this->parents = array();
		foreach($this->tree as $pt => $children)
		{
			foreach($children as $id)
			{
				$this->parents[$id] = $pt;
			}
		}
		$roots = array_diff(array_values($this->parents), array_keys($this->parents));
		return $this->ids_hierarchy_lvl(reset($roots));
	}

	protected function ids_hierarchy_lvl($pt)
	{
		$hierarchy = array();
		foreach(safe_array(array_keys($this->parents, $pt)) as $kid)
		{
			$hierarchy[$kid] = $this->ids_hierarchy_lvl($kid);
		}
		return $hierarchy;
	}

	///////////////////////////////////////////////
	// internal private functions. call these directly and die.

	function _int_init_empty()
	{
		$this->tree = array();
	}

	function _int_load($filter)
	{
		// load using only lists, not datasource. funky.
		if (false && !$filter["parent"])
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_tree::filter(): parent filter parameter must always be passed!")
			));
		}

		if (!empty($filter["parent"]))
		{
			$filter["parent"] = $GLOBALS["object_loader"]->param_to_oid($filter["parent"]);
		}

		$this->_int_init_empty();
		$this->_int_req_filter($filter);
	}

	function _int_req_filter($filter)
	{
		list($oids, $meta_filter, $acldata, $parentdata, $objdata) = $GLOBALS["object_loader"]->ds->search($filter);

		// set acldata to memcache
		if (is_array($acldata))
		{
			foreach($acldata as $a_oid => $a_dat)
			{
				$a_dat["status"] = $objdata[$a_oid]["status"];
				$GLOBALS["__obj_sys_acl_memc"][$a_oid] = $a_dat;
			}
		}
		$acl_oids = array();
		foreach($oids as $oid => $oname)
		{
			// in ut, one folder was brothered benath itself - this made the tree go in an infinite loop
			if ($GLOBALS["object_loader"]->ds->can("view", $oid) && $parentdata[$oid] != $objdata[$oid]["brother_of"])
			{
				$this->tree_objdata[$parentdata[$oid]][$oid] = $objdata[$oid];
				$this->tree_names[$oid] = $oname;
				if (count($meta_filter) > 0)
				{
					$o = new object($oid);
					$add = true;
					foreach($meta_filter as $mf_k => $mf_v)
					{
						$tmp = $o->meta($mf_k);
						if (is_numeric($mf_v))
						{
							$tmp = (int)$tmp;
							$mf_v = (int)$mf_v;
						}
						if ($tmp != $mf_v)
						{
							$add = false;
						}
					}

					if ($add)
					{
						$this->tree[$o->parent()][$o->id()] = $o;
						$acl_oids[] = $objdata[$oid]["class_id"] == 1 ? $objdata[$oid]["brother_of"] : $oid;
					}
				}
				else
				{
					$this->tree[$parentdata[$oid]][$oid] = $oid;
					$acl_oids[] = $objdata[$oid]["class_id"] == 1 ? $objdata[$oid]["brother_of"] : $oid;
				}
			}
		}
		if (sizeof($acl_oids) > 0)
		{
			$filter["parent"] = $acl_oids;
			$this->_int_req_filter($filter);
		};
	}

	function _int_subtree($parent)
	{
		$ol = new object_tree();
		$this->_int_req_subtree($parent, $ol);
		return $ol;
	}

	function _int_req_subtree($parent, $ol)
	{
		foreach($this->level($parent) as $oid => $o)
		{
			$ol->tree[$o->parent()][$oid] = $o;
			$this->_int_req_subtree($oid);
		}
	}

	function _int_req_remove($o, &$cnt)
	{
		if (is_array($this->tree[$o->id()]))
		{
			foreach($this->tree[$o->id()] as $oid => $_o)
			{
				$_o = obj($oid);
				$this->_int_req_remove($_o, $cnt);
			}
			unset($this->tree[$o->id()]);
		}
		if (isset($this->tree[$o->parent()][$o->id()]))
		{
			unset($this->tree[$o->parent()][$o->id()]);
			$cnt++;
		}
	}

	function _int_filter_current($filter)
	{
		foreach($this->tree as $_pt => $level)
		{
			foreach($level as $oid => $o)
			{
				$o = obj($oid);
				$arr = $o->arr();
				foreach($filter as $k => $v)
				{
					if ($arr[$k] != $v)
					{
						$this->remove($oid);
						break;
					}
				}
			}
		}
	}
}

?>
