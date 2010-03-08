<?php

interface site_search_content_group_interface
{
	/** This gets called when the user does a search. it should return search results
		@attrib api=1 params=name

		@param obj required type=object
			The site_search_content object

		@param str required type=string
			The string the user searched for

		@param group required type=oid
			The id of the search group object to fetch results for

		@param opts required type=array
			An array of search options the user set array { str => array { opt => [S_OPT_*] } }

		@param date optional type=array
			The date the user searched for

		@param field optional type=string
			where to search title or content

		@param keyword optionsl type=string
			The keyword to search for, defaults to empty

		@returns 
			An array of search results. This will be passed to the reaults display method
	**/
	function scs_get_search_results($arr);

	/** Displays the search results
		@attrib api=1 params=name

		@param results required type=array
			The result array returned from the scs_get_search_results method

		@param group required type=oid
			The id of the search group

		@param str required type=string
			The string the user searched for

		@returns
			the HTML to display to the user

	**/
	function scs_display_search_results($arr);
}

?>