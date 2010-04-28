<?php

namespace automatweb;
// document_import.aw - Dokumentide import
/*
@classinfo syslog_type=ST_DOCUMENT_IMPORT relationmgr=yes prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

	@property file type=fileupload store=no
	@caption XML Fail

	@property d_period type=relpicker reltype=RELTYPE_DOCIMP_PERIOD field=meta method=serialize
	@caption Periood

	@property found type=text store=no
	@caption Leitud dokumendid

	@property do_import type=checkbox store=no ch_value=1
	@caption Impordi

@groupinfo settings caption="Seaded"
@default group=settings

	@groupinfo xml_settings caption="XML" parent=settings
	@default group=xml_settings

		@property di_cfgform type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption Seadete vorm

		@property location_tags type=textbox field=meta method=serialize
		@caption Asukohta m&auml;&auml;ravad tagid (formaat: rubriik_aktuaalne=890,rubriik_kala=900)

		@property field_tags type=textbox field=meta method=serialize
		@caption Sisuv&auml;lju m&auml;&auml;ravad tagid (formaat: rubriik_aktuaalne=890,rubriik_kala=900)

		@property end_tag type=textbox field=meta method=serialize
		@caption dokumenti l&otilde;petav tag

		@property content_transform type=generated generator=generate_tag_fields

	@groupinfo html_settings caption="HTML" parent=settings
	@default group=html_settings

		@property root_folder type=relpicker reltype=RELTYPE_ROOT_FOLDER field=meta method=serialize
		@caption Juurkaust

		@property file_url type=textbox size=80 field=meta method=serialize
		@caption Faili URL

		@property title_pattern type=textbox size=80 field=meta method=serialize
		@caption Pealkirja muster
		@comment nt. <h1>%s</h1>

		@property content_pattern type=textbox size=80 field=meta method=serialize
		@caption Sisu muster
		@comment nt. <body>%s</body>

		@property scan_log type=table no_caption=1
		@caption Log

		@property do_html_import type=checkbox ch_value=1 store=no
		@caption Impordi

@reltype DOCIMP_PERIOD value=1 clid=CL_PERIOD
@caption Periood

@reltype CFGFORM value=2 clid=CL_CFGFORM
@caption Seadete vorm

@reltype ROOT_FOLDER value=3 clid=CL_MENU
@caption Juurkaust
*/

class document_import extends class_base
{
	const AW_CLID = 221;

	function document_import()
	{
		$this->init(array(
			"tpldir" => "import/document_import",
			"clid" => CL_DOCUMENT_IMPORT
		));
	}

	function get_property($args)
	{
		$prop = &$args["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "found":
				$prop["value"] = "";
				if ($args["obj_inst"]->meta("temp_file") != "")
				{
					$tf = $this->get_file(array("file" => $args["obj_inst"]->meta("temp_file")));
					if ($tf !== false)
					{
						$doc_list = $this->_do_import_from_string($tf, $args["obj_inst"]);

						$prop["value"] = $this->_draw_document_list_from_arr($doc_list, $args["obj_inst"]->meta("orig_filename"));
					}
				}
				if ($prop["value"] == "")
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "do_import":
				$retval = PROP_IGNORE;
				if ($args["obj_inst"]->meta("temp_file") != "")
				{
					$tf = $this->get_file(array("file" => $args["obj_inst"]->meta("temp_file")));
					if ($tf !== false)
					{
						$retval = PROP_OK;
					}
				}
				break;
		};
		return $retval;
	}

	function set_property($args = array())
	{
		$prop = &$args["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "file":
				if ($_FILES["file"]["tmp_name"] != "")
				{
					$tf = aw_ini_get("server.tmpdir")."/docimp-".gen_uniq_id().".xml";
					if (move_uploaded_file($_FILES["file"]["tmp_name"], $tf))
					{
						if ($args["obj_inst"]->meta("temp_file") != "")
						{
							@unlink($args["obj_inst"]->meta("temp_file"));
						}
						$args["obj_inst"]->set_meta("temp_file",$tf);
						$args["obj_inst"]->set_meta("orig_filename",$_FILES["file"]["name"]);
					}
					else
					{
						@unlink($tf);
					}
				}
				break;

			case "do_import":
				if ($prop["value"] == 1)
				{
					if ($args["obj_inst"]->meta("temp_file") != "")
					{
						$tf = $this->get_file(array("file" => $args["obj_inst"]->meta("temp_file")));
						if ($tf !== false)
						{
							$doc_list = $this->_do_import_from_string($tf, $args["obj_inst"]);
							$this->_save_imported_data($doc_list, $args["obj_inst"]);
							$args["obj_inst"]->set_meta("temp_file","");
							$args["obj_inst"]->set_meta("orig_filename","");
						}
					}
				}
				break;
		}
		return $retval;
	}

	function _get_scan_log($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'title',
			'caption' => t('---')
		));
		$t->define_field(array(
			'name' => 'content',
			'caption' => t('Sisu')
		));


		$scan_result = $this->_scan_html_document(array(
			'url' => $arr['obj_inst']->prop('file_url'),
			'title_pattern' => $arr['obj_inst']->prop('title_pattern'),
			'content_pattern' => $arr['obj_inst']->prop('content_pattern')
		));

		if ($scan_result === false)
		{
			return PROP_IGNORE;
		}

		$t->define_data(array(
			'title' => t('Pealkiri'),
			'content' => $scan_result['title']
		));
		$t->define_data(array(
			'title' => t('Sisu'),
			'content' => $scan_result['content']
		));

		foreach ($scan_result['links'] as $key => $link)
		{
			$checkbox = html::checkbox(array(
				'name' => 'link['.$key.']',
				'value' => 1,
				'caption' => t('Lae alla ja tee objektiks')
			));
			$t->define_data(array(
				'title' => t('Link ').$key,
				'content' => $link.$checkbox
			));
			$link_counter++;
		}
		return PROP_OK;
	}


	function _set_do_html_import($arr){
		if ($arr['prop']['value'] == 1)
		{

			$root_folder = $arr['obj_inst']->prop('root_folder');
			if (empty($root_folder))
			{
				$root_folder = $arr['obj_inst']->parent();
			}
			$url = $arr['obj_inst']->prop('file_url');

			$pathinfo = pathinfo($url);
			$url_parts = parse_url($pathinfo['dirname']);
			$folders = explode('/', $url_parts['path']);
			foreach ($folders as $folder)
			{
				if (!empty($folder))
				{
					$ol = new object_list(array(
						'parent' => $root_folder,
						'class_id' => CL_MENU,
						'name' => $folder
					));
					if ($ol->count() == 0)
					{
						$o = new object();
						$o->set_class_id(CL_MENU);
						$o->set_name($folder);
						$o->set_status(STAT_ACTIVE);
						$o->set_parent($root_folder);
						$root_folder = $o->save();
					}
					else
					{
						$root_folder = $ol->begin();
						$root_folder = $root_folder->id();
					}
				}
			}

			// now, after creating the folders. i should have the last folder id in $root_folder variable
			// so lets get the document data and put an document object there:

			$ol = new object_list(array(
				'parent' => $root_folder,
				'class_id' => CL_DOCUMENT,
				'name' => $scan_result['title']
			));

			if ($ol->count() == 0)
			{
				$doc = new object();
				$doc->set_class_id(CL_DOCUMENT);
				$doc->set_parent($root_folder);
				$doc->save();

			}
			else
			{
				$doc = $ol->begin();
			}

			$scan_result = $this->_scan_html_document(array(
				'url' => $url,
				'title_pattern' => $arr['obj_inst']->prop('title_pattern'),
				'content_pattern' => $arr['obj_inst']->prop('content_pattern')
			));

			$file_inst = new file();
			$aliasmgr = get_instance("alias_parser");
			$file_objects = array();
			if (!empty($arr['request']['link']))
			{
				foreach ($arr['request']['link'] as $key => $value)
				{
					$link = $scan_result['links'][$key];
					preg_match("/href.*=.*[\"'](.*)[\"']/imsU", $link, $matches);
					$filename = basename($matches[1]);
					$ol = new object_list(array(
						'class_id' => CL_FILE,
						'name' => urldecode($filename),
						'parent' => $root_folder
					));
					if ($ol->count() == 0)
					{
						if (strpos('http://', $filename) === false)
						{
							$file_download_url = str_replace(basename($url), $filename, $url);
						}
						else
						{
							$file_download_url = $matches[1];
						}
						$file_content = file_get_contents($file_download_url);
						$file_object_id = $file_inst->create_file_from_string(array(
							'name' => urldecode($filename),
							'parent' => $root_folder,
							'content' => $file_content
						));
						$doc->connect(array(
							'to' => $file_object_id
						));
					}
					else
					{
						$file_object = $ol->begin();
						$file_object_id = $file_object->id();
					}

					$file_objects[$file_object_id] = $link;
				}

				$aliases = $aliasmgr->get_alias_list_for_obj_as_aliasnames($doc->id());
				$content = $scan_result['content'];

				foreach ($file_objects as $oid => $link)
				{
					$content = str_replace($link, $aliases[$oid], $content);
				}
			}

			$doc->set_name($scan_result['title']);
			$doc->set_prop('content', $content);
			$doc->save();
		}
	}

	function _scan_html_document($params)
	{

		if (empty($params['url']))
		{
			return false;
		}

		$url = $params['url'];
		$title_pattern = "/".str_replace( '%s', '(.*)', preg_quote($params['title_pattern'], "/") )."/imsU";
		$content_pattern = "/".str_replace( '%s', '(.*)', preg_quote($params['content_pattern'], "/") )."/imsU";

		$file = file_get_contents($url);
		preg_match($title_pattern, $file, $matches);
		$title = $matches[1];

		preg_match($content_pattern, $file, $matches);
		$content = $matches[1];

		$trimmed_content = array();
		$content_lines = explode("\n", $content);
		foreach ($content_lines as $line)
		{
			$line = trim($line);
			if (!empty($line))
			{
				$trimmed_content[] = $line;
			}
		}
		$content = implode("\n", $trimmed_content);

		preg_match_all("/\<a(.*)\<\/a>/imsU", $content, $matches);
		$links = $matches[0];

		return array(
			'title' => $title,
			'content' => $content,
			'links' => $links
		);

	}

	function _do_import_from_string($str, $obj_inst)
	{
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,1);
//		xml_parser_set_option($parser,XML_OPTION_TARGET_ENCODING, "ISO-8859-1");
		xml_parse_into_struct($parser,$str,&$values,&$tags);
		if (($err = xml_get_error_code($parser)))
		{
			echo xml_error_string($err);
			return array();
		};
		xml_parser_free($parser);

		$docs = array();

		$location_tags = $this->_explode_tags($obj_inst->prop("location_tags"));
		$field_tags = $this->_explode_tags($obj_inst->prop("field_tags"));
		$end_tag = strtoupper($obj_inst->prop("end_tag"));
		$meta = $obj_inst->meta();

		$cur_doc = array();

		$jrk = 1;
		foreach($values as $idx => $val)
		{
			if (isset($location_tags[$val["tag"]]))
			{
				$cur_doc["parent"] = $location_tags[$val["tag"]];
			}

			if (isset($field_tags[$val["tag"]]))
			{
				$vl = $meta["pre_".$val["tag"]].$val["value"].$meta["post_".$val["tag"]];
				$cur_doc[$field_tags[$val["tag"]]] .= $vl;
			}

			if ($val["tag"] == trim($end_tag) && $val["type"] == "close")
			{
				if ($cur_doc["parent"])
				{
					$cur_doc["jrk"] = $jrk++;
					$docs[] = $cur_doc;
				}
				$cur_doc = array("parent" => $cur_doc["parent"]);
			}
		}

		return $docs;
	}

	function _draw_document_list_from_arr($list, $orig_filename)
	{
		load_vcl("table");
		$t = new aw_table(array("layout" => "generic"));
		$t->define_field(array(
			"name" => "parent",
			"caption" => t("Asukoht"),
		));
		$t->define_field(array(
			"name" => "title",
			"caption" => t("Pealkiri"),
		));
		$t->define_field(array(
			"name" => "author",
			"caption" => t("Autor"),
		));
		$t->define_field(array(
			"name" => "content",
			"caption" => t("Sisu"),
		));

		foreach($list as $doc)
		{
			$o = obj($doc["parent"]);
			$doc["parent"] = $o->path_str(array(
				"max_len" => 3
			));
			$doc["content"] = str_replace("\n","<br><br>",trim($doc["lead"]))."<br>".str_replace("\n","<br><br>",trim($doc["content"]));
			$t->define_data($doc);
		}

		return "Fail: ".$orig_filename." <br>".$t->draw();
	}

	function _save_imported_data($docs, $obj)
	{
		$per = get_instance(CL_PERIOD);
		if ($obj->prop("d_period"))
		{
			$perid = $per->get_id_for_oid($obj->prop("d_period"));
		}
		foreach($docs as $doc)
		{
			$doc["lead"] = str_replace("\n", "<br><br>", trim($doc["lead"]));
			$doc["content"] = str_replace("\n", "<br><br>", trim($doc["content"]));
			$o = obj($doc);
			$o->set_name($doc["title"]);
			if ($perid)
			{
				$o->set_period($perid);
			}
			$o->set_class_id(CL_DOCUMENT);
			$o->set_status(STAT_ACTIVE);
			$o->set_ord($doc["jrk"]);
			$o->set_site_id(aw_ini_get("site_id"));
			$o->set_meta("cfgform_id", $obj->prop("di_cfgform"));
			$o->set_meta("show_print", 1);
			$props = $o->get_property_list();
			foreach($props as $prop)
			{
				if (isset($doc[$prop["name"]]))
				{
					$o->set_prop($prop["name"],nl2br(trim($doc[$prop["name"]])));
				}
			}
			$id = $o->save();
			$this->db_query("UPDATE documents SET modified = '".time()."' WHERE docid = $id");
		}
	}

	function generate_tag_fields($arr)
	{
		$obj = obj($arr["id"]);
		$ret = array();

		$tags = $this->_explode_tags($obj->meta("field_tags"));
		foreach($tags as $tag => $fld)
		{
			$rt = 'pre_'.$tag;

			$ret[$rt] = array(
				'name' => $rt,
				'caption' => sprintf(t("Tagi %s sisu ette pane "), $tag),
				'type' => 'textbox',
				'table' => 'objects',
				'field' => 'meta',
				'method' => 'serialize',
				'group' => 'settings'
			);

			$rt = 'post_'.$tag;

			$ret[$rt] = array(
				'name' => $rt,
				'caption' => sprintf(t("Tagi %s sisu taha pane "), $tag),
				'type' => 'textbox',
				'table' => 'objects',
				'field' => 'meta',
				'method' => 'serialize',
				'group' => 'settings'
			);

		}
		return $ret;
	}

	function _explode_tags($str)
	{
		$tags = array();
		$_tags = explode(",", $str);
		foreach($_tags as $tg)
		{
			list($tag, $fld) = explode("=", $tg);
			$tag = strtoupper($tag);
			$tags[$tag] = $fld;
		}
		return $tags;
	}
}
?>
