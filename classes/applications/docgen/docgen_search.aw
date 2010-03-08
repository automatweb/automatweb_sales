<?php

/** Docgen search class - can search by mostly everything, search options defined in $this->search_options **/
class docgen_search extends class_base
{
	// docgen instance for style template
	private $docgen;

	function docgen_search()
	{
		$this->init("core/docgen");
		$this->docgen = get_instance("docgen_viewer");
	}

	/**	
		@attrib name=doc_search_form all_args=1
	**/
	function doc_search_form($arr)
	{
		$search[] = t("Search word(s)").":";
		$search[] = html::textbox(array(
			"name"=>"search", 
			"value" => isset($arr["search"]) ? $arr["search"] : "",
		));

		// list all search interface classes and let them work it
		foreach(class_index::get_classes_by_interface("docgen_search_module") as $class_name)
		{
			if (!isset($arr["from"]) && (!isset($done) || $done !== true))
			{
				$arr["from"] = array($class_name);
				$done = true;
			}
			
			$search[] = html::checkbox(array(
				"name" => "from[]", 
				"value" => $class_name, 
				"checked" => in_array($class_name, $arr["from"])
			))." ".get_instance($class_name)->get_module_name();
		}

		$search[] = html::submit(array(
			"value" => t("Search")
		));

		$search[] = '';
	
		$search[] = $this->mk_reforb("doc_search_form", array("no_reforb" => 1));

		$ret = html::form(array(
			"action" => $this->mk_my_orb("doc_search_form"),
			"method" => "get",
			"content" => implode('<br />', $search),
		));

		if (isset($arr["search"]) && $arr["search"] != "")
		{
			$ret .= "<hr/>".$this->_fetch_search_results($arr);
		}

		die($this->docgen->finish_with_style($ret));
	}

	private function _fetch_search_results($arr)
	{
		$res = array();

		foreach(class_index::get_classes_by_interface("docgen_search_module") as $class_name)
		{
			if (in_array($class_name, $arr["from"]))
			{
				$i = get_instance($class_name);
				$res[$class_name] = $i->do_search($arr);
			}
		}

		$ret = "";
		foreach($res as $res_key => $res_arr)
		{
			$ret .= "<b>".(get_instance($res_key)->get_module_name())."</b><br/>";
			$ret .= join("<br/>", safe_array($res_arr))."<br/><br/>";
		}

		return $ret;
	}
}

/** Docgen search plugin interface definiion **/
interface docgen_search_module
{
	/** this must return the module name as a string **/
	function get_module_name();
	
	/** Should do the search and return array of results **/
	function do_search($arr);
}
