<?php
/*
@classinfo  maintainer=kristo
*/

classload("formgen/form_base");
class form_export_db extends form_base
{
	function form_export_db()
	{
		$this->form_base();
	}

	////
	// !exports form entries to another table
	// parameters:
	//	id - form id
	//	tbl - table name to export to
	function do_export($arr)
	{
		extract($arr);

		flush();
		$this->load($id);

		$e = get_instance(CL_EXPORT_RULE);

		$cols = array("id int primary key auto_increment");

		$elem2col = array();

		$elems = $this->get_all_els();
		foreach($elems as $el)
		{
			if ($el->get_type() != "")
			{
				$nm = $e->fix_fn($el->get_el_name());
				if ($nm != "")
				{
					$cols[] = $nm." text ";
					$elem2col[$el->get_id()] = $nm;
				}
			}
		}

		echo "ekspordin, palun oodake .... <br />\n\n";
		flush();

		// create table
		$sql = "DROP TABLE IF EXISTS $tbl";
		$this->db_query($sql);
		$sql = "CREATE TABLE $tbl (	".join(",", $cols).")";
		$this->db_query($sql);

		$sql = "SELECT fe.* FROM form_".$id."_entries fe LEFT JOIN objects o ON o.oid = fe.id WHERE o.status != 0";
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$cls = array();
			$vls = array();

			$this->save_handle();
			foreach($row as $c => $v)
			{
				list($p, $o) = explode("_", $c);
				if ($p == "ev" && isset($elem2col[$o]))
				{
					$cls[] = $elem2col[$o];
					$this->quote(&$v);
					$vls[] = "'".$v."'";
				}
			}

			$sql = "INSERT INTO $tbl (".join(",", $cls).") VALUES(".join(",",$vls).")";
			$this->db_query($sql);
			$this->restore_handle();
			$cnt++;
			echo "rida $cnt <br />\n";
			flush();
		}
		echo "Eksportisin $cnt sisestust tabelisse $tbl!<br />\n\n\n\n\n\n";
		flush();
		die();
	}
}
?>
