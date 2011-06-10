<?php

class rss_reader_obj extends _int_object
{
	const CLID = 1531;

	/**
		@attrib api=1
	**/
	public function get_rss_items($show_all = false)
	{
		$xml = $this->_fetch_xml();
		$items = $this->_parse_xml_items($xml, $show_all);
		return $items;
	}

	private function _fetch_xml()
	{
		$cache_key = "rss_content-".$this->id();
		$c = get_instance("cache");;
		if (($xml = $c->file_get_ts($cache_key, time() - (60 * $this->prop("update_interval")))) !== false)
		{
			if ($xml != "")
			{
				$this->updated_time = $c->get_modified_time($cache_key);
				return $xml;
			}
		}
		$this->updated_time = time();
		$xml = file_get_contents(trim($this->prop("rss_url")));
		if (trim($xml) == "")
		{
			$xml = file_get_contents(trim($this->prop("rss_url")));
		}
		$c->file_set($cache_key, $xml);
		return $xml;
	}

	public function get_updated_time()
	{
		return $this->updated_time;
	}

	private function _parse_xml_items($xml, $show_all = false)
	{
		try 
		{
			$xml = new SimpleXMLElement($xml);
		}
		catch (Exception $e)
		{
			return array();
		}
		$items = array();
		foreach($xml->channel->item as $item)
		{
			$items[] = array(
				"title" => iconv("utf-8", aw_global_get("charset"), $item->title),
				"link" => iconv("utf-8", aw_global_get("charset"), $item->link),
				"description" => iconv("utf-8", aw_global_get("charset"), $item->description),
				"guid" => iconv("utf-8", aw_global_get("charset"), $item->guid),
				"pubDate" => iconv("utf-8", aw_global_get("charset"), $item->pubDate)
			);
		}
		if (!$show_all && ($max_items = $this->prop("max_display_items")) > 0)
		{
			return array_slice($items, 0, $max_items);
		}
		return $items;
	}
}

?>
