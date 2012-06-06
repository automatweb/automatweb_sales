<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/gallery/gallery_conf.aw,v 1.24 2009/01/28 12:32:37 instrumental Exp $
/*

@classinfo syslog_type=ST_GALLERY_CONF relationmgr=yes maintainer=kristo

@groupinfo imgsize caption=Piltide&nbsp;suurused
@groupinfo logo caption=Lisapilt

@default table=objects
@default field=meta
@default method=serialize

@default group=general

	@property conf_folders type=relpicker reltype=RELTYPE_FOLDER multiple=1
	@caption Kataloogid, kus konf kehtib

	@property conf_ratings type=relpicker reltype=RELTYPE_RATE multiple=1
	@caption Hindamisobjektid

	@property images_folder type=relpicker reltype=RELTYPE_IMAGES_FOLDER
	@caption Piltide asukoht

	@property def_layout type=relpicker reltype=RELTYPE_LAYOUT
	@caption Default layout:

	@property apply_image type=checkbox ch_value=1 
	@caption Kehtib ka pildiobjektidele

	@property send_from_addr type=textbox
	@caption Meili From aadress

	@property controller type=relpicker reltype=RELTYPE_CONTROLLER
	@caption Pildi muundamise kontroller

	@property comm_edit_grp type=relpicker multiple=1 reltype=RELTYPE_EDIT_GRP
	@caption Kommentaaride muutmise grupid

@default group=imgsize

	@property resize_before_crop type=checkbox ch_value=1 
	@caption Muuda suurust enne kadreerimist

	@property img_vert type=text subtitle=1
	@caption Kui pilt on k&otilde;rgem kui laiem

	@property v_tn_subimage type=checkbox ch_value=1 
	@caption Kas v&auml;ike pilt on kadreeritud

	@property v_tn_subimage_stretch type=checkbox ch_value=1 
	@caption V&auml;ikest pilti ei venitata

	@property v_subimage_stretch type=checkbox ch_value=1 
	@caption Suurt pilti ei venitata

	@property v_tn_subimage_left type=textbox size=5 
	@caption Mitu pikslit vasakult kaader algab

	@property v_tn_subimage_top type=textbox size=5 
	@caption Mitu pikslit &uuml;levalt kaader algab

	@property v_tn_subimage_width type=textbox size=5 
	@caption Kaadri laius

	@property v_tn_subimage_height type=textbox size=5
	@caption Kaadri k&otilde;rgus

	@property v_tn_width type=textbox size=5 
	@caption V&auml;ikese pildi laius

	@property v_tn_height type=textbox size=5 
	@caption V&auml;ikese pildi k&otilde;rgus

	@property v_width type=textbox size=5 
	@caption Suure pildi laius

	@property v_height type=textbox size=5 
	@caption Suure pildi k&otilde;rgus

	@property img_horiz type=text subtitle=1
	@caption Kui pilt on laiem kui k&otilde;rgem 

	@property h_tn_subimage type=checkbox ch_value=1
	@caption Kas v&auml;ike pilt on kadreeritud

	@property h_tn_subimage_stretch type=checkbox ch_value=1 
	@caption V&auml;ikest pilti ei venitata

	@property h_subimage_stretch type=checkbox ch_value=1 
	@caption Suurt pilti ei venitata

	@property h_tn_subimage_left type=textbox size=5 
	@caption Mitu pikslit vasakult kaader algab

	@property h_tn_subimage_top type=textbox size=5 
	@caption Mitu pikslit &uuml;levalt kaader algab

	@property h_tn_subimage_width type=textbox size=5 
	@caption Kaadri laius

	@property h_tn_subimage_height type=textbox size=5 
	@caption Kaadri k&otilde;rgus

	@property h_tn_width type=textbox size=5 
	@caption V&auml;ikese pildi laius

	@property h_tn_height type=textbox size=5 
	@caption V&auml;ikese pildi k&otilde;rgus

	@property h_width type=textbox size=5 
	@caption Suure pildi laius

	@property h_height type=textbox size=5 
	@caption Suure pildi k&otilde;rgus

@default group=logo

	@property insert_logo type=checkbox ch_value=1
	@caption Kas suurele pildile kleepida lisapilt

	@property logo_img type=relpicker reltype=RELTYPE_LOGO
	@caption Lisapilt

	@property logo_corner type=select 
	@caption Mis nurgas

	@property logo_dist_x type=textbox size=5 
	@caption Mitu pikslit vertikaalsest servast

	@property logo_dist_y type=textbox size=5 
	@caption Mitu pikslit horisontaalsest servast

	@property logo_transparency type=textbox size=5 
	@caption Lisapildi l&auml;bipaistvus, 0-100 (0 -0 t&auml;iesti l&auml;bipaistev)

	@property logo_text type=textbox 
	@caption Lisapildi tekst (%nimi% asendatakse galerii nimega)

	@property tn_insert_logo type=checkbox ch_value=1
	@caption Kas v&auml;ikesele pildile kleepida lisapilt

	@property tn_logo_img type=relpicker reltype=RELTYPE_LOGO
	@caption Lisapilt

	@property tn_logo_corner type=select 
	@caption Mis nurgas

	@property tn_logo_dist_x type=textbox size=5 
	@caption Mitu pikslit vertikaalsest servast

	@property tn_logo_dist_y type=textbox size=5 
	@caption Mitu pikslit horisontaalsest servast

	@property tn_logo_transparency type=textbox size=5 
	@caption Lisapildi l&auml;bipaistvus, 1-100 (1-t&auml;iesti l&auml;bipaistev)

	@property tn_logo_text type=textbox 
	@caption Lisapildi tekst (%nimi% asendatakse galerii nimega)


@reltype FOLDER value=1 clid=CL_MENU
@caption hallatav kataloog

@reltype RATE value=2 clid=CL_RATE
@caption Hindamisobjektid

@reltype IMAGES_FOLDER value=3 clid=CL_MENU
@caption galerii piltide kataloog

@reltype LOGO value=4 clid=CL_IMAGE
@caption logo pilt

@reltype LAYOUT value=5 clid=CL_LAYOUT
@caption galerii lehe layout

@reltype CONTROLLER value=6 clid=CL_FORM_CONTROLLER
@caption Kontroller

@reltype EDIT_GRP value=7 clid=CL_GROUP
@caption Muutmise grupp
*/

define("CORNER_LEFT_TOP", 1);
define("CORNER_LEFT_BOTTOM", 2);
define("CORNER_RIGHT_TOP", 3);
define("CORNER_RIGHT_BOTTOM", 4);

class gallery_conf extends class_base
{
	function gallery_conf()
	{
		$this->init(array(
			'clid' => CL_GALLERY_CONF
		));
	}

	function callback_post_save($arr)
	{
		extract($arr);
		// check table structure
		$this->db_query("DELETE FROM gallery_conf2menu WHERE conf_id = '$id'");
		$d = new aw_array($arr["obj_inst"]->prop("conf_folders"));
		$appi = $arr["obj_inst"]->prop("apply_image");
		foreach($d->get() as $fld)
		{
			$this->db_query("INSERT INTO gallery_conf2menu(menu_id, conf_id, apply_image) VALUES('$fld','$id','$appi')");
		}
	}

	function get_image_folder($id)
	{
		$obj = new object($id);
		return $obj->prop("images_folder");
	}

	function get_rate_objects($id)
	{
	/*	if(empty($id))
		{
			return "";
		}*/
		$obj = new object($id);
		return $obj->prop("conf_ratings");
	}

	function get_property(&$arr)
	{
		$prop =& $arr["prop"];
		switch($prop['name'])
		{
			case "v_tn_subimage_top":
			case "v_tn_subimage_left":
			case "v_tn_subimage_width":
			case "v_tn_subimage_height":
				if ($arr["obj_inst"]->prop("v_tn_subimage") != 1)
				{
					return PROP_IGNORE;
				}
				break;

			case "h_tn_subimage_top":
			case "h_tn_subimage_left":
			case "h_tn_subimage_width":
			case "h_tn_subimage_height":
				if ($arr["obj_inst"]->prop("h_tn_subimage") != 1)
				{
					return PROP_IGNORE;
				}
				break;

			case "logo_img":
				if ($arr["obj_inst"]->prop("insert_logo") != 1)
				{
					return PROP_IGNORE;
				}
				break;

			case "logo_corner":
				$prop["options"] = array(
					CORNER_LEFT_TOP => "&Uuml;lemine vasak",
					CORNER_LEFT_BOTTOM => "Alumine vasak",
					CORNER_RIGHT_TOP => "&Uuml;lemine parem",
					CORNER_RIGHT_BOTTOM => "Alumine parem"
				);
			case "logo_dist_x":
			case "logo_transparency":
			case "logo_dist_y":
				if ($arr["obj_inst"]->prop("insert_logo") != 1)
				{
					return PROP_IGNORE;
				}
				break;

			case "tn_logo_img":
				if ($arr["obj_inst"]->prop("tn_insert_logo") != 1)
				{
					return PROP_IGNORE;
				}
				break;

			case "tn_logo_corner":
				$prop["options"] = array(
					CORNER_LEFT_TOP => "&Uuml;lemine vasak",
					CORNER_LEFT_BOTTOM => "Alumine vasak",
					CORNER_RIGHT_TOP => "&Uuml;lemine parem",
					CORNER_RIGHT_BOTTOM => "Alumine parem"
				);
			case "tn_logo_dist_x":
			case "tn_logo_transparency":
			case "tn_logo_dist_y":
				if ($arr["obj_inst"]->prop("tn_insert_logo") != 1)
				{
					return PROP_IGNORE;
				}
				break;
		}
		return PROP_OK;
	}

	function get_default_layout($id)
	{
		if($id)
		{
			$obj = new object($id);
			return $obj->prop("def_layout");
		}
		else
		{
			return null;
		}
	}

	function do_check_tbl()
	{
		return;
		$tbld = $this->db_get_table("gallery_conf2menu");
		if (!isset($tbld["fields"]["apply_image"]))
		{
			$this->db_query("ALTER TABLE gallery_conf2menu ADD apply_image int default 0");
			$this->db_query("ALTER TABLE gallery_conf2menu ADD index apply_image(apply_image)");
		}
	}

	/** returns new image sizes based on the conf and current size

		@comment

			$conf - conf object
			$prefix - empty if big image, "tn_" if small image
			$w - width
			$h - height

	**/
	function get_xydata_from_conf($arr)
	{
		extract($arr);
		if ($w > $h)
		{
			$width = $conf->prop("h_".$prefix."width");
			$height = $conf->prop("h_".$prefix."height");
			$is_subimage = $conf->prop("h_".$prefix."subimage") == 1;
			if ($is_subimage)
			{
				$si_top = $conf->prop("h_".$prefix."subimage_top");
				$si_left = $conf->prop("h_".$prefix."subimage_left");
				$si_width = $conf->prop("h_".$prefix."subimage_width");
				$si_height = $conf->prop("h_".$prefix."subimage_height");
			}
			$dont_stretch = $conf->prop("v_".$prefix."subimage_stretch") == 1;
		}
		else
		{
			$width = $conf->prop("v_".$prefix."width");
			$height = $conf->prop("v_".$prefix."height");
			$is_subimage = $conf->prop("v_".$prefix."subimage") == 1;
			if ($is_subimage)
			{
				$si_top = $conf->prop("v_".$prefix."subimage_top");
				$si_left = $conf->prop("v_".$prefix."subimage_left");
				$si_width = $conf->prop("v_".$prefix."subimage_width");
				$si_height = $conf->prop("v_".$prefix."subimage_height");
			}
			$dont_stretch = $conf->prop("v_".$prefix."subimage_stretch") == 1;
		}

		if($dont_stretch)
		{
			// Select maximum width, height allowed so that the proportions stay unchanged.
			$ratio_w = $width ? $w / $width : 1;
			$ratio_h = $height ? $h / $height : 1;
			$ratio = max($ratio_w, $ratio_h);
			$width = (int)($w / $ratio);
			$height = (int)($h / $ratio);
		}

		// check if the user only specified one of width/height and then calc the other one
		if ($width && !$height)
		{
			if ($width{strlen($width)-1} == "%")
			{
				$height = $width;
			}
			else
			{
				$ratio = $width / $w;
				$height = (int)($h * $ratio);
			}
		}

		if (!$width && $height)
		{
			if ($height{strlen($height)-1} == "%")
			{
				$width = $height;
			}
			else
			{
				$ratio = $height / $h;
				$width = (int)($w * $ratio);
			}
		}

		if ($si_width && !$si_height)
		{
			if ($si_width{strlen($si_width)-1} == "%")
			{
				$si_height = $si_width;
			}
			else
			{
				$ratio = $si_width / $i_width;
				$si_height = (int)($h * $ratio);
			}
		}

		if (!$si_width && $si_height)
		{
			if ($si_height{strlen($si_height)-1} == "%")
			{
				$si_width = $si_height;
			}
			else
			{
				$ratio = $si_height / $h;
				$si_width = (int)($w * $ratio);
			}
		}


		if (!$width)
		{
			$width = $w;
		}
		if (!$height)
		{
			$height = $h;
		}

		// now convert to pixels
		if ($width{strlen($width)-1} == "%")
		{
			$width = (int)($w * (((int)substr($width, 0, -1))/100));
		}
		if ($height{strlen($height)-1} == "%")
		{
			$height = (int)($h * (((int)substr($height, 0, -1))/100));
		}

		if ($si_width{strlen($si_width)-1} == "%")
		{
			$si_width = (int)($width * (((int)substr($si_width, 0, -1))/100));
		}
		if ($si_height{strlen($si_height)-1} == "%")
		{
			$si_height = (int)($height * (((int)substr($si_height, 0, -1))/100));
		}
		/*if($w < $width && $h < $height)
		{
			$width = $w;
			$height = $h;
		}
		elseif($h < $height && $w > $width)
		{
			$ratio = $h / $height;
			$width = (int)($w * $ratio);
			$height = (int)($h * $ratio);
		}
		elseif($w < $width && $h > $height)
		{
			$ratio = $w / $width;
			$width = (int)($w * $ratio);
			$height = (int)($h * $ratio);
		}*/
		return array(
			"width" => $width,
			"height" => $height,
			"is_subimage" => $is_subimage,
			"si_top" => $si_top,
			"si_left" => $si_left,
			"si_width" => $si_width,
			"si_height" => $si_height
		);
	}
}
?>
