<span class="menuBar" style="height:15px;text-align: center;
border:0px;background-color: transparent ;
"><a class="menuButton" href="" 
style="border:0px;"
onclick="return buttonClick(event, '{VAR:menu_id}');"
oncontextmenu="return buttonClick(event, '{VAR:menu_id}');" alt="{VAR:alt}" title="{VAR:alt}">
<img alt="{VAR:alt}" title="{VAR:alt}" border="0" src='{VAR:menu_icon}' id='mb_{VAR:menu_id}' width='16' height='16'>
</a>
</div>
<div id="{VAR:menu_id}" class="menu" onmouseover="menuMouseover(event)">
<!-- SUB: MENU_ITEM -->
<a class="menuItem" href="{VAR:link}">{VAR:text}</a>
<!-- END SUB: MENU_ITEM --></div>
