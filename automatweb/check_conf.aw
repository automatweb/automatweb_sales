<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="/automatweb/css/stiil.css" rel="stylesheet" type="text/css" />
<link href="/automatweb/css/sisu.css" rel="stylesheet" type="text/css" />
<link href="/automatweb/css/aw06.css" rel="stylesheet" type="text/css" />
</head>
<html>
<?php

class check_server_conf 
{
	function _get_results($arr)
	{
		return $this->do_check();
	}

	function do_check()
	{
		$t = new vcl_table();
		$t->define_field(array(
			"name" => "setting",
			"caption" => t("M&auml;&auml;rang"),
			"align" => "center",
			"width" => "20%",
			"chgbgcolor" => "col"
		));
		$t->define_field(array(
			"name" => "result",
			"caption" => t("Tulemus"),
			"align" => "center",
			"width" => "30%",
			"chgbgcolor" => "col"
		));
		$t->define_field(array(
			"name" => "why",
			"caption" => t("Mille jaoks vajalik / vajalik v&auml;&auml;rtus"),
			"align" => "center",
			"width" => "50%",
			"chgbgcolor" => "col"
		));

		// php settings
		$this->_php_settings($t);

		// mysql settings
		$this->_mysql_settings($t);

		// server folders
		$this->_server_folders($t);

		// server software
		$this->_server_software($t);

		$t->set_default_sortby("setting");
		$t->sort_by(array(
			"rgroupby" => array("grp" => "grp")
		));
		return $t->draw();
	}

	function _php_settings(&$t)
	{
		// modules
		// 	eaccelerator
		//	calendar: recommended
		//	curl: realestate / ut_xml / digidoc
		//	exif: recommended / minigallery / image
		// 	ftp: persona_import  / gallery_v2 / xml_export / scala_import 
		//	gd: if ! imagemagick/imagick  / gallery / image
		//	iconv: bloody everything
		//	imap: messenger
		//	ldap: ldap auth
		//	mbstring: crm
		//	mssql: windows/awmyadmin
		//	mysql: guess :P
		//	openssl: bank_payment / id card login
		//	pcre: all over
		//	session: yeah
		//	tokenizer: class parser / make orb
		//	xml: all over
		//
		$loaded_exts = get_loaded_extensions();

		$opt_exts = array(
			"calendar" => "", 
			"curl" => "Kinnisvara import, UT xml import, digidoc", 
			"exif" => "minigalerii, pilt", 
			"ftp" => "persona import, galerii v2, xml eksport, scala import", 
			"gd" => "galerii, pilt",  
			"imap" => "messenger", 
			"ldap" => "ldap autentimine", 
			"mssql" => "awmyadmin msqql serveritega", 
			"openssl" => "pangamaksed, id kaart",
		);
		$req_exts = array(
			"iconv", "mbstring", "mysql", "pcre", "eAccelerator"
		);

		foreach($req_exts as $ext)
		{
			if (in_array($ext, $loaded_exts))
			{
				$t->define_data(array(
					"grp" => t("N&otilde;utud PHP laiendid"),
					"setting" => $ext, 
					"result" => t("OK"),
					"col" => "#00FF00",
				));
			}
			else
			{
				$t->define_data(array(
					"grp" => t("N&otilde;utud PHP laiendid"),
					"setting" => $ext, 
					"result" => t("Laadimata, vajalik!"),
					"col" => "#FF0000"
				));
			}
		}

		foreach($opt_exts as $ext => $mods)
		{
			if (in_array($ext, $loaded_exts))
			{
				$t->define_data(array(
					"grp" => t("Soovitatud PHP laiendid"),
					"setting" => $ext, 
					"result" => t("OK"),
					"col" => "#00FF00",
					"why" => $mods
				));
			}
			else
			{
				$t->define_data(array(
					"grp" => t("Soovitatud PHP laiendid"),
					"setting" => $ext, 
					"result" => t("Laadimata, soovitatav!"),
					"col" => "#e1a9a9",
					"why" => $mods
				));
			}
		}

		// ini settings
		// 	file_uploads post_max_size safe_mode upload_max_filesize 
		//	eaccel: eaccelerator.check_mtime eaccelerator.compress eaccelerator.enable eaccelerator.optimizer eaccelerator.shm_only eaccelerator.shm_size
		//	session: session.bug_compat_42 session.bug_compat_warn 
		$req_inis = array(
			"session.bug_compat_42" => 1,
			"session.bug_compat_warn" => 0
		);

		$opt_inis = array(
			"file_uploads" => 1,
			"post_max_size" => "32M",
			"safe_mode" => 0,
			"upload_max_filesize" => "16M",
			"eaccelerator.check_mtime" => 1,
			"eaccelerator.compress" => 0,
			"eaccelerator.enable" => 1,
			"eaccelerator.optimizer" => 1,
			"eaccelerator.shm_only" => 1,
			"eaccelerator.shm_size" => "64M"
		);
		foreach($req_inis as $setting => $val)
		{
			if (("".trim(ini_get($setting))) == ("".trim($val)))
			{
				$t->define_data(array(
					"grp" => t("N&otilde;utud PHP INI settingud"),
					"setting" => $setting, 
					"result" => t("OK"),
					"col" => "#00FF00",
					"why" => $val
				));
			}
			else
			{
				$t->define_data(array(
					"grp" => t("N&otilde;utud PHP INI settingud"),
					"setting" => $setting, 
					"result" => t("Vale v&auml;&auml;rtus! (".ini_get($setting).")"),
					"col" => "#FF0000",
					"why" => $val
				));
			}
		}

		foreach($opt_inis as $setting => $val)
		{
			if (strpos($val, "M") !== false)
			{
				$bs = ini_get($setting) >= ((int)$val);
			}
			else
			{
				$bs = ((int)trim(ini_get($setting))) == ((int)trim($val));
			}
			if ($bs)
			{
				$t->define_data(array(
					"grp" => t("Soovitavad PHP INI settingud"),
					"setting" => $setting, 
					"result" => t("OK"),
					"col" => "#00FF00",
					"why" => $val
				));
			}
			else
			{
				$t->define_data(array(
					"grp" => t("Soovitavad PHP INI settingud"),
					"setting" => $setting, 
					"result" => t("Vale v&auml;&auml;rtus! (".ini_get($setting).")"),
					"col" => "#FF0000",
					"why" => $val
				));
			}
		}
	}

	function _mysql_settings(&$t)
	{
		
	}

	function _server_folders(&$t)
	{
		// pagecache
		$tf = aw_ini_get("cache.page_cache")."/chk";
		$f = @fopen($tf, "w");
		$writeable = $f ? true : false;
		if ($f)
		{
			fwrite($f, "a");
			fclose($f);
			@unlink($tf);
		}
		if ($writeable)
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => aw_ini_get("cache.page_cache"), 
				"result" => t("OK"),
				"col" => "#00FF00",
				"why" => "pagecache failid"
			));
		}
		else
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => aw_ini_get("cache.page_cache"), 
				"result" => t("Ei saa kirjutada!"),
				"col" => "#FF0000",
				"why" => "pagecache failid"
			));
		}
		// files in lang folder for site, so that translation editor can work
		$wt = false;
		$fs = $this->files(aw_ini_get("site_basedir")."/lang");
		foreach($fs as $file)
		{
			if (!is_writable($file))
			{
				$t->define_data(array(
					"grp" => t("Serveri kataloogid"),
					"setting" => $file, 
					"result" => t("Ei saa kirjutada!"),
					"col" => "#e1a9a9",
					"why" => "T&otilde;lgete editor ei saa faili kirjutada!"
				));
			}
		}

		// scheduler
		$tf = aw_ini_get("basedir")."/files/scheduler.schedule";
		if (is_writeable($tf))
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => $tf, 
				"result" => t("OK"),
				"col" => "#00FF00",
				"why" => "scheduleri ajakava"
			));
		}
		else
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => $tf, 
				"result" => t("Ei saa kirjutada!"),
				"col" => "#FF0000",
				"why" => "scheduleri ajakava"
			));
		}
		
		// code files
		$tf = aw_ini_get("basedir")."/files";
		if (is_writeable($tf))
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => $tf, 
				"result" => t("OK"),
				"col" => "#00FF00",
				"why" => "koodi juures olevad failid"
			));
		}
		else
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => $tf, 
				"result" => t("Ei saa kirjutada!"),
				"col" => "#FF0000",
				"why" => "koodi juures olevad failid"
			));
		}

		// site files
		$tf = aw_ini_get("site_basedir")."/files";
		if (is_writeable($tf))
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => $tf, 
				"result" => t("OK"),
				"col" => "#00FF00",
				"why" => "saidi juures olevad failid"
			));
		}
		else
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => $tf, 
				"result" => t("Ei saa kirjutada!"),
				"col" => "#FF0000",
				"why" => "saidi juures olevad failid"
			));
		}

		// tmp folder
		$tf = aw_ini_get("server.tmpdir");
		if (is_writeable($tf))
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => $tf, 
				"result" => t("OK"),
				"col" => "#00FF00",
				"why" => "ajutiste failide kataloog"
			));
		}
		else
		{
			$t->define_data(array(
				"grp" => t("Serveri kataloogid"),
				"setting" => $tf, 
				"result" => t("Ei saa kirjutada!"),
				"col" => "#FF0000",
				"why" => "ajutiste failide kataloog"
			));
		}
	}

	function _server_software(&$t)
	{
		$sets = array( 
			"server.mysqldump_path" => "backup, statistika arhiiv",
			"server.mysql_path" => "statistika arhiiv",
			"server.gzip_path" => "google sitemap, statistika arhiiv", 
			"server.gunzip_path" => "statistika arhiiv",
			"server.tar_path" => "backup",
			"server.zip_path" => "saidi html eksport",
			"server.unzip_path" => "statistika arhiiv, galerii v2, minigalerii, otsing sxw failidest",
			"server.convert_dir" => "galeriid, pilt",
			"server.identify_dir" => "galeriid, pilt",
			"server.composite_dir" => "galerii thumbnailid",
			"server.nslookup" => "saidi installer, dns serveri haldus",
			"server.fop_dir" => "CRM pakkumise eksport PDF formaati",
			"server.xls2csv" => "otsing XLS failidest",
			"server.rtf2txt" => "otsing RTF failidest",
			"server.catppt" => "otsing PPT failidest",
			"server.pdftotext" => "otsing PDF failidest",
			"server.catdoc" => "otsing DOC failidest", 
			"html2pdf.htmldoc_path" => "HTMList PDF genereerimine"
		);
		foreach($sets as $ini => $why)
		{
			if (!is_executable(aw_ini_get($ini)))
			{
				// try and find the damn thing
				list(, $prg) = explode(".", $ini);
				$prg = str_replace("_path", "", str_replace("_dir", "", $prg));
				
				$rpath = `which $prg`;
				if (trim($rpath) == "")
				{
					$tmp = explode("\n", `locate $prg`);
					foreach($tmp as $line)
					{
						if (basename(trim($line)) == $prg && !is_dir(trim($line)) && is_executable(trim($line)))
						{
							$rpath = $line;
						}
					}
				}	

				$app = "<br>Praegune m&auml;&auml;rang: ".aw_ini_get($ini)." <br>";
				if (trim($rpath) != "")
				{
					$app .= "<b>Tundub et programm asub: ".trim($rpath)."</b><br>";
				}
				$t->define_data(array(
					"grp" => t("Serveri programmid"),
					"setting" => $ini, 
					"result" => t("Ei ole k&auml;ivitatav!").$app,
					"col" => "#e1a9a9",
					"why" => $why
				));
			}
			else
			{
				$t->define_data(array(
					"grp" => t("Serveri programmid"),
					"setting" => $ini, 
					"result" => t("OK - ").aw_ini_get($ini),
					"col" => "#00FF00",
					"why" => $why
				));
			}
		}
	}

	/**
		@attrib name=check
	**/
	function check()
	{
		return $this->do_check();
	}

	function files($dir)
	{
		$res = array();
		$this->req_files($dir, $res);
		foreach($this->dirfiles($dir) as $fn)
		{
			$res[] = $fn;
		}
		return $res;
	}

	function req_files($dir, &$res)
	{
		foreach($this->dirdirs($dir) as $nd)
		{
			$this->req_files($nd, $res);
			foreach($this->dirfiles($nd) as $fn)
			{
				$res[] = $fn;
			}
		}
	}

	function dirfiles($dir)
	{
		$files = array();
		if ($DH = @opendir($dir)) {
			while (false !== ($file = readdir($DH))) {
				$fn = $dir . "/" . $file;
				if (is_file($fn) && (substr($fn, -2) == "aw"))
				{
					$files[$fn] = $fn;
				};
			}
			closedir($DH);
		}
		return $files;
	}

	function dirdirs($dir)
	{
		$files = array();
		if ($DH = @opendir($dir)) {
			while (false !== ($file = readdir($DH))) {
				$fn = $dir . "/" . $file;
				if (is_dir($fn) && $file != "." && $file != "..")
				{
					$files[$fn] = $fn;
				};
			}
			closedir($DH);
		}
		return $files;
	}
}



class aw_table 
{
	////
	// !constructor - paramaters:
	// prefix - a symbolic name for the table so we could tell it apart from the others
	// tbgcolor - default cell background color
	var $scripts;
	var $id = 'table_0';
	var $filter_name = "awTblFlt";
	var $name = "awTable0";

	var $table_caption = ''; 
	var $parsed_pageselector = '';

	function aw_table($data = array())
	{
		$this->id = uniqid('table_');
		if (file_exists(aw_ini_get("site_basedir")."/public/img/up.gif"))
		{
			$this->imgurl = aw_ini_get("baseurl")."/img";
		}
		else
		{
			$this->imgurl = aw_ini_get("baseurl")."/automatweb/images";
		}
		$this->up_arr = sprintf("<img src='%s' border='0' />",$this->imgurl . "/up.gif");
		$this->dn_arr = sprintf("<img src='%s' border='0' />",$this->imgurl . "/down.gif");
		// prefix - kasutame sessioonimuutujate registreerimisel
		$this->prefix = isset($data["prefix"]) ? $data["prefix"] : "";
		// table cell background color
		$this->tbgcolor = isset($data["tbgcolor"]) ? $data["tbgcolor"] : "";

		$this->header_attribs = array();

		// ridade v&auml;rvid (och siis stiilid) muutuvad
		// siin defineerime nad
		$this->style1 = "#AAAAAA";
		$this->style2 = "#CCCCCC";

		// initsialiseerime muutujad
		$this->rowdefs = array();
		$this->rowdefs_key_index = array();
		$this->data = array();
		$this->actions = array();
		$this->col_styles = array();
		$this->nfields = array();
		$this->filters = array();
		$this->selected_filters = array();
		$this->filter_index = array();
		$this->rowspans = array();
		
		if ($data["prop_name"] != "")
		{
			$this->filter_name = $data["prop_name"].$_GET["id"];
		}
		else
		if ($_GET["id"] && $_GET["group"])
		{
			$this->filter_name = md5($_GET["id"].$_GET["group"]);
		}
		$this->filter_name.= ++$GLOBALS["__aw_table_count_on_page"];
		$this->sortable = true;
		$this->rowdefs_ordered = false;

		// esimene kord andmeid sisestada?
		// seda on vaja selleks, et m&auml;&auml;rata default sort order.
		$this->first = true;
		$layout = !empty($data["layout"]) ? $data["layout"] : "generic";
		$this->set_layout($layout);
		if (isset($data["xml_def"]))
		{
			$this->parse_xml_def($data["xml_def"]);
		};
		$this->use_chooser = false;
		// if true and chooser is used, checking chooser checkboxes changes the style of the row as well
		$this->chooser_hilight = true;
	}

	/**
	@attrib api=1 params=pos
	@param arg required type=bool
		If it is false, the table is not sortable
	**/
	function set_sortable($arg)
	{
		$this->sortable = $arg;
	}

	/**
	@attrib api=1 params=pos
	@param arg required type=array
	**/
	function set_rgroupby($arg)
	{
		$this->rgroupby = $arg;
	}

	/**
	@attrib api=1 params=pos
	@param $arr required type=array
		table header
	@comments
		some users need to put simple plain text above the
		table, give them a setter for this
	**/
	function set_header($arr)
	{
		$this->table_header = $arr;
	}

	function set_caption($arr)
	{
		$this->table_caption = $arr;
	}

	////
	// !sisestame andmed
	/**
	@attrib api=1 params=pos
	@param $row required type=array
		array(column name => value, column name => value, column name => value , ....)
	@return False, if filter applying fails, true, if everything is ok.
	@example ${draw}
	@comments
		add's a row to the table
	**/
	function define_data($row)
	{
		### apply filter
		foreach ($this->selected_filters as $filter_key => $filter_array)
		{
			extract ($filter_array);
			$field_name = $this->filter_index[$filter_key];

			// if exists value filtervalue-[rowname] use that for filtering (good for linked values etc)
			$value = $row[(isset($row['filtervalue-'.$field_name]) ? 'filtervalue-' : '') . $field_name];

			if (!empty($filter_txtvalue))
			{
				if (isset($this->filter_comparators[$field_name]))
				{
					$fc = $this->filter_comparators[$field_name];
					if (is_array($fc))
					{
						if ($fc[0]->$fc[1]($field_name, $filter_txtvalue, $row) === false)
						{
							return false;
						}
					}
					else
					{
						if ($this->filter_comparators[$field_name]($field_name, $filter_txtvalue, $row) === false)
						{
							return false;
						}
					}
				}
				else
				if (stristr($value, $filter_txtvalue) === false)
				{
					return false;
				}
			}
		}

		$this->data[] = $row;

		if(!$this->no_recount)
		{
			$this->d_row_cnt++;
		}
		return true;
	}

	/**
	@attrib api=1 params=pos
	@param $idx required type=int
		an array key
	@param $row required type=array
		array(column name => value, column name => value, column name => value , ....)
	@comments
		sets $this->data[$idx] = $row
	**/
	function set_data($idx, $row)
	{
		$this->data[$idx] = $row;
	}

	/**
	@attrib api=1
	@return array(row id => row data, row id => row data, ....)
	@comments
		returns all data set for the table
	**/
	function get_data()
	{
		return $this->data;
	}

	////
	// !Clear the data
	/**
	@attrib api=1
	@comments
		clear the data array
	**/
	function clear_data()
	{
		$this->data = array();
	}

	////
	// !merge the given data with the last data entered
	// XXX: does not seem to be used?
	/**
	@attrib api=1 params=pos
	@param $row required type=array
		Array(column name => value, column name => value, column name => value , ....)
	@comments
		merges $row with last row in data array
	**/
	function merge_data($row)
	{
		$cnt = sizeof($this->data);
		$this->data[$cnt-1]  = array_merge($this->data[$cnt-1],$row);
	}

	/**
	@attrib api=1 params=name
	@param link optional type=string
		Part of the action's link,
	@param field optional type=string
		The field that will be used to complete the action link,
	@param caption optional type=string
		Action text
	@param cspan optional type=string
		Colspan
	@param rspan optional type=string
		Rowspan
	@param remote optional type=string
		If specified, the link will open in a new window and this parameter also must contain the height,width of the popup
		)
	@comments
		here you can add action rows to the table
	**/
	function define_action($row)
	{
		$this->actions[] = $row;
	}

	/**
	@attrib api=1 params=name
	@param $name required type=string
		Checkbox name, the checkbox element name attribute is set $name[value_which is set in define_data() method]
	@param $field required type=string
		Field name, via this name you can add value to chooser in define_data() method
	@param $caption optional type=string default=Vali
		Caption of the chooser column
	@example ${draw}
	@comments
		Defines a chooser (a column of checkboxes)
	**/
	function define_chooser($arr)
	{
		$this->chooser_config = $arr;
		$this->use_chooser = true;
	}

	/**
	@attrib api=1
	@comments
		Removes a chooser
	**/
	function remove_chooser()
	{
		$this->chooser_config = NULL;
		$this->use_chooser = false;
	}

	/**
	@attrib api=1 params=pos
	@param $caption required type=string
		The caption at the left of the header
	@param $links optional type=array
		An array of link => text pairs that will be put in the header
	@comments
		Here you can define additional headers
	**/
	function define_header($caption,$links = array())
	{
		$this->headerstring = $caption;
		$hlinks = array();
		if ($this->headerlinkclassid)
		{
			$hlcl=" class='".$this->headerlinkclassid."' ";
		};
		reset($links);
		while(list($k,$v) = each($links))
		{
			if ($k=="extra")
			{
				$this->headerextra = $v;
			}
			else
			if ($k=="extrasize")
			{
				$this->headerextrasize = $v;
			}
			else
			{
				$hlinks[] = sprintf("<a href='$k' $hlcl>$v</a>",$k,$v);
			};
		};
		$this->headerlinks = join(" | ",$hlinks);
	}

	/**
	@attrib api=1 params=pos
	@param $elname required type=string
		Field name
	@comments
		This lets you set a field as numeric, so that they will be sorted correctly
	**/
	function set_numeric_field($elname)
	{
		$this->nfields[$elname] = 1;
	}

	/**
	@attrib api=1 params=pos
	@param $sortby required type=string/array
		The default sorting element(s)
	@example ${draw}
	@comments
		sets the default sorting element(s) for the table
		if the sorting function finds that there are no other sorting arrangements made, then it will use
		the element(s) specified here.
		sortby - a single element or an array of elements.
		sometimes the array can be a bit weird though - namely, the key specifies the column in which the
		element is and that is used when determining if a column is sorted
		but when doing the actual sorting, the value is used and that contains an ordinary element, not a column name
		right now this only applies to form tables
		- so, when setting an array, always have the key and value be the same, unless you really know what you are doing
		This lets you set a field as numeric, so that they will be sorted correctly
	**/
	function set_default_sortby($sortby)
	{
		$this->default_order = $sortby;
	}

	/**
	@attrib api=1 params=pos
	@param $dir required type=string/array
		A string (asc/desc) or an array of strings - it will be linked to the sort element by index
	@example ${draw}
	@comments
		Sets the default sorting order
		If the sorting function finds that there are no other sorting arrangements made, then it will use
		The order specified here.
	**/
	function set_default_sorder($dir)
	{
		$this->default_odir = $dir;
	}

	/** defines that the table has a pager
		@attrib api=1 params=name
		@param type required type=string
			"text" || "buttons" || "lb"
		@param records_per_page required type=int
			Number of records per page.
		@param d_row_cnt required type=int
			Number of records in total.
		@param no_recount type=bool
		@param position type=string default=top
			Pageselector position in table layout "top" || "bottom" || "both". default is "top".
		@comment
	**/
	function define_pageselector($arr)
	{
		$this->has_pages = true;
		$this->records_per_page = $arr["records_per_page"];
		$this->pageselector = $arr["type"];
		$this->pageselector_position = $arr["position"] ? $arr["position"] : "top";

		if($arr["d_row_cnt"])
		{
			$this->d_row_cnt = $arr["d_row_cnt"];
			$this->no_recount = 1;
		}

		if($arr["no_recount"])
		{
			$this->no_recount = 1;
		}
	}

	/** sorts the data previously entered
		@attrib api=1 params=name
		@param field optional type=string
			what to sort by. you really don't need to specify this, the table can manage it on it's own
		@param sorder optional type=string
			sorting order - asc/desc. you really don't need to specify this, the table can manage it on it's own
		@param rgroupby optional type=array
			An array of elements whose values will be grouped in the table
		@param vgroupby optional type=array
			Array of elements that will be vertically grouped
		@example ${draw}
		@return false, if $this->sortable is not true
	**/
	function sort_by($params = array())
	{
		if (!$this->sortable)
		{
			return false;
		};

		// see peaks olema array,
		// kus on regitud erinevate tabelite andmed
		$aw_tables = array();
		$sess_field_key   = $this->prefix . "_sortby";
		$sess_field_order = $this->prefix . "_sorder";

		// figure out the column by which we must sort
		// start from the parameters
		if (!($this->sortby = (isset($params["field"]) ? $params["field"] : NULL)))
		{
			// if it was not specified as a parameter the next place is the url
			if (!($this->sortby = $_GET["sortby"]))
			{
				// and if it is not in the url either, we will try the session
				if (!($this->sortby = $aw_tables[$sess_field_key]))
				{
					// and finally we get the default
					$this->sortby = isset($this->default_order) ? $this->default_order : NULL;
				}
			}
		}

		// now figure out the order of sorting
		// start with parameters
		if (!($this->sorder = $params["sorder"]))
		{
			// if it was not specified as a parameter the next place is the url
			if (!($this->sorder = $_GET["sort_order"]))
			{
				// and if it is not in the url either, we will try the session
				if (!($this->sorder = $aw_tables[$sess_field_order]))
				{
					// and finally we get the default
					if (!($this->sorder = $this->default_odir))
					{
						$this->sorder = "asc";
					}
				}
			}
		}


		// we should mark this down only when we have clicked on a link and thus changed something from the default
		// what's the difference? well - if the defaults change and this is written a reload does not change things
		//
		// and well, I think this kinda sucks - I don't want the damn thing to remember the state.

		// grouping - whenever a value of one of these elements changes an extra row gets inserted into the table
		$this->rgroupby = isset($params["rgroupby"]) ? $params["rgroupby"] : $this->rgroupby;
		$this->rgroupsortdat = isset($params["rgroupsortdat"]) ? $params["rgroupsortdat"] : "";
		$this->vgroupby = isset($params["vgroupby"]) ? $params["vgroupby"] : "";
		$this->vgroupdat = isset($params["vgroupdat"]) ? $params["vgroupdat"] : "";

		// ok, all those if sentences are getting on my nerves - we will make sure that all sorting options
		// after this point are always arrays
		$this->make_sort_prop_arrays();

		// switch to estonian locale
		$old_loc = setlocale(LC_COLLATE,0);
		setlocale(LC_COLLATE, 'et_EE');


		// sort the data
		usort($this->data,array($this,"sorter"));

		// switch back to estonian
		setlocale(LC_COLLATE, $old_loc);

		// now go over the data and make the rowspans for the vertical grouping elements
		if (is_array($this->vgroupby))
		{
			$tmp = $this->vgroupby;
			$this->vgrowspans = array();

			foreach($this->vgroupby as $_vgcol => $_vgel)
			{
				foreach($this->data as $row)
				{
					$val = "";
					foreach($tmp as $__vgcol => $__vgel)
					{
						$val .= $row[$__vgel];
						if ($_vgcol == $__vgcol && $_vgel == $__vgel)
						{
							break;
						}
					}
					$this->vgrowspans[$val]++;
				}
			}
		}
	}

	function sorter($a,$b)
	{
		// what the hell is going on here you ask? well.
		//Basically the idea is that we go over the sorted columns until we find two values that are different
		//and then we can compare them and thus we get to sort the entire array.
		//why don't we just concatenate the strings together? well, because what if the first is a text element, the next
		//a number and the 3rd a text element - if we cat them together we lose the ability to do numerical comparisons..
		$v1=NULL;$v2=NULL;
		$skip = false;

		if (is_array($this->vgroupby))
		{
			// $_vgcol and $vgel are the same
			foreach($this->vgroupby as $_vgcol => $vgel)
			{
				// if there is a sorting element for this vertical group, then actually use it's value for sorting
				if ($this->vgroupdat[$vgel]["sort_el"])
				{
					$v1 = $a[$this->vgroupdat[$vgel]["sort_el"]];
					$v2 = $b[$this->vgroupdat[$vgel]["sort_el"]];
					$this->sort_flag = $this->nfields[$this->vgroupdat[$vgel]["sort_el"]] ? SORT_NUMERIC : SORT_REGULAR;
					if ($v1 == $v2)
					{
						// if they are equal, then try to sort by the display element
						$v1 = $a[$vgel];
						$v2 = $b[$vgel];
						$this->sort_flag = $this->nfields[$vgel] ? SORT_NUMERIC : SORT_REGULAR;
					}
				}
				else
				{
					$v1 = $a[$vgel];
					$v2 = $b[$vgel];
					$this->sort_flag = $this->nfields[$vgel] ? SORT_NUMERIC : SORT_REGULAR;
				}
				// the sort numeric is specified for the actual sorting element, but the order for the column
				$this->u_sorder = $this->sorder[$vgel];
				if ($v1 != $v2)
				{
					$skip = true;
					break;
				}
			}
		}

		if (is_array($this->rgroupby) && !$skip)
		{
			foreach($this->rgroupby as $_rgcol => $rgel)
			{
				if (is_array($this->rgroupsortdat[$_rgcol]) && $this->rgroupsortdat[$_rgcol]["sort_el"])
				{
					$v1 = $a[$this->rgroupsortdat[$_rgcol]["sort_el"]];
					$v2 = $b[$this->rgroupsortdat[$_rgcol]["sort_el"]];
					$this->sort_flag = $this->nfields[$this->rgroupsortdat[$_rgcol]["sort_el"]] ? SORT_NUMERIC : SORT_REGULAR;
					if ($v1 == $v2)
					{
						// if they are equal, then try to sort by the display element
						$v1 = $a[$rgel];
						$v2 = $b[$rgel];
						$this->sort_flag = $this->nfields[$_rgcol] ? SORT_NUMERIC : SORT_REGULAR;
					}
				}
				else
				{
					$v1 = $a[$rgel];
					$v2 = $b[$rgel];
					$this->sort_flag = $this->nfields[$_rgcol] ? SORT_NUMERIC : SORT_REGULAR;
				}
				$this->u_sorder = $this->sorder[$_rgcol];
				if ($v1 != $v2)
				{
					$skip = true;
					break;
				}
			}
		}

		if (is_array($this->sortby) && !$skip)
		{
			foreach($this->sortby as $_coln => $_eln)
			{
				$this->u_sorder = $this->sorder[$_eln];
				$this->sort_flag = isset($this->nfields[$_eln]) ? SORT_NUMERIC : SORT_REGULAR;
				$v1 = $a[$_eln];
				$v2 = $b[$_eln];
				if ($v1 != $v2)
				{
					break;
				}
			}
		}

		if (isset($this->sort_flag) && ($this->sort_flag == SORT_NUMERIC))
		{
			$_a = (float)strtolower(strip_tags($v1));
			$_b = (float)strtolower(strip_tags($v2));

			if ($this->u_sorder == "asc")
			{
				if ($_a < $_b)
				{
					return -1;
				}
				else
				if ($_a > $_b)
				{
					return 1;
				}
				return 0;
			}
			else
			{
				if ($_a > $_b)
				{
					return -1;
				}
				else
				if ($_a < $_b)
				{
					return 1;
				}
				return 0;
			}
		}
		else
		{
			$_a = strtolower(strip_tags($v1));
			$_b = strtolower(strip_tags($v2));
			$ret = strcoll($_a, $_b);
			if (isset($this->u_sorder) && ($this->u_sorder == "asc"))
			{
				return $ret;
			}
			else
			{
				return -$ret;
			}
		}
	}

	function _sort_by_field_order($a, $b)
	{
		if ($a["order"] == $b["order"])
		{
			return 0;
		}
		else
		{
		   return ($a["order"] < $b["order"]) ? -1 : 1;
		}
	}
	
	/**
		@attrib api=1 params=name
		@param header required type=array
			Consists of same type of arrays that the ones define_field() takes. As much such array's you put here, as much fields you get. The array keys set the order of the fields.
		@param row_01 optional type=array
			First row's data. Array has the same structure as define_data() parameter.
		@param row_02 optional type=array
			Second row's data. Array has the same structure as define_data() parameter.
		@param finalize optional type=array
			if this is set to true, html source for the table is returned.
		
		@errors
			if $arg["header"] is not array or hasn't any fields in it... funcion returns false.
		@comment
			Reads in an array and makes a table out of it. It generates field's from argument 'header', and sets the data from other rows. It's important to put correct array key's for the data array's.. so that the rows will be in correct order.
	**/
	function gen_tbl_from_array($arg)
	{
		if($arg["finalize"])
		{
			$final = $arg["finalize"];
		}
		unset($arg["finalize"]);
		if(is_array($arg["header"]) && count($arg["header"]))
		{
			$header = $arg["header"];
			unset($arg["header"]);
		}
		else
		{
			return false;
		}
		foreach($header as $field)
		{
			$this->define_field($field);
		}
		foreach($arg as $data)
		{
			$this->define_data($data);
		}
		if($final)
		{
			return $this->draw();
		}
		return true;
	}

	/** sets the REQUEST_URI
		@attrib api=1 params=pos
		@param ru required type=string
			URL
	**/
	function set_request_uri($ru)
	{
		$this->REQUEST_URI = $ru;
	}

	/** Draws the table
	@attrib api=1 params=name
	@param records_per_page optional type=int
		How many records per page
	@param pageselector optional type=string
		Defines witch page selector to use. Possibilities are text, buttons, lb
	@param has_pages optional type=bool
		If this is set, this table has pages
	@param rgroupby optional type=string
		If it is set, calculates how many elements are there in each rgroup and puts them in $this->rgroupcounts
	@return string/html table
	@example
		classload("vcl/table");
		$table = new aw_table(array(
			"layout" => "generic"
		));
		$table->define_field(array(
			"name" => "modify",
			"caption" => t("Vaata/Muuda"),
		));
		$table->define_field(array(
			"name" => "sisseastuja_nr",
			"caption" => t("Sisseastuja Nr."),
			"sortable" => 1
		));
		$table->define_chooser(array(
			"name" => "selection",
			"field" => "sisseastuja_id",
		));
		$table->set_default_sortby("sisseastuja_nr");
		$table->set_default_sorder("desc");
		$table->sort_by();
		$table->define_data(array(
			"modify" => html::href(array(
				"caption" => t("Muuda"),
				"url" => $change_url,
			)),
			"sisseastuja_nr" => sprintf("%04d", $sisseastuja->prop("sisseastuja_nr")),
			"sisseastuja_id" => $sisseastuja->id(),
		));
		$data = $table->draw(array(
			"records_per_page" => 100,
			"pageselector" => "text",
			"has_pages" => 1
		));
	**/
	function draw($arr = array())
	{
		// v&auml;ljastab tabeli
		if (!is_array($this->rowdefs))
		{
			print "Don't know what to do";
			return;
		}

		if ($this->rowdefs_ordered)
		{
			usort($this->rowdefs, array(&$this, "_sort_by_field_order"));
		}

		if ($this->rgroupby && !$arr["rgroupby"])
		{
			$arr["rgroupby"] = $this->rgroupby;
		}

		if (isset($arr["rgroupby"]) && is_array($arr["rgroupby"]))
		{
			$this->do_rgroup_counts($arr["rgroupby"]);
		}

		extract($arr);
		$PHP_SELF = $_SERVER["PHP_SELF"];
		if (isset($this->REQUEST_URI))
		{
			$REQUEST_URI = $this->REQUEST_URI;
		}
		else
		{
			$REQUEST_URI = $_SERVER["REQUEST_URI"];
		}
		$this->titlebar_under_groups = isset($arr["titlebar_under_groups"]) ? $arr["titlebar_under_groups"] : $this->titlebar_under_groups;
		$tbl = "";

		foreach($this->rowdefs as $rd)
		{
			$this->sh_counts_by_parent[$rd["name"]] = $this->_get_sh_count_by_parent($rd["name"]);
		}

		$this->_get_max_level_cnt("");
		$this->max_sh_count = $this->_max_gml-1;
		if (!empty($this->table_header))
		{
			$tbl .= $this->table_header;
		}

		if (!empty($pageselector))
		{
			$this->pageselector = $pageselector;
		}

		if (!empty($records_per_page))
		{
			$this->records_per_page = $records_per_page;
		}

		if (!empty($has_pages))
		{
			$this->has_pages = $has_pages;
		}


		// moodustame v&auml;limise raami alguse
		/*
		if (is_array($this->frameattribs))
		{
			$tmp = $this->frameattribs;
			$tmp["name"] = "table";
			$tbl .= $this->opentag($tmp);
			$tbl .= "<tr>\n";
			$tattr = array(
				"name" => "td",
				"bgcolor" => $this->framebgcolor,
			);
			if ($this->framebgclass != "")
			{
				$tattr["class"] = $this->framebgclass;
				unset($tattr["bgcolor"]);
			}
			$tbl .= $this->opentag($tattr);
		};
		*/

		// moodustame tabeli alguse
		if (is_array($this->tableattribs))
		{
			$tmp = $this->tableattribs;
			$tmp["name"] = "table";
			$tmp["classid"] = "awmenuedittabletag";
			$tbl .= $this->opentag($tmp);
		}

		if (!empty($this->pageselector_string))
		{
			$colspan = sizeof($this->rowdefs) + sizeof($this->actions)-(int)$this->headerextrasize;
			$tbl .= "<tr>\n";
			$tbl .= "<td colspan='$colspan' class='" . $this->style1 . "'>";
			$tbl .= $this->pageselector_string;
			$tbl .= "</td>\n";
			$tbl .= "</tr>\n";
		}

		if (!empty($this->headerstring))
		{
			$colspan = sizeof($this->rowdefs) + sizeof($this->actions)-(int)$this->headerextrasize;
			$tbl .= "<tr>\n";
			$tbl .= "<td colspan='$colspan' class='" . $this->titlestyle . "'>";
			$tbl .= "<strong>" . $this->headerstring . ": ". $this->headerlinks . "</strong>";
			$tbl .= "</td>\n";
			$tbl .= $this->headerextra;
			$tbl .= "</tr>\n";
		}

		// if we show title under grouping elements, then we must not show it on the first line!
		if (empty($this->titlebar_under_groups) && empty($arr["no_titlebar"]))
		{
			$tbl .= $this->_req_draw_header("");
		}

		$this->lgrpvals = array();

		if (!isset($act_page))
		{
			$act_page = $GLOBALS["ft_page"];
		}

		if ($act_page*$this->records_per_page > count($this->data))
		{
			$act_page = 0;
		}

		// koostame tabeli sisu
		if (is_array($this->data))
		{
			// ts&uuml;kkel &uuml;le data
			$counter = 0; // kasutame ridadele erineva v&auml;rvi andmiseks
			$p_counter = 0;
			foreach($this->data as $k => $v)
			{
				$enum[$k] = $v["id"];
				$counter++;
				$p_counter++;
				// if this is not on the active page, don't show the damn thing
				if (isset($this->has_pages) && $this->has_pages && isset($this->records_per_page) && $this->records_per_page && !$this->no_recount)
				{
					$cur_page = (int)(($p_counter-1) / $this->records_per_page);
					if ($cur_page != $act_page)
					{
						continue;
					}
				}

				$row_style = $counter % 2 == 0 ? $this->tr_style2 : $this->tr_style1;
				if ($v["_active"])
				{
					$row_style = $this->tr_active;
				};

				// rida algab
				// rowid/domid is needed for the selector script
				$rowid = $this->prefix . $this->id . $counter;
				$tbl .= "<tr id='$rowid' class='$row_style'>";


				$tmp = "";
				// grpupeerimine
				if (isset($rgroupby) && is_array($rgroupby))
				{
					$tmp = $this->do_col_rgrouping($rgroupby, $rgroupdat, $rgroupby_sep, $v, $rowid, $row_style);
				};
				if ($tmp != "")
				{
					$counter = 1;
				}

				$tbl .= $tmp;
					if ($this->use_chooser)
					{
						$chooser_value = $v[$this->chooser_config["field"]];
						$name = $this->chooser_config["name"] . "[${chooser_value}]";
						$onclick = "";
						if ($this->chooser_hilight)
						{
							$onclick = " onClick=\"hilight(this,'${rowid}')\" ";
						};
						$stl = "";
						if (!empty($this->chooser_config["chgbgcolor"]) && !empty($v[$this->chooser_config["chgbgcolor"]]))
						{
							$stl =  "style=\"background:".$v[$this->chooser_config["chgbgcolor"]]."\"";
						}
						if(!empty($this->chooser_config["width"]))
						{
							$width = " width=\"".$this->chooser_config["width"]."\"";
						}
						if($chooser_value)
						{
							$tbl .= "<td align='center' ".$stl.$width."><input type='checkbox' name='${name}' value='${chooser_value}' ${onclick} ".($v[$this->chooser_config["name"]] ? "checked" : "")."></td>";
						}
						else
						{
							$tbl .= "<td align='center'>&nbsp;</td>";
						}
					};

				// ts&uuml;kkel &uuml;le rowdefsi, et andmed oleksid oiges j&auml;rjekorras
				foreach($this->rowdefs as $k1 => $v1)
				{
					if ($this->sh_counts_by_parent[$v1["name"]] > 0)
					{
						continue;
					}
					$rowspan = 1;
					$style = false;
					if (isset($this->vgroupby) && is_array($this->vgroupby))
					{
						if (isset($this->vgroupby[$v1["name"]]))
						{
							// if this column is a part of vertical grouping, check if it's value has changed
							// if it has, then set the new rowspan
							// if not, then skip over this column

							// build the value from all higher grouping els and this one as well
							$_value = "";
							foreach($this->vgroupby as $_vgcol => $_vgel)
							{
								$_value .= $v[$_vgel];
								if ($v1["name"] == $_vgcol)
								{
									break;
								}
							}
							if (!isset($this->vgrouplastvals[$_value]))
							{
								$this->vgrouplastvals[$_value] = $_value;
								$rowspan = $this->vgrowspans[$_value];
								$style = $this->group_style;
							}
							else
							{
								continue;
							}
						}
					}

					// m&auml;&auml;rame &auml;ra staili
					if (!$style)
					{
						if (isset($this->sortby[$v1["name"]]))
						{
							$style_key = (($counter % 2) == 0) ? "content_sorted_style2" : "content_sorted_style1";
							$bgcolor = ($counter % 2) ? $this->selbgcolor1 : $this->selbgcolor2;
						}
						else
						{
							$style_key = (($counter % 2) == 0) ? "content_style2" : "content_style1";
							$bgcolor = ($counter % 2) ? $this->bgcolor1 : $this->bgcolor2;
						};

						if (isset($this->col_styles[$v1["name"]][$style_key]))
						{
							$style = $this->col_styles[$v1["name"]][$style_key];
						}
						else
						{
							$style = "";
						};

						if (!$style)
						{
							if (isset($this->sortby[$v1["name"]]))
							{
								$style = (($counter % 2) == 0) ? $this->selected2 : $this->selected1;
								$bgcolor = ($counter % 2) ? $this->selbgcolor1 : $this->selbgcolor2;
							}
							else
							{
								$style = (($counter % 2) == 0) ? $this->style2 : $this->style1;
								$bgcolor = ($counter % 2) ? $this->bgcolor1 : $this->bgcolor2;
							};
						}
					}

					// moodustame celli
					$rowspan = isset($this->actionrows) ? $this->actionrows : $rowspan;
					//järgnev peaks suutma tulpi kokku liita ja järgmised vastavald ära blokeerima
					if($this->rowspans[$v1["name"]]>1)
					{
						$this->rowspans[$v1["name"]]--;
						continue;
					}
					elseif(isset($v[$v1["rowspan"]]))
					{
						$rowspan = $v[$v1["rowspan"]];
						$this->rowspans[$v1["name"]] = $v[$v1["rowspan"]];
					}
					$tbl .= $this->opentag(array(
						"name"    => "td",
						"classid" => $style,
						"width" => isset($v1["width"]) ? $v1["width"] : "",
						"rowspan" => ($rowspan > 1) ? $rowspan : 0,
						"style" => ((!empty($v1["chgbgcolor"]) && !empty($v[$v1["chgbgcolor"]])) ? ("background:".$v[$v1["chgbgcolor"]]) : ""),
						"align" => isset($v1["align"]) ? $v1["align"] : "",
						"valign" => isset($v1["valign"]) ? $v1["valign"] : "",
						"nowrap" => isset($v1["nowrap"]) ? 1 : "",
						"bgcolor" => isset($v["bgcolor"]) ? $v["bgcolor"] : $bgcolor,
						"domid" => isset($v[$v1["id"]]) ? $v[$v1["id"]] : "",
						"onclick" => isset($v[$v1["onclick"]]) ? $v[$v1["onclick"]] : "",
	//					"onclick" => isset($v[$v1["onclick"]]) ? $v[$v1["onclick"]] : "",
					));

					if ($v1["name"] == "rec")
					{
						$val = $counter;
					}
					else
					{
						if (isset($v1["strformat"]))
						{
							$format = localparse($v1["strformat"],$v);
							$val = sprintf($format,$v[$v1["name"]]);
						}
						else
						{
							$val = $v[$v1["name"]];
						};
					};

					if (empty($v1["type"]))
					{
						$v1["type"] = "";
					};

					if (isset($v1["type"]) && $v1["type"] == "time")
					{
						if (!empty($v1["smart"]))
						{
							$today = date("dmY");
							$thisdate = date("dmY",$val);
							if ($today == $thisdate)
							{
								// XX: make it translatable
								$val = date("H:i",$val) . " t&auml;na";
							}
							else
							{
								$val = date($v1["format"],$val);
							};
						}
						else
						{
							$is_link = false;
							if (preg_match("/<a (.*)>(.*)<\/a>/U",$val, $tmt))
							{
								$is_link = true;
								$val = $tmt[2];
							}

							if (!isset ($val))
							{
								$val = "";
							}
							elseif ($val < 1)
							{
								$val = "n/a";
							}
							else
							{
								$val = date($v1["format"],$val);
							};
							if ($is_link)
							{
								$val = "<a ".$tmt[1].">".$val."</a>";
							}
						};
					};

					if (!strlen($val) && $v1["type"]!="int")
					{
						$val = "&nbsp;";
					};

					//v&otilde;eh, &uuml;hes&otilde;naga laseme $val l&auml;bi functsiooni, mis on defineeritud v&auml;ljakutsuva klassi sees
					//ja $t->define_field(array(
					//	...
					//	"callback" => array(&$this, "method")
					//   ));
					if (isset($v1["callback"]) && is_callable($v1["callback"]))
					{
						$v["_this_cell"] = $v1["name"];
						$val = call_user_func ($v1["callback"], isset($v1['callb_pass_row']) ? $v : $val);
					}

					if (isset($v1["thousands_sep"]))
					{
						// insert separator every after every 3 chars, starting from the end.
						$val = strrev(chunk_split(strrev(trim($val)),3,$v1["thousands_sep"]));
						// chunk split adds one too many separators, so remove that
						$val = substr($val,strlen($v1["thousands_sep"]));
					}

					$tbl .= str_replace("[__jrk_replace__]",$counter,$val);
					$tbl .= "</td>\n";
				};

				// joonistame actionid
				$actionridu = isset($this->actionrows) ? $this->actionrows : 1;

				for ($arow = 1; $arow <= $actionridu; $arow++)
				{
					// uutele actioni ridadele tuleb teha uus <tr>
					if ($arow > 1)
					{
						$tbl .= "<tr>\n";
					};
					$style = (($counter % 2) == 0) ? $this->style1 : $this->style2;
					// joonistame actionid
					foreach($this->actions as $ak => $av)
					{
						// joonista ainult need actionid, mis siia ritta kuuluvad
						if ($this->actionrows ? ($arow == $av["row"] || ($arow==1 && !$av["row"]) ):1)
						{
							$tbl .= $this->opentag(array(
								"name"=>"td",
								"classid" => ($av["style"]) ? $av["style"] : $style,
								"align" => "center",
								"colspan" => ($av["cspan"] ? $av["cspan"] : ""),
								"rowspan" => ($av["rspan"] ? $av["rspan"] : ""),
							));

							$tbl.=$av["remote"]?
								"<a href='javascript:remote(0,".$av["remote"].",\"$PHP_SELF?".$av["link"]."&id=".$v[$av["field"]].'");\'>'.$av["caption"]."</a>":
								"<a href='$PHP_SELF?" . $av["link"] . "&id=" . $v[$av["field"]] . "&" . $av["field"] . "=" . $v[$av["field"]] . "'>$av[caption]</a>";
							$tbl .= "</td>\n";

						};
					};

					// rida lopeb
					$tbl .= "</tr>\n";
				};
				if($this->final_enum)
				{
					aw_session_set("table_enum", $enum);
				}
			};
		};
		// sisu joonistamine lopeb

		if ($this->titlebar_repeat_bottom)
		{
			$tbl .= $this->_req_draw_header("");
		}

		// tabel kinni
		if (is_array($this->tableattribs))
		{
			$tbl .= "</table>\n";
		}

		// raam kinni
		/*
		if (is_array($this->frameattribs))
		{
			$tbl .= "</td></tr></table>\n";
		};
		*/

		return $tbl;
	}

	/**
		@param arg optional type=bool
		@comment
			sets the final table enumeration(after sortings and everything) with key's to session var
	**/
	function set_final_enum($arg = true)
	{
		$this->final_enum = $arg;
	}

	function _format_csv_field($d, $sep = ";")
	{
		$new=strtr($d,array('"'=>'""'));
		if (!(strpos($d,$sep)===false) || $new != $d || strpos($d, "\n") !== false)
		{
			$new='"'.$new.'"';
		};
		return strip_tags($new);
	}

	// tagastab csv andmed, kustuda v&auml;lja draw asemel
	/**returns cvs data
		@attrib api=1 params=pos
		@param sep optional type=string default=;
			csv separator
		@return string/csv table
		@example
			if ($GLOBALS["get_csv_file"])
			{
				header('Content-type: application/octet-stream');
				header('Content-disposition: root_access; filename="csv_output_'.$id.'.csv"');
				print $ft->t->get_csv_file();
				die();
			};
	**/
	function get_csv_file($sep = ";")
	{
		//$sep = "\t";
		$d = array();
		reset($this->rowdefs);
		$tbl = "";
		if(is_array($this->rowdefs))
		{
			foreach($this->rowdefs as $v)
			{
				$tbl .= ($tbl ? $sep : "").$this->_format_csv_field($v["caption"], $sep);
			}
		}
		$d[] = $tbl;

		// koostame tabeli sisu
		if(is_array($this->data))
		{
			reset($this->data);
			$cnt = 0;
			foreach($this->data as $k => $v)
			{
				$tbl = "";
				$cnt++;
				reset($this->rowdefs);
				if(is_array($this->rowdefs))
				foreach($this->rowdefs as $k1 => $v1)
				{
					if ($v1["name"] == "rec")
					{
						$val = $cnt;
					} else
					{
						if ($v1["strformat"])
						{
							$format = localparse($v1["strformat"], $v);
							$val = sprintf($format, $v[$v1["name"]]);
						}
						else
						{
							$val = $v[$v1["name"]];
						};
					};

					if ($v1["type"] == "time")
					{
						$val = date($v1["format"], $val);
					};

					if (!$val && $v1["type"] != "int")
					{
						$val = "";
					};

					$val = str_replace("[__jrk_replace__]",$cnt,$val);

					$tbl .= ($tbl ? $sep : "").$this->_format_csv_field($val, $sep);
				};
				$d[] = $tbl;
			};
		};
		// sisu joonistamine lopeb
		return join("\r\n", $d);
	}

	// genereerib html tagi
	function tag($data)
	{
		if (!is_array($data))
		{
			// kui anti vigased andmed, siis bail out
			return;
		};

		// eraldame nime ja atribuudid
		// moodustame atribuutidest stringi
		$attr_list = "";
		$name = "";
		foreach($data as $k => $v)
		{
			if ($k == "name")
			{
				$name = $v;
			}
			// whats up with this id?
			elseif ($k == "id")
			{
				$attr_list .= " name='$v'";
			}
			elseif ($k == "domid")
			{
				$attr_list .= " id='$v'";
			}
			elseif ($k == "title" and !empty ($v))
			{
				$attr_list .= " title='$v'";
			}
			elseif ($k == "onclick" and !empty ($v))
			{
				$attr_list .= ' onClick="'.$v.'"';
			}
			elseif ($v != "")
			{
				if ($k == "nowrap")
				{
					$attr_list .= " $k";
				}
				elseif ($k == "classid")
				{
					$attr_list .= " class='$v'";
				}
				else
				{
					$attr_list .= " $k='$v'";
				};
			};
		};

		// koostame tagi
		$retval = "";
		if (!empty($name))
		{
			$retval = "<" . $name . $attr_list . ">\n";
		};
		// ja tagastame selle
		return $retval;
	}

	// alias eelmisele, monikord voiks selle kasutamine loetavusele kaasa aidata
	function opentag($data)
	{
		return $this->tag($data);
	}

	// loeb faili. Hiljem liigutame selle kuhugi baasklassi
	function get_file_contents($name,$bytes = 8192)
	{
		$fh = fopen($name,"r");
		$data = fread($fh,$bytes);
		fclose($fh);
		return $data;
	}

	// xml funktsioonid
	function _xml_start_element($parser,$name,$attrs)
	{
		if (!isset($attrs["value"]))
		{
			$attrs["value"] = "";
		};
		switch($name)
		{
			// vaikimisi m&auml;&auml;ratud sorteerimisj&auml;rjekord
			case "default_order":
				$this->default_order = $attrs["value"];
				$this->default_odir = isset($attrs["order"]) ? $attrs["order"] : "";
				break;

			// tabeli atribuudid
			case "tableattribs":
				$this->tableattribs = $attrs;
				break;

			// v&auml;limise tabeli atribuudid
			case "frameattribs":
				$this->frameattribs = $attrs;
				break;

			case "framebgcolor":
				$this->framebgcolor = isset($attrs["bgcolor"]) ? $attrs["bgcolor"] : "";
				$this->framebgclass = isset($attrs["class"]) ? $attrs["class"] : "";
				break;

			case "titlebar":
				$this->titlestyle = isset($attrs["style"]) ? $attrs["style"] : "";
				// lauri muudetud
				$this->headerlinkclassid = isset($attrs["linkclass"]) ? $attrs["linkclass"] : "";
				break;

			// tavalise (mittesorteeritava) headeri stiil
			case "header_normal":
				$this->header_normal = $attrs["value"];
				break;

			// sorteeritava headeri stiil
			case "header_sortable":
				$this->header_sortable = $attrs["value"];
				break;

			case "group_style":
				$this->group_style = $attrs["value"];
				break;

			case "group_add_els_style":
				$this->group_add_els_style = $attrs["value"];
				break;

			// filtri stiil
			case "filter_normal":
				$this->filter_normal = $attrs["value"];
				break;

			// valitud filtri stiil
			case "filter_active":
				$this->filter_active = $attrs["value"];
				break;

			// stiil, mida kasutada parajasti sorteeritud v&auml;lja headeri n&auml;itamiseks
			case "header_sorted":
				$this->header_sorted = $attrs["value"];
				break;

			// stiilid contenti kuvamiseks
			case "content_style1":
				$this->style1 = $attrs["value"];
				$this->bgcolor1 = isset($attrs["bgcolor"]) ? $attrs["bgcolor"] : "";
				break;

			case "content_style2":
				$this->style2 = $attrs["value"];
				$this->bgcolor2 = isset($attrs["bgcolor"]) ? $attrs["bgcolor"] : "";
				break;

			// stiilid millega kuvatakse sorteeritud v?lja sisu
			case "content_style1_selected":
				$this->selected1 = $attrs["value"];
				$this->selbgcolor1 = isset($attrs["bgcolor"]) ? $attrs["bgcolor"] : "";
				break;

			case "content_style2_selected":
				$this->selected2 = $attrs["value"];
				$this->selbgcolor2 = isset($attrs["bgcolor"]) ? $attrs["bgcolor"] : "";
				break;

			// stiilid contenti kuvamiseks <tr> jaoks
			case "content_tr_style1":
				$this->tr_style1 = $attrs["value"];
				break;

			case "content_tr_style2":
				$this->tr_style2 = $attrs["value"];
				break;

			case "content_tr_sel":
				$this->tr_sel = $attrs["value"];
				break;

			case "content_tr_active":
				$this->tr_active = $attrs["value"];
				break;

			// actionid
			case "action":
				$this->actions[] = $attrs;
				break;

			case "actionrows":
				$this->actionrows = $attrs["value"];
				break;

			// v&auml;ljad
			case "field":
				$temp = array();
				while(list($k,$v) = each($attrs))
				{
					$temp[$k] = $v;
				};
				$this->rowdefs[] = $temp;

				if (isset($attrs["numeric"]))
				{
					$this->nfields[$attrs["name"]] = 1;
				};

				if (!empty($attrs["header_normal"]))
				{
					$this->col_styles[$attrs["name"]]["header_normal"] = $attrs["header_normal"];
				}
				if (!empty($attrs["header_sortable"]))
				{
					$this->col_styles[$attrs["name"]]["header_sortable"] = $attrs["header_sortable"];
				}
				if (!empty($attrs["header_sorted"]))
				{
					$this->col_styles[$attrs["name"]]["header_sorted"] = $attrs["header_sorted"];
				}
				if (!empty($attrs["content_style1"]))
				{
					$this->col_styles[$attrs["name"]]["content_style1"] = $attrs["content_style1"];
				}
				if (!empty($attrs["content_style2"]))
				{
					$this->col_styles[$attrs["name"]]["content_style2"] = $attrs["content_style2"];
				}
				if (!empty($attrs["content_sorted_style1"]))
				{
					$this->col_styles[$attrs["name"]]["content_sorted_style1"] = $attrs["content_sorted_style1"];
				}
				if (!empty($attrs["content_sorted_style2"]))
				{
					$this->col_styles[$attrs["name"]]["content_sorted_style2"] = $attrs["content_sorted_style2"];
				}
				if (!empty($attrs["group_style"]))
				{
					$this->col_styles[$attrs["name"]]["group_style"] = $attrs["group_style"];
				}
				if (!empty($attrs["filter_normal"]))
				{
					$this->col_styles[$attrs["name"]]["filter_normal"] = $attrs["filter_normal"];
				}
				if (!empty($attrs["filter_active"]))
				{
					$this->col_styles[$attrs["name"]]["filter_active"] = $attrs["filter_active"];
				}
				break;

			default:
				// do nothing
		}; // end of switch
	}

	var $filters_updated = false;

	/**Defines table field
		@attrib api=1 params=name
		@param name optional type=string
			Field name
		@param caption optional type=string
			Field caption
		@param sortable optional type=bool
			If set, the table is sortable
		@param type optional type=string
			Field type
		@param format optional type=string
			Field format
		@param numeric optional type=bool
		@param filter_compare optional array
		@param order optional
		
		@param onclick optional type=string
			variable name for onClick actions in define_field function
		@param rowspan optional type=string
			variable name for roswpan in define_field function
			
		@param filter optional

		@param filter_options optional

		@example ${draw}
	**/
	function define_field($args = array())
	{
		$this->filter_comparators[$args["name"]] = $args["filter_compare"];
		$this->rowdefs_key_index[$args["name"]] = count($this->rowdefs);
		$this->rowdefs[] = $args;
		$this->rowdefs_ordered = isset($args["order"]);

		if (isset($args["numeric"]))
		{
			$this->nfields[$args["name"]] = 1;
		}

		### filter definition for UI parsing
		if (!empty ($args["filter"]))
		{
			### add filter definition
			$filter = $args["filter"];
			asort($filter);
			$filter_key = count ($this->filters) + 1;
			$this->filters[$args["name"]] = array (
				"key" => $filter_key,
				"filter" => $filter,
				"name" => $args["name"],
				"type" => $args['filter'] == 'text' ? 'text' : 'select',
				"active" => false,
			);
			$this->filter_index[$filter_key] = $args["name"];

			if (is_array ($args["filter_options"]))
			{
				if (!empty ($args["filter_options"]["selected"]))
				{
					$defaults_selected = aw_global_get ($this->filter_name . "DefaultsSelected");

					if (!$defaults_selected)
					{
						$selected = $args["filter_options"]["selected"];
						$this->selected_filters[$filter_key] = array(
							'filter_selection' => (int) reset (array_keys ($filter, $selected)),
							'filter_txtvalue' => $selected,
						);
						aw_session_set ($this->filter_name . "Saved", aw_serialize ($this->selected_filters));
						aw_session_set ($this->filter_name . "DefaultsSelected", 1);
					}
				}
			}
		}
		return count($this->rowdefs)-1;
	}

	/**
		@comment
			sorts defined fields by name
	**/
	function sort_fields($startkey = 0, $endkey = false)
	{
		$endkey = $endkey?$endkey:count($this->rowdefs)-1;
		$tmp = array_flip($this->rowdefs_key_index);
		for($j = $startkey;$j < $endkey;$j++)
		{
			for($i = $startkey;$i<$endkey;$i++)
			{
				$cmp = strcasecmp(($a_name = $this->rowdefs[$i]["name"]), ($b_name = $this->rowdefs[$i+1]["name"]));
				if($cmp > 0)
				{
					$tmp_rowdefs = $this->rowdefs[$i];
					$this->rowdefs[$i] = $this->rowdefs[$i+1];
					$this->rowdefs[$i+1] = $tmp_rowdefs;
					$tmp[$i] = $b_name;
					$tmp[$i+1] = $a_name;
				}
			}
		}
		$this->rowdefs_key_index = array_flip($tmp);
	}
	
	function field_exists($field)
	{
		return isset($this->rowdefs_key_index[$field]);
	}

	function remove_field($name)
	{
		unset ($this->filters[$name]);
		unset($this->rowdefs[$this->rowdefs_key_index[$name]]);
		$this->rowdefs_ordered = false;

		foreach ($this->rowdefs as $def)
		{
			if (isset($def["order"]))
			{
				$this->rowdefs_ordered = true;
				break;
			}
		}
	}

	// same arguments as for define_field(), "name" is required
	// exceptions:
	// filter updating not implemented
	// additional argument "order" explicitly defined here also applies to define_field()
	function update_field($args)
	{
		if (is_array($args) and array_key_exists($args["name"], $this->rowdefs_key_index))
		{
			$this->rowdefs_ordered = isset($args["order"]);
			$this->rowdefs[$this->rowdefs_key_index[$args["name"]]] = $args + $this->rowdefs[$this->rowdefs_key_index[$args["name"]]];
		}
	}

	function _xml_end_element($parser,$name)
	{
		// actually, this is only a dummy function that does nothing
	}

	function parse_xml_def_string($xml_data)
	{
		$xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
		xml_set_object($xml_parser,&$this);
		xml_set_element_handler($xml_parser,"_xml_start_element","_xml_end_element");
		if (!xml_parse($xml_parser,$xml_data))
		{
			echo(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($xml_parser)),
			xml_get_current_line_number($xml_parser)));
		};
	}

	function set_layout($def)
	{
		$realdef = "generic";
		if ($def == "cool")
		{
			$realdef = "cool";
		};
		$this->parse_xml_def($realdef . "_table");
	}

	function parse_xml_def($file)
	{
		//if (substr($file,0,1) != "/"  && substr($file,0,2) != "C:")
		if (substr($file,0,1) != "/" && !preg_match("/^[a-z]:/i", substr($file,0,2)))
		{
			//if (!is_admin())
			//{
			$path = aw_ini_get("site_basedir") . "/xml/" . $file . ".xml";
			//}

			if (!file_exists($path))
			{
				$path = aw_ini_get("basedir") . "/xml/" . $file . ".xml";
			}
		}
		else
		{
			$path = $file;
		};
		$xml_data = $this->get_file_contents($path);
		return $this->parse_xml_def_string($xml_data);
	}

	////
	// !this makes sure that all sort properties (sortby, sort_order) are
	// arrays and not strings - just to unify things
	function make_sort_prop_arrays()
	{
		if (!is_array($this->sortby))
		{
			$this->sortby = ($this->sortby == "" ? array() : array($this->sortby => $this->sortby));
		}
		if (!is_array($this->sorder))
		{
			$tmp = $this->sorder;
			$this->sorder = array();
			foreach($this->sortby as $_coln => $_eln)
			{
				$this->sorder[$_coln] = $tmp == "" ? "asc" : $tmp;
				$this->sorder[$_eln] = $tmp == "" ? "asc" : $tmp;
			}
		}
		if (is_array($this->vgroupdat))
		{
			foreach($this->vgroupdat as $_eln => $dat)
			{
				if ($dat["sort_el"])
				{
					$this->sorder[$_eln] = $dat["sort_order"];
				}
			}
		}
		if (is_array($this->rgroupsortdat))
		{
			foreach($this->rgroupsortdat as $_eln => $dat)
			{
				if ($dat["sort_el"])
				{
					$this->sorder[$_eln] = $dat["sort_order"];
				}
			}
		}
	}

	function draw_titlebar_under_rgrp()
	{
		$tbl .= "<tr>\n";
		foreach($this->rowdefs as $k => $v)
		{
			// the headers between groups are never clickable - less confusing that way
			$tbl .= $this->opentag(array(
				"name" => "td",
				"title" => $v["tooltip"],
				"classid" => ($this->col_styles[$v["name"]]["header_normal"] ? $this->col_styles[$v["name"]]["header_normal"] : $this->header_normal),
				"align" => ($v["talign"] ? $v["talign"] : "center"),
				"valign" => ($v["tvalign"] ? $v["tvalign"] : ""),
				"bgcolor" => ($this->tbgcolor ? $this->tbgcolor : ""),
				"width" => ($v["width"] ? $v["width"] : "")
			));

			// if the column is sortable, turn it into a link
			if ($v["sortable"])
			{
				// by default (the column is not sorted) don't show any arrows
				$sufix = "";
				// by default, if a column is not sorted and you click on it, it should be sorted asc
				$so = "asc";

				// kui on sorteeritud selle v?lja j?rgi
				if ($this->sortby[$v["name"]])
				{
					$sufix = $this->sorder[$v["name"]] == "desc" ? $this->up_arr : $this->dn_arr;
					$so = $this->sorder[$v["name"]] == "desc" ? "asc" : "desc";
				}
				if ($_GET["sort_order"])
				{
					$so = $_GET["sort_order"] == "asc" ? "desc" : "asc";
				}
				$url = aw_global_get("REQUEST_URI");
				$url = preg_replace("/sortby=[^&$]*/","",$url);
				$url = preg_replace("/sort_order=[^&$]*/","",$url);
				$url = preg_replace("/&{2,}/","&",$url);
				$url = str_replace("?&", "?",$url);
				$sep = (strpos($url,"?") === false) ?	"?" : "&";
				$url .= $sep."sortby=".$v["name"]."&sort_order=".$so;

				$tbl .= "<b><a href='$url'>$v[caption] $sufix</a></b>";
			}
			else
			{
				$tbl .= $v["caption"];
			};
			$tbl .= "</td>\n";
		}

		// kui actionid on defineeritud, siis joonistame nende jaoks vajaliku headeri
		if (is_array($this->actions) && (sizeof($this->actions) > 0))
		{
			$tbl .= $this->opentag(array(
				"name" => "td",
				"align" => "center",
				"classid" => $this->header_normal,
				"colspan" => sizeof($this->actions)
			));
			$tbl .= "Tegevused";
			$tbl .= "</td>\n";
		};
		$tbl .= "</tr>";
		return $tbl;
	}

	function do_col_rgrouping($rgroupby, $rgroupdat, $rgroupby_sep, $v, $rowid, $row_style)
	{
		$tbl = "";
		foreach($rgroupby as $rgel)
		{
			$_a = preg_replace("/<a (.*)>(.*)<\/a>/U","\\2",$v[$rgel]);
			$links = true;
			if (strpos($_a, "http") === false)
			{
				$links = false;
				$_a = $v[$rgel];
			}
			if ($this->lgrpvals[$rgel] != $_a)
			{
				// kui on uus v22rtus grupeerimistulbal, siis paneme rea vahele
				if (is_array($rgroupdat[$rgel]) && count($rgroupdat[$rgel]) > 0)
				{
					$tbl.=$this->opentag(array(
						"name" => "td",
						"colspan" => count($this->rowdefs) + ($this->use_chooser ? 1 : 0),
					));

					if (isset($rgroupby_sep[$rgel]["real_sep_before"]))
					{
						$tbl .= $rgroupby_sep[$rgel]["real_sep_before"];
					}

					$tbl .= $this->opentag(array(
						"name" => "span",
						"classid" => ($this->col_styles[$v["name"]]["group_style"] ? $this->col_styles[$v["name"]]["group_style"] : $this->group_style)
					));
					if ($links)
					{
						$tbl.= create_links($_a);
					}
					else
					{
						$tbl .= $_a;
					}
					$tbl .= "</span>";
				}
				else
				{
					$tbl.=$this->opentag(array(
						"name" => "td",
						"colspan" => count($this->rowdefs) + ($this->use_chooser ? 1 : 0),
						"classid" => ($this->col_styles[$v["name"]]["group_style"] ? $this->col_styles[$v["name"]]["group_style"] : $this->group_style)
					));
					if (isset($rgroupby_sep[$rgel]["real_sep_before"]))
					{
						$tbl .= $rgroupby_sep[$rgel]["real_sep_before"];
					}
					if ($links)
					{
						$tbl.= create_links($_a);
					}
					else
					{
						$tbl .= $_a;
					}
				}

				$this->lgrpvals[$rgel] = $_a;
				// if we should display some other elements after the group element
				// they will be passed in the $rgroupdat array
				if (isset($this->group_add_els_style) && $this->group_add_els_style != "")
				{
					$tbl .= $this->opentag(array(
						"name" => "span",
						"classid" => $this->group_add_els_style
					));
				}
				$val = "";
				if (is_array($rgroupdat[$rgel]))
				{
					$val .= $rgroupby_sep[$rgel]["pre"];
					$_ta = array();
					foreach($rgroupdat[$rgel] as $rgdat)
					{
						if (trim($v[$rgdat["el"]]) != "")
						{
							$_ta[] = $rgdat["sep"].$v[$rgdat["el"]].$rgdat["sep_after"];
						}
					}
					$val .= join($rgroupby_sep[$rgel]["mid_sep"],$_ta);
					$val .= $rgroupby_sep[$rgel]["after"];
				}
				$tbl .= str_replace("[__jrk_replace__]",$this->rgroupcounts[$_a],$val);

				if (isset($this->group_add_els_style) && $this->group_add_els_style != "")
				{
					$tbl .= "</span>";
				}
				$tbl .= "</td></tr>";

				// draw the damn titlebar under the grouping element if so instructed
				if ($this->titlebar_under_groups)
				{
					$tbl.=$this->draw_titlebar_under_rgrp();
				}

				$tbl .= $this->opentag(array("name" => "tr", "domid" => $rowid, "class" => $row_style));
			}
		}
		return $tbl;
	}

	////
	// !this calculates how many elements are there in each rgroup and puts them in $this->rgroupcounts
	function do_rgroup_counts($rgroupby)
	{
		$this->rgroupcounts = array();
		foreach($this->data as $row)
		{
			foreach($rgroupby as $rgel)
			{
				$this->rgroupcounts[$row[$rgel]] ++;
			}
		}
	}

	////
	// !draws a listbox pageselector.
	// parameters:
	//	style - id of the css style to apply to the page
	//	records_per_page - number of records on each page
	function draw_lb_pageselector($arr)
	{
		$this->read_template("lb_pageselector.tpl");
		return $this->finish_pageselector($arr);
	}

	function draw_text_pageselector($arr)
	{
		$this->read_template("text_pageselector.tpl");
		return $this->finish_pageselector($arr);
	}

	function draw_button_pageselector($arr)
	{
		$this->read_template("button_pageselector.tpl");
		return $this->finish_pageselector($arr);
	}

	function finish_pageselector($arr)
	{
		extract($arr);
		$ru = preg_replace("/ft_page=\d*/", "", aw_global_get("REQUEST_URI"));
		$sep = "&";
		if (strpos($ru, "?") === false)
		{
			$sep = "?";
		}
		$ru = $ru.$sep;
		$url = preg_replace("/\&{2,}/","&",$ru);
		$style = "";
		if ($arr["style"])
		{
			$style = "class=\"style_".$style."\"";
		}
		
		$_drc = ($arr["d_row_cnt"] ? $arr["d_row_cnt"] : $this->d_row_cnt);

		$act_page = $GLOBALS["ft_page"];
		if ($act_page*$records_per_page > $_drc)
		{
			$act_page = 0;
		}

		$num_pages = $_drc / $records_per_page;
		for ($i = 0; $i < $num_pages; $i++)
		{
			$from = $i*$records_per_page+1;
			$to = min(($i+1)*$records_per_page, $_drc);
			$this->vars(array(
				"style" => $arr["style"],
				"url" => $url . "ft_page=".$i,
				"pageurl" => $url,
				"text" => $from . " - " . $to,
				"ft_page" => $i,
				"pagenum" => $i+1,
			));
			$rv .= $this->parse($act_page == $i ? "sel_page" : "page");
			if ($i < ($num_pages - 1) && $this->is_template("sep"))
			{
				$rv .= $this->parse("sep");
			}
		}
		$this->vars(array(
			"page" => $rv,
		));
		return $this->parse();
	}

	function _get_sh_count_by_parent($parent)
	{
		$ret = 0;
		foreach($this->rowdefs as $rd)
		{
			if ($rd["parent"] == $parent)
			{
				$tmp = $this->_get_sh_count_by_parent($rd["name"]);
				if ($tmp == 0)
				{
					$ret++;
				}
				else
				{
					$ret += $tmp;
				}
			}
		}
		return $ret;
	}

	function _get_max_level_cnt($parent)
	{
		$this->_gml++;
		if ($this->_gml > $this->_max_gml)
		{
			$this->_max_gml = $this->_gml;
		}
		foreach($this->rowdefs as $rd)
		{
			if ($rd["parent"] == $parent)
			{
				$this->_get_max_level_cnt($rd["name"]);
			}
		}
		$this->_gml--;
	}

	function _req_draw_header($parent)
	{
		$this->_sh_req_level++;
		$tbl = "";
		$subs = array();
		$cell_count = 0;
		// make header!
		$tbl .= "<tr>\n";
		foreach($this->rowdefs as $k => $v)
		{
			if (!(($parent == "" && empty($v["parent"])) || isset($parent[$v["parent"]])))
			{
				continue;
			}

			$subs[$v["name"]] = $v["name"];
		}

		if (!count($subs))
		{
			$this->_sh_req_level--;
			return "";
		}

		$tbl2 = $this->_req_draw_header($subs);

		if ($this->use_chooser && $this->_sh_req_level == 1)
		{
			$opentag = array(
				"name" => "td",
				"align" => "center",
				"classid" => $this->header_normal,
				"rowspan" => $this->max_sh_count
			);
			if(($tmp = $this->chooser_config["width"]))
			{
				$opentag["width"] = $tmp;
			}
			$tbl .= $this->opentag($opentag);
			$name = $this->chooser_config["name"];
			$caption = isset($this->chooser_config["caption"]) ? $this->chooser_config["caption"] : t('Vali');
			$tbl .= "<a href='javascript:selall(\"${name}\")'>" . $caption . "</a>";
			$tbl .= "</td>";
			$cell_count++;
		};

		foreach($this->rowdefs as $k => $v)
		{
			if (!(($parent == "" && empty($v["parent"])) || isset($parent[$v["parent"]])))
			{
				continue;
			}

			$style = false;
			if (isset($v["sortable"]))
			{
				if (isset($this->sortby[$v["name"]]))
				{
					$style_key = "header_sorted";
				}
				else
				{
					$style_key = "header_sortable";
				};
			}
			else
			{
				$style_key = "header_normal";
			};
			$style = isset($this->col_styles[$v["name"]][$style_key]) ? $this->col_styles[$v["name"]][$style_key] : "";
			if (!$style)
			{
				$style = (isset($v["sortable"]) ? (isset($this->sortby[$v["name"]]) ? $this->header_sorted : $this->header_sortable) : $this->header_normal);
			}

			$sh_cnt = $this->sh_counts_by_parent[$v["name"]];
			$tbl.=$this->opentag(array(
				"name" => "td",
				"title" => $v["tooltip"],
				"classid"=> $style,
				"align" => isset($v["talign"]) ? $v["talign"] : "center",
				"valign" => isset($v["tvalign"]) ? $v["tvalign"] : "",
				"bgcolor" => isset($this->tbgcolor) ? $this->tbgcolor : "",
				"nowrap" => isset($v["nowrap"]) ? 1 : "",
				"width" => isset($v["width"]) ? $v["width"] : "",
				"colspan" => ($sh_cnt > 0 ? $sh_cnt : 1),
				"rowspan" => ($sh_cnt == 0 ? $this->max_sh_count - ($this->_sh_req_level-1) : 1)
			));

			// if the column is sortable, turn it into a link
			if (isset($v["sortable"]))
			{
				// by default (the column is not sorted) don't show any arrows
				$sufix = "";
				// by default, if a column is not sorted and you click on it, it should be sorted asc
				$so = "asc";

				// kui on sorteeritud selle v&auml;lja j&auml;rgi
				if (isset($this->sortby[$v["name"]]))
				{
					$sufix = $this->sorder[$v["name"]] == "desc" ? $this->up_arr : $this->dn_arr;
					$so = $this->sorder[$v["name"]] == "desc" ? "asc" : "desc";
				}
				else
				if ($_GET["sort_order"] && $_GET["sortby"] == $v["name"])
				{
					$sufix = $_GET["sort_order"] == "desc" ? $this->up_arr : $this->dn_arr;
					$so = $_GET["sort_order"] == "desc" ? "asc" : "desc";
				}
				if (isset($this->REQUEST_URI))
				{
					$url = $this->REQUEST_URI;
				}
				else
				{
					$url = aw_global_get("REQUEST_URI");
				}
				$url = preg_replace("/sortby=[^&$]*/","",$url);
				$url = preg_replace("/sort_order=[^&$]*/","",$url);
				$url = preg_replace("/&{2,}/","&",$url);
				$url = str_replace("?&", "?",$url);
				$sep = (strpos($url,"?") === false) ?	"?" : "&";
				$url .= $sep."sortby=".$v["name"]."&sort_order=".$so;

				$tbl .= "<b><a href='$url'>$v[caption] $sufix</a></b>";
			}
			else
			{
				$tbl .= $v["caption"];
			};

			// ### add filter if defined for current column
			// if (isset ($this->filters[$v["name"]]))
			// {
				// $filter_values = $this->filters[$v["name"]]["filter"];
				// $filter_active = $this->filters[$v["name"]]["active"];
				// $filter_name = $this->filter_name;

				// ### get filter change url
				// $url = preg_replace("/{$this->filter_name}=\d,\d/", "", aw_global_get("REQUEST_URI"));
				// $sep = (strpos($url, "?") === false) ? "?" : "&";
				// $url = $url . $sep;
				// $url = preg_replace("/\&{2,}/","&",$url);

				// ### format active filter
				// $filter_active = $filter_active ? 'red' : 'white';

				// ### add filter selectbox
				// $args = array (
					// "name" => $filter_name,
					// "options" => $filter_values,
					// "onchange" => "window.location='{$url}{$filter_name}='+{$this->filters[$v["name"]]["key"]}+','+this.options[this.selectedIndex].value",
					// "textsize" => "10px",
				// );
				// $tbl .= '<div style="width: 100%; background-color: ' . $filter_active . ';">' . html::select ($args) . '</div>';
			// }

			$tbl .= "</td>\n";
			$cell_count++;
		}

		// kui actionid on defineeritud, siis joonistame nende jaoks vajaliku headeri
		if (is_array($this->actions) && (sizeof($this->actions) > 0))
		{
			$tbl .= $this->opentag(array(
				"name" => "td",
				"align" => "center",
				"classid" => $this->header_normal,
				"colspan" => sizeof($this->actions)
			));
			$tbl .= "Tegevused";
			$tbl .= "</td>\n";
			$cell_count++;
		};


		// header kinni
		$tbl .= "</tr>";

		if (empty ($tbl2) and count ($this->filters))
		{
			$tbl2 = "<tr>\n";

			### get filter change url
			$ru = aw_global_get("REQUEST_URI");
			if (isset($this->REQUEST_URI))
			{
				$ru = $this->REQUEST_URI;
			}
			$url = preg_replace("/.{$this->filter_name}=[^&]*/", "", $ru);
			$sep = (strpos($url, "?") === false) ? "?" : "&";

			if ($this->use_chooser)
			{
				$tbl2 .= $this->opentag(array(
					"name" => "td",
					"align" => "center",
					"classid" => $this->filter_normal,
				));
				$tbl2 .= "&nbsp;";
				$tbl2 .= "</td>\n";
			}
			foreach($this->rowdefs as $k => $v)
			{
				$filter_style = "filter_normal";
				$filter_key = $this->filters[$v['name']]['key'];

				### add filter if defined for current column
				if (isset ($this->filters[$v["name"]]) && $this->filters[$v["name"]]["type"] == "select")
				{
					$filter_values = $this->get_filter ($v["name"]);
					$filter_name = $this->filter_name;

					### (re)set filter style
					$filter_style = $this->filters[$v["name"]]["active"] ? "filter_active" : $filter_style;

					### add filter selectbox
					$args = array (
						"name" => $filter_name,
						"options" => $filter_values,
						"onchange" => "window.location='{$url}{$sep}{$filter_name}={$filter_key},'+this.options[this.selectedIndex].value+','+this.options[this.selectedIndex].text",
					);
					$filter_contents = html::select ($args);
				}
				else if (isset ($this->filters[$v["name"]]) && $this->filters[$v["name"]]["type"] == "text")
				{
					$newurl = $url.$sep.$this->filter_name.'='.$filter_key;
					$filter_contents = html::textbox(array(
						'name' => $this->filter_name.'['.$v["name"].']',
						'size' => 20,
						'value' => isset($this->selected_filters[$filter_key]) ? $this->selected_filters[$filter_key]['filter_txtvalue'] : '',
						'onkeypress' => "var key = window.event ? window.event.keyCode : (event ? event.which : NULL);
							if(key == 13)
							{
								window.location = this.value.length>0 ? '$newurl,1,' + this.value : '$url' ;
								return false;
							}",
					));
				}
				else
				{
					$filter_contents = "&nbsp;";
				}

				$tbl2 .= $this->opentag(array(
					"name" => "td",
					"align" => "center",
					"classid" => $filter_style,
				));
				$tbl2 .= $filter_contents;
				$tbl2 .= "</td>\n";
				$cell_count--;
			}

			while ($cell_count-- > 1)
			{
				$tbl2 .= $this->opentag(array(
					"name" => "td",
					"align" => "center",
					"classid" => $this->filter_normal,
				));
				$tbl2 .= "&nbsp;";
				$tbl2 .= "</td>\n";
			}

			$tbl2 .= "</tr>\n";
		}

		$this->_sh_req_level--;
		return $tbl.$tbl2;
	}

	function get_automatic_filter ($field_name)
	{
		$filter = array ();

		foreach ($this->data as $row)
		{
			$filter[] = $row[$field_name];
		}

		$filter = array_unique ($filter);
		$filter = (count ($filter) > 1) ? $filter : array ();
		return $filter;
	}

	function get_filter ($field_name)
	{
		### get filter values
		if ($this->filters[$field_name]["filter"] === "automatic")
		{
			$filter = $this->get_automatic_filter ($field_name);
		}
		else
		{
			$filter = $this->filters[$field_name]["filter"];
		}

		### add "All" selection
		$filter = array_merge (array (0 => t("K&otilde;ik")), $filter);

		foreach ($this->selected_filters as $selected_filter_key => $filter_array)
		{
			extract ($filter_array);
			if ($selected_filter_key == $this->filters[$field_name]["key"])
			{ ### add selected item to first position
				$filter = array_merge (array ("_" => $filter_txtvalue), $filter);
				$this->filters[$field_name]["active"] = true;
				break;
			}
		}

		$this->filters[$field_name]["filter"] = $filter;
		foreach($filter as $k => $v)
		{
			$filter[$k] = substr($v, 0, 20);
		}
		return $filter;
	}

	/** Sets whether to display the titlebar at the bottom of the table as well
		@attrib api=1
		@param display_titlebar_below required type=bool
	**/
	function set_lower_titlebar_display($display_titlebar_below)
	{
		$this->titlebar_repeat_bottom = $display_titlebar_below;
	}
}

// this is needed to make this work with get_instance
class vcl_table extends aw_table
{
	function vcl_table($arr = array())
	{
		return $this->aw_table($arr);
	}

	function init_vcl_property($arr)
	{
		// I need access to class information!
		$pr = &$arr["property"];
		if (!is_object($pr["vcl_inst"]))
		{
			$this->set_layout("generic");
			if (is_array($arr["columns"]) && sizeof($arr["columns"]) > 0)
			{
				foreach($arr["columns"] as $ckey => $cval)
				{
					if ($cval["table"] != $pr["name"])
					{
						continue;
					};
					$this->define_field(array(
						"name" => $ckey,
						"caption" => $cval["caption"],
						"sortable" => $cval["sortable"],
					));
				};
			};
			$pr["vcl_inst"] = $this;
		};
		return array($pr["name"] => $pr);
	}

	function get_html($bare = false)
	{
		$this->sort_by();
		$rv = $this->draw();
		if (count($this->data) == 0 && count($this->rowdefs) == 0 || $bare)
		{
			return $rv;
		}

		// Let's figure out where we should show the pageselector:
		$pageselector_top = '';
		$pageselector_bottom = '';
		if ( !empty($this->parsed_pageselector) )
		{
			switch ($this->pageselector_position)
			{
				case 'top':
					$pageselector_top = $this->parsed_pageselector;
					break;
				case 'bottom':
					$pageselector_bottom = $this->parsed_pageselector;
					break;
				case 'both':
				default:
					$pageselector_top = $pageselector_bottom = $this->parsed_pageselector;

			}
		}

		// tagastame selle käki
		return '<div id="tablebox">
		    <div class="pais">
			<div class="caption">'.$this->table_caption.'</div>
			<div class="navigaator">
				<!-- siia tuleb ühel ilusal päeval lehtede kruttimise navigaator, homseks seda vaja pole, seega las see div jääb tühjaks -->
				'.$pageselector_top.'
				
			</div>
		    </div>
		    <div class="sisu">
		    <!-- SUB: GRID_TABLEBOX_ITEM -->
			'.$rv.'
		    <!-- END SUB: GRID_TABLEBOX_ITEM -->
		    </div>
		    <div>
				'.$pageselector_bottom.'
		    </div>	
		</div>';
		return $rv;
	}

	/** assumes that table columns are defined - iterates over object_list passed and reads correct props from it

		@comment
			$args can contain:

				change_col - column name with change link to object
	**/
	function data_from_ol($ol, $args = array())
	{
		$clss = aw_ini_get("classes");
		$u = new user();
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$data = array("oid" => $o->id());
			foreach($this->rowdefs as $k => $v)
			{
				if ($v["name"] == "oid")
				{
					$val = $o->id();
				}
				else
				if ($v["name"] == "createdby")
				{
					$val = $o->createdby();
				}
				else
				if ($v["name"] == "createdby_person")
				{
					$val = $o->createdby();
					// get person for user
					if ($val != "")
					{
						$val = $u->get_person_for_uid($val);
						$val = $val->name();
					}
				}
				else
				if ($v["name"] == "modifiedby")
				{
					$val = $o->modifiedby();
				}
				else
				if ($v["name"] == "modifiedby_person")
				{
					$val = $o->modifiedby();
					// get person for user
					if ($val != "")
					{
						$val = $u->get_person_for_uid($val);
						$val = $val->name();
					}
				}
				else
				if ($v["name"] == "created")
				{
					$val = $o->created();
				}
				else
				if ($v["name"] == "modified")
				{
					$val = $o->modified();
				}
				else
				if ($v["name"] == "class_id")
				{
					$val = $clss[$o->class_id()]["name"];
				}
				else
				if ($v["name"] == "ord")
				{
					$val = $o->ord();
				}
				else
				if ($v["name"] == "change")
				{
					$val = html::get_change_url($o->id(), array("return_url" => get_ru()), "Muuda");
				}
				else
				if ($v["_type"] == "rel")
				{
					$val = html::obj_change_url($o->prop($v["name"]));
				}
				else
				{
					$val = $o->prop_str($v["name"]);
				}

				if (isset($args["change_col"]) && $args["change_col"] == $v["name"])
				{
					$val = html::get_change_url($o->id(), array("return_url" => get_ru()), parse_obj_name($val));
				}
				$data[$v["name"]] = $val;
			}

			if ($this->use_chooser)
			{
				$data[$this->chooser_config["field"]] = $o->id();
			}

			$this->define_data($data);
		}
	}

	function table_from_ol(&$ol, $props, $clid)
	{
		$tmp = obj();
		$tmp->set_class_id($clid);
		$ps = $tmp->get_property_list();
		$ps["name"]["caption"] = t("Nimi");
		$ps["createdby"]["caption"] = t("Looja");
		$ps["created"]["caption"] = t("Loodud");
		$ps["modifiedby"]["caption"] = t("Muutja");
		$ps["modified"]["caption"] = t("Muudetud");
		foreach($props as $prop)
		{
			$d = array(
				"name" => $prop,
				"caption" => $ps[$prop]["caption"],
				"align" => "center",
				"sortable" => 1
			);
			if ($prop == "created" || $prop == "modified" || $ps[$prop]["type"] == "datetime_select")
			{
				$d["type"] = "time";
				$d["numeric"] = 1;
				$d["format"] = "d.m.Y H:i";
			}
			else
			if ($ps[$prop]["type"] == "date_select")
			{
				$d["type"] = "time";
				$d["numeric"] = 1;
				$d["format"] = "d.m.Y";
			}
			if ($ps[$prop]["type"] == "relpicker")
			{
				$d["_type"] = "rel";
			}
			$this->define_field($d);
		}
		$this->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
		$this->data_from_ol($ol, array("change_col" => "name"));
	}
}

include("const.aw");
$sc = new check_server_conf;
echo $sc->do_check();
?>
</html>
</body>
