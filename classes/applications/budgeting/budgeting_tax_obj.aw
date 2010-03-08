<?php
/*

@classinfo maintainer=markop

*/
class budgeting_tax_obj extends _int_object
{
	function get_terms($from)
	{
		$filter = array(
			"class_id" => CL_BUDGETING_TAX_TERM,
			"lang_id" => array(),
			"site_id" => array(),
		);
		if($from)
		{
			$filter["from_place"] = $from;
		}

		$ol = new object_list($filter);
		return $ol;
	}
	
	function calculate_amount_to_transfer($account, $sum = 0)
	{
		$m = get_instance("applications/budgeting/budgeting_model");
		$account = $m->get_account_object($account);
		if(!is_object($account))
		{
			return 0;
		}

		$rel_obj = $this->get_folder_rel_object($account , $sum);

		if(!$sum)
		{
			$sum = $m->get_account_balance($account->id());
		}

		if(is_object($rel_obj))
		{
			return $rel_obj->get_transfer_amount($sum);
		}
		else
		{
//		if (substr($tax->prop("amount"), -1) == "%")
//		{
			return $sum * ((double)$this->prop("amount") / 100.0); 
//		}
		}

		return $tax->prop("amount");
	}

	function calculate_amount_needed($account, $sum = 0)
	{
		$m = get_instance("applications/budgeting/budgeting_model");
		$account = $m->get_account_object($account);
		if(!is_object($account))
		{
			return $sum;
		}
		$rel_obj = $this->get_folder_rel_object($account , $sum);

		if(is_object($rel_obj))
		{
			return $rel_obj->get_needed_amount($sum);
		}
		else
		{
			if($this->prop("amount") < 100 && $this->prop("amount") > 0)
			{
				return 100 * $sum / (100 - $this->prop("amount"));
			}
		}

		return $sum;
	}

	private function get_folder_rel_object($account, $sum)
	{
		$rel = "";
		$pri = 0;
		if(is_object($account))
		{
			$account = $account->id();
		}
		if(is_oid($account))
		{
			$m = get_instance("applications/budgeting/budgeting_model");
			$account = $m->get_cat_id_description($account);
		}
		$ol = new object_list(array(
			"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
			"lang_id" => array(),
			"site_id" => array(),
			"tax" => $this->id(),
			"folder" => $açcount,
		));
		foreach($ol->arr() as $o)
		{
			if($o->prop("pri") > $pri) // + veel tingimuse kontrolli vaja
			{
				if($sum && $o->prop("term"))
				{
					if(substr_count(">=" , $o->prop("term")))
					{
						$term_sum = explode(">=");
						if(!($sum >= trim($term_sum[1])))
						{
							continue;
						}
					}
					elseif(substr_count("<=" , $o->prop("term")))
					{
						$term_sum = explode("<=");
						if(!($sum <= trim($term_sum[1])))
						{
							continue;
						}
					}
					elseif(substr_count(">" , $o->prop("term")))
					{
						$term_sum = explode(">");
						if(!($sum > trim($term_sum[1])))
						{
							continue;
						}
					}
					elseif(substr_count("<" , $o->prop("term")))
					{
						$term_sum = explode("<");
						if(!($sum < trim($term_sum[1])))
						{
							continue;
						}
					}

				}
				
				$pri = $o->prop("pri");
				$rel = $o;
			}
		}
		return $rel;
	}

}
?>
