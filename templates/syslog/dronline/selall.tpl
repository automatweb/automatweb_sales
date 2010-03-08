<script language="javascript">
var chk_status = true;

function selall()
{
	len = document.blokk.elements.length;
	for (i=0; i < len; i++)
	{
		if (document.blokk.elements[i].name.indexOf("blo") != -1)
		{
			document.blokk.elements[i].checked=chk_status;
			window.status = ""+i+" / "+len;
		}
	}
	chk_status = !chk_status;
}
</script>
