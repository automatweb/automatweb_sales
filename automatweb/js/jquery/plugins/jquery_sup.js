/**
 * @version 1
 * Examples: 
 	// 1 - one param to change of current url
	url = $.sup("class", "blah");
	
	// 2 - one param to change & url as third param
	url = $.sup("class", "blah", "http://neti.ee/automatweb/orb.aw?class=admin_if&action=change&group=o&id=177");

 	// 3 - lots of params to change (notice the '"class" : "test"'. class is special word in ie so it needs to be in quotes)
	url = $.sup({
		url : "http://neti.ee/automatweb/orb.aw?class=admin_if&action=change&group=o&id=177",
		params : {
			"class" : "test",
			action : "blah",
			fak : "blah",
			fak2 : "blah2",
		}
	})
 */
(function($){
    $.extend({
        sup: function(param, value, url){
            return new $.sup(param, value, url);
        },
        sup: function(param, value, url){
			if (typeof(url) == "undefined")
			{
				url = document.location+"";
			}
			if (typeof(param)=="object")
			{
				if (typeof(param.url) == "undefined")
				{
					url = document.location+"";
				}
				else
				{
					url = param.url;
				}
				for(key in param.params)
				{
					url = this.replace_param(key, param.params[key], url);
				};
			}
			else
			{
				url = this.replace_param(param, value, url);
			}
			return url;
        },
		replace_param: function(param, value, url)
		{
			param = param.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
			var regexS = "([\\?&]"+param+"=)([^&#]*)";
			var regex = new RegExp( regexS );
			var results = regex.exec( url );
			if( results == null )
			{
				if ( url.indexOf( "?", url ) > 0)
				{
					url += "&"+param+"="+value;
				}
				else
				{
					url += "?"+param+"="+value;
				}
			}
		  	else
			{
				url = url.replace(results[0], results[1]+value);
			}
			return url;
		}
    });
})(jQuery);
