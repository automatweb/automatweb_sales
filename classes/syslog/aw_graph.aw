<?php
// $Header: /home/cvs/automatweb_dev/classes/syslog/aw_graph.aw,v 1.3 2008/01/31 13:55:30 kristo Exp $
/*
@classinfo  maintainer=kristo
*/
class aw_graph extends aw_template
{
	function aw_graph()
	{
		$this->init("");
		lc_load("definition");
	}

	function import_data($arr)
	{
		$this->data = $arr;
	}

	function import_multi($arr)
	{
		$this->multi = $arr;
	}

	function import_color($arr)
	{
		$this->color = $arr;
	}

	// for internal use
	function _xml_start_element($parser,$name,$attribs)
	{
		switch($name)
		{
			// telgede info
			case "AXIS":
				$this->axis[$attribs[NAME]] = $attribs;
				break;

			// üldine info graafikute kohta
			case "GENERAL":
				$this->config = $attribs;
			break;

			case "BORDER":
				$this->border = $attribs;
				break;

			default:
				// do nothing
		};
	}

	function _xml_end_element($parser,$name)
	{
	}

	function _xml_data_handler($parser,$data)
	{
	}

	// graafiku kuju defineeritakse XML faili abil
	function parse_xml_def($file)
	{
		if (!$file)
		{
			print "FUBAR";
			die;
		};
		$xmldata = $this->get_file(array("file" => $this->cfg["basedir"]."/xml/$file"));
		$xml_parser = xml_parser_create();
		xml_set_object($xml_parser,&$this);
		xml_set_element_handler($xml_parser,"_xml_start_element","_xml_end_element");
		if (!xml_parse($xml_parser,$xmldata,true))
		{
			die(sprintf("XML error: %s at line %d",
			               xml_error_string(xml_get_error_code($xml_parser)),
				       xml_get_current_line_number($xml_parser)));
		};
		xml_parser_free($xml_parser);
	}

	function rgb2arr($rgb)
	{
		preg_match("/^#(..)(..)(..)$/",$rgb,$m);
		$res = array(hexdec($m[1]),hexdec($m[2]),hexdec($m[3]));
		return $res;
	}

	function find_top($arr)
	{
		while(list($k,$v) = each($arr))
		{
			if ($v > $highest)
			{
				$highest = $v;
			};
		};
		return $highest;
	}

	function draw_graph()
	{
		$width = $this->config["WIDTH"];
		$height = $this->config["HEIGHT"];
		$border = $this->border["SIZE"];
		$image = imagecreate($width,$height);
		list($r,$g,$b) = $this->rgb2arr($this->border["COLOR"]);
		$bordercolor = imagecolorallocate($image,$r,$g,$b);
		imagefill($image, 0, 0, $bordercolor);
		list($r,$g,$b) = $this->rgb2arr($this->config["BGCOLOR"]);
		$bgcolor = imagecolorallocate($image,$r,$g,$b);
		// täidame kogu pildi raami värviga
		// joonistame selle sisse sisu
		$c_left = $border;
		$c_top  = $border;
		$c_right = $width - ($border * 2);
		$c_bottom = $height - ($border * 2);
		imagefilledrectangle($image, $c_left,$c_top,$c_right,$c_bottom,$bgcolor);
		// nyyd yritame grid-i joonistada
		$gh = 20;
		$gw = 20;
		$barw = 10;
	
		$ymax = $border; // sest arvepidamine algab ülevalt
		$ymin = $height - $c_bottom; // alumine serv

		$green = imagecolorallocate($image,255,0,0);

		$highest = 0;
		if (is_array($this->multi))
		{
			$highest = 0;
			$multi = true;
			$size = 0;
			while(list($k,$v) = each($this->multi))
			{
				$data = $this->multi[$k];
				if (sizeof($data) > $size)
				{
					$size = sizeof($data);
				};
				$th = $this->find_top($data);
				if ($th > $highest)
				{
					$highest = $th;
				};
			}
		}
		else
		{
			$multi = false;
			$highest = $this->find_top($this->data);
			$size = sizeof($this->data);
		}

		// step-i peame välja arvutama siiski
		$step = round(($width / $size) - 1);
	
		$gcolor = imagecolorallocate($image,210,210,210);
		$black = imagecolorallocate($image,0,0,0);
		// vertikaalsed jooned
		$i = 0;
		for ($x = $c_left; $x <= $c_right; $x += $step)
		{
			$i++;
			imageline($image,$x,$c_top,$x,$c_bottom,$gcolor);
			imagestring($image,1,$x,$c_bottom - 7,$i,$black);
		};
		// horisontaalsed jooned
		for ($y = $c_top; $y <= $c_bottom; $y += $gh)
		{
			imageline($image,$c_left,$y,$c_right,$y,$gcolor);
			#imagestring($image,1,$c_left,$y,$y,$black);
		};
		imagestring($image,5,40,2,$this->config[TITLE],$black);

		if ($multi)
		{
			reset($this->multi);
			while(list($k,$v) = each($this->multi))
			{
				list($r,$g,$b) = $this->rgb2arr($this->color[$k]);
				$col = imagecolorallocate($image,$r,$g,$b);
				for ($i = 0; $i < $size -1; $i ++)
				{
					$left = ($i * $step);
					$bottom = $height - round(($v[$i+1] * $height) / $highest);
					$ceiling = $height - round(($v[$i] * $height) / $highest);
					imageline($image,$left,$ceiling,$left + $step,$bottom,$col);
					imageline($image,$left,$ceiling - 1,$left + $step,$bottom - 1,$col);
				};
			};
		}
		else
		{
			$data = $this->data;
			for ($i = 0; $i < $size -1; $i ++)
			{
				$left = ($i * $step);
				$bottom = $height - round(($data[$i+1] * $height) / $highest);
				$ceiling = $height - round(($data[$i] * $height) / $highest);
				imageline($image,$left,$ceiling,$left + $step,$bottom,$green);
				imageline($image,$left,$ceiling - 1,$left + $step,$bottom - 1,$green);
			};
		};
			
		if ($this->config["IMGTYPE"] == "png")
		{
			header("Content-type: image/png");
			imagepng($image);
		}
		else
		{
			header("Content-type: image/gif");
			imagegif($image);
		};	
        	imagedestroy($image);
	}
};
//$gd = new aw_graph();
//$data = array("40","67","36","46","50","44","10","70","50","35");
//$gd->import_data($data);
//$gd->draw_graph();
?>
