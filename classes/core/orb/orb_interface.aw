<?php

/**
Defines required interface for classes ready to serve requests mediated by
AutomatWeb ORB module.
**/
interface orb_public_interface
{
	/**
		Sets request to be carried out/executed
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
		@errors none
	**/
	public function set_request(aw_request $request);
}

