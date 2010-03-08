<div class="aw04kalendersubevent" style="width:100%;{VAR:bgcolor}">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr class="aw04kalendersubevent">
			<td style="width: 20px;" align="center">
				<!-- SUB: DCHECKED -->
				<input type="checkbox" id="sel[{VAR:id}]" name="sel[{VAR:id}]" value="{VAR:id}">
				<!-- END SUB: DCHECKED -->
			</td>
				<td>
				<table cellspacing="0" cellpadding="0">
				<tr>
					<td style="width:27px;"><img src="{VAR:iconurl}" alt="" border="0" align="middle"></td>
					<td style="width: 135px; white-space: nowrap;">{VAR:lc_date}</td>
					<td>- <a href="{VAR:link}" title="{VAR:title}" alt="{VAR:title}">{VAR:name}</a>
				<!-- SUB: COMMENT -->
				/<i>{VAR:comment_content}</i>
				<!-- END SUB: COMMENT -->

				{VAR:modifiedby}
				</td></tr></table>
			</td>
		</tr>
	</table>
</div>

