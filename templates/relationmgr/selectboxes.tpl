<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/selectboxes.js"></script>
</script>

<script LANGUAGE="JavaScript">
var listB = new DynamicOptionList("aselect","reltype");

{VAR:rels1}

{VAR:defaults1}

function init()
{
	var theform = document.changeform;
	listB.init(theform);
}

</script>

<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/selectbox_selector.js"></script>

