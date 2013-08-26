<?php

define("OP_START_BLK", 1);
define("OP_END_BLK", 2);
define("OP_IF_VISIBLE", 3);			// params { a_parent, level, in_parent_tpl }
define("OP_SHOW_ITEM", 4);			// params { tpl (fully qualified name), has_image_tpl, no_image_tpl}

define("OP_LOOP_LIST_BEGIN", 5);	// params { a_parent, level, in_parent_tpl}

// list filter creation
define("OP_LIST_BEGIN", 6);			// params { a_parent, level, a_parent_p_fn}
define("OP_LIST_FILTER", 7);		// params { prop, value }
define("OP_LIST_END", 8);			// params {}

define("OP_LOOP_LIST_END", 9);		// params { tpl (not-fully qualified name)}

define("OP_IF_BEGIN", 10);			// params {}
define("OP_IF_COND", 11); 			// params {prop, value}
define("OP_IF_END", 12);			// params {}
define("OP_IF_ELSE", 13);			// params {}

define("OP_CHECK_SUBITEMS_SEL", 14);// params { tpl, fq_tpl }

define("OP_AREA_CACHE_CHECK", 15);	// params { a_parent,level,a_name }
define("OP_AREA_CACHE_SET", 16);	// params { a_parent,level }

define("OP_CHECK_NO_SUBITEMS_SEL", 17);// params { tpl, fq_tpl }

define("OP_SHOW_ITEM_INSERT", 18);			// params { tpl (fully qualified name), has_image_tpl, no_image_tpl}

define("OP_INSERT_SEL_IDS", 19);

define("OP_IF_OBJ_TREE", 20);		// params { a_parent, level}
define("OP_GET_OBJ_TREE_LIST", 21);	// params { a_parent, level, a_parent_p_fn}

define("OP_LIST_INIT", 22);	// params { a_parent, level, a_parent_p_fn}

define("OP_HAS_LUGU", 23);	// params { a_parent, level, a_parent_p_fn}

define("OP_IF_SUBMENUS", 24);		// params { a_parent, level}
define("OP_GET_OBJ_SUBMENUS", 25);	// params { a_parent, level, a_parent_p_fn}
define("OP_IF_LOGGED", 26);	// params { }

define("OP_GRP_BEGIN", 27);
define("OP_GRP_END", 28);

define("OP_LIST_INIT_END", 29);	// params { level }

define("OP_AUTOGRP_BEGIN", 30);
define("OP_AUTOGRP_END", 31);

class site_template_compiler extends aw_template
{
	function site_template_compiler()
	{
		$this->init("automatweb/menuedit");
		$this->op_lut = array(
			1 => "OP_START_BLK",
			2 => "OP_END_BLK",
			3 => "OP_IF_VISIBLE",
			4 => "OP_SHOW_ITEM",
			5 => "OP_LOOP_LIST_BEGIN",
			6 => "OP_LIST_BEGIN",
			7 => "OP_LIST_FILTER",
			8 => "OP_LIST_END",
			9 => "OP_LOOP_LIST_END",
			10 => "OP_IF_BEGIN",
			11 => "OP_IF_COND",
			12 => "OP_IF_END",
			13 => "OP_IF_ELSE",
			14 => "OP_CHECK_SUBITEMS_SEL",
			15 => "OP_AREA_CACHE_CHECK",
			16 => "OP_AREA_CACHE_SET",
			17 => "OP_CHECK_NO_SUBITEMS_SEL",
			18 => "OP_SHOW_ITEM_INSERT",
			19 => "OP_INSERT_SEL_IDS",
			20 => "OP_IF_OBJ_TREE",
			21 => "OP_GET_OBJ_TREE_LIST",
			22 => "OP_LIST_INIT",
			23 => "OP_HAS_LUGU",
			24 => "OP_IF_SUBMENUS",
			25 => "OP_GET_OBJ_SUBMENUS",
			26 => "OP_IF_LOGGED",
			27 => "OP_GRP_BEGIN",
			28 => "OP_GRP_END",
			29 => "OP_LIST_INIT_END",
			30 => "OP_AUTOGRP_BEGIN",
			31 => "OP_AUTOGRP_END",
		);

		$this->id_func = (aw_ini_get("menuedit.show_real_location") != 1 ? "brother_of" : "id");
	}

	function compile($path, $tpl, $mdefs = NULL, $no_cache = false)
	{
		$this->tpl_init($path, true);
		$this->no_use_ma_cache = $no_cache;
		//echo "compiling \$this->read_template($tpl,true)<br>";
		$success = $this->read_template($tpl);

		if (!$success)
		{
			return false;
		}

		$this->tplhash = md5($path.$tpl);
		$this->parse_template_parts($mdefs);
		$this->compile_template_parts();
		$code =  "<?php\n".$this->generate_code()."?>";
		return $code;
	}

	////
	// !this parses the template parts into data that the compiler uses
	// so this is sort of a 3-step compilation process
	function parse_template_parts($mdefs = NULL)
	{
		$this->menu_areas = array();

		// get all subtemplates
		$tpls = $this->get_subtemplates_regex("(MENU_.*)");

		// now figure out the menu areas that are used
		$_tpls = array();
 		foreach($tpls as $tpl)
		{
			list($tpl) = explode(".", $tpl);
			$_tpls[] = $tpl;
		}
		$tpls = array_unique($_tpls);

		if ($mdefs === NULL)
		{
			$mdefs = aw_ini_get("menuedit.menu_defs");
			if (aw_ini_get("menuedit.lang_defs") == 1)
			{
				$mdefs = $mdefs[AW_REQUEST_CT_LANG_ID];
			}
		}

		if (!empty($_GET["TPLC_DBG"]))
		{
			echo "tpls = ".dbg::dump($tpls)." <br>";
		}
		foreach($tpls as $tpl)
		{
			$parts = explode("_", $tpl);
			$area = $parts[1];
			if ($parts[2] === "SEEALSO")
			{
				continue;
			}
			$level = substr($parts[2], 1);

			if (!$this->_mf_srch($area, $mdefs))
			{
				continue;
			}

			if ($parts[3] === "GRP")
			{
				$this->menu_areas[$area]["grps"][$level] = $parts[4];
				continue;
			}
			elseif($parts[3] === "AUTOGRP")
			{
				$this->menu_areas[$area]["autogrps"][$level] = $parts[4];
				continue;
			}

			$this->menu_areas[$area]["levels"][$level]["templates"][] = $parts;
			$this->menu_areas[$area]["parent"] = $this->_mf_srch($area, $mdefs);
			foreach($parts as $part)
			{
				$this->menu_areas[$area]["levels"][$level]["all_opts"][$part] = $part;
			}

			// check if it has HAS_IMAGE subtemplate or NO_IMAGE subtemplate
			if ($this->is_parent_tpl("HAS_IMAGE", $tpl) || $this->is_template($tpl.".HAS_IMAGE"))
			{
				$this->menu_areas[$area]["levels"][$level]["has_image_tpl"] = 1;
			}
			if ($this->is_parent_tpl("NO_IMAGE", $tpl) || $this->is_template($tpl.".NO_IMAGE"))
			{
				$this->menu_areas[$area]["levels"][$level]["no_image_tpl"] = 1;
			}

			if ($this->is_parent_tpl("HAS_COMMENT", $tpl) || $this->is_template($tpl.".HAS_COMMENT"))
			{
				$this->menu_areas[$area]["levels"][$level]["has_comment_tpl"] = 1;
			}
			if ($this->is_parent_tpl("NO_COMMENT", $tpl) || $this->is_template($tpl.".NO_COMMENT"))
			{
				$this->menu_areas[$area]["levels"][$level]["no_comment_tpl"] = 1;
			}


			if ($this->is_parent_tpl("HAS_LUGU", $tpl) || $this->is_template($tpl.".HAS_LUGU"))
			{
				$this->menu_areas[$area]["levels"][$level]["has_lugu"] = 1;
			}

			if ($this->is_template("PREV_LINK_".$area."_L".$level))
			{
				$this->menu_areas[$area]["levels"][$level]["has_prev_link"] = 1;
			}

			if ($this->is_template("NEXT_LINK_".$area."_L".$level))
			{
				$this->menu_areas[$area]["levels"][$level]["has_next_link"] = 1;
			}

			// figure out if the template was inside another menu template
			// 	to do that, we get the parent template and check if it has the same menu area and level -1
			$is_in_parent = false;

			$parent_tpls = $this->get_parent_templates($tpl);
			foreach($parent_tpls as $parent_tpl)
			{
				if (strpos($parent_tpl, "GRP_") !== false)
				{
					$parent_tpls = $this->get_parent_templates($parent_tpl);
					$parent_tpl = reset($parent_tpls);
				}

				if (substr($parent_tpl, 0, 5) === "MENU_")
				{
					$parts = explode("_", $parent_tpl);
					$parent_area = $parts[1];
					$parent_level = substr($parts[2], 1);
					if ($parent_area == $area && ($parent_level+1) == $level)
					{
						$is_in_parent = true;
					}
					// set this template as the parent's sub template
					$this->menu_areas[$parent_area]["levels"][$parent_level]["child_tpls"][$parent_tpl] = array(
						"area" => $area,
						"level" => $level,
						"parts" => $parts
					);
				}
				else
				if (substr($parent_tpl, 0, strlen("HAS_SUBITEMS")) === "HAS_SUBITEMS" || substr($parent_tpl, 0, strlen("NO_SUBITEMS")) === "NO_SUBITEMS")
				{
					// fetch the parent templates for that and try again
					$parent_tpls2 = $this->get_parent_templates($parent_tpl);
					foreach($parent_tpls2 as $parent_tpl2)
					{
						if (substr($parent_tpl2, 0, 5) === "MENU_")
						{
							$parts = explode("_", $parent_tpl2);
							$parent_area = $parts[1];
							$parent_level = substr($parts[2], 1);
							if ($parent_area == $area && ($parent_level+1) == $level)
							{
								$is_in_parent = true;
							}
							// set this template as the parent's sub template
							$this->menu_areas[$parent_area]["levels"][$parent_level]["child_tpls"][$parent_tpl2] = array(
								"area" => $area,
								"level" => $level,
								"parts" => $parts,
							);
						}
					}
				}

				if (!isset($this->menu_areas[$area]["levels"][$level]["inside_parent_menu_tpl"]))
				{
					$this->menu_areas[$area]["levels"][$level]["inside_parent_menu_tpl"] = null;
				}
				$this->menu_areas[$area]["levels"][$level]["inside_parent_menu_tpl"] |= $is_in_parent;

				if ($parent_tpl == "logged")
				{
					$this->menu_areas[$area]["levels"][$level]["in_logged"] = true;
				}
			}
		}
		// HAS_SUBITEMS_AREA_L1_SEL check - these will go after each level is inserted in the template
		$tpls = $this->get_subtemplates_regex("(HAS_SUBITEMS.*)");
		// now figure out the menu areas that are used
		$_tpls = array();
 		foreach($tpls as $tpl)
		{
			list($tpl) = explode(".", $tpl);
			$_tpls[] = $tpl;
		}
		$tpls = array_unique($_tpls);
		foreach($tpls as $tpl)
		{
			$parts = explode("_", $tpl);
			$area = $parts[2];
			$p_fqname = $this->v2_name_map[$tpl];

			$has_inside = false;

			// check if the no subitems tpl has any menu templates inside it
			foreach($this->v2_name_map as $shname => $fqname)
			{
				//if (strlen($fqname) > strlen($p_fqname))
				//{
					if (strpos($fqname, $tpl) !== false)
					{
						$has_inside = true;
					}
				//}
			}

			$level = substr($parts[3], 1)+1;


			if ($has_inside && $parts[count($parts)-1] !== "AFTER")
			{
				$this->menu_areas[$area]["levels"][$level]["has_subitems_sel_check"] = true;
				$this->menu_areas[$area]["levels"][$level]["has_subitems_sel_check_tpl"] = $tpl;
				$this->menu_areas[$area]["levels"][$level]["has_subitems_sel_check_tpl_fq"] = $this->v2_name_map[$tpl];
			}
			else
			{
				$this->menu_areas[$area]["levels"][($level-1)]["has_subitems_sel_check_after_item"] = true;
				$this->menu_areas[$area]["levels"][($level-1)]["has_subitems_sel_check_tpl"] = $tpl;
				$this->menu_areas[$area]["levels"][($level-1)]["has_subitems_sel_check_tpl_fq"] = $this->v2_name_map[$tpl];
			}
		}


		// NO_SUBITEMS_AREA_L1 check - these will go after each level is inserted in the template
		$tpls = $this->get_subtemplates_regex("(NO_SUBITEMS.*)");

		// now figure out the menu areas that are used
		$_tpls = array();
 		foreach($tpls as $tpl)
		{
			list($tpl) = explode(".", $tpl);
			$_tpls[] = $tpl;
		}
		$tpls = array_unique($_tpls);
		foreach($tpls as $tpl)
		{
			$parts = explode("_", $tpl);
			$area = $parts[2];

			$p_fqname = $this->v2_name_map[$tpl];

			$has_inside = false;

			// check if the no subitems tpl has any menu templates inside it
			foreach($this->v2_name_map as $shname => $fqname)
			{
				if (strlen($fqname) > strlen($p_fqname))
				{
					if (substr($fqname, 0, strlen($p_fqname)) == $p_fqname)
					{
						$has_inside = true;
					}
				}
			}

			$level = substr($parts[3], 1)+1;

			if ($has_inside && $parts[count($parts)-1] !== "AFTER")
			{
				$this->menu_areas[$area]["levels"][$level]["no_subitems_sel_check"] = true;
				$this->menu_areas[$area]["levels"][$level]["no_subitems_sel_check_tpl"] = $tpl;
				$this->menu_areas[$area]["levels"][$level]["no_subitems_sel_check_tpl_fq"] = $this->v2_name_map[$tpl];
			}
			else
			{
				$this->menu_areas[$area]["levels"][($level-1)]["no_subitems_sel_check_after_item"] = true;
				$this->menu_areas[$area]["levels"][($level-1)]["no_subitems_sel_check_tpl"] = $tpl;
				$this->menu_areas[$area]["levels"][($level-1)]["no_subitems_sel_check_tpl_fq"] = $this->v2_name_map[$tpl];
			}
		}

		if (!empty($_GET["TPLC_DBG"]))
		{
			echo "menu_areas= ".dbg::dump($this->menu_areas)." <br>";
		}
	}

	function _mf_srch($area, $defs)
	{
		foreach($defs as $mdid => $md)
		{
			if (in_array($area, explode(",", $md)))
			{
				return $mdid;
			}
		}
		return false;
	}

	function compile_template_parts()
	{
		// go over all the used templates found
		// and make a list of script actions to generate code from
		$this->ops = array();

		$this->no_top_level_code_for = array();

		$this->ops[] = array(
			"op" => OP_INSERT_SEL_IDS,
			"params" => array(
				"data" => $this->menu_areas
			)
		);

		$this->req_level = 0;

		foreach($this->menu_areas as $area => $adat)
		{
			if ($area === "LOGGED")
			{
				$adat["a_parent_p_fn"] = "\$this->_helper_get_login_menu_id()";
			}
			else
			{
				$adat["a_parent_p_fn"] = $adat["parent"];
			}

			ksort($adat["levels"]);
			foreach($adat["levels"] as $level => $ldat)
			{
				if (isset($this->no_top_level_code_for[$area][$level]))
				{
					continue;
				}
				// WHATTA FUCK IS THIS SUPPOSED TO BE????
				// all the pages that have a glitch in their template,
				// are more than a bit fucked up "thanks" to this -- ahz
				$this->compile_template_level($area, $adat, $level, $ldat);
			}
		}

		if (!empty($_GET["TPLC_DBG"]))
		{
			$this->dbg_show_template_ops();
		}
	}

	function compile_template_level($area, $adat, $level, $ldat)
	{
		if (!isset($ldat["templates"]) || !is_array($ldat["templates"]))
		{
			return;
		}

		/*if ($this->_a_comp[$area][$level] == 1)
		{
			error::raise(array(
				"id" => "ERR_TPL",
				"msg" => sprintf(t("site_template_compiler::cimpile_template_level(%s, %s): broken template near MENU_%s_L%s"), $area, $level, $area, $level)
			));
		}
		$this->_a_comp[$area][$level] = 1;
		*/
		$this->req_level ++;

		$end_block = false;

		if (!empty($ldat["in_logged"]))
		{
			$this->ops[] = array(
				"op" => OP_IF_LOGGED,
				"params" => array()
			);
			$this->ops[] = array(
				"op" => OP_START_BLK,
				"params" => array()
			);
		}

		// figure out if we need to determine visibility
		if ($level > 1)
		{
			$this->ops[] = array(
				"op" => OP_IF_VISIBLE,
				"params" => array(
					"a_parent" => $adat["parent"],
					"level" => $level,
					"in_parent_tpl" => $ldat["inside_parent_menu_tpl"]
				)
			);
			$this->ops[] = array(
				"op" => OP_START_BLK,
				"params" => array()
			);
			$end_block = true;
		}


		if ($this->req_level == 1 && aw_ini_get("template_compiler.no_menu_area_cache") != 1 && !$this->no_use_ma_cache)
		{
			$this->ops[] = array(
				"op" => OP_AREA_CACHE_CHECK,
				"params" => array(
					"a_parent" => $adat["parent"],
					"level" => $level,
					"a_name" => $area
				)
			);
		}

		// groups
		if (!empty($adat["grps"][$level]))
		{
			$this->ops[] = array(
				"op" => OP_GRP_BEGIN,
				"params" => array(
					"a_parent" => $adat["parent"],
					"level" => $level,
					"a_name" => $area,
					"grp_cnt" => $adat["grps"][$level]
				)
			);
		}

		// autogroups
		if (!empty($adat["autogrps"][$level]))
		{
			$this->ops[] = array(
				"op" => OP_AUTOGRP_BEGIN,
				"params" => array(
					"a_parent" => $adat["parent"],
					"level" => $level,
					"a_name" => $area,
					"autogrp_cnt" => $adat["autogrps"][$level]
				)
			);
		}

		// now figure out the code for displaying
		// menu items

		// go over all different subtemplate
		// combos for this level
		// for each make the appropriate list
		// and then display it
		//
		// that was all nice and dandy, except for the littel detail that it *didn't fucking work*
		// problem was - if you had a _sel template, then the selected menu has to use that
		// and it might be in the middle somewhere, and then the different-list-for-each-option-combo broke down
		// so now we do the one-list-per-area-level and then if's to match the correct template to the current obj

		// if the menu has the object_tree property set, the list has to come from the object tree, so check

		$this->ops[] = array(
			"op" => OP_LIST_INIT,
			"params" => array(
				"a_parent" => $adat["parent"],
				"a_parent_p_fn" => ( !empty($adat["a_parent_p_fn"]) ) ? $adat["a_parent_p_fn"] : '',
				"level" => $level,
				"in_parent_tpl" => $ldat["inside_parent_menu_tpl"]
			)
		);

		$this->_insert_obj_tree_ops($adat, $ldat, $level);

		$this->ops[] = array(
			"op" => OP_LOOP_LIST_BEGIN,
			"params" => array(
				"has_prev_link" => ( !empty($ldat["has_prev_link"]) ) ? $ldat["has_prev_link"] : '',
				"has_next_link" => ( !empty($ldat["has_next_link"]) ) ? $ldat["has_next_link"] : '',
				"a_name" => $area,
				"level" => $level,
				"a_parent" => $adat["parent"]
			)
		);

		$tt = $ldat["templates"];
		usort($tt, create_function('$a, $b','$ca = count($a); $cb = count($b); if ($ca == $cb) { return 0; }else if ($ca > $cb) { return -1; }else{return 1;}'));
		// right, now we gots to figure out the template
		// we do that by trying to match the current object to any template on this area on this level
		foreach($tt as $idx => $tdat)
		{
			if ($idx > 0)
			{
				$this->ops[] = array(
					"op" => OP_IF_ELSE,
					"params" => array()
				);
			}

			$cur_tpl = join("_",$tdat);
			$cur_tpl_fqn = $this->v2_name_map[$cur_tpl];

			$this->ops[] = array(
				"op" => OP_IF_BEGIN,
				"params" => array()
			);

			$no_display_item = false;
			$has_sel = false;
			foreach($tdat as $tpl_opt)
			{
				$params = $this->get_if_filter_from_tpl_opt($tpl_opt, $ldat["all_opts"]);
				if ($params)
				{
					$params["a_parent"] = $adat["parent"];
					$params["level"] = $level;
					$this->ops[] = array(
						"op" => OP_IF_COND,
						"params" => $params
					);
					$no_display_item |= !empty($params["no_display_item"]);
				}
				if ($tpl_opt === "SEL")
				{
					$has_sel = true;
				}
			}

			$this->ops[] = array(
				"op" => OP_IF_END,
				"params" => array()
			);

			$this->ops[] = array(
				"op" => OP_START_BLK,
				"params" => array()
			);

			if (!$no_display_item)
			{
				// here we gotta check if we need to
				// insert the items for the next level in between here.
				// to do that, we need to check if the next level subtemplate is
				// inside the current template
				if (isset($ldat["child_tpls"][$cur_tpl]))
				{
					$chd_tpl_dat = $ldat["child_tpls"][$cur_tpl];
					$chd_area = $chd_tpl_dat["area"];
					$chd_lv = $chd_tpl_dat["level"];
					if (!($chd_area == $area && $chd_lv == $level))
					{
						$this->compile_template_level($chd_area, $this->menu_areas[$chd_area], $chd_lv, $this->menu_areas[$chd_area]["levels"][$chd_lv]);
					}
					$this->no_top_level_code_for[$chd_area][$chd_lv] = true;
				}

				if (!empty($ldat["has_lugu"]))
				{
					$this->ops[] = array(
						"op" => OP_HAS_LUGU,
						"params" => array(
							"tpl" => $cur_tpl_fqn,
							"has_image_tpl" => $ldat["has_image_tpl"],
							"has_comment_tpl" => $ldat["has_comment_tpl"],
							"no_comment_tpl" => $ldat["no_comment_tpl"],
							"no_image_tpl" => $ldat["no_image_tpl"],
						)
					);
				}

				$this->ops[] = array(
					"op" => OP_SHOW_ITEM,
					"params" => array(
						"tpl" => $cur_tpl_fqn,
						"has_image_tpl" => ( !empty($ldat["has_image_tpl"]) ) ? $ldat["has_image_tpl"] : '',
						"no_image_tpl" => ( !empty($ldat["no_image_tpl"]) ) ? $ldat["no_image_tpl"] : '',
						"has_comment_tpl" => ( !empty($ldat["has_comment_tpl"]) ) ? $ldat["has_comment_tpl"] : '',
						"no_comment_tpl" => ( !empty($ldat["no_comment_tpl"]) ) ? $ldat["no_comment_tpl"] : '',
						"a_parent" => ( !empty($adat["parent"]) ) ? $adat["parent"] : '',
						"level" => $level,
						"has_prev_link" => ( !empty($ldat["has_prev_link"]) ) ? $ldat["has_prev_link"] : '',
						"has_next_link" => ( !empty($ldat["has_next_link"]) ) ? $ldat["has_next_link"] : ''
					)
				);

				if (!empty($ldat["has_subitems_sel_check_after_item"]))
				{
					$this->ops[] = array(
						"op" => OP_CHECK_SUBITEMS_SEL,
						"params" => array(
							"tpl" => ( !empty($ldat["has_subitems_sel_check_tpl"]) ) ? $ldat["has_subitems_sel_check_tpl"] : '',
							"fq_tpl" => $cur_tpl_fqn . "." . ( !empty($ldat["has_subitems_sel_check_tpl"]) ) ? $ldat["has_subitems_sel_check_tpl"] : '',
							"a_parent" => ( !empty($adat["parent"]) ) ? $adat["parent"] : '',
							"level" => $level+1
						)
					);
				}

				$tpl_l = isset($ldat["no_subitems_sel_check_tpl"]) ? strlen($ldat["no_subitems_sel_check_tpl"]) : 0;
				$tpl_s = isset($ldat["no_subitems_sel_check_tpl"]) ? $ldat["no_subitems_sel_check_tpl"] : "";
				if (!empty($ldat["no_subitems_sel_check_after_item"]) && ($has_sel || !($tpl_s{$tpl_l-1} == "L" && $tpl_s{$tpl_l-2} == "E" && $tpl_s{$tpl_l-3} == "S")))
				{
					$this->ops[] = array(
						"op" => OP_CHECK_NO_SUBITEMS_SEL,
						"params" => array(
							"tpl" => $ldat["no_subitems_sel_check_tpl"],
							"fq_tpl" => $ldat["no_subitems_sel_check_tpl_fq"]//$cur_tpl_fqn.".".$ldat["no_subitems_sel_check_tpl"],
						)
					);
				}

				$grp_p = false;
				if (!empty($adat["grps"][$level]))
				{
					$grp_p = array(
						"a_parent" => $adat["parent"],
						"level" => $level,
						"a_name" => $area,
						"grp_cnt" => $adat["grps"][$level],
						"tpl" => $cur_tpl_fqn
					);
				}
				$autogrp_p = false;
				if (!empty($adat["autogrps"][$level]))
				{
					$autogrp_p = array(
						"a_parent" => $adat["parent"],
						"level" => $level,
						"a_name" => $area,
						"autogrp_cnt" => $adat["autogrps"][$level],
						"tpl" => $cur_tpl_fqn
					);
				}
				$this->ops[] = array(
					"op" => OP_SHOW_ITEM_INSERT,
					"params" => array(
						"tpl" => $cur_tpl_fqn,
						"has_image_tpl" => ( !empty($ldat["has_image_tpl"]) ) ? $ldat["has_image_tpl"] : '',
						"no_image_tpl" => ( !empty($ldat["no_image_tpl"]) ) ? $ldat["no_image_tpl"] : '',
						"has_comment_tpl" => ( !empty($ldat["has_comment_tpl"]) ) ? $ldat["has_comment_tpl"] : '',
						"no_comment_tpl" => ( !empty($ldat["no_comment_tpl"]) ) ? $ldat["no_comment_tpl"] : '',
						"grp_p" => $grp_p,
						"autogrp_p" => $autogrp_p
					)
				);
			}

			$this->ops[] = array(
				"op" => OP_END_BLK,
				"params" => array()
			);
		}

		$cc = false;
		if ($this->req_level == 1  && aw_ini_get("template_compiler.no_menu_area_cache") != 1 && !$this->no_use_ma_cache)
		{
			$cc = true;
			$this->ops[] = array(
				"op" => OP_AREA_CACHE_SET,
				"params" => array(
					"a_parent" => $adat["parent"],
					"level" => $level,
					"a_name" => $area
				)
			);
		}

		if (!empty($adat["grps"][$level]))
		{
			$this->ops[] = array(
				"op" => OP_GRP_END,
				"params" => array(
					"a_parent" => $adat["parent"],
					"level" => $level,
					"a_name" => $area,
					"grp_cnt" => $adat["grps"][$level],
					"tpl" => $cur_tpl_fqn,
					//"stack_pop" => 1
				)
			);
		}
		elseif (!empty($adat["autogrps"][$level]))
		{
			$this->ops[] = array(
				"op" => OP_AUTOGRP_END,
				"params" => array(
					"a_parent" => $adat["parent"],
					"level" => $level,
					"a_name" => $area,
					"autogrp_cnt" => $adat["autogrps"][$level],
					"tpl" => $cur_tpl_fqn,
					//"stack_pop" => 1
				)
			);
		}
		else
		{
			$this->ops[] = array(
				"op" => OP_LOOP_LIST_END,
				"params" => array(
					"tpl" => $cur_tpl,
					"no_pop" => $cc
				)
			);
		}

		if (!empty($ldat["has_subitems_sel_check"]))
		{
			$this->ops[] = array(
				"op" => OP_CHECK_SUBITEMS_SEL,
				"params" => array(
					"tpl" => $ldat["has_subitems_sel_check_tpl"],
					"fq_tpl" => $ldat["has_subitems_sel_check_tpl_fq"],
				)
			);
		}

		if (!empty($ldat["no_subitems_sel_check"]))
		{
			$this->ops[] = array(
				"op" => OP_CHECK_NO_SUBITEMS_SEL,
				"params" => array(
					"tpl" => $ldat["no_subitems_sel_check_tpl"],
					"fq_tpl" => $ldat["no_subitems_sel_check_tpl_fq"]
				)
			);
		}

		if ($end_block)
		{
			$this->ops[] = array(
				"op" => OP_END_BLK,
				"params" => array()
			);
		}

		/*$this->ops[] = array(
			"op" => OP_LIST_INIT_END,
			"params" => array("level" => $level)
		);*/

		if (!empty($ldat["in_logged"]))
		{
			$this->ops[] = array(
				"op" => OP_END_BLK,
				"params" => array()
			);
		}

		$this->req_level --;
	}

	function get_if_filter_from_tpl_opt($opt, $all_opts)
	{
		switch($opt)
		{
			case "BEGIN":
				return array(
					"prop" => "loop_counter",
					"value" => "0"
				);
				break;

			case "END":
				return array(
					"prop" => "loop_counter",
					"value" => "list_end"
				);
				break;

			case "SEL":
				return array(
					"prop" => "oid",
					"value" => "is_in_path"
				);
				break;

			case "SEP":
				return array(
					"prop" => "clickable",
					"value" => "0"
				);
				break;

			case "MID":
				return array(
					"prop" => "mid",
					"value" => "1"
				);
				break;

			case "NOTACT":
				return array(
					"prop" => "level_selected",
					"value" => "not_in_path",
					"no_display_item" => true
				);
 				break;

			case "SUBOBJ":
				return array(
					"prop" => "level_selected",
					"value" => "obj_not_menu",
					"no_display_item" => true
				);
 				break;

			case "FPONLY":
				return array(
					"prop" => "frontpage",
					"value" => "1"
				);
				break;

			case "PREVSEL":
				return array(
					"prop" => "prev_oid",
					"value" => "is_in_path"
				);
			default:
				break;
		}

		return false;
	}

	function _insert_obj_tree_ops($adat, $ldat, $level)
	{
		$this->ops[] = array(
			"op" => OP_IF_SUBMENUS,
			"params" => array(
				"a_parent" => $adat["parent"],
				"level" => $level
			)
		);

		$this->ops[] = array(
			"op" => OP_START_BLK,
			"params" => array()
		);

		$this->ops[] = array(
			"op" => OP_GET_OBJ_SUBMENUS,
			"params" => array(
				"a_parent" => $adat["parent"],
				"a_parent_p_fn" => ( !empty($adat["a_parent_p_fn"]) ) ? $adat["a_parent_p_fn"] : '',
				"level" => $level,
				"in_parent_tpl" => $ldat["inside_parent_menu_tpl"]
			)
		);

		$this->ops[] = array(
			"op" => OP_END_BLK,
			"params" => array()
		);

		$this->ops[] = array(
			"op" => OP_IF_OBJ_TREE,
			"params" => array(
				"a_parent" => $adat["parent"],
				"level" => $level
			)
		);

		$this->ops[] = array(
			"op" => OP_START_BLK,
			"params" => array()
		);

		$this->ops[] = array(
			"op" => OP_LIST_BEGIN,
			"params" => array(
				"a_parent" => $adat["parent"],
				"a_parent_p_fn" => ( !empty($adat["a_parent_p_fn"]) ) ? $adat["a_parent_p_fn"] : '',
				"level" => $level,
				"in_parent_tpl" => $ldat["inside_parent_menu_tpl"]
			)
		);

		$this->ops[] = array(
			"op" => OP_LIST_END,
			"params" => array()
		);

		$this->ops[] = array(
			"op" => OP_END_BLK,
			"params" => array()
		);

		$this->ops[] = array(
			"op" => OP_IF_ELSE,
			"params" => array()
		);

		$this->ops[] = array(
			"op" => OP_START_BLK,
			"params" => array()
		);

		$this->ops[] = array(
			"op" => OP_GET_OBJ_TREE_LIST,
			"params" => array(
				"a_parent" => $adat["parent"],
				"a_parent_p_fn" => isset($adat["a_parent_p_fn"]) ? $adat["a_parent_p_fn"] : NULL,
				"level" => $level,
				"in_parent_tpl" => $ldat["inside_parent_menu_tpl"]
			)
		);

		$this->ops[] = array(
			"op" => OP_END_BLK,
			"params" => array()
		);
	}

	function dbg_show_template_ops()
	{
		$tab = "&nbsp;&nbsp;&nbsp;";
		$tabbing = "";
		$level = 0;

		foreach($this->ops as $num => $op)
		{
			$tabbing = str_repeat($tab, $level);

			if (OP_END_BLK === $op["op"])
			{
				--$level;
				$tabbing = str_repeat($tab, $level);
				echo $tabbing . "<b>}</b><br>\n";
				continue;
			}

			if (OP_START_BLK === $op["op"])
			{
				echo $tabbing . "<b>{</b><br>\n";
				++$level;
				continue;
			}

			echo $tabbing . "op $num: { op => ".$this->op_lut[$op["op"]]." , params = { ";
			foreach($op["params"] as $k => $v)
			{
				echo $k ." => ".$v.",";
			}
			echo "} }<br>\n";
		}
	}

	function generate_code()
	{
		//$this->dbg_show_template_ops();
		$code = "";
		$this->brace_level = 0;
		$this->list_name_stack = array();
		foreach($this->ops as $op)
		{
			$op_name = $this->op_lut[$op["op"]];
			$gen = "_g_".$op_name;
			if (!method_exists($this, $gen))
			{
				error::raise(array(
					"id" => ERR_TPL_COMPILER,
					"msg" => sprintf(t("show_site::generate_code(): could not find generator for op %s (%s) op = %s"), $op_name, $gen, $op["op"])
				));
			}

			$code .= $this->$gen($op["params"]);
		}

		return $code;
	}

	function _gi()
	{
		return str_repeat("\t", $this->brace_level);
	}

	function _g_op_start_blk($arr)
	{
		$ret = $this->_gi()."{\n";
		$this->brace_level++;
		return $ret;
	}

	function _g_op_end_blk($arr)
	{
		$this->brace_level--;
		return $this->_gi()."}\n".$this->_gi()."\n";
	}

	function _g_op_if_visible($arr)
	{
		if ($arr["in_parent_tpl"])
		{
			if ($arr["level"] == 2)
			{
				// if the level == 2 and the tpl is in parent, then
				// it is always shown, because the previous level is 1 and that is always visible.
				// but we can't optimize this out completely, because
				// the next, deeper levels might need this info, so we just set it to true
				$ret  = $this->_gi()."if ((\$this->menu_levels_visible[".$arr["a_parent"]."][".$arr["level"]."] = true) || true)\n";
			}
			else
			// > 2
			{
				$ret  = $this->_gi()."\$path_level_cnt = \$this->_helper_get_levels_in_path_for_area(".$arr["a_parent"].");\n";
				$ret .= $this->_gi()."if ((\$this->menu_levels_visible[".$arr["a_parent"]."][".$arr["level"]."] = ((\$path_level_cnt+1 >= ".$arr["level"]." ) || (\$this->menu_levels_visible[".$arr["a_parent"]."][".($arr["level"]-1)."]))))\n";
			}
		}
		else
		{
			$ret  = $this->_gi()."\$path_level_cnt = \$this->_helper_get_levels_in_path_for_area(".$arr["a_parent"].");\n";
			$ret .= $this->_gi()."if (\$path_level_cnt+1 >= ".$arr["level"]." )\n";
		}
		return $ret;
	}

	function _g_op_show_item($arr)
	{
		// get the latest list name / o name from the stack
		$dat = end($this->list_name_stack);
		$list_name = $dat["list_name"];
		$o_name = $dat["o_name"];
		$content_name = $dat["content_name"];
		$inst_name = $dat["inst_name"];
		$fun_name = $dat["fun_name"];
		$ret = "";

                        $ret .= $this->_gi()."if (is_object(".$o_name.") && ".$o_name."->is_brother() && (!\$this->brother_level_from || \$this->brother_level_from == ".$arr["level"]."))\n";
                        $ret .= $this->_gi()."{\n";
                        $this->brace_level++;
                                $ret .= $this->_gi()."\$this->brother_level_from = ".$arr["level"].";\n";
                        $this->brace_level--;
                        $ret .= $this->_gi()."}\n";
                        $ret .= $this->_gi()."else\n";
                        $ret .= $this->_gi()."{\n";
                        $this->brace_level++;
                                $ret .= $this->_gi()."if (\$this->brother_level_from >= ".$arr["level"].")\n";
                                $ret .= $this->_gi()."{\n";
                                $this->brace_level++;
                                $ret .= $this->_gi()."\$this->brother_level_from = null;\n";
                                $this->brace_level--;
                                $ret .= $this->_gi()."}\n";
                        $this->brace_level--;
                        $ret .= $this->_gi()."}\n";

		$ret .= $this->_gi()."if (\"make_menu_item\" != " . $fun_name . ")\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;

		$ret .= $this->_gi()."\$tmp_vars_array = array(\n";
		$this->brace_level++;

		$ret .= $this->_gi()."\"id\" => ".$o_name."->id,\n";
		$ret .= $this->_gi()."\"parent\" => ".$o_name."->parent,\n";
		$ret .= $this->_gi()."\"comment\" => ".$o_name."->comment,\n";
		$ret .= $this->_gi()."\"alias\" => ".$o_name."->alias,\n";
		$ret .= $this->_gi()."\"target_prop\" => ".$o_name."->target ? 1 : 0,\n";
		$ret .= $this->_gi()."\"link_prop\" => ".$o_name."->link,\n";
		$ret .= $this->_gi()."\"users_only\" => ".$o_name."->users_only ? 1 : 0,\n";
		$ret .= $this->_gi()."\"ord\" => ".$o_name."->ord(),\n";
		
		if (aw_ini_get("user_interface.content_trans") == 1)
		{
			$ret .= $this->_gi()."\"text\" => ".$o_name."->trans_get_val(\"name\"),\n";
		}
		else
		{
			$ret .= $this->_gi()."\"text\" => ".$o_name."->name(),\n";
		}

		$ret .= $this->_gi()."\"link\" => ".$inst_name."->".$fun_name."($o_name),\n";
		$ret .= $this->_gi()."\"target\" => (".$o_name."->prop(\"target\") ? \"target=\\\"_blank\\\"\" : \"\"),\n";
		$ret .= $this->_gi()."\"section\" => ".$o_name."->".$this->id_func."(),\n";
		$ret .= $this->_gi()."\"menu_edit\" => \$this->__helper_menu_edit(".$o_name."),\n";

		if ($arr["level"] > 1)
		{
			$obj_name = "\$o_".$arr["a_parent"]."_".($arr["level"]-1);
			$ret .= $this->_gi()."\"parent_section\" => (isset({$obj_name}) and is_object({$obj_name})) ? {$obj_name}->{$this->id_func}() : {$o_name}->parent(),\n";
		}

		$ret .= $this->_gi()."\"colour\" => ".$o_name."->prop(\"color\"),\n";
		$ret .= $this->_gi()."\"comment\" => ".$o_name."->trans_get_val(\"comment\"),\n";
		$this->brace_level--;
		$ret .= $this->_gi().");\n";

		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi()."\n";
		$ret .= $this->_gi()."\n";

		$ret .= $this->_gi()."if(\$parent_obj->prop(\"submenus_from_cb\")) \$tmp_vars_array = \$tmp_vars".$arr["level"]."; \$this->vars(\$tmp_vars_array);\n\n";

		if ($arr["has_image_tpl"] || $arr["no_image_tpl"])
		{
			$ret .= $this->_gi()."\$has_images = false;\n";
		}

		// do menu images
		$n_img = aw_ini_get("menu.num_menu_images");

		$ret .= $this->_gi()."if (empty(\$mmi_cnt) && acl_base::can(\"view\", ".$o_name."->prop(\"images_from_menu\")))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$tmp = obj(".$o_name."->prop(\"images_from_menu\"));\n";
		$ret .= $this->_gi()."\$img = \$tmp->meta(\"menu_images\");\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi()."elseif (empty(\$mmi_cnt))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$img = ".$o_name."->meta(\"menu_images\");\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$ret .= $this->_gi()."if (is_array(\$img) && count(\$img) > 0)\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;

		$ret .= $this->_gi()."for(\$i = 0; \$i < $n_img; \$i++)\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;

		$ret .= $this->_gi()."if (!empty(\$img[\$i][\"image_id\"]))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;

		$ret .= $this->_gi()."\$img[\$i][\"url\"] = \$this->image->get_url_by_id(\$img[\$i][\"image_id\"]);\n";

		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$ret .= $this->_gi()."if (!empty(\$img[\$i][\"url\"]))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$imgurl = image::check_url(\$img[\$i][\"url\"]);\n";
		$ret .= $this->_gi()."\$this->vars(array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"menu_image_\".\$i => \"<img src='\".\$imgurl.\"'>\",\n";
		$ret .= $this->_gi()."\"menu_image_\".\$i.\"_url\" => \$imgurl\n";
		$this->brace_level--;
		$ret .= $this->_gi()."));\n";
		if ($arr["has_image_tpl"] || $arr["no_image_tpl"])
		{
			$ret .= $this->_gi()."\$has_images = true;\n";
		}

		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		if ($arr["has_image_tpl"])
		{
			$ret .= $this->_gi()."if (\$has_images)\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$this->vars(array(\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\"HAS_IMAGE\" => \$this->parse(\"".$arr["tpl"].".HAS_IMAGE\")\n";
			$this->brace_level--;
			$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."else\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$this->vars(array(\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\"HAS_IMAGE\" => \"\"\n";
			$this->brace_level--;
			$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
		}

		if ($arr["no_image_tpl"])
		{
			$ret .= $this->_gi()."if (!\$has_images)\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$this->vars(array(\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\"NO_IMAGE\" => \$this->parse(\"".$arr["tpl"].".NO_IMAGE\")\n";
			$this->brace_level--;
			$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."else\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$this->vars(array(\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\"NO_IMAGE\" => \"\"\n";
			$this->brace_level--;
			$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
		}


		if ($arr["has_comment_tpl"])
		{
			$ret .= $this->_gi()."if (empty(\$mmi_cnt) && ".$o_name."->comment() != \"\")\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$this->vars(array(\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\"HAS_COMMENT\" => \$this->parse(\"".$arr["tpl"].".HAS_COMMENT\")\n";
			$this->brace_level--;
			$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."else\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$this->vars(array(\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\"HAS_COMMENT\" => \"\"\n";
			$this->brace_level--;
			$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
		}

		if ($arr["no_comment_tpl"])
		{
			$ret .= $this->_gi()."if (empty(\$mmi_cnt) && ".$o_name."->comment() == \"\")\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$this->vars(array(\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\"NO_COMMENT\" => \$this->parse(\"".$arr["tpl"].".NO_COMMENT\")\n";
			$this->brace_level--;
			$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."else\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$this->vars(array(\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\"NO_COMMENT\" => \"\"\n";
			$this->brace_level--;
			$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
		}

		if (aw_ini_get("menus.mark_newer_than") > 0)
		{
			$ret .= $this->_gi()."\$tmp1 = \$tmp2 = \"\";\n";
			$ret .= $this->_gi()."if (".$o_name."->modified() > (time() - ".aw_ini_get("menus.mark_newer_than")."))\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$tmp1 = \$this->parse(\"".$arr["tpl"].".IS_NEW\");\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."else\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$tmp2 = \$this->parse(\"".$arr["tpl"].".NOT_NEW\");\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."\$this->vars(array(\"IS_NEW\" => \$tmp1, \"NOT_NEW\" => \$tmp2));\n";
		}

		return $ret;
	}

	function _g_op_show_item_insert($arr)
	{
		$dat = end($this->list_name_stack);
		$content_name = $dat["content_name"];
		$o_name = $dat["o_name"];
		$ret = "";
		// TODO: this could be optimized out for non - login menus
		if (aw_ini_get("menuedit.no_show_users_only"))
		{
			$ret .= $this->_gi()."if (!(\$this->skip || (".$o_name."->prop(\"users_only\") && aw_global_get(\"uid\") == \"\")))\n";
		}
		else
		{
			$ret .= $this->_gi()."if (!\$this->skip)\n";
		}
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi().$content_name." .= \$this->parse(\"".$arr["tpl"]."\");\n";

		if ($arr["grp_p"])
		{
			$arr["grp_p"]["no_pop"] = 1;
			$ret .= $this->_g_op_grp_end($arr["grp_p"]);
		}
		elseif ($arr["autogrp_p"])
		{
			$arr["autogrp_p"]["no_pop"] = 1;
			$ret .= $this->_g_op_autogrp_end($arr["autogrp_p"]);
		}
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		return $ret;
	}

	function _g_op_list_begin($arr)
	{
		$dat = end($this->list_name_stack);
		$list_name = $dat["list_name"];
		$ret = "";

		$ret  .= $this->_gi()."\$__list_filter = array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."new obj_predicate_acl(),\n"; // add authorization filter to exclude menu items not accessible to current user
		$ret .= $this->_gi()."\"parent\" => \$parent_obj->".($this->id_func === "id" ? "brother_of" : $this->id_func)."(),\n";
		$ret .= $this->_gi()."\"class_id\" => array(CL_MENU,CL_BROTHER),\n";
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$ret .= $this->_gi()."\"status\" => array(STAT_ACTIVE, STAT_NOTACTIVE),\n";
		}
		else
		{
			$ret .= $this->_gi()."\"status\" => STAT_ACTIVE,\n";
			$ret .= $this->_gi()."\$parent_obj->prop(\"content_all_langs\") ? null : new object_list_filter(array(\n";

			$this->brace_level++;
			$ret .= $this->_gi()."\"logic\" => \"OR\",\n";
			$ret .= $this->_gi()."\"conditions\" => array(\n";

			$this->brace_level++;
			$ret .= $this->_gi()."\"lang_id\" => AW_REQUEST_CT_LANG_ID,\n";
			$ret .= $this->_gi()."\"type\" => array(MN_CLIENT,MN_PMETHOD),\n";
			$this->brace_level--;

			$ret .= $this->_gi().")\n";

			$this->brace_level--;
			$ret .= $this->_gi().")),\n";
		}

		$ret .= $this->_gi()."\"sort_by\" => (\$parent_obj->prop(\"sort_by_name\") ? \"objects.name\" : \"objects.jrk,objects.created\"),\n";
		if (!aw_ini_get("menuedit.objects_from_other_sites"))
		{
			$ret .= $this->_gi()."\"site_id\" => aw_ini_get(\"site_id\"),\n";
		}
		return $ret;
	}

	function _g_op_list_filter($arr)
	{
		return $this->_gi()."\"".$arr["key"]."\" => ".$arr["value"].",\n";
	}

	function _g_op_list_end($arr)
	{
		$dat = end($this->list_name_stack);
		$list_name = $dat["list_name"];
		$inst_name = $dat["inst_name"];
		$fun_name = $dat["fun_name"];

		$ret = "";
		$this->brace_level--;
		$ret .= $this->_gi().");\n";


		if (aw_ini_get("menuedit.no_show_users_only"))
		{
			$ret .= $this->_gi()."if (aw_global_get(\"uid\") == \"\")\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$__list_filter[\"users_only\"] = 0;\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
		}

		$ret .= $this->_gi()."$list_name = new object_list(\$__list_filter);\n";

		$ret .= $this->_gi()."$inst_name = \$this;\n";
		$ret .= $this->_gi()."$fun_name = \"make_menu_link\";\n";
		return $ret;
	}

	function _g_op_loop_list_begin($arr)
	{
		// get the latest list name / o name from the stack
		$dat = end($this->list_name_stack);
		$list_name = $dat["list_name"];
		$o_name = $dat["o_name"];
		$fun_name = $dat["fun_name"];
		$inst_name = $dat["inst_name"];
		$content_name = $dat["content_name"];
		$loop_counter_name = $dat["loop_counter_name"];
		$parent_is_from_obj_name = $dat["parent_is_from_obj_name"];
		$this_is_from_obj_name = "\$p_is_o_".$arr["a_parent"]."_".$arr["level"];
		$act_count_name = "\$o_act_count_".$arr["a_parent"]."_".$arr["level"];

		$ret = "";
		$ret .= $this->_gi().$content_name." = \"\";\n";
		$ret .= $this->_gi()."\$mmi_cnt = 0; if(empty(\$cnt_menus))\$cnt_menus = 0;\n";
		$ret .= $this->_gi().$act_count_name." = \$this->_helper_get_act_count(".$list_name.");\n";
		$ret .= $this->_gi()."for(\n((\"make_menu_item\" == " . $fun_name . ") ? (\$mmi_cnt = 1) : (".$o_name." = ".$list_name."->begin())), ((\"make_menu_item\" == " . $fun_name . ") ? ((".$fun_name."_cb)?(\$tmp_vars_array = " . $inst_name . "->make_menu_item_from_tabs(\$tmp,".$arr["level"].",\$parent_obj, \$this,\$cnt_menus)):\$tmp_vars_array = " . $inst_name . "->make_menu_item(\$tmp,".$arr["level"].",\$parent_obj, \$this,\$cnt_menus))  : (null)),".$loop_counter_name." = 0, ((\"make_menu_item\" == " . $fun_name . ") ? \$mmi_cnt : (\$prev_obj = NULL));\n ((\"make_menu_item\" == " . $fun_name . ") ? is_array(\$tmp_vars_array) : (!".$list_name."->end()));\n ((\"make_menu_item\" == " . $fun_name . ") ? ((".$fun_name."_cb)? \$tmp_vars_array = " . $inst_name . "->make_menu_item_from_tabs(\$tmp,".$arr["level"].",\$parent_obj, \$this,\$cnt_menus):\$tmp_vars_array = " . $inst_name . "->make_menu_item(\$tmp,".$arr["level"].",\$parent_obj, \$this,\$cnt_menus)) : (\$prev_obj = ".$o_name.")), ((\"make_menu_item\" == " . $fun_name . ") ? \$mmi_cnt : (".$o_name." = ".$list_name."->next())), ".$loop_counter_name."++\n)\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;

			$ret .= $this->_gi()."if(!empty(".$fun_name."_cb))\$cnt_menus++;\n";
			$ret .= $this->_gi()."\$tmp_vars".$arr["level"]." = isset(\$tmp_vars_array) ? \$tmp_vars_array : array();\n";
			$ret .= $this->_gi()."if (empty(\$mmi_cnt))\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;

			$ret .= $this->_gi()."\$this->_cur_menu_path[] = ".$o_name."->id();\n";

			if (aw_ini_get("user_interface.hide_untranslated"))
			{
				$ret .= $this->_gi()."if (!".$o_name."->prop_is_translated(\"name\"))\n";
				$ret .= $this->_gi()."{\n";
				$this->brace_level++;
				$ret .= $this->_gi()."continue;\n";
				$this->brace_level--;
				$ret .= $this->_gi()."}\n";
			}

			if (aw_ini_get("user_interface.full_content_trans"))
			{
				$ret .= $this->_gi()."if (!(".$o_name."->status() == STAT_ACTIVE || (".$o_name."->lang_id() != AW_REQUEST_CT_LANG_ID && ".$o_name."->meta(\"trans_\".AW_REQUEST_CT_LANG_ID.\"_status\"))))\n";
			//	$ret .= $this->_gi()."if ((aw_ini_get(\"languages.default\") == AW_REQUEST_CT_LANG_ID && ".$o_name."->status() != STAT_ACTIVE) || (aw_ini_get(\"languages.default\") != AW_REQUEST_CT_LANG_ID && !".$o_name."->meta(\"trans_\".AW_REQUEST_CT_LANG_ID.\"_status\")))\n";
				$ret .= $this->_gi()."{\n";
				$this->brace_level++;
					$ret .= $this->_gi()."continue;\n";
				$this->brace_level--;
				$ret .= $this->_gi()."}\n";
			}

			$ret .= $this->_gi()."if (isset({$o_name}) && is_object({$o_name}) && {$o_name}->is_brother() && (!\$this->brother_level_from || \$this->brother_level_from == ".$arr["level"]."))\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
				$ret .= $this->_gi()."\$this->brother_level_from = ".$arr["level"].";\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."else\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
				$ret .= $this->_gi()."if (\$this->brother_level_from == ".$arr["level"].")\n";
				$ret .= $this->_gi()."{\n";
				$this->brace_level++;
					$ret .= $this->_gi()."\$this->brother_level_from = null;\n";
				$this->brace_level--;
				$ret .= $this->_gi()."}\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."if (!empty({$this_is_from_obj_name}[{$o_name}->parent()]))\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."{$this_is_from_obj_name}[{$o_name}->id()] = {$this_is_from_obj_name}[{$o_name}->parent()];\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";


			// add hide_noact check
			$ret .= $this->_gi()."if (".$o_name."->prop(\"hide_noact\") == 1)\n";
			$ret .= $this->_gi()."{\n";
				$this->brace_level++;
				$ret .= $this->_gi()."if (aw_global_get(\"act_per_id\") > 1)\n";
				$ret .= $this->_gi()."{\n";
					$this->brace_level++;
					$ret .= $this->_gi()."\$_tmp = ".$o_name."->meta(\"active_documents_p\");\n";
					$ret .= $this->_gi()."if (!is_array(\$_tmp[aw_global_get(\"act_per_id\")]) || count(\$_tmp[aw_global_get(\"act_per_id\")]) < 1)\n";
					$ret .= $this->_gi()."{\n";
						$this->brace_level++;
						$ret .= $this->_gi()."continue;\n";
						$this->brace_level--;
					$ret .= $this->_gi()."}\n";
					$this->brace_level--;
				$ret .= $this->_gi()."}\n";

		$ret .= $this->_gi()."else\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;

		$ret .= $this->_gi()."\$_tmp = ".$o_name."->meta(\"active_documents\");\n";
		$ret .= $this->_gi()."if (!is_array(\$_tmp) || count(\$_tmp) < 1)\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."continue;\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		// if nextprev, then this be the place
		if ($arr["has_prev_link"])
		{
			$ret .= $this->_gi()."if (\$prev_obj && aw_global_get(\"section\") == ".$o_name."->id())\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
				$ret .= $this->_gi()."\$this->vars(array(\n";
				$this->brace_level++;
					$ret .= $this->_gi()."\"link\" => \$this->make_menu_link(\$prev_obj)\n";
				$this->brace_level--;
				$ret .= $this->_gi()."));\n";

				$ret .= $this->_gi()."\$this->vars(array(\n";
				$this->brace_level++;
					$ret .= $this->_gi()."\"PREV_LINK_".$arr["a_name"]."_L".$arr["level"]."\" => \$this->parse(\"PREV_LINK_".$arr["a_name"]."_L".$arr["level"]."\")\n";
				$this->brace_level--;
				$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
		}

		if ($arr["has_next_link"])
		{
			$ret .= $this->_gi()."if ((\$prev_obj && aw_global_get(\"section\") == \$prev_obj->id()) || (!\$prev_obj && aw_global_get(\"section\") == ".$o_name."->parent()))\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
				$ret .= $this->_gi()."\$this->vars(array(\n";
				$this->brace_level++;
					$ret .= $this->_gi()."\"link\" => \$this->make_menu_link(".$o_name.")\n";
				$this->brace_level--;
				$ret .= $this->_gi()."));\n";
				$ret .= $this->_gi()."\$this->vars(array(\n";
				$this->brace_level++;
					$ret .= $this->_gi()."\"NEXT_LINK_".$arr["a_name"]."_L".$arr["level"]."\" => \$this->parse(\"NEXT_LINK_".$arr["a_name"]."_L".$arr["level"]."\")\n";
				$this->brace_level--;
				$ret .= $this->_gi()."));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
		}

		$this->brace_level--;
		$ret .= $this->_gi()."}\n\n";

		return $ret;
	}

	function _g_op_loop_list_end($arr)
	{
		// pop one item off the list name stack
		$dat = array_pop($this->list_name_stack);
		$content_name = $dat["content_name"];
		$this->last_list_dat = $dat;

		$ret = !$arr["no_pop"] ? $this->_gi()."array_pop(\$this->_cur_menu_path);\n" : "";

		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi()."\$this->vars_safe(array(\"".$arr["tpl"]."\" => ".$content_name."));\n";
		return $ret;
	}

	function _g_op_if_begin($arr)
	{
		$ret = $this->_gi()."if (";
		return $ret;
	}

	function _g_op_if_cond($arr)
	{
		$dat = end($this->list_name_stack);
		$o_name = $dat["o_name"];
		$loop_counter_name = $dat["loop_counter_name"];
		$list_name = $dat["list_name"];
		$act_count_name = "\$o_act_count_".$arr["a_parent"]."_".$arr["level"];

		if(!$arr)$ret = "(false)";
		if ($arr["prop"] == "level_selected")
		{
			if ($arr["value"] == "not_in_path")
			{
				$ret = "(empty(\$mmi_cnt) && (\$this->_helper_get_levels_in_path_for_area(".$arr["a_parent"].") >= ".$arr["level"].") && !\$this->_helper_is_in_path(".$o_name."->".$this->id_func."()) && \$this->_helper_is_in_path(".$o_name."->parent())) && ";
			}
			else
			if ($arr["value"] == "obj_not_menu")
			{
				$ret = "(empty(\$mmi_cnt) && (\$this->_helper_get_levels_in_path_for_area(".$arr["a_parent"].") >= ".$arr["level"].") && !\$this->_helper_is_in_path(".$o_name."->".$this->id_func."()) && \$this->_helper_is_in_path(".$o_name."->parent())) && \$this->section_obj->id() != \$this->sel_section_obj->id() && ";
			}
		}
		elseif ($arr["prop"] == "loop_counter")
		{
			if ($arr["value"] == "list_end")
			{
				//$ret = "((empty(\$mmi_cnt) && (".$loop_counter_name." == (".$list_name."->count()-1))) ||  (\$mmi_cnt && \$tmp_vars_array['is_end'])) && ";
				$ret = "((empty(\$mmi_cnt) && (".$loop_counter_name." == (".$act_count_name."-1))) ||  (\$mmi_cnt && \$tmp_vars_array['is_end'])) && ";
			}
			else
			{
				$ret = "(".$loop_counter_name." == ".$arr["value"].") && ";
			}
		}
		elseif ($arr["prop"] == "oid")
 		{
			if ($arr["value"] == "is_in_path")
			{
				$ret = "((empty(\$mmi_cnt) && \$this->_helper_is_in_path(".$o_name."->".$this->id_func."()))
				|| (\$mmi_cnt && \$tmp_vars_array['is_selected'])) && ";


//|| (\$mmi_cnt && !(".$dat["fun_name"]."_cb)) || (\$mmi_cnt && \$this->_helper_is_in_url(".$o_name."))) && ";
				//else $ret = "(empty(\$mmi_cnt) && \$this->_helper_is_in_path(".$o_name."->".$this->id_func."())) && ";
			}
			else
			{
				$ret = "(".$o_name."->".$this->id_func."() == ".$arr["value"].") && ";
			}
		}
		elseif ($arr["prop"] == "prev_oid")
		{
			if ($arr["value"] == "is_in_path")
			{
				$ret = "(\$prev_obj && \$this->_helper_is_in_path(\$prev_obj->".$this->id_func."())) && ";
			}
			else
			{
				$ret = "(\$prev_obj->".$this->id_func."() == ".$arr["value"].") && ";
			}
		}
		else
		{
			$ret = "(".$o_name."->prop(\"".$arr["prop"]."\") == \"".$arr["value"]."\") && ";
		}
		return $ret;
	}

	function _g_op_if_end($arr)
	{
		$ret = " true )\n";
		return $ret;
	}

	function _g_op_if_else($arr)
	{
		$ret = $this->_gi()."else\n";
		return $ret;
	}

	function _g_op_check_subitems_sel($arr)
	{
		$ret = "";
		if (isset($arr["a_parent"]))
		{
			$content_name = "\$content_".$arr["a_parent"]."_".$arr["level"];
		}
		else
		{
			$dat = $this->last_list_dat;
			$list_name = $dat["list_name"];
			$content_name = $dat["content_name"];
		}

		$ret .= $this->_gi()."if (".$content_name." != \"\")\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$this->vars_safe(array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"".$arr["tpl"]."\" => \$this->parse(\"".$arr["fq_tpl"]."\")\n";
		$this->brace_level--;
		$ret .= $this->_gi()."));\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$ret .= $this->_gi()."else\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$this->vars_safe(array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"".$arr["tpl"]."\" => \"\"\n";
		$this->brace_level--;
		$ret .= $this->_gi()."));\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		return $ret;
	}

	function _g_op_check_no_subitems_sel($arr)
	{
		$ret = "";
		$dat = isset($this->last_list_dat) ? $this->last_list_dat : null;
		$list_name = $dat["list_name"];
		$content_name = $dat["content_name"];
		if ($content_name == "")
		{
			// get it from the current stack
			$dat = end($this->list_name_stack);
			$list_name = $dat["list_name"];
			$content_name = $dat["content_name"];
		}

		$ret .= $this->_gi()."if (".$content_name." == \"\")\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$this->vars_safe(array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"".$arr["tpl"]."\" => \$this->parse(\"".$arr["fq_tpl"]."\")\n";
		$this->brace_level--;
		$ret .= $this->_gi()."));\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$ret .= $this->_gi()."else\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$this->vars_safe(array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"".$arr["tpl"]."\" => \"\"\n";
		$this->brace_level--;
		$ret .= $this->_gi()."));\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		return $ret;
	}

	function _g_op_area_cache_check($arr)
	{
		// assumes cache inst of $this->cache
		$content_name = "\$content_".$arr["a_parent"]."_".$arr["level"];
		$res = "";
		$res .= $this->_gi()."if ((".$content_name." = cache::file_get_pt_oid_ts(\"menu_area_cache\", aw_global_get(\"section\"), \"site_show_menu_area_cache_tpl_".$this->tplhash."_lid_\".AW_REQUEST_CT_LANG_ID.\"_section_\".aw_global_get(\"section\").\"_".$arr["a_name"]."_level_".$arr["level"]."_uid_\".aw_global_get(\"uid\").\"_period_\".aw_global_get(\"act_per_id\"),\$this->_helper_get_objlastmod())) == \"\")\n";
		$res .= $this->_gi()."{\n";
		$this->brace_level++;
		return $res;
	}

	function _g_op_area_cache_set($arr)
	{
		$dat = current($this->list_name_stack);
		$content_name = $dat["content_name"];
		$cache_name = $dat["cache_name"];

		$res = "";
		$res .= $this->_gi()."array_pop(\$this->_cur_menu_path);\n";

		$res .= $this->_gi()."if (".$cache_name.")\n";
		$res .= $this->_gi()."{\n";
		$this->brace_level++;
		$res .= $this->_gi()."cache::file_set_pt_oid(\"menu_area_cache\", aw_global_get(\"section\"), \"site_show_menu_area_cache_tpl_".$this->tplhash."_lid_\".AW_REQUEST_CT_LANG_ID.\"_section_\".aw_global_get(\"section\").\"_".$arr["a_name"]."_level_".$arr["level"]."_uid_\".aw_global_get(\"uid\").\"_period_\".aw_global_get(\"act_per_id\"), ".$content_name.");\n";
		$this->brace_level --;
		$res .= $this->_gi()."}\n";
		$this->brace_level --;
		$res .= $this->_gi()."}\n";

		return $res;
	}

	function _g_op_insert_sel_ids($arr)
	{
		$res = "";

		$dat = $arr["data"];
		foreach($dat as $area => $adat)
		{
			if (!$adat["parent"])
			{
				continue;
			}

			$this->brace_level++;

			$ares = "";

			$ni = aw_ini_get("menu.num_menu_images");
			foreach($adat["levels"] as $level => $ldat)
			{
				$has_vars = false;
				$vres = "";

				$this->brace_level++;

				$varname = "sel_menu_".$area."_L".$level."_id";
				if ($this->template_has_var_full($varname))
				{
					$vres .= $this->_gi()."\$vars[\"$varname\"] = \$tmp;\n";
					$has_vars = true;
				}

				$vres .= $this->_gi()."\$tmp_o = obj(\$tmp);\n";

				$varname = "sel_menu_".$area."_L".$level."_text";
				if ($this->template_has_var_full($varname))
				{
					if (aw_ini_get("user_interface.content_trans"))
					{
						$vres .= $this->_gi()."\$vars[\"$varname\"] = \$tmp_o->trans_get_val(\"name\");\n";
					}
					else
					{
						$vres .= $this->_gi()."\$vars[\"$varname\"] = \$tmp_o->name();\n";
					}
					$has_vars = true;
				}

				$varname = "sel_menu_".$area."_L".$level."_comment";
				if ($this->template_has_var_full($varname))
				{
					$vres .= $this->_gi()."\$vars[\"$varname\"] = \$tmp_o->comment();\n";
					$has_vars = true;
				}

				$varname = "sel_menu_".$area."_L".$level."_colour";
				if ($this->template_has_var_full($varname))
				{
					$vres .= $this->_gi()."\$vars[\"$varname\"] = \$tmp_o->prop(\"color\");\n";
					$has_vars = true;
				}

				$varname = "sel_menu_".$area."_L".$level."_url";
				if ($this->template_has_var_full($varname))
				{
					$vres .= $this->_gi()."\$vars[\"$varname\"] = \$this->make_menu_link(\$tmp_o);\n";
					$has_vars = true;
				}

				// insert image urls
				for($i = 0; $i < $ni; $i++)
				{
					$imres = "";
					$varname1 = "sel_menu_".$area."_L".$level."_image_".$i."_url";
					$varname2 = "sel_menu_".$area."_L".$level."_image_".$i;

					$has_v1 = $this->template_has_var_full($varname1);
					$has_v2 = $this->template_has_var_full($varname2);

					if ($has_v1 != false || $has_v2 != false)
					{
						$imres .= $this->_gi()."if (\$tmp_im[$i][\"image_id\"])\n";
						$imres .= $this->_gi()."{\n";
						$this->brace_level++;

						$imres .= $this->_gi()."\$tmp_im[$i][\"url\"] = \$this->image->get_url_by_id(\$tmp_im[$i][\"image_id\"]);\n";

						$this->brace_level--;
						$imres .= $this->_gi()."}\n";
						if ($has_v1)
						{
							$imres .= $this->_gi()."\$vars[\"$varname1\"] = image::check_url(\$tmp_im[".$i."][\"url\"]);\n";
							$has_vars = true;
						}
						if ($has_v2)
						{
							$imres .= $this->_gi()."\$vars[\"$varname2\"] = image::make_img_tag(image::check_url(\$tmp_im[".$i."][\"url\"]));\n";
							$has_vars = true;
						}
					}

					if ($imres != "")
					{
						$vres .= $this->_gi()."\$tmp_im = \$tmp_o->meta(\"menu_images\");\n".$imres;
					}
				}

				if ($has_vars)
				{
					$this->brace_level--;
					$ares .= $this->_gi()."\$tmp = \$this->_helper_find_parent(".$adat["parent"].", ".($level+1).");\n";
					$ares .= $this->_gi()."if (\$tmp)\n";
					$ares .= $this->_gi()."{\n";
					$ares .= $vres;
					$ares .= $this->_gi()."}\n";
				}
				else
				{
					$this->brace_level--;
				}
			}

			if ($ares != "")
			{
				$this->brace_level--;
				$res .= $this->_gi()."if (\$this->_helper_get_levels_in_path_for_area(".$adat["parent"].") > 0)\n";
				$res .= "{\n";
				$this->brace_level++;
				$res .= $this->_gi()."\$vars = array();\n";
				$res .= $ares;
				$res .= $this->_gi()."\$this->vars(\$vars);\n";
				$this->brace_level--;
				$res .= $this->_gi()."}\n";
			}
			else
			{
				$this->brace_level--;
			}
		}

		return $res;
	}

	function _g_op_if_obj_tree($arr)
	{
		$ret = "";

		$o_name = "\$o_".$arr["a_parent"]."_".$arr["level"];

		$p_v_name = "\$ot_".$arr["a_parent"]."_".($arr["level"] > 0 ? $arr["level"]-1 : $arr["level"]);

		$add = "";
		if ($arr["level"] > 0)
		{
			$add = " || !empty(".$p_v_name.")";
		}

		$ret .= $this->_gi()."else\n";
		$ret .= $this->_gi()."if (!(\$parent_obj->prop(\"show_object_tree\") $add))\n";
		return $ret;
	}

	function _g_op_get_obj_tree_list($arr)
	{
		$ret = "";
		$dat = end($this->list_name_stack);
		$list_name = $dat["list_name"];
		$inst_name = $dat["inst_name"];
		$fun_name = $dat["fun_name"];
		$cache_name = $dat["cache_name"];
		$p_v_name = "\$ot_".$arr["a_parent"]."_".$arr["level"];

		$ret .= $this->_gi()."\$o_treeview = new object_treeview();\n";
		$ret .= $this->_gi().$list_name." = \$o_treeview->get_folders_as_object_list(\$parent_obj);\n";

		$ret .= $this->_gi()."$inst_name = \$o_treeview;\n";
		$ret .= $this->_gi()."$fun_name = \"make_menu_link\";\n";
		$ret .= $this->_gi().$p_v_name." = true;\n";
		$ret .= $this->_gi().$cache_name." = false;\n";

		return $ret;
	}

	function _g_op_list_init($arr)
	{
		// we can include constants in the code, this will
		// get executed in aw ...

		// insert new list item in the list name stack

		// the object_list for this area this level
		$list_name = "\$list_".$arr["a_parent"]."_".$arr["level"];

		// the name of the list item object this area level
		$o_name = "\$o_".$arr["a_parent"]."_".$arr["level"];

		// the name of the content string for this level area
		$content_name = "\$content_".$arr["a_parent"]."_".$arr["level"];

		// the name of the loop counter for this level area
		$loop_counter_name = "\$i_".$arr["a_parent"]."_".$arr["level"];

		// the object to call make_menu_link from
		$inst_name = "\$inst_".$arr["a_parent"]."_".$arr["level"];

		// the make_menu_link function name
		$fun_name = "\$fun_".$arr["a_parent"]."_".$arr["level"];
		$fun_parent_name_cb = "\$fun_".$arr["a_parent"]."_".($arr["level"] - 1)."_cb";

		// the cache file name
		$cache_name = "\$use_cache_".$arr["a_parent"]."_".$arr["level"];

		// if the parent level menus are from another object
		$parent_is_from_obj_name = "\$p_is_o_".$arr["a_parent"]."_".($arr["level"]-1);

		// the start level of the menus-from-object for this area
		$parent_is_from_obj_start_level = "\$p_is_o_level_".$arr["a_parent"];

		array_push($this->list_name_stack, array(
			"list_name" => $list_name,
			"o_name" => $o_name,
			"content_name" => $content_name,
			"loop_counter_name" => $loop_counter_name,
			"inst_name" => $inst_name,
			"fun_name" => $fun_name,
			"cache_name" => $cache_name,
			"parent_is_from_obj_name" => $parent_is_from_obj_name,
			"parent_is_from_obj_start_level" => $parent_is_from_obj_start_level
		));


		$ret = "";

		// also set the area as visible, because if we get here in execution, it is visible.
		$ret .= $this->_gi()."\$this->menu_levels_visible[".$arr["a_parent"]."][".$arr["level"]."] = 1;\n";
		if ($arr["level"] == 1)
		{
			$ret .= $this->_gi()."if (acl_base::can(\"view\", ".$arr["a_parent_p_fn"]."))\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$parent_obj = new object(".$arr["a_parent_p_fn"].");\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."else\n";
			$ret .= $this->_gi()."if (acl_base::can(\"view\", aw_ini_get(\"rootmenu\")))\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$parent_obj = new object(aw_ini_get(\"rootmenu\"));\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
			$ret .= $this->_gi()."else\n";
			$ret .= $this->_gi()."{\n";
			$this->brace_level++;
			$ret .= $this->_gi()."\$parent_obj = new object();\n";
			$this->brace_level--;
			$ret .= $this->_gi()."}\n";
		}
		else
		{
			// here find_parent will fail for menus that are shown even if they are not in the path
			// BUT! we don't need to get their id from the path anyway,
			// because we are inside a loop that has the parent object as the current object!
			// so, we just get it from that!
			if ($arr["in_parent_tpl"])
			{
				$parent_o_name = "\$o_".$arr["a_parent"]."_".($arr["level"]-1);
				$ret .= $this->_gi()."if(empty(".$parent_is_from_obj_name."[\$parent_obj->id()]) &&  empty(".$fun_parent_name_cb."))\$parent_obj = ".$parent_o_name.";\n";
			}
			else
			{
				$ret .= $this->_gi()."if (acl_base::can(\"view\", \$this->_helper_find_parent(".$arr["a_parent"].",".$arr["level"].")))\n";
				$ret .= $this->_gi()."{\n";
				$this->brace_level++;

				$ret .= $this->_gi()."\$parent_obj = new object(\$this->_helper_find_parent(".$arr["a_parent"].",".$arr["level"]."));\n";
				$this->brace_level--;
				$ret .= $this->_gi()."}\n";
				$ret .= $this->_gi()."else\n";
				$ret .= $this->_gi()."{\n";
				$this->brace_level++;
				$ret .= $this->_gi()."\$parent_obj = new object(aw_ini_get(\"rootmenu\"));\n";
				$this->brace_level--;
				$ret .= $this->_gi()."}\n";
			}
		}

		$ret .= $this->_gi().$cache_name." = true;\n";
		$ret .= $this->_gi()."\$p_is_o_".$arr["a_parent"]."_".($arr["level"])."[\$parent_obj->id()] = NULL;\n";
		return $ret;
	}

	function _g_op_has_lugu($arr)
	{
		$dat = end($this->list_name_stack);
		$o_name = $dat["o_name"];

		$ret = "";
		$ret .= $this->_gi()."\$has_lugu = \"\";\n";
		$ret .= $this->_gi()."if (".$o_name."->meta(\"show_lead\") && (!aw_ini_get(\"menuedit.show_lead_in_menu_only_active\") || \$this->_helper_is_in_path(".$o_name."->".$this->id_func."())))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
	$ret .= $this->_gi()."\$xfilt = array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"parent\" => ".$o_name."->".$this->id_func."(),\n";
		$ret .= $this->_gi()."\"status\" => STAT_ACTIVE,\n";
		$ret .= $this->_gi()."\"period\" => aw_global_get(\"act_per_id\"),\n";
		$ret .= $this->_gi()."\"class_id\" => array(CL_PERIODIC_SECTION, CL_DOCUMENT),\n";
		$ret .= $this->_gi()."\"sort_by\" => \$this->get_folder_document_sort_by(".$o_name."),\n";
		$ret .= $this->_gi()."\"limit\" => (int)aw_ini_get(\"menuedit.show_lead_in_menu_count\")\n";
		$this->brace_level--;
		$ret .= $this->_gi().");\n";
		$ret .= $this->_gi()."if (".$o_name."->prop(\"all_pers\"))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."unset(\$xfilt[\"period\"]);\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi()."\$xdat = new object_list(\$xfilt);\n";
		$ret .= $this->_gi()."for(\$o = \$xdat->begin(); !\$xdat->end(); \$o = \$xdat->next())\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$__tmp_tpl = \"nadal_film_side_lead.tpl\";\n";
		$ret .= $this->_gi()."if (".$o_name."->prop(\"show_lead_template\"))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$tmp_o = obj(".$o_name."->prop(\"show_lead_template\"));\n";
		$ret .= $this->_gi()."\$__tmp_tpl = \$tmp_o->prop(\"filename\");\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$ret .= $this->_gi()."\$done = \$this->doc->gen_preview(array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"docid\" => \$o->".$this->id_func."(), \n";
		$ret .= $this->_gi()."\"tpl\" => \$__tmp_tpl,\n";
		$ret .= $this->_gi()."\"leadonly\" => 1, \n";
		$ret .= $this->_gi()."\"section\" => ".$o_name."->".$this->id_func."(),\n";
		$ret .= $this->_gi()."\"strip_img\" => 0\n";
		$this->brace_level--;
		$ret .= $this->_gi()."));\n";
		$ret .= $this->_gi()."\$this->vars(array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"lugu\" => \$done\n";
		$this->brace_level--;
		$ret .= $this->_gi()."));\n";
		$ret .= $this->_gi()."\$has_lugu .= \$this->parse(\"HAS_LUGU\");\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi()."\$this->vars(array(\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\"HAS_LUGU\" => \$has_lugu\n";
		$this->brace_level--;
		$ret .= $this->_gi()."));\n";

		return $ret;
	}

	function _g_op_if_submenus($arr)
	{
		$ret = "";
		$dat = end($this->list_name_stack);
		$inst_name = $dat["inst_name"];
		$fun_name = $dat["fun_name"];


		$parent_is_from_obj_name = $dat["parent_is_from_obj_name"];

		$ret .= $this->_gi()."if (acl_base::can(\"view\", \$parent_obj->prop(\"submenus_from_menu\")))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$parent_obj = obj(\$parent_obj->prop(\"submenus_from_menu\"));\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";



		$ret .= $this->_gi()."if (\$parent_obj->prop(\"submenus_from_cb\"))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."$inst_name = \$parent_obj->instance();\n";
		$ret .= $this->_gi().$fun_name."_cb = 1;\n";
		$ret .= $this->_gi()."$fun_name = \"make_menu_item\";\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$ret .= $this->_gi()."elseif (acl_base::can(\"view\", \$parent_obj->prop(\"submenus_from_obj\")) || !empty(".$parent_is_from_obj_name."[\$parent_obj->id()]))\n";

		return $ret;
	}

	function _g_op_get_obj_submenus($arr)
	{
		$dat = end($this->list_name_stack);
		$list_name = $dat["list_name"];
		$inst_name = $dat["inst_name"];
		$fun_name = $dat["fun_name"];
		$cache_name = $dat["cache_name"];
		$p_v_name = "\$os_".$arr["a_parent"]."_".$arr["level"];
		$prev_o_name = "\$o_".$arr["a_parent"]."_".($arr["level"]-1);
		$this_is_from_obj_name = "\$p_is_o_".$arr["a_parent"]."_".$arr["level"]."[\$parent_obj->id()]";
		$parent_is_from_obj_name = $dat["parent_is_from_obj_name"];
		$parent_is_from_obj_start_level = $dat["parent_is_from_obj_start_level"];
		$ret = "";

		$ret .= $this->_gi()."if (!empty(".$parent_is_from_obj_name."[\$parent_obj->id()]))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$tmp = ".$parent_is_from_obj_name."[\$parent_obj->id()];\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi()."else\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."if (acl_base::can(\"view\", \$parent_obj->prop(\"submenus_from_obj\")))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."\$tmp = obj(\$parent_obj->prop(\"submenus_from_obj\"));\n";
		$ret .= $this->_gi().$parent_is_from_obj_start_level." = ".$arr["level"].";\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi().$this_is_from_obj_name." = \$tmp;\n";
		$ret .= $this->_gi()."\$o_obj_from = get_instance(\$tmp->class_id());\n\n";

		$ret .= $this->_gi()."if (method_exists(\$o_obj_from, \"make_menu_link\"))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."$inst_name = \$o_obj_from;\n";
		$ret .= $this->_gi()."$fun_name = \"make_menu_link\";\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi()."else\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."$inst_name = \$this;\n";
		$ret .= $this->_gi()."$fun_name = \"make_menu_link\";\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n\n";

		$ret .= $this->_gi()."if (method_exists(\$o_obj_from, \"make_menu_item\"))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."$inst_name = \$o_obj_from;\n";
		$ret .= $this->_gi()."$fun_name = \"make_menu_item\";\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";
		$ret .= $this->_gi()."else\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi().$list_name." = \$o_obj_from->get_folders_as_object_list(\$tmp,".$arr["level"]." - ".$parent_is_from_obj_start_level.", (isset({$prev_o_name}) ? {$prev_o_name} : null));\n"; //\$parent_obj);\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n\n";

		$ret .= $this->_gi()."if (\$parent_obj->prop(\"submenus_from_cb\"))\n";
		$ret .= $this->_gi()."{\n";
		$this->brace_level++;
		$ret .= $this->_gi()."$inst_name = \$parent_obj->instance();\n";
		$ret .= $this->_gi().$fun_name."_cb = 1;\n";
		$ret .= $this->_gi()."$fun_name = \"make_menu_item\";\n";
		$this->brace_level--;
		$ret .= $this->_gi()."}\n";

		$ret .= $this->_gi().$p_v_name." = true;\n";
		$ret .= $this->_gi().$cache_name." = false;\n";

		return $ret;
	}

	function _g_op_if_logged($arr)
	{
		$ret = "";

		$ret .= $this->_gi()."if (aw_global_get(\"uid\") != \"\")\n";
		return $ret;
	}

	function _g_op_list_init_end($arr)
	{
		if ($arr["level"] == 1)
		{
			$this->brace_level--;
			return $this->_gi()."}\n";
		}
	}

	function _g_op_grp_begin($arr)
	{
		$res = "";

		$grp_ct_name = "\$grp_ct_".$arr["a_parent"]."_".$arr["level"];

		$res .= $this->_gi()."$grp_ct_name = \"\";\n";

/*
		$gn = "\$grp_".$arr["a_name"]."_".$arr["level"];
		$g_txt = "\$grp_".$arr["a_name"]."_".$arr["level"]."_ct";

		$res .= $this->_gi()."$g_txt = \"\";\n";
		$res .= $this->_gi()."for($gn = 0; $gn < $arr[grp_cnt]; $gn++)\n";
		$res .= $this->_gi()."{\n";
		$this->brace_level++;
	*/
		return $res;
	}

	function _g_op_grp_end($arr)
	{
		$res = "";
		$dat = end($this->list_name_stack);
		$content_name = $dat["content_name"];
		$o_name = $dat["o_name"];
		$loop_counter_name = $dat["loop_counter_name"];
		$list_name = $dat["list_name"];

		$grp_ct_name = "\$grp_ct_".$arr["a_parent"]."_".$arr["level"];
		$grp_tpl = "MENU_".$arr["a_name"]."_L".$arr["level"]."_GRP_".$arr["grp_cnt"];

		$tpl = substr($arr["tpl"], strrpos($arr["tpl"], ".")+1);

		// if % count
		$res .= $this->_gi()."if (($loop_counter_name > 0 && ($loop_counter_name % $arr[grp_cnt]) == ".($arr["grp_cnt"] - 1).") || ($loop_counter_name == (".$list_name."->count() - 1)) )\n";
		$res .= $this->_gi()."{\n";
		$this->brace_level++;

			$res .= $this->_gi()."\$this->vars_safe(array(\n";
			$this->brace_level++;

				$res .= $this->_gi()."\"".$tpl."\" => $content_name,\n";
				$res .= $this->_gi()."\"".(substr($tpl, -4) == "_SEL" ? substr($tpl, 0, -4) : $tpl."_SEL")."\" => \"\"\n";

			$this->brace_level--;
			$res .= $this->_gi()."));\n";

			$res .= $this->_gi()."$content_name = \"\";\n";
			$res .= $this->_gi()."$grp_ct_name .= \$this->parse(\"".$grp_tpl."\");\n";

		$this->brace_level--;
		$res .= $this->_gi()."}\n";

		// this->vars(arr[tpl] => $content_name)
		// parse grp tpl

		// insert the same thing that op_loop_list_end does, but for groups

                if ($arr["stack_pop"])                                                                                                      {
                        $res .= $this->_gi()."array_pop(\$this->_cur_menu_path);\n";
                }

		// pop one item off the list name stack
		if (!$arr["no_pop"])
		{
			$dat = array_pop($this->list_name_stack);
			$this->last_list_dat = $dat;


			$this->brace_level--;
			$res .= $this->_gi()."}\n";
			$res .= $this->_gi()."\$this->vars_safe(array(\"".$grp_tpl."\" => ".$grp_ct_name."));\n";
		}
		return $res;
	}

	function _g_op_autogrp_begin($arr)
	{
		$autogrp_ct_name = "\$autogrp_ct_".$arr["a_parent"]."_".$arr["level"];
		return $this->_gi()."$autogrp_ct_name = \"\";\n";
	}

	function _g_op_autogrp_end($arr)
	{
		$res = "";
		$dat = end($this->list_name_stack);
		$content_name = $dat["content_name"];
		$o_name = $dat["o_name"];
		$loop_counter_name = $dat["loop_counter_name"];
		$list_name = $dat["list_name"];

		$autogrp_ct_name = "\$autogrp_ct_".$arr["a_parent"]."_".$arr["level"];
		$autogrp_tpl = "MENU_".$arr["a_name"]."_L".$arr["level"]."_AUTOGRP_".$arr["autogrp_cnt"];

		$tpl = substr($arr["tpl"], strrpos($arr["tpl"], ".")+1);

		// if % count
		$backtrace = debug_backtrace();
		if($backtrace[1]["function"] != "generate_code")
		{
			$res .= $this->_gi()."if (($loop_counter_name + 1) % ceil(".$list_name."->count() / $arr[autogrp_cnt]) == 0 || $loop_counter_name == ".$list_name."->count() - 1)\n";
			$res .= $this->_gi()."{\n";
			$this->brace_level++;

				$res .= $this->_gi()."\$this->vars_safe(array(\n";
				$this->brace_level++;

					$res .= $this->_gi()."\"".$tpl."\" => $content_name,\n";
					$res .= $this->_gi()."\"".(substr($tpl, -4) == "_SEL" ? substr($tpl, 0, -4) : $tpl."_SEL")."\" => \"\"\n";

				$this->brace_level--;
				$res .= $this->_gi()."));\n";

				$res .= $this->_gi()."$content_name = \"\";\n";
				$res .= $this->_gi()."$autogrp_ct_name .= \$this->parse(\"".$autogrp_tpl."\");\n";

			$this->brace_level--;
			$res .= $this->_gi()."}\n";
		}

		// this->vars(arr[tpl] => $content_name)
		// parse autogrp tpl

		// insert the same thing that op_loop_list_end does, but for groups

                if ($arr["stack_pop"])                                                                                                      {
                        $res .= $this->_gi()."array_pop(\$this->_cur_menu_path);\n";
                }

		// pop one item off the list name stack
		if (!$arr["no_pop"])
		{
			$dat = array_pop($this->list_name_stack);
			$this->last_list_dat = $dat;


			$this->brace_level--;
			$res .= $this->_gi()."}\n";
			$res .= $this->_gi()."\$this->vars_safe(array(\"".$autogrp_tpl."\" => ".$autogrp_ct_name."));\n";
		}
		return $res;
	}
}
