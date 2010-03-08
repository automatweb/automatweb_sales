<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_import.aw,v 1.9 2008/01/31 13:54:34 kristo Exp $
classload("formgen/form_base");
/*
@classinfo  maintainer=kristo
*/
class form_import extends form_base
{
	function form_import()
	{
		$this->form_base();
		$this->sub_merge = 1;
		lc_load("definition");
		$this->lc_load("form","lc_form");
	}

	/** shows the form which allows user to select a csv file and upload it. 
		
		@attrib name=import_form_entries params=name default="0"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function import_form_entries($arr)
	{
		extract($arr);
		$this->read_template("import_entries.tpl");
		$o = obj($id);
		$this->mk_path($o->parent(), "<a href='".$this->mk_my_orb("change", array("id" => $id),"form")."'>Muuda formi</a> / Impordi sisetusi");

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_form", array("id" => $id))
		));
		return $this->parse();
	}

	/** receives the uploaded file and moves it into a temporary directory for further processing 
		
		@attrib name=submit_form params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_form($arr)
	{
		extract($arr);
		global $file,$file_type;
		if (!is_uploaded_file($file))
		{
			$this->raise_error(ERR_FG_NOFILE,LC_FORM_IMPORT_NOT_FILE_SELECTED,true);
		}

		// liigutame faili kuskile seifi kohta ja j2tame meelde selle
		$fname = gen_uniq_id();

		// peaks ka kontrollima, kas tmpdir on ikka olemas ja kirjutatav
		move_uploaded_file($file,aw_ini_get("server.tmpdir")."/".$fname);

		return $this->mk_my_orb("select_form_els", array("id" => $id, "file" => $fname));
	}

	/** Step 2 of import, shows a combined table of the fields from the csv file and and all form 
		
		@attrib name=select_form_els params=name default="0"
		
		@param id required
		@param file required
		
		@returns
		
		
		@comment
		elements

	**/
	function select_form_els($arr)
	{
		extract($arr);
		$f = get_instance(CL_FORM);
		$f->load($id);
		$this->mk_path($f->parent, "<a href='".$this->mk_my_orb("change", array("id" => $id),"form")."'>Muuda formi</a> / Vali elemendid");
		$this->read_template("import_entries2.tpl");

		// leiame mitu tulpa failis oli ja loeme sealt esimese rea
		$fp = fopen(aw_ini_get("server.tmpdir")."/".$file,"r");
		$ar = fgetcsv($fp,100000,"\t");
		fclose($fp);

		$cnt = 0;
		foreach($ar as $v)
		{
			$this->vars(array(
				"val" => $v,
				"cnt" => $cnt++
			));
			$this->parse("FCOL");
		}

		$els = $f->get_all_elements(array("type" => 1));
		foreach($els as $elid => $block)
		{
			$elname = $block["name"];
			$eltype = $block["type"];
			// we should really check the type of the form element
			if (strlen($eltype) == 0)
			{
				continue;
			};
			$this->vars(array(
				"el_name" => $elname,
				"el_type" => $eltype,
				"el_id" => $elid
			));
			$cnt=0;
			$cc = "";
			foreach($ar as $v)
			{
				$this->vars(array(
					"col" => $cnt
				));
				$cc.=$this->parse("COL");
				$cnt++;
			}
			$this->vars(array(
				"COL" => $cc
			));
			$this->parse("ROW");
		}

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_form_els", array("id" => $id,"file" => $file))
		));
		return $this->parse();
	}

	/** here we have gathered all the necessary info and must perform the real import 
		
		@attrib name=submit_form_els params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_form_els($arr)
	{
		extract($arr);

		$f = get_instance(CL_FORM);
		$f->load($id);

		// open the file and start reading lines from it and for each line turn it into an entry
		$fp = fopen(aw_ini_get("server.tmpdir")."/".$file,"r");

		$els = $f->get_all_elements();

		$parent = $f->arr["ff_folder"];

		while (($ar = fgetcsv($fp,100000,"\t")))
		{
			$rowels = array();
			$rowvals = array();
			$entry = array();
			$entry_name = "";
			foreach($els as $elid => $elname)
			{
				// for this element find the column that was specified and insert it into the array
				if (isset($el[$elid]) && $el[$elid] != -1)
				{
					$elref = $f->get_element_by_id($elid);
					if ($elref->get_type() == "listbox")
					{
						// here we must search the listbox for a matcihng string
						$elvalue = "element_".$elid."_lbopt_0";
						foreach($elref->arr["listbox_items"] as $num => $str)
						{
							if ($str == $ar[$el[$elid]])
							{
								$elvalue = "element_".$elid."_lbopt_".$num;
								break;
							}
						}
					}
					else
					if ($elref->get_type() == "multiple")
					{
						$elvalue = "";
						foreach($elref->arr["multiple_items"] as $num => $str)
						{
							if ($str == $ar[$el[$elid]])
							{
								$elvalue = $num;
								break;
							}
						}
					}
					else
					if ($elref->get_type() == "date")
					{
						$elvalue = strtotime($ar[$el[$elid]]);
					}
					else
					{
						$elvalue = $ar[$el[$elid]];
					}

					if (is_array($f->arr["name_els"]))
					{
						if (in_array($elid,$f->arr["name_els"]))
						{
							$entry_name.=($entry_name == "" ? "" : " ").$ar[$el[$elid]];
						}
					}
					else
					if ($elid == $f->arr["name_el"])
					{
						$entry_name = $ar[$el[$elid]];
					}
					$this->quote(&$elvalue);
					$elval2 = $ar[$el[$elid]];
					$this->quote(&$elval2);
					$rowels[] = "el_".$elid;
					$rowels[] = "ev_" . $elid;
					$rowvals[] = "'".$elvalue."'";
					$rowvals[] = "'".$elval2."'";
					$entry[$elid] = $elvalue;
				}
				else
				{
					// init element with empty value, so that the columns will not contain NULL's - cause then the searches will fail l8r
					$rowels[] = "el_".$elid;
					$rowels[] = "ev_" . $elid;
					$rowvals[] = "''";
					$rowvals[] = "''";
					$entry[$elid] = "";
				}
			}
			// now insert it into the correct tables, form_entry and form_$fid_entries
			$o = obj();
			$o->set_name($entry_name);
			$o->set_parent($parent);
			$o->set_class_id(CL_FORM_ENTRY);
			$entry_id = $o->save(); 

			$q = "insert into form_entries(id,form_id) values($entry_id, $id)";
			$this->db_query($q);
			
			$sels = "id,".join(",",$rowels);
			$svals = $entry_id.",".join(",",$rowvals);

			$sql = "INSERT INTO form_".$id."_entries($sels) VALUES($svals)";
			$this->db_query($sql);
		}
		
		fclose($fp);
		unlink(aw_ini_get("server.tmpdir")."/".$file);
		return $this->mk_my_orb("change", array("id" => $id),"form");
	}

	/**  
		
		@attrib name=import_chain_entries params=name default="0"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function import_chain_entries($arr)
	{
		extract($arr);
		$this->read_template("import_entries.tpl");
		$o = obj($id);
		$this->mk_path($o->parent(), sprintf(LC_IMPORT_CHAIN_HEADER,$this->mk_my_orb("change", array("id" => $id),"form_chain")));

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_chain", array("id" => $id))
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_chain params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_chain($arr)
	{
		extract($arr);

		global $HTTP_POST_FILES;
                if (!is_uploaded_file($HTTP_POST_FILES["file"]["tmp_name"]))
                {
                        $this->raise_error(ERR_FG_NOFILE,LC_FORM_IMPORT_NOT_FILE_SELECTED,true);
                }

		// liigutame faili kuskile seifi kohta ja j2tame meelde selle
		$fname = gen_uniq_id();
		move_uploaded_file($HTTP_POST_FILES["file"]["tmp_name"],aw_ini_get("server.tmpdir")."/".$fname);

		return $this->mk_my_orb("select_chain_els", array("id" => $id, "file" => $fname));
	}

	/**  
		
		@attrib name=select_chain_els params=name default="0"
		
		@param id required
		@param file required
		
		@returns
		
		
		@comment

	**/
	function select_chain_els($arr)
	{
		extract($arr);
		$f = get_instance(CL_FORM_CHAIN);
		$ch = $f->load_chain($id);
		$this->mk_path($ch["parent"], sprintf(LC_FORM_IMPORT_CHAIN_ELS,$this->mk_my_orb("change", array("id" => $id),"form_chain")));
		$this->read_template("import_entries2.tpl");

		// leiame mitu tulpa failis oli ja loeme sealt esimese rea
		$fp = fopen(aw_ini_get("server.tmpdir")."/".$file,"r");
		$ar = fgetcsv($fp,100000,"\t");
		fclose($fp);

		$cnt = 0;
		foreach($ar as $v)
		{
			$this->vars(array(
				"val" => $v,
				"cnt" => $cnt++
			));
			$this->parse("FCOL");
		}

		$els = array();
		$form = get_instance(CL_FORM);
		foreach($f->chain["forms"] as $fid)
		{
			$form->load($fid);
			$els = $form->get_all_elements() + $els;
		}

		foreach($els as $elid => $elname)
		{
			$this->vars(array(
				"el_name" => $elname,
				"el_id" => $elid
			));
			$cnt=0;
			$cc = "";
			foreach($ar as $v)
			{
				$this->vars(array(
					"col" => $cnt
				));
				$cc.=$this->parse("COL");
				$cnt++;
			}
			$this->vars(array(
				"COL" => $cc
			));
			$this->parse("ROW");
		}

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_chain_els", array("id" => $id,"file" => $file))
		));
		return $this->parse();
	}

	/** here we have gathered all the necessary info and must perform the real import 
		
		@attrib name=submit_chain_els params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_chain_els($arr)
	{
		extract($arr);

		aw_set_exec_time(AW_LONG_PROCESS);
		$f = get_instance(CL_FORM_CHAIN);
		$f->load_chain($id);

		// open the file and start reading lines from it and for each line turn it into an entry
		$fp = fopen(aw_ini_get("server.tmpdir")."/".$file,"r");

		$form = get_instance(CL_FORM);
		$ar = fgetcsv($fp,100000,"\t");	// skipime esimese rea
		while (($ar = fgetcsv($fp,100000,"\t")))
		{
			// first we create a new chain entry for this line
			$o = obj();
			$o->set_parent($f->chain["save_folder"]);
			$o->set_class_id(CL_CHAIN_ENTRY);
			$chain_entry_id = $o->save(); 

			$this->db_query("INSERT INTO form_chain_entries(id,chain_id,uid) values($chain_entry_id,$id,'".aw_global_get("uid")."')");

			$chentrys = array();
			/// now create entries for all forms in the chain
			foreach($f->chain["forms"] as $fid)
			{
				$form->load($fid);
				$parent = $form->arr["ff_folder"];
				$rowels = array();
				$rowvals = array();
				$entry = array();
				$entry_name = "";
				$fels = $form->get_all_elements();
				foreach($fels as $elid => $elname)
				{
					// for this element find the column that was specified and insert it into the array
					if (isset($el[$elid]) && $el[$elid] != -1)
					{
						$elref = $form->get_element_by_id($elid);
						if ($elref->get_type() == "listbox")
						{
							// here we must search the listbox for a matcihng string
							$elvalue = "element_".$elid."_lbopt_0";
							foreach($elref->arr["listbox_items"] as $num => $str)
							{
								if ($str == $ar[$el[$elid]])
								{
									$elvalue = "element_".$elid."_lbopt_".$num;
									break;
								}
							}
						}
						else
						if ($elref->get_type() == "multiple")
						{
							$elvalue = "";
							foreach($elref->arr["multiple_items"] as $num => $str)
							{
								if ($str == $ar[$el[$elid]])
								{
									$elvalue = $num;
									break;
								}
							}
						}
						else
						{
							$elvalue = $ar[$el[$elid]];
						}


						if ($elid == $form->arr["name_el"])
						{
							$entry_name = $elvalue;
						}
						$this->quote(&$elvalue);
						$rowels[] = "el_".$elid;
						$rowvals[] = "'".$elvalue."'";

						$_ev = $ar[$el[$elid]];
						$this->quote(&$_ev);
						$rowels[] = "ev_".$elid;
						$rowvals[] = "'".$_ev."'";

						$entry[$elid] = $elvalue;
					}
				}
				// now insert it into the correct tables, form_entry and form_$fid_entries
				$o = obj();
				$o->set_name($entry_name);
				$o->set_parent($parent);
				$o->set_class_id(CL_FORM_ENTRY);
				$entry_id = $o->save(); 

				$chentrys[$fid] = $entry_id;

				$this->db_query("insert into form_entries(id,form_id) values($entry_id, $fid)");
				
				$_rowels = join(",",$rowels);
				if ($_rowels != "")
				{
					$sels = "id,chain_id,".$_rowels;
					$svals = $entry_id.",".$chain_entry_id.",".join(",",$rowvals);
	
					$sql = "INSERT INTO form_".$fid."_entries($sels) VALUES($svals)";
					$this->db_query($sql);
				}
			}
			$ches = aw_serialize($chentrys,SERIALIZE_XML);
			$this->quote(&$ches);
			$this->db_query("UPDATE form_chain_entries SET ids = '$ches' WHERE id = $chain_entry_id");
		}
		
		fclose($fp);
		unlink(aw_ini_get("server.tmpdir")."/".$file);
		return $this->mk_my_orb("change", array("id" => $id),"form_chain");
	}
}
?>
