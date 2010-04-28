<?php

namespace automatweb;


class site_diff_obj extends _int_object
{
	const AW_CLID = 1486;

	public function diff($arr)
	{
		$o = obj($this->id());

		$url_1 = parse_url($o->url_1);
		$url_2 = parse_url($o->url_2);

		// Index the original site
		$urls = $this->index_pages($o->url_1);
		foreach($urls as $urlo)
		{
			$urlc = str_replace($url_1["host"], $url_2["host"], $urlo);

			$f = fopen(aw_ini_get("site_basedir")."/files/site_diff_".$o->id()."_urlo.txt", "w");
			fwrite($f, file_get_contents($urlo));
			fclose($f);

			$f = fopen(aw_ini_get("site_basedir")."/files/site_diff_".$o->id()."_urlc.txt", "w");
			fwrite($f, file_get_contents($urlc));
			fclose($f);

			$diff = shell_exec("diff ".aw_ini_get("site_basedir")."/files/site_diff_".$o->id()."_urlo.txt ".aw_ini_get("site_basedir")."/files/site_diff_".$o->id()."_urlc.txt;");

			unlink(aw_ini_get("site_basedir")."/files/site_diff_".$o->id()."_urlo.txt");
			unlink(aw_ini_get("site_basedir")."/files/site_diff_".$o->id()."_urlc.txt");

			if($o->use_hack_diff)
			{
				$diff = $this->hack_diff($diff, $o);
			}

			if(strlen(trim($diff)) > 0)
			{
				$time = time();
				$hash = md5($urlo.$time);
				$path = aw_ini_get("site_basedir")."/files/site_diff_".$hash.".txt";
				$f = fopen($path, "w");
				fwrite($f, $diff);
				fclose($f);

				$diffs[] = array(
					"time" => $time,
					"urlo" => $urlo,
				);
				$diff_links[] = array(
					"urlo" => $urlo,
					"urlc" => $urlc,
					"urld" => $o->instance()->mk_my_orb("show_diff", array(
						"id" => $o->id(), 
						"url" => $o->url_1, 
						"url_diff" => $urlo,
						"time" => $time,
					)),
				);
			}
		}
		$diff_id = "site_diffs_".time();
		$o->set_meta($diff_id, array(
			"diffs" => $diffs,
			"url_1" => $o->url_1,
			"url_2" => $o->url_2,
		));
		aw_disable_acl();
		$o->save();
		aw_restore_acl();

		if(is_email($o->email))
		{
			$this->tell_me_its_ready($o, count($diffs) !== 0, $diff_links, $diff_id);
		}
	}

	public function index_pages($U, $only_same_host = true)
	{
		$url = parse_url($U);

		$this->urls = array($U);
		$this->host = $url["host"];
		$this->visited = array();

		$this->recursive_indexing(0);

		foreach($this->urls as $k => $v)
		{
			$url = parse_url($v);
			if($url["host"] != $this->host && $only_same_host)
			{
				unset($this->urls[$k]);
			}
		}

		$this->urls = array_unique($this->urls);
		sort($this->urls);

		return $this->urls;
	}

	private function recursive_indexing($depth)
	{
		if($depth > 2)
		{
			return false;
		}
		foreach($this->urls as $link)
		{
			if(!in_array($link, $this->visited))
			{
				$p = new ss_parser_html($link);
				$this->visited[] = $link;
				$this->urls = array_merge($this->urls, $p->get_links());
				$this->recursive_indexing($depth + 1);
			}
		}
	}

	/** It will check the UNIX diff and get rid of bunch of things:
		1) http://cv.post.ee/ and http://cv.post.d.struktuur.ee/ are the same!
		2)			http://cv.post.ee/orb.aw/class=image/action=show/fastcall=1/file=bfd4d5c1a57ffb90484a48551348fb14.jpg?rand=1052741
			and		http://cv.post.ee/orb.aw/class=image/action=show/fastcall=1/file=bfd4d5c1a57ffb90484a48551348fb14.jpg?rand=1823728
			are the same!

	**/
	private function hack_diff($diff, $o)
	{
		// Some conf before we get to it.
		$url_1 = parse_url($o->url_1);
		$old_host = $url_1["host"];
		$url_2 = parse_url($o->url_2);
		$new_host = $url_2["host"];

		$diff_arr = explode("\n", $diff);
		$diff_patches = array();
		$diff_patch = array();
		foreach($diff_arr as $dr)
		{
			// Number marks the beginning of a new difference 
			if(is_numeric(substr($dr, 0, 1)))
			{
				if(count($diff_patch) > 0)
				{
					$diff_patches[] = $diff_patch;
				}

				$diff_patch = array();
				$diff_patch["row"] = $dr;
				$side = "L";
			}
			// --- marks the 
			elseif($dr == "---")
			{
				$side = "R";
			}
			else
			{
				$diff_patch[$side][] = $dr;
			}
		}

		// Let's hack some! :P -kaarel
		foreach($diff_patches as $mk => $diff_patch)
		{
			// Can't hack it if they have different number of rows
			if(count($diff_patch["L"]) !== count($diff_patch["R"]))
			{
				continue;
			}
			foreach($diff_patch["L"] as $k => $v)
			{
				$unset = false;
				$left = substr($v, 2);
				$right = substr($diff_patch["R"][$k], 2);

				//	http://cv.post.ee/ and
				//	http://cv.post.d.struktuur.ee/ are the same!
				$right = str_replace($new_host, $old_host, $right);

				//		http://cv.post.ee/orb.aw/class=image/action=show/fastcall=1/file=bfd4d5c1a57ffb90484a48551348fb14.jpg?rand=1052741
				//	and	http://cv.post.ee/orb.aw/class=image/action=show/fastcall=1/file=bfd4d5c1a57ffb90484a48551348fb14.jpg?rand=1823728
				//	are the same!
				$left = preg_replace("/\?rand=[0-9]+/", "", $left);
				$right = preg_replace("/\?rand=[0-9]+/", "", $right);

				// If we found a way to hack it, remove it from the diff.
				if(strcmp($left, $right) === 0)
				{
					unset($diff_patches[$mk]["L"][$k]);
					unset($diff_patches[$mk]["R"][$k]);
				}
			}
		}

		// Put the diff back together
		$new_diff = "";
		foreach($diff_patches as $diff_patch)
		{
			$diff_tmp = $diff_patch["row"]."\n";
			if(count($diff_patch["L"]) === 0 && count($diff_patch["R"]) === 0)
			{
				continue;
			}
			foreach($diff_patch["L"] as $diff_left)
			{
				$diff_tmp .= $diff_left."\n";
			}
			$diff_tmp .= "---\n";
			foreach($diff_patch["R"] as $diff_right)
			{
				$diff_tmp .= $diff_right."\n";
			}
			$new_diff .= $diff_tmp;
		}
		return $new_diff;
	}

	private function tell_me_its_ready($o, $diff = false, $diff_links = array(), $diff_id)
	{
		$to = $o->email;

		$subject = t("Sinu saitide v&otilde;rdlus on tehtud!");

		$message = sprintf(t("Tervist!
		
V&otilde;rdlesin j&auml;rgmisi saite:
Originaal: %s
V&otilde;rreldav: %s

Tulemus: "), $o->url_1, $o->url_2);
		if($diff && $o->send_diff_links)
		{
			$message .= t("leidsin j&auml;rgmised erinevused:");
			foreach(safe_array($diff_links) as $diff_link)
			{
				$message .= "\n\n".$diff_link["urlo"]." vs ".$diff_link["urlc"]."\nDiff: ".$diff_link["urld"];
			}
		}
		elseif($diff)
		{
			$message .= sprintf(t("Leiti %u erinevust!\n"), count($diff_links));
			$message .= $o->instance()->mk_my_orb("change", array("id" => $o->id(), "group" => "history", "diff_id" => $diff_id));
		}
		else
		{
			$message .= t("ei leitud &uuml;htegi erinevust!");
		}
		$message .= "

Tervitades,
Sinu ".$o->name;

		$hack_chars = array("&auml;", "&otilde;", "&uuml;", "&ouml;");
		foreach($hack_chars as $hack_char)
		{
			$subject = str_replace($hack_char, html_entity_decode($hack_char), $subject);
			$message = str_replace($hack_char, html_entity_decode($hack_char), $message);
		}

		send_mail($to, $subject, $message, "From: diff@automatweb.com");
	}

	public function delete_history($arr)
	{
		foreach(safe_array($arr["sel"]) as $di)
		{
			$data = parent::meta($di);
			foreach($data["diffs"] as $v)
			{
				$hash = md5($v["urlo"].$v["time"]);
				unlink(aw_ini_get("site_basedir")."/files/site_diff_{$hash}.txt");
			}
			parent::set_meta($di, NULL);
		}
		parent::save();

		return $arr["post_ru"];
	}
}

?>
