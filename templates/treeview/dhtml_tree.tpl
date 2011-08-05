<div id="treeDivWithID_{VAR:tree_id}">
<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/aw.js"></script>
<script type="text/javascript">

var feeding_node;

var show_one_active_{VAR:tree_num} = {VAR:only_one_level_opened};
var active_level_{VAR:tree_num} = 1;
var level_{VAR:tree_num} = {VAR:level};
load_auto_{VAR:tree_num} = {VAR:load_auto};
open_nodes_{VAR:tree_num} = new Array({VAR:open_nodes});
tree_id_{VAR:tree_num} = '{VAR:tree_id}';
from_click_{VAR:tree_num} = false;

function generic_loader() {
	// on page load
	if(/*window.onload &&*/ load_auto_{VAR:tree_num} && level_{VAR:tree_num} < open_nodes_{VAR:tree_num}.length) {
		try{ load_tree_state_1(); } catch(e) {}
		try{ load_tree_state_2(); } catch(e) {}
		try{ load_tree_state_3(); } catch(e) {}
		try{ load_tree_state_4(); } catch(e) {}
		try{ load_tree_state_5(); } catch(e) {}
		try{ load_tree_state_6(); } catch(e) {}
		try{ load_tree_state_7(); } catch(e) {}
		try{ load_tree_state_8(); } catch(e) {}
	}
}

function load_beneath_{VAR:tree_num}() {
	// on iframe load
	if(load_auto_{VAR:tree_num} && level_{VAR:tree_num} < open_nodes_{VAR:tree_num}.length) {
		load_tree_state_{VAR:tree_num}();
	}
}

function load_tree_state_{VAR:tree_num}() {
	node = false;
	if((level_{VAR:tree_num}-1) == open_nodes_{VAR:tree_num}.length) {
		load_auto_{VAR:tree_num} = false;
	}

	for(i=level_{VAR:tree_num}; i < open_nodes_{VAR:tree_num}.length; i++) {
		data = false;
		thisElem = document.getElementById(open_nodes_{VAR:tree_num}[i]);
		if(thisElem) {
			data = thisElem.getAttribute("data_loaded");
		}

		if(data == 'true' && thisElem) {
			node = open_nodes_{VAR:tree_num}[i];
			level_{VAR:tree_num} = i + 1;
			continue;
		}
		node = open_nodes_{VAR:tree_num}[i];
		level_{VAR:tree_num} = i + 1;
		set_cookie(tree_id_{VAR:tree_num} + "_level_{VAR:tree_num}", level_{VAR:tree_num});
		if(node) {
			toggle_children_{VAR:tree_num}(node,1);
		}
		break;
	}
}

function isInt(myNum) {
	var myMod = myNum % 1;
	if (myMod == 0){
		return true;
	}
	return false;
}

function in_array(needle, haystack) {
    for (var i = 0; i < haystack.length; i++) {
        if (haystack[i] == needle) {
            return true;
        }
    }
    return false;
}

function toggle_children_{VAR:tree_num}(objref,menu_level) {
	if(objref == 'javascript:void();'){
		from_click_{VAR:tree_num} = true;
		elemID = objref.getAttribute("attachedsection");
	}
	else {
		elemID = objref;
	}

	thisElem = document.getElementById(elemID);
	if (!thisElem) {
		return;
	}
	has_data = thisElem.getAttribute("has_data");
	data_loaded = thisElem.getAttribute("data_loaded");
	thisDisp = thisElem.style.display;
	icon = document.getElementById("icon-"+elemID);
	iconfld = document.getElementById("iconfld-"+elemID);
	if (thisDisp == 'none') {
		if (get_branch_func_{VAR:tree_num} != "" && data_loaded == "false" && has_data == "0") {
			thisElem.innerHTML = '<span style="color: #CCC; margin-left: 20px;">loading....</span>';
			// fire treeloader
			feeding_node = elemID;
			fetch_node_{VAR:tree_num}(elemID);
		}

		thisElem.style.display = 'block';

		if (icon)
			icon.innerHTML = tree_collapseMeHTML;
		if (iconfld.src == tree_closed_fld_icon)
			iconfld.src = tree_open_fld_icon;

		if (persist_state_{VAR:tree_num}) {
			if (!aw_in_array(elemID, open_nodes_{VAR:tree_num})) {
				open_nodes_{VAR:tree_num}.push(elemID);
				set_cookie(tree_id_{VAR:tree_num},open_nodes_{VAR:tree_num}.join('^'));
			}
		}

		if(show_one_active_{VAR:tree_num} && menu_level==active_level_{VAR:tree_num}) {
			close_all_nodes(1,objref);
		}
	}
	else {
		thisElem.style.display = 'none';
		if (icon)
			icon.innerHTML = tree_expandMeHTML;
		if (iconfld.src == tree_open_fld_icon)
			iconfld.src = tree_closed_fld_icon;

		if (persist_state_{VAR:tree_num}) {
			if (aw_in_array(elemID,open_nodes_{VAR:tree_num})) {
				open_nodes_{VAR:tree_num} = aw_remove_arr_el(elemID,open_nodes_{VAR:tree_num});
				set_cookie(tree_id_{VAR:tree_num},open_nodes_{VAR:tree_num}.join('^'));
			}
		}
	}
	return false;
}

function onload_handler(arg) {
	if (window.event) {
		el = window.event.srcElement.id.substr(1);
	}
	else {
		el = arg;
	}
	document.getElementById(el).innerHTML = document.getElementById("f"+el).contentWindow.document.body.innerHTML;
	document.getElementById(el).setAttribute("data_loaded",true);
	// if not from nodeclick checks if any nodes need to be auto opened
	if(!from_click_{VAR:tree_num}) {
		load_beneath_{VAR:tree_num}();
	}
	from_click_{VAR:tree_num} = false;
}

function fetch_node_{VAR:tree_num}(node) {
	uri = get_branch_func_{VAR:tree_num} + '&parent=' + node + '&tree_num={VAR:tree_num}&called_by_js=true&load_auto_{VAR:tree_num}=' + load_auto_{VAR:tree_num};
	var frame = document.createElement("iframe");
	frame.setAttribute("width",0);
	frame.setAttribute("height",1);
	frame.setAttribute("frameborder",0);
	frame.setAttribute("id","f"+node);
	frame.setAttribute("src",uri);
	if (frame.attachEvent)
		frame.attachEvent('onload',onload_handler);
	else
		frame.setAttribute("onload","onload_handler('" + node + "');");
	document.body.appendChild(frame);
}

var attached_sections_{VAR:tree_num} = Array();

function close_all_nodes(level, skip) {
	var i;
	for(i in attached_sections_{VAR:tree_num}[level]) {
		elem = document.getElementById(attached_sections_{VAR:tree_num}[level][i]+"treenode");
		elemId = elem.getAttribute('attachedsection');
		//if(document.getElementById(elemId).style.display'block')
		if(elem!=skip && document.getElementById(elemId).style.display!="none") {
			toggle_children_{VAR:tree_num}(elem);
		}
	}
}

//i'm craving for a need of is_numeric in js
function is_numeric(input) {
	var valid_chars = "0123456789.,-";

	for(i=0;i<input.length;i++) {
		if(valid_chars.indexOf(input.charAt(i))==-1) {
			return false;
		}
	}
	return true;
}

// would be nice to have those generated for me
// so, how do I call javascript for those
tree_expandMeHTML = '<img src="{VAR:baseurl}/automatweb/images/plusnode.gif" border="0" style="vertical-align: middle;">';
tree_collapseMeHTML = '<img src="{VAR:baseurl}/automatweb/images/minusnode.gif" border="0" style="vertical-align:middle;">';

tree_closed_fld_icon = "{VAR:baseurl}/automatweb/images/closed_folder.gif";
tree_open_fld_icon = "{VAR:baseurl}/automatweb/images/open_folder.gif";

get_branch_func_{VAR:tree_num} = '{VAR:get_branch_func}';
persist_state_{VAR:tree_num} = '{VAR:persist_state}';
open_nodes_{VAR:tree_num} = new Array({VAR:open_nodes});
tree_id_{VAR:tree_num} = '{VAR:tree_id}';

$(document).ready(function(){
	function attachTreeNodeOnClick(o){
		if($(o).attr("tagName") == "A" && typeof $(o).attr("id") != "undefined" && $(o).attr("id").substr(-8) != "treenode"){
			$(o).click(function(){
				$("a[tree_id='treeDivWithID_{VAR:tree_id}']").removeClass("nodetext_selected");
				$(o).addClass("nodetext_selected");
			}).attr("tree_id", "treeDivWithID_{VAR:tree_id}");
		}
		$(o).children().each(function(){attachTreeNodeOnClick(this);});
	}
	$("#treeDivWithID_{VAR:tree_id}").each(function(){attachTreeNodeOnClick(this);});
});
</script>
<style>
.iconcontainer {
	margin-left: 4px;
}
.nodetext {
	color: black;
	font-family: Arial,Helvetica,sans-serif;
	font-size: 11px;
	text-decoration: none;
	vertical-align: middle;
	white-space: nowrap;
	font-weight: normal;
}

a.nodetext_selected {
	color: black;
	font-weight: bold;
}

.nodetextbuttonlike {
	color: black;
	font-family: Arial,Helvetica,sans-serif;
	font-size: 12px;
	text-decoration: none;
	vertical-align: middle;
}

.nodetext a {
	color: black;
}
</style>
<!-- SUB: HAS_ROOT -->
<div class="nodetext">
<img style="vertical-align: middle;" src="{VAR:icon_root}" border="0"> <a href="{VAR:rooturl}" target="{VAR:target}">{VAR:rootname}</a>
</div>
<!-- END SUB: HAS_ROOT -->
<!-- hästi tore oleks, kui ma saaks need folderite ikoonid kuidagi automaatselt lisada -->
<!-- SUB: TREE_NODE -->
<script language="text/javascript">
if(attached_sections_{VAR:tree_num}[{VAR:menu_level}]==undefined) {
	attached_sections_{VAR:tree_num}[{VAR:menu_level}] = new Array();
}
tmp = '{VAR:id}';
if(is_numeric(tmp)) {
	attached_sections_{VAR:tree_num}[{VAR:menu_level}][tmp] = tmp;
}
</script>
<div style="width: 250px">
<div class="nodetext"><a attachedsection="{VAR:id}" id="{VAR:id}treenode" onClick="toggle_children_{VAR:tree_num}(this,{VAR:menu_level});return false;" href="javascript:void();" alt="{VAR:alt}" title="{VAR:alt}"><span id="icon-{VAR:id}" class="iconcontainer"><img src="{VAR:node_image}" border="0" style="vertical-align:middle;"></span><span><img id="iconfld-{VAR:id}" src="{VAR:iconurl}" border="0" style="vertical-align:middle;"></span></a>&nbsp;<a href="{VAR:url}" target="{VAR:target}" {VAR:onClick} alt="{VAR:alt}" title="{VAR:alt}">{VAR:name}</a>
<!-- SUB: SUB_NODES -->
<div id="{VAR:id}" has_data="{VAR:has_data}" data_loaded="{VAR:data_loaded}" style="padding-left: 16px; display: {VAR:display}; ">
<!-- SUB: SINGLE_NODE -->
<div class="nodetext"><span class="iconcontainer"><img src="{VAR:iconurl}" border="0" style="vertical-align:middle; margin-left: 16px;"></span>&nbsp;<a target="{VAR:target}" href="{VAR:url}" {VAR:onClick} alt="{VAR:alt}" title="{VAR:alt}">{VAR:name}</a></div>
<!-- END SUB: SINGLE_NODE -->
</div>
<!-- END SUB: SUB_NODES -->
</div></div>
<!-- END SUB: TREE_NODE -->
