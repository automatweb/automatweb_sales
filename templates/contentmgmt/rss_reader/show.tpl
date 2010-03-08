<div class="rss_item">
<div class="header">
<ul>
	<li>{VAR:name}</li>
	<li class="space">|</li>
	<li><a href="?show_all={VAR:oid}">view all items</a></li>
	<li class="space">|</li>
	<li><a href="{VAR:rss_url}" class="rss"></a></li>
</ul>
<br class="clear" />
</div>

<div class="content">
<!-- SUB: ITEM -->
<a href='{VAR:link}'>{VAR:title}</a><br />
{VAR:description}
<div class="hr"><!-- --></div>
<!-- END SUB: ITEM -->
<div class="footer">This feed was last fetched: {VAR:last_update}</div>
</div>

</div>

<div class="rss_item_top""><a href="#top"><img src="/img/arrow3.gif" alt="" /></a></div>
