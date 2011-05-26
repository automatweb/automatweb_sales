<?php

class bug_object extends _int_object
{
	const CLID = 868;

	function save($exclusive = false, $previous_state = null)
	{
		// before saving, set default props if they are not set yet
		if (!is_oid($this->id()))
		{
			$this->_set_default_bug_props();
		}

		$res = parent::save($exclusive, $previous_state);

		$this->update_all_rows();

		return $res;
	}

	function set_prop($name,$value)
	{
		if($name === "project" && is_oid($value) && is_oid($this->id()))
		{
			foreach($this->connections_from(array(
				"type" => "RELTYPE_PROJECT",
			)) as $c)
			{
				if ($c->prop("to") != $value)
				{
					$c->delete();
				}
			}
		}

		if($name === "send_bill" && !$this->prop("send_bill"))
		{
			$this->set_prop("to_bill_date" , time());
		}

		parent::set_prop($name,$value);
	}

	function _set_default_bug_props()
	{
		if (!$this->prop("orderer"))
		{
			$c = get_current_company();
			if($c)
			{
				$this->set_prop("orderer", $c->id());
			}
		}
		if (!$this->prop("orderer_unt"))
		{
			$p = get_current_person();
			if($p)
			{
				$sets = $p->prop("org_section");
			}

			if (is_array($sets))
			{
				$sets = reset($sets);
			}
			$this->set_prop("orderer_unit", $sets);
		}
		if (!$this->prop("orderer_person"))
		{
			$p = get_current_person();
			if($p)
			{
				$this->set_prop("orderer_person", $p->id());
			}
		}

		if (0 !== $this->prop("send_bill"))
		{
			$this->set_prop("send_bill", 1);
		}
	}

	function sum_guess()
	{
		$sum = 0;
		if($this->prop("num_hrs_guess"))
		{
			$sum = $this->prop("num_hrs_guess") * $this->prop("skill_used.hour_price");
		}
		return $sum;
	}

	function get_lifespan($arr)
	{
		// calculate timestamp
		$i_created = $this->created();
		if ($this->prop("bug_status") == BUG_CLOSED)
		{
			$o_bug_comments = new object_list(array(
				"class_id" => array(CL_TASK_ROW,CL_BUG_COMMENT),
				"parent" => $this->id(),
				"sort_by" => "objects.created"
			));

			$i_lifespan = end($o_bug_comments->arr())->created() - $i_created;
		}
		else
		{
			$i_lifespan = time() - $i_created;
		}

		// format output
		$i_lifespan_hours = $i_lifespan/3600;
		if ($i_lifespan_hours<=24)
		{
			if ($arr["only_days"])
			{
				if ($arr["without_string_prefix"])
				{
					$s_out = round($i_lifespan_hours/24);
				}
				else
				{
					$s_out = ($i_temp = round($i_lifespan_hours/24))==1 ? $i_temp." ".t("tund") : $i_temp." ".t("tundi");
				}
			}
			else
			{
				if ($arr["without_string_prefix"])
				{
					$s_out = round($i_lifespan_hours);
				}
				else
				{
					$s_out = ($i_temp = round($i_lifespan_hours))==1 ? $i_temp." ".t("tund") : $i_temp." ".t("tundi");
				}
			}
		}
		else
		{
			if ($arr["without_string_prefix"])
			{
				$s_out = round($i_lifespan_hours/24);
			}
			else
			{
				$s_out = ($i_temp = round($i_lifespan_hours/24))==1 ? $i_temp." ".t("p&auml;ev") : $i_temp." ".t("p&auml;eva");
			}
		}

		return $s_out;
	}

	/** returns last comment
		@attrib api=1
		@returns object
	**/
	public function get_last_comment()
	{
		$comments = $this->connections_from(array(
			"type" => "RELTYPE_COMMENT",
		));
		if(!sizeof($comments))
		{
			return false;
		}
		$comments = array_values($comments);
		$connection = $comments[sizeof($comments) - 1];
		$obj = $connection->to();
		return $obj;
	}

	/** returns last bug comment time
		@attrib api=1
		@returns timestamp
			bug comment time, if no comments, then bug created time
	**/
	public function get_last_comment_time()
	{
		$comment = $this->get_last_comment();
		if(!$comment)
		{
			return $this->created();
		}
		return $comment->created();
	}

	/** returns all bug comments
		@attrib api=1
		@returns object list
	**/
	public function get_bug_comments()
	{
		$ol = new object_list();
		$comments = $this->connections_from(array(
			"type" => "RELTYPE_COMMENT",
		));
		foreach($comments as $c)
		{
			$ol->add($c->prop("to"));
		}
		return $ol;
	}

	/**
		@attrib api=1 params=pos
		@param start
		@param end
		@returns double
	**/
	public function get_bug_comments_time($start = null, $end = null)
	{
		$sum = 0;
		$filter = array(
			"class_id" => array(CL_TASK_ROW,CL_BUG_COMMENT),
			"parent" => $this->id(),
			"sort_by" => "objects.created desc",
		);

		if ($start && $end)
		{
			$filter["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($start - 1), ($end+ 1));
		}
		else
		if ($start)
		{
			$filter["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $start);
		}
		else
		if ($end)
		{
			$filt["created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $end);
		}

		$ol = new object_list($filter);

		foreach($ol->arr() as $o)
		{
			$sum+= (double)$o->prop("add_wh");
		}

		return $sum;
	}

	//see testimiseks praegu annab k6ik kommentaarid.. pole veel arvega yhendamist tehtud
	/** returns bug comments without bill
		@attrib api=1
		@returns object list
	**/
	public function get_billable_comments($arr)
	{
		$ol = new object_list();
		$comments = $this->connections_from(array(
			"type" => "RELTYPE_COMMENT",
			"to.class_id" => CL_TASK_ROW,
		));
		$inst = get_instance(CL_BUG);
		foreach($comments as $c)
		{
			$comment = $c->to();
//selle asemele peaks miski ilus filter olema hoopis
			if(
				($arr["start"] && $arr["start"] > $comment->prop("date")) ||
				($arr["end"] && $arr["end"] < $comment->prop("date"))
			)
			{
				continue;
			}
			if($comment->prop("on_bill") && !$inst->can("view" , $comment->prop("bill")))
			{
				$ol->add($comment->id());
			}
		}
		return $ol;
	}

	/** returns bug orderer
		@attrib api=1
		@returns oid
	**/
	public function get_orderer()
	{
		return $this->prop("orderer");
	}

	/** returns bug hour_price
		@attrib api=1
		@returns oid
	**/
	public function get_hour_price()
	{
		return $this->prop("hr_price");
	}

	/** returns bug participants object list
		@attrib api=1
	**/
	public function get_participants()
	{
		$ol = new object_list();
		if(is_array($this->prop("monitors")))
		{
			$ol->add($this->prop("monitors"));
		}
		return $ol;
	}

	/** returns bug projects
		@attrib api=1
		@return object list
	**/
	public function get_projects()
	{
// 		$ol = new object_list(array(
// 			"class_id" =>  CL_CRM_PARTY,
// 			"lang_id" => array(),
// 			"participant.class_id" => CL_PROJECT,
// 			"site_id" => array(),
// 			"task" => $this->id(),
// 			"limit" => 1,
// 		));
		$projects = new object_list();
// 		foreach($ol->arr() as $party)
// 		{
// 			$projects->add($party->prop("project"));
// 		}

		$conns = $this->connections_from(array(
			"type" => "RELTYPE_PROJECT",
		));
		foreach($conns as $con)
		{
			$projects->add($con->prop("to"));
		}

		return $projects;
	}

	/** Finds and returns bug tracker associated with this bug
		@attrib api=1 params=pos
		@comment
		@returns CL_BUG_TRACKER|NULL
			Returns NULL if no bug tracker found
		@errors
			throws awex_obj_acl when bt object found but access denied
	**/
	public function get_tracker()
	{
		$bt = null;

		try
		{
			if ($this->prop("tracker"))
			{
				$bt = obj($this->prop("tracker"), array(), CL_BUG_TRACKER);
			}
			else
			{
				$parent = $this->parent() ? $this->parent() : automatweb::$request->arg("parent");
				if ($parent)
				{
					$po = new object($parent);
					$pt = $po->path();
					foreach($pt as $pi)
					{
						if ($pi->class_id() == CL_BUG_TRACKER)
						{
							$bt = $pi;
							break;
						}
					}
				}
			}
		}
		catch (awex_obj_acl $e)
		{
			throw $e;
		}
		catch (Exception $e)
		{
		}

		return $bt;
	}

	private function update_all_rows()
	{
		$comments = $this->get_bug_comments();
		foreach($comments->arr() as $comment)
		{
			$comment->save();
		}
	}
}
