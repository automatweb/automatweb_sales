<html>
<head>
	<style>
		.value {
			font-size:4mm;
		}
		.caption {
			font-size:4mm;
			font-weight:bold;
		}
	</style>
</head>
<body>
	<table style="border:1px solid black;border-padding:0px;border-spacing:0px;width:210mm;" cellpadding="0" cellpadding="0">
		<tr>
			<td style="width:160mm;border-right:1px solid black;border-bottom:1px solid black;" valign="top"><img src="{VAR:company_logo}"/></td>
			<td style="border-bottom:1px solid black;">
				<div class="caption">Name</div><br/>
				<div class="value">{VAR:first_name} {VAR:last_name}</div>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black;border-right:1px solid black;" valign="top">
				<div class="caption">Career Summary:</div><br/>
				<!-- SUB: WORK_EXPERIENCES -->
				{VAR:start} - {VAR:end}, {VAR:company}<br/>
				<!-- END SUB: WORK_EXPERIENCES -->
				<br/>
			</td>
			<td style="border-bottom:1px solid black;">
				<div class="caption">Date of birth</div><br/>
				<div class="value">{VAR:birthday}</div>
			</td>
		</tr>
		<tr>
			<td rowspan="5" style="border-right:1px solid black;" valign="top">
				<div class="caption">Additional training</div><br/>
				<div class="value">
					<!-- SUB: ADDITIONAL_TRAINING -->
					{VAR:education_company}, {VAR:education_theme}, {VAR:education_time}<br/>
					<!-- END SUB: ADDITIONAL_TRAINING -->
				</div>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black;font-size:4mm;">
				<div class="caption">Date of joining the company</div><br/>
				<div class="value">{VAR:cur_org_start}</div>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black;">
				<div class="caption">Position within the company</div><br/>
				<div class="value">{VAR:cur_org_position}</div>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black;">
				<div class="caption">Time in this position</div><br/>
				<div class="value">{VAR:cur_org_time}</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="caption">Photo</div><br/>
				<img src="{VAR:picture_url}" border="0"/>
			</td>
		</tr>
	</table>
	<!-- SUB: PROJECT -->
	<br/><br/>
	<table style="border:1px solid black; width:210mm;">
		<tr>
			<td class="caption">
				Period
			</td>
			<td class="value">
				{VAR:project_start} - {VAR:project_end}
			</td>
		</tr>
		<tr>
			<td class="caption">
				Contract
			</td>
			<td>
				{VAR:project_contract}
			</td>
		</tr>
		<tr>
			<td class="caption">
				Value
			</td>
			<td>
				{VAR:project_value}
			</td>
		</tr>
		<tr>
			<td class="caption">
				Role
			</td>
			<td>
				{VAR:project_roles}
			</td>
		</tr>
		<tr>
			<td colspan="2" class="caption">
				Duties
			</td>
		</tr>
		<tr>
			<td colspan="2">
				{VAR:project_tasks}
			</td>
		</tr>
	</table>
	<!-- END SUB: PROJECT -->
</body>
</html>
