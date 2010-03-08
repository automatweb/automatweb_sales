var sfield1
var sfield2
var count = 0
var agurl = "{VAR:agurl}"
function get_mail_field(field, field2)
{
	sfield1 = field
	sfield2 = field2
	setTimeout("get_field()", 500)
}
function get_field()
{
	el2 = aw_get_el(sfield2)
	get_agurl = agurl + "&find="+sfield1+"&val="+el2.value
	el = aw_get_el(sfield1)
	data = aw_get_url_contents(get_agurl)
	tmp = data.split("||")
	if(tmp[0] == "ok")
	{
		el.value = tmp[1]
	}
}