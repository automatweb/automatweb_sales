<style>
.vis-1 {
	background-color: red;
}

.vis0 {
	background-color: yellow;
}

.vis1 {
	background-color: green;
};
</style>
<center>
 <TABLE cellSpacing=1 cellPadding=3 width="400" bgColor=#C5D0D2 border=0>
<TBODY>
<tr>
<TD class=title10px align=middle bgColor=#EAF8FA>Date</td>
<TD class=title10px align=middle bgColor=#EAF8FA>Room-type</td>
<TD class=title10px align=middle bgColor=#EAF8FA>Allotment</td>
<TD class=title10px align=middle bgColor=#EAF8FA>Reserved</td>
<TD colspan=2 class=title10px align=middle bgColor=#EAF8FA>Free</td>

</tr>
<!-- SUB: LINE -->
<tr>
<TD class=text10px align=middle bgColor=#EAF8FA>{VAR:start}</td>
<TD class=text10px align=middle bgColor=#EAF8FA>{VAR:txtid}</td>
<TD class=text10px align=middle bgColor=#EAF8FA>{VAR:max}<sup><!--<small><a href="#" alt="{VAR:q1}" title="{VAR:q1}">q</a></small></sup>--></td>
<TD class=text10px align=middle bgColor=#EAF8FA>{VAR:reserved}<sup><!--<small><a href="#" alt="{VAR:q2}" title="{VAR:q1}">q</a></small></sup>--></td>
<TD class=text10px align=middle bgColor=#EAF8FA>{VAR:free}</td>
<td class="vis{VAR:free_id}" width="20" bgColor=#EAF8FA></td>
</tr>
<!-- END SUB: LINE -->
</tbody>
</table>
</center>
