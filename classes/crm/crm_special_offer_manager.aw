<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_special_offer_manager.aw,v 1.5 2009/03/11 10:39:45 instrumental Exp $
// crm_special_offer_manager.aw - Organisatsiooni eripakkumiste haldus 
// Valitud eripakkumiste veebi kuvamiseks
/*

@classinfo syslog_type=ST_CRM_SPECIAL_OFFER_MANAGER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@property listtype type=chooser orient=vertical field=meta method=serialize
@caption Kuvatava nimekirja t&uuml;&uuml;p

@property url_to type=textbox field=meta method=serialize 
@caption Aadress, kuhu nimekirjast suunata

@groupinfo list_all caption="K&otilde;ik eripakkumised"
@default group=list_all

	@property all_offers_table type=table caption=no
	@caption Eripakkumised

@groupinfo list_show caption="Kuvatavad eripakkumised"
@default group=list_show

@groupinfo list_manual caption="Valitud nimekiri" submit=yes parent=list_show
@default group=list_manual

	@property offers type=releditor reltype=RELTYPE_CRM_SPECIAL_OFFER field=meta method=serialize mode=manager props=name,status,valid_from,valid_to table_fields=name,valid_from,valid_to direct_links=1 store=no delete_relations=1
	@caption Valitud pakkumised

@groupinfo list_automatic caption="Automaatne nimekiri" parent=list_show
@default group=list_automatic

	@property crm_db type=objpicker clid=CL_CRM_DB method=serialize field=meta
	@caption Hallatav kliendibaas
	
	@property num_offers type=textbox default=10 method=serialize field=meta
	@caption Kuvatavate eripakkumiste arv
	
@reltype CRM_SPECIAL_OFFER value=1 clid=CL_CRM_SPECIAL_OFFER
@caption Eripakkumine

@reltype CRM_DB value=2 clid=CL_CRM_DB
@caption Kliendibaas

*/

class crm_special_offer_manager extends class_base
{
	const AW_CLID = 995;

	function crm_special_offer_manager()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "crm/crm_special_offer_manager",
			"clid" => CL_CRM_SPECIAL_OFFER_MANAGER
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case 'listtype':
				$prop["options"] = array(
					1 => t("Valitud eripakkumised"),
					2 => t("Automaatselt uusimad eripakkumised"),
				);
			break;
			case 'all_offers_table':
				$this->_mk_all_offers_table(&$prop['vcl_inst'], $arr);
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
			case 'num_offers':
				if (!is_numeric($prop['value']) || $prop['value'] < 1)
				{
					$retval = PROP_ERROR;
					$prop['error'] = t("Kuvatavate pakkumiste arv peab olema positiivne!");
				}
			break;
			case 'listtype':
				if (!is_numeric($prop['value']) || ($prop['value'] != 1 && $prop['value'] !=2) )
				{
					$retval = PROP_ERROR;
					$prop['error'] = t("Nimekirja t&uuml;&uuml;p vigane.");
				}
			break;
			case 'all_offers_table':
				$o = $arr['obj_inst'];
				$mustconnect = array();
				// Find offers we must connect to
				foreach (ifset($arr['request'],'selected') as $ofid)
				{
					$of = obj($ofid);
					if ($of->class_id() == CL_CRM_SPECIAL_OFFER)
					{
						$mustconnect[$ofid] = $ofid;
					}
				}
				
				// Loop through existing connections, removing unneeded ones
				$conns = $o->connections_from(array(
					'type' => 'RELTYPE_CRM_SPECIAL_OFFER',
				));
				foreach ($conns as $conn)
				{
					$to = $conn->conn['to'];
					if (isset($mustconnect[$to]))
					{
						unset($mustconnect[$to]);
					}
					else
					{
						$conn->delete();
					}
				}
				
				// Connect ones still left
				foreach ($mustconnect as $to)
				{
					$o->connect(array(
						'to' => $to,
						'type' => 'RELTYPE_CRM_SPECIAL_OFFER',
					));
				}
			break;

		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
	
	// Generates table of all special offers in syste
	function _mk_all_offers_table (&$t, $arr)
	{
		$fields = array(
			array(
				'name' => 'name',
				'caption' => t("Eripakkumise nimi"),
				'filter' => "text",
			),
			array(
				'name' => 'company',
				'caption' => t("Organisatsioon"),
				'filter' => "text",
			),
			array(
				'name' => 'sectors',
				'caption' => t("Organisatsiooni tegevusalad"),
				'sortable' => false,
			),
			array(
				'name' => 'valid_from',
				'caption' => t("Kehtivuse algus"),
			),
			array(
				'name' => 'valid_to',
				'caption' => t("Kehtivuse l&otilde;pp"),
			),
		);
		foreach ($fields as $f)
		{
			 // By default fields are sortable and aligned to right
			$f['sortable'] = isset($f['sortable']) ? $f['sortable'] : true;
			$f['align'] = isset($f['align']) ? $f['align'] : 'right';
			$t->define_field($f);
		}
		$t->define_chooser(array(
			'field' => 'sel_sp',
			'name' => 'selected',
			'caption' => t("Kuvamiseks valitud"),
		));
		$ol = new object_list(array(
			'class_id' => CL_CRM_SPECIAL_OFFER,
		));
		
		// Find a date format
		$df = aw_ini_get('config.dateformats');

		// List offers selected for viewing
		$selected_offers = array();
		$o = $arr['obj_inst'];
		$conns = $arr['obj_inst']->connections_from(array(
			'type' => 'RELTYPE_CRM_SPECIAL_OFFER',
		));
		foreach ($conns as $con)
		{
			$selected_offers[$con->conn['to']] = true;
		}
		
		// Populate table
		$target = $ol->arr();
		foreach ($target as $oid => $o)
		{
			if (!$this->can('view', $o->id()))
			{
				continue;
			}
		
			// Find company and sectors
			$company = $sectors = $company_name = "";
			$conns = $o->connections_to(array(
				'from.class_id' => CL_CRM_COMPANY,
				'type' => 'RELTYPE_SPECIAL_OFFERS',
			));
			if (count($conns))
			{
				$co = $conns[0]->from();
				$company = html::href(array(
					'caption' => $co->name(),
					'url' => $this->mk_my_orb("change", array(
						'id' => $co->id(),
						'return_url' => get_ru(),
					), CL_CRM_COMPANY),
				));
				$company_name = $co->name();

				// Sectors / tegevusalad
				$ol = new object_list(array(
					"class_id" => CL_CRM_SECTOR,
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_SECTOR.RELTYPE_TEGEVUSALAD(CL_CRM_COMPANY).id" => $co->id(),
							"CL_CRM_SECTOR.RELTYPE_SECTOR(CL_CRM_COMPANY_SECTOR_MEMBERSHIP).company" => $co->id(),
						),
					)),
					"lang_id" => array(),
					"site_id" => array(),
				));
				$sectors = array();
				foreach($ol->names() as $sid => $name)
				{
					$s = $con->to();
					$name = strlen($name) ? $name : '('.t("nimetu").' '.$sid.')';
					$sectors[] = html::href(array(
						'caption' => '<b>'.$name.'</b>',
						'url' => $this->mk_my_orb("change", array(
							'id' => $sid,
							'return_url' => get_ru(),
						), CL_CRM_SECTOR),
					));
				}
				$sectors = join(", ", $sectors);
			}
			
			
			$row = array(
				'company' => $company,
				'sectors' => $sectors,
				'valid_from' => date($df[2], $o->prop('valid_from')),
				'valid_to' => date($df[2], $o->prop('valid_to')),
				'name' => html::href(array(
					'caption' => strlen($o->name()) ? $o->name() : '('.t("nimetu").' '.$o->id().')',
					'url' => $this->mk_my_orb("change", array(
						'id' => $o->id(),
						'return_url' => get_ru(),
					), CL_CRM_SPECIAL_OFFER),
				)),
				'filtervalue-name' => $o->name(),
				'filtervalue-company' => $company_name,
//				'status' => $o->prop('status') == STAT_ACTIVE ? t("Jah") : t("Ei"),
				'sel_sp' => $o->id(),
				'selected' => isset($selected_offers[$o->id()]),
			);
			$t->define_data($row);
		}
	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		if (isset($_GET['offer']) && is_oid($_GET['offer']) && !aw_global_get("in_promo_display"))
		{
			$return = $this->show_offer(array('id' => $arr['alias']['target'], 'offer' => $_GET['offer']));
		}
		else
		{
			$return = $this->show_list(array("id" => $arr["alias"]["target"]));
		}
		return $return;
	}

	////
	// Shows selected offer
	function show_offer($arr)
	{
		$oid = $arr['offer'];
		$this->read_template('show.tpl');
		$this->sub_merge = 1;
		if (!is_oid($oid) || !$this->can('view', $oid))
		{
			return "";
		}
		$offer = obj($oid);
		if ($offer->class_id() != CL_CRM_SPECIAL_OFFER)
		{
			return "";
		}
		$instance = get_instance(CL_CRM_SPECIAL_OFFER);
		$this->vars(array(
			'offer' => $instance->show(array('id' => $oid)),
		));
		return $this->parse('offer_show');
	}

	////
	// Shows list of offers
	function show_list($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("list.tpl");
		$this->sub_merge	= 1;
		$this->use_eval		= 0;
		$list = "";
		$url = $ob->prop('url_to');
		if (empty($url))
		{
			$url = '/'.aw_global_get('section').'?offer=';
		}
		$this->vars(array(
			'name' => $ob->prop("name"),
			'url'  => $url,
		));
		$instance = get_instance(CL_CRM_SPECIAL_OFFER);
		switch ($ob->prop("listtype"))
		{
			case 1: // Manual list
				foreach ($ob->connections_from(array('type' => "RELTYPE_CRM_SPECIAL_OFFER")) as $id => $c)
				{
					$o = $c->to();
					$this->vars(array(
					//	"offer" => $instance->show(array('id' => $item_o->oid)),
						'offer_id' => $o->oid,
						'offer_name' => $o->name(),
					));
					$this->parse("offer1");
				}
			break;
			case 2: // Automatic list
				$id_db = $ob->prop("crm_db");
				if (!is_oid($id_db))
				{
					break;
				}
				$db = obj($id_db);
				$id_dir = $db->prop("dir_firma");
				if (is_array($id_dir))
				{
					$id_dir = reset($id_dir);
				}
				if (is_oid($id_dir) && ($dir=obj($id_dir)) && $dir->class_id() == CL_MENU)
				{
					$ol = new object_list(array(
						'class_id' => CL_CRM_SPECIAL_OFFER,
						'status' => STAT_ACTIVE,
					));
					$ol->sort_by(array(
						'prop'	=> 'modified',
						'order'	=> 'desc',
					));
					
					$togo = $ob->prop('num_offers');
					
					for ($o =& $ol->begin(); $togo > 0 && !$ol->end(); $o =& $ol->next())
					{
						$ok = false;
						// Basically check for every special_offer to be in selected CRM_DB
						foreach ($o->connections_to(array('type' => 40, 'from.class_id' => CL_CRM_COMPANY)) as $id => $c)
						{
							$o_company = $c->from();
							if($o_company->parent() == $id_dir)
							{
								$ok = true;
							}
						}
						
						if ($ok)
						{
							$togo--;
							$this->vars(array(
								'offer_id' => $o->oid,
								'offer_name' => $o->name(),
							));
							$this->parse("offer1");
						}
					}
				}
			break;
		}
		return $this->parse('offer_list');
	}

//-- methods --//
}
?>
