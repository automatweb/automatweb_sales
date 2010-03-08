<style type="text/css">
.awtab {
font-family: verdana, sans-serif;
font-size: 11px;
font-weight: bold;
color: #1664B9;
background-color: #CDD5D9;
}
.awtab a {color: #1664B9; text-decoration:none;}
.awtab a:hover {color: #000000; text-decoration:none;}

.awtabdis {
font-family: verdana, sans-serif;
font-size: 11px;
font-weight: bold;
color: #686868;
background-color: #CDD5D9;
}

.awtabsel {
font-family: verdana, sans-serif;
font-size: 11px;
font-weight: bold;
color: #FFFFFF;
background-color: #478EB6;
}
.awtabsel a {color: #FFFFFF; text-decoration:none;}
.awtabsel a:hover {color: #000000; text-decoration:none;}

.awtabseltext {
font-family: verdana, sans-serif;
font-size: 11px;
font-weight: bold;
color: #FFFFFF;
background-color: #478EB6;
}
.awtabseltext a {color: #FFFFFF; text-decoration:none;}
.awtabseltext a:hover {color: #000000; text-decoration:none;}

.awtablecellbackdark {
font-family: verdana, sans-serif;
font-size: 10px;
background-color: #478EB6;
}

.awtablecellbacklight {
background-color: #DAE8F0;
}

.awtableobjectid {
font-family: verdana, sans-serif;
font-size: 10px;
text-align: left;
color: #DBE8EE;
background-color: #478EB6;
}

</style>
<script src="/automatweb/js/popup_menu.js" type="text/javascript">
</script>
{VAR:toolbar}

<div class="menuBar" style="white-space:nowrap">
<!-- SUB: menubutton -->
<a href=""
class="menuButton" title="{VAR:title}"
onclick="return buttonClick(event, '{VAR:id}');">
<u>{VAR:caption}</u>
</a>
<!-- END SUB: menubutton -->

<!-- SUB: selected_menubutton -->
<a href=""
class="menuButton" title="{VAR:title}"
style="font-weight:bold;"
onclick="return buttonClick(event, '{VAR:id}');">
<u>{VAR:caption}</u>
</a>
<!-- END SUB: selected_menubutton -->

<!-- SUB: disabled_menubutton -->
<a href=""
class="menuButton" title="{VAR:title}"
style="color:gray;"
onclick="false">
{VAR:caption}
</a>
<!-- END SUB: disabled_menubutton -->

<!-- SUB: menubutton_nosub -->
<a href="{VAR:link}"
class="menuButton" title="{VAR:title}"
onmouseover="buttonMouseover(event);">
{VAR:caption}
</a>
<!-- END SUB: menubutton_nosub -->

<!-- SUB: selected_menubutton_nosub -->
<a href="{VAR:link}"
class="menuButton" title="{VAR:title}"
style="font-weight:bold;"
onmouseover="buttonMouseover(event);">
{VAR:caption}
</a>
<!-- END SUB: selected_menubutton_nosub -->

<!-- SUB: disabled_menubutton_nosub -->
<a href=""
class="menuButton" title="{VAR:title}"
style="color:gray;"
onclick="return false;"
onmouseover="buttonMouseover(event);">
{VAR:caption}
</a>
<!-- END SUB: disabled_menubutton_nosub -->

</div>


<!-- SUB: menu -->
<div id="{VAR:parent}" class="menu" style="visibility:visile;" onmouseover="menuMouseover(event)">
		<!-- SUB: menuitem -->
			<a class="menuItem" href="{VAR:link}">{VAR:caption}</a>
		<!-- END SUB: menuitem -->
		<!-- SUB: selected_menuitem -->
			<a class="menuItem" style="font-weight:bold;" href="{VAR:link}">{VAR:caption}</a>
		<!-- END SUB: selected_menuitem -->
		<!-- SUB: disabled_menuitem -->
			<a class="menuItem" style="color:gray;" href="" onclick="return false;">{VAR:caption}</a>
		<!-- END SUB: disabled_menuitem -->
</div>
<!-- END SUB: menu -->

{VAR:mainmenu}


<!-- content start -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td class="awtableobjectid"><div style="width:6px;height:5px" /></td></tr></table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td rowspan="2" align="left" valign="bottom" width="6" class="awtablecellbackdark"><IMG SRC="{VAR:baseurl}/automatweb/images/blue/awtable_nurk.gif" WIDTH="6" HEIGHT="5" BORDER=0 ALT=""></td>
<td align="left" valign="top" width="99%" bgcolor="#FFFFFF">
{VAR:content}
</td>
</tr>
<tr>
<td class="awtablecellbacklight"><div style="width:85px;height:5px" /></td>
</tr>
</table>
{VAR:toolbar2}
<br>

<!-- content ends  -->
