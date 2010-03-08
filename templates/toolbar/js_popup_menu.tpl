<!-- SUB: MENU_HEADER -->
<script src="{VAR:baseurl}/automatweb/js/popup_menu.js" type="text/javascript">
</script>
<!-- END SUB: MENU_HEADER -->
<div id="{VAR:id}" class="menu" onmouseover="menuMouseover(event)">
<!-- SUB: MENU_ITEM -->
<a class="menuItem" href="{VAR:url}" {VAR:onClick}>{VAR:text}</a>
<!-- END SUB: MENU_ITEM -->

<!-- SUB: MENU_ITEM_DISABLED -->
<a class="menuItem" href="" title="{VAR:title}" onclick="return false;" style="color:gray">{VAR:text}</a>
<!-- END SUB: MENU_ITEM_DISABLED -->

<!-- SUB: MENU_ITEM_SUB -->
<a class="menuItem" href=""
        onclick="return false;"
	onmouseover="menuItemMouseover(event, '{VAR:sub_menu_id}');">
	<span class="menuItemText">{VAR:text}</span><span class="menuItemArrow"><img style="border:0px" src="{VAR:baseurl}/automatweb/images/arr.gif" alt=""></span></a>
<!-- END SUB: MENU_ITEM_SUB -->


<!-- SUB: MENU_SEPARATOR -->
<div class="menuItemSep"></div>
<!-- END SUB: MENU_SEPARATOR -->

</div>





