box_handle_close()
box_handle_minimize();

function box_handle_close()
{
	$(".box .close").click(function(){
		$(this).parent().parent().parent().remove();
	});
}

function box_handle_minimize()
{
	$(".box .minimize").click(function(){
		$(this).parent().parent().next().slideToggle(100);;
	});
}

function iaw_box_settings(id)
{
	e = $("#"+id);
	e_menu = e.next();
	e_menu.css("display", "block");
	e_menu.css("top", e.offset().top+18);
	e_menu.css("left", e.offset().left-20);
}