<?php

class file_obj extends _int_object
{
	const CLID = 41;

	private $_file_content = false;

	////
	// !returns file by id
	function get_file($fetch_file = true)
	{
		$file_cb = get_instance(CL_FILE);
		$ret = $this->fetch();
		$ret["id"] = $this->id();

		$ret["file"] = isset($ret["file"]) ? basename($ret["file"]) : "";
		if ($fetch_file)
		{
			if ($ret["meta"]["file_url"] != "")
			{
				$proto_find = new protocol_finder();
				$proto_inst = $proto_find->inst($ret["meta"]["file_url"]);

				$ret["content"] = $proto_inst->get($ret["meta"]["file_url"]);
				$ret["type"] = $proto_inst->get_type();
			}
			elseif ($ret["file"])
			{
				// file saved in filesystem - fetch it
				if ($this->meta("force_path") != "")
				{
					$tmp = $file_cb->get_file(array("file" => $this->prop("file")));
					if ($tmp !== false)
					{
						$ret["content"] = $tmp;
					}
				}
				else
				{
					$file = $this->check_file_path($this->prop("file"));
					$tmp = $file_cb->get_file(array("file" => $file));
					if ($tmp !== false)
					{
						$ret["content"] = $tmp;
					}
				}
			}
			else
			{
				object_loader::ds()->dequote($ret["content"]);
			}
		}

		if (aw_ini_get("user_interface.content_trans") == 1 && ($cur_lid = aw_global_get("lang_id")) != $this->lang_id())
		{
			$trs = $this->meta("translations");
			if (isset($trs[$cur_lid]))
			{
				$t = $trs[$cur_lid];
				foreach($file_cb->trans_props as $p)
				{
					$ret[$p] = $t[$p];
				}
			}
		}
		return $ret;
	}

	function check_file_path($fname)
	{
		// get the file name
		$slash = strrpos($fname, "/");
		$f1 = substr($fname, 0, $slash);

		// get the last folder
		$slash1 = strrpos($f1, "/");
		$f2 = substr($f1, $slash1+1);

		// add site basedir
		return aw_ini_get("site_basedir")."files/".$f2."/".substr($fname, $slash+1);
	}

	/** Returns the download url for the file.
		@attrib name=get_url params=pos api=1
		@param name optional type=string
		@returns
			Returns the download url for the file.
		@comment
	**/
	function get_url($name = "")
	{
		if(!$name)
		{
			$name = $this->name();
		}
		$retval = str_replace("automatweb/", "", core::mk_my_orb("preview", array("id" => $this->id()),"file", false,true,"/"))."/".urlencode(str_replace("/","_",$name));
//		$retval = $this->mk_my_orb("preview", array("id" => $id),"file", false,true);
		return $retval;
	}

	/** Set file content. Overwrites old file if exists
		@attrib api=1 params=pos
		@param content type=string
		@comment
		@returns void
		@errors
	**/
	public function set_content($content)
	{
		if (!is_string($content))
		{
			throw new awex_param_type("Invalid content parameter");
		}

		$this->_file_content = $content;
	}

	public function delete($full_delete = false)
	{
		$r = parent::delete($full_delete);

		if ($full_delete)
		{ // also delete file from file system
			$file = file::check_file_path($this->prop("file"));
			if (is_file($file))
			{
				unlink($file);
			}
		}

		return $r;
	}

	public function save($check_state = false)
	{
		if (false !== $this->_file_content)
		{
			// save file data
			//TODO
		}

		return parent::save();
	}

	/**	Returns the the object in JSON
		@attrib api=1
	**/
	public function json($encode = true)
	{
		$data = array(
			"id" => $this->id(),
			"name" => $this->prop("name"),
			"comment" => $this->prop("comment"),
			"status" => $this->prop("status"),
			"alias" => $this->prop("alias"),
			"file" => $this->prop("file"),
			"type" => $this->prop("type"),
			"file_url" => $this->prop("file_url"),
			"newwindow" => (bool)$this->prop("newwindow"),
			"show_framed" => (bool)$this->prop("show_framed"),
			"show_icon" => (bool)$this->prop("show_icon"),
		);

		$json = new json();
		return $encode ? $json->encode($data, aw_global_get("charset")) : $data;
	}
}
