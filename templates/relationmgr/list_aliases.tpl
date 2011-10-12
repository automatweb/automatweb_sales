<script language="Javascript">
function create_new_object()
{
var clids = new Array();
{VAR:class_ids}
	with(document.changeform)
	{
		cl = aselect.options[aselect.selectedIndex].value;
		if (cl == "capt_new_object")
		{
			alert("Choose object type!");
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

function awdelete()
{
	len = document.changeform.elements.length;
	idx = 0;
	for (i = 0; i < len; i++)
	{
		with(document.changeform.elements[i])
		{
			if (type == "checkbox" && name.indexOf("check") != -1 )
			{
				if (checked)
				{
					idx++;
				};
			}
		}
	};

	if (idx > 0)
	{
		if (confirm('Kustutada need ' + idx + ' aliast?'))
		{
			document.changeform.subaction.value = 'delete';
			document.changeform.submit();
		};
	}
	else
	{
		alert('Vali kustutatavad objektid.');
	}
}
</script>
<script language= "javascript">init();</script>
<input type="hidden" name="subaction" id="subaction" value="">


