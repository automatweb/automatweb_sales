<?php

/** This writes data about classes to the docgen database. The tables in that are:

aw_da_func_data - fulltext search index, all data from analyze_file dumped into one string
aw_da_callers - data about what function calls what function
aw_da_classes - data about all classes/interfaces, inheritance and implements, maintainer, has_apis
aw_da_funcs - data about functions; where defined
aw_da_func_attribs - all function attributes from doc comments are written here

**/
class docgen_db_writer extends db_connector
{
	private $handler_classes = array();

	function docgen_db_writer()
	{
		$this->init();
	}

	/** updates the class/function definitions in the database

		@attrib name=do_db_update
	**/
	function do_db_update()
	{
		foreach(class_index::get_classes_by_interface("docgen_index_module") as $class_name)
		{
			$this->handler_classes[] = get_instance($class_name);
		}
		$this->_run_handlers("handle_index_start", array());

		$files = array();
		$p = new parser();
		$p->_get_class_list($files, aw_ini_get("classdir"));

		while (count($files))
		{
			$file = array_shift($files);
			$da = new docgen_analyzer_simple_db_writer();
			$data = $da->analyze_file($file, true);
			$da = null;
			$this->_write_one_file($file, $data);
		}

		die(t("ALL DONE"));
	}

	private function _run_handlers($func, $params)
	{
		foreach($this->handler_classes as $cl)
		{
			call_user_func_array(array($cl, $func), $params);
		}
	}


	private function _write_one_file($file, $data)
	{
		$rel_file = str_replace(aw_ini_get("basedir"), "",$file);

		$this->_run_handlers("handle_index_file", array($rel_file, $data));

		// write class/func/caller data
		$this->db_query("DELETE FROM aw_da_classes WHERE file = '$rel_file'");
		if (isset($data["classes"]))
		{
			foreach(safe_array($data["classes"]) as $class => $c_data)
			{
				$this->_write_one_class($rel_file, $class, $c_data);
			}
		}
	}

	private function _write_one_class($rel_file, $class, $c_data)
	{
		$this->_run_handlers("handle_index_class", array($rel_file, $class, $c_data));

		$this->db_query("DELETE FROM aw_da_funcs WHERE class = '{$class}'");
		$this->db_query("DELETE FROM aw_da_func_attribs WHERE class = '{$class}'");

		echo "writing class {$class}... <br>\n";
		flush();
		$has_apis = "0";
		if (isset($c_data["functions"]))
		{
			foreach(safe_array($c_data["functions"]) as $fname => $fdata)
			{
				$this->_write_one_function($rel_file, $class, $fname, $fdata);
				$has_apis |= !empty($fdata["doc_comment"]["attribs"]["api"]);
			}
		}

		$implements = (isset($c_data["implements"]) and is_array($c_data["implements"])) ? implode(",", $c_data["implements"]) : "";
		$extends = isset($c_data["extends"]) ? $c_data["extends"] : "";
		$type = isset($c_data["type"]) ? $c_data["type"] : "";
		$this->db_query("INSERT INTO aw_da_classes(file, class_name, extends, implements, class_type, has_apis)
			VALUES('{$rel_file}','{$class}','{$extends}', '{$implements}', '{$type}', {$has_apis}) ");
	}

	private function _write_one_function($rel_file, $class, $fname, $fdata)
	{
		$this->_run_handlers("handle_index_method", array($rel_file, $class, $fname, $fdata));

		$this->db_query("INSERT INTO aw_da_funcs(class,func, ret_class, file)
			values(
				'{$class}',
				'{$fname}',
				'". (isset($fdata["return_var"]["class"]) ? $fdata["return_var"]["class"] : "") . "',
				'{$rel_file}'
			)
		");

		// also attribs
		$docc = isset($fdata["doc_comment"]["attribs"]) ? safe_array($fdata["doc_comment"]["attribs"]) : array();
		foreach($docc as $aname => $avalue)
		{
			$this->db_query("
				INSERT INTO aw_da_func_attribs(class,func,attrib_name,attrib_value)
					VALUES('{$class}','{$fname}','{$aname}','{$avalue}')
			");
		}
	}
}

/** plugin interface for docgen db writer - implement this and you will get called whenever the index is generated **/
interface docgen_index_module
{
	/** This will be called once every time the index is generated **/
	function handle_index_start();

	/** This will be called for every file indexed
		@attrib api=1 params=pos

		@param rel_file required type=string
			The file currently being indexed, relative to basedir

		@param data required type=array
			Data returned by aw_code_analyzer for the file
	**/
	function handle_index_file($rel_file, $data);

	/** This will be called for every class indexed
		@attrib api=1 params=pos

		@param rel_file required type=string
			The file currently being indexed, relative to basedir

		@param class required type=string
			The name of the class being processed

		@param c_data required type=array
			The data for the class, returned by aw_code_analyzer
	**/
	function handle_index_class($rel_file, $class, $c_data);

	/** This will be called for every method or function indexed
		@attrib api=1 params=pos

		@param rel_file required type=string
			The file currently being indexed, relative to basedir

		@param class required type=string
			The name of the class being processed

		@param fname required type=string
			The name of the method/function being processed

		@param fdata required type=array
			The data for the method/function, returned by aw_code_analyzer
	**/
	function handle_index_method($rel_file, $class, $fname, $fdata);
}
