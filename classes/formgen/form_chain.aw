<?php

namespace automatweb;

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_chain.aw,v 1.32 2008/01/31 13:54:33 kristo Exp $
// form_chain.aw - form chains
/*
@classinfo  maintainer=kristo
*/

classload("formgen/form_base");
class form_chain extends form_base
{
	const AW_CLID = 92;

	const AW_CLID = 68;

	function form_chain()
	{
		$this->form_base();
		$this->sub_merge = 1;
		lc_load("definition");
		$this->lc_load("form","lc_form");
	}

	/**  
		
		@attrib name=new params=name default="0"
		
		@param parent required acl="add"
		@param alias_doc optional
		
		@returns
		
		
		@comment

	**/
	function add($arr)
	{
		extract($arr);
		$this->mk_path($parent,LC_FORM_CHAIN_ADD_WREATH);
		$this->read_template("add_chain.tpl");

		$this->vars(array(
			"forms" => $this->multiple_option_list(array(),$this->get_list(FTYPE_ENTRY,false,true)),
			"reforb" => $this->mk_reforb("submit", array("parent" => $parent,"alias_doc" => $alias_doc)),
			"search_doc" => $this->mk_orb("search_doc", array(),"links"),
			"folders" => $this->picker(0,$this->get_menu_list())
		));
		return $this->parse();
	}

	/** Submits a new form chain 
		
		@attrib name=submit params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit($arr)
	{
		extract($arr);
		$ct = array();
		$ct["forms"] = array();
		if (is_array($forms))
		{
			foreach($forms as $fid)
			{
				$ct["forms"][$fid] = $fid;
			}
		}

		$ct["lang_form_names"] = $fname;
		$ct["form_order"] = $fjrk;
		$ct["gotonext"] = $fgoto;

		$ct["fillonce"] = $fillonce;

		$ct["after_show_entry"] = $after_show_entry;
		$ct["after_show_op"] = $after_show_op;

		$ct["during_show_entry"] = $during_show_entry;
		$ct["during_show_op"] = $during_show_op;
		$ct["op_pos"] = $op_pos;
		$ct["rep"] = $rep;

		$ct["after_redirect"] = $after_redirect;
		$ct["after_redirect_url"] = $after_redirect_url;

		$ct["save_folder"] = $save_folder;
		
		$ct["show_reps"] = $show_reps;
		$ct["rep_tbls"] = $rep_tbls;
		$ct["rep_ops"] = $rep_ops;

		$ct["cal_controller"] = $cal_controller;
		$ct["no_load"] = $no_load;
		$ct["controllers"] = $controllers;
		
		$this->chain = $ct;

		uksort($ct["forms"],array($this,"__ch_sort"));
		
		$content = aw_serialize($ct,SERIALIZE_PHP);
		$this->quote(&$content);

		$flags = ($has_calendar) ? OBJ_HAS_CALENDAR : 0;
	
		if ($id)
		{
			$o = obj($id);
			$o->set_name($name);
			$o->set_comment($comment);
			$o->set_flags($flags);
			$o->save();
			$this->db_query("UPDATE form_chains SET content = '$content' WHERE id = $id");

		}
		else
		{
			$o = obj();
			$o->set_parent($parent);
			$o->set_name($name);
			$o->set_comment($comment);
			$o->set_status(STAT_ACTIVE);
			$o->set_class_id(CL_FORM_CHAIN);
			$o->set_flags($flags);
			$id = $o->save();
			$this->db_query("INSERT INTO form_chains(id,content) VALUES($id,'$content')");
			if ($alias_doc)
			{
				$o = obj($alias_doc);
				$o->connect(array(
					"to" => $id
				));
			}

		}

		$this->db_query("DELETE FROM form2chain WHERE chain_id = $id");
		if (is_array($forms))
		{
			foreach($forms as $fid)
			{
				$this->db_query("INSERT INTO form2chain(form_id,chain_id,ord,rep) values($fid,$id,'".$ct["form_order"][$fid]."','".$rep[$fid]."')");
			}
		}
		return $this->mk_my_orb("change", array("id" => $id));
	}

	function __ch_sort($a,$b)
	{
		if ($this->chain["form_order"][$a] < $this->chain["form_order"][$b])
		{
			return -1;
		}
		else
		if ($this->chain["form_order"][$a] > $this->chain["form_order"][$b])
		{
			return 1;
		}
		return 0;
	}

	/**  
		
		@attrib name=change params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function change($arr)
	{
		extract($arr);
		$fc = $this->load_chain($id);
		$this->mk_path($fc["parent"], LC_FORM_CHAIN_CHANGE_WREATH);
		$this->read_template("add_chain.tpl");

		$la = new languages();
		$lar = $la->listall(true);


		$rl = "";
		foreach($lar as $l)
		{
			$aru = $this->chain["after_redirect_url"];
			if (is_array($aru))
			{
				$aru = $aru[$l["id"]];
			}
			$this->vars(array(
				"lang_name" => $l["name"],
				"lang_id" => $l["id"],
				"after_redirect_url" => $aru,
				"search_doc" => $this->mk_orb("search_doc", array(),"links"),
			));
			$lh.=$this->parse("LANG_H");
			$rl.=$this->parse("REDIR_LANG");
		}

		$ch_forms = array();

		if (is_array($this->chain["forms"]))
		{
			foreach($this->chain["forms"] as $fid)
			{
				$lg= "";
				foreach($lar as $l)
				{
					if (!isset($this->chain["lang_form_names"][$fid][$l["id"]]))
					{
						$fname = $this->db_fetch_field("SELECT name FROM objects WHERE oid = $fid", "name");
					}
					else
					{
						$fname = $this->chain["lang_form_names"][$fid][$l["id"]];
					}
					$this->vars(array(
						"form_id" => $fid,
						"fname" => $fname,
						"lang_id" => $l["id"]
					));
					$ch_forms[$fid] = $fname;
					$lg.=$this->parse("LANG");
				}

				$ol = new object_list(array(
					"class_id" => CL_FORM_TABLE,
					"site_id" => array(),
					"lang_id" => array()
				));
				$ft_names = $ol->names();

				$ol = new object_list(array(
					"class_id" => CL_FORM_OUTPUT,
					"site_id" => array(),
					"lang_id" => array()
				));
				$op_names = array("" => "") + $ol->names();

				$ol = new object_list(array(
					"class_id" => CL_FORM_CONTROLLER,
					"site_id" => array(),
					"lang_id" => array()
				));
				$fc_names = array("" => "") + $ol->names(array(
					"add_folders" => true
				));


				$this->vars(array(
					"fjrk" => $this->chain["form_order"][$fid],
					"fgoto" => checked($this->chain["gotonext"][$fid]),
					"rep" => checked($this->chain["rep"][$fid]),
					"show_reps" => checked($this->chain["show_reps"][$fid]),
					"rep_tbls" => $this->picker($this->chain["rep_tbls"][$fid], $ft_names),
					"rep_ops" => $this->picker($this->chain["rep_ops"][$fid], $op_names),
					"no_load" => checked($this->chain["no_load"][$fid]),
					"LANG" => $lg,
					"controllers" => $this->mpicker($this->chain["controllers"][$fid],$fc_names)
				));
				$this->parse("FORM");
			}
		}

		$forms = $this->get_flist(array(
			"type" => FTYPE_ENTRY,
		));

		$cntforms = $this->get_flist(array(
			"subtype" => FSUBTYPE_CAL_CONF,
		));

		// no form should be selected if the user has not made a choice
		// yet, so we add a default choise to the front of both lists
		$default = array("" => "-- Vali --");
		$ch_forms = $default + $ch_forms;
		//$ev_entry_forms = $default + $ev_entry_forms;

		$ol = new object_list(array(
			"class_id" => CL_FORM_OUTPUT,
			"site_id" => array(),
			"lang_id" => array()
		));
		$op_names = $ol->names();

		$this->vars(array(
			"forms" => $this->multiple_option_list($this->chain["forms"],$forms),
			"name" => $fc["name"],
			"comment" => $fc["comment"],
			"fillonce" => checked($this->chain["fillonce"]),
			"import" => $this->mk_my_orb("import_chain_entries", array("id" => $id),"form_import"),
			"entries" => $this->mk_my_orb("show_chain_entries", array("id" => $id)),
			"ops" => $this->picker($this->chain["after_show_op"], $op_names),
			"after_show_entry" => checked($this->chain["after_show_entry"] == 1),
			"during_show_entry" => checked($this->chain["during_show_entry"] == 1),
			"op_up" => checked($this->chain["op_pos"] == "up"),
			"op_down" => checked($this->chain["op_pos"] == "down"),
			"op_left" => checked($this->chain["op_pos"] == "left"),
			"op_right" => checked($this->chain["op_pos"] == "right"),
			"d_ops" => $this->picker($this->chain["during_show_op"], $op_names),
			"LANG_H" => $lh,
			"search_doc" => $this->mk_orb("search_doc", array(),"links"),
			"after_redirect" => checked($this->chain["after_redirect"] == 1),
			"folders" => $this->picker($this->chain["save_folder"],$this->get_menu_list()),
			"has_calendar" => checked($fc["flags"] && OBJ_HAS_CALENDAR),
			//"cal_forms" => $this->picker($this->chain["cal_form"],$selected_forms),
			"cal_controllers" => $this->picker($this->chain["cal_controller"],$cntforms),
			//"cal_entry_forms" => $this->picker($this->chain["cal_entry_form"],$ev_entry_forms),
			"reforb" => $this->mk_reforb("submit", array("id" => $id)),
			"REDIR_LANG" => $rl
		));
		return $this->parse();
	}

	function parse_alias($args = array())
	{
		extract($args);
		global $section;
		$ar = array("id" => $alias["target"], "section" => $section);

		if ($GLOBALS["form_id"])
		{
			$ar["form_id"] = $GLOBALS["form_id"];
		}
		if ($GLOBALS["entry_id"])
		{
			$ar["entry_id"] = $GLOBALS["entry_id"];
		}
		$ar["from_alias"] = true;
		$replacement = $this->show($ar);
		return $replacement;
	}

	function get_default_form_in_chain()
	{
		if ($this->chain["default_form"])
		{
			return $this->chain["default_form"];
		}
		if (is_array($this->chain["forms"]))
		{
			reset($this->chain["forms"]);
			list($fid,) = each($this->chain["forms"]);
		}
		return $fid;
	}

	/** shows the form chain 
		
		@attrib name=show params=name nologin="1" default="0"
		
		@param id required
		@param section optional
		@param form_id optional
		@param entry_id optional
		@param form_entry_id optional
		@param start_el optional
		@param end_el optional
		@param start optional
		@param end optional
		
		@returns
		
		
		@comment
		args:
		id - chain id
		section - the document's id in what we are
		form_id - the active form in the chain, if omitted the default is opened
		entry_id - the chain entry id

	**/
	function show($arr)
	{
		extract($arr);
		if (!$this->can("view", $form_entry_id))
		{
			$form_entry_id = NULL;
		}

		$this->start_el = $start_el;
		$this->end_el = $end_el;
		$this->start = $start;
		$this->end = $end;
		$ch = $this->load_chain($id);

      if (!$this->can("view", $form_entry_id))
		{
			$form_entry_id = NULL;
		}
		if (!$form_id)
		{
			$form_id = $this->get_default_form_in_chain();
		}

		aw_global_set("is_showing_chain", 1);

		$this->read_template("chain.tpl");

		if ($this->chain["fillonce"] && aw_global_get("uid"))
		{
			// kui seda saab aint yx kord t2ita siis yritame leida selle t2itmise
			$entry_id = $this->db_fetch_field("SELECT id FROM form_chain_entries WHERE chain_id = $id AND uid = '".aw_global_get("uid")."'","id");
		}

		$fc = get_instance(CL_FORM_CONTROLLER);
		$toshow = array();
		$all = array();
		foreach($this->chain["forms"] as $fid)
		{
			// check controllers
			$dat = new aw_array($this->chain["controllers"][$fid]);
			$show = true;
			foreach($dat->get() as $ctr)
			{
				if (!$fc->eval_controller($ctr, $fid))
				{
					$show = false;
				}
			}
			$all[] = array(
				"cur" => $form_id  == $fid,
				"show" => $show,
				"id" => $fid
			);
			if (!$show)
			{
				continue;
			}
			$toshow[$fid] = $fid;
		}

		// check if the form we are currently showing can be shown, if not, pick the first available
		if (!$toshow[$form_id])
		{
			$form_id = NULL;
			foreach($all as $ent)
			{
				if ($prev["cur"] && $ent["show"])
				{
					$form_id = $ent["id"];
					//echo "god id as $form_id <br>";
				}

				$prev = $ent;
			}
			$form_entry_id = NULL;

			if (!$form_id)
			{
				reset($toshow);
				list(,$form_id) = each($toshow);
				$form_entry_id = NULL;
			}
		}

//		echo "entry_id = $entry_id <br>";
		if ($entry_id && !$this->chain["rep"][$form_id] && !$form_entry_id)
		{
			$ear = $this->get_chain_entry($entry_id);
			$form_entry_id = $ear[$form_id];
//			echo "ear = <pre>", var_dump($ear),"</pre> <br />";
		}

		$sep = $this->parse("SEP");
		$first = true;
		$lang_id = aw_global_get("lang_id");


		$prev = false;

		foreach($this->chain["forms"] as $fid)
		{
			if (!$toshow[$fid])
			{
				continue;
			}
			if (!$first)
			{
				$ff.=$sep;
			}
			if ($section && $from_alias)
			{
				$url = $this->cfg["baseurl"]."/index.".$this->cfg["ext"]."/section=".$section."/form_id=".$fid."/entry_id=".$entry_id;
			}
			else
			{
				$url = $this->mk_my_orb("show", array("id" => $id, "section" => $section, "form_id" => $fid, "entry_id" => $entry_id));
			}
			if (isset($this->chain["lang_form_names"]))
			{
				$name = $this->chain["lang_form_names"][$fid][$lang_id];
			}
			else
			{
				$name = $this->chain["form_names"][$fid];
			}
			$this->vars(array(
				"url" => $url,
				"name" => $name
			));
			if ($fid != $form_id)
			{
				$ff.=$this->parse("FORM");
			}
			else
			{
				$ff.=$this->parse("SEL_FORM");
			}
			$first = false;

			if ($prev == $form_id)
			{
				aw_global_set("chain_next_form_url", $url);
			}

			$prev = $fid;
		}

		$cur_form = "";
		if ($this->chain["show_reps"][$form_id] && $entry_id)
		{
			// show form table with all the entries for the current chain entry
			$cur_form = $this->show_table_for_chain_entry(array(
				"chain" => $id,
				"chain_entry" => $entry_id,
				"form_id" => $form_id,
				"section" => $section,
				"table" => $this->chain["rep_tbls"][$form_id],
				"op" => $this->chain["rep_ops"][$form_id],
				"attribs" => array(
					"id" => $id,
					"section" => $section,
					"form_id" => $form_id,
					"entry_id" => $entry_id,
				)
			));
		}

		if (!$form_entry_id)
		{
			$led = $GLOBALS["load_entry_data"];
			$lcd = $GLOBALS["load_chain_data"];
		}
		$f = get_instance(CL_FORM);
		$f->set_current_chain_entry($entry_id);
		aw_global_set("current_chain_entry", $entry_id);
		aw_global_set("current_chain",$id);
		// FIXME: blah
		global $load_chain_data;
		$lcd = $load_chain_data;

		if (!$this->can("view", $form_entry_id))
		{
			$form_entry_id = NULL;
		}
//		echo "showng form $form_id entry $form_entry_id $led $lcd <br />";
		$cur_form .= $f->gen_preview(array(
			"id" => $form_id,
			"entry_id" => $form_entry_id, 
			"load_entry_data" => $led,
			"load_chain_data" => $lcd,
			"reforb" => $this->mk_reforb("submit_form", array(
				"id" => $id, 
				"section" => $section, 
				"form_id" => $form_id, 
				"chain_entry_id" => $entry_id,
				"form_entry_id" => $form_entry_id,
				"load_chain_data" => $load_chain_data, 
				"from_alias" => $from_alias
			))
		));

		$this->vars(array(
			"cur_form" => $cur_form,
			"FORM" => $ff,
			"SEL_FORM" => "",
			"SEP" => ""
		));

		if ($this->chain["during_show_entry"] && $entry_id && $this->chain["during_show_op"])
		{
			if (!is_array($ear))
			{
				$ear = $this->get_chain_entry($entry_id);
			}
			// siin on j2relikult $ear array olemas k6ikidest formi sisestustest ja tuleb n2idata v2ljundit valitud kohas
			$show_form_id = 0;
			$this->db_query("SELECT * FROM output2form WHERE op_id = ".$this->chain["during_show_op"]);
			while ($row = $this->db_next())
			{
				if ($ear[$row["form_id"]])
				{
					$show_form_id = $row["form_id"];
					break;
				}
			}
			if ($show_form_id)
			{
				$f = get_instance(CL_FORM);
				$entry = $f->show(array("id" => $show_form_id, "entry_id" => $ear[$show_form_id],"op_id" => $this->chain["during_show_op"]));
				switch ($this->chain["op_pos"])
				{
					case "down":
						$this->vars(array("down_entry" => $entry));
						break;
					case "left":
						$this->vars(array("left_entry" => $entry));
						break;
					case "right":
						$this->vars(array("right_entry" => $entry));
						break;
					case "up":
					default:
						$this->vars(array("up_entry" => $entry));
						break;
				}
			}
		}
		return $this->parse();
	}

	/** this is invoked when a form in the chain is submitted 
		
		@attrib name=submit_form params=name nologin="1" default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_form($arr)
	{
		extract($arr);

		$ch = $this->load_chain($id);

		// ok, here we must create a new chain_entry if none is specified
		if (!$chain_entry_id)
		{
			aw_disable_acl();
			$o = obj();
			$o->set_parent($this->chain["save_folder"]);
			$o->set_class_id(CL_CHAIN_ENTRY);
			$chain_entry_id = $o->save();
			aw_restore_acl();

			$this->db_query("INSERT INTO form_chain_entries(id,chain_id)
						VALUES($chain_entry_id,$id)");
			$new_chain_entry = true;
		}

		aw_global_set("proc_chain_entry_id", $chain_entry_id);
		aw_global_set("current_chain_entry", $chain_entry_id);
		aw_global_set("current_chain",$id);

		// then we must let formgen process the form entry and then add the entry to the chain. 

		// if this form is part of form calendar definition and is used for defining
		// periods, then we need to set a special flag so form->process_entry can
		// call form_calendar to update the calendar2timedef table

		// performs some check before actually doing the update

		// processing takes place inside form->process_entry because I have better
		// access to form elements from there
		$update_fcal_timedef = false;
		$cal_id = false;
		$has_calendar = $ch["flags"] & OBJ_HAS_CALENDAR;

		if ($has_calendar && $this->chain["cal_controller"])
		{
			$frm = get_instance("formgen/form_base");
			$frm->load($this->chain["cal_controller"]);
			/*
			print "<pre>";
			print_r($this->chain);
			print "</pre>";
			*/
			// first I have to load an entry from the calendar2forms which correspondends
			// to this chain

			// but which one? One calendar can have multiple event entry forms
			// I have to figure out which of those should I choose.
			// and that is where the el_relation field comes to play
			$q = "SELECT * FROM calendar2forms WHERE cal_id = '$id'";
			$this->db_query($q);
			$c2f = $this->db_next();
			// now c2f["form_id"] has the id of event entry form
			// and c2f["el_relation"] has the id of the element in that form, which
			// interests us. A relation element. Now we figure from which form
			// that element originates
			$q = "SELECT * FROM form_relations WHERE el_to = $c2f[el_relation]";
			$this->db_query($q);
			$f_r = $this->db_next();

			// $f_r[form_from] is it.
			// $f_r[el_from] is the element id which contains the information we want
			/*
			print "form: $f_r[form_from] el: $f_r[el_from]<br />";
			*/

			// and now the final step - figure out, which 
			// load the current chain entry

			$q = "SELECT * FROM form_chain_entries WHERE id = '$chain_entry_id'";
			$this->db_query($q);
			$fce = $this->db_next();
			$_eids = aw_unserialize($fce["ids"]);

			/*
			print "<pre>";
			print_r($_eids);
			print "</pre>";
			*/

			$_eid = $_eids[$f_r["form_from"]];

			/*
			$q = sprintf("SELECT * FROM form_%d_entries WHERE id = '$_eid'",$f_r["form_from"]);
			$this->db_query($q);
			$_xxx = $this->db_next();
			*/

			/*
			print "chain_entry_id = $chain_entry_id<br />";
			print "<pre>";
			print_r($_xxx);
			print "</pre>";
			*/

			$update_fcal_timedef = $chain_entry_id;
			$cal_id = $id;
			//print "updating time definition<br />";
		};
		
		$f = get_instance(CL_FORM);
		$f->process_entry(array(
				"id" => $form_id,
				"chain_entry_id" => $chain_entry_id,
				"entry_id" => $form_entry_id,
				"cal_id" => $cal_id,
				"update_fcal_timedef" => $update_fcal_timedef,
				"no_vac_check" => $no_vac_check,
				"cal_relation" => $_eid,
				"no_ml_rules" => true
		));
		$this->quote(&$f->entry_name);
		$submitted_form_id = $form_id;

//		echo "err = $f->has_controller_errors , nce  =$new_chain_entry <br />";
		if ($f->has_controller_errors && $new_chain_entry)
		{
			$tmp = obj($chain_entry_id);
			$tmp->delete();
			$chain_entry_id = 0;
		}
		else
		if ($f->has_controller_warnings && $new_chain_entry)
		{
			aw_disable_acl();
			$tmp = obj($chain_entry_id);
			$tmp->set_name($f->entry_name);
			$tmp->save();
			aw_restore_acl();

			$sbt = $f->get_opt("el_submit");

			$this->add_entry_to_chain($chain_entry_id,$f->entry_id,$form_id);
		}
		else
		{
			// now update the chain entry object with the form entry name
			aw_disable_acl();
			$tmp = obj($chain_entry_id);
			$tmp->set_name($f->entry_name);
			$tmp->save();
			aw_restore_acl();

			$sbt = $f->get_opt("el_submit");

			$this->add_entry_to_chain($chain_entry_id,$f->entry_id,$form_id);

			$tfid = $form_id;

			// sbt is a reference to the submit button object that was clicked

			// the following code figures out which form in the chain should be
			// shown next
			if ($this->chain["gotonext"][$form_id] && ($sbt["chain_forward"] == 0) && ($sbt["confirm"] == 0))
			{
				$prev = 0;

				// XXX: rewrite this without using breaks

				// need to figure out whether we have to go "back" in chain
				// so we cycle over all the forms in the chain and ...
				foreach($this->chain["forms"] as $fid)
				{
					if ( $sbt["chain_backward"] > 0)
					{
						if ($fid == $form_id)
						{
							// if prev was set in the last cycle
							// set form_id to that
							if ($prev)
							{
								$form_id = $prev;
							}
							// otherwise just drop out. first form
							// can't go back.
							break;
						}
					}

					// default action, go to the next form.
					// but only if it is not the last in chain
					if ($prev == $form_id)
					{
						$form_id = $fid;
						break;
					}

					$prev = $fid;

					if ($sbt["chain_finish"])
					{
						$tfid = $form_id;
						$sbt["chain_forward"] = 0;
						// let the code below redirect us to the end.
						break;
					}
				}
			}

			// so, if this was the last form in the chain and no_chain_forward is
			// not set, do what we are supposed to to after filling the last form.

			if ($tfid == $form_id && ($sbt["chain_forward"] == 0) )
			{
				// check that if we are after the last form then if the user has selected that we should show the entry then do so
				if ($this->chain["after_show_entry"] == 1 && $this->chain["after_show_op"] > 0  && $this->chain["gotonext"][$form_id] == 1)
				{
					return $this->mk_my_orb("show_entry", array("id" => $form_id,"entry_id" => $f->entry_id,"op_id" => $this->chain["after_show_op"],"section" => $section),"form");
				}
				else
				if ($this->chain["after_redirect"] == 1 && $this->chain["gotonext"][$form_id] == 1)
				{
					$url = $this->chain["after_redirect_url"];
					if (is_array($this->chain["after_redirect_url"]))
					{
						$url = $this->chain["after_redirect_url"][aw_global_get("lang_id")];
					}

					return $url;
				}
			}
		}

		// has something to do with embedding
		if (($section && $GLOBALS["class"] == "") || ($section && $from_alias))
		{
			$url = $this->cfg["baseurl"]."/index.".$this->cfg["ext"]."/section=".$section."/form_id=".$form_id."/entry_id=".$chain_entry_id;
		}
		else
		{
			$url = $this->mk_my_orb("show", array("id" => $id, "section" => $section, "form_id" => $form_id, "entry_id" => $chain_entry_id));
		}

		if ($f->go_to_after_submit != "")
		{
			aw_session_del("form_redir_after_submit_".$submitted_form_id);
			return $f->go_to_after_submit;
		}

		return $url;
	}

	function add_entry_to_chain($chain_entry_id,$form_entry_id,$form_id)
	{
		$this->db_query("SELECT * FROM form_chain_entries WHERE id = $chain_entry_id");
		$row = $this->db_next();
		$ar = aw_unserialize($row["ids"]);
		$ar[$form_id] = $form_entry_id;
		$tx = aw_serialize($ar,SERIALIZE_XML);
		$this->quote(&$tx);
		$this->db_query("UPDATE form_chain_entries SET ids = '$tx',tm = '".time()."' WHERE id = $chain_entry_id");
	}

	/**  
		
		@attrib name=show_chain_entries params=name default="0"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function show_chain_entries($arr)
	{
		extract($arr);
		$ob = $this->load_chain($id);
		$this->mk_path($ob["parent"],"<a href='".$this->mk_my_orb("change", array("id" => $id)).LC_FORM_CHAIN_CHANGE_WREATH_INPUT);
		$this->read_template("show_chain_entries.tpl");

		$this->db_query("SELECT form_chain_entries.*,objects.created AS tm,objects.createdby AS uid FROM form_chain_entries LEFT JOIN objects ON objects.oid = form_chain_entries.id WHERE chain_id = $id AND objects.status != 0");
		while ($row = $this->db_next())
		{
			$this->vars(array(
				"tm" => $this->time2date($row["tm"], 2),
				"uid" => $row["uid"],
				"change" => $this->mk_my_orb("show", array("id" => $id, "entry_id" => $row["id"])),
				"delete" => $this->mk_my_orb("delete_entry", array("id" => $id, "entry_id" => $row["id"])),
			));
			$this->parse("LINE");
		}
		return $this->parse();
	}

	/**  
		
		@attrib name=delete_entry params=name default="0"
		
		@param id required
		@param entry_id required
		
		@returns
		
		
		@comment

	**/
	function delete_entry($arr)
	{
		extract($arr);

		// get all form entries for chain entry and delete all of those as well.
		$e = $this->get_chain_entry($entry_id);
		foreach($e as $fid => $fentry_id)
		{
			$tmp = obj($fentry_id);
			$tmp->delete();
			$this->db_query("UPDATE form_".$fid."_entries SET chain_id = NULL WHERE id = $fentry_id");
		}

		// now delete chain entry object
		$tmp = obj($entry_id);
		$tmp->delete();
		header("Location: ".$this->mk_my_orb("show_chain_entries", array("id" => $id)));
	}

	/**  
		
		@attrib name=convchainentries params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function convchainentries($arr)
	{
		// for each chain
		aw_set_exec_time(AW_LONG_PROCESS);
		$this->db_query("SELECT * FROM objects WHERE class_id = ".CL_FORM_CHAIN." AND status != 0");
		while ($row = $this->db_next())
		{
			$f = get_instance(CL_FORM_CHAIN);
			$f->load_chain($row["oid"]);

			echo "chain $row[oid] <br />";
			flush();
			// for each entry in chain
			$this->save_handle();
			$this->db_query("SELECT * FROM form_chain_entries WHERE chain_id = ".$row["oid"]);
			while ($erow = $this->db_next())
			{
				$this->save_handle();
				// create object for it
				echo "entry $erow[id] for chain $row[oid] <br />";
				aw_disable_acl();
				$o = obj();
				$o->set_parent($f->chain["save_folder"]);
				$o->set_class_id(CL_CHAIN_ENTRY);
				$chain_entry_id = $o->save();
				aw_restore_acl();
				
				// update id in form_chain_entries table and all form_xxx_entries tables for the chain entry
				$e = $this->get_chain_entry($erow["id"]);
				foreach($e as $fid => $fentry_id)
				{
					echo "form_entry $fentry_id for form $fid of chain_entry $erow[id] for chain $row[oid] <br />";
					flush();
					$this->db_query("UPDATE form_".$fid."_entries SET chain_id = $chain_entry_id WHERE chain_id = $erow[id]");
				}

				$this->db_query("UPDATE form_chain_entries SET id = $chain_entry_id WHERE id = $erow[id] AND chain_id = $row[oid]");
				$this->restore_handle();
			}
			$this->restore_handle();
		}
	}

	////
	// !this shows all the form $form_id entries for the chain $chain for the entry $chain_entry with form table $table
	function show_table_for_chain_entry($arr)
	{
		extract($arr);
		$ft = get_instance(CL_FORM_TABLE);
		// now get all entries of the form for the chain entry
		$entdat = $ft->get_entries(array(
			"id" => $form_id,
			"all_data" => true,
			"chain_id" => $chain_entry
		));

		$ft->start_table($table);

		if ($this->start_el)
		{
			// filter out everything outside the range that interests us
			// GOD DAMMIT, this sucks
			$new_entdat = array();
			foreach($entdat as $row)
			{
				$rstart = $row["el_" . $this->start_el];
				$rend = $row["el_" . $this->end_el];
				if ( ($rstart > $this->start) && ($rend < $this->end) )
				{
					$new_entdat[] = $row;
				};
			}
			$entdat = $new_entdat;
		}

		foreach($entdat as $row)
		{
			$ft->row_data($row,$form_id,$section,$op,$chain,$chain_entry);
		}

		return $ft->finalize_table();
	}

	function delreplicas()
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		// for each form
		$this->db_query("SELECT * FROM objects WHERE class_id = ".CL_FORM);
		while ($row = $this->db_next())
		{
			echo "form $row[oid] <br />";
			flush();
			$this->save_handle();
			// for each entry
			$this->db_query("SELECT id FROM form_".$row["oid"]."_entries");
			while ($erow = $this->db_next())
			{
				echo "entry $erow[id] <br />";
				flush();
				// update form_entries table's form_id
				$this->save_handle();
				$this->db_query("UPDATE form_entries SET form_id = $row[oid] WHERE id = $erow[id]");
				$this->restore_handle();
			}
			$this->restore_handle();
		}
	}
}
?>
