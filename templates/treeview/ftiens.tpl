<script src="{VAR:baseurl}/automatweb/js/ua.js" type="text/javascript"></script>
<script src="{VAR:baseurl}/automatweb/js/ftiens_no_root.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
USETEXTLINKS = 1
LINKTARGET = "{VAR:linktarget}";
SHOWNODE = "{VAR:shownode}";
ICONPATH = '{VAR:baseurl}/automatweb/images/';

pr_{VAR:root} = gFld("", "","{VAR:baseurl}/automatweb/images/transparent.gif","")

<!-- SUB: TREE -->
	pr_{VAR:id} = insFld(pr_{VAR:parent}, gFld("{VAR:name}", "{VAR:url}","{VAR:iconurl}","{VAR:id}"));
<!-- END SUB: TREE -->
<!-- SUB: DOC -->
	pr_{VAR:id} = insDoc(pr_{VAR:parent}, gLnk("R", "{VAR:name}","{VAR:url}","{VAR:iconurl}","{VAR:id}"));
<!-- END SUB: DOC -->

foldersTree = pr_{VAR:root};
initializeDocument();
</script>
