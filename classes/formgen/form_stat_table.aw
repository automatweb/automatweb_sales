<?php
classload("formgen/form_base");
/*
@classinfo  maintainer=kristo
*/
class form_stat_table extends form_base
{
	function form_stat_table()
	{
		$this->form_base();
	}

	/**  
		
		@attrib name=new params=name default="0"
		
		@param parent required acl="add"
		
		@returns
		
		
		@comment

	**/
	function add($arr)
	{
		extract($arr);
		$this->read_template("add_stat_table.tpl");
		$this->mk_path($parent, "Lisa stat tabel");

		$this->vars(array(
			"forms" => $this->multiple_option_list(array(), $this->get_flist(array("type" => FTYPE_ENTRY))),
			"reforb" => $this->mk_reforb("submit", array("parent" => $parent))
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit($arr)
	{
		extract($arr);

		if (!$id)
		{
			$o = obj();
			$o->set_name($name);
			$o->set_class_id(CL_FORM_STAT_TABLE);
			$o->set_parent($parent);
			$id = $o->save();
		}

		$this->load_stat_table($id);

		$this->s_table["name"] = $name;
		$this->s_table["meta"]["forms"] = $this->make_keys($forms);

		$element2form = $this->get_elements_for_forms($forms, true);

		// save col/row elements
		$this->s_table["meta"]["cols"] = array();
		$this->s_table["meta"]["num_cols"] = 0;

		$this->s_table["meta"]["rows"] = array();
		$this->s_table["meta"]["num_rows"] = 0;

		if (is_array($col_el))
		{
			$cnt = 0;
			foreach($col_el as $cnum => $cel)
			{
				if ($cel)
				{
					$this->s_table["meta"]["cols"][$cnt]["element"] = $cel;
					$this->s_table["meta"]["cols"][$cnt++]["form"] = $element2form[$cel];
				}
			}
			$this->s_table["meta"]["num_cols"] = $cnt;
		}

		if (is_array($row_el))
		{
			$cnt = 0;
			foreach($row_el as $rnum => $rel)
			{
				if ($rel)
				{
					$this->s_table["meta"]["rows"][$cnt]["element"] = $rel;
					$this->s_table["meta"]["rows"][$cnt++]["form"] = $element2form[$rel];
				}
			}
			$this->s_table["meta"]["num_rows"] = $cnt;
		}
//		echo "data = <pre>", var_dump($this->s_table["meta"]),"</pre> <br />";
		$this->save_stat_table();

		return $this->mk_my_orb("change", array("id" => $id));
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
		$this->load_stat_table($id);
		$this->mk_path($this->s_table["parent"], "Muuda stat tabelit");
		$this->read_template("add_stat_table.tpl");

		// cause we can't load 2 templates at once
		$pv = $this;
		$this->vars(array(
			"preview" => $pv->mk_admin_preview(),
			"name" => $this->s_table["name"],
			"forms" => $this->multiple_option_list($this->s_table["meta"]["forms"], $this->get_flist(array("type" => FTYPE_ENTRY))),
			"reforb" => $this->mk_reforb("submit", array("id" => $id))
		));

		return $this->parse();
	}

	function load_stat_table($id)
	{
		$tmp = obj($id);
		$this->s_table = $tmp->fetch();
	}

	function save_stat_table()
	{
		$o = obj($this->s_table["oid"]);
		$o->set_name($this->s_table["name"]);
		$awa = new aw_array($this->s_table["meta"]);
		foreach($awa->get() as $k => $v)
		{
			$o->set_meta($k, $v);
		}
		$o->save(); 
	}

	function mk_admin_preview()
	{
		$this->read_template("stbl_admin_preview.tpl");

//		$this->mk_data();

		echo "col_data = <pre>", var_dump($this->col_data),"</pre> <br />";

		$els = $this->get_elements_for_forms($this->s_table["meta"]["forms"],false,true);

		for ($col=0; $col <= $this->s_table["meta"]["num_cols"]; $col++)
		{
			$tcol = "";
			$this->vars(array(
				"colspan" => 1,
				"num" => $col, 
				"content" => $this->picker($this->s_table["meta"]["cols"][$col]["element"],$els)
			));
			$tcol.= $this->parse("C_COL");


			$this->vars(array(
				"C_COL" => $tcol
			));
			$line.=$this->parse("C_LINE");
		}

		$this->vars(array(
			"r_rowspan" => $this->s_table["meta"]["num_rows"]+1
		));
		$fr = $this->parse("FIRST_R");

		for ($row=0; $row <= $this->s_table["meta"]["num_rows"]; $row++)
		{
			if ($row == 0)
			{
				$trow = $fr;
			}
			else
			{
				$trow = "";
			}
			$this->vars(array(
				"rowspan" => 1, 
				"num" => $row, 
				"content" => $this->picker($this->s_table["meta"]["rows"][$row]["element"],$els)
			));
			$trow.= $this->parse("R_COL");

			if ($row == 0)
			{
				// here draw selected data
				for ($i=0; $i < $this->s_table["meta"]["num_cols"]; $i++)
				{
					$this->vars(array(
						"content" => $row.".".$i
					));
					$trow.= $this->parse("DAT_COL");
				}
			}

			$this->vars(array(
				"R_COL" => $trow,
				"DAT_COL" => ""
			));
			$line.=$this->parse("R_LINE");
		}

		$this->vars(array(
			"C_LINE" => $line,
			"R_LINE" => "",
			"sp_colspan" => $this->s_table["meta"]["num_rows"]+2,
			"sp_rowspan" => $this->s_table["meta"]["num_cols"]+2,
		));
		return $this->parse();
	}

	function mk_data()
	{
		$this->col_data = array();

		// get the data so we can determine how many columns the table will have
		$f = get_instance(CL_FORM);
		for ($col=0; $col < $this->s_table["meta"]["num_cols"]; $col++)
		{
			$this->col_data[$col] = $f->get_entries_for_element(array(
				"rel_form" => $this->s_table["meta"]["cols"][$col]["form"],
				"rel_element" => $this->s_table["meta"]["cols"][$col]["element"],
				"rel_unique" => true,
				"ret_values" => true
			));
		}
	}
}
?>
