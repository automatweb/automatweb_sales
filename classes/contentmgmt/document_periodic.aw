<?php

namespace automatweb;
/*
@classinfo  maintainer=kristo
*/
  
classload("document");
class document_periodic extends document
{
	const AW_CLID = 29;

	function document_periodic()
	{
		$this->document();
	}
}
?>