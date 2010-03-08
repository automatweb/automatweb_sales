var arrBronsTry = Array(); // make the array for checkBrons(), if all's ok, then copy to arrBronsActive and draw
var arrBronsActive = Array(); // currently red on the screen
var bronTexts = Array();
var isClicked=false; // used in changeRoomReservationLength(). it is either false or contains rooms reservation length
bronTexts["BRON"] = "Reserve";
bronTexts["FREE"] = "FREE";
var bronErrors = Array();
bronErrors["CANT_BRON"] = "Unable to add reservation";
bronErrors["CANT_BRON_NO_TIME"] = "Time decreased to fit current between current timespan";
var room_reservation_length_max = false
var room_reservation_length_clone = false;
var room_reservation_index = false;

/**
	* does bron and goes to the next step as well
**/
function doBronExec(strId, intCalendarIntervall, intRoomReservationLength, intProduct)
{
	doBron(strId, intCalendarIntervall, intRoomReservationLength, intProduct);
	submit_changeform('do_add_reservation');
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
	// find the max value from input#room_reservation_length
	if (room_reservation_length_max == false)
	{
		room_reservation_length_max = $("#room_reservation_length option:last").html()
	}
	
	if (!room_reservation_length_clone)
	{
		//room_reservation_length_clone = $("#room_reservation_length").clone();
	}
	else
	{
		//room_reservation_length_clone.selectedIndex = room_reservation_index;
		//$("#room_reservation_length").html(room_reservation_length_clone.html());
	}

	isClicked = intCalendarIntervall;
	
	// change hour dropdown according to available actual available time
	max_bron_hours = getMaxBronHours (strId, intCalendarIntervall)
	i = 0;
	$("#room_reservation_length option").each(function(){
		if ($(this).attr("selected"))
		{
			room_reservation_index = $(this).html()-1;
		}

		if (max_bron_hours == 1)
		{
			document.getElementById("room_reservation_length").selectedIndex = 0;
		}
		else if (($(this).html()*1.0)==max_bron_hours-1 && room_reservation_index===false)
		{
			$(this).next().attr("selected", true);
		}
	});
	room_reservation_index = false;
	
	if (!intRoomReservationLength)
	{
		sel = document.getElementById("room_reservation_length");
		intRoomReservationLength = sel.options[sel.selectedIndex].value*intCalendarIntervall;
	}
	
	if (intProduct)
		document.getElementById("product").value = intProduct;
	
	setBrons (strId, intCalendarIntervall, intRoomReservationLength);
}

function changeRoomReservationLength(that)
{
	if (isClicked)
	{
		intCalendarIntervall = isClicked;
		
		strId = arrBronsActive[0];
		
		// change hour dropdown according to available actual available time
		max_bron_hours = getMaxBronHours (strId, intCalendarIntervall);
		if (that.selectedIndex>max_bron_hours-1)
		{
			alert (bronErrors["CANT_BRON_NO_TIME"]);
			that.selectedIndex = max_bron_hours-1;
		}
		
		intRoomReservationLength = that[that.selectedIndex].value*intCalendarIntervall;
		setBrons (strId, intCalendarIntervall, intRoomReservationLength);
	}
}

function setBrons (strId, intCalendarIntervall, intRoomReservationLength)
{
	setArrBronsTry (strId, intCalendarIntervall, intRoomReservationLength);
	
	if (canBron())
	{
		drawBrons();
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

function getMaxBronHours (strId, intCalendarIntervall)
{
	var strNextId = strId;
	var intTS = getTSFromPrefixAndTimestamp(strId) ;
	var intRID = getRIDFromPrefixAndTimestamp (strId);
	var tmp;
	var i=1;
	intRoomReservationLength = room_reservation_length_max * intCalendarIntervall;
	while (intRoomReservationLength>0)
	{
		tmp = intCalendarIntervall+getTSFromPrefixAndTimestamp(strNextId);
		strNextId = intRID+"_"+tmp;
		intRoomReservationLength -= intCalendarIntervall;
		try {
			document.getElementById(strNextId).childNodes[2].id;
		}
		catch (e) {return i}
		i++;
	}
	return 6;
}

/**
 * @param strId fields id - td's id
 * Checks if field exists, has hidden form element (which has id)
 */
function isTimeBronned (strId)
{
	try {
		if (document.getElementById(strId).childNodes[2].id)
			return false
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
	current_popup.style.display = 'none';
}
