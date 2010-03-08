<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset={VAR:charset}">
<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/aw.js"></script>
<script type="text/javascript">

var feeding_node;

var show_one_active = {VAR:only_one_level_opened};
var active_level = 1;

function add_node (nodeID)
{
	TreeInput = document.getElementById("treeinput" + "{VAR:checkbox_data_var}");
	AddedNodes = TreeInput.value;
	// ButtonImg = document.getElementById("nodeimg" + nodeID);
	// ButtonImg.src = "{VAR:baseurl}/automatweb/images/node_add_button_click.gif";

	if (AddedNodes)
	{
		AddedNodes = AddedNodes.split ("{VAR:separator}");
	}
	else
	{
		AddedNodes = new Array ();
	}

	AddedNodesCount = AddedNodes.length + 1;

	ResourceName = document.getElementById("ResourceName"+nodeID);
	if (!ResourceName.originalName)
	{
		ResourceName.originalName = ResourceName.innerHTML;
	}

	if (aw_in_array(nodeID, AddedNodes))
	{
		ResourceName.innerHTML += "," + AddedNodesCount;
	}
	else
	{
		ResourceName.innerHTML += " - " + AddedNodesCount;
	}


	AddedNodes.push (nodeID);
	AddedNodes = AddedNodes.join ("{VAR:separator}");
	TreeInput.value = AddedNodes;
	AddedResourcesDisplay = document.getElementById("AddedResourcesDisplay{VAR:tree_id}");
	ResourceTag = document.getElementById("ResourceName" + nodeID);
	Resource = ResourceTag.originalName + " - " + AddedNodesCount;
	AddedResources = AddedResourcesDisplay.innerHTML;
	AddedResources = AddedResources + "<br>" + Resource;
	AddedResourcesDisplay.innerHTML = AddedResources;
}

function toggle_children(objref,menu_level) {
	elemID = objref.getAttribute("attachedsection");
	thisElem = document.getElementById(elemID);
	data_loaded = thisElem.getAttribute("data_loaded");
	thisDisp = thisElem.style.display;
	icon = document.getElementById("icon-"+elemID);
	iconfld = document.getElementById("iconfld-"+elemID);

	if (thisDisp == 'none')
	{
		if (get_branch_func != "" && data_loaded == "false")
		{
			thisElem.innerHTML = '<span style="color: #CCC; margin-left: 20px;">loading....</span>';
			// fire treeloader
			feeding_node = elemID;
			fetch_node(elemID);
		};

		thisElem.style.display = 'block';

		if (icon)
			icon.innerHTML = tree_collapseMeHTML;
		if (iconfld.src == tree_closed_fld_icon)
			iconfld.src = tree_open_fld_icon;

		if (persist_state)
		{
			if (!aw_in_array(elemID,open_nodes))
			{
				open_nodes.push(elemID);
				set_cookie(tree_id,open_nodes.join('^'));
			};
		};

		if(show_one_active && menu_level==active_level)
		{
			close_all_nodes(1,objref);
		}
	}
	else
	{
		thisElem.style.display = 'none';
		if (icon)
			icon.innerHTML = tree_expandMeHTML;
		if (iconfld.src == tree_open_fld_icon)
			iconfld.src = tree_closed_fld_icon;

		if (persist_state)
		{
			if (aw_in_array(elemID,open_nodes))
				{
					open_nodes = aw_remove_arr_el(elemID,open_nodes);
					set_cookie(tree_id,open_nodes.join('^'));
				}
		}
	}
	return false;
}

function onload_handler(arg)
{
	if (window.event)
                el = window.event.srcElement.id.substr(1);
        else
                el = arg;
        document.getElementById(el).innerHTML = document.getElementById("f"+el).contentWindow.document.body.innerHTML;
	document.getElementById(el).setAttribute("data_loaded",true);
}

function fetch_node(node)
{
	uri = get_branch_func + parseInt(node);
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

   var attached_sections = Array();

   function close_all_nodes(level, skip)
   {
      var i;
      for(i in attached_sections[level])
      {
         elem = document.getElementById(attached_sections[level][i]+"treenode");
			elemId = elem.getAttribute('attachedsection');
         //if(document.getElementById(elemId).style.display'block')
			if(elem!=skip && document.getElementById(elemId).style.display!="none")
         {
            toggle_children(elem);
         }
      }
   }

	//i'm craving for a need of is_numeric in js
	function is_numeric(input)
	{
		var valid_chars = "0123456789.,-";

		for(i=0;i<input.length;i++)
		{
			if(valid_chars.indexOf(input.charAt(i))==-1)
			{
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

get_branch_func = '{VAR:get_branch_func}';
persist_state = '{VAR:persist_state}';
tree_id = '{VAR:tree_id}';
open_nodes = new Array({VAR:open_nodes});
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
<a href="{VAR:rooturl}" target="{VAR:target}"><img style="vertical-align: middle;" src="{VAR:icon_root}" border="0">{VAR:rootname}</a>
</div>
<!-- END SUB: HAS_ROOT -->
<!-- hästi tore oleks, kui ma saaks need folderite ikoonid kuidagi automaatselt lisada -->
<!-- SUB: TREE_NODE -->
<script language='JavaScript1.2'>
   if(attached_sections[{VAR:menu_level}]==undefined)
   {
      attached_sections[{VAR:menu_level}] = new Array();
   }
	tmp = '{VAR:id}';
	if(is_numeric(tmp))
	{
		attached_sections[{VAR:menu_level}][tmp] = tmp;
	}
</script>
<div class="nodetext"><a attachedsection="{VAR:id}" id="{VAR:id}treenode" onClick="toggle_children(this,{VAR:menu_level});return false;" href="javascript:void();"><span id="icon-{VAR:id}" class="iconcontainer"><img src="{VAR:node_image}" border="0" style="vertical-align:middle;"></span><span><img id="iconfld-{VAR:id}" src="{VAR:iconurl}" border="0" style="vertical-align:middle;"></span></a>&nbsp;<a href="{VAR:url}" target="{VAR:target}">{VAR:name}</a>
<!-- SUB: SUB_NODES -->
<div id="{VAR:id}" data_loaded="{VAR:data_loaded}" style="padding-left: 16px; display: {VAR:display}; ">
<!-- SUB: SINGLE_NODE -->
<div class="nodetext"><span class="iconcontainer"><img src="{VAR:iconurl}" border="0" style="vertical-align:middle; margin-left: 16px;"></span>&nbsp;<a target="{VAR:target}" href="{VAR:url}">{VAR:name}</a></div>
<!-- END SUB: SINGLE_NODE -->
<!-- SUB: SINGLE_NODE_BUTTON -->
<div class="nodetext"><span class="iconcontainer"><img src="{VAR:baseurl}/automatweb/images/node_add_button.gif" border="0" style="vertical-align:middle; margin-left: 16px; cursor: pointer;" onclick="add_node({VAR:id})" id="nodeimg{VAR:id}" alt="Lisa ressursile töö"></span>&nbsp;<span style="cursor: pointer;" onclick="add_node({VAR:id})" id="ResourceName{VAR:id}">{VAR:name}</span></div>
<!-- END SUB: SINGLE_NODE_BUTTON -->
<!-- END SUB: SUB_NODES -->
</div></div>
<!-- END SUB: TREE_NODE -->
<input type="hidden" name="{VAR:checkbox_data_var}" value="" id="treeinput{VAR:checkbox_data_var}">
<div id="AddedResourcesDisplay{VAR:tree_id}" class="nodetext">
<br>
</div>
