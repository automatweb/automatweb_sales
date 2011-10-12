<script type="text/javascript">
function aw_save()
{
	cnt = 0;
	res = "";
	
	if (document.changeform.check.length)
	{
		len = document.changeform.check.length;
		for (i = 0; i < len; i++)
		{
			if (document.changeform.check[i].checked)
			{
				if (cnt == 0)
				{
					res = document.changeform.check[i].value;
				}
				else
				{
					res = res + "," + document.changeform.check[i].value;
				}
				cnt++;
			};
		};
	}
	else
	{
		res = document.changeform.check.value;
	};

	if (res.length > 0)
	{
		link = '{VAR:saveurl}&alias=' + res + '&reforb=1';
		window.location = link;
	}
	else
	{
		alert('Ühtegi objekti pole valitud!');
	};
}

var chk_status = true;

function selall()
{
	len = document.changeform.elements.length;
	for (i=0; i < len; i++)
	{
		if (document.changeform.elements[i].name.indexOf("check") != -1)
		{
			document.changeform.elements[i].checked = chk_status;
			window.status = ""+i+" / "+len;
		}
	}
	chk_status = !chk_status;
}

function create_new_object()
{
var clids = new Array();
{VAR:clids}

	with(document.changeform)
	{
		cl = aselect.options[aselect.selectedIndex].value;
		if (cl == "capt_new_object")
		{
			alert('Vali objekti tüüp!');
		}
		else
		<!-- SUB: HAS_DEF_FOLDER -->
		if (cl == {VAR:def_fld_clid})
		{
			rel_type = reltype.options[reltype.selectedIndex].value;
			window.location.href="orb{VAR:ext}?class=" + clids[cl] + "&action=new&parent={VAR:def_parent}&period={VAR:period}&alias_to={VAR:id}&return_url={VAR:return_url}&reltype=" + rel_type;
		}
		else
		<!-- END SUB: HAS_DEF_FOLDER -->
		{
			rel_type = reltype.options[reltype.selectedIndex].value;
			window.location.href="orb{VAR:ext}?class=" + clids[cl] + "&action=new&parent={VAR:parent}&period={VAR:period}&alias_to={VAR:id}&return_url={VAR:return_url}&reltype=" + rel_type;
		};
	};
};

function search_for_object()
{
	var search_url = "{VAR:search_url}";
	reltype = document.changeform.reltype.options[document.changeform.reltype.selectedIndex].value;
	objtype = document.changeform.aselect.value;
	rurl = "{VAR:return_url}"

	window.location.href=search_url + "&reltype=" + reltype + "&aselect=" + objtype + "&return_url=" + rurl;
}
</script>
<script language= "javascript">
init();
</script>
