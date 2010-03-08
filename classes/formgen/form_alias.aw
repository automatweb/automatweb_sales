<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_alias.aw,v 1.14 2008/08/27 07:56:15 kristo Exp $
/*
@classinfo  maintainer=kristo
*/

classload("formgen/form");
class form_alias extends form_base
{
	function form_alias()
	{
		$this->form_base();
	}

	/**  
		
		@attrib name=new params=name default="0"
		
		@param return_url required
		@param parent optional
		@param alias_to optional
		@param sf optional
		@param entry_id optional
		@param form_submit optional
		@param id optional
		
		@returns
		
		
		@comment

	**/
	function do_new($arr)
	{
		return $this->new_entry_alias($arr);
	}
	/**  
		
		@attrib name=new_entry_alias params=name default="0"
		
		@param return_url required
		@param parent optional
		@param alias_to optional
		@param sf optional
		@param entry_id optional
		@param form_submit optional
		@param id optional
		
		@returns
		
		
		@comment

	**/
	function new_entry_alias($arr)
	{
		extract($arr);
		$this->read_template("add_form_alias.tpl");
		$this->mk_path(0, "<a href='".$return_url."'>Tagasi</a> / Lisa sisestuse alias");

		if ($form_submit)
		{
			global $HTTP_GET_VARS;
			$f = get_instance(CL_FORM);
			$f->process_entry(array(
				"id" => $sf,
				"entry_id" => $entry_id,
				"values" => $_GET
			));

			$entry_id = $f->entry_id;
		}

		if ($sf)
		{
			$f = get_instance(CL_FORM);
			$form = $f->gen_preview(array(
				"id" => $sf,
				"reforb" => $this->mk_reforb("new_entry_alias",array("no_reforb" => true,"parent" => $parent, "return_url" => $return_url,"sf" => $sf,"entry_id" => $entry_id,"form_submit" => true,"alias_to" => $alias_to,"id" => $id),"form_alias"),
				"entry_id" => $entry_id,
				"form_action" => "orb.".$this->cfg["ext"],
				"method" => "GET"
			));

			if ($entry_id)
			{
				$entry = $f->show(array(
					"id" => $sf,
					"entry_id" => $entry_id,
					"op_id" => 1
				));
			}
		}

		$this->vars(array(
			"reforb" => $this->mk_reforb("new_entry_alias", array("no_reforb" => true, "parent" => $parent, "return_url" => $return_url,"alias_to" => $alias_to,"id" => $id)),
			"sfs" => $this->picker($sf,$this->get_flist(array("type" => FTYPE_SEARCH))),
			"form" => $form,
			"entry" => $entry,
			"a_reforb" => $this->mk_reforb("submit_entry_alias", array("parent" => $parent, "return_url" => $return_url,"sf" => $sf, "entry_id" => $entry_id,"alias_to" => $alias_to,"id" => $id))
		));
		$this->vars(array("results" => $this->parse("results")));
		if ($form != "")
		{
			$this->vars(array("show_form" => $this->parse("show_form")));
		}
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_entry_alias params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_entry_alias($arr)
	{
		extract($arr);

		if ($alias_to)
		{
			$o = obj($alias_to);
			$o->connect(array(
				"to" => $entry_id,
				"data" => serialize(array("type" => "show", "output" => 1, "form_id" => $sf))
			));
		}
		else
		if ($id)
		{
			// change alias
			$c = new connection($id);
			$c->change(array(
				"to" => $entry_id,
				"data" => serialize(array("type" => "show", "output" => 1, "form_id" => $sf))
			));
		}

		return $return_url;
	}

	/**  
		
		@attrib name=change_entry_alias params=name default="0"
		
		@param id required
		@param return_url required
		
		@returns
		
		
		@comment

	**/
	/**  
		
		@attrib name=change params=name default="0"
		
		@param id required
		@param return_url required
		
		@returns
		
		
		@comment

	**/
	function change_entry_alias($arr)
	{
		extract($arr);

		$this->db_query("SELECT * FROM aliases WHERE target = '$id'");
		$ret = $this->db_next();
		$dat = aw_unserialize($ret["data"]);

		return $this->new_entry_alias(array(
			"sf" => $dat["form_id"],
			"return_url" => $return_url,
			"entry_id" => $id,
			"id" => $ret["id"]
		));
//		$this->mk_path(0, "<a href='".$return_url."'>Tagasi</a> / Muuda sisestuse aliast");
//		return $this->parse();
	}

	///
	// !Kasutatakse ntx dokumendi sees olevate aliaste asendamiseks. Kutsutakse v2lja callbackina
	function parse_alias($args = array())
	{
		extract($args);
		$GLOBALS["cur_process_alias"] = $alias["from"];
		$alias_data = unserialize($alias["data"]);
		$fo = get_instance(CL_FORM);

		aw_global_set("fg_cur_processing_alias_data", array(
			"id" => $alias_data["form_id"],
			"entry_id" => $alias["target"],
			"op_id" => $alias_data["output"]
		));
		
		if ($alias_data["type"] == "show")
		{
			$replacement = $fo->show(array(
				"id" => $alias_data["form_id"],
				"entry_id" => $alias["target"],
				"op_id" => $alias_data["output"]
			));
		}
		else
		{
			$replacement = $fo->gen_preview(array(
				"id" => $alias_data["form_id"],
				"entry_id" => $alias["target"],
			));
		}
		if (is_object($args["oid"]))
		{
			$args["oid"] = $args["oid"]->id();
		}
		if (aw_global_get("section") != $args["oid"])
		{
			//$replacement = str_replace(aw_global_get("section"),$args["oid"], $replacement);
		}
		return $replacement;
	}

	function callback_alias_cache_get_url_hash()
	{
		$ru = preg_replace('/tbl_sk=[^&$]*/','',aw_global_get("REQUEST_URI"));
		$ru = preg_replace('/old_sk=[^&$]*/','',$ru);
		// also insert current user's groups in the url. yeah yeah I know that we could do with less caches, but what the hell
		$ru .= "&gid=".join(",",aw_global_get("gidlist"));
		return gen_uniq_id($ru);
	}

}
?>
