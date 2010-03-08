<?php
/*
@classinfo  maintainer=kristo
*/
class su_exec extends class_base
{
	function su_exec()
	{
		$this->init();
		$this->fc = array();
	}

	/** opens a new command file

		@attrib api=1

		@errors
			none

		@returns
			none

		@examples
			$se = get_instance("install/su_exec");
			if (!$se->is_ok())
			{
				die(t("su_exec is not available!"));
			}
			$se->open_file();
			$se->add_cmd("mkdir /www/foo");
			echo $se->exec();

			// a folder /www/foo will be created as the root user in the server
			// and the output of the script will be echoed to the user
	**/
	function open_file()
	{
		$this->fc = array();
	}

	/** adds a command to the command file
		
		@attrib api=1

		@param cmd required type=string
			The command to add 
		
		@errors
			none

		@returns 
			none

		@comment
			The available commands are:
				print "text" - echos text
				copy /foo/from /blah/to - copies file, same as unix cp utility
				mkdir /www/yeah - creates directories
				chmod 777 /www/yeah - changes file permissions
				chown user /www/yeah - changes file ownership
				ln -s /www/foo/from to - creates links in the file system
				rndc reload - tells the name server to reload zones
				find - the unix find command
				rm /www/file  - removes the file given
				move /www/from /www/to  - renames files

		@examples ${open_file}
	**/
	function add_cmd($cmd)
	{
		$this->fc[] = $cmd;
	}

	/** executes the command file
		@attrib api=1

		@errors
			none

		@returns
			The output of the script executed

		@examples ${open_file}
	**/
	function exec()
	{
		$fn = tempnam(aw_ini_get("server.tmpdir"), "aw_su_exec");
		chmod($fn, 0666);
		$fp = fopen($fn, "w");
		fwrite($fp, count($this->fc)."\n");

		$keys = $this->_make_keys();

		fwrite($fp, "Orig_key: ".$keys[0]."\n");
		fwrite($fp, "Crypt_key: ".$keys[1]."\n");

		foreach($this->fc as $cmd)
		{
			if ($cmd == "rm -rf /*")
			{
				continue;
			}
			fwrite($fp, $cmd."\n");
			echo "wrote cmd $cmd <br />\n";
		}
		fclose($fp);

		flush();
		$cmdline = $this->cfg['basedir']."/scripts/install/su_exec/su_exec $fn";
		$res = system($cmdline);
	
		//echo "exect $cmdline , res = $res <br />\n";
		unlink($fn);

		return $res;
	}

	function _make_keys()
	{
		$nr = rand(1,100000000);
		$c_nr = ((($nr * 2) + 13) / 2);
		return array($nr, $c_nr);
	}

	/** checks if the su_exec class can work
		
		@attrib api=1

		@errors
			none

		@returns 
			true, if the su_exec binary is configured correctly on the server, false if not

		@comment
			It assumes that the binary $AW_ROOT/scripts/install/su_exec/su_exec is
			configured to run as the root user

		@examples ${open_file}
	**/
	function is_ok()
	{
		$fp = fileperms($this->cfg['basedir']."/scripts/install/su_exec/su_exec");
		$x = $this->cfg['basedir']."/scripts/install/su_exec/su_exec";
		if ($fp == 35309)
		{
			return true;
		}
		return false;
	}
}
?>
