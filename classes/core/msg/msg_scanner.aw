<?php
/*
@classinfo  maintainer=kristo
*/

// this class generates the message dispatch tables from the class files

class msg_scanner extends class_base
{
	function msg_scanner()
	{
		aw_global_set("no_db_connection", true);
		$this->init(array(
			"no_db" => 1
		));
		$this->folder = AW_DIR."xml/msgmaps";
	}

	function scan()
	{
		// generate list of all class files in aw
		$parser = get_instance("core/aw_code_analyzer/parser");
		$files = array();
		$parser->_get_class_list(&$files, $this->cfg["classdir"]);
		$files[] = AW_DIR . "lib/defs.aw"; // temporary. until MSG_MAIL_SENT emit declaration moved from defs.aw

		// scan them for all dispatched / recieved messages
		list($messages, $recievers, $recievers_param) = $this->_scan_files($files);

		// check the maps for validity
		$this->_check_message_maps($messages, $recievers, $recievers_param);

		// generate one xml file for each message that lists the recievers of that message
		$this->_save_message_maps($messages, $recievers, $recievers_param);
	}

	function _scan_files($files)
	{
		$messages = array();
		$recievers = array();
		$recievers_param = array();
		foreach($files as $file)
		{
			$blen = strlen(AW_DIR."classes")+1;
			$class = substr($file, $blen, strlen($file) - (strlen(".".$this->cfg["ext"])+$blen));

			$fc = $this->get_file(array("file" => $file));
			if (preg_match_all("/EMIT_MESSAGE\((.*)\)/U",$fc, $mt, PREG_SET_ORDER))
			{
				foreach($mt as $m)
				{
					$messages[trim($m[1])] = trim($m[1]);
				}
			}

			if (preg_match_all("/HANDLE_MESSAGE\((.*),(.*)\)/U",$fc, $mt, PREG_SET_ORDER))
			{
				foreach($mt as $m)
				{
					if (isset($recievers[trim($m[1])][$class]))
					{
						die(sprintf(t("ERROR: function %s already defined as message handler\n       for message %s, can not define several recievers\n       for one message in the same class!\n\n"), $recievers[trim($m[1])][$class], $m[1]));
					}
					$recievers[trim($m[1])][$class] = trim($m[2]);
				}
			}

			if (preg_match_all("/HANDLE_MESSAGE_WITH_PARAM\((.*),(.*),(.*)\)/U",$fc, $mt, PREG_SET_ORDER))
			{
				foreach($mt as $m)
				{
					if (isset($recievers_param[trim($m[1])][$class][trim($m[2])]))
					{
						die(sprintf(t("ERROR: function %s already defined as message handler\n       for message %s with param %s, can not define several recievers\n       for one message with same param in the same class!\n\n"), $recievers[trim($m[1])][$class][trim($m[2])], $m[1], $m[2]));
					}
					$recievers_param[trim($m[1])][$class][trim($m[2])] = trim($m[3]);
				}
			}
		}
		return array($messages, $recievers, $recievers_param);
	}

	function _check_message_maps($messages, $recievers, $recievers_param)
	{
		foreach($recievers as $msg => $cldat)
		{
			if (!in_array($msg, $messages))
			{
				$clstr = join(",",array_keys($cldat));
				if (count(array_keys($cldat)) > 1)
				{
					$mul = "es:";
				}
				echo sprintf(t("ERROR: message %s is not defined, but recieved by class%s %s!\n\n"), $msg, $mul, $clstr);
				die();
			}

			foreach($cldat as $class => $handler)
			{
				$inst = get_instance($class);
				if (!method_exists($inst, $handler))
				{
					echo sprintf(t("ERROR: class %s defines function %s as message handler for message %s,\n       but the function does not exist in that class!\n\n"), $class, $handler, $msg);
					echo dbg::process_backtrace(debug_backtrace());
					die();
				}
			}
		}

		foreach($recievers_param as $msg => $cldat)
		{
			if (!in_array($msg, $messages))
			{
				$clstr = join(",",array_keys($cldat));
				if (count(array_keys($cldat)) > 1)
				{
					$mul = "es:";
				}
				echo sprintf(t("ERROR: message %s is not defined, but recieved by class%s %s!\n\n"), $msg, $mul, $clstr);
				die();
			}

			foreach($cldat as $class => $hdat)
			{
				foreach($hdat as $param => $handler)
				{
					$inst = get_instance($class);
					if (!method_exists($inst, $handler))
					{
						echo sprintf(t("ERROR: class %s defines function %s as message handler for message %s (with param %s),\n       but the function does not exist in that class!\n\n"), $class, $handler, $msg, $param);
						echo dbg::process_backtrace(debug_backtrace());
						die();
					}
				}
			}
		}
	}

	function _save_message_maps($messages, $recievers, $recievers_param)
	{
		$this->_delete_old_maps();

		foreach($messages as $msg)
		{
			// find all recievers for this message
			$m_recvs = new aw_array($recievers[$msg]);
			$r = array();
			foreach($m_recvs->get() as $class => $func)
			{
				$r[] = array("class" => $class, "func" => $func);
			}
			$m_recvs = new aw_array($recievers_param[$msg]);
			foreach($m_recvs->get() as $class => $fdat)
			{
				foreach($fdat as $param => $func)
				{
					$r[] = array("class" => $class, "func" => $func, "param" => $param);
				}
			}

			// serialize
			$xml = aw_serialize($r, SERIALIZE_XML);
			// write file
			$file = $this->folder."/".$msg.".xml";
			$this->put_file(array(
				"file" => $file,
				"content" => $xml
			));
			echo "\t.. generated $file\n";
		}
	}

	function _delete_old_maps()
	{
		$fs = array();
		if (($dir = @opendir($this->folder)))
		{
			while (($file = readdir($dir)) !== false)
			{
				$fn = $this->folder."/".$file;

				if (is_file($fn) && substr($file,strlen($file)-4) == ".xml")
				{
					if (!is_writable($fn))
					{
						die(sprintf(t("ERROR: no write access to file %s!\n\n"), $fn));
					}

					$fs[] = $fn;
				}
			}
		}
		else
		{
			die(sprintf(t("ERROR: folder %s where message maps are stored, does not exist!\n\n"), $folder));
		}

		foreach($fs as $fn)
		{
			if (!@unlink($fn))
			{
				die(sprintf(t("ERROR: no write access to file %s!\n\n"), $fn));
			}
		}
	}
}
?>
