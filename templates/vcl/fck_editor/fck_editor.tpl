<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/fckeditor/{VAR:fck_version}/fckeditor.js"></script>
<script type="text/javascript">
<!--
var document_form = new Array();
var document_form_original = new Array();
var document_instances = new Array();
var changed = false;

/*
 * here we set change listeners
 */
function FCKeditor_OnComplete( editorInstance )
{
	if ($.browser.msie)	{
		editorInstance.EditorDocument.attachEvent( 'onkeyup', FCKeditor_OnChange ) ;
		editorInstance.EditorDocument.attachEvent( 'onkeydown', FCKeditor_OnChange ) ;
	}
	else
	{
		editorInstance.Events.AttachEvent( 'OnSelectionChange', FCKeditor_OnChange ) ;
		editorInstance.EditorDocument.addEventListener( 'keyup', FCKeditor_OnChange, true ) ;
		editorInstance.EditorDocument.addEventListener( 'keydown', FCKeditor_OnChange, true ) ;
	}
	
	document_form_original[editorInstance.Name] = editorInstance.EditorDocument.body.innerHTML;
	// we'll use those instances to get the content of FCK in unloadHandler() just after leaving page
	document_instances[editorInstance.Name] = editorInstance;
	
	function FCKeditor_OnChange()
	{
		if (!changed)
		{
			set_changed();
		}
	}
}

function FCKeditor_CreateEditor(name, version, width, height, lang)
{
	var oFCKeditor = new FCKeditor(name);
	oFCKeditor.BasePath = "{VAR:baseurl}/automatweb/js/fckeditor/"+version+"/";
	oFCKeditor.Width = width;
	oFCKeditor.Height = height;
	oFCKeditor.Config["AutoDetectLanguage"] = false;
	{VAR:config}
	oFCKeditor.ReplaceTextarea();	
}

/*
 * turns array to string
 */
function serializeArray (arr)
{
	s_out = "";
	
	for (key in arr)
	{
		s_out += encodeURIComponent(key)+"="+encodeURIComponent(arr[key])+"&";
	}
	
	s_out = s_out.substr(0, s_out.length-1);
	return s_out;
}

/*
 * if executed, content has been modified
 */
function set_changed()
{
	changed = true;
}

if ($.browser.opera && jQuery.browser.version>="9.50")
{	
	// don't really know what i need for new opera here... nothing seems to work
}
else
{
	$(window).unload( function () {
		//unloadHandler ();
	});
}

/*
 * executed after leaving page if content has changed and not saved
 */
function unloadHandler()
{
	if(changed)
	{
		var prompt = "{VAR:msg_leave}";
		if(confirm(prompt))
		{
			for (key in document_instances)
			{
				editorInstance = document_instances[key];
				document_form[editorInstance.Name] = editorInstance.GetHTML();
			}
			 $.ajax({
				type: "POST",
				url: "orb.aw",
				data: $("form").serialize()+"&"+serializeArray(document_form)+"&posted_by_js=1",
				async: false,
				success: function(msg){
				 //alert( "Data Saved: " + msg );
				},
				error: function(msg){
					//alert( "{VAR:msg_leave_error}");
				}

			});
		}
	}

}

<!-- SUB: EDITOR_FCK -->
FCKeditor_CreateEditor("{VAR:name}", "{VAR:fck_version}", "{VAR:width}", "{VAR:height}", "{VAR:lang}")
<!-- END SUB: EDITOR_FCK -->

<!-- SUB: EDITOR_ONDEMAND -->
$("#{VAR:name}").css("width", "{VAR:width}").click(function(){
	FCKeditor_CreateEditor("{VAR:name}", "{VAR:fck_version}", "{VAR:width}", "{VAR:height}", "{VAR:lang}")
});
<!-- END SUB: EDITOR_ONDEMAND -->

-->
</script>
