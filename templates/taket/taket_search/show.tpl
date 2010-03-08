<!-- tere -->
<script>
	function postSearch(start, order, direction){
		document.hiddenSearchForm.start.value=start;
		document.hiddenSearchForm.orderBy.value=order;
		document.hiddenSearchForm.direction.value=direction;
		document.hiddenSearchForm.submit();
	}
	
	function fillSearchForm(){
		//hiddenSearchForm
		document.hiddenSearchForm.tootekood.value=document.getElementById('newtootekood').value;
		document.hiddenSearchForm.toote_nimetus.value=document.getElementById('newtoote_nimetus').value;
		//document.hiddenSearchForm.otsitunnus.value=document.getElementById('newotsitunnus').value;
		document.hiddenSearchForm.kogus.value=document.getElementById('newkogus').value;
		document.hiddenSearchForm.asendustooted.value=document.getElementById('newasendustooted').value;
		document.hiddenSearchForm.laos.value=document.getElementById('newlaos').value;
		document.hiddenSearchForm.osaline.value=document.getElementById('newosaline').value;
		document.hiddenSearchForm.orderBy.value=document.getElementById('neworderBy').value;
		document.hiddenSearchForm.direction.value=document.getElementById('direction').value;
		document.hiddenSearchForm.asukoht.value=document.getElementById('newasukoht').value;
		//search form

		document.searchForm.tootekood.value=document.getElementById('newtootekood').value;
		document.searchForm.toote_nimetus.value=document.getElementById('newtoote_nimetus').value;
// we don't have this field in searchForm anymore
//		document.searchForm.kogus.value=document.getElementById('newkogus').value;
		document.searchForm.asendustooted.checked=(document.getElementById('newasendustooted').value==1)?true:false;
		document.searchForm.laos.checked=(document.getElementById('newlaos').value==1)?true:false;
		document.searchForm.orderBy.value=document.getElementById('neworderBy').value;
		document.searchForm.osaline.checked=(document.getElementById('newosaline').value==1)?true:false;
		document.searchForm.direction.value=document.getElementById('direction').value;
	}

	function submitSearchFormExtended(product_code, quantity, asukoht){
                document.hiddenSearchForm.laos.value=(document.searchForm.laos.checked==true)?1:0;
                document.hiddenSearchForm.asendustooted.value=(document.searchForm.asendustooted.checked==true)?1:0;
                document.hiddenSearchForm.osaline.value=(document.searchForm.osaline.checked==true)?1:0;

	//	document.hiddenSearchForm.asukoht.value=asukoht;
		document.hiddenSearchForm.asukoht.value=document.searchForm.asukoht.selectedIndex;
		document.hiddenSearchForm.tootekood.value=product_code;
		document.hiddenSearchForm.kogus.value=quantity;
		document.hiddenSearchForm.submit();
	}
</script>
<form name='hiddenSearchForm' action='index.aw' method='GET'>
<!-- input type='hidden' name='otsitunnus' -->
<input type='hidden' name='tootekood'>
<input type='hidden' name='toote_nimetus'>
<input type='hidden' name='laos'>
<input type='hidden' name='kogus'>
<input type="hidden" name="asukoht">
<input type='hidden' name='asendustooted'>
<input type='hidden' name='start'>
<input type='hidden' name='orderBy'>
<input type='hidden' name='direction'>
<input type='hidden' name='osaline'>
{VAR:reforb}
</form>
<form name='searchForm' action='index.aw' method='GET'>
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=20 rowspan=7>&nbsp;</td>
			<td class=formText nowrap>{VAR:trans_product_code}</td>
			<td class=formText>&nbsp;</td>
			<td width="65" rowspan="5" align="right" nowrap><a href='javascript:document.searchForm.submit();'><input type='image' src="img/{VAR:trans_search_button}" border="0"></a></td>
		</tr>
		<tr>
			<td nowrap><input class=formBox size=25 name=tootekood id='tootekood' style="width:160px"></td>
			<td class=formCheck><input type=checkbox value='1' name="asendustooted" id='asendustooted'> {VAR:trans_dont_search_replacements}&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<!-- <td class=formText nowrap>{VAR:trans_choose_quantity}</td>-->
			<td class=formText nowrap>{VAR:trans_product_name}</td>
			<td nowrap class=formCheck><input type='checkbox' name='osaline' value='1' id='osaline' checked> {VAR:trans_search_partial_code}</td>
		</tr>
		<tr>
			<!--<td nowrap><input name="kogus" class=formBox id="kogus" size=25  style="width:160px"></td>-->
			<td nowrap><input name="toote_nimetus" class=formBox id="toote_nimetus" size=25  style="width:160px;"></td>
			<td nowrap class=formCheck><input type='checkbox' name='laos' value='1' id='laos'> {VAR:trans_search_only_instock}</td>
		</tr>
		<tr>
			<td class="formText" nowrap>{VAR:trans_choose_location}</td>
			<td class="formCheck" nowrap><input name="wvat" id="wvat" value="1" type="checkbox" {VAR:wvat_check}> {VAR:trans_prices_w_vat}</td>
		</tr>
		<tr>
			<td nowrap>
				<select name="asukoht" class="formBox" id="asukoht" style="width:160px">
					<option value="0" {VAR:lis_sel0}>{VAR:trans_loc2}</option>
					<option value="1" {VAR:lis_sel1}>{VAR:trans_loc3}</option>
					<option value="2" {VAR:lis_sel2}>{VAR:trans_loc4}</option>
					<option value="3" {VAR:lis_sel3}>{VAR:trans_loc5}</option>
					<option value="4" {VAR:lis_sel4}>{VAR:trans_loc6}</option>
					<option value="5" {VAR:lis_sel5}>{VAR:trans_loc7}</option>
					<option value="-1" {VAR:lis_sel}>{VAR:trans_loc1}</option>
				</select>
			</td>
			<td class="formCheck" nowrap colspan="2">
				<table cellpadding=0 cellspacing=0 border=0 width="100%">
					<tr>
									<td align='left' valign="bottom">
										&nbsp;&nbsp;&nbsp;
										<input type='reset' class='formButton' value='{VAR:trans_clean_form}'>
									</td>
									<td align='right' valign="bottom">
										<input type='button' class='formButton' value='{VAR:trans_expanded_search}' onClick="window.open('{VAR:trans_expanded_search_id}','','directory=0,height=520,width=350,resizable=1, statusbar=0, hotkeys=0,menubar=0,scrollbars=0,status=0,toolbar=0')">
									</td>
								</tr>
							</table>
						  </td>
						  </tr>
								{VAR:reforb}
								<input type='hidden' id='start' value='0' name='start'>
								<input type='hidden' id='orderBy' value='' name='orderBy'>
								<input type='hidden' id='direction' value='' name='direction'>
								</form>
                      </table>
							 </td>
                    <td width="1" align="right" valign="middle">                      
