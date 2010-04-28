<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/site_search/site_search_content_grp_fs.aw,v 1.8 2008/01/31 13:52:39 kristo Exp $
// site_search_content_grp_fs.aw - Otsingu failis&uuml;steemi indekseerija 
/*

@classinfo syslog_type=ST_SITE_SEARCH_CONTENT_GRP_FS relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default field=meta
@default method=serialize

@default group=general

	@property path type=textbox field=meta method=serialize
	@caption Kataloog, mida indekseerida
	
	@property short_name type=textbox field=meta method=serialize
	@caption L&uuml;hend
	
	@property meta_ctr type=relpicker reltype=RELTYPE_META_CTR field=meta method=serialize
	@caption Metaandmete kontroller
	
	@property i_status type=text store=no
	@caption Staatus

	@property run type=text store=no

@default group=regex

	@property title_regex type=textbox default="/<TITLE>(.*)<\/TITLE>/iUs"
	@caption Pealkirja regulaaravaldis

	@property content_regex type=textbox default=""
	@caption Sisu regulaarvaldis

@default group=indexer

	@property indexer_sleep type=textbox size=5
	@caption Mitu sekundit oodata lehtede vahel

	@property bg_run_always type=checkbox ch_value=1
	@caption Indekseerija k&auml;ib pidevalt

	@property rc_txt type=text subtitle=1
	@caption Kordus indekseerija k&auml;ivitamiseks

	@property recur_edit type=releditor reltype=RELTYPE_RECURRENCE use_form=emb rel_id=first
	@caption Automaatse impordi seadistamine

@groupinfo regex caption="Regulaarvaldised"
@groupinfo indexer caption="Indekseerija"

@reltype RECURRENCE value=1 clid=CL_RECURRENCE
@caption kordus

@reltype META_CTR value=2 clid=CL_FORM_CONTROLLER
@caption metaandmete kontroller

*/

classload("core/run_in_background");
class site_search_content_grp_fs extends run_in_background
{
	const AW_CLID = 976;

	function site_search_content_grp_fs()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_search/site_search_content_grp_fs",
			"clid" => CL_SITE_SEARCH_CONTENT_GRP_FS
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function bg_run_init($o)
	{
		$this->pages = array();
		classload("contentmgmt/site_search/parsers/parser_finder");

		$path = $o->prop("path");
		$this->path = $path;
		$this->queue = get_instance("core/queue");
		$this->queue->push($path);
		echo "after init, queue = ".dbg::dump($this->queue);
	}

	function bg_run_continue($o)
	{
		// restore queue
		if (count(safe_array($o->meta("stored_queue"))) < 2)
		{
			// if restored queue count is too small, start over.
			echo "restored queue too small, restarting <br>";
			return;
		}
		$this->queue->set_all($o->meta("stored_queue"));
		$this->pages = $this->make_keys($o->meta("stored_visited_pages"));
		
		echo "restored queue, items = ".$this->queue->count()." pages = ".count($this->pages)."<br>";
	}

	function bg_run_step($o)
	{
		$path = $this->queue->get();
		echo  "read from queue path $path <br>\n";
		flush();
		if ($path == "")
		{
		echo "ret for path = empty <br>\n";
		flush();
			return $this->queue->has_more() ? BG_OK : BG_DONE;
		}

		if (isset($this->pages[$path]))
		{
		echo "ret for page done <br>\n";
		flush();
			return $this->queue->has_more() ? BG_OK : BG_DONE;
		}
		$i = parser_finder::instance($path);
		if ($i === NULL)
		{
		echo "ret cause no parser for path <br>\n";
		flush();
			return $this->queue->has_more() ? BG_OK : BG_DONE;
		}
echo "loaded parser <br>\n";
flush();
		$this->pages[$path]["o"] =& $i;
		$page =& $this->pages[$path]["o"];
		if (get_class($i) != "ss_parser_dir")
		{
			if (!$this->_store_content($page, $o->id()) && get_class($i) != "ss_parser_file_list")
			{
			echo "ret for false from store <br>\n";
			flush();
				unset($this->pages[$path]);
				return;
			}
		}
		$p_cnt = 0;
		$paths = $page->get_links();
		echo "got links <br>\n";
		flush();
		foreach($paths as $path)
		{
			if (!isset($this->pages[$path]) && !$this->queue->contains($path) && $path[0] == "/")
			{
				$this->queue->push($path);
			$p_cnt++;
			if (($p_cnt % 1000) == 1)
			{
			echo "p_cnt = $p_cnt <br>\n";
			flush();
			}
			}
			
		}
		echo "pushed $p_cnt links to queue <Br>\n";
		flush();
		if ($o->prop("indexer_sleep") > 0)
		{
			sleep($o->prop("indexer_sleep"));
		}
echo "end step <br>\n";
flush();
		return $this->queue->has_more() ? BG_OK : BG_DONE;
	}

	function bg_checkpoint($o)
	{
	echo "checkpointing queue, ".dbg::dump(count($this->queue->get_all()));
		$o->set_meta("stored_queue", $this->queue->get_all());
		$o->set_meta("stored_visited_pages", array_keys($this->pages));
		echo "checkpoint done <br>\n";
		flush();
	}

	function bg_run_finish($o)
	{
		$o->set_meta("stored_visited_pages", "");
		$o->set_meta("stored_queue", "");
		$o->set_meta("bg_run_log", sprintf(t("Indekseerija k&auml;ivitati %s, l&otilde;petas edukalt %s.\nIndekseeriti %s lehte, mis sialdasid %s baiti teksti."), 
			date("d.m.Y H:i", $o->meta("bg_run_start")),
			date("d.m.Y H:i", time()),
			count($this->pages),
			number_format($this->size)
		));
		
		$this->db_query("DELETE FROM static_content WHERE site_id = ".$o->id()." AND created_by = 'ss_fs' AND last_modified < ".$o->meta("bg_run_start"));

		echo "all done, fetched ".count($this->pages)." files, containing ".$this->size." bytes of text <br>\n";
		flush();
	}

	function bg_run_get_log_entry($o)
	{
		$res  = sprintf(t("Indekseerija nimega %s alustas t&ouml;&ouml;d %s.\n"), $o->name(), date("d.m.Y H:i", $o->meta("bg_run_start")));
		$res .= sprintf(t("Hetkel on indekseeritud %s lehte.\n"), count($this->pages));
		$res .= sprintf(t("J&auml;rjekorras on %s lehte.\n"), $this->queue->count());
		$res .= sprintf(t("Viimati uuendati staatust %s.\n"), date("d.m.Y H:i"));

		return $res;
	}

	function bg_halt($o)
	{
		$o->set_meta("stored_visited_pages", "");
		$o->set_meta("stored_queue", "");
		$o->set_meta("bg_run_log", sprintf(t("Indekseerija k&auml;ivitati %s, peatati %s."), 
			date("d.m.Y H:i", $o->meta("bg_run_start")),
			date("d.m.Y H:i", time())
		));
	}

	function _store_content(&$page, $indexer_id)
	{
		$o = obj($indexer_id);

		$h_id = md5($page->get_url());
		$fc = $page->get_text_content($o);
		$modified = $page->get_last_modified();
		$title = $page->get_title($o);
		$fn = $page->get_url();
		$url = str_replace("automatweb/", "", $this->mk_my_orb("showdoc", array("id" => $indexer_id, "doc" => $h_id), "", false, false, "/"))."/".basename($fn);

		$fc = html_entity_decode($fc, ENT_COMPAT, "iso-8859-4");
		$title = html_entity_decode($title, ENT_COMPAT, "iso-8859-4");
		
		$this->quote(&$fc);
		$this->quote(&$title);
		$this->quote(&$url);
		$this->quote(&$fn);

		$fields = array(
			"content" => $fc,
			"modified" => $modified,
			"title" => $title,
			"last_modified" => time(),
			"url" => $url,
			"file_name" => $fn,
			"id" => $h_id,
			"created_by" => 'ss_fs',
			"site_id" => $indexer_id
		);
		
		if (is_oid($o->prop("meta_ctr")) && $this->can("view", $o->prop("meta_ctr")))
		{
			$m_ctr = obj($o->prop("meta_ctr"));
			$ctr_i = $m_ctr->instance();
			$res = $ctr_i->eval_controller_ref($m_ctr->id(), $h_id, $page, $page);
			if ($res == false)
			{
				return false;
			}
			else
			if (is_array($res))
			{
				foreach($res as $k => $v)
				{
					$fields[$k] = $v;
				}
			}
		}

		$this->size += strlen($fc);
		
		// see if we already got this hash-indexer-site_id copy and if we do, update it
		$cnt = $this->db_fetch_field("SELECT count(*) AS cnt FROM static_content WHERE id = '$h_id' AND created_by = 'ss_fs' AND site_id = '$indexer_id'", "cnt");
		if ($cnt > 0)
		{
			$sets = join(",", map2("%s = '%s'", $fields));
			$q = "
				UPDATE static_content SET 
					$sets
				WHERE
					id = '$h_id' AND created_by = 'ss_fs' AND site_id = '$indexer_id'
			";
		}
		else
		{
			$flds = join(",", array_keys($fields));
			$vals = join(",", map("'%s'", array_values($fields)));
			$q = "
				INSERT INTO static_content($flds) 
				VALUES($vals)
			";
		}
		$this->db_query($q);
		
		return true;
	}

	/**

		@attrib name=showdoc nologin="1"

		@param id required type=int acl=view
		@param doc required

	**/
	function showdoc($arr)
	{
		$this->quote(&$arr);
		$row = $this->db_fetch_row("SELECT * FROM static_content WHERE id = '$arr[doc]' and site_id = '$arr[id]'");
		if (substr($row["file_name"], 0, 4) == "http")
		{
			header("Location: ".$row["file_name"]);
			die();
		}
		if ($row && (is_readable($row["file_name"])))
		{
			$i = get_instance("core/aw_mime_types");
			header("Content-type: ".$i->type_for_file($row["file_name"]));
			readfile($row["file_name"]);
			die();
		}
		die(t("Sellist faili ei ole!"));
	}

        function scs_get_search_results($r)
        {
                $i = get_instance(CL_SITE_SEARCH_CONTENT);
//$GLOBALS["INTENSE_DUKE"] = 1;
                return $i->fetch_static_search_results(array(
                        "str" => $r["str"],
                        "no_lang_id" => true,
                        "site_id" => $r["group"]
                ));
        }

}
?>
