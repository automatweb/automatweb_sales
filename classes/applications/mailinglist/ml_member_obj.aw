<?php

class ml_member_obj extends _int_object
{
	function prop($name)
	{
		//FIXME: konverteerimine viia mujale. kasutada prop_xml-i vms.
		return htmlspecialchars(parent::prop($name));
	}

	/**
		@attrib name=get_persons api=1 params=name
		@param id required type=oid,array(oid)
	**/
	function get_persons($arr)
	{
		$ret = new object_list;

		// The e-mail might be connected to the person via work relation.
		$cs = connection::find(array(
			"from" => array(),
			"to" => $arr["id"],
			"type" => "RELTYPE_EMAIL",
			"from.class_id" => CL_CRM_PERSON_WORK_RELATION,
		));
		if(count($cs) > 0)
		{
			$wrids = array();
			foreach($cs as $c)
			{
				$wrids[] = $c["from"];
			}
			$cs = connection::find(array(
				"from" => array(),
				"to" => $wrids,
				"from.class_id" => CL_CRM_PERSON,
			));
			foreach($cs as $c)
			{
				$ret->add($c["from"]);
			}
		}
		$cs = connection::find(array(
			"from" => array(),
			"to" => $arr["id"],
			"type" => "RELTYPE_EMAIL",
			"from.class_id" => CL_CRM_PERSON,
		));
		foreach($cs as $c)
		{
			$ret->add($c["from"]);
		}

		return $ret;
	}
/*
	function save()
	{
		$oid = parent::id();

		$conn_id = parent::prop("conn_id");
		// This is not supposed to be saved.
		parent::set_prop("conn_id", NULL);
		$conn_ids = isset($conn_id) ? (is_array($conn_id) ? $conn_id : array($conn_id)) : array();

		// New
		if(!is_oid($oid))
		{
			return $this->parent_save($conn_ids);
		}

		// If no connections remain with the old e-mail obj, there's no point in keeping it. So we'll just change the current one.
		if(count($this->conns_remain_unchanged($conn_ids)) == 0)
		{
			return $this->parent_save($conn_ids);
		}

		// Getting the current name..
		$q = oql::compile_query("SELECT mail FROM CL_ML_MEMBER WHERE CL_ML_MEMBER.oid = %u");
		$r = oql::execute_query($q, $oid);

		$nmail = parent::prop("mail");
		$cmail = $r[$oid]["mail"];

		$old_obj = obj($oid);

		if($nmail !== $cmail)
		{
			$ol = new object_list(array(
				"class_id" => CL_ML_MEMBER,
				"mail" => $nmail,
				"lang_id" => array(),
				"site_id" => array(),
				"limit" => 1,
			));
			if($ol->count() > 0)
			{
				$emo = $ol->begin();
				// What if I wanna save the e-mail object under different parent?
				if($emo->parent() != parent::parent())
				{
					$emo_bro_id = $emo->create_brother(parent::parent());
					$emo = obj($emo_bro_id);
				}
			}
			else
			{
				$emo = obj(parent::save_new());
				$emo->mail = $nmail;
				$emo->save();
			}

			if(count($conn_ids) > 0)
			{
				foreach($conn_ids as $conn_id)
				{
					if(!is_numeric($conn_id))
					{
						continue;
					}
					try
					{
						$c = new connection();
						$c->load($conn_id);
						$c->change(array(
							"from" => $c->prop("from") == $oid ? $emo->id() : $c->prop("from"),
							"to" => $c->prop("to") == $oid ? $emo->id() : $c->prop("to"),
						));
					}
					catch (Exception $e)
					{
					}
				}
			}
			return $emo->id();
		}
		return parent::save();
	}
*/
	/*	object::set_prop(brother_of, ): no property brother_of defined for current object!
	function delete()
	{
		$ol = new object_list(array(
			"class_id" => CL_ML_MEMBER,
			"mail" => $this->prop("mail"),
			"lang_id" => array(),
			"site_id" => array(),
			"brother_of" => $this->id(),
		));
		$ol->remove($this->id());
		if(sizeof($ol->ids()))
		{
			$id = reset(sizeof($ol->ids()));
			foreach($ol->arr() as $o)
			{
				$o -> set_prop("brother_of" , $id);
				$o->save();
			}
		}
		return parent::delete();
	}
	*/

	private function conns_remain_unchanged($conns)
	{
		$r = array();
		foreach(parent::connections_from(array()) as $c)
		{
			if(!in_array($c->id(), $conns))
			{
				$r[] = $c;
			}
		}
		foreach(parent::connections_to(array()) as $c)
		{
			if(!in_array($c->id(), $conns))
			{
				$r[] = $c;
			}
		}
		return $r;
	}

	private function parent_save($conn_ids)
	{
		// Check if we already have an e-mail address with the same parent and mail property.
		// If so, return this instead of creating new one (or changing the current one).
		$ol = new object_list(array(
			"class_id" => CL_ML_MEMBER,
			"mail" => parent::prop("mail"),
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
			"parent" => parent::parent(),
		));
		if($ol->count() > 0)
		{
			$oid = reset($ol->ids());
			parent::load($oid);
			return $oid;
		}
		// If not, check if we have an e-mail address with same mail property and ANY parent.
		// If so, create brother under the requested parent and return the original instead of creating a new one  (or changing the current one).
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_ML_MEMBER,
				"mail" => parent::prop("mail"),
				"lang_id" => array(),
				"site_id" => array(),
				"limit" => 1,
				"parent" => array(),
			));
			if($ol->count() > 0)
			{
				$o = $ol->begin();
				$oid = $o->create_brother(parent::parent());
				// If it's connected to anything, we have to change to connections also!
				if(is_oid(parent::id()))
				{
					foreach($conn_ids as $conn_id)
					{
						try
						{
							$c = new connection();
							$c->load($conn_id);
							$c->change(array(
								"from" => $c->prop("from") == parent::id() ? $oid : $c->prop("from"),
								"to" => $c->prop("to") == parent::id() ? $oid : $c->prop("to"),
							));
						}
						catch (Exception $e)
						{
						}
					}
				}
				parent::load($oid);
				return $oid;
			}
		}
		// If there ain't any e-mail addresses with the same mail property, create new one (or change the current one).
		return parent::save();
	}
}

?>
