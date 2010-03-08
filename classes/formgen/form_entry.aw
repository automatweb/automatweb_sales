<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_entry.aw,v 1.12 2008/01/31 13:54:34 kristo Exp $

// basically this is an interface class :)
// it provides a form_entry manipulating interface to menueditor via orb. 
// but it doesn't contain any of the functionality, it just forwards calls to class form
// well, ok, not an interface class in it's purest meaning, but still pretty cool

/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_FORM_ENTRY, on_add_alias)

*/
/*
@classinfo  maintainer=kristo
*/

class form_entry extends aw_template
{
	function form_entry()
	{
		$this->db_init();
		$this->tpl_init("forms");
		lc_load("definition");
		$this->lc_load("form", "lc_form");
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
		$fid = $this->db_fetch_field("SELECT form_id FROM form_entries WHERE id = $id", "form_id");


		$o = obj($id);
		$this->mk_path($o->parent(), LC_FORM_ENTRY_CHANGE_ENTRY);

		$f = get_instance(CL_FORM);
		return $f->gen_preview(array("id" => $fid, "entry_id" => $id));
	}

	/**
		@attrib name=new all_args=1
	**/
	function orb_new($arr)
	{
		$i = get_instance("formgen/form_alias");
		return $i->do_new($arr);
	}

	////
	// !fetchib info mingi entry kohta
	// argumendid:
	// eid(int) - entry id
	function get_entry($args = array())
	{
		extract($args);
		// koigepealt teeme kindlaks, millise vormi juurde see entry kuulub
		$fid = $this->db_fetch_field("SELECT form_id FROM form_entries WHERE id = '$eid'","form_id");
		
		$entry = $this->get_record("form_" . $fid . "_entries","id",$eid);
		
		// if it is part of a chain, then fetch all the other entries as well
		if ($entry["chain_id"])
		{
			$chain_entry = $this->get_record("form_chain_entries","id",$row["chain_id"]);
			$els = aw_unserialize($chain_entry["ids"]);
		}
		else
		{
			$els = array($fid => $eid);
		};
 
		$block = array();

		foreach($els as $form_id => $entry_id)
		{
			$block = $block + $this->get_record("form_" . $form_id . "_entries","id",$entry_id);
		};
		return $block;
	}

	////
	// !Teeb entryst koopia
	// argumendid:
	// eid (int), entry_id, mida kopeerida
	// parent (int), mille alla uu koopia teha. Kui defineerimata, siis jääb samasse kohta
	function cp($args = array())
	{
		extract($args);
		// koigepealt registreerime uue objekti.
		$old = obj($eid);
		// üle kanname koik andmed, parent-i asendame
		if ($args["parent"])
		{
			$old->set_parent($args["parent"]);
		};
		// acl-iga voib kamm tekkida.
		$new_id = $old->save_new();
	
		$oldentry = $this->get_record("form_entries","id",$eid);

		$q = "INSERT INTO form_entries(id,form_id) VALUES ('$new_id','$oldentry[form_id]')";
		$this->db_query($q);

		$ftable = sprintf("form_%s_entries",$oldentry["form_id"]);

		$old_f_entry = $this->get_record($ftable,"id",$eid);

		$old_f_entry["id"] = $new_id;

		$keys = array(); $values = array();
		foreach($old_f_entry as $key1 => $value1)
		{
			// numbrilisi key-sid ei kopeeri
			// rec pannakse db_next-s sisse. kas seda üldse kusagil kasutatakse ka?
			if ( (!is_numeric($key1)) && ($key1 != "rec") )
			{
				$keys[] = $key1;
				$this->quote($value1);
				$values[] = "'$value1'";
			};
		};

		$q = sprintf("INSERT INTO $ftable (%s) VALUES (%s)",join(",",$keys),join(",",$values));
		
		$this->db_query($q);
		
		return $new_id;

	}

	////
	// !adding alias to document support
	// we must let the user select whether he wants to view or edit the entry
	function on_add_alias($arr)
	{
		extract($arr);
		$this->mk_path($al["parent"],"<a href='".$this->mk_my_orb("list_aliases", array("id" => $arr["connection"]->prop("from")),"aliasmgr")."'>Tagasi</a> / Vali aliase t&uuml;&uuml;p");
		$this->read_template("alias_type.tpl");

		$fb = get_instance("formgen/form_base");
		$form = $fb->get_form_for_entry($arr["connection"]->prop("to"));

		$opl = $fb->get_op_list($form);

		$this->vars(array(
			"op_sel" => $this->picker("", $opl[$form]),
			"reforb" => $this->mk_reforb("submit_select_alias", array("docid" => $docid, "alias" => $arr["connection"]->prop("from"), "form_id" => $form))
		));
		$ret = $this->parse();
		return $ret;
	}

	function submit_select_alias($arr)
	{
		extract($arr);
		$o = obj($docid);
		$o->connect(array(
			"to" => $alias,
			"data" => serialize(array("type" => $type, "output" => $output, "form_id" => $form_id))
		));
		return $this->mk_my_orb("list_aliases",array("id" => $id),"aliasmgr");
	}

	function on_delete_hook($eid)
	{
		// mark the deleted column in the form table
		$f = get_instance(CL_FORM);
		$fid = $f->get_form_for_entry($eid);
		if ($fid)
		{
			$this->db_query("UPDATE form_".$fid."_entries SET deleted = 1 WHERE id = '$eid'");
		}
	}
}
?>
