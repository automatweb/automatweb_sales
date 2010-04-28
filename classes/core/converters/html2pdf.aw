<?php

namespace automatweb;

define("HTML2PDF_HTMLDOC", 1);
define("HTML2PDF_DOMPDF", 2);

class html2pdf extends class_base
{
	function html2pdf()
	{
		$this->init();
		$this->converter = ($_t = aw_ini_get("html2pdf.use_converter"))?$_t:HTML2PDF_DOMPDF;
	}

	/**
		@attrib api=1
		@comment
			check's if any html2pdf conversion is possible at the moment
		@returns
			true if all ok, false otherwise
	**/
	function can_convert()
	{
		switch($this->converter)
		{
			case HTML2PDF_HTMLDOC:
				if (is_file(aw_ini_get("html2pdf.htmldoc_path")))
				{
					return true;
				}
				break;
			case HTML2PDF_DOMPDF:
				if(is_dir(aw_ini_get("html2pdf.dompdf_path")) && is_array(aw_ini_get("classes.".CL_DOMPDF)))
				{
					return true;
				}
				break;
			default:
				return false;
				break;
		}
		return false;
	}

	////
	// !converts html to pdf, returns pdf content
	// parameters:
	//	source - html source to convert
	/**
		@attrib api=1 params=name
		@param source required type=string
			html soucre to be converted
		@param landscape optional type=bool
			if set to true.. landscape pdf is created
		@param no_numbers optional type=bool
			if set to true, no page numbers are set to pdf
		@comment
			converts html contents to pdf
		@returns
			converted pdf
		@errors
			throws awex_html2pdf if there aren't any available converters found.
	**/
	function convert($arr)
	{
		// right, figure out which converter we got
		// first, try htmldoc
		switch($this->converter)
		{
			case HTML2PDF_HTMLDOC:
				$hd = aw_ini_get("html2pdf.htmldoc_path");
				if (file_exists($hd) && is_executable($hd))
				{
					return $this->_convert_using_htmldoc($arr);
				}
				else
				{
					throw new awex_html2pdf("Selected converter not found!");
				}
				break;
			case HTML2PDF_DOMPDF:
				$dompdf = get_instance(CL_DOMPDF);
				return $dompdf->convert($arr);
				break;
			default:
				throw new awex_html2pdf("No converter selected!");
				break;
		}
	}

	/**
		@attrib api=1 params=name
		@param source required type=string
			html soucre to be converted
		@param landscape optional type=bool
			if set to true.. landscape pdf is created
		@param no_numbers optional type=bool
			if set to true, no page numbers are set to pdf
		@comment
			generates pdf and outputs it to browser with correct headers.
		@errors
			raises ERR_CONVERT error if there aren't any available converters found.

	**/
	function gen_pdf($arr)
	{
		$str = $this->convert($arr);
		$file_name = strpos($arr["filename"], ".pdf") === (strlen($arr["filename"]) - 4) ? $arr["filename"] : $arr["filename"].".pdf";$file_name = str_replace(" " , "_" , $file_name);
		header("Cache-Control: private, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: private");
		header("Content-type: application/pdf");
		header("Content-disposition: attachment; filename={$file_name}");
		header("Content-Length: ".strlen($str));
		echo $str;
		exit;
	}

	function _convert_using_htmldoc($arr)
	{
		$tmpf = aw_ini_get("server.tmpdir")."/aw-html2pdf-".gen_uniq_id();
		$fp = fopen($tmpf, "w");
		fwrite($fp, $arr["source"]);
		fclose($fp);

		$lds = "";
		if ($arr["landscape"] == 1)
		{
			$lds = "--landscape";
		}

		$nns = "";
		if ($arr["no_numbers"] == 1)
		{
			$nns = "--no-numbered";
		}

		$hd = aw_ini_get("html2pdf.htmldoc_path");
		$cmdl = $hd." -t pdf --quiet --book --jpeg --webpage $lds $nns '$tmpf'";
		$pdf = shell_exec($cmdl);
		unlink($tmpf);
		return $pdf;
	}
}

class awex_html2pdf extends aw_exception {}

?>
