$(document).ready(function() {
	(function() {
		$('input[name^=publications]').keyup(function(){
			name = this.name.substr(0, this.name.length -6);
			$("input[name='" + name.replace('years', 'total') + "']").val($("input[name^='" + name + "']").sum());
		});
	})();
});