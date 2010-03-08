/**
 * Convert a single file-input element into a 'multiple' input list
 *
 * Usage:
 *
 *   1. Create a file input element (no name)
 *      eg. <input type="file" id="first_file_element">
 *
 *   2. Create a DIV for the output to be written to
 *      eg. <div id="files_list"></div>
 *
 *   3. Instantiate a MultiSelector object, passing in the DIV and an (optional) maximum number of files
 *      eg. var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 3 );
 *
 *   4. Add the first element
 *      eg. multi_selector.addElement( document.getElementById( 'first_file_element' ) );
 *
 *   5. That's it.
 *
 *   You might (will) want to play around with the addListRow() method to make the output prettier.
 *
 *   You might also want to change the line 
 *       element.name = 'file_' + this.count;
 *   ...to a naming convention that makes more sense to you.
 * 
 * Licence:
 *   Use this however/wherever you like, just don't blame me if it breaks anything.
 *
 * Credit:
 *   If you're nice, you'll leave this bit:
 *  
 *   Class by Stickman -- http://www.the-stickman.com
 *      with thanks to:
 *      [for Safari fixes]
 *         Luis Torrefranca -- http://www.law.pitt.edu
 *         and
 *         Shawn Parker & John Pennypacker -- http://www.fuzzycoconut.com
 *      [for duplicate name bug]
 *         'neal'
 */
function MultiSelector( list_target, max )
{
	// cuz page refreshes without it in ie - body has refresher for some reason
	window.onresize = function () {return false;}

	// Where to write the list
	this.list_target = list_target;
	// How many elements?
	this.count = 0;
	// How many elements total - counting allready added items
	this.counter;
	// How many elements?
	this.id = 0;
	// Is there a maximum?
	if( max ){
		this.max = max;
	} else {
		this.max = -1;
	};
	this.input_element;
	
	/**
	 * Add a new file input element
	 */
	this.addElement = function( element ){

		// Make sure it's a file input element
		if( element.tagName == 'INPUT' && element.type == 'file' ){
			this.input_element = element;
		
			// Element name -- what number am I?
			element.name = 'file[]';
			element.id = 'fail_input_'+this.count;

			// Add reference to this object
			element.multi_selector = this;

			// What to do when a file is selected
			element.onchange = function(){

				// New file input
				var new_element = document.createElement( 'input' );
				new_element.type = 'file';

				// Add new element
				this.parentNode.insertBefore( new_element, this );
				
				// Apply 'update' to element
				this.multi_selector.addElement( new_element );

				// Update list
				this.multi_selector.addListRow( this );

				// Hide this: we can't use display:none because Safari doesn't like it
				this.style.position = 'absolute';
				this.style.left = '-1000px';

			};
			// If we've reached maximum number, disable input element
			if( this.max != -1 && this.count >= this.max || this.counter >= this.max ){
				element.disabled = true;
			};

			// File element counter
			this.count++;
			// Most recent element
			this.current_element = element;
			
		} else {
			// This can only be applied to file input elements!
			alert( 'Error: not a file input element' );
		};

	};
	
	this.refresh = function()
	{
			// If we've reached maximum number, disable input element
			if( this.counter >= this.max ){
				this.input_element.disabled = true;
			};
	}

	/**
	 * Add a new row to the list of files
	 */
	this.addListRow = function( element )
	{
		// Row div
		var new_row = document.createElement( 'div' );

		var new_tr = document.createElement("tr");
		var new_td_index = document.createElement("td");
		var new_td_filename = document.createElement("td");
		var new_td_button = document.createElement("td");
		
		new_td_index.innerHTML = ++this.counter;
		
		
		// Delete button
		var new_row_button = document.createElement( 'input' );
		new_row_button.type = 'button';
		new_row_button.value = 'kustuta';
		new_row_button.count = this.count;
		new_row_button.className = 'delete';

		// References
		new_row.element = element;

		// Delete function
		new_row_button.onclick= function(){

			// Remove element from form
			var div_form = document.getElementById("multifile_upload_form");
			var input_element = document.getElementById('fail_input_'+(this.count-2));
			div_form.removeChild (input_element);
			
			// Remove this row from the list
			//this.parentNode.parentNode.removeChild( this.parentNode );
			this.parentNode.parentNode.parentNode.removeChild (this.parentNode.parentNode);

			// Decrement counter
			multi_selector.count--
			multi_selector.counter--;

			// Re-enable input element (if it's disabled)
			//this.parentNode.element.multi_selector.current_element.disabled = false;
			multi_selector.current_element.disabled = false;
			
			// Appease Safari
			//    without it Safari wants to reload the browser window
			//    which nixes your already queued uploads
			return false;
		};

		// Set row value
		new_row.innerHTML = element.value;
		
		// Add button
		new_td_button.appendChild( new_row_button );
		new_td_filename.appendChild(new_row);
		new_tr.appendChild(new_td_index);
		new_tr.appendChild(new_td_filename);
		new_tr.appendChild(new_td_button);
	
		// Add it to the list
		this.list_target.appendChild( new_tr );
		this.refresh();
	};

};

function multifile_delete(id)
{
	var check = confirm("Oled kindel?");
	if(check){
		var XMLHttpRequestObject = false;
		
		if (window.XMLHttpRequest) {
			XMLHttpRequestObject = new XMLHttpRequest();
		} else if (window.ActiveXObject) {
			XMLHttpRequestObject = new
			ActiveXObject("Microsoft.XMLHTTP");
		}
		
		if(XMLHttpRequestObject) {
			XMLHttpRequestObject.open("GET", '/orb.aw?class=multifile_upload&action=ajax_delete_obj&id='+id);
			XMLHttpRequestObject.onreadystatechange = function()
			{
				if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
					//callback(XMLHttpRequestObject.responseText);
					parentDiv = document.getElementById("multifile_upload_files_list");
					Div = document.getElementById("multifile_"+id);
					parentDiv.removeChild (Div);
					delete XMLHttpRequestObject;
					XMLHttpRequestObject = null;
					multi_selector.count--
					multi_selector.counter--;
					multi_selector.current_element.disabled = false;
				}
			}
			XMLHttpRequestObject.send(null);
		}
	}
}