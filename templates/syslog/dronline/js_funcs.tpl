<script language="javascript">
function save_as_obj()
{
	nimi = prompt('Sisesta objekti nimi');
	url = window.location;
	url += '&do_save_as_obj='+nimi;
	window.location.href = url;
}

function sel_tmsp(form)
{
	tspid = form.def_span.options[form.def_span.selectedIndex].value;

	url = form.def_url.value;
	url += '&def_span='+tspid;
	window.location.href = url;
}

function update_ip(form)
{
	ipallow = form.ip_allow.options[form.ip_allow.selectedIndex].value;
	ipblock = form.ip_block.options[form.ip_block.selectedIndex].value;

	url = form.def_url.value;
	url += '&sel_ip_allow='+ipallow;
	url += '&sel_ip_block='+ipblock;
	window.location.href = url;
}

</script>
