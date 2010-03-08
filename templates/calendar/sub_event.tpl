<div class="aw04kalendersubevent" style="width:100%;{VAR:bgcolor}">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr class="aw04kalendersubevent">
			<td width="4%" align="center" valign="top">
				<!-- SUB: DCHECKED -->
				<input type="checkbox" id="sel[{VAR:id}]" name="sel[{VAR:id}]" value="{VAR:id}">
				<!-- END SUB: DCHECKED -->
			</td>
			<td>
				<a href="{VAR:link}" title="{VAR:title}" alt="{VAR:title}">{VAR:time} <img src="{VAR:iconurl}" border="0"/> {VAR:name}</a>
				<!-- SUB: COMMENT -->
				<hr size="1" width="100%" color="#CCCCCC">
				{VAR:comment}
				<!-- END SUB: COMMENT -->
			</td>
		</tr>
	</table>
</div>
