<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> AutomatWeb {VAR:site_title}</TITLE>

<meta http-equiv="Content-Type" content="text/html; charset={VAR:sel_charset}">

<link rel="icon" href="/img/favicon.ico" type="image/ico">
<link rel="shortcut icon" href="/img/favicon.ico">

<META NAME="Generator" CONTENT="AutomatWeb&reg;">
<META NAME="Author" CONTENT="Struktuur Varahaldus">

		<meta name="Keywords" content="{VAR:keywords}">
		<meta name="Description" content="{VAR:description}">


		<link rel=stylesheet href="{VAR:baseurl}/css/styles.css" type="text/css">


		<link rel=stylesheet href="{VAR:baseurl}/css/site.css" type="text/css">


		<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
		<!--
function remote(toolbar,width,height,file) {
	self.name = "root";
	var wprops = "toolbar=" + toolbar + ",location=0,directories=0,status=0, "+
	"menubar=0,scrollbars=1,resizable=1,width=" + width + ",height=" + height;
	openwindow = window.open(file,"remote",wprops);
}

		function box2(caption,url){
		var answer=confirm(caption)
		if (answer)
		window.location=url
		}

// AW Javascript functions

// see on nö "core" funktsioon popuppide kuvamiseks. Interface juures on soovitav kasutada
// jargnenaid funktsioone
function _aw_popup(file,name,toolbar,location,status,menubar,scrollbars,resizable,width,height)
{
	 var wprops = 	"toolbar=" + toolbar + "," + 
	 		"location= " + location + "," +
			"directories=0," + 
			"status=" + status + "," +
	        	"menubar=" + menubar + "," +
			"scrollbars=" + scrollbars + "," +
			"resizable=" + resizable + "," +
			"width=" + width + "," +
			"height=" + height;

	openwindow = window.open(file,name,wprops);
};

function aw_popup(file,name,width,height)
{
	_aw_popup(file,name,0,1,0,0,0,1,width,height);
}

function aw_popup_s(file,name,width,height)
{
	_aw_popup(file,name,0,1,0,0,1,1,width,height);
};

function aw_popup_scroll(file,name,width,height)
{
	_aw_popup(file,name,0,0,0,0,1,1,width,height);
};

		//-->
		</SCRIPT>

<script language="javascript" type="text/javascript">
<!--
var Open = ""
var Closed = ""

function preload(){
if(document.images){
	Open = new Image(16,13)    
	Closed = new Image(16,13)
	Open.src = "{VAR:baseurl}/img/nool1down.gif"
	Closed.src = "{VAR:baseurl}/img/nool1.gif"
}}


function showhide(what,what2)
{
	if (what && what.style && what2)
	{
		if (what.style.display=='none')
		{
			what.style.display='block';
			what2.src="{VAR:baseurl}/img/nool1down.gif";
		}
		else
		{
			what.style.display='none'
			what2.src="{VAR:baseurl}/img/nool1.gif";
		}
	}
}

function nothing()
{
	return false;
}
-->
</script>
<script language="Javascript" type="text/javascript">
<!--

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}



// -->
</script>


</head>

<BODY bgcolor="#FFFFFF" marginwidth="20" marginheight="0" leftmargin="0" topmargin="0" onLoad="gimme();">
<center>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
	<td height="4"><img src='{VAR:baseurl}/img/trans.gif' height=4></td>
</tr>
</table>
<table width="780" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="500"><a href="{VAR:baseurl}"><IMG src="{VAR:sel_menu_UUS_L1_image_0_url}" BORDER=0 ALT="AutomatWeb - culture in database"></a></td>
		<td align="right" valign="top" class="topmenu">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="right" valign="top" class="topmenu">
						<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="1" HEIGHT="5" BORDER=0 ALT=""><br>
						<!-- SUB: MENU_YLEMINE_L1_ITEM_BEGIN -->
						<a {VAR:target} href="{VAR:link}">{VAR:text}</a>
						<!-- END SUB: MENU_YLEMINE_L1_ITEM_BEGIN -->
						<!-- SUB: MENU_YLEMINE_L1_ITEM -->
						| <a {VAR:target} href="{VAR:link}">{VAR:text}</a>
						<!-- END SUB: MENU_YLEMINE_L1_ITEM -->
						<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="19" HEIGHT="1" BORDER=0 ALT=""><br>
						<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="1" HEIGHT="2" BORDER=0 ALT=""><br>
						<IMG src="{VAR:baseurl}/img/joon_gray.gif" WIDTH="230" HEIGHT="1" BORDER=0 ALT=""><br>
					</td>
				</tr>
				<tr>
					<td height="20">&nbsp;</td>
				</tr>
<script>
<!--
function select_this(s){
	var d = s.options[s.selectedIndex].value;
	if (d != "_")
	{
		location.href=d;
	}
}
//-->
</script>
				<tr>
					<td class="topmenu" align="right" valign="center">
						<select name="hippopotamus!" onchange="select_this(this);">
							<!-- SUB: MENU_UUS_L1_ITEM -->
							<option value="_">{VAR:text}</option>

							<!-- SUB: MENU_UUS_L2_ITEM -->
							<option value="{VAR:link}">&nbsp;&nbsp;&nbsp;&nbsp;{VAR:text}</option>
							<!-- END SUB: MENU_UUS_L2_ITEM -->

							<!-- SUB: MENU_UUS_L2_ITEM_SEL -->
							<option SELECTED value="{VAR:link}">&nbsp;&nbsp;&nbsp;&nbsp;{VAR:text}</option>
							<!-- END SUB: MENU_UUS_L2_ITEM_SEL -->

							<!-- END SUB: MENU_UUS_L1_ITEM -->


						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="1" HEIGHT="2" BORDER=0 ALT=""><br>


<!--BEGIN MAIN_MENU-->
<table width="780" border="0" cellpadding="0" cellspacing="0">
<tr>

<td width="30"  height="25" background="{VAR:baseurl}/img/intranet/menu_taust.gif"><IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="30" HEIGHT="1" BORDER=0 ALT=""></td>
<td width="750" height="25" background="{VAR:baseurl}/img/intranet/menu_taust.gif">

<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="1"  class="menujoon"><IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td>

<!-- SUB: MENU_UUS_L3_ITEM_BEGIN -->
<td height="25">
	<!-- SUB: HAS_IMAGE -->
	<a {VAR:target} href='{VAR:link}'><img src='{VAR:menu_image_0_url}' alt='{VAR:text}' title='{VAR:text}' border="0"></a><br>

	<!--<a {VAR:target} href='{VAR:link}' onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('button{VAR:section}','','{VAR:menu_image_1_url}',1)"><img name="button{VAR:section}" src='{VAR:menu_image_0_url}' alt='{VAR:text}' title='{VAR:text}' border="0"></a>-->

	<!-- END SUB: HAS_IMAGE -->

	<!-- SUB: NO_IMAGE -->
	<span class="mainmenutext">&nbsp;&nbsp;<a {VAR:target} href='{VAR:link}'>{VAR:text}</a>&nbsp;&nbsp;</span>
	<!-- END SUB: NO_IMAGE -->
</td>
<td width="1" class="menujoon"><IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td>
<!-- END SUB: MENU_UUS_L3_ITEM_BEGIN -->




<!-- SUB: MENU_UUS_L3_ITEM_BEGIN_SEL -->
<td class="menubacksel" height="25">

	<!-- SUB: HAS_IMAGE -->
	<a {VAR:target} href='{VAR:link}'><img src='{VAR:sel_image_url}' alt='{VAR:text}'  title='{VAR:text}' border="0"></a><br>
	<!-- END SUB: HAS_IMAGE -->

	<!-- SUB: NO_IMAGE -->
	<span class="mainmenutextsel">&nbsp;&nbsp;<a {VAR:target} href='{VAR:link}'>{VAR:text}</a>&nbsp;&nbsp;</span>
	<!-- END SUB: NO_IMAGE -->


</td>
<td width="2" class="menujoon"><IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="2" HEIGHT="1" BORDER=0 ALT=""></td>
<!-- END SUB: MENU_UUS_L3_ITEM_BEGIN_SEL -->



<!-- SUB: MENU_UUS_L3_ITEM_END -->
<td height="25">


	<!-- SUB: HAS_IMAGE -->
	<a {VAR:target} href='{VAR:link}' ><img src='{VAR:menu_image_0_url}' alt='{VAR:text}' title='{VAR:text}' border="0"></a><br>
	<!-- END SUB: HAS_IMAGE -->

	<!-- SUB: NO_IMAGE -->
	<span class="mainmenutext">&nbsp;&nbsp;<a {VAR:target} href='{VAR:link}'>{VAR:text}</a>&nbsp;&nbsp;</span>
	<!-- END SUB: NO_IMAGE -->

</td>
<td width="1" class="menujoon"><IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td>
<!-- END SUB: MENU_UUS_L3_ITEM_END -->

<!-- SUB: MENU_UUS_L3_ITEM_END_SEL -->
<td class="menubacksel" height="25">

	<!-- SUB: HAS_IMAGE -->
	<a {VAR:target} href='{VAR:link}'><img src='{VAR:sel_image_url}' alt='{VAR:text}'  title='{VAR:text}' border="0"></a><br>
	<!-- END SUB: HAS_IMAGE -->

	<!-- SUB: NO_IMAGE -->
	<span class="mainmenutextsel">&nbsp;&nbsp;<a {VAR:target} href='{VAR:link}'>{VAR:text}</a>&nbsp;&nbsp;</span>
	<!-- END SUB: NO_IMAGE -->

</td>
<td width="2" class="menujoon"><IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="2" HEIGHT="1" BORDER=0 ALT=""></td>
<!-- END SUB: MENU_UUS_L3_ITEM_END_SEL -->



<!-- SUB: MENU_UUS_L3_ITEM_SEL -->
<td class="menubacksel" height="25">

	<!-- SUB: HAS_IMAGE -->
	<a {VAR:target} href='{VAR:link}'><img src='{VAR:sel_image_url}' alt='{VAR:text}'  title='{VAR:text}' border="0"></a><br>
	<!-- END SUB: HAS_IMAGE -->

	<!-- SUB: NO_IMAGE -->
	<span class="mainmenutextsel">&nbsp;&nbsp;<a {VAR:target} href='{VAR:link}'>{VAR:text}</a>&nbsp;&nbsp;</span>
	<!-- END SUB: NO_IMAGE -->

</td>
<td width="2" class="menujoon"><IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="2" HEIGHT="1" BORDER=0 ALT=""></td>
<!-- END SUB: MENU_UUS_L3_ITEM_SEL -->

<!-- SUB: MENU_UUS_L3_ITEM -->
<td height="25">

	<!-- SUB: HAS_IMAGE -->
	<a {VAR:target} href='{VAR:link}' ><img src='{VAR:menu_image_0_url}' alt='{VAR:text}'  title='{VAR:text}' border="0"></a><br>
	<!-- END SUB: HAS_IMAGE -->

	<!-- SUB: NO_IMAGE -->
	<span class="mainmenutext">&nbsp;&nbsp;<a {VAR:target} href='{VAR:link}'>{VAR:text}</a>&nbsp;&nbsp;</span>
	<!-- END SUB: NO_IMAGE -->
</td>
<td width="1" class="menujoon"><IMG
src="{VAR:baseurl}/img/intranet/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td>
<!-- END SUB: MENU_UUS_L3_ITEM -->
</tr>
</table>

</td>
</tr>
</table>
<!--END MAIN_MENU-->


<!--YAH-->
<table width="780" border="0" cellpadding="0" cellspacing="0">
<tr><td height="25" class="taust"><span class="yah"><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="1" BORDER=0 ALT="">
<!--{VAR:date}&nbsp;|
<a href="{VAR:baseurl}">Esileht</a> -->
<!-- SUB: YAH_LINK -->
> <a href="{VAR:link}">{VAR:text}</a> 
<!-- END SUB: YAH_LINK -->
</span>
</td>
<td align="right" class="taust">


<!-- SUB: SEARCH_SEL -->
<table border="0" cellspacing="3" cellpadding="0">
<form method="get" action="{VAR:baseurl}/index.{VAR:ext}">
<tr>
<td><input type="text" size="13" class="formsearch2" name="str" value="{VAR:str}"></td>
<td><input type="submit" value=" {VAR:LC_SEARCH_BTN} " class="formbutton"></td>
<input type="hidden" name="parent" value="7">
<input type="hidden" name="class" value="document">
<input type="hidden" name="action" value="search">
<input type="hidden" name="section" value="{VAR:section}">
</tr>
</form>
</table>
<!-- END SUB: SEARCH_SEL -->










</td>

</tr>
</table>
<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
<!--END YAH-->








<!--KESKMINE OSA-->


<table width="780" border="0" cellpadding="0" cellspacing="0">
<tr>

<!--LEFT_PANE 259 - 50  -->
<td rowspan="2" width="209" valign="top" class="taust">



	<!--BEGIN-->
	<table width="209" border="0" cellpadding="10" cellspacing="0">
	<tr><td>

	<span class="pealkiri2">{VAR:sel_menu_UUS_L3_name}</span>
	<br>
	<img src="{VAR:baseurl}/img/trans.gif" border="0" width="1" height="10" alt=""><br>

<!--left menu-->
<table width="189" border="0" cellspacing="0" cellpadding="0">
<!--begin link-->

<tr><td>
<!-- begin 1 -->




<!-- SUB: MENU_UUS_L4_ITEM -->

<!-- SUB: HAS_SUBITEMS_UUS_L4 -->
<span id="menu{VAR:section}" onClick="showhide(document.getElementById('menu{VAR:section}outline'),document.getElementById('menu{VAR:section}sign'))" style="cursor:pointer; text-decoration:underline; font-weight:bold;" class="leftlink"><img id="menu{VAR:section}sign" src="{VAR:baseurl}/img/nool1.gif" valign="bottom"><font color="#002F7F">{VAR:text}</font></span><br>
<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br>
<!-- END SUB: HAS_SUBITEMS_UUS_L4 -->

<!-- SUB: NO_SUBITEMS_UUS_L4 -->
<span id="menu{VAR:section}" style="cursor:pointer; text-decoration:underline; font-weight:bold" class="leftlink"><img id="menu{VAR:section}sign" src="{VAR:baseurl}/img/nool1.gif" valign="bottom"><a {VAR:target} href="{VAR:link}">{VAR:text}</a></span><br>
<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br>
<!-- END SUB: NO_SUBITEMS_UUS_L4 -->

<span id="menu{VAR:section}outline" style="display:none">
		<!-- begin 1 links -->
		<table border="0" cellpadding="0" cellspacing="0">
		<!-- SUB: MENU_UUS_L5_ITEM -->
		<tr>
			<td><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="18" HEIGHT="3" BORDER=0 ALT=""></td>
			<td valign="top"><IMG	src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br><IMG src="{VAR:baseurl}/img/nool2.gif" WIDTH="10" HEIGHT="8" BORDER=0 ALT=""></td>
			<td class="text" valign="top"><a {VAR:target} href="{VAR:link}">{VAR:text}</a><br><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="18" HEIGHT="5" BORDER=0 ALT=""></td>
		</tr>
		<!-- END SUB: MENU_UUS_L5_ITEM -->

		</table>
		<!-- end 1 links -->

</span> 
<!-- END SUB: MENU_UUS_L4_ITEM -->


<!-- SUB: MENU_UUS_L4_ITEM_SEL -->

<!-- SUB: HAS_SUBITEMS_UUS_L4 -->
<span id="menu{VAR:section}" onClick="showhide(document.getElementById('menu{VAR:section}outline'),document.getElementById('menu{VAR:section}sign'))" style="cursor:pointer; text-decoration:underline; font-weight:bold;" class="leftlink"><img id="menu{VAR:section}sign" src="{VAR:baseurl}/img/nool1.gif" valign="bottom"><font color="black">{VAR:text}</font></span><br>
<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br>
<!-- END SUB: HAS_SUBITEMS_UUS_L4 -->

<!-- SUB: NO_SUBITEMS_UUS_L4 -->
<span id="menu{VAR:section}" style="cursor:pointer; text-decoration:underline; font-weight:bold" class="leftlink"><img id="menu{VAR:section}sign" src="{VAR:baseurl}/img/nool1.gif" valign="bottom"><a {VAR:target} href="{VAR:link}"><font color="black">{VAR:text}</font></a></span><br>
<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br>
<!-- END SUB: NO_SUBITEMS_UUS_L4 -->

<span id="menu{VAR:section}outline" style="display:none">
		<!-- begin 1 links -->
		<table border="0" cellpadding="0" cellspacing="0">
		{VAR:MENU_UUS_L5_ITEM}

		<!-- SUB: MENU_UUS_L5_ITEM_SEL -->
		<tr>
			<td><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="18" HEIGHT="3" BORDER=0 ALT=""></td>
			<td valign="top"><IMG	src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br><IMG src="{VAR:baseurl}/img/nool2.gif" WIDTH="10" HEIGHT="8" BORDER=0 ALT=""></td>
			<td class="text" valign="top"><a {VAR:target} href="{VAR:link}"><font color="black">{VAR:text}</font></a><br><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="18" HEIGHT="5" BORDER=0 ALT=""></td>
		</tr>

			

		<!-- END SUB: MENU_UUS_L5_ITEM_SEL -->

		</table>
		<!-- end 1 links -->

</span> 
<!-- END SUB: MENU_UUS_L5_ITEM_SEL -->















<!-- end 1 -->
</td></tr>

<!--end link-->
</table>


	</td></tr>
	</table>
	<br>

	<!--END-->




	<!-- SUB: LEFT_PROMO -->

	<table width="209" border="0" cellpadding="5" cellspacing="0">
	<!-- SUB: SHOW_TITLE -->
	<tr class="promotaust"><td class="leftpromopealkiri">&nbsp;{VAR:title}</td></tr>
	<!-- END SUB: SHOW_TITLE -->

	<tr><td>

		<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td><span class="text">{VAR:content}</span></td></tr></table>
	</td></tr>
	</table>
	<br>
	<!-- END SUB: LEFT_PROMO -->




	<!--LOGIN MENYY-->
	<table width="209" border="0" cellpadding="5" cellspacing="0">
	<tr class="promotaust"><td class="leftpromopealkirismall">&nbsp;{VAR:uid} @ {VAR:date}</td></tr>
	<tr><td>

		<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr><td>
	<span class="text">

	<!-- SUB: MENU_LOGGED_L1_ITEM -->
		<b>{VAR:text}</b><br>

	<!-- SUB: MENU_LOGGED_L2_ITEM -->
		<a href="{VAR:link}">{VAR:text}</a><br>
	<!-- END SUB: MENU_LOGGED_L2_ITEM -->

	<!-- END SUB: MENU_LOGGED_L1_ITEM -->



	</span>
	

		</td></tr>
		</table>
	</td></tr>
	</table>
	<br>

	<!--END LOGIN MENYY-->
















</td>
		<td rowspan="2" width="1" valign="top"><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td>
		<!--INDEX CENTER PANE 520 + 50-->

		<td width="570" valign="top" height="99%">

		<table width="570" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" width="20"><IMG SRC="{VAR:baseurl}/img/trans.gif" WIDTH="20" HEIGHT="1" BORDER=0 ALT=""></td>
				<td width="550" valign="top">
				<IMG SRC="{VAR:baseurl}/img/trans.gif" WIDTH="1" HEIGHT="5" BORDER=0 ALT=""><br>
<!--			<a href="/kampaania"><IMG SRC="/img/freenotebook.gif" WIDTH="500" HEIGHT="80" BORDER=0 ALT="Tasuta sülearvuti!"></a>-->
					<!-- SUB: top_doc -->

					<!-- END SUB: top_doc -->

					<IMG SRC="{VAR:baseurl}/img/trans.gif" WIDTH="1" HEIGHT="10" BORDER=0 ALT=""><br>

<!-- SUB: HAS_SUBITEMS_UUS_L5_SEL -->
<table width="160" cellpadding="1" cellspacing="0" align="right">
<tr><td class="taust">
<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
<td class="text2" bgcolor="#FFFFFF">


<!--BEGIN 1-->
		<!-- links -->
		<table border="0" cellpadding="0" cellspacing="0">
	
		<!-- SUB: MENU_UUS_L6_ITEM -->
		<tr>
			<td valign="top" colspan="2" class="textmiddle"><b>{VAR:text}</b><br>
			<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""></td>
		</tr>

		<!-- SUB: MENU_UUS_L7_ITEM -->
		<tr>
			<td valign="top"><IMG	src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br><IMG src="{VAR:baseurl}/img/nool2.gif" WIDTH="10" HEIGHT="8" BORDER=0 ALT=""></td>
			<td class="textmiddle" valign="top"><a  href="{VAR:link}" {VAR:target}>{VAR:text}</a><br><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="18" HEIGHT="5" BORDER=0 ALT=""></td>
		</tr>
		<!-- END SUB: MENU_UUS_L7_ITEM -->

		<!-- SUB: MENU_UUS_L7_ITEM_SEL -->
		<tr>
			<td valign="top"><IMG	src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br><IMG src="{VAR:baseurl}/img/nool2.gif" WIDTH="10" HEIGHT="8" BORDER=0 ALT=""></td>
			<td class="textmiddle" valign="top"><a  href="{VAR:link}" {VAR:target}><font color="#000000">{VAR:text}</font></a><br><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="18" HEIGHT="5" BORDER=0 ALT=""></td>
		</tr>
		<!-- END SUB: MENU_UUS_L7_ITEM_SEL -->

		<!-- SUB: MENU_UUS_L7_ITEM_BEGIN_SEL -->
		<tr>
			<td valign="top"><IMG	src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br><IMG src="{VAR:baseurl}/img/nool2.gif" WIDTH="10" HEIGHT="8" BORDER=0 ALT=""></td>
			<td class="textmiddle" valign="top"><a  href="{VAR:link}" {VAR:target}><font color="#000000">{VAR:text}</font></a><br><IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="18" HEIGHT="5" BORDER=0 ALT=""></td>
		</tr>
		<!-- END SUB: MENU_UUS_L7_ITEM_BEGIN_SEL -->

		<!-- END SUB: MENU_UUS_L6_ITEM -->

		</table>
		<!-- end links -->
<IMG src="{VAR:baseurl}/img/trans.gif" WIDTH="10" HEIGHT="3" BORDER=0 ALT=""><br>
<!--END 1-->

</td>
</tr>
</table>

</td>
</tr>
</table>
<!-- END SUB: HAS_SUBITEMS_UUS_L5_SEL -->




					{VAR:doc_content}


				<br><br><br>

				<!-- SUB: DOWN_PROMO -->
				<table width="100%" border="0" cellpadding="5" cellspacing="0">

				<!-- SUB: SHOW_TITLE -->
				<tr><td class="taust"><span class="text"><b>{VAR:title}</b></span></td></tr>
				<!-- END SUB: SHOW_TITLE -->

				<tr><td>
			{VAR:content}
				</td></tr>
				</table>
			<!-- END SUB: DOWN_PROMO -->

				</td>
			</tr>
		</table>
	


		</td>
	</tr>
	
	<tr>
		<td valign="bottom" height="1%">
			
		</td>
	</tr>
	
</table>










<!--FOOTER-->
<IMG src="{VAR:baseurl}/img/joon_gray.gif" WIDTH="780" HEIGHT="1" BORDER=0 ALT=""><br>
<br>
<span class="topmenu">
AutomatWeb® on Struktuur Varahalduse registreeritud kaubamärk.<br>
Kõik õigused kaitstud 1999-2003.
</span><br><br>
<!--END FOOTER-->





<script language="javascript" type="text/javascript">

var menuoutline;
var menusign;

function gimme()
{
	preload();
	showhide(document.getElementById('menu{VAR:sel_menu_UUS_L4_id}outline'),document.getElementById('menu{VAR:sel_menu_UUS_L4_id}sign'));
	<!-- SUB: ENG -->
	showhide(document.getElementById('menu{VAR:sel_menu_FRONTPAGE_E_L1_id}outline'),document.getElementById('menu{VAR:sel_menu_FRONTPAGE_E_L1_id}sign'));
	<!-- END SUB: ENG -->

	<!-- SUB: NOT_ENG -->
	showhide(document.getElementById('menu{VAR:sel_menu_FRONTPAGE_L1_id}outline'),document.getElementById('menu{VAR:sel_menu_FRONTPAGE_L1_id}sign'));
	<!-- END SUB: NOT_ENG -->
}
</script>

</center>



</body>
</html>













