/**
 * @version 1
 * Example: alert ($.gup("class"));
 */
(function($){
	$.extend({
		gup: function(param){
			return new $.gup(param);
		},
		gup: function(param){
			param = param.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
			var regexS = "[\\?&]"+param+"=([^&#]*)";
			var regex = new RegExp( regexS );
			var results = regex.exec( window.location.href );
			if( results == null )
			{
				return "";
			}
			else
			{
				return results[1];
			}
		},
		gpn: function(){
			return new $.gpn();
		},
		gpn: function(){
			var params = new Array( );
			var regex = /[?&]([^=]+)=/g;
			while((results=regex.exec(window.location.href)) != null)
			{
				params.push(results[1]);
			}
			return params;
		},
		gpnv_as_obj: function(){
			return new $.gpnv_as_obj();
		},
		gpnv_as_obj: function(){
			var oString = {};
			var values = new Array();
			values = $.gpn();
			for(var i=0; i < values.length; i++){
				oString[values[i]] = $.gup(values[i]);
			}
			return oString;
		}
	});
})(jQuery);
