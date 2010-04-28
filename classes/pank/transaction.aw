<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/pank/transaction.aw,v 1.6 2008/01/31 13:55:00 kristo Exp $
// transaction.aw - Ülekanne 
/*

@classinfo syslog_type=ST_TRANSACTION relationmgr=yes no_status=1 no_comment=1 maintainer=kristo
@tableinfo pank_transaction index=oid master_table=objects master_index=oid

@groupinfo general caption="Üldine" submit=no

@default table=objects
@default group=general

//nime väljas hoian selgitust ja kasutajale näitan "nimi" asemel "Selgitus" teksti
@property name type=textbox
@caption Selgitus

@default table=pank_transaction

@property trans_from_object type=text store=no
@caption Kandja

@property trans_from_account_hr type=text table=pank_transaction store=no 
@caption Kandja konto

@property trans_from_account type=hidden table=pank_transaction no_caption=1 datatype=int
@caption Kandja konto

@property trans_to_object type=text store=no
@caption Saaja

@property trans_to_account_hr type=text store=no
@caption Saaja konto

@property trans_to_account type=hidden no_caption=1 datatype=int
@caption Saaja konto

@property sum type=textbox
@caption Summa

@property time type=hidden no_caption=1 datatype=int
@caption Kellaaeg

@property time_hr type=text store=no
@caption Kellaaeg

@property is_completed type=hidden no_caption=1 datatype=int
@caption Kinnitatud

@property completion_confirmation type=hidden no_caption store=no value=0
@caption Completion confirmation

@property soorita type=submit store=no no_caption=1

*/

define('PERCENTAGE_TAX', 0);
define('SUM_TAX', 1);

class transaction extends class_base
{
	const AW_CLID = 650;

	var $completion_confirmation = 0;

	function transaction()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "pank/transaction",
			"clid" => CL_TRANSACTION
		));
	}

	function callback_on_load($arr)
	{
		if(is_oid($arr['request']['id']))
		{
			$obj = new object($arr['request']['id']);
			$params = array(
				'request' => array(
					'from_account' => $obj->prop('trans_from_account'),
					'parent' => $obj->prop('trans_to_account'),
				),
			);

			$this->init_info($params);
			$this->transaction_time = $obj->prop('time');
		}
		else
		{
			$this->init_info($arr);
		}

		if($arr['is_completed'])
		{
			$this->completion_confirmation = 1;
		}
	}

	function init_info($arr)
	{
		//niih, tundub, et from on olemas
		if(is_oid($arr['request']['from_account']))
		{
			//let's determine the needed "from" info
			$this->from_account = new object($arr['request']['from_account']);
			$this->from_object = new object($this->from_account->parent());

			//let's determine the needed "to" info
			$this->to_account = new object($arr['request']['parent']);
			$this->to_object = new object($this->to_account->parent());

			$this->transaction_time = time();
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		if(true || $arr['request']['action'] == 'new')
		{
			switch($prop["name"])
			{
				case 'name':
					if($arr['obj_inst']->prop('is_completed'))
					{
						$prop['type'] = 'text';
					}
					break;
				case 'sum':
					if($arr['obj_inst']->prop('is_completed'))
					{
						$prop['type'] = 'text';	
					}
					break;
				case 'trans_from_object':
					$prop['value'] = $this->from_object->prop('name');
					break;
				case 'trans_from_account':
					$prop['value'] = $this->from_account->id();
					break;
				case 'trans_from_account_hr':
					$prop['value'] = $this->from_account->prop('name');
					break;

				case 'trans_to_object':
					$prop['value'] = $this->to_object->prop('name');
					break;
				case 'trans_to_account':
					$prop['value'] = $this->to_account->id();
					break;
				case 'trans_to_account_hr':
					$prop['value'] = $this->to_account->prop('name');
					break;
					
				case 'time_hr':
					$prop['value'] = $this->time2date($this->transaction_time);
					break;

				case 'time':
					$prop['value'] = $this->transaction_time;
					break;
				case 'soorita':
					if($arr['obj_inst']->prop('is_completed'))
					{
						return PROP_IGNORE;
					}

					if(is_oid($arr['obj_inst']->id()))
					{
						$prop['caption'] = 'Kinnitan';
					}
					break;
				case 'completion_confirmation':
					if(is_oid($arr['obj_inst']->id()))
					{
						$prop['value'] = 1;
					}
					break;
			}
		}
		return $retval;
	}

	function callback_post_save($arr)
	{
		if($this->completion_confirmation)
		{
			/*
				arr['obj_inst'] - ülekanne, mis asub trans_to_account konto sees.
					mix trans_to_account konto sees? sellepärast, et interfaisist
					ülekande tegemisel parentiks läheb to objekt
				$transaction - ülekanne, mis asub trans_from_account konto
					sees
			*/
			$from_account = new object($arr['obj_inst']->prop('trans_from_account'));
			$to_account = new object($arr['obj_inst']->prop('trans_to_account'));
			$parent = $from_account->id();

			$transaction = new object();
			$transaction->set_class_id(CL_TRANSACTION);
			$transaction->set_parent($parent);
			$transaction->set_prop('trans_from_account', $from_account->id() );
			$transaction->set_prop('trans_to_account', $to_account->id() );
			$transaction->set_prop('sum', $arr['obj_inst']->prop('sum') );

			$transaction->set_prop('is_completed', 1);
			$arr['obj_inst']->set_prop('is_completed', 1);

			$time = time();

			$arr['obj_inst']->set_prop('time',$time);
			$transaction->set_prop('time',$time);

			$arr['obj_inst']->save();
			$transaction->save();

			//transactions in place, can tranfer the money itself
			$sum = $arr['obj_inst']->prop('sum');
			$from_account->set_prop('account_balance',$from_account->prop('account_balance')-$sum);
			$to_account->set_prop('account_balance',$to_account->prop('account_balance')+$sum);
			$from_account->save();
			$to_account->save();

			//have to post a message
			//the "to" part of the transaction
			//wants to know cos then it can
			//collect taxes
			//NOW I'M THINKING, THAT WHY DO I NEED TO MESSAGE?
			$this->do_taxes(array(
				'transaction' => &$transaction,
				'from' => &$from_account,
				'to' => &$to_account,
				'do_taxes' => 1,
			));
			//transaction completed
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'completion_confirmation':
				$this->completion_confirmation = $prop['value'];
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

	/**
		Will do the transaction. A transaction consists of 2 objects. 
		First transaction object is created under the "from" account
		and corresponding transaction is create under the "to" account.
		The transaction will add/subtract money from the "from" account
		and the same goes for the to account.
	
		arr
			from - account id or account object
			to - account id or account object
			sum - sum that is transfered from 
					"from" account to "to" account.
			name - name of the transaction
			comment - comment of the transaction
	**/
	function do_transaction($arr)
	{
		//init from account
		$from_account = $arr['from'];
		if(!is_object($from_account))
		{
			$from_account = new object($from_account);
		}
		
		//init to account
		$to_account = $arr['to'];

		if(!is_object($to_account))
		{
			$to_account = new object($to_account);
		}

		$from_transaction = new object();
		$from_transaction->set_class_id(CL_TRANSACTION);
		$from_transaction->set_parent($from_account->id());
		$from_transaction->set_prop('trans_from_account', $from_account->id());
		$from_transaction->set_prop('trans_to_account', $to_account->id());
		$from_transaction->set_prop('is_completed',1);
		$from_transaction->set_prop('name', $arr['name']);

		$to_transaction = new object();
		$to_transaction->set_class_id(CL_TRANSACTION);
		$to_transaction->set_parent($to_account->id());
		$to_transaction->set_prop('trans_from_account', $from_account->id());
		$to_transaction->set_prop('trans_to_account', $to_account->id());
		$to_transaction->set_prop('is_completed',1);
		$to_transaction->set_prop('name', $arr['name']);
		//
		$time = time();
		//
		$to_transaction->set_prop('time',$time);
		$from_transaction->set_prop('time',$time);
		//
		$to_transaction->save();
		$from_transaction->save();

		//transactions in place, can transfer the money itself
		$arr['sum'] = (float)$arr['sum'];
		$from_account->set_prop('account_balance',$from_account->prop('account_balance')-$arr['sum']);
		$to_account->set_prop('account_balance',$to_account->prop('account_balance')+$arr['sum']);
		//
		$from_account->save();
		$to_account->save();

		return true;
	}

	function get_info_on_transaction($trans)
	{
		$rtrn = array();

		if(!is_object($trans))
		{
			$trans = new object($trans);
		}

		$obj = new object($trans->prop('trans_from_account'));
		$rtrn['from_account'] = $obj;
		$obj = new object($obj->parent());
		$rtrn['from_obj'] = $obj;
		
		$obj = new object($trans->prop('trans_to_account'));
		$rtrn['to_account'] = $obj;
		$obj = new object($obj->parent());
		$rtrn['to_obj'] = $obj;

		return $rtrn;
	}

	function do_taxes($arr)
	{
		if($arr['do_taxes'])
		{
			$to_parent = new object($arr['to']->parent());
		
			$pank = get_instance(CL_PANK);
			$tax_to_account = $pank->get_tax_account_for_obj(&$to_parent);
			
			$conns = $to_parent->connections_from(array(
							'type' => 'RELTYPE_TAX_CHAIN'
			));

			foreach($conns as $tax_chain)
			{
				$tax_chain = $tax_chain->to();
				
				$conns2 = $tax_chain->connections_from(array(
								'type' => 'RELTYPE_TAX'
				));

				foreach($conns2 as $tax)
				{
					$tax2 = new object($tax->prop('to'));
					$tax = $tax->to();
					$sum = $arr['transaction']->prop('sum')
								*
							 ($tax->prop('tax_percentage')/100);
							 
					//summa maxu puhul võtan maha
					//kindla summa raha, mitte protsendi :)
					if($tax->prop('tax_type')==SUM_TAX)
					{
						$sum = $tax->prop('tax_amount');
					}
					$this->do_transaction(array(
						'from' => &$arr['to'],
						'to' => &$tax_to_account,
						'sum' => $sum,
						'name' => "Maks ".$tax->name(),
					));
				}
			}
		}
	}
}
?>
