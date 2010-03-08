<?php
/*
@classinfo  maintainer=kristo
*/

// ok. what is this, you ask?
//
// well, lemme tell ya a story:
//
// once upon a time in a software not so far away, there was this component called FormGen
// and this component could create and mantain database tables of it's own kind nicely
// but it severely lacked an interface through which it could easily and transparently
// use database tables created not by itself, but by the strange people from the worlds beyond the computer screen.
// after some pondering there was a loud TWANG! and lo and behold - a class by the name of form_db_base was created
// to magically swipe away all the worries of the FormGen.
// it had functions to read and write data - to query the deeply mythical structures of the Database Tables - and even
// modify them, so great was it's power!
// so it fulfilled the void in the heart of FormGen and is still happily doing it until today.
//
// right. a simple rule - all functions in this class MUST be wrapped in save_handle() / restore_handle() calls so
// that they will not interfere with their callers - even if it's pretty freaking obvious they do something with
// the database.
//   
// everything here assumes that the form is already loaded - having to load the form would be too high level
// for this class - it's here to handle the dirty little details and not used from outside formgen - that's also
// the reason why we should look lightly at calling functions defined in classes derived from this 
// well, that of course doesn't mean that you can't load forms if you need to, just that usually something
// is already loaded
//
// - terryf

// Documentation is like sex: when it is good, it is very, very good; and
// when it is bad, it is better than nothing.
//
// - duke


class form_db_base extends class_base
{
	function form_db_base()
	{
		$this->form_base();
	}

	////
	// !this alters the database tables necessary for the new element $el of form $fid
	// basically it just adds two columns to the form's default table for each element 
	// - so we can always save the data entered somewhere if no other table was specified
	function add_element_cols($fid,$el)
	{
		$this->save_handle();
		if (is_numeric($el))
		{
			// we must update the form_$fid_entries table
			// sigh. would be really nice if we could have elements of another type - integer for example
			// that would make some searches really faster, but alas - formgen is about as type-neutral
			// as you can get. sucks. 
			$this->db_query("ALTER TABLE form_".$fid."_entries ADD el_$el TEXT DEFAULT ''");
			$this->db_query("ALTER TABLE form_".$fid."_entries ADD ev_$el TEXT DEFAULT ''");

			// add indexes to form tables aswell
			// can't add these - mysql has a limit of 10 indexes per table :((
			// so we can't have more than 5 elements per form when we do this :((
//			$this->db_query("ALTER TABLE form_".$fid."_entries ADD INDEX el_$el(el_$el(10))");
//			$this->db_query("ALTER TABLE form_".$fid."_entries ADD INDEX ev_$el(ev_$el(10))");

			// and add this form to the list of forms in which the element is
			$this->db_query("INSERT INTO element2form(el_id,form_id) VALUES ($el,$fid)");
		}
		$this->restore_handle();
	}

	////
	// !removes the database tables that were used to save values entered in element $el of form $fid
	function del_element_cols($fid, $el)
	{
		$this->save_handle();
		// remove this form from the list of forms in which the element is
		$this->db_query("DELETE FROM element2form WHERE el_id = ".$el." AND form_id = ".$fid);

		// also remove the column for this element from the form
		$this->db_query("ALTER TABLE form_".$fid."_entries DROP el_".$el);
		$this->db_query("ALTER TABLE form_".$fid."_entries DROP ev_".$el);
		$this->restore_handle();
	}

	////
	// !returns the id of the form with what the entry ($entry_id) was created
	function get_form_for_entry($entry_id)
	{
		$this->save_handle();
		if (!($ret = aw_cache_get("form_for_entry_cache", $entry_id)))
		{
			$ret = $this->db_fetch_field("SELECT form_id FROM form_entries WHERE id = '$entry_id'","form_id");
			aw_cache_set("form_for_entry_cache", $entry_id, $ret);
		}
		$this->restore_handle();
		return $ret;
	}

	////
	// !checks if objects are to be created for this form and if they are, then creates the object based 
	// on the parameters in $arr, assumes form is loaded already
	// if objects are not to be created tries to generate a new id in one of the tables and returns that
	// in this case it also completely ignores the arguments passed to this function
	function create_entry_object($arr)
	{
		$this->save_handle();
		$entry_id = 0;
		if ($this->arr["save_table"] == 1)
		{
			// here we must figure out if we need to create any objects for the tables
			if ($this->arr["save_tables_obj_tbl"] != "")
			{
				// if we get here, we must create an object in the object table and also add a row in the corresponding table
				aw_disable_acl();
				$o = obj();
				$o->set_name($arr["name"]);
				$o->set_parent($arr["parent"]);
				$o->set_class_id($arr["class_id"]);
				$entry_id = $o->save(); 
				aw_restore_acl();
			}
			else
			{
				// if we got here then no object has to be created - but we do have to come up with
				// a new unique id that identifies the entry
				// of course this will create a problem if we save data to several tables, cause then just one id won't be enough
				// instead we need one for every table.
				// so, now we gotta figure out where the hell do we save them so we can find them later
				// well. but. maybe we don't need several id's after all, cause all the forms have to be related anyway
				// so that we could save data to them and be able to pick it apart later.
				// so what we must now do - is build a whole lotta cyclicity checking and integrity checking where you create the relations
				// right. did that. now when you successfully save the table relations ( you shouldn't be able to save them if they
				// don't add up ) it takes a guess at what would be the best table to start from and writes that in
				// $this->arr["save_table_start_from"] so we could easily use it here

				// so. we start by creating a new row in the first table and return it's id - then we can later use that
				// to find or create the other necessary rows. right? well, yeah sounds kinda fishy, I know, but it sould
				// work..
				$tbl = $this->arr["save_table_start_from"];
				$index_col = $this->arr["save_tables"][$tbl];
				$entry_id = $this->db_fetch_field("SELECT MAX(".$index_col.") as id FROM $tbl","id")+1;
				// yeah, yeah, I know, race condition, bla-bla, yadda yada. just fuck off, will ya.
				$q = "INSERT INTO $tbl($index_col) VALUES('$entry_id')";
				$this->db_query($q);
			}
		}
		else
		{
			// if we are not saving in precreated tables we always create a new object for entry forms
			// and we should have an option for search forms so that their entries could be saved in the session
			// so we won't be creating all kinds of useless search-objects and slow down the system.
			aw_disable_acl();
			$o = obj();
			$o->set_name($arr["name"]);
			$o->set_parent($arr["parent"]);
			$o->set_class_id($arr["class_id"]);
			$entry_id = $o->save(); 
			aw_restore_acl();
		}
		$this->restore_handle();
		return $entry_id;
	}

	////
	// !if objects are used in this form it updates the properies for object $oid
	// if not, it doesn't do anything
	function update_entry_object($arr)
	{
		$this->save_handle();
		if (!($this->arr["save_table"] == 1 && $this->arr["save_tables_obj_tbl"] == ""))
		{
			aw_disable_acl();
			$tmp = obj($arr["oid"]);
			$tmp->set_name($arr["name"]);
			if ($arr["parent"])
			{
				$tmp->set_parent($arr["parent"]);
			}
			$tmp->save();
			aw_restore_acl();
		}
		$this->restore_handle();
	}

	////
	// !this maps the data entered through the form to the necessary form (ev_555 => documents.title for instance)
	// and writes it to the correct tables in the database or if we ever do session saving of form entries then there too
	// $entry_id = the entry's id that is to be created - this sould be the id returned from form_db_base::create_entry_object
	// $entry_data = array of element_id => element_data pairs
	// $chain_entry_id = if the form is a part of a chain entry then here's how you can have it written to the database
	function create_entry_data($args = array())
	{
		extract($args);
		// set to 0 if not set
		$chain_entry_id = (int)$chain_entry_id;
		$cal_id = (int)$cal_id;
		$entry_data = $entry;

		$this->save_handle();
		if ($this->arr["save_table"] == 1)
		{
			// here we must write the data to the forms as specified in the form
			// we must start from the table secified in $this->arr[save_table_start_from] and then follow the relations from that
			// and create rows in the other tables with the correct data
			$this->req_create_entry_data($this->arr["save_table_start_from"],$entry_id,$entry_data,$chain_entry_id,true);
		}
		else
		{
			// add new entry - just so that we can determine the form id from the entry's id when we need to
			$this->db_query("INSERT INTO form_entries(id,form_id,cal_id) VALUES($entry_id, $this->id,$cal_id)");

			// create sql
			$ids = "id";
			$vals = "$entry_id";
			if ($chain_entry_id)
			{
				$ids.=",chain_id";
				$vals.=",".$chain_entry_id;
			}

			$first = true;
			reset($entry_data);
			while (list($k, $v) = each($entry_data))
			{
				$el = $this->get_element_by_id($k);
				if (is_object($el))
				{
					$ids.=",el_$k,ev_$k";

					// see on pildi uploadimise elementide jaoks
					if (is_array($v))
					{
						$v = aw_serialize($v,SERIALIZE_NATIVE);
					}
					$this->quote(&$v);

					$ev = $el->get_value();
					$this->quote(&$ev);

					$vals.=",'$v','$ev'";
				};
			}

			// now, if this form is translated, then copy the entry to all languages
			if ($this->arr["is_translatable"])
			{
				$l = get_instance("languages");
				$ll = $l->get_list();
				foreach($ll as $lid => $ld)
				{
					$try = $this->db_fetch_field("SELECT id FROM form_".$this->id."_entries WHERE id = '$entry_id'", "id");
					if ($try != $entry_id)
					{
						$sql = "INSERT INTO form_".$this->id."_entries($ids,lang_id) VALUES($vals,$lid)";
						$this->db_query($sql);
					}
				}
			}
			else
			{
				// damn, I hate to do this, but I can't figure this out right now
				$try = $this->db_fetch_field("SELECT id FROM form_".$this->id."_entries WHERE id = '$entry_id'", "id");
				if ($try != $entry_id)
				{
					$sql = "INSERT INTO form_".$this->id."_entries($ids) VALUES($vals)";
					$this->db_query($sql);
				}
			}
		}
		$this->restore_handle();
	}

	////
	// !takes the data entered by the user and writes it to the database table $tbl and then recursively follows relations
	// so that all the data gets written to the necessary tables
	// parameters: 
	// $tbl - the table to write the data in
	// $entry_id - the id of the entry
	// $entry_data - the data that the user entered
	// $chain_entry_id - just in case we figure out a way to use random tables in form chains
	function req_create_entry_data($tbl,$entry_id,$entry_data,$chain_entry_id = 0,$first = false)
	{
		$this->save_handle();
		$els = $this->get_elements_for_table($tbl);

		// now try and piece together a query that sticks the data in the table
		$idx_col = $this->arr["save_tables"][$tbl];
		$colnames = array();
		$colnames[] = $idx_col;	// index column in table
		$elvalues = array();
		$elvalues[] = $entry_id;

		foreach($els as $el)
		{
			$colnames[] = $el->get_save_col();
			$ev = $el->get_value();
			$this->quote(&$ev);
			$elvalues[] = $ev;
		}

		if ($first && $this->arr["save_tables_obj_tbl"] == "")	
		{
			// if we don't create objects for entries, convert the arrays into the correct form
			$dat = array();
			foreach($colnames as $_id => $_colname)
			{
				if ($colname != $idx_col)
				{
					$dat[$_colname] = $elvalues[$_id];
				}
			}
			// we do this (update, not insert), because we already created a row in the first table to get the entry_id.
			$q = "UPDATE $tbl SET ".join(",",map2("%s = '%s'",$dat))." WHERE $idx_col = '$entry_id'";
		}
		else
		{
			// here we must insert a new row in the correct table
			$q = "INSERT INTO $tbl (".join(",",$colnames).") VALUES(".join(",",map("'%s'",$elvalues)).")";
		}
		$this->db_query($q);

		// we have managed to write the data, now we must recurse with the next table in line
		$_tmp = $this->arr["save_tables_rels"][$tbl];
		if (is_array($_tmp))
		{
			// go through the tables related to this one one by one and have them write their data
			foreach($_tmp as $r_tbl)
			{
				$this->req_create_entry_data($r_tbl,$entry_id,$entry_data,$chain_entry_id);
			}
		}
		$this->restore_handle();
	}

	////
	// !returns an array that contains references to all of the loaded form's elements that should be written to table $tbl 
	function get_elements_for_table($tbl)
	{
		$ret = array();
		$els = $this->get_all_els();
		foreach($els as $el)
		{
			if ($el->get_save_table() == $tbl)
			{
				$ret[] = $el;
			}
		}
		return $ret;
	}

	////
	// !updates the data in the correct storage medium from the data gathered from the POST data
	function update_entry_data($entry_id,$entry_data)
	{
		$this->save_handle();
		if ($this->arr["save_table"] == 1)
		{
			// update all the tables recursively following the relations
			$this->req_update_entry_data($this->arr["save_table_start_from"],$entry_id,$entry_data);
		}
		else
		{
			// create sql 
			$ids = "id = $entry_id";
			$lang_ids = "";

			$is_trans = $this->arr["is_translatable"];

			reset($entry_data);
			while (list($k, $v) = each($entry_data))
			{
				$el = $this->get_element_by_id($k);
				if ($el)
				{
					$ev = $el->get_value();
					$this->quote(&$ev);
					if (is_array($v))
					{
						$v = aw_serialize($v, SERIALIZE_NATIVE);
					}
					$this->quote(&$v);
					if ($el->is_translatable() && $is_trans)
					{
						$lang_ids.=",el_$k = '$v',ev_$k = '$ev'";
					}
					else
					{
						$ids.=",el_$k = '$v',ev_$k = '$ev'";
					}
				}
			}

			$sql = "UPDATE form_".$this->id."_entries SET $ids WHERE id = $entry_id";
			if ($GLOBALS["save_dbg"] == 1)
			{
				echo "save sql = $sql <br>";
			}
			$this->db_query($sql);

			if ($lang_ids != "")
			{
				$ids = substr($lang_ids, 1);
				$sql = "UPDATE form_".$this->id."_entries SET $ids WHERE id = $entry_id AND lang_id = ".aw_global_get("lang_id");
				$this->db_query($sql);
			}
		}
		$this->restore_handle();
	}

	////
	// !recursively writes the data to the correct tables and maps it from elements to table columns
	function req_update_entry_data($tbl,$entry_id,$entry_data)
	{
		$this->save_handle();
		$els = $this->get_elements_for_table($tbl);

		// now iterate over the elements and do the correct column mappings
		$dat = array();
		foreach($els as $el)
		{
			$ev = $el->get_value();
			$this->quote(&$ev);
			$dat[$el->get_save_col()] = $ev;
		}

		// and now just turn it into a query - hey, this is easy
		$idx_col = $this->arr["save_tables"][$tbl];
		$q = "UPDATE $tbl SET ".join(",",map2("%s = '%s'",$dat,0,true))." WHERE $idx_col = '$entry_id'";
//		echo "q = $q <br />";
		$this->db_query($q);

		// and now recurse to the other tables
		$_tmp = $this->arr["save_tables_rels"][$tbl];
		if (is_array($_tmp))
		{
			// go through the tables related to this one one by one and have them write their data
			foreach($_tmp as $r_tbl)
			{
				$this->req_update_entry_data($r_tbl,$entry_id,$entry_data);
			}
		}
		$this->restore_handle();
	}

	////
	// !deletes $entry_id of form $id and redirects to hexbin($after) 
	// before deleting it checks if the form has objects created - if not, no entry is deleted
	function delete_entry($arr)
	{
		extract($arr);
		$this->do_delete_entry($id, $entry_id);
		$after = $this->hexbin($after);
		header("Location: ".$after);
		die();
	}

	function do_delete_entry($id, $entry_id)
	{
		if ($this->id != $id)
		{
			$this->load($id);
		}
		if (($this->arr["save_table"] == 1 && $this->arr["save_tables_obj_tbl"] != "") || $this->arr["save_table"] != 1)
		{
			$tmp = obj($entry_id);
			$tmp->delete();
			// mark it deleted in the default table
			$this->db_query("update form_".$id."_entries SET deleted = 1 where id = '$entry_id'");
			$this->_log(ST_FORM_ENTRY, SA_DELETE,"form $this->name sisestus $entry_id", $entry_id);
		}
		else
		{
			// delete permanently from the table then I guess
			$q = "DELETE FROM ".$this->arr["save_table_start_from"]." WHERE ".$this->arr["save_tables"][$this->arr["save_table_start_from"]]." = '$entry_id'";
			$this->db_query($q);
			$this->_log(ST_FORM_ENTRY, SA_DELETE,"form $this->name sisestus $entry_id (tabelist ".$this->arr["save_table_start_from"].")");
		}

		// also run all controller actions
		$fact = get_instance("formgen/form_actions");
		$fact->do_on_delete_actions(&$this, $entry_id);
	}

	////
	// !reads the data for the entry from it's designated place, maps it to elements and bundles it in an array of
	// $el_id => $el_value pairs which it returns
	// if only_el is true, the query does not fetch ev_[xxx] values - they are not needed for instance, 
	// when loading a single form entry
	function read_entry_data($entry_id, $only_el = false)
	{
		$this->save_handle();

		if ($this->type == FTYPE_SEARCH)
		{
			// we do an optimization here. since search entries are never related to each other
			// and search forms cannot write to pre-existing database tables
			// we know that we only need to load one row from form_xxx_entries table. kickass
			$sql_data = " * ";
			$sql_join = "form_".$this->id."_entries";
			$idx_tbl = "form_".$this->id."_entries";
			$idx_col = "id";
			if ($this->arr["is_translatable"])
			{
				$lang = " AND $idx_tbl.lang_id = ".aw_global_get("lang_id");
			}
		}
		else
		{
			// put all the joins into sql
			$sql_join = $this->get_sql_joins_for_search(false,$this->id, true);

			// now get fetch data part ( yeah, it must be done exactly in this order, cause the functions 
			// prepare instance variables for eachother in the interest of speed)
			$sql_data = $this->get_sql_fetch_for_search($this->_joins, $this->id,false, array(), false, $only_el);

			if ($this->arr["save_table"])
			{
				$idx_tbl = form_db_base::mk_tblname($this->arr["save_table_start_from"], $this->id);
				$idx_col = $this->arr["save_tables"][$this->arr["save_table_start_from"]];
			}
			else
			{
				$idx_tbl = form_db_base::mk_tblname("form_".$this->id."_entries", $this->id);
				$idx_col = "id";
			}

			if ($this->arr["is_translatable"])
			{
				$lang = " AND $idx_tbl.lang_id = ".aw_global_get("lang_id");
			}
		}
		$sql = "SELECT $sql_data FROM $sql_join WHERE $idx_tbl.$idx_col = '$entry_id' $lang LIMIT 1";
		if ($GLOBALS["fg_re_dbg"] == 1)	echo "read_entry sql = $sql <br />";

		$this->db_query($sql);
		$row = $this->db_next();
		$this->dequote(&$row);
		// we gather the el_id => el_value pairs here
		$ret = array();
		$this->read_elements_from_q_result($row, &$ret);

		$this->restore_handle();
		return $ret;
	}

	////
	// !this function basically copies data from one array to another except that it just copies entries
	// whose key starts with el_ and is followed by a number
	// it reads the number after ev_ and puts it on $res and assigns the same value to it
	function read_elements_from_q_result($row,&$res)
	{
		if (is_array($row))
		{
			foreach($row as $k => $v)
			{
				if (substr($k,0,3) == "el_")
				{
					$res[substr($k,3)] = str_replace("\\'","'",$v);
				}
			}
		}
	}

	////
	// !returns the sql query that will perform the search, based on the loaded form and the loaded entry
	// parameters:
	//	$used_els - if it is omitted or is an empty array, then all elements from all the forms are returned
	//			if it contains some element/form id's, then only those elements are returned
	//	$group_collect_els - if we are doing grouping on the form_table and we have set some elements as "collect" 
	//			elements, then they end up in this array. the end result of this is that the values these elements
	//			have on the rows that are not displayed, are concatenated together. 
	//	$group_els - grouping elements - if specified, the sql will do a GROUP BY on all these elements
	//  $ret_id_only - if true, only the entry id is fetched in the query, nothing else
	//	$sort_by - array of elements to sort results by
	//	$sort_order - array of asc/desc orders for sorting elements
	function get_search_query($arr)
	{
		extract($arr);
		if (!is_array($used_els))
		{
			$used_els = array();
		}

		// ugh. this is the complicated bit again. 

		// first the where part - these _must_ be called in this order cause they init member variables that they use
		$sql_where = $this->get_sql_where_clause();

//		echo "sw = $sql_where <br />";
		// now put all the joins into sql
		$sql_join = $this->get_sql_joins_for_search($used_els,$this->arr["start_search_relations_from"],false,$ret_id_only);
//		echo "sj = $sql_join <br />";

		// now get fetch data part
		$sql_data = $this->get_sql_fetch_for_search($this->_joins,$this->arr["start_search_relations_from"],$used_els, $group_collect_els, $group_els,false,$ret_id_only);
//		echo "sd = $sql_data <br />";

		// this adds deleted object checking if the form entries have objects attached
		$sql_where = $this->get_sql_where_objects_part($sql_where);
//		echo "sw = $sql_where <br />";

		// if we are showing a form table and it has groupings set, they will end up here and this turns them into sql
		$sql_grpby = $this->get_sql_grpby($group_els);
//		echo "sg = $sql_grpby <br />";


		$sql_orderby = $this->get_sql_orderby($sort_by, $sort_order);

		$sql = "SELECT ".$sql_data." FROM ".$sql_join.$sql_where.$sql_grpby.$sql_orderby;
		if ($GLOBALS["fg_dbg"]) 
		{
			echo ("sql = $sql <br />");
		}
		return $sql;
	}

	////
	// !returns an instance of form $fid - caches the instances as well
	function &cache_get_form_instance($fid)
	{
		if (!$fid)
		{
			return get_instance(CL_FORM);
		}

		if (!is_object(($finst =& aw_cache_get("cache_get_form_instance", $fid))))
		{
			$finst = get_instance(CL_FORM);
			$finst->load($fid);
			aw_cache_set("cache_get_form_instance", $fid, &$finst);
		}
		return $finst;
	}

	////
	// !returns an array of db tables for this form - 
	//		if it's a normal form then it's just one table - form_[id]_entries
	//		but if the form writes to other tables then this returns all the names of the tables and the info on how to join them
	function get_tables_for_form()
	{
		if ($this->arr["save_table"] == 1)
		{
			return array(
				"from" => $this->arr["save_table_start_from"],
				"joins" => $this->arr["save_tables_rels"],
				"join_via" => $this->arr["save_tables_rel_els"],
				"table_indexes" => $this->arr["save_tables"]
			);
		}
		else
		{
			$ftn = "form_".$this->id."_entries";
			$fta = array();
			$fta[$ftn][$ftn] = $ftn;
			return array(
				"from" => $ftn, 
				"joins" => $fta,
				"join_via" => array(),
				"table_indexes" => array($ftn => "id")
			);
		}
	}

	////
	// !this builds the sql joins necessary for the search to succeed
	// also builds the relation tree $this->form_rel_tree , $this->_joins array ant $this->table2form_map
	// used_els - the elements whose values we must be able to produce 
	// start_relations_from - the starting point of the relation finding
	// no_reverse_rels - don't try to find reverse relations between tables 
	//		(connected_el -> relation_el - this finds a lot of relations that are not always necessary 
	// no_fetch_joins - no forms are joined that are used in fetch data part, only the forms used in where clause
	function get_sql_joins_for_search($used_els, $start_relations_from,$no_reverse_rels = false, $no_fetch_joins = false)
	{
		// recurse through the selected search form relations. boo-ya!
		$this->build_form_relation_tree($start_relations_from, 0, $no_reverse_rels);

		// now go over the rel tree and kick out the ones that are set as not to be inkluded
		$this->prune_relation_tree();

		$this->_joins = array();

		if ($no_fetch_joins)
		{
			$used_els = array();
		}
		else
		if (!is_array($used_els))
		{
			// if used_els is not an array, that means all elements from all forms should be included
			$used_els = array();
			if (is_array($this->form_rel_tree))
			{
				foreach($this->form_rel_tree as $fid => $frels)
				{
					$used_els[$fid] = array();
					foreach($frels as $frfid => $frdat)
					{
						$used_els[$frfid] = array();
					}
				}
			}
		}

		$srfi = $this->cache_get_form_eldat($start_relations_from);

		if ($srfi["save_table"])
		{
			$tn = form_db_base::mk_tblname($srfi["save_table_start_from"], $start_relations_from);
		}
		else
		{
			$tn = form_db_base::mk_tblname("form_".$start_relations_from."_entries",$start_relations_from);
		}
		// add the start table to the join map
		$this->_joins[] = array(
			"from_tbl" => $tn, 
			"from_el" => false,
			"to_tbl" => false,
			"to_el" => false
		);
		if (!$srfi["save_table"] || ($srfi["save_tables_obj_tbl"] != ""))
		{
			$this->_joins[] = array(
				"from_tbl" => $tn, 
				"from_el" => "id",
				"to_tbl" => "objects",
				"to_el" => "oid"
			);
		}

		// this is used in determining whether we should add AND objects.status != 0 to the where part (in get_sql_where_objects_part())
		// it is stored here for efficiency reasons
		$this->has_obj_table = false;
		if ($srfi["save_tables_obj_tbl"] != "" || !$srfi["save_table"])
		{
			$this->has_obj_table = true;
		}
		$this->table2form_map[$tn] = $start_relations_from;


		// here's how we're gonna do this (creating the actual join paths from the relation tree):
		// loop over all forms used in the query and for each form
		// find the path to it from the starting form, via relations
		// always start from the form marked as the one to start the searches from

		// get a list of all forms used in the query 
		// - to do that we add the elements that are used in the where part (come from the search form)
		// to the elements that are already in the used_elements array (come from form_table)
		$forms_queried = $this->get_forms_used_in_where();
		if (!$no_fetch_joins)
		{
			foreach($used_els as $fid => $els)
			{
				$forms_queried[$fid] = $fid;
			}
		}
		foreach($forms_queried as $fid)
		{
			if ($fid != $start_relations_from)
			{
				// put the path in the tree onto a stack
				$jp = $this->get_join_path($start_relations_from, $fid);
				// now build the correct relations from the path
				$this->build_join_rels_from_path($jp);
			}
		}

		// ok, so we can assume that we have all the necessary relations in $this->_joins, so convert that into sql
		$sql = "";
		$first = true;
		$this->join_sql_used = array($tn => $tn);
		$this->join_sql_used_forms_from = array();
						//echo "rrjoins = <pre>", var_dump($this->_joins),"</pre> <br />";
		foreach($this->_joins as $jdata)
		{
			if ($first)
			{
				// the first table is the root table - not joined but the FROM table
				$sql = $this->get_rtbl_from_tbl($jdata["from_tbl"])." AS ".$jdata["from_tbl"];
				$prev = $jdata;
				$this->sql_from_table = $jdata["from_tbl"];
			}
			else
			{
				if (!isset($this->join_sql_used[$jdata["to_tbl"]]))
				{
					if ($jdata["to_el"] == "oid" && $jdata["to_tbl"] == "objects")
					{
						// yeah, this fuking fixes a php bug, I know it looks damn weird
						$jdata["from_el"] = "id";
					}
					// other tables get joined - making sure we don't accidentally join them several times
					$__t = $this->get_rtbl_from_tbl($jdata["to_tbl"]);
					if ($__t != "")
					{
						// now check exclusion list
						$__tfid = $this->table2form_map[$jdata["from_tbl"]];
						$__t2fid = $this->table2form_map[$jdata["to_tbl"]];
						if (!($this->do_not_fetch[$__tfid] == $__tfid || $this->do_not_fetch[$__t2fid] == $__t2fid) || $jdata["to_tbl"] == "objects")
						{
							// also, get the form and check if it is translatable. 
							// if it is, then we must add the lang_id parameter to the join clause.
							$__fi = $this->cache_get_form_eldat($__t2fid);
							$lang = "";
							if ($__fi["is_translatable"] && !$dat["save_table"])
							{
								$lang = " AND $jdata[to_tbl].lang_id = ".aw_global_get("lang_id");
							}

							$del = "";
							if (substr($jdata["to_tbl"], 0, 5) == "form_")
							{
								$del = " AND $jdata[to_tbl].deleted != 1 ";
							}

							// now, detect if the join is to the same chain
							/*if ($jdata["from_el"] == "chain_id")
							{
								// and if previously, the chain's first form was joined
								$chff = $this->form2first_form_in_chain[$__tfid];
								if ($chff && $this->join_sql_used_forms_from[$chff] != "")
								{
									// rewrite the join to be to the first table
									// so that all the forms in between would not have to be filled
									$jdata["from_tbl"] = $this->join_sql_used_forms_from[$chff];
								}
							}*/

							$sql.=" LEFT JOIN ".$__t." AS ".$jdata["to_tbl"]." ON (".$jdata["from_tbl"].".".$jdata["from_el"]." = ".$jdata["to_tbl"].".".$jdata["to_el"]." $lang $del) ";

							$this->join_sql_used[$jdata["to_tbl"]] = $jdata["to_tbl"];
							$this->join_sql_used_forms_from[$__tfid] = $jdata["from_tbl"];
						}
						else
						{
							$this->do_not_fetch[$__t2fid] = $__t2fid;
						}
					}
				}
			}
			$first = false;
		}
//		echo " sql = $sql <br />";
		return $sql;
	}

	////
	// !this creates the fetch data part of the query 
	// it just loops over all the used elements, finds their database tables and gives them correct names
	// params:
	// joins - we use this as a map for which forms we should look, this makes sure that we don't
	//		accidentally add any elements whose table was not joined 
	// start_relations_from - obvious
	// used_els - the elements that we need to fetch - if not array, then we fetch all elements
	// gp_coll_els - the elements whose value should be "collected" (concatenated) through all the grouped rows
	// group_els - the elements that will be in the GROUP BY clause
	// only_el - if true, only el_[xxx] fetches will be created, if true, both - we don't need the ev_[xxx] fetches when we 
	//		are simply loading a single entry
	// only_eid - if true, only entry_id's are put in the fetch sql
	function get_sql_fetch_for_search($joins, $start_relations_from, $used_els, $gp_coll_els = array(), $group_els = false, $only_el = false, $only_eid = false)
	{
		$sql = "";
		$usedtbls = $this->pruned_forms;
		foreach($joins as $jdata)
		{
			// find the form for the table and get all elements of the form and find out what columns they map to in the table
			$tbl = $jdata["from_tbl"];
			if (!$usedtbls[$tbl])
			{
				$usedtbls[$tbl] = 1;
				$fid = $this->table2form_map[$tbl];
				if ($this->do_not_fetch[$fid] == $fid)
				{
					continue;
				}
				$form = $this->cache_get_form_eldat($fid);

				if ($sql == "")
				{
					$_tf = $this->cache_get_form_eldat($fid);
					$_tftbls = $_tf["save_tables"];
				
					$_rtbl = form_db_base::get_rtbl_from_tbl($tbl);
					// make sure we get the id column as entry_id
					$sql=$tbl.".".$_tftbls["table_indexes"][$_rtbl]." AS entry_id "; 
					if ($this->arr["search_chain"])
					{
						// if we are doing a chain search, then also get the chain id
						$sql.=",".$tbl.".chain_id AS chain_entry_id "; 
					}
					if ($only_eid)
					{
						return $sql;
					}
				}

				// include only the necessary elements - if used_els is not an array, then all elements, 
				// otherwise the elements from the used_els that are from the current iteration's table
				$elar = $form["els"];
				if (is_array($used_els) && count($used_els) > 0)
				{
					$elar = array();
					if (is_array($used_els[$fid]))
					{
						foreach($used_els[$fid] as $el)
						{
							$elar[$el] = $form["els"][$el];
						}
					}
				}
				if (is_array($elar))
				{
					foreach($elar as $el => $eltbls)
					{
						$s_t = form_db_base::mk_tblname($eltbls["table"], $fid);
						if ($s_t == $tbl && $tbl != "")
						{
							// if this element gets written to the current table, include it in the sql
							if ($eltbls["col"] != "" && !$only_el)
							{
								if (isset($gp_coll_els[$el]))
								{
									$sql.=", stradd(\"".$gp_coll_els[$el]["sep"]."\",".$tbl.".".$eltbls["col"].",1) AS ev_".$el;
								}
								else
								{
									$sql.=", ".$tbl.".".$eltbls["col"]." AS ev_".$el;
								}
							}
							if ($eltbls["col2"] != "")
							{
								if (isset($gp_coll_els[$el]))
								{
									$sql.=", stradd(\"".$gp_coll_els[$el]["sep"]."\",".$tbl.".".$eltbls["col2"].",1) AS el_".$el;
								}
								else
								{
									$sql.=", ".$tbl.".".$eltbls["col2"]." AS el_".$el;
								}
							}
						}
					}
				}
			}
			// now do exactly the same thing for the other end (table) of the join
			$tbl = $jdata["to_tbl"];
			if (!$usedtbls[$tbl])
			{
				$usedtbls[$tbl] = 1;
				$fid = $this->table2form_map[$tbl];
				if ($this->do_not_fetch[$fid] == $fid)
				{
					continue;
				}
				if ($fid)
				{
					$form = $this->cache_get_form_eldat($fid);

					if ($sql == "")
					{
						$_tf = $this->cache_get_form_eldat($fid);
						$_tftbls = $_tf["save_tables"];

						$sql=$tbl.".".$_tftbls["table_indexes"][form_db_base::get_rtbl_from_tbl($tbl)]." AS entry_id "; 
						if ($this->arr["search_chain"])
						{
							// if we are doing a chain search, then also get the chain id
							$sql.=",".$tbl.".chain_id AS chain_entry_id "; 
						}
					}

					$elar = $form["els"];
					if (is_array($used_els) && count($used_els) > 0)
					{
						$elar = array();
						if (is_array($used_els[$fid]))
						{
							foreach($used_els[$fid] as $el)
							{
								$elar[$el] = $form["els"][$el];
							}
						}
					}
					$_t = new aw_array($elar);
					foreach($_t->get() as $el => $eldat)
					{
						$s_t = form_db_base::mk_tblname($eldat["table"], $fid);
						if ($s_t == $tbl && $tbl != "")
						{
							// if this element gets written to the current table, include it in the sql
							if ($eldat["col"] != "" && !$only_el)
							{
								if (isset($gp_coll_els[$el]))
								{
									$sql.=", stradd(\"".$gp_coll_els[$el]["sep"]."\",".$tbl.".".$eldat["col"].",1) AS ev_".$el;
								}
								else
								{
									$sql.=", ".$tbl.".".$eldat["col"]." AS ev_".$el;
								}
							}
							if ($eldat["col2"] != "")
							{
								if (isset($gp_coll_els[$el]))
								{
									$sql.=", stradd(\"".$gp_coll_els[$el]["sep"]."\",".$tbl.".".$eldat["col2"].",1) AS el_".$el;
								}
								else
								{
									$sql.=", ".$tbl.".".$eldat["col2"]." AS el_".$el;
								}
							}
						}
					}
				}
			}
		}

		// now if the start search from form is not written to table it will get objects table
		// joined and then we can fetch creator/modifier and other fields
		$srfi = $this->cache_get_form_eldat($start_relations_from);
		if (!$srfi["save_table"])
		{
			$sql.=", objects.modified as modified, objects.modifiedby as modifiedby, objects.created as created, objects.createdby as createdby ";
		}

		// and if we are doing grouping, always add a count() column, because we might need it when showing the table
		if (is_array($group_els) && count($group_els) > 0)
		{
			$sql.=", count(*) as ev_cnt ";
		}
//		echo "sql = $sql <br />";
		return $sql;
	}

	////
	// !this builds the WHERE sql clause from the data that the user entered into the search form
	// this should be called first when constructing a query, because the other query-part functions
	// use $this->forms_used_in_where that gets initialized in this function
	function get_sql_where_clause()
	{
		$this->forms_used_in_where = array();

		// checkbox groups are stored here (checkboxes with the same group get OR between them)
		$ch_q = array();

		// loop through all the elements of this form 
		$els = $this->get_all_els();
		reset($els);
		while( list(,$el) = each($els))
		{
			if ($el->arr["linked_form"] && $el->arr["linked_element"])	
			{
				$relf = $this->cache_get_form_eldat($el->arr["linked_form"]);
				$linked_el = $relf["els"][$el->arr["linked_element"]];
				$elname = form_db_base::mk_tblcol($linked_el["table"],$linked_el["col2"],$el->arr["linked_form"]);
				$elname2 = form_db_base::mk_tblcol($linked_el["table"],$linked_el["col"],$el->arr["linked_form"]);

				// include only elements into which the user has entered a value
				if (($value = trim($el->get_value())) != "")	
				{
					$this->forms_used_in_where[$el->arr["linked_form"]] = $el->arr["linked_form"];

					if ($el->arr["type"] == "multiple")
					{
						// multiple select box - any selected element must match
						if ($query != "")
						{
							$query.=" AND ";
						}
						$query.=" (";

						$qpts = array();

						$ec=explode(",",$el->entry);
						foreach($ec as $v)
						{
							$qpts[] = " ".$elname2." like '%".$el->arr["multiple_items"][$v]."%' ";
						}

						$query.= join("OR",$qpts).")";
					}
					else
					if ($el->arr["type"] == "checkbox")
					{	
						// only search checkboxes if they are checked
						if ($el->get_value(true) == 1)
						{
							// group checkboxes so that there are AND's between groups and OR's inside groups
							// checkbox values must always match exactly - there seems to be no point in
							// checking for partial matches
							$ch_q[$el->get_ch_grp()][] = " ".$elname." = '1' ";
						}
					}
					else
					if ($el->arr["type"] == "radiobutton")
					{
						// only search radiobuttons that are checked
						if ($el->get_value(true) == 1)
						{
							if ($query != "")
							{
								$query.=" AND ";
							}
							// seems to me that radiobutton values should also always be an exact match, not just partial
							$query.=" (".$elname2." LIKE '%".$value."%')";
						}
					}
					else
					if ($el->arr["type"] == "listbox")
					{
						$elname2 = form_db_base::mk_tblcol($linked_el["table"],$linked_el["col"],$el->arr["linked_form"]);
						if ($query != "")
						{
							$query.=" AND ";
						}
						// and like radiobuttons and checkboxes, listbox items should be exact matches
						if ($el->arr["lb_search_like"])
						{
							$query.=" (".$elname2." LIKE '%".$value."%')";
						}
						else
						{
							$query.=" (".$elname2." = '".$value."')";
						}
					}
					else
					if ($el->arr["type"] == "date")
					{
						$_ts = $el->get_val();
						if ($_ts > 1)
						{
							if ($query != "")
							{
								$pre = " AND";
							}
							if ($el->arr["subtype"] == "from")
							{
								$query.= $pre." (".$elname." >= ".$_ts.")";
							}
							else
							if ($el->arr["subtype"] == "to")
							{
								$query.= $pre." (".$elname." <= ".$_ts.")";
							}
							else
							{
								$query.= $pre." (".$elname." = ".$_ts.")";
							}
						}
					}
					else
					if ( ($el->arr["type"] == "textbox") && ($el->arr["subtype"] == "count") )
					{
						// count is special, we don't want to search in that field
						// think calendar!
					}
					else
					{
						if ($el->arr["search_separate_words"])
						{
							if (preg_match("/\"(.*)\"/",$value,$matches))
							{
								if ($el->arr["search_field_in_set"] == 1)
								{
									$qstr = " FIND_IN_SET('$matches[1]',$elname2) ";
								}
								else
								if ($el->arr["search_all_text"] != 1)
								{
									$qstr = " $elname2 LIKE '%$matches[1]%' ";
								}
								else
								{
									$sep = " = ";
									if (strpos($matches[1], "%") !== false)
									{
										$sep = " LIKE ";
									}
									$qstr = " $elname2 $sep '$matches[1]' ";
								}
							}
							else
							{
								// now split it at the spaces
								$sep = " ";
								if ($el->arr["search_separate_words_sep"] != "")
								{
									$sep = $el->arr["search_separate_words_sep"];
								}
								$pieces = explode($sep,$value);
								if (is_array($pieces))
								{
									if ($el->arr["search_field_in_set"] == 1)
									{
										$qstr = join (" OR ",map("FIND_IN_SET('%s',$elname)",$pieces));
									}
									else
									if ($el->arr["search_all_text"] != 1)
									{
										$qstr = join (" OR ",map("$elname LIKE '%%%s%%'",$pieces));
									}
									else
									{
										$qstr = join (" OR ",map("$elname = '%s'",$pieces));
									}
								}
								else
								{
									if ($el->arr["search_field_in_set"] == 1)
									{
										$qstr = " FIND_IN_SET('$value',$elname2) ";
									}
									else
									if ($el->arr["search_all_text"] != 1)
									{
										$qstr = " $elname2 LIKE '%$value%' ";
									}
									else
									{
										$sep = " = ";
										if (strpos($value, "%") !== false)
										{
											$sep = " LIKE ";
										}
										$qstr = " $elname2 $sep '$value' ";
									}
								};
							};
						}
						else
						if ($el->arr["search_logical"])
						{
							$value = $el->arr["search_logical_prepend"]." ".$value." ".$el->arr["search_logical_append"];
//							echo "val= $value <br />";
							// here we try to parse the damn text entered in the element
							// let's try and do NOT / AND / OR / () here
							$qstr = "";
							$pics = explode(" ",$value);
//							echo "pics = <pre>", htmlentities(var_dump($pics)),"</pre> <br />";
							reset($pics);
							while (list(,$pic) = each($pics))
//							foreach($pics as $pic)
							{
//								echo "pic = $pic <br />";
								if ($pic == "")
								{
									continue;
								}
								$is_null = false;
								$is_not_null = false;
								$npic = "";
								if ($pic == ">")
								{
									while ($npic == "")
									{
										list(,$npic) = each($pics);
									}
									$qstr .=" $elname2 > $npic ";
									continue;
								}
								else
								if ($pic == "<")
								{
									while ($npic == "")
									{
										list(,$npic) = each($pics);
									}
									$qstr .=" $elname2 < $npic ";
									continue;
								}
								else
								if ($pic == ">=")
								{
									while ($npic == "")
									{
										list(,$npic) = each($pics);
									}
									$qstr .=" $elname2 >= $npic ";
									continue;
								}
								else
								if ($pic == "<=")
								{
									while ($npic == "")
									{
										list(,$npic) = each($pics);
									}
									$qstr .=" $elname2 <= $npic ";
									continue;
								}
								else
								if ($pic == "!=")
								{
									while ($npic == "")
									{
										list(,$npic) = each($pics);
									}
									$qstr .=" $elname2 != $npic ";
									continue;
								}
								else
								if ($pic == "AND")
								{
									$qstr .= " AND ";
								}
								else
								if ($pic == "OR")
								{
									$qstr .= " OR ";
								}
								else
								if ($pic == "IS_NULL")
								{
									$is_null = true;
									$qstr .= ' ';
								}
								else
								if ($pic == "IS_NOT_NULL")
								{
									$is_not_null = true;
									$qstr .= ' ';
								}
								else
								if ($pic == "NOT")
								{
									$nextnot = true;
								}
								else
								if ($pic == "(" || $pic ==")")
								{
									$qstr.=$pic;
								}
								else
								{
									if ($nextnot)
									{
										$nextnot = false;
										if ($el->arr["search_all_text"] != 1)
										{
											$qstr .= " $elname2 NOT LIKE '%$pic%' ";
										}
										else
										{
											$qstr .= " $elname2 != '$pic' ";
										}
									}
									else
									{
										if ($el->arr["search_all_text"] != 1)
										{
											$qstr .= " $elname2 LIKE '%$pic%' ";
										}
										else
										{
											$sep = " = ";
											if (strpos($pic, "%") !== false)
											{
												$sep = " LIKE ";
											}
											$qstr .= " $elname2 $sep '$pic' ";
										}
									}
								}

								if ($is_null)
								{
									$qstr .= " $elname2 IS NULL ";
								}
								else
								if ($is_not_null)
								{
									$qstr .= " $elname2 IS NOT NULL ";
								}
							}
//							echo "got qstr $qstr <br />";
							if ($qstr != "")
							{
								if ($query != "")
								{
									$query .= "AND ";
								}
								$query.= " ($qstr) ";
							}
						}
						else
						{
							if ($el->arr["search_field_in_set"] == 1)
							{
								$qstr = " FIND_IN_SET('$value',$elname2) ";
							}
							else
							if ($el->arr["search_all_text"] != 1)
							{
								$qstr = " $elname2 LIKE '%$value%' ";
							}
							else
							{
								$sep = " = ";
								if (strpos($value, "%") !== false)
								{
									$sep = " LIKE ";
								}
								if ($elname2 == "ut_struktuurid_364.3taseme_ylem_id" && $value == "" || $value == "%%")
								{
									$value = -1;
								}
								$qstr = " $elname2 $sep '$value' ";
							}
						}

						if ($query != "")
						{
							$query .= "AND ";
						}
						$query.= "($qstr)";
					}
				}
			}
		}

		// go through the checkbox groups and stick AND between groups and OR between checkboxes within a group
		foreach($ch_q as $chgrp => $ch_ar)
		{
			$chqs = join(" OR ", $ch_ar);
			if ($chqs !="")
			{
				if ($query != "")
				{
					$query.=" AND ";
				}
				$query.=" ($chqs)";
			}
		}

		return $query;
	}

	////
	// !returns an array of entry_id => entry_name pairs for form 
	// $id - if specified, otherwise the loaded form
	// $parent - if specified, only entries under these folders are returned
	// $all_data - if specified, all data for entry is returned
	// $addempty - if true, empty element is prepended
	// $user - if set, only that user's entries are returned
	// $max_lines - if set, only that many lines are returned
	// $chain_id - if set, only entried with that chain_id are returned
	function get_entries($args = array())
	{
		$this->save_handle();
		extract($args);
		$ret = array();
		if ($addempty)
		{
			$ret[""] = "";
		}
		$fid = ($id) ? $id : $this->id;
		$form =& $this->cache_get_form_instance($fid);

		// filter by parent if specified
		$pstr = ($parent) ? " AND objects.parent IN (" . join(",",map("'%s'",$parent)) . ")" : "";

		if ($user != "")
		{
			$pstr.=" AND objects.createdby = '$user' ";
		}

		if ($max_lines != "")
		{
			$lim = " LIMIT $max_lines ";
		}

		// if the form writes to tables, get entries from there
		if ($form->arr["save_table"] == 1)
		{
			// here we ought to find the elements that are used to name the entries and select their values from 
			// the tables that they are in. 

			// ok, we crap out and to the easy version only, this will be a 
			// FIXME : implement this
		}
		else
		{
			$table = sprintf("form_%d_entries",$fid);
			if ($chain_id)
			{
				$ch = " AND ".$table.".chain_id = '".$chain_id."' ";
			}
			$q = "SELECT objects.oid as oid,$table.id as entry_id,objects.name as name,objects.parent as parent, $table.* FROM $table LEFT JOIN objects ON ($table.id = objects.oid) WHERE objects.status != 0 $pstr $ch ORDER BY objects.oid $lim";
			$this->db_query($q);
			while ($row = $this->db_next())
			{
				if ($all_data)
				{
					$ret[] = $row;
				}
				else
				{
					$ret[$row["oid"]] = $row["name"];
				}
			}
		}
		$this->restore_handle();
		return $ret;
	}

	////
	// !finds the path from form $f_from to $f_to in the join tree created previously
	function get_join_path($f_from, $f_to)
	{
		// we do this in a pretty slow way -
		// search through the form relations tree ($this->form_rel_tree)
		// it is assumed, that the root element for the tree is $f_from
		// and the tree contains all the relations for the forms in it.
		// well - I see no way of avoiding this exhaustive search, and it really does not take that long
		//$this->_clear_stack("join_path");
		$this->join_path = array();
		$this->rgjp_beenthere = array();
		if ($this->arr["join_optimizer_pessimist"])
		{
			$ret = $this->req_get_join_path_pessimistic($f_from, $f_to);
		}
		else
		{
			$ret = $this->req_get_join_path($f_from, $f_to);
		}
		
		if ($ret)
		{
			return $this->join_path;
		}
		// FIXME: this error message needs to be able to point it out to the user where the link broke
		// or at least give some hint towards the breakage
		//$this->raise_error(ERR_FG_NOFORMRELS, "Ei suuda leida seost formide $f_from ja $f_to  vahel!", false,true);
		return $this->join_path;
	}

	function req_get_join_path($f_root, $f_to)
	{
		if ($this->rgjp_beenthere[$f_root])
		{
			return;
		}
		$this->rgjp_beenthere[$f_root] = $f_root;
		array_push($this->join_path,$f_root);
		if (is_array($this->form_rel_tree[$f_root]))
		{
			foreach($this->form_rel_tree[$f_root] as $r_fid => $r_data)
			{
				if ($r_fid == $f_to)	// we found the end, get out of here
				{
					array_push($this->join_path,$f_to);
					return true;
				}
			}
			// we req only if we don't find a direct relation, because
			// we only need to req if there is no direct relation!
			foreach($this->form_rel_tree[$f_root] as $r_fid => $r_data)
			{
				if ($this->req_get_join_path($r_fid, $f_to) == true)
				{
					return true;
				}
			}
		}
		array_pop($this->join_path);
		return false;
	}

	function req_get_join_path_pessimistic($f_root, $f_to)
	{
		if ($this->rgjp_beenthere[$f_root])
		{
			return;
		}
		$this->rgjp_beenthere[$f_root] = $f_root;
		array_push($this->join_path,$f_root);
		if (is_array($this->form_rel_tree[$f_root]))
		{
			foreach($this->form_rel_tree[$f_root] as $r_fid => $r_data)
			{
				if ($r_fid == $f_to)	// we found the end, get out of here
				{
					array_push($this->join_path,$f_to);
					return true;
				}
				if ($this->req_get_join_path_pessimistic($r_fid, $f_to) == true)
				{
					return true;
				}
			}
		}
		array_pop($this->join_path);
		return false;
	}

	////
	// !this creates the form relations tree, starting from $f_root and puts it in $this->form_rel_tree
	// if $chain is specified, $f_root is assumed to be a member of chain $chain and other chains it belongs to are ignored
	// $this->form_rel_tree is an array - index is the form id and value is an array of relations
	// from the key form index is the related form id and value is an array of the relation data
	function build_form_relation_tree($f_root, $chain = 0, $no_reverse_rels = false)
	{
		$this->_fr_forms_used = array();
		$this->form_rel_tree = array();
		// REMOVE THIS $this->form2first_form_in_chain = array();

		$frfo = aw_global_get("_fr_forms_used::".((int)$f_root)."::".((int)$no_reverse_rels));
		$frt = aw_global_get("form_rel_tree::".((int)$f_root)."::".((int)$no_reverse_rels));
		// REMOVE THIS $ffc = aw_global_get("form2first_form_in_chain::".((int)$f_root)."::".((int)$no_reverse_rels));
		if (is_array($frfo) && is_array($frt) && false)
		{
			$this->_fr_forms_used = $frfo;
			$this->form_rel_tree = $frt;
			// REMOVE THIS $this->form2first_form_in_chain = $ffc;
			return;
		}

		$this->req_build_form_relation_tree($f_root, $no_reverse_rels);
		aw_session_set("_fr_forms_used::".((int)$f_root)."::".((int)$no_reverse_rels),$this->_fr_forms_used);
		aw_session_set("form_rel_tree::".((int)$f_root)."::".((int)$no_reverse_rels), $this->form_rel_tree);
		// REMOVE THIS aw_session_set("form2first_form_in_chain::".((int)$f_root)."::".((int)$no_reverse_rels), $this->form2first_form_in_chain);
//		echo "built form relations tree, starting from $f_root: <br /><pre>", var_dump($this->form_rel_tree),"</pre> <br />";
	}

	////
	// and this is where things get really bloody screwed up. this includes a whole bunch of forms that
	// we don't need. 
	// so the only way to avoid loading them is to check the elements that we need for showing
	// and only join the tables that contain those elements and of course the tables that join them together
	//
	// the question is - how the hell do we do this?
	// since we don't know through which tables we must go to get from the first element to the last
	// we can't easily skip any - we can only do that if we have found all the elements (then we can skip the rest)
	// but the order in which we find the forms might leave the form with the one needed element as the last one
	// so we will end up loading all of the forms anyway that we don't need. 
	// 
	// hm. but what if we could figure out the relations without loading the forms - then we could prune the 
	// unnecessary branches from the relation tree and only load the forms that are actually needed
	//
	// or we could do this in the reverse direction - start from the first form (start_search_relations_from), load it
	// then try to do the query and find that we need to get the join path to another element
	// so, load the form that the needed element is in and check if it is related to the first form
	// if not, recurse on all of it's relations until we end up with the path to the first form and then stop the search
	// the question here of course is - will this load any less forms? well, if all elements are only 1 step away then, yes, 
	// we will only load the necessary forms. but when the distance is more than one step we hit uncertainty again, 
	// cause we don't know the correct order in which to go through the relations.
	//
	// bloody hell this is complicated
	//
	// hmm. ok, so. I propose this. first we read in all the relations from the database (form_relations table)and 
	// don't load any forms and based on this data, we can build the join tree. 
	//		this tree can also be easily cached
	//		the cache would get reset every time any form changes - this is bad, but since I hope that loading just the
	//		rels is relatively fast it sould not be that bad
	// now. we start building the join paths to the needed elements and do the form loading on demand - loading only
	// the forms that are touched by the join paths (we need to load those, because we need to know to what tables they write)
	// and so we should be able to only do the expensive form loads for the forms that contain the elements that we actually 
	// need to query. 
	// and maybe we don't even have to load the forms - I wonder would it be faster to have a table
	// that contains has a row for each (element_id,form_id) and the tables/cols where the element writes data
	// on systems with looots of forms this could become quite big and maybe querying it for all the elements
	// would be slower than loading the forms, especially if we need to do very few joins. 
	// or maybe have a table that has a row for each form and that contains an array that specifies the elements' tables and cols
	// then we could do one query for all the forms and simply unserialize the data, avoiding the quite expensive creation
	// of form element instances for each form. hm. perhaps instead of having a separate table we could just have an argument
	// to form_load that says, don't create elements, just read the data structure. well, but still that will contain a lot 
	// of data that we don't need here. 
	//
	// so, I decided finally on this: we load the rel tree from the database without touching
	// any forms. then we start building the query from it and if we need to know something
	// about some form, then we load the version that is stored in el_tables - just the elements
	// and the tables where they write to and the table connections for the form - 
	// - and it seems to be pretty damn fast :)
	// it can build joins for 5 tables out of a relation tree that contains ~40 forms in 0.3 seconds.
	// - and that includes reading the relations from the db and reading the form descriptions from the db
	// unserializing them and processing the whole mess. 
	// pretty cool. 
	function req_build_form_relation_tree($f_root, $no_reverse_rels = false)
	{
		// let's not do this twice for a form
		if ($this->_fr_forms_used[$f_root] == true)
		{
			return;
		}
		$this->_fr_forms_used[$f_root] = true;

		$this->save_handle();

		// check if the form is a member of any chain
		$this->db_query("SELECT * FROM form2chain WHERE form_id = '$f_root'");
		while ($row = $this->db_next())
		{
			// if it is, then load all forms for the chain
			$chain_forms = $this->get_forms_for_chain($row["chain_id"]);
			//$first = true;
			foreach($chain_forms as $chfid)
			{
				/*if ($first)
				{
					$first_form_in_chain = $chfid;
				}
				$this->form2first_form_in_chain[$chfid] = $first_form_in_chain;
				$first = false;*/
				if ($chfid == $f_root)
				{
					continue;
				}

				// and for each form, mark the relation 
				$this->form_rel_tree[$f_root][$chfid] = array("form_from" => $f_root, "el_from" => "chain_id", "form_to" => $chfid, "el_to" => "chain_id");

				// and recurse if the form is not used already
				if (!$this->_fr_forms_used[$chfid])
				{
					$this->req_build_form_relation_tree($chfid);
				}
			}
		}

		// detect relation elements one way - from the listbox to the related element
		$q = "SELECT * FROM form_relations LEFT JOIN objects ON objects.oid = form_relations.form_from LEFT JOIN forms ON forms.id = form_relations.form_from WHERE form_to = '$f_root' AND objects.status != 0 AND forms.type = ".FTYPE_ENTRY;
		$this->db_query($q);
		while ($row = $this->db_next())
		{
			$this->form_rel_tree[$f_root][$row["form_from"]] = array("form_from" => $f_root, "el_from" => $row["el_to"], "form_to" => $row["form_from"], "el_to" => $row["el_from"]);

			if (!$this->_fr_forms_used[$row["form_from"]])
			{
				$this->req_build_form_relation_tree($row["form_from"]);
			}
		}

		if (!$no_reverse_rels)
		{
			// reverse relation elements detecting - from related element to listbox
			$q = "SELECT * FROM form_relations LEFT JOIN objects ON objects.oid = form_relations.form_to LEFT JOIN forms ON forms.id = form_relations.form_to WHERE form_from = '$f_root' AND objects.status != 0 AND forms.type = ".FTYPE_ENTRY;
			$this->db_query($q);
			while ($row = $this->db_next())
			{
				$this->form_rel_tree[$f_root][$row["form_to"]] = array("form_from" => $f_root, "el_from" => $row["el_from"], "form_to" => $row["form_to"], "el_to" => $row["el_to"]);
				if (!$this->_fr_forms_used[$row["form_to"]])
				{
					$this->req_build_form_relation_tree($row["form_to"]);
				}
			}
		}
		$this->restore_handle();
	}

	////
	// !returns the data that make_sql_where_clause gave it. 
	// it is an array that contains the forms that are used in the search where clause
	function get_forms_used_in_where()
	{
		if (!is_array($this->forms_used_in_where))
		{
			return array();
		}
		return $this->forms_used_in_where;
	}

	////
	// !this builds the $this->_joins array from the path through the join tree that is given to it
	// what that means is, that the relation tree contains relations between forms, then
	// get_join_path figures out the path from one form to another, still via forms
	// and finally, this function builds the _joins array, that contains the same path, 
	// but now instead of containing relations between forms, it contains relations
	// between sql tables 
	function build_join_rels_from_path($path)
	{
		if (!is_array($path))
		{
			return;
		}
//	echo "build_join_rels_from_path path eq <pre>", var_dump($path),"</pre> <br />";
		reset($path);
		while(list(,$fid) = each($path))
		{
//			echo "got $fid from path <br />";
			// get next from path
			if (!list(,$n_fid) = each($path))
			{
				// we are at the last form - so exit
//				echo "returnig because of eop <br />";
				return;
			}

			// now move pointer back one, so we can build the nex rel next time in the loop
			prev($path);

			$f_dat = $this->cache_get_form_eldat($fid);
			$t_dat = $this->cache_get_form_eldat($n_fid);
//			echo "eldat = $n_fid = <pre>", var_dump($t_dat),"</pre> <br />";
		
//			echo "get from f_dat elfrom = ",$this->form_rel_tree[$fid][$n_fid]["el_from"]," <br />";
			$f_el = $f_dat["els"][$this->form_rel_tree[$fid][$n_fid]["el_from"]];
			$t_el = $t_dat["els"][$this->form_rel_tree[$fid][$n_fid]["el_to"]];
//			echo "f_el = <pre>", var_dump($f_el),"</pre> <br />";
//			echo "join from $fid to $n_fid , from el = $f_el[table] $f_el[col] , to el = $t_el[table] $t_el[col] <br />";

			if ($this->form_rel_tree[$fid][$n_fid]["el_from"] == "chain_id")
			{
				$from_tbl = form_db_base::mk_tblname("form_".$fid."_entries",$fid);
				$from_el = "chain_id";
			}
			else
			{
				$from_tbl = form_db_base::mk_tblname($f_el["table"],$fid);
				$from_el = $f_el["col"];
			}

			if ($this->form_rel_tree[$fid][$n_fid]["el_to"] == "chain_id")
			{
				$to_tbl = form_db_base::mk_tblname("form_".$n_fid."_entries", $n_fid);
				$to_el = "chain_id";
			}
			else
			{
				$to_tbl = form_db_base::mk_tblname($t_el["table"],$n_fid);
				$to_el = $t_el["col"];
			}

//			echo "from table = ", $f_el["table"]," from el = ", $f_el["col"]," to table = ", $t_el["table"]," to col = ",$t_el["col"]," <br />";
			// and mark down the join
			if ($from_tbl == "" && $from_el == "")
			{
				// if the join is broken, mark it for the fetch data part not to fetch from the joined form
				$this->do_not_fetch[$n_fid] = $n_fid;
			}
			else
			{
				$this->_joins[] = array(
					"from_tbl" => $from_tbl, 
					"from_el" =>  $from_el,
					"to_tbl" => $to_tbl,
					"to_el" => $to_el
				);
			}

			if ($f_el)
			{
				$this->table2form_map[form_db_base::mk_tblname($f_el["table"],$fid)] = $fid;
			}
			else
			{
				$this->table2form_map[form_db_base::mk_tblname("form_".$fid."_entries",$fid)] = $fid;
			}

			if ($t_el)
			{
				$this->table2form_map[form_db_base::mk_tblname($t_el["table"],$n_fid)] = $n_fid;
			}
			else
			{
				$this->table2form_map[form_db_base::mk_tblname("form_".$n_fid."_entries",$n_fid)] = $n_fid;
			}
		}
	}

	////
	// !returns an array of distinct values for the element $rel_element in form $rel_form
	// return value is array, first member is the number of elements in the result
	// and the next member is the result array
	// this is most heavily used in getting the contents for relation elements
	// arguments:
	// rel_form - the form that the element is in
	// rel_element - the element whose values we must read
	// sort_by_alpha - if true, results will be sorted 
	// rel_unique - if true, only distinct values are returned
	// ret_values - if set, the return value is just the result array
	// ret_ids - if set, array index is entry_id
	// user_entries_only - if set, only entries that were created by the current user are returned
	// chain_entries_only - if set, only chain entries are returned
	// limit_chain_id - if set, only entries with that chain id are returned
	// gefe_add_empty - if set, first element is empty element
	function get_entries_for_element($args)
	{
		extract($args);
		$this->save_handle();

		$where = "";
		$order_by = "";

		$inst = $this->cache_get_form_eldat($rel_form);
		if ($inst["save_table"] == 1)
		{
			$rel_tbl = $inst["els"][$rel_element]["table"];
			$rel_el = $rel_tbl.".".$inst["els"][$rel_element]["col"];
			$id_col = $inst["save_table_data"][$rel_tbl]." as id,";
			$join = "";
			if ($el_sort_by)
			{
				$order_by = " order by ".$inst["els"][$el_sort_by]["table"].".".$inst["els"][$el_sort_by]["col"];
			}
		}
		else
		{
			$rel_tbl = "form_".$rel_form."_entries";
			$rel_el = $rel_tbl.".ev_".$rel_element;
			$id_col = $rel_tbl.".id as id,";
			$join = " LEFT JOIN objects ON objects.oid = ".$rel_tbl.".id  WHERE objects.status != 0 ";
			if ($user_entries_only)
			{
				// if you are in the exclude group, ignore
				$awa = new aw_array($user_entries_only_exclude);
				$t = array_intersect($awa->get(), array_values(aw_global_get("gidlist")));
//				echo "t = ".dbg::dump($t)." gl = ".dbg::dump(aw_global_get("gidlist"))." awa = ".dbg::dump($awa)." <br>";
				if (count($t) < 1)
				{
					$where = " AND objects.createdby = '".aw_global_get("uid")."' ";
				}
			}
			if ($chain_entries_only)
			{
				$where .= " AND ".$rel_tbl.".chain_id IS NOT NULL ";
				if ($limit_chain_id)
				{
					$where .= " AND ".$rel_tbl.".chain_id = '$limit_chain_id' ";
				}
			}
			if ($this->arr["is_translatable"])
			{
				$where .= " AND ".$rel_tbl.".lang_id = '".aw_global_get("lang_id")."' ";
			}
			
			if ($el_sort_by)
			{
				$order_by = " order by el_".$el_sort_by;
			}
		}

		if ($sort_by_alpha && $rel_el != "" && $order_by == "")
		{
			$order_by = " ORDER BY $rel_el ";
		}
		else
		if ($sort_by_alpha && $rel_el && $order_by != "")
		{
			$order_by .= ", $rel_el ";
		}

		$gpby = "";
		if ($rel_unique == 1)
		{
//			$rel_el = "distinct(".$rel_el.")";
//			$id_col = "";
			$gpby = " GROUP BY $rel_el ";
		}

		$q = "SELECT $id_col $rel_el as el_val FROM ".$rel_tbl.$join.$where.$gpby.$order_by;
		if ($GLOBALS["fg_dbg"] == 2) echo "_grlc q = $q <br />";

		// try to read the result from the cache
/*		if (($ret = aw_cache_get("get_entries_for_element_cache", $q.((int)$ret_values).((int)$ret_ids))))
		{
			return $ret;
		}*/

		$cnt=0;
		if ($gefe_add_empty)
		{
			$result = array("0" => "");
			$cnt = 1;
		}
		else
		{
			$result = array();
		}

		$this->db_query($q);
		while($row = $this->db_next())
		{
			if ($ret_ids)
			{
				$result[$row["id"]] = $row["el_val"];
			}
			else
			{
				$result[$cnt++] = $row["el_val"];
			}
		}

		$this->restore_handle();
		if ($ret_values)
		{
			aw_cache_set("get_entries_for_element_cache", $q.((int)$ret_values), $result);
			return $result;
		}
		else
		{
			aw_cache_set("get_entries_for_element_cache", $q.((int)$ret_values), array($cnt,$result));
			return array($cnt,$result);
		}
	}

	////
	// !creates the GROUP BY sql part for search
	function get_sql_grpby($grpby)
	{
		$gpb = array();
		if (!is_array($grpby))
		{
			return false;
		};

		foreach($grpby as $fid => $fdat)
		{
			if ($fid)
			{
				$finst = $this->cache_get_form_eldat($fid);
				foreach($fdat as $elid)
				{
					$gpb[] = form_db_base::mk_tblcol($finst["els"][$elid]["table"],$finst["els"][$elid]["col"],$fid);
				}
			}
			else
			{
				if (in_array("entry_id", $fdat))
				{
					$gpb[] = "entry_id";
				}
			}
		}

		$ret = join(",", $gpb);
		if ($ret != "")
		{
			$ret = " GROUP BY ".$ret;
		}
		return $ret;
	}

	////
	// !reads the lightweight form descriptions (just the element id's and the tables they write to) 
	// from the db and also caches them. if no lightweight description exists, emits an error 
	// telling the user what to do create the description (he needs to save the damn form again)
	// $fid - the form whose description we want to read
	function cache_get_form_eldat($fid)
	{
		if (!$fid)
		{
			return array();
		}

		if (is_array($dt = aw_cache_get("cache_get_form_eldat",$fid)))
		{
			return $dt;
		}
		$res = $this->db_fetch_field("SELECT el_tables FROM forms WHERE id = $fid","el_tables");
		$dat = aw_unserialize($res);
		if (!is_array($dat) || !is_array($dat["els"]))
		{
			$this->raise_error(0,sprintf(t("Please %sgo here</a> and click the save button"), "<a href='".aw_ini_get("baseurl")."/automatweb/orb.aw?class=form&action=change&id=$fid'>"), true);
		}
		aw_cache_set("cache_get_form_eldat",$fid,$dat);
		return $dat;
	}

	////
	// !adds object status checking to the where query if forms create objects
	// this is separate, for performance reasons - we need to do the where bit
	// before any others and we don't know whether to add the object table there yet.
	function get_sql_where_objects_part($query)
	{
		if ($this->has_obj_table)
		{
			if ($query != "")
			{
				$query.=" AND ";
			}
			$fid = $this->table2form_map[$this->sql_from_table];
			$dat = $this->cache_get_form_eldat($fid);
			if ($dat["is_translatable"])
			{
				$query .= " ".$this->sql_from_table.".lang_id = '".aw_global_get("lang_id")."' AND ";
			}
			$query.=" objects.status != 0 ";
			if ($this->arr["search_act_lang_only"])
			{
				$query.=" AND objects.lang_id = '".aw_global_get("lang_id")."'";
			}
		}

		if ($query != "")
		{
			$query = " WHERE ".$query;
		}
		return $query;
	}

	////
	// !makes the table name for table $tbl, in form $frm
	// - why is this? well, all the tables get renamed in the query to tablename_formid
	// - that allows us to join the same table twice, provided that it has two separate forms
	// - that write to it - using that we can even do self-joins
	// static
	function mk_tblname($tbl,$frm)
	{
		if ($tbl == "" || !$frm)
		{
			return "";
		}
		return $tbl."_".$frm;
	}

	////
	// !makes the table and col name for table $tbl, col $col , in form $frm
	// static
	function mk_tblcol($tbl,$col,$frm)
	{
		if ($tbl != "" && $frm != "" && $col != "")
		{
			return $tbl."_".$frm.".".$col;
		}
		return "";
	}

	////
	// !extracts the real table name from the mangled table name
	// (removes _formid part)
	function get_rtbl_from_tbl($tbl)
	{
		$_p = strrpos($tbl,"_");
		if ($_p === false)
		{
			return $tbl;
		}
		return substr($tbl,0,$_p);
	}

	function prune_relation_tree()
	{
		$this->pruned_forms = array();
		if (is_array($this->arr["leave_out_joins"]))
		{
			$nrt = array();
			foreach($this->form_rel_tree as $_ff_id => $_td)
			{
				foreach($_td as $_tf_id => $_jdat)
				{
					$relid = "rel_".$_jdat["form_from"]."_".$_jdat["form_to"]."_".$_jdat["el_from"]."_".$_jdat["el_to"];
					if ($this->arr["leave_out_joins"][$relid] != $relid)
					{
						$nrt[$_ff_id][$_tf_id] = $_jdat;
					}
					else
					{
						$this->pruned_forms[$_tf_id] = $_tf_id;
					}
				}
			}
			$this->form_rel_tree = $nrt;
		}
	}

	function get_sql_orderby($sort_by, $sort_order)
	{
		if (!is_array($sort_by))
		{
			return " ";
		}
		
		$str = "";
		$first = true;
		foreach($sort_by as $nr => $el)
		{
			// get the related element
			$el = $this->get_element_by_id($el);
				
			$le = $el->arr["linked_element"];
			$lf = $el->arr["linked_form"];
			if ($le && $lf)
			{
				$finst = $this->cache_get_form_eldat($lf);
				if (!$first)
				{
					$str .= ",";
				}
				$str .= form_db_base::mk_tblcol($finst["els"][$le]["table"],$finst["els"][$le]["col"], $lf)." ".$sort_order[$nr]." ";
				$first = false;
			}
		}

		if ($str != "")
		{
			return " ORDER BY ".$str;
		}
		return $str;
	}
}
?>
