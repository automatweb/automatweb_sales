// load accompanying css
var bsnAsFilename = "js/jquery/plugins/ptTimeSelect/jquery.ptTimeSelect.css";
var bsnAsFileref = document.createElement("link");bsnAsFileref.setAttribute("rel", "stylesheet");bsnAsFileref.setAttribute("type", "text/css");bsnAsFileref.setAttribute("href", bsnAsFilename);if(typeof bsnAsFileref != "undefined"){document.getElementsByTagName("head")[0].appendChild(bsnAsFileref);}

var bsnAsFilename = "js/jquery/plugins/ptTimeSelect/ui.all.css";
var bsnAsFileref = document.createElement("link");bsnAsFileref.setAttribute("rel", "stylesheet");bsnAsFileref.setAttribute("type", "text/css");bsnAsFileref.setAttribute("href", bsnAsFilename);if(typeof bsnAsFileref != "undefined"){document.getElementsByTagName("head")[0].appendChild(bsnAsFileref);}



/***********************************************************************
 * FILE: jquery.ptTimeSelect.js
 *
 * 		jQuery plug in for displaying a popup that allows a user
 * 		to define a time and set that time back to a form's input
 * 		field.
 *
 *
 * AUTHOR:
 *
 * 		*Paul T.*
 *
 * 		- <http://www.purtuga.com>
 * 		- <http://pttimeselect.sourceforge.net>
 *
 *
 * DEPENDECIES:
 *
 * 		-	jQuery.js
 * 			<http://docs.jquery.com/Downloading_jQuery>
 *
 *
 * LICENSE:
 *
 * 		Copyright (c) 2007 Paul T. (purtuga.com)
 *		Dual licensed under the:
 *
 * 		-	MIT
 * 			<http://www.opensource.org/licenses/mit-license.php>
 *
 * 		-	GPL
 * 			<http://www.opensource.org/licenses/gpl-license.php>
 *
 * INSTALLATION:
 *
 * There are two files (.css and .js) delivered with this plugin and
 * that must be incluced in your html page after the jquery.js library,
 * and prior to making any attempts at using it. Both of these are to
 * be included inside of the 'head' element of the document.
 * |
 * |	<link rel="stylesheet" type="text/css" href="jquery.ptTimeSelect.css" />
 * |	<script type="text/javascript" src="jquery.ptTimeSelect.js"></script>
 * |
 *
 * USAGE:
 *
 * 	-	See <$(ele).ptTimeSelect()>
 *
 *
 *
 * LAST UPDATED:
 *
 * 		- $Date: 2009/09/12 07:33:47 $
 * 		- $Author: voldemar $
 * 		- $Revision: 1.3 $
 *
 *
 **********************************************************************/

jQuery.ptTimeSelect = {};

/***********************************************************************
 * PROPERTY: jQuery.ptTimeSelect.options
 * 		The default options for all timeselect attached elements. Can be
 * 		overwriten wiht <jQuery.fn.ptTimeSelect()>
 *
 * 	containerClass	-
 *
 *
 */
jQuery.ptTimeSelect.options = {
		containerClass: undefined,
		containerWidth: undefined,
		//~ hoursLabel: 'Hour',
		//~ minutesLabel: 'Minutes',
		setButtonLabel: 'OK',
		popupImage: undefined,
		onFocusDisplay: true,
		zIndex: 10,
		onBeforeShow: undefined,
		onClose: undefined
};


/***********************************************************************
 * METHOD: jQuery.ptTimeSelect._ptTimeSelectInit()
 * 		Internal method. Called when page is initalized to add the time
 * 		selection area to the DOM.
 *
 * PARAMS:
 *
 * 		none.
 *
 * RETURNS:
 *
 * 		nothing.
 *
 *
 */
jQuery.ptTimeSelect._ptTimeSelectInit = function () {
	jQuery(document).ready(
		function () {
			//if the html is not yet created in the document, then do it now
			if (!jQuery('#ptTimeSelectCntr').length) {
				jQuery("body").append(
						'<div id="ptTimeSelectCntr" class="">'
					+	'		<div class="ui-widget ui-widget-content">'
					+	'		<div class="ptTimeSelectHeader">'
					+	'			<div id="ptTimeSelectCloseCntr" style="float: right;">'
					+	'				<a href="javascript: void(0);" onclick="jQuery.ptTimeSelect.closeCntr();" '
					+	'						onmouseover="jQuery(this).removeClass(\'ui-state-default\').addClass(\'ui-state-hover\');" '
					+	'						onmouseout="jQuery(this).removeClass(\'ui-state-hover\').addClass(\'ui-state-default\');"'
					+	'						class="ui-state-default">'
					+	'					<span class="ui-icon ui-icon-circle-close">X</span>'
					+	'				</a>'
					+	'			</div>'
					+	'			<div id="ptTimeSelectUserTime" style="float: left;">'
					+	'				<span id="ptTimeSelectUserSelHr">1</span> : '
					+	'				<span id="ptTimeSelectUserSelMin">00</span> '
					//~ +	'				<span id="ptTimeSelectUserSelAmPm">AM</span>'
					+	'			</div>'
					+	'			<br style="clear: both;" /><div></div>'
					+	'		</div>'
					+	'		<div class="ui-widget-content">'
					+	'			<div>'
					+	'				<div class="ptTimeSelectTimeLabelsCntr">'
					+	'					<div class="ptTimeSelectLeftPane" style="width: 50%; text-align: center; float: left;" class="">Hour</div>'
					+	'					<div class="ptTimeSelectRightPane" style="width: 50%; text-align: center; float: left;">Minutes</div>'
					+	'				</div>'
					+	'				<div>'
					+	'					<div style="float: left; width: 50%;">'
					+	'						<div class="ui-widget-content ptTimeSelectLeftPane">'
					//~ +	'							<div class="ptTimeSelectHrAmPmCntr">'
					//~ +	'								<a class="ptTimeSelectHr ui-state-default" href="javascript: void(0);" '
					//~ +	'										style="display: block; width: 45%; float: left;">AM</a>'
					//~ +	'								<a class="ptTimeSelectHr ui-state-default" href="javascript: void(0);" '
					//~ +	'										style="display: block; width: 45%; float: left;">PM</a>'
					//~ +	'								<br style="clear: left;" /><div></div>'
					//~ +	'							</div>'
					+	'							<div class="ptTimeSelectHrCntr">'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">0</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">1</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">2</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">3</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">4</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">5</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">6</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">7</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">8</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">9</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">10</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">11</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">12</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">13</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">14</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">15</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">16</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">17</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">18</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">19</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">20</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">21</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">22</a>'
					+	'								<a class="ptTimeSelectHr" href="javascript: void(0);">23</a>'
					+	'								<br style="clear: left;" /><div></div>'
					+	'							</div>'
					+	'						</div>'
					+	'					</div>'
					+	'					<div style="width: 50%; float: left;">'
					+	'						<div class="ui-widget-content ptTimeSelectRightPane">'
					+	'							<div class="ptTimeSelectMinCntr">'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">00</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">05</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">10</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">15</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">20</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">25</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">30</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">35</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">40</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">45</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">50</a>'
					+	'								<a class="ptTimeSelectMin" href="javascript: void(0);">55</a>'
					+	'								<br style="clear: left;" /><div></div>'
					+	'							</div>'
					+	'						</div>'
					+	'					</div>'
					+	'				</div>'
					+	'			</div>'
					+	'			<div style="clear: left;"></div>'
					+	'		</div>'
					+	'		<div id="ptTimeSelectSetButton">'
					+	'			<a href="javascript: void(0);" onclick="jQuery.ptTimeSelect.setTime()"'
					+	'					onmouseover="jQuery(this).removeClass(\'ui-state-default\').addClass(\'ui-state-hover\');" '
					+	'						onmouseout="jQuery(this).removeClass(\'ui-state-hover\').addClass(\'ui-state-default\');"'
					+	'						class="ui-state-default">'
					+	'				SET'
					+	'			</a>'
					+	'			<br style="clear: both;" /><div></div>'
					+	'		</div>'
					+	'		<!--[if lte IE 6.5]>'
					+	'			<iframe style="display:block; position:absolute;top: 0;left:0;z-index:-1;'
					+	'				filter:Alpha(Opacity=\'0\');width:3000px;height:3000px"></iframe>'
					+	'		<![endif]-->'
					+	'	</div></div>'
				);

				var e = jQuery('#ptTimeSelectCntr');

				// Add the events to the functions
				e.find('.ptTimeSelectMin')
					.bind("click", function(){
						jQuery.ptTimeSelect.setMin($(this).text());
	 				});

				e.find('.ptTimeSelectHr')
					.bind("click", function(){
						jQuery.ptTimeSelect.setHr($(this).text());
	 				});

				$(document).mousedown(jQuery.ptTimeSelect._doCheckMouseClick);
				$(document).keypress(jQuery.ptTimeSelect._doCheckKeyPress);
			}//end if
		}
	);
}(); /* jQuery.ptTimeSelectInit() */


/***********************************************************************
 * METHOD: jQuery.ptTimeSelect.setHr(h)
 * 		Sets the hour selected by the user on the popup.
 *
 *
 * PARAMS:
 *
 * 		h -	[int] interger indicating the hour. This value is the same
 * 			as the text value displayed on the popup under the hour.
 * 			This value can also be the words AM or PM.
 *
 *
 * RETURN:
 *
 * 		none
 */
jQuery.ptTimeSelect.setHr = function(h) {
	if (	h.toLowerCase() == "am"
		||	h.toLowerCase() == "pm"
	) {
		jQuery('#ptTimeSelectUserSelAmPm').empty().append(h);
	}
	else
	{
		jQuery('#ptTimeSelectUserSelHr').empty().append(h);
	}
}/* END setHr() function */

/***********************************************************************
 * METHOD: jQuery.ptTimeSelect.setMin(m)
 * 		Sets the minutes selected by the user on the popup.
 *
 *
 * PARAMS:
 *
 * 		m -	[int] interger indicating the minutes. This value is the same
 * 			as the text value displayed on the popup under the minutes.
 *
 *
 * RETURN:
 *
 * 		none
 */
jQuery.ptTimeSelect.setMin = function(m) {
	jQuery('#ptTimeSelectUserSelMin').empty().append(m);
}/* END setMin() function */

/***********************************************************************
 * METHOD: jQuery.ptTimeSelect.setTime()
 * 		Takes the time defined by the user and sets it to the input
 * 		element that the popup is currently opened for.
 *
 *
 * PARAMS:
 *
 * 		none.
 *
 *
 * RETURN:
 *
 * 		none
 */
jQuery.ptTimeSelect.setTime = function() {
	var tSel = jQuery('#ptTimeSelectUserSelHr').text()
				+ ":"
				+ jQuery('#ptTimeSelectUserSelMin').text()
				//~ + " "
				//~ + jQuery('#ptTimeSelectUserSelAmPm').text()
	jQuery(".isPtTimeSelectActive").val(tSel);
	this.closeCntr();

}/* END setTime() function */

/***********************************************************************
 * METHOD: jQuery.ptTimeSelect.openCntr()
 * 		Displays the time definition area on the page, right below
 * 		the input field.  Also sets the custom colors/css on the
 * 		displayed area to what ever the input element options were
 * 		set with.
 *
 * PARAMS:
 *
 * 		uId	-	STRING. Id of the element for whom the area will
 * 				be displayed. This ID was created when the
 * 				ptTimeSelect() method was called.
 *
 * RETURN:
 *
 * 		nothing.
 *
 */
jQuery.ptTimeSelect.openCntr = function (ele) {
	jQuery.ptTimeSelect.closeCntr()
	jQuery(".isPtTimeSelectActive").removeClass("isPtTimeSelectActive");
	var cntr			= jQuery("#ptTimeSelectCntr");
	var i				= jQuery(ele).eq(0).addClass("isPtTimeSelectActive");
	var opt				= i.data("ptTimeSelectOptions");
	var style			= i.offset();
	style['z-index']	= opt.zIndex;
	style.top			= (style.top + i.outerHeight());
	if (opt.containerWidth) {
		style.width = opt.containerWidth;
	}
	if (opt.containerClass) {
		cntr.addClass(opt.containerClass);
	}
	cntr.css(style);
	var hr	= 1;
	var min	= '00';
	var tm	= 'AM';
	if (i.val()) {
		var re = /([0-9]{1,2}).*:.*([0-9]{2}).*(PM|AM)/i;
		var match = re.exec(i.val());
		if (match) {
			hr	= match[1] || 1;
			min	= match[2] || '00';
			tm	= match[3] || 'AM';
		}
	}
	cntr.find("#ptTimeSelectUserSelHr").empty().append(hr);
	cntr.find("#ptTimeSelectUserSelMin").empty().append(min);
	cntr.find("#ptTimeSelectUserSelAmPm").empty().append(tm);
	cntr.find(".ptTimeSelectTimeLabelsCntr .ptTimeSelectLeftPane")
		.empty().append(opt.hoursLabel);
	cntr.find(".ptTimeSelectTimeLabelsCntr .ptTimeSelectRightPane")
		.empty().append(opt.minutesLabel);
	cntr.find("#ptTimeSelectSetButton a").empty().append(opt.setButtonLabel);
	if (opt.onBeforeShow) {
		opt.onBeforeShow(i, cntr);
	}
	cntr.slideDown("fast");

}/* END openCntr() function */

/***********************************************************************
 * METHOD: jQuery.ptTimeSelect.closeCntr()
 * 		Closes (hides it) the popup container.
 *
 * PARAMS:
 *
 * 		@param {Object} i	-	Optional. The input field for which the
 * 								container is being closed.
 *
 * RETURN:
 *
 * 		none
 *
 */
jQuery.ptTimeSelect.closeCntr = function(i) {
	if ($("#ptTimeSelectCntr:visible").length) {
		jQuery('#ptTimeSelectCntr')
			.slideUp("fast", function(){
				jQuery('#ptTimeSelectCntr').removeClass().css("width", "");
				if (!i) {
					i = $(".isPtTimeSelectActive");
				}
				if (i) {
					var opt = i.removeClass("isPtTimeSelectActive")
								.data("ptTimeSelectOptions");
					if (opt && opt.onClose) {
						opt.onClose(i);
					}
				}
			});
	}
	return;
} /* END setTime() function */


jQuery.ptTimeSelect._doCheckMouseClick = function(ev){
	if (!$("#ptTimeSelectCntr:visible").length) {
		return;
	}
	if (!jQuery(ev.target).closest("#ptTimeSelectCntr").length){
		jQuery.ptTimeSelect.closeCntr();
	}

}/* jQuery.ptTimeSelect._doCheckMouseClick */


jQuery.ptTimeSelect._doCheckKeyPress = function(ev){
	if (!$("#ptTimeSelectCntr:visible").length)
	{
		return;
	}
	else if (13 == ev.keyCode)
	{
		jQuery.ptTimeSelect.setTime();
		jQuery.ptTimeSelect.closeCntr();
		return false;
	}

}/* jQuery.ptTimeSelect._doCheckMouseClick */


/***********************************************************************
 * METHOD: $(ele).ptTimeSelect()
 * 	Attaches a ptTimeSelect widget to each matched element. Matched
 * 	elements must be input fields that accept a values (input field).
 * 	Each element, when focused upon, will display a time selection
 * 	popoup where the user can define a time.
 *
 * PARAMS:
 *
 * 	@param {OBJECT}	opt -	(Optional) An object with the options for
 * 							the time selection widget.
 *
 * OPTIONS:
 *
 * 	containerClass	-	String. A class to be assocated with the popup widget.
 * 						(default: none)
 * 	containerWidth	-	String. Css width for the container. (default: none)
 * 	hoursLabel		-	String. Label for the Hours. (default: Hours)
 * 	minutesLabel	-	String. Label for the Mintues. (default: Minutes)
 * 	setButtonLabel	-	String. Label for the Set button. (default: SET)
 * 	popupImage		-	String. The html element (ex. img or text) to be
 * 						appended next to each input field and that will display
 * 						the time select widget upon click.
 * 	zindex			-	Int. Interger for the popup widget z-index.
 * 	onBeforeShow	-	Function. Function to be called before the widget is
 * 						made visible to the user. Function is passed 2 arguments:
 * 						1) the input field as a jquery object and 2) the popup
 * 						widget as a jquery object.
 * 	onClose			-	Function. Function to be called after closing the
 * 						popup widget. Function is passed 1 argument: the input
 * 						field as a jquery object.
 * 	onFocusDisplay	-	Boolean. True or False indicating if popup is auto
 * 						displayed upon focus of the input field. (default:true)
 *
 *
 * RETURNS:
 *
 * 		- @return {object} jQuery
 *
 *
 * EXAMPLE:
 *
 * 	|		$('#fooTime').ptTimeSelect();
 *
 */
jQuery.fn.ptTimeSelect = function (opt) {
	this.each(function(){
		if(this.nodeName.toLowerCase() != 'input') return;
		var e = jQuery(this);
		if (e.hasClass('hasPtTimeSelect')){
			return this;
		}
		var thisOpt = {};
		thisOpt = $.extend(thisOpt, jQuery.ptTimeSelect.options, opt);
		e.addClass('hasPtTimeSelect').data("ptTimeSelectOptions", thisOpt);

		//Wrap the input field in a <div> element with
		// a unique id for later referencing.
		if (thisOpt.popupImage || !thisOpt.onFocusDisplay) {
			var img = jQuery(
						'<span>&nbsp;</span><a href="javascript:" onclick="'
					+	'jQuery.ptTimeSelect.openCntr(jQuery(this).data(\'ptTimeSelectEle\'));">'
					+	thisOpt.popupImage + '</a>'
				)
				.data("ptTimeSelectEle", e);
			e.after(img);
		}
		if (thisOpt.onFocusDisplay){
			e.focus(function(){
				jQuery.ptTimeSelect.openCntr(this);
			});
		}
		return this;
	});
}/* End of jQuery.fn.timeSelect */


/***********************************************************************
 * SECTION: HTML INSETED INTO DOM
 * 	The only html created on the page is the popup window widget. For
 * 	details on the structure of this element see
 * 	<jQuery.ptTimeSelect._ptTimeSelectInit()>
 *
 */


