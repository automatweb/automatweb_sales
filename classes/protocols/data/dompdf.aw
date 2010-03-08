<?php
/*
@classinfo syslog_type=ST_DOMPDF relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo
@tableinfo aw_dompdf master_index=brother_of master_table=objects index=aw_oid

@default table=objects
@default group=general

*/

class dompdf extends class_base
{
	function dompdf()
	{
		$this->init(array(
			"tpldir" => "protocols/data/dompdf",
			"clid" => CL_DOMPDF
		));
		$this->tmpfile = aw_ini_get("server.tmpdir")."/aw-html2pdf-".gen_uniq_id();
		$this->tmptofile = aw_ini_get("server.tmpdir")."/aw_html2pdf-".gen_uniq_id(); 
	}

	/**
		@attrib api=1 params=name
		@param source required type=string
			html soucre to be converted
		@param landscape optional type=bool
			if set to true.. landscape pdf is created
		@comment
			converts html contents to pdf
		@returns
			converted pdf
	**/
	public function convert($arr)
	{
		$this->set_html_source($arr["source"]);
		$this->set_paper("a4", $arr["landscape"]);
		return $this->output_pdf();
	}

	/** Sets html source to be converted.
		@attrib api=1 params=pos
		@param source required type=string
			HTML source to be converted
	 **/
	public function set_html_source($src = "")
	{
		$this->source = $src;
	}

	/** Sets paper size and orientation
		@attrib api=1 params=pos
		@param paper_size  optional type=string default=a4
			Paper size
		@param landscape optional type=bool default=false
			If set to true, landscape paper is drawn
	 **/
	public function set_paper($paper_size = "a4", $landscape = false)
	{
		$this->paper_size = $paper_size;
		$this->orientation = $landscape?"landscape":"portrait";
	}

	/** Saves the rendered pdf to given file.
		@attrib api=1 params=pos
		@param filename required type=string
			Filename to save the pdf.
	 **/
	public function save_pdf()
	{
		$fp = fopen($this->tmpfile, "w");
		fwrite($fp, $this->source);
		fclose($fp);

		$lds = "";
		$lds .= " -p ".($this->paper_size?$this->paper_size:"a4");
		//$lds .= " -b ".aw_ini_get("site_basedir")."/public";
		$lds .= " -f ".$this->tmptofile;
		$lds .= " ".$this->tmpfile;
		$lds .= " -o ".$this->orientation;
		$hd = aw_ini_get("html2pdf.dompdf_path");
		$cmdl = "php ".$hd."/dompdf.php ".$lds;
		$cmdl2 = "php ".$hd."/dompdf.php ".$lds2;//arr($cmdl);arr($this->source); die();
		shell_exec($cmdl);
		unlink($this->tmpfile);
	}

	/** Returns the rendered pdf contents
		@attrib api=1
	 **/
	public function output_pdf()
	{
		$this->save_pdf();
		$pdf = file_get_contents($this->tmptofile);
		unlink($this->tmptofile);
		return $pdf;
	}
}

?>
