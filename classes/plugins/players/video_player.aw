<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_video_player master_index=brother_of master_table=objects index=aw_oid

@default table=aw_video_player
@default group=general

*/

class video_player extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "plugins/players/video_player",
			"clid" => CL_VIDEO_PLAYER
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_video_player" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_video_player` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_video_player", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
