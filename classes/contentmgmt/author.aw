<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_AUTHOR no_status=1 no_comment=1 maintainer=dragut

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property limit type=textbox 
@caption Mitu viimast
@comment Mitut viimast dokumenti n&auml;idata

@property only_active_period type=checkbox ch_value=1
@caption Ainult aktiivsest perioodist
@comment N&auml;ita dokumente ainult aktiivsest perioodist

*/

// esimene asi - n2itamisviis
// v6iks saada m22rata mitmest viimasest perioodist lugusid v6etakse?

class author extends class_base
{
	const AW_CLID = 301;

	const DEFAULT_LIMIT = 20;

	function author()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/author",
			"clid" => CL_AUTHOR
		));
	}

	function show($arr)
	{
		return $this->author_docs(array(
			"obj_inst" => new object($arr["id"]),
		));
	}

	////
	// !This will list all documents created by an author
	private function author_docs($arr)
	{
		$author = $arr['obj_inst']->prop("name");
		$limit = $arr['obj_inst']->prop("limit");
		$only_active_period = $arr['obj_inst']->prop("only_active_period");

		$this->read_template("show.tpl");

		// composing parameters for documents object_list
		$object_list_parameters = array(
			"class_id" => CL_DOCUMENT,
			"author" => $author,
			"sort_by" => "objects.created DESC",
		);
		
		// is there set a limit, how many documents should be displayed?
		if (!empty($limit) || $limit == "0")
		{
			$object_list_parameters['limit'] = $limit;
		}
		
		// if is set, that documents only from active period should be displayed
		if (!empty($only_active_period))
		{
			$object_list_parameters['period'] = aw_global_get("act_per_id");
		}

		// so lets get the documents:
		$documents = new object_list($object_list_parameters);

		// and parse the output:
		$retval = "";
		foreach ($documents->arr() as $document)
		{
			$document_id = $document->id();

			// so, document comments are not objects yet, so, the only way to get them, is via sql query
			$comments_count = $this->db_fetch_field("SELECT count(*) AS cnt FROM comments WHERE board_id = '$document_id'","cnt");
			$this->vars(array(
				"link" => obj_link($document_id),
				"title" => $document->name(),
				"comments_link" => $this->mk_my_orb("show_threaded", array("board" => $document_id), "forum"),
				"comments_count" => $comments_count,
			));

			// if there are comments, then parse the HAS_COMMENTS sub
			$has_comments  = "";
			if ($comments_count > 0)
			{
				$has_comments = $this->parse("HAS_COMMENTS");
			}
			$this->vars(array("HAS_COMMENTS" => $has_comments));

			$retval .= $this->parse("AUTHOR_DOCUMENT");

		}
		return $retval;
	}

	// This is used at least in crm_person class, so it cannot be removed
	/** Returns a list of documents for an author
		@attrib api=1 params=name

		@param author required type=string
			The author to search for

		@param limit optional type=int
			Limit documents, defaults to self::DEFAULT_LIMIT

		@param date optional type=int
			Find documents around the given date

		@returns
			array { array { prev => bool, next => bool }, array { doc_oid => array { docid => doc_oid, mod => timestamp, commcount => int } } }
	**/
	function get_docs_by_author($arr)
	{
		$filt = array(
			"class_id" => CL_DOCUMENT,
			"site_id" => array(),
			"lang_id" => array(),
			"author" => $arr["author"],
			"status" => STAT_ACTIVE,
			new obj_predicate_sort(array("created" => "desc"))
		);

		$_lim = !empty($arr["limit"]) ? $arr["limit"] : self::DEFAULT_LIMIT;
		if ($_lim)
		{
			$filt[] = new obj_predicate_limit($_lim + 1);
		}

		if (aw_ini_get("search_conf.only_active_periods"))
		{
			$pei = get_instance(CL_PERIOD);
			$plist = $pei->period_list(0,false,1);
			$filt["period"] = array_keys($plist);
		}

		if ($arr["date"])
		{
			$filt["doc_modified"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["date"]);
			// now I also have to figure out whether there is a document after this one
		}

		// ookey, now I need to implement look-ahead and look-back to determine whether
		// there are any documents to be shown in the future or in the past

		// if there is no date given, then we simply show last "limit" items
		//	there are no "next" items
		// 	get limit + 1 items, leave the last one out of the search results, but if it does
		//	exist, then I know that I have to display the "previous" link

		// if there is a date given, then show the last "limit" items starting from that
		// date and going backwards. But this means that I'll have to use timestamps as dates,
		// because otherwise I cannot give exact dates, can I?

		//	get limit +1 items, leave the last one 

		// if (int)$_REQUEST["date"] == $_REQUEST["date"] - use it as a timestamp then?

		// get documents from active periods only
		$ol = new object_list($filt);
		$ids = array();
		$c = 0;
		$has_prev = false;
		$max = $ol->count();
		foreach($ol->arr() as $o)
		{
			$c++;
			if ($c == $max && $max == ($_lim + 1))
			{
				$has_prev = $o->doc_modified;
			}
			else
			{
				$ids[$o->id()] = array(
					"docid" => $o->id(),
					"mod" => $o->doc_modified,
				);
			};
		};

		$nav = array();

		if ($arr["date"])
		{
			// nüüd on vaja teada, et kas järgmisi dokke on olemas või mitte ja kui on,
			// sii on vaja leida viimane nendest
			$filt["doc_modified"] = new obj_predicate_compare(OBJ_COMP_GREATER, $arr["date"]);
			$ol = new object_list($filt);
			$max = $ol->count();
			$last_mod = 0;
			foreach($ol->arr() as $o)
			{
				$last_mod = $o->doc_modified;
			};
			if ($last_mod)
			{
				$has_next = $last_mod;
			};
		};

		if ($has_prev)
		{
			$nav["prev"] = $has_prev;
		};
		if ($has_next)
		{
			$nav["next"] = $has_next;
		};

		if (sizeof($ids) > 0)
		{
			$idarr = join(",",array_keys($ids));
			$comm_q = "SELECT count(*) AS cnt,board_id FROM comments
						WHERE board_id IN ($idarr) GROUP BY board_id";
			$this->db_query($comm_q);
			while($row = $this->db_next())
			{
				$ids[$row["board_id"]]["commcount"] = $row["cnt"];
			};
		};
		return array($nav,$ids);
	}

}
?>