<?php
/*
@classinfo  maintainer=kristo
*/
class virus_scanner extends class_base
{
	function virus_scanner($arr = array())
	{
		$this->init();
	}

	function scan_file($file)
	{
		$type = "_do_scan_file_".$this->cfg["type"];
		$ret = $this->$type($file);
		return $ret;
	}

	function _do_scan_file_clamscan($file)
	{
		$f = $this->cfg["path"];
		$cmd = "$f --stdout --no-summary \"$file\"";
		$res = trim(shell_exec($cmd));
		$len = strlen($res);
		if ($res[$len-1] == "K" && $res[$len-2] == "O")
		{
			return false;
		}
		list($file, $vir) = explode(":", $res);
		return trim($vir);
	}
}
?>
