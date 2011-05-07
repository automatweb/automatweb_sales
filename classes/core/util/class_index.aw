<?php

class class_index
{
	const INDEX_DIR = "files/class_index/";
	const CLASS_DIR = "classes/";
	const LOCAL_CLASS_DIR = "files/classes/";
	const LOCAL_CLASS_PREFIX = "_aw_local_class__"; // local class names in form LOCAL_CLASS_PREFIX . $class_obj_id
	const UPDATE_EXEC_TIMELIMIT = 300;
	const CL_NAME_MAXLEN = 1024;  // max string length of class names

	// update collects implemented interfaces in this, so we can update the interface definitions with the list of classes that implement that interface
	private static $implements_interface = array();

	// collect and save ancestor hierarchies to improve is_extension_of() performance. array(parent1 => array(child1, ...), ...)
	private static $parent_index = array();

	// initial solution. additional classes that can handle requests.
	// requestable classes should be those that contain methods that can be called through exec()
	private static $requestable_classes = array(
		"sys",
		"popup_search",
		"document"
	);

	/**
	@attrib api=1 params=pos
	@param full_update optional type=bool
		Update all definitions regardless if class files modified and perform maintenance. Default false
	@returns void
	@comment
		Updates entire class index. Reads all files in class directory and parses them, looking for php class definitions.
	**/
	public static function update($full_update = true)
	{
		// ...
		$max_execution_time_prev_val = ini_get("max_execution_time");
		set_time_limit(self::UPDATE_EXEC_TIMELIMIT);

		// update
		$found_classes = self::_update("", "", $full_update);
		self::update_one_file(AW_DIR."init.aw", $found_classes, $full_update, "../");

		if ($full_update)
		{
			self::do_post_update_processing();
			self::clean_up($found_classes);
		}

		// restore normality
		set_time_limit($max_execution_time_prev_val);
	}

	private static function _update($class_dir = "", $path = "", $full_update = false)
	{
		$time = time();

		if (empty($class_dir))
		{
			$class_dir = AW_DIR . self::CLASS_DIR;
		}

		$index_dir = AW_DIR. self::INDEX_DIR;

		// make index directory if not found
		if (!is_dir($index_dir))
		{
			$ret = mkdir($index_dir, 0777);

			if (!$ret)
			{
				$e = new awex_clidx_filesys(sprintf("Failed to create index directory %s.", $index_dir));
				$e->clidx_file = $index_dir;
				$e->clidx_op = "mkdir";
				throw $e;
			}

			$ret = chmod($index_dir, 0777);

			if (!$ret)
			{
				$e = new awex_clidx_filesys("Failed to change index directory permissions.");
				$e->clidx_file = $index_dir;
				$e->clidx_op = "mkdir";
				throw $e;
			}

		}

		if (!is_dir($class_dir))
		{
			throw new awex_clidx(sprintf("Class directory doesn't exist %s.", $class_dir));
		}

		// scan all files in given class directory for php class definitions
		$found_classes = array(); // names of all found classes/ifaces

		if ($handle = opendir($class_dir))
		{
			$non_dirs = array(".", "..", "CVS");

			if (0 === strlen(AW_FILE_EXT))
			{
				$ext_len = self::CL_NAME_MAXLEN;
			}
			else
			{
				$ext_len = - strlen(AW_FILE_EXT);
			}

			// these files are ignored under class directory
			$cl_dir_tmp = AW_DIR . self::CLASS_DIR;
			$ignore_files = array($cl_dir_tmp . "core/fastcall_base" . AW_FILE_EXT);

			while (($file = readdir($handle)) !== false)
			{
				$class_file = $class_dir . $file;

				if ("file" === filetype($class_file) and strrchr($file, ".") === AW_FILE_EXT and !in_array($class_file, $ignore_files))
				{ // process only applicable code files
					self::update_one_file($class_file, $found_classes, $full_update, $path);
				}
				elseif ("dir" === filetype($class_file) and !in_array($file, $non_dirs))
				{
					$found_classes = array_merge(self::_update($class_dir . $file . "/", $path . $file . "/", $full_update), $found_classes);
				}
			}

			closedir($handle);
		}
		else
		{
			$e = new awex_clidx_filesys("Couldn't open class directory.");
			$e->clidx_file = $class_dir;
			$e->clidx_op = "opendir";
			throw $e;
		}

		return $found_classes;
	}

	private static function update_one_file($class_file, &$found_classes, $full_update, $path)
	{
		$time = time();
		$index_dir = AW_DIR . self::INDEX_DIR;

		if (0 === strlen(AW_FILE_EXT))
		{
			$ext_len = self::CL_NAME_MAXLEN;
		}
		else
		{
			$ext_len = - strlen(AW_FILE_EXT);
		}

		$cl_handle = null; // class file resource handle
		// parse code
		$tmp = token_get_all(file_get_contents($class_file));
		$type = "";

		foreach ($tmp as $token)
		{
			if (T_CLASS === $token[0])
			{
				$type = "class";
			}
			elseif (T_INTERFACE === $token[0])
			{
				$type = "interface";
			}
			elseif (T_STRING === $token[0] and ("class" === $type or "interface" === $type))
			{
				if (is_resource($cl_handle) and !empty($class_dfn))
				{ // write previous class/iface dfn file
					// aquire lock
					if (!flock($cl_handle, LOCK_EX))
					{
						$e = new awex_clidx_filesys("Unable to update class index for '" . $class_file . "'. Failed to aquire lock.");
						$e->clidx_cl_name = $class_name;
						$e->clidx_file = $class_dfn_file;
						$e->clidx_op = "flock";
						throw $e;
					}
					fwrite($cl_handle, serialize($class_dfn));
					fclose($cl_handle);
				}

				$modified = filemtime($class_file);
				$class_path = $path . substr(basename($class_file), 0, $ext_len);// relative path + file without extension
				$class_name = $token[1];
				$class_dfn_file = $index_dir . $class_name . AW_FILE_EXT;

				// look for redeclared classes
				if (in_array($class_name, $found_classes))
				{
					if (!is_readable($class_dfn_file))
					{
						$e = new awex_clidx_filesys("Can't read redeclared class definition file '" . $class_dfn_file . "'.");
						$e->clidx_cl_name = $class_name;
						$e->clidx_file = $class_dfn_file;
						$e->clidx_op = "is_readable";
						throw $e;
					}

					$class_dfn = unserialize(file_get_contents($class_dfn_file));
					$e = new awex_clidx_double_dfn("Duplicate definition of '" . $class_name . "' in '" . $class_dfn["file"] . "' and '" . $class_path . "'.");
					$e->clidx_cl_name = $class_name;
					$e->clidx_path1 = $class_dfn["file"];
					$e->clidx_path2 = $class_path;
					throw $e;
				}

				if (!$full_update and is_readable($class_dfn_file))
				{ // try to read old data for class/iface found
					$class_dfn = unserialize(file_get_contents($class_dfn_file));
				}
				else
				{
					$class_dfn = array();
				}

				if (
					!isset($class_dfn["last_update"]) or
					false === $modified or
					$class_dfn["last_update"] < $modified or
					$class_dfn["file"] !== $class_path
				)
				{ // previous definition not found or class/iface modified
					// new definition
					$class_dfn = array(
						"file" => $class_path,
						"clidx_version" => 5, // to comply with changes to class index format
						"last_update" => $time,
						"ancestors" => array(),
						"type" => $type
					);

					// open index file for this class/iface
					$cl_handle = fopen($class_dfn_file, "w");
				}
				else
				{
					$class_dfn = array();
				}

				$found_classes[] = $class_name;
				$type = "";
			}
			elseif (T_EXTENDS === $token[0])
			{
				$type = "extends";
			}
			elseif (T_STRING === $token[0] and "extends" === $type and !empty($class_dfn))
			{ // 'extends' always comes right after class name therefore variables are still set.
				$class_parent = $token[1];
				$class_dfn["extends"] = $class_parent;
				self::$parent_index[$class_name] = $class_parent; // to not rewrite all cldfn files in post processing
				$type = "";
			}
			elseif (T_IMPLEMENTS === $token[0])
			{
				$type = "implements";
			}
			elseif (T_STRING === $token[0] and "implements" === $type and !empty($class_dfn))
			{ // 'implements' always comes right after class name therefore variables are still set.
				$interface = $token[1];
				$class_dfn["implements"] = $interface;
				self::$implements_interface[$interface][] = $class_name;
				// can't empty type, because we can have multiple implements
				//$type = "";
			}
			elseif ($token == "{")
			{
				// this comes after all implements and things are done in the class
				$type = "";
			}
		}

		if (is_resource($cl_handle) and !empty($class_dfn))
		{ // write last class dfn file
			// aquire lock
			if (!flock($cl_handle, LOCK_EX))
			{
				$e = new awex_clidx_filesys("Unable to update class index for '" . $class_file . "'. Failed to aquire lock.");
				$e->clidx_cl_name = $class_name;
				$e->clidx_file = $class_dfn_file;
				$e->clidx_op = "flock";
				throw $e;
			}
			fwrite($cl_handle, serialize($class_dfn));
			fclose($cl_handle);
		}
	}

	/**
	@attrib api=1 params=pos
	@param name required type=string
		Class name
	@returns string Class definition file absolute path
	**/
	public static function get_file_by_name($name)
	{
		// determine if class is aw class or local
		if (0 === strpos($name, self::LOCAL_CLASS_PREFIX))
		{
			// load local class
			$class_file = aw_ini_get("site_basedir") . self::LOCAL_CLASS_DIR . $name . AW_FILE_EXT;

			if (!is_readable($class_dfn_file))
			{
				$e = new awex_clidx_filesys("Local class definition not found or not readable.");
				$e->clidx_cl_name = $name;
				$e->clidx_file = $class_dfn_file;
				$e->clidx_op = "is_readable";
				throw $e;
			}
		}
		else
		{
			// try existing index
			$class_dfn_file = AW_DIR . self::INDEX_DIR . $name . AW_FILE_EXT;
			$class_dir = AW_DIR . self::CLASS_DIR;

			if (!is_readable($class_dfn_file))
			{
				// update index and try again
				self::update();

				if (!is_readable($class_dfn_file))
				{
					$e = new awex_clidx_filesys("'" . $name . "' class/interface definition not found.");
					$e->clidx_cl_name = $name;
					$e->clidx_file = $class_dfn_file;
					$e->clidx_op = "is_readable";
					throw $e;
				}
			}

			$class_dfn = unserialize(file_get_contents($class_dfn_file));

			if (1 >= (int) $class_dfn["last_update"])
			{ // in case definition is corrupt or ...
				self::update();
				$class_dfn = unserialize(file_get_contents($class_dfn_file));
			}

			// load aw class dfn
			$class_file = $class_dir . $class_dfn["file"] . AW_FILE_EXT;

			if (!is_readable($class_file))
			{
				// class file may have changed, update index.
				self::update();

				if (!is_readable($class_dfn_file))
				{
					$e = new awex_clidx_filesys("'" . $name . "' class/interface definition not found.");
					$e->clidx_cl_name = $name;
					$e->clidx_file = $class_dfn_file;
					$e->clidx_op = "is_readable";
					throw $e;
				}

				$class_dfn = unserialize(file_get_contents($class_dfn_file));
				$class_file = $class_dir . $class_dfn["file"] . AW_FILE_EXT;

				if (!is_readable($class_file))
				{
					$e = new awex_clidx_filesys("Class file not found. Update created an index file with false data.");
					$e->clidx_cl_name = $name;
					$e->clidx_file = $class_file;
					$e->clidx_op = "is_readable";
					throw $e;
				}
			}
		}

		return $class_file;
	}

	/**
	@attrib api=1 params=pos
	@param name required type=string
		Class name
	@param parent required type=string
		Parent class name
	@returns boolean
	@comment Checks whether $parent is among class parents
	**/
	public static function is_extension_of($name, $parent)
	{
		if (!is_string($name) or !is_string($parent) or !strlen($name) or !strlen($parent))
		{
			return false;
		}

		$class_dfn_file = AW_DIR . self::INDEX_DIR . $name . "." . aw_ini_get("ext");

		if (!is_readable($class_dfn_file))
		{
			self::update();

			if (!is_readable($class_dfn_file))
			{
				$e = new awex_clidx_filesys("Class definition not found or not readable.");
				$e->clidx_cl_name = $name;
				$e->clidx_file = $class_dfn_file;
				$e->clidx_op = "is_readable";
				throw $e;
			}
		}

		$class_dfn = unserialize(file_get_contents($class_dfn_file));

		if ($class_dfn["clidx_version"] < 5) // clidx_version must be >=5, earlier formats don't have 'extends' parameter or ancestor info.
		{
			self::update();
			$class_dfn = unserialize(file_get_contents($class_dfn_file));
		}

		return (bool) in_array($parent, $class_dfn["ancestors"]);
	}

	private static function clean_up($classes)
	{
		$index_dir = AW_DIR . self::INDEX_DIR;
		$ext_len = strlen(AW_FILE_EXT);
		$ext_len = (0 === $ext_len) ? self::CL_NAME_MAXLEN :  (- $ext_len);

		if ($handle = opendir($index_dir))
		{
			while (($cl_dfn_file = readdir($handle)) !== false)
			{
				$file = $index_dir . $cl_dfn_file;

				if ("file" === @filetype($file) and !in_array(substr($cl_dfn_file, 0, $ext_len), $classes))
				{
					$deleted = unlink($file);

					if (!$deleted)
					{
						$e = new awex_clidx_filesys("Couldn't delete redundant file in class index");
						$e->clidx_file = $file;
						$e->clidx_op = "unlink";
						throw $e;
					}
				}
			}

			closedir($handle);
		}
		else
		{
			$e = new awex_clidx_filesys("Couldn't open index directory");
			$e->clidx_file = $index_dir;
			$e->clidx_op = "opendir";
			throw $e;
		}
	}

	/** Returns a list of classes that implement a particular interface
		@attrib api=1 params=pos

		@param interface required type=string
			The name of the interface to search for

		@returns
			array { class_name_with_folder => class_name } for all classes that implement the given interface
	**/
	public static function get_classes_by_interface($interface)
	{
		$index_dir = AW_DIR . self::INDEX_DIR;
		$if_file = $index_dir.$interface.".".aw_ini_get("ext");

		if (!file_exists($if_file))
		{
			self::update(true);
			if (!file_exists($if_file))
			{
				$e = new awex_clidx_filesys("Could not open interface $if_file class index.");
				$e->clidx_file = $if_file;
				$e->clidx_op = "file_exists";
				throw $e;
			}
		}
		$fc = unserialize(file_get_contents($if_file));

		if ($fc["type"] !== "interface")
		{
			$e = new awex_clidx("get_classes_by_interface($interface): the requested interface is not an interface!");
			$e->clidx_cl_name = $interface;
			throw $e;
		}
		return isset($fc["implemented_by"]) ? safe_array($fc["implemented_by"]) : array();
	}

	/** Returns true if the given interface name is actually an interface, false otherwise.
		@attrib api=1 params=pos

		@param interface required type=string
			The name of the interface to search for

		@returns
			Boolean
	**/
	public static function is_interface($interface)
	{
		$index_dir = AW_DIR . self::INDEX_DIR;
		$if_file = $index_dir.$interface.".".aw_ini_get("ext");

		if (!file_exists($if_file))
		{
			self::update(true);
			if (!file_exists($if_file))
			{
				$e = new awex_clidx_filesys("Could not open interface $if_file class index.");
				$e->clidx_file = $if_file;
				$e->clidx_op = "file_exists";
				throw $e;
			}
		}
		$fc = unserialize(file_get_contents($if_file));

		return $fc["type"] === "interface";
	}

	private static function do_post_update_processing()
	{
		$index_dir = AW_DIR . self::INDEX_DIR;

		// write implemented-by info
		foreach(self::$implements_interface as $if_name => $implemented_by)
		{
			$if_file = $index_dir . $if_name . AW_FILE_EXT;

			if (!is_readable($if_file))
			{
				$e = new awex_clidx_filesys("Interface '{$if_name}' definition file not found. Possibly an interface that is used (by '" . implode("', '", $implemented_by) . "') but not declared.");
				$e->clidx_file = $if_name;
				$e->clidx_op = "is_readable";
				throw $e;
			}

			$fc = unserialize(file_get_contents($if_file));
			if (!is_array($fc))
			{
				$e = new awex_clidx_filesys("Could not open interface '{$if_name}' class index.");
				$e->clidx_file = $if_name;
				$e->clidx_op = "file_get_contents";
				throw $e;
			}

			$fc["implemented_by"] = $implemented_by;
			$f = fopen($if_file, "w");

			// aquire lock
			if (!flock($f, LOCK_EX))
			{
				$e = new awex_clidx_filesys("Unable to update class index for '" . $if_file . "'. Failed to aquire lock.");
				$e->clidx_cl_name = $if_name;
				$e->clidx_file = $if_file;
				$e->clidx_op = "flock";
				throw $e;
			}

			fwrite($f, serialize($fc));
			fclose($f);
		}

		// write descendant hierarchy info
		foreach(self::$parent_index as $class_name => $parent)
		{
			$ancestors = array($parent);
			while (isset(self::$parent_index[$parent]))
			{
				$parent = self::$parent_index[$parent];
				$ancestors[] = $parent;
			}

			$class_dfn_file = $index_dir . $class_name . AW_FILE_EXT;
			$fc = unserialize(file_get_contents($class_dfn_file));

			if (!is_array($fc))
			{
				$e = new awex_clidx_filesys("Couldn't open interface $class_name class definition.");
				$e->clidx_file = $class_name;
				$e->clidx_op = "file_get_contents";
				throw $e;
			}

			$fc["ancestors"] = $ancestors;
			$f = fopen($class_dfn_file, "w");

			// aquire lock
			if (!flock($f, LOCK_EX))
			{
				$e = new awex_clidx_filesys("Unable to update class index for '" . $class_dfn_file . "'. Failed to aquire lock.");
				$e->clidx_cl_name = $class_name;
				$e->clidx_file = $class_dfn_file;
				$e->clidx_op = "flock";
				throw $e;
			}

			fwrite($f, serialize($fc));
			fclose($f);
		}
	}

	/** Returns whether class public methods can be called when executing an aw request.
		@attrib api=1 params=pos
		@param class_name required type=string
		@returns boolean
	**/
	public static function is_requestable($class_name)
	{
		$is_requestable = false;
		if (in_array($class_name, self::$requestable_classes) or self::is_extension_of($class_name, "class_base"))
		{
			$is_requestable = true;
		}
		return $is_requestable;
	}
}

/** generic class index error condition **/
class awex_clidx extends aw_exception
{
	public $clidx_cl_name;
}

/** exceptional condition in file system operation **/
class awex_clidx_filesys extends awex_clidx
{
	public $clidx_file;
	public $clidx_op;
}

/** class with same name is defined in more than one location **/
class awex_clidx_double_dfn extends awex_clidx
{
	public $clidx_path1;
	public $clidx_path2;
}

/** definition container couldn't be locked for modification **/
class awex_clidx_lock extends aw_exception {}

?>
