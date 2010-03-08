function getcoords()
{
	var form = document.forms.changeform
	var elname = "sel"
	var len = form.elements.length;
	var adrbox, num, cdata, tmp
	for(i = 0; i < len; i++)
	{
		if (form.elements[i].name.indexOf(elname) != -1)
		{
			if(form.elements[i].checked)
			{
				tmp = form.elements[i].name.split("[")
				tmp = tmp[1].split("]")
				num = tmp[0]
				adrbox = document.getElementById("adr"+num)
				address = adrbox.innerHTML
				data = "class=geoinfo_manager&action=get_address&id={VAR:obj_id}&adr="+address+"&num="+num
				 $.ajax({
					type: "GET",
					url: "{VAR:query_url}",
					data: data,
					success: function(msg){
						cdata = msg.split(",")
						if(cdata[0] == "200")
						{
							var xc = aw_get_el("coord_x-"+cdata[4],0)
							var yc = aw_get_el("coord_y-"+cdata[4],0)
							xc.value = cdata[2]
							yc.value = cdata[3]
						}
					}
				});
			}
		}
	}
}