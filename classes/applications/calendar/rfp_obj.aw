<?php

namespace automatweb;

class rfp_obj extends _int_object
{
	const AW_CLID = 1175;


	function set_prop($pn, $pv)
	{
		switch($pn)
		{
			case "data_subm_name":
			case "data_subm_organisation":
			case "data_subm_organizer":
			case "data_billing_company":
			case "data_billing_contact":
				if(is_oid($this->id()))
				{
					$cs = $this->connections_from(array(
						"type" => "RELTYPE_".strtoupper($pn),
					));
					foreach($cs as $c)
					{
						$c->delete();
					}
				}
				if(strlen($pv))
				{
					$inst = $this->instance();
					if(!$inst->can("view", $pv))
					{
						$inst = $this->instance();
						$list = new object_list(array(
							"class_id" => $inst->prop_to_relclid[$pn],
							"name" => $pv,
						));
						if(!$list->count())
						{
							$obj = obj();
							$obj->set_class_id($inst->prop_to_relclid[$pn]);
							if($inst->rfpm)
							{
								$pt = $inst->rfpm->prop("clients_folder");
							}
							if(!$pt)
							{
								$pt = $this->parent();
							}
							$obj->set_parent($pt);
							$obj->set_name($pv);
							$obj->save();
						}
						else
						{
							$obj = $list->begin();
						}
						$pv = $obj->id();
					}
					$this->connect(array(
						"to" => $pv,
						"type" => "RELTYPE_".strtoupper($pn),
					));
				}
				break;

			case "cancel_and_payment_terms":
			case "accomondation_terms":
				$tt = $this->meta("trans_terms");
				$tt[$this->prop("default_language")][$pn] = $pv;
				$this->set_meta("trans_terms", $tt);
				break;

			case "confirmed":
				if(is_oid($this->id()))
				{
					$bron_verified = 0;
					if($pv == RFP_STATUS_CONFIRMED)
					{
						$bron_verified = 1;
					}
					foreach($this->connections_from(array("type" => "RELTYPE_RESERVATION")) as $c)
					{
						$rvo = $c->to();
						$rvo->set_prop("verified", $bron_verified);
						$rvo->save();
					}
					foreach($this->connections_from(array("type" => "RELTYPE_CATERING_RESERVATION")) as $c)
					{
						$rvo = $c->to();
						$rvo->set_prop("verified", $bron_verified);
						$rvo->save();
					}
				}
				break;
		}
		return parent::set_prop($pn, $pv);
	}

	function prop($pn)
	{
		switch($pn)
		{
			case "cancel_and_payment_terms":
			case "accomondation_terms":
				
				$rfpm = get_instance(CL_RFP_MANAGER);
				$obj = obj($rfpm->get_sysdefault());
				$terms = $this->meta("trans_terms");
				$def_language = ($_thisl = $this->prop("default_language"))?$_thisl:$obj->prop("default_language");
				if(strlen($str = $terms[$this->prop("default_language")][$pn]))
				{
					return $str;
				}
				else
				{
					$trs = $obj->meta("translations");
					
					if (isset($trs[$def_language]) && $obj->meta("trans_".$def_language."_status") == 1)
					{
						return $trs[$def_language][$pn];
					}
					else
					{
						return $obj->prop($pn);
					}
				}
			case "final_catering_rooms":
				$val = parent::prop($pn);
				if(!is_array($val))
				{
					$conn = parent::connections_from(array(
						"type" => "RELTYPE_CATERING_ROOM",
					));
					$val = array();
					foreach($conn as $c)
					{
						$val[$c->prop("to")] = $c->prop("to");
					}
					return $val;
				}
				break;
		}
		return parent::prop($pn);
	}

	function delete($full_delete = false)
	{
		$connections = $this->connections_from(array(
			"type" => "RELTYPE_RESERVATION",
		));
		foreach($connections as $conn)
		{
			$reservation = $conn->to();
			$reservation->delete();
		}
		$connections = $this->connections_from(array(
			"type" => "RELTYPE_CATERING_RESERVATION",
		));
		foreach($connections as $conn)
		{
			$reservation = $conn->to();
			$reservation->delete();
		}
		parent::delete($full_delete);
	}

	/** Returns all reservations for this rfp
		@attrib api=1
	 **/
	public function get_reservations()
	{
		$connections = $this->connections_from(array(
			"type" => "RELTYPE_RESERVATION",
		));
		$return = array();
		$gather_res_props = array(
			"id", "people_count", "start1", "end", "resource",
		);
		$gather_res_meta = array(
			"amount"
		);
		foreach($connections as $conn)
		{
			$reservation = $conn->to();
			$return[$reservation->id]["rfp"] = $this->id();
			foreach($gather_res_props as $prop)
			{
				$return[$reservation->id()][$prop] = $reservation->prop($prop);
			}
			foreach($gather_res_meta as $meta)
			{
				$return[$reservation->id()]["meta_".$meta] = $reservation->meta($meta);
			}
		}
		return $return;
	}

	/** Returns all resreved resources for this rfp
		@attrib api=1
	 **/
	public function get_resources()
	{
		$connections = $this->connections_from(array(
			"type" => "RELTYPE_RESERVATION",
		));
		$gather_res_props = array(
			"id", "people_count", "start1", "end", "resource",
		);
		$gather_res_meta = array(
			"amount"
		);
		foreach($connections as $conn)
		{
			$reservation = $conn->to();

			$new = array(
				"rfp" => $this->id(),
				"reservation" => $reservation->id(),
			);
			foreach($gather_res_props as $prop)
			{
				$new[$prop] = $reservation->prop($prop);
			}
			foreach($gather_res_meta as $meta)
			{
				$new["meta_".$meta] = $reservation->meta($meta);
			}
			foreach($reservation->get_resources_data() as $resource => $resource_data)
			{
				$new2 = array(
					"real_resource" => $resource,
				);
				$new["resources"][] = $new2 + $resource_data;
			}
			$return[] = $new;
		}
		return $return;
	}

	/** Returns housing information for this rfp
		@attrib api=1
	 **/
	public function get_housing()
	{
		$housing = $this->meta("housing");
		return $housing;
	}

	/** Returns catering information for this rfp
		@attrib api=1
	 **/
	public function get_catering()
	{
		$products = $this->meta("prods");
		return $products;
	}

	/** Sets the catering data (returned by get_catering())
		@attrib api=1 params=pos
		@param catering_data required type=array
			Data to be set
	 **/
	public function set_catering($data)
	{
		$this->set_meta("prods", $data);;
	}

	/** Removes given room reservation
		@attrib api=1 params=pos
		@param reservation type=oid required
	 **/
	public function remove_room_reservation($reservation)
	{
		if($this->can("view", $reservation))
		{
			$res_obj = obj($reservation);
			$this->disconnect(array(
				"from" => $reservation,
				"type" => 3,
			));
			$res_obj->delete();
			return true;
		}
		return false;
	}

	/** Removes all given rooms reservations
		@attrib api=1 params=pos
		@param room type=oid required
	 **/
	public function remove_room_reservations($room)
	{
		if($this->can("view", $room))
		{
			$conns = $this->connections_from(array(
				"to.class_id" => CL_RESERVATION,
				"CL_RESERVATION.resource" => $room,
				"type" => 3,
			));
			$others = new object_list();
			foreach($conns as $conn)
			{
				$res = $conn->to();
				$conn->delete();
				$res->delete();
			}
			return true;
		}
		return false;
	}

	/** Removes product from catering reservation
		@attrib api=1 params=pos
		@param reservation required type=oid
			Reservation object id to remove from
		@param product required type=oid
			Product oid to remove
	 **/
	public function remove_catering_reservation_product($reservation = false, $product = false)
	{
		if(!$this->can("view", $reservation) OR !$this->can("view", $product))
		{
			return false;
		}
		$reservation = obj($reservation);
		$rdata = $reservation->get_product_amount();
		unset($rdata[$product]);
		$reservation->set_product_amount($rdata, false);
		$reservation->save();
		
		$data = $this->get_catering();
		unset($data[$product.".".$reservation->id()]);
		$this->set_catering($data);
		return true;
	}


	/** Marks all reservations, connected to this rfp, unverified
	 **/
	public function mark_reservations_unverified()
	{
		$rels = $this->connections_from(array(
			"to.class_id" => CL_RESERVATION,
		));
		foreach($rels as $rel)
		{
			$rv = $rel->to();
			$rv->set_prop("verified", 0);
			$rv->save();
		}
	}

	/** Marks all reservations, connected to this rfp, verified
	 **/
	public function mark_reservations_verified()
	{
		$rels = $this->connections_from(array(
			"to.class_id" => CL_RESERVATION,
		));
		foreach($rels as $rel)
		{
			$rv = $rel->to();
			$rv->set_prop("verified", 1);
			$rv->save();
		}
	}

	/** Gets RFP package's custom price
		@attrib api=1
	 **/
	public function get_package_custom_price()
	{
		return $this->meta("package_custom_price");
	}

	/** Gets RFP package's custom discount
		@attrib api=1
	 **/
	public function get_package_custom_discount()
	{
		return $this->meta("package_custom_discount");
	}

	/** Set's RFP package's custom price
		@attrib api=1 params=pos
		@param price required type=int
	 **/
	public function set_package_custom_price($price)
	{
		$this->set_meta("package_custom_price", $price);
	}

	/** Set's RFP package's custom discount
		@attrib api=1 params=pos
		@param discount required type=int
	 **/
	public function set_package_custom_discount($discount)
	{
		$this->set_meta("package_custom_discount", $discount);
	}


	/** Gets additinal services info
		@attrib api=1
		@returns
			Array of additional services for current rfp
			array(
				time => unix_timestamp,
				service => service_name(string),
				price => price (string),
				amount => amount (string),
				sum => sum (string),
				comment => comment (string),
			)
	 **/
	public function get_additional_services()
	{
		return $this->meta("additional_services");
	}

	/** Sets additinal services info
		@attrib api=1 params=pos
		@param data type=array required
			Array in same structure that get_additional_servives() returns
	 **/
	public function set_additional_services($data = array())
	{
		return $this->set_meta("additional_services", $data);
	}

}
?>
