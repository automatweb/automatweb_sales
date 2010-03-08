<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>TakeT Tellimiskeskus</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<!-- link href="css/taket_tellimiskeskus.css" rel="stylesheet" type="text/css" -->
<style type="text/css">

body {
	background-image: url(img/bg.gif);
}

a:link {
	color: #CC0000;
	text-decoration: none;
}

a:visited {
	color: #CC0000;
	text-decoration: none;
}

a:hover {
	color: #CC0000;
	text-decoration: underline;
}

.text {
font-family: Arial, sans-serif;
font-size: 12px;
color: #000000;
line-height: 16px;
text-decoration: none;
}
.text a {color: #3C9917; text-decoration:underline;}
.text a:hover {color: #000000; text-decoration:underline;}

.formBox {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #484E66;
	border: 1px solid #4A4E69;
}
.formText {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #4A4E69;
	padding-right: 15px;
	padding-top: 3px;
}

.formCheck {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #4A4E69;
	padding-right: 15px;
}

.formButton {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #4A4E69;
	background-color: #B9BED1;
	border: 1px solid #FFFFFF;
}
.LcolTitle {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #4A4E69;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 20px;
	background-color: #B9BED1;
}
.menuTitle {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #4A4E69;
	padding-top: 5px;
	padding-bottom: 3px;
	padding-left: 20px;
}

.menuLink {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 10px;
	padding-left: 20px;
	padding-bottom: 5px;
	padding-top: 5px;
}
.listTitle {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #4A4E69;
	padding-top: 3px;
	padding-bottom: 3px;
	background-color: #B9BED2;
	padding-right: 5px;
	padding-left: 5px;
}

.listTitleSort {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #4A4E69;
	padding-top: 3px;
	padding-bottom: 3px;
	background-color: #B9BED2;
	padding-right: 5px;
	padding-left: 5px;
}

.listItem {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #4A4E69;
	padding-left: 5px;
}

.listItemSec {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #4A4E69;
	padding-left: 5px;
	border-left-width: 1px;
	border-left-style: solid;
	border-left-color: #B9BED2;
}
.listNone {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #ADADAD;
	padding-left: 5px;
	border-left-width: 1px;
	border-left-style: solid;
	border-left-color: #B9BED2;
}

.listItemRep {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9px;
	color: #FF9900;
	padding-left: 5px;
}

.orange {
	color: #FF9900;
}
.lrgTitle {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 15px;
	font-weight: bold;
	color: #4A4E69;
	padding-left: 10px;
}
.total {
	color: #4A4E69;
	background-color: #EEEFF4;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: normal;
	padding-top: 10px;
	padding-right: 5px;
	padding-bottom: 10px;
	padding-left: 5px;
}
.nr {
	padding-top: 10px;
	padding-right: 20px;
	padding-bottom: 10px;
	padding-left: 20px;
}
.nrText {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #4A4E69;
	padding-top: 10px;
	padding-bottom: 10px;
	padding-right: 20px;
}
.logoSub {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #4A4E69;
	padding-top: 20px;
}
.gray {
	color: #999999;
}


</style>

<script language="JavaScript">
<!--

function SymError()
{
  return true;
}

window.onerror = SymError;

//-->
</script>

<script language="JavaScript">
<!--
   function addOne(obj){
		var tmp = obj.value;
		if(tmp<0 || isNaN(tmp))
			tmp=0;
		else
			tmp++;
		obj.value=tmp;
   }

   function subtractOne(obj){
		var tmp=0;
      tmp = obj.value;
		if(tmp<=0 || isNaN(tmp))
			tmp=0;
		else
			tmp--;
		obj.value=tmp;
   }


/**
 * Sets/unsets the pointer in browse mode
 *
 * @param   object   the table row
 * @param   object   the color to use for this row
 *
 * @return  boolean  whether pointer is set or not
 */
function setPointer(theRow, thePointerColor)
{
    if (thePointerColor == '' || typeof(theRow.style) == 'undefined') {
        return false;
    }
    if (typeof(document.getElementsByTagName) != 'undefined') {
        var theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        var theCells = theRow.cells;
    }
    else {
        return false;
    }

    var rowCellsCnt  = theCells.length;
    for (var c = 0; c < rowCellsCnt; c++) {
        theCells[c].style.backgroundColor = thePointerColor;
    }

    return true;
} // end of the 'setPointer()' function
-->
</script>

</head>

<body>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td bgcolor="#B9BED2">
              	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
                  <tr>
							<td height="70" valign="middle" bgcolor="#FFFFFF">
								<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
									<tr>
										<td class='text' width=100>
											Nimi
										</td>
										<td class='listItem'>{VAR:eesperenimi}</td>
									</tr>
									<tr>
										<td class='text'>
											Telefon
										</td>
										<td class='listItem'>{VAR:kontakttelefon}</td>
									</tr>
									<tr>
										<td class='text'>
											Transport
										</td>
										<td class='listItem'>{VAR:transport_name}</td>
									</tr>
									<tr>
										<td class='text'>
											Info
										</td>
										<td class='listItem'>{VAR:info}</td>
									</tr>
								</table>
							<tr>
						</tr>
					</table>
				  </td>
			   </tr>
			  </table>		
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td bgcolor="#B9BED2">
              	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
                  <tr>
							<td valign="middle" bgcolor="#FFFFFF">
								<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
									<tr>
										<td class='text' width=100>
											Kasutajatunnus
										</td>
										<td class='listItem'>{VAR:user_id}</td>
									</tr>
								</table>
							<tr>
						</tr>
					</table>
				  </td>
			   </tr>
			  </table>		
		</td>
	</tr>
  <tr>
    <td>
		{VAR:content}
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
