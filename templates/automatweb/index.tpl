<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={VAR:charset}" />
	<title>{VAR:html_title} {VAR:title_action}</title>
	<link rel="shortcut icon" href="{VAR:baseurl}automatweb/images/aw06/favicon.ico" />
	<link href="{VAR:baseurl}automatweb/css/style-min.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="{VAR:baseurl}automatweb/js/js-min.js"></script>
	{VAR:javascript}
	<script type="text/javascript">
	xchanged = 0;
	</script>
	<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="{VAR:baseurl}automatweb/css/iefix.css" />
	<![endif]-->
</head>
<body onload="check_generic_loader();" class=" nihilo ">

<script type="text/javascript">

// aw object quickadd. use ctrl+alt+u to use
var options = {
	maxresults : 8,
	baseurl    : "{VAR:baseurl}",
	parent     : "{VAR:parent}"
};

var recKp = [];

function aw_keyhandler_rec(event)
{
	recKp[recKp.length] = event;
}

function aw_keyhandler_init(event)
{
	recKp[recKp.length] = event;
	$(window).unbind("keydown", aw_keyhandler_init);
	$(window).keydown(aw_keyhandler_rec);

	var html = '<div id="aw_object_quickadd" style="display: none;">\
		<div class="icon"><img src="/automatweb/images/aw06/blank.gif" width="40" alt="" /></div>\
		<div class="selected_object_name"></div>\
		<input type="text" id="aw_object_quickadd_input" class="text" /></div>\
		<div id="aw_object_quickadd_results" style="display: none;" ></div>';
	$("body").append(html);

	$.get("{VAR:baseurl}automatweb/orb.aw?class=shortcut_manager&action=parse_shortcuts_from_xml", {}, function (d)
		{
			eval(d);
			// fetch items on demand
			$("#aw_object_quickadd").aw_object_quickadd(null, options);

			jQuery.hotkeys.add('Ctrl+Shift+a', function(){
				desc = prompt("Kirjeldus", "nimetu");
				if(desc){
					aw_popup_scroll("{VAR:stop_pop_url_add}&name=" + desc, "quick_task_entry", 800,600);
				}
			});

			jQuery.hotkeys.add('Ctrl+Shift+q', function(){
				aw_popup_scroll("{VAR:stop_pop_url_quick_add}", "quick_task_entry", 800,600);
			});

			jQuery.hotkeys.add('Ctrl+Shift+e', function(){
				aw_popup_scroll("{VAR:stop_pop_url_qw}", "quick_task_entry", 800,600);
			});

			$(window).unbind("keydown", aw_keyhandler_rec);
			for(var i = 0; i < recKp.length; i++)
			{
				$(window).trigger("keydown", recKp[i]);
			}
		}
	);
}
$(window).keydown(aw_keyhandler_init);


// init session modal which pops up 5 minutes before session end
$.init_session_modal({
	session_end_msg			: "{VAR:session_end_msg}",
	btn_session_end_continue	: "{VAR:btn_session_end_continue}",
	btn_session_end_cancel		: "{VAR:btn_session_end_cancel}",
	session_length			: {VAR:session_length}
});

<!-- SUB: CFG_ADMIN_MODE -->
function cfEditClick(prop, oid)
{
	$.get("orb.aw", {"class": "cfgform", "action": "cfadm_click_prop", "oid": oid, "prop": prop}, function (pic) {
		$("#cfgEditProp"+prop).attr("src", pic);
	});
}
function cfEditClickGroup(group, oid)
{
	$.get("orb.aw", {"class": "cfgform", "action": "cfadm_click_group", "oid": oid, "group": group}, function (pic) {
		$("#cfgEditGroup"+group).attr("src", pic);
	});
}
<!-- END SUB: CFG_ADMIN_MODE -->

</script>

<!-- SUB: HEADER -->
<div id="pais">
<!-- p2is -->
<div id="awMainHeaderContainer">
	<div class="logo">
		<span>{VAR:prod_family}</span>
		<a href="{VAR:prod_family_href}" title="AutomatWeb"><img src="{VAR:baseurl}automatweb/images/aw06/aw_logo.gif" alt="AutomatWeb.com" width="183" height="34" border="0" /></a>
	</div>
	<div class="top-left-menyy">
		<!-- SUB: SHOW_CUR_CLASS -->
		<span class="currentClassTitle">{VAR:cur_class}</span>
		<!-- END SUB: SHOW_CUR_CLASS -->
		<!-- SUB: SHOW_CUR_OBJ -->
		"<a href="{VAR:cur_obj_url}">{VAR:cur_obj_name}</a>"
		<!-- END SUB: SHOW_CUR_OBJ -->
	</div>
	<div class="top-right-menyy">
		<div class="top-right-menu-item">
			{VAR:lang_pop}
		</div>
		<div class="top-right-menu-item">
			{VAR:settings_pop}
		</div>
		<div class="top-right-menu-item">
			<div style="padding-right: 2px;">
			<!-- SUB: SHOW_CUR_P -->
			{VAR:logged_in_text} <a href="{VAR:cur_p_url}">{VAR:cur_p_name}</a>
			<!-- END SUB: SHOW_CUR_P -->
			<!-- SUB: SHOW_CUR_P_VIEW -->
			{VAR:logged_in_text} <a href="{VAR:cur_p_url_view}">{VAR:cur_p_name}</a>
			<!-- END SUB: SHOW_CUR_P_VIEW -->
			<!-- SUB: SHOW_CUR_P_TEXT -->
			{VAR:logged_in_text} {VAR:cur_p_name}
			<!-- END SUB: SHOW_CUR_P_TEXT -->
			<!-- SUB: SHOW_CUR_CO -->
			<a href="{VAR:cur_co_url}">({VAR:cur_co_name})</a>
			<!-- END SUB: SHOW_CUR_CO -->
			<!-- SUB: SHOW_CUR_CO_VIEW -->
			<a href="{VAR:cur_co_url_view}">({VAR:cur_co_name})</a>
			<!-- END SUB: SHOW_CUR_CO_VIEW -->
			<!-- SUB: SHOW_CUR_CO_TEXT -->
			({VAR:cur_co_name})
			<!-- END SUB: SHOW_CUR_CO_TEXT -->
			</div>
			<a href="{VAR:baseurl}automatweb/orb.aw?class=users&action=logout" title="{VAR:logout_text}"><img src="{VAR:baseurl}automatweb/images/aw06/ikoon_logout.gif" width="26" height="14" border="0" alt="{VAR:logout_text}"></a>
		</div>
	</div>

	<!-- SUB: YAH -->
	<div class="olekuriba">
		{VAR:location_text}
		{VAR:site_title}
	</div>
	<!-- END SUB: YAH -->
</div>
<!-- END SUB: HEADER -->

<!-- SUB: NO_HEADER -->
<!-- END SUB: NO_HEADER -->

	{VAR:content}

<!-- //sisu -->
<!-- jalus -->
<!-- SUB: YAH2 -->
	<div id="jalus">
		{VAR:footer_l1} <br />
		{VAR:footer_l2} <a href="http://www.struktuur.ee">Struktuur Varahaldus</a>, <a href="http://www.automatweb.com/">AutomatWeb</a>.
	</div>
<!-- END SUB: YAH2 -->
<!--//jalus -->

<!-- SUB: POPUP_MENUS -->
<!-- END SUB: POPUP_MENUS -->

	<div id="ajaxLoader" style="display: none;">
	<img src="/automatweb/images/ajax-loader2.gif" width="220" height="19" alt="" />
	<div>{VAR:ajax_loader_msg}</div>
</div>

{VAR:javascript_bottom}

</body>
</html>
