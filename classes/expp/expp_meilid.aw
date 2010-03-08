<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_meilid.aw,v 1.3 2007/11/23 07:18:28 dragut Exp $
// expp_meilid.aw - expp meilid 
/*

@classinfo syslog_type=ST_EXPP_MEILID relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

@property valjaanded_mailid_tabel type=table 
@caption V&auml;ljaanded - meilid tabel

*/

class expp_meilid extends class_base
{
	function expp_meilid()
	{
		$this->init(array(
			"tpldir" => "expp/expp_meilid",
			"clid" => CL_EXPP_MEILID
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
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

	function _get_valjaanded_mailid_tabel($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'valjaanne',
			'caption' => t('V&auml;ljaanne'),
			'width' => '30%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'mail',
			'caption' => t('E-mail'),
			'width' => '70%',
			'align' => 'center'
		));
		$t->define_chooser(array(
			'name' => 'del_ids',
			'caption' => t('Kustuta'),
			'field' => 'delete'
		));
		
		$data = $arr['obj_inst']->meta(valjaanded_mailid);

		foreach ($data as $key => $value)
		{
			$t->define_data(array(
				'valjaanne' => html::textbox(array(
					'name' => 'rida['.$key.'][valjaanne]',
					'value' => $value['valjaanne'],
					'size' => '20'
				)),
				'mail' => html::textbox(array(
					'name' => 'rida['.$key.'][mail]',
					'value' => $value['mail'],
					'size' => '60'
				)),
				'delete' => $key + 1
			));
		}
		// uus:
		$t->define_data(array(
			'valjaanne' => html::textbox(array(
				'name' => 'uus_rida[valjaanne]',
				'size' => '20'
			)),
			'mail' => html::textbox(array(
				'name' => 'uus_rida[mail]',
				'size' => '60'
			)),
		));
		
		return PROP_OK;
	}

	function _set_valjaanded_mailid_tabel($arr)
	{

		$data = $arr['obj_inst']->meta('valjaanded_mailid');

		// muutmisele:
		foreach ($arr['request']['rida'] as $key => $value)
		{
			$data[$key] = $value;
		}
		
		// kustutamisele:
		if (!empty($arr['request']['del_ids']))
		{
			foreach ($arr['request']['del_ids'] as $del_id)
			{
				unset($data[$del_id - 1]);
			}
		}

		
		// uus rida:
		if (!empty($arr['request']['uus_rida']['valjaanne']))
		{
			$data[] = $arr['request']['uus_rida'];
		}

		$arr['obj_inst']->set_meta('valjaanded_mailid', $data);
		return PROP_OK;
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

//-- methods --//
}
?>
