<script src="/automatweb/js/popup_menu.js" type="text/javascript"></script>

<script language="javascript">
<!--
var chk_status = true;

function selall()
{
	len = document.foo.elements.length;
	for (i=0; i < len; i++)
	{
		if (document.foo.elements[i].name.indexOf("sel") != -1)
		{
			document.foo.elements[i].checked=chk_status;
			window.status = ""+i+" / "+len;
		}
	}
	chk_status = !chk_status;
}

function add()
{
	if (document.foo.type.selectedIndex < 2)
	{
		alert("Valige objekt, mida lisada soovite!");
	}
	else
	{
		url = "orb.{VAR:ext}?class="+document.foo.type.options[document.foo.type.selectedIndex].value+"&action=new&parent={VAR:parent}&period={VAR:period}";
		window.location = url;
	}
}

function submit(val)
{
	document.foo.action.value=val;
	document.foo.submit();
}

function change(val)
{
	cnt = 0;
	len = document.foo.elements.length;
	for (i=0; i < len; i++)
	{
		if (document.foo.elements[i].name.indexOf("sel") != -1)
		{
			if (document.foo.elements[i].checked)
			{
				cnt++;
			}
		}
	}

	if (cnt == 1)
	{
		document.foo.action.value="change_redir";
		document.foo.submit();
	}
	else
	{
		alert("Valige ainult yks objekt palun!");
	}
}

function go_add(cl)
{
	url = "orb.{VAR:ext}?class=" + cl + "&action=new&parent={VAR:parent}&period={VAR:period}";
	document.location = url;
}

function go_change(cl,id,par)
{
	pare = par ? par : "{VAR:parent}";
	url = "orb.{VAR:ext}?class=" + cl + "&action=change&parent=" + pare + "&period={VAR:period}&id=" + id ;
	document.location = url;
}


function go_view(cl,id,par)
{
	pare = par ? par : "{VAR:parent}";
	url = "orb.{VAR:ext}?class=" + cl + "&action=view&parent=" + pare + "&period={VAR:period}&id=" + id ;
	document.location = url;
}

function go_cut(id,par)
{
	pare = par ? par : "{VAR:parent}";
	url = "orb.{VAR:ext}?class=menuedit&reforb=1&action=cut&parent=" + pare + "&id=" + id + "&sel[" + id + "]=1";
	document.location = url;
}

function go_copy(id,par)
{
	pare = par ? par : "{VAR:parent}";
	url = "orb.{VAR:ext}?class=menuedit&reforb=1&action=copy&parent=" + pare + "&id=" + id + "&sel[" + id + "]=1";
	document.location = url;
}

function go_delete(id,par)
{
	pare = par ? par : "{VAR:parent}";
	url = "orb.{VAR:ext}?class=menuedit&reforb=1&action=delete&parent=" + pare + "&id=" + id + "&sel[" + id + "]=1";
	document.location = url;
}

function go_open(id)
{
	url = "orb.{VAR:ext}?class=menuedit&action=right_frame&parent=" + id + "&period={VAR:period}";
	document.location = url;
}

function go_go(par,perio)
{
	perio = perio ? perio : "{VAR:period}";
	url = "orb.{VAR:ext}?class=menuedit&action=right_frame&parent=" + par + "&period=" + perio;
	document.location = url;
}

function go_acl(id)
{
	url = "editacl.{VAR:ext}?file=default.xml&oid=" + id;
	document.location = url;
}
// -->
</script>


<!-- begin ICONS table -->
<form action='reforb.{VAR:ext}' method="post" name="foo" style="display: inline;">
{VAR:toolbar}

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td class="{VAR:viewstyle}" bgcolor="#bdbdbd">
{VAR:table}

</td></tr></table>

{VAR:reforb}
</form>
