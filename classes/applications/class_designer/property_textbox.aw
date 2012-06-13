<?php

// property_textbox.aw - Element - tekstikast
/*

@classinfo no_comment=1

@default table=objects
@default group=general

@property ord type=textbox size=2 field=jrk
@caption Jrk

@default field=meta
@default method=serialize

@property size type=textbox size=2 datatype=int
@caption Pikkus

@property maxlength type=textbox size=2 datatype=int
@caption Max. pikkus

*/

class property_textbox extends class_base
{
	function property_textbox()
	{
		$this->init(array(
			"clid" => CL_PROPERTY_TEXTBOX
		));
	}
}
