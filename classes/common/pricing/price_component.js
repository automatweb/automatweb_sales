$("document").ready(function(){
	$("#value_ratio").change(function(){
		$("#value_absolute").val("");
		$("input[type=hidden][name=is_ratio]").val(1);
	});
	$("#value_absolute").change(function(){
		$("#value_ratio").val("");
		$("input[type=hidden][name=is_ratio]").val(0);
	});
});