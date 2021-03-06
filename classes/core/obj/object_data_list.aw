<?php

// take the filter from an object_list, make the where from that
// but take the fetch from another array and allow sql funcs in that
// and return just the data

class object_data_list implements IteratorAggregate
{
	private $list_data = array();

	public function getIterator()
	{
		return new ArrayIterator($this->list_data);
	}

	/** Constructs object data list
		@attrib params=pos api=1

		@param param type=array() default=NULL

		@param props type=array() default=NULL

		@errors
			Returns error if param is not an array or NULL.

		@returns void

		@comment
			You can not rename the following fields in the fetch/props part: oid, parent, name, brother_of, status, class_id

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
	public function object_data_list($param = NULL, $props = NULL)
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
	public function arr()
	{
		return $this->list_data;
	}

	/** Returns an array of all the object IDs in the list.
		@attrib api=1
	**/
	public function ids()
	{
		return array_keys($this->list_data);
	}

	private function _int_load($arr, $props)
	{
		$this->_int_init_empty();
		list($oids, $meta_filter, $acldata, $parentdata, $objdata, $data, $has_sql_func) = object_loader::ds()->search($arr, $props);

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
				// TODO: implementeerida lukustatuse check?
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

					if ($mf_v{0} === "%")
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
		else
		{
			foreach($oids as $oid => $oname)
			{
				// TODO: implementeerida lukustatuse check?
				$this->list_data[$oid] = $data[$oid];
			}
		}
	}

	private function _int_init_empty()
	{
		$this->list_data = array();
	}

	/** Returns object count currently in list
		@attrib api=1 params=pos
		@returns int
		@errors none
	**/
	public function count()
	{
		return count($this->list_data);
	}

	/** Works almost the same as array_slice(), except it doesn't return anything, but modifies the object_data_list it is applied to.
		@attrib api=1 params=pos
		@param offset type=int
		@param length type=int default=NULL
		@errors none
		@returns void
	**/
	public function slice($start, $length = null)
	{
		$this->list_data = array_slice($this->list_data, $start, $length, true);
	}

	/** Returns array of values in list identified by $name, a column by $name.
		@attrib api=1 params=pos
		@param name type=string
		@returns array
			oid => value pairs
		@errors none
	**/
	public function get_element_from_all($name)
	{
		$ret = array();
		foreach($this->arr() as $oid => $o)
		{
			if(isset($o[$name]))
			{
				$ret[$oid] = $o[$name];
			}
		}
		return $ret;
	}
}
