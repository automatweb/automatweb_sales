<?php
/*
@classinfo  maintainer=kristo
*/

class debug extends class_base
{
	function debug()
	{
		$this->init();
	}

	/**  
		
		@attrib name=syntaxcheck params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function syntaxcheck()
	{
		// include all the class files
		$clf = aw_ini_get("classdir");
		$this->_req_inc($clf);
	}

	function _req_inc($fld)
	{
		if ($dir = @opendir($fld)) 
		{
			while (($file = readdir($dir)) !== false) 
			{
				if (!($file == "." || $file == ".." || $file == "CVS" || $file == "fastcall_base.aw" || $file == "contact.aw" || $file == "pop3.aw" || $file == "translation.aw"))
				{
					if (!preg_match("/\.\#/",$file))
					{
						$fn = $fld."/".$file;
						if (is_dir($fn))
						{
							echo "recursing into $fn <br />\n";
							$this->_req_inc($fn);
						}
						else
						{
							echo "including $fn <br />\n";
							include_once($fn);
						}
					}
				}
			}
			closedir($dir);
		}
	}
}
?>
