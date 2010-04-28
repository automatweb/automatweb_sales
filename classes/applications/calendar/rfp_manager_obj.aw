<?php

namespace automatweb;

class rfp_manager_obj extends _int_object
{
	const AW_CLID = 1192;


	/** Fetches conference rooms
		@comment
			Fetches rooms from folder pointed by room_folder prop
		@returns
			Object list of rooms
	 **/
	public function get_rooms_from_room_folder()
	{
		$ol = new object_list(array(
			"class_id" => CL_ROOM,
			"parent" => $this->prop("room_folder"),
		));
		return $ol;
	}

	/** Fetches catering rooms
		@comment
			Fetches rooms from folder pointed by catering_room_folder prop(Rooms for conference catering)
		@returns
			Object list of rooms
	 **/
	public function get_rooms_from_catering_room_folder()
	{
		$ol = new object_list(array(
			"class_id" => CL_ROOM,
			"parent" => $this->prop("catering_room_folder"),
		));
		return $ol;
	}

	/** Returns info about extra hours in reservating rooms
		@returns
			returns array(
				room_oid => array(
					"min_hours" => value,
					"min_prices" => array(
						currency => price,
						...
					),
					"max_hours" => value,
					"max_prices" => array(
						currency => price,
						...
					),
				)
			)
	 **/
	public function get_extra_hours_prices()
	{
		return $this->meta("extra_hours_prices");
	}
	
	/** Sets the extra hours pricedata
		@attrib params=pos api=1
		@param data type=array
			the pricedata, example array in #get_extra_hours_prices
	 **/
	public function set_extra_hours_prices($data = array())
	{
		$this->set_meta("extra_hours_prices", $data);
	}

	/** Returns packages and their info
		@attrib api=1
	 **/
	public function get_packages()
	{
		$ol = new object_list(array(
			"class_id" => CL_META,
			"parent" => $this->prop("packages_folder"),
		));
		$metainfo = $this->meta("pk_prices");
		$return = array();
		foreach($ol->arr() as $oid => $obj)
		{
			foreach($metainfo[$oid]["prices"] as $pr_oid => $data)
			{
				if(get_instance(CL_MENU)->can("view", $pr_oid))
				{
					$pr = obj($pr_oid);
					if($pr->status() != STAT_ACTIVE)
					{
						unset($metainfo[$oid]["prices"][$pr_oid]);
					}
				}
				else
				{	
					unset($metainfo[$oid]["prices"][$pr_oid]);
				}
			}
			$return[$oid] = $metainfo[$oid];
		}
		return $return;
	}

	/** Sets packages and their info
		@attrib api=1
	 **/
	public function set_packages($data = array())
	{
		$old_data = $this->meta("pk_prices");
		$new_data = $old_data;
		foreach($data as $pk => $pkdata)
		{
			foreach($pkdata["prices"] as $pr => $prdata)
			{
				$new_data[$pk]["prices"][$pr] = $prdata;
			}
		}
		$this->set_meta("pk_prices", $new_data);
	}

	/** Returns event types for this manager
		@attrib api=1
		@returns
			array of meta objects
			array(
				oid => obj
			);
	 **/
	public function event_types()
	{
		if($this->can("view", $this->prop("event_type_folder")))
		{
			$ol = new object_list(array(
				"class_id" => CL_META,
				"parent" => $this->prop("event_type_folder"),
			));
			return $ol->arr();
		}
		return array();
	}

	/** Get's resources default prices
		@attrib api=1
		@returns
			Resources prices in array.
			Array(
				room_oid => array(
					resource_oid => array(
						currency_oid => [price]
					),
				)
			)

	 **/
	public function get_resource_default_prices()
	{
		return $this->meta("resource_default_prices");
	}

	/** Set's resources default prices
		@attrib api=1 params=pos
		@param data required type=array
			Resources prices in array.
			Array(
				room_oid => array(
					resource_oid => array(
						currency_oid => [price]
					),
				)
			)

	 **/
	public function set_resource_default_prices($data = array())
	{
		$this->set_meta("resource_default_prices", $data);
	}

	/** Get contact preferences from their folder
		@attrib api=1
		@returns
			Array of CL_META objects
			array(
				oid => obj
			)
	 **/
	public function get_contact_preferences()
	{
		$ol = new object_list(array(
			"class_id" => CL_META,
			"parent" => $this->prop("contact_preference_folder"),
		));
		return $ol->arr();
	}
}
?>
