<?php
/*
@classinfo syslog_type=ST_CSS relationmgr=yes maintainer=kristo

@default group=general 
@default table=objects
@default field=meta
@default method=serialize

@property ffamily1 type=select
@caption Font 1

@property ffamily2 type=select
@caption Font 3

@property ffamily3 type=select
@caption Font 3

@property ffamily type=hidden 

@property italic type=checkbox ch_value=1 
@caption <i>Italic</i>

@property bold type=checkbox ch_value=1 
@caption <b>Bold</b>

@property underline type=checkbox ch_value=1
@caption <u>Underline</u>

@property size type=textbox size=5
@caption Kirja suurus (px)

@property fgcolor type=colorpicker
@caption Teksti v&auml;rv

@property bgcolor type=colorpicker 
@caption Tausta v&auml;rv

@property lineheight type=textbox size=5
@caption Joone k&otilde;rgus 

@property border type=textbox size=5
@caption &Auml;&auml;rejoone j&auml;medus

@property bordercolor type=colorpicker 
@caption &Auml;&auml;rejoone v&auml;rv

@property align type=select 
@caption Align

@property valign type=select 
@caption Valign

@property width type=textbox size=5
@caption Laius

@property height type=textbox size=5
@caption K&otilde;rgus

@property nowrap type=checkbox ch_value=1 
@caption Nowrap

@property padding type=textbox size=5 
@caption Elementide vahe (cellspacing) (px)

@property margin type=textbox size=5 
@caption Sisu kaugus joontest (cellspadding) (px)

@property a_style type=relpicker reltype=RELTYPE_CSS
@caption Lingi stiil

@property a_hover_style type=relpicker reltype=RELTYPE_CSS
@caption Lingi stiil (hover)

@property a_visited_style type=relpicker reltype=RELTYPE_CSS
@caption Lingi stiil (visited)

@property a_active_style type=relpicker reltype=RELTYPE_CSS
@caption Lingi stiil (active)

@property user_css type=textarea width=30 height=5 
@caption Kasutaja css

@property site_css type=textbox 
@caption Saidi css failis defineeritud stiil

@groupinfo table caption="Tabel"
@default group=table

@property frow_style type=relpicker reltype=RELTYPE_CSS
@caption Esimeste ridade stiil

@property num_frows type=textbox size=5 datatype=int
@caption Mitmele esimesele reale

@property fcol_style type=relpicker reltype=RELTYPE_CSS
@caption Esimeste tulpade stiil

@property num_fcols type=textbox size=5 datatype=int
@caption Mitmele esimesele tulbale

@property lrow_style type=relpicker reltype=RELTYPE_CSS
@caption Viimaste ridade stiil

@property num_lrows type=textbox size=5 datatype=int
@caption Mitmele viimasele reale

@property lcol_style type=relpicker reltype=RELTYPE_CSS
@caption Viimaste tulpade stiil

@property num_lcols type=textbox size=5 datatype=int
@caption Mitmele viimasele tulbale

@property header_style type=relpicker reltype=RELTYPE_CSS
@caption P&auml;ise stiil

@property footer_style type=relpicker reltype=RELTYPE_CSS
@caption Jaluse stiil

@property odd_style type=relpicker reltype=RELTYPE_CSS
@caption Paaritu rea stiil

@property even_style type=relpicker reltype=RELTYPE_CSS
@caption Paaris rea stiil

@groupinfo preview caption=Eelvaade
@property pre type=text group=preview no_caption=1
@caption Eelvaade

@reltype CSS value=1 clid=CL_CSS
@caption css stiil
*/

class css extends class_base
{
	private $font_families;
	private $ff;

	function css ($args = array())
	{
		$this->init(array(
			"tpldir" => "css",
			"clid" => CL_CSS
		));

		$this->lc_load("css","lc_css");

		// fondifamilyd, do not change the order
		$this->font_families = array(
			"0" => "Verdana,Helvetica,sans-serif",
			"1" => "Arial,Helvetica,sans-serif",
			"2" => "Tahoma,sans-serif",
			"3" => "serif",
			"4" => "sans-serif",
			"5" => "monospace",
			"6" => "cursive",
			"7" => "Trebuchet MS,Tahoma,sans-serif"
		);

		$this->ff = array("", "Verdana", "Helvetica", "Arial", "Tahoma", "Trbuchet MS", "sans-serif", "serif", "cursive", "monospace");
	}


	/** I could not think of another place to stick this. oh well. whatever
		@attrib name=colorpicker params=name all_args="1" default="0"
	**/
	function colorpicker($arr)
	{
		if (method_exists($this,"db_init"))
		{
			$this->db_init();
		};
		if (method_exists($this,"tpl_init"))
		{
			$this->tpl_init("automatweb");
		}
		$this->read_template("colorpicker.tpl");
		die($this->parse());
	}

	/** Generates a css style definition from the given css object
		@attrib api=1 params=pos

		@param id required type=oid
			The oid of the css style object to generate the style from 
		
		@returns
			css style definition
	**/
	function get_style_data_by_id($id)
	{
		if (!empty($id))
		{
			$style_obj = new object($id);
			$css = $style_obj->meta("css");
			if (!is_array($css) || count($css) < 1)
			{
				$css = $style_obj->meta();
			}
			return $this->_gen_css_style("st${id}",$css);
		};
	}

	/** genereerib arrayst tegeliku stiili
		name - stiili nimi, data, array css atribuutidest
	**/
	private function _gen_css_style($name,$data = array())
	{
		$retval = ".$name {\n";
		if (!(is_array($data)))
		{
			return false;
		};
		foreach($data as $key => $val)
		{
			$mask = "";
			if ($val === "")
			{
				continue;
			}
			$ign = false;
			switch($key)
			{
				case "ffamily":
					if (is_numeric($val))
					{
						$val = $this->font_families[$val];
					}
					$mask = "font-family: %s;\n";
					break;

				case "fstyle":
					$mask = "font-style: %s;\n";
					break;

				case "fweight":
					$mask = "font-weight: %s;\n";
					break;

				case "bold":
					if ($val == 1)
					{
						$mask = "font-weight: bold;\n";
					}
					else
					{
						$ign = true;
					};
					break;

				case "italic":
					if ($val == 1)
					{
						$mask = "font-style: italic;\n";
					}
					else
					{
						$ign = true;
					};
					break;

				case "fgcolor":
					$mask = "color: ".($val{0} == "#" ? "" : "#")."%s;\n";
					break;

				case "bgcolor":
					$mask = "background-color: ".($val{0} == "#" ? "" : "#")."%s;\n";
					break;

				case "underline":
					if ($val == 1)
					{
						$mask = "text-decoration: underline;\n";
					}
					else
					{
						$mask = "";
					}
					break;

				case "textdecoration":
					$mask = "text-decoration: %s;\n";
					break;

				case "lineheight":
					$mask = "line-height: %spx;\n";
					break;

				case "border":
					//$mask = "border-width: %spx;\n";
					$mask = "border-collapse: collapse;\n";
					break;
				
				case "valign":
					$mask = "vertical-align: %s;\n";
					break;
				
				case "align":
					$mask = "text-align: %s;\n";
					break;
				
				case "width":
					if (substr($val, -1) == "%")
					{
						$mask = "width: %s;\n";
					}
					else
					{
						$mask = "width: %spx;\n";
					}
					break;

				case "height":
					if (substr($val, -1) == "%")
					{
						$mask = "height: %s;\n";
					}
					else
					{
						$mask = "height: %spx;\n";
					}
					break;

				case "margin":
					$mask = "margin: %spx;\n";
					break;

				case "padding":
					$mask = "padding: %spx;\n";
					break;

				case "size":
					$mask = "font-size: %spx;\n";
					break;
	
				default:
					$ign = true;
					break;
			};

			if ($mask == "")
			{
				continue;
			}
			if (!$ign)
			{
				$retval .= sprintf("\t" . $mask,$val);
				if ($key == "border")
				{
					$has_border = true;
					$retval .= "\tborder: $data[border]px solid ";
					if (trim($data["bordercolor"]) != "")
					{
						$bc =trim($data["bordercolor"]);
						if ($bc{0} != "#")
						{
							$retval .= "#";
						}
						$retval .= $bc;
					}
					$retval .= ";\n";
				}
			};
		}

		$retval .= $data["user_css"];

		$retval .= "}\n";

		if ($has_border || trim($data["padding"]) != "" || trim($data["margin"]) != "" || trim($data["fgcolor"]) != "")
		{
			$retval .= ".$name td {\n";
			if (trim($data["border"]) != "")
			{
				$retval .= "\tborder: $data[border]px solid ";
				if (trim($data["bordercolor"]) != "")
				{
					$bc = trim($data["bordercolor"]);
					if ($bc{0} != "#")
					{
						$retval .= "#";
					}
					$retval .= $bc;
				}
				$retval .= ";\n";
			}
			if (trim($data["fgcolor"]) != "")
			{
				$retval .= "\tcolor: ".($data["fgcolor"]{0} == "#" ? "" : "#").$data["fgcolor"].";\n";
			}
			$retval .= " }\n";
		}

		if (!$this->in_gen)
		{
			$this->in_gen = true;
			if ($data["a_style"])
			{
				$retval.=$this->_gen_css_style($name." a:link",$this->get_cached_style_data($data["a_style"]));
			}
			if ($data["a_hover_style"])
			{
				$retval.=$this->_gen_css_style($name." a:hover",$this->get_cached_style_data($data["a_hover_style"]));
			}
			if ($data["a_visited_style"])
			{
				$retval.=$this->_gen_css_style($name." a:visited",$this->get_cached_style_data($data["a_visited_style"]));
			}
			if ($data["a_active_style"])
			{
				$retval.=$this->_gen_css_style($name." a:active",$this->get_cached_style_data($data["a_active_style"]));
			}
			$this->in_gen = false;
		}
		return $retval;
	}

	private function get_cached_style_data($id)
	{
		if (!aw_cache_get("AW_CSS_STYLE_CACHE",$id))
		{
			$o = obj($id);
			aw_cache_set("AW_CSS_STYLE_CACHE",$id,$o->meta("css"));
		}
		return aw_cache_get("AW_CSS_STYLE_CACHE",$id);
	}

	/** Returns a list of css styles available
		@attrib api=1 params=pos

		@param addempty optional type=bool
			If you want an empty element to be the first one in the returned array, say true here

		@returns
			array { style_id => style_name, .. } for all css styles in the system in the current language and site
	**/
	function get_select($addempty = false)
	{
		$ret = array();
		if ($addempty)
		{
			$ret[0] = "";
		}
		$ol = new object_list(array(
			"class_id" => CL_CSS,
		));
		return $ret + $ol->names();
	}

	function get_property(&$arr)
	{
		$prop =& $arr["prop"];
		if (is_numeric($ffm =$arr["obj_inst"]->prop("ffamily")))
		{
			$ffm = $this->font_families[$ffm];
		}
		list($f1,$f2,$f3) = explode(",", $ffm);

		switch($prop['name'])
		{
			case "ffamily1":
				$prop['options'] = $this->ff;
				$prop["value"] = array_search($f1, $this->ff);
				break;

			case "ffamily2":
				list(,$f) = explode(",", $arr["obj_inst"]->prop("ffamily"));
				$prop['options'] = $this->ff;
				$prop["value"] = array_search($f2, $this->ff);
				break;

			case "ffamily3":
				list(,,$f) = explode(",", $arr["obj_inst"]->prop("ffamily"));
				$prop['options'] = $this->ff;
				$prop["value"] = array_search($f3, $this->ff);
				break;

			case "align":
				$prop['options'] = array(
					"" => "",
					"left" => "Vasak",
					"center" => "Keskel",
					"right" => "Paremal"
				);
				break;

			case "valign":
				$prop['options'] = array(
					"" => "",
					"top" => "&Uuml;leval",
					"middle" => "Keskel",
					"bottom" => "All"
				);
				break;

			case "pre":
				$this->read_template("preview.tpl");
				$st = get_instance(CL_STYLE);
				$this->vars(array(
					"clname" => $st->get_style_name($arr["obj_inst"]->id())
				));
				classload("layout/active_page_data");
				active_page_data::add_site_css_style($arr["obj_inst"]->id());
				$prop['value'] = $this->parse();
				break;
		}
		return PROP_OK;
	}

	function callback_pre_edit($arr)
	{
		$cssmeta = $arr["obj_inst"]->meta("css");
		if (is_array($cssmeta) && count($cssmeta) > 0)
		{
			return;
		}
		$_t = new aw_array($cssmeta);
		foreach($_t->get() as $k => $v)
		{
			$arr["obj_inst"]->set_meta($k,$v);
		}
	}

	function callback_pre_save($arr)
	{
		// put ffamily style together from bits
		$ffm = array();
		if ($arr["obj_inst"]->prop("ffamily1") > 0 )
		{
			$ffm[] = $this->ff[$arr["obj_inst"]->prop("ffamily1")];
		}
		if ($arr["obj_inst"]->prop("ffamily2") > 0)
		{
			$ffm[] = $this->ff[$arr["obj_inst"]->prop("ffamily2")];
		}
		if ($arr["obj_inst"]->prop("ffamily3") > 0)
		{
			$ffm[] = $this->ff[$arr["obj_inst"]->prop("ffamily3")];
		}
		$ffm = join(",", $ffm);

		$arr["obj_inst"]->set_prop("ffamily", $ffm);
		$meta = $arr["obj_inst"]->meta();
		$cssmeta = array(); ;
		$_t = new aw_array($meta);
		foreach($_t->get() as $k => $v)
		{
			$cssmeta[$k] = $v;
		}
		unset($cssmeta["css"]);
		$arr["obj_inst"]->set_meta("css",$cssmeta);
	}
};