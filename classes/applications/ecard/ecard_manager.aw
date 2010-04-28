<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/ecard/ecard_manager.aw,v 1.5 2007/12/06 14:33:28 kristo Exp $
// ecard_manager.aw - E-kaardi haldur 
// Use this class as alias in a document. CL_ECARD is for internal use
// Make sure you attach the folders and a mini_gallery object
//
// 
/*
@classinfo syslog_type=ST_ECARD_MANAGER relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default method=serialize

@property dir_images type=relpicker reltype=RELTYPE_DIR_IMAGES field=meta method=serialize
@caption Piltide kataloog
@comment Pilte otsitakse ka selle alamkataloogidest

@property dir_ecards type=relpicker reltype=RELTYPE_DIR_ECARDS field=meta method=serialize
@caption E-kaartide salvestamise kataloog

@property gallery type=relpicker reltype=RELTYPE_GALLERY field=meta method=serialize automatic=1
@caption Minigalerii

@reltype DIR_IMAGES value=1 clid=CL_MENU
@caption Piltide kataloog

@reltype DIR_ECARDS value=2 clid=CL_MENU
@caption E-kaartide salvestamise kataloog

@reltype GALLERY value=3 clid=CL_MINI_GALLERY
@caption Minigalerii objekt 

*/

define('ECARD_POSITION_BELOW', 1);
define('ECARD_POSITION_ASIDE', 2);

class ecard_manager extends class_base
{
	const AW_CLID = 996;

	var $formdata;
	function ecard_manager()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		$this->init(array(
			"tpldir" => "applications/ecard/ecard_manager",
			"clid" => CL_ECARD_MANAGER
		));
	}
	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"], "doc_id" => aw_global_get("section")));
	}

	////
	// !this shows the object
	function show($arr)
	{
		$this->formdata = array( // Form fields etc
			'from_name'	=> array(type => 'textbox', 'caption' => t("Teie nimi"), 'optional' => false),
			'from_mail'	=> array(type => 'textbox', 'caption' => t("Teie e-post"), 'optional' => false),
			'to_name'	=> array(type => 'textbox', 'caption' => t("Saaja nimi"), 'optional' => false),
			'to_mail'	=> array(type => 'textbox', 'caption' => t("Saaja e-post"), 'optional' => false),
			'comment'	=> array(type => 'textarea', 'caption' => t("Tekst kaardil"), 'optional' => true),
			'send_date'	=> array(type => 'date_select', 'caption' => t("Millal saata"), 'year_from' => date('Y'), 'optional' => false),
			'position'	=> array(type => 'select', 'caption' => t("Paigutus"), 'options' => array ('1' => t("Tekst all"), '2' => t("Tekst k&otilde;rval")), 'optional' => false),
			'spy'	=> array(type => 'checkbox', 'value' => 1, 'caption' => t("Teata vaatamisest meiliga")),
			'submit'	=> array(type => 'submit', 'value' => t("Edasi eelvaatele"), 'caption' => ''),
			'act'	=> array(type => 'hidden', 'value' => 'form_submit'),
			'class2'	=> array(type => 'hidden', 'value' => 'ecard_manager'), 
			'doc_id' => array(type => 'hidden', 'value' => $arr["doc_id"]),
			'id' => array(type => 'hidden', 'value' => $arr["id"]),
			'card'	=> array(type => 'hidden', 'value' => null, 'optional' => false), 
		);
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->sub_merge = 1;
		$card = null;
		$status = 'form'; // steps: browse, form, review, final 
		
		$this->vars(array(
			"name" => $ob->prop("name"),
		));

		// Some properties must be defined
		$id_dir = $ob->prop('dir_images');
		if (!is_oid($id_dir) || !$this->can('view', $id_dir) || !($dir=obj($id_dir)) || $dir->class_id() != CL_MENU)
		{
			exit;
		}

		$id_dir_cards = $ob->prop('dir_ecards');
		if (!is_oid($id_dir_cards) || !$this->can('view', $id_dir_cards) || !($dir_cards=obj($id_dir_cards)) || $dir_cards->class_id() != CL_MENU)
		{
			exit;
		}

		// VIEWING A CARD
		$card_output = $this->_get_view_card_html(array(
			'card' => $_GET['view_card'],
			'hash' => $_GET['hash'],
		));	
		if (!is_null($card_output))
		{
			return $card_output;
		}
		
		// INPUT VALIDATION ON FORM SUBMIT 
		if (isset($_POST['id']) && $_POST['id'] == $arr['id'] && $_POST['act'] == 'form_submit' && $_POST['class2'] == 'ecard_manager')
		{
			$status = $this->_validate_form_input(array(
				'args' => $_POST,
			));
		}
	
		if (isset($_REQUEST['card']) && is_oid($_REQUEST['card']) && $this->can("view", $_REQUEST['card']))
		{
			// SENDING CARD - PREVIEW 
			$card_output = $form_output = "";
			$tmp_sub_merge = $this->sub_merge;
			$this->sub_merge = 0;

			$card = obj($_REQUEST['card']);
			$parent = obj($card->parent());
			$gparent = $parent->parent();
			if ($card->class_id() != CL_IMAGE || ($parent->id() != $id_dir && $gparent != $id_dir))
			{
				exit;
			}
			
			$position = ECARD_POSITION_BELOW; // 1-below, 2-aside
			if (isset($_POST['position']) && in_array($_POST['position'], array(ECARD_POSITION_BELOW, ECARD_POSITION_ASIDE))) 
			{
				$position = $_POST['position'];
			}
			
			$card_output = $this->_get_view_card_html(array(
				'image' => $card->id(),
				'comment' => $status != 'from' && isset($_POST['comment']) ? $_POST['comment'] : "",
				'from' => $status != 'form' ? $_POST['from_name'] : "",
				'to' => $status != 'form' ? $_POST['to_name'] : "",
				'position' => $position,
			));
				


			if ($status == 'final')
			{
				// Create CL_ECARD object, send out e-mail
				$this->_finalize_card_send(array(
					'dir' => $id_dir_cards,
					'o_card' => $card,
					'doc_id' => $arr['doc_id'],
					'from_name' => $_POST['from_name'],
					'comment' => $_POST['comment'],
					'from_mail' => $_POST['from_mail'],
					'to_mail' => $_POST['to_mail'],
					'to_name' => $_POST['to_name'],
					'send_date' => $_POST['send_date'],
					'position' => $_POST['position'],
					'spy' => isset($_POST['spy']) ? 1 : 0,
				));
			}
			else
			{ 
				// SENDING CARD - FORM GENERATION 
				$this->formdata['card']['value'] = $card->id();

				$form_output = $this->_generate_form(array(
					'status' => $status,
					'action' => aw_ini_get('baseurl').'/'.$arr['doc_id'],
				));
				
			}
			
			$this->vars(array(
				'card' => $card_output,
				'form' => $form_output,
			));

			$this->sub_merge = $tmp_sub_merge;
			$this->parse('ecard_input');
		}
		else
		{
			// BROWSING - DIRECTORY LIST 
/*
			$this->vars(array(
				'dirurl' => aw_ini_get('baseurl').'/'.$arr['doc_id'],
				'dirname' => t("Esimesed"),
			));

			$this->parse('dirlist_item');
*/
			$ol = new object_list(array(
				'parent' => $id_dir,
				'class_id' => CL_MENU,
				'status' => STAT_ACTIVE,
				'lang_id' => 1,
			));
			for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				$this->vars(array(
					'dirurl' => aw_ini_get('baseurl').'/'.$arr['doc_id'].'?card_dir='.$o->id(),
					'dirname' => $o->name(),
				));
				$this->parse('dirlist_item');
			}


			// BROWSING - IMAGES GALLERY
			
			// Choose correct directory
			$chosen_dir = $id_dir;
			if (isset($_GET['card_dir']) && is_oid($_GET['card_dir']) && $this->can('view', $_GET['card_dir']) && ($d=obj($_GET['card_dir'])) && $d->class_id() == CL_MENU && $d->parent() == $id_dir)
			{
				$chosen_dir = $_GET['card_dir'];
			}
		
			$gal = obj($ob->prop('gallery')); // Uses premade mini_gallery object for listing images
			$gal->connect(array(
				'type'	=> 1,
				'to'	=> $chosen_dir,
			));
			$gal->set_prop('folder', $chosen_dir);
			$i_gal = $gal->instance();
			$gal_output = $i_gal->show(array(
				'id' => $gal->id(),
				'link_prefix' => '/'.aw_global_get("section")."?card=",
			));
		
			$this->vars(array(
				"list" => $gal_output,
			));
			$this->parse('images_list');
		}
		return $this->parse();
	}

	// SENDING CARD - FORM GENERATION 
	function _generate_form ($arr)
	{
		$form = "";
		foreach ($this->formdata as $name => $vals)
		{
			$vals['name'] = $name;
			if ($arr['status'] == 'review' && $vals['type'] != 'submit')
			{
				$vals['type'] = 'hidden';
			}
			$element = call_user_func(array('html',$vals['type']), $vals); // Create inputs
			if ($vals['type'] == 'hidden')
			{
				$form .= $element;
			}
			else
			{
				$error = "";
				if (isset($vals['error']))
				{
					$this->vars(array(
						'errormsg' => t("Viga").": ".$vals['error'],
					));
					$error = $this->parse('form_item_error');
				}
				$this->vars(array(
					'caption' => $vals['type'] == "checkbox" ? "" : $vals['caption'],
					'element' => $element,
					'error' => $error,
				));
				$form .= $this->parse('form_item');
			}		
		}
		return  html::form(array(
			'action' => $arr['action'],
			'method' => "POST",
			'name' => "ecard",
			'content' => $form,
		));
	}
	
	// Create CL_ECARD object, send out e-mail
	function _finalize_card_send ($arr)
	{
		$dir = $arr['dir'];
		$card = $arr['o_card'];
		$o = new object(array(
			'class_id' => CL_ECARD,
			'parent' => $dir,
		));
		$o->save();
		$o->set_name('Kaart ' . $o->id());
		$o->connect(array(
			'to' => $card,
			'type' => 1,
		));
		$o->set_prop('image', $card->id());
		$o->set_prop('from_name', $arr['from_name']);
		$o->set_prop('comment', $arr['comment']);
		$o->set_prop('from_mail', $arr['from_mail']);
		$o->set_prop('to_name', $arr['to_name']);
		$o->set_prop('to_mail', $arr['to_mail']);
		$o->set_prop('senddate', $arr['send_date']);
		$o->set_prop('position', $arr['position']);
		$o->set_prop('hash',generate_password());
		$o->set_prop('spy', $arr['spy'] ? 16 : 0);
		$o->save();


		$d = $arr['send_date'];
		if ($d['day'] == date('d') && $d['month'] == date('m') && $d['year'] == date('Y'))
		{ // Send mail now
			$this->send_card(array('card' => $o->id(), 'doc_id' => $arr["doc_id"]));
			$this->vars(array(
				'message' => t("Kaart saadetud"),
			));
		}
		else
		{ // Schedule for given date
			$url = $this->mk_my_orb("send_card", array('card' => $o->id(), 'id' => $ob->id, 'doc_id' => $arr["doc_id"]), CL_ECARD_MANAGER);
			$scheduler = get_instance("scheduler");
			$scheduler->add(array(
				'event' => $url,
				'time' => mktime(9, 0, 0, $d['month'], $d['day'], $d['year']),
			));
			$this->vars(array(
				'message' => t("Kaart salvestatud"),
			));
		}
	}

	// Validate form input
	function _validate_form_input ($arr)
	{
		$args = $arr['args'];
		$errors = 0;
		foreach ($this->formdata as $name => $vals)
		{
			if (isset($vals['optional']) && $vals['optional'] == false && empty($args[$name]))
			{
				$this->formdata[$name]['error'] = t("Väli peab olema täidetud");
				$errors++;
			}
			if ($vals['type'] == 'checkbox')
			{
				$this->formdata[$name]['checked'] = isset($args[$name]) ? 1 : 0;
			}
			else if ($vals['type'] != 'hidden' && $vals['type'] != 'submit')
			{
				$this->formdata[$name]['value'] = isset($args[$name]) ? str_replace("'", "&#039;", $args[$name]) : "";
			}	
		}	

		if (isset($args['from_mail']) && !is_email($args['from_mail']))
		{
			$this->formdata['from_mail']['error'] = t("Teie e-posti aadress vigane");
			$errors++;
		}
		if (isset($args['to_mail']) && !is_email($args['to_mail']))
		{
			$this->formdata['to_mail']['error'] = t("Saaja e-posti aadress vigane");
			$errors++;
		}
		$d = $args['send_date'];
		if (!is_date($d['day'].'-'.$d['month'].'-'.$d['year']) || date('Y') > $d['year'])
		{
			$this->formdata['send_date']['error'] = t("Kuupäev vigane");
			$errors++;
		}
		if ($errors === 0)
		{
			if (isset($args['reviewed']) && !isset($args['back']))
			{
				return 'final';
			}	
			else if (!isset($args['back']))
			{
				$this->formdata['submit']['value'] = t("Saada");
				$this->formdata['reviewed']	= array(type => 'hidden', 'value' => '1'); 
				$this->formdata['back']	= array(type => 'submit', 'value' => t("Tagasi"), 'caption' => '');
				$this->formdata['send_date[day]'] = array(type => 'hidden', 'value' => $args['send_date']['day']);
				$this->formdata['send_date[month]'] = array(type => 'hidden', 'value' => $args['send_date']['month']);
				$this->formdata['send_date[year]'] = array(type => 'hidden', 'value' => $args['send_date']['year']);
				unset($this->formdata['send_date']);
				return 'review';
			}	
		}
		return 'view';
	}
	
	// Generate card showing html
	function _get_view_card_html($arr)
	{
		$card = $image = $hash = $c = null;
		$comment = $from = $to = "";
		$spy = false;
		$img_inst = get_instance(CL_IMAGE);

		if (isset($arr['card']))
		{
			$hash = $arr['hash'];
			$card = $arr['card'];
			// $card is id of CL_CARD object
			if ( isset($card) && is_oid($card) && ($c = obj($card)) && $c->class_id() == CL_ECARD && $this->can('view', $card)
				&& isset($hash) && $hash == $c->prop('hash'))
			{
				$image = $c->prop('image');
				$comment = $c->prop('comment');
				$from = $c->prop('from_name');
				$to = $c->prop('to_name');
				$position = $c->prop('position');
				$spy = $c->prop('spy');
			}
		}
		else if (isset($arr['image']) && is_oid($arr['image']))
		{
			$image = $arr['image'];
			$comment = $arr['comment'];
			$from = $arr['from'];
			$to = $arr['to'];
			$position = $arr['position'];
		}
		
		if (is_oid($image) && ($img = obj($image)) && $img->class_id() == CL_IMAGE && $this->can('view', $image))
		{
			$save_sub_merge = $this->sub_merge;
			$this->sub_merge = 0;
			$file2 = basename($img->prop("file2")); // Filename for big image
			$author = "";
			if (strlen($img->prop("author")))
			{
				$this->vars(array('img_author' => $img->prop("author")));
				$author = $this->parse('card_'.$position.'_imgauthor');
			}
			$this->vars(array(
				'imgurl' => $this->mk_my_orb("show", array('id'=>$img->id(), 'fastcall' => 1, 'file' => $file2 ), CL_IMAGE),
				'card_'.$position.'_imgauthor' => $author,
				'imgcomment' => $img->prop("comment"),
				'imgtext' => htmlspecialchars($comment),
				'from'	=> htmlspecialchars($from),
				'to'	=> htmlspecialchars($to),
			));
			
			$card_output = $this->parse('card_'.$position); // Different templates for different layouts

			// If needed, send notification mail to ecard sender
			if ($spy)
			{
				send_mail($c->prop('from_mail'), 'Kaarti vaadati', "Tere\n\nTeie ".$c->prop('to_name')."-le saadetud kaarti on Visittartu.com keskkonnas vaadatud!\n\nViljaõnne!");
				$c->set_prop('spy', 0); // Send only once
				$c->save();
			}
			$this->sub_merge = $saved_sub_merge; 
			return $card_output;
		}
		return null;
	}	
	
	//-- methods --//
	
	/**
		@attrib name=send_card nologin="1" 
		@param card required type=int acl=view class_id=CL_ECARD
		@param doc_id required type=int acl=view class_id=CL_MENU

		@desc sends e-mails
	**/
	function send_card($arr)
	{
		$message = "Tere %s,\n\n%s on saatnud Teile e-kaardi!\n\nKaarti näete aadressil:\n%s\n\nKaart saadeti visittartu.com keskkonnast.\n\n"; 

		$o = obj($arr['card']);
		$url = aw_ini_get('baseurl').'/'.$arr['doc_id'].'?view_card='.$arr['card'].'&hash='.urlencode($o->prop('hash'));	
		send_mail($o->prop('to_name').' <'.$o->prop('to_mail').'>', 
			"Teile on e-kaart!", 
			sprintf($message, $o->prop('to_name'), $o->prop('from_name'), $url), 
			"From: ".$o->prop('from_name').' <'.$o->prop('from_mail').'>'
		);
	}
}
?>
