<?php
// forum_comment.aw - foorumi kommentaar
/*

@default table=objects
@default group=general

@property name type=textbox
@caption Pealkiri

@default table=forum_comments

@property uname type=textbox
@caption Kasutaja

@property pname type=textbox
@caption Nimi

@property uemail type=textbox
@caption E-post

@property remember type=checkbox store=no
@caption J&auml;ta nimi ja e-post meelde

@property commtext type=textarea
@caption Kommentaar

@property ip type=textbox
@caption IP

@property commtype type=select
@caption T&uuml;&uuml;p

@classinfo syslog_type=ST_COMMENT no_status=1 relationmgr=yes maintainer=dragut

@reltype FORUM_IMAGE value=1 clid=CL_IMAGE
@caption Pilt

@tableinfo forum_comments index=id master_table=objects master_index=oid
*/

        /*
		mysql> describe forum_comments;
		+-------------+---------------------+------+-----+---------+-------+
		| Field       | Type                | Null | Key | Default | Extra |
		+-------------+---------------------+------+-----+---------+-------+
		| id          | bigint(20) unsigned |      | PRI | 0       |       |
		| comm_parent | bigint(20) unsigned |      |     | 0       |       |
		| uname       | varchar(255)        | YES  |     | NULL    |       |
		| pname       | varchar(255)        | YES  |     | NULL    |       |
		| uemail      | varchar(255)        | YES  |     | NULL    |       |
		| commtext    | text                | YES  |     | NULL    |       |
		| ip          | varchar(255)        | YES  |     | NULL    |       |
		+-------------+---------------------+------+-----+---------+-------+
        */

class forum_comment extends class_base
{
	function forum_comment()
	{
		$this->init(array(
			'tpldir' => 'forum',
			'clid' => CL_COMMENT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "commtype":
				$prop["options"] = array(
					"" => t("--vali--"),
					1 => t("Positiivne"),
					2 => t("Neutraalne"),
					3 => t("Negatiivne"),
				);
				break;

			case "uname":
				if (is_object($arr["obj_inst"]) && !is_oid($arr["obj_inst"]->id()))
				{
					$prop["value"] = $_COOKIE["aw_mb_name"];
					$this->dequote($prop["value"]);
				};
				break;

			case "pname":
				if (is_object($arr["obj_inst"]) && !is_oid($arr["obj_inst"]->id()))
				{
					$uid = aw_global_get("uid");

					if(!empty($uid))
					{
						$cl_users = get_instance(CL_USER);
						$p_o = $cl_users->get_person_for_uid($uid);

						if (is_object($p_o))
						{
							$prop["value"] = $p_o->name();
						}
					}
				}
				break;

			case "uemail":
				if (is_object($arr["obj_inst"]) && !is_oid($arr["obj_inst"]->id()))
				{
					$prop["value"] = $_COOKIE["aw_mb_mail"];
					$this->dequote($prop["value"]);

				};
				break;

			case "comment":
			case "ip":
				$retval = PROP_IGNORE;
				break;

		}
		return $retval;
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "remember":
				if (!empty($prop["value"]) && !headers_sent())
				{
					$t = time();
					setcookie("aw_mb_name",$arr["request"]["uname"],time()+24*3600*1000);
					setcookie("aw_mb_mail",$arr["request"]["uemail"],time()+24*3600*1000);
				};
				break;

			case "ip":
				if (empty($prop["value"]))
				{
					$prop["value"] = aw_global_get("REMOTE_ADDR");
				};
				break;
		};
		return $retval;
	}


	////
	// !Returns a list of comments
	function get_comment_list($arr)
	{
		if (empty($arr["parent"]))
		{
			return array();
		}
		//arr($arr);
		$clist = new object_list(array(
			"parent" => $arr["parent"],
			"class_id" => $this->clid,
			"period" => isset($arr["period"]) ? $arr["period"] : 0,
			"sort_by" => !empty($arr["sort_by"]) ? $arr["sort_by"] : "created",
			"site_id" => array()
		));
		//arr($clist);
		$retval = array();
		foreach($clist->arr() as $comment)
		{
			$pname = $comment->prop("pname");

			if (empty($pname))
			{
				$uid = $comment->createdby();

				if(!empty($uid))
				{
					$cl_users = get_instance(CL_USER);
					$p_o = $cl_users->get_person_for_uid($uid);

					if (is_object($p_o))
					{
						$pname = $p_o->name();
					}
				}
			}

			$row = $comment->properties();
			$row["created"] = $comment->created();
			$row["createdby"] = $comment->createdby();
			$row["modified"] = $comment->modified();
			$row["modifiedby"] = $comment->modifiedby();
			$row["pname"] = $pname;
			$row["oid"] = $comment->id();
			$retval[$comment->id()] = $row;
		};
		return $retval;
	}

	////
	// !Returns a number of comments under parent
	//   parent - commented object
	//   period -
	function get_comment_count($arr)
	{
		if (empty($arr["parent"]))
		{
			return array();
		}
		$clist = new object_list(array(
			"parent" => $arr["parent"],
			"class_id" => $this->clid,
			"period" => isset($arr["period"]) ? $arr["period"] : 0,
			"lang_id" => array(),
			"site_id" => array()
		));
		return $clist->count();
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ($table === "forum_comments")
		{
			ini_set("ignore_user_abort", "1");

			switch($field)
			{
				case "pname":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "VARCHAR(255)"
					));
					return true;

				case "commtype":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "int",
					));
					return true;
			}
		}
		return false;
	}
}
