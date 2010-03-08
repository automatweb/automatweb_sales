<?php

class file_archive extends core
{
	const COMPRESS_ZIP = 1;
	const COMPRESS_TAR_GZIP = 2;
	const COMPRESS_TAR_BZIP = 3;

	private $compressor;

	function file_archive()
	{
		$this->init();
		$this->set_compression_method(file_archive::COMPRESS_ZIP);
	}

	/** Sets the compression method. 
		@attrib api=1 params=pos

		@param method required type=int
			One of the compression method constants. The default is file_archive::COMPRESS_ZIP

		@comment
			Must be called before anything is added to the archive.
	**/
	function set_compression_method($method)
	{
		$comp_name = "file_archive_compressor_".$method;
		$this->compressor = new $comp_name;
	}

	/** Resets the archive class and prepares for a new archive.
		@attrib api=1

		@comment
			You only need to call this if you want to start a new one, after already creating one. 
	**/
	function new_archive()
	{
		return $this->compressor->clean();
	}

	/** Adds a file to the archive from the file system
		@attrib api=1 params=pos

		@param fp required type=string
			The path to the file to add to the archive

		@param file_name optional type=string
			The name of the file in the archive. If empty, the name of the added file is used
	
		@param pt_folder optional type=string
			The name of the folder in the archive to add the file to

		@examples
			$i= new file_archive; 
			$i->set_compression_method(file_archive::COMPRESS_TAR_BZIP); 
			$i->add_file_fs("/www/dev/terryf/site/aw.ini"); 
			$i->add_folder("a"); 
			$i->add_folder("b", "a"); 
			$i->add_file_fs("/www/dev/terryf/site/aw.ini", "a/b"); 
			$i->save_as_file("/home/kristo/tmp.tar.gz");	
	**/
	public function add_file_fs($fp, $file_name = "", $pt_folder = "")
	{
		return $this->compressor->add_file_fs($fp, $file_name, $pt_folder);
	}


	/** Adds a file to the archive from a string
		@attrib api=1 params=pos

		@param content required type=string
			The content of the file to add

		@param file_name required type=string
			The name of the file in the archive. 
	
		@param pt_folder optional type=string
			The name of the folder in the archive to add the file to
	**/
	public function add_file_string($content, $file_name, $pt_folder = "")
	{
		return $this->compressor->add_file_string($content, $file_name, $pt_folder);
	}	

	/** Adds a folder to the archive
		@attrib api=1 params=pos

		@param name required type=string
			The name of the folder to add

		@param pt_folder optional type=string
			The name of the parent folder for the new folder

	**/
	public function add_folder($name, $pt_folder = "")
	{
		return $this->compressor->add_folder($name, $pt_folder);
	}

	/** Saves the current archive as a file
		@attrib api=1 params=pos

		@param file_name required type=string
			The name of the file to save to.
	**/
	public function save_as_file($file_name)
	{
		return $this->compressor->save_as_file($file_name);
	}

	/** Returns the content of the current archive
		@attrib api=1 
	**/		
	public function get()
	{
		return $this->compressor->get();
	}

	/** Serves the current archive to the user as a downloadable file
		@attrib api=1 params=pos

		@param file_name required type=string
			The name of the archive file
	**/		
	public function serve_to_user($file_name)
	{
		return $this->compressor->serve_to_user($file_name);
	}
}

class file_archive_compressor_1 extends file_archive_fs_based_compressor_base
{
	private function get_zip_path()
	{
		$z = aw_ini_get("server.zip_path");
		if (!is_executable($z))
		{
			$z = trim(`which zip`);
		}
		return $z;
	}

	protected function get_compressor_command($file_name)
	{
		return $this->get_zip_path()." -r $file_name *";
	}

	protected function get_file_extension()
	{
		return "zip";
	}

	protected function get_mime_type()
	{
		return "application/zip";
	}
}

class file_archive_compressor_2 extends file_archive_fs_based_compressor_base
{
	protected function get_compressor_command($file_name)
	{
		return $this->get_tar_path()." cvfz $file_name *";
	}

	protected function get_file_extension()
	{
		return "tar.gz";
	}

	protected function get_mime_type()
	{
		return "application/x-gtar";
	}

	private function get_tar_path()
	{
		$z = aw_ini_get("server.tar_path");
		if (!is_executable($z))
		{
			$z = trim(`which tar`);
		}
		return $z;
	}
}

class file_archive_compressor_3 extends file_archive_fs_based_compressor_base
{
	protected function get_compressor_command($file_name)
	{
		return $this->get_tar_path()." cvfj $file_name *";
	}

	protected function get_file_extension()
	{
		return "tar.bz";
	}

	protected function get_mime_type()
	{
		return "application/x-gtar";
	}

	private function get_tar_path()
	{
		$z = aw_ini_get("server.tar_path");
		if (!is_executable($z))
		{
			$z = trim(`which tar`);
		}
		return $z;
	}
}

abstract class file_archive_fs_based_compressor_base
{
	protected $folder = "";

	function __construct()
	{
		$this->clean();
	}

	function clean()
	{
		if ($this->folder != "")
		{
			$this->_req_del_fld($this->folder);
		}
		$this->folder = aw_ini_get("server.tmpdir")."/aw_fld_compr_".gen_uniq_id();
		mkdir($this->folder, 0777);
		chmod($this->folder, 0777);
	}

	public function add_file_fs($fp, $file_name = "", $pt_folder = "")
	{
		copy($fp, $this->folder."/".$pt_folder."/".($file_name == "" ? basename($fp) : $file_name));
	}

	public function add_file_string($content, $file_name, $pt_folder = "")
	{
		$f = fopen($this->folder."/".$pt_folder."/".$file_name, "w");
		fwrite($f, $content);
		fclose($f);
	}	

	public function add_folder($name, $pt_folder = "")
	{
		mkdir($this->folder."/".$pt_folder."/".$name, 0777);
		chmod($this->folder."/".$pt_folder."/".$name, 0777);
	}

	public function save_as_file($file_name)
	{
		chdir($this->folder);
		
		$cmd = $this->get_compressor_command($file_name);
		$res = `$cmd`;
	}

	public function get()
	{
		$tmp_fn = $this->folder."/".gen_uniq_id().".".$this->get_file_extension();
		$this->save_as_file($tmp_fn);
		$fc = file_get_contents($tmp_fn);
		unlink($tmp_fn);
		return $fc;
	}

	public function serve_to_user($file_name)
	{
		$tmp_fn = $this->folder."/".gen_uniq_id().$this->get_file_extension();
		$this->save_as_file($tmp_fn);

		header("Content-type: ".$this->get_mime_type());
		header("Content-length: ".filesize($tmp_fn));
		header("Content-disposition: inline; filename=".$file_name.";");
		readfile($tmp_fn);
		unlink($tmp_fn);
	}

	private function _req_del_fld($dir)
	{
		if ($dh = opendir($dir)) 
		{
			while (($file = readdir($dh)) !== false) 
			{
				if ($file == "." || $file == "..")
				{
					continue;
				}
				if (is_dir($dir."/".$file))
				{
					$this->_req_del_fld($dir."/".$file);
					rmdir($dir."/".$file);
				}
				else
				{
					unlink($dir."/".$file);
				}
			}
			closedir($dh);
			rmdir($dir);
		}
	}

	function __destruct()
	{
		if ($this->folder)
		{
			$this->_req_del_fld($this->folder);
		}
	}

	abstract protected function get_compressor_command($file_name);
	abstract protected function get_file_extension();
	abstract protected function get_mime_type();
}