<?php

/**
	@comment

		uses the base analyzer to create a simple analysis that just lists functions and their parameters
		and writes these to the database

**/

class docgen_analyzer_simple_db_writer extends aw_code_analyzer
{
	function docgen_analyzer_simple_db_writer()
	{
		$this->init("core/docgen");
	}

	function analyze_file($file, $is_fp = false)
	{
		return parent::analyze_file($file, $is_fp);
	}
}
