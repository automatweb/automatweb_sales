<?php

class msg_index
{
	const INDEX_DIR = "files/message_index/";
	const INDEX_DIR_PERMISSIONS = 0744;
	const DEFINITIONS_DIR = "xml/msgmaps/";

	/** Returns array of class methods or functions that are registered to handle given message
		@attrib api=1 params=pos
		@param id type=string
			Message identifier
		@returns array
			array(array("class" => class_name, "method" => method_name), ...)
		@errors
			throws awex_param if $id parameter is not a valid message identifier
			throws awex_msg_na
	**/
	public static function get_handlers($id)
	{
		static $cache = array();
		if (!isset($cache[$id]))
		{
			$message_cache = AW_DIR . self::INDEX_DIR . $id;
			$msgmap_file = AW_DIR . self::DEFINITIONS_DIR . "{$id}.xml";
			if (file_exists($message_cache) and filemtime($message_cache) > filemtime($msgmap_file))
			{
				$cache[$id] = unserialize(file_get_contents($message_cache));
			}
			else
			{
				if ($id !== preg_replace("/[^A-Z_]/", "", $id)) throw new awex_param(sprintf("Invalid message identifier '%s'", $id));

				try
				{
					$cache[$id] = array();
					$msgmap = new DOMDocument();
					$msgmap->load($msgmap_file);
					$xpath = new DOMXPath($msgmap);
					$query = "/msgmap/handler";
					$handlers = $xpath->query($query);
					foreach ($handlers as $handler)
					{
						$class = $xpath->query("class", $handler)->item(0);
						$class = $class ? $class->nodeValue : "";
						$method = $xpath->query("method", $handler)->item(0);
						$method = $method ? $method->nodeValue : "";
						$param = $xpath->query("param", $handler)->item(0);
						$param = $param ? $param->nodeValue : "";
						$cache[$id][] = array(
							"class" => $class,
							"method" => $method,
							"param" => $param
						);
					}

					try
					{
						file_put_contents($message_cache, serialize($cache[$id]));
					}
					catch (ErrorException $e)
					{
						if (!is_dir(AW_DIR . self::INDEX_DIR))
						{
							mkdir(AW_DIR . self::INDEX_DIR, self::INDEX_DIR_PERMISSIONS);
							file_put_contents($message_cache, serialize($cache[$id]));
						}
					}
				}
				catch (ErrorException $e)
				{
					throw new awex_msg_na(sprintf("Message '%s' cache not writable in file '%s'.", $id, $message_cache));
				}
				catch (Exception $e)
				{
					throw new awex_msg_na(sprintf("Message '%s' definition not readable in file '%s'.", $id, $msgmap_file));
				}
			}
		}

		return $cache[$id];
	}

	public static function add_handler($id, $class, $method, $param = "")
	{
		if ($id !== preg_replace("/[^A-Z_]/", "", $id)) throw new awex_param(sprintf("Invalid message identifier '%s'", $id));
		if (empty($class) or empty($method)) throw new awex_msg_handler(sprintf("Invalid message handler '%s::%s' for '%s'", $class, $method, $id));

		$msgmap_file = AW_DIR . self::DEFINITIONS_DIR . $id . ".xml";
		if (file_exists($msgmap_file))
		{
			try
			{
				$msgmap = new DOMDocument();
				$msgmap->formatOutput = true;
				$msgmap->preserveWhiteSpace = false;
				$msgmap->load($msgmap_file);
				$xpath = new DOMXPath($msgmap);
				$handlers = $xpath->query("/msgmap/handler");
				$add = true;
				foreach ($handlers as $handler)
				{
					$tmp_class = $xpath->query("class", $handler)->item(0);
					$tmp_class = $tmp_class ? $tmp_class->nodeValue : "";
					$tmp_method = $xpath->query("method", $handler)->item(0);
					$tmp_method = $tmp_method ? $tmp_method->nodeValue : "";
					$tmp_param = $xpath->query("param", $handler)->item(0);
					$tmp_param = $tmp_param ? $tmp_param->nodeValue : "";

					if ($class === $tmp_class and $method === $tmp_method and (!strlen($param) or $param === $tmp_param))
					{
						$add = false;
					}
				}

				if ($add)
				{
					$handler = $msgmap->createElement("handler");
					$handler->appendChild($msgmap->createElement("class", $class));
					$handler->appendChild($msgmap->createElement("method", $method));
					if (strlen($param)) $handler->appendChild($msgmap->createElement("param", $param));
					$msgmap->documentElement->appendChild($handler);
					$msgmap->save($msgmap_file);
				}

				$msgmap = null;
			}
			catch (Exception $e)
			{
				echo "Error reading message map file '{$msgmap_file}'\n";
			}
		}
		else
		{
			$param = strlen($param) ? "\n<param>{$param}</param>\n" : "";
			$msgmap = <<<MSGMAP
<?xml version='1.0'?>
<msgmap id="{$id}">
	<handler>
		  <class>{$class}</class>
		  <method>{$method}</method>{$param}
	</handler>
</msgmap>
MSGMAP;
			file_put_contents($msgmap_file, $msgmap);
		}
	}

	public static function remove_handler($id, $class, $method, $param = "")
	{
		if ($id !== preg_replace("/[^A-Z_]/", "", $id)) throw new awex_param(sprintf("Invalid message identifier '%s'", $id));

		$msgmap_file = AW_DIR . self::DEFINITIONS_DIR . $id . ".xml";
		if (file_exists($msgmap_file))
		{
			try
			{
				$msgmap = new DOMDocument();
				$msgmap->formatOutput = true;
				$msgmap->preserveWhiteSpace = false;
				$msgmap->load($msgmap_file);
				$xpath = new DOMXPath($msgmap);
				$handlers = $xpath->query("/handler");
				$changed = false;
				foreach ($handlers as $handler)
				{
					$tmp_class = $xpath->query("class", $handler)->item(0)->nodeValue;
					$tmp_method = $xpath->query("method", $handler)->item(0)->nodeValue;
					$tmp_param = $xpath->query("param", $handler)->item(0)->nodeValue;
					if ($class === $tmp_class and $method === $tmp_method and (strlen($param) < 1 or $param === $tmp_param))
					{
						$msgmap->documentElement->removeChild($handler);
						$changed = true;
					}
				}

				if ($changed)
				{
					$msgmap->save($msgmap_file);
				}

				$msgmap = null;
			}
			catch (Exception $e)
			{
				echo "Error reading message map file '{$msgmap_file}'\n";
			}
		}
	}
}

class awex_msg extends aw_exception {}

/** Message not available **/
class awex_msg_na extends awex_msg {}

