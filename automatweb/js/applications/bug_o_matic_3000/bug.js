(function($) {
		
$.extend({
	bug_respond: {
		remoteCount: 0 // TODO in Tabs 3 this is going to be more cleanly in one single namespace
	}
}); 
		
$.fn.bug_respond = function(){
	return this.each(function(){
		$(this).click(function(){
			i_bug_comm_id = this.id.split("_")[2];
			s_bug_comment = strip_bug_info($("#bug_comm_content_"+i_bug_comm_id).html());
			s_bug_comment_new = "\n\n"+smart_split_string_to_lines(s_bug_comment);
			// insert the value
			offset = $("#bug_content_comm").val(s_bug_comment_new).offset();
			$("#bug_content_comm").focus();
			$("#bug_content_comm").each(function(){
				this.focus();
				setSelRange(this,0,0);
			});
			scroll(0,offset.top )			
		});
    });
};

function smart_split_string_to_lines(s_bug_comment)
{
	a_bug_comment = s_bug_comment.split(" ");
	if (a_bug_comment.length>0)
	{
		s_bug_comment_new = "\n\n> ";
	}
	else
	{		
		s_bug_comment_new = "";
	}
	max_chars_on_line = 85;
	line_length = 0;
	a_bug_comment_length = a_bug_comment.length
	for(key=0;key<a_bug_comment_length;key++)
	{
		line_length += a_bug_comment[key].length;
		if (line_length<max_chars_on_line)
		{
			s_bug_comment_new += a_bug_comment[key] + " ";
		}
		else
		{
			s_bug_comment_new += "\n> ";
			line_length = 0;
			key--;
		}
	}
	
	s_bug_comment_new = trim(s_bug_comment_new);

	a_bug_comment = s_bug_comment_new.split("\n");
	a_bug_comment_length = a_bug_comment.length;
	s_bug_comment_new = "";
	for(key=0;key<a_bug_comment_length;key++)
	{
		if (a_bug_comment[key].indexOf('>')===0)
		{
			s_bug_comment_new += a_bug_comment[key]+"\n";
		}
		else
		{
			 s_bug_comment_new += "> " + a_bug_comment[key]+"\n";
		}
	}

	return s_bug_comment_new;
}

function strip_bug_info(s_bug)
{
	if($.browser.msie)
	{
		a_bug = s_bug.split("<BR>");
	}
	else
	{
		a_bug = s_bug.split("\n");
	}
	out = "";
	for (key in a_bug)
	{
		if (a_bug[key].indexOf('Tegelik tundide arv')===0 ||
		   a_bug[key].indexOf('Prognoositud tundide arv')===0 ||
		   a_bug[key].indexOf('Tunde kliendile arv')===0 ||
		   a_bug[key].indexOf('Isiku prognoositud tundide arv')===0||
		   a_bug[key].indexOf('Kellele muudeti')===0
		   )
		{
			continue
		}
		out += a_bug[key] + "\n";
	}
	out = strip_tags(out);
	return out;
}

function strip_tags(str, allowed_tags) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Luke Godfrey
    // +      input by: Pul
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // +      input by: Alex
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Marc Palau
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: strip_tags('<p>Kevin</p> <br /><b>van</b> <i>Zonneveld</i>', '<i><b>');
    // *     returns 1: 'Kevin <b>van</b> <i>Zonneveld</i>'
    // *     example 2: strip_tags('<p>Kevin <img src="someimage.png" onmouseover="someFunction()">van <i>Zonneveld</i></p>', '<p>');
    // *     returns 2: '<p>Kevin van Zonneveld</p>'
    // *     example 3: strip_tags("<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>", "<a>");
    // *     returns 3: '<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>'
 
    var key = '', tag = '', allowed = false;
    var matches = allowed_array = [];
 
    var replacer = function(search, replace, str) {
        return str.split(search).join(replace);
    };
 
    // Build allowes tags associative array
    if (allowed_tags) {
        allowed_array = allowed_tags.match(/([a-zA-Z]+)/gi);
    }
  
    str += '';
 
    // Match tags
    matches = str.match(/(<\/?[^>]+>)/gi);
 
    // Go through all HTML tags
    for (key in matches) {
        if (isNaN(key)) {
            // IE7 Hack
            continue;
        }
 
        // Save HTML tag
        html = matches[key].toString();
 
        // Is tag not in allowed list? Remove from str!
        allowed = false;
 
        // Go through all allowed tags
        for (k in allowed_array) {
            // Init
            allowed_tag = allowed_array[k];
            i = -1;
 
            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+'>');}
            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+' ');}
            if (i != 0) { i = html.toLowerCase().indexOf('</'+allowed_tag)   ;}
 
            // Determine
            if (i == 0) {
                allowed = true;
                break;
            }
        }
 
        if (!allowed) {
            str = replacer(html, "", str); // Custom replace. No regexing
        }
    }
 
    return str;
}

function trim (str, charlist) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: mdsjack (http://www.mdsjack.bo.it)
    // +   improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
    // +      input by: Erkekjetter
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: DxGx
    // +   improved by: Steven Levithan (http://blog.stevenlevithan.com)
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // *     example 1: trim('    Kevin van Zonneveld    ');
    // *     returns 1: 'Kevin van Zonneveld'
    // *     example 2: trim('Hello World', 'Hdle');
    // *     returns 2: 'o Wor'
    // *     example 3: trim(16, 1);
    // *     returns 3: 6
 
    var whitespace, l = 0, i = 0;
    str += '';
    
    if (!charlist) {
        // default list
        whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    } else {
        // preg_quote custom list
        charlist += '';
        whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
    }
    
    l = str.length;
    for (i = 0; i < l; i++) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(i);
            break;
        }
    }
    
    l = str.length;
    for (i = l - 1; i >= 0; i--) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(0, i + 1);
            break;
        }
    }
    
    return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}

function setSelRange(inputEl, selStart, selEnd) { 
	if (inputEl.setSelectionRange) { 
		inputEl.focus(); 
		inputEl.setSelectionRange(selStart, selEnd); 
	} else if (inputEl.createTextRange) { 
		var range = inputEl.createTextRange(); 
		range.collapse(true); 
		range.moveEnd('character', selEnd); 
		range.moveStart('character', selStart); 
		range.select(); 
	}
}

})(jQuery);

$(".bug_respond").bug_respond();
