form = document.forms.changeform
var len = form.elements.length
var users = 0
var elname = "sel"
function check_delete()
{
	for(i = 0; i < len; i++)
	{
		if (form.elements[i].name.indexOf(elname) != -1)
		{
			if(form.elements[i].checked)
			{
				tmp = form.elements[i].name.split("[")
				tmp = tmp[1].split("]")
				num = tmp[0]
				if(oids[num])
				{
					users += 1
				}
			}
		}
	}
	if(users>0)
	{
		var confm = confirm("NB! kasutaja kustutamisel ei ole v&otilde;imalik seda taastada, vaid tuleb luua uus, samanimeline kasutaja.")
		if(confm)
		{
			submit_changeform("delete_bms")
		}
	}
	else
	{
		submit_changeform("delete_bms")
	}
}


// remove input elements from search if not set
if ($.browser.msie)
{
	$("form select").each(function(){
		if( !$(this).attr("selectedIndex") )
		{
			$(this).attr( "name_tmp", $(this).attr("name") ); 
			$(this).attr( "name", "");
		}
		
	})
	
	$("form select").change(function(){
		new_name = $(this).attr( "name_tmp")
		$(this).attr( "name", new_name); 
	})
}


//function check_generic_loader ()
//{
//}

function select_reltypes(el)
{
	if (el.selectedIndex)
	{
		clid = el.options[el.selectedIndex].value;

		$.ajax({
			type: "POST",
			url: "orb.aw?class=aw_object_search&action=get_relation_types",
			data: "s_clid="+clid,
			success: function(msg){
				$("#s_rel_type1").html(msg);
			}
		});
	}
}
