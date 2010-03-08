<?php

class postal_codes_obj extends _int_object
{
	const DEFAULT_REGISTER_URL = "http://register.automatweb.com";

	const REQUEST_PAGE_SIZE = 1000;

	public function get_postal_codes($arr)
	{
		$add = "";
		if(isset($arr["from"]) && $arr["count"])
		{
			$add = " LIMIT ".((int)$arr["from"]).",".((int)$arr["count"]);
		}
		$i = get_instance(CL_POSTAL_CODES);
		$data = $i->db_fetch_array("SELECT * FROM aw_postal_codes".$add);
		if(!$data)
		{
			$data = false;
		}
		return $data;
	}

	public function get_code($arr)
	{
		self::check_db_data();
		$i = get_instance(CL_POSTAL_CODES);
		$where = array();
		$db_fields = self::get_db_fields();
		foreach($arr as $var => $val)
		{
			if($db_fields[$var] && self::parameter_check($val))
			{
				$where[] = "`".$var."` LIKE '".htmlspecialchars($val)."'";
			}
		}
		if(self::parameter_check($arr["house"]))
		{
			$hn = $arr["house"];
			if(is_numeric($hn))
			{
				$where[] = "`house_start` <= ".$hn."";
				$where[] = "`house_end` >= ".$hn."";
			}
			else
			{
				$where[] = "`house_start` = '".$hn."'";
				$where[] = "`house_end` = '".$hn."'";
			}
		}
		if(count($where))
		{
			$sql = implode(" AND ", $where);
			$data = $i->db_fetch_array("SELECT * FROM aw_postal_codes WHERE".$sql);
			if(count($data)==1)
			{
				return $data[0];
			}
			elseif(count($data)>1)
			{
				if($hn)
				{
					foreach($data as $row)
					{
						if($row["house_start"] == 0)
						{
							return $row;
						}
						elseif($hn%2==0 && $row["house_start"]%2==0)
						{
							return $row;
						}
						elseif($hn%2==1 && $row["house_start"]%2==1)
						{
							return $row;
						}
					}
				}
				return false;
			}
		}
		return false;
	}

	public function get_locations_from_param($arr)
	{
		$db_fields = self::get_db_fields();
		$where = array();
		foreach($arr as $var=>$val)
		{
			if(self::parameter_check($val))
			{
				if($db_fields[$var])
				{
					$add = "";
					if(substr($val, 0, 1)=="!")
					{
						$add = "!";
					}
					$where[] = "`".$var."`".$add."='".iconv("UTF-8", aw_global_get("charset"), $val)."'";
				}
				elseif($var == "house")
				{
					$tmp = explode(" - ", $val);
					if(count($tmp) == 2)
					{
						$where[] = "`house_start`='".$tmp[0]."'";
						$where[] = "`house_end`='".$tmp[1]."'";
					}
					else
					{
						$where[] = "`house_start`='".$val."'";
					}
				}
				elseif($var == "userhouse")
				{
					$hn = $val;
					if(is_numeric($val))
					{
						$where[] = "`house_start` <= ".$val."";
						$where[] = "`house_end` >= ".$val."";
					}
					else
					{
						$where["house_start"] = "`house_start` = '".$val."'";
						$where["house_end"] = "`house_end` = '".$val."'";
					}
				}
			}
		}
		if($db_fields[$arr["find"]] || $arr["find"] == "house")
		{
			$sql = "";
			if(count($where))
			{
 				$sql = " WHERE ".implode(" AND ", $where);
			}
			$i = get_instance(CL_POSTAL_CODES);
			if($hn && $arr["find"] == "zip")
			{
				$res = array();
				$rows = $i->db_fetch_array("SELECT house_start, house_end, zip FROM aw_postal_codes ".$sql." ORDER BY `house_start` ASC");
				$high = 0;
				if(count($rows)<1 && !is_numeric($hn))
				{
					if(ereg("([0-9]{1,})*", $hn, $num))
					{
						$where["house_start"] = "`house_start` <= ".$num[1]."";
						$where["house_end"] = "`house_end` >= ".$num[1]."";
						$sql = " WHERE ".implode(" AND ", $where);
						$rows = $i->db_fetch_array("SELECT house_start, house_end, zip FROM aw_postal_codes ".$sql." ORDER BY `house_start` ASC");
					}
				}
				if(count($rows)>1)
				{
					foreach($rows as $id => $row)
					{
						if($row["house_start"] >= $rows[$high]["house_start"] && ($hn%2==0 && $row["house_start"]%2==0 || $hn%2==1 && $row["house_start"]%2==1 || $row["house_start"] == 0))
						{
							$high = $id;
						}
					}
					$row = $rows[$high];
				}
				else
				{
					$row = $rows[0];
				}
				$res[] = array("zip" => $row["zip"]);
			}
			elseif($arr["find"] == "house")
			{
				$res = array();
				$rows = $i->db_fetch_array("SELECT house_start, house_end FROM aw_postal_codes ".$sql." ORDER BY `house_start` ASC");
				foreach($rows as $row)
				{
					$tmp = $row["house_start"];
					if($row["house_start"] != $row["house_end"])
					{
						$tmp .= " - ".$row["house_end"];	
					}
					$res[] = array("house" => $tmp);
				}
			}
			else
			{
				$res = $i->db_fetch_array("SELECT DISTINCT `".$arr["find"]."` FROM aw_postal_codes ".$sql." ORDER BY `".$arr["find"]."` ASC");
			}
			return $res;
		}
		return array();
	}

	public function get_file_fields($obj)
	{
		$fields = array("none" => "");
		$data = $this->get_file_data($obj);
		$row = $data[0];
		$csv_fields = $this->get_data_row($row);
		$fields = $fields + $csv_fields;
		return $fields;
	}

	public function get_db_fields()
	{
		$fields = array(
			"country" => t("Riik"),
			"state" => t("Maakond"),
			"area" => t("Vald"),
			"city" => t("Linn / Alev / K&uuml;la"),
			"street" => t("T&auml;nav / Talu"),
			"house_start" => t("Maja nr algus"),
			"house_end" => t("Maja nr l&otilde;pp"),
			"type" => t("T&uuml;&uuml;p (linn / alev / k&uuml;la)"),
			"zip" => t("Postiindeks"),
		);
		return $fields;
	}

	public function export_csv($arr)
	{
		$obj = obj($arr["id"]);
		$i = $obj->instance();
		$db_fields = $this->get_db_fields();
		$data = $this->get_postal_codes();
		$rows = array();
		$rows[] = $this->get_export_row($db_fields, $db_fields);
		foreach($data as $raw)
		{
			$rows[] = $this->get_export_row($raw, $db_fields);
		}
		$csv = implode(chr(13).chr(10), $rows);
		header("Content-type: text/csv");
		header("Content-Disposition: filename=data.csv");
		die($csv);
	}

	public function import_from_csv($arr)
	{
		$values = $arr["values"];
		$obj = obj($arr["id"]);
		$i = $obj->instance();
		$data = $this->get_file_data($obj);
		$sql = array();
		$rows = $this->get_import_rows($data, $values);
		$sql = $this->get_file_import_sql($rows);
		$this->import_from_sql($sql);
	}

	public function import_from_register($arr)
	{
		if(is_oid($arr["id"]))
		{
			$obj = obj($arr["id"]);
			$site = ($url = $obj->prop("register_url"))?$url:self::DEFAULT_REGISTER_URL;
		}
		$i = get_instance(CL_POSTAL_CODES);
		if(!$site)
		{
			$site = self::DEFAULT_REGISTER_URL;
		}
		$count = self::REQUEST_PAGE_SIZE;
		$end = false;
		$start = true;
		$data = array();
		$from = 0;
		//aw_global_set("xmlrpc_dbg", 1);
		while(!$end)
		{
			$result = $i->do_orb_method_call(array(
				"server" => $site,
				"action" => "get_postal_codes",
				"class" => "postal_codes",
				"params" => array(
					"from" => $from,
					"count" => $count,
				),
				"method" => "xmlrpc",
			));
			if(is_array($result))
			{
				$data = array_merge($data, $result);
				$from += $count;
				$start = false;
			}
			else
			{
				$end = true;
			}
		}
		if(count($data))
		{
			$sql = $this->get_register_import_sql($data);
			$this->import_from_sql($sql);
		}
	}

	private function check_db_data()
	{
		$i = get_instance(CL_POSTAL_CODES);
		$count = $i->db_fetch_field("SELECT count(*) as chk FROM aw_postal_codes", "chk");
		if(!$count)
		{
			self::import_from_register();
		}
	}

	private function get_export_row($raw, $fields)
	{
		foreach($fields as $field => $name)
		{
			$row[] = '"'.html_entity_decode($raw[$field]).'"';
		}
		$ret = implode(";", $row);
		return $ret;
	}

	private function get_file_data($obj)
	{
		$i = $obj->instance();
		$fid = $obj->prop("file");
		$data = array();
		if($i->can("view", $fid))
		{
			$fo = obj($fid);
			$fi = $fo->instance();
			$url = $fi->get_url($fid, $fo->prop("filename"));
			$data = @file($url);
			if(!$data)
			{
				throw new awex_pcodes_badfile("Unable to open file");
			}
		}
		return $data;
	}

	private function get_data_row($row)
	{
		$fields = explode(";", $row);
		if(count($fields)<2)
		{
			$fields = explode(",", $row);
		}
		if(count($fields)<2)
		{
			throw new awex_pcodes_badfile("File format incorrect. CSV expected");
		}
		return $fields;
	}

	private function get_import_rows($data, $values)
	{
		$db_fields = $this->get_db_fields();
		foreach($data as $id=>$raw)
		{
			if(!$id)
			{
				continue;
			}
			$row = $this->get_data_row($raw);
			$s_row = array();
			foreach($db_fields as $col=>$foo)
			{
				if(strlen($def = $values[$col]["default"]))
				{
					$val = $def;
				}
				else
				{
					$key = $values[$col]["csv"];
					$val = str_replace('"', '', $row[$key]);
				}
				$s_row[$col] = htmlspecialchars(trim($val), ENT_QUOTES);
			}
			$rows[] = $s_row;
		}
		return $rows;
	}

	private function get_file_import_sql($rows)
	{
		$sql = array();
		$db_fields = $this->get_db_fields();
		$db_cols = array_keys($db_fields);
		$alphabet = "ABCDEFGHIJKLMOPQRSTUV";
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			if(!is_numeric($row["house_end"]) && $row["house_start"] != $row["house_end"])
			{
				$chk1 = ereg("^([0-9]+)([A-Z]*$)", $row["house_start"], $tmp1);
				$chk2 = ereg("([0-9]+)([A-Z]{1})", $row["house_end"], $tmp2);
				if($chk1 && $chk2)
				{
					if($l = $tmp1[2])
					{
						$start = $l;
					}
					else
					{
						$start = "A";
					}
					$end = $tmp2[2];
					$letters = strstr($alphabet, $start);
					for($j = 0;$j<strlen($letters); $j++)
					{
						$letter = $letters[$j];
						$new_row = $row;
						$new_row["house_start"] = $new_row["house_end"] = $tmp2[1].$letter;
						$rows[] = $new_row;
					}
				}
			}
			$insert = "
				INSERT INTO aw_postal_codes (`".implode('`, `', $db_cols)."`)
				VALUES('".htmlspecialchars(implode("', '", $row))."')";
			$sql[] = $insert;
		}
		return $sql;
	}

	private function get_register_import_sql($data)
	{
		$sql = array();
		foreach($data as $row)
		{
			unset($row["id"]);
			$fields = array_keys($row);
			$insert = "
				INSERT INTO aw_postal_codes (`".implode('`, `', $fields)."`)
				VALUES('".implode("', '", $row)."')";
			$sql[] = $insert;
		}
		return $sql;
	}

	private function import_from_sql($sql)
	{
		$i = get_instance(CL_POSTAL_CODES);
		if(count($sql))
		{
			$i->db_query("TRUNCATE TABLE aw_postal_codes");
			foreach($sql as $id=>$query)
			{
				$i->db_query($query);
			}
		}
	}

	private function parameter_check($value)
	{
		$letters = "&auml;&Auml;&ouml;&Ouml;&uuml;&Uuml;&otilde;&Otilde;";
		if(ereg("[0-9a-zA-Z".html_entity_decode($letters)."\/\-]", $value))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

class awex_pcodes extends awex_obj {}
class awex_pcodes_badfile extends awex_pcodes {}
?>
