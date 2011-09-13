<?php

abstract class aw_content_document
{
	public function __construct()
	{
	}

	/**
		@attrib api=1 params=pos
		@comment
		@returns string
		@errors
	**/
	public function render()
	{
		return "";
	}

	/**
		@attrib api=1 params=pos
		@returns string
	**/
	public function __toString()
	{
		return $this->render();
	}
}
