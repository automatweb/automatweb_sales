<!-- SUB: SHOW_CHANGEFORM -->
<form action="{VAR:handler}{VAR:ext}" method="{VAR:method}" enctype="multipart/form-data" name="changeform" id="changeform" onsubmit="if(awcbChangeFormSubmitted){ return false; } submit_changeform('{VAR:action}', true);" style="margin-top: 0px;" {VAR:form_target}>

<!-- END SUB: SHOW_CHANGEFORM -->
<table id="{VAR:contenttbl_id}" width="100%" border="0" cellspacing="0" cellpadding="0"> <!-- aw06contenttable -->

	<!-- SUB: MESSAGE -->
	<tr>
	    <td colspan="2" class="awcbUIMsg awcbUIMsg{VAR:class}">{VAR:text}</td>
	</tr>
	<!-- END SUB: MESSAGE -->

	{VAR:content}

	<!-- SUB: PROP_ERR_MSG -->
	<tr>
	    <td width="100"></td>
	    <td class="awcbPropErr">{VAR:err_msg}</td>
	</tr>
	<!-- END SUB: PROP_ERR_MSG -->

	<!-- SUB: LINE -->
	<tr>
	    <td width="100" id="linecaption">
		{VAR:cfgform_edit_mode}
		{VAR:caption}
		{VAR:comment}
		</td>
	    <td id="lineelment">{VAR:element}</td>
	</tr>
	<!-- END SUB: LINE -->

	<!-- SUB: RADIOBUTTON -->
	<input class="radiobutton" type="radio" name="{VAR:name}" id="{VAR:id}" value="{VAR:value}"{VAR:onclick}{VAR:checked}{VAR:disabled} /> {VAR:caption}
	<!-- END SUB: RADIOBUTTON -->

	<!-- SUB: CHECKBOX -->
	<input class="checkbox" type="checkbox" id="{VAR:name}" name="{VAR:name}" value="{VAR:value}"{VAR:onblur}{VAR:title}{VAR:onclick}{VAR:checked}{VAR:disabled} /> {VAR:caption}
	{VAR:post_append_text}
	<!-- END SUB: CHECKBOX -->

	<!-- SUB: HEADER -->
	<tr>
	    <td></td>
	    <td id="header">{VAR:caption}</td>
	</tr>
	<!-- END SUB: HEADER -->

	<!-- SUB: SUB_TITLE -->
	<tr>
	    <td colspan="2" id="subtitle">{VAR:cfgform_edit_mode}{VAR:value}</td>
	</tr>
	<!-- END SUB: SUB_TITLE -->

	<!-- SUB: CONTENT -->
	<tr>
	    <td colspan="2" id="sitecontent" data-property-name="{VAR:name}">{VAR:value}</td>
	</tr>
	<!-- END SUB: CONTENT -->

	<!-- SUB: SUBMIT -->
	<tr>
	    <td width="100"></td>
	    <td id="buttons">
		<!-- SUB: BACK_BUTTON -->
		<input id="button" type="submit" name="{VAR:back_button_name}" value="{VAR:back_button_caption}" />
		<!-- END SUB: BACK_BUTTON -->
		<input id="button" type="submit" name="{VAR:name}" value="{VAR:sbt_caption}" accesskey="s" />
		<!-- SUB: FORWARD_BUTTON -->
		<input id="button" type="submit" name="{VAR:forward_button_name}" value="{VAR:forward_button_caption}" />
		<!-- END SUB: FORWARD_BUTTON -->
	    </td>
	</tr>
	<!-- END SUB: SUBMIT -->


		<!-- SUB: SUBITEM -->
		<span style='color: red'>{VAR:err_msg}</span>
		{VAR:element}
		<span class="aw04contentcellright">{VAR:caption}</span>
		&nbsp;
		<!-- END SUB: SUBITEM -->

		<!-- SUB: SUBITEM2 -->
		<span style='color: red'>{VAR:err_msg}</span>
		<div class="aw04contentcellleft">{VAR:caption}</div>
		<div class="aw04contentcellright">{VAR:element}</div>
		<!-- END SUB: SUBITEM2 -->

		<!-- SUB: GRIDITEM -->
		<div class="aw04gridcell_caption">
			<!-- SUB: GRID_ERR_MSG -->
			<span style="color: red;">{VAR:err_msg}</span>
			<!-- END SUB: GRID_ERR_MSG -->

			<!-- SUB: CAPTION_TOP -->
			{VAR:cfgform_edit_mode}
			<span class="awcbPropCaption">{VAR:caption}</span>
			{VAR:comment}
			<br/>
			{VAR:element}
			<!-- END SUB: CAPTION_TOP -->

			<!-- SUB: CAPTION_LEFT -->
			<table border="0" width="100%">
			<tr>
				<td width="20%" align="right">
				{VAR:cfgform_edit_mode}<span class="aw04gridCellCaptionText awcbPropCaption">{VAR:caption}</span>
				{VAR:comment}
				</td>
				<td width="80%">
				{VAR:element}
				</td>
			</tr>
			</table>
			<!-- END SUB: CAPTION-LEFT -->
		</div>
		<!-- END SUB: GRIDITEM -->

		<!-- SUB: GRIDITEM_NO_CAPTION -->
		<div class="aw04gridcell_no_caption" name="{VAR:element_name}">{VAR:cfgform_edit_mode}{VAR:element}</div>
		<!-- END SUB: GRIDITEM_NO_CAPTION -->

		<!-- SUB: GRID_HBOX_OUTER -->
		<div id="{VAR:grid_outer_name}">
			<!-- SUB: GRID_HBOX -->
				<!-- SUB: GRID_NO_CAPTION -->
			<div id="{VAR:grid_name}">
				<!-- END SUB: GRID_NO_CAPTION -->
				<!-- SUB: GRID_HAS_CAPTION -->
			<div id="vbox">
				<div class="pais">
					<div class="caption">{VAR:area_caption}</div>
					<!-- SUB: GRID_HAS_CLOSER -->
					<div class="closer"><a href="#" onclick='el=document.getElementById("{VAR:grid_name}");im=document.getElementById("{VAR:grid_name}_closer_img");if (el.style.display=="none") { el.style.display="block";im.src="{VAR:baseurl}automatweb/images/aw06/closer_up.gif"; im.alt=im.title="{VAR:close_text}"; aw_get_url_contents("{VAR:open_layer_url}");} else { el.style.display="none";im.src="{VAR:baseurl}automatweb/images/aw06/closer_down.gif"; im.alt=im.title="{VAR:open_text}"; aw_get_url_contents("{VAR:close_layer_url}");} return false;' tabindex="10"><img src="{VAR:baseurl}automatweb/images/aw06/closer_{VAR:closer_state}.gif" title="{VAR:start_text}" alt="{VAR:start_text}" width="20" height="15" border="0" class="button" id="{VAR:grid_name}_closer_img"/></a></div>
					<!-- END SUB: GRID_HAS_CLOSER -->
				</div>
				<div class="sisu" id="{VAR:grid_name}" style="display: {VAR:display}">
				<!-- END SUB: GRID_HAS_CAPTION -->
				<table border=0 cellspacing=0 cellpadding=0 width='100%'>
					<tr>
						<!-- SUB: GRID_HBOX_ITEM -->
						<td valign='top' {VAR:item_width} style='padding-left: 0px;'>{VAR:item}</td>
						<!-- END SUB: GRID_HBOX_ITEM -->
					</tr>
				</table>
				<!-- SUB: GRID_HAS_CAPTION_END -->
				</div>
			</div>
				<!-- END SUB: GRID_HAS_CAPTION_END -->
				<!-- SUB: GRID_NO_CAPTION_END -->
			</div>
				<!-- END SUB: GRID_NO_CAPTION_END -->
			<!-- END SUB: GRID_HBOX -->
		</div>
		<!-- END SUB: GRID_HBOX_OUTER -->


		<!-- SUB: GRID_VBOX_OUTER -->
		<div id="{VAR:grid_outer_name}">
			<!-- SUB: GRID_VBOX -->
			<!-- SUB: VGRID_NO_CAPTION -->
			<div id="{VAR:grid_name}">
			<!-- END SUB: VGRID_NO_CAPTION -->
			<!-- SUB: VGRID_HAS_CAPTION -->
			<div id="vbox">
				<div class="pais">
					<div class="caption">{VAR:area_caption}</div>
					<!-- SUB: VGRID_HAS_CLOSER -->
					<div class="closer"><a href="#" onClick='el=document.getElementById("{VAR:grid_name}");im=document.getElementById("{VAR:grid_name}_closer_img");if (el.style.display=="none") { el.style.display="block";im.src="{VAR:baseurl}automatweb/images/aw06/closer_up.gif"; im.alt=im.title="{VAR:close_text}"; aw_get_url_contents("{VAR:open_layer_url}");} else { el.style.display="none";im.src="{VAR:baseurl}automatweb/images/aw06/closer_down.gif"; im.alt=im.title="{VAR:open_text}"; aw_get_url_contents("{VAR:close_layer_url}");} return false;' tabindex="10"><img src="{VAR:baseurl}automatweb/images/aw06/closer_{VAR:closer_state}.gif" title="{VAR:start_text}" alt="{VAR:start_text}" width="20" height="15" border="0" class="button"  id="{VAR:grid_name}_closer_img"/></a></div>
					<!-- END SUB: VGRID_HAS_CLOSER -->
				</div>
				<div class="sisu" id="{VAR:grid_name}" style="display: {VAR:display}">
				<!-- SUB: VGRID_HAS_PADDING -->
				<div class="sisu2">
				<!-- END SUB: VGRID_HAS_PADDING -->

				<!-- SUB: VGRID_NO_PADDING -->
				<div class="sisu2nop">
				<!-- END SUB: VGRID_NO_PADDING -->
			<!-- END SUB: VGRID_HAS_CAPTION -->

					<!-- SUB: GRID_VBOX_ITEM -->
					<div class="sisu3">{VAR:item}</div>
					<!-- END SUB: GRID_VBOX_ITEM -->
				<!-- SUB: VGRID_HAS_CAPTION_END -->
				</div>
				</div>
			</div>
				<!-- END SUB: VGRID_HAS_CAPTION_END -->
				<!-- SUB: VGRID_NO_CAPTION_END -->
				</div>
				<!-- END SUB: VGRID_NO_CAPTION_END -->
			<!-- END SUB: GRID_VBOX -->
		</div>
		<!-- END SUB: GRID_VBOX_OUTER -->


		<!-- SUB: GRID_VBOX_SUB_OUTER -->
		<div id="{VAR:grid_outer_name}">
			<!-- SUB: GRID_VBOX_SUB -->
			<!-- SUB: VSGRID_NO_CAPTION -->
			<div id="{VAR:grid_name}">
			<!-- END SUB: VSGRID_NO_CAPTION -->
			<!-- SUB: VSGRID_HAS_CAPTION -->
			<div id="vbox_sub">
				<div class="pais">
					<div class="caption">{VAR:area_caption}</div>
					<!-- SUB: VSGRID_HAS_CLOSER -->
					<div class="closer"><a href="#" onClick='el=document.getElementById("{VAR:grid_name}");im=document.getElementById("{VAR:grid_name}_closer_img");if (el.style.display=="none") { el.style.display="block";im.src="{VAR:baseurl}automatweb/images/aw06/closer_2_up.gif"; im.alt=im.title="{VAR:close_text}"; aw_get_url_contents("{VAR:open_layer_url}");} else { el.style.display="none";im.src="{VAR:baseurl}automatweb/images/aw06/closer_2_down.gif"; im.alt=im.title="{VAR:open_text}"; aw_get_url_contents("{VAR:close_layer_url}");} return false;' tabindex="10"><img src="{VAR:baseurl}automatweb/images/aw06/closer_2_{VAR:closer_state}.gif" title="{VAR:start_text}" alt="{VAR:start_text}" width="20" height="15" border="0" class="button"  id="{VAR:grid_name}_closer_img"/></a></div>
					<!-- END SUB: VSGRID_HAS_CLOSER -->
				</div>
				<div class="sisu" id="{VAR:grid_name}" style="display: {VAR:display}">
				<!-- SUB: VSGRID_HAS_PADDING -->
				<div class="sisu2">
				<!-- END SUB: VSGRID_HAS_PADDING -->

				<!-- SUB: VSGRID_NO_PADDING -->
				<div class="sisu2nop">
				<!-- END SUB: VSGRID_NO_PADDING -->
			<!-- END SUB: VSGRID_HAS_CAPTION -->

					<!-- SUB: GRID_VBOX_SUB_ITEM -->
					<div class="sisu3">{VAR:item}</div>
					<!-- END SUB: GRID_VBOX_SUB_ITEM -->
				<!-- SUB: VSGRID_HAS_CAPTION_END -->
				</div>
				</div>
			</div>
				<!-- END SUB: VSGRID_HAS_CAPTION_END -->
				<!-- SUB: VSGRID_NO_CAPTION_END -->
				</div>
				<!-- END SUB: VSGRID_NO_CAPTION_END -->
			<!-- END SUB: GRID_VBOX_SUB -->
		</div>
		<!-- END SUB: GRID_VBOX_SUB_OUTER -->

		<!-- SUB: GRID_TABLEBOX -->
		<div id="tablebox">
		    <div class="pais">
			<div class="caption">Tabeli pealkiri</div>
			<div class="navigaator">
			    <!-- siia tuleb yhel ilusal p2eval lehtede kruttimise navigaator, homseks seda vaja pole, seega las see div j22b tyhjaks -->
			</div>
		    </div>
		    <div class="sisu">
		    <!-- SUB: GRID_TABLEBOX_ITEM -->
			{VAR:item}
		    <!-- END SUB: GRID_TABLEBOX_ITEM -->
		    </div>
		</div>
		<!-- END SUB: GRID_TABLEBOX -->
	</table>

<!-- SUB: SHOW_CHANGEFORM2 -->
	<span id="reforb">
	{VAR:reforb}
	</span>
</form>

<script type="text/javascript">
	{VAR:scripts}

	var awcbChangeFormSubmitted = false;
	function submit_changeform(action, nosubmit) {
		$(document.changeform).trigger("awbeforesubmit");
		changed = 0;

		{VAR:submit_handler}

		if (typeof(aw_submit_handler) != "undefined") {
			if (aw_submit_handler() == false) {
				this.disabled=false;
				return false;
			}
		}

		if (typeof action == "string" && action.length > 0) {
			document.changeform.action.value = action;
		}

		if (!nosubmit && !awcbChangeFormSubmitted) {
			document.changeform.submit();
		}

		awcbChangeFormSubmitted = true;
	}
</script>
<!-- END SUB: SHOW_CHANGEFORM2 -->

<!-- SUB: iframe_body_style -->
body
{
        background-color: #FFFFFF;
        margin: 0px;
        overflow-y: hidden;
        overflow:hidden;
}
<!-- END SUB: iframe_body_style -->

<!-- SUB: PROPERTY_HELP -->
<div class="awcbPropertyHelpContainer">
<img src="{VAR:baseurl}automatweb/images/aw06/ikoon_abi_small.gif" alt="" border="0"/>
<div class="awcbPropertyHelp" style="display: none;">{VAR:property_comment}</div>
</div>
<!-- END SUB: PROPERTY_HELP -->

<!-- SUB: HAS_PROPERTY_HELP -->
<style>
div.awcbPropertyHelp
{
	position: absolute;
	width: 25em;
	text-align: left;
	border: 1px solid grey;
	background-color: white;
	color: #333;
	font-size: 1.1em;
	font-weight: normal;
	padding: .2em;
	padding-left: .5em;

}
div.awcbPropertyHelpContainer
{
	display: inline;
}
div.awcbPropertyHelpVisible
{
	display: block ! important;
}
</style>
<script type="text/javascript">
$("div.awcbPropertyHelpContainer").hover(
	function () {
		$(this).find("div.awcbPropertyHelp").addClass("awcbPropertyHelpVisible");
	},
	function () {
		$(this).find("div.awcbPropertyHelp").removeClass("awcbPropertyHelpVisible");
	}
);
</script>
<!-- END SUB: HAS_PROPERTY_HELP -->

<!-- SUB: CHECK_LEAVE_PAGE -->
<script type="text/javascript">
var changed = 0;
var disable_set_changed;
var confirm_save = true;

function set_changed() {
	if(!disable_set_changed) {
		changed = 1;
	}
}

function generic_loader2() {
	// set onchange event handlers for all form elements
	$("form[name='changeform']").find(":input").bind("change", function(){
		if(!disable_set_changed) {
			changed = 1;
		}
	});
}

function generic_unloader() {
	if (changed && !xchanged && confirm_save) {
		if (confirm("{VAR:msg_unload_leave_notice}")) {
			document.changeform.submit();
		}
	}
}
</script>
<!-- END SUB: CHECK_LEAVE_PAGE -->
