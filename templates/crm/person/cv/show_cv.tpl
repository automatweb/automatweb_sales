<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
a:link {
	color: #1D2D7C;
	text-decoration: none;
}

a:visited {
	color: #1D2D7C;
	text-decoration: none;
}

a:hover {
	color: #1D2D7C;
	text-decoration: underline;
}

.link10px {
	font-family: Tahoma, Verdana, Arial, Sans-serif;
	font-size: 10px;
	color:#FFFFFF;
}

.link10px a:link {
	color: #FFFFFF;
	text-decoration: none;
}

.link10px a:visited {
	color: #FFFFFF;
	text-decoration: none;
}

.link10px a:hover {
	color: #FFFFFF;
	text-decoration: underline;
}



.banner {
	background-image: url(../img/banner_bg_2.jpg);
	background-repeat: no-repeat;
}

.aw04tab2content {
height: 24px;
font-family: Arial, sans-serif;
font-size: 11px;
font-weight: bold;
color:#5D5D5D;
vertical-align: middle;
}

.aw04tab2content a {
color: #0017AF;
text-decoration:none;
}

.aw04tab2content a:hover {
color: #0017AF;
text-decoration:underline;
}




.aw04tab2selcontent {
height: 24px;
font-family: Arial, sans-serif;
font-size: 11px;
font-weight: bold;
color:#FFFFFF;
vertical-align: middle;
}

.aw04tab2selcontent a {
color: #FFFFFF;
text-decoration:none;
}

.aw04tab2selcontent a:link {
color: #FFFFFF;
text-decoration:none;
}

.aw04tab2selcontent a:hover {
color: #FFFFFF;
text-decoration:underline;
}

.topBg {
	background-image: url(../img/top_bg.gif);
	background-color: #62BFE5;
}

.aw04tab2smallcontent {
height: 18px;
font-family: Arial, sans-serif;
font-size: 11px;
color:#FFFFFF;
vertical-align: middle;
}

.aw04tab2smallcontent a {
color: #000000;
text-decoration:none;
}

.aw04tab2smallcontent a:hover {
color: #0017AF;
text-decoration:underline;
}


.text11px {
	font-family: Tahoma, Verdana, Arial, Sans-serif;
	font-size: 11px;
	color: #000000;
}
.date {
	font-family: Tahoma, Verdana, Arial, Sans-serif;
	font-size: 10px;
	color: #666666;
}

.dateGr {
	font-family: Tahoma, Verdana, Arial, Sans-serif;
	font-size: 10px;
	color: #009900;
}

.link11px {
	font-family: Tahoma, Verdana, Arial, Sans-serif;
	font-size: 11px;
	color: #000000;
}
.input {
	border: 1px solid #6473C0;
	font-family: Tahoma, Verdana, Arial, Sans-serif;
	font-size: 11px;
	color: #333333;
}
.text10px {
	font-family: Tahoma, Verdana, Arial, Sans-serif;
	font-size: 10px;
	color: #000000;
}
.text16px {
	font-family: Tahoma, Verdana, Arial, Sans-serif;
	font-size: 16px;
	color: #000000;
}
-->
</style>
<!--<link href="http://personal.struktuur.ee/css/personal.css" rel="stylesheet" type="text/css">-->
<center>

<div class="text16px"><b>CURRICULUM VITAE</b></div><br>
<table width="546" cellpadding="1" cellspacing="1" border="0">
  <tr bgcolor="#B1DFF2">
    <td height="20" colspan="2" align="left" valign="top" class="text11px" style="padding-bottom:5px; padding-top:5px; padding-left:10px;"><strong>&nbsp;Isikuandmed </strong> </td>
  </tr>
  <tr>
    <td width="174" height="20" align="left" valign="middle" bgcolor="#EBEBEB" class="text11px" style="padding-left:10px;"><strong>Nimi:&nbsp;</strong></td>
    <td class="text11px" width="372" height="20" align="left" style="padding-left:10px;"><b>{VAR:name}</b></td>

  </tr>
  <tr>
    <td width="174" height="20" align="left" valign="middle" bgcolor="#EBEBEB" class="text11px" style="padding-left:10px;"><strong>Sugu:&nbsp;</strong></td>
    <td class="text11px" width="372" height="20" align="left" style="padding-left:10px;"><b>{VAR:gender}</b></td>
  </tr>
  <tr>
    <td width="174" height="20" align="left" valign="middle" bgcolor="#EBEBEB" class="text11px" style="padding-left:10px;"><strong>Sünniaeg:&nbsp;</strong></td>
    <td class="text11px" width="372" height="20" align="left" style="padding-left:10px;"><b>{VAR:birthday}</b></td>
  </tr>
  <tr>
    <td width="174" height="20" align="left" valign="middle" bgcolor="#EBEBEB" class="text11px" style="padding-left:10px;">CV uuendamise kuupäev:&nbsp;</td>
    <td class="text11px" width="372" height="20" align="left" valign="top" style="padding-left:10px;">{VAR:modified}</td>
  </tr>
  <tr>
    <td class="cvVormSpacer" colspan="2">&nbsp;</td>
  </tr>
</table>  


<table width="546" cellpadding="1" cellspacing="1" border="0">
  <tr bgcolor="#B1DFF2">
    <td height="20" colspan="3" align="left" valign="top" class="text11px" style="padding-bottom:5px; padding-top:5px; padding-left:10px;"><strong>Haridus</strong></span></td>
  </tr>
	<!-- SUB: ED -->
  <tr>
    <td width="174" height="20" align="left" bgcolor="#EBEBEB" class="link11px" style="padding-left:10px;">{VAR:from} - {VAR:to}</td>
    <td class="text11px" height="20" colspan="2" width="372" align="left" style="padding-left:10px;">{VAR:where}, {VAR:extra}</td>
  </tr>
	<!-- END SUB: ED -->
  <tr>
    <td height="20" colspan="2" align="left" valign="middle" class="text11px" style="padding-left:10px;">&nbsp;</td>
  </tr>
</table>


<table width="546" cellpadding="1" cellspacing="1" border="0">
  <tr>
    <td height="20" colspan="2" align="left" valign="top" bgcolor="#B1DFF2" class="text11px" style="padding-bottom:5px; padding-top:5px; padding-left:10px;"><strong>Arvutioskused</strong></td>
  </tr>
	<!-- SUB: COMP_SKILL -->
  <tr>
    <td height="20" width="174" align="left" valign="top" nowrap class="text11px">{VAR:skill_name}</td>
    <td height="20" width="372" align="left" valign="top" nowrap class="text11px">{VAR:skill_skill}</td>
  </tr>
	<!-- END SUB: COMP_SKILL -->

  <tr>
    <td height="20" colspan="2" align="left" valign="middle" class="text11px" style="padding-left:10px;">&nbsp;</td>
  </tr>
</table>

<table width="546" cellpadding="1" cellspacing="1" border="0">
  <tr>
    <td height="20" colspan="2" align="left" valign="top" bgcolor="#B1DFF2" class="text11px" style="padding-bottom:5px; padding-top:5px; padding-left:10px;"><strong>Keeleoskused</strong></td>
  </tr>
	<!-- SUB: LANG_SKILL -->
  <tr>
    <td height="20"  width="174" align="left" valign="top" nowrap class="text11px">{VAR:skill_name}</td>
    <td height="20" width="372" align="left" valign="top" nowrap class="text11px">{VAR:skill_skill}</td>
  </tr>
	<!-- END SUB: LANG_SKILL -->

  <tr>
    <td height="20" colspan="2" align="left" valign="middle" class="text11px" style="padding-left:10px;">&nbsp;</td>
  </tr>
</table>

<table width="546" cellpadding="1" cellspacing="1" border="0">
  <tr>
    <td height="20" width="546" colspan="2" align="left" valign="top" bgcolor="#B1DFF2" class="text11px" style="padding-bottom:5px; padding-top:5px; padding-left:10px;"><strong>Juhiload</strong></td>
  </tr>
  <tr>
    <td height="20" colspan="2"  align="left" valign="top" nowrap class="text11px" style="padding-left:10px;">
		<!-- SUB: DRIVE_SKILL -->
		{VAR:skill_name}, alates aastast {VAR:driving_since}
		<!-- END SUB: DRIVE_SKILL -->
	</td>
  </tr>

  <tr>
    <td height="20" colspan="2" align="left" valign="middle" class="text11px" style="padding-left:10px;">&nbsp;</td>
  </tr>
</table>


<table width="546" cellpadding="1" cellspacing="1" border="0">
  <tr bgcolor="#B1DFF2">
    <td height="20" colspan="2" align="left" valign="top" class="text11px" style="padding-bottom:5px; padding-top:5px; padding-left:10px;"><strong>&nbsp;Töökogemused </strong></td>
  </tr>
  {VAR:kogemused_list}
  <tr>
    <td height="20" colspan="2" align="left" valign="middle" style="padding-left:10px;">&nbsp;</td>
  </tr>
</table>

<table width="546" cellpadding="1" cellspacing="1" border="0">
  <tr bgcolor="#B1DFF2">
    <td height="20" colspan="3" align="left" valign="top" class="text11px" style="padding-bottom:5px; padding-top:5px; padding-left:10px;"><strong>Soovitajad</strong></span></td>
  </tr>
  <tr>
    <td colspan="2" class="text11px" height="20" colspan="2" width="372" align="left" style="padding-left:10px;">{VAR:recommenders}</td>
  </tr>
  <tr>
    <td height="20" colspan="2" align="left" valign="middle" class="text11px" style="padding-left:10px;">&nbsp;</td>
  </tr>
</table>









<!-- SUB: work_experiences -->
	<tr>
		<td width="174" height="20" align="left" valign="middle" bgcolor="#EBEBEB" class="text11px" style="padding-left:10px;"><b>Asutus:</b></td>
		<td class="text11px" width="372" height="20" align="left" bgcolor="#EBEBEB" style="padding-left:10px;"><b>{VAR:company}</b></td>
	</tr>
	
	<tr>
		<td width="174" height="20" align="left" valign="middle" bgcolor="#EBEBEB" class="text11px" style="padding-left:10px;">Periood:</td>
		<td class="text11px" width="372" height="20" align="left" style="padding-left:10px;">{VAR:period}</td>
	<tr>
	
	<tr>
		<td width="174" height="20" align="left" valign="middle" bgcolor="#EBEBEB" class="text11px" style="padding-left:10px;">Ametinimetus:</td>
		<td class="text11px" width="372" height="20" align="left" style="padding-left:10px;">{VAR:profession}</td>
	</tr>
	
	<tr>
		<td width="174" height="20" align="left" valign="middle" bgcolor="#EBEBEB" class="text11px" style="padding-left:10px;">Töökokohustused:</td>
		<td class="text11px" width="372" height="20" align="left" style="padding-left:10px;">{VAR:duties}</td>
	</tr>
	
	</tr>
<!-- END SUB: work_experiences -->

 
</center>
asdfa({VAR:cur_org_start})</br>
{VAR:cur_org_position}<br/>
{VAR:cur_org_time}<br/>
<img src="{VAR:picture_url}"/><br/>

