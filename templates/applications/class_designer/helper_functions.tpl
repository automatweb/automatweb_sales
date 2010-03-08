<script type="text/javascript">
function create_group()
{
	var groupname = prompt("Sisesta grupi nimi:");
	if (groupname)
	{
		document.changeform.tmp_name.value = groupname;
		submit_changeform("create_group");
	};
}

function create_grid()
{
	submit_changeform("create_grid");
};

function create_element(clid)
{
	var elname = prompt("Sisesta elemendi nimi:");
	if (elname)
	{
		document.changeform.tmp_name.value = elname;
		document.changeform.element_type.value = clid;
		submit_changeform("create_element");
	};
};
</script>
