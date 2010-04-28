<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/xml_editor/xml_editor.aw,v 1.5 2008/04/20 23:05:42 dragut Exp $
// xml_editor.aw - xml editor 
/*

@classinfo syslog_type=ST_XML_EDITOR relationmgr=yes no_status=1 no_comment=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

@property xml_file type=textbox field=meta method=serialize
@caption XML fail

@property xml_content type=table store=no no_caption=1
@caption xml-i sisu tabel

*/

class xml_editor extends class_base
{
	const AW_CLID = 817;

	function xml_editor()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/xml_editor/xml_editor",
			"clid" => CL_XML_EDITOR
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{

		};
		return $retval;
	}


	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _get_xml_file($arr)
	{
		if (!is_writable($arr['prop']['value']))
		{
			$arr['prop']['error'] = t('XML fail ei ole kirjutatav');
		}
	}

	function _get_xml_content($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "xml_element",
			"caption" => t("xml elements"),
		));

		$xml_file_content = file_get_contents($arr['obj_inst']->prop('xml_file'));

		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		xml_parse_into_struct($parser, $xml_file_content, $values, $index);
		xml_parser_free($parser);

		foreach ($values as $key => $value)
		{
			if ($value['type'] == 'cdata')
			{
				continue;
			}

			$str = '';
			if ($value['type'] == 'open')
			{
				$str = '&lt;'.$value['tag'].'&gt;';
			}
			if ($value['type'] == 'complete')
			{
				$str = str_repeat( '&nbsp;', (($value['level'] - 1) * 5) ).'&lt;'.$value['tag'];
				foreach ($value['attributes'] as $attr_name => $attr_value)
				{
					 $str .= '<br /> '.str_repeat('&nbsp;', (($value['level'] - 1) * 10) ).$attr_name.'="';
					 $str .= html::textbox(array(
						 'name' => 'element['.$key.']['.$attr_name.']',
						 'value' => utf8_decode($attr_value),
						 'size' => 30,
					 ));
					 $str .= '"';
				}
				$str .= ' /&gt;';
			}
			if ($value['type'] == 'close')
			{
				$str = '&lt;/'.$value['tag'].'&gt;'; 
			}

			$t->define_data(array(
				'xml_element' => $str
			));		
		}
		return PROP_OK;
	}

	function _set_xml_content($arr)
	{
		$file = $arr['obj_inst']->prop('xml_file');
		$xml_file_content = file_get_contents($file);

		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		xml_parse_into_struct($parser, $xml_file_content, $values, $index);
		xml_parser_free($parser);

		foreach ($arr['request']['element'] as $key => $value)
		{
			foreach ($value as $attr_key => $attr_value)
			{
				$values[$key]['attributes'][$attr_key] = utf8_encode($attr_value);
			}
		}

		$res_xml = '';
		foreach ($values as $tag)
		{
			if ($tag['type'] == 'open')
			{
				$res_xml .= str_repeat("\t", $tag['level'] - 1)."<".$tag['tag'].">\n";
			}
			if ($tag['type'] == 'complete')
			{
				$attribs = array();
				if (!empty($tag['attributes']))
				{
					foreach ($tag['attributes'] as $attr_key => $attr_value)
					{
						$attribs[] = $attr_key."=\"".$attr_value."\" ";
					}
				}
				$res_xml .= str_repeat("\t", $tag['level'] - 1)."<".$tag['tag']." ".implode(" ", $attribs)." />\n";
			}
			if ($tag['type'] == 'close')
			{
				$res_xml .= str_repeat("\t", $tag['level'] - 1)."</".$tag['tag'].">\n";
			}
		}

		// Maybe I should add here some checks as well if the file can be written
		// but probably it would be better to notify user when he/she sets the file
		// to change if the file is actually writable or not. --dragut@17.03.2008
		$f = fopen($file, "w");
		fwrite($f, $res_xml);
		fclose($f);
		return PROP_OK;
	}
}
?>
