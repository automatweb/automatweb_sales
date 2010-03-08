<?php

/**

	@classinfo  maintainer=kristo

	@comment 
		
		uses the base analyzer to create a simple analysis that just lists functions and their parameters
		and writes these to the database

**/
classload("core/aw_code_analyzer/aw_code_analyzer");
class docgen_analyzer_simple_db_writer extends aw_code_analyzer
{
	function docgen_analyzer_simple_db_writer()
	{
		$this->init("core/docgen");
	}

	function analyze_file($file, $is_fp = false)
	{
return parent::analyze_file($file, $is_fp);
		if (!$is_fp)
		{
			$fp = aw_ini_get("basedir")."/classes".$file;
		}
		else
		{
			$fp = $file;
		}
		$this->tokens = token_get_all(file_get_contents($fp));
		$this->data = array();
		$this->data["classes"] = array();
		$this->brace_level = 0;
		$this->in_class = false;
		$this->in_function = false;
		$this->cur_line = 1;
		$this->cur_file = $file;
		
		reset($this->tokens);
		while ($token = $this->get())
		{
			if (is_array($token))
			{
				list($id, $str) = $token;
				switch($id)
				{
					case T_CLASS:
						$this->handle_class_begin();
						break;

					case T_FUNCTION:
						$this->handle_function_begin();
						break;

					case T_DOC_COMMENT:
					case T_COMMENT:
						$this->last_comment = $str;
						$this->last_comment_line = $this->get_line();
						break;

					case T_DOLLAR_OPEN_CURLY_BRACES:
						$this->handle_brace_begin();
						break;

					case T_RETURN:
						$this->handle_return();
						break;
				}
			}
			else
			{
				switch($token)
				{
					case "{":
						$this->handle_brace_begin();
						break;
					
					case "}":
						$this->handle_brace_end();
						break;
				}
			}
		}

		return $this->data;
	}
}
?>
