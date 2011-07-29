<?php

define("IMAGE_PNG", 1);
define("IMAGE_JPEG", 2);
define("IMAGE_GIF", 3);
define("IMAGE_WBMP", 4);

class image_convert extends core
{
	var $driver;

	function image_convert()
	{
		$this->init();

		$driver = $this->_get_driver();

		if ($driver != "")
		{
			$this->driver = new $driver;
			$this->driver->ref = $this;
		}
	}

	// this is here, because the authors of the php gd module are stupid idiots.
	// there is *NO* safe way of telling which version of gd is installed for all 4.x versions of php
	// except for this.
	function my_gd_info()
	{
		ob_start();
		eval("phpinfo();");
		$info = ob_get_contents();
		ob_end_clean();

		foreach(explode("\n", $info) as $line)
		{
			if(strpos($line, "GD Version")!==false)
			{
				$ret = trim(str_replace("GD Version", "", strip_tags($line)));
			}
		}
		return $ret;
	}

	function _get_driver()
	{
		$driver = "";

		// detect if gd is available
		if (function_exists("imagecreatetruecolor"))
		{
			// some kind of gd, but check to be sure
			if (function_exists("gd_info"))
			{
				$dat = gd_info();
				if (!empty($dat["JPG Support"]) && !empty($dat["PNG Support"]) && isset($dat["GD Version"]) && strpos($dat["GD Version"], "2.") !== false)
				{
					// accept all gd's that can use jpg and png and have version number 2.x
					$driver = "gd";
				}
			}
			else
			{
				// god this sucks
				$ver = $this->my_gd_info();
				if (strpos($ver, "2.") !== false)
				{
					// accept all gd's have version number 2.x
					$driver = "gd";
				}
			}
		}

		if ($driver == "")
		{
			// try imagick module
			if (extension_loaded("imagick"))
			{
				$driver = "imagick_module";
			}
		}

		if ($driver == "")
		{
			// try to detect imagemagick
			$convert = aw_ini_get("server.convert_dir");
			if (file_exists($convert) && is_executable($convert))
			{
				$driver = "imagick";
			}
		}

		if ($driver == "")
		{
			return "";
		}

		$driver = "_int_image_convert_driver_".$driver;
		return $driver;
	}

	/**
		@attrib api=1
		@comment
			returns true/false depending on if there is a converter driver loaded(if class methods can be used).
		@returns
			true if image manipulation is possible
			false otherwise
	**/
	function can_convert()
	{
		return $this->_get_driver() != "" ? true : false;
	}

	/**
		@attrib api=1 params=pos
		@param str required type=string
			image source
		@comment
			loads image from string
		@errors
			raises ERR_IMAGE_DRIVER error if there isn't any driver loaded.
		@examples
			$img = get_instance("core/converters/image_convert");
			$fc = $this->get_file(array(
				"file" => "local/file/path/and/file/name.jpg",
			));
			$img->load_from_string($fc);
			list($width, $height) = $img->size();
			print $width." x ".$height;
	**/
	function load_from_string($str)
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		$this->driver->load_from_string($str);
	}

	/**
		@attrib api=1 params=pos
		@param file required type=string
		@comment
			loads image from given file
		@errors
			raises ERR_IMAGE_DRIVER error if there isn't any driver loaded.
		@examples
			$img = get_instance("core/converters/image_convert");
			$img->load_from_file(array(
				"file" => "local/file/path/and/file/name.jpg",
			));
			list($width, $height) = $img->size();
			print $width." x ".$height;
	**/
	function load_from_file($str)
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		$this->driver->load_from_file($str);
	}

	/**
		@attrib api=1
		@comment
			gets currently loaded image's info
		@errors
			raises ERR_IMAGE_DRIVER error if there isn't any driver loaded.
		@returns
			currently loaded image's info
			array(width,height);
		@examples
			#load_from_file
	**/
	function size()
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		return $this->driver->size();
	}

	////
	// !resize
	// x, y, width, height, new_width, new_height
	/**
		@attrib params=name api=1
		@param x required type=int
			the x point from where to copy
		@param y required type=int
			the y point from where to copy
		@param width required type=int
			the width of the area to copy
		@param height required type=int
			the height of the area to copy
		@param new_width required type=int
			images new width.
		@param new_height required type=int
			images new height.
		@comment
			resizes currently loaded image. takes the old image and copys a portion from the old image(top left corner marked by $x/$y)
			to new and streches it if copyed height/width and new height/width don't match.
		@errors
			raises ERR_IMAGE_DRIVER error if there isn't any driver loaded.
		@examples
			$img = get_instance("core/converters/image_convert");
			#load_from_file
			$img->load_from_file(array(
				"file" => "file.jpg",
			));
			$img->resize(
				"x" => 50,
				"y" => 50,
				"width" => 320,
				"height" => 240,
				"new_width" => 100,
				"new_height" => 80,
			);
			list($width, $height) = $img->size();
			print ($width." x ".$height);
			// takes the old image, cuts the part width size 320x240px starting from point x=50, y=50.
			// then resizes the part to 100x80px and replaces the currently loaded image with the brand new resized one.
			// ..and prints out new dimensions (100 x 80)
	**/
	function resize($arr)
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		return $this->driver->resize($arr);
	}

	/**
		@attrib params=pos api=1
		@param width required type=int
			images new width
		@param height required type=int
			images new height
		@comment
			just resizes the image to $width/$height
		@errors
			raises ERR_IMAGE_DRIVER error if there isn't any driver loaded.
		@examples
			$img = get_instance("core/converters/image_convert");
			#load_from_file
			$img->load_from_file(array(
				"file" => "file.jpg",
			));
			$img->resize_simple(
				"width" => 100,
				"height" => 80,
			);
			// resizes the image to 100x80 in this case, wheather the old image was smaller or bigger.
	**/
	function resize_simple($width, $height)
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		list($w, $h) = $this->size();
		return $this->driver->resize(array(
			"x" => 0,
			"y" => 0,
			"height" => $h,
			"width" => $w,
			"new_width" => $width,
			"new_height" => $height
		));
	}

	/**
		@attrib api=1 params=pos
		@param type required type=int
			the image format you want to be returned, must be one of the following:
			1 - png
			2 - jpeg
			3 - gif
			4 - wbmp
		@comment
			returns the currently loaded image as a string.
		@returns
			currently loaded image as a string.
		@errors
			raises error ERR_IMAGE_TYPE, if param $type is not one of the allowed types
		@examples
			$img = get_instance("core/converters/image_convert");
			#load_from_file
			$img->load_from_file(
				"file" => "img.jpg",
			);
			#resize_simple
			$img->resize_simple(
				"width" => "100",
				"height" => "80",
			);
			$img_contents = $img->get(4);
			// returns image (resized to 100x80 and converted to wbmp) as a string.

	**/
	function get($type)
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		return $this->driver->get($type);
	}

	/**
		@attrib api=1 params=pos
		@param filename required type=string
			the filename to where to save the image
		@param type required type=int
			the image format you want to be returned, must be one of the following:
			1 - png
			2 - jpeg
			3 - gif
			4 - wbmp

		@comment
			saves the currently loaded image into $filename in $type format.
		@errors
			raises ERR_IMAGE_TYPE, if param $type is not one of the allowed types
		@examples
			$img = get_instance("core/converters/image_convert");
			#load_from_file
			$img->load_from_file(
				"file" => "img.jpg",
			);
			#resize_simple
			$img->resize_simple(
				"width" => "100",
				"height" => "80",
			);
			$img_contents = $img->save("new_image.jpg", 2);
			// saves image (resized to 100x80 and converted to jpeg) to new_image.jpg.
	**/
	function save($filename, $type)
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		return $this->driver->save($filename, $type);
	}

	/**
		@attrib api=1
		@comment
			returns an instance of this class that has the same image loaded, but any operations on it, will not affect the original image
		@returns
			a copy on the class instance.
		@errors
			raises ERR_IMAGE_DRIVER error if there isn't any driver loaded.
		@examples
	**/
	function &copy()
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		$ic = new image_convert;
		$ic->driver = $this->driver->copy();
		return $ic;
	}

	/**
		@attrib api=1
		@comment
			destroys currently loaded image and thus, any changes made to it
		@errors
			raises ERR_IMAGE_DRIVER error if there isn't any driver loaded.
	**/
	function destroy()
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		$this->driver->destroy();
		unset($this);
	}

	/**
		@attrib api=1 params=name
		@param source required type=object
			the image object you want to copy onto current image
		@param x required type=int
			the point from where you want to paste the source pic
		@param y required type=int
			the point from where you want to paste the source pic
		@param pct required type=int
			an integer between 0 and 100. when set to 0, no action will be taken. when set to 100, the source pic is overwrites old picture completly(if source has any transparency then old picture is shown there).
		@comment
			merges two pictures together
		@examples
			$img = get_instance("core/converters/image_convert");
			$img2 = get_instance("core/converters/image_convert");

			#load_from_file
			$img->load_from_file(
				"file" => "img.jpg",
			);
			$img2->load_from_file(
				"file" => "logo.png"
			)
			$img->merge(array(
				"source" => $img2,
				"x" => 0,
				"y" => 0,
				"pct" => 50,
			));

			// $img now contains img.jpg picture, and a semi-transparent(50%) logo.png in top-left corner

		@errors
			raises ERR_IMAGE_DRIVER error if there isn't any driver loaded.
	**/
	function merge($img)
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		$this->driver->merge($img);
	}

	function set_error_reporting($rep)
	{
		error::raise_if(!($this->driver), array(
			"id" => "ERR_IMAGE_DRIVER",
			"msg" => t("image_covert: could not detect any supported imagehandlers!")
		));
		$this->driver->error_rep = $rep;
	}
	/**
		@attrib api=1
		@comment
			checks if any image manipulation can be done.
		@returns
			true if driver is loaded and everything is ok, false otherwise
	**/
	function is_error()
	{
		if (!$this->driver)
		{
			return true;
		}
		return $this->driver->is_error;
	}
}

class _int_image_convert_driver_gd extends core
{
	var $image;
	var $ref;
	var $error;
	var $is_error = false;

	function _int_image_convert_driver_gd()
	{
		$this->error_rep = true;
	}

	function load_from_string($str)
	{
		if (function_exists("imagecreatefromstring"))
		{
			$this->image = imagecreatefromstring($str);
		}
		else
		{
			// save temp file
			$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv");
			$this->put_file(array(
				"file" => $tn,
				"content" => $str
			));
			$this->load_from_file($tn);
			unlink($tn);
		}

		if (!$this->image)
		{
			if ($this->error_rep == false)
			{
				$this->is_error = true;
			}
			else
			{
				error::raise(array(
					"id" => ERR_IMAGE_FORMAT,
					"msg" => t("image_convert::gd_driver::load_from_string(): could not detect image format!")
				));
			}
		}
	}

	function load_from_file($tn)
	{
		$this->image = imagecreatefromjpeg($tn);
		if (!$this->image)
		{
			$this->image = imagecreatefrompng($tn);
		}
		if (!$this->image && function_exists("imagecreatefromgif"))
		{
			$this->image = imagecreatefromgif($tn);
		}
		if (!$this->image && function_exists("imagecreatefromwbmp"))
		{
			$this->image = imagecreatefromwbmp($tn);
		}
		if (!$this->image)
		{
			if ($this->error_rep == false)
			{
				$this->is_error = true;
			}
			else
			{
				error::raise(array(
					"id" => ERR_IMAGE_FORMAT,
					"msg" => t("image_convert::gd_driver::load_from_file(): could not detect image format!")
				));
			}
		}
	}


	function size()
	{
		return array(imagesx($this->image), imagesy($this->image));
	}

	////
	// !resize
	// x, y, width, htight, new_width, new_height
	function resize($arr)
	{
		set_time_limit(1200);
		$tmpimg = imagecreatetruecolor($arr["new_width"], $arr["new_height"]);
		imagecopyresampled($tmpimg, $this->image,0,0, $arr["x"], $arr["y"], $arr["new_width"], $arr["new_height"], $arr["width"], $arr["height"]);
		imagedestroy($this->image);
		$this->image = $tmpimg;
	}

	function get($type)
	{
		$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv");
		switch($type)
		{
			case IMAGE_PNG:
				imagepng($this->image, $tn);
				break;
			case IMAGE_JPEG:
				imagejpeg($this->image, $tn, 85);
				break;
			case IMAGE_GIF:
				imagegif($this->image, $tn);
				break;
			case IMAGE_WBMP:
				imagewbmp($this->image, $tn);
				break;
			default:
				$this->raise_error(ERR_IMAGE_TYPE, sprintf(t("image_convert::get(%s): unknown image type!"), $type));
		}
		$content = $this->get_file(array("file" => $tn));
		unlink($tn);
		return $content;
	}

	function save($tn, $type)
	{
		switch($type)
		{
			case IMAGE_PNG:
				imagepng($this->image, $tn);
				break;
			case IMAGE_JPEG:
				imagejpeg($this->image, $tn);
				break;
			case IMAGE_GIF:
				imagegif($this->image, $tn);
				break;
			case IMAGE_WBMP:
				imagewbmp($this->image, $tn);
				break;
			default:
				if ($this->error_rep == false)
				{
					$this->is_error = true;
				}
				else
				{
					$this->raise_error(ERR_IMAGE_TYPE, sprintf(t("image_convert::save(%s,%s): unknown image type!"), $filename,$type));
				}
		}
	}

	function copy()
	{
		$inst = new _int_image_convert_driver_gd;
		$inst->image = imagecreatetruecolor(imagesx($this->image), imagesy($this->image));
		imagecopy($inst->image, $this->image, 0, 0, 0, 0,imagesx($this->image), imagesy($this->image));
		return $inst;
	}

	function destroy()
	{
		imagedestroy($this->image);
		unset($this);
	}

	// $source, $x, $y, $pct
	function merge($arr)
	{
		extract($arr);
		// make transparency as well.
		$trans = imagecolorat ($source->driver->image, 0, 0);
		imagecolortransparent($source->driver->image, $trans);

		list($w, $h) = $source->size();

		imagecopymerge($this->image, $source->driver->image, $x, $y, 0,0, $w, $h, $pct);
	}
}

class _int_image_convert_driver_imagick extends core
{
	var $filename;
	var $identify;
	var $convert;
	var $copmposite;

	function _int_image_convert_driver_imagick()
	{
		$this->identify = aw_ini_get("server.identify_dir");
		$this->convert = aw_ini_get("server.convert_dir");
		$this->composite = aw_ini_get("server.composite_dir");
		$this->error_rep = true;
	}

	function load_from_string($str)
	{
		// save string to temp file
		$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv");
		$this->put_file(array(
			"file" => $tn,
			"content" => $str
		));
		$this->filename = $tn;
	}

	function load_from_file($str)
	{
		// copy to temp file
		$str = $this->get_file(array("file" => $str));
		if ($str === false)
		{
			if ($this->error_rep == false)
			{
				$this->is_error = true;
			}
			else
			{
				$this->raise_error(ERR_NO_FILE, sprintf(t("image_convert::load_from_file(%s): no such file!"), $str));
			}
		}
		$this->load_from_string($str);
	}


	function size()
	{
		$cmd = $this->identify." -format \"%w %h\" ".$this->filename;
		$op = shell_exec($cmd);
		return explode(" ", $op);
	}

	////
	// !resize
	// x, y, width, height, new_width, new_height
	function resize($arr)
	{
		extract($arr);
		$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv");
		$cmd = $this->convert." -geometry ".$new_width."x".$new_height."+".$x."+".$y."! ".$this->filename." ".$tn;
		$op = shell_exec($cmd);
		unlink($this->filename);
		$this->filename = $tn;
	}

	function get($type)
	{
		$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv");
		switch($type)
		{
			case IMAGE_PNG:
				shell_exec($this->convert." ".$this->filename." png:".$tn);
				break;
			case IMAGE_JPEG:
				$cmd = $this->convert." ".$this->filename." jpeg:".$tn;
				$op = shell_exec($cmd);
				break;
			case IMAGE_GIF:
				shell_exec($this->convert." ".$this->filename." gif:".$tn);
				break;
			case IMAGE_WBMP:
				shell_exec($this->convert." ".$this->filename." wbmp:".$tn);
				break;
			default:
				if ($this->error_rep == false)
				{
					$this->is_error = true;
				}
				else
				{
					$this->raise_error(ERR_IMAGE_TYPE, sprintf(t("image_convert::get(%s): unknown image type!"), $type));
				}
		}
		$fc = $this->get_file(array("file" => $tn));
		unlink($tn);
		return $fc;
	}

	function save($filename, $type)
	{
		$tn = $this->filename;
		switch($type)
		{
			case IMAGE_PNG:
				shell_exec($this->convert." ".$tn." png:".$filename);
				break;
			case IMAGE_JPEG:
				shell_exec($this->convert." ".$tn." jpeg:".$filename);
				break;
			case IMAGE_GIF:
				shell_exec($this->convert." ".$tn." gif:".$filename);
				break;
			case IMAGE_WBMP:
				shell_exec($this->convert." ".$tn." wbmp:".$filename);
				break;
			default:
				if ($this->error_rep == false)
				{
					$this->is_error = true;
				}
				else
				{
					$this->raise_error(ERR_IMAGE_TYPE, sprintf(t("image_convert::save(%s, %s): unknown image type!"), $filename,$type));
				}
		}
	}

	////
	// !returns an instance of this class that has the same image loaded, but any operations on it, will not affect the original image
	function &copy()
	{
		$inst = new _int_image_convert_driver_imagick;
		$inst->load_from_string($this->get(IMAGE_PNG));
		return $inst;
	}

	function destroy()
	{
		unlink($this->filename);
		unset($this);
	}

	////
	// !merges the current image with the image given as $img
	// $source, $x, $y, $pct
	function merge($arr)
	{
		extract($arr);
		$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv");

		$cmd = $this->composite." -geometry +".$x."+".$y." ".$source->driver->filename." ".$this->filename." ".$tn;
		shell_exec($cmd);
		unlink($this->filename);
		$this->filename = $tn;
	}
}


class _int_image_convert_driver_imagick_module extends core
{
	function _int_image_convert_driver_imagick()
	{
		$this->error_rep = true;
	}

	function load_from_string($str)
	{
		// save string to temp file
		$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv");
		$this->put_file(array(
			"file" => $tn,
			"content" => $str
		));

		$this->handle = imagick_readimage($tn);
		if ( imagick_iserror( $this->handle ) )
		{
			$reason = imagick_failedreason( $this->handle ) ;
			$description = imagick_faileddescription( $this->handle ) ;

			if ($this->error_rep == false)
			{
				$this->is_error = true;
			}
			else
			{
				$this->raise_error(ERR_NO_FILE, sprintf(t("image_convert::load_from_string(): could not read image from string! reason = %s, desc = %s"), $reason, $description));
			}
		}
	}

	function load_from_file($str)
	{
		if (!file_exists($str) || filesize($str) == 0)
		{
			if ($this->error_rep == false)
			{
				$this->is_error = true;
				return;
			}
			else
			{
				$this->raise_error(ERR_NO_FILE, sprintf(t("image_convert::load_from_file(%s): could not read image from file! reason = %s, desc = %s"), $str, $reason, $description));
				return;
			}
		}

		$this->handle = imagick_readimage($str);
		if ( imagick_iserror( $this->handle ) )
		{
			$reason = imagick_failedreason( $this->handle ) ;
			$description = imagick_faileddescription( $this->handle ) ;

			if ($this->error_rep == false)
			{
				$this->is_error = true;
			}
			else
			{
				$this->raise_error(ERR_NO_FILE, sprintf(t("image_convert::load_from_file(%s): could not read image from file! reason = %s, desc = %s"), $str, $reason, $description));
			}
		}
	}


	function size()
	{
		$tmp = array(imagick_getwidth($this->handle), imagick_getheight($this->handle));
		return $tmp;
	}

	////
	// !resize
	// x, y, width, height, new_width, new_height
	function resize($arr)
	{
		extract($arr);
		imagick_resize( $this->handle, $new_width, $new_height, IMAGICK_FILTER_UNKNOWN, 0);
	}

	function get($type)
	{
		switch($type)
		{
			case IMAGE_PNG:
				$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv").".png";
				imagick_writeimage($this->handle, $tn);
				break;

			case IMAGE_JPEG:
				$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv").".jpg";
				imagick_writeimage($this->handle, $tn);
				break;

			case IMAGE_GIF:
				$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv").".gif";
				imagick_writeimage($this->handle, $tn);
				break;

			case IMAGE_WBMP:
				$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv").".wbmp";
				imagick_writeimage($this->handle, $tn);
				break;

			default:
				if ($this->error_rep == false)
				{
					$this->is_error = true;
				}
				else
				{
					$this->raise_error(ERR_IMAGE_TYPE, sprintf(t("image_convert::get(%s): unknown image type!"), $type));
				}
		}
		$fc = $this->get_file(array("file" => $tn));
		unlink($tn);
		return $fc;
	}

	function save($filename, $type)
	{
		imagick_writeimage($this->handle, $filename);
	}

	////
	// !returns an instance of this class that has the same image loaded, but any operations on it, will not affect the original image
	function &copy()
	{
		$inst = new _int_image_convert_driver_imagick_module;
		$inst->load_from_string($this->get(IMAGE_PNG));
		return $inst;
	}

	function destroy()
	{
		imagick_free($this->handle);
		unset($this);
	}

	////
	// !merges the current image with the image given as $img
	// $source, $x, $y, $pct
	function merge($arr)
	{
		return E_NOIMPL;
		extract($arr);
		$tn = tempnam(aw_ini_get("server.tmpdir"), "aw_img_conv");

		$cmd = $this->composite." -geometry +".$x."+".$y." ".$source->driver->filename." ".$this->filename." ".$tn;
		shell_exec($cmd);
		unlink($this->filename);
		$this->filename = $tn;
	}
}
