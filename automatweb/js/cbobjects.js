// (C) Shelley Powers, YASD, 1998 - 2000
//
// These are the newest version of the cross-browser scripting objects, 
// incorporated into one Javascript file. 
//
// Each DOM gets its own object, with similar interfaces but differing implementations

// The W3C DOM Object
//
//*************************************************************************************
function dom_object(obj) {
	this.css2 = obj;
	this.name = obj.id;
	this.objResizeBy = domResizeBy;
	this.objHide = domHide;
	this.objShow = domShow;
      this.objDisplay = domDisplay;
	this.objGetLeft = domGetLeft;
	this.objGetTop = domGetTop;
	this.objSetTop = domSetTop;
	this.objSetLeft = domSetLeft;
	this.objMoveAbsolute = domMoveAbsolute;
	this.objMoveRelative = domMoveRelative;
	this.objGetWidth = domGetWidth;
	this.objGetHeight = domGetHeight;
	this.objSetHeight = domSetHeight;
	this.objSetWidth = domSetWidth;
	this.objSetZIndex = domSetZIndex;
	this.objGetZIndex = domGetZIndex;
	this.objSetClipRect = domSetClipRect;
      this.objGetClipRect = domGetClipRect;
	this.objGetClipLeft = domGetClipLeft;
	this.objGetClipRight = domGetClipRight;
	this.objGetClipTop = domGetClipTop;
	this.objGetClipBottom = domGetClipBottom;
	this.replace_html = domReplaceHTML;
	this.objReplaceHTML = domParamReplaceHTML;
	this.objReplaceText = domReplaceText;
      this.objGetVisibility = domGetVisibility;
}


// The IE 4.x and 5.x DOM Object
//
//*************************************************************************************
function ie_object(obj) {
	this.css2 = obj;
	this.name = obj.id;
	this.objResizeBy = domResizeBy;
	this.objHide = domHide;
	this.objShow = domShow;
      this.objDisplay = domDisplay;
	this.objGetLeft = domGetLeft;
	this.objGetTop = domGetTop;
	this.objSetTop = domSetTop;
	this.objSetLeft = domSetLeft;
	this.objMoveAbsolute = domMoveAbsolute;
	this.objMoveRelative = domMoveRelative;
	this.objGetWidth = domGetWidth;
	this.objGetHeight = domGetHeight;
	this.objSetHeight = domSetHeight;
	this.objSetWidth = domSetWidth;
	this.objSetZIndex = domSetZIndex;
	this.objGetZIndex = domGetZIndex;
	this.objSetClipRect = domSetClipRect;
      this.objGetClipRect = domGetClipRect;
	this.objGetClipLeft = domGetClipLeft;
	this.objGetClipRight = domGetClipRight;
	this.objGetClipTop = domGetClipTop;
	this.objGetClipBottom = domGetClipBottom;
	this.replace_html = domReplaceHTML;
	this.objReplaceHTML = domParamReplaceHTML;
	this.objReplaceText = domReplaceText;
      this.objGetVisibility = domGetVisibility;
}



// The Navigator DOM Object
//
//*************************************************************************************
function ns_object(obj) {
	this.css2 = obj;
	this.name = obj.name;
	this.objResizeBy = domResizeBy;
	this.objHide = nsobjHide;
	this.objShow = nsobjShow;
        this.objDisplay = nsobjDisplay;
	this.objGetLeft = nsobjGetLeft;
	this.objGetTop = nsobjGetTop;
	this.objSetTop = nsobjSetTop;
	this.objSetLeft = nsobjSetLeft;
	this.objMoveAbsolute = domMoveAbsolute;
	this.objMoveRelative = domMoveRelative;
	this.objGetWidth = nsobjGetWidth;
	this.objGetHeight = nsobjGetHeight;
	this.objSetHeight = nsobjSetHeight;
	this.objSetWidth = nsobjSetWidth;
	this.objSetZIndex = nsobjSetZIndex;
	this.objGetZIndex = nsobjGetZIndex;
	this.objSetClipRect = nsobjSetClipRect;
      this.objGetClipRect = nsobjGetClipRect;
	this.objGetClipLeft = nsobjGetClipLeft;
	this.objGetClipRight = nsobjGetClipRight;
	this.objGetClipTop = nsobjGetClipTop;
	this.objGetClipBottom = nsobjGetClipBottom;
	this.replace_html = nsreplace_html;
	this.objReplaceHTML = nsParamReplaceHTML;
	this.objReplaceText = nsReplaceText;
      this.objGetVisibility = nsVisibility;
}

//*************************************************************************************
//
// The implementations
//
//*************************************************************************************


// The DOM Object Implementations
//
//*************************************************************************************


function domResizeBy(wincr,hincr) {
   var wdth = this.objGetWidth();
   wdth += wincr;
   this.objSetWidth(wdth);
   
   var ht = this.objGetHeight();
   ht += hincr;
   this.objSetHeight(ht);
}


// element's left position
function domGetLeft() {
        var lt = parseInt(this.css2.style.left);
	return lt;
}

// element's top position
function domGetTop () {
        var tp = parseInt(this.css2.style.top);
	return tp;
}

// set element's top position
function domSetTop (top) {
	this.css2.style.top = top + "px";
}

// set element's left position
function domSetLeft(left) {
	this.css2.style.left = left + "px";
}


// get element's width
function domGetWidth() {
        var wd = parseInt(this.css2.style.width);
	return wd;
}

// get element's height
function domGetHeight() {
        var ht = parseInt(this.css2.style.height);
	return ht;
}

// set element's height
function domSetHeight(height) {
	this.css2.style.height = height + "px";
}

// set element's width
function domSetWidth(width) {
	this.css2.style.width = width + "px";
}


// hide element
function domHide() {
   this.css2.style.visibility = "hidden";
}

// show element
function domShow() {
   this.css2.style.visibility = "visible";
}


// display element
function domDisplay(type) {
   this.css2.style.display = type;
}

// make absolute move
function domMoveAbsolute(newleft, newtop) {
   this.objSetLeft(newleft);
   this.objSetTop(newtop);
}

// move relative to current location
function domMoveRelative(left, top) {
   this.objSetLeft(left + this.objGetLeft());
   this.objSetTop(top + this.objGetTop());    
}

// return clipping rectangle
function domGetClipRect() {
   return this.css2.style.clip;
}

// clip object
function domSetClipRect(top, left, bottom, right) {
   if (top == null) top = this.objGetClipTop();
   if (left == null) left = this.objGetClipLeft();
   if (bottom == null) bottom = this.objGetClipBottom();
   if (right == null) right = this.objGetClipRight();
   strng = "rect(" + top + "px, " + right + "px, " + bottom + "px, " + left + "px)";
  this.css2.style.clip = strng;
}

// convert string to value
function convert(strng) {
    var i = parseInt(strng);
    return i;
}

// get clipping value for specific dimension
function get_entry(obj,indx) {
	strng = obj.css2.style.clip;
        if (strng.length > 0) {
	   strng = strng.slice(5,strng.length-1);
	   var entries = strng.split(" ");
           }
        else {
            var entries = new Array(5);
            for (i = 0; i < entries.length; i++)
                entries[i] = "auto";
            }
	if (indx == "top") {
		if (entries[0] == "auto") 
                   return 0;
		else
		   return convert(entries[0]);
            }
	else if (indx == "left") {
		if (entries[3] == "auto") 
		   return 0;
		else
		   return convert(entries[3]);
		}
	else if (indx == "bottom"){
		if (entries[2] == "auto") {
		   return obj.objGetHeight();
                   }
		else
		   return convert(entries[2]);
              }
	else if (indx == "right") {
		if (entries[1] == "auto") 
		   return obj.objGetWidth();
		else
		   return convert(entries[1]);
		}
	
}
	
// clip object on left
function domGetClipLeft() {
	return get_entry(this,"left");
}

// clip object on right
function domGetClipRight() {
	return get_entry(this, "right");
}

// clip object at top
function domGetClipTop() {
	return get_entry(this,"top");
}

// clip object at bottom
function domGetClipBottom() {
	return get_entry(this,"bottom");
}

// set element's zindex order
function domSetZIndex(zindex) {
   this.css2.style.zIndex = zindex;
}

// get element's current zindex order
function domGetZIndex(zindex) {
   return this.css2.style.zIndex;
}


// replace text (equivalent to innerText)
function domReplaceText(txt_string) {

   var nodes = this.css2.childNodes;
   var node = nodes.item(0);
   node.replaceData(0,node.length,txt_string);   
}

// replace html (innerHTML)
function domReplaceHTML(html_string) {
	this.css2.innerHTML = html_string;
} 


// replace HTML -- replace contents with specific object
function domParamReplaceHTML(tag,clss,id,contents) {

    this.objHide();
    var r = this.css2.ownerDocument.createRange();
    r.selectNodeContents(this.css2);
    r.deleteContents();

    var elem = document.createElement(tag);
    elem.setAttribute("id",id);
    elem.setAttribute("className",clss);
    var txt = document.createTextNode(contents);
    elem.appendChild(txt);
    this.css2.appendChild(elem); 
    this.objShow();
  }


// return visibility
function domGetVisibility() {
    return this.css2.style.visibility;
}




// The IE Object Implementations
//
//*************************************************************************************



// replace html (with specific element)
function ieParamReplaceHTML(tag, clss, id, contents) {
    var strng = "<" + tag + " class='" + clss + "' id='" + id + "'>";
    strng = strng + contents;
    strng = strng + "</" + tag +  ">";
    this.css2.innerHTML = strng;
     }





// The Navigator 4.x Object Implementations
//
//*************************************************************************************

// hide element
function nsobjHide() {
	this.css2.visibility = "hidden";
}

// show element
function nsobjShow() {
	this.css2.visibility = "inherit";
}


// element display
function nsobjDisplay(type) {
   if (type == "none")
       this.objHide();
   else
       this.objShow();
}

// element's left position
function nsobjGetLeft() {
	return this.css2.left;
}

// element's top position
function nsobjGetTop () {
	return this.css2.top;
}

// set element's top position
function nsobjSetTop(top) {
	this.css2.top = top;
}

// set element's left position
function nsobjSetLeft(left) {
	this.css2.left = left;
}


// get element's width
function nsobjGetWidth() {
	return this.css2.clip.width;
}

// get element's height
function nsobjGetHeight() {
	return this.css2.clip.height;
}

// set element's width
function nsobjSetWidth(width) {
	this.css2.clip.width = width;
}

// set element's height
function nsobjSetHeight(height) {
	this.css2.clip.height = height;
}

// set element's zindex order
function nsobjSetZIndex(zindex) {
	this.css2.zIndex = zindex;
}

// get element's current zindex order
function nsobjGetZIndex() {
	return this.css2.zIndex;
}

// clip object
function nsobjSetClipRect (top,left,bottom,right) {
	if (top == null) top = this.objGetClipTop();
	if (left == null) left = this.objGetClipLeft();
	if (bottom == null) bottom = this.objGetClipBottom();
	if (right == null) right = this.objGetClipRight();
	this.css2.clip.left = left;
	this.css2.clip.right = right;
	this.css2.clip.top = top;
	this.css2.clip.bottom = bottom;
}

function nsobjGetClipRect () {
   var strng;
   var left = this.css2.clip.left;
   var right = this.css2.clip.right;
   var bottom = this.css2.clip.bottom;
   var top = this.css2.clip.top;

   strng = "rect(" + top + "px, " + right + "px, " + bottom + "px, " + left + "px)";
   return strng;
}

// get current clip right 
function nsobjGetClipRight() {
	return this.css2.clip.right;
}

// get current clip left
function nsobjGetClipLeft() {
	return this.css2.clip.left;
}

// get current clip top
function nsobjGetClipTop() {
	return this.css2.clip.top;
}

// get current clip bottom
function nsobjGetClipBottom() {
	return this.css2.clip.bottom;
}
	
// replace html (navigator)
function nsreplace_html(html_string) {
	this.css2.document.write(html_string);
	this.css2.document.close();
}


function nsParamReplaceHTML(tag, clss, id, contents) {
    var strng = "<" + tag + " class='" + clss + "' id='" + id + "'>";
    strng = strng + contents;
    strng = strng + "</" + tag + ">";
    this.css2.document.write(strng);
    this.css2.document.close();
    }

function nsReplaceText(text_string) {
  // this function not implemented
}

function nsVisibility() {
   return this.css2.visibility;
}





//****************************************************************************************
//
// Create the objects
//
//****************************************************************************************


// For IE, pull all DIV blocks into object array
function create_ie_objects() 
{
	theelements = document.all.tags("DIV");
	theobjs = new Array();
	for (i = 0; i < theelements.length; i++)
	{
		if (theelements[i].id != "") 
		{
			theobjs[theelements[i].id] = new ie_object(theelements[i]);
		}
	}
}

// For Navigator 4.x, pull all DIV blocks into object array
function create_ns_objects(newarray) {
   theobjs = new Array();
   for (i = 0; i < document.layers.length; i++){
     if (document.layers[i].name != "") 
   	 theobjs[document.layers[i].name] = new ns_object(document.layers[i]);
     }
}

// For W3C DOM (Navigator 6.x, Mozilla), pull all named DIV blocks into an array
function create_dom_objects() {
  theelements = document.getElementsByTagName("DIV");
  theobjs = new Array();
  for (i = 0; i < theelements.length; i++) {
      var obj = theelements[i];
      if (obj.id != "")
         theobjs[obj.id] = new dom_object(obj);
      }
}


function create_objects() 
{
	// if IE
	if (navigator.appName == "Microsoft Internet Explorer")
	{
		create_ie_objects();
	}
	else // Navigator or Mozilla
	{
		if (navigator.appName == "Mozilla" || navigator.appName == "Netscape")
		{
			if (navigator.appVersion.indexOf("4.") == -1)
			{
				create_dom_objects();
			}
			else
			{
				create_ns_objects();
			}
		}
	}
}
