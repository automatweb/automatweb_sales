<?php
/** This can be used to call an orb action on each site in the aw site list. 
@classinfo maintainer=kristo
*/
class foreach_site extends class_base
{
	function foreach_site()
	{
		$this->init(array(
			'tpldir' => 'admin/foreach_site',
		));
	}

	/**  
		@attrib name=exec params=name default="1"
	**/
	function exec($arr)
	{
		$this->mk_path(aw_ini_get("rootmenu"),t("Tee igal saidil"));

		$htmlc = new htmlclient(array(
			'template' => "default",
		));
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "eurl",
			"type" => "textbox",
			"size" => 60,
			"caption" => t("Url"),
		));

		$htmlc->add_property(array(
			"name" => "same_code",
			"type" => "checkbox",
			"ch_value" => 1,
			"caption" => t("Ainult samal koodil saidid"),
		));

		$htmlc->add_property(array(
			"name" => "suubitt",
			"type" => "submit",
			"value" => t("Tee!"),
		));

		$htmlc->finish_output(array(
			"action" => "submit_exec",
			"method" => "GET",
			"data" => array(
				"orb_class" => "foreach_site",
				"reforb" => 0
			)
		));


		$tp = get_instance("vcl/tabpanel");
		$tp->add_tab(array(
			"active" => true,
			"caption" => t("Tee igal saidil"),
			"link" => get_ru(),
		));		

		return $tp->get_tabpanel(array(
			"content" => $htmlc->get_result(array(
				"form_only" => 1
			))
		));
	}

	/** Call an url for each site in the site list
		@attrib name=submit_exec params=name api=1

		@param eurl required type=string
			The url to exec for each site. 

		@param same_code optional type=bool
			If set to true, only sites on the same code branch are affected

		@comment
			The output from all the sites will be echoed to the user.

		@examples
			get_instance("admin/foreach_site")->do_call(array(
				"url" => "/orb.aw?class=maitenance&action=cache_clear&clear=1",
				"same_code" => 1
			));
	**/
	function do_call($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		
		// try remoting
		$sl = get_instance("install/site_list");
		$sl->_do_update_list_cache();

		$sites = $sl->get_site_list();

		$cur_site = $sites[aw_ini_get("site_id")];

		foreach($sites as $site)
		{
			if (1 != $site["site_used"])
			{
				continue;
			};
			$url = $site["url"];
			if ($url == "")
			{
				continue;
			}

			if (1 == $arr["same_code"])
			{
				$cur_code = aw_ini_get("basedir");
				// read remote code
				$inivals = $this->do_orb_method_call(array(
					"class" => "objects",
					"action" => "aw_ini_get_mult",
					"method" => "xmlrpc",
					"server" => "register.automatweb.com",
					"params" => array(
						"vals" => array(
							"basedir"
						)
					)
				));

				if ($inivals["basedir"] != $cur_code || $cur_site["server_id"] != $site["server_id"])
				{
					echo "<font color=red>skipping site $url, because it is using a different code path (remote: $site[server_id]:$inivals[basedir]  vs local: $cur_site[server_id]:$cur_code)</font> <br><br>";
					continue;
				}
			}

			if (substr($url, 0, 4) != "http")
			{
				$url = "http://".$url;
			}

			
			echo "<b>exec for site $url <br />\n";

			$url = $url."/".str_replace("/automatweb","",$eurl);

			echo "complete url is $url <br /><br />\n\n</b>";
			flush();

			echo "------------------------------------------------------------------------------------------------------------------------------------<br /><br />\n\n";

			preg_match("/^http:\/\/(.*)\//U",$url, $mt);
			$_url = $mt[1];

			$awt = get_instance("protocols/file/http");
			$awt->handshake(array("host" => $_url));

			echo "do send req $url ",substr($url,strlen("http://")+strlen($_url))," <br />";
			$req = $awt->do_send_request(array(
				"host" => $_url, 
				"req" => substr($url,strlen("http://")+strlen($_url))
			));

			echo "result = $req <br />";		

			echo "------------------------------------------------------------------------------------------------------------------------------------<br /><br />\n\n";

		}
		die(t("all done"));
	}
}
?>