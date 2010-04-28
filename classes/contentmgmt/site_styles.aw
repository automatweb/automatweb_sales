<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/site_styles.aw,v 1.14 2008/02/17 21:13:00 kristo Exp $
// site_styles.aw - Saidi stiilid 
//

// Usage: Create object, define alias, stylesheet files, default and random option.
// add to template empty SUB named SITE_STYLES__LOCATION, eg:
/*
<!-- SUB: SITE_STYLES__LOCATION -->
<!-- END SUB: SITE_STYLES__LOCATION -->
*/
// This will be replaced with html tags for css links
// Change styles with url argument /?set_style_(alias)=val where (alias) means object's alias 
// and val is one of: prev, next, last, random or numeric value representing style order num.
/*


@classinfo syslog_type=ST_SITE_STYLES no_comment=1 maintainer=kristo

@default table=objects
@default group=general

@property alias type=textbox
@caption Alias

@property styles type=text store=no
@caption Stiilifailide URLid

@property default_style type=textbox size=3 field=meta method=serialize
@caption Vaikimisi stiili jrk.

@property random type=chooser field=meta method=serialize 
@caption Juhuslik valik

@property vars type=table group=test

@groupinfo test caption="Muutujad"



*/

define("SITE_STYLES_NO_RANDOM", 1);
define("SITE_STYLES_RAND_SESSION", 2);
define("SITE_STYLES_RAND_REFRESH", 3);

class site_styles extends class_base implements main_subtemplate_handler
{
	const AW_CLID = 998;

	var $selected;

	function site_styles()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"clid" => CL_SITE_STYLES
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case 'styles':
				$prop['value'] = $this->admin_stylepicker_get($arr);
			break;
			case 'random':
				$prop['options'] = array(
					SITE_STYLES_NO_RANDOM => t("Ei valita juhuslikult"),
					SITE_STYLES_RAND_SESSION => t("Juhuslik sessiooniti"),
					SITE_STYLES_RAND_REFRESH => t("Juhuslik igal laadimisel"),
				);
			break;
			case "vars":
				//$arr["prop"]["value"] = $this->_get_styles($arr);
				$this->_get_styles($arr);
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
			//-- set_property --//
			case 'styles':
				$store = array ();
				// Reformat input into an array
				if (isset($arr['request']['styles']) && is_array($arr['request']['styles']))
				{	
					foreach ($arr['request']['styles'] as $in)
					{
						if (!strlen($in['url']))
						{
							continue; // delete empty styles
						}
						if (!is_numeric($in['ord']))
						{
							$noord[] = $in['url'];
						}
						else
						{
							$store[(int)$in['ord']] = $in['url'];
						}
						$mpic[$in["ord"]] = $in["menupic_nr"];
					}
				}
				$store = array_flip($store);
				asort($store, SORT_NUMERIC);

				// Replace order numbers to sequence
				$final = array();
				foreach ($store as $url => $ord)
				{
					$final[] = $url;
				}
				foreach ($noord as $ord => $url)
				{
					$final[] = $url;
				}
				$arr['obj_inst']->set_meta('styles', $final);
				$arr["obj_inst"]->set_meta('menupic_nrs', $mpic);
			break;
			case "vars":
				foreach($arr["request"]["var_name"] as $var_old_name => $var_new_name)
				{
					if(!strlen($var_new_name))
					{
						unset($arr["request"]["var_val"][$var_old_name]);
					}
					else
					{
						$tmp[$var_new_name] = $arr["request"]["var_val"][$var_old_name];
					}
				}
				$final = $tmp;
				if(strlen($arr["request"]["new_name"]) && is_array($arr["request"]["new"]))
				{
					foreach($arr["request"]["new"] as $style => $var_val)
					{
						$final[$arr["request"]["new_name"]][$style] = $var_val;
					}
				}
				foreach($final as $var_name=>$vars)
				{
					if(!strlen($var_name))
					{
						unset($final[$var_name]);
					}
				}
				$arr["obj_inst"]->set_meta('vars', $final);
			break;
		}
		return $retval;
	}	

	function admin_stylepicker_get($arr)
	{
		$html = "";
		$i = 0;
		$ord = 0;
		$value = $arr['obj_inst']->meta('styles');
		$menupic_nrs = $arr["obj_inst"]->meta("menupic_nrs");
		if (is_array($value))
		{
			foreach ($value as $ord => $url)
			{
				if (!strlen($url))
				{
					continue;
				}

				$html .= $this->_make_stylepicker_row($ord, $url, ++$i, $menupic_nrs[$ord]);
				$html .= '<br />';
			}
		}
		$html .= '<br />'.t("Lisa").":&nbsp;&nbsp;&nbsp;".$this->_make_stylepicker_row($ord+1, "", ++$i);
		return $html;
	}

	function _get_styles(&$arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		
		$styles = $arr["obj_inst"]->meta("styles");
		$vars = $arr["obj_inst"]->meta("vars");
		$tbl_a["header"][1] = array(
			"name" => "nr",
			"caption" => t("Nr"),
		);
		$tbl_a["header"][2] = array(
			"name" => "css_url",
			"caption" => t("CSS url"),
		);

		// for var names		
		foreach($vars as $var_name => $var_values)
		{
			$tbl_a[-1]["var_name[".$var_name."]"] = html::textbox(array(
					"value" => $var_name,
					"size" => "6",
					"name" => "var_name[".$var_name."]",
			));
		}
		// for other crap, wery shitty solution indeed..
		foreach($styles as $style => $css_url)
		{
			$tbl_a[$style]["nr"] = $style;
			$tbl_a[$style]["css_url"] = $css_url;
			foreach($vars as $var_name => $var_values)
			{
				// for var values
				$tbl_a[$style]["var_name[".$var_name."]"] = html::textbox(array(
						"value" => $var_values[$style],
						"size" => 9,
						"name" => "var_val[".$var_name."][".$style."]",
				));
				
				// for header
				$tbl_a["header"][$var_name] = array(
					"name" => "var_name[".$var_name."]",
					"caption" => $var_name,
				);
			}
			// new var textboxes for each style
			$tbl_a[$style]["new"] = html::textbox(array(
				"size" => "7",
				"name" => "new[".$style."]",
			));
		}
		$tbl_a[-1]["new"] = html::textbox(array(
			"size" => "7",
			"name" => "new_name",
		));
		$tbl_a["header"]["new"] = array(
			"name" => "new",
			"caption" => t("Uus muutuja"),
		);
		ksort($tbl_a);
		$t->set_sortable(false);
		$t->gen_tbl_from_array($tbl_a);
	}

	function _make_stylepicker_row($ord, $url, $idx, $menupic_nr = "")
	{
		$ret = t("jrk").": ".html::textbox(array(
			'name' => 'styles['.$idx.'][ord]',
			'value' => $ord,
			'size' => 3,
		));
		$ret .= " ".t("url").": ".html::textbox(array(
			'name' => 'styles['.$idx.'][url]',
			'value' => $url,
		));
		$ret .= " ".t("Men&uuml;&uuml;pildi nr").": ".html::textbox(array(
			"name" => "styles[".$idx."][menupic_nr]",
			"size" => 3,
			"value" => $menupic_nr,
		));
		return $ret;
	}


//-- methods --//
	/**
		Select random style
	**/
	function select_random($arr)
	{ 
		$this->select($arr, rand(0, $this->last_style_ord($arr)));
	}

	/**
		Select next style
	**/
	function select_next($arr)
	{
		$this->select($arr, $this->selected_style_ord($arr) >= $this->last_style_ord($arr) ? $this->last_style_ord($arr) : $this->selected_style_ord($arr)+1);
	}

	/**
		Select previous style
	**/
	function select_prev($arr)
	{
		$this->select($arr, $this->selected_style_ord($arr)>0 ? $this->selected_style_ord($arr)-1 : 0);
	}

	/**
		Select last style
	**/
	function select_last($arr)
	{
		$this->select($arr, $this->last_style_ord($arr));
	}

	/**
		Select style nr $ord
	**/
	function select($arr, $ord)
	{
		$o = obj($arr['oid']);
		$styles = $o->meta('styles');
		if (isset($styles[$ord]))
		{
			$_SESSION['style_'.$arr['oid']] = $ord;
			$this->selected = $ord;
		}
	}

	/**
		Get order number of selected style
	**/
	function selected_style_ord($arr)
	{
		if (is_null($this->selected) && is_oid($arr["oid"]))
		{
			$o = obj($arr['oid']);
			if(isset($_SESSION['style_'.$arr['oid']]))
			{
				$this->selected = $_SESSION['style_'.$arr['oid']];
			}
			else
			{
				$def = $o->prop('default_style');
				$this->selected = is_numeric($def) ? $def : 0;
			}
		}
		return $this->selected;
	}

	/**
		Get order number of last style
	**/
	function last_style_ord($arr)
	{
		$o = obj($arr['oid']);
		$styles = $o->meta('styles');
		return max(array_keys($styles));
	}

	/**
		Get url for currently selected style 
	**/
	function selected_style_url($arr)
	{
		$ord = $this->selected_style_ord($arr);
		$o = obj($arr['oid']);
		$styles = $o->meta('styles');
		return $styles[$ord];
	}
	
	/**
		called from site_show.. to import extra site vars
	**/
	function on_site_show_import_vars($arr)
	{
		// this call to _select_next_style should be moved if necessary. it selects next style.
		$this->_select_next_style();
		
		$ol = new object_list(array(
			"class_id" => CL_SITE_STYLES,
			"status" => STAT_ACTIVE,
			"lang_id" => "%",
		));
		$ar = $ol->arr();
		$ss = obj(key($ar));
		$styles = $ss->meta("styles");
		$vars = $ss->meta("vars");
		$sel = $this->selected_style_ord(array(
			"oid" => key($ar),
		));
		foreach(safe_array($vars) as $varname => $values)
		{
			$arr["inst"]->vars(array(
				$varname => $values[$sel],
			));
		}
	}

	/** this has to be separate function, because call to it may be moved. shit solution actually. 
	**/
	function _select_next_style()
	{

		$ol = new object_list(array(
			'class_id' => CL_SITE_STYLES,
			'status' => STAT_ACTIVE,
		));

		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$alias = $o->prop('alias');
			$inst = $o->instance();
			$aoid = array('oid' => $o->id());

			if (isset($_REQUEST['set_style_'.$alias]))
			{
				switch ($_REQUEST['set_style_'.$alias])
				{
					case 'next':
						$inst->select_next($aoid);
					break;
					case 'prev':
						$inst->select_prev($aoid);
					break;
					case 'last':
						$inst->select_last($aoid);
					break;
					case 'random':
						$inst->select_random($aoid);
					break;
					default:
						$inst->select($aoid, $_REQUEST['set_style_'.$alias]);
					break;
				}
			}
			else
			{
				$r = $o->prop('random');
				if ($r == SITE_STYLES_RAND_REFRESH || ($r == SITE_STYLES_RAND_SESSION && !isset($_SESSION['style_'.$o->id()]) ))
				{
					$inst->select_random($aoid);
				}
			}
		}

	}

	/**
		Called by message ON_SITE_SHOW_IMPORT_VARS, adds value for template variable {VAR:styles}
	**/
	function on_get_subtemplate_content($arr)
	{
		$fn = "site_styles::on_get_subtemplate_content";
		enter_function($fn);
		$name1 = 'SITE_STYLES__LOCATION';
		// Double is no trouble
		if ($arr['inst']->is_template($name1))
		{
			$styles = "";
			$ol = new object_list(array(
				'class_id' => CL_SITE_STYLES,
				'status' => STAT_ACTIVE,
				"lang_id" => array(),
			));

			for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				$link = '<link href="%s" rel="stylesheet" type="text/css"';
				$alias = $o->prop('alias');
				$aoid = array('oid' => $o->id());
				if (!empty($alias))
				{
					$link .= ' id="'.$alias.'"';
				}	
				$inst = $o->instance();
				$styles .= sprintf($link.">\n", $inst->selected_style_url($aoid));
			}
			if ($styles != "")
			{
				$arr['inst']->vars(array(
					$name1 => $styles
				)); 
			}
		}
		else
		{
			exit_function($fn);
			return;
		}
		exit_function($fn);
	}
}
?>
