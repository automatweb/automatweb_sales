$(document).ready(function(){
	$.ajax({
		type: "GET",
		url: "orb.aw?class=site_copy_client&action=check_site",
		data: "url="+escape($("#bug_url").val()),
		success: function(json)
		{
			data = eval("("+json+")");
			msg = data["msg"];

			var html = "";
			switch(msg)
			{
				case 0:
				case "0":
					html = "URL on sisestamata!";
					break;

				case 1:
				case "1":
					html = "Saidist ei ole veel koopiat. Tellimiseks kliki siia!";
					$("#site_copy").click(function(){
						$.get("orb.aw?orb.aw?class=site_copy_client&action=add_site", "url="+escape($("#bug_url").val()), function(){
							$("#site_copy").html("Saidi koopia on tellitud, aga pole veel valmis.");
							$("#site_copy").css("color", "#AAAA00");
						});
					});
					break;

				case 2:
				case "2":
					html = "Saidi koopia on tellitud, aga pole veel valmis.";
					$("#site_copy").css("color", "#AAAA00");
					break;

				case 3:
				case "3":
					html = "Saidi koopia on tellitud ja valmis.<br /><a href=\""+data["url"]+"\">"+data["url"]+"</a><br /><a href=\""+data["url_cvs"]+"\">"+data["url_cvs"]+"</a>";
					$("#site_copy").css("color", "#00CC00");
					break;
				
				default:
					html = "Ma olen natuke segaduses. Sain vastuseks '"+msg+"'.";
					break;
			}
			$("#site_copy").html(html);
		}
	});
});