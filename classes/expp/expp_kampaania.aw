<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_kampaania.aw,v 1.2 2007/11/23 07:18:28 dragut Exp $
// expp_kampaania.aw - Expp kampaania 
/*

@classinfo syslog_type=ST_EXPP_KAMPAANIA relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

@property kampaaniad_tabel type=table no_caption=1
@caption Kampaaniad

*/

class expp_kampaania extends class_base
{
	function expp_kampaania()
	{
		$this->init(array(
			"tpldir" => "expp/expp_kampaania",
			"clid" => CL_EXPP_KAMPAANIA
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

	function _get_kampaaniad_tabel($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'id',
			'caption' => t('ID'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'nimetus',
			'caption' => t('Kampaania nimetus'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'tellimine',
			'caption' => t('Tellimine'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'kampaania',
			'caption' => t('Kampaania'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'kirjeldus',
			'caption' => t('Kirjeldus'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'email_noutud',
			'caption' => t('e-mail kohustuslik'),
			'align' => 'center',
			'chgbgcolor' => 'line_color'
		));
		$t->define_chooser(array(
			'name' => 'delete',
			'field' => 'delete_field',
			'caption' => t('Kustuta'),
			'width' => '5%',
			'chgbgcolor' => 'line_color'
		));

		$this->db_query('select * from expp_kampaania');
		$counter = 0;
		while ($rida = $this->db_next())
		{
			$t->define_data(array(
				'id' => $rida['id'],
				'nimetus' => html::textbox(array(
					'name' => 'kampaania['.$rida['id'].'][nimetus]',
					'value' => $rida['nimetus'],
					'size' => '20'
				)),
				'tellimine' => t('Algus: ').html::date_select(array(
					'name' => 'kampaania['.$rida['id'].'][tellimine_algus]',
					'value' => $rida['tellimine_algus'],
					'format' => array('day_textbox', 'month_textbox', 'year_textbox')
				)),
				'kampaania' => t('Algus: ').html::date_select(array(
					'name' => 'kampaania['.$rida['id'].'][kampaania_algus]',
					'value' => $rida['kampaania_algus'],
					'format' => array('day_textbox', 'month_textbox', 'year_textbox')
				)),
				'kirjeldus' => html::textarea(array(
					'name' => 'kampaania['.$rida['id'].'][kirjeldus]',
					'value' => $rida['kirjeldus']
				)),
				'email_noutud' => html::checkbox(array(
					'name' => 'kampaania['.$rida['id'].'][email_noutud]',
					'value' => 1,
					'checked' => ($rida['email_noutud'] == 1) ? true : false,
				)),
				'delete_field' => $rida['id'],
				'line_color' => (($counter % 2) == 0) ? 'lightblue' : 'lightgreen'
			));

			$pilt_str = '';
			if (!empty($rida['pilt']))
			{
				$pilt_str = html::img(array(
					'url' => $rida['pilt'],
				)).'<br />';
			}
			
			$t->define_data(array(
				'id' => $rida['id'],
				'tellimine' => t('L&otilde;pp: ').html::date_select(array(
					'name' => 'kampaania['.$rida['id'].'][tellimine_lopp]',
					'value' => $rida['tellimine_lopp'],
					'format' => array('day_textbox', 'month_textbox', 'year_textbox')
				)),
				'kampaania' => t('L&otilde;pp: ').html::date_select(array(
					'name' => 'kampaania['.$rida['id'].'][kampaania_lopp]',
					'value' => $rida['kampaania_lopp'],
					'format' => array('day_textbox', 'month_textbox', 'year_textbox')
				)),
				'kirjeldus' => $pilt_str.html::fileupload(array(
					'name' => 'kampaania['.$rida['id'].'][pilt]',
				)),
				'line_color' => (($counter % 2) == 0) ? 'lightblue' : 'lightgreen'
			));
			$counter++;
		}

		$t->define_data(array(
			'id' => t('Uus'),
			'nimetus' => html::textbox(array(
				'name' => 'kampaania[uus][nimetus]',
				'size' => '20'
			)),
			'tellimine' => t('Algus: ').html::date_select(array(
				'name' => 'kampaania[uus][tellimine_algus]',
				'format' => array('day_textbox', 'month_textbox', 'year_textbox')
			)),
			'kampaania' => t('Algus: ').html::date_Select(array(
				'name' => 'kampaania[uus][kampaania_algus]',
				'format' => array('day_textbox', 'month_textbox', 'year_textbox')
			)),
			'email_noutud' => html::checkbox(array(
				'name' => 'kampaania[uus][email_noutud]',
				'value' => 1,
			)),
			'line_color' => 'orange'
		));
		$t->define_data(array(
			'id' => t('Uus'),
			'tellimine' => t('L&otilde;pp: ').html::date_select(array(
				'name' => 'kampaania[uus][tellimine_lopp]',
				'format' => array('day_textbox', 'month_textbox', 'year_textbox')
			)),
			'kampaania' => t('L&otilde;pp: ').html::date_select(array(
				'name' => 'kampaania[uus][kampaania_lopp]',
				'format' => array('day_textbox', 'month_textbox', 'year_textbox')
			)),
			'line_color' => 'orange'
		));
		
		return PROP_OK;
	}

	function _set_kampaaniad_tabel($arr)
	{
		$data = $arr['request']['kampaania'];

		// lets delete the fields which are marked to be deleted:
		if (!empty($arr['request']['delete']))
		{
			$delete_ids = implode(',', $arr['request']['delete']);
			$this->db_query('delete from expp_kampaania where id in ('.$delete_ids.')');
			foreach ($arr['request']['delete'] as $delete_id)
			{
				unset($data[$delete_id]);
			}
		}

		// if there is new row set (non empty nimetus field, then add new row:
		if (!empty($data['uus']['nimetus']))
		{
			$email_noutud = (empty($data['uus']['email_noutud'])) ? 0 : $data['uus']['email_noutud'];
			$sql = 'insert into 
					expp_kampaania 
				set 
					nimetus = \''.$data['uus']['nimetus'].'\',
					tellimine_algus = '.$this->get_timestamp($data['uus']['tellimine_algus']).',
					tellimine_lopp = '.$this->get_timestamp($data['uus']['tellimine_lopp']).',
					kampaania_algus = '.$this->get_timestamp($data['uus']['kampaania_algus']).',
					kampaania_lopp = '.$this->get_timestamp($data['uus']['kampaania_lopp']).',
					email_noutud = '.$email_noutud.',
					kirjeldus = \''.$data['uus']['kirjeldus'].'\'
			';
			$this->db_query($sql);
			
			// ok, teeme hetkel nii, et uuendame seda expp_hind tabelit kah, aga kirjutame sellesse expp_kampaania tabelisse ka esialgu siiski
		}

		unset($data['uus']);

		$upload_dir = aw_ini_get('expp_kampaania.upload_dir');
		foreach ($_FILES['kampaania']['error'] as $id => $error)
		{
			if ($error['pilt'] == UPLOAD_ERR_OK)
			{
				$filename = basename($_FILES['kampaania']['name'][$id]['pilt']);
				$uploaded_file = $upload_dir . $filename;
				move_uploaded_file($_FILES['kampaania']['tmp_name'][$id]['pilt'], $uploaded_file);
				$data[$id]['pilt'] = aw_ini_get('baseurl').'/img/kampaaniad/' . $filename;

			}
		}
		// and change the rest of the rows:
		// right now here is no checking if the data is actually changed or not
		foreach ($data as $id => $fields)
		{
			if (!isset($fields['email_noutud']))
			{
				$fields['email_noutud'] = 0;
			}
			if (empty($fields['pilt']))
			{
			
				$sql = 'update 
						expp_kampaania 
					set 
						nimetus = \''.$fields['nimetus'].'\',
						tellimine_algus = '.$this->get_timestamp($fields['tellimine_algus']).',
						tellimine_lopp = '.$this->get_timestamp($fields['tellimine_lopp']).',
						kampaania_algus = '.$this->get_timestamp($fields['kampaania_algus']).',
						kampaania_lopp = '.$this->get_timestamp($fields['kampaania_lopp']).',
						email_noutud = '.$fields['email_noutud'].',
						kirjeldus = \''.$fields['kirjeldus'].'\'
					where
						id = '.$id.'
				';
			}
			else
			{
				$sql = 'update 
						expp_kampaania 
					set 
						nimetus = \''.$fields['nimetus'].'\',
						tellimine_algus = '.$this->get_timestamp($fields['tellimine_algus']).',
						tellimine_lopp = '.$this->get_timestamp($fields['tellimine_lopp']).',
						kampaania_algus = '.$this->get_timestamp($fields['kampaania_algus']).',
						kampaania_lopp = '.$this->get_timestamp($fields['kampaania_lopp']).',
						email_noutud = '.$fields['email_noutud'].',
						kirjeldus = \''.$fields['kirjeldus'].'\',
						pilt = \''.$fields['pilt'].'\'
					where
						id = '.$id.'
				';
			}
			$this->db_query($sql);
			if (!empty($fields['nimetus']))
			{
				$ts = $this->get_timestamp($fields['kampaania_lopp']);
				$year = date('Y', $ts);
				$month = date('m', $ts);
				$day = date('d', $ts);
				$date_str = "$year-$month-$day 00:00:00";

				$sql = "
					update
						expp_hind
					set
						lopp = '".$date_str."'
					where
						kampaania = '".strtoupper($fields['nimetus'])."'
				";
				$this->db_query($sql);
			}
		}
	}

	function get_timestamp($arr)
	{
		return mktime(0, 0, 0, (int)$arr['month'], (int)$arr['day'], (int)$arr['year']);
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
