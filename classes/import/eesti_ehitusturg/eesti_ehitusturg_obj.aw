<?php

class eesti_ehitusturg_obj extends _int_object
{
	public function import()
	{		
		$i = $this->instance();
		$i->read_template("import.tpl");

		$i->vars(array(
			"url" => $this->prop("url"),
			"load_html_url" => $i->mk_my_orb("load_html", array("id" => $this->id())),
		));

		return $i->parse();
	}

	public function get_html()
	{
		$url = new aw_uri(automatweb::$request->arg_isset("url") ? automatweb::$request->arg("url") : $this->prop("url"));
		$html = $this->handle_html(file_get_contents($url));

		return $html;
	}

	protected function get_javascript_tag()
	{
		$i = $this->instance();
		$i->read_template("import.js");

		$script = $i->parse();

		return sprintf("<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"></script>\n</script><script type=\"text/javascript\">\n%s\n</script>\n", $script);
	}

	protected function handle_html($html)
	{
		$html = str_ireplace("src=\"", "src=\"".$this->prop("url")."/", $html);
		$html = str_replace("href=\"style.css\"", "href=\"".$this->handle_url("style.css")."\"", $html);
		$html = str_replace("background=\"", "background=\"".$this->prop("url")."/", $html);

		return $html;
	}

	protected function handle_url($url)
	{
		return substr($url, 0, 7) == "http://" ? $url : $this->prop("url")."/".$url;
	} 
}

?>
