<form method="post" action="{VAR:action}" name="changeform">
<table border="1" width="100%">
	<!-- SUB: FIRST_RFP_YAH -->
	<!-- END SUB: FIRST_RFP_YAH -->
	<!-- SUB: OTHER_RFP_YAH -->
	<tr>
		<td align="center">
			<!-- SUB: YAH_BAR -->
				<!-- SUB: YAH_FIRST_BTN -->
					[ {VAR:step_nr}.
				<!-- END SUB: YAH_FIRST_BTN -->
				<!-- SUB: YAH_FIRST_BTN_HREF -->
					[ <a href="{VAR:url}">{VAR:step_nr}.</a>
				<!-- END SUB: YAH_FIRST_BTN_HREF -->

				<!-- SUB: ACT_YAH_FIRST_BTN -->
					[ <b>{VAR:step_nr}.</b>
				<!-- END SUB: ACT_YAH_FIRST_BTN -->
				<!-- SUB: ACT_YAH_FIRST_BTN_HREF -->
					[ <b></a href="{VAR:url}">{VAR:step_nr}.</a></b>
				<!-- END SUB: ACT_YAH_FIRST_BTN_HREF -->


				<!-- SUB: YAH_BTN -->
					{VAR:step_nr}.
				<!-- END SUB: YAH_BTN -->
				<!-- SUB: YAH_BTN_HREF -->
					<a href="{VAR:url}">{VAR:step_nr}.</a>
				<!-- END SUB: YAH_BTN_HREF -->

				<!-- SUB: ACT_YAH_BTN -->
					<b>{VAR:step_nr}. {VAR:caption}</b>
				<!-- END SUB: ACT_YAH_BTN -->

				<!-- SUB: YAH_BTN_AFTER -->
				{VAR:step_nr}.(after active)
				<!-- END SUB: YAH_BTN_AFTER -->
				
				<!-- SUB: ACT_YAH_BTN_HREF -->
					<b><a href="{VAR:url}">{VAR:step_nr}.</a></b>
				<!-- END SUB: ACT_YAH_BTN_HREF -->
				
				<!-- SUB: YAH_LAST_BTN_AFTER -->
					({VAR:step_nr}.)
				<!-- END SUB: YAH_LAST_BTN_AFTER -->

				<!-- SUB: YAH_LAST_BTN -->
					{VAR:step_nr}.]
				<!-- END SUB: YAH_LAST_BTN -->
				<!-- SUB: YAH_LAST_BTN_HREF -->
					<a href="{VAR:url}">{VAR:step_nr}.</a> ]
				<!-- END SUB: YAH_LAST_BTN_HREF -->

				<!-- SUB: ACT_YAH_LAST_BTN -->
					<b>{VAR:step_nr}. {VAR:caption}</b> ]
				<!-- END SUB: ACT_YAH_LAST_BTN -->
				<!-- SUB: ACT_YAH_LAST_BTN_HREF -->
					<b><a href="{VAR:url}">{VAR:step_nr}.</a></b> ]
				<!-- END SUB: ACT_YAH_LAST_BTN_HREF -->
			<!-- END SUB: YAH_BAR -->
		</td>
	</tr>
	<!-- END SUB: OTHER_RFP_YAH -->
	<!-- SUB: MISSING_ERROR -->
		<tr>
			<td>
				{VAR:caption}
			</td>
		</tr>
	<!-- END SUB: MISSING_ERROR -->
	<tr>
		<td>
			{VAR:sub_contents}
		</td>
	</tr>
	<!-- SUB: FIRST_RFP_SUBMIT -->
	<tr>
		<td align="center">
			<input type="button" onClick="javascript:submit_changeform('submit_forward');" value="continue" />
		</td>
	</tr>
	<!-- END SUB: FIRST_RFP_SUBMIT -->
	<!-- SUB: OTHER_RFP_SUBMIT -->
	<tr>
		<td align="center">
			<input type="button" onClick="javascript:submit_changeform('submit_back');" value="back" />&nbsp;<input type="button" onClick="javascript:submit_changeform('submit_forward');" value="continue" />
		</td>
	</tr>
	<!-- END SUB: OTHER_RFP_SUBMIT -->
	<!-- SUB: LAST_RFP_SUBMIT -->
	<tr>
		<td align="center">
			<input type="button" onClick="javascript:submit_changeform('submit_back');" value="Change data" />&nbsp;<input type="button" onClick="javascript:checkConfirm();javascript:submit_changeform('submit_final');" value="Submit" />
		</td>
	</tr>
	<!-- END SUB: LAST_RFP_SUBMIT -->
</table>
{VAR:reforb}
<script type="text/javascript">
	function submit_changeform(action)
	{
		changed = 0;
		if (typeof(aw_submit_handler) != "undefined")
		{
			if (aw_submit_handler() == false)
			{
				return false;
			}
		}
		if (typeof action == "string" && action.length>0)
		{
			document.changeform.action.value = action;
		};
		document.changeform.submit();
	}
	function checkConfirm()
	{
		el = document.getElementById('{VAR:confirm_ch_id}');
		if(!(el.checked))
		{
			alert('{VAR:LC_PLEASE_CONFIRM}');
			exit();
		}
	}
</script>
</form>
