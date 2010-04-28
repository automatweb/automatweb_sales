<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_special_offer.aw,v 1.5 2008/01/31 13:54:16 kristo Exp $
// crm_special_offer.aw - Organisatsiooni eripakkumine
// Not related to crm_offer
/*

@classinfo syslog_type=ST_CRM_SPECIAL_OFFER relationmgr=yes maintainer=markop

@default table=objects


@default group=general

@property comment type=textarea field=comment
@caption Kirjeldus

@property status type=status default=2
@caption Aktiivne

@property valid_from type=datetime_select field=meta method=serialize
@caption Kehtivuse algus

@property valid_to type=datetime_select field=meta method=serialize
@caption Kehtivuse lõpp



@groupinfo images caption="Pildid" submit=no
@default group=images

@property images type=releditor reltype=RELTYPE_IMAGE field=meta method=serialize mode=manager props=name,ord,status,file,file2,new_w,new_h,new_w_big,new_h_big,comment table_fields=name,ord table_edit_fields=ord
@caption Pildid

@groupinfo products caption="Tooted" submit=no
@default group=products

// @property products_caption type=text
// @caption Tooted

@property products type=releditor reltype=RELTYPE_SHOP_PRODUCT field=meta method=serialize mode=manager props=name,status,price,must_order_num table_fields=name direct_links=1
@caption Tooted

// @property packets_caption type=text
// @caption Toote paketid

@property packets type=releditor reltype=RELTYPE_SHOP_PACKET field=meta method=serialize mode=manager props=name,price,separate_items table_fields=name direct_links=1
@caption Toote paketid



@reltype IMAGE value=1 clid=CL_IMAGE
@caption Pilt

@reltype SHOP_PRODUCT value=2 clid=CL_SHOP_PRODUCT
@caption Lao toode

@reltype SHOP_PACKET value=3 clid=CL_SHOP_PACKET
@caption Lao pakett

*/

class crm_special_offer extends class_base
{
	const AW_CLID = 994;

	function crm_special_offer()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "crm/crm_special_offer",
			"clid" => CL_CRM_SPECIAL_OFFER
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

		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
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
		// Find company ID
		$conns = $ob->connections_to(array(
			'from.class_id' => CL_CRM_COMPANY,
		));
		$org_id = null;
		if (count($conns))
		{
			$conn = $conns[0];
			$org_id = $conn->conn['from'];
		}
		$this->read_template("show.tpl");

		// Images
		$images_html = "";
		if (empty($arr['short']) OR true)
		{
			$conns = $ob->connections_from(array(
				'type' => 'RELTYPE_IMAGE',
			));
			$inst_img = get_instance(CL_IMAGE);
			$images = array();
			foreach ($conns as $conn)
			{
				$image = $conn->to();
				if ($image->prop('status') != STAT_ACTIVE)
				{
					continue;
				}
				$tmp = $inst_img->parse_alias(array(
					'alias' => array(
						'target' => $image->id(),
					),
				));
				$images[] = $tmp['replacement']; // No, replacement is not a logical name in this context. However, it works!
			}
			$images_html = join('<br><br>', $images);
		}
		$this->vars(array(
			"images" => $images_html,
			"id" => $arr['id'],
			"name" => $ob->prop("name"),
			"desc" => $ob->comment(),
			"org_id" => $org_id,
			"url" => '/org?org=',
			"txt_orglehele" => t("Asutuse juurde"),
		));
		if (!empty($arr['short']))
		{
			return $this->parse('short');
		}
		else
		{
			return $this->parse('normal');
		}
	}
}
?>
