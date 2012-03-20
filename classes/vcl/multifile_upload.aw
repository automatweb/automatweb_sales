<?php

class multifile_upload extends class_base implements vcl_interface, orb_public_interface
{
	const MAX_FILES = 32;

	function multifile_upload()
	{
		$this->init(array(
			"tpldir" => "vcl/multifile_upload"
		));
	}

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	function init_vcl_property($arr)
	{
		$this->read_template("multifile_upload.tpl");
		$content = "";
		$tmp = "";

		// read props from the given class
		$prop = $arr["prop"];

		$tp = $arr["prop"];
		$tp["type"] = "text";

		if (!empty($prop["max_files"]))
		{
			$i_max_files = $prop["max_files"];
		}
		else
		{
			$i_max_files = self::MAX_FILES;
		}

		if ($arr["new"] != 1)
		{
			$i = 1;
			foreach($arr["obj_inst"]->connections_from(array("type" => $arr["prop"]["reltype"])) as $file)
			{
				$fo = $file->to();
				$file_instance = $fo->instance();

				if ($fo->is_a(CL_IMAGE))
				{
					$file_url = file_exists($fo->prop("file2")) ? $file_instance->get_big_url_by_id($fo->id()) : $file_instance->get_url_by_id($fo->id());
				}
				else
				{
					$file_url = $file_instance->get_url($fo->id(), $fo->name());
				}

				$this->vars(array(
					"id" => $fo->id(),
					"counter" => $i++,
					"file_name"=>$fo -> name(),
					"file_url" => $file_url,
					"edit_url" => html::get_change_url($fo->id()),
					"delete_url" => $this->mk_my_orb("ajax_delete_obj", array("id" => $fo->id()), "multifile_upload"),
				));
				$tmp .= $this->parse('file');
			}
		}


		$this->vars(array(
			"file" => $tmp,
			"max" => $i_max_files,
		));

		$content = $this->parse();

		$tp["value"] = $content;
		return array($tp["name"] => $tp);
	}

	function process_vcl_property(&$arr)
	{
	}

	function callback_post_save($arr)
	{
		$parent = $arr["obj_inst"]->parent();
		$oid = $arr["obj_inst"]->id();
		$clid = $arr["obj_inst"]->class_id();
		$o = obj($oid);

		if(!empty($arr["prop"]["image"]))
		{
			$fi = new image();
		}
		else
		{
			$fi = new file();
		}

		$files = $fi -> add_upload_multifile("file", $parent);
		foreach ($files as $file)
		{
			$o->connect(array(
				"to" => $file["id"],
				"type" => $arr["prop"]["reltype"],
			));
		}
	}

	/**
	@attrib name=ajax_delete_obj
	@param id required type=oid acl=delete
	**/
	function ajax_delete_obj ($arr)
	{
		$o = obj($arr["id"]);
		$o -> delete();
		exit;
	}
}
