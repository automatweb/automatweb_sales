<?php

class template_logger extends core
{
	function template_logger()
	{
		$this->init();
	}

	/**
		@attrib name=recieve_log_data all_args=1 nologin=1
	**/
	function recieve_log_data($arr)
	{
		// inseert into log table
		$s = serialize($arr["data"]);
		$this->quote(&$s);
		$this->db_query(
			sprintf(
				"INSERT INTO template_log_data(site_id, tm, y, m, tpl_list) values('%s', %d, %d, %d, '%s')",
				(int)$arr["site_id"],
				time(),
				$arr["y"],
				$arr["m"],
				$s
			)
		);
		return 1;
	}

	/**
		@attrib name=send_template_log_data all_args=1 nologin=1
	**/
	function send_template_log_data($arr)
	{
		while ($row = $this->db_fetch_row("SELECT * FROM template_log_data LIMIT 1"))
		{
			$url = "http://dev.struktuur.ee/wiki_updater.php?site_id=".$row["site_id"];
			echo "fetch $url <br>";
			$h = get_instance("protocols/file/http");
			list($h, $c) = explode("\r\n\r\n", $h->post_request(
				"dev.struktuur.ee",
				"/wiki_updater.php",
				$row
			));

			if (trim($c) == "1")
			{
				// delete row
				$this->save_handle();
				$this->db_query("DELETE FROM template_log_data WHERE id = '$row[id]'");
				$this->restore_handle();
				echo "did row $row[id] <br>";
			}
			else
			{
				die( "fail content $c <br>");
			}
			if (++$row > 300)
			{
				break;
			}
		}
		die("all done");
	}

	/**
		@attrib name=fetch_logs
	**/
	function fetch_logs($arr)
	{
		$sl = get_instance("install/site_list");
		foreach($sl->get_site_list() as $id => $row)
		{
			if (!$row["site_used"])
			{
				continue;
			}
			$url = $row["url"]."/orb.aw?class=sys&action=consolidate_template_logs";
			echo "site $id fetch url $url <br>\n"; 
			flush();
			$ct = file_get_contents($url);
		}
		die("all done");
	}

}
