<?php

// take the filter from an object_list, make the where from that
// but take the fetch from another array and allow sql funcs in that
// and return just the data

class object_data_list
{
	private $list_data = array();

	/** Returns object_data_list.
		@attrib params=pos name=object_data_list api=1

		@param params

		@param props

		@errors
			Returns error if param is not an array.

		@returns
			object_data_list object.

		@comment
			You can not rename the following fields in the fetch part: oid, parent, name, brother_of, status, class_id

		@examples
			$odl = new object_data_list(
				array(
					"class_id" => CL_FILE
				),
				array(
					CL_FILE => array("oid", "name")
				)
			);

			$odl = new object_data_list(
				array(
					"class_id" => CL_FILE
				),
				array(
					CL_FILE => "name"
				)
			);

			$odl = new object_data_list(
				array(
					"class_id" => array(CL_FILE, CL_MENU),
					"parent" => 126,
				),
				array(
					CL_FILE => array("oid" => "id", "name"),
					CL_MENU => array("oid", "name" => "nimi"),
				)
			);
	**/
	function object_data_list($param = NULL, $props = NULL)
	{
		if (!is_array($param))
		{
			$this->_int_init_empty();
		}
		else
		{
			error::raise_if(!is_array($props) || count($props) == 0, array(
				"id" => "ERR_EMPTY_ARRAY",
				"msg" => t("The fetch parameter can not be empty!")
			));

			// make sure that internal props are not renamed
			foreach($props as $clid => $dat)
			{
				if (is_array($dat))
				{
					foreach($dat as $k => $v)
					{
						if (!is_numeric($k) && ($k === "oid" || $k === "parent" || $k === "name" || $k === "brother_of" || $k === "status" || $k === "class_id") && $v != $k)
						{
							error::raise(array(
								"id" => "ERR_INVALID_RENAME",
								"msg" => sprintf(t("The fields oid,parent,name,brother_of,status,class_id can not be renamed in fetches. Current rename is %s => %s"), $k, $v)
							));
						}
					}
				}
			}

			$this->_int_load($param, $props);
		}
	}

	/** Returns an array of all the objects in the list.
		@attrib api=1

		@errors
			none

		@returns
			array of data of objects in the list, array key is object id, value is object instance

		@examples
			$odl = new object_data_list(
				array(
					"class_id" => CL_FILE
				),
				array(
					CL_FILE => array("oid" => "id", "name"),
				)
			);
			$files_data = $odl->arr();

			$files_data = Array (
				[1232] => Array(
					[id] => 1232
					[name] => Foo
				)
				[123125] => Array(
					[id] => 123125
					[name] => Yahoo
				)
			)

			This example currently works only for single class queries
			$odl2 = new object_data_list(
				array(
					"class_id" => CL_FILE
				),
				array(
					CL_FILE => "name"
				)
			);
			$files_data = $odl2->arr();

			$files_data = Array (
				[1232] => Foo,
				[123125] => Yahoo
			)
	**/
	function arr()
	{
		return $this->list_data;
	}

	/** Returns an array of all the object IDs in the list.
		@attrib api=1
	**/
	function ids()
	{
		return array_keys($this->list_data);
	}


	////////// private

	function _int_load($arr, $props)
	{
		$this->_int_init_empty();
		list($oids, $meta_filter, $acldata, $parentdata, $objdata, $data, $has_sql_func) = $GLOBALS["object_loader"]->ds->search($arr, $props);

		if ($has_sql_func)
		{
			// no acl or anything with sql functions
			$this->list_data = $data;
			return;
		}

		if (!is_array($oids))
		{
			return false;
		}

		// set acldata to memcache
		if (is_array($acldata))
		{
			foreach($acldata as $a_oid => $a_dat)
			{
				$a_dat["status"] = $objdata[$a_oid]["status"];
				$GLOBALS["__obj_sys_acl_memc"][$a_oid] = $a_dat;
			}
		}

		if (count($meta_filter) > 0)
		{
			foreach($oids as $oid => $oname)
			{
				if ($GLOBALS["object_loader"]->ds->can("view", $oid))
				{
					$add = true;
					$_o = new object($oid);
					foreach($meta_filter as $mf_k => $mf_v)
					{
						if (is_object($mf_v))
						{
							error::raise(array(
								"id" => "ERR_META_FILTER",
								"msg" => sprintf(t("object_list::filter(%s => %s): can not complex searches on metadata fields!"), $mf_k, $mf_v)
							));
						}
						if ($mf_v{0} == "%")
						{
							error::raise(array(
								"id" => "ERR_META_FILTER",
								"msg" => sprintf(t("object_list::filter(%s => %s): can not do LIKE searches on metadata fields!"), $mf_k, $mf_v)
							));
						}

						$tmp = $_o->meta($mf_k);
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
						$this->list_data[$oid] = $data[$oid];
					}
				}
			}
		}
		else
		{
			foreach($oids as $oid => $oname)
			{
				if (object_loader::can("view", $oid))
				{
					$this->list_data[$oid] = $data[$oid];
				}
			}
		}
	}

	function _int_init_empty()
	{
		$this->list_data = array();
	}

	function count()
	{
		return count($this->list_data);
	}

	/** Works almost the same as array_slice(), except it doesn't return anything, but modifies the object_data_list it is applied to.
		@attrib api=1 name=slice params=pos

		@param offset required type=int

		@param length optional type=int

		@errors
			none

		@returns
			nothing
	**/
	public function slice($start, $length = null)
	{
		$this->list_data = array_slice($this->list_data, $start, $length, true);
	}

	function get_element_from_all($col)
	{
		$ret = array();
		foreach($this->arr() as $oid => $o)
		{
			if(isset($o[$col]))
			{
				$ret[$oid] = $o[$col];
			}
		}
		return $ret;
	}
}
