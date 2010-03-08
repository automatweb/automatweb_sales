  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>

      <td width="210" valign="top"><table cellpadding="0" cellspacing="0" class="leftcontainer">
        <tr>
          <td width="213" class="header">{VAR:uid} ({VAR:date})</td>
        </tr>
        <tr>
          <td class="body">
		  	  <span class="caption">Minu tegevused</span><br>
              <a href="{VAR:add_event}">Lisa sündmus</a><br>

              <a href="{VAR:add_link}">Lisa link</a><br>
<!--              <a href="#">Lisa fail</a><br>-->
              <a href="{VAR:change_pwd}">Muuda parooli</a><br>
              <a href="{VAR:logout}">Logi välja</a>		   </td>
        </tr>
      </table>

	<!-- SUB: POLL -->
        <table cellpadding="0" cellspacing="0" class="leftcontainer">
          <tr>
            <td width="213" class="header">Küsitlus</td>
          </tr>
          <tr>
            <td class="body">
			{VAR:poll_ct}
			</td>
          </tr>
      </table>
	<!-- END SUB: POLL -->
	</td>
      <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="rightcontainer">
        <tr>
          <td width="49%" bgcolor="#979797" class="header">Tänased sündmused / {VAR:date2}              </td>
          <td width="51%" bgcolor="#979797" class="header"><div align="right">

            <select name="select" id="teiste_paev">
		{VAR:others}
            </select>
          </div></td>

        </tr>
        <tr>
          <td colspan="2" class="body"><table class="tablebody" width="100%" border="0" cellspacing="3" cellpadding="0">
	<!-- SUB: EVENT -->
            <tr>
              <td width="2%" valign="top"><img src="{VAR:icon}" width="16" height="16"></td>
              <td width="9%" valign="top"><div align="center">{VAR:timespan}</div></td>
              <td width="56%" valign="top">{VAR:name}</td>
              <td width="15%" valign="top">{VAR:cust} </td>
              <td width="18%" valign="top">{VAR:parts}</td>
              </tr>
	<!-- END SUB: EVENT -->
          </table></td>
        </tr>
      </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="rightcontainer">
          <tr>
            <td class="header">Homsed sündmused / {VAR:date3}</td>

          </tr>
          <tr>
            <td class="body"><table class="tablebody" width="100%" border="0" cellspacing="3" cellpadding="0">
	<!-- SUB: EVENT2 -->
            <tr>
              <td width="2%" valign="top"><img src="{VAR:icon}" width="16" height="16"></td>
              <td width="9%" valign="top"><div align="center">{VAR:timespan}</div></td>
              <td width="56%" valign="top">{VAR:name}</td>
              <td width="15%" valign="top">{VAR:cust} </td>
              <td width="18%" valign="top">{VAR:parts}</td>
              </tr>
	<!-- END SUB: EVENT2 -->

            </table></td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="rightcontainer">
          <tr>
            <td class="header">Mulle lisatud failid </td>

          </tr>
          <tr>
            <td class="body"><table class="tablebody" width="100%" border="0" cellspacing="3" cellpadding="0">
		<!-- SUB: MY_FILE -->
              <tr>
                <td width="2%" valign="top"><img src="{VAR:icon}"></td>
                <td width="47%" valign="top">{VAR:docs}</td>
                <td width="43%" valign="top">{VAR:name}</td>

                </tr>
		<!-- END SUB: MY_FILE -->
            </table></td>

          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="rightcontainer">
          <tr>
            <td class="header">Minu lingid </td>
          </tr>
          <tr>
            <td class="body"><table width="100%" class="tablebody" border="0" cellspacing="3" cellpadding="0">
		<!-- SUB: MY_LINK -->
              <tr>
                <td width="2%" valign="top"><img src="{VAR:icon}"></td>
                <td width="47%" valign="top"><a href="{VAR:url}">{VAR:name}</a></td>
                <td width="51%" valign="top">{VAR:link}</td>
                <td width="10%" valign="top"><a href='{VAR:del_link}'>Kustuta</a></td>
                </tr>
		<!-- END SUB: MY_LINK -->
            </table></td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="rightcontainer">
          <tr>
            <td class="header">Foorumi viimased teemad </td>
          </tr>
          <tr>

            <td class="body">
{VAR:forum}

</td>
          </tr>
        </table></td>
    </tr>
  </table