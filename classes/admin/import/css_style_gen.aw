<?php
/*
@classinfo syslog_type=ST_CSS_STYLE_GEN relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta 
@default method=serialize

@property css_file type=textbox
@caption CSS Faili URL

@property folder type=relpicker reltype=RELTYPE_FOLDER
@caption Kataloog stiilide jaoks

@property do_gen type=checkbox ch_value=1
@caption Genereeri!

@reltype FOLDER value=1 clid=CL_MENU
@caption kataloog
*/

class css_style_gen extends class_base
{
	function css_style_gen()
	{
		$this->init(array(
			"tpldir" => "admin/css_style_gen",
			"clid" => CL_CSS_STYLE_GEN
		));
	}

	function callback_post_save($arr)
	{
		$o = $arr["obj_inst"];
		if ($o->prop("css_file") && $o->prop("folder") && $o->prop("do_gen"))
		{
			$o->set_prop("do_gen", 0);
			$o->save();

			$styls = $this->_get_style_names($o->prop("css_file"));
			
			// get existing styles
			$ex = array();
			$ol = new object_list(array(
				"parent" => $o->prop("folder"),
				"class_id" => CL_CSS
			));
			foreach($ol->arr() as $o)
			{
				if ($o->prop("site_css") != "")
				{
					$ex[$o->prop("site_css")] = $o->id();
				}
			}

			foreach($styls as $styl)
			{
				if (!$ex[$styl])
				{
					$s = new object();
					$s->set_class_id(CL_CSS);
					$s->set_name($styl);
					$s->set_parent($o->prop("folder"));
					$s->set_prop("site_css", $styl);
					$s->save();
				}
			}
		}
	}

	private function _get_style_names($url)
	{
		$ret = array();

		$fc = file_get_contents($url);
		preg_match_all("/^(.*)\{(.*)\}/imsU", $fc, $mt, PREG_PATTERN_ORDER);
		foreach($mt[1] as $styln)
		{
			$styln = trim($styln);
			if ($styln{0} != ".")
			{
				continue;
			}
			list($styln) = explode(" ", $styln);
			$styln = substr($styln, 1);

			$ret[$styln] = $styln;
		}
		return $ret;
	}
}
?>
