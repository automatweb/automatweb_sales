<?php
// $Header: /home/cvs/automatweb_dev/classes/pank/pank.aw,v 1.12 2008/04/28 13:59:43 kristo Exp $
// crm_pank.aw - Pank 
/*
@classinfo syslog_type=ST_PANK relationmgr=yes maintainer=kristo
@tableinfo pank index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@default table=pank

@groupinfo account_overview caption="Konto ülevaade"

@default group=account_overview

@property account_balance type=textbox field=account_balance
@caption Konto saldo

@groupinfo accounts caption="Kontod" submit=no
@caption Kontod
@default group=accounts

@property accounts_table type=table no_caption=1
@caption Kontod

@groupinfo pank_owner_group caption="..." submit=no
@groupinfo pank_owner_group_main_sub caption="..." submit=no parent=pank_owner_group

@default group=pank_owner_group_main_sub

@layout main_toolbar_hbox type=hbox group=pank_owner_group_main_sub

@property main_toolbar type=toolbar parent=main_toolbar_hbox no_caption=1
@caption Suurepärane toolbar

@layout main_hbox type=hbox group=pank_owner_group_main_sub width=20%:80%

@layout main_vbox_left type=vbox group=pank_owner_group_main_sub parent=main_hbox

@property main_treeview type=treeview parent=main_vbox_left no_caption=1
@caption Treeview

@layout main_vbox_right type=vbox group=pank_owner_group_main_sub parent=main_hbox

@property main_company_info type=table parent=main_vbox_right no_caption=1 store=no
@caption Firma info

@property main_company_projects_info type=table parent=main_vbox_right no_caption=1 store=no
@caption Firma projektide info

@property main_table type=table parent=main_vbox_right no_caption=1
@caption Tabel

@groupinfo pank_make_trans caption="Tee ülekanne" 
@default group=pank_make_trans

@layout trans_hbox type=hbox group=pank_make_trans width=20%:20%:30%

@layout trans_vbox_left type=vbox group=pank_make_trans parent=trans_hbox

@property konto_caption type=text parent=trans_vbox_left no_caption=1 value=Konto store=no
@caption kapten

@property from_account type=select parent=trans_vbox_left no_caption=1 store=no
@caption Konto

@layout trans_vbox_middle type=vbox group=pank_make_trans parent=trans_hbox 

@property to_company_caption type=text parent=trans_vbox_middle no_caption=1 value=Saaja store=no
@caption kapten

@property to_company type=select parent=trans_vbox_middle no_caption=1 store=no
@caption Kellele

@layout trans_vbox_right type=vbox group=pank_make_trans parent=trans_hbox

@property summa_caption type=text parent=trans_vbox_right no_caption=1 value=Summa store=no
@caption kapten

@property summa type=textbox parent=trans_vbox_right no_caption=1 size=10 store=no
@caption Summa

@layout trans_vbox_last type=vbox group=pank_make_trans parent=trans_hbox

@property submit_caption type=text parent=trans_vbox_last no_caption=1 value=Soorita store=no

@property submit type=submit parent=trans_vbox_last no_caption=1 size=10 store=no 
@caption Soorita

@reltype OWNER value=1 clid=CL_CRM_COMPANY 
@caption Kellele kuulub

*/

define('NORMAL_ACCOUNT', 0);
define('TAX_ACCOUNT', 1);

class pank extends class_base
{
	var $company_account = null;

	function pank()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "pank/pank",
			"clid" => CL_PANK
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
			case 'main_toolbar':
				$this->do_main_toolbar(&$arr);
				break;
			case 'main_company_projects_info':
				switch($arr['request']['main'])
				{
					case 'projects':
						if(is_oid($arr['request']['company']))
						{
							$this->do_company_projects_table($arr);
						}				
					break;
					case 'events':
						if(is_oid($arr['request']['project_id']))
						{
							$this->do_company_projects_events_table($arr);
						}						
					break;
					case 'event_members':
						if(is_oid($arr['request']['event_id']))
						{
							$this->do_company_projects_events_members_table($arr);	
						}
					break;
					default :
						return PROP_IGNORE;
						break;
				}
				break;
			case 'main_company_info':
				//näitame firma kontot
				if($arr['request']['main']=='projects' && is_oid($arr['request']['company']))
				{
					$arr['parent'] = $arr['request']['company'];	
				}
				//näitame projekti kontot
				else if($arr['request']['main'] == 'events' && is_oid($arr['request']['project_id']))
				{
					$arr['parent'] = $arr['request']['project_id'];
				}
				//näitame evendi kontot
				else if($arr['request']['main'] == 'event_members' && is_oid($arr['request']['event_id']))
				{
					$arr['parent']= $arr['request']['event_id'];
				}
				//näitame isiku kontot
				else if($arr['request']['main'] == 'member' && is_oid($arr['request']['person_id']))
				{
					$arr['parent'] = $arr['request']['person_id'];
				}
				//näitame lusikat
				else
				{
					return PROP_IGNORE;
				}
				$this->do_objects_account_info_table($arr);
				break;
			case 'main_table':
				if(
					is_oid($arr['request']['company']) 
						|| 
					is_oid($arr['request']['project_id'])
						||
					is_oid($arr['request']['event_id'])
						||
					is_oid($arr['request']['person_id'])
				)
				{
					$arr['parent'] = $arr['request']['project_id']?$arr['request']['project_id']:$arr['request']['company'];

					if(is_oid($arr['request']['person_id']))
					{
						$arr['parent'] = $arr['request']['person_id'];
						echo "näitame persooni ülekandeid<br>";
					}
					else if(is_oid($arr['request']['event_id']))
					{
						$arr['parent'] = $arr['request']['event_id'];
						echo "näitame evendi ülekandeid<br>";
					}
					else if(is_oid($arr['request']['project_id']))
					{
						$arr['parent'] = $arr['request']['project_id'];
						echo "näitame projekti ülekandeid<br>";
					}
					else if(is_oid($arr['request']['company']))
					{
						$arr['parent'] = $arr['request']['company'];
						echo "näitame firma ülekandeid<br>";
					}
					else
					{
						return PROP_IGNORE;
					}
			
					$this->do_transactions_table($arr);	
				}
				break;
			case 'main_treeview':
				$this->do_main_treeview($arr);
				break;
			case 'accounts_table':
				$this->do_accounts_table($arr);
				break;
			case 'from_account':
				$ol = new object_list(array(
						'parent' => $arr['obj_inst']->id(),
						'class_id' => CL_ACCOUNT
				));
				$prop['options'] = $ol->list_names;
				break;
			case 'to_company':
				$company = get_instance(CL_CRM_COMPANY);
				$clients = array();
				if (!$arr["new"])
				{
					$comp = $this->get_owner($arr['obj_inst']);
				};
				if($comp)
				{
					$company->get_customers_for_company($comp, &$clients);
					$ol = new object_list(array(
							'class_id' => CL_CRM_COMPANY,
							'oid' => $clients
					));
					$prop['options'] = $ol->list_names;
				}
				break;
		};
		return $retval;
	}

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

	function do_accounts_table($arr)
	{
		$table = &$arr['prop']['vcl_inst'];

		$table->define_field(array(
			'name' => 'name',
			'caption' => 'Nimi',
		));

		$table->define_field(array(
			'name' => 'saldo',
			'caption'=> 'Saldo',
			'type' => 'int',
		));

		$ol = new object_list(array(
					'class_id' => CL_ACCOUNT,
					'parent' => $arr['obj_inst']->id()
		));
		
		for($o=$ol->begin();!$ol->end();$o=$ol->next())
		{
			$table->define_data(array(
				'name' => $o->prop('name'),				
				'saldo' => $o->prop('account_balance'),
			));
		}
	}

	function callback_mod_tab($arr)
	{
		if(!$arr["new"] && ($arr['id']=='pank_owner_group_main_sub' || $arr['id'] == 'pank_owner_group'))
		{
			$company = $this->get_owner($arr['obj_inst']);
			if($company)
			{
				$arr['caption'] = $company->prop('name');
			}
		}
	}

	function do_main_treeview($arr)
	{
		$tree = &$arr['prop']['vcl_inst'];
		
		$counter = 5;
		/*$tree_node_info = array(
			'id' => 1,
			'name' => 'Projektid',
		);
		$tree->add_item(0, $tree_node_info);
		$obj = $this->get_owner($arr['obj_inst']);
		if($obj)
		{
			$this->do_main_tree_projects(&$tree, $obj, 1, &$counter);
		}*/
		
		/*$tree_node_info = array(
			'id' => 2,
			'name' => 'Tegijad',
		);
		$tree->add_item(0, $tree_node_info);
		
		$tree_node_info = array(
			'id' => 3,
			'name' => 'Tegevused',
		);
		$tree->add_item(0, $tree_node_info);
		*/

		/*$tree_node_info = array(
			'id' => 4,
			'name' => 'Organisatsioonid',
		);
		
		$tree->add_item(0, $tree_node_info);*/
		
		$this->do_main_tree_orgs(&$tree, &$arr, 0, &$counter);
	}

	function do_main_tree_projects($tree, $obj, $parent, $ids)
	{
		if($obj)
		{
			$conns = $obj->connections_from(array(
				'type' => "RELTYPE_PROJECT",
			));
			
			foreach($conns as $conn)
			{
				$tree_node_info = array(
					'id' => $ids++,
					'name' => $conn->prop('to.name'),
					'iconurl' => icons::get_icon_url(CL_PROJECT),
					'url' => aw_url_change_var(array(
									'project_id' => $conn->prop('to'),
									'return_url' => '',
									'main' => 'events',
									'event_id' => '',
									'person_id' => '',
								)),
				);
				$tree->add_item($parent, $tree_node_info);
				$obj = $conn->to();
				$this->do_main_tree_project_events(&$tree, &$obj, $ids-1, &$ids);
			}
		}
		else
		{
			return null;
		}
	}

	function do_main_tree_project_events($tree, $obj, $parent, $ids)
	{
		if($obj)
		{
			$conns = $obj->connections_from(array(
				'type' => "RELTYPE_PRJ_EVENT",
			));
			
			foreach($conns as $conn)
			{
				$tree_node_info = array(
					'id' => $ids++,
					'name' => $conn->prop('to.name'),
					'iconurl' => icons::get_icon_url(CL_PROJECT),
					'url' => aw_url_change_var(array(
									'event_id' => $conn->prop('to'),
									'person_id' => '',
									'return_url' => '',
									'main' => 'event_members',
								)),
				);
				$tree->add_item($parent, $tree_node_info);
				$obj = $conn->to();
				$this->do_main_tree_event_participants(&$tree, $obj, $ids-1, &$ids);
			}
		}
	}
	
	function do_main_tree_event_participants($tree, $obj, $parent, $ids)
	{
		if($obj)
		{
			$conns = $obj->connections_to(array(
				'type' => 10, //crm_person.reltype_person_task
			));

			
			foreach($conns as $conn)
			{
				$tree_node_info = array(
					'id' => $ids++,
					'name' => $conn->prop('from.name'),
					'iconurl' => icons::get_icon_url(CL_PROJECT),
					'url' => aw_url_change_var(array(
									'person_id' => $conn->prop('from'),
									'main' => 'member',
									'return_url' => ''
								)),
				);
				$tree->add_item($parent, $tree_node_info);
				$obj = $conn->from();
			}
		}
	}

	function do_main_tree_orgs($tree, $arr,$parent, $ids)
	{
		$obj = $this->get_owner($arr['obj_inst']);
		if(sizeof($obj))
		{
			$company = get_instance(CL_CRM_COMPANY);
			$companies = array();
			$company->get_customers_for_company($obj, &$companies);
			
			foreach($companies as $key=>$value)
			{
				$obj = new object($value);
				$tree_node_info = array(
					'id' => $ids++,
					'name' => strlen($obj->prop('name'))>15?substr($obj->prop('name'),0,15)."...":$obj->prop('name'),
					'iconurl' => icons::get_icon_url(CL_CRM_COMPANY),
					'url' => aw_url_change_var(array(
									'company'=>$obj->id(),
									'return_url' => '',
									'project_id' => '',
									'event_id' => '',
									'person_id' => '',
									'main' => 'projects',
								)),
				);
				$tree->add_item($parent, $tree_node_info);
				//kuvan ka kõik projektid selle kompanii alt
				$this->do_main_tree_projects(&$tree, &$obj, $ids-1, &$ids);
			}
		}
	}

	/*
		Every compay has ONE account. This function returns that
		account. If it doesn't find any, then i'll create one manually.
		
	*/
	function get_company_account($comp)
	{
		//listing all the accounts from the company
		$ol = new object_list(array(
						'class_id' => CL_ACCOUNT,
						'parent' => $comp->id(),
				));

		//checking if there is one
		if(sizeof($ol->ids()))
		{
			$tmp = $ol->arr();
			return current($tmp);
		}
		//none found, will create one
		else
		{
		
		}
	}

	function get_owner($obj)
	{
		//let's get the company of this pank
		//kui vaadata järgmist 3 rida koodi kaugemalt, siis
		//tundub see olevat amb kõrvalt vaates
		$conns = $obj->connections_from(array(
			'type' => 'RELTYPE_OWNER'
		));

		if(sizeof($conns))
		{
			$obj = current($conns);
			return $obj->to();
		}
		else
		{
			return null;
		}
	}

	/*

	*/
	function get_tax_account_for_obj($parent)
	{
		return $this->get_account_for_obj(&$parent, TAX_ACCOUNT);	
	}

	/*
		every company can have just one account, 
		its more like it should have one account.

		This function returns the account of the
		give company, if none exist, will make 
		a new one, and return that one.
	*/
	function get_account_for_obj($parent, $type=NORMAL_ACCOUNT)
	{
		//paistab, et tuli id hoopis sisse
		if(!is_object($parent))
		{
			$parent = new object($parent);
		}
		
		$ol = new object_list(array(
						'parent' => $parent->id(),
						'class_id' => CL_ACCOUNT,
						'account_type' => $type
		));
		if(sizeof($ol->ids()))
		{
			return new object(current($ol->ids()));
		}
		//make account
		else
		{
			$name = $parent->name()." konto";
			if($type==TAX_ACCOUNT)
			{
				$name = $parent->name().' maksu konto';
			}
			$obj = new object();
			$obj->set_class_id(CL_ACCOUNT);
			$obj->set_parent($parent->id());
			$obj->set_name($name);
			$obj->set_prop('account_type', $type);
			$obj->save();
			return $obj;
		}
	}

	function do_transactions_table($arr)
	{
		$table = &$arr['prop']['vcl_inst'];
		
		$table->define_field(array(
			'name' => 'from',
			'caption' => 'Kandja',			
		));

		$table->define_field(array(
			'name' => 'to',
			'caption' => 'Saaja'
		));

		$table->define_field(array(
			'name' => 'selgitus',
			'caption' => 'Selgitus',
		));
		
		$table->define_field(array(
			'name' => 'sum',
			'caption' => 'Summa',
		));

		$table->define_field(array(
			'name' => 'date',
			'caption' => 'Aeg'
		));


		$parent = $this->get_account_for_obj($arr['parent']);

		//let's list the accounts
		$ol = new object_list(array(
					'parent' => $parent->id(),
					'class_id' => CL_TRANSACTION,
					'is_completed' => 1,
					'sort_by' => 'pank_transaction.time asc',
		));
	
		$trans = get_instance(CL_TRANSACTION);

		$accounts = $ol->arr();
		
		foreach($accounts as $obj)
		{
			$trans_info = $trans->get_info_on_transaction(&$obj);
			if(!$trans_info)
			{
				continue;
			}

			$table->define_data(array(
					'from' => $trans_info['from_obj']->name(),
					'to' => $trans_info['to_obj']->name(),
					'selgitus' => $obj->name(),
					'date' => $this->time2date($obj->prop('time'))." ".$obj->prop('time'),
					'sum' => $obj->prop('sum'),
			));
		}
	}
	
	function do_objects_account_info_table($arr)
	{
		$table = &$arr['prop']['vcl_inst'];

		$table->set_layout('cool');

		$table->define_field(array(
			'name' => 'account_name',
			'caption' => 'Konto nimi',
		));

		$table->define_field(array(
			'name' => 'saldo',
			'caption' => 'Saldo',
			'type' => 'int',
		));

		$company = new object($arr['parent']);

		$account = $this->get_account_for_obj($company);
		
		$table->define_data(array(
					'account_name' => $account->name(),
					'saldo' => $account->prop('account_balance'),
		));
	}

	function do_company_projects_table($arr)
	{
		$table = &$arr['prop']['vcl_inst'];

		$table->define_field(array(
			'name' => 'project_name',
			'caption' => 'Projekt',
		));


		$table->define_field(array(
			'name' => 'project_account',
			'caption' => 'Konto',
		));

		$table->define_field(array(
					'name' => 'saldo',
					'caption' => 'Saldo',
					'type' => int,
		));

		$table->define_chooser(array(
					'name' => 'check',
					'field' => 'id',
					'caption' => 'X',
		));
	
		$ol = new object_list(array(
					'parent' => $arr['obj_inst']->id(),
					'class_id' => CL_ACCOUNT
		));


		$company = new object($arr['request']['company']);
		$crm_company = get_instance(CL_CRM_COMPANY);
		//getting all the projects for this company
		$projects = $crm_company->get_all_projects_for_company(array('id'=>$arr['request']['company']));
		
		$from_account = $this->get_account_for_obj($company);
		
		foreach($projects as $project)
		{
			$project_account = $this->get_account_for_obj($project); 

			$project_name = $this->mk_my_orb('change',
									array(
										'group' => $arr['request']['group'],
										'id' => $arr['request']['id'],
									),
									CL_PANK);

			$change_project_url = $this->mk_my_orb('change',array(
												'id' => $project->id()
											),CL_PROJECT);

			$table->define_data(array(
				'project_name' => html::href(array(
											'url'=>aw_url_change_var(array(
												'project_id' => $project->id(),
												'account_id' => '',
												'person_id' => '',
												'return_url' => '',
											)),
											'caption'=>$project->name()
										)).' '.
										html::href(array(
											'url' => $change_project_url,
											'caption' => 'Muuda'
										)).' '.
										html::href(array(
											'url' => $admin_menus_project_url,
											'caption' => 'Ava'
										)),
				'saldo' => $project_account->prop('account_balance'),
				'project_account' => $project_account->name()
								."<input type='hidden' name='from_account[".$project_account->id()
								."]' value='".$from_account->id()."'>",
				'id' => $project_account->id(),
			));
		}
		
		//now we have to get all the banking information for this company
		$conns = $company->connections_to(array(
			'type' => 1 //RELTYPE_OWNER
		));
	
	}

	function do_main_toolbar($arr)
	{
		$toolbar = &$arr['prop']['toolbar'];

		$toolbar->add_button(array(
			'name' => 'Tee ülekanne',
			'img' => 'objects/document.gif',
			'tooltip' => 'Soorita ülekanne',
			'action' => 'submit_make_transaction'
		));
	}
	
	/**
		@attrib name=submit_make_transaction
		@param id required type=int acl=view
	**/
	function submit_make_transaction($arr)
	{
		//arr['check'] sees on kontode id
		if(is_array($arr['check']) && sizeof($arr['check'])==1)
		{
			$account_id = current($arr['check']);
			$url =  $this->mk_my_orb('new',array(
								'parent' => $account_id,
								'from_account' => $arr['from_account'][$account_id],
							),
							CL_TRANSACTION
			);
			return $url;
		}
		else
		{
			echo "Mida ma pean tegema, kui kasutaja ei vali ühtegi checkboxi välja või valib rohkem kui ühe?";
		}
	}

	function do_company_projects_events_table($arr)
	{
		$table = &$arr['prop']['vcl_inst'];

		$table->define_field(array(
			'name' => 'event_name',
			'caption' => 'Sündmus',
		));

		$table->define_field(array(
			'name' => 'event_account',
			'caption' => 'Konto'
		));

		$table->define_field(array(
					'name' => 'saldo',
					'caption' => 'Saldo',
					'type' => int,
		));

		$table->define_chooser(array(
					'name' => 'check',
					'field' => 'id',
					'caption' => 'X',
		));
	
		$event_object = new object($arr['request']['project_id']);

		$from_account = $this->get_account_for_obj(&$event_object);
		
		$conns = $event_object->connections_from(array(
			'type' => "RELTYPE_PRJ_EVENT"
		));

		foreach($conns as $conn)
		{
			$event_account = $this->get_account_for_obj($conn->prop('to'));
			$table->define_data(array(
				'event_name' => html::href(array(
					'url' => aw_url_change_var(array(
							'event_id' => $conn->prop('to'),
							'return_url' => '',
					)),
					'caption' => $conn->prop('to.name'),
				)),
				'event_account' => $event_account->prop('name')
								."<input type='hidden' name='from_account[".$event_account->id()
								."]' value='".$from_account->id()."'>",
				'saldo' => $event_account->prop('account_balance'),
				'id' => $event_account->id(),
			));
		}
	}

	function do_company_projects_events_members_table($arr)
	{
		$table = &$arr['prop']['vcl_inst'];

		$table->define_field(array(
			'name' => 'member_name',
			'caption' => 'Liige',
		));


		$table->define_field(array(
			'name' => 'member_account',
			'caption' => 'Konto',
		));

		$table->define_field(array(
					'name' => 'saldo',
					'caption' => 'Saldo',
					'type' => int,
		));

		$table->define_chooser(array(
					'name' => 'check',
					'field' => 'id',
					'caption' => 'X',
		));
	
		$obj = new object($arr['request']['event_id']);
		$from_account = $this->get_account_for_obj(&$obj);
		$conns = $obj->connections_to(array(
			'type' => 10, //crm_person.reltype_person_task
		));	

		foreach($conns as $conn)
		{
			$obj = $this->get_account_for_obj($conn->from());
			$table->define_data(array(
				//'member_name' => $conn->prop('from.name'),
				'member_name' => html::href(array(
										'url' => aw_url_change_var(array(
											'person_id' => $conn->prop('from'),
											'return_url' => '',
										)),
										'caption' => $conn->prop('from.name'),
									)),
				'member_account' => $obj->prop('name')
								."<input type='hidden' name='from_account[".$obj->id()
								."]' value='".$from_account->id()."'>",
				'saldo' => $obj->prop('account_balance'),
				'id' => $obj->id(),
			));
		}
	}
}
?>
