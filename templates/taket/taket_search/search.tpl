<script>
	var chkstatus=true;
	function selall(limit)
	{	
		var obj;
		for(i=0;i<limit;i++){
			obj=document.getElementById('valitud'+i);
			
			if(obj)
			{
				obj.checked=chkstatus;
			}
		}
		chkstatus=!chkstatus;
	}
</script>
<br>
<form action='index.aw?class=taket_ebasket&action=add_items' method='POST' name='foo'>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#B9BED2">
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td height="70" valign="middle" bgcolor="#FFFFFF">
	    <table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
              <tr>
	        <td height=30 class="listItem" colspan="3">{VAR:trans_searched_for} &quot;<b>{VAR:otsisin}</b>&quot;, {VAR:trans_found} {VAR:results} {VAR:trans_results}.</td>
	        <td colspan="13" align="right" class="listItem" >&nbsp;&nbsp;{VAR:trans_locs} <a href="javascript:void(0)" onClick="window.open('{VAR:trans_search_qmark}','','directory=0,height=340,width=350,resizable=1, statusbar=0, hotkeys=0,menubar=0,scrollbars=0,status=0,toolbar=0')"><img src="img/qmark.gif" width="20" height="20" hspace="10" border="0" align="absmiddle"></a></td>
              </tr>
              <tr>
                        <td width=50 height=30 class="{VAR:cssstaatus}"><a href="javascript: postSearch({VAR:prev},'staatus','{VAR:direction}')">{VAR:trans_status}</a></td>
                        <td width=120 height=30 class="{VAR:csstootekood}" nowrap><a href="javascript: postSearch({VAR:prev},'tootekood','{VAR:direction}')">{VAR:trans_product_code_table}</a></td>
                        <td width=170 class="{VAR:cssnimetus}"><a href="javascript: postSearch({VAR:prev},'nimetus','{VAR:direction}')">{VAR:trans_name}</a></td>
                        <td width=350 class="{VAR:cssotsitunnus}"><a href="javascript: postSearch({VAR:prev},'otsitunnus','{VAR:direction}')">{VAR:trans_searchcode2}</a></td>
                        <td width=30 class="{VAR:csshind}"><a href="javascript: postSearch({VAR:prev},'hind','{VAR:direction}')">{VAR:trans_original_price}</a></td>
                        <td width=20 class="{VAR:cssallahindlus}"><a href="javascript: postSearch({VAR:prev},'allahindlus','{VAR:direction}')">{VAR:trans_percentage}</a></td>
                        <td width=30 class="{VAR:csslopphind}"><a href="javascript: postSearch({VAR:prev},'lopphind','{VAR:direction}')">{VAR:trans_finalprice}</a></td>
			<td width=30 class="{VAR:csslopphind}"><a href="javascript:void(0)">{VAR:trans_tarjoushinta}</a></td>
                        <td width=10 class="listTitle" align='center'><a href="javascript:void(0)">{VAR:trans_quantity}</a></td>
                        <td width=40 class="{VAR:csslaos}" align='center'><a title="{VAR:trans_instock_msg}" href="javascript: postSearch({VAR:prev},'laos','{VAR:direction}')">{VAR:trans_instock}</a></td>
			<td width=40 class="{VAR:csslaos}" align='center'><a title="{VAR:trans_instock_msg}" href="javascript: postSearch({VAR:prev},'laos','{VAR:direction}')">{VAR:trans_instock2}</a></td>
			<td width=40 class="{VAR:csslaos}" align='center'><a title="{VAR:trans_instock_msg}" href="javascript: postSearch({VAR:prev},'laos','{VAR:direction}')">{VAR:trans_instock3}</a></td>
			<td width=40 class="{VAR:csslaos}" align='center'><a title="{VAR:trans_instock_msg}" href="javascript: postSearch({VAR:prev},'laos','{VAR:direction}')">{VAR:trans_instock4}</a></td>
			<td width=40 class="{VAR:csslaos}" align='center'><a title="{VAR:trans_instock_msg}" href="javascript: postSearch({VAR:prev},'laos','{VAR:direction}')">{VAR:trans_instock5}</a></td>
			<td width=40 class="{VAR:csslaos}" align='center'><a title="{VAR:trans_instock_msg}" href="javascript: postSearch({VAR:prev},'laos','{VAR:direction}')">{VAR:trans_instock6}</a></td>
                        <td class="listTitle" align='center'><a href="javascript:selall(40)">{VAR:trans_order}</a></td>
              </tr>
			 <!-- SUB: asendustoodeblock -->
                        <td class="{VAR:staatuscss}"><span title='{VAR:peatoode}'>{VAR:trans_replacement}</span>&nbsp;</td>
			 <!-- END SUB: asendustoodeblock -->
							 
			 <!-- SUB: mainproduct -->
                        <td class="{VAR:staatuscss}"><span>{VAR:trans_asked}</span>&nbsp;</td>
			 <!-- END SUB: mainproduct -->

			 <!-- SUB: product -->
              <tr onmouseover="setPointer(this, '#EEEFF4')" onmouseout="setPointer(this, '#FFFFFF')">
							 	{VAR:esimeneVeerg}
                        <td class="listItemSec" >{VAR:product_code}&nbsp;</td>
                        <td class="listItemSec" >{VAR:product_name}&nbsp;</td>
                        <td class="listItemSec" >{VAR:search_code}&nbsp;</td>
                        <td class="listItemSec" >{VAR:price}&nbsp;</td>
                        <td class="listItemSec" >{VAR:discount}&nbsp;</td>
                        <td class="listItemSec" >{VAR:finalPrice}&nbsp;</td>
			<td class="listItemSec" >{VAR:tarjoushinta}&nbsp;</td>
			{VAR:quantityParsed}
                        <td align=center class="listItemSec">{VAR:in_Stock3}</td>
			<td align=center class="listItemSec">{VAR:in_Stock4}</td>
			<td align=center class="listItemSec">{VAR:in_Stock5}</td>
			<td align=center class="listItemSec">{VAR:in_Stock6}</td>
			<td align=center class="listItemSec">{VAR:in_Stock7}</td>
			<td align=center class="listItemSec">{VAR:in_Stock8}</td>
			{VAR:karuParsed}
              </tr>
			<!-- END SUB: product -->

			<!-- SUB: instockno -->
				{VAR:trans_instock_no}
			<!-- END SUB: instockno -->
							
			<!-- SUB: instockyes -->
				<span class="red_text">{VAR:trans_instock_yes}</span>
			<!-- END SUB: instockyes -->
							
			<!-- SUB: instockpartially -->
				{VAR:trans_instock_partially}
			<!-- END SUB: instockpartially -->
							
				{VAR:productParsed}

			<!-- SUB: cannotSetQuantity -->
                        <td class="listItemSec" valign='middle'>
				<input id="koguseId{VAR:i}" name='quantity[{VAR:i}]' type="text" class="formBox" size="2" value='{VAR:quantity}'>
									<!-- a href="javascript:void()" onClick='addOne(document.getElementById("koguseId{VAR:i}"))'><img src="img/sym_inc.gif" width="7" height="7" border="0"></a>
									<a href="javascript:void()" onClick='subtractOne(document.getElementById("koguseId{VAR:i}"))'><img src="img/sym_deg.gif" width="7" height="7" border="0"></a -->
                                <input type='hidden' name='productId[{VAR:i}]' value='{VAR:product_code}'></td>
							<!-- END SUB: cannotSetQuantity -->

							<!-- SUB: canSetQuantity -->
                        <td class="listItemSec" valign='middle'>
				<input id="koguseId{VAR:i}" name='quantity[{VAR:i}]' type="text" class="formBox" size="2" value='{VAR:quantity}'>
									<!-- a href="javascript:void()" onClick='addOne(document.getElementById("koguseId{VAR:i}"))'><img src="img/sym_inc.gif" width="7" height="7" border="0"></a>
									<a href="javascript:void()" onClick='subtractOne(document.getElementById("koguseId{VAR:i}"))'><img src="img/sym_deg.gif" width="7" height="7" border="0"></a -->
               	                <input type='hidden' name='productId[{VAR:i}]' value='{VAR:product_code}'></td>
							<!-- END SUB: canSetQuantity -->

							 
							<!-- SUB: karu -->
                        <!-- td align="center" class="listItemSec" style="cursor:hand" onClick='document.location.href="index.aw?class=taket_ebasket&action=add_item&product_code={VAR:product_code2}&quantity={VAR:quantity}"'>
									<img src="img/karu.gif" width="13" height="10" border="0"></td -->
			<td align='center' class='listItemSec' valign='middle'><input type='checkbox' name='valitud[{VAR:i}]' id='valitud{VAR:i}' value='1'></td>
									<!-- END SUB: karu -->
								
								<!-- SUB: karupole -->
								<td align='center' class='listItemSec' valign='middle'><input type='checkbox' name='valitud[{VAR:i}]' id='valitud{VAR:i}' value='1'></td>
								<!-- END SUB: karupole -->

							<tr>
								<td colspan="11" align='right'>
									{VAR:ebasket_list_value}
									<!-- SUB: ebasket_list -->
									<select name='ebasket_name_list' class='formButton'>
										<option value='thisnevercomes'>{VAR:trans_choose_current_cart}</option>
										{VAR:ebasket_list_items}
									</select>
									<!-- END SUB: ebasket_list -->
									<!-- SUB: ebasket_list_item -->
									<option value='{VAR:ebasket_list_item_name}'>{VAR:ebasket_list_item_name}</option>
									<!-- END SUB: ebasket_list_item -->
								<td colspan='2'>
									<input class='formButton' type='textbox' name='ebasket_name' size=10>
								</td>
								<td colspan='5'>
									<input class='formButton' type='submit' value='{VAR:trans_add_to_cart}'>
								</td>
							</tr>
							<tr>
								<td colspan="6" class="listItem">1 - Kadaka pst, 2 - Punane tn, 3 - Tartu, 4 - P&auml;rnu, 5 - Paavli, 6 - Viljandi</td>
								<td colspan='5'></td>
								<td colspan='2' class='listItem'>{VAR:trans_ebasket_name}</td>
								<td colspan='5'></td>
							</tr>
                      <tr>
							 	<td height=30 class="listItem" colspan="16" align='middle'>
									<input type='hidden' id='newasendustooted' value='{VAR:asendustooted}'>
									<input type='hidden' id='newkogus' value='{VAR:kogus}'>
									<input type='hidden' id='newlaos' value='{VAR:laos}'>
									<input type='hidden' id='newtootekood' value='{VAR:tootekood}'>
									<input type='hidden' id='newtoote_nimetus' value='{VAR:toote_nimetus}'>
									<input type='hidden' id='neworderBy' value='{VAR:orderBy}'>
									<input type='hidden' id='newdirection' value='{VAR:direction}'>
									<input type='hidden' id='newosaline' value='{VAR:osaline}'>
									<input type="hidden" id="newasukoht" name="asukoht" value="{VAR:asukoht}">
									<script>
										fillSearchForm();
									</script>
									<!-- SUB: numbersPart -->
									<a href='javascript: postSearch({VAR:prev},"{VAR:orderBy}","{VAR:direction}")'>&laquo;</a>
									{VAR:pageNumbersParsed}
									<a href='javascript:postSearch({VAR:next},"{VAR:orderBy}","{VAR:direction}");'>&raquo;</a>
									<!-- END SUB: numbersPart -->
									<!-- SUB: pageNumbers -->
									<a href='javascript:postSearch({VAR:start},"{VAR:orderBy}","{VAR:direction}");'>{VAR:pageNumber}</a>
									<!-- END SUB: pageNumbers -->
								</td>
                      </tr>
                    </table>					
						</td>
                  </tr>
              </table>
				  </td>
              <td width="2" valign="top" bgcolor="#B9BED2"><img src="img/one_w.gif" width="2" height="2"></td>
            </tr>
            <tr>
              <td align="left" bgcolor="#B9BED2"><img src="img/one_w.gif" width="2" height="2"></td>
              <td bgcolor="#B9BED2"><img src="img/one.gif" width="2" height="2"></td>
            </tr>
          </table>
</form>
          <p>&nbsp;</p>
