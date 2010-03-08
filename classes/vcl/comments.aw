<?php
// comments VCL component

// what kind of forms do I need?
// 1. uid, title of comment, comment - not implemented
// 2. user, email, title of comment, comment - not imlemented
// 4. user, profession, title of comment, comment - not implemented
// 3. uid, comment - for logged in users - implemented
/*
@classinfo  maintainer=kristo
*/
class comments extends class_base
{
	function comments()
	{
		$this->init("");
	}

	// prop['only_form'] - returns only form, no comment list
	// prop['no_form'] - the opposite
	// prop['no_heading']
	// prop['textarea_rows'] and prop['textarea_cols']
	function init_vcl_property($arr)
	{
		$prop = &$arr["property"];
		$prname = isset($prop["name"]) ? $prop["name"] : "comments";
		$rv = array();

		$this->obj = $arr["obj_inst"];
		// alright, It seems that I need another way to to initialize this object
		// comments for an image get saved under the image itself
		if (is_object($this->obj))
		{
			$oid = $this->obj->id();
		};

		$parent = !empty($prop["use_parent"]) ? $prop["use_parent"] : $oid;

		if (empty($prop['only_form']))
		{
			$fcg = get_instance(CL_COMMENT);
			$comms = $fcg->get_comment_list(array(
				"parent" => $parent,
				"sort_by" => isset($prop["sort_by"]) ? $prop["sort_by"] : null,
			));
			$pager = $this->pager(array(
				"total" => count($comms),
				"onpage" => 20,
			));
			$res = "";
			if (!empty($prop["heading"]))
			{
				$res .= "<h2>" . $prop["heading"] . "</h2>";
			}
			elseif (empty($prop["no_heading"]) && is_object($this->obj))
			{
				$res .= "<h2>" . $this->obj->name() . "</h2>";
			}

			$res .= count($comms) . " ".t("kommentaari")."<br><br>";
			$res .= "$pager<br><br>";
			$c = 0;
			foreach($comms as $row)
			{
				$c++;
				if ($c >= $this->from && $c <= $this->to)
				{
					$author = empty($row["uname"]) ? $row["createdby"] : $row["uname"];
					$res .= "<p><b>" . $author . "</b>, " . aw_locale::get_lc_date($row["created"], aw_locale::DATETIME_SHORT_FULLYEAR) . "<br>";
					$res .= nl2br(create_links($row["commtext"]));
					if(!empty($prop["edit"]))
					{
						$baseurl = aw_ini_get("baseurl");
						$res .= "<br>" . html::href(array(
							"url" => str_replace($baseurl, $baseurl."/automatweb", $this->mk_my_orb("change", array(
								"id" => $row["oid"],
								"return_url" => get_ru()
							), CL_IMAGE)),
							"caption" => t("Muuda"),
						)) . " " . html::href(array(
							"url" => $this->mk_my_orb("del_comment", array(
								"id" => $row["oid"],
								"return_url" => get_ru()
							), CL_IMAGE),
							"caption" => t("Kustuta"),
						));
					}
					$res .=  "</p>";
				};
			};
			$pr1 = $prop;
			$pr1["type"] = "text";
			$pr1["value"] = $res;
			$pr1["name"] = $prname . "[list]";
			$rv[$prname . "_list"] = $pr1;
		}

		if (!empty($prop["no_form"]) or !empty($arr["view"]))
		{
			return $rv;
		}

		$cols = empty($prop['textarea_cols']) ? 60 : $prop['textarea_cols'];
		$rows = empty($prop['textarea_rows']) ? 10 : $prop['textarea_rows'];
		$rv2 = array(
			$prname . "_capt2" => array(
				"type" => "text",
				"subtitle" => 1,
				"value" => t("Lisa kommentaar"),
				"name" => $prname . "[capt2]",
			),
			$prname . "_capt" => array(
				"type" => "text",
				"caption" => t("Kasutaja"),
				"value" => "<b>" . aw_global_get("uid") . "</b>",
				"name" => $prname . "[capt]",
			),
			$prname . "_comment" => array(
				"type" => "textarea",
				"caption" => t("Kommentaar"),
				"name" => $prname . "[comment]",
				"cols" => $cols,
				"rows" => $rows,
			),
			$prname . "_obj_id" => array(
				"type" => "hidden",
				"caption" => t(""),
				"value" => $parent,
				"name" => $prname . "[obj_id]",
			),
		);
		$rv = !empty($prop["form_before_comments"]) ? ($rv2 + $rv) : ($rv + $rv2);
		return $rv;
	}

	function process_vcl_property($arr)
	{
		$comm = get_instance(CL_COMMENT);
		$commdata = $arr["prop"]["value"];
		if (strlen($commdata["comment"]))
		{
			$nc = $comm->submit(array(
				"parent" => $commdata["obj_id"],  // "parent" => $arr["obj_inst"]->id(),
				"commtext" => $commdata["comment"],
				"return" => "id",
			));
		}
	}

	function pager($arr)
	{
		$pages = $arr["total"] / $arr["onpage"];
		$res = array();
		$page = (int) automatweb::$request->arg("page");
		for ($i = 0; $i < $pages; $i++)
		{
			$from = $i * $arr["onpage"] + 1;
			$to = min(($i+1)*$arr["onpage"], $arr["total"]);
			if ($i == $page)
			{
				$res[] = "<strong>${from}-${to}</strong>";
				$this->from = $from;
				$this->to = $to;
			}
			else
			{
				$res[] = html::href(array(
						"url" => aw_url_change_var(array("page" => $i)),
						"caption" => $from . "-" . $to,
				));
			};
		};
		return join(" | ",$res);
	}
};
?>
