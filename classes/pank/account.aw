<?php
// $Header: /home/cvs/automatweb_dev/classes/pank/account.aw,v 1.8 2008/01/31 13:55:00 kristo Exp $
// account.aw - Konto 
/*
@tableinfo pank_account index=oid master_table=objects master_index=oid
@classinfo syslog_type=ST_ACCOUNT relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

@default table=pank_account

@property account_type type=text group=general datatype=int
@caption Konto tüüp

@groupinfo account_overview caption="Konto ülevaade" submit=no
@default group=account_overview

@groupinfo account_overview_incoming caption="Sissemaksed" submit=no parent=account_overview
@default group=account_overview_incoming

@property account_balance type=text
@caption Konto saldo

@property account_transactions_incoming type=table store=no no_caption=1
@caption Ülekanded

@groupinfo account_overview_outgoing caption="Väljamaksed" submit=no parent=account_overview
@default group=account_overview_outgoing

@property account_balance_outgoing type=text
@caption Konto saldo

@property account_transactions_outgoing type=table store=no no_caption=1
@caption Ülekanded

@groupinfo other_account_overview caption="Konto ülevaade" submit=no

@groupinfo other_account_overview_incoming caption="Sissemaksed" submit=no parent=other_account_overview
@default group=other_account_overview_incoming

@property other_account_balance_incoming type=text store=no
@caption Konto saldo

@property other_account_transactions_incoming type=table store=no no_caption=1 store=no
@caption Ülekanded

@groupinfo other_account_overview_outgoing caption="Väljamaksed" submit=no parent=other_account_overview
@default group=other_account_overview_outgoing

@property other_account_balance_outgoing type=text store=no
@caption Konto saldo

@property other_account_transactions_outgoing type=table store=no no_caption=1 store=no
@caption Ülekanded

@reltype TAX_CHAIN value=1 clid=CL_TAX_CHAIN
@caption Maksu pärg

*/

define('NORMAL_ACCOUNT', 0);
define('TAX_ACCOUNT', 1);

define('ACCOUNT_INCOMING', 0);
define('ACCOUNT_OUTGOING', 1);

class account extends class_base
{
	function account()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "pank/account",
			"clid" => CL_ACCOUNT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'account_balance_outgoing':
				$prop['value'] = $arr['obj_inst']->prop('account_balance');
				break;
			case 'account_type':
				if($arr['obj_inst']->prop('account_type')==NORMAL_ACCOUNT)
				{
					$prop['value'] = 'Tava konto';	
				}
				else
				{
					$prop['value'] = 'Maksu konto';	
				}
			break;
			case 'account_transactions_outgoing':
				$arr['extra']['type'] = ACCOUNT_OUTGOING;
				$this->do_account_transactions($arr);
			break; 
			case 'account_transactions_incoming':
				$arr['extra']['type'] = ACCOUNT_INCOMING;
				$this->do_account_transactions($arr);
			break; 
			case 'other_account_transactions_incoming':
				$arr['extra']['type'] = ACCOUNT_INCOMING;
				$arr['parent'] = $this->get_the_other_account(&$arr['obj_inst']);
				$this->do_account_transactions($arr);
			break;
			case 'other_account_transactions_outgoing':
				$arr['extra']['type'] = ACCOUNT_OUTGOING;
				$arr['parent'] = $this->get_the_other_account(&$arr['obj_inst']);
				$this->do_account_transactions($arr);
			break;
			case 'other_account_balance_incoming':
				$other_account = $this->get_the_other_account(&$arr['obj_inst']);
				$prop['value']= $other_account->prop('account_balance');
			break;
			case 'other_account_balance_outgoing':
				$other_account = $this->get_the_other_account(&$arr['obj_inst']);
				$prop['value']= $other_account->prop('account_balance');
			break;
		};
		return $retval;
	}

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
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
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
	
	function do_account_transactions($arr)
	{
		$table = &$arr['prop']['vcl_inst'];
		$table->set_sortable(false);

		$table->define_field(array(
			'name' => 'name',
			'caption' => 'Selgitus',
			'sortable' => 1,
		));

		$table->define_field(array(
			'name' => 'from_object',
			'caption' => 'Kellelt',
			'sortable' => 1,
		));
		
		$table->define_field(array(
			'name' => 'from_account',
			'caption' => 'Konto',
			'sortable' => 1,
		));

		$table->define_field(array(
			'name' => 'to_object',
			'caption' => 'Kellele',
			'sortable' => 1,
		));

		$table->define_field(array(
			'name' => 'to_account',
			'caption' => 'Konto',
			'sortable' => 1,
		));

		$table->define_field(array(
			'name' => 'sum',
			'caption' => 'Summa',
			'sortable' => 1,
			'type' => int,
		));
		
		$table->define_field(array(
			'name' => 'time',
			'caption' => 'Aeg',
			'sortable' => 1,
		));

		if(!is_object($arr['parent']))
		{
			$arr['parent'] = &$arr['obj_inst'];
		}
		
		$ol_params = array(
			'parent' => $arr['parent']->id(),
			'class_id' => CL_TRANSACTION,
			'is_completed' => 1,
			'sort_by' => 'time desc',
		);

		if($arr['extra']['type'] == ACCOUNT_INCOMING)
		{
			$ol_params['trans_to_account'] = $arr['parent']->id();;
		}
		else if($arr['extra']['type'] == ACCOUNT_OUTGOING)
		{
			$ol_params['trans_from_account'] = $arr['parent']->id();	
		}

		$ol = new object_list($ol_params);

		$transactions = $ol->arr();

		$trans = get_instance(CL_TRANSACTION);
		
		foreach($transactions as $transaction)
		{
			$trans_info = $trans->get_info_on_transaction(&$transaction);

			$from_object_url = $this->mk_my_orb('change',
										array(
											'id' => $trans_info['from_obj']->id(),
											'group' => 'general',
										),
										$trans_info['from_obj']->class_id()
			);

			$from_object_url = html::href(array(
										'url' => $from_object_url,
										'caption' => $trans_info['from_obj']->name(),
			));
			
			$to_object_url = $this->mk_my_orb('change',
										array(
											'id' => $trans_info['to_obj']->id(),
											'group' => 'general',
										),
										$trans_info['to_obj']->class_id()
			);

			$to_object_url = html::href(array(
										'url' => $to_object_url,
										'caption' => $trans_info['to_obj']->name(),
			));
			
			$table->define_data(array(
				'name' => $transaction->name(),
				'from_object' => $from_object_url,
				'from_account' => $trans_info['from_account']->name(),
				'to_object' => $to_object_url, 
				'to_account' => $trans_info['to_account']->name(),
				'sum' => $transaction->prop('sum'),
				'time' => $this->time2date($transaction->prop('time')),
			));

		}
	} 

	function callback_mod_tab($arr)
	{
		$suffix = 'ülevaade';
		if($arr['id'] == 'other_account_overview' && $_GET["action"] == "change")
		{
			$account = $this->get_the_other_account(&$arr['obj_inst']);
			$arr['caption'] = $account->name().' '.$suffix;
		}
		else if($arr['id'] == 'account_overview')
		{
			$arr['caption'] = $arr['obj_inst']->name().' '.$suffix;
		}
	}

	/*
		Returns the corresponding account for the
		given account. Will return a tax account for
		normal account and a normal account for tax
		account.
		
		every object has 2 accounts, the normal one, and
	   the tax account, sometimes it is needed to get
		"the other" account for an account. This is were
		this function comes to play.
	*/
	function get_the_other_account($account)
	{
		if(!is_object($account))
		{
			$account = new object($account);
		}
		$pank = get_instance(CL_PANK);
		if($account->prop('account_type')==TAX_ACCOUNT)
		{
			return $pank->get_account_for_obj($account->parent());
		}
		else
		{
			return $pank->get_tax_account_for_obj($account->parent());
		}
	}

	function do_db_upgrade($tbl, $f)
	{
		switch($f)
		{
			case "account_balance_outgoing":
				$this->db_query("ALTER TABLE pank_account ADD account_balance_outgoing double");
				return true;
				break;
		}
	}
}
?>
