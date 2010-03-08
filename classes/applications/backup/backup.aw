<?php
/*
@classinfo no_comment=1 no_status=1 syslog_type=ST_BACKUP maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property folder type=server_folder_selector
	@caption Serveri kataloog, kuhu backupi fail kirjutatakse

	@property do_bup type=checkbox ch_value=1
	@caption Loo backup

*/
class backup extends class_base
{
	function backup()
	{
		$this->init(array(
			"tpldir" => "backup",
			"clid" => CL_BACKUP
		));
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];

		switch($prop["name"])
		{
			case "do_bup":
				if ($arr["obj_inst"]->prop("folder") == "")
				{
					return PROP_IGNORE;
				}
				break;
		}
	}

	function callback_post_save($arr)
	{
		if ($arr["obj_inst"]->prop("do_bup"))
		{
			$arr["obj_inst"]->set_prop("do_bup", 0);
			$arr["obj_inst"]->save();
			$this->do_backup($arr["obj_inst"]);
		}
	}

	private function do_backup($o)
	{
		extract($arr);
		ob_end_clean();
		$folder = $o->prop("folder");
		error::raise_if(!$folder, array(
			"id" => "ERR_NO_FOLDER",
			"msg" => t("backup::do_backup(): no server folder set for backup!")
		));

		aw_set_exec_time(AW_LONG_PROCESS);
		// ookay. first, the database dump.
		$tmpnam = aw_ini_get("server.tmpdir")."/".gen_uniq_id();
		mkdir($tmpnam,0777);
		
		$cmd = aw_ini_get("server.mysqldump_path")." --add-drop-table --user=".aw_ini_get("db.user")." --host=".aw_ini_get("db.host")." --password=".aw_ini_get("db.pass")." --quick ".aw_ini_get("db.base")." > ".$tmpnam."/db_dump.sql";
		echo "Creating backup - this might take a long time<br />\n";
		flush();
		echo "creating database dump ...<br />\n";
		flush();
		$res = `$cmd`;

		// now, backup aw code dir.
		$cmd = aw_ini_get("server.tar_path")." -c -z -C ".$this->cfg["basedir"]." -f ".$tmpnam."/aw_code.tar.gz ".$this->cfg["basedir"];
		echo "backing up AW code ...<br />\n";
		flush();
		$res = `$cmd`;

		// backup site dir
		$cmd = aw_ini_get("server.tar_path")." -c -z -C ".$this->cfg["site_basedir"]." -f ".$tmpnam."/site_code.tar.gz ".$this->cfg["site_basedir"];
		echo "backing up site code ...<br />\n";
		flush();
		$res = `$cmd`;

		// now pack them all together
		$bn = date("Y")."-".date("m")."-".date("d");
		$cmd = aw_ini_get("server.tar_path")." -c -z -C $tmpnam -f ".$folder."/backup-".$bn.".tar.gz ".$tmpnam;
		echo "creating backup file ...<br />\n";
		flush();
		$res = `$cmd`;

		echo "deleting temporary files...<br />\n";
		flush();
		// now delete tmp files
		unlink($tmpnam."/db_dump.sql");
		unlink($tmpnam."/aw_code.tar.gz");
		unlink($tmpnam."/site_code.tar.gz");
		rmdir($tmpnam);
		echo "finished! <br /><br />\n\n";
		echo "backup file created as ".$folder."/backup-".$bn.".tar.gz <br />\n";
		flush();
		die();
	}
}
?>
