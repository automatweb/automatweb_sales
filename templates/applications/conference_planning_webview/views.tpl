<link href="automatweb/css/rfpCalendar.css" rel="stylesheet" type="text/css" media="screen, projection" />
<script type="text/javascript" src="automatweb/js/rfpCalendar/calendar.js"></script>
<script type="text/javascript" src="automatweb/js/rfpCalendar/calendar-ee.js"></script>
<script type="text/javascript" src="automatweb/js/rfpCalendar/calendar-setup.js"></script>
<style>
	TR TH label
	{
		color: #525252;
		font-family: Verdana;
		font-size:11px;
	}
	TR TH
	{
		text-align:right;
	}
	.subheading TH
	{
		color:#525252;
		font-family:verdana;
		text-align:left;
		font-size:11px;
		height:1px;
		border-bottom:1px solid #000000;
	}
	TR TD input
	{
		border: 1px solid gray;
	}
	.curview
	{
		width:100%;
	}
	.yahbar
	{
		border-bottom:1px solid #000000;
	}
	.yahbar TD
	{
		border-top:1px solid silver;
		border-left:1px solid silver;
		border-right:1px solid silver;
		font-family: verdana;
		font-weight:bold;
		color:white;
		background-color:silver;
		font-size:12px;
	}
	.yahbar TR TD a
	{
		font-family: verdana;
		font-weight:bold;
		color:white;
		background-color:silver;
		font-size:12px;
		text-decoration:none;
	}
	.yahbar TR TD a:hover
	{
		color:#FF3E43;
		text-decoration:none;
	}
	.yahbar TR .act
	{
		background-color:#FF3E43;
		color:white;
	}
</style>

<table class="yahbar">
	<tr>
	<td>asd</td>
		<!-- SUB: YAH_BAR -->
			<!-- SUB: YAH_FIRST_BTN -->
					<td>{VAR:step_nr}. {VAR:caption}</td>
			<!-- END SUB: YAH_FIRST_BTN -->
			<!-- SUB: YAH_FIRST_BTN_HREF -->
					<td><a href="{VAR:url}">{VAR:step_nr}. {VAR:caption}</a></td>
			<!-- END SUB: YAH_FIRST_BTN_HREF -->
			<!-- SUB: ACT_YAH_FIRST_BTN -->
					<td class="act">{VAR:step_nr}. {VAR:caption}</td>
			<!-- END SUB: ACT_YAH_FIRST_BTN -->
	
			<!-- SUB: YAH_BTN -->
					<td>{VAR:step_nr}. {VAR:caption}</td>
			<!-- END SUB: YAH_BTN -->

			<!-- SUB: YAH_BTN_AFTER -->
					<td>d</td>
			<!-- END SUB: YAH_BTN_AFTER -->
			
			<!-- SUB: YAH_BTN_HREF -->
					<td><a href="{VAR:url}">{VAR:step_nr}. {VAR:caption}</a></td>
			<!-- END SUB: YAH_BTN_HREF -->
			
			<!-- SUB: ACT_YAH_BTN -->
					<td class="act">{VAR:step_nr}. {VAR:caption}</td>
			<!-- END SUB: ACT_YAH_BTN -->

			<!-- SUB: YAH_LAST_BTN -->
					<td>{VAR:step_nr}. {VAR:caption}</td>
			<!-- END SUB: YAH_LAST_BTN -->
			<!-- SUB: YAH_LAST_BTN_AFTER -->
					<td>{VAR:step_nr}. {VAR:caption}</td>
			<!-- END SUB: YAH_LAST_BTN_AFTER -->
			<!-- SUB: ACT_YAH_LAST_BTN -->
					<td class="act">{VAR:step_nr}. {VAR:caption}</td>
			<!-- END SUB: ACT_YAH_LAST_BTN -->
		<!-- END SUB: YAH_BAR -->
	</tr>
</table>
<br/><br/>
