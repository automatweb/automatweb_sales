<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
<Document>
	<name>{VAR:filename}</name>
<!-- SUB: styles -->
	<Style id="{VAR:id}">
		<IconStyle>
			<color>{VAR:icon_transp}{VAR:icon_color}</color>
			<scale>{VAR:icon_size}</scale>
			<Icon>
				<href>{VAR:icon_url}</href>
			</Icon>
			<hotSpot x="20" y="2" xunits="pixels" yunits="pixels"/>
		</IconStyle>
		<LabelStyle>
			<color>{VAR:label_transp}{VAR:label_color}</color>
			<scale>{VAR:label_size}</scale>
		</LabelStyle>
	</Style>
<!-- END SUB: styles -->
<!-- SUB: placemarks -->
	<Placemark>
		<name>{VAR:name}</name>
		<description>
		<![CDATA[
		<style type="text/css">
<!--
.style1 {color: #999999}
-->
</style>
<p class="style1">Aadress: {VAR:address}</p>
<table width="650" border="0" cellpadding="5" cellspacing="0">
  <tr>
    <td align="center"><img src="http://klient.struktuur.ee/ttu/kaart/flash/Pildid/{VAR:userta2}_1.JPG" width="200" height="150" border="1" bordercolor="#0099CC" /></td>
    <td align="center"><img src="http://klient.struktuur.ee/ttu/kaart/flash/Pildid/{VAR:userta2}_2.JPG" width="200" height="150" border="1" bordercolor="#0099CC" /></td>
    <td align="center"><img src="http://klient.struktuur.ee/ttu/kaart/flash/Pildid/{VAR:userta2}_3.JPG" width="200" height="150" border="1" bordercolor="#0099CC" /></td>
  </tr>
</table>
<table width="650" border="1" bordercolor="#6699FF" cellspacing="0" cellpadding="5">
  <tr>
    <td><span class="style1">Hoone kood:</span></td>
    <td><span class="style1">{VAR:userta2}</span></td>
  </tr>
  <tr>
    <td width="55%"><span class="style1">Hoone maht (m3)</span></td>
    <td width="45%"><span class="style1">{VAR:desc1}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Suletud netopind (m2)</span></td>
    <td><span class="style1">{VAR:desc2}</span></td>
  </tr>
  <tr>
    <td><span class="style1">&Otilde;pilaste arv:</span></td>
    <td><span class="style1">{VAR:userta3}</span></td>
  </tr>
  <tr>
    <td><span class="style1">M&auml;rkused:</span></td>
    <td><span class="style1">{VAR:userta4}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone soojuse eritarbimine  kWh/(m2 a), 2003</span></td>
    <td><span class="style1">{VAR:usertf1}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone soojuse eritarbimine  kWh/(m2 a), 2004</span></td>
    <td><span class="style1">{VAR:usertf2}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone soojuse eritarbimine  kWh/(m2 a), 2005</span></td>
    <td><span class="style1">{VAR:usertf3}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone soojuse eritarbimine  kWh/(m2 a), 2006</span></td>
    <td><span class="style1">{VAR:usertf4}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone elektri eritarbimine kWh/(m2 a ) 2003</span></td>
    <td><span class="style1">{VAR:usertf5}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone elektri eritarbimine kWh/(m2 a ) 2004</span></td>
    <td><span class="style1">{VAR:usertf6}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone elektri eritarbimine kWh/(m2 a ) 2005</span></td>
    <td><span class="style1">{VAR:usertf7}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone elektri eritarbimine kWh/(m2 a ) 2006</span></td>
    <td><span class="style1">{VAR:usertf8}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone energia  eritarbimine kWh/(m2 a ) 2003</span></td>
    <td><span class="style1">{VAR:usertf9}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone energia  eritarbimine kWh/(m2 a ) 2004</span></td>
    <td><span class="style1">{VAR:usertf10}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone energia  eritarbimine kWh/(m2 a ) 2005</span></td>
    <td><span class="style1">{VAR:userta5}</span></td>
  </tr>
  <tr>
    <td><span class="style1">Hoone energia  eritarbimine kWh/(m2 a ) 2006</span></td>
    <td><span class="style1">{VAR:userta1}</span></td>
  </tr>
</table>
		<!--{VAR:address} {VAR:desc1} {VAR:desc2} {VAR:usertf1} {VAR:userta1} {VAR:usertf2} {VAR:userta2} {VAR:usertf3} {VAR:userta3} {VAR:usertf4} {VAR:userta4} {VAR:usertf5} {VAR:userta5} {VAR:usertf6} {VAR:userta6} {VAR:usertf7} {VAR:userta7} {VAR:usertf8} {VAR:userta8} {VAR:usertf9} {VAR:userta9} {VAR:usertf10} {VAR:userta10}-->
		]]>
		</description>
		<LookAt>
			<longitude>{VAR:coord_y}</longitude>
			<latitude>{VAR:coord_x}</latitude>
			<altitude>0</altitude>
			<range>{VAR:view_range}</range>
			<tilt>{VAR:view_tilt}</tilt>
			<heading>{VAR:view_heading}</heading>
			<altitudeMode>relativeToGround</altitudeMode>
		</LookAt>
		<styleUrl>{VAR:style}</styleUrl>
		<Point>
			<extrude>1</extrude>
			<altitudeMode>relativeToGround</altitudeMode>
			<coordinates>{VAR:coord_y},{VAR:coord_x},{VAR:icon_height}</coordinates>
		</Point>
	</Placemark>
<!-- END SUB: placemarks -->
</Document>
</kml>
