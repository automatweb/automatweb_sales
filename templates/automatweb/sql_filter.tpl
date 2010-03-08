<form action="reforb.{VAR:ext}" METHOD="POST" name="fr" OnSubmit="return true;">
{VAR:reforb}
<input type="hidden" name="setfilt" id="setfilt">
<input type="hidden" name="selt" id="selt" value="">
<input type="hidden" name="type" id="typeid" value="">
<table border="0" cellspacing="0" cellpadding="0" >
<tr><td class="title"></td></tr>
<tr>
<td bgcolor="#CCCCCC">
<table border="0" cellspacing="1" cellpadding="2" width="100%">

<tr><td class="fgtitle" width="1%">Nimi</td><td class="fgtext" colspan="4"><input type="text" name="name" value="{VAR:name}" class="small_button" Style="width:280px;"></td></tr>
<tr><td class="fgtitle" >SQL</td><td class="fgtext" colspan="4" ><input disabled type="text" name="sql" value="{VAR:sql}" class="small_button" Style="width:280px;"></td></tr>
<tr><td class="fgtext" colspan="1" ><input type="submit" name="save_data"  value="Salvesta" class="small_button"></td>
<td colspan="4" class="fgtext" align="right">
<input type="submit"   value="Muuda" class="small_button" OnClick="sendcmd('filter_edit_change_part');">
<input type="submit"   value="Kustuta" class="small_button" OnClick="sendcmd('filter_edit_del');"></td></tr>

<tr>
<td class="title" align="left"><b><a>Seos</a></b></td>
<td class="title" align="left"><b><a>Tingimus</a></b></td>
<td class="title" align="center">Jrk</td>
<td class="title" align="center">Ign</td>
<td class="title" align="center">Vali</td>
</tr>
<!-- SUB: tingimused -->
<tr>
<td class="{VAR:rclass}" align="left">{VAR:join}</td>
<td class="{VAR:rclass}" align="left"><a href="{VAR:modifylink}">{VAR:sql}</a></td>
<td class="{VAR:rclass}" align="center"><input type="text" name="j2rjekord[{VAR:tid}]" value="{VAR:tid}" size="2"></td>
<td class="{VAR:rclass}" align="center"><input type="checkbox" name="ign[{VAR:tid}]" {VAR:ignchecked}></td>
<td class="title" align="center">
<input type="checkbox" name="sel[]" value="{VAR:tid}">

<!-- no kui te tıesti mugavamalt ei taha, ex siis teeme keeruliselt 
<a href="javascript:sendcmd2('filter_edit_up',{VAR:tid})"><img border=0 alt="kırgemale" src="/images/up_r_arr.gif"></a>
<a href="javascript:sendcmd2('filter_edit_down',{VAR:tid})"><img  border=0 alt="madalamale" src="/images/down_r_arr.gif"></a>
-->
</td>
</tr>
<!-- END SUB: tingimused -->

<tr><td class="fgtext" colspan="5" >&nbsp;</td></tr>

<tr>
<!-- SUB: LISA -->
<td class="ftitle2" colspan="5">Lisa <input type="radio" name="addpos"  value="before" class="small_button">enne&nbsp;&nbsp;<input type="radio" name="addpos" value="after" class="small_button" checked>p‰rast valitut</td>
<!-- END SUB: LISA -->

<!-- SUB: MUUDA -->
<td class="ftitle2" colspan="5">Muuda filtri osa
<input type="hidden" name="is_change_part" value="1">
<input type="hidden" name="change_part" value="{VAR:change_part}"></td>
<!-- END SUB: MUUDA -->
</tr>
<tr><td class="fgtitle" >Seos</td><td class="fgtext" colspan="4"><input type="radio" name="op" id="opand" value="and" {VAR:c_opand}  class="small_button">JA&nbsp;&nbsp;<input type="radio" name="op" id="opor" value="or" class="small_button" {VAR:c_opor}>V’I</td></tr>

<tr><td class="fgtitle" >V‰li</td><td class="fgtext" colspan="4"><select class="small_button" id="fie" name="fie" onchange="onfiechange(document.forms.fr.fie.selectedIndex)">
{VAR:fieldlist}
</select></td></tr>
<tr><td class="fgtitle">Vırdlus</td><td class="fgtext" colspan="4">
<select class="small_button" id="expr" name="expr"></select>
</td></tr>
<tr><td class="fgtitle">V‰‰rtus</td><td class="fgtext" colspan="4">
<span id="dval">
<input class="small_button" type=text id="val" name="val" Style="width:280px;">
</span>
<span id="dvalhelp" valign="top">
<select class="small_button" id="valhelp" onchange="javascript:document.forms.fr.val.value=document.forms.fr.valhelp.options[document.forms.fr.valhelp.selectedIndex].value;"></select>
</span>
<span id="ddateval" valign="top">
{VAR:dedit}<br>
<input type="radio" id="datespecial" OnClick="datespecialclick();" class="small_button" name="blah">kellaaeg
</span>
<span id="ddateval2" valign="top">
<input type="radio" id="datespecial2" OnClick="datespecial2click();" class="small_button" name="blah2">vali kindel aeg<br>
<input type="radio" name="dsplusminus" value="-" class="small_button"><b>miinus</b>&nbsp;
<input type="radio" name="dsplusminus" value="+" class="small_button"><b>pluss</b>&nbsp;
<input type="text" name="dsval"  class="small_button" size=5>&nbsp;
<select  class="small_button" name="dsyhik">
<option value="m">minutit</option>
<option value="h">tundi</option>
<option value="d">p‰eva</option>
</select>
</span>
</td></tr>
<tr><td class="fgtext"><input class="small_button" type="submit" onclick="javascript:sendcmd('filter_edit_add');" value="{VAR:buttoncaption}">
</td>
<td colspan="4" class="fgtext">
<!-- SUB: addpars -->
&nbsp;&nbsp;<input class="small_button" type="submit" onclick="javascript:sendcmd2('filter_edit_add','left');" value="Lisa (" disabled>
&nbsp;&nbsp;<input class="small_button" type="submit" onclick="javascript:sendcmd2('filter_edit_add','right');" value="Lisa )" disabled>
<!-- END SUB: addpars -->


</td></tr>
</table>
</td></tr>
</table>
Vali v&auml;ljund:<select name='filter_op'>{VAR:ops}</select>
</form>
<script language="javascript">
exprs=new Array("=","!=","LIKE",">","<",">=","<=");
validexprs=new Array(new Array(0,1,2,3,4,5,6),new Array(0,1,3,4,5,6),new Array(0,1,3,4,5,6));
ftnames=new Array("string","number","aeg");
ftypes=new Array({VAR:ftypes});
vhelp=new Array({VAR:foptions});
vhelpkeys=new Array({VAR:foptionsk});

function datespecialclick()
{
n2ita("ddateval2",1);
n2ita("ddateval",0);
};

function datespecial2click()
{
n2ita("ddateval2",0);
n2ita("ddateval",1);
};

function sendcmd(a)
{
document.fr.action.value=a;
};

function sendcmd2(a,b)
{
document.fr.action.value=a;
document.fr.selt.value=b;
fr.submit();
};

function n2ita(m,n)
{
if (n)
{
 if (document.all)
 {
  eval("document.all."+m+".style.display='';");
 } else
 {
  eval("document.all."+m+".style.display='';");
 };
} else
{
 if (document.all)
 {
  eval("document.all."+m+".style.display='none';")
 } else
 {
  eval("document.all."+m+".style.display='none';");
 };

};
};

function onfiechange(sel)
{
l=document.fr.expr.length;
//alert("onfiechange");
for (i=0;i<l;i++)
	document.fr.expr.options[0]=null;
for (i=0;i<validexprs[ftypes[sel]].length;i++)
{
	document.fr.expr.options[i]=new Option(exprs[validexprs[ftypes[sel]][i]],exprs[validexprs[ftypes[sel]][i]]);
	//alert(i+":"+exprs[validexprs[ftypes[sel]][i]]);
};

l=document.forms.fr.valhelp.length;
for (i=0;i<l;i++)
	document.forms.fr.valhelp.options[0]=null;
document.forms.fr.valhelp.options[0]=new Option("                    ");
for (i=0;i<vhelp[sel].length;i++)
{
	document.forms.fr.valhelp.options[i]=new Option(vhelp[sel][i],vhelpkeys[sel][i]);
	//alert("valhelp "+i+": v=*"+vhelp[sel][i]+"* k=*"+vhelpkeys[sel][i]+"*");
};

if (vhelp[sel].length>0)
{
	document.forms.fr.val.value="0"; //kuna esimene on valitud
	n2ita("dvalhelp",1);
	n2ita("dval",0);
}
else
{
	document.forms.fr.val.value="kirjuta "+ftnames[ftypes[sel]];
	n2ita("dvalhelp",0);
	n2ita("dval",1);
};

document.forms.fr.typeid.value=ftypes[sel];
if (ftypes[sel]==2)
{
 n2ita("ddateval",1);
 n2ita("dval",0);
 document.forms.fr.val.value="_date";
} else
{
 n2ita("ddateval",0);
};
n2ita("ddateval2",0);
};

onfiechange({VAR:change_p_fieldnum});

document.forms.fr.val.value="{VAR:selectedval}";
document.forms.fr.valhelp.value="{VAR:selectedval}";
document.forms.fr.expr.value="{VAR:selectedexpr}";

</script>