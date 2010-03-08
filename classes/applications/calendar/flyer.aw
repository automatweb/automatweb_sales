<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/flyer.aw,v 1.8 2007/12/06 14:32:55 kristo Exp $
// flyer.aw - Flaier 
/*

@classinfo syslog_type=ST_FLYER no_comment=1 no_status=1 maintainer=kristo

@tableinfo images index=id master_table=objects master_index=oid

@default table=objects
@default group=general

@property file1 type=fileupload table=images form=+emb field=file
@caption Flaier

@property dimensions1 type=text store=no
@caption Mõõtmed

@property file2 type=fileupload table=objects field=meta method=serialize form=+emb
@caption Flaier suurelt

@property dimensions2 type=text store=no
@caption Mõõtmed

@property file3 type=fileupload table=objects field=meta method=serialize form=+emb
@caption Flaieri teine külg

@property dimensions3 type=text store=no
@caption Mõõtmed

@property file3_del type=checkbox ch_value=1 store=no
@caption Kustuta teine külg

*/

class flyer extends class_base
{
	/**
		@attrib name=show_flyer nologin=1
		@param id required type=int acl=edit
		@param side optional
	**/
	function show_flyer($arr)
	{
		$obj = obj($arr["id"]);
		$side = 2;
		if($arr["side"] == 3)
		{
			$side = 3;
		}
		$file3 = $obj->prop("file3");
		if(!empty($file3))
		{
			$image = html::href(array(
				"url" => $this->mk_my_orb("show_flyer", array(
					"id" => $obj->id(), 
					"side" => (($arr["side"] == 2 || empty($arr["side"])) ? 3 : 2),
				), CL_FLYER, false ,true),
				"caption" => html::img(array(
					"url" => $this->image->get_url($obj->prop("file{$side}")),
					"border" => 0,
				)),
			));
		}
		else
		{
			$image = html::img(array(
				"url" => $this->image->get_url($obj->prop("file{$side}")),
				"border" => 0,
			));
		}
		
		$this->read_template("flyer_show.tpl");
		$this->vars(array(
			"name" => $obj->name(),
			"image" => $image,
		));
		return $this->parse();
	}
	
	function flyer()
	{
		$this->init(array(
			"tpldir" => "applications/calendar",
			"clid" => CL_FLYER
		));
		$this->image = get_instance(CL_IMAGE);
	}
	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "file1":
			case "file2":
			case "file3":
				$url = $this->image->get_url($arr["obj_inst"]->prop($prop["name"]));
				if ($url != "")
				{
					$prop["value"] = html::img(array("url" => $url));
				}
				else
				{
					$prop["value"] = "";
				};
				break;
				
			case "dimensions1":
			case "dimensions2":
			case "dimensions3":
				if(!$prop["value"] = $this->_get_size($arr))
				{
					return PROP_IGNORE;
				}
				break;
		};
		return $retval;
	}
	
	function _get_size($arr)
	{
		$x = (int)$arr["prop"]["name"]{(strlen($arr["prop"]["name"]) - 1)};
		$fl = $arr["obj_inst"]->prop("file{$x}");
		if (!empty($fl))
		{
			$fl = basename($fl);
			if ($fl{0} != "/")
			{
				$fl = $this->cfg["site_basedir"]."/files/".$fl{0}."/".$fl;
			}
			$sz = @getimagesize($fl);
			if($arr["request"])
			{
				return $sz[0] . " X " . $sz[1];
			}
			else
			{
				return array("width" => $sz[0], "height" => $sz[1]); 
			}
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "file1":
			case "file2":
			case "file3":
				$x = (int)$prop["name"]{(strlen($prop["name"]) - 1)};
				if ($arr["request"]["file3_del"] == 1 && $prop["name"] == "file3")
				{
					$prop['value'] = '';
				}
				else
				{
					if (isset($_FILES["file{$x}"]["tmp_name"]))
					{
						$src_file = $_FILES["file{$x}"]["type"];
						$ftype = $_FILES["file{$x}"]["type"];
					}
					else
					if (isset($prop["value"]["tmp_name"]))
					{
						$src_file = $prop["value"]["tmp_name"];
						$ftype = $prop["value"]["type"];
					};

					if (is_uploaded_file($src_file))
					{
						$_fi = get_instance(CL_FILE);
						$final_name = $_fi->generate_file_path(array(
							"type" => $ftype,
						));
						move_uploaded_file($src_file,$final_name);
						$prop["value"] = $final_name;

						if ($arr["obj_inst"]->name() == "")
						{
							$arr["obj_inst"]->set_name($prop["value"]["name"]);
						}
					}
					else
					{
						$retval = PROP_IGNORE;
					};
				}
				break;

		}
		return $retval;
	}
	
	function show($obj)
	{
		$mes = $this->_get_size(array(
			"prop" => array(
				"name" => "file2",
			),
			"obj_inst" => $obj,
		));
		return html::popup(array(
			"caption" => html::img(array(
				"url" => $this->image->get_url($obj->prop("file1")),
				"border" => 0,
			)),
			"width" => $mes["width"],
			"height" => $mes["height"],
			"url" => $this->mk_my_orb("show_flyer", array("id" => $obj->id()), CL_FLYER, false ,true),
			"resizable" => 1,
		));
	}
}
?>
