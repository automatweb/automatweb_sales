<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/xml_editor/maja_xml_editor.aw,v 1.20 2009/02/04 08:24:45 dragut Exp $
// maja_xml_editor.aw - maja xml-i editor 
/*

@classinfo syslog_type=ST_MAJA_XML_EDITOR relationmgr=yes no_status=1 no_comment=1 maintainer=dragut

@default table=objects
@default group=general

	@property orig_xml_file type=textbox field=meta method=serialize size=50
	@caption Originaal xml fail

	@property new_xml_file type=textbox field=meta method=serialize size=50
	@caption Uus xml fail

	@property db_table_contents type=relpicker reltype=RELTYPE_DB_TABLE_CONTENTS field=meta method=serialize
	@caption Andmebaasitabeli sisu objekt

@groupinfo db_saving_settings caption="Baasi salvestamise seaded"
@default group=db_saving_settings

	@property house_name type=textbox field=meta method=serialize
	@caption Maja nimi/aadress

	@property xml_to_db_connections type=table field=meta method=serialize no_caption=1
	@caption XML ja DB v&auml;ljade vahelised seosed

@groupinfo additional_info caption="Lisainfo korterite tabeli jaoks"
@default group=additional_info

	@property additional_info_table type=table no_caption=1
	@caption Additional info

@groupinfo content_change caption="XML-i muutmine"

	@groupinfo group_01 caption="Tab 01" parent=content_change

		@property content_table_01 type=table store=no no_caption=1 group=group_01
		@caption Sisu tabel 01

		@property xml_field_01 type=select group=group_01 field=meta method=serialize
		@caption Parent XMl-ist

		@property db_table_01 type=relpicker group=group_01 field=meta method=serialize reltype=RELTYPE_DB_TABLE_CONTENTS
		@caption Andmebaasi tabel

	@groupinfo group_02 caption="Tab 02" parent=content_change

		@property content_table_02 type=table store=no no_caption=1 group=group_02
		@caption Sisu tabel 02

		@property xml_field_02 type=select group=group_02 field=meta method=serialize
		@caption Parent XML-ist

		@property db_table_02 type=relpicker group=group_02 field=meta method=serialize reltype=RELTYPE_DB_TABLE_CONTENTS
		@caption Andmebaasi tabel

	@groupinfo group_03 caption="Tab 03" parent=content_change

		@property content_table_03 type=table store=no no_caption=1 group=group_03
		@caption Sisu tabel 03

		@property xml_field_03 type=select group=group_03 field=meta method=serialize
		@caption Parent XML-ist

		@property db_table_03 type=relpicker group=group_03 field=meta method=serialize reltype=RELTYPE_DB_TABLE_CONTENTS
		@caption Andmebaasi tabel

	@groupinfo group_04 caption="Tab 04" parent=content_change

		@property content_table_04 type=table store=no no_caption=1 group=group_04
		@caption Sisu tabel 04

		@property xml_field_04 type=select group=group_04 field=meta method=serialize
		@caption parent XML-ist

		@property db_table_04 type=relpicker group=group_04 field=meta method=serialize reltype=RELTYPE_DB_TABLE_CONTENTS
		@caption Andmebaasi tabel

	@groupinfo group_05 caption="Tab 05" parent=content_change

		@property content_table_05 type=table store=no no_caption=1 group=group_05
		@caption Sisu tabel 05

		@property xml_field_05 type=select group=group_05 field=meta method=serialize
		@caption parent XML-ist

		@property db_table_05 type=relpicker group=group_05 field=meta method=serialize reltype=RELTYPE_DB_TABLE_CONTENTS
		@caption Andmebaasi tabel


@reltype DB_TABLE_CONTENTS value=1 clid=CL_DB_TABLE_CONTENTS
@caption Andmebaasitabeli sisu objekt

*/

class maja_xml_editor extends class_base
{
	function maja_xml_editor()
	{
		$this->init(array(
			"tpldir" => "applications/xml_editor/maja_xml_editor",
			"clid" => CL_MAJA_XML_EDITOR
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		$xml_content = $this->get_xml(array("obj_inst" => $arr['obj_inst']));
		if (!$xml_content)
		{
			$xml_content[0] = array();
		}
		$xml_open_tags = array();
		foreach ($xml_content[0] as $value)
		{
			if ($value['type'] == "open")
			{
//				array_push($xml_open_tags, $value['tag']);
				$xml_open_tags[$value['tag']] = $value['tag'];
			}
		}
		switch($prop["name"])
		{

			case "xml_field_01":
			case "xml_field_02":
			case "xml_field_03":
			case "xml_field_04":
			case "xml_field_05":
				$prop['options'] = $xml_open_tags;
				break;
			case "content_table_01":
			case "content_table_02":
			case "content_table_03":
			case "content_table_04":
			case "content_table_05":
				$this->create_content_table($arr);
				break;
//			case "xml_content":
//				$this->create_content_table($arr);
//				break;
	
			case "xml_to_db_connections":
				$this->create_xml_to_db_connections_table($arr);
				break;
			case "additional_info_table":
				$this->create_additional_info_table($arr);
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
			case "xml_to_db_connections":
			//	arr($arr['request']);
				$arr['obj_inst']->set_meta("xml_to_db_conns", $arr['request']['xml_to_db_values']);
				break;
			case "house_name":
			//Here I'll check if house_name has changed, cause when it is
			//I have to change it in database too

				$old_house_name = $arr['obj_inst']->meta("house_name");
				$new_house_name = $prop['value'];

				if($old_house_name != $new_house_name)
				{
					$db_table_contents_obj = obj($arr['obj_inst']->prop("db_table_contents"));
					$db_table_name = $db_table_contents_obj->prop("db_table");

					$this->db_query("UPDATE ".$db_table_name." SET maja_nimi='".$new_house_name."' WHERE maja_nimi='".$old_house_name."'");
					
				}
				break;
			case "xml_content":
				$arr['obj_inst']->set_meta("floors", $arr['request']['floors_flats']);
				break;
		}
		return $retval;
	}	


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
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

////////////////////////////////////////////////////////////////////////////////
//
// creating XML editor table
//
////////////////////////////////////////////////////////////////////////////////

	function create_content_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		
		$t->set_sortable(false);

		$xml_file_content = $this->get_xml(array(
			"obj_inst" => $arr['obj_inst'],
		));
		$first = true;
		
		$table_num = end(explode("_", $arr['prop']['name']));
		$parent_tag = $arr['obj_inst']->prop("xml_field_".$table_num);
		$complete_tags = $this->get_complete_xml_tags(array(
			"obj_inst" => $arr['obj_inst'],
			"parent" => $parent_tag,
		));
//		foreach($xml_file_content[0] as $key => $value)
		if (!is_array($complete_tags))
		{
			return false;
		}
		
		foreach($complete_tags as $key => $value)
		{
			if($value['type'] == "open")
			{
/*

		// antud juhul ei ole seda siin vaja, kuna "open" tyypi tagidel selles
		// xml failis ei ole atribuute (maja element on ainus open tyypi tag)

				$attributes = "";
				if(isset($value['attributes']))
				{
					foreach($value['attributes'] as $attrib_key => $attrib_value)
					{
						$attribs .= " ".$attrib_key."=\"".$attrib_value."\" ";
					}
				}
*/

			}

			if($value['type'] == "complete")
			{
				$attributes = array();
				if(isset($value['attributes']))
				{
					foreach($value['attributes'] as $attribute_key => $attribute_value)
					{
						$textfield_size = 40;
						if(($attribute_key == "korterinr") || ($attribute_key == "number") || ($attribute_key == "tubadearv") || ($attribute_key == "pindala"))
						{
							$textfield_size = 5;
						}
						if($attribute_key == "plaan" || $attribute_key == "asukoht")
						{
							$textfield_size = 35;
						}

						$attribute_textfield = html::textbox(array(
							"name" => $key."@".$attribute_key,
							"value" => $attribute_value,
							"size" => $textfield_size,
						));
						if ($first)
						{
							$t->define_field(array(
								"name" => $attribute_key,
								"caption" => $attribute_key,
							));
						}
						$attributes[$attribute_key] = $attribute_textfield;
					}
				}

				$first = false;
				$t->define_data($attributes);
			}
			
			if($value['type'] == "close")
			{
	
			}
		}
		
	}
////////////////////////////////////////////////////////////////////////////////
//
// returns all xml tags, which type is complete, and parent is specified
// obj_inst - instance of current object
// parsed_xml_file - parsed xml file
// parent - the tagname which should be the parent of the returned tags
//
////////////////////////////////////////////////////////////////////////////////
	function get_complete_xml_tags($arr)
	{
		if (isset($arr['parsed_xml_file']))
		{
			$xml_file = $arr['parsed_xml_file'];
		}
		else
		{
			$xml_file = $this->get_xml(array("obj_inst" => $arr['obj_inst']));
		}
		foreach($xml_file[0] as $key => $value)
		{
			if ($value['type'] == "open" && $value['tag'] == $arr['parent'])
			{
				$tags = array();
			}
			if ($value['type'] == "complete")
			{
				$tags[$key] = $value;
			}
			if ($value['type'] == "close" && $value['tag'] == $arr['parent'])
			{
				return $tags;
			}
		}
		// in case there is no 'close' tag (ie. there is only one tag in xml and its type is 'complete')
		// then return the complete tags 
		return $tags;
	}

////////////////////////////////////////////////////////////////////////////////
//
// creating XML to DB connections table
//
////////////////////////////////////////////////////////////////////////////////
	function create_xml_to_db_connections_table($arr)
	{
	
		$t = &$arr['prop']['vcl_inst'];


//		$o = obj($arr['id']);

		$db_table_contents_obj = obj($arr['obj_inst']->prop("db_table_contents"));
		$db_table_contents_inst = get_instance(CL_DB_TABLE_CONTENTS);
		$table_fields = $db_table_contents_inst->get_fields($db_table_contents_obj);

		
// i'll unset some unneeded array members, which are 2 table fields, which content
// has to come somewhere else
		unset($table_fields['id'], $table_fields['maja_nimi']);

		$t->define_field(array(
			"name" => "first_column",
			"caption" => t("---"),
		));
		$t->define_field(array(
			"name" => "empty",
			"caption" => t("Ei Salvestata"),
		));
		foreach($table_fields as $key => $value)
		{

			$t->define_field(array(
				"name" => $key,
				"caption" => $value,
				"align" => "center",
			));
		}

		$data_file_content = file_get_contents($arr['obj_inst']->prop("orig_xml_file"));

		$xml_file_content = parse_xml_def(array("xml" => $data_file_content));
		
// here I'll take the first <korter> elements attributes and assume that all the
// other <korter> elements have the same attributes
		foreach ($xml_file_content[0] as $v)
		{
			if ($v['tag'] == "korter" && $v['type'] == "complete") 
			{
				$korter_el_attribs = array_keys($v['attributes']);
				break;
			}
		}
		
		$xml_to_db_values = $arr['obj_inst']->meta("xml_to_db_conns");
		

		

		foreach($korter_el_attribs as $value)
		{
			$row = array("first_column" => $value);
			$row['empty'] = html::radiobutton(array(
				"name" => "xml_to_db_values[$value]",
				"value" => "empty",
				"checked" => checked($xml_to_db_values[$value] == "empty"),
			));
			foreach($table_fields as $tf_key => $tf_value)
			{
				$row[$tf_key] = html::radiobutton(array(
					"name" => "xml_to_db_values[$value]",
					"value" => $tf_key,
					"checked" => checked($xml_to_db_values[$value] == $tf_key),
				));
			}
			$t->define_data($row);
		}
//		arr($xml_file_content[0][1]);
		
		$t->set_sortable(false);
	}

////////////////////////////////////////////////////////////////////////////////
//
// creating additional info table
//
////////////////////////////////////////////////////////////////////////////////
	function create_additional_info_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$fields = array(
			"flat_nr" => t("Korteri nr."),
			"flat_nr2" => t("Alternatiivne korteri nr."),
			"korrus" => t("Korruse nr."),
			"korrus_nimi" => t("Korruse nimi"),
			"staatus2" => t("Alternatiivne staatus"),
			"panipaik" => t("Panipaik"),
			"terrass" => t("Terrass"),
			"r6du" => t("R&otilde;du"),
			"korteriomand" => t("Korteriomand"),
			"parklakoht" => t("Parklakoht"),
		);
		foreach($fields as $field_key => $field_value)
		{
			$t->define_field(array(
				"name" => $field_key,
				"caption" => $field_value,
				"align" => "center",
			));
		}
		$db_table_contents_obj = obj($arr['obj_inst']->prop("db_table_contents"));
		$db_table_name = $db_table_contents_obj->prop("db_table");


		$flats = $this->db_fetch_array("SELECT * FROM ".$db_table_name." WHERE maja_nimi='".$arr['obj_inst']->prop('house_name')."' ORDER BY korter ASC");
		foreach($flats as $flat)
		{
/*

// not sure if it is going to work and don't have time right now to
// fool around with it :(
			$row = array("flat_nr" => $flat['korter']);
			foreach($fields as $field_key => $field_value)
			{
				if($field_key != "flat_nr")
				{
					$row[$field_key] = html::textbox(array(
						"name" => "row[".$flat['id']."][".$field_key."]",
						"value" =>
					));
				}
				
			}
*/
			$t->define_data(array(
				"flat_nr" => $flat['korter'],
				"flat_nr2" => html::textbox(array(
						"name" => "row[".$flat['id']."][korter2]",
						"value" => $flat['korter2'],
						"size" => 5,
					)),
				"korrus" => html::textbox(array(
						"name" => "row[".$flat['id']."][korrus]",
						"value" => $flat['korrus'],
						"size" => 5,
					)),
				"korrus_nimi" => html::textbox(array(
						"name" => "row[".$flat['id']."][korrus_nimi]",
						"value" => $flat['korrus_nimi'],
						"size" => 10,
					)),
				"staatus2" => html::textbox(array(
						"name" => "row[".$flat['id']."][staatus2]",
						"value" => $flat['staatus2'],
						"size" => 15,
					)),
				"panipaik" => html::textbox(array(
						"name" => "row[".$flat['id']."][panipaik]",
						"value" => $flat['panipaik'],
						"size" => 5,
					)),
				"terrass" => html::textbox(array(
						"name" => "row[".$flat['id']."][terrass]",
						"value" => $flat['terrass'],
						"size" => 5,
					)),
				"r6du" => html::textbox(array(
						"name" => "row[".$flat['id']."][r6du]",
						"value" => $flat['r6du'],
						"size" => 10,
					)),
				"korteriomand" => html::textbox(array(
						"name" => "row[".$flat['id']."][korteriomand]",
						"value" => $flat['korteriomand'],
						"size" => 5,
					)),
				"parklakoht" => html::textbox(array(
						"name" => "row[".$flat['id']."][parklakoht]",
						"value" => $flat['parklakoht'],
						"size" => 10,
					)),

			));
		}

		$t->set_sortable(false);
	}

	///
	// gets the xml file and parses it
	// params:
	// obj_inst - current obj. inst

	function get_xml($arr)
	{
		$orig_xml_file = $arr['obj_inst']->prop("orig_xml_file");
	
		if($orig_xml_file == false)
		{
//			echo "faili ei &otilde;nnestunud leida!";
			return false;
		}
	
		$data_file_content = $this->get_file(array(
			"file" => $orig_xml_file,
		));

		return parse_xml_def(array("xml" => $data_file_content));

	}

	function callback_pre_save($arr)
	{

		if ($arr["new"])
		{
			return false;
		};

		$default_db_table = "db_table_01";
		$db_table_contents_obj = obj($arr['obj_inst']->prop($default_db_table));
		$db_table_name = $db_table_contents_obj->prop("db_table");
		$xml_parent_tag = "";
		$counter = 0;
		foreach ($arr['request'] as $key => $value)
		{
			if (stristr($key, "db_table_") !== false && $key != $default_db_table)
			{
//				$db_table_contents_obj = obj($arr['obj_inst']->prop($key));
				$db_table_contents_obj = obj($value);
				$db_table_name = $db_table_contents_obj->prop("db_table");
				$default_db_table = $key;
				$counter++;
			}
			if (stristr($key, "xml_field_") !== false)
			{
				$xml_parent_tag = $value;
				$counter++;
			}
			if ($counter == 2)
			{
				break;
			}
		}
		
		if ($default_db_table == "db_table_01")
		{
			$db_tmp = $this->db_fetch_array("SELECT id,staatus,staatus2,korter,korter2 FROM ".$db_table_name." WHERE maja_nimi LIKE '%".$arr['obj_inst']->prop("house_name")."%'");
		}
		else
		{
			$db_tmp = $this->db_fetch_array("SELECT id FROM ".$db_table_name." WHERE maja_nimi LIKE '%".$arr['obj_inst']->prop("house_name")."%'");
		}


		if ($arr['request']['group'] == "additional_info")
		{

			foreach($arr['request']['row'] as $key => $value)
			{
				$db_query = "UPDATE ".$db_table_name." SET ";
				foreach($value as $k => $v)
				{
					if(($k == "staatus2" || $k == "korter2") && empty($v))
					{
//						$staatus_from_xml = $this->db_fetch_array("SELECT staatus FROM ".$db_table_name." WHERE id=".$key);
						foreach($db_tmp as $tmp_key => $tmp_value)
						{
							if($tmp_value['id'] == $key)
							{
								if($k == "staatus2")
								{
//									$v = $staatus_from_xml[0]['staatus'];
									$v = $tmp_value['staatus'];
									break;
								}
								if($k == "korter2")
								{
									$v = $tmp_value['korter'];
									break;
								}
							}
						}
					}
					$db_query .= $k."='".$v."', ";
				}
				$db_query = substr($db_query, 0, (strlen($db_query)-2));
				$this->db_query($db_query." WHERE id=".$key);
			}

		}

//		if ($arr['request']['group'] == "content_change_korterid" || $arr['request']['group'] == "content_change")
		if ($arr['request']['group'] == "content_change" || stristr($arr['request']['group'], "group_") !== false)
		{

			$xml_file_content = $this->get_xml(array("obj_inst" => $arr['obj_inst']));
// updateing the xml_file_content array with the content from $arr['request']

			foreach($arr['request'] as $key => $value)
			{
				$keys = explode("@", $key);
				$xml_file_content[0][$keys[0]]['attributes'][$keys[1]] = $value;
			}

// Here I'll loop through all the xml_file_content array and but together a string
// which I can nicely write into file.

			

			$result = "";


// some preparations for updating the db table

	//		$sql_commands = array();
	//
	//		$xml_to_db_conns = $arr['obj_inst']->meta("xml_to_db_conns");


			
			foreach($xml_file_content[0] as $key => $value)
			{
				if($value['type'] == "open")
				{
					$result .= "<".$value['tag'].$attribs.">\n";
				
				}

				if($value['type'] == "complete")
				{
					$attributes = "";

	//				$sql_command = array();

					if(isset($value['attributes']))
					{
						foreach($value['attributes'] as $attribute_key => $attribute_value)
						{
							$attributes .= " ".$attribute_key."=\"".$attribute_value."\" ";
	//						if($value['tag'] == "korter" && $xml_to_db_conns[$attribute_key] != "empty")
	//						{
	//							$sql_command[$xml_to_db_conns[$attribute_key]] = $attribute_value;
	//						}
						}
					}


					$result .= "\t<".$value['tag'].$attributes."/>\n";
	//				if(!empty($sql_command))
	//				{
	//					array_push($sql_commands, $sql_command);
	//				}
				}

				if($value['type'] == "close")
				{
					$result .= "</".$value['tag'].">\n";
				}
			}

// ok, i can write the xml stuff to file here, because i had the string here and i'm going to read the info
// from file later

			if(!$file_handle = fopen($arr['obj_inst']->prop("new_xml_file"), "w"))
			{
				die(sprintf(t("Couldn't open the file (%s) to write"), $arr['obj_inst']->prop("new_xml_file")));
			}


			if(fwrite($file_handle, $result) === FALSE)
			{
				die(sprintf(t("Cannot write to this file: %s"), $arr['obj_inst']->prop("new_xml_file")));
			}

			fclose($file_handle);


			$house_name = $arr['obj_inst']->prop("house_name");

			$sql_commands = array();

			$xml_to_db_conns = $arr['obj_inst']->meta("xml_to_db_conns");
// tuleb kokku panna sql_commands massiiv, mis koosneb $sql_command massiividest kus on kirjas milliseid v2lju uuendatakse
// baasis 
//			arr($arr);

			$xml_tags = $this->get_complete_xml_tags(array(
		//		"obj_inst" => $arr['obj_inst'],
				"parsed_xml_file" => parse_xml_def(array("xml" => $result)),
				"parent" => $xml_parent_tag,
			));
			foreach ($xml_tags as $xml_tag)
			{
				$sql_command = array();
				if (isset($xml_tag['attributes']))
				{
					foreach($xml_tag['attributes'] as $attribute_key => $attribute_value)
					{
						if ($default_db_table == "db_table_01")
						{
							if ($xml_to_db_conns[$attribute_key] != "empty" && $xml_to_db_conns[$attribute_key] != "")
							{
								$sql_command[$xml_to_db_conns[$attribute_key]] = $attribute_value;
							}
						}
						else
						{
							$sql_command[$attribute_key] = $attribute_value;
						}
					}
				}
				if (!empty($sql_command))
				{
					$sql_commands[] = $sql_command;
				}
			}

			$db_result = $this->db_fetch_array("SELECT * FROM ".$db_table_name." WHERE maja_nimi='".$house_name."'");
			$insert = false;
			if(empty($db_result))
			{
				$insert = true;
			}
			foreach($sql_commands as $sql_command)
			{
				if($insert)
				{
					$sql_query = "INSERT INTO ".$db_table_name." SET maja_nimi='".$house_name."', ";
				}
				else
				{
					$sql_query = "UPDATE ".$db_table_name." SET ";
				}
				foreach($sql_command as $sql_c_key => $sql_c_value)
				{
					// check if the status is changed, if it is changed, 
					// then it is updated in staatus2 field also
					// same thing with korter field
					if(($sql_c_key == "staatus" || $sql_c_key == "korter"))
					{
/*
						$staatus_from_db = $this->db_fetch_array("SELECT staatus FROM ".$db_table_name." WHERE korter='".$sql_command['korter']."'");
						if($staatus_from_db[0]['staatus'] != $sql_c_value)
						{
							$sql_query .= "staatus2='".$sql_c_value."', ";
							
						}
*/
						// insert data into db table (xml file is changed at the first time)	
						if ($insert)
						{
							if ($sql_c_key == "staatus")
							{
								$sql_query .= "staatus='".$sql_c_value."', ";
								if ($default_db_table == "db_table_01")
								{
									$sql_query .= "staatus2='".$sql_c_value."', ";
								}
							}
							if ($sql_c_key == "korter")
							{
								$sql_query .= "korter='".$sql_c_value."', ";
								if ($default_db_table == "db_table_01")
								{
									$sql_query .= "korter2='".$sql_c_value."', ";
								}
							}
						}
						// update the db table
						else
						{
							foreach($db_tmp as $tmp_value)
							{
								if($tmp_value['korter'] == $sql_command['korter'] )
								{
									if($sql_c_key == "staatus")
									{
										if($tmp_value['staatus'] != $sql_c_value || empty($tmp_value['staatus2']))
										{
											$sql_query .= "staatus='".$sql_c_value."', ";	
											if ($default_db_table == "db_table_01" )
											{
												$sql_query .= "staatus2='".$sql_c_value."', ";
											}
											break;
										}
									}
									if($sql_c_key == "korter")
									{
										if($tmp_value['korter'] != $sql_c_value || empty($tmp_value['korter2']))
										{
											$sql_query .= "korter2='".$sql_c_value."', ";
											break;
										}
									}
								}
							}
						}
						
					}
					else
					{
						$sql_query .= $sql_c_key."='".$sql_c_value."', ";
						if ($insert)
						{
							
						}
					}
				}


				$sql_query = trim($sql_query);
				if ($sql_query{strlen($sql_query)-1} == ",")
				{
					$sql_query{strlen($sql_query)-1} = " ";
				//$sql_query = substr($sql_query, 0, (strlen($sql_query)-2));
				
				if($insert)
				{
					$this->db_query($sql_query);
			//		echo $sql_query."<br>";
				}
				else
				{
					if ($default_db_table == "db_table_01")
					{
						$this->db_query($sql_query." WHERE maja_nimi='".$house_name."' AND korter='".$sql_command['korter']."'");
//						echo $sql_query." WHERE maja_nimi='".$house_name."' AND korter='".$sql_command['korter']."'<br>";

					}
					else
					{
						$this->db_query($sql_query." WHERE maja_nimi='".$house_name."' AND nr='".$sql_command['nr']."'");
					}
//					echo $sql_query." WHERE maja_nimi='".$house_name."' AND korter='".$sql_command['korter']."'<br>";
//					echo $sql_query." WHERE maja_nimi='".$house_name."' AND nr='".$sql_command['nr']."'<br>";

				}
				}
			}
			
//			$this->db_query();


		}


	}

	function callback_mod_tab($arr)
	{
		$tab_no = end(explode("_", $arr['id']));
		if (!$arr['obj_inst']->prop("xml_field_".$tab_no) && $arr['id'] == "group_".$tab_no)
		{
//			return false;
		}
		else
		if ($arr['id'] == "group_".$tab_no)
		{
			$arr['caption'] = $arr['obj_inst']->prop("xml_field_".$tab_no);
		}
	}
}
?>
