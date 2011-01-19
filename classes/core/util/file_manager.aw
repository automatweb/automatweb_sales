<?php

/**
File system manager for AutomatWeb. Only forward slash '/' used as delimiter
@errors
	throws awex_filemanager_filesdir if errors encountered with AW standard files directory (creation, modification, ...)
**/
class filesystem_manager
{
	private static $initialized = false;

	// configuration
	private static $site_files_dir = "";

	/** Checks and converts given file system path to absolute canonical form. Returns empty string if file doesn't exist
	@attrib api=1 params=pos
	@param path type=string
	@return string
	**/
	public static function safe_path($path)
	{
		$path = realpath($path);
		return false === $path ? "" : str_replace("\\", "/", $path);
	}

	/**
		@attrib api=1 params=pos
		@param file_name type=string
			If left empty, random name is generated
		@returns
			Absolute path with file name
	**/
	public static function generate_path($file_name = "")
	{
		self::setup();
		$files_dir = self::$site_files_dir;

		if (!empty($file_name))
		{
			$file_name = basename($file_name);
			$i = 0;
			while(1)
			{
				$fn = "{$files_dir}{$i}/{$file_name}";
				$dir = "{$files_dir}{$i}/";

				if (!is_dir($dir))
				{
					$r = mkdir($dir, 0777);
					if (!$r)
					{
						throw new awex_filemanager_filesdir("Couldn't create directory '{$dir}' for file '{$file_name}'");
					}
				}

				if (!file_exists($fn))
				{
					return $fn;
				}

				++$i;
			}
		}

		// generate random name
		$filename = gen_uniq_id();
		$dir = "{$files_dir}" . substr($filename, 0, 1) . "/";

		if (!is_dir($dir))
		{
			$r = mkdir($dir, 0705);
			if (!$r)
			{
				throw new awex_filemanager_filesdir("Couldn't create directory '{$dir}' for autonamed file '{$filename}'");
			}
		}

		$file = $dir . $filename;
		return $file;
	}

	private static function setup()
	{
		if (!self::$initialized)
		{
			$files_dir = aw_ini_get("file.site_files_dir");
			if (!is_dir($files_dir))
			{
				$r = mkdir($files_dir, 0777);

				if (!$r)
				{
					throw new awex_filemanager_filesdir("Couldn't create files directory for site ('{$files_dir}')");
				}
			}

			self::$site_files_dir = $files_dir;
			self::$initialized = true;
		}
	}
}

class awex_filemanager extends aw_exception {}

/** Directory related errors **/
class awex_filemanager_filesdir extends awex_filemanager {}
