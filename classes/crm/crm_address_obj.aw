<?php
/*
@classinfo  maintainer=markop
*/

class crm_address_obj extends _int_object
{
	function save($exclusive = false, $previous_state = null)
	{
		$this->set_name($this->get_address_name());
		return parent::save($exclusive, $previous_state);
	}

	function prop($k)
	{
		if($k === "name" && strlen(parent::prop($k)) === 0)
		{
			return $this->get_address_name();
		}
		return parent::prop($k);
	}

	/** Sets county to address
		@attrib api=1 params=pos
		@param county optional type=string/oid
			county
		@return oid
			county object id
	**/
	public function set_county($county)
	{
		if(!$county)
		{
			return null;
		}
		if(is_oid($county) && $GLOBALS["object_loader"]->can("view" , $county))
		{
			$o = obj($county);
		}
		elseif(strlen($county))
		{
			$filter = array();
			$filter["class_id"] = CL_CRM_COUNTY;
			$filter["lang_id"] = array();
			$filter["site_id"] = array();
			$filter["name"] = $county;
			$ol = new object_list($filter);
			$o = reset($ol->arr());
		}

		if(!is_object($o))
		{
			$o = new object();
			$o->set_class_id(CL_CRM_COUNTY);
			$o->set_parent($this->id());
			$o->set_name($county);
			$o->save();
		}

		$this->set_prop("maakond" , $o->id());
		$this->save();
		return $o->id();
	}

	/** Sets city to address
		@attrib api=1 params=pos
		@param city optional type=string/oid
			county
		@return oid
			city object id
	**/
	public function set_city($city)
	{
		if(!$city)
		{
			return null;
		}
		if(is_oid($city) && $GLOBALS["object_loader"]->can("view" , $city))
		{
			$o = obj($city);
		}
		elseif(strlen($city))
		{
			$filter = array();
			$filter["class_id"] = CL_CRM_CITY;
			$filter["lang_id"] = array();
			$filter["site_id"] = array();
			$filter["name"] = $city;
			$ol = new object_list($filter);
			$o = reset($ol->arr());
		}

		if(!is_object($o))
		{
			$o = new object();
			$o->set_class_id(CL_CRM_CITY);
			$o->set_parent($this->id());
			$o->set_name($city);
			$o->save();
		}

		$this->set_prop("linn" , $o->id());
		$this->save();
		return $o->id();
	}

	private function get_address_name()
	{
		$name = array();
		if ($this->prop("aadress") != "")
		{
			$name[] = $this->prop("aadress");
		};

		if ($this->prop("aadress2") != "")
		{
			$name[] = $this->prop("aadress2");
		};

		if ($this->prop("linn.name") != "")
		{
			$name[] = $this->prop("linn.name");
		};

		if ($this->prop("maakond.name") != "")
		{
			$name[] = $this->prop("maakond.name");
		};

		if ($this->prop("riik.name") != "")
		{
			$name[] = $this->prop("riik.name");
		};

		return join(", ",$name);
	}

}

?>
