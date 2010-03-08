<?php
/*
@classinfo maintainer=markop
*/
class crm_email_mgr extends aw_template
{
	function crm_email_mgr($arr)
	{
		$this->init();
	}

	/**
		@attrib name=upd_mails nologin=1
	**/
	function upd_mails($arr)
	{
		// list all persons with mailboxes
		$messenger_list = new object_list(array(
			"class_id" => CL_MESSENGER_V2,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_MESSENGER_V2.RELTYPE_MESSENGER_OWNERSHIP.name" => "%"
		));
		
		foreach($messenger_list->arr() as $messenger)
		{
			$owner = $messenger->get_first_obj_by_reltype("RELTYPE_MESSENGER_OWNERSHIP");
			if ($owner)
			{
				echo "messenger = ".$messenger->name()." <br>";
				// get imap config from messenger
				$imap = $messenger->get_first_obj_by_reltype("RELTYPE_MAIL_SOURCE");
				if ($imap)
				{
					$this->update_mails_from_imap($imap, $owner);
				}
			}
		}
		die(t("booyaa!"));
	}

	function update_mails_from_imap($imap, $owner)
	{
		$p = $owner->get_first_obj_by_reltype("RELTYPE_PERSON");
		$imap_i = $imap->instance();
		$imap_i->connect_server(array(
			"obj_inst" => $imap
		));
		echo $imap_i->test_connection(array(
			"obj_inst" => $imap
		))."<br>";

	echo "tested conns <br>\n";
	flush();
		// now we need to figure out which emails to scan. preferably new ones only. 
		$fld_c = $imap_i->get_folder_contents(array("from" => 0, "to" => 100000));
echo "got cont <br>\n";
flush();

		// make a list of all customer e-mail addresses
		$cust_mails = $this->_get_customer_email_list();
echo "got email list <br>\n";
flush();
		$existing_messages = $this->_get_existing_message_list();
echo "got existing <br>\n";
flush();
		echo "checking messages <br>\n";
		flush();
		// for each message, check if we can find the customer for it
		// if we can, import it
		foreach($fld_c as $id => $message)
		{
			if (isset($cust_mails[$message["froma"]]) && !isset($existing_messages[$id]))
			{
				$imap_i->msg_content = null;
				//$imap_i = get_instance("protocols/mail/imap");//$imap->instance();
				$cust_id = $cust_mails[$message["froma"]];
				$cust_obj = obj($cust_id);
//				$co_object = $cust_obj->get_first_obj_by_reltype("RELTYPE_WORK");
				if($cust_obj->class_id() == CL_CRM_PERSON && $cust_obj->company_id())
				{
					$cust_id = $cust_obj->company_id();
				}
				echo "import message ".$message["subject"]." to cust $cust_id <br>";
				
				$ms = $imap_i->fetch_message(array("msgid" => $id));
				$m = obj();
				$m->set_class_id(CL_CRM_EMAIL);
				$m->set_parent($cust_id);

				$m->set_name($message["subject"]);
				$m->set_prop("customer", $cust_id);
				$m->set_prop("from", $message["from"]);
				$m->set_prop("to", $ms["to"]);
				$m->set_prop("date", $message["tstamp"]);
				$m->set_prop("content", $ms["content"]);
				$m->set_prop("imap_id", $id);
				
				aw_disable_acl();
				$m->save();

				// attach participant
				$m->connect(array(
					"to" => $p->id(),
					"type" => "RELTYPE_PARTICIPANT"
				));
				aw_restore_acl();
				echo "imported message ".html::obj_change_url($m)." <br>";
			}
		}
	}

	function _get_existing_message_list()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_EMAIL,
			"lang_id" => array(),
			"site_id" => array()
		));
		$ret = array();
		foreach($ol->arr() as $o)
		{
			$ret[$o->prop("imap_id")] = $o->id();
		}
		return $ret;
	}

	function _get_customer_email_list()
	{
		$ret = array();

		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_CRM_COMPANY,
			"type" => "RELTYPE_EMAIL"
		));
		$ids = array();
		foreach($conns as $con)
		{
			$ids[] = $con["to"];
		}
		if (count($ids))
		{
			$ol = new object_list(array("oid" => $ids, "lang_id" => array(), "site_id" => array()));
			$ol->arr();
		}

		foreach($conns as $con)
		{
			$eml = obj($con["to"]);

			if ($eml->prop("mail") != "")
			{
				$ret[$eml->prop("mail")] = $con["from"];
			}
		}

		$conns = $c->find(array(
			"from.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_EMAIL"
		));
		$ids = array();
		foreach($conns as $con)
		{
			$ids[] = $con["to"];
		}
		if (count($ids))
		{
			$ol = new object_list(array("oid" => $ids, "lang_id" => array(), "site_id" => array()));
			$ol->arr();
		}
		foreach($conns as $con)
		{
			$eml = obj($con["to"]);

			if ($eml->prop("mail") != "")
			{
				$ret[$eml->prop("mail")] = $con["from"];
			}
		}

		return $ret;
	}
}

?>
