<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset={VAR:charset}" />
	<?php title(); ?>
	<link href="{VAR:baseurl}/css/style.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{VAR:baseurl}/automatweb/css/popup_menu.css" />

	<script src="{VAR:baseurl}/automatweb/js/popup_menu.js" type="text/javascript"></script>
	<script src="{VAR:baseurl}/automatweb/js/aw.js" type="text/javascript"></script>
	
	<script type="text/javascript" src="{VAR:baseurl}/js/jquery-1.3.1.min.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery/plugins/jquery_aw_releditor.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery/plugins/jquery_formreset.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery/plugins/jquery.selectboxes.min.js"></script>

	<script type="text/javascript" src="{VAR:baseurl}/css/tb/thickbox.js"></script>
	<link rel="stylesheet" href="{VAR:baseurl}/css/tb/thickbox.css" type="text/css" media="screen" />
	<link rel="shortcut icon" href="{VAR:baseurl}/img/favico.ico" type="image/ico" />
<!-- SUB: logged -->
</head>

<body>

<div class="wrapper">

	<form method="get" id="otsing" action="{VAR:baseurl}/index.{VAR:ext}" name="search">
		<input type="hidden" name="class" value="site_search_content" />
		<input type="hidden" name="action" value="do_search" />
		<input type="hidden" name="set_lang_id" value="{VAR:se_lang_id}" />
		<input type="hidden" name="id" value="5842" />
		<input type="hidden" name="section" value="7508" />
		<div style="position: absolute; background-color: #FFFFFF; padding: 5px 10px 6px 10px; left: 168px; top: 0px; font-size: 11px; ">
			<!-- SUB: LANG -->
				<a href='{VAR:lang_url}'>{VAR:name}</a>
			<!-- END SUB: LANG -->
			<!-- SUB: SEL_LANG -->
				{VAR:name} 
			<!-- END SUB: SEL_LANG -->
		</div>
		<div class="abimenyy">
			<!-- SUB: MENU_ABI_L1_ITEM_BEGIN -->
				<img src="{VAR:menu_image_0_url}" alt="" /> <a href="{VAR:link}" {VAR:target} >{VAR:text}</a>
			<!-- END SUB: MENU_ABI_L1_ITEM_BEGIN -->

			<!-- SUB: MENU_ABI_L1_ITEM -->
			| <!-- SUB: HAS_IMAGE -->
				<img src="{VAR:menu_image_0_url}" alt="" /> 
			<!-- END SUB: HAS_IMAGE -->
				<a href="{VAR:link}" {VAR:target}>{VAR:text}</a>
			<!-- END SUB: MENU_ABI_L1_ITEM -->
		
			<input name="str" type="text" class="otsingukast" size="5" value="{VAR:LC_SEARCH}" onfocus="if(this.value=='{VAR:LC_SEARCH}')this.value = ''" onblur="if(this.value=='')this.value='{VAR:LC_SEARCH}';" />
			<input name="Submit2" type="image" class="otsingunupp" value="Submit" src="{VAR:baseurl}/img/luup.gif" />
		</div>
	</form>

	<!-- header-->
	<div class="header">
	
		<div>
	  		<a href="{VAR:baseurl}"><img src="{VAR:baseurl}/img/sm_logo.gif" width="137" height="106" alt="" class="logo" border="0" /></a>
			<img src="{VAR:baseurl}/img/kald.gif" width="73" height="106" alt="" class="kald" />
		</div>
		
		<!-- main menu l1 -->
		<div class="peamenyy">
			<!-- SUB: MENU_P6HI_L1_ITEM -->
	  			<div class="link"><a href="{VAR:link}" {VAR:target}>{VAR:text}</a></div>
			<!-- END SUB: MENU_P6HI_L1_ITEM -->
			
			<!-- SUB: MENU_P6HI_L1_ITEM_SEL -->
	  			<div class="akt"><a href="{VAR:link}" {VAR:target}>{VAR:text}</a></div>
			<!-- END SUB: MENU_P6HI_L1_ITEM_SEL -->
		</div>
		<!-- /main menu -->
	</div>
	<!-- /header -->
	
	<!-- content -->
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="saidi_body_algus">
	<tr>
	  
	  	<!-- left pane -->
		<td class="vasakpaan" valign="top">
			<img src="{VAR:baseurl}/img/logokaar.gif" width="210" height="17" alt="" class="logokaar" />
			
			<!-- main menu l2 -->
			<!-- SUB: HAS_SUBITEMS_P6HI_L1_ITEM_SEL -->
			<div class="pohimenyy2">
				<div class="pohimenyy2caption">{VAR:sel_menu_P6HI_L1_text}</div>
					<ul>
						<!-- SUB: MENU_P6HI_L2_ITEM -->
						<li><a href="{VAR:link}">{VAR:text}</a></li>
						<!-- END SUB: MENU_P6HI_L2_ITEM -->

						<!-- SUB: MENU_P6HI_L2_ITEM_SEL -->
						<li><a href="{VAR:link}" class="akt" >{VAR:text}</a>
							<!-- SUB: HAS_SUBITEMS_P6HI_L2_ITEM_SEL -->
								<ul>
								<!-- SUB: MENU_P6HI_L3_ITEM_SEL -->
								<li><b>&#183;</b>&nbsp;<a href="{VAR:link}" class="akt">{VAR:text}</a></li>
								<!-- END SUB: MENU_P6HI_L3_ITEM_SEL -->
								<!-- SUB: MENU_P6HI_L3_ITEM -->
								<li><b>&#183;</b>&nbsp;<a href="{VAR:link}">{VAR:text}</a></li>
								<!-- END SUB: MENU_P6HI_L3_ITEM -->
								</ul>
							<!-- END SUB: HAS_SUBITEMS_P6HI_L2_ITEM_SEL -->
						</li>
						<!-- END SUB: MENU_P6HI_L2_ITEM_SEL -->
					</ul>
			</div>
			<!-- END SUB: HAS_SUBITEMS_P6HI_L1_ITEM_SEL -->
			<!-- /main menu l2 -->
			
			<!-- yellow container -->
			<!-- SUB: KOLLANE_PROMO -->
				<div class="kollanekonteiner">
					<div class="kollanecaption">{VAR:caption}</div>
					{VAR:content}
				</div>
			<!-- END SUB: KOLLANE_PROMO -->
			<!-- /yellow container -->
		
			<!-- login menu -->
			<div class="loginmenu"><div class="logincaption"><b>{VAR:uid}</b> ({VAR:date})</div>
				<ul>
				<!-- SUB: MENU_LOGGED_L1_ITEM -->
					<li><b>{VAR:text}</b></li>

					<!-- SUB: MENU_LOGGED_L2_ITEM -->
						<li><a {VAR:target} href="{VAR:link}">{VAR:text}</a></li>
					<!-- END SUB: MENU_LOGGED_L2_ITEM -->
				<!-- END SUB: MENU_LOGGED_L1_ITEM -->
				</ul>
			</div>
			<!-- /login menu -->
			
			<!-- pink container -->
			<!-- SUB: ROOSA_PROMO -->
				<div class="roosakonteiner">
					<div class="roosacaption">{VAR:caption}</div>
					{VAR:content}
					<p><a href="{VAR:link}"><b>{VAR:link_caption}</b></a></p>
				</div>
			<!-- END SUB: ROOSA_PROMO -->
			<!-- /pink container -->

			<!-- blue container -->
			<!-- SUB: SININE_PROMO -->
				<div class="sininekonteiner">
					<div class="sininecaption">{VAR:caption}</div>
					<p>{VAR:comment}</p>
					{VAR:content}
				</div>
			<!-- END SUB: SININE_PROMO -->
			<!-- /blue container -->
		</td>
		<!-- /left pane -->
		
		<!-- center pane -->
		<td class="doccontent" valign="top">
			<div class="tekstiala">
				{VAR:doc_content}
			</div>
			
			<!-- SUB: ITEMBOX -->
			<div class="bodycont">
		  		<div class="caption">{VAR:f_comment}</div>
				<div class="content">
					<div class="pilt"><img src="{VAR:f_img}" width="64" height="67" alt="" /></div>
					<div class="tekst">{VAR:f_doc}</div>
					<div class="lingid">
						<!-- SUB: ITEMBOX_SUB -->
						<a href="{VAR:s_link}" {VAR:s_target}>{VAR:s_text}</a> 
						<!-- END SUB: ITEMBOX_SUB -->
					</div>
				</div>
			</div>
			<!-- END SUB: ITEMBOX -->
			
			<!-- SUB: ITEMBOX_END -->
			<div class="bodycontlast">
		  		<div class="caption">{VAR:f_comment}</div>
				<div class="content">
					<div class="pilt"><img src="{VAR:f_img}" width="63" height="67" alt="" /></div>
					<div class="tekst">{VAR:f_doc}</div>
					<div class="lingid">
						<!-- SUB: ITEMBOX_SUB -->
						<a href="{VAR:s_link}" {VAR:s_target}>{VAR:s_text}</a> 
						<!-- END SUB: ITEMBOX_SUB -->
					</div>
				</div>
			</div>
			<!-- END SUB: ITEMBOX_END -->
		</td>
		<!-- /center pane -->
		
		<!-- SUB: RIGHT_PANE -->
		<!-- right pane -->
		<td class="parempaan" valign="top">
			<!--PAREMA PAANI MENÜÜPILDI ALGUS-->
			<!-- SUB: RANDOMPIC_PROMO -->
			<div class="menyypilt">{VAR:content}</div>
			<!-- END SUB: RANDOMPIC_PROMO -->
			<!--PAREMA PAANI MENÜÜPILDI LÕPP-->
			
			<!-- pink container -->
			<!-- SUB: NEWSRIGHT_PROMO -->
				<div class="roosakonteiner">
					<div> 
						<div class="roosacaption">{VAR:caption}</div>
						{VAR:content}
						<br />
						<p><a href="{VAR:link}"><b>{VAR:link_caption}</b></a></p>
					</div>
				</div>
			<!-- END SUB: NEWSRIGHT_PROMO -->
			<!-- /pink container -->
			
		</td>
		<!-- /right pane -->
		<!-- END SUB: RIGHT_PANE -->

	</tr>
	</table>
	<!-- /content -->
	
	<!-- footer -->
	<div class="footer">
		<span class="menu">
			<!-- SUB: MENU_P6HIALUMINE_L1_ITEM_BEGIN -->
				<a href="{VAR:link}">{VAR:text}</a>
			<!-- END SUB: MENU_P6HIALUMINE_L1_ITEM_BEGIN -->
			<!-- SUB: MENU_P6HIALUMINE_L1_ITEM -->	
				| <a href="{VAR:link}">{VAR:text}</a>
			<!-- END SUB: MENU_P6HIALUMINE_L1_ITEM -->
			<!-- SUB: MENU_P6HIALUMINE_L1_ITEM_BEGIN_SEL -->
				<a href="{VAR:link}"><b>{VAR:text}</b></a>
			<!-- END SUB: MENU_P6HIALUMINE_L1_ITEM_BEGIN_SEL -->
			<!-- SUB: MENU_P6HIALUMINE_L1_ITEM_SEL -->	
				| <a href="{VAR:link}"><b>{VAR:text}</b></a>
			<!-- END SUB: MENU_P6HIALUMINE_L1_ITEM_SEL -->
		</span>
	
		<span class="copyright">
			<!-- SUB: MENU_FOOTER_L1_ITEM -->
				<a href="{VAR:link}" {VAR:target}>{VAR:text}</a>
			<!-- END SUB: MENU_FOOTER_L1_ITEM -->
	
			<!-- SUB: MENU_FOOTER_L1_ITEM_SEP -->
				{VAR:text}
			<!-- END SUB: MENU_FOOTER_L1_ITEM_SEP -->
		</span>
	</div>
	<!-- /footer-->

</div>

</body>
<!-- END SUB: logged -->

<!-- SUB: login -->
<style type="text/css">
body { background: #ffffff; }
.logintable {
	font-family:  Tahoma, Verdana, Arial, sans-serif;
	font-size: 11px;
	color: #000000;
	text-decoration: none;
	background-color: #ffffff;
	border: 0px;
}
.logintable a {
	color: #0467a0;
	text-decoration:none;
	font-size: 11px;
}
.logintable a:hover {
	color: #0075B8;
	text-decoration:underline;
	font-size: 11px;
}
.logintable .loginbt {
	font-family:  Tahoma, Verdana, Arial, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background: #0075B8;
	border-color: #79B3D5;
}
.logintable .note {
	color: #c3162f;
	font-weight: bold;
	text-align: center;
	padding-bottom: 13px;
	font-size: 13px;
}
.logintable .caption {
	font-weight: bold;
	text-align: right;
	color: #000000;
	font-size: 11px;
}
.logintable .element {
	font-weight: bold;
	text-align: left;
	color: #000000;
	font-size: 11px;
}
.logintable .lingid {
	font-size: 11px;
	font-weight: bold;
	text-align: right;
	color: #000000;
	font-size: 11px;
}
.logintable .lingid a {
	color: #0467a0;
	text-decoration:none;
	font-size: 11px;
}
.logintable .lingid a:hover {
	color: #0467a0;
	text-decoration:underline;
	font-size: 11px;
}
.logintable .logo {
	text-align: left;
	padding-bottom: 23px;
	color: #000000;
	font-size: 11px;
}
.logintable .footer {
	text-align: center;
	border-top: 2px solid #0075B8;
	padding-top: 7px;
	color: #000000;
	font-size: 11px;
}
.logintable .footer p {
	margin: 0;
}
.logintable .textbox {
	background-color: #FFFFFF;
	font-family:  Tahoma, Verdana, Arial, sans-serif;
	border: 1px solid #0075B8;
	padding: 2px 5px 2px 5px;
	margin: 0 0 0 0;
	width: 250px;
	color: #000000;
	font-size: 11px;
}
.logintable .select {
	background-color: #FFFFFF;
	font-family:  Tahoma, Verdana, Arial, sans-serif;
	font-size: 11px;
	border: 1px solid #0075B8;
	margin: 0 0 0 0;
	color: #000000;
	font-size: 11px;
}
</style>

</head>

<body style="background-color: #ffffff;">

<center>

<table cellspacing="0" cellpadding="13" style="border: 2px solid #0075B8; margin-top: 50px;">
<tr>
<td>

<form action='{VAR:baseurl}/reforb.{VAR:ext}' method="post">
<table border="0" cellspacing="1" cellpadding="2" class="logintable">
	<tr>
		<td colspan="2" class="logo">
			<img src="{VAR:baseurl}/img/intraneti_logo.gif" alt="" border="0" />
		</td>
	</tr>

	<tr>
		<td class="caption">
			Kasutajanimi/Username:
		</td>
		<td class="element">
			<input type="text" name="uid" size="40" class="textbox" />
		</td>
	</tr>

	<tr>
		<td class="caption">
			Parool/Password:
		</td>
		<td class="element">
			<input type="password" name="password" size="40" class="textbox" />
		</td>
	</tr>

	<tr>
		<td class="caption">
		</td>
		<td align="left">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td  width="50%">
						<input type="submit" name="Submit" value="Sisene/Log in" class="loginbt" />
					</td>
					<td class="lingid" width="50%" align="right">
						<a href="{VAR:baseurl}/class=users&action=send_hash">Unustasid parooli?</a><br>
						<a target="new" href="http://support.automatweb.com">Abikeskkond</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="43">&nbsp;</td>
	</tr>
	<tr>
		<td class="footer" colspan="2">
			<p>OÜ Struktuur Meedia</p>
			<p>Aadress: Pärnu mnt. 158b, 11317, Tallinn</p>
			<p>Infotelefon: 655 8336</p>
			<p>E-mail: <a href="mailto:info@struktuur.ee">info@struktuur.ee</a></p>
		</td>
	</tr>
</table>

	<input type='hidden' name='action' value='login' />
	<input type='hidden' name='class' value='users' />

</form>

</td>
</tr>
</table>

</center>

</body>
<!-- END SUB: login -->

</html>



<?php

function title()
{
	$active_document_title = <<<EOF
	{VAR:active_document_title}
EOF;
	$active_document_title = trim(strip_tags($active_document_title));
	if (strlen($active_document_title)>0)
	{
		echo "<title>$active_document_title - AutomatWeb&reg; Demo</title>";
	}
	else
	{
		echo "<title>AutomatWeb&reg; Demo</title>";
	}
}

?>