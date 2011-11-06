<?php

define("HTML2PDF_HTMLDOC", 1);
define("HTML2PDF_DOMPDF", 2);
define("HTML2PDF_HTML2PS", 3);
define("HTML2PDF_TCPDF", 4);
define("HTML2PDF_MPDF", 5);

class html2pdf extends class_base
{
	function html2pdf()
	{
		$this->init();
		$this->converter = ($_t = aw_ini_get("html2pdf.use_converter")) ? $_t : HTML2PDF_DOMPDF;
	}

	/**
		@attrib api=1 params=pos
		@comment
			checks if any html2pdf conversion is possible at the moment
		@returns bool
			true if all ok, false otherwise
	**/
	function can_convert()
	{
		$can = false;
		switch($this->converter)
		{
			case HTML2PDF_HTMLDOC:
				$hd = aw_ini_get("html2pdf.htmldoc_path");
				if (file_exists($hd) && is_executable($hd))
				{
					$can = true;
				}
				break;

			case HTML2PDF_DOMPDF:
				if(is_dir(aw_ini_get("html2pdf.dompdf_path")))
				{
					$can = true;
				}
				break;

			case HTML2PDF_TCPDF:
				if(is_dir(aw_ini_get("html2pdf.tcpdf_path")))
				{
					$can = true;
				}
				break;

			case HTML2PDF_MPDF:
				if(is_dir(aw_ini_get("html2pdf.mpdf_path")))
				{
					$can = true;
				}
				break;

			case HTML2PDF_HTML2PS:
				if(is_dir(aw_ini_get("html2pdf.html2ps_path")))
				{
					$can = true;
				}
				break;
		}
		return $can;
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
		switch($this->converter)
		{
			case HTML2PDF_HTMLDOC:
				return $this->_convert_using_htmldoc($arr);

			case HTML2PDF_HTML2PS:
				return $this->_convert_using_html2ps($arr);

			case HTML2PDF_DOMPDF:
				return $this->_convert_using_dompdf($arr);

			case HTML2PDF_TCPDF:
				return $this->_convert_using_tcpdf($arr);

			case HTML2PDF_MPDF:
				return $this->_convert_using_mpdf($arr);

			default:
				throw new awex_html2pdf("No converter selected!");
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
	public function gen_pdf($arr)
	{
		switch($this->converter)
		{
			case HTML2PDF_HTMLDOC:
				$this->_output_using_htmldoc($arr);
				exit;

			case HTML2PDF_HTML2PS:
				$this->_output_using_html2ps($arr);
				exit;

			case HTML2PDF_DOMPDF:
				$this->_output_using_dompdf($arr);
				exit;

			case HTML2PDF_TCPDF:
				$this->_output_using_tcpdf($arr);
				exit;

			case HTML2PDF_MPDF:
				$this->_output_using_mpdf($arr);
				exit;

			default:
				throw new awex_html2pdf("No converter selected!");
		}
	}

	private function _convert_using_html2ps($arr)
	{
	}

	private function _convert_using_dompdf($arr)
	{
		require_once(aw_ini_get("html2pdf.dompdf_path") . "dompdf_config.inc.php");
		$dompdf = new DOMPDF();
		$dompdf->set_paper("A4", (empty($arr["landscape"]) ? "portrait" : "landscape"));
		$dompdf->load_html($arr["source"]);
		return $dompdf->output();
	}


	private function _output_using_dompdf($arr)
	{
		$str = $this->_convert_using_dompdf($arr);
		$file_name = strpos($arr["filename"], ".pdf") === (strlen($arr["filename"]) - 4) ? $arr["filename"] : $arr["filename"].".pdf";$file_name = str_replace(" " , "_" , $file_name);
		header("Cache-Control: private, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: private");
		header("Content-type: application/pdf");
		header("Content-disposition: inline; filename={$file_name}");
		header("Content-Length: ".strlen($str));
		automatweb::$result->set_data($str);
		automatweb::http_exit();
	}

	private function _output_using_mpdf($arr)
	{
		$path = aw_ini_get("html2pdf.mpdf_path");
		require_once("{$path}mpdf.php");

		$file_name = str_replace(" " , "_" , (strpos($arr["filename"], ".pdf") === (strlen($arr["filename"]) - 4) ? $arr["filename"] : $arr["filename"].".pdf"));
		// mPDF ($mode, $format, $default_font_size, $default_font, $margin_left, $margin_right, $margin_top , $margin_bottom, $margin_header, $margin_footer, $orientation)
		$mpdf = new mPDF("", "A4", 0, "", 13, 13, 13, 20, 9, 8, (empty($arr["landscape"]) ? "P" : "L"));
		$this->_process_mpdf($mpdf, $arr);
		$mpdf->Output($file_name, "I");
		exit;
	}

	private function _convert_using_mpdf($arr)
	{
		$path = aw_ini_get("html2pdf.mpdf_path");
		require_once("{$path}mpdf.php");

		$mpdf = new mPDF("", "A4", 0, "", 13, 13, 13, 20, 9, 8, (empty($arr["landscape"]) ? "P" : "L"));
		$this->_process_mpdf($mpdf, $arr);
		return $mpdf->Output("", "S");
	}

	private function _process_mpdf($mpdf, $arr)
	{
		if (!empty($arr["header"])) $mpdf->SetHTMLHeader($arr["header"]);
		if (!empty($arr["footer"])) $mpdf->SetHTMLFooter($arr["footer"]);
		$mpdf->WriteHTML($arr["source"]);
	}

	private function _convert_using_tcpdf($arr)
	{
	}

	private function _convert_using_htmldoc($arr)
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

	private function _output_using_htmldoc($arr)
	{
		$str = $this->_convert_using_htmldoc($arr);
		$file_name = strpos($arr["filename"], ".pdf") === (strlen($arr["filename"]) - 4) ? $arr["filename"] : $arr["filename"].".pdf";$file_name = str_replace(" " , "_" , $file_name);
		header("Cache-Control: private, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: private");
		header("Content-type: application/pdf");
		header("Content-disposition: inline; filename={$file_name}");
		header("Content-Length: ".strlen($str));
		automatweb::$result->set_data($str);
		automatweb::http_exit();
	}
}

class awex_html2pdf extends aw_exception {}
