<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/rate/rate_scale.aw,v 1.20 2008/03/31 12:08:29 instrumental Exp $

/*

@classinfo syslog_type=ST_RATE_SCALE relationmgr=yes maintainer=kristo

@groupinfo scale caption=Skaala

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property rate_clid type=select multiple=1
@caption Mis objektit&uuml;&uuml;bile skaala kehtib

@property rate_folders type=relpicker multiple=1 reltype=RELTYPE_FOLDER
@caption Mis kataloogidele skaala kehtib

@default group=scale

@property nr_from type=textbox size=3 
@caption Alates

@property nr_to type=textbox size=3
@caption Kuni

@property nr_step type=textbox size=3
@caption Aste

@property nr_descs type=callback callback=_get_nr_desc_form
@caption Tekst hindele

@property no_rate type=chooser multiple=1
@caption Lisa "ei hinda" valik

@reltype FOLDER value=1 clid=CL_MENU
@caption Kehtiv kataloog

*/

define("RATING_NUMERIC", 1);
define("RATING_TEXT", 2);

class rate_scale extends class_base
{
	function rate_scale()
	{
		$this->init(array(
			"clid" => CL_RATE_SCALE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		switch($prop["name"])
		{
			case "no_rate":
				$prop["options"] = array(1 => "");
				break;
			case "rate_clid":
				$prop['options'] = get_class_picker();
				break;
		}
		return PROP_OK;
	}

	function set_property ($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
			case 'nr_step':
				if (!is_numeric($prop['value']) || $prop['value'] < 1)
				{
					$prop['value'] = 1;
				}
			break;
			case 'nr_descs':
				$descs = $arr['obj_inst']->prop('nr_descs');
				foreach ($prop['value'] as $i => $desc)
				{
					if (is_numeric($i))
					{
						$descs[$i] = $desc;
					}
				}
				$prop['value'] = $descs;
			break;
		}
		return $retval;
	
	}

	function callback_post_save($arr)
	{
		extract($arr);
		$ob = $arr["obj_inst"];
		$id = $ob->id();
		$this->db_query("DELETE FROM rate2menu WHERE rate_id = '$id'");
		$this->db_query("DELETE FROM rate2clid WHERE rate_id = '$id'");
		
		$d = new aw_array($ob->prop("rate_folders"));
		$clids = new aw_array($ob->prop("rate_clid"));
		foreach ($clids->get() as $rate_clid)
		{
			foreach($d->get() as $fld)
			{
				$this->db_query("INSERT INTO rate2menu(menu_id, rate_id, clid) VALUES('$fld','$id','".$rate_clid."')");
			}

			if ($d->count() == 0)
			{
				$this->db_query("INSERT INTO rate2clid(rate_id, clid) VALUES('$id','".$rate_clid."')");
			}
			else
			{
				$d->reset();
			}
		}	
	}

	// Return rate_scale objects that can be applied to object $oid
	function get_scale_objs_for_obj($oid, $onlyfirst = false)
	{
		$ob = obj($oid);
		$oc = $ob->path();
		$scales = array();
		foreach($oc as $odp)
		{
			$sql = "SELECT clid, rate_id FROM rate2menu WHERE menu_id = '".$odp->id()."'";
			$this->db_query($sql);
			while ($row = $this->db_next())
			{
				if ($row["clid"] == $ob->class_id())
				{
					if ($onlyfirst)
					{
						return array($row["rate_id"]);
					}
					else
					{
						$scales[] = $row['rate_id'];
					}
				}
			}
		}
		$this->db_query("SELECT rate_id FROM rate2clid WHERE clid = '".$ob->class_id()."'");
		while ($row = $this->db_next())
		{
			$scales[] = $row['rate_id'];
		}
		return $scales;
	}

	// Returns first found rate_scale object from object $oid
	function get_scale_for_obj($oid)
	{
		// Get all scales
		$scales = $this->get_scale_objs_for_obj($oid, true);
		if (is_array($scales) && count($scales) > 0)
		{
			return $this->_get_scale($scales[0]);
		}
		else
		{
			return array();
			//$this->raise_error(ERR_RATE_NOT_FOUND, "rate::get_scale_for_obj($oid) - no rate for object is set!", true, false);
		}
	}

	/**
		@attrib name=_get_scale api=1 params=pos

		@param id required type=oid acl=view

		@returns Array of rate scale options. (value => caption)
	**/
	function _get_scale($id)
	{
		$ret = array();
		if ($this->can("view", $id))
		{
			$ob = obj($id);
			$no_rate = $ob->prop("no_rate");
			if(!empty($no_rate))
			{
				$ret[0] = t("ei hinda");
			}
			if ($ob->prop("nr_step") >= 1) // let's prevent that infinite loop, shall we
			{
				$descs = $ob->prop('nr_descs');
				for ($i = $ob->prop("nr_from"); $i <= $ob->prop("nr_to"); $i += $ob->prop("nr_step"))
				{
					$ret[$i] = isset($descs[$i]) ? $descs[$i] : $i;
				}
			}
		}
		return $ret;
	}

	// Generates form for adding descriptions to numerical values
	function _get_nr_desc_form ($arr)
	{
		$retval = array();
		$ob = $arr['obj_inst'];
		if ($ob->prop("nr_step") >= 1) // let's prevent that infinite loop here too, shall we
		{
			$descs = $ob->prop('nr_descs');
			for ($i = $ob->prop("nr_from"); $i <= $ob->prop("nr_to"); $i += $ob->prop("nr_step"))
			{
				$retval[] = array(
					'type' => 'textbox',
					'size' => 60,
					'name' => $arr['prop']['name'].'['.$i.']',
					'caption' => $arr['prop']['caption'] . $i,
					'value' => ifset($descs,$i),
				);
			}
		}
		return $retval;
	}
}
?>
