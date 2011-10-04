<?php

class aw_mime_types
{
	private static $type2ext = array(
		"application/andrew-inset" => "ez",
		"application/mac-binhex40" => "hqx",
		"application/mac-compactpro" => "cpt",
		"application/msword" => "doc",
		"application/octet-stream" => "bin,dms,lha,lzh,exe,class,so,dll",
		"application/oda" => "oda",
		"application/pdf" => "pdf",
		"application/postscript" => "ai,eps,ps",
		"application/smil" => "smi,smil",
		"application/vnd.mif" => "mif",
		"application/vnd.ms-excel" => "xls",
		"application/vnd.ms-powerpoint" => "ppt",
		"application/vnd.wap.wbxml" => "wbxml",
		"application/vnd.wap.wmlc" => "wmlc",
		"application/vnd.wap.wmlscriptc" => "wmlsc",
		"application/x-bcpio" => "bcpio",
		"application/x-cdlink" => "vcd",
		"application/x-chess-pgn" => "pgn",
		"application/x-cpio" => "cpio",
		"application/x-csh" => "csh",
		"application/x-director" => "dcr,dir,dxr",
		"application/x-dvi" => "dvi",
		"application/x-futuresplash" => "spl",
		"application/x-gtar" => "gtar",
		"application/x-hdf" => "hdf",
		"application/x-javascript" => "js",
		"application/x-koan" => "skp,skd,skt,skm",
		"application/x-latex" => "latex",
		"application/x-netcdf" => "nc,cdf",
		"application/x-sh" => "sh",
		"application/x-shar" => "shar",
		"application/x-shockwave-flash" => "swf",
		"application/x-stuffit" => "sit",
		"application/x-sv4cpio" => "sv4cpio",
		"application/x-sv4crc" => "sv4crc",
		"application/x-tar" => "tar",
		"application/x-tcl" => "tcl",
		"application/x-tex" => "tex",
		"application/x-texinfo" => "texinfo,texi",
		"application/x-troff" => "t,tr,roff",
		"application/x-troff-man" => "man",
		"application/x-troff-me" => "me",
		"application/x-troff-ms" => "ms",
		"application/x-ustar" => "ustar",
		"application/x-wais-source" => "src",
		"application/xhtml+xml" => "xhtml,xht",
		"application/zip" => "zip",
		"audio/basic" => "au,snd",
		"audio/midi" => "mid,midi,kar",
		"audio/mpeg" => "mpga,mp2,mp3",
		"audio/x-aiff" => "aif,aiff,aifc",
		"audio/x-mpegurl" => "m3u",
		"audio/x-pn-realaudio" => "ram,rm",
		"audio/x-pn-realaudio-plugin" => "rpm",
		"audio/x-realaudio" => "ra",
		"audio/x-wav" => "wav",
		"chemical/x-pdb" => "pdb",
		"chemical/x-xyz" => "xyz",
		"image/bmp" => "bmp",
		"image/gif" => "gif",
		"image/ief" => "ief",
		"image/jpeg" => "jpeg,jpg,jpe",
		"image/png" => "png",
		"image/tiff" => "tiff,tif",
		"image/svg+xml" => "svg",
		"image/vnd.djvu" => "djvu,djv",
		"image/vnd.wap.wbmp" => "wbmp",
		"image/x-cmu-raster" => "ras",
		"image/x-portable-anymap" => "pnm",
		"image/x-portable-bitmap" => "pbm",
		"image/x-portable-graymap" => "pgm",
		"image/x-portable-pixmap" => "ppm",
		"image/x-rgb" => "rgb",
		"image/x-xbitmap" => "xbm",
		"image/x-xpixmap" => "xpm",
		"image/x-xwindowdump" => "xwd",
		"model/iges" => "igs,iges",
		"model/mesh" => "msh,mesh,silo",
		"model/vrml" => "wrl,vrml",
		"text/css" => "css",
		"text/html" => "html,htm",
		"text/plain" => "asc,txt",
		"text/richtext" => "rtx",
		"text/rtf" => "rtf",
		"text/sgml" => "sgml,sgm",
		"text/tab-separated-values" => "tsv",
		"text/vnd.wap.wml" => "wml",
		"text/vnd.wap.wmlscript" => "wmls",
		"text/x-setext" => "etx",
		"text/xml" => "xml,xsl",
		"text/ddoc" => "ddoc",
		"video/mpeg" => "mpeg,mpg,mpe",
		"video/quicktime" => "qt,mov",
		"video/vnd.mpegurl" => "mxu",
		"video/x-msvideo" => "avi",
		"video/x-sgi-movie" => "movie",
		"video/flv" => "flv",
		"x-conference/x-cooltalk" => "ice",
		"application/vnd.sun.xml.writer" => "sxw",
		"application/vnd.sun.xml.calc" => "sxc",
	);

	 private static $ext2type = array(
		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'php' => 'text/html',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'xml' => 'application/xml',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',

		// images
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',

		// archives
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'exe' => 'application/x-msdownload',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',

		// audio/video
		'mp3' => 'audio/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',

		// adobe
		'pdf' => 'application/pdf',
		'psd' => 'image/vnd.adobe.photoshop',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',

		// ms office
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',

		// open office
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	);

	/** Returns the MIME type of an extension

		@attrib name=type_for_ext params=pos api=1

		@param ext required type=string
			File extension

		@returns
			MIME type of the extension
			false, if the MIME type for the extension is unknown

		@examples
			$mimeregistry = get_instance("core/aw_mime_types");
			echo $mimeregistry->type_for_ext('rtf'); // prints 'text/rtf'

	**/
	public static function type_for_ext($ext)
	{
		$ext = strtolower($ext);
		foreach(self::$type2ext as $type => $_ext)
		{
			if (in_array($ext, explode(",", $_ext)))
			{
				return $type;
			}
		}
		return false;
	}

	/** Returns the MIME type of a file

		@attrib name=type_for_file params=pos api=1

		@param file required type=string
			File name

		@returns
			MIME type of the file
			false, if the MIME type for the file is unknown

		@comment
			For MIME type detection, file extension is used

		@examples
			echo aw_mime_types::type_for_file('my_cv.rtf'); // prints 'text/rtf'

	**/
	public static function type_for_file($file)
	{
		$pathinfo = pathinfo($file);
		return self::type_for_ext($pathinfo["extension"]);
	}

	/** Returns the extension for a MIME type

		@attrib name=ext_for_type params=pos api=1

		@param type required type=string
			MIME type

		@returns
			Extension for the MIME type

		@examples
			echo aw_mime_types::ext_for_type('text/rtf'); // prints 'rtf'

	**/
	public static function ext_for_type($type)
	{
		list($ext) = explode(",", self::$type2ext[$type]);
		return $ext;
	}
}
