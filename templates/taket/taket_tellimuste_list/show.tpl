<br>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td bgcolor="#B9BED2">
				  		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
                  <tr>
                    <td height="70" valign="middle" bgcolor="#FFFFFF">
						  	<table border=0 cellspacing=0 cellpadding=2 width=100%>
                        <tr>
                          <td colspan="7">
								  		<table width="100%" height="30"  border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                <td class="lrgTitle">{VAR:trans_order_history} </td>
                                <td align="right"><span class="listItem"><a href="javascript:void()" onClick="window.open('{VAR:trans_qmark}','','directory=0,height=360,width=350,resizable=1, statusbar=0, hotkeys=0,menubar=0,scrollbars=0,status=0,toolbar=0')"><img src="img/qmark.gif" width="20" height="20" hspace="10" border="0"></a></span></td>
                              </tr>
                            </table></td>
                          </tr>
                      <tr>
                        <td class="{VAR:timestmpcss}"><a href="?class=taket_tellimuste_list&action=show&sort=timestmp&dir={VAR:timestmpdir}">{VAR:trans_registered}</a></td>
                        <td class="{VAR:order_idcss}"><a href="?class=taket_tellimuste_list&action=show&sort=order_id&dir={VAR:order_iddir}">{VAR:trans_order_no}</a></td>
						<td class="{VAR:order_idcss}">{VAR:trans_ebasket}</td>
                        <td class="{VAR:pricecss}"><a href="?class=taket_tellimuste_list&action=show&sort=price&dir={VAR:pricedir}">{VAR:trans_sum}</a></td>
                        <td class="{VAR:contactcss}"><a href="?class=taket_tellimuste_list&action=show&sort=contact&dir={VAR:contactdir}">{VAR:trans_contact}</a></td>
                        <td class="{VAR:transportcss}"><a href="?class=taket_tellimuste_list&action=show&sort=transport&dir={VAR:transportdir}">{VAR:trans_transport}</a></td>
						<td class="{VAR:transportcss}"><a href="?class=taket_tellimuste_list&action=show&sort=location&dir={VAR:locationdir}">{VAR:trans_location}</a></td>
                        <td class="{VAR:commentcss}"><a href="?class=taket_tellimuste_list&action=show&sort=comment&dir={VAR:commentdir}">{VAR:trans_comment}</a></td>
                        <td class="{VAR:statuscss}"><a href="?class=taket_tellimuste_list&action=show&sort=status&dir={VAR:statusdir}">{VAR:trans_status}</a></td>
                      </tr>
<!-- SUB: tellimus -->
                      <tr onmouseover="setPointer(this, '#EEEFF4')" onmouseout="setPointer(this, '#FFFFFF')">
                        <td class="listItem">{VAR:timestmp}</td>
                        <td class="listItemSec"><a href='index.aw?class=taket_tellimuste_list&action=show_order&order_id={VAR:id}'>{VAR:id}</a></td>
						<td class="listItemSec">{VAR:ebasket}</td>
                        <td class="listItemSec">{VAR:price}</td>
                        <td class="listItemSec">{VAR:contact}</td>
                        <td class="listItemSec">{VAR:transport} &nbsp;</td>
						<td class="listItemSec">{VAR:location} &nbsp;</td>
                        <td class="listItemSec">{VAR:comments} &nbsp;</td>
                        <td class="listItemSec">{VAR:trans_status_value}</td>
                      </tr>
<!-- END SUB: tellimus -->
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
