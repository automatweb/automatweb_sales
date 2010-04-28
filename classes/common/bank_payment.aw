<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_BANK_PAYMENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default field=meta
@default method=serialize

@default group=general
	
	@layout general_l type=hbox width=40%:60%
	@layout general_left type=vbox closeable=1 area_caption=N&otilde;utavad v&nbsp;&auml;&auml;rtused parent=general_l

		@property name type=textbox field=name method=none parent=general_left
		@caption Nimi
	
		@property cancel_url type=textbox parent=general_left
		@caption Url, kuhu tagasi tulla eba&otilde;nnestunud makse puhul
	
	@layout general_right type=vbox closeable=1 area_caption=&nbsp; parent=general_l
		
		@property default_unit_sum type=textbox parent=general_right size=6
		@caption Vaikimisi &uuml;hiku summa
	
		@property template type=select parent=general_right
		@caption Pangavormide template

		@property private_key type=relpicker reltype=RELTYPE_KEY parent=general_right
		@caption Privaatv&otilde;ti
	
		@property nordea_private_key type=textbox parent=general_right
		@caption Nordea privaatv&otilde;ti
	
		@property bank_return_url type=textbox parent=general_right
		@caption Url, kuhu tagasi tulla eduka makse puhul

		@property expl type=textbox parent=general_right
		@caption Selgitus

		@property expl_controller type=relpicker reltype=RELTYPE_CTR table=objects field=meta method=serialize  parent=general_right
		@caption Selgituse kontroller

		@property imap type=relpicker reltype=RELTYPE_IMAP table=objects field=meta method=serialize  parent=general_right
		@caption Imap

		@property currency type=relpicker reltype=RELTYPE_CURRENCY table=objects field=meta method=serialize  parent=general_right
		@caption Vaikimisi valuuta

		@property test type=checkbox parent=general_right no_caption=1
		@caption testre&#382;iim (toimib ainult nende pankadega , millel on olemas testkeskkond)

		@property not_clickable_ref type=checkbox parent=general_right no_caption=1
		@caption &Auml;ra kuva viitenumbrit lingina

		@property credit_card_log_upload type=fileupload reltype=RELTYPE_CCL_UPLOAD form=+emb parent=general_right
		@caption Lisa reproduktsioon

@groupinfo bank caption="Pankade info"

@default group=bank submit=no
	@property bank type=table no_caption=1

@groupinfo log caption="Logi"

@groupinfo test caption="Test"
@default group=test submit=no

	@property test_priv_key type=relpicker reltype=RELTYPE_P_KEY
	@caption test privaatv&otilde;ti

	@property bank_test type=callback callback=callback_bank_test store=no no_caption=1

@default group=log
	@property find_date_start type=date_select store=no
	@caption Alates
	
	@property find_date_end type=date_select store=no
	@caption Kuni

	@property find_name type=textbox store=no
	@caption Maksja nimi
	
	@property find_ref type=textbox store=no
	@caption viide

	@property find_one type=checkbox store=no
	@caption 1 rida makse kohta

	@property do_find type=submit no_caption=1
	@caption Otsi
	
	@property log type=text store=no no_caption=1

@groupinfo doc caption="Teadmiseks" submit=no
@default group=doc
	
	@property doc type=text store=no no_caption=1
	@caption Dokumentatsioon

@groupinfo as_alias caption="Aliasena"
@default group=as_alias

	@property alias_amount type=textbox size=10 field=meta method=serialize
	@caption Summa

	@property alias_amount_ctr type=relpicker field=meta method=serialize reltype=RELTYPE_AMT_CTR
	@caption Summa kontrollerist

	@property alias_desc type=textbox maxlength=70 field=meta method=serialize
	@caption Kirjeldus

	@property alias_desc_ctr type=relpicker field=meta method=serialize reltype=RELTYPE_DESC_CTR
	@caption Kirjeldus kontrollerist

	@property alias_return_ctr type=relpicker field=meta method=serialize reltype=RELTYPE_RETURN_CTR
	@caption Makse kinnituse kontroller

#RELTYPES

@reltype KEY value=2 clid=CL_FILE
@caption Privaatv&otilde;ti

@reltype P_KEY value=3 clid=CL_FILE
@caption Privaatv&otilde;ti

@reltype AMT_CTR value=4 clid=CL_CFGCONTROLLER
@caption Summa kontroller

@reltype DESC_CTR value=5 clid=CL_CFGCONTROLLER
@caption Kirjelduse kontroller

@reltype CTR value=6 clid=CL_FORM_CONTROLLER
@caption Kontroller

@reltype RETURN_CTR value=7 clid=CL_CFGCONTROLLER
@caption Makse kinnituse kontroller

@reltype IMAP value=8 clid=CL_PROTO_IMAP
@caption Reproduktsioon

@reltype CCL_UPLOAD value=19 clid=CL_FILE
@caption Reproduktsioon

@reltype CURRENCY value=9 clid=CL_CURRENCY
@caption Valuuta
*/

class bank_payment extends class_base
{
	const AW_CLID = 1077;
	//olemasolevad pangad
	var $banks = array (
		"hansapank"		=> "Hansapank",
		"seb"			=> "SEB Eesti &Uuml;hispank",
		"nordeapank"		=> "Nordea Pank",
		"krediidipank"		=> "Krediidipank",
		"sampopank"		=> "Sampo Pank",
		"hansapank_lv"		=> "L&auml;ti Hansapank",
		"hansapank_lt"		=> "Leedu Hansapank",
		"credit_card"		=> "Kaardikeskus (krediitkaart)",
		"snoras"		=> "AB bankas SNORAS"
	);
	
	var $different_accounts = array(
		"hansapank", "seb", "krediidipank" , "sampopank" ,"hansapank_lv" , "hansapank_lt" ,"snoras","nordeapank"
	);
	
	//koikidele pankadele yhine info
	var $for_all_banks = array(
		"amount"	=> "Summa",
		"expl"		=> "Selgitus",
	);
	//igal pangal on vaja selliseid asju teada
	var $bank_props = array(
		"sender_id"	=> "Kaupmehe ID",
		"stamp"		=> "Arvenumber",
		"rec_name"	=> "Saaja nimi",
	);

	//erinevate pankade lingid
	var $bank_link = array(
		"hansapank"		=> "https://www.hanza.net/cgi-bin/hanza/pangalink.jsp",
		"seb"			=> "https://www.seb.ee/cgi-bin/unet3.sh/un3min.r",
		"sampopank"		=> "https://www2.sampopank.ee/ibank/pizza/pizza",
		//"sampopank"		=> "https://www.sampo.ee/cgi-bin/pizza",
		"krediidipank"		=> "https://i-pank.krediidipank.ee/teller/maksa",
		//"nordeapank"		=> "https://solo3.merita.fi/cgi-bin/SOLOPM01",
		"nordeapank"		=> "https://netbank.nordea.com/pnbepay/epay.jsp",
		"hansapank_lv"		=> "https://www.hanzanet.lv/banklink/",
		"hansapank_lt"		=> "https://lt.hanza.net/banklink/bl-lt/",
		"credit_card"		=> "https://pos.estcard.ee/webpos/servlet/iPAYServlet",
		"snoras" 		=> "https://ib.snoras.com/cgi/ib01/ib100.pl?service=PAY"
	);

	var $merchant_id = array(
		"EYP" => "seb",
		"HP" => "hansapank",
		"afb" => "credit_card",
		"SAMPOPANK" => "sampopank",
		"0002" => "nordeapank",
		"SNORLT22" => "snoras",
	);
	
	var $public_key_files = array(
		"seb" => "EYP_pub.pem",
		"hansapank" => "HP_pub.pem",
		"credit_card" => "credit_card.crt",
		"sampopank" => "SAMPOPANK_pub.pem",
		"hansapank_lv" => "HP_lv_pub.pem",
		"hansapank_lt" => "HP_lt_pub.pem",
		"snoras" => "SNORLT22_pub.pem",
	);
	
	var $default_banks = array(
		"hansapank",
		"seb",
		"sampopank",
		"krediidipank",
	);

	//monel pangal testkeskkond, et tore monikord seda kasutada proovimiseks
	var $test_link = array(
		"seb"	=> "https://www.seb.ee/cgi-bin/dv.sh/un3min.r",
		"credit_card"	=> "https://pos.estcard.ee/test-pos/servlet/iPAYServlet",
	);

	//test keskkonnas laheb yldjuhul miskeid testandmeid vaja
	var $test_priv_keys = array(
		"seb"	=> "seb_test_priv.pem",
	);

//edasi logi jaoks infi... et ei peaks liialt if lauseid tegema
	var $payer_name = array(
		"seb" => "VK_SND_NAME",
		"hansapank" => "VK_SND_NAME",
		"sampopank" => "VK_SND_NAME",
		"snoras" => "VK_SND_NAME",
		"credit_card" => "msgdata",
	);

	var $ref = array(
		"seb" => "VK_REF",
		"hansapank" => "VK_REF",
		"sampopank" => "VK_REF",
		"credit_card" => "ecuno",
		"nordeapank" => "SOLOPMT_RETURN_REF",
		"snoras" => "VK_REF",
	);
		
	var $cur = array(
		"seb" => "VK_CURR",
		"hansapank" => "VK_CURR",
		"sampopank" => "VK_CURR",
		"credit_card" => "cur",
		"nordeapank" => "SOLOPMT_CURR",
		"snoras" => "VK_CURR",
	);

	var $languages = array(
		"hansa" => array(
			"et" => "EST",
			"EST" => "EST",
			"" => "EST",
			"en" => "ENG",
			"ENG" => "ENG",
		),
		"snoras" => array(
			"lt" => "LIT",
			"LIT" => "LIT",
			"" => "ENG",
			"en" => "ENG",
			"ENG" => "ENG",
			"en" => "RUS",
			"ru" => "RUS",
		),
		"nordea" => array(
			"et" => 4,
			"EST" => 4,
			"en" => 3,
			"ENG" => 3,
			"" => 4,
			"fi" => 1,
		),
		"cc" => array(
			"et" => "et",
			"EST" => "et",
			"" => "et",
			"en" => "en",
			"ENG" => "en",
		),
	);

	/** 
		@attrib api=1

 	**/
	function bank_payment()
	{
		$this->init(array(
			"tpldir" => "common/bank_payment",
			"clid" => CL_BANK_PAYMENT
		));
		
	}

	/** 
		@attrib name=form_test_case nologin=1 is_public=1 all_args=1

 	**/
	function form_test_case($arr)
	{
		die('
			<form name="makse" id="makse" method="post" action="http://marko.dev.struktuur.ee/orb.aw?class=bank_payment&id=10580">
			<br>
			<input type="textbox" name="amount" value=3000000>
			<input type=submit value="maksa ilgelt pappi">
			</form>'
		);
	}
	
	/**
		@attrib name=bank_forms api=1 default=1 nologin=1 is_public=1 all_args=1
	@param id optional type=oid
		bank_payment object ID 
	@param amount optional type=int
		Amount to be paid. Max length=17
	@param units optional type=int
		if amount is not set, you give how many units, ... payment_id must be set then and payment objects prop default_unit_price also
	@param reference_nr optional type=int
		Reference number of payment order. Max length=19
	@param service optional type=int default=1002
		Number of service. Length=4
	@param sender_id optional type=string
		if no ID is set, can find a payment object by sender_id
	@param stamp optional type=string
		Query ID. Max length=20
	@param expl optional type=string
		Explanation of payment order. Max length=70
	@param return_url optional type=string
		URL to which response is sent in performing the transaction. Max length=60. 
	@param cancel_url optional type=string default=$return_url
		URL to which response is sent when the transaction is unsuccessful. Max length=60
	@param lang optional type=string default="EST"
		Preferred language of communication. Length=3
	@returns String/html - the bank payment site or correct form.

	@comment
		calculates the reference number and digital signature VK_MAC
		Returns the bank payment site or correct form.
	@example
		<form name="makse" id="makse" method="post" action="http://marko.dev.struktuur.ee/orb.aw?class=bank_payment&id=10580">
		<input type="textbox" name="amount" value=3000000>
		<input type=submit value="maksa ilgelt pappi">
		</form>
	**/
	function bank_forms($arr = array())
	{
		$data = $_GET+$_POST+$arr;
		if($arr["id"])
		{
			$data["id"] = $arr["id"];
		}
		$payment = $this->_get_payment_object($data);
		if(!is_object($payment))
		{
			return "";
		}
		
		if($payment->prop("template"))
		{
			$tpl = $payment->prop("template");
		}
		else
		{
			$tpl = "bank_forms.tpl";
		}
		
		if(!$this->read_template($tpl, $silent=1))
		{
			return "Makse templeit puudu";	
		}
		
		lc_site_load("bank_payment", &$this);
		
		//tegelt neid 2 j'rgmist pole vaja, sest iga panga puhul tulevad need va lisamised paratamatult uuesti
		//votab objekti seest moningad puuduvad vaartused
//		$data = $this->_add_object_data($payment,$data);
		//lisab puuduvad default vaartused
//		$data = $this->_add_default_data($data);
		//paneb panga crapi templatesse
		$this->_init_banks($payment,$data);

		$this->vars(array(
			"go_to_cc_url" => $this->mk_my_orb("redir_to_cc", array("id" => $arr["id"]))
		));

		return $this->parse();
	}

	/**
		@attrib name=redir_to_cc
		@param id required
	**/
	function redir_to_cc($arr)
	{
		$o = obj($arr["id"]);
		$amt = $o->prop("alias_amount");
		$desc = $o->prop("alias_desc");

		if ($this->can("view", $o->prop("alias_amount_ctr")))
		{
			$i = get_instance(CL_CFGCONTROLLER);
			$amt = $i->check_property($o->prop("alias_amount_ctr"), $o->id(), $arr);
		}
		if ($this->can("view", $o->prop("alias_desc_ctr")))
		{
			$i = get_instance(CL_CFGCONTROLLER);
			$desc = $i->check_property($o->prop("alias_desc_ctr"), $o->id(), $arr);
		}
		die($this->do_payment(array(
			"payment_id" => $arr["id"],
			"bank_id" => "credit_card",
			"amount" => $amt,
			"expl" => $desc,
			"lang" => aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC")
		)));
	}

	private function _get_explanation($arr)
	{
		extract($arr);
		if($payment->prop("expl") && (strlen($payment->prop("expl")) + strlen($data["expl"])  < 70))
		{
			$data["expl"] = $payment->prop("expl")." ".$data["expl"];
		}
		
		if($this->can("view" , $payment->prop("expl_controller")))
		{
			$pco = obj($payment->prop("expl_controller"));
			$pci = $pco->instance();
			$new_expl = $pci->eval_controller($pco->id(), $data);
			if($new_expl) $data["expl"] = $new_expl;
		}
		return trim(substr($data["expl"], 0, 70));
	}

	private function _add_object_data($payment,$data)
	{
		if($payment->prop("test"))
		{
			$data["test"] = 1;
		}
		$data["expl"] = $this->_get_explanation(array(
			"payment" => $payment,
			"data" => $data,
		));

		if($data["test"] &&  $this->test_link[$data["bank_id"]])
		{
			if($payment->prop("test_priv_key"))
			{
				$file_inst = new file();
				$file = $file_inst->get_file_by_id($payment->prop("test_priv_key"));
				$data["priv_key"] = $file["content"];
			}
			else
			{
				$fp = fopen($this->cfg["site_basedir"]."/pank/".$data["bank_id"]."_test_priv.pem", "r");
				$file_data = fread($fp, 8192);
				fclose($fp);
				if($file_data)
				{
					$data["priv_key"] = $file_data;
				}
			}
		}

		if(!$data["priv_key"] && $payment->prop("private_key"))
		{
			$file_inst = new file();
			$file = $file_inst->get_file_by_id($payment->prop("private_key"));
			$data["priv_key"] = $file["content"];
		}

		if(!$data["return_url"])
		{
			//$data["return_url"] = $payment->prop("return_url");
		}
		if(!$data["cancel_url"])
		{
			$c = $payment->prop("cancel_url");
			if(is_oid($c))
			{
				$c = aw_ini_get("baseurl")."/".$c;
			}
			$data["cancel_url"] = $c;
			$_SESSION["bank_payment"]["cancel"] = $c;
		}
		
		if(!$data["amount"] && $data["units"] && $payment->prop("default_unit_sum"))
		{
			$data["amount"] = $data["units"]*$payment->prop("default_unit_sum");
		}
		
		$payment_data = $payment->meta("bank");
		$data["sender_id"] = $payment_data[$data["bank_id"]]["sender_id"];
		$data["stamp"] =  substr(($data["reference_nr"].time()), 0, 20);
//		$data["stamp"] =  substr($payment_data[$data["bank_id"]]["stamp"], 0, 20);
		if(strlen($payment_data[$data["bank_id"]]["stamp"]) > 5 && $payment_data[$data["bank_id"]]["rec_name"])
		{
			$data["service"] = "1001";
			$data["acc"] = $payment_data[$data["bank_id"]]["stamp"];
			$data["name"] = $payment_data[$data["bank_id"]]["rec_name"];
		}
		
		if($data["units"])
		{
			$data["amount"] = $data["units"]*$payment->prop("default_unit_sum");
		}
		return $data;
	}
	
	private function _add_default_data($data)
	{
		return $data;
	}

	private function _init_banks($payment,$data)
	{
		$bank_data = $payment->meta("bank");
		foreach($this->banks as $bank => $name)
		{
			if(array_key_exists($bank , $bank_data) && $bank_data[$bank]["sender_id"])
			{
				$c = "";
				$bank_form = $this->do_payment(array(
					"form"		=> 1,
					"bank_id"	=> $bank,
					"service"	=> $data["service"],
					"lang" 		=> $data["lang"],
					"sender_id"	=> $bank_data[$bank]["sender_id"],
					"stamp"		=> $bank_data[$bank]["stamp"],
					"amount"	=> $data["amount"],
					"reference_nr"	=> $data["reference_nr"],
					"expl"		=> $data["expl"],
					"priv_key" 	=> $data["priv_key"],
					"cancel_url"	=> $data["cancel_url"],
					"return_url"	=> $data["return_url"],
					"payment_id" 	=> $payment->id(),
				));
				$link = $this->_get_link_url($bank, $payment->prop("test"));
				$this->vars(array(
					"data" => $bank_form,
					"link" => $link,
				));
				$c .= $this->parse($bank);
				$this->vars(array(
					$bank => $c,
				));
			}
		}
	}

	private function _get_link_url($bank, $test = 0)
	{
		$url = $this->bank_link[$bank];
		if($test)
		{
			if($this->test_link[$bank])
			{
				return $this->test_link[$bank];
			}
		}
		return $url;
	}

	private function _get_payment_object($arr)
	{
		extract($arr);
				
		if(is_oid($id) && $this->can("view" , $id))
		{
			$payment_object = obj($id);
		}
		else 
		{
			$ol = new object_list(array(
				"class_id" => CL_BANK_PAYMENT,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => $name,
			));
			if(sizeof($ol->arr() == 1))
			{
				$payment_object = reset($ol->arr());
			}
			else
			{
				$ol = new object_list(array(
					"class_id" => CL_BANK_PAYMENT,
					"lang_id" => array(),
					"site_id" => array(),
				));
				foreach($ol->arr() as $payment)
				{
					$meta = $payment->meta("bank");
					foreach($meta as $data)
					{
						if($data["sender_id"] == $sender_id)
						{
							$payment_object = $payment;
							break;
						}
					}
				}
			}
		}
		return $payment_object;
	}

	private function get_log_data($o)
	{
		classload("core/date/date_calc");
		$filter = $o->meta("search_data");
		$o->set_meta("search_data" , null);
		$myFile = $GLOBALS["site_dir"]."/bank_log.txt";
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);
		$log_array = explode("\n" , $theData);
		//arr($log_array);
		$log_data = array();
		$done = array();
		$from = date_edit::get_timestamp($_SESSION["bank_payment"]["find_date_start"]);
		$to = date_edit::get_timestamp($_SESSION["bank_payment"]["find_date_end"]);
		if(!($to > 100)) $to = time();
		if(!($from > 100)) $from = time() - 3600*24*31;
		$log_array = array_reverse($log_array);
		foreach($log_array as $log)
		{
			if(is_array(unserialize($log)))
			{
				$val = unserialize($log);
				if($val["VK_SND_ID"])
				{
					$bank_id = $this->merchant_id[$val["VK_SND_ID"]];
				}
				elseif($val["action"])
				{
					$bank_id = $this->merchant_id[$val["action"]];
				}
				else
				{
					$bank_id = $this->merchant_id[$val["SOLOPMT_RETURN_VERSION"]];
				}
				if($from > 1 && !($from == $to) && $from > $val["timestamp"])
				{
					continue;
				}
				if($to > 1 && !($from == $to) && $to < $val["timestamp"])
				{
					continue;
				}
				if($filter["find_name"] && !(substr_count(strtoupper($val[$this->payer_name[$bank_id]]) ,strtoupper($filter["find_name"]))))
				{
					continue;
				}
				if($filter["find_ref"] && !(substr_count($val[$this->ref[$bank_id]] , $filter["find_ref"])))
				{
					continue;
				}
				if(!array_key_exists("find_one" , $_SESSION["bank_payment"]) || ($filter["find_one"]))/*  && ($val["VK_SERVICE"] == 1101 || $val["Respcode"] == "000" || $val["SOLOPMT-RETURN-VERSION"])))*/
				{
					if(array_key_exists($val[$this->ref[$bank_id]] ,  $done)) continue;
					$done[$val[$this->ref[$bank_id]]] = $val[$this->ref[$bank_id]];
				}
/*				if(aw_global_get("uid") == "markop"){arr($val);
 					$_SESSION["bank_return"]["data"] = $val;
 					arr($val["good"] = $this->check_response($val));
 				}*/
				if($val["timestamp"])
				{
					$log_data[$val["timestamp"]]["payer"] = $val["VK_SND_NAME"];
					$log_data[$val["timestamp"]]["ref"] = $val[$this->ref[$bank_id]];
					$log_data[$val["timestamp"]]["msg"] = $val["VK_MSG"];
					$log_data[$val["timestamp"]]["ip"] = $val["ip"];
					$log_data[$val["timestamp"]]["sum"] = $val["VK_AMOUNT"];
					$log_data[$val["timestamp"]]["bank"] = $bank_id;
					$log_data[$val["timestamp"]]["acc"] = $val["VK_REC_ACC"];
					if($val["eamount"])
					{
						$log_data[$val["timestamp"]]["sum"] = $val["eamount"]/100;
					}
					if($val["ecuno"])
					{
						$log_data[$val["timestamp"]]["ref"] = $val["ecuno"];
						$log_data[$val["timestamp"]]["msg"] = substr($val["ecuno"], 0, -1);
					}
					if($val["msgdata"])
					{
						$log_data[$val["timestamp"]]["payer"] = $val["msgdata"];
					}
					if($val["VK_SERVICE"] == 1101 || $val["respcode"] == "000" || $val["SOLOPMT_RETURN_PAID"])
					{
						$log_data[$val["timestamp"]]["ok"] = 1;
					}
					else
					{
						$log_data[$val["timestamp"]]["ok"] = 0;
					}

				//ajutiselt et saaks makse info kohta p2rimise lingi
					if($bank_id == "nordeapank" && $val["good"])
					{
						$pc_url = $this->mk_my_orb("get_nordea_stats", $val + array("id" => $o->id()));
						$log_data[$val["timestamp"]]["payer"] = html::href(array(
							"url" => $pc_url,
							"caption" => t("(Makse info)"),
						));
					}	

					$log_data[$val["timestamp"]]["good"] = $val["good"];
					if($val["actiontext"])
					{
						$log_data[$val["timestamp"]]["msg"].= " (".$val["actiontext"].")";
					}
					
					//objektile klikitav viitenumber
					$id = substr($log_data[$val["timestamp"]]["ref"], 0, -1);
					if(!$o->prop("not_clickable_ref") && is_oid($id) && $this->can("view" , $id))
					{
						$log_data[$val["timestamp"]]["ref"] = html::obj_change_url($id , $log_data[$val["timestamp"]]["ref"]);
					}
				}
				else
				{
					$log_data[] = array("payer" => $val["VK_SND_NAME"] , "ref" => $val["VK_REF"],"msg" => $val["VK_MSG"], "sum" => $val["VK_AMOUNT"]);
				}
			}
			else
			{
				//$log_data[] = array("msg" => $log);
			}
		}
		krsort($log_data);
		return $log_data;
	}

	private function get_log(&$arr)
	{
		$log_data = $this->get_log_data($arr["obj_inst"]);
		classload("vcl/table");
		$t = new vcl_table;
		$this->init_log($t);
		$sum = 0;
		foreach($log_data as $key => $val)
		{
			$sum = $sum + $val["sum"];
			$t->define_data(array(
				"sum" => $val["sum"],
				"bank" => $this->banks[$val["bank"]],
				"time" => date("d.m.Y H:i" ,$key),
				"ref" => $val["ref"],
				"expl" => $val["msg"],
				"payer" => $val["payer"],
				"ok" =>  $val["ok"] ? t("&otilde;nnestus") : t("eba&otilde;nnestus"),
				"good" => $val["good"] ? t("ok") : t(""),
				"acc" => $val["acc"],
				"ip" => $val["ip"],
			));
		}
		//see summa toimib hetkel vaid eeldusel , et valuuta on igal pool sama
		$t->define_data(array(
			"sum" => $sum,
			"payer" => t("Kokku:"),
		));
		
		return $this->get_fs_string().$t->draw();
	}

	private function get_fs_string()
	{
		$fs = filesize($GLOBALS["site_dir"]."/bank_log.txt");
		if($fs)
		{
			$fs_string = t("Logifaili suurus on hetkel") . " " . $fs . " " .t("baiti") . "\n<br>" ;
		}
		if($fs = FALSE)
		{
			$fs_string = t("Mingine jama on failiga"). " " . $GLOBALS["site_dir"]."/bank_log.txt" . " " . t("Kas pole &otilde;igusi, v&otilde; faili");
		}
		return $fs_string; 
	}

	function get_property($arr)
	{
		$search_data = $arr["obj_inst"]->meta("search_data");
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "log":
				$prop["value"] = $this->get_log($arr);
				break;

			case "template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "common/bank_payment"
				));
				unset($prop["options"][""]);
				if(!sizeof($prop["options"]))
				{
					$prop["value"] = t("Hetkel pole kataloogis"). " ".$this->site_template_dir." ".t("&uuml;htegi temleidi faili");
					$prop["type"] =  "text";
				}
				break;
			case "find_one":
				$prop["value"] = 1;
				break;

			case "find_date_start":
				if(isset($_SESSION["bank_payment"]["find_date_start"]))
				{
					$prop["value"] = $_SESSION["bank_payment"]["find_date_start"];
				}
				else
				{
					$prop["value"] = array(
						"day" => date("d" , (time()-(31 * 24 * 3600))),
						"month" => date("m" , (time()-(31 * 24 * 3600))),
						"year" => date("Y" , (time()-(31 * 24 * 3600))),
					);
				}
				break;
			case "find_date_end":
				if(isset($_SESSION["bank_payment"]["find_date_end"]))
				{
					$prop["value"] = $_SESSION["bank_payment"]["find_date_end"];
				}
				else
				{
					$prop["value"] = array(
						"day" => date("d" , time()) + 1,
						"month" => date("m" , time()),
						"year" => date("Y" , time()),
					);
				}
				break;

			case "find_name":
			case "find_ref":
				if($search_data[$prop["name"]])
				{
					$prop["value"] = $search_data[$prop["name"]];
				}
				break;
			case "test_priv_key":
				if(!$arr["obj_inst"]->prop("test"))
				{
					return PROP_IGNORE;
				}
				break;
			case "bank_test":
				break;
			case "doc":
				$prop["value"] = $this->_get_documentation();
				break;
		};
		return $retval;
	}

	private function init_log(&$t)
	{
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "payer",
			"caption" => t("Maksja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "ref",
			"caption" => t("Viitenumber"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "expl",
			"caption" => t("Seletus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "bank",
			"caption" => t("Pank"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "acc",
			"caption" => t("Arve"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "ip",
			"caption" => t("IP"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "ok",
			"caption" => t("&Otilde;nnestus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "good",
			"caption" => t("SK"),
			"align" => "center"
		));
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
			case "bank":
				$this->submit_meta($arr);
				break;
			case "bank_test":
				$arr["request"]["meta"][$arr["request"]["meta"]["new_bank"]] = $arr["request"]["meta"]["new"];
				$this->submit_meta($arr);
				break;
			case "log":
				$_SESSION["bank_payment"]["find_one"] = $arr["request"]["find_one"];
				$_SESSION["bank_payment"]["find_date_start"] = $arr["request"]["find_date_start"];
				$_SESSION["bank_payment"]["find_date_end"] = $arr["request"]["find_date_end"];
				$arr["request"]["rawdata"] = null;
				$arr["obj_inst"]->set_meta("search_data" , $arr["request"]);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
	
	function submit_meta($arr = array())
	{
		$meta = $arr["request"]["meta"];
		if (is_array($meta))
		{
			$arr["obj_inst"]->set_meta($arr["prop"]["name"], $meta);
			$arr["obj_inst"]->save();
		};
	}
	
	function _get_bank($arr)
	{
	//	$props = $this->callback_bank($arr);
		$meta = $arr["obj_inst"]->meta("bank");
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$t->set_caption("Oluline info pangamaksete teostamiseks (\"Kaupmehe ID\" n&otilde;utav, teised juhuks kui raha peaks laekuma kindlale arvele)");
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Pank"),
		));
		foreach($this->bank_props as $prop => $caption)
		{
			$t->define_field(array(
				"name" => $prop,
				"caption" => $caption,
			));
		}
			
		foreach($this->banks as $key => $val)
		{
			$data = array();
			$data["use"] = array(
				"name" => "meta[".$key."][use]",
				"type" => "chechbox" ,
				"ch_value" => 1 ,
				"value" => $meta["key"],
				"caption" => $val,
			);
			$data["name"] = $val;
			foreach($this->bank_props as $prop => $caption)
			{
				if(!in_array($key, $this->different_accounts) && ($prop == "stamp" || $prop == "rec_name") )
				{
					continue;
				}
				$data[$prop] = html::textbox(array(
					"name" => "meta[".$key."][".$prop."]",
//					"type" => "textbox",
					"value" => $meta[$key][$prop],
					"size" => 35,
//					"caption" => $caption
				));
			}
			$t->define_data($data);
		}
	}
	
	/**
	@attrib api=1 params=name
	@param bank_id required type=string
		bank id. possible choices: "seb", "hansapank" , "sampopank", "nordeapank" , "krediidipank" , credit_card
	@param amount optional type=int
		Amount to be paid. Max length=17
	@param units optional type=int
		if amount is not set, you give how many units, ... payment_id must be set then and payment objects prop default_unit_price also
	@param reference_nr optional type=int
		Reference number of payment order. Max length=19
	@param payment_id optional type=oid
		if set, takes sender_id,explanation and ...  data from bank payment object	
	@param service optional type=int default=1002
		Number of service. Length=4
	@param version optional type=int default=008
		Encryption algorithm used. Length=3	
	@param sender_id optional type=string
		ID of compiler of query (merchant's ID). Max length=10
	@param stamp optional type=string
		Query ID. Max length=20
	@param currency optional type=string default="EEK"
		Name of currency: EEK/DEM/FIM etc. Length=3
	@param expl optional type=string
		Explanation of payment order. Max length=70
	@param return_url optional type=string default=aw_ini_get("baseurl")."/automatweb/bank_return.aw"
		URL to which response is sent in performing the transaction. Max length=60. If it is not set, you must set $_SESSION["bank_payment"]["url"].
	@param cancel_url optional type=string default=$return_url
		URL to which response is sent when the transaction is unsuccessful. Max length=60
	@param lang optional type=string default="EST"
		Preferred language of communication. Length=3
	@param priv_key optional type=string
		Query compiler's private key (merchant's private key)
	@param form optional type=int
		If form is set, function returns html form, else returns to bank site.
	@param test optional type=int
		If test is set, the function uses the bank test site if it exists
	@param cntr optional type=string
		bank country code
		if set and exists bank id named "bank_id + _ + cntr" , then uses it
		
	@returns bank web page, or string/html form
	
	@comment
		Calculates the reference number and digital signature VK_MAC
		Directs to the bank payment site or returns correct form.
		amount, reference_nr must be set.
		payment_id or (expl , sender_id) must be set
		Have to set $_SESSION["bank_payment"]["url"] if you want to get response from the bank to unusual url (aw_ini_get("baseurl")."/automatweb/bank_return.aw" is default), return_url is only for url with no parameters.
	
	@example
		$bank_payment = get_instance(CL_BANK_PAYMENT);
		$oc = obj($o->parent())
		return $bank_payment->do_payment(array(
			"payment_id" 	=> $oc->prop("bank_payment")
			"bank_id"	=> "seb",
			"amount"	=> 500,
			"reference_nr"	=> $o->id(),
			"expl"		=> $o->name(),
		));

	**/	
	function do_payment($arr)
	{
		if(is_oid($arr["payment_id"]))
		{
			$payment = obj($arr["payment_id"]);
		}

		if(!$arr["reference_nr"])
		{
			$arr["reference_nr"] = time();
			if($payment && !$_SESSION["bank_payment"]["url"])
			{
				//kui pole viitenumber miski id , ega tagasip88rdumise urli m22ratud, siis v6tab selle ise objekti juurest
				$_SESSION["bank_payment"]["url"] = $payment->prop("bank_return_url");
			}
		}

		//selle nomeduse pidi siia ette panema, sest hiljem bank_id'd muutes peaks muidu siia funktsiooni tagasi poorama
		if(is_oid($arr["payment_id"]))
		{
			$bank_data = $payment->meta("bank");

			if($arr["cntr"])
			{
				if($bank_data[$arr["bank_id"]."_".$arr["cntr"]])
				{
					$arr["bank_id"] = $arr["bank_id"]."_".$arr["cntr"];
				}
			}
			//siia paneb krapi mida peaks makselt tagasi tulles kuskilt katte saama... parim on ikka see objekt mida maksma minnakse
			if(is_oid($arr["reference_nr"]) && $this->can("view" , $arr["reference_nr"]))
			{
				$ref_object = obj($arr["reference_nr"]);
				if($arr["cntr"])
				{
					$ref_object->set_meta("bank_cntr" , $arr["cntr"]);
				}
				$ref_object->set_meta("bank_payment_id" , $arr["payment_id"]);//hiljem saaks logis kasutada
				$ref_object->set_meta("bank_is_test" , $payment->meta("test"));//selle jargi voiks testimise sertifikaadikontrolli toole panna
				aw_disable_acl();
				$ref_object->save();
				aw_restore_acl();
			}
/*			if($bank_data[$arr["bank_id"]."_".$_SESSION["ct_lang_lc"]]["sender_id"])
			{
				$arr["bank_id"] = $arr["bank_id"]."_".$_SESSION["ct_lang_lc"];
			}
*/		
		}
		
		switch($arr["bank_id"]) {
			case "seb":
				$arr = $this->check_args($arr);
				return $this->seb($arr);
				break;
			case "hansapank_lv":
				$arr = $this->check_args($arr);
				return $this->hansa_lv($arr);
				break;
			case "hansapank_lt":
				$arr = $this->check_args($arr);
				return $this->hansa_lt($arr);
				break;
			case "hansapank":
				$arr = $this->check_args($arr);
				return $this->hansa($arr);
				break;
			case "sampopank":
				$arr = $this->check_args($arr);		
				return $this->sampo($arr);
				break;
			case "nordeapank":
				$arr = $this->check_nordea_args($arr);
				return $this->nordea($arr);
				break;
			case "krediidipank":
				$arr = $this->check_args($arr);
				return $this->krediidi($arr);
				break;
			case "credit_card":
				$arr = $this->check_cc_args($arr);
				return $this->credit_card($arr);
				break;
			case "snoras":
				$arr = $this->check_snoras_args($arr);
				return $this->snoras($arr);
				break;
		}
	}
	
	
	private function check_args($arr)
	{
		if(is_oid($arr["payment_id"]))
		{
			$payment = obj($arr["payment_id"]);
			$arr = $this->_add_object_data($payment , $arr);
			$payment_data = $payment->meta("bank");
		}
		if(!$arr["service"]) $arr["service"] = "1002";
		if(!$arr["version"]) $arr["version"] = "008";
		if(!$arr["curr"]) $arr["curr"] = "EEK";
		$arr["expl"] =  trim($arr["expl"]);
		if(array_key_exists($arr["lang"] , $this->languages["hansa"]))
		{
			$arr["lang"] = $this->languages["hansa"][$arr["lang"]];
		}
		else
		{
			$arr["lang"] = "ENG";
		}
/*	
		if($arr["lang"] == "et")
		{
			$arr["lang"] = "EST";
		}
		if($arr["lang"] == "en")
		{
			$arr["lang"] = "ENG";
		}
		if(!$arr["lang"]) $arr["lang"] = "EST";*/
		if(!$arr["stamp"]) $arr["stamp"] = "666";
		if(!$arr["cancel_url"]) $arr["cancel_url"] = aw_ini_get("baseurl")."/automatweb/bank_return.aw";
		if(!$arr["return_url"]) $arr["return_url"] = aw_ini_get("baseurl")."/automatweb/bank_return.aw";
		if(!$arr["priv_key"])
		{
			if($arr["test"] && $this->test_priv_keys[$arr["bank_id"]])
			{
				$file = $this->test_priv_keys[$arr["bank_id"]];
			}
			else
			{
				$file = "privkey.pem";
			}
			$fp = fopen($this->cfg["site_basedir"]."/pank/".$file, "r");
			$arr["priv_key"] = fread($fp, 8192);
			fclose($fp);
		}
		$arr["reference_nr"].= (string)$this->viitenr_kontroll_731($arr["reference_nr"]);
		return($arr);
	}
		
	//if form = 1, returns hrml input tags in form.
	private function submit_bank_info($args)
	{
		extract($args);
		$return = "";
		if(!$form) $return.= '<form name="postform" id="postform" method="post" action='.$link.'>
		';
		foreach($params as $key => $val)
		{
			$return.= '<input type="hidden" name='.$key.' value="'.(string)$val.'">
			';
		};
//		if(aw_global_get("uid") == "struktuur"){ arr($return); die();}
		if($form) return $return;
		print $return.'<p class="text">'.t("Kui suunamist mingil p&otilde;hjusel ei toimu, palun vajutage").'<a href="#" onClick="document.postform.submit();"> '.t("siia").'</a></p>
		</form>
		<script type="text/javascript">
			function pform() {
				document.postform.submit();
			}
			function WindowOnload(f){
				var prev=window.onload;
				window.onload=function(){
					if(prev)prev();
					f();
				}
			}
			WindowOnload(pform);
		</script>';
		die();	
	}
	
	private function snoras($args)
	{
		extract($args);
		$VK_message = sprintf("%03d",strlen($service)).$service;
		$VK_message.= sprintf("%03d",strlen($version)).$version;
		$VK_message.= sprintf("%03d",strlen($sender_id)).$sender_id;
//		$VK_message.= sprintf("%03d",strlen($stamp)).$stamp;
		$VK_message.= sprintf("%03d",strlen($amount)).$amount;
		$VK_message.= sprintf("%03d",strlen($curr)).$curr;
//		$VK_message.= sprintf("%03d",strlen($reference_nr)).$reference_nr;
		$VK_message.= sprintf("%03d",strlen($expl)).$expl;
		$VK_signature = "";
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);

		$link = $this->bank_link["snoras"];
		$params = array(
			"VK_SERVICE"	=> $service,	//"1001"
			"VK_VERSION"	=> $version,	//"008"
			"VK_SND_ID"	=> $sender_id,
//			"VK_STAMP"	=> $stamp,	//row["arvenr"]
			"VK_AMOUNT"	=> $amount,	//$row["summa"];
			"VK_CURR"	=> $curr,	//"EEK"
			"VK_REF"	=> $reference_nr,
			"VK_MSG"	=> $expl,	//"Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
			"VK_MAC" 	=> $VK_MAC,
			"VK_RETURN"	=> $return_url, //$this->burl."/tellimine/makse/tanud/";//60	URL, kuhu vastatakse edukal tehingu sooritamisel
//			"VK_CANCEL"	=> $cancel_url,	//this->burl."/tellimine/makse/";//60	URL, kuhu vastatakse ebaonnestunud tehingu puhul
			"VK_LANG" 	=> $lang,	//"EST"
		);
		$optional = array(
			"VK_ACC" => "acc",
			"VK_NAME" => "name",
			"VK_STAMP" => "stamp",
		);

		foreach($optional as $key => $val)
		{
			if($$val)
			{
				$params[$key] = $$val;
			}
		}
		$optional = array("VK_ACC" => "acc");
		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	//	return $http->post_request($link, $handler, $params, $port = 80);
	}


	private function hansa($args) 
	{
		extract($args);
		$VK_message = sprintf("%03d",strlen($service)).$service;
		$VK_message.= sprintf("%03d",strlen($version)).$version;
		$VK_message.= sprintf("%03d",strlen($sender_id)).$sender_id;
		$VK_message.= sprintf("%03d",strlen($stamp)).$stamp;
		$VK_message.= sprintf("%03d",strlen($amount)).$amount;
		$VK_message.= sprintf("%03d",strlen($curr)).$curr;
		if($service == 1001)
		{
			$VK_message.= sprintf("%03d",strlen($acc)).$acc;
			$VK_message.= sprintf("%03d",strlen($name)).$name;
		}
		$VK_message.= sprintf("%03d",strlen($reference_nr)).$reference_nr;
		$VK_message.= sprintf("%03d",strlen($expl)).$expl;
		$VK_signature = "";
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);

		$http = get_instance("protocols/file/http");
		$link = "https://www.hanza.net/cgi-bin/hanza/pangalink.jsp";
		$handler = "https://www.hanza.net/cgi-bin/hanza/pangalink.jsp";
		$params = array(
			"VK_SERVICE"	=> $service,	//"1002"
			"VK_VERSION"	=> $version,	//"008"
			"VK_SND_ID"	=> $sender_id,	//"EXPRPOST"
			"VK_STAMP"	=> $stamp,	//row["arvenr"]
			"VK_AMOUNT"	=> $amount,	//$row["summa"];
			"VK_CURR"	=> $curr,	//"EEK"
			"VK_REF"	=> $reference_nr,
			"VK_MSG"	=> $expl,	//"Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
			"VK_MAC" 	=> $VK_MAC,
			"VK_RETURN"	=> $return_url, //$this->burl."/tellimine/makse/tanud/";//60	URL, kuhu vastatakse edukal tehingu sooritamisel
			"VK_CANCEL"	=> $cancel_url,	//this->burl."/tellimine/makse/";//60	URL, kuhu vastatakse ebaonnestunud tehingu puhul
			"VK_LANG" 	=> $lang,	//"EST"
		);
		if($service == 1001)
		{
			$params["VK_ACC"] = $acc;
			$params["VK_NAME"] = $name;
		
		}
		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	//	return $http->post_request($link, $handler, $params, $port = 80);
	}

	private function hansa_lv($args) 
	{
		$args["lang"] = "ENG";
		extract($args);
		$VK_message = sprintf("%03d",strlen($service)).$service;
		$VK_message.= sprintf("%03d",strlen($version)).$version;
		$VK_message.= sprintf("%03d",strlen($sender_id)).$sender_id;
		$VK_message.= sprintf("%03d",strlen($stamp)).$stamp;
		$VK_message.= sprintf("%03d",strlen($amount)).$amount;
		$VK_message.= sprintf("%03d",strlen($curr)).$curr;
		if($service == 1001)
		{
			$VK_message.= sprintf("%03d",strlen($acc)).$acc;
			$VK_message.= sprintf("%03d",strlen($name)).$name;
		}
		$VK_message.= sprintf("%03d",strlen($reference_nr)).$reference_nr;
		$VK_message.= sprintf("%03d",strlen($expl)).$expl;
		$VK_signature = "";
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);

		$http = get_instance("protocols/file/http");
		$link = $this->bank_link["hansapank_lv"];
		$handler = $link;
		$params = array(
			"VK_SERVICE"	=> $service,	//"1002"
			"VK_VERSION"	=> $version,	//"008"
			"VK_SND_ID"	=> $sender_id,	//"EXPRPOST"
			"VK_STAMP"	=> $stamp,	//row["arvenr"]
			"VK_AMOUNT"	=> $amount,	//$row["summa"];
			"VK_CURR"	=> $curr,	//"EEK"
			"VK_REF"	=> $reference_nr,
			"VK_MSG"	=> $expl,	//"Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
			"VK_MAC" 	=> $VK_MAC,
			"VK_RETURN"	=> $return_url, //$this->burl."/tellimine/makse/tanud/";//60	URL, kuhu vastatakse edukal tehingu sooritamisel
			"VK_CANCEL"	=> $cancel_url,	//this->burl."/tellimine/makse/";//60	URL, kuhu vastatakse ebaonnestunud tehingu puhul
			"VK_LANG" 	=> $lang,	//"EST"
		);
		if($service == 1001)
		{
			$params["VK_ACC"] = $acc;
			$params["VK_NAME"] = $name;
		
		}
		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	//	return $http->post_request($link, $handler, $params, $port = 80);
		
	}

	private function hansa_lt($args) 
	{
		$args["lang"] = "ENG";
		extract($args);
		$VK_message = sprintf("%03d",strlen($service)).$service;
		$VK_message.= sprintf("%03d",strlen($version)).$version;
		$VK_message.= sprintf("%03d",strlen($sender_id)).$sender_id;
		$VK_message.= sprintf("%03d",strlen($stamp)).$stamp;
		$VK_message.= sprintf("%03d",strlen($amount)).$amount;
		$VK_message.= sprintf("%03d",strlen($curr)).$curr;
		if($service == 1001)
		{
			$VK_message.= sprintf("%03d",strlen($acc)).$acc;
			$VK_message.= sprintf("%03d",strlen($name)).$name;
		}
		$VK_message.= sprintf("%03d",strlen($reference_nr)).$reference_nr;
		$VK_message.= sprintf("%03d",strlen($expl)).$expl;
		$VK_signature = "";
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);

		$http = get_instance("protocols/file/http");
		$link = $this->bank_link["hansapank_lt"];
		$handler = $link;
		$params = array(
			"VK_SERVICE"	=> $service,	//"1002"
			"VK_VERSION"	=> $version,	//"008"
			"VK_SND_ID"	=> $sender_id,	//"EXPRPOST"
			"VK_STAMP"	=> $stamp,	//row["arvenr"]
			"VK_AMOUNT"	=> $amount,	//$row["summa"];
			"VK_CURR"	=> $curr,	//"EEK"
			"VK_REF"	=> $reference_nr,
			"VK_MSG"	=> $expl,	//"Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
			"VK_MAC" 	=> $VK_MAC,
			"VK_RETURN"	=> $return_url, //$this->burl."/tellimine/makse/tanud/";//60	URL, kuhu vastatakse edukal tehingu sooritamisel
			"VK_CANCEL"	=> $cancel_url,	//this->burl."/tellimine/makse/";//60	URL, kuhu vastatakse ebaonnestunud tehingu puhul
			"VK_LANG" 	=> $lang,	//"EST"
		);
		if($service == 1001)
		{
			$params["VK_ACC"] = $acc;
			$params["VK_NAME"] = $name;
		
		}
		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	//	return $http->post_request($link, $handler, $params, $port = 80);
	}

	private function seb($args)
	{
		extract($args);
		$link = "https://www.seb.ee/cgi-bin/unet3.sh/un3min.r";
		if($test)
		{
			 $link = "https://www.seb.ee/cgi-bin/dv.sh/un3min.r";
			$sender_id = "testvpos";
		}

		$VK_message = sprintf("%03d",strlen($service)).$service;
		$VK_message.= sprintf("%03d",strlen($version)).$version;
		$VK_message.= sprintf("%03d",strlen($sender_id)).$sender_id;
		$VK_message.= sprintf("%03d",strlen($stamp)).$stamp;
		$VK_message.= sprintf("%03d",strlen($amount)).$amount;
		$VK_message.= sprintf("%03d",strlen($curr)).$curr;
		if($service == 1001)
		{
			$VK_message.= sprintf("%03d",strlen($acc)).$acc;
			$VK_message.= sprintf("%03d",strlen($name)).$name;
		}
		$VK_message.= sprintf("%03d",strlen($reference_nr)).$reference_nr;
		$VK_message.= sprintf("%03d",strlen($expl)).$expl;
		$VK_signature = "";
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode($VK_signature);
		$http = get_instance("protocols/file/http");
		$handler = "https://www.seb.ee/cgi-bin/unet3.sh/un3min.r";
		$params = array(
			"VK_SERVICE"	=> $service,	//"1002"
			"VK_VERSION"	=> $version,	//"008"
			"VK_SND_ID"	=> $sender_id,	//"EXPRPOST" //	15	Paringu koostaja ID (Kaupluse ID)
			"VK_STAMP"	=> $stamp,	//row["arvenr"]
			"VK_AMOUNT"	=> $amount,	//$row["summa"];
			"VK_CURR"	=> $curr,	//"EEK"
			"VK_REF"	=> $reference_nr,
			"VK_MSG"	=> $expl,	//"Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
			"VK_MAC" 	=> $VK_MAC,
			"VK_RETURN"	=> $return_url, //$this->burl."/tellimine/makse/tanud/";	//	60	URL, kuhu vastatakse edukal tehingu sooritamisel
			"VK_CANCEL"	=> $cancel_url,	//this->burl."/tellimine/makse/";	//	60	URL, kuhu vastatakse ebaonnestunud tehingu puhul
			"VK_LANG" 	=> $lang,	//"EST"
		);
		if($service == 1001)
		{
			$params["VK_ACC"] = $acc;
			$params["VK_NAME"] = $name;
		
		}
		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	//	return $http->post_request($link, $handler, $params, $port = 80);
	}

	private function sampo($args)
	{
		extract($args);
		$VK_message = sprintf("%03d",strlen($service)).$service;
		$VK_message.= sprintf("%03d",strlen($version)).$version;
		$VK_message.= sprintf("%03d",strlen($sender_id)).$sender_id;
		$VK_message.= sprintf("%03d",strlen($stamp)).$stamp;
		$VK_message.= sprintf("%03d",strlen($amount)).$amount;
		$VK_message.= sprintf("%03d",strlen($curr)).$curr;
		if($service == 1001)
		{
			$VK_message.= sprintf("%03d",strlen($acc)).$acc;
			$VK_message.= sprintf("%03d",strlen($name)).$name;
		}
		$VK_message.= sprintf("%03d",strlen($reference_nr)).$reference_nr;
		$VK_message.= sprintf("%03d",strlen($expl)).$expl;
		$VK_signature = "";
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);

		$http = get_instance("protocols/file/http");
		$link = $handler = $this->bank_link["sampopank"];//"https://www.sampo.ee/cgi-bin/pizza";
	//	$handler = "https://www.sampo.ee/cgi-bin/pizza";
		$params = array(
			"VK_SERVICE"	=> $service,	//"1002"
			"VK_VERSION"	=> $version,	//"008"
			"VK_SND_ID"	=> $sender_id,	//"EXPRPOST" //	15	Paringu koostaja ID (Kaupluse ID)
			"VK_STAMP"	=> $stamp,	//row["arvenr"]
			"VK_AMOUNT"	=> $amount,	//$row["summa"];
			"VK_CURR"	=> $curr,	//"EEK"
			"VK_REF"	=> $reference_nr,
			"VK_MSG"	=> $expl,	//"Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
			"VK_MAC" 	=> $VK_MAC,
			"VK_RETURN"	=> $return_url, //$this->burl."/tellimine/makse/tanud/";	//	60	URL, kuhu vastatakse edukal tehingu sooritamisel
			"VK_CANCEL"	=> $cancel_url,	//this->burl."/tellimine/makse/";	//	60	URL, kuhu vastatakse ebaonnestunud tehingu puhul
			"VK_LANG" 	=> $lang,	//"EST"
		);
		if($service == 1001)
		{
			$params["VK_ACC"] = $acc;
			$params["VK_NAME"] = $name;
		
		}
		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	//	return $http->post_request($link, $handler, $params, $port = 80);
	}

	private function krediidi($args)
	{
		extract($args);
		$VK_message = sprintf("%03d",strlen($service)).$service;
		$VK_message.= sprintf("%03d",strlen($version)).$version;
		$VK_message.= sprintf("%03d",strlen($sender_id)).$sender_id;
		$VK_message.= sprintf("%03d",strlen($stamp)).$stamp;
		$VK_message.= sprintf("%03d",strlen($amount)).$amount;
		$VK_message.= sprintf("%03d",strlen($curr)).$curr;
		if($service == 1001)
		{
			$VK_message.= sprintf("%03d",strlen($acc)).$acc;
			$VK_message.= sprintf("%03d",strlen($name)).$name;
		}
		$VK_message.= sprintf("%03d",strlen($reference_nr)).$reference_nr;
		$VK_message.= sprintf("%03d",strlen($expl)).$expl;
		$VK_signature = "";
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($VK_message, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = base64_encode( $VK_signature);

		$http = get_instance("protocols/file/http");
		$link = "https://i-pank.krediidipank.ee/teller/maksa";
		$handler = "https://i-pank.krediidipank.ee/teller/maksa";
		$params = array(
			"VK_SERVICE"	=> $service,	//"1002"
			"VK_VERSION"	=> $version,	//"008"
			"VK_SND_ID"	=> $sender_id,	//"EXPRPOST" //	15	Paringu koostaja ID (Kaupluse ID)
			"VK_STAMP"	=> $stamp,	//row["arvenr"]
			"VK_AMOUNT"	=> $amount,	//$row["summa"];
			"VK_CURR"	=> $curr,	//"EEK"
			"VK_REF"	=> $reference_nr,
			"VK_MSG"	=> $expl,	//"Ajakirjade tellimus. Arve nr. ".$row["arvenr"];
			"VK_MAC" 	=> $VK_MAC,
			"VK_RETURN"	=> $return_url, //$this->burl."/tellimine/makse/tanud/";	//	60	URL, kuhu vastatakse edukal tehingu sooritamisel
			"VK_CANCEL"	=> $cancel_url,	//this->burl."/tellimine/makse/";	//	60	URL, kuhu vastatakse ebaonnestunud tehingu puhul
			"VK_LANG" 	=> $lang,	//"EST"
		);
		if($service == 1001)
		{
			$params["VK_ACC"] = $acc;
			$params["VK_NAME"] = $name;
		
		}
		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	//	return $http->post_request($link, $handler, $params, $port = 80);
	}	

	function check_nordea_args($arr)
	{
		if(is_oid($arr["payment_id"]))
		{
			$payment = obj($arr["payment_id"]);
			$arr = $this->_add_object_data($payment , $arr);
			$arr["priv_key"] = $payment->prop("nordea_private_key");
		}
		if(!$arr["priv_key"])
		{
			$fp = fopen($this->cfg["site_basedir"]."/pank/nordea.mac", "r");
			$arr["priv_key"] = fread($fp, 8192);
			fclose($fp);
		}

		if(!$arr["service"]) $arr["service"] = "0002";
		if(!$arr["version"]) $arr["version"] = "0001";
		if(!$arr["curr"]) $arr["curr"] = "EEK";
		if(!$arr["confirm"]) $arr["confirm"] = "YES";
		if(!$arr["acc"]) $arr["acc"] = "";
		if(!$arr["name"]) $arr["name"] = "";
		if(!$arr["recieve_id"]) $arr["recieve_id"] = "10354213";
		if(!$arr["date"]) $arr["date"] = 'EXPRESS';
		if(!$arr["cancel_url"]) $arr["cancel_url"] = aw_ini_get("baseurl")."/automatweb/bank_return.aw";
//		if(!substr_count("http" , $arr["cancel_url"]))$arr["cancel_url"] = aw_ini_get("baseurl").$arr["cancel_url"];
		if(!$arr["return_url"]) $arr["return_url"] = aw_ini_get("baseurl")."/automatweb/bank_return.aw";
		$arr["reference_nr"].= (string)$this->viitenr_kontroll_731($arr["reference_nr"]);
		if(array_key_exists($arr["lang"] , $this->languages["nordea"]))
		{
			$arr["lang"] = $this->languages["nordea"][$arr["lang"]];
		}
		else
		{
			$arr["lang"] = "3";
		}

	/*	if($arr["lang"] == "et" || $arr["lang"] == "EST")
		{
			$arr["lang"] = 4;
		}
		if($arr["lang"] == "en" || $arr["lang"] == "ENG")
		{
			$arr["lang"] = 3;
		}
		if(!($arr["lang"] > 0)) $arr["lang"] = "3";
		*/
		return($arr);
	}

	private function check_snoras_args($arr)
	{
		if(is_oid($arr["payment_id"]))
		{
			$payment = obj($arr["payment_id"]);
			$arr = $this->_add_object_data($payment , $arr);
			$payment_data = $payment->meta("bank");
		}
		if(!$arr["service"]) $arr["service"] = "1001";
		if(!$arr["version"]) $arr["version"] = "008";
		if(!$arr["curr"]) $arr["curr"] = "EUR";
		if(array_key_exists($arr["lang"] , $this->languages["snoras"]))
		{
			$arr["lang"] = $this->languages["snoras"][$arr["lang"]];
		}
		else
		{
			$arr["lang"] = "ENG";
		}
		$arr["expl"] =  trim($arr["expl"]);
		if(!$arr["stamp"]) $arr["stamp"] = "666";
		if(!$arr["cancel_url"]) $arr["cancel_url"] = aw_ini_get("baseurl")."/automatweb/bank_return.aw";
		if(!$arr["return_url"]) $arr["return_url"] = aw_ini_get("baseurl")."/automatweb/bank_return.aw";
		if(!$arr["priv_key"])
		{
			if($arr["test"] && $this->test_priv_keys[$arr["bank_id"]])
			{
				$file = $this->test_priv_keys[$arr["bank_id"]];
			}
			else
			{
				$file = "privkey.pem";
			}
			$fp = fopen($this->cfg["site_basedir"]."/pank/".$file, "r");
			$arr["priv_key"] = fread($fp, 8192);
			fclose($fp);
		}
		$arr["reference_nr"].= (string)$this->viitenr_kontroll_731($arr["reference_nr"]);
		return($arr);
	}

	private function check_cc_args($arr)
	{
		if(is_oid($arr["payment_id"]))
		{
			$payment = obj($arr["payment_id"]);
			$arr = $this->_add_object_data($payment , $arr);
		}
		if(!$arr["curr"]) $arr["curr"] = "EEK";

		if(array_key_exists($arr["lang"] , $this->languages["cc"]))
		{
			$arr["lang"] = $this->languages["cc"][$arr["lang"]];
		}
		else
		{
			$arr["lang"] = "en";
		}
		$arr["expl"] =  trim($arr["expl"]);
		if(!$arr["cancel_url"]) $arr["cancel_url"] = aw_ini_get("baseurl")."/automatweb/bank_return.aw";
		if(!$arr["return_url"]) $arr["return_url"] = aw_ini_get("baseurl")."/automatweb/bank_return.aw";
		if(!$arr["priv_key"])
		{
			$file = "privkey.pem";
			$fp = fopen($this->cfg["site_basedir"]."/pank/".$file, "r");
			$arr["priv_key"] = fread($fp, 8192);
			fclose($fp);
		}
		$arr["reference_nr"].= (string)$this->viitenr_kontroll_731($arr["reference_nr"]);
		$arr["amount"] = $arr["amount"]*100; //sentides
		$arr["datetime"] = date("YmdHis", time());
		if(!$arr["service"]) $arr["service"] = "gaf";
		if(!$arr["version"]) $arr["version"] = "002";
		return($arr);
	}
	
	private function credit_card($args)
	{
		extract($args);
		//test:
		$action="$service";
		$ver="$version";
		$id="$sender_id";
		$idnp = $id;
		$ecuno='123456';
		$eamount='1000';
		$cur='EEK';
		$datetime=date("YmdHis");
		$id=sprintf("%-10s", "$id");
		$ecuno=sprintf("%012s", "$reference_nr");
		$eamount=sprintf("%012s", "$amount");
		$data = $ver . $id . $ecuno . $eamount . $cur . $datetime;
		$signature=sha1($data);
	//	echo "signatuur: <pre>$data</pre><br>";
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($data, $signature, $pkeyid);
		openssl_free_key($pkeyid);
		$mac=bin2hex($signature);
		//echo "https://pos.estcard.ee/webpos/servlet/iPAYServlet?action=$action&amp;ver=$ver&amp;id=$idnp&amp;ecuno=$ecuno&amp;eamount=$eamount&amp;cur=$cur&amp;datetime=$datetime&amp;mac=$mac&amp;lang=en";
		//testi lopp
		$VK_message = $version;
		$VK_message.= sprintf("%-10s", $sender_id);
		$VK_message.= sprintf("%012s",$reference_nr);
		$VK_message.= sprintf("%012s",$amount);
		$VK_message.= $curr;
		$VK_message.= $datetime;

		$signature=sha1($VK_message);
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($data, $VK_signature, $pkeyid);
		openssl_free_key($pkeyid);
		$VK_MAC = bin2hex($VK_signature);//base64_encode( $VK_signature);/
//		if(aw_global_get("uid") == "struktuur")
//		{
//			echo "https://pos.estcard.ee/webpos/servlet/iPAYServlet?action=$action&amp;ver=$version&amp;id=$sender_id&amp;ecuno=$reference_nr&amp;eamount=$amount&amp;cur=$curr&amp;datetime=$datetime&amp;mac=$VK_MAC&amp;lang=en";
//			die();
//		}
		$link = $this->bank_link["credit_card"];
		if($test)
		{
			$link = $this->test_link["credit_card"];
		}
		$params = array(
			"action"	=> $service,		//"gaf"
			"ver"		=> $version,		//Protokolli versioon, Fikseeritud vaartus: 002
			"id"		=> $sender_id,		//Kaupmehe kasutajanimi systeemis
			"ecuno"		=> $ecuno,	//Tehingu unikaalne number kaupmehe systeemis,min. lubatud vaaartus 100000
			"eamount"	=> $eamount,		//Kaupmehe systeemi poolt antav tehingu summa sentides.;
			"cur"		=> $curr,		//Tehingu valuuta nimi . Fikseeritud: EEK
			"datetime"	=> $datetime,		//AAAAKKPPTTmmss 	Tehingu kuupaev,kellaaeg
			"mac" 		=> $VK_MAC,		//Sonumi signatuur (MAC)*
			"lang" 		=> $lang,		//et,en . Systeemis kasutatav keel. et - Eesti, en - Inglise
		);//		if(aw_global_get("uid") == "struktuur")
		//{arr($params); die();
		//}

		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	}

	/**
		@attrib name=get_nordea_stats params=name all_args=1
	**/
	public function get_nordea_stats($val)
	{
		extract($val);
		$fp = fopen($this->cfg["site_basedir"]."/pank/nordea.mac", "r");
		$priv_key = fread($fp, 8192);
		fclose($fp);
		 
		$service = $version = "0001";
		$stamp = date("YmdHis" , time())."0001";
		$o = obj($id);
		$bank_data = $o->meta("bank");
		$sender_id = $bank_data["nordeapank"]["sender_id"];
		$det = "Y";
		$type = "html";
		$alg = "01";
		$SOLOPMT_MAC = '';
		$VK_message  = $service.'&';
		$VK_message .= $stamp.'&';
		$VK_message .= $sender_id.'&';
		$VK_message .= '3&';
		$VK_message .= $type.'&';
		$VK_message .= $val["SOLOPMT_RETURN_STAMP"].'&';
		$VK_message .= $val["SOLOPMT_RETURN_REF"].'&';
		$VK_message .= $version.'&';
		$VK_message .= $alg.'&';
		$VK_message .= $priv_key.'&';
		$SOLOPMT_MAC = strtoupper(md5( $VK_message ));
		//arr($VK_message);
		$params = array(
			"SOLOPMT_VERSION"     => $service,
			"SOLOPMT_TIMESTMP"    => $stamp,
			"SOLOPMT_RCV_ID"      => $sender_id, 
			"SOLOPMT_LANGUAGE"    => 3,//$SOLOPMT_MAC,
			"SOLOPMT_RESPTYPE"    => $type,
			"SOLOPMT_STAMP"       => $val["SOLOPMT_RETURN_STAMP"],
			"SOLOPMT_REF"         => $val["SOLOPMT_RETURN_REF"],//$SOLOPMT_MAC,
			"SOLOPMT_KEYVERS"     => $version,// 17.   Key Version    
			"SOLOPMT_ALG"         => $alg,
			"SOLOPMT_MAC"         => $SOLOPMT_MAC,
		);//arr($params);arr($val);die();
			$link = "https://netbank.nordea.com/pnbepay/query.jsp";
					$this->submit_bank_info(array(
						"params" => $params,
						"link" => "https://netbank.nordea.com/pnbepay/query.jsp",
						//"form" => $form
					));
// 					 $args = array(
// 					       "http" => array(
// 					         "method" => "POST",
//            					 "header"=> "Content-type: application/x-www-form-urlencoded\r\n",
// 					         "content" => http_build_query($params),
// 					       )
// 					     );
// 					     $context = stream_context_create($args);
// 					     // I know, it works. No need to send 'em anymore for testing purposes.
//  		$mobi_answer = file_get_contents($link, false, $context);
// 		//arr($link);
// 
// $headers=array("Content-Type: application/xml");
// 
// $curl = curl_init();
// curl_setopt($curl, CURLOPT_URL, $link);
// curl_setopt($curl, CURLOPT_HEADER, $headers);
// curl_setopt($curl, CURLOPT_POST, 1);
// curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
// $myResponse = curl_exec ($curl);
// curl_close ($curl);die(); 
// //die($myResponse);
// $result = curl_exec ($curl);
//		die($mobi_answer);

	}

	private function nordea($args)
	{//arr($args); die();
		extract($args);
		$SOLOPMT_MAC      = '';
		$VK_message       = $service.'&';
		$VK_message       .= $stamp.'&';
		$VK_message       .= $sender_id.'&';
		$VK_message       .= $amount.'&';
		$VK_message       .= $reference_nr.'&';
		$VK_message       .= $date.'&';
		$VK_message       .= $curr.'&';
		$VK_message       .= $priv_key.'&';
		//arr($VK_message);die();
		//$VK_message = "0003&1998052212254471&12345678&570,00&55&EXPRESS&EUR&LEHTI&";
		$SOLOPMT_MAC      = strtoupper(md5( $VK_message ));
		
		$http = get_instance("protocols/file/http");
		$link = $this->bank_link["nordeapank"];
		$params = array(
			"SOLOPMT_VERSION"     => $service,// 1.    Payment Version   SOLOPMT_VERSION   "0002"   AN 4  M
			"SOLOPMT_STAMP"       => $stamp,// 2.    Payment Specifier    SOLOPMT_STAMP  Code specifying the payment   N 20  M 
			"SOLOPMT_RCV_ID"      => $sender_id, // 3.    Service Provider ID  SOLOPMT_RCV_ID    Customer ID (in Nordea's register)  AN 15    M 
			"SOLOPMT_RCV_ACCOUNT" => $acc,// 4.    Service Provider's Account    SOLOPMT_RCV_ACCOUNT  Other than the default account   AN 15    O
			"SOLOPMT_RCV_NAME"    => $name,//5.    Service Provider's Name    SOLOPMT-RCV_NAME  Other than the default name   AN 30    O 
			"SOLOPMT_LANGUAGE"    => $lang,// 6.    Payment Language  SOLOPMT_LANGUAGE  1 = Finnish 2 = Swedish 3 = English    N 1   O 
			"SOLOPMT_AMOUNT"      => $amount,// 7.    Payment Amount    SOLOPMT_AMOUNT    E.g. 990.00    AN 19    M 
			"SOLOPMT_REF"         => $reference_nr,// 8.    Payment Reference Number   SOLOPMT_REF    Standard reference number  AN 20    M 
			"SOLOPMT_DATE"        => $date,// 9.    Payment Due Date  SOLOPMT_DATE   "EXPRESS" or "DD.MM.YYYY"  AN 10    M 
			"SOLOPMT_MSG"         => $expl,// 10.   Payment Message   SOLOPMT_MSG    Service user's message  AN 234   O 
			"SOLOPMT_RETURN"      => $return_url,// 11.   Return Address    SOLOPMT_RETURN    Return address following payment    AN 60    M 
			"SOLOPMT_CANCEL"      => $cancel_url,// 12.   Cancel Address    SOLOPMT_CANCEL    Return address if payment is cancelled    AN 60    M 
			"SOLOPMT_REJECT"      => $cancel_url,// 13.   Reject Address    SOLOPMT_REJECT    Return address for rejected payment    AN 60    M 
							// 14.   Solo Button OR Solo Symbol    SOLOPMT_ BUTTON SOLOPMT_IMAGE    Constant    Constant    O       // $SOLOPMT_ BUTTON SOLOPMT_IMAGE   Constant    Constant    O 			
			"SOLOPMT_MAC"         => $SOLOPMT_MAC,  // 15.   Payment MAC    SOLOPMT_MAC    MAC   AN 32    O 
			"SOLOPMT_CONFIRM"     => $confirm,// 16.   Payment Confirmation    SOLOPMT_CONFIRM   YES or NO   A 3   O 
			"SOLOPMT_KEYVERS"     => $version,// 17.   Key Version    SOLOPMT_KEYVERS   E.g. 0001   N 4   O 
			"SOLOPMT_CUR"         => $curr,// 18.   Currency Code  SOLOPMT_CUR    EUR   A 3   O 
		);
		if(!($name && $acc))
		{
			unset($params["SOLOPMT_RCV_NAME"]);
			unset($params["SOLOPMT_RCV_ACCOUNT"]);
		}
		return $this->submit_bank_info(array("params" => $params , "link" => $link , "form" => $form));
	//	return $http->post_request($link, $handler, $params, $port = 80);	
	}

	private function viitenr_kontroll_731($nr)
	{
		$nr = (string)$nr;
		$count = strlen($nr);
		$sum = 0;
		$x = 7;
		while($count > 0)
		{
			$count = $count - 1;
			$sum = $sum + (integer)$nr[$count]*$x;
			if($x == 7) $x = 3;
			elseif($x == 3) $x = 1;
			elseif($x == 1) $x = 7;
		}
		return (10 - ($sum%10))%10;
	}
	
	/**
	@attrib name=pay_site is_public="1" caption="Change" no_login=1 api=1 params=name
	@param die optional type=bool
		if set, dies instead of return
	@returns string/html
	@comment
		makes a list of supported banks with correct forms
		before calling this function you should fill $_SESSION["bank_payment"]
		uses template file bank_pay_site.tpl, if it exists , then every sub gets vars:
			"data" - hidden input fields needed in form 
			"link" - url to banklink
	@example
		$targ = obj($arr["alias"]["target"]);
		$_SESSION["bank_payment"] = array(
			"data"		=> $targ->meta("bank")// Array(
				//	[amount] //Amount to be paid. Max length=17
				//	[expl] //Explanation of payment order. Max length=70
				//	[bank_id] => Array//bank id. possible choices: "seb", "hansapank" , "sampopank", "nordeapank" , "krediidipank"
				//	(
				//		[sender_id]//ID of compiler of query (merchant's ID). Max length=10
				//		[stamp]//Query ID. Max length=20
				//	)
				//	[bank_id2] => Array
				//	(
				//		[sender_id]
				//		[stamp]
				//	))
			"reference_nr"	=> $_SESSION["realestate_input_data"]["realestate_id"],//Reference number of payment order. Max length=19
			"url" 		=> post_ru(),//optional
			"cancel"	=> post_ru(),//optional 
		);
		$bank_payment = get_instance(CL_BANK_PAYMENT);
		$ret.= '<a href="';
		$ret.= $bank_payment->mk_my_orb("pay_site", array());
		$ret.= '"> Maksma </a>';
	**/
	function pay_site($args)
	{
		global $die;
		extract($args);
		if(!$_SESSION["bank_payment"]) return false;
		extract($_SESSION["bank_payment"]);
		$tpl = "bank_pay_site.tpl";
		if($this->read_template($tpl, $silent=1))
		{
			$template_exists = 1;
		}
		$ret = "";
		foreach($this->banks as $bank => $name)
		{
			if(array_key_exists($bank , $data) && $data[$bank]["sender_id"])
			{
				$ret.='<img src="'.aw_ini_get("baseurl").'/automatweb/images/pank/'.$bank.'_pay.gif">';
				$bank_form = $this->do_payment(array(
					"form"		=> 1,
					"test"		=> $test,
					"bank_id"	=> $bank,
					"sender_id"	=> $data[$bank]["sender_id"],
					"stamp"		=> $data[$bank]["stamp"],
					"amount"	=> $data["amount"],
					"reference_nr"	=> $reference_nr,
					"expl"		=> $data["expl"],
				));
				if(($template_exists) && ($this->is_template($bank)))
				{
					if($test && $this->test_link[$bank]) $link = $this->test_link[$bank];
					else $link = $this->bank_link[$bank];
					
					$this->vars(array(
						"data" => $bank_form,
						"link" => $link,
					));
					$c .= $this->parse($bank);
					$this->vars(array(
						$bank => $c,
					));
					$c = "";
				}
				$ret.= $bank_form;
				$ret.= '<br><input type="submit" value="maksma"></form>';
			}
		}
		if($template_exists)
		{
			$this->vars(array(
				"data" => $ret,
			));
			return $this->parse();
		}
		if($die) die($ret);
		return $ret;
	}
	
	//mones kohas akki tahab kuskile objekti ka salvestada infot makse kohta... naiteks broneeringu juures..
	//niiet paneb koik selle kama sessiooni selgemalt kirja... votab sessioonist $_SESSION["bank_return"]["data"] kyljest koik
	function get_payment_info($val)
	{
		$ret = array(
			"time" => time(),
		);
		if(!$val) $val = $_SESSION["bank_return"]["data"];
		if($val["VK_SND_ID"])
		{
			$bank_id = $this->merchant_id[$val["VK_SND_ID"]];
			$ret["sum"] = $val["VK_AMOUNT"];
			$ret["payer"] = $val["VK_SND_NAME"];

			unset($val["VK_MAC"]);
		}
		elseif($val["action"])
		{
			$bank_id = $this->merchant_id[$val["action"]];
			$ret["sum"] = $val["eamount"]/100;
			$ret["payer"] = $val["msgdata"];

			unset($val["mac"]);
		}
		else
		{
			$bank_id = $this->merchant_id[$val["SOLOPMT_RETURN_VERSION"]];
			unset($val["SOLOPMT_RETURN_MAC"]);
		}
		$ret["bank"] = $this->banks[$bank_id];
		$ret["bank_id"] = $bank_id;
		$ret["curr"] = $val[$this->curr[$bank_id]];
		$ret["time"] = $val["timestamp"];

		//kogu infi ka yheks stringiks kokku
		unset($val["timestamp"]);
		$ret["all"] = "";
		foreach($val as $var => $val)
		{
			$ret["all"].= $var."=\"".$val."\",";
		}
		$ret["all"] = substr($ret["all"], 0, 255);
		return $ret;
	}

	/**
	@attrib name=check_response is_public="1" caption="Change" no_login=1 api=1
	@returns 1 if the signature is correct, 0 if it is incorrect, and -1 on error
	@comment
		checks if the response from a bank is correct
		reads data from $_SESSION["bank_return"]["data"]
	**/
	function check_response()
	{
		extract($_SESSION["bank_return"]["data"]);
		if($action == "afb")//selliselt tulevad krediitkaardimakse tagasipoordumised
		{
			return $this->check_cc_response();
		}
		if($_SESSION["bank_return"]["data"]["SOLOPMT_RETURN_VERSION"] == "0002")//selliselt tulevad Nordeapangamaksed
		{
			return $this->check_nordea_response();
		}
		$data = substr("000".strlen($VK_SERVICE),-3).$VK_SERVICE
		.substr("000".strlen($VK_VERSION),-3).$VK_VERSION
		.substr("000".strlen($VK_SND_ID),-3).$VK_SND_ID
		.substr("000".strlen($VK_REC_ID),-3).$VK_REC_ID
		.substr("000".strlen($VK_STAMP),-3).$VK_STAMP
		.substr("000".strlen($VK_T_NO),-3).$VK_T_NO
		.substr("000".strlen($VK_AMOUNT),-3).$VK_AMOUNT
		.substr("000".strlen($VK_CURR),-3).$VK_CURR
		.substr("000".strlen($VK_REC_ACC),-3).$VK_REC_ACC
		.substr("000".strlen($VK_REC_NAME),-3).$VK_REC_NAME
		.substr("000".strlen($VK_SND_ACC),-3).$VK_SND_ACC
		.substr("000".strlen($VK_SND_NAME),-3).$VK_SND_NAME
		.substr("000".strlen($VK_REF),-3).$VK_REF
		.substr("000".strlen($VK_MSG),-3).$VK_MSG
		.substr("000".strlen($VK_T_DATE),-3).$VK_T_DATE;

		$signature = base64_decode($VK_MAC);

		//vaike hakk siis teiste riikide samade pankade jaoks
		$id = substr($VK_REF ,0 , -1 );
		if(is_oid($id) && $this->can("view" , $id))
		{
			$ref_object = obj($id);
			$cntr = $ref_object->meta("bank_cntr");
			if($cntr)
			{
				$cntr = substr($cntr,0,2);
				if(file_exists($this->cfg["site_basedir"]."/pank/".$VK_SND_ID."_".$cntr."_pub.pem"))
				{
					$VK_SND_ID.= "_".$cntr;
				}
			}
		}

		$fp = fopen($this->cfg["site_basedir"]."/pank/".$VK_SND_ID."_pub.pem", "r");
		$cert = fread($fp, 8192);
		fclose($fp);
		$pubkeyid = openssl_get_publickey($cert);
		$ok = openssl_verify($data, $signature, $pubkeyid);
		openssl_free_key($pubkeyid);
		return $ok;
		if ($ok == 1)
		{
			echo "good";
		}
		elseif ($ok == 0) 
		{
			echo "bad";
		}
		else {
			echo "ugly, error checking signature";
		}	
	}
	
	function hex2str($hex) {
		for($i=0;$i<strlen($hex);$i+=2) $str.=chr(hexdec(substr($hex,$i,2)));
		return $str;
	}
	
	private function check_cc_response()
	{
		extract($_SESSION["bank_return"]["data"]);
		$data = sprintf("%03s", $ver) . sprintf("%-10s", "$id") .
		sprintf("%012s", $ecuno) . sprintf("%06s", $receipt_no) . sprintf("%012s",
		$eamount) . sprintf("%3s", $cur) . $respcode . $datetime . sprintf("%-40s",
		$msgdata) . sprintf("%-40s", $actiontext);
		$mac = $this->hex2str($mac);
		$signature = sha1($data);
		$fp = fopen($this->cfg["site_basedir"]."/pank/credit_card.crt", "r");
		$cert = fread($fp, 8192);
		fclose($fp);
		$pubkeyid = openssl_get_publickey($cert);
		$ok = openssl_verify($data, $mac, $pubkeyid);
		openssl_free_key($pubkeyid);
		return $ok;
	}

	private function check_nordea_response()
	{
		extract($_SESSION["bank_return"]);
		$fp = fopen($this->cfg["site_basedir"]."/pank/nordea.mac", "r");
		$cert = fread($fp, 8192);
		fclose($fp);
		$str = $data["SOLOPMT_RETURN_VERSION"]."&".$data["SOLOPMT_RETURN_STAMP"]."&".$data["SOLOPMT_RETURN_REF"]."&".$data["SOLOPMT_RETURN_PAID"]."&".$cert."&";
		if($data["SOLOPMT_RETURN_MAC"] == strtoupper(md5($str))) $ok = 1;
		else $ok = 0;
		//a seniks returnime ok, nagu oleks koik hasti
		return $ok;
	}

	function _get_documentation()
	{
		$t = "";
		$t.= t("Veidi olulisemat infot");
		$t.= "\n<br>\n<br>";
		$t.= t("Alustamiseks:");
		$t.= "\n<br>";
		
		$t.= "1.";
		$t.= t("Vaja s&otilde;lmida pangalingi leping pankadega - ilma ei juhtu midagi...");
		$t.= "\n<br>\n<br>";
		
		$t.= "2.";
		$t.= t("Vaja tekitada kaupmehele privaatv&otilde;ti ja sertifikaadi p&auml;ring");
		$t.= "\n<br>openssl req -newkey 1024 -nodes -out ./cert_req.pem\n<br>\n<br>";
		
		$t.= "3.";
		$t.= t("privkey.pem on tekkinud privaatv&otilde;ti mis peab saama asukohaks");
		$t.= " ".$this->cfg["site_basedir"]."/pank/privkey.pem\n<br>\n<br>";
		
		$t.= "4.";
		$t.= t("cert_req.pem on tekkinud sertifikaadip&auml;ring mis tuleb saata pankadele");
		$banks = array();
		foreach($this->default_banks as $b){$banks[]=$this->banks[$b];}
		$banks[] = $this->banks["credit_card"];
		$t.= ":\n<br>".join(", " , $banks);
		$t.= "\n<br>\n<br>";
		
		$t.= "5.";
		$t.= t("Vastu saadakse avalikud v&otilde;tmed, mis peavad j&otilde;udma kataloogi");
		$t.= " ".$this->cfg["site_basedir"]."/pank/\n<br>";
		$banks = array();
		foreach($this->public_key_files as $b=> $fn){$banks[] = $this->banks[$b]. " - ".$fn;}
		$t.= t("failide nimed peaksid olema vastavalt pankadele:");
		$t.= ":\n<br>".join("\n<br>" , $banks);
		$t.= "\n<br>\n<br>";

		$t.= "6.";
		$t.= t("Tab'i \"Pankade info\" alla pangalt saadud kaupmehe ID, \n<br>kui kindlale arvele vaja raha saata, siis oleks vaja m&auml;rkida ka arve number ja saaja nimi (toimib pankadel millel on ka selline v&otilde;imalus olemas)");
		$t.= "\n<br>\n<br>";
	
		$t.= "7.";
		$t.= t("Kontrolli , et kataloogis ").$this->cfg["site_basedir"].t(" oleks olemas ja kirjutamis&otilde;igustega fail nimega bank_log.txt");
		$t.= "\n<br>\n<br>";
	
		$t.= "8.";
		$t.= t("Lihtsamal juhul peaks n&uuml;&uuml;d maksmine toimima, keerulisemal on vaja templeitides v&ouml;i koodis mudida");
		$t.= "\n<br>\n<br>";
	
		$t.= "Nordea. ";
		$t.= t("Kasutab lihtsalt &uuml;ht MAC v&otilde;tit, mis peaks asuma v&otilde;tmete kataloogis nimega nordea.mac");
		$t.= "\n<br>\n<br>";
	
		$t.= "Kaardikeskus. ";
		$t.= t("Neile vaja saata tagasiside url, milleks on");
		$t.= ":\n<br>".aw_ini_get("baseurl")."/automatweb/bank_return.aw\n<br>";
		$t.= t("Vaja teha veel testv&otilde;ti ja sertifikaadi p&auml;ring testimiseks(seal tuleb enne katsetada testkeskkonnas ja vastavad tegelased (Kaardikeskusest) peaks saama &uuml;le vaadata kas k&otilde;ik on nagu peab)");
		$t.= "\n<br>\n<br>";
	
		$t.= "Test. ";
		$t.= t("Toimib vaid Krediitkaardi , SEB &uuml;hispanga ja Nordea'ga")."\n<br>";
		$t.= t("Nordea puhul peab pangast saama testkasutaja andmed &uuml;hekordsete testmaksete jaoks (k&otilde;ik toimib nagu p&auml;riselt, lihtsalt raha ei tule arvele)\n<br>");
		$t.= t("Krediitkaardi puhul peab lisama testprivaatv&ouml;tme Tab'i test alla")."\n<br>";
		$t.= t("SEB puhul peab lisama testprivaatv&ouml;tmeks SEB testkaupmehe privaatv&otilde;tme")."\n<br>";
		$t.= t("Nii krediitkaardi kui ka SEB puhul on eraldi testkeskkond, kus saab tegutseda pangalt saadud testkasutajaga.. et suunamine sinna toimuks, vaja m&auml;rkida testrezhiim linnuke");
		$t.= "\n<br>\n<br>";
	
		$t.= t("Kaupluse avaleht")."\n<br>";
		$t.= t("Kauluse avalehel tahavad pangad n&auml;ha viidet selle kohta, et saidil saab maksta nende pangalingiga.. selleks peaks olema avalehel vastav ikoon. Neid saab :")."\n<br>";
		$t.= aw_ini_get("baseurl").'/automatweb/images/pank/"'.t("panga kood").'".gif'." v&ouml;i panga kodulehelt\n<br>";
		$t.= t("Maksmisele minnes tuleks &uuml;ldjuhul kasutada ka pankade poolt antud ikoone. Neid saab :")."\n<br>";
		$t.= aw_ini_get("baseurl").'/automatweb/images/pank/"'.t("panga kood").'"_pay.gif v&ouml;i panga kodulehelt';
		$t.= "\n<br>\n<br>";
	
		$t.= t("Panga koodid").":\n<br>";
		$t.= t("(Neid l&auml;heb vaja templeitide valmistamisel m&otilde;nel juhul)");
		$banks = array();
		foreach($this->banks as $b => $fn){$banks[] = $fn. " - ".$b;}
		$t.= "\n<br>".join("\n<br>" , $banks);
		$t.= "\n<br>\n<br>";
	
		return $t;
	}

	function show($arr)
	{
		$o = obj($arr["id"]);
		$amt = $o->prop("alias_amount");
		$desc = $o->prop("alias_desc");

		if ($this->can("view", $o->prop("alias_amount_ctr")))
		{
			$i = get_instance(CL_CFGCONTROLLER);
			$amt = $i->check_property($o->prop("alias_amount_ctr"), $o->id(), $arr);
		}
		if ($this->can("view", $o->prop("alias_desc_ctr")))
		{
			$i = get_instance(CL_CFGCONTROLLER);
			$desc = $i->check_property($o->prop("alias_desc_ctr"), $o->id(), $arr);
		}

		//kui peaks tekkima juhuseid, et seda funktsiooni kasutades peaks miskile id'le suunama, siis tuleb j2rgnev rida 2ra muuta
		$_SESSION["bank_payment"]["url"] = $o->prop("bank_return_url");

		return $this->bank_forms(array(
			"id" => $o->id(),
			"reference_nr" => "",
			"amount" => $amt,
			"expl" => $desc,
			"lang" => "en"
		));
	}

	function bank_return($arr)
	{
		$o = obj($arr["id"]);
		if ($this->can("view", $o->prop("alias_return_ctr")))
		{
			$i = get_instance(CL_CFGCONTROLLER);
			$desc = $i->check_property($o->prop("alias_return_ctr"), $o->id(), $arr);
		}
	}

	function get_cc_check_imap($id)
	{
		$imap = null;
		if($this->can("view" , $id))
		{
			$bp = obj($id);
			$imap = $bp->prop("imap");
		}
		if(!$imap)
		{
			$ol = new object_list(array(
				"class_id" => CL_BANK_PAYMENT,
				"site_id" => array(),
				"lang_id" => array(),
			));
			foreach($ol->arr() as $o)
			{
				if($o->prop("imap"))
				{
					return $o->prop("imap");
				}
			}
		}
		
		return $imap;
	}


	//seeee funktsioon on selle jaoks, et kuskile croni vms saaks panna k2ima skripti mis aegajalt kontrollib maile mis on saadetud kaardikeskuse poolt ja kus oleks makse aruanded.... oeh, kuna see tulevikus on niikuinii m6ttetu, siis .........
	/**
	@attrib name=cccheck nologin=1 api=1 all_args=1
	@returns 1 if the signature is correct, 0 if it is incorrect, and -1 on error
	**/
	function mutafuggah($arr)
	{
		$imap_id = $this->get_cc_check_imap($arr["id"]);
		if(!$this->can("view" , $imap_id))
		{
			die(t("Ei suutnud imap seadistust leida"));
		}
		global $site_dir;
		$log = "";
		$imap = obj($imap_id);
		$imap_i = $imap->instance();
		$inf = $imap_i->connect_server(array(
			"obj_inst" => $imap
		));
		$ecard_mail_count = $ecuno_count = 0;
		$fld_c = $imap_i->get_folder_contents(array("from" => 0, "to" => 50));

		krsort($fld_c);
		foreach($fld_c as $key => $val)
		{
			if($ecard_mail_count >= 6)
			{
				break;
			}
			$ms = $imap_i->fetch_message(array("msgid" => $key));
			if($ms["from"] == "card@estcard.ee")
			{
				$ecard_mail_count++;
			}
			else
			{
				continue;
			}

			$content_rows = explode("\n" , $ms["content"]);
			$ecunos = array();

			foreach($content_rows as $row)
			{
				$cols = explode(" " , $row);
				foreach($cols as $k => $v)
				{
					if(!(strlen($v) > 2))
					{
						unset($cols[$k]);
					}
				}
				if(sizeof($cols) >= 8)
				{
					$possible = substr(end($cols) , 0 , -2);
					if(is_oid($possible))
					{
						$ecunos[$possible] = $cols;
					}
				}
			}
			$count = 0;
			foreach($ecunos as $ecuno => $data)
			{
				if($this->can("view" , $ecuno))
				{
					$ecuno_object = obj($ecuno);
					$ecuno_inst = $ecuno_object->instance();
					$payment_marked = $check_marked = 0;
					if(method_exists($ecuno_object,"payment_marked"))
					{
						$payment_marked = $ecuno_object->payment_marked();
				
					}
					elseif(method_exists($ecuno_inst,"payment_marked"))
					{
						$payment_marked = $ecuno_inst->payment_marked($ecuno);
					}

					if(!$payment_marked)
					{
						$data = array_merge($data , array());

						$log.= "idga ".$ecuno." on laekunud kuid ei ole kinnitatud. maksis  ".$data[6]." ".$data[7]." ".$data[1]." " .$data[3]. " EEK<br>\n";
						$loga = array();
						$loga["timestamp"] = time()+$ecuno_count;
						$loga["good"] = 1;
						$loga["eamount"] = $data[3]*100;
						$loga["actiontext"] = t("makse kinnitus krediitkaardi saadetud logidest");
						$loga["bank"] = t("Krediitkaart");
						$loga["ecuno"] = $ecuno."0";
						$loga["msgdata"] = $data[6]." ".$data[7];
						$loga["action"] = "afb";
						$loga["respcode"] = "000";
						$myFile = $site_dir."/bank_log.txt";
						$fh = fopen($myFile, 'a');
						fwrite($fh, serialize($loga)."\n");
						fclose($fh);
						$ecuno_count++;
						$count++;
					}

					if(!$payment_marked && method_exists($ecuno_object,"bank_return"))
					{
						$log.= "teeb kinnituse l2bi. objekti klassi id: ".$ecuno_object->class_id()."<br>\n";
						$ecuno_object->bank_return(array(
							"do_not_die" => 1
						));
					}
					elseif(!$payment_marked && method_exists($ecuno_inst,"bank_return"))
					{
						$log.= "teeb kinnituse l2bi. objekti klassi id: ".$ecuno_object->class_id()."<br>\n";
						$ecuno_inst->bank_return(array(
							"id" => $ecuno,
							"do_not_die" => 1
						));
					}
				}
			}
			if($count)
			{
				$log.= $ms["date"]." kirjast ".$count." kinnitatud<br>\n<br>\n";
			}
		}
		$log.= date("d.m.Y H:i")." kokku ".$ecuno_count."<br>\n<br>\n<br>\n";

		print $log;


		$myFile = "/tmp/cc_update_log.txt";
		$fh = fopen($myFile, 'a');
		fwrite($fh, $log);
		fclose($fh);

		die();
	}
}

?>
