<?php

interface request_startup
{
	/** This will get called in the beginning if the aw request and should initialize things that this class needs
		@attrib api=1
	**/
	function request_startup();
}

?>
