<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_SITE_SEARCH_CONTENT_GRP_DB relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default field=meta
@default method=serialize
@default table=objects
@default group=general

	@property meta_ctr type=relpicker reltype=RELTYPE_META_CTR field=meta method=serialize
	@caption Metaandmete kontroller
	
	@property i_status type=text store=no
	@caption Staatus

	@property run type=text store=no

@default group=indexer

	@property indexer_sleep type=textbox size=5
	@caption Mitu sekundit oodata lehtede vahel

	@property bg_run_always type=checkbox ch_value=1
	@caption Indekseerija k&auml;ib pidevalt

	@property rc_txt type=text subtitle=1
	@caption Kordus indekseerija k&auml;ivitamiseks

	@property recur_edit type=releditor reltype=RELTYPE_RECURRENCE use_form=emb rel_id=first
	@caption Automaatse impordi seadistamine

@default group=ctr

	@property code_bg_run_init type=textarea rows=20 cols=80
	@caption bg_run_init

	@property code_bg_run_continue type=textarea rows=20 cols=80
	@caption bg_run_continue

	@property code_bg_run_step type=textarea rows=20 cols=80
	@caption bg_run_step

	@property code_bg_checkpoint type=textarea rows=20 cols=80
	@caption bg_checkpoint

	@property code_bg_run_finish type=textarea rows=20 cols=80
	@caption bg_run_finish

	@property code_bg_run_get_log_entry type=textarea rows=20 cols=80
	@caption bg_run_get_log_entry

	@property code_bg_halt type=textarea rows=20 cols=80
	@caption bg_halt


@groupinfo indexer caption="Indekseerija"
@groupinfo ctr caption="Kood"

@reltype RECURRENCE value=1 clid=CL_RECURRENCE
@caption kordus

@reltype META_CTR value=2 clid=CL_FORM_CONTROLLER
@caption metaandmete kontroller

*/

classload("core/run_in_background");
class site_search_content_grp_userdefined extends run_in_background
{
	const AW_CLID = 1200;

	function site_search_content_grp_userdefined()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_search/site_search_content_grp_userdefined",
			"clid" => CL_SITE_SEARCH_CONTENT_GRP_USERDEFINED
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "run":
				return $this->bg_run_get_property_control($arr);
				break;

			case "i_status":
				return $this->bg_run_get_property_status($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "bg_run_always":
				$this->bg_check_scheduler();
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function bg_run_init($o)
	{
		eval($o->prop("code_".__FUNCTION__));
	}

	function bg_run_continue($o)
	{
		eval($o->prop("code_".__FUNCTION__));
	}

	function bg_run_step($o)
	{
		eval($o->prop("code_".__FUNCTION__));
	}

	function bg_checkpoint($o)
	{
		eval($o->prop("code_".__FUNCTION__));
	}

	function bg_run_finish($o)
	{
		eval($o->prop("code_".__FUNCTION__));
	}

	function bg_run_get_log_entry($o)
	{
		eval($o->prop("code_".__FUNCTION__));
	}

	function bg_halt($o)
	{
		eval($o->prop("code_".__FUNCTION__));
	}

		function _store_content($arr, $indexer_id)
		{
			$o = obj($indexer_id);
			extract($arr);


			$h_id = md5($url);

			// rewrite entities in html
			$fc = html_entity_decode($text, ENT_COMPAT, "UTF-8");
			$title = html_entity_decode($title, ENT_COMPAT, "UTF-8");

			$fc = str_replace("\"", "", $fc);
			$title = str_replace("\"", "", $title);

			$fields = array(
				"content" => $fc,
				"modified" => $modified,
				"title" => $title,
				"last_modified" => time(),
				"url" => $url,
				"file_name" => $fn,
				"id" => $h_id,
				"created_by" => 'ss_ud',
				"site_id" => $indexer_id,
				"file_type" => $ct
			);

			$fields["content"] = iconv("UTF-8", "ISO-8859-4//IGNORE", $fields["content"]);
			$fields["title"] = iconv("UTF-8", "ISO-8859-4//IGNORE", $fields["title"]);

			$this->quote(&$fields["content"]);
			$this->quote(&$fields["content_lower"]);
			$this->quote(&$fields["title"]);
			$h_id = md5($fields["url"]);

			$this->size += strlen($fields["content"]);

			// replace spaces
			$fields["content"] = str_replace(chr(160), " ", $fields["content"]);
			$fields["content"] = preg_replace("/\s+/", " ", $fields["content"]);
			$fields["content_lower"] = mb_strtolower($fields["content"], "iso-8859-4");

			$fields["content"] = str_replace("&#160;", " ", $fields["content"]);
			$fields["content_lower"] = str_replace("&#160;", " ", $fields["content_lower"]);

			$fields["content"] = str_replace(chr(167), "ggparagg", $fields["content"]);
			$fields["content_lower"] = str_replace(chr(167), "ggparagg", $fields["content_lower"]);

			$fields["content"] = str_replace(chr(185), "ggprimgg", $fields["content"]);
			$fields["content_lower"] = str_replace(chr(185), "ggprimgg", $fields["content_lower"]);

			$fields["content"] = str_replace(chr(178), "ggpripgg", $fields["content"]);
			$fields["content_lower"] = str_replace(chr(178), "ggpripgg", $fields["content_lower"]);

			$this->db_query("LOCK TABLE static_content_temp READ");
			$this->db_query("LOCK TABLE static_content_temp WRITE");
			// see if we already got this hash-indexer-site_id copy and if we do, update it
			$cnt = $this->db_fetch_field("SELECT count(*) AS cnt FROM static_content_temp WHERE id = '$h_id'", "cnt");
			if ($cnt > 0)
			{
				$sets = join(",", map2("%s = '%s'", $fields));
				$q = "
				UPDATE static_content_temp SET 
				$sets
				WHERE
				id = '$h_id' 
				";
			}
			else
			{
				$flds = join(",", array_keys($fields));
				$vals = join(",", map("'%s'", array_values($fields)));
				$q = "
				INSERT INTO static_content_temp($flds) 
				VALUES($vals)
				";
			}
			$this->db_query($q);
			$this->db_query("UNLOCK TABLES");
			return true;
		}								
}
?>
