<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@default group=general
@default field=meta
@default method=serialize

	@property default_player type=objpicker clid=CL_VIDEO_PLAYER
	@caption Vaikimisi player

*/

class video_gallery extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/gallery/video_gallery",
			"clid" => CL_VIDEO_GALLERY
		));
	}
}
