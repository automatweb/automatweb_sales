<script type="text/javascript">
var chk_status = true;
function selall()
{
	len = document.changeform.elements.length;
	for (i = 0; i < len; i++)
	{
		document.changeform.elements[i].checked = chk_status;
	}
	chk_status = !chk_status;
}
</script>
<div class="aw04kalenderkast03">
<small>
<!-- SUB: DCHECK -->
	<div class="aw04kalendersubevent">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr class="aw04kalendersubevent">
				<td width="4%" align="center">
					<a href="javascript:selall()">Vali</a>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</table>
	</div>
<!-- END SUB: DCHECK -->
	{VAR:EVENT}
</small>
</div>
