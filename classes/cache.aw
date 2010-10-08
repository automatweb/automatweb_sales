<?php

/*
@classinfo  maintainer=kristo
@comment
	Class for caching data, cache is kept in the file system, in folder defined in ini file by variable cache.page_cache
*/

class cache extends core
{
	function cache()
	{
		$this->db_init();
		aw_config_init_class($this);
	}

	/** writes a page to the html page cache
		@attrib params=pos api=1

		@param oid required type=string
			Object id, which is cached.
		@param arr required type=array
			Array containing parameters identifying object ('period' for example), which is used to compose filename in cache.
		@param content required type=string
			Data which is cached.
		@param xxx optional type=bool default=true
			Unused
		@param real_section optional type=oid default=NULL
			[xxx] If this is set, then oid parameter will be overwritten by this parameter. Seems that oid parameters value may not always be valid object id, so it is possible to supply correct object id via real_section parameter.

		@errors
			none

		@returns
			none

		@comment
			Writes data into cache.

		@examples
			none
	**/
	public static function set($oid,$arr,$content,$xxx = true, $real_section = NULL)
	{
		if (
			aw_ini_get("cache.use_page_cache") &&
			!aw_global_get("uid") && // no cache for logged in users
			!aw_global_get("no_cache")
		)
		{
			if ($real_section === NULL)
			{
				$real_section = $oid;
			}

			$fname = "/".str_replace("/","_",$oid);
			foreach($arr as $v)
			{
				$fname.="-".str_replace("/","_",str_replace(" ","_",$v));
			}

			if (strlen($fname) > 100)
			{
				$fname = "/".md5($fname);
			}

			self::file_set_pt_oid("html", $real_section, $fname, $content);
		}
	}

	/** reads a page from the html page cache
		@attrib params=pos api=1

		@param oid required type=string
			Object id, which is cached.

		@param arr required type=array
			Array containing parameters identifying object ('period' for example), which is used to compose filename in cache.

		@param real_oid optional type=oid default=NULL
			[xxx] If this is set, then oid parameter will be overwritten by this parameter. Seems that oid parameters value may not always be valid object id, so it is possible to supply correct object id via real_oid parameter.

		@errors
			none

		@returns
			If the cache for the given parameters exists and is valid, then returns cache content, else false

	**/
	public static function get($oid,$arr, $real_oid = NULL)
	{
		if (
			aw_ini_get("cache.use_page_cache") &&
			!aw_global_get("uid") && // no cache for logged in users
			!aw_global_get("no_cache")
		)
		{
			if ($real_oid === NULL)
			{
				$real_oid = $oid;
			}

			$fname = "/".str_replace("/","_",$oid);
			foreach($arr as $v)
			{
				$fname.="-".str_replace("/","_",str_replace(" ","_",$v));
			}

			if (strlen($fname) > 100)
			{
				$fname = "/".md5($fname);
			}

			return self::file_get_pt_oid("html", $real_oid, $fname);
		}
		else
		{
			return false;
		}
	}

	/** Writes the given data to a cache file in the main cache folder
		@attrib params=pos api=1

		@param key required type=string
			String that is used to compose the path and filename which holds the cached data.

		@param value required type=string
			Cached data.

		@errors
			none

		@returns
			none

		@comment
			Not recommended to use.

		@examples
			$cache = get_instance('cache');
			$cache->file_set('foo', 'bar');
	**/
	public static function file_set($key,$value)
	{
		if (aw_ini_isset("cache.page_cache"))
		{
			$fname = aw_ini_get("cache.page_cache");
			$hash = md5($key);
			$fname .= "/".$hash{0};
			if (!is_dir($fname))
			{
				mkdir($fname, 0777);
				chmod($fname, 0777);
			}

			$fname .= "/{$key}";
			self::put_file(array("file" => $fname, "content" => $value));
			chmod($fname, 0666);
		}
	}

	/** Reads a cached file from the main cache folder
		@attrib params=pos api=1

		@param key required type=string
			String that is used to set the filename in cache.

		@errors
			none

		@returns
			Contents of the file in cache.
			false if page_cache is not set in aw.ini of file is not found

		@comment
			Not recommended to use.

		@examples
			$cache = get_instance('cache');
			$cache->file_set('foo', 'bar');
			echo $cache->file_get('foo'); // prints 'bar'

	**/
	public static function file_get($key)
	{
		if (!aw_ini_isset("cache.page_cache"))
		{
			return false;
		}

		return self::get_file(array(
			"file" => self::get_fqfn($key)
		));
	}

	public static function get_fqfn($key)
	{
		$hash = md5($key);

		if ($key{0} == "/")
		{
			return aw_ini_get("cache.page_cache")."/".$hash{0}.$key;
		}
		else
		{
			return aw_ini_get("cache.page_cache")."/".$hash{0}."/".$key;
		}
	}

	/** Returns cache file modified time
		@attrib params=pos api=1

		@param key required type=string
			String that is used to set the filename in cache.
		@errors
			none

		@returns int
			timestamp
		@examples
			$cache = get_instance('cache');
			$cache->file_set('foo', 'bar');
			sleep(5);
			var_dump($cache->get_modified_time('foo');
	**/
	public static function get_modified_time($key)
	{
		$hash = md5($key);
		if ($key{0} === "/")
		{
			$file = aw_ini_get("cache.page_cache")."/".$hash{0}.$key;
		}
		else
		{
			$file =  aw_ini_get("cache.page_cache")."/".$hash{0}."/".$key;
		}

		if(is_readable($file))
		{
			return filectime($file);
		}

		return false;
	}

	/** Returns cache file contents if the cache is not older than the given time
		@attrib params=pos api=1

		@param key required type=string
			String that is used to set the filename in cache.

		@param ts required type=int
			Timestamp

		@errors
			none

		@returns
			- Contents of the file in cache.
			- false if the cache file does not exist
			- false if page_cache is not set in aw.ini
			- false if supplied timestamp has newer time than the file's modification time

		@comment
			Checks, if the file in cache modification time is older than the time supplied via parameter. If it is older, then returns false, else filecontent.

		@examples
			$cache = get_instance('cache');
			$cache->file_set('foo', 'bar');
			sleep(5);
			// file in cache is newer than supplied timestamp, so file's content is returned
			var_dump($cache->file_get_ts('foo', time() - 3600));
			// file in cache is older than supplied timestamp, so false is returned
			var_dump($cache->file_get_ts('foo', time()));

	**/
	public static function file_get_ts($key, $ts)
	{
		if (!aw_ini_isset("cache.page_cache"))
		{
			return false;
		}

		$fqfn = self::get_fqfn($key);
		if (file_exists($fqfn) && filemtime($fqfn) < $ts)
		{
			return false;
		}

		return self::get_file(array("file" => $fqfn));
	}

	/** Clears the given file from the cache
		@attrib params=pos api=1

		@param key required type=string
			String that is used to set the filename in cache.

		@errors
			none

		@returns
			none

		@comment
			Deletes the file from cache

		@examples
			$cache = get_instance('cache');
			$cache->file_set('foo', 'bar');
			var_dump($cache->file_get('foo')); // prints 'bar'
			$cache->file_invalidate('foo');
			var_dump($cache->file_get('foo')); // prints false

	**/
	public static function file_invalidate($key)
	{
		if (aw_ini_isset("cache.page_cache"))
		{
			unlink(self::get_fqfn($key));
		}
	}

	/** Sets cache content for parent and oid and function
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache
		@param oid required type=int
			Object id. The last number of the id is used as a folder name created under the folder specified by $pt parameter
		@param fn required type=string
			Filename containing cached data
		@param cont required type=string
			Data to be cached
		@errors
			Throws error when file cannot be opened for writing

		@returns
			none

		@comment
			none

		@examples
			$cache = get_instance('cache');
			$cache->file_set_pt_oid('foo', 1234, 'bar', 'Hello World');
			// creates into folder $site/pagecache/foo/4/ file bar which contains 'Hello World'

	**/
	public static function file_set_pt_oid($pt, $oid, $fn, $cont)
	{
		return self::file_set_pt($pt, substr($oid, -1, 1), $fn, $cont);
	}

	/** Returns cache content for parent and oid and function
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache
		@param oid required type=int
			Object id. The last number of the id is used as a folder name created under the folder specified by $pt parameter
		@param fn required type=string
			Filename containing cached data

		@errors
			none

		@returns
			false if file cannot be opened for reading

		@comment
			none

		@examples
			$cache = get_instance('cache');
			$cache->file_set_pt_oid('foo', 1234, 'bar', 'Hello World');
			$val = $cache->file_get_pt_oid('foo', 1234, 'bar');
			echo $val; // prints 'Hello World'
	**/
	public static function file_get_pt_oid($pt, $oid, $fn)
	{
		return self::file_get_pt($pt, substr($oid, -1, 1), $fn);
	}

	/** Returns cache content for parent and oid and function if it is not older than the given timestamp
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache
		@param oid required type=int
			Object id. The last number of the id is used as a folder name created under the folder specified by $pt parameter
		@param fn required type=string
			Filename containing cached data
		@param ts required type=int
			Timestamp
		@errors
			none

		@returns
			- file content
			- false if timestamp is bigger than file modification time
			- false if file cannot be opened for reading

		@comment
			Checks, if the file in cache modification time is older than the time supplied via parameter. If it is older, then returns false, else filecontent.


		@examples
			$cache = get_instance('cache');
			$cache->file_set_pt_oid('foo', 1234, 'bar', 'Hello World');
			sleep(5);
			// file in cache is newer than supplied timestamp, so file's content is returned
			var_dump($cache->file_get_pt_oid_ts('foo', 1234, 'bar', time() - 3600));

			// file in cache is older than supplied timestamp, so false is returned
			var_dump($cache->file_get_pt_oid_ts('foo', 1234, 'bar', time()));


	**/
	public static function file_get_pt_oid_ts($pt, $oid, $fn, $ts)
	{
		return self::file_get_pt_ts($pt, substr($oid, -1, 1), $fn, $ts);
	}

	/** Sets cache content for parent folder and function
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache
		@param subf required type=string
			Folder under the folder specified by $pt parameter
		@param fn required type=string
			Filename containing cached data
		@param cont required type=string
			Data to be cached
		@errors
			Throws error when file cannot be opened for writing

		@returns
			none

		@comment
			none

		@examples
			$cache = get_instance('cache');
			$cache->file_set_pt('foo', 'asd', 'bar', 'Hello World');
			// creates into folder $site/pagecache/foo/asd/ file bar which contains 'Hello World'
	**/
	public static function file_set_pt($pt, $subf, $fn, $cont)
	{
		$fq = aw_ini_get("cache.page_cache")."/{$pt}/{$subf}/{$fn}";
		$f = is_writable($fq) ? fopen($fq, "w") : false;
		if (!$f)
		{
			return;
		}
		fwrite($f, $cont);
		fclose($f);
		chmod($fq, 0666);
	}

	/** Returns cache content for parent folder function
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache
		@param subf required type=string
			Folder under the folder specified by $pt parameter
		@param fn required type=string
			Filename containing cached data

		@errors
			none

		@returns
			- file content
			- false if file does no exist in cache or cannot be opened for reading


		@comment
			none

		@examples
			$cache = get_instance('cache');
			$cache->file_set_pt('foo', 'asd', 'bar', 'Hello World');
			$val = $cache->file_get_pt('foo', 'asd', 'bar');
			echo $val; // prints 'Hello World'

	**/
	public static function file_get_pt($pt, $subf, $fn)
	{
		$fq = aw_ini_get("cache.page_cache")."/{$pt}/{$subf}/{$fn}";
		if (!file_exists($fq))
		{
			return false;
		}
		$f = fopen($fq, "r");
		if (!$f)
		{
			return false;
		}
		$ret = fread($f, filesize($fq));
		fclose($f);
		return $ret;
	}

	/** Returns cache content for parent folder function if it is not older than the given timestamp
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache
		@param subf required type=string
			Folder under the folder specified by $pt parameter
		@param fn required type=string
			Filename containing cached data
		@param ts required type=int
			Timestamp

		@errors
			none

		@returns
			- file content
			- false if timestamp is bigger than file modification time
			- false if file cannot be opened for reading

		@comment
			Checks, if the file in cache modification time is older than the time supplied via parameter. If it is older, then returns false, else filecontent.

		@examples
			$cache = get_instance('cache');
			$cache->file_set_pt_oid('foo', 1234, 'bar', 'Hello World');
			sleep(5);
			// file in cache is newer than supplied timestamp, so file's content is returned
			var_dump($cache->file_get_pt_ts('foo', 1234, 'bar', time() - 3600));

			// file in cache is older than supplied timestamp, so false is returned
			var_dump($cache->file_get_pt_ts('foo', 1234, 'bar', time()));


	**/
	public static function file_get_pt_ts($pt, $subf, $fn, $ts)
	{
		$fq = aw_ini_get("cache.page_cache")."/".$pt."/".$subf."/".$fn;

		if (!file_exists($fq) || filemtime($fq) < $ts)
		{
			return false;
		}

		return file_get_contents($fq);
	}

	/** Clears cache for parent
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache

		@errors
			none

		@returns
			none

		@comment
			Clears the cache folder

		@examples
			$cache = get_instance('cache');
			$cache->file_clear_pt('foo');

	**/
	public static function file_clear_pt($pt)
	{
		if (aw_global_get("no_cache_flush") == 1)
		{
			return;
		}

		if ($pt === "html" and aw_ini_get("cache.manual_html_clear") or !aw_ini_get("cache.use_html_cache"))
		{
			return;
		}

		if ($pt === "menu_area_cache" and aw_ini_get("template_compiler.no_menu_area_cache"))
		{
			return;
		}

		if ($pt === "acl" and aw_ini_get("acl.no_check"))
		{
			return;
		}

		// now, this is where the magic happens.
		// basically, we rename the whole folder and clear it's contents later.
		$cache_dir = aw_ini_get("cache.page_cache");
		$fq = "{$cache_dir}/{$pt}";
		$nn = "{$cache_dir}/temp/{$pt}_".gen_uniq_id();

		rename($fq, $nn);
		self::_crea_fld($pt);
	}

	/** Clears cache for parent and oid
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache

		@param oid required type=int
			Object id. The last number of the id is used as a folder name created under the folder specified by $pt parameter

		@errors
			throws error if the folder cannot be renamed

		@returns
			none

		@examples
			$cache = get_instance('cache');
			$cache->file_clear_pt_oid('foo', 1234);
	**/
	public static function file_clear_pt_oid($pt, $oid)
	{
		$of = substr($oid, -1, 1);
		$cachedir = aw_ini_get("cache.page_cache");
		$fq = "{$cachedir}/{$pt}/{$of}";
		$nn = "{$cachedir}/temp/{$pt}_{$of}_".gen_uniq_id();

		rename($fq, $nn);

		// recreate
		mkdir($fq, 0777);
		chmod($fq, 0777);
	}

	/** Clears cache for parent oid and function
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache
		@param oid required type=int
			Object id. The last number of the id is used as a folder name created under the folder specified by $pt parameter
		@param fn required type=string
			Filename containing cached data

		@errors
			none

		@returns
			none

		@comment
			deletes the file from cache

		@examples
			$cache = get_instance('cache');
			$cache->file_clear_pt_oid_fn('foo', 1234, 'bar');

	**/
	public static function file_clear_pt_oid_fn($pt, $oid, $fn)
	{
		$of = substr($oid, -1, 1);
		$fq = aw_ini_get("cache.page_cache")."/{$pt}/{$of}/{$fn}";

		// here we know the full path to the file, so just delete the damn thing
		if(file_exists($fq))
		{
			unlink($fq);
		}
	}

	/** Clears cache for parent and folder
		@attrib params=pos api=1

		@param pt required type=string
			Folder under $site/pagecache
		@param subf required type=string
			Folder under the folder specified by $pt parameter

		@errors
			Throws error when the folder cannot be renamed.

		@returns
			none

		@comment
			none

		@examples
			$cache = get_instance('cache');
			$cache->file_clear_pt_sub('foo', 'asd');

	**/
	public static function file_clear_pt_sub($pt, $subf)
	{
		$cachedir = aw_ini_get("cache.page_cache");
		$fq = "{$cachedir}/{$pt}/{$subf}";
		$nn = "{$cachedir}/temp/{$pt}_{$subf}_" . gen_uniq_id();

		if (!rename($fq, $nn))
		{
			error::raise(array(
				"id" => "ERR_CACHE_CLEAR",
				"msg" => sprintf(t("cache::file_clear_pt_sub(%s, %s): could not rename %s to %s!"), $pt, $sub, $fq, $nn)
			));
		}

		// recreate
		mkdir($fq, 0777);
		chmod($fq, 0777);
	}

	private static function _crea_fld($f)
	{
		$fq = aw_ini_get("cache.page_cache")."/{$f}";
		mkdir($fq, 0777);
		chmod($fq, 0777);
		for($i = 0; $i < 16; $i++)
		{
			$ffq = $fq ."/".($i < 10 ? $i : chr(ord('a') + ($i- 10)));
			mkdir($ffq, 0777);
			chmod($ffq, 0777);
		}
	}

	/** Returns client-unserialized cache file
		@attrib params=name api=1

		@param fname required type=string
			Fully qualified file name minus basedir.

		@param unserializer required type=array
			Reference in form of array("classname","function") to the unserializer function.

		@errors
			none

		@returns
			- false if file is not readable
			- false if valid cache_id could not be calculated

		@comment
			none

		@examples
			none

	**/
	public static function get_cached_file($args = array())
	{
		extract($args);

		// now calculate fqfn
		// XXX: is stripping dots good enough?
		$pathinfo = pathinfo($args["fname"]);
		$dirname = str_replace(".","",$pathinfo["dirname"]);

		$fqfn = aw_ini_get("basedir") . $dirname . "/" . $pathinfo["basename"];
		if (!is_file($fqfn) || !is_readable($fqfn))
		{
			$fqfn = aw_ini_get("site_basedir") . $dirname . "/" . $pathinfo["basename"];
		}

		// this is all nice and good, but I need a way to load files from the
		// site directory as well.

		if (is_file($fqfn) && is_readable($fqfn))
		{
			// figure out the cache id for the file
			// xml/properties/search.xml becomes properties_search.cache
			// xml/orb/search.xml becomes orb_search.cache
			$prefix = substr($dirname,strrpos($dirname,"/")+1);
			if (strlen($prefix) == 0)
			{
				// could not calculate a valid cache_id, bail out
				return false;
			}
			$cache_id = $prefix . "_" . $pathinfo["basename"] . ".cache";
		}
		else
		{
			// no source file, bail out

			// an idea to consider, perhaps we _should_ have a way to
			// work only with serialized files?
			return false;
		}

		$cachedir = aw_ini_get("cache.page_cache");
		$cachefile = "{$cachedir}/{$cache_id}";

		// now get mtime for both files, source and cache
		$source_mtime = filemtime($fqfn);
		$cache_mtime = 0;
		$src = "";
		if (file_exists($cachefile))
		{
			$cache_mtime = filemtime($cachefile);

			// get the cache contents here, so we can check whether it is empty, cause for some weird reason
			// cache files get to be empty sometimes, damned if I know why

			$src = self::get_file(array(
				"file" => $cachefile,
			));
		}

		if (($source_mtime > $cache_mtime) || (strlen($src) < 1))
		{
			//print "need to reparse<br />";
			// 1) get an instance of the unserializer class,

			$clobj = &$args["unserializer"][0];
			$clmeth = $unserializer[1];
			if (is_object($clobj) && method_exists($clobj,$clmeth))
			{
				// 2) get the contents of the source file
				$contents = self::get_file(array("file" => $fqfn));
				// 3) pass them to unserializer
				$result = $clobj->$clmeth(array(
					"fname" => $fqfn,
					"content" => $contents,
				));
			};
			$clobj = &$args["loader"][0];
			$clmeth = $loader[1];
			if (is_object($clobj) && method_exists($clobj,$clmeth))
			{
				$clobj->$clmeth(array("data" => $result));
			};
			if (is_writable($cachedir))
			{
				$ser_res = aw_serialize($result,SERIALIZE_PHP);
				self::put_file(array(
					"file" => $cachefile,
					"content" => $ser_res,
				));
				chmod($cachefile, 0666);
			};
			// Now I somehow need to retrieve the results of unserialization
			// and write them out to the file
			// 4) aquire reference to results
		}
		else
		{
			// 1) get the contents of cached file
			// 2) awunserialize the data

			$clobj = &$args["loader"][0];
			$clmeth = $loader[1];
			if (is_object($clobj) && method_exists($clobj,$clmeth))
			{
				$clobj->$clmeth(array("data" => aw_unserialize($src)));
			}
		}
	}

	function _get_cache_files($fld)
	{
		if ($dir = opendir($fld))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (!($file === "." || $file === ".."))
				{
					if (is_dir($fld."/".$file))
					{
						$this->_get_cache_files($fld."/".$file);
					}
					else
					{
						$this->cache_files[] = $file;
						$this->cache_files2[] = $fld."/".$file;
					}
				}
			}
		}
	}


	/** Returns the date for the last time any object in the system was modified
		@attrib api=1

		@errors
			none

		@returns
			Timestamp of the last modified object.

		@comment
			If the method is called first time and there is no objlastmod file in cache, then last modified object is taken (from the objects table by the field modified) and it will be cached into objlastmod file in cache. If site_show.objlastmod_only_menu aw.ini setting is set (for example to 1), then last modified menu object is taken (class_id = CL_MENU)

		@examples
			$cache = get_instance('cache');
			echo date("d.m.Y H:m:s", $cache->get_objlastmod());
	**/
	function get_objlastmod()
	{
		static $last_mod;
		if (!$last_mod)
		{
			if (($last_mod = self::file_get("objlastmod")) === false)
			{
				$add = "";
				if (aw_ini_get("site_show.objlastmod_only_menu"))
				{
					$add = " WHERE class_id = ".CL_MENU;
				}
				$last_mod = $this->db_fetch_field("SELECT MAX(modified) as m FROM objects".$add, "m");
				self::file_set("objlastmod", $last_mod);
			}
		}
		return $last_mod;
	}

	/** Completely clears the cache.
		@attrib params=pos api=1

		@errors
			none

		@returns
			none


		@examples
			$cache = get_instance('cache');
			$cache->file_set('foo', 'bar');
			echo $cache->file_get('foo'); // prints 'bar'
			$cache->full_flush();
			echo $cache->file_get('foo'); // prints nothing

	**/
	function full_flush()
	{
		if (aw_global_get("no_cache_flush") == 1)
		{
			return;
		}
		$this->cache_files = array();
		$this->cache_files2 = array();
		$this->_get_cache_files(aw_ini_get("cache.page_cache"));

		foreach($this->cache_files2 as $file)
		{
			unlink($file);
		}
	}
}
?>
