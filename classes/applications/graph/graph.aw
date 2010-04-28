<?php

namespace automatweb;
// graph.aw - graafikute haldamine

/*

@classinfo no_status=1 relationmgr=yes syslog_type=ST_GRAPH maintainer=kristo

@tableinfo graphs index=id

@default group=general

	@property type type=select table=graphs
	@caption T&uuml;&uuml;p

@default group=data_settings

	@property ycount type=textbox size=5 table=graphs
	@caption Andmegruppe

@default table=objects
@default field=meta
@default method=serialize
@default group=data_entry

	@property x_axis_data type=textbox
	@caption X-Telje andmed
	@comment Eralda andmed komaga

	@property y_axis_data type=table store=no
	@caption Y-Telje andmed
	@comment Eralda andmed komaga

@default group=data_pie

	@property pie_labels type=textbox default="Mai,Juuni,Juuli,August,September"
	@caption Kirjeldused

	@property pie_data type=textbox default="5,13,18,7,3"
	@caption V&auml;&auml;rtused

@default group=settings

	@property title type=textbox default=Pealkiri group=settings,settings_pie
	@caption Pealkiri

	@property title_col type=colorpicker default=000000 group=settings,settings_pie
	@caption Pealkirja v&auml;rv

	@property back_col type=colorpicker default=aabbaa group=settings,settings_pie
	@caption Tausta v&auml;rv

	@property width type=textbox size=5 default=300 group=settings,settings_pie
	@caption Laius

	@property height type=textbox size=5 default=200 group=settings,settings_pie
	@caption K&otilde;rgus

	@property frame type=textbox size=5 default=3
	@caption Raam

	@property inside type=textbox size=5 default=30
	@caption Sisemine laius

	@property y_axis_text type=textbox default=Arv
	@caption Y Telje tekst

	@property y_axis_col type=colorpicker default=FF0000
	@caption Y Telje teksti v&auml;rv

	@property show_y_val type=checkbox ch_value=1 default=1
	@caption N&auml;itan Y teljel max ja min v&auml;&auml;rtusi

	@property x_axis_text type=textbox default=Aeg
	@caption X Telje tekst

	@property x_axis_col type=colorpicker default=000000
	@caption X Telje teksti v&auml;rv

	@property y_grid type=textbox size=5 default=6
	@caption Y telje &uuml;hikute arv

	@property y_grid_col type=colorpicker default=0000FF
	@caption Y Telje &uuml;hikute v&auml;rv

	@property show_grid_val type=checkbox ch_value=1 default=1
	@caption N&auml;itan gridil v&auml;&auml;rtusi

	@property fir_col type=colorpicker default=00FF00
	@caption Andmede v&auml;rv

@default group=settings_pie

	@property radius type=textbox size=5 default=50
	@caption Raadius

	@property percentage type=checkbox ch_value=1 default=1
	@caption N&auml;itan piruka peal protsente

	@property showlabels type=checkbox ch_value=1 default=1
	@caption N&auml;itan kirjeldusi

@default group=preview

	@property img type=text store=no no_caption=1

@groupinfo data caption="Andmed"
	@groupinfo data_settings caption="M&auml;&auml;rangud" parent=data
	@groupinfo data_entry caption="Sisestamine" parent=data

@groupinfo data_pie caption="Andmed"

@groupinfo settings caption="Seaded"
@groupinfo settings_pie caption="Seaded"
@groupinfo preview caption="Eelvaade" submit=no
*/
define("TYPE_PIE",0);
define("TYPE_LINE",1);
define("TYPE_BAR",2);

class graph extends class_base
{
	const AW_CLID = 28;

	//mis tyypi pilte me siin 6ieti loome
	var $outputimagetype;

	function graph()
	{
		$this->init(array(
			"tpldir" => "",
			"clid" => CL_GRAPH
		));
		$this->outputimagetype = $this->cfg["image_type"];

		$this->graph_types = array(
			TYPE_BAR => t("Tulbad"),
			TYPE_LINE => t("Jooned"),
			TYPE_PIE => t("Tort")
		);
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "type":
				$prop["options"] = $this->graph_types;
				break;

			case "y_axis_data":
				$this->_y_axis_data($arr);
				break;

			case "img":
				$prop["value"] = html::img(array(
					"url" => $this->mk_my_orb("show", array("id" => $arr["obj_inst"]->id()))
				));
				break;
		}
		return PROP_OK;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "y_axis_data":
				$arr["obj_inst"]->set_meta("d", $arr["request"]["d"]);
				break;
		}
		return PROP_OK;
	}

	function _init_y_axis_data(&$t)
	{
		$t->define_field(array(
			"name" => "num",
			"caption" => t("Grupp"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "data",
			"caption" => t("Andmed"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "color",
			"caption" => t("V&auml;rv"),
			"align" => "center"
		));
	}

	function _y_axis_data($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_y_axis_data($t);

		$d = safe_array($arr["obj_inst"]->meta("d"));

		for($i = 0; $i < $arr["obj_inst"]->prop("ycount"); $i++)
		{
			$t->define_data(array(
				"num" => "Y_".$i,
				"data" => html::textbox(array(
					"name" => "d[$i][data]",
					"value" => $d[$i]["data"]
				)),
				"color" => html::textbox(array(
					"name" => "d[$i][color]",
					"size" => 7,
					"value" => $d[$i]["color"]
				)),
			));
		}
		$t->set_sortable(false);
	}

	function parse_alias($args = array())
	{
		extract($args);
		return "<img src='".$this->mk_my_orb("show", array("id" => $alias["target"]), "graph",false,true)."'>";
	}

	/**

		@attrib name=show params=name default="0"

		@param id required

		@returns


		@comment

	**/
	function show($ar)
	{
		$o = obj($ar["id"]);
		switch ($o->prop("type"))
		{
			case TYPE_PIE:
				die($this->pie_show($ar["id"]));
				break;

			case TYPE_BAR:
				$p = $this->show_line_bar($ar["id"]);
				header("Content-type: image/".$this->outputimagetype);
				$create = "image".$this->outputimagetype;
				eval("$create(\$p);");
				imagedestroy($p);
				die;
				break;

			case TYPE_LINE:
				$p = $this->show_line_bar($ar["id"]);
				header('Content-type: image/'.$this->outputimagetype);
				$create = "image".$this->outputimagetype;
				eval("$create(\$p);");
				imagedestroy($p);
				die;
				break;
			default:
				break;
		}
	}

	function pie_show($id)
	{
		$o = obj($id);
		classload("applications/graph/tt_pie");
		$p = new PieGraph(2,5,1);

		$p->GraphBase($o->prop("width"),$o->prop("height"),$o->prop("back_col"));
		$p->parsedata(array(
			"labels" => $o->prop("pie_labels"),
			"data" => $o->prop("pie_data")
		));
		$p->create($o->prop("radius"),$o->prop("percentage"),$o->prop("showlabels"));
		$p->title($o->prop("title"),$o->prop("title_col"));

		header("Content-type: image/png");
		imagepng($p->image);
		die;
	}

	//K6igetegija bari ja line jaoks, peamiselt teeb valmis pildi ja returnib image pointeri.
	function show_line_bar($id)
	{
		if ($id)
		{
			$o = obj($id);

			switch ($o->prop("type"))
			{
				case TYPE_BAR:
					classload("applications/graph/tt_bar");
					$type="bar";
					$Im = new BarGraph(1,$o->prop("inside"),$o->prop("frame"));
					break;

				case TYPE_LINE:
					classload("applications/graph/tt_line");
					$type="line";
					$Im = new LineGraph(1,$setup["inside"],$setup["frame"]);
					break;

				default:
					break;
			}

			/*
			* Joonistame Base valmis, kus on borderid ja asjad k6ik paigas
			*/
			$Im->GraphBase($o->prop("width"),$o->prop("height"),$o->prop("back_col"));

			$data = $this->_get_data($o);
			$Im->parseData($data["xdata"],$data["ydata"]);
			$Im->title($o->prop("title"),$o->prop("title_col"));
			$Im->xaxis($data["xdata"],$o->prop("x_axis_text"),$o->prop("x_axis_col"));

			$o->prop("show_grid_val") ? $drawg = true : $drawg = false;
			$Im->grid($o->prop("y_grid"),$drawg,$o->prop("y_grid_col"));

			$o->prop("show_y_val")  ? $drawy = true : $drawy = false;
			$Im->yaxis($drawy,$o->prop("y_axis_text"),$o->prop("y_axis_col"),$o->prop("y_grid_col"));

			if ($type=="bar")
			{
				$Im->makeBar($data["ydata"],$data["ycol"]);
			}
			else
			{
				for($i=0;$i<count($data["ydata"]);$i++)
				{
					$Im->makeLine($data["ydata"]["ydata_".$i], $data["ycol"]["ycol_".$i]);
				}
			}

			$image=$Im->getImage();
			return $image;
		}
	}

	function _get_data($o)
	{
		$ydata = array();
		$_data = $o->meta("d");
		foreach(safe_array($_data) as $entry)
		{
			$ydata["ydata_".(int)$num] = explode(",", $entry["data"]);
			$ydata["ycol_".(int)$num] = $entry["color"];
			$num++;
		}

		return array(
			"xdata" => explode(",", $o->prop("x_axis_data")),
			"ydata" => $ydata
		);
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "settings" && $arr["obj_inst"]->prop("type") == TYPE_PIE)
		{
			return false;
		}
		if ($arr["id"] == "settings_pie" && $arr["obj_inst"]->prop("type") != TYPE_PIE)
		{
			return false;
		}
		if ($arr["id"] == "data" && $arr["obj_inst"]->prop("type") == TYPE_PIE)
		{
			return false;
		}
		if ($arr["id"] == "data_pie" && $arr["obj_inst"]->prop("type") != TYPE_PIE)
		{
			return false;
		}
		return true;
	}
};
?>
