<!-- SUB: writer -->
<script type="text/javascript">
// default to the first area on page
var rte_styles = "{VAR:rte_styles}";
var sel_el = "{VAR:name}_edit";
var rte_list = Array();
var mozbr = browser.isGecko ? "<br />" : "";
function write_editor(el_name,width,height)
{
	val = document.forms['changeform'].elements[el_name].value;
        if (browser.isIE5up || browser.has_midas)
	{
                write_rte(el_name,width*10,height*10);
        }
        else
	{
                //other browser
                write_default(el_name,width,height,val)
        }
};

function write_rte(el_name,width,height)
{
	realname = el_name + '_edit';
	document.writeln('<iframe onFocus="sel_el=\''+realname+'\';" class="rtebox" id="'+realname+'" width="'+width+'px" height="'+height+'px" frameborder="1"></iframe>');
	document.writeln('<script>setTimeout("enable_design_mode(\''+el_name+'\')",10); <' + '/script>');
	
	val = document.forms['changeform'].elements[el_name].value;
	realvictim = document.getElementById(realname);
	
        if (browser.isIE5up) {
		apply_editor_style(frames(realname).document,val,realvictim);
        }
        else
	{
		apply_editor_style(realvictim.contentWindow.document,val,realvictim);
        }
	
};

function write_default(el_name,width,height,val)
{
	document.writeln('<textarea name="' + el_name + '" id="' + el_name + '" cols="'+width+'" rows="'+height+'">'+val+'</textarea>');
};

function activate_editor(frm)
{
	sel_el = frm.target.aw_owner_frame;
}

function deactivate_editor(frm)
{

}

// Setting document.designMode must NOT be done in the script section of the head. We suggest the onLoad
// function for the body where the iframe is contained.
//	 Mozilla docs

function enable_design_mode(frm)
{
	val = document.forms['changeform'].elements[frm].value;

	// iframe väärtustamisele on tegelikult veel 1 alternatiiv .. anda iframele lihtsalt
	// source .. ja siis ta muutub ju automaatselt muudetavaks contentEditable seadmisega
	// see peaks pealegi oluliselt veakindlam olema .. kuigi ehk muudab mõnda asja
	// aeglasemaks.

	// I like that approach a LOT more than the current one with hidden form elements
	// and what else


	// but, right now I need to solve the saving problem, so that this rte can actually
	// start being useful

	victim = frm+'_edit';

        if (browser.isIE5up) {
		realvictim = frames(victim).document;
                frames(victim).document.designMode = "On";

		tgt_frame = frames(victim).document;

		//frames(victim).document.aw_owner_frame = victim;
        }
        else {
		realvictim = document.getElementById(victim);
                realvictim.contentDocument.designMode = "on"

		realvictim.contentWindow.document.execCommand("useCSS",false,true);
		tgt_frame = realvictim.contentWindow.document;

                //realvictim.contentWindow.document.aw_owner_frame = victim;
                realvictim.contentWindow.document.addEventListener("focus",activate_editor,true);
        }

	tgt_frame.aw_owner_frame = victim;
	awlib_addevent(tgt_frame,"keypress",function (event) {return kb_handler(event)});

};

function apply_editor_style(doc,val,iframe)
{
	doc.writeln("<html><head>");
	doc.writeln("<style>");
	doc.writeln(rte_styles);
	doc.writeln("</style></head>");
	//doc.writeln("<body style='border: 1px; margin: 1px;'>");
	doc.write("<body class='text'>");
	doc.write(val);
	doc.writeln("</body></html>");
	doc.close();
};

function kb_handler(evt)
{
	var keyEvent = (evt.type == "keydown") || (evt.type == "keypress");
	if (keyEvent && evt.ctrlKey)
	{	
		var key = String.fromCharCode(browser.isIE5up ? evt.keyCode : evt.charCode).toLowerCase();
		cmd = '';
		value = '';
		switch (key)
		{
			case 'b': cmd = "bold"; break;
			case 'i': cmd = "italic"; break;
			case 'u': cmd = "underline"; break;
		};

		if (cmd) {
			// execute simple command
			document.getElementById(sel_el).contentWindow.document.execCommand(cmd, false, true);
			//realvictim.contentWindow.document.execCommand("useCSS",false,true);
			if (browser.isIE5up)
			{
				evt.cancelBubble = true;
				evt.returnValue = false;
			}
			else
			{
				evt.preventDefault();
				evt.stopPropagation();
			}
		}
	};
}

function format_selection(huh)
{
	option = "";

	victim = document.getElementById(sel_el).contentWindow;

	victim.focus();
	victim.document.execCommand(huh, false, option);
	victim.focus();

}

function insertNodeAtSelection(toBeInserted)
{
	var sel = getSelection();
	var range = createRange(sel);
	// remove the current selection
	sel.removeAllRanges();
	range.deleteContents();
	var node = range.startContainer;
	var pos = range.startOffset;
	range = createRange();
	switch (node.nodeType) {
	    case 3: // Node.TEXT_NODE
		// we have to split it at the caret position.
		if (toBeInserted.nodeType == 3) {
			// do optimized insertion
			node.insertData(pos, toBeInserted.data);
			range.setEnd(node, pos + toBeInserted.length);
			range.setStart(node, pos + toBeInserted.length);
		} else {
			node = node.splitText(pos);
			node.parentNode.insertBefore(toBeInserted, node);
			range.setStart(node, 0);
			range.setEnd(node, 0);
		}
		break;
	    case 1: // Node.ELEMENT_NODE
		node = node.childNodes[pos];
		node.parentNode.insertBefore(toBeInserted, node);
		range.setStart(node, 0);
		range.setEnd(node, 0);
		break;
	}
	sel.addRange(range);
};

function replaceHTML(html)
{
	var sel = getSelection();
	var range = createRange(sel);
        if (browser.isIE5up) {
		range.pasteHTML(html);
	} else {
		// construct a new document fragment with the given HTML
		victim = document.getElementById(sel_el).contentWindow;
		var fragment = victim.document.createDocumentFragment();
		var div = victim.document.createElement("div");
		div.innerHTML = html;
		while (div.firstChild) {
			// the following call also removes the node from div
			fragment.appendChild(div.firstChild);
		}
		
		// this also removes the selection
		var node = insertNodeAtSelection(fragment);

	}
};

function insertHTML(html) {
	var victim = document.getElementById(sel_el).contentWindow;
	victim.focus();
	if (browser.isIE5up) {
		var range = victim.document.selection.createRange();
		range.pasteHTML(html);
		range.collapse(false);
		range.select();
	} else {
		victim.document.execCommand('insertHTML', false, html);
	}
}

// now I need a list of all textareas
function clearstyles()
{
	if (!confirm('Tühistada kõik stiilid?'))
	{
		return;
	};
	for (i = 0; i < rte_list.length; i++)
	{
		el = document.getElementById(rte_list[i] + "_edit");
		// get old innerHTML
		old = el.contentWindow.document.body.innerHTML;
		//alert(old);
		// nuke span tags
		old2 = old.replace(/<span.+?>|<\/span>|<font.+?>|<\/font>/gi,"");
		//old2 = old2.replace(/<p.+?>/gi,"<p>");
		old2 = old2.replace(/class=".*?"/gi,"");
		old2 = old2.replace(/style=".*?"/gi,"");
		old2 = old2.replace(/<!--.+?-->/gi,"");
		old2 = old2.replace(/<o:p>/gi,"");
		old2 = old2.replace(/<\/o:p>/gi,"");
		old2 = old2.replace(/(&nbsp;)+/gi,"&nbsp;");

		//alert('clearing styles from ' + sel_el);
		// put innerHTML back
		el.contentWindow.document.body.innerHTML = old2;
	};
	//alert(old2);
}

// that's the place I need javascript selection functions in
function surroundHTML(startTag, endTag)
{
	var html = getSelectedHTML();
	// the following also deletes the selection
	//this.insertHTML(startTag + html + endTag);
	this.replaceHTML(startTag + html + endTag);
};

function colortext(color)
{
	start = "<span style='color:" + color + "'>";
	end = "</span>";
	surroundHTML(start,end);
};

/// Retrieve the selected block
function getSelectedHTML()
{
	var sel = getSelection();
	var range = createRange(sel);
	var existing = null;
        if (browser.isIE5up) {
		existing = range.htmlText;
	} else {
		victim = document.getElementById(sel_el).contentWindow;
		var div = victim.document.createElement("div");
		div.appendChild( range.cloneContents() );
		existing = div.innerHTML;
	}
	return existing;
};


function getSelection()
{
	victim = document.getElementById(sel_el).contentWindow;
        if (browser.isIE5up) {
		//return this._doc.selection;
		// is this right?
		return victim.document.selection;
	} else {
		return victim.getSelection();
	}
};

function createRange(sel)
{ 
        if (browser.isIE5up) {
		return sel.createRange();
	} else {
		victim = document.getElementById(sel_el).contentWindow;
		victim.focus();
		if (sel) {
			return sel.getRangeAt(0);
		} else {
			// ??
			return victim.document.createRange();
		}
	}
};

function table_dialog()
{
	victim = document.getElementById(sel_el).contentWindow;
	window.open("/automatweb/orb.aw?class=rte&action=table_dialog","insert_table","width=360,height=180");

};

function link_dialog()
{
	victim = document.getElementById(sel_el).contentWindow;
	window.open("/automatweb/orb.aw?class=rte&action=link_dialog","insert_table","width=360,height=180");
};

function get_closest_tag(tagName)
{
        var editor = sel_el;
        var ancestors = get_all_ancestors(sel_el);
        var ret = null;
        tagName = ("" + tagName).toLowerCase();
        for (var i in ancestors) {
                var el = ancestors[i];
                if (el.tagName.toLowerCase() == tagName) {
                        ret = el;
                        break;
                }
        }
        return ret;
};

function insert_row()
{
	// nii, mul on vaja lähimat tr tagi
	var sel = getSelection();
	var tr = get_closest_tag("tr");
	if (tr)
	{
		var otr = tr.cloneNode(true);
		clear_row(otr);
		tr.parentNode.insertBefore(otr,tr.nextSibling);
	}
	 document.getElementById(sel_el).contentWindow.focus();


};

function delete_row()
{
	document.getElementById(sel_el).contentWindow.focus();
	var tr = get_closest_tag("tr");
	if (tr) {
		var par = tr.parentNode;
		if (par.rows.length > 1) {
			// set the caret first to a position that doesn't
			// disappear.
			select_next_node(tr);
			par.removeChild(tr);
		}
	}
	document.getElementById(sel_el).contentWindow.focus();
}

function insert_column()
{
	document.getElementById(sel_el).contentWindow.focus();
	var td = get_closest_tag("td");
	if (td) {
	
		var rows = td.parentNode.parentNode.rows;
		var index = td.cellIndex;
		for (var i = rows.length; --i >= 0;) {
			var tr = rows[i];
			var ref = tr.cells[index + 1];
			victim = document.getElementById(sel_el).contentWindow;
			var otd = victim.document.createElement("td");
			otd.setAttribute("class","text");
			otd.innerHTML = mozbr;
			if (!ref)
			{
				ref = null;
			};
			//if (ref)
			//{
				tr.insertBefore(otd, ref);
			//}
			//else
			//{
			//	tr.insertBefore(otd);
			//};

		}
	}
	 document.getElementById(sel_el).contentWindow.focus();
}

function delete_column()
{
	document.getElementById(sel_el).contentWindow.focus();
	var td = get_closest_tag("td");
	if (td) {
		var index = td.cellIndex;
		if (td.parentNode.cells.length > 1) {
			// set the caret first to a position that doesn't disappear
			select_next_node(td);
			var rows = td.parentNode.parentNode.rows;
			for (var i = rows.length; --i >= 0;) {
				var tr = rows[i];
				tr.removeChild(tr.cells[index]);
			}
		}
	}
	document.getElementById(sel_el).contentWindow.focus();
}

function get_all_ancestors(el)
{
        //var p = sel_el.getParentElement();
        var p = get_parent_element();
        var a = [];
        while (p && (p.nodeType == 1) && (p.tagName.toLowerCase() != 'body')) {
                a.push(p);
                p = p.parentNode;
        }
        a.push(document.getElementById(sel_el).contentWindow.document.body);
        return a;
};

function get_parent_element()
{
        var sel = getSelection();
        var range = createRange(sel);
        if (browser.isIE5up) {
                switch (sel.type) {
                    case "Text":
                    case "None":
                        // It seems that even for selection of type "None",
                        // there _is_ a parent element and it's value is not
                        // only correct, but very important to us.  MSIE is
                        // certainly the buggiest browser in the world and I
                        // wonder, God, how can Earth stand it?
                        return range.parentElement();
                    case "Control":
                        return range.item(0);
                    default:
                        return this._doc.body;
                }
        } else try {
                var p = range.commonAncestorContainer;
                if (!range.collapsed && range.startContainer == range.endContainer &&
                    range.startOffset - range.endOffset <= 1 && range.startContainer.hasChildNodes())
                        p = range.startContainer.childNodes[range.startOffset];
                /*
                alert(range.startContainer + ":" + range.startOffset + "\n" +
                      range.endContainer + ":" + range.endOffset);
                */
                while (p.nodeType == 3) {
                        p = p.parentNode;
                }
                return p;
        } catch (e) {
                return null;
        }
};

function select_next_node(el)
{
	var node = el.nextSibling;
        while (node && node.nodeType != 1) {
       	 node = node.nextSibling;
        }
                if (!node) {
                        node = el.previousSibling;
                        while (node && node.nodeType != 1) {
                                node = node.previousSibling;
                        }
                }
                if (!node) {
                        node = el.parentNode;
                }
                select_node_contents(node);
};

function select_node_contents(node)
{
        var range;
        var collapsed = (typeof pos != "undefined");
        if (browser.isIE5up) {
        	range = document.getElementById(sel_el).contentWindow.document.body.createTextRange();
                range.moveToElementText(node);
                (collapsed) && range.collapse(pos);
                range.select();
        } else {
                var sel = getSelection();
                range = createRange();
                range.selectNodeContents(node);
                (collapsed) && range.collapse(pos);
                sel.removeAllRanges();
                sel.addRange(range);
        }
};

function clear_row(tr)
{
	var tds = tr.getElementsByTagName("td");
	for (var i = tds.length; --i >= 0;) {
		var td = tds[i];
		td.rowSpan = 1;
		td.innerHTML = mozbr;
	}
}

function split_row(td)
{
	if (!td)
	{
		var td = get_closest_tag("td");
	}
	if (td)
	{
		var n = parseInt("" + td.rowSpan);
		var nc = parseInt("" + td.colSpan);
		td.rowSpan = 1;
		tr = td.parentNode;
		var itr = tr.rowIndex;
		var trs = tr.parentNode.rows;
		var index = td.cellIndex;
		while (--n > 0) {
			tr = trs[++itr];
			victim = document.getElementById(sel_el).contentWindow;
			var otd = victim.document.createElement("td");
			otd.colSpan = td.colSpan;
			otd.innerHTML = mozbr;
			tr.insertBefore(otd, tr.cells[index]);
		}

	};
}

function split_col(td)
{
	if (!td)
	{
		var td = get_closest_tag("td");
	};
	if (td)
	{
		var nc = parseInt("" + td.colSpan);
		td.colSpan = 1;
		tr = td.parentNode;
		var ref = td.nextSibling;
		while (--nc > 0) {
			victim = document.getElementById(sel_el).contentWindow;
			var otd = victim.document.createElement("td");
			otd.rowSpan = td.rowSpan;
			otd.innerHTML = mozbr;
			tr.insertBefore(otd, ref);
		}
	};
}

function split_cell()
{
	var td = get_closest_tag("td");
	var nc = parseInt("" + td.colSpan);
	split_col(td);
	var items = td.parentNode.cells;
	var index = td.cellIndex;
	while (nc-- > 0) {
		split_row(items[index++]);
	}



}
</script>

<style>
.rtebox {
	border: 1px solid gray;
	margin: 1px;
	padding: 1px;
	background: #fff;
}
</style>

<!-- END SUB: writer -->


<!-- SUB: toolbar -->
<div id="rte_toolbar" style="visibility: hidden; height:1; width: 0; overflow:hidden">
</div>
<!-- END SUB: toolbar -->

<!-- SUB: field -->
<input type="hidden" name="{VAR:name}" value="{VAR:value}">
<script type="text/javascript">
write_editor('{VAR:name}','{VAR:width}','{VAR:height}');
rte_list.push("{VAR:name}");
</script>
<!-- END SUB: field -->
