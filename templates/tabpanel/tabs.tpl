	<!-- SUB: tabs_L1 -->
	<div class="tabs">
		<!-- SUB: tab_L1 -->
		<div class="norm">
			<div class="right">
				<a {VAR:target} href="{VAR:link}">{VAR:caption}{VAR:cfgform_edit_mode}</a>
			</div>
		</div>
		<!-- END SUB: tab_L1 -->
		<!-- SUB: disabled_tab_L1 -->
		<!-- END SUB: disabled_tab_L1 -->
		<!-- SUB: sel_tab_L1 -->
		<div class="akt">
			<div class="right">
				<a {VAR:target} href="{VAR:link}">{VAR:caption}{VAR:cfgform_edit_mode}</a>
			</div>
		</div>
		<!-- END SUB: sel_tab_L1 -->
	</div>
	<!-- END SUB: tabs_L1 -->

<!-- SUB: ADDITIONAL_TEXT -->
	{VAR:addt_content}
<!-- END SUB: ADDITIONAL_TEXT -->

  <!-- SUB: disabled_tab_L1 -->
  <!-- END SUB: disabled_tab_L1 -->

<!-- SUB: HAS_TABS -->
	<br class="clear" />
	<!-- SUB: NOT_POPUP -->
	<div class="toiming">
		{VAR:qa_pop}
		{VAR:bm_pop}
		{VAR:history_pop}
		<!-- SUB: HAS_SEARCH -->
		<a href="{VAR:srch_link}" class="nupp"><img src="{VAR:baseurl}/automatweb/images/aw06/ikoon_luup.gif" alt="" width="13" height="13" border="0" class="ikoon" />{VAR:search_text}</a>
		<!-- END SUB: HAS_SEARCH -->
	</div>
	<div class="application_links">
		{VAR:application_links}
	</div>
	<!-- END SUB: NOT_POPUP -->

</div>
<!-- //header -->

	<div id="k_menyy">
		<!-- SUB: tabs_L2 -->
		<div class="tabs">
			<!-- SUB: tab_L2 -->
			<div class="norm">
				<div class="right">
					<a {VAR:target} href="{VAR:link}">{VAR:caption}{VAR:cfgform_edit_mode}</a>
				</div>
			</div>
			<!-- END SUB: tab_L2 -->
			<!-- SUB: disabled_tab_L2 -->
			<!-- END SUB: disabled_tab_L2 -->
			<!-- SUB: sel_tab_L2 -->
			<div class="akt">
				<div class="right">
					<a {VAR:target} href="{VAR:link}">{VAR:caption}{VAR:cfgform_edit_mode}</a>
				</div>
			</div>
			<!-- END SUB: sel_tab_L2 -->
		</div>
		<!-- END SUB: tabs_L2 -->
		<div class="p">
			<!-- SUB: HAS_WARNING -->
			<a href="javascript:showhide_help('warn_layer');">Hoiatus</a>
			<!-- END SUB: HAS_WARNING -->
			<img src="{VAR:baseurl}/automatweb/images/aw06/ikoon_tagasiside.gif" name="ico1" alt="tagasiside" width="19" height="16" vspace="2" /><a href="{VAR:feedback_link}" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ico1','','{VAR:baseurl}/automatweb/images/aw06/ikoon_tagasiside_ov.gif',1)">{VAR:feedback_text}</a>

			<img src="{VAR:baseurl}/automatweb/images/aw06/ikoon_kasutajatugi.gif" name="ico2" alt="kasutajatugi" width="16" height="16" /><a href="{VAR:feedback_m_link}" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ico2','','{VAR:baseurl}/automatweb/images/aw06/ikoon_kasutajatugi_ov.gif',1)">{VAR:feedback_m_text}</a>

			<img src="{VAR:baseurl}/automatweb/images/aw06/ikoon_abi.gif" name="ico3" alt="abi" width="16" height="16" /><a href="javascript:showhide_help();"  onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ico3','','{VAR:baseurl}/automatweb/images/aw06/ikoon_abi_ov.gif',1)">{VAR:help_text}</a>
		</div>
		<br class="clear" />
	</div>

<!-- END SUB: HAS_TABS -->
<!-- SUB: NO_TABS -->
</div>
<!-- END SUB: NO_TABS -->

<!-- SUB: WARNING_LAYER -->
<div id="warn_layer" style="background-color: #F7F7F7; border: 1px solid #91DA52; display: none; padding: 5px;font-family: verdana, sans-serif; font-size: 11px; font-weight: normal; color: #000000;">
	{VAR:warn}
</div>
<!-- END SUB: WARNING_LAYER -->
<div id="help_layer" style="background-color: #F7F7F7; border: 1px solid #91DA52; display: none; padding: 5px;">
	<div id="helptext_layer" style="font-family: verdana, sans-serif; font-size: 11px; font-weight: normal; color: #000000; height: 28px; background-color: #F7F7F7; ">
	{VAR:help}
	</div>
<div style="text-align: right; width: 100%; font-family: verdana, sans-serif; font-size: 11px; font-weight: normal; color: #000000;">
{VAR:translate_url}
<a href="javascript:void(0);" onclick="window.open('{VAR:help_url}','awhelp','width=750,height=550,resizable=1,scrollbars=1');">{VAR:more_help_text}</a> | <a href="javascript:close_help();">{VAR:close_help_text}</a>
</div>
</div>


<script type="text/javascript">
function showhide_help(layer)
{
	if(!layer)
	{
		layer = 'help_layer';
	}
	help_layerv = document.getElementById(layer);
	if (help_layerv.style.display == 'none')
	{
		$(".help").EnablePropHelp();
		show_help(layer);
	}
	else
	{
		$(".help").DisablePropHelp();
		close_help(layer);
	};
}

function show_help(layer)
{
        help_layerv = document.getElementById(layer);
        help_layerv.style.display = 'block';
}

function close_help(layer)
{
	if(!layer)
	{
		layer = 'help_layer';
	}

	help_layerv = document.getElementById(layer);
	help_layerv.style.display = 'none';
}

function show_property_help(propname)
{
	prophelp_layer = document.getElementById('property_' + propname + '_help');
	prophelp_layer.style.position = 'absolute';
	prophelp_layer.style.width = '40em';
	prophelp_layer.style.backgroundColor = 'white';
	prophelp_layer.style.border = '1px solid black';

	if (prophelp_layer.style.display == 'none')
	{
		prophelp_layer.style.display = 'block';
	}
	else
	{
		prophelp_layer.style.display = 'none';
	}
}
</script>

<!-- sisu -->
	<div id="sisu">{VAR:content}</div>
<!-- //sisu -->

