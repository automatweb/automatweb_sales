var _aw_fck_selected_image_oid = false;
var _aw_fck_selected_image_alias = false;

var InsertAWImageCommand=function(){};
InsertAWImageCommand.Name='ImageUpload';
InsertAWImageCommand.prototype.Execute=function(){}
InsertAWImageCommand.GetState=function() { return FCK_TRISTATE_OFF; }
InsertAWImageCommand.Execute=function() {
	if ( _fck_awdoc_exists() )
	{
		window.open(FCKConfig.AWBaseurl+'/automatweb/orb.aw?class=image_manager&doc='+escape(window.parent.location.href), 
			'InsertAWImageCommand', 'width=800,height=600,scrollbars=no,scrolling=no,location=no,toolbar=no');
	}
	else
	{
		alert (FCKLang.ErrorNotSaved);
	}
}
FCKCommands.RegisterCommand('awimageupload', InsertAWImageCommand ); 
var oawimageuploadItem = new FCKToolbarButton('awimageupload', FCKLang.AWUploadImage);
if ( _fck_awdoc_exists() )
{
	oawimageuploadItem.IconPath = FCKPlugins.Items['awimageupload'].Path + 'image.gif' ;
}
else
{
	oawimageuploadItem.IconPath = FCKPlugins.Items['awimageupload'].Path + 'button_disabled.gif' ;
}

FCKToolbarItems.RegisterItem( 'awimageupload', oawimageuploadItem ) ;



var InsertAWImageCommand=function(){};
InsertAWImageCommand.Name='ImageChange';
InsertAWImageCommand.prototype.Execute=function(){}
InsertAWImageCommand.GetState=function() { return FCK_TRISTATE_OFF; }
InsertAWImageCommand.Execute=function() {
	oid =  _aw_fck_selected_image_oid;
	window.open(FCKConfig.AWBaseurl+'/automatweb/orb.aw?class=image_manager&doc='+escape(window.parent.location.href)+"&in_popup=1&image_id="+oid,
		'InsertAWImageCommand', 'width=800,height=500,scrollbars=no,scrolling=no,location=no,toolbar=no');
}
FCKCommands.RegisterCommand('awimagechange', InsertAWImageCommand ); 


var FloatImage2LeftCommand=function(){};
FloatImage2LeftCommand.Name='Float2Left';
FloatImage2LeftCommand.prototype.Execute=function(){}
FloatImage2LeftCommand.GetState=function() { return FCK_TRISTATE_OFF; }
FloatImage2LeftCommand.Execute=function() {
	FCKAWImagePlaceholders.Add(FCK, _aw_fck_selected_image_alias, "v" )
}
FCKCommands.RegisterCommand('awimagechange_float_left', FloatImage2LeftCommand ); 


// float to right command
var FloatImage2RightCommand=function(){};
FloatImage2RightCommand.Name='Float2Right';
FloatImage2RightCommand.prototype.Execute=function(){}
FloatImage2RightCommand.GetState=function() { return FCK_TRISTATE_OFF; }
FloatImage2RightCommand.Execute=function() {
	FCKAWImagePlaceholders.Add(FCK, _aw_fck_selected_image_alias, "p" )
}
FCKCommands.RegisterCommand('awimagechange_float_right', FloatImage2RightCommand ); 

// remove foat
var FloatImageRemove=function(){};
FloatImageRemove.Name='RemoveFloat';
FloatImageRemove.prototype.Execute=function(){}
FloatImageRemove.GetState=function() { return FCK_TRISTATE_OFF; }
FloatImageRemove.Execute=function() {
	FCKAWImagePlaceholders.Add(FCK, _aw_fck_selected_image_alias )
}
FCKCommands.RegisterCommand('awimagechange_float_remove', FloatImageRemove ); 




/** backward compability 
 *
 */
var InsertAWImageCommandOld=function(){};
InsertAWImageCommandOld.Name='ImageChangeOld';
InsertAWImageCommandOld.prototype.Execute=function(){}
InsertAWImageCommandOld.GetState=function() { return FCK_TRISTATE_OFF; }
InsertAWImageCommandOld.Execute=function() {
  window.open(FCKConfig.AWBaseurl+'/automatweb/orb.aw?class=image_manager&doc='+escape(window.parent.location.href)+"&in_popup=1&imgsrc="+escape(FCK.Selection.GetSelectedElement().src), 
					'InsertAWImageCommand', 'width=800,height=500,scrollbars=no,scrolling=no,location=no,toolbar=no');
}
FCKCommands.RegisterCommand('awimagechange_old', InsertAWImageCommandOld ); 

FCK.ContextMenu.RegisterListener( {
	AddItems : function( menu, tag, tagName )
	{
		if ( tagName == 'IMG')
		{
			if (tag._awimageplaceholder)
			{
				menu.AddSeparator();
				menu.AddItem( "awimagechange_float_remove", "Eemalda joondus", 37 ) ;
				menu.AddItem( "awimagechange_float_left", "Joonda vasakule", 37 ) ;
				menu.AddItem( "awimagechange_float_right", "Joonda paremale", 37 ) ;
				menu.AddSeparator();
				menu.AddItem( "awimagechange", "Pildi atribuudid", 37 ) ;
			}
			else
			{
				alert ( "if ( tagName == 'IMG' .... } else ... ");
			}
		}
	}}
);
 
/**
 * placeholder code
 */

// The object used for all AWFilePlaceholder operations.
var FCKAWImagePlaceholders = new Object() ;

// Add a new placeholder at the actual selection.
FCKAWImagePlaceholders.Add = function( oEditor, name, float2 )
{
	if (!float2)
	{
		float2 = "";
	}
	name = name.match(/pict[0-9]{1,}/ig);

	oEditor.InsertHtml( '#' + name + float2 +'#'  )
	FCKAWImagePlaceholders.Redraw();
}

FCKAWImagePlaceholders.GUP = function(param)
{
	param = param.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+param+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( window.parent.location.href );
	if( results == null )
	{
		return "";
	}
	else
	{
		return results[1];
	}
}

FCKAWImagePlaceholders.GetUrlContents = function( url )
{
	var req;
	if (window.XMLHttpRequest) 
	{
		req = new XMLHttpRequest();
		req.open('GET', url, false);
		req.send(null);
	} 
	else 
	if (window.ActiveXObject) 
	{
		req = new ActiveXObject('Microsoft.XMLHTTP');
		if (req) 
		{
			req.open('GET', url, false);
			req.send();
		}
	}
	return req.responseText; 
}


FCKAWImagePlaceholders.GetImageFloat = function( image_name )
{
	float_sufix = image_name.match( /[a-z]*[0-9]{1,}(.*)$/ )[1];
	var out;
	if (float_sufix.length > 0)
	{
		if (float_sufix=="p")
		{
			out = "right";
		}
		else if (float_sufix=="v")
		{
			out = "left";
		}
		else if (float_sufix=="k")
		{
			out = "center";
		}
		return out;
	}
	else
	{
		return false;
	}
}

FCKAWImagePlaceholders.SetupImg = function( img, name )
{
	doc_id = FCKAWImagePlaceholders.GUP("id");
	tmp = FCKAWImagePlaceholders.GetUrlContents(FCKConfig.AWBaseurl+"/automatweb/orb.aw?class=image&action=get_connection_details_for_doc&doc_id="+doc_id+"&alias_name="+name);
	eval (tmp);
	if ( typeof(connection_details_for_doc["#"+name+"#"]) == "object" )
	{
		//alert ("F");
		img_float = FCKAWImagePlaceholders.GetImageFloat(name);
		img.alias = "#"+name+"#";
		img._awimageplaceholder = name;
		img.src= connection_details_for_doc["#"+name+"#"]["url"];
		img.width = connection_details_for_doc["#"+name+"#"]["width"];
		img._oid = connection_details_for_doc["#"+name+"#"]["id"]
		if (img_float == "left" || img_float == "right")
		{
			img.setAttribute("style","float:"+img_float);
			img.style.styleFloat = img_float;
		}
		else if (img_float == "center")
		{
			img.style.textAlign = "center";
			img.style.width= "100%";
		}
	}
	else
	{
		img.style.display = "none";
		alert ( "#"+name+"#" + FCKLang.AliasNotFound );
	}
}

FCKAWImagePlaceholders._SetupClickListener = function()
{
	// because we call out FCKAWImagePlaceholders.Redraw() many times we 
	// can't attach multiple events doing the same things
	if (!FCKAWImagePlaceholders._SetupClickListenerInitialized)
	{
		FCKAWImagePlaceholders._SetupClickListenerInitialized = true
	}
	else
	{
		return false;
	}
	
	FCKAWImagePlaceholders.onPaste = function( e )
	{
		if (!e) var e = FCK.EditorWindow.event ;

		setTimeout(function() {
			sHTML = FCK.EditorDocument.body.innerHTML;
			var re=/alias/;
			if (re.test(sHTML))
			{
				FCK.EditorDocument.body.innerHTML = sHTML.replace(/<span.*img.*?alias="(.*?)".*?span>/g, "$1")
				FCKAWImagePlaceholders.Redraw();
				e.returnValue = false;
			}
		}, 1); // 1ms should be enough
	}
	
	FCKAWImagePlaceholders.onPasteIE = function(  )
	{
		var e = FCK.EditorWindow.event ;
		e.target = e.srcElement
		e.preventDefault();
		e.stopPropagation(); // ie
	}

	FCKAWImagePlaceholders._ClickListener = function( e )
	{
		if (!e) {var e = FCK.EditorWindow.event ; e.target = e.srcElement}
		if ( e.target.tagName == 'IMG')
		{
			if (e.target._awimageplaceholder )
			{
				_aw_fck_selected_image_oid = e.target._oid;
				_aw_fck_selected_image_alias = e.target._awimageplaceholder;
			}
		}
	}
	
	FCKAWImagePlaceholders._ClickListenerIE = function(  )
	{
		var e = FCK.EditorWindow.event ;
		e.target = e.srcElement

		if ( e.target.tagName == 'IMG' && e.target._awimageplaceholder )
		{
			setTimeout(function() {
				_aw_fck_selected_image_oid = e.target._oid;
				_aw_fck_selected_image_alias = e.target._awimageplaceholder;
				//FCKSelection.SelectNode( e.target.parentNode ) ;
			}, 1); // 1ms should be enough
		}
	}
	
	if (document.all) {        // If Internet Explorer.
		// this was intended for ie's right click, so image caption could also be right clicked
		//FCK.EditorDocument.attachEvent("onclick", FCKAWImagePlaceholders._ClickListenerIE ) ;
		FCK.EditorDocument.attachEvent("onmousedown", FCKAWImagePlaceholders._ClickListenerIE ) ;
		//FCK.EditorDocument.attachEvent( 'OnPaste', FCKAWImagePlaceholders.onPasteIE ) ;
	} else {                // If Gecko.
		//FCK.EditorDocument.addEventListener( 'click', DenGecko_OnKeyDown, true ) ;
		FCK.EditorDocument.addEventListener( 'click', FCKAWImagePlaceholders._ClickListener, true ) ;
		FCK.EditorDocument.addEventListener( 'paste', FCKAWImagePlaceholders.onPaste, true ) ;	
	}
}

// Open the AWFilePlaceholder dialog on double click.
FCKAWImagePlaceholders.OnDoubleClick = function( span )
{
	if ( span.tagName == 'SPAN' && span._awimageplaceholder )
		FCKCommands.GetCommand( 'AWFilePlaceholder' ).Execute() ;
}

FCK.RegisterDoubleClickHandler( FCKAWImagePlaceholders.OnDoubleClick, 'SPAN' ) ;

// Check if a Placholder name is already in use.
FCKAWImagePlaceholders.Exist = function( name )
{
	var aSpans = FCK.EditorDocument.getElementsByTagName( 'SPAN' ) ;

	for ( var i = 0 ; i < aSpans.length ; i++ )
	{
		if ( aSpans[i]._awimageplaceholder == name )
			return true ;
	}

	return false ;
}

if ( FCKBrowserInfo.IsIE )
{
	FCKAWImagePlaceholders.Redraw = function()
	{
		if ( FCK.EditMode != FCK_EDITMODE_WYSIWYG )
			return ;

		var aPlaholders = FCK.EditorDocument.body.innerText.match( /(#pict[^#]+#)/g ) ;
		if ( !aPlaholders )
			return ;
			
		var oRange = FCK.EditorDocument.body.createTextRange() ;

		for ( var i = 0 ; i < aPlaholders.length ; i++ )
		{
			if ( oRange.findText( aPlaholders[i] ) )
			{
				var name = aPlaholders[i].match( /#([^#]*?)#/ )[1] ;

				doc_id = FCKAWImagePlaceholders.GUP("id");
				tmp = FCKAWImagePlaceholders.GetUrlContents(FCKConfig.AWBaseurl+"/automatweb/orb.aw?class=image&action=get_connection_details_for_doc&doc_id="+doc_id+"&alias_name="+name);
				eval(tmp);
				img_float = FCKAWImagePlaceholders.GetImageFloat(name);
				img_align = "";
				
				if (img_float)
				{
					if (img_float == "left" || img_float == "right")
					{
						img_align = "style=\"float:"+img_float+"\"";
					}
					else if (img_float == "center" ) 
					{
						img_align = "style=\"text-align: center\"";
					}
				}
				oRange.pasteHTML( "<img "+img_align+" width="+connection_details_for_doc["#"+name+"#"]["width"]+" src='"+connection_details_for_doc["#"+name+"#"]["url"]+"' _awimageplaceholder="+name+" _oid="+connection_details_for_doc["#"+name+"#"]["id"]+" />");
			}
		}
		FCKAWImagePlaceholders._SetupClickListener() ;
	}
}
else
{
	FCKAWImagePlaceholders.Redraw = function()
	{
		if ( FCK.EditMode != FCK_EDITMODE_WYSIWYG )
			return ;

		var oInteractor = FCK.EditorDocument.createTreeWalker( FCK.EditorDocument.body, NodeFilter.SHOW_TEXT, FCKAWImagePlaceholders._AcceptNode, true ) ;

		var	aNodes = new Array() ;

		while ( ( oNode = oInteractor.nextNode() ) )
		{
			aNodes[ aNodes.length ] = oNode ;
		}

		for ( var n = 0 ; n < aNodes.length ; n++ )
		{
			var aPieces = aNodes[n].nodeValue.split( /(#[^#]+#)/g ) ;

			for ( var i = 0 ; i < aPieces.length ; i++ )
			{
				if ( aPieces[i].length > 0 )
				{
					if ( aPieces[i].indexOf( '#' ) == 0 )
					{
						var sName = aPieces[i].match( /#\s*([^#]*?)\s*#/ )[1] ;

						var oImg = FCK.EditorDocument.createElement( 'img' ) ;
						FCKAWImagePlaceholders.SetupImg( oImg, sName ) ;

						aNodes[n].parentNode.insertBefore( oImg, aNodes[n] ) ;
					}
					else
						aNodes[n].parentNode.insertBefore( FCK.EditorDocument.createTextNode( aPieces[i] ) , aNodes[n] ) ;
				}
			}

			aNodes[n].parentNode.removeChild( aNodes[n] ) ;
		}

		FCKAWImagePlaceholders._SetupClickListener() ;
	}

	// accept aw aliases
	FCKAWImagePlaceholders._AcceptNode = function( node )
	{
		if ( /#pict[^#]+#/.test( node.nodeValue ) )
			return NodeFilter.FILTER_ACCEPT ;
		else
			return NodeFilter.FILTER_SKIP ;
	}
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FCKAWImagePlaceholders.Redraw ) ;

// We must process the SPAN tags to replace then with the real resulting value of the placeholder.
FCKXHtml.TagProcessors['img'] = function( node, htmlNode )
{
	if ( htmlNode._awimageplaceholder )
		node = FCKXHtml.XML.createTextNode( '#' + htmlNode._awimageplaceholder + '#' ) ;
	else
		FCKXHtml._AppendChildNodes( node, htmlNode, false ) ;
		
	return node ;
}