<!-- SUB: ICON -->

<table 
class="boxA" id="dra{VAR:oid}" 
style="border:1px dotted white"
onmouseover="document.getElementById('dra{VAR:oid}').style.border='1px dotted #888888'"
onmouseout="document.getElementById('dra{VAR:oid}').style.border='1px dotted white'"

><tr><td valign="top" align="center" class="awmenuedittabletext">
<a oncontextmenu="return buttonClick(event, 'mi{VAR:oid}');">
<img
class="menuButton"
href="" title=""
ondblclick="document.location='{VAR:chlink}';"
onmouseover="buttonMouseover(event, 'mi{VAR:oid}');"
src="{VAR:icon_url}"
height="24" border="0"/>
</td></tr>
<tr><td valign="top" align="center"
class="awmenuedittabletext"
><a href="{VAR:chlink}">{VAR:caption}</a>
</td></tr>
<tr><td valign="top" align="center" style="height:100%;"></td></tr>
</table>

<div id="mi{VAR:oid}" class="menu" onmouseover="menuMouseover(event)">
<!-- SUB: MENU_ITEM -->
<a class="menuItem" href="{VAR:link}">{VAR:text}</a>
<!-- END SUB: MENU_ITEM -->
</div>

<!-- END SUB: ICON -->
