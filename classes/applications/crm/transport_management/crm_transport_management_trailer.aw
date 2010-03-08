<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/transport_management/crm_transport_management_trailer.aw,v 1.2 2007/12/06 14:33:24 kristo Exp $
// crm_transport_management_trailer.aw - Haagis 
/*

@classinfo syslog_type=ST_CRM_TRANSPORT_MANAGEMENT_TRAILER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_transport_management_trailer index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property nr type=textbox table=crm_transport_management_trailer
	@caption Haagise nr.

	@property manufacturer_and_model type=textbox table=crm_transport_management_trailer
	@caption Tootja ja mudel

	@property bodytype type=select table=crm_transport_management_trailer
	@caption Keret&uuml;&uuml;p

	@property axle_count type=textbox table=crm_transport_management_trailer
	@caption Sildade arv

	@property color type=textbox table=crm_transport_management_trailer
	@caption V&auml;rvus

	@property year type=textbox table=crm_transport_management_trailer
	@caption V&auml;ljalaskeaasta

	@property price type=textbox table=crm_transport_management_trailer
	@caption Hind

@groupinfo technical_data caption="Tehnilised andmed"
@default group=technical_data

	@property superstructure type=select table=crm_transport_management_trailer
	@caption Pealisehitus 

	@property length type=textbox table=crm_transport_management_trailer
	@caption Pikkus

	@property width type=textbox table=crm_transport_management_trailer
	@caption Laius

	@property height type=textbox table=crm_transport_management_trailer
	@caption K&otilde;rgus

	@property merchant_space_capacity type=textbox table=crm_transport_management_trailer
	@caption Kaubaruumi maht

	@property merchant_space_length type=textbox table=crm_transport_management_trailer
	@caption Kaubaruumi pikkus

	@property merchant_space_width type=textbox table=crm_transport_management_trailer
	@caption Kaubaruumi laius

	@property merchant_space_height type=textbox table=crm_transport_management_trailer
	@caption Kaubaruumi k&otilde;rgus

	@property suspension type=textbox table=crm_transport_management_trailer
	@caption Vedrustus

	@property deadweight type=textbox table=crm_transport_management_trailer
	@caption Kandev&otilde;ime

	@property empty_weight type=textbox table=crm_transport_management_trailer
	@caption T&uuml;himass

	@property full_weight type=textbox table=crm_transport_management_trailer
	@caption T&auml;ismass

	
*/

define('BODYTYPE_PLATFORM', 1);
define('BODYTYPE_VAN', 2);
define('BODYTYPE_DUMPER', 3);
define('BODYTYPE_TARPAULIN', 4);
define('BODYTYPE_TANKER', 5);
define('BODYTYPE_CONTAINER_TRANSPORTER', 6);
define('BODYTYPE_OTHER', 7);

class crm_transport_management_trailer extends class_base
{

	var $bodytypes = array();

	function crm_transport_management_trailer()
	{
		$this->init(array(
			"tpldir" => "applications/crm/transport_management/crm_transport_management_trailer",
			"clid" => CL_CRM_TRANSPORT_MANAGEMENT_TRAILER
		));

		$this->bodytypes = array(
			BODYTYPE_PLATFORM => t('Madel'),
			BODYTYPE_VAN => t('Furgoon'),
			BODYTYPE_DUMPER => t('Kallur'),
			BODYTYPE_TARPAULIN => t('Tent'),
			BODYTYPE_TANKER => t('Tsistern'),
			BODYTYPE_CONTAINER_TRANSPORTER => t('Konteinerveok'),
			BODYTYPE_OTHER => t('Muu')
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'superstructure':
			case 'bodytype':
				$prop['options'] = $this->bodytypes;
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'bodytype':
			case 'superstructure':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
			case 'nr':
			case 'manufacturer_and_model':
			case 'axle_count':
			case 'color':
			case 'year':
			case 'price':
			case 'length':
			case 'width':
			case 'height':
			case 'merchant_space_capacity':
			case 'merchant_space_length':
			case 'merchant_space_width':
			case 'merchant_space_height':
			case 'suspension':
			case 'deadweight':
			case 'empty_weight':
			case 'full_weight':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
                }

		return false;
	}
}
?>
