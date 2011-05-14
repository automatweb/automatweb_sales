<?php

class crm_offer_obj extends crm_offer_price_component_handler
{
	protected $rows;
	protected $price_components;
	protected $price_components_loaded = false;
	protected $row_price_components;
	protected $row_price_components_loaded = array();
	protected $salesman_data;
	protected $all_prerequisites_by_price_component;
	protected $mail_data = null;
	protected static $state_names;

	const STATE_NEW = 0;
	const STATE_SENT = 1;
	const STATE_CONFIRMED = 2;
	const STATE_CANCELLED = 3;

	public static function state_names($state = null)
	{
		if (0 === count(self::$state_names))
		{
			self::$state_names = array(
				self::STATE_NEW => t("Koostamisel"),
				self::STATE_SENT => t("Saadetud"),
				self::STATE_CONFIRMED => t("Kinnitatud"),
				self::STATE_CANCELLED => t("T&uuml;histatud")
			);
		}

		if (isset($state))
		{
			if (isset(self::$state_names[$state]))
			{
				$state_names = array($state => self::$state_names[$state]);
			}
			else
			{
				$state_names = array();
			}
			return $state_names;
		}
		else
		{
			return self::$state_names;
		}
	}

	/**	Creates crm_offer_template object enabling user to use this crm_offer as a template when creating new ones
		@attrib api=1 params=pos
		@param name required type=string
		@returns void
		@errors Throws awex_crm_offer if this offer is not saved
	**/
	public function create_template($name)
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before it can be saved as a template!");
		}

		$o = obj();
		$o->set_class_id(CL_CRM_OFFER_TEMPLATE);
		$o->set_parent($this->id());
		$o->set_name($name);
		$o->set_prop("offer", $this->id());
		$o->save();
	}

	/**	Creates duplicate of this crm_offer_obj object and returns the new crm_offer_obj object.
		@attrib api=1
		@returns crm_offer_obj object
	**/
	public function duplicate()
	{
		$rows = $this->get_rows();

		$o = obj($this->save_new());
		$o->set_prop("state", self::STATE_NEW);

		foreach ($rows as $row)
		{
			$new_row = obj($row->save_new());
			$new_row->offer = $o->id();
			$new_row->save();
		}

		$o->save();

		return $o;
	}
	
	/**	Returns temporary (or default value if temporary is not set) value of a given mail property
		@attrib api=1
	**/
	public function get_mail_prop($k)
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before mail data can be accessed!");
		}

		if($this->mail_data === null)
		{
			$this->mail_data = aw_global_get(sprintf("crm_offer_mail_data.%s", $this->id()));
		}

		return isset($this->mail_data[$k]) ? $this->mail_data[$k] : $this->default_mail_prop($k);
	}

	protected function default_mail_prop($k)
	{
		switch ($k)
		{
			case "mail_from":
				$salesman = obj($this->prop("salesman"));
				return $salesman->get_mail();

			case "mail_from_name":
				return $this->prop("salesman.name");

			case "mail_subject":
				return t("Pakkumus nr #offer_no#");

			case "mail_content":
				return t("Lugupeetav #customer.director#,

Saadame Teile pakkumuse nr #offer_no#.

Parimat,
#signature#");

			case "custom_recipients":
				return array();

			default:
				return null;
		}
	}

	public function set_mail_prop($k, $v)
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before mail data can be stored!");
		}

		if($this->mail_data === null)
		{
			$this->mail_data = safe_array(aw_global_get(sprintf("crm_offer_mail_data.%s", $this->id())));
		}

		$this->mail_data[$k] = $v;
		aw_session_set(sprintf("crm_offer_mail_data.%s", $this->id()), $this->mail_data);
	}

	public function clear_mail_data()
	{
		unset($this->mail_data);
		aw_session_del(sprintf("crm_offer_mail_data.%s", $this->id()));
	}

	/** Returns offer email recipients data
		@attrib api=1 params=pos
		@param type type=array default=array()
			Type(s) of recipients to return. Empty/default means all.
			Valid options for array elements:
				'' -- all recipients
				'project_managers' -- people associated with this project as project managers
				'user' -- bill creator and current user
				'default' -- crm default bill recipients
				'customer_general' -- general customer email contacts
				'customer_director' -- customer director email
				'customer_offer' -- customer offer reception email contacts
				'custom' -- user defined custom recipients
		@returns array
			Associative multidimensional array
				$string_recipient_email_address => array($string_recipient_oid_or_zero, $string_recipient_name)
		@errors
	**/
	public function get_mail_recipients($type = array())
	{
		if (!is_array($type))
		{
			throw new awex_obj_type("Invalid type argument " . var_export($type, true));
		}

		$recipients = array();
		$customer_oid = $this->prop("customer");
		$salesorg_oid = automatweb::$request->get_application()->prop("owner");

		if (!count($type) or in_array("customer_general", $type))
		{
			$name = $this->prop("customer.name");
			$oid = $this->prop("customer");
			foreach($this->get_cust_mails() as $email)
			{
				if (is_email($email))
				{
					$recipients[$email] = array($oid, $name);
				}
			}
		}

		if (!count($type) or in_array("customer_director", $type))
		{
			$director_oid = $this->prop("customer.firmajuht");
			if (is_oid($director_oid))
			{
				$director = obj($director_oid);
				$email = $director->get_mail($customer_oid);
				if (is_email($email))
				{
					$recipients[$email] = array($director->id(), $director->name());
				}
			}
		}

		if (!count($type) or in_array("customer_bill", $type))
		{

			$cro = $this->get_customer_relation();

			if ($cro !== false)
			{
				$bill_person_ol = new object_list($cro->connections_from(array("reltype" => "RELTYPE_BILL_PERSON")));
				if($bill_person_ol->count())
				{
					$person = $bill_person_ol->begin();

					do
					{
						$email = $person->get_mail($customer_oid);
						if (is_email($email))
						{
							$recipients[$email]  = array($person->id(), $person->name());
						}
					}
					while ($person = $bill_person_ol->next());
				}
			}
		}

		if (!count($type) or in_array("salesman", $type))
		{
			if ($this->prop("salesman"))
			{
				$person = obj($this->prop("salesman"));
				$email = $person->get_mail($salesorg_oid);
				if (is_email($email))
				{
					$recipients[$email] = array($person->id(), $person->name());
				}
			}
		}

		if (!count($type) or in_array("custom", $type))
		{
			// add current user
			if (aw_global_get("uid_oid"))
			{
				$user_inst = new user();
				$u = obj(aw_global_get("uid_oid"));
				$person = obj($user_inst->get_current_person());
				$email = $u->get_user_mail_address();
				if (is_email($email))
				{
					$recipients[$email] = array($person->id(), $person->name());
				}
			}

			// manually added recipients
			$custom = $this->get_mail_prop("custom_recipients");
			foreach ($custom as $email => $person_oid)
			{
				if (is_email($email))
				{
					if ($person_oid)
					{
						$person = new object($person_oid);
						$recipients[$email]  = array($person->id(), $person->name());
					}
					else
					{
						$recipients[$email]  = array(0, "");
					}
				}
			}

			// recipients defined by object relation
			$custom = $this->connections_from(array("type" => "RELTYPE_RECEIVER"));
			foreach($custom as $c)
			{
				$person = $c->to();
				$email = $person->get_mail();
				if (is_email($email))
				{
					$recipients[$email]  = array($person->id(), $person->name());
				}
			}
		}

		return $recipients;
	}

	protected function get_cust_mails()
	{
		if(!is_oid($this->prop("customer")))
		{
			return array();
		}

		$cust = obj($this->prop("customer"));
		if($cust->class_id() == CL_CRM_PERSON)
		{
			$mails = $cust->emails();
		}
		else
		{
			$mails = $cust->get_mails(array());
		}

		$ret = array();
		$default_mail = null;
		foreach($mails->arr() as $mail)
		{
			if($mail->prop("mail"))
			{
				$default_mail = $mail;
				if($mail->prop("contact_type") == 1)
				{
					$ret[$mail->id()]= $mail->prop("mail");
				}
			}
		}

		if(!sizeof($ret) && is_object($default_mail))
		{
			$ret[$default_mail->id()]= $default_mail->prop("mail");
		}
		return $ret;
	}

	/** Parses variables in invoice e-mail body or subject text
		@attrib api=1 params=pos
		@param text type=string
			Text to parse variables in
		@comment
			Available variables are
			#offer_no#
			#customer.name#
			#customer.director#
			#signature#

		@returns string
		@errors
	**/
	public function parse_mail_text($text)
	{
		$replace = array(
			"#offer_no#" => $this->id(),
			"#customer.name#" => $this->prop("customer.name"),
			"#customer.director#" => $this->prop("customer.firmajuht.name"),
			"#signature#" => $this->get_sender_signature(),
		);

		foreach($replace as $key => $val)
		{
			$text = str_replace($key, $val , $text);
		}

		return $text;
	}

	public function get_offer_data()
	{
		$currency = new object($this->prop("currency"));

		return array(
			"id" => $this->id(),
			"date" => $this->prop("date"),
			"currency" => $currency->name(),
		);
	}

	/**	Returns an parsed array of customer data
		@attrib api=1
		@errors
			throws awex_crm_offer_customer if no customer is set for the offer
	**/
	public function get_customer_data()
	{
		if (!is_oid($customer_id = $this->prop("customer")))
		{
			throw new awex_crm_offer_customer("No customer set for this offer!");
		}

		$customer = new object($customer_id);
		$director = new object($customer->prop("firmajuht"));

		$director_profession = "";
		if($director->is_a(CL_CRM_PERSON))
		{
			$director_professions = $director->get_profession_names();
			$director_profession = reset($director_professions);
		}

		return array(
			"customer.name" => $customer->name(),
			"customer.mail" => $customer->get_mail(),
//			"customer.phone" => $customer->get_phone(),
			"customer.kmk_nr" => $customer->prop("tax_nr"),
			"customer.reg_nr" => $customer->prop("reg_nr"),

			"customer.address.address" => $this->get_customer_address(),
			"customer.address.city" => $this->get_customer_address("city"),
			"customer.address.county" => $this->get_customer_address("county"),
			"customer.address.index" => $this->get_customer_address("index"),
			"customer.address.country" => $this->get_customer_address("country"),
			"customer.address.street" => $this->get_customer_address("street"),

			"customer.director.name" => $director->name(),
			"customer.director.profession" => $director_profession,
		);
	}

	protected function get_customer_address($prop = "")
	{
		$customer = obj($this->prop("customer"));
		$a = $customer->is_a(CL_CRM_COMPANY) ? "contact" : "address";

		switch($prop)
		{
			case "street":
				return $customer->prop($a.".aadress");

			case "index":
				return $customer->prop($a.".postiindeks");

			case "country":
				return $customer->prop($a.".riik.name");

			case "county":
				return $customer->prop($a.".maakond.name");

			case "city":
				return $customer->prop($a.".linn.name");

			default:
				return $customer->prop($a.".name");
		}
	}

	public function get_salesman_data()
	{
		$salesman = new object($this->prop("salesman"));
		$salesman_professions = $salesman->get_profession_names();
		$salesman_profession = reset($salesman_professions);

		return array(
			"salesman.name" => $salesman->name(),
			"salesman.profession" => $salesman_profession ,
		);
	}

	public function get_salesorg_data()
	{
		$salesorg = new object(automatweb::$request->get_application()->prop("owner"));

		$data = array(
			"salesorg.name" => $salesorg->name(),
			"salesorg.reg_nr" => $salesorg->prop("reg_nr"),
			"salesorg.kmk_nr" => $salesorg->prop("tax_nr"),
			"salesorg.fax" => $salesorg->prop_str("telefax_id", true),
			"salesorg.url" => $salesorg->prop_str("url_id", true),
			"salesorg.phone" => $salesorg->prop_str("phone_id", true),
			"salesorg.ou" => $salesorg->prop("ettevotlusvorm.shortname"),
			"salesorg.bank_accounts" => array(),
		);

		foreach($salesorg->connections_from(array("type" => "RELTYPE_BANK_ACCOUNT")) as $c)
		{
			$acc = $c->to();
			$bank = obj();
			if ($this->can("view", $acc->prop("bank")))
			{
				$bank = obj($acc->prop("bank"));
			}
			$data["salesorg.bank_accounts"][] = array(
				"salesorg.bank_name" => $bank->name(),
				"salesorg.acct_no" => $acc->prop("acct_no"),
				"salesorg.bank_iban" => $acc->prop("iban_code")
			);
		}

		if ($this->can("view", $salesorg->prop("contact")))
		{
			$ct = obj($salesorg->prop("contact"));
			$ap = array($ct->prop("aadress"));
			if ($ct->prop("linn"))
			{
				$vars["salesorg.address.city"] = $ct->prop_str("linn");
				$ap[] = $ct->prop_str("linn");
			}
			$aps = join(", ", $ap).html::linebreak();
			$aps .= $ct->prop_str("maakond");
			$aps .= " ".$ct->prop("postiindeks");
			$data["salesorg.address.index"] = $ct->prop("postiindeks");
			$data["salesorg.address.county"] = $ct->prop_str("maakond");
			$data["salesorg.address.addr"] = $aps;
			$data["salesorg.address.street"] = $ct->prop("aadress");

			if ($this->can("view", $ct->prop("riik")))
			{
				$riik = obj($ct->prop("riik"));
				$data["salesorg.address.country"] = $riik->name();
			}
		}
		
		return $data;
	}

	protected function get_sender_signature()
	{
		$ret = array();
		$u = get_instance(CL_USER);
		$p = obj($u->get_current_person());
		$ret[]= $p->name();
		$names = $p->get_profession_names();
		$ret[]= reset($names);
		$names = $p->get_companies()->names();
		$ret[]= reset($names);
		$ret[]= $p->get_phone();
		$ret[]= $p->get_mail();
		return join("\n" , $ret);
	}

	/**
		@attrib api=1
	**/
	public function sent()
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before 'sent to's can be queried!");
		}

		$ol = new object_list(array(
			"class_id" => CL_CRM_OFFER_SENT,
			"offer" => $this->id(),
		));

		return $ol;
	}

	/** Sends offer document by e-mail
		@attrib api=1 params=pos
		@param to type=array
			Associative array of email addresses => names to send e-mail to
		@param subject type=string
			E-mail subject
		@param body type=string
			E-mail body text
		@param cc type=array
			Associative array of email addresses => names to send e-mail copy to
		@param bcc type=array
			Associative array of email addresses => names to send e-mail blind copy to
		@param from type=string default=""
			Sender e-mail address, default means either defined system default or current user e-mail address
		@param from_name type=string default=""
			Sender name, default means either defined system default or current user name
		@comment
		@returns void
		@errors
			throws awex_crm_offer_email if an invalid e-mail address given. awex_crm_offer_email::$email empty if no recipients or the faulty email address if encountered
			throws awex_crm_offer_send if sending e-mail fails
			throws awex_crm_offer_file if file attachment fails
		@qc date=20110512 standard=aw3
	**/
	public function send($to, $subject, $body, $cc = array(), $bcc = array(), $from = "", $from_name = "")
	{
		if (!count($to) and !count($cc) and !count($bcc))
		{
			throw new awex_crm_offer_email("Can't send mail, no recipients specified");
		}
		
		$offer_pdf = $this->make_pdf();

		if (!is_object($offer_pdf))
		{
			throw new awex_crm_offer_file("Offer file lost or not created. Offer id " . $this->id());
		}

		// parse recipients
		foreach ($to as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_crm_offer_email("Invalid email address '{$email_address}'. Sending offer " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$to[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}
		$to = implode(",", $to);

		foreach ($cc as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_crm_offer_email("Invalid email address '{$email_address}'. Sending offer " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$cc[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}
		$cc = implode(",", $cc);

		foreach ($bcc as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_crm_offer_email("Invalid email address '{$email_address}'. Sending offer " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$bcc[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}
		$bcc = implode(",", $bcc);

		// compose mail
		$from = is_email($from) ? $from : $this->get_mail_from();
		$from_name = empty($from_name) ? $this->get_mail_from_name() : $from_name;

		$awm = new aw_mail();
		$awm->create_message(array(
			"froma" => $from,
			"fromn" => $from_name,
			"subject" => $subject,
			"body" => $body,
			"to" => $to,
			"cc" => $cc,
			"bcc" => $bcc
		));

		/// add attachments
		$success = $awm->fattach(array(
			"path" => $offer_pdf->prop("file"),
			"contenttype"=> aw_mime_types::type_for_file($offer_pdf->name()),
			"name" => $offer_pdf->name(),
		));
/*		$att_comment = html::href(array(
			"caption" => html::img(array(
				"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
				"border" => 0,
			)).$offer_pdf->name(),
			"url" => $offer_pdf->get_url(),
		));*/

		if (!$success)
		{
			throw new awex_crm_offer_file("Attaching offer file (id: " . $invoice_pdf->id() . ") failed. Offer id " . $this->id());
		}

		$awm->htmlbodyattach(array(
			"data" => $body
		));


		// send mail
		$mail_sent = $awm->gen_mail();

		if (!$mail_sent)
		{
			throw new awex_crm_offer_send ("Sending '".$this->id()."' failed");
		}
/*
		// write log
		/// mail message object for logging
		$mail = obj(null, array(), CL_MESSAGE);
		$mail->set_parent($this->id());
		$mail->set_name(t("saadetud arve")." ".$this->id()." ".t("kliendile")." ".$this->get_customer_name());
		$mail->save();

		$attachments = array($invoice_pdf->id());
		$invoice_pdf ->set_parent($mail->id());
		$invoice_pdf->save();

		$mail->set_prop("attachments", $attachments);
		$mail->set_prop("customer", $this->prop("customer"));
		$mail->set_prop("message", $body);
		$mail->set_prop("html_mail", 1);
		$mail->set_prop("mfrom_name", $from_name);
		$mail->set_prop("mto", $to);
		$mail->set_prop("cc", $cc);
		$mail->set_prop("bcc", $bcc);
		$mail->save();

		$comment = html_entity_decode(sprintf(t("%s saatis arve nr. %s; summa %s; kuup&auml;ev: %s; kellaaeg: %s; aadressidele: %s; koopia aadressidele: %s; tekst: %s; lisatud failid: %s. "), aw_global_get("uid"), $this->prop("bill_no") , $this->prop("sum") , date("d.m.Y") , date("H:i") , htmlspecialchars($to), htmlspecialchars($cc), $body, $att_comment));
		$this->add_comment($comment);
*/

		$this->set_prop("state", self::STATE_SENT);
		$this->save();
	}

	public function make_pdf()
	{
		$pdf = null;
		if (is_oid($this->meta("last_pdf_file_oid")))
		{
			try
			{
				$id = $this->meta("last_pdf_file_oid");
				if (is_oid($id))
				{
					$pdf = obj($id, array(), CL_FILE);
				}
			}
			catch (awex_obj $e)
			{
			}
		}

		if (!$pdf)
		{
			$f = new file();
			$id = $f->create_file_from_string(array(
				"parent" => $this->id(),
				"content" => $this->get_pdf(),
				"name" => t("pakkumus_nr")."_".$this->id().".pdf",
				"type" => "application/pdf"
			));
			$this->set_meta("last_pdf_file_oid", $id);
			$this->save();
			$pdf = obj($id, array(), CL_FILE);
		}

		return $pdf;
	}

	private function get_pdf()
	{
		$inst = new crm_offer();
		return $inst->show(array(
			"id" => $this->id(),
			"pdf" => true,
		));
	}

	/**
		@attrib api=1 params=name
		@param firstname required type=string
		@param lastname required type=string
		@param organisation required type=string
		@param profession required type=string
		@param phone required type=string
		@param email required type=string
	**/
	public function confirm($arr)
	{
		$this->instance()->db_query(sprintf("INSERT INTO aw_crm_offer_confirmations
			(aw_offer, aw_firstname, aw_lastname, aw_organisation, aw_profession, aw_phone, aw_email, aw_time)
			VALUES (%u, '%s', '%s', '%s', '%s', '%s', '%s', %u)",
			$this->id(), $arr["firstname"], $arr["lastname"], $arr["organisation"], $arr["profession"], $arr["phone"], $arr["email"], time()));

		$this->set_prop("state", self::STATE_CONFIRMED);
		$this->save();
	}

	/**
		@attrib api=1
	**/
	public function confirmed_by()
	{
		$rows = $this->instance()->db_fetch_array(sprintf("SELECT * FROM aw_crm_offer_confirmations WHERE aw_offer = %u;",
			$this->id()));

		$data = array();
		foreach($rows as $i => $row)
		{
			foreach($row as $k => $v)
			{
				$data[$i][str_replace("aw_", "", $k)] = $v;
			}
		}

		return $data;
	}

	public function awobj_get_name()
	{
		return $this->id();
	}

	public function awobj_get_sum()
	{
		return aw_math_calc::string2float(parent::prop("sum"));
	}

	public function awobj_get_contracts()
	{
		$ol = $this->is_saved() ? new object_list(array(
			"class_id" => CL_CRM_DEAL,
			"CL_CRM_DEAL.RELTYPE_CONTRACT(CL_CRM_OFFER)" => $this->id(),
		)) : new object_list();
		return $ol;
	}

	public function awobj_set_contracts($contracts)
	{
		$contracts = (array)$contracts;
		if($this->is_saved())
		{
			$conns = $this->connections_from(array(
				"type" => "RELTYPE_CONTRACT",
			));
			$done = array();
			foreach($conns as $conn)
			{
				if(!in_array($conn->prop("to"), $contracts))
				{
					$conn->delete();
				}
				else
				{
					$done[] = $conn->prop("to");
				}
			}

			foreach($contracts as $contract)
			{
				if(!in_array($contract, $done))
				{
					$this->connect(array(
						"to" => $contract,
						"type" => "RELTYPE_CONTRACT"
					));
				}
			}
		}
		else
		{
			$this->set_meta("contracts", $contracts);
		}
	}

	public function save($exclusive = false, $previous_state = null)
	{
		if(is_oid($this->prop("customer")))
		{
			$this->set_customer_relation();
		}

		return parent::save($exclusive, $previous_state);
	}

	public function awobj_get_date()
	{
		$date = parent::prop("date");
		return !empty($date) ? $date : time();
	}

	/**	Returns true if this offer contains the given object, false otherwise
		@attrib api=1 params=pos
		@param object type=object
			The object to be added to the offer
		@returns boolean
		@errors Throws awex_crm_offer if this offer is not saved
	**/
	public function contains_object(object $o)
	{
		if(!isset($this->rows))
		{
			try
			{
				$this->load_rows();
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		foreach($this->rows as $row)
		{
			if($o->id() == $row->prop("object"))
			{
				return true;
			}
		}

		return false;
	}

	/**
		@attrib api=1
		@param object type=object
			The object to be added to the offer
		@returns void
		@error
			Throws awex_crm_offer if this offer is not saved.
			TODO: Throws awex_crm_offer if the object to be added doesn't implement crm_sales_price_component_interface.
	**/
	public function add_object(object $o)
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before rows can be added!");
		}

		$row = obj(null, array(), CL_CRM_OFFER_ROW);
		$row->set_parent($this->id());
		$row->set_prop("offer", $this->id());
		$row->set_prop("object", $o->id());
		$row->save();
	}

	/**	Returns array of
		@attrib api=1
		@returs crm_offer_row_obj[]
		@error Throws awex_crm_offer if this offer is not saved
	**/
	public function get_rows()
	{
		if(!isset($this->rows))
		{
			try
			{
				$this->load_rows();
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		return $this->rows;
	}

	/**
		@attrib api=1
		@returns object_list
		@errors Throws awex_crm_offer if this offer is not saved
	**/
	public function get_price_components_for_row(object $row)
	{
		if (!$this->price_components_loaded)
		{
			try
			{
				$this->load_price_components();
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		if (empty($this->row_price_components_loaded[$row->id()]))
		{
			try
			{
				$this->load_price_components_for_row($row);
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		return $this->row_price_components[$row->id()];
	}

	/**
		@attrib api=1
		@returns object_list
		@errors Throws awex_crm_offer if this offer is not saved
	**/
	public function get_price_components_for_total()
	{
		if (!$this->price_components_loaded)
		{
			try
			{
				$this->load_price_components();
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		return $this->price_components[crm_sales_price_component_obj::TYPE_TOTAL];
	}

	/**	Returns true if given price component is compulsory for this offer, false otherwise
		@attrib api=1 params=pos
		@param price_component required type=price_component_obj
			The price component the compulsoriness is queried for
		@returns boolean
	**/
	public function price_component_is_compulsory($price_component)
	{
		if($price_component->type == crm_sales_price_component_obj::TYPE_NET_VALUE)
		{
			return true;
		}

		$compulsory = false;

		if(!isset($this->salesman_data))
		{
			try
			{
				$this->load_offer_data_for_price_component();
			}
			catch(awex_crm_offer $e)
			{
				throw $e;
			}
		}

		$priority_of_current_compulsoriness = 0;
		$priorities_of_compulsoriness = array(
			CL_CRM_SECTION => 1,
			CL_CRM_PROFESSION => 2,
			CL_CRM_PERSON_WORK_RELATION => 3,
		);

		foreach($price_component->get_restrictions()->arr() as $restriction)
		{
			if(
				(
					in_array($restriction->prop("subject"), $this->salesman_data["work_relations"]->ids())
					|| in_array($restriction->prop("subject"), $this->salesman_data["professions"]->ids())
					|| in_array($restriction->prop("subject"), $this->salesman_data["sections"]->ids())
				)
				&& $priority_of_current_compulsoriness < $priorities_of_compulsoriness[$restriction->prop("subject.class_id")]
			)
			{
				$priority_of_current_compulsoriness = $priorities_of_compulsoriness[$restriction->prop("subject.class_id")];
				$compulsory = $restriction->prop("compulsory");
			}
		}

		return $compulsory;
	}

	/**	Returns array of lower and upper tolerance of given price component for this offer
		@attrib api=1 params=pos
		@param price_component required type=price_component_obj
			The price component the tolerance is queried for
		@returns array($min, $max)
	**/
	public function get_tolerance_for_price_component($price_component)
	{
		if(!isset($this->salesman_data))
		{
			try
			{
				$this->load_offer_data_for_price_component();
			}
			catch(awex_crm_offer $e)
			{
				throw $e;
			}
		}

		$min = $max = $price_component->prop("value");
		$value = $price_component->prop("value");

		$priority_of_current_tolerance = 0;
		$priorities_of_tolerance = array(
			CL_CRM_SECTION => 1,
			CL_CRM_PROFESSION => 2,
			CL_CRM_PERSON_WORK_RELATION => 3,
		);

		foreach($price_component->get_restrictions()->arr() as $restriction)
		{
			if(
				(
					in_array($restriction->prop("subject"), $this->salesman_data["work_relations"]->ids())
					|| in_array($restriction->prop("subject"), $this->salesman_data["professions"]->ids())
					|| in_array($restriction->prop("subject"), $this->salesman_data["sections"]->ids())
				)
				&& $priority_of_current_tolerance < $priorities_of_tolerance[$restriction->prop("subject.class_id")]
			)
			{
				$priority_of_current_tolerance = $priorities_of_tolerance[$restriction->prop("subject.class_id")];
				$min = $restriction->has_lower_tolerance() ? $restriction->prop("lower_tolerance") * $value / 100 : $value;
				$max = $restriction->has_upper_tolerance() ? $restriction->prop("upper_tolerance") * $value / 100 : $value;
			}
		}

		return array($min, $max);
	}

	public function sort_price_components($a, $b)
	{
		if(in_array($a->id(), $this->all_prerequisites_by_price_component[$b->id()]))
		{
			return -1;
		}
		elseif(in_array($b->id(), $this->all_prerequisites_by_price_component[$a->id()]))
		{
			return 1;
		}
		return 0;
	}

	public function get_all_prerequisites_for_price_component(object $price_component)
	{
		if(isset($this->all_prerequisites_by_price_component[$price_component->id()]))
		{
			return $this->all_prerequisites_by_price_component[$price_component->id()];
		}
		else
		{
			return array();
		}
	}

	/**	Loads relevant data to check if price component is compulsory and to find the correct tolerance.
	 *	Relevant data is currently section, work_relation and profession, all of which will be taken from the salesman of the offer.
	**/
	protected function load_offer_data_for_price_component()
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before rows can be loaded!");
		}

		//	Offer must always have a salesman!
		if(!is_oid($this->prop("salesman")))
		{
			throw new awex_crm_offer("No salesman defined for this offer!");
		}

		$salesman = obj($this->prop("salesman"));

		$this->salesman_data = array(
			"professions" => $salesman->get_professions(),
			"work_relations" => $salesman->get_active_work_relations(),
			"sections" => $salesman->get_sections(),
		);
	}

	protected function load_price_components_for_row(object $row)
	{
		$odl = new object_data_list(
			array(
				"class_id" => CL_CRM_SALES_PRICE_COMPONENT,
				"type" => array(crm_sales_price_component_obj::TYPE_UNIT, crm_sales_price_component_obj::TYPE_ROW, crm_sales_price_component_obj::TYPE_NET_VALUE),
				"applicables.id" => $row->prop("object"),
//				"application" => automatweb::$request->get_application()->id()
			),
			array(
				CL_CRM_SALES_PRICE_COMPONENT => array("applicables")
			)
		);

		$valid_price_components = array();
		foreach($odl->arr() as $oid => $odata)
		{
			if(true)	//	This is the place to check applicables
			{
				$valid_price_components[] = $oid;
			}
		}

		$ol = new object_list();
		$ol->add($valid_price_components);
		$ol->add($this->price_components[crm_sales_price_component_obj::TYPE_UNIT]);
		$ol->add($this->price_components[crm_sales_price_component_obj::TYPE_ROW]);

		$this->load_all_prerequisites_for_price_component_ol($ol);

		$ol->sort_by_cb(array($this, "sort_price_components"));
		$this->row_price_components[$row->id()] = $ol;

		$this->row_price_components_loaded[$row->id()] = true;
	}

	protected function load_all_prerequisites_for_price_component_ol($ol)
	{
		$net_value_price_components = array();
		foreach($ol->arr() as $o)
		{
			if(!isset($this->all_prerequisites_by_price_component[$o->id()]))
			{
				$this->all_prerequisites_by_price_component[$o->id()] = $o->get_all_prerequisites();
			}

			if($o->type == crm_sales_price_component_obj::TYPE_NET_VALUE)
			{
				$net_value_price_components[$o->id()] = $o->id();
			}
		}

		foreach($ol->arr() as $o)
		{
			$this->all_prerequisites_by_price_component[$o->id()] += $net_value_price_components;
			if(isset($this->all_prerequisites_by_price_component[$o->id()][$o->id()]))
			{
				unset($this->all_prerequisites_by_price_component[$o->id()][$o->id()]);
			}
		}
	}

	protected function load_price_components()
	{
		/*
		 *	This is the place where we'll load all the price components that are not row specific
		 */

		$this->all_prerequisites_by_price_component = array();

		//	Price components without applicables
		$q = sprintf("
			SELECT o.oid
			FROM objects o LEFT JOIN aliases a ON o.oid = a.source AND a.reltype = %u
			WHERE a.target IS NULL AND o.class_id = %u;", 2 /* RELTYPE_APPLICABLE */, CL_CRM_SALES_PRICE_COMPONENT);

		$price_components_without_applicables = array();
		foreach($this->instance()->db_fetch_array($q) as $row)
		{
			$price_components_without_applicables[] = $row["oid"];
		}

		if(!empty($price_components_without_applicables))
		{
			$odl = new object_data_list(
				array(
					"class_id" => CL_CRM_SALES_PRICE_COMPONENT,
					"oid" => $price_components_without_applicables,
					"type" => array(crm_sales_price_component_obj::TYPE_UNIT, crm_sales_price_component_obj::TYPE_ROW, crm_sales_price_component_obj::TYPE_TOTAL),
//					"application" => automatweb::$request->get_application()->id()
				),
				array(
					CL_CRM_SALES_PRICE_COMPONENT => array("type"),
				)
			);
			$price_component_ids_by_type = array(
				crm_sales_price_component_obj::TYPE_UNIT => array(),
				crm_sales_price_component_obj::TYPE_ROW => array(),
				crm_sales_price_component_obj::TYPE_TOTAL => array(),
			);
			foreach($odl->arr() as $oid => $odata)
			{
				$price_component_ids_by_type[$odata["type"]][] = $oid;
			}
			$ol = new object_list();
			$ol->add($price_component_ids_by_type[crm_sales_price_component_obj::TYPE_UNIT]);
			$this->price_components[crm_sales_price_component_obj::TYPE_UNIT] = $ol;

			$ol = new object_list();
			$ol->add($price_component_ids_by_type[crm_sales_price_component_obj::TYPE_ROW]);
			$this->price_components[crm_sales_price_component_obj::TYPE_ROW] = $ol;

			$ol = new object_list();
			$ol->add($price_component_ids_by_type[crm_sales_price_component_obj::TYPE_TOTAL]);
			$this->price_components[crm_sales_price_component_obj::TYPE_TOTAL] = $ol;
		}
		else
		{
			$ol = new object_list();
			$this->price_components[crm_sales_price_component_obj::TYPE_UNIT] = $ol;

			$ol = new object_list();
			$this->price_components[crm_sales_price_component_obj::TYPE_ROW] = $ol;

			$ol = new object_list();
			$this->price_components[crm_sales_price_component_obj::TYPE_TOTAL] = $ol;
		}

		$this->price_components_loaded = true;
	}

	/**	Used to set rows to be used with get_rows().
		@attrib api=1
	**/
	public function set_rows($rows)
	{
		$this->rows = $rows;
	}

	protected function load_rows()
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before rows can be loaded!");
		}

		$ol = new object_list(array(
			"class_id" => CL_CRM_OFFER_ROW,
			"offer" => $this->id(),
		));
		$this->rows = $ol->arr();
	}

	protected function set_customer_relation()
	{
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$owner = $application->prop("owner");
			$customer = new object($this->prop("customer"));
			$customer_relation = $customer->find_customer_relation($owner, true);
			if(is_object($customer_relation))
			{
				$customer_relation_id = $customer_relation->id();
				$this->set_prop("customer_relation", $customer_relation_id);

				return $customer_relation;
			}
		}
		return false;
	}

	protected function get_customer_relation()
	{
		$customer_relation = new object($this->prop("customer_relation"));
		if (!$customer_relation->is_a(CL_CRM_COMPANY_CUSTOMER_DATA))
		{
			$customer_relation = $this->set_customer_relation();
		}
		return $customer_relation;
	}
}

/** Generic crm_offer exception **/
class awex_crm_offer extends awex_crm {}

/** Customer errors **/
class awex_crm_offer_customer extends awex_crm {}

/** E-mail address errors **/
class awex_crm_offer_email extends awex_crm_offer
{
	public $email;
}

/** E-mail sending errors **/
class awex_crm_offer_send extends awex_crm_offer {}

/** PDF or other files related errors **/
class awex_crm_offer_file extends awex_crm_offer {}

/** status related errors **/
class awex_crm_offer_state extends awex_crm_offer {}
