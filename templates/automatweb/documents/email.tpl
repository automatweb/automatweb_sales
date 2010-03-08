<SCRIPT LANGUAGE="JavaScript">
<!--
function Validator(theForm)	{
if (theForm.to.value == "") {
alert("VIGA! Te olete jätnud 'Kellele' e-maili lahtri tühjaks. - ERROR! E-mail field 'To' is empty.");
theForm.to.focus();
return (false);
}
if (theForm.from.value == ""){
alert("VIGA! Te olete jätnud 'Kellelt' e-maili lahtri tühjaks. - ERROR! E-mail field 'From' is empty.");
theForm.from.focus();
return (false);
}
return (true);
}
//-->
</SCRIPT>



    <!--begin KESKMISE TEKSTI OSA-->
<span class="text">Soovita dokumenti "<a href='/{VAR:section}'>{VAR:doc_name}</a>"</span> <br><br>

<FORM METHOD="post" ACTION="/reforb.{VAR:ext}" onSubmit="return Validator(this)" name="email">

<span class="text">

</span>


		<table border="0" cellspacing="5" cellpadding="1">


		<tr>
		<td align="right" class="text" valign="top">Saaja e-mail:</TD>
		<td valign="top"><input type="text" name='to' size="25"></td>
		</tr>

		<tr>
		<td align="right" class="text" valign="top">Saatja e-mail:</TD>

		<td valign="top"><input type="text" name='from' size="25"><br>
		</td>
		</tr>



		<tr>
		<td align="right" valign=top class="text">Sõnum:</TD>
		<td><TEXTAREA cols=30 name=comment rows=4></TEXTAREA></td>
		</tr>
		<TR>
		<TD valign="top" align="right"></TD>
		<TD valign="top">
		<INPUT type="submit" class="formbutton" value="Saadan">&nbsp;<INPUT type="reset" class="formbutton" value="Tühista">

		
		</TR>
		</table>




{VAR:reforb}
</form>











