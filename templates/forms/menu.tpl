<!-- SUB: FG_MENU -->
<!-- see on formgeni ylemine menyy-->
<script language="Javascript" src="{VAR:baseurl}/automatweb/js/cbobjects.js"></script>
<script language="Javascript">
function init() {
    create_objects();
    toggle('menu1');
    toggle('menu1b');
}

function hideall() {
	theobjs["menu1"].objHide();
	theobjs["menu1b"].objHide();
	theobjs["menu2"].objHide();
	theobjs["menu2b"].objHide();
};

function toggle(layer) {
        hideall();
        theobjs[layer].objShow();
};

function toggle1(layer1,layer2) {
	hideall();
        theobjs[layer1].objShow();
        theobjs[layer2].objShow();
};
</script>

<div id="muh" class="muh">

<div id="mainmenu" class="mainmenu">


<!--tabelraam-->
<table width="100%" border="0" cellspacing="0" cellpadding="1">
<tr><td class="tableborder">

	<!--tabelshadow-->
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td width="1" class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td><td class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
		<!--tabelsisu-->
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr><td><td class="tableinside" height="29">


<table border="0" cellpadding="0" cellspacing="0">
<tr><td width="5"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="5" HEIGHT="29" BORDER=0 ALT=""></td>

<td valign="bottom">

			<table border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td class="tab"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
				<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tab" valign="middle"><a href="javascript:toggle1('menu1','menu1b')">{VAR:LC_FORMS_TOIMETA}</a></td><td class="tab"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>


				<td class="tab"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
				<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tab" valign="middle"><a href="javascript:toggle1('menu2','menu2b')" >{VAR:LC_FORMS_SETTINGS}</a></td><td class="tab"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>


			</tr></table>

</td></tr></table>

</td></tr></table>
</td></tr></table>
<IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="5" HEIGHT="22" BORDER=0 ALT=""></td></tr></table>

</div>

<!-- begin# menu 1 Toimeta -->

<div id="menu1" class="menyy1"><table border="0" cellspacing="0" cellpadding="0"><tr>
<td class="tabsel"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tabsel" valign="bottom"><a href="#">{VAR:LC_FORMS_TOIMETA}</a></td><td class="tabsel"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>
</tr></table></div>

<div id="menu1b" class="alammenyy">&nbsp;
<!-- SUB: CAN_GRID -->
<a class="alamlingid" href='{VAR:change}'>{VAR:LC_FORMS_ADMIN_FORM}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>
<!-- SUB: SEARCH_SEL -->
<a class="alamlingid" href='{VAR:sel_search}'><!-- IMHO: LOOKED != OTSITAVAD aga hui sellega-->{VAR:LC_FORMS_CHOOSE_LOOKED_FORMS}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>
<!-- END SUB: SEARCH_SEL -->

<!-- SUB: FILTER_SEARCH_SEL -->
<a class="alamlingid" href='{VAR:sel_filter_search}'>{VAR:LC_FORMS_CHOOSE_USEABLE_FILTER}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>
<!-- END SUB: FILTER_SEARCH_SEL -->

<!-- END SUB: CAN_GRID -->

<!-- SUB: CAN_PREVIEW -->
<a class="alamlingid" href='{VAR:show}'>{VAR:LC_FORMS_PREVIEW}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>
<!-- END SUB: CAN_PREVIEW -->

<!-- SUB: CAN_ALL -->
<a class="alamlingid" href='{VAR:all_elements}'>{VAR:LC_FORMS_ALL_ELEMENTS}</a>

<a class="alamlingid" href='{VAR:all_elements2}'>Elementide administreerimine</a>
<!-- END SUB: CAN_ALL -->

<a class="alamlingid" href='{VAR:import_entries}'>{VAR:LC_FORMS_IMPORT_DATA}</a>

<!-- SUB: HAS_ALIASMGR -->
<a class="alamlingid" href="{VAR:aliasmgr}">{VAR:LC_FORMS_ALIASMGR}</a>
<!-- END SUB: HAS_ALIASMGR -->

</div>
<!-- end# menu 1 -->

<!-- begin# menu 2 M22rangud -->
<div id="menu2" class="menyy2"><table border="0" cellspacing="0" cellpadding="0"><tr>
<td class="tabsel"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tabsel" valign="bottom"><a href="#">{VAR:LC_FORMS_SETTINGS}</a></td><td class="tabsel"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>
</tr></table></div>

<div id="menu2b" class="alammenyy">&nbsp;
<!-- SUB: CAN_TABLE -->
<a href='{VAR:table_settings}' class="alamlingid">{VAR:LC_FORMS_TABLE_STYLES}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>
<!-- END SUB: CAN_TABLE -->

<!-- SUB: CAN_ACTION -->
<a class="alamlingid" href='{VAR:actions}'>{VAR:LC_FORMS_FORM_ACTIONS}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>
<!-- END SUB: CAN_ACTION -->

<!-- SUB: CAN_META -->
<a class="alamlingid" href='{VAR:metainfo}'>Metainfo</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>
<!-- END SUB: CAN_META -->

<!-- SUB: USES_CALENDAR -->
<a class="alamlingid" href='{VAR:calendar}'>Kalender</a><img src='{VAR:baseurl}/images/transa.gif' width=8 height=1 border=0>
<!-- END SUB: USES_CALENDAR -->

<a class="alamlingid" href='{VAR:set_folders}'>{VAR:LC_FORMS_CHOOSE_CATALOGUES}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>

<a class="alamlingid" href='{VAR:translate}'>{VAR:LC_FORMS_LANGS}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>

<a class="alamlingid" href='{VAR:tables}'>{VAR:LC_FORMS_TABLES}</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>

<!-- SUB: RELS -->
<a class="alamlingid" href='{VAR:joins}'>Seosed</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>
<!-- END SUB: RELS -->

<a class="alamlingid" href='{VAR:export}'>Ekspordi</a><img src='{VAR:baseurl}/images/transa.gif' WIDTH=8 height=1 border=0>

</div>
<!-- end# menu 2 -->

</div>

<script language = javascript>
init();
<!-- SUB: GRID_SEL -->
toggle1('menu1', 'menu1b');
<!-- END SUB: GRID_SEL -->

<!-- SUB: SETTINGS_SEL -->
toggle1('menu2', 'menu2b');
<!-- END SUB: SETTINGS_SEL -->
</script>
<!-- END SUB: FG_MENU -->

<br><br><br><br><br><br>
