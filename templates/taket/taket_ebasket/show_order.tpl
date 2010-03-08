<br>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td bgcolor="#B9BED2"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
                  <tr>
                    <td height="70" valign="middle" bgcolor="#FFFFFF"><table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
                        <tr>
                          <td colspan="7"><table width="100%" height="30"  border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                <td class="lrgTitle">Tellimus {VAR:orderid} </td>
                                <td align="right">&nbsp;</td>
                              </tr>
                            </table></td>
                          </tr>
                        <tr>
                          <td width="19%" class="listTitle"><a href="#">Tootekood</a></td>
                          <td width="18%" class="{VAR:product_namecss}"><a href="#">Nimetus</a></td>
                          <td width="10%" class="{VAR:pricecss}"><a href="#">Jaehind</a></td>
                          <td width="10%" class="{VAR:discountcss}"><a href="#">Allah %</a></td>
                          <td width="5%" class="{VAR:finalpricecss}"><a href="#">Lõpphind</a></td>
						  <td width="5%" class="{VAR:finalpricecss}"><a href="#">Erihind</a></td>
                          <td width="10%" class="{VAR:quantitycss}" colspan=2><a href="#">Kogus </a></td>
                        </tr>
{VAR:toodeParsed}
<!-- SUB: toode -->
                        <tr onmouseover="setPointer(this, '#EEEFF4')" onmouseout="setPointer(this, '#FFFFFF')">
                          <td class="listItem" >{VAR:product_code}</td>
                          <td class="listItemSec" >{VAR:product_name}</td>
                          <td class="listItemSec" >{VAR:price}</td>
                          <td class="listItemSec" >{VAR:discount}</td>
                          <td class="listItemSec" >{VAR:finalprice}</td>
						  <td class="listItemSec" >{VAR:tarjoushinta}</td>
                          <td class="listItemSec" colspan=2 >{VAR:quantity}</td>
                        </tr>
<!-- END SUB: toode -->                        
                        <tr bgcolor="#B9BED2">
                          <td colspan="7"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
                            <tr bgcolor="#FFFFFF">
                              <td width="70%" align="right" class="listItem">Summa k&auml;ibemaksuta: </td>
                              <td align="right" class="listItem">{VAR:priceWithoutTax}&nbsp;</td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                              <td align="right" class="listItem">K&auml;ibemaks:</td>
                              <td align="right" class="listItem">{VAR:tax}&nbsp;</td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                              <td align="right" class="listItem">Hind kokku: </td>
                              <td align="right" class="listItem"><b>{VAR:priceGrandTotal}&nbsp;</b></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                              <td align="right" class="listItem">&nbsp;</td>
                              <td align="right" class="listItem">&nbsp;</td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                              <td align="right" class="listItem">Staatus: </td>
                              <td align="right" class="listItem"><b>{VAR:status}&nbsp;</b></td>
                            </tr>
                          </table>                            </td>
                          </tr>
                        <tr>
                          <td colspan="7">&nbsp;</td>
                        </tr>
                    </table></td>
                  </tr>
              </table></td>
              <td width="2" valign="top" bgcolor="#B9BED2"><img src="img/one_w.gif" width="2" height="2"></td>
            </tr>
            <tr>
              <td align="left" bgcolor="#B9BED2"><img src="img/one_w.gif" width="2" height="2"></td>
              <td bgcolor="#B9BED2"><img src="img/one.gif" width="2" height="2"></td>
            </tr>
          </table>
