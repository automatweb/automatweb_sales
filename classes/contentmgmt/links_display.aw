<?php
/*
@classinfo  maintainer=kristo
*/

class links_display 
{
	////
	// !Hoolitseb ntx doku sees olevate extlinkide aliaste parsimise eest (#l2#)
	function parse_alias($args = array())
	{
		extract($args);

		$this->img = false;

		list($url,$target,$caption) = $this->draw_link($alias["target"]);

		if (substr($url, 0, 3) == "www")
		{
			$url = "http://".$url;
		}
		$caption2 = $caption;
		if ($this->img)
		{
			$caption = $this->img;
		};
		$caption = !empty($htmlentities) ? htmlentities($caption) : $caption;
		$alt = $this->cur_link->trans_get_val("alt");
		$url = str_replace("'", "\"", $url);
		$before_link = $this->cur_link->trans_get_val("before_link");
		$after_link = $this->cur_link->trans_get_val("after_link");
		$style_class = strlen($this->cur_link->trans_get_val("style_class")) ? "class=\"".$this->cur_link->trans_get_val("style_class")."\"" : "";
		$caption = $before_link.$caption.$after_link;
		$vars = array(
			"style_class" => $style_class,
			"url" => $url,
			"caption" => str_replace("&", "&amp;", $caption),
			"caption2" => $caption2,
			"target" => $target,
			"img" => $this->img,
			"img_url" => $this->img,
			"real_link" => str_replace("&", "&amp;", $this->real_link),
			"alt" => $alt,
			"comment" => $this->cur_link->comment()
		);
		if (isset($tpls["link"]))
		{
			$replacement = trim(localparse($tpls["link"],$vars));
		}
		else
		{
			if ($this->img)
			{

				$replacement = sprintf("<a href='%s' %s title='%s' %s><img src='%s' alt='%s'></a>",$url,$target,$alt,$style_class,$this->img,$alt);
			}
			else
			{
				$replacement = sprintf("<a href='%s' %s title='%s' %s>%s</a>",$url,$target,$alt,$style_class,$caption);
			}
		};
		$this->img = "";
		return $replacement;
	}

	function draw_link($target)
	{
		$link = obj($target);
		$this->cur_link = $link;

		$url_pv = $link->trans_get_val("url");

		if (strpos($url_pv,"@") > 0)
		{
			$linksrc = $url_pv;
		}
		elseif (aw_ini_get("extlinks.directlink") == 1)
		{
			$linksrc = $url_pv;
			if (substr($linksrc, 0, 3) == "www")
			{
				$linksrc = "http://".$linksrc;
			}
		}
		elseif(strpos($url_pv, "#") === 0)
		{
			$linksrc = $url_pv;
		}
		else
		{
			$linksrc = aw_ini_get("baseurl")."/".$link->id();
		};
		$this->real_link = $url_pv;

		if ($link->prop("link_image_check_active") && ($link->prop("link_image_active_until") < 100 || $link->prop("link_image_active_until") >= time()) )
		{
			$img = new object_list(array(
				"parent" => $link->id(),
				"class_id" => CL_FILE,
				"lang_id" => array()
			));

			$awf = new file();
			$o = $img->begin();
			if ($img->count() > 0 && $awf->can_be_embedded($o))
			{
				$img = $awf->get_url($o->id(),"");
			}
			else
			{
				$img = "";
			};

			$this->img = $img;
		}

		if ($link->prop("use_javascript"))
		{
			$target = sprintf("onClick='javascript:window.open(\"%s\",\"w%s\",\"toolbar=%d,location=%d,menubar=%d,scrollbars=%d,width=%d,height=%d\")'",
				$linksrc,
				$link->id(),
				$link->prop("newwintoolbar"),
				$link->prop("newwinlocation"),
				$link->prop("newwinmenu"),
				$link->prop("newwinscroll"),
				$link->prop("newwinwidth"),
				$link->prop("newwinheight")
			);
			$url = "javascript:void(0)";
		}
		else
		{
			$url = $linksrc;
			$target = $link->prop("newwindow") ? "target='_blank'" : "";
		};
		return array($url,$target,$link->trans_get_val("name"));
	}
}
?>
