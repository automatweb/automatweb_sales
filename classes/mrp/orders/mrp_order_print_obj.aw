<?php

class mrp_order_print_obj extends mrp_order_obj
{
	const CLID = 1520;

	private $sel_cover_list;

	function get_applicable_warehouse()
	{
		$mrp_ws = obj($this->prop("workspace"))->mrp_workspace()->prop("RELTYPE_PURCHASING_MANAGER.id");
		if (is_array($mrp_ws))
		{
			$mrp_ws = reset($mrp_ws);
		}

		return obj($mrp_ws);
	}

	function get_job_list()
	{
		// jobs from case
		$case = $this->get_case();
		if (!$case)
		{
			return array();
		}
		return $case->get_job_list();
	}

	function set_prop($k, $v)
	{
		if ($k == "deadline" && ($case = $this->get_case()))
		{
			$case->set_prop("due_date", $v);
			$case->save();
		}

		if ($k == "e_name" && $this->name() == "")
		{
			$this->set_name($v);
		}
		return parent::set_prop($k, $v);
	}

	function get_total_price()
	{
		return $this->get_total_price_for_amt($this->prop("amount"), true);
	}

	function get_total_price_for_amt($amt, $do_cov = false)
	{
		$pr = 0;
		$pr += $this->_get_mat_price($amt, $do_cov);
		$pr += $this->_get_res_price($amt, $do_cov);
		if ($do_cov)
		{
			$pr += $this->_get_cov_price($amt);
		}
		return $pr;
	}

	function get_materials_price($do_cov = true)
	{
		return $this->_get_mat_price($this->prop("amount"), $do_cov);
	}

	function get_cover_price()
	{
		return $this->_get_cov_price($this->prop("amount"));
	}

	function get_resource_price($do_cov = true)
	{
		return $this->_get_res_price($this->prop("amount"), $do_cov);
	}

	protected function _get_cov_price($amt)
	{
		$sum = 0;
		$sel_covers = $this->get_selected_covers();
		foreach($sel_covers as $cover)
		{
			$sum += $cover->get_price_for_order_and_amt(obj($this->id()), $amt);
		}
		return $sum;
	}

	protected function _get_mat_price($amt, $do_cov = false)
	{	
		$sums = array();

		$sel_covers_mat = $this->meta("sel_covers_mat");

		foreach($this->connections_from(array("type" => "RELTYPE_REQUESTED_MATERIAL")) as $c)
		{
			$mo = obj($c->to()->material);
			$mp = $this->_get_mat_price_for_amt($this, $amt, $mo, $c->to());
			foreach($mp as $mp_k => $mp_v)
			{
				if ($do_cov)
				{
					foreach(safe_array($sel_covers_mat[$mo->id()]) as $cover_id)
					{
						if ($this->can("view", $cover_id))
						{
							$cover = obj($cover_id);
							$mp_v += $cover->get_price_for_order_and_amt_and_price($this, $amt, $mp_v);
						}
					}
				}
				$sums[$mp_k] += $mp_v;
			}			
		}
		return join(" ", $sums);
	}

	private function _get_mat_price_for_amt($o, $amt, $mo, $expense_row)
	{
		static $cur_list;
		if ($cur_list === null)
		{
			$cur_list = get_instance(CL_CURRENCY)->get_list(RET_NAME);
		}

		$tot_mat_price = array(); 

		// calculate amount for the amount requested approximately
		$per_one = $expense_row->prop("amount") / $o->prop("amount");
		$calc_amt = $per_one * $amt;

		foreach($cur_list as $cur_id => $cur_name)
		{
			$tot_mat_price[$cur_id] += ($mo->price_get_by_currency(obj($cur_id)) * $calc_amt);
		}
		return $tot_mat_price;
	}

	protected function _get_res_price($amt, $do_cov = false)
	{
		$pricelist = obj($this->prop("mrp_pricelist"));

		$sel_covers_job_res = $this->meta("sel_covers_job_res");

		$pr = 0;
		foreach($this->get_job_list() as $job)
		{
			$resource = $job->get_resource();

			$tmp = $pricelist->get_price_for_resource_and_amount($resource, $amt);
			if ($job->length > 0)
			{
				$tmp += $pricelist->get_price_for_resource_and_time($resource, $job->length / 3600);
			}
			$pr += $tmp;

			if ($do_cov)
			{
				foreach(safe_array($sel_covers_job_res[$job->id()][$resource->id()]) as $cover_id)
				{
					if ($this->can("view", $cover_id))
					{
						$cover = obj($cover_id);
						$pr += $cover->get_price_for_order_and_amt_and_price($this, $amt, $tmp);
					}
				}
			}
		}
		return $pr;
	}

	public function get_price_for_job($job)
	{
		$pricelist = obj($this->prop("mrp_pricelist"));

		$resource = $job->get_resource();
		$rv = $pricelist->get_price_for_resource_and_amount($resource, $this->prop("amount"));
		if ($job->length > 0)
		{
			$rv += $pricelist->get_price_for_resource_and_time($resource, $job->length / 3600);
		}
		return $rv;
	}

	function get_selected_covers()
	{
		if ($this->sel_cover_list === null)
		{
			$ol = new object_list($this->connections_from(array("type" => "RELTYPE_SEL_COVER")));
			$this->sel_cover_list = $ol->arr();
		}
		return $this->sel_cover_list;
	}

	function set_selected_covers($cover_list)
	{
		$cover_list = array_flip($cover_list);
		$cur = $this->get_selected_covers();
		// remove unnecessary
		foreach($cur as $cover)
		{
			if (!isset($cover_list[$cover->id()]))
			{
				$this->disconnect(array("from" => $cover->id(), "type" => "RELTYPE_SEL_COVER"));
			}
		}

		// add new
		foreach($cover_list as $id => $tmp)
		{
			if (!isset($cur[$id]))
			{
				$this->connect(array("to" => $id, "type" => "RELTYPE_SEL_COVER"));
			}
		}

		$this->sel_cover_list = null;
	}

	public function get_customer_name()
	{
		if ($GLOBALS["object_loader"]->can("view", $this->prop("customer")))
		{
			return $this->prop("customer.name");
		}
		return $this->prop("e_orderer_co");
	}

	public function get_contact_name()
	{
		if ($GLOBALS["object_loader"]->can("view", $this->prop("orderer_person")))
		{
			return $this->prop("orderer_person.name");
		}
		return $this->prop("e_orderer_person");
	}

	public function get_contact_mail()
	{
		if ($GLOBALS["object_loader"]->can("view", $this->prop("orderer_person")))
		{
			return $this->prop("orderer_person.email.mail");
		}
		return $this->prop("e_orderer_email");
	}

	public function get_saved_files()
	{
		$ol = new object_list($this->connections_from(array("type" => "RELTYPE_SAVED_FILE")));
		return $ol->arr();
	}

	public function get_sent_offers()
	{
		$ol = new object_list($this->connections_from(array("type" => "RELTYPE_SENT_OFFER")));
		$r = array();
		foreach($ol->arr() as $item)
		{
			if ($item->do_send)
			{
				$r[] = $item;
			}
		}
		return $r;
	}

	public function get_pending_offers()
	{
		$ol = new object_list($this->connections_from(array("type" => "RELTYPE_SENT_OFFER")));
		$r = array();
		foreach($ol->arr() as $item)
		{
			if (!$item->do_send)
			{
				$r[] = $item;
			}
		}
		return $r;
	}

	public function file_is_sent($file)
	{
		// find sent thingies connected to this and the file
		foreach($this->get_sent_offers() as $offer)
		{
			if ($offer->is_connected_to(array("to" => $file->id())))
			{
				return true;
			}
		}
		return false;
	}

	public function get_colour_options()
	{
		return array(
			t("1/0 - &uuml;helt poolt &uuml;he v&auml;rviga tr&uuml;kitud"),
			t("1/1 - m&otilde;lemalt poolt 1 v&auml;rviga tr&uuml;kitud"),
			t("4/0 - &uuml;helt poolt CMYK t&auml;isv&auml;rvitr&uuml;kis (saab tr&uuml;kkida v&auml;rvilisi fotosid)"),
			t("4/4 - m&otilde;lemalt poolt v&auml;rviline")
		);
	}

	public function get_used_materials()
	{
		$ol = new object_list($this->connections_from(array("type" => "RELTYPE_REQUESTED_MATERIAL")));
		$rv = array();
		foreach($ol->arr() as $o)
		{
			$rv[$o->material] = $o;
		}

		return $rv;
	}

	public function calculate_needed_paper_amount($paper, $material_request = null)
	{
		if (!$this->can("view", $this->prop("e_format")))
		{
			return 0;
		}

		// get gramweight, amount of pages, amount of total items, area for format, convert to m2, multiply by gramweight, pages and so on
		$gramweight = $paper->gramweight;

		$pages_per_sqm = obj($this->prop("e_format"))->per_sqm;

		$page_count = $this->prop("page_count");
		if (is_object($material_request))
		{
			$page_count = $material_request->pages_with_this;
		}
		
		$sqms = ($page_count * $this->prop("amount")) / $pages_per_sqm;
		$paper_weight_kg = ($sqms * $gramweight) / 1000;

		// add error % from resource for this paper
		$res = $this->get_default_resource_for_material($paper);
		if ($res && $res->error_pct > 0)
		{
			$paper_weight_kg += ($paper_weight_kg * $res->error_pct) / 100.0;
		}

		return $paper_weight_kg;
	}

	function get_default_resource_for_material($mat)
	{
		$ol = new object_list(array(
			"class_id" => CL_MATERIAL_EXPENSE_CONDITION,
			"lang_id" => array(),
			"site_id" => array(),
			"product" => $mat->id()
		));
		if (!$ol->count())
		{
			return null;
		}
		return $ol->begin()->resource();
	}
}

?>
