<?php
// $Header: /home/cvs/automatweb_dev/classes/pank/tax.aw,v 1.5 2008/01/31 13:55:00 kristo Exp $
// tax.aw - Maks 
/*

@classinfo syslog_type=ST_TAX relationmgr=yes maintainer=kristo
@tableinfo tax index=oid master_table=objects master_index=oid 

@default table=objects
@default group=general

@property jrk type=textbox
@caption Järjekord

@default table=tax

@property tax_type type=chooser datatype=int
@caption Maksu tüüp

@property tax_percentage type=textbox
@caption Suhtarv

@property tax_amount type=textbox
@caption Tüüparv

*/

class tax extends class_base
{
	var $ignore_percentage = false;
	var $ignore_sum = false;
	
	function tax()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "pank/tax/tax",
			"clid" => CL_TAX
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'tax_type':
				if($prop['value'])
				{
					$this->ignore_percentage = true;
					$this->ignore_sum = false;
				}
				else
				{
					$this->ignore_sum = true;
					$this->ignore_percentage = false;
				}
				$prop['options'] = array(
								'0' => 'Protsent',
								'1' => 'Kindel arv',
				);
			break;
			case 'tax_percentage':
				if($this->ignore_percentage)
				{
					return PROP_IGNORE;
				}
			break;
			case 'tax_amount':
				if($this->ignore_sum)
				{
					return PROP_IGNORE;
				}
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
}
?>
