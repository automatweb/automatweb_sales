<!-- SUB: start -->
<script type="text/javascript">
if ( typeof jQuery == "undefined" )
{
        document.write("<script src=\"{VAR:baseurl}/automatweb/js/jquery/jquery-1.2.3.min.js\" type=\"text/javascript\"><\/script>");
}
</script>
<table id="toimingud">
	<td class="v">
<!-- END SUB: start -->
		<!-- SUB: button -->
		{VAR:surround_start}
		<div nowrap class="tb_but" onMouseOver="this.className='tb_but_ov'" onMouseOut="this.className='tb_but'" onMouseDown="this.className='tb_but_ov'" onMouseUp="this.className='tb_but'">
		<a href="{VAR:url}" onclick="{VAR:onclick}" name="{VAR:name}" target="{VAR:target}" {VAR:href_id}><img style="button" src="{VAR:img_url}" border="0" title="{VAR:tooltip}" alt="{VAR:tooltip}" /></a>
		</div>
		{VAR:surround_end}
		<!-- END SUB: button -->

		<!-- SUB: button_disabled -->
		<div nowrap class="aw04toolbarbutton">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><span style="position: relative; width: 100%; height: 100%;"><img src="{VAR:img_url}" border="0" title="{VAR:tooltip}" alt="{VAR:tooltip}" /><span style="position: absolute;  top: 0px; left: 0px; background: transparent url('{VAR:imgbase}/disabled_background.gif'); width: 100%; height: 100%; font-size: 2px;">&nbsp;</span></span></td>
				</tr>
			</table>
		</div>
		<!-- END SUB: button_disabled -->

		<!-- SUB: menu_button -->
		<div nowrap class="tb_but" valign="middle" onMouseOver="this.className='tb_but_ov'" onMouseOut="this.className='tb_but'" onMouseDown="this.className='tb_but_ov'" onMouseUp="this.className='tb_but'">
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td valign='bottom'>
					<a href="{VAR:url}" target="{VAR:target}" onclick="{VAR:onclick}"><img src="{VAR:img_url}" title="{VAR:tooltip}" alt="{VAR:tooltip}" border="0" /></a>
					</td>
					<td valign='bottom'>
						<a href="{VAR:url}" target="{VAR:target}" onclick="{VAR:onclick}"><img src="{VAR:imgbase}/downarr.png" alt="" border="0"></a></td>
					</a>
				</tr>
			</table>
		</div>
		<!-- END SUB: menu_button -->

		<!-- SUB: menu_button_lod -->
		<div id='tb_lod_{VAR:lod_name}' nowrap class="tb_but" valign="middle" onMouseOver="this.className='tb_but_ov'" onMouseOut="this.className='tb_but'" onMouseDown="this.className='tb_but_ov'" onMouseUp="this.className='tb_but'">
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td valign='bottom'>
					<a href="javascript:void(0);" onclick="tb_tb_lod{VAR:tb_lod_num}()"><img src="{VAR:img_url}" border="0"  width="16" height="16" title="{VAR:tooltip}" alt="{VAR:tooltip}" /></a>
					</td>
					<td valign='bottom'>
						<a href="javascript:void(0);" onclick="tb_tb_lod{VAR:tb_lod_num}()"><img src="{VAR:imgbase}/downarr.png" border="0"  width="7" height="4" title="{VAR:tooltip}" alt="{VAR:tooltip}" /></a></td>
					</a>
				</tr>
			</table>
		</div>
		<script language=javascript>
		function tb_tb_lod{VAR:tb_lod_num}()
		{
			el = document.getElementById("tb_lod_{VAR:lod_name}");
			el.innerHTML=aw_get_url_contents("{VAR:load_on_demand_url}");
			nhr=document.getElementById("href_{VAR:lod_name}");
			if (document.createEvent) {evObj = document.createEvent("MouseEvents");evObj.initEvent( "click", true, true );nhr.dispatchEvent(evObj);}
			else {
				nhr.fireEvent("onclick");
			}
		}
		</script>
		<!-- END SUB: menu_button_lod -->

		<!-- SUB: text_button -->
		 <div class="tb_but" valign="middle" onMouseOver="this.className='tb_but_ov'" onMouseOut="this.className='tb_but'" onMouseDown="this.className='tb_but_ov'" onMouseUp="this.className='tb_but'" title="{VAR:tooltip}" alt="{VAR:tooltip}"><a href="{VAR:url}" target="{VAR:target}" onclick="{VAR:onclick}" style="text-decoration: none; white-space: nowrap;">{VAR:caption}</a></div>
		<!-- END SUB: text_button -->

		<!-- SUB: text_menu_button -->
		<div class="tb_but" valign="middle" onMouseOver="this.className='tb_but_ov'" onMouseOut="this.className='tb_but'" onMouseDown="this.className='tb_but_ov'" onMouseUp="this.className='tb_but'" title="{VAR:tooltip}" alt="{VAR:tooltip}"><a  href="{VAR:url}" target="{VAR:target}" onclick="{VAR:onclick}" style="text-decoration: none; white-space: nowrap;">{VAR:text}</a>
		</div>
		<!-- END SUB: text_menu_button -->

		<!-- SUB: text_button_disabled -->
		 <div class="aw04toolbarbutton"><span style="white-space: nowrap;">{VAR:tooltip}</span></div>
		<!-- END SUB: text_button_disabled -->

		<!-- SUB: separator -->
		 <img style="float: left;" src="/automatweb/images/aw06/vahe_joon.gif" alt=" " width="2" height="26" class="vahe" hspace="2" />
		<!-- END SUB: separator -->

		<!-- SUB: cdata -->
			 <div class="cdata">{VAR:data}</div>
		<!-- END SUB: cdata -->

	</td><!-- mis see on? -->

	<!-- SUB: end -->
<!--	</div> -->
	<!-- END SUB: end -->

	<!-- SUB: right_side -->
	<td class="p">
		{VAR:right_side_content}
	<!-- END SUB: right_side -->

<!-- SUB: end -->
</td>
<!-- END SUB: end -->
<!-- SUB: real_end -->
</table>
<br class="clear" />
<!-- END SUB: real_end -->
