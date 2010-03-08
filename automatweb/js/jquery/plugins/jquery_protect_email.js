jQuery.protect_email = function ()
{
	$('span._aw_email').each(function()
	{
		$("img", this).replaceWith("@");
		m = $(this).html().split(" ");
		if ( m.length == 1 )
		{
			m[1] = m[0];
		}
		$(this).html("<a href='mailto:"+m[0]+"'>"+m[1]+"</a>");
	});
}