var arrBronsTry = Array(); // make the array for checkBrons(), if all's ok, then copy to arrBronsActive and draw
var arrBronsActive = Array(); // currently red on the screen
var bronTexts = Array();
var isClicked=false; // used in changeRoomReservationLength(). it is either false or contains rooms reservation length
bronTexts["BRON"] = "Broneeri";
bronTexts["FREE"] = "VABA";
var bronErrors = Array();
var current_timestamp = 0;
bronErrors["CANT_BRON"] = "Ei saa broneerida";

/**
	* does bron and goes to the next step as well
**/
function doBronExec(
		    strId, 
		    intCalendarIntervall, 
		    intRoomReservationLength, 
		    intProduct,
		    strUrl,
		    intWidth,
		    intHeight,
		    intNoPopup
)
{
	if (typeof(intRoomReservationLength)=="undefined" || intRoomReservationLength==null)
	{
		sel = document.getElementById("room_reservation_length");
		intRoomReservationLength = sel.options[sel.selectedIndex].value*intCalendarIntervall;
	}

	if (!doBron(strId, intCalendarIntervall, intRoomReservationLength, intProduct))
	{
		return false;
	}
	if (strUrl)
	{
		cancel_bron_popup_dialog();

		strUrl += '&start1='+current_timestamp+'&end='+(current_timestamp+intRoomReservationLength);
		if (intNoPopup)
		{
			window.location.href=strUrl;
		}
		else
		{
			aw_popup_scroll(strUrl, 'bronpop', intWidth, intHeight);
		}
	}
	else
	{
		document.changeform.action.value = 'do_add_reservation';
		document.changeform.submit();
		return true;
	}
}

/**
 * Does the bronning work
 *
 * @param strId 
 * @param intCalendarIntervall
 * @param intRoomReservationLength optional
 * @param intProduct optional
 *
 * For bronning with product all 4 params have to be set. 
 */
function doBron (strId, intCalendarIntervall, intRoomReservationLength, intProduct)
{
	isClicked = intCalendarIntervall;

	if (typeof(intRoomReservationLength)=="undefined")
	{
		sel = document.getElementById("room_reservation_length");
		intRoomReservationLength = sel.options[sel.selectedIndex].value*intCalendarIntervall;
	}
	if (intProduct)
		document.getElementById("product").value = intProduct;

	return setBrons (strId, intCalendarIntervall, intRoomReservationLength);
}

function changeRoomReservationLength(that)
{
	if (isClicked)
	{
		intCalendarIntervall = isClicked;
		intRoomReservationLength = that[that.selectedIndex].value*intCalendarIntervall;
		strId = arrBronsActive[0];
	
		setBrons (strId, intCalendarIntervall, intRoomReservationLength);
	}
}

function setBrons (strId, intCalendarIntervall, intRoomReservationLength)
{
	setArrBronsTry (strId, intCalendarIntervall, intRoomReservationLength);

	if (canBron())
	{
		drawBrons();
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Draws nice colored boxed on calendar... and also sets value for the hidden formelement
 */
function drawBrons()
{
	var i;
	var strId;
	clearBrons ();
	arrBronsActive = arrBronsTry;

	for (i=0;i<arrBronsActive.length;i++)
	{
		strId = arrBronsActive[i];
		document.getElementById(strId).style.background = "red";
		document.getElementById(strId).parentNode.firstChild.style.background = "red";
		if (navigator.userAgent.indexOf("MSIE") > 0)
		{
			if (""+document.getElementById(strId).childNodes[0] != "[Object]" && ""+document.getElementById(strId).childNodes[0] != "[object]")
			{
				continue;
			}
			document.getElementById(strId).childNodes[0].innerHTML = bronTexts["BRON"];
			document.getElementById(strId).childNodes[1].value = 1;
		}
		else
		{
			document.getElementById(strId).childNodes[1].innerHTML = bronTexts["BRON"];
			document.getElementById(strId).childNodes[2].value = 1;
		}
	}
}

/**
 * @return bool
 * Uses global variable arrCurrentBrons to turn bronned times to not bronned
 */
function clearBrons ()
{
	if (arrBronsActive.length==0)
		return true;

	var strNextId;
	
	for (i=0;i<arrBronsActive.length;i++)
	{
		strNextId = arrBronsActive[i];
		document.getElementById(strNextId).style.background = "#e1e1e1";
		document.getElementById(strNextId).parentNode.firstChild.style.background = "#e1e1e1";
		if (navigator.userAgent.indexOf("MSIE") > 0)
		{
			document.getElementById(strNextId).childNodes[0].innerHTML = bronTexts["FREE"];
			document.getElementById(strNextId).childNodes[1].value = 0;
		}
		else
		{
			document.getElementById(strNextId).childNodes[1].innerHTML = bronTexts["FREE"];
			document.getElementById(strNextId).childNodes[2].value = 0;
		}
	}
	arrBronsActive = Array(); // reset
	return true;
}

/**
 * Checks if clicked elements exist and, if so, then if it is not bronned
 * Matches id's in  arrBronsTry to ones on page
 */
function canBron ()
{
	var i;

	for (i=0;i<arrBronsTry.length;i++)
	{
		if (isTimeBronned(arrBronsTry[i]) )
		{
			alert (bronErrors["CANT_BRON"]);
			return false;
		}
	}
	return true;
}

/**
 * @param strId fields id - td's id
 * Checks if field exists, has hidden form element (which has id)
 */
function isTimeBronned (strId)
{
	try {
		if (navigator.userAgent.indexOf("MSIE") > 0)
		{
			if (document.getElementById(strId).childNodes[1].id)
				return false;
			else
				return true;
		}
		else
		{
			if (document.getElementById(strId).childNodes[2].id)
				return false;
			else
				return true;
		}
	}
	catch (e) {return true;}
}

/**
 * Before anything, arrBronsTry will be set with id of html elements.
 * arrBronsTry is used by canBron() to see if all indexes exist on page thus meaning if times ar 
 * availabe for bronning.
 */
function setArrBronsTry (strId, intCalendarIntervall, intRoomReservationLength)
{
	arrBronsTry = Array();
	var strNextId = strId;
	var intTS = getTSFromPrefixAndTimestamp(strId) ;
	var intRID = getRIDFromPrefixAndTimestamp (strId);
	var tmp;
	var i=1;
	current_timestamp = intTS;
	while (intRoomReservationLength>0)
	{
		arrBronsTry[arrBronsTry.length] = strNextId;
		
		tmp = intCalendarIntervall+getTSFromPrefixAndTimestamp(strNextId);
		strNextId = intRID+"_"+tmp;
		intRoomReservationLength -= intCalendarIntervall;
		i++;
	}
}
 
 


function splitPrefixAndTimestamp (str)
{
	arrOutput = new Array ();
	str = str+"";
	
	intSplitI = str.indexOf("_");
	arrOutput["prefix"] = str.substring(0,intSplitI);
	arrOutput["timestamp"] = str.substring(intSplitI+1);
	return arrOutput;
}

function getTSFromPrefixAndTimestamp (str)
{
	t = new Array ();
	t = splitPrefixAndTimestamp (str);
	return t["timestamp"]*1.0;
}

function getRIDFromPrefixAndTimestamp (str)
{
	t = new Array ();
	t = splitPrefixAndTimestamp (str);
	return t["prefix"];
}

var current_popup;
function bron_disp_popup(elname, timestamp, position_el)
{
	var pop_el, el, x, y;
	pop_el=document.getElementById(elname);
	current_popup = pop_el;

	el = document.getElementById(position_el);
	activeButton=el;

	x = getPageOffsetLeft(el);
	if (pp_browser.isIE)
	{
		y = getPageOffsetTop(el) + el.offsetHeight + 10 ;
	}
	else
	{
		y = getPageOffsetTop(el) + el.offsetHeight;
	}
	if (pp_browser.isIE) 
	{
		x += el.offsetParent.clientLeft;
		y += el.offsetParent.clientTop;
	}
	pop_el.style.left = x + "px";
	pop_el.style.top  = y + "px";
	pop_el.style.visibility = "visible";
	current_timestamp = timestamp;
	reset_func = cancel_bron_popup_dialog;
	pop_el.style.display='block';
}

function cancel_bron_popup_dialog(el)
{
  if (current_popup)
    {
	current_popup.style.display = 'none';
    }
} 