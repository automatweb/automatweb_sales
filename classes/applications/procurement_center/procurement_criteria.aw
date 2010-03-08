<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_criteria.aw,v 1.3 2007/12/06 14:33:50 kristo Exp $
// procurement_criteria.aw - Pakkumise hindamise kriteerium 
/*

@classinfo syslog_type=ST_PROCUREMENT_CRITERIA relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@tableinfo aw_procurement_criteria index=aw_oid master_index=brother_of master_table=objects

@default table=aw_procurement_criteria
@default group=general

	@property type type=select field=aw_type
	@caption T&uuml;p

	@property pct type=textbox size=5 field=aw_pct
	@caption T&auml;htsusprotsent
*/

define("CRIT_TIME", 1);
define("CRIT_PRICE", 2);
define("CRIT_ASSESS", 3);
class procurement_criteria extends class_base
{
	function procurement_criteria()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_criteria",
			"clid" => CL_PROCUREMENT_CRITERIA
		));
		$this->types = array(
			CRIT_TIME => t("Kiire valmimine"),
			CRIT_PRICE => t("Madal hind"),
			CRIT_ASSESS => t("K&otilde;rged hinded")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "type":
				$prop["options"] = $this->get_types();
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_procurement_criteria(aw_oid int primary key, aw_type int, aw_pct double)");
			return true;
		}
	}

	function get_score_for_crit($crit, $offers, $proc)
	{
		switch($crit->prop("type"))
		{
			case CRIT_TIME:
				// get slowest time - score 0
				// get fastest time - score 100, interpolate between
				$min = $proc->prop("completion_date") + 24*3600*1000;
				$max = 0;
				foreach($offers->arr() as $offer)
				{
					if ($offer->prop("completion_date") > 300)
					{
						$min = min($min, $offer->prop("completion_date"));
						$max = max($max, $offer->prop("completion_date"));
					}
				}

				$ret = array();
				$diff = $max - $min;
				$step = $diff / 100.0;
				foreach($offers->arr() as $offer)
				{
					$ret[$offer->id()] = ($max - $offer->prop("completion_date")) / $step;
				}
				return $ret;

			case CRIT_PRICE:
				// get smallest price - score 100
				// get highest price - score 0, interpolate between
				$min = 2000000000;
				$max = 0;
				foreach($offers->arr() as $offer)
				{
					$min = min($min, $offer->prop("price"));
					$max = max($max, $offer->prop("price"));
				}

				$ret = array();
				$diff = $max - $min;
				$step = $diff / 100.0;
				foreach($offers->arr() as $offer)
				{
					$ret[$offer->id()] = ($max - $offer->prop("price")) / $step;
				}
				return $ret;

			case CRIT_ASSESS:
				// 0 is smallest score
				// get max score - 100
				$min = 0;
				$max = 0;
				$avgs = array();
				foreach($offers->arr() as $offer)
				{
					$oi = $offer->instance();
					$avgs[$offer->id()] = $oi->get_avg_score($offer);
					$oi = $offer->instance();
					$max = max($max, $avgs[$offer->id()]);
				}
				$ret = array();
				$diff = $max - $min;
				$step = $diff / 100.0;
				foreach($offers->arr() as $offer)
				{
					$ret[$offer->id()] = ($avgs[$offer->id()] - $min) / $step;
				}
				return $ret;

			default:
				error::raise(array(
					"id" => "ERR_NO_CRIT",
					"msg" => sprintf(t("procurement_criteria::get_score_for_crit(): no type set for criteria %s"),$crit->prop("type"))
				));
		}
	}

	function get_types()
	{
		return $this->types;
	}
}
?>
