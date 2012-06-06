<?php

define("IS_PARSED", 1);
define("NOT_PARSED", 0);

class parser extends aw_template
{
	function parser()
	{
		$this->init("parser");
		$this->whitear = array(" ","\t","\n","\r","\$",";","\"","'","(",")");
	}

	function _get_class_list(&$files, $classdir)
	{
		if (strpos($classdir, "lang/") !== false)
		{
			return;
		}
		if (($dir = opendir($classdir)))
		{
			while (($file = readdir($dir)) !== false)
			{
				$fn = $classdir.$file;
				if ($file !== "." && $file != ".." && $file !== "parser.aw" && $file !== "timer.aw")
				{
					if (is_dir($fn))
					{
						$this->_get_class_list($files, $fn."/");
					}
					elseif (is_file($fn) && substr($file,strlen($file)-3) === ".aw")
					{
						$files[] = $fn;
					}
				}
			}
		}
	}

	/** user interface for the parser

		@attrib name=opts params=name default="1"
		@returns
		@comment

	**/
	function opts($arr)
	{
		extract($arr);
		$pd = aw_unserialize($this->get_cval("parser::class_status"));

		$this->read_adm_template("list_classes.tpl");

		$files = array();
		$this->_get_class_list($files, $this->cfg["classdir"]);

		sort($files);
		foreach($files as $file)
		{
			$this->vars(array(
				"name" => $file,
				"parsed" => ($pd[$file] == IS_PARSED ? LC_YES : LC_NO),
				"checked" => checked($pd[$file] == IS_PARSED)
			));
			$l.=$this->parse("LINE");
		}
		$this->vars(array(
			"LINE" => $l,
			"reforb" => $this->mk_reforb("submit_parse"),
			"list_check" => checked(true)
		));
		return $this->parse();
	}

	/** translate the variables from the parser ui to the parser func

		@attrib name=submit_parse params=name default="0"
		@returns
		@comment

	**/
	function submit_parse($arr)
	{
		extract($arr);
		return $this->orb_parse(array(
			"add_enter_func" => ($paction === "add_enter_func"),
			"remove_enter_func" => ($paction === "remove_enter_func"),
			"show_tree" => ($paction === "list_funcs"),
			"classes" => $parse
		));
	}

	/** goes through all files in class directory and if $add_enter_func = true, adds enter_function / exit_function calls

		@attrib name=parse params=name default="0"

		@param add_enter_func optional
		@param remove_enter_func optional
		@param classes optional

		@returns


		@comment
		and if remove_enter_func is true, removes the enter/exit_function calls
		if neither is, set, simply displays the list of functions for every class
		processes all files in classes array

	**/
	function orb_parse($arr)
	{
		extract($arr);
		aw_set_exec_time(AW_LONG_PROCESS);
		$pd = aw_unserialize($this->get_cval("parser::class_status"));
		$co = get_instance("config");

		if (is_array($classes))
		{
			$this->class_count = 0;
			$this->function_count = 0;
			$this->max_brace_level = 0;
			$this->max_brace_file = "";
			$this->max_brace_line = 0;

			foreach($classes as $class)
			{
				$_class = $class;//$this->cfg["classdir"]."/".$class;
				echo "loading and parsing file $_class <br />\n";
				flush();
				$this->do_parse($_class);
				$this->saved_trees[$class]["functions"] = $this->functions;
				$this->saved_trees[$class]["classes"] = $this->classes;
				$this->saved_trees[$class]["fun_returns"] = $this->fun_returns;
			}

			foreach($classes as $class)
			{
				$this->functions = $this->saved_trees[$class]["functions"];
				$this->classes = $this->saved_trees[$class]["classes"];
				$this->fun_returns = $this->saved_trees[$class]["fun_returns"];

				$_class = $class;//$this->cfg["classdir"]."/".$class;
				if ($show_tree)
				{
					$this->display_tree();
				}

				if ($add_enter_func)
				{
					echo "adding enter/exit_function calls to $_class <br />\n";
					$fc = $this->add_enter_func($_class);
					$pd[$class] = IS_PARSED;
					$_str = aw_serialize($pd);
					$this->quote($_str);
					$co->set_simple_config("parser::class_status", $_str);
				}
				else
				if ($remove_enter_func)
				{
					echo "removing enter/exit_function calls to $_class <br />\n";
					$fc = $this->remove_enter_func($_class);
					$pd[$class] = NOT_PARSED;
					$_str = aw_serialize($pd);
					$this->quote($_str);
					$co->set_simple_config("parser::class_status", $_str);
				}
			}
		}
		die(sprintf(t("class count = %s <br />\nfunction count = %s <br />\nMaximum brace depth = %s \n<br />max level in file %s on line %s <br />\n"), $this->class_count, $this->function_count, $this->max_brace_level, $this->max_brace_file, $this->max_brace_line));
	}

	////
	// !parses the file specified and puts the function list and other necessary parameters in $this->funlist
	function do_parse($file)
	{
		$f = fopen($file,"r");
		if (!$f)
		{
			echo "could not open file $file for reading, exiting <br />";
			die();
		}
		$fc = fread($f,filesize($file));
		fclose($f);

		$fc = $this->strip_comments($fc);
/*		echo "stripped comments: <br /><pre>";
		$lar = explode("\n",$fc);
		$cnt = 1;
		foreach($lar as $line)
		{
			echo "line $cnt: ".htmlentities($line)." \n";
			$cnt++;
		}*/

		$this->classes = array();
		$this->functions = array();
		$this->fun_returns = array();

		$this->find_funcs($fc,$file);


//		echo "\n\nclasses = ", var_dump($this->classes),"\n ";
//		echo "\n\nfunctions = ", var_dump($this->functions),"\n ";
		return $fc;
	}

	////
	// !this removes all comments from the text, leaving lines intact, so the line numbers will be accurate
	function strip_comments($fc)
	{
		// this must also ignore any text in strings!
		// use the parser for this as well, because that has string handling built in
		$this->p_init($fc);

//		echo "stripping comments <br />\n";
		while (!$this->p_eos())
		{
			$tok = $this->_p_get_token();
//			echo "token = $tok <br />\n";
			if (substr($tok,0,2) === "//")
			{
//				echo "onelinecomment <br />";
				$in_line = true;
				while ($in_line)
				{
					$_tok = $this->_p_get_token(true);	// strings inside comments must be ignored
//					echo "skipping <pre>",var_dump($_tok),"</pre> as part of oneline <br />";
					if ($_tok === false)
					{
						$in_line = false;
					}
					if ($_tok == "\n")
					{
						$in_line = false;
					}
				}
				$ret.="\n";
//				echo "end of oneline <br />";
			}
			elseif (substr($tok,0,1) === "#")
			{
//				echo "onelinecomment2 <br />";
				$in_line = true;
				while ($in_line)
				{
					$_tok = $this->_p_get_token(true);	// strings inside comments must be ignored
//					echo "skipping <pre>",var_dump($_tok),"</pre> as part of oneline2 <br />";
					if ($_tok === false)
					{
						$in_line = false;
					}
					if ($_tok == "\n")
					{
						$in_line = false;
					}
				}
				$ret.="\n";
//				echo "end of oneline2 <br />";
			}
			elseif (substr($tok,0,2) === "/*")
			{
				$in_comment = true;
				while ($in_comment)
				{
					$_tok = $this->_p_get_token(true); // strings inside comments must be ignored
					if ($_tok === "\n")
					{
						$ret.=$_tok;
					}
					else
					if ($_tok === false || $_tok === "*/")
					{
						$in_comment = false;
					}
				}
			}
			else
			{
				$ret.=$tok;
			}
		}
		return $ret;
	}

	////
	// !ok, this is the hard bit. it goes through the file and keeps track of the functions and classes it is in
	function find_funcs($fc,$file_name)
	{
		$this->p_init($fc);
		$cur_class = "__global";
		$in_class = false;
		$class_start_brace_level = 0;

		$cur_func = "";
		$in_func = false;
		$func_start_brace_level = 0;

		$brace_level = 0;
		while (!$this->p_eos())
		{
			$tok = $this->p_get_token();
			// find char before token, in case it is $, which must be used as a separator, but we must prepend it to the token if
			// it is there, to make sure variables with name s like $class do not get mistaken fo class starts
			if ($tok !== "{" && $tok !== "}")
			{
				$p_ch = $this->p_get_at_pos($this->p_get_p_pos()-(strlen($tok)+1));
				if ($p_ch === "\$")
				{
					$tok = "\$".$tok;
				}
			}
//			echo "tok = <pre>", htmlspecialchars(var_dump($tok)),"</pre> <br />";
			// process token
			if ($tok === "class")
			{
//				echo "tok == $tok , prev_tok = $prev_tok <br />";
				$cur_class = $this->p_get_token();
				if (!$cur_class)
				{
					die(sprintf(t("error - end of file after class in line %s <br />"), $this->p_get_line()));
				}
				$this->classes[$cur_class] = array("name" => $cur_class,"file" => $file_name, "start_line" => $this->p_get_line());
//				echo "found class $cur_class in line ".$this->p_get_line()." <br />";

				$try = $this->p_get_token();
//				echo "try after class = $try <br />";
//				flush();
				if ($try === "extends")
				{
					$ex_name = $this->p_get_token();
					$this->classes[$cur_class]["extends"] = $ex_name;
//					echo "class extends $ex_name <br />";
//					flush();
					$try = $this->p_get_token();
				}

				if ($try === "{")	// class starts
				{
					$in_class = true;
					$class_start_brace_level = $brace_level;
					$brace_level++;
					$this->class_count++;
//					echo "found brace, class starts <br />";
//					flush();
				}
			}
			elseif ($tok === "function")
			{
				// found function def
				// read function name and args
				$_tok = $this->p_get_token();
				$cur_func = $_tok;
//				echo "found function, name = $cur_func , line = ".$this->p_get_line()."<br />";
//				flush();
				$args = "";
				while (($_tok = $this->_p_get_token()) !== "("); // opening (
				$bracket_level = 1;
				// now. we must find the end of the argument string, can't just end it at ) , because it might contain array()
				// so we do bracket level counting to find the real end
				do
				{
					$_tok = $this->_p_get_token();
					if ($_tok === "(")
					{
						$bracket_level++;
					}
					else
					if ($_tok === ")")
					{
						$bracket_level--;
					}
					if ($bracket_level > 0)
					{
						$args.=$_tok;
					}
//					echo "got token = $_tok , brack_lev = $bracket_level <br />\n";
//					flush();
				} while ($bracket_level > 0);

//				echo "argstr = $args <br />";
//				flush();
				// now, opening {
				$_tok = $this->p_get_token();
				if ($_tok === "{")
				{
					$func_start_brace_level = $brace_level;
					$brace_level++;
					$in_function = true;
					$this->function_count++;
//					echo "found brace, func starts , name= $cur_func, level = $func_start_brace_level <br />";
				}
				$this->functions[$cur_class][$cur_func] = array("name" => $cur_func,"file" => $file_name, "start_line" => $this->p_get_line(),"args" => $args);
			}
			elseif ($tok === "{")
			{
				$brace_level++;
				if ($this->max_brace_level < $brace_level)
				{
					$this->max_brace_level = $brace_level;
					$this->max_brace_file = $file_name;
					$this->max_brace_line = $this->p_get_line();
				}
//				echo "found { in line ".$this->p_get_line()." , brace_level = $brace_level <br />";
			}
			elseif ($tok === "}")
			{
				// ok, brace close, we must figure out what this means.
				// check if class ends
				$brace_level--;
//				echo "found } in line ".$this->p_get_line()." , brace_level = $brace_level , class_start = $class_start_brace_level <br />";
				if ($brace_level == $class_start_brace_level && $in_class)
				{
					// class ends
					$this->classes[$cur_class]["end_line"] = $this->p_get_line();
//					echo "found end of class $cur_class at line ".$this->p_get_line()." <br />";
					$in_class = false;
					$cur_class = "__global";
				}
				else
				if ($brace_level == $func_start_brace_level && $in_function)
				{
					$this->functions[$cur_class][$cur_func]["end_line"] = $this->p_get_line();
//					echo "found end of function $cur_func at line ".$this->p_get_line()." <br />";
					$in_function = false;
					$cur_func = "";
				}
			}
			elseif ($tok === "return")
			{
				// return from function - mark this down so we know the function exit points for all functions
				$this->fun_returns[$cur_class][$cur_func][$this->p_get_line()] = $this->p_get_line();
			}
		}
	}

	function p_init($str)
	{
		$this->p_str = $str;
		$this->p_len = strlen($str);
		$this->p_pos = 0;
		$this->p_line = 1;
	}

	////
	// !returns true if the current parse stream is over
	function p_eos()
	{
		return $this->p_pos >= $this->p_len;
	}

	////
	// !gets the next character from the stream, returns false if end of stream
	function p_getch()
	{
		if (!$this->p_eos())
		{
			if ($this->p_str[$this->p_pos] === "\n")
			{
				$this->p_line++;
			}
//			echo "returninf ".$this->p_str[$this->p_pos]." , line = ".$this->p_line." <br />";
			return $this->p_str[$this->p_pos++];
		}
		return false;
	}

	function p_ungetch()
	{
		if ($this->p_pos > 0)
		{
			$this->p_pos--;
			if ($this->p_str[$this->p_pos] === "\n")
			{
				$this->p_line--;
			}
		}
	}

	////
	// !returns char at pos $pos in parser string
	function p_get_at_pos($pos)
	{
		return $this->p_str[$pos];
	}

	////
	// !returns the parser position in string
	function p_get_p_pos()
	{
		return $this->p_pos;
	}

	////
	// !if the char is a token separator, return true
	function p_is_sep($ch)
	{
		return in_array($ch,$this->whitear);
	}

	////
	// !returns the next token in the parse stream, tokens are separated by whitespace
	function p_get_token()
	{
		$tok = $this->_p_get_token();
		while($this->p_is_sep($tok))
		{
			$tok = $this->_p_get_token();
		}
		return $tok;
	}

	function _p_get_token($ignore_strings = false)
	{
		$tok = false;
		do {
			$ch = $this->p_getch();
			if ($ch === "{" || $ch === "}")
			{
				// brace always gets it's own token
				if ($tok != "")
				{
					// if we are in the middle of a token, end it, so the next time around we get just the brace
					$this->p_ungetch();
					return $tok;
				}
				else
				{
					// return just the brace as the token
					return $ch;
				}
			}

			if ($ch === false)	// if end of stream, return
			{
				return $tok;
			}

			if (!$this->p_is_sep($ch))	// if char is not separator, add it to token
			{
				$tok.=$ch;
			}
			else
			if ($tok == "")	// if it is, but token is empty, return separator, to avoid infinite loops
			{
				// quotes are a special case - strings form a complete token no matter what is contained
				if (($ch === "\"" || $ch === "'") && !$ignore_strings)
				{
					$this->p_ungetch();
					return $this->p_get_string();
				}
				else
				{
					return $ch;
				}
			}
			else
			{
				// is separator, we must return that next time around, so ungetch
				$this->p_ungetch();
			}
		} while (!$this->p_is_sep($ch));
		return $tok;
	}

	////
	// !this reads a string, assuming that the next char is the starting quote
	function p_get_string()
	{
		$str_start = $this->p_getch();
//		echo "enter p_get_string() <br />";
		if ($str_start !== "\"" && $str_start != "'")
		{
			die(sprintf(t("error in p_get_string (line: %s , pos = %s ) , called without string start <br />\n"), $this->p_get_line(), $this->p_pos));
		}
		$ret = $str_start;
//		echo "str_start = $str_start, line = ".$this->p_get_line()." \n";
		while (($ch = $this->p_getch()) !== false)
		{
			if ($ch === "\\")
			{
				// skip over escaped chars, they can't end strings
				$ret.=$ch;
				$ret.=$this->p_getch();
//				echo "found \\ , skipping , ret = ".htmlspecialchars($ret)." <br />";
			}
			else
			if ($ch == $str_start)
			{
//				echo "found eos $ch , ret = ".htmlspecialchars($ret)." <br />";
				return $ret.$ch;
			}
			else
			{
				$ret.=$ch;
//				echo "normal char, $ch adding, ret = ".htmlspecialchars($ret)." <br />";
			}
		}
//		echo "ch = <pre>", var_dump($ch),"</pre> <br />";
		die(sprintf(t("error in line %s file ended with open string! <br />\n"), $this->p_get_line()));
	}

	////
	// !returns the current line of the parser
	function p_get_line()
	{
		return $this->p_line;
	}

	////
	// !this adds enter_function calls to all functions in file, assuming that the file has been previously parsed
	// and the parse tree is in $this->classes and $this->functions
	function add_enter_func($file)
	{
		$this->proc_lines();

		$fc = $this->get_file(array("file" => $file));
		$fc_lines = explode("\n", $fc);

		$final = array();
		// go over all the lines in the file and if we hit a function start line or a function end line or a function return line,
		// add the necessary stuff to it
		foreach($fc_lines as $lnr => $line)
		{
			if (($fdat = $this->is_func_start_line($lnr)))
			{
				// add enter function
				$final[] = "\t\tenter_function(\"".$fdat["class"]."::".$fdat["func"]."\",array());";
			}

			if (($fdat = $this->is_func_end_or_return_line($lnr)))
			{
				if ($lnr != ($last_exit+1))	// this to avoid 2 exit funcs at the end of a function that returns something
				{
					// add exit function
					$final[] = "\t\texit_function(\"".$fdat["class"]."::".$fdat["func"]."\");";
					$last_exit = $lnr;
				}
			}

			$final[] = $line;
		}

		$fc_n = join("\n", $final);
		if ($fc_n != $fc)
		{
			/*$backup_name = $file.".".time().".aw-backup";
			echo "creating back-up of old class: $file to $backup_name <br />\n";
			if (!copy($file, $backup_name))
			{
				die(sprintf(t("copy of %s to %s failed, stopping <br />\n"), $file, $backup_name));
			}
			echo "saving new file as $file ....<br />\n";*/
			$this->put_file(array("file" => $file, "content" => $fc_n));
			chmod($file,0666);
			//echo "all done. <br /><br />\n\n";
		}
		else
		{
			echo "no changes made in class $file\n";
		}
	}

	////
	// !this returns the code for the function $func_name,
	// assuming that the parse tree is in $this->functions
	// and $lines contain the lines of code for the file in the parse tree
	function get_func_content($class_name, $func_name, $lines)
	{
		$fd = $this->functions[$class_name][$func_name];
		$ret = "";
		$cnt = $fd["start_line"];
//		echo "func_name = $func_name , cnt = $cnt , looping to ".$fd["end_line"]." , fd = <pre>", var_dump($fd)," </pre><br />";
		while ($cnt < ($fd["end_line"]-1))
		{
			$ret.=$lines[$cnt]."\n";
			$cnt++;
		}
		return $ret;
	}

	////
	// !this removes enter_function calls from all functions in file, assuming that the file has been previously parsed
	// and the parse tree is in $this->classes and $this->functions
	function remove_enter_func($file)
	{
		$fc = $this->get_file(array("file" => $file));
		$fc_o = $fc;

		foreach($this->functions as $class_name => $class_data)
		{
			foreach($class_data as $func_name => $func_data)
			{
				$fc = preg_replace("/\t\tenter_function\(\"".$class_name."::".$func_name."\",array\(\)\);\n/U","",$fc);
				$fc = preg_replace("/\t\texit_function\(\"".$class_name."::".$func_name."\"\);\n/U","",$fc);
			}
		}

		if ($fc_o != $fc)
		{
			$backup_name = $file.".".time().".aw-backup";
			echo "creating back-up of old class: $file to $backup_name <br />\n";
			if (!copy($file, $backup_name))
			{
				die(sprintf(t("copy of %s to %s failed, stopping <br />\n"), $file, $backup_name));
			}
			echo "saving new file as $file ....<br />\n";
			$this->put_file(array("file" => $file, "content" => $fc));
			echo "all done. <br /><br />\n\n";
		}
		else
		{
			echo "no changes made in class $file <br /><br />\n\n";
		}
	}

	function display_tree()
	{
		echo "<pre>\n";
		foreach($this->functions as $cl => $clfs)
		{
			echo "class $cl extends ".$this->classes[$cl]["extends"]." \n{\n";
			foreach($clfs as $fn => $fd)
			{
				echo "  function $fn($fd[args]) \n";
			}
			echo "} \n";
		}
		echo "</pre><br />\n";
		flush();
	}

	function proc_lines()
	{
		$this->start_lines = array();
		$this->end_lines = array();

		foreach($this->functions as $class => $cldat)
		{
			foreach($cldat as $fnname => $fndat)
			{
				$this->start_lines[$fndat["start_line"]] = array("class" => $class , "func" => $fnname);
			}
		}

		foreach($this->functions as $class => $cldat)
		{
			foreach($cldat as $fnname => $fndat)
			{
				$this->end_lines[$fndat["end_line"]-1] = array("class" => $class , "func" => $fnname);
			}
		}

		foreach($this->fun_returns as $class => $cldat)
		{
			foreach($cldat as $fnname => $fndat)
			{
				foreach($fndat as $eline)
				{
					$this->end_lines[$eline-1] = array("class" => $class , "func" => $fnname);
				}
			}
		}
	}

	function is_func_start_line($line)
	{
		return $this->start_lines[$line];
	}

	function is_func_end_or_return_line($line)
	{
		return $this->end_lines[$line];
	}
}
