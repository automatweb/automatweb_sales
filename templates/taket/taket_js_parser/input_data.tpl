/*-------------------------------------------
Copyright Notice
--------------------------------------------*/

   DQM_Notice = "DHTML QuickMenu, Copyright (c) - 2001, OpenCube Inc. - www.opencube.com"



/*-------------------------------------------
Required menu Settings
--------------------------------------------*/

   DQM_sub_menu_width = 140;


   DQM_urltarget = "_self"
   
   DQM_onload_statement = ""
   DQM_codebase = "scripts/"

   DQM_border_color = "#B9BED2";

   DQM_menu_bgcolor = "#FFFFFF";
   DQM_hl_bgcolor = "#EEEFF4";

   DQM_sub_xy = "0,0";
   DQM_border_width = 1;
   DQM_divider_height = 1;

   /*---IE5-MAC Offset Fix - The following two
   -----parameters correct a position reporting
   -----bug in IE5 on the Mac platform. Adjust
   -----the offsets below untill the first level
   -----sub menus pop up in the correct location.*/

   DQM_ie5mac_offset_X = 10
   DQM_ie5mac_offset_Y = 12


/*-------------------------------------------
Required font Settings
--------------------------------------------*/


   DQM_textcolor = "#394280"
   DQM_fontfamily = "Verdana"
   DQM_fontsize = 10
   DQM_fontsize_ie4 = 9	
   DQM_textdecoration = "normal"
   DQM_fontweight = "normal"
   DQM_fontstyle = "normal"
   DQM_hl_textcolor = "#394280"
   DQM_hl_textdecoration = "underline"

   DQM_margin_top = 3
   DQM_margin_bottom = 3
   DQM_margin_left = 5




/*---------------------------------------------
Optional Icon Images
-----------------------------------------------*/

   DQM_icon_image0 = ""
   DQM_icon_rollover0 = ""
   DQM_icon_image_wh0 = ""

   DQM_icon_image1 = ""
   DQM_icon_rollover1 = ""
   DQM_icon_image_wh1 = ""


/*---------------------------------------------
Sub Menu Item Settings
-----------------------------------------------*/
/*-------------------------------------------
Copyright Notice
--------------------------------------------*/

   DQM_Notice = "DHTML QuickMenu, Copyright (c) - 2001, OpenCube Inc. - www.opencube.com"



/*-------------------------------------------
Required menu Settings
--------------------------------------------*/

   DQM_sub_menu_width = 140;


   DQM_urltarget = "_self"
   
   DQM_onload_statement = ""
   DQM_codebase = "scripts/"

   DQM_border_color = "#B9BED2";

   DQM_menu_bgcolor = "#FFFFFF";
   DQM_hl_bgcolor = "#EEEFF4";

   DQM_sub_xy = "0,0";
   DQM_border_width = 1;
   DQM_divider_height = 1;

   /*---IE5-MAC Offset Fix - The following two
   -----parameters correct a position reporting
   -----bug in IE5 on the Mac platform. Adjust
   -----the offsets below untill the first level
   -----sub menus pop up in the correct location.*/

   DQM_ie5mac_offset_X = 10
   DQM_ie5mac_offset_Y = 12


/*-------------------------------------------
Required font Settings
--------------------------------------------*/


   DQM_textcolor = "#394280"
   DQM_fontfamily = "Verdana"
   DQM_fontsize = 10
   DQM_fontsize_ie4 = 9	
   DQM_textdecoration = "normal"
   DQM_fontweight = "normal"
   DQM_fontstyle = "normal"
   DQM_hl_textcolor = "#394280"
   DQM_hl_textdecoration = "underline"

   DQM_margin_top = 3
   DQM_margin_bottom = 3
   DQM_margin_left = 5




/*---------------------------------------------
Optional Icon Images
-----------------------------------------------*/

   DQM_icon_image0 = ""
   DQM_icon_rollover0 = ""
   DQM_icon_image_wh0 = ""

   DQM_icon_image1 = ""
   DQM_icon_rollover1 = ""
   DQM_icon_image_wh1 = ""


/*---------------------------------------------
Sub Menu Item Settings
-----------------------------------------------*/

<!-- SUB: frstLevel -->
	DQM_sub_xy{VAR:counter1} = "0,13"
	DQM_sub_menu_width{VAR:counter1} = 140;
	{VAR:scndLevelParsed}
	<!-- SUB: scndLevel -->
		DQM_subdesc{VAR:counter1}_{VAR:counter2} = "{VAR:text}"
		DQM_url{VAR:counter1}_{VAR:counter2} = "{VAR:link}"
	<!-- END SUB: scndLevel -->
<!-- END SUB: frstLevel -->
{VAR:frstLevelParsed}