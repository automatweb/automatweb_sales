{LINK:menu.tpl}
<script language=javascript>
function popup(id)
{
	self.name = "Image preview";
	var wprops = "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=300,height=400";
	openwindow = window.open("/showimg.{VAR:ext}?id="+id,"remote",wprops);
	openwindow.location.reload();
}
</script>
<div><font face='tahoma, arial, geneva, helvetica' size="4"><b>{VAR:LC_FORMS_NAME}: {VAR:form_name}</b><br></font></div><img src="/images/transa.gif" width='1' height='10' border='0' vspace='0' hspace='0'><br>
<font size='2'><b>{VAR:LC_FORMS_COMMENT}:</b><br>
{VAR:form_comment}<br></font>
<br>
{LINK:show.tpl}
