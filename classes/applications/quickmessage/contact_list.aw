<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/quickmessage/contact_list.aw,v 1.10 2008/05/08 20:15:11 kristo Exp $
// contact_list.aw - Aadressiraamat 
/*

@classinfo syslog_type=ST_CONTACT_LIST relationmgr=yes maintainer=kristo

@default table=objects
@default group=general


@groupinfo contact_list caption="Aadressiraamat" submit=no

@property contact_list_toolbar type=toolbar group=contact_list no_caption=1
@property Aadressiraamatu toolbar

@property contact_list type=table group=contact_list no_caption=1
@caption Aadressiraamat

@groupinfo test caption="test"

@property new_name type=textbox group=test
@caption Nimi

@property new_mail type=textbox group=test
@caption E-mail


//@groupinfo search caption="Otsing"

//@property search_form type=text group=search
//@caption Otsinguvorm


//@groupinfo addnew caption="Lisa uus"

//@property fake type=text group=addnew
//@caption asd


@reltype LIST_OWNER value=1 clid=CL_USER
@caption Aadressiraamatu omanik

@reltype ADDED_PERSON value=2 clid=CL_CRM_PERSON
@caption Lisatud aadress

@reltype LIST_PROFILE_SEARCH value=3 cl=CL_CB_SEARCH
@caption Profiilide otsing

*/

class contact_list extends class_base
{
	function contact_list()
	{
		$this->init(array(
			"tpldir" => "applications/quickmessage/",
			"clid" => CL_CONTACT_LIST
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "contact_list_toolbar":
				$tb = & $prop["vcl_inst"];
				
				$tb->add_button(array(
            		"name" => "add",
            		"tooltip" => t("Lisa aadressiraamatusse"),
            		"img" => "new.gif",
            		"url" => $this->mk_my_orb(
						"change", array("group" => "addnew", "id" => $arr["obj_inst"]->id()), CL_CONTACT_LIST),
        		));
				
				$tb->add_separator();
				$tb->add_button(array(
					"name" => "search",
					"tooltip" => t("Otsi kontakte"),
            		"img" => "search.gif",
            		"action" => "",
        		));
				$tb->add_separator();
				
				$tb->add_button(array(
            		"name" => "delete",
            		"tooltip" => t("Kustuta kontakte"),
            		"img" => "delete.gif",
            		"action" => "delete",
					"confirm" => t("Oled kindel, et tahad valitud eemaldada?"),
        		));
				break;
			case "contact_list":

				$prop["vcl_inst"]->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
				));
				$prop["vcl_inst"]->define_field(array(
					"name" => "email",
					"caption" => t("E-Mail"),
				));
				
				$adds = $this->get_addresses($arr["obj_inst"]->id());
				foreach($adds as $add)
				{
					$prop["vcl_inst"]->define_data(array(
						"name" => $add["name"],
						"email" => $add["mail"],
					));
				}
				break;
		};
		return $retval;
	}
	
	/**
		@param oid required type=int
		messenger's id
		@comment
		finds contact_lists connected to given messenger
		@returns
		array of connections from messenger to any contact list
	**/
	function get_contact_lists_for_messenger($arr)
	{
		$conn = new connection();
		$conns = $conn->find(array(
			"from" => $arr,
			"to.class_id" => CL_CONTACT_LIST,
		));
		foreach($conns as $con)
			$ret[] = $con["to"];
		return count($ret)?$ret:false;
	}

	/**
		@attrib api=1

		@comment
		finds email objects connected to that contact list
		@returns
		array(
			email,
			name,
		)
	**/
	function get_addresses($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_ML_MEMBER,
			"parent"=> $arr,
		));
		foreach($ol->arr() as $oid => $el)
		{
			$obj = new object($oid);
			$tmp["name"] = $obj->prop("name");
			$tmp["mail"] = $obj->prop("mail");
			$ret[] = $tmp;
		}
		return $ret;
	}

	/**	
		@attrib name=delete
		
		@param id required type=int acl=view
		@param group optional
		@param sel required
	**/
	function delete($arr)
	{
		$obj = obj($arr["id"]);
		if(is_array($arr["sel"]))
		{
			foreach($arr["sel"] as $id)
			{
				$obj->disconnect(array(
					"from" => $id,
					"reltype" => "RELTYPE_ADDED_PERSON",
					"errors" => false,
				));
			}
		}
		return html::get_change_url($arr["id"], array("group" =>  $arr["group"]));
	}
	
	/**	
		@attrib name=show_list
		
		@param id required type=int acl=view
	**/
	function show_list($arr)
	{
		$this->read_template("show_list.tpl");
		echo dbg::process_backtrace(debug_backtrace());
		return $this->parse();
	}
	
	
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		if(strlen($prop["value"]));
		//arr($prop);
		switch($prop["name"])
		{
		}
		return $retval;
	}

	function callback_pre_save($arr)
	{
		$request = $arr["request"];
		if(strlen($request["new_mail"]))
		{
			/*
				siia peaks nyyd see tsekk tulema kas messengeriga on contact_list yhendatud, kui pole siis tuleb tekitada... vist?:S
			*/
			$mail = new object();
			$mail->set_parent($arr["id"]);
			$mail->set_class_id(CL_ML_MEMBER);
			$mail->set_prop("name", $request["new_name"]);
			$mail->set_prop("mail", $request["new_mail"]);
			$mail->save_new();
		}
	}		
	
}
?>
