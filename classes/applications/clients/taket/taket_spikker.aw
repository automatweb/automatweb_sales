<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/taket/taket_spikker.aw,v 1.1 2008/10/01 14:17:40 markop Exp $
// taket_spikker.aw - Taket Spikker 
/*

@classinfo syslog_type= relationmgr=yes

@default table=objects
@default group=general

*/

class taket_spikker extends class_base
{
	const AW_CLID = 236;

	var $spikker_data;
	function taket_spikker()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_spikker",
			"clid" => CL_TAKET_SPIKKER
		));
		$this->initialize_spikker_data();
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
	}	
	*/

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$this->read_template("taket_spikker.tpl");
		$tootekoodid='';
		$otsitunnused='';
		foreach($this->spikker_data as $key=>$value)
		{
			$this->vars(array(
				'tootekood' => $value,
				'otsitunnus' => $key
			));
			$tootekoodid.=$this->parse('TOOTEKOODSUB');
			$otsitunnused.=$this->parse('OTSITUNNUS');
		}
		$this->vars(array(
			'tootekoodid' => $tootekoodid,
			'otsitunnused' => $otsitunnused
		));
		return $this->parse();
	}

	function initialize_spikker_data()
	{
		$this->spikker_data['Chrysler OE.'] = 'CH-5072216AA';
		$this->spikker_data['Daewoo OE.'] = 'DA-96565412';
		$this->spikker_data['Daihatsu OE.'] = 'DA-13504-87701';
		$this->spikker_data['Honda OE.'] = 'HO-15400-PH1-F02';
		$this->spikker_data['Hyundai OE.'] = 'HY-28113-32510';
		$this->spikker_data['Isuzu OE.'] = 'IS-8-94217100-0';
		$this->spikker_data['Kia OE.'] = 'KI-0K954-12-205';
		$this->spikker_data['Mazda OE.'] = 'MA-B603-23-603';
		$this->spikker_data['Mitsubishi OE.'] = 'MI-MD322508';
		$this->spikker_data['Nissan OE.'] = 'NI-15208-20N00';
		$this->spikker_data['Subaru OE.'] = 'SU-16546-AA070';
		$this->spikker_data['Suzuki OE.'] = 'SZ-13780-86000';
		$this->spikker_data['Toyota OE.'] = 'TO-17801-10030';

		$this->spikker_data['Arwidson OY'] = '31-2178';
		$this->spikker_data['Atoy OY'] = '1103-C300593';
		$this->spikker_data['Koivunen OY'] = '481-20006E';

		$this->spikker_data['AE Rihm'] = '48-TB213';
		$this->spikker_data['AISIN Sidur'] = '92-DM016';
		$this->spikker_data['AISIN Veepump'] = '91-WM016';
		$this->spikker_data['AJUSA'] = 'AJ-10042400';
		$this->spikker_data['ATM'] = '86-RT201N';
		$this->spikker_data['CDX'] = 'CDX-674';
		$this->spikker_data['CLYCO Kepsusaaled'] = '76-4-1825-000CP';
		$this->spikker_data['CLYCO Raamsaaled'] = '77-6593M-';
		$this->spikker_data['DAIKIN DA-'] = 'DA-';
		$this->spikker_data['DENSO'] = 'J-W22EPRU';
		$this->spikker_data['FEDERAL MOGUL'] = '72-';
		$this->spikker_data['FRAM'] = 'FR-CA3660';
		$this->spikker_data['FUROLATOR'] = '87-FK27800M';
		$this->spikker_data['GATES'] = 'GA-G5033';
		$this->spikker_data['GLO'] = 'GLO-3145';
		$this->spikker_data['JAPAN CARS'] = 'JC-J64004';
		$this->spikker_data['JAPANPARTS'] = 'J-FO502';
		$this->spikker_data['KOPARTS'] = 'K-10Y505';
		$this->spikker_data['KYB'] = '067-341054';
		$this->spikker_data['MOOG Rooliosad'] = '58-VO-BJ-0319';
		$this->spikker_data['NARVA'] = 'NA-48881';
		$this->spikker_data['NGK'] = 'NGK-BPR5ES';
		$this->spikker_data['NUOVA MEYSTER'] = '99-7358BB';
		$this->spikker_data['PRECISION Int.'] = '87-K2700D';
		$this->spikker_data['PUROLATOR'] = '87-K5100A';
		$this->spikker_data['SKF Laagrikompl.'] = '088-VKBA1350';
		$this->spikker_data['SONNAK Akud'] = 'SON-050442';
		$this->spikker_data['ZAP Akud'] = 'ZAP-545115';
		$this->spikker_data['ZF'] = '0710-0734310316';
		$this->spikker_data['TRANSTAR'] = '87-56103A-B';
		$this->spikker_data['TRW'] = '71-';
		$this->spikker_data['TRW Rooliosad'] = '58-ES2194R';
		$this->spikker_data['VOLTA Akud'] = 'VOL-55503';
		$this->spikker_data['MOTIP Keemia'] = 'MO-55000';
		$this->spikker_data['MOTIP Pintslipudel'] = 'PP-50055';
	}
}
?>
