<?php
// $Header: /home/cvs/automatweb_dev/classes/output/xml/rss.aw,v 1.6 2008/06/03 09:25:58 kristo Exp $
// rss.aw - RSS feed generator
/*
@classinfo maintainer=kristo
*/
define(ITEM_TPL,"
<item rdf:about=\"%s\">
<title>%s</title>
<link>%s</link>
<dc:description>%s</dc:description>
<dc:creator>%s</dc:creator>
<dc:publisher>%s</dc:publisher>
<dc:date>%s</dc:date>
<dc:language>ee</dc:language>
</item>
");

class rss extends aw_template
{
	var $li;
	var $items;
	function rss($args = array())
	{
		$this->li = "";
		$this->items = "";
		extract($args);
		$this->title = $this->_convert($title);
		$this->description = $this->_convert(aw_ini_get("document.publisher"));
		$this->about = $about;
		$this->init("");
	}

	////
	// !Konverdib sisendstringi sobivale kujule, ehk 2x läbi htmlentities funktsiooni
	// Ärge küsige minult, miks seda vaja on
	function _convert($str)
	{
		$str = preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",strip_tags($str));
		return htmlentities(htmlentities($str));
	}

	function add_item($args = array())
	{
		$baseurl = $this->cfg["baseurl"];
		$ext = $this->cfg["ext"];
		if (is_array($args))
		{
			extract($args);
			$id = ($id) ? $id : $docid;
			$description = ($description) ? $description : $lead;
			$creator = $this->_convert($author);
			$link = sprintf("%s/%d",$baseurl,$id);
			$title = $this->_convert($title);
			$description = "<a href='$link'>$title</a>" . " - " . $this->_convert($description);
			$publisher = $this->_convert($publisher);
			$date = date("Y-m-d",$args["modified"]) . "T" . date("H:i",$args["modified"]) . "+02:00";
			$this->li .= sprintf("<rdf:li resource=\"%s\"/>\n",$link);
			$this->items .= sprintf(ITEM_TPL,$link,$title,$link,$description,$creator,aw_ini_get("document.publisher"),$date);
		}
	}

	function gen_output()
	{
		$retval = "";
		$retval .= "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n\n";
		$retval .= "<rdf:RDF\n";
		$retval .= "xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n";
		$retval .= "xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n";
		$retval .= "xmlns=\"http://purl.org/rss/1.0/\"\n";
		$retval .= ">\n\n";
		
		$retval .= sprintf("<channel rdf:about=\"%s\">\n",$this->about);
		$retval .= sprintf("<title>%s</title>\n",$this->title);
		$retval .= sprintf("<link>%s</link>\n",$this->link);
		$retval .= sprintf("<description>%s</description>\n",$this->description);
		$retval .=  "<items>\n<rdf:Seq>\n";
		$retval .= $this->li;
		$retval .= "</rdf:Seq>\n</items>\n</channel>\n\n";
		
		$retval .= $this->items;
		$retval .= "\n\n</rdf:RDF>\n";
		return $retval;
	}


	////
	// !Generates a RSS feed from all documents under a menu. Or from all articles
	// in the current period, if a menu is not specified
	function gen_rss_feed($args = array())
	{
		extract($args);
		$baseurl = $this->cfg["baseurl"];
		$ext = $this->cfg["ext"];
		$parent = (int)$parent;
		$obj = obj($parent);
		$this->link = "$baseurl/$parent";
		$this->about = "$baseurl/index.$ext/section=$section/format=rss";
		$this->title = aw_ini_get("stitle") . " / " . $obj->name();
		$rootmenu = $this->cfg["rootmenu"];


		$m = get_instance("contentmgmt/site_show");

		// loome sisu
		$d = new document();

		if ($periodic)
		{
			$docid = $m->get_default_document(array(
				"periodic_content" => true,
				"obj" => obj($parent)
			));
			foreach((array)$docid as $id)
			{
				$o = obj($id);
				$row = array(
					"docid" => $id,
					"title" => $o->title,
					"lead" => $o->lead,
					"author" => $o->author,
					"modified" => $o->modified
				);
				$this->add_item($row);
			}
		}
		else
		{
		// sektsioon pole perioodiline
			$docid = $m->get_default_document($parent);
			$dlist = new aw_array($docid);
			$docids = join(",",$dlist->get());

			if (strlen($docids) > 0)
			{
				$q = "SELECT documents.docid as docid,title,lead,author,objects.modified FROM documents
					LEFT JOIN objects ON (documents.docid = objects.oid)
					WHERE docid IN ($docids)";
				$this->db_query($q);
				while($row = $this->db_next())
				{
					$this->add_item($row);
				}
			}
		}
		header("Content-Type: text/xml");
		print $this->gen_output();
		// I know, I know, it's damn ugly
		die();
	}
};
?>
