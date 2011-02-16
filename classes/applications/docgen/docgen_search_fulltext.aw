<?php

class docgen_search_fulltext extends core implements docgen_search_module, docgen_index_module
{
	function docgen_search_fulltext()
	{
		$this->init();
	}

	function get_module_name()
	{
		return t("Fulltext search");
	}

	function do_search($arr)
	{
		$ret = array();

		$words = explode(' ', $arr['search']);

		$basedir = aw_ini_get("basedir")."/docs/tutorials";
		$tut_ret = array();
		$this->doc_srch_scan_files($basedir, $words, $tut_ret);
		if(count($tut_ret))
		{
			$ret[] = '';
			$ret[] = 'In tutorials:';
			$ret = array_merge($ret, $tut_ret);
		}

		$classdir = isset($this->cfg["classdir"]) ? $this->cfg["classdir"] : NULL;
		$cl_ret = array();
		$this->doc_srch_class_info($classdir, $words, $cl_ret);
		if(count($cl_ret))
		{
			$ret[] = '';
			$ret[] = 'In classes:';
			$ret = array_merge($ret, $cl_ret);
		}
		return $ret;
	}

	function handle_index_start()
	{
		$this->db_query("CREATE TABLE IF NOT EXISTS aw_da_func_data (`id` INT NOT NULL AUTO_INCREMENT, `text` TEXT, `file` TEXT, PRIMARY KEY (`id`))");
		$this->db_query("DELETE FROM aw_da_func_data");
	}

	function handle_index_file($rel_file, $data)
	{
		// write fulltext search data
		$text = dbg::dump($data);
		$this->db_query("INSERT INTO aw_da_func_data(`id`, `text`, `file`) VALUES(0, '".htmlspecialchars($text, ENT_QUOTES)."', '".$rel_file."')");
	}

	function handle_index_class($rel_file, $class, $c_data)
	{
	}

	function handle_index_method($rel_file, $class, $fname, $fdata)
	{
	}

	/** Searches from within files for given string. highly inefficient, but used only for tutorials so that's ok.
	**/
	private function doc_srch_scan_files($dir, $words, &$ret)
	{
		$dh = opendir($dir);
		while(($file = readdir($dh)) !== false)
		{
			$fh = $dir."/".$file;
			if ($file != "." && $file != ".." && $file != "CVS" && substr($file, 0,2) != ".#")
			{
				if(is_dir($fh))
				{
					$this->doc_srch_scan_files($fh, $words, $ret);
				}
				else
				{
					$fp = fopen($fh, 'r');
					$data = fread($fp, filesize($fh));
					fclose($fp);
					$found = 1;
					foreach($words as $word)
					{
						if(stristr($data, $word) === FALSE)
						{
							$found = 0;
						}
					}
					if($found)
					{
						$basedir = aw_ini_get("basedir")."/docs/tutorials";
						$filedir = substr($dir, strlen($basedir));
						$ret[] = html::href(array(
							"url" => $this->mk_my_orb("show_doc", array("file" =>$filedir."/".$file), "docgen_viewer"),
							"target" => "list",
							"caption" => $file
						));
					}
				}
			}
		}
	}

	/** searches from the fulltext search database
	**/
	function doc_srch_class_info($dir, $words, &$ret)
	{
		$wordsql = "`text` LIKE '%".implode("%' AND `text` LIKE '%", $words)."%'";
		$results = $this->db_fetch_array("SELECT * FROM aw_da_func_data WHERE ".$wordsql);
		foreach($results as $result)
		{
			$ret[] = html::href(array(
				"url" => $this->mk_my_orb("class_info", array(
					"file" => str_replace("/classes", "", $result['file'])
				), "docgen_viewer"),
				"target" => "list",
				"caption" => str_replace("/classes", "", $result['file'])
			));
		}
	}

}
