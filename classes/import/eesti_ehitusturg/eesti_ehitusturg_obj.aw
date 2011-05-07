<?php

class eesti_ehitusturg_obj extends _int_object
{
	public function get_browser_html()
	{
		$url = new aw_uri(automatweb::$request->arg_isset("url") ? automatweb::$request->arg("url") : $this->prop("url"));
		foreach(array("valdkond", "valdkond2", "maakond", "action" => "EEaction") as $k => $v)
		{
			$k = is_int($k) ? $v : $k;
			if(automatweb::$request->arg_isset($v))
			{
				$url->set_arg($k, automatweb::$request->arg($v));
			}
		}
		return $this->handle_html(file_get_contents($url->get()));
	}

	protected function handle_html($html)
	{
		$html = str_ireplace("src=\"", "src=\"".$this->prop("url")."/", $html);
		$html = str_replace("href=\"style.css\"", "href=\"".$this->handle_url("style.css")."\"", $html);
		$html = str_replace("background=\"", "background=\"".$this->prop("url")."/", $html);
		
		// Change the action for search form
		$html = str_ireplace(array("javascript:submitForm()", "javascript:document.otsivorm.submit()"), "", $html);
		
		// Attach additional JS and CSS
		$html = str_ireplace("</head>", "<link href=\"".aw_ini_get("baseurl")."/automatweb/css/popup_menu.css\" rel=\"stylesheet\" type=\"text/css\" />\n<script type=\"text/javascript\" src=\"".aw_ini_get("baseurl")."/automatweb/js/jquery/jquery-1.3.2.min.js\"></script>\n<script type=\"text/javascript\" src=\"".aw_ini_get("baseurl")."/automatweb/js/applications/first_contact_tools/eesti_ehitusturg.js\"></script>\n<script type=\"text/javascript\" src=\"".aw_ini_get("baseurl")."/automatweb/js/aw.js\"></script>\n<script type=\"text/javascript\" src=\"".aw_ini_get("baseurl")."/automatweb/js/popup_menu.js\"></script>\n<script type=\"text/javascript\" src=\"".aw_ini_get("baseurl")."/automatweb/js/jquery/plugins/jquery_gup.js\"></script></head>", $html);
		
		// Handle document location
		$html = preg_replace_callback("/document.location='([a-zA-Z0-9?=\.&]*)';/", array($this, "handle_document_location"), $html);

		// Handle company detailed info link
		$html = preg_replace_callback("/href=\"(\?leht=[0-9]\&rn=[a-zA-Z0-9&=]*)\"/", array($this, "handle_company_detailed_info_link"), $html);
		
		// Add HTML for the drop-down menu
		$html = str_ireplace("</body>", $this->get_drop_down_menu_html()."</body>", $html);

		return $html;
	}

	protected function get_drop_down_menu_html()
	{
		$popup_menu = get_instance("popup_menu");
		$popup_menu->begin_menu("my_popup_menu");

		$ol = new object_list(array(
			"class_id" => CL_MESSAGE_TEMPLATE,
			"CL_MESSAGE_TEMPLATE.RELTYPE_EMAIL_TEMPLATE(CL_EESTI_EHITUSTURG).id" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
		));

		foreach($ol->names() as $oid => $name)
		{
			$popup_menu->add_item(array(
				"text" => parse_obj_name($name),
				"link" => "javascript:awEestiEhitusturg.sendSpam({$oid});",
			));
		}

		return "<div id=\"awSpecialDiv\" style=\"display: none;\">".$popup_menu->get_menu(array(
			"text" => t("AW: Valin ja saadan e-kirja")
		))."<br /><br /></div>";
	}

	public function handle_company_detailed_info_link($arr)
	{
		return str_replace($arr[1], aw_url_change_var(array(
			"url" => $this->handle_url($arr[1]),
			"EEaction" => NULL
		)), $arr[0]);
	}

	public function handle_document_location($arr)
	{
		return "document.location='".aw_url_change_var("url", $this->handle_url($arr[1]))."'";
	}

	protected function handle_url($url)
	{
		return substr($url, 0, 7) == "http://" ? $url : $this->prop("url")."/".$url;
	}

	protected function parse_email_template($field)
	{
		$t = get_instance("aw_template");

		$t->use_template($m->content);

		$tpl = obj(automatweb::$request->arg("mail_tpl"));
		return $tpl->prop($field);
	}

	public function send_spam()
	{
		$inst = get_instance("protocols/mail/aw_mail");
		$to_email = "kaareln@gmail.com";

		$content = $this->parse_email_template("content");
		$subject = $this->parse_email_template("subject");
		$froma = "eesti.ehitus@automatweb.com";
		$fromn = "AW";

		$inst->set_header("Content-Type","text/plain; charset=\"".aw_global_get("charset")."\"");
		if($m->is_html)
		{
			$inst->create_message(array(
				"froma" => $froma,
				"fromn" => $fromn,
				"subject" => $subject,
				"to" => $to_email,
				"body" => t("Kahjuks sinu kirjalugeja ei oska kuvada HTML-formaadis kirju."),
			));
			$inst->htmlbodyattach(array(
				"data" => $content,
			));
		}
		else
		{
			$inst->create_message(array(
				"froma" => $froma,
				"fromn" => $fromn,
				"subject" => $subject,
				"to" => $to_email,
				"body" => $content,
			));
		}
		$inst->gen_mail();

		$msg = sprintf("Saatsin e-kirja aadressile %s ja lisasin Sinu m&auml;rkmikusse k&otilde;ne.", $to_email);
		die(iconv("ISO-8859-1", "UTF-8", html_entity_decode($msg)));
	}
}

?>
