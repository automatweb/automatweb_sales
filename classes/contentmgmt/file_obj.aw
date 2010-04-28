<?php

namespace automatweb;

/*

@classinfo maintainer=markop

*/
class file_obj extends _int_object
{
	const AW_CLID = 41;

	////
	// !returns file by id
	function get_file($fetch_file = true)
	{
		$file_cb = new file();
		$ret = $this->fetch();
		$ret["id"] = $this->id();

		$ret["file"] = basename($ret["file"]);
		if ($fetch_file)
		{
			if ($ret["meta"]["file_url"] != "")
			{
				$proto_find = get_instance("protocols/protocol_finder");
				$proto_inst = $proto_find->inst($ret["meta"]["file_url"]);

				$ret["content"] = $proto_inst->get($ret["meta"]["file_url"]);
				$ret["type"] = $proto_inst->get_type();
			}
			else
			if ($ret["file"] != "")
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
				$this->dequote($ret["content"]);
			};
		}

		if (aw_ini_get("user_interface.content_trans") == 1 && ($cur_lid = aw_global_get("lang_id")) != $tmpo->lang_id())
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
		return aw_ini_get("site_basedir")."/files/".$f2."/".substr($fname, $slash+1);
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
		$retval = str_replace("automatweb/","",$GLOBALS["object_loader"]->mk_my_orb("preview", array("id" => $this->id()),"file", false,true,"/"))."/".urlencode(str_replace("/","_",$name));
//		$retval = $this->mk_my_orb("preview", array("id" => $id),"file", false,true);
		return $retval;
	}

}
?>
