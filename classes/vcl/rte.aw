<?php
// $Header: /home/cvs/automatweb_dev/classes/vcl/rte.aw,v 1.20 2009/08/13 08:20:52 instrumental Exp $
// rte.aw - Rich Text Editor 
/*

@classinfo syslog_type=ST_RTE relationmgr=yes maintainer=hannes

@default table=objects
@default group=general

*/

class rte extends aw_template
{
	function rte()
	{
		$this->init(array(
			"tpldir" => "rte",
//			"clid" => CL_RTE
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
	}	

	*/
	
	/**
		@attrib name=table_dialog all_args="1"
	**/
	function table_dialog($arr)
	{
		$this->read_template("insert_table.tpl");
		die($this->parse());
	}

	/**
		@attrib name=link_dialog
	**/
	function link_dialog($arr)
	{
		$this->read_template("insert_link.tpl");
		die($this->parse());
	}

	/** Adds RTE buttons to given toolbar

		@attrib name=get_rte_toolbar params=name api=1

		@param toolbar required type=object
			Toolbar object
		@errors 
			none

		@returns 
			none

		@comment 
			none
		@examples
			none
	**/
	function get_rte_toolbar($arr)
	{

		$toolbar = &$arr["toolbar"];
		$toolbar->add_separator();

		$js_url_prefix = "";
		if (!empty($arr["target"]))
		{
			$js_url_prefix = "parent.contentarea.";
		};
		if($arr["no_rte"] == 1)
		{
			$toolbar->add_button(array(
				"name" => "source",
				"tooltip" => t("RTE"),
				"target" => "_self",
				"url" => aw_url_change_var("no_rte", ""),
			));
		}
		else
		{
			$toolbar->add_button(array(
				"name" => "bold",
				"tooltip" => t("Bold"),
				"url" => "javascript:${js_url_prefix}format_selection('bold');",
				"img" => "rte_bold.gif",
			));

			$toolbar->add_button(array(
				"name" => "italic",
				"tooltip" => t("Italic"),
				"url" => "javascript:${js_url_prefix}format_selection('italic');",
				"img" => "rte_italic.gif",
			));

			$toolbar->add_button(array(
				"name" => "underline",
				"tooltip" => t("Underline"),
				"url" => "javascript:${js_url_prefix}format_selection('underline');",
				"img" => "rte_underline.gif",
			));

			$toolbar->add_separator();

			$toolbar->add_button(array(
				"name" => "align_left",
				"tooltip" => t("Align left"),
				"url" => "javascript:${js_url_prefix}format_selection('justifyleft');",
				"img" => "rte_align_left.gif",
			));

			$toolbar->add_button(array(
				"name" => "align_center",
				"tooltip" => t("Align center"),
				"url" => "javascript:${js_url_prefix}format_selection('justifycenter');",
				"img" => "rte_align_center.gif",
			));

			$toolbar->add_button(array(
				"name" => "align_right",
				"tooltip" => t("Align right"),
				"url" => "javascript:${js_url_prefix}format_selection('justifyright');",
				"img" => "rte_align_right.gif",
			));

			$toolbar->add_separator();

			$toolbar->add_button(array(
				"name" => "num_list",
				"tooltip" => t("Numbered list"),
				"url" => "javascript:${js_url_prefix}format_selection('insertorderedlist');",
				"img" => "rte_num_list.gif",
			));

			$toolbar->add_button(array(
				"name" => "bul_list",
				"tooltip" => t("Bulleted list"),
				"url" => "javascript:${js_url_prefix}format_selection('insertunorderedlist');",
				"img" => "rte_bul_list.gif",
			));

			$toolbar->add_separator();

			$toolbar->add_button(array(
				"name" => "outdent",
				"tooltip" => t("Outdent"),
				"url" => "javascript:${js_url_prefix}format_selection('outdent');",
				"img" => "rte_outdent.gif",
			));

			$toolbar->add_button(array(
				"name" => "indent",
				"tooltip" => t("Indent"),
				"url" => "javascript:${js_url_prefix}format_selection('indent');",
				"img" => "rte_indent.gif",
			));
			
			$toolbar->add_separator();

			$toolbar->add_button(array(
				"name" => "link",
				"tooltip" => t("link"),
				"url" => "javascript:${js_url_prefix}link_dialog();",
				"img" => "rte_link.gif",
			));


			$toolbar->add_separator();

			$toolbar->add_menu_button(array(
				"name" => "table_props",
				"tooltip" => t("Tabel"),
				"img" => "rte_table.gif",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "table_props",
				"text" => t("Lisa tabel"),
				"url" => "javascript:document.getElementById('table_props').style.visibility='hidden';{$js_url_prefix}table_dialog();",
			));
			
			$toolbar->add_menu_separator(array("parent" => "table_props"));

			$toolbar->add_menu_item(array(
				"parent" => "table_props",
				"text" => t("Lisa tulp"),
				"url" => "javascript:document.getElementById('table_props').style.visibility='hidden';{$js_url_prefix}insert_column();",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "table_props",
				"text" => t("Lisa rida"),
				"url" => "javascript:document.getElementById('table_props').style.visibility='hidden';{$js_url_prefix}insert_row();",
			));

			$toolbar->add_menu_separator(array("parent" => "table_props"));
			
			$toolbar->add_menu_item(array(
				"parent" => "table_props",
				"text" => t("Kustuta tulp"),
				"url" => "javascript:document.getElementById('table_props').style.visibility='hidden';{$js_url_prefix}delete_column();",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "table_props",
				"text" => t("Kustuta rida"),
				"url" => "javascript:document.getElementById('table_props').style.visibility='hidden';{$js_url_prefix}delete_row();",
			));
		
			/*
			$toolbar->add_menu_separator(array("parent" => "table_props"));
			$toolbar->add_menu_item(array(
				"parent" => "table_props",
				"text" => t("Poolita rida"),
				"url" => "javascript:{$js_url_prefix}split_row();",
			));
			$toolbar->add_menu_item(array(
				"parent" => "table_props",
				"text" => t("Poolita tulp"),
				"url" => "javascript:{$js_url_prefix}split_cell();",
			));
			*/

			$toolbar->add_separator();


			$toolbar->add_button(array(
				"name" => "source",
				"tooltip" => t("HTML"),
				"target" => "_self",
				"url" => "javascript:oldurl=window.location.href;window.location.href=oldurl + '&no_rte=1';",
			));

			$toolbar->add_separator();
			
			/*$toolbar->add_menu_button(array(
				"name" => "headings",
				"tooltip" => t("Päised"),
				"img" => "h1.gif",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "headings",
				"text" => t("Heading 1"),
				"url" => "javascript:{$js_url_prefix}surroundHTML('<h1>','</h1>');",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "headings",
				"text" => t("Heading 2"),
				"url" => "javascript:{$js_url_prefix}surroundHTML('<h2>','</h2>');",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "headings",
				"text" => t("Heading 3"),
				"url" => "javascript:{$js_url_prefix}surroundHTML('<h3>','</h3>');",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "headings",
				"text" => t("Heading 4"),
				"url" => "javascript:{$js_url_prefix}surroundHTML('<h4>','</h4>');",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "headings",
				"text" => t("Heading 5"),
				"url" => "javascript:{$js_url_prefix}surroundHTML('<h5>','</h5>');",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "headings",
				"text" => t("Heading 6"),
				"url" => "javascript:{$js_url_prefix}surroundHTML('<h6>','</h6>');",
			));
			
			$toolbar->add_menu_button(array(
				"name" => "textcolor",
				"tooltip" => t("Teksti värv"),
				"img" => "textcolor.gif",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "textcolor",
				"text" => t("Sinine"),
				"url" => "javascript:{$js_url_prefix}colortext('#0000ff');",
			));
			
			$toolbar->add_menu_item(array(
				"parent" => "textcolor",
				"text" => t("Punane"),
				"url" => "javascript:{$js_url_prefix}colortext('#ff0000');",
			));*/


			$this->get_styles_from_site();

				   
				/*
					$toolbar->add_separator();
			$this->read_template("stylebox.tpl");
			$toolbar->add_cdata($this->parse(),"right");
			*/

					$toolbar->add_separator(array(
				"side" => "right",
			));

			$toolbar->add_button(array(
				"name" => "clearstyles",
				"img" => "clearstyles.gif",
				"tooltip" => T("Tühista stiilid"),
				"url" => "javascript:${js_url_prefix}clearstyles()",
				"side" => "right",
			));
		}
	}

	function get_styles_from_site($arr = array())
	{
	//	$contents = file_get_contents(aw_ini_get("site_basedir") . "/public/css/styles.css");
		// now I need to parse things out of this place
	//	print "<pre>";
	//	print $contents;
	//	print "</pre>";


	}
	/** Draws the RTE editor

		@attrib name=draw_editor params=name api=1 

		@param name required type=string

		@errors 
			none

		@returns 
			none

		@comment 
			All parameters are set as template variables

		@examples
			none
	**/
	function draw_editor($arr)
	{
                // richtext editors are inside a template
                static $rtcounter = 0;
                $rtcounter++;
                $retval = "";
                $this->read_template("aw_richtexteditor.tpl");
                $this->vars($arr);

                if ($rtcounter == 1)
                {
                        $this->rt_elements = array($arr["name"]);
                        // get the site styles
                        $site_styles = $this->get_file(array(
                                "file" => aw_ini_get("site_basedir") . "/public/css/styles.css",
                        ));
                        preg_match("/(\.text \{.+?\})/sm",$site_styles,$m);
                        $text_style = str_replace("\n"," ",$m[1]);
			$text_style = str_replace("\r","",$text_style);

                        $this->vars(array(
                                "rte_styles" => $text_style,
                                //"rte_styles" => $text_style . " .styl1 {color: green; font-family: Verdana; font-weight: bold;} .styl2 {color: blue; font-size: 20px;} .styl3 {color: red; border: 1px solid blue;}",
                        ));
                        $retval .= $this->parse("writer");
                        $retval .= $this->parse("toolbar");
                };
                $retval .= $this->parse("field");
                return $retval;
        }
	
}
?>
