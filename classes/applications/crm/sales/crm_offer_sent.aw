<?php
/*
@classinfo syslog_type=ST_CRM_OFFER_SENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_offer_sent master_index=brother_of master_table=objects index=aw_oid

@default group=general

@default table=aw_crm_offer_sent

	@property offer type=hidden field=aw_offer
	@caption Pakkumus


@default table=messages

	@property edit_toolbar type=toolbar store=no no_caption=1 form=showmsg
	@caption Kirja redigeerimise toolbar

	@property uidl type=hidden
	@caption UIDL

	@property mfrom_name type=hidden table=objects field=meta method=serialize
	@caption Kellelt nimi

	@property mfrom type=relpicker reltype=RELTYPE_MAIL_ADDRESS no_sel=1
	@caption Kellelt

	@property mto type=textbox size=80
	@caption Kellele

	@property mto_relpicker type=relpicker reltype=RELTYPE_TO_MAIL_ADDRESS multiple=1 store=connect
	@caption Kellele

	@property cc type=textbox field=mtargets1 size=80
	@caption Koopia

	@property bcc type=textbox field=mtargets2 size=80
	@caption Pimekoopia

	@property name type=textbox size=80 table=objects
	@caption Teema

	property date type=text store=no
	caption Kuup&auml;ev

	@property customer type=relpicker reltype=RELTYPE_CUSTOMER multiple=1 table=objects field=meta method=serialize
	@caption Klient

	@property project type=relpicker reltype=RELTYPE_PROJECT table=objects field=meta method=serialize
	@caption Projekt

	@property html_mail type=checkbox ch_value=1 field=type method=bitmask ch_value=1024
	@caption HTML kiri

	@property add_contacts type=checkbox ch_value=1 field=meta method=serialize table=objects
	@caption Lisa kontaktandmed

	@property message type=textbox field=meta method=serialize table=objects
	@caption Sisu

	@property attachments type=relmanager table=objects field=meta method=serialize reltype=RELTYPE_ATTACHMENT props=comment,file chooser=no new_items=5
	@caption Manused

	property send type=submit value=Saada store=no
	caption Saada

	property aliasmgr type=aliasmgr store=no
	caption Aliased

	@property msgrid type=hidden store=no form=all
	@caption Msgrid

	@property msgid type=hidden store=no form=all
	@caption Msgid

	@property mailbox type=hidden store=no form=all
	@caption Mailbox

	@property cb_part type=hidden store=no form=all
	@caption Cb part

	@groupinfo general caption="&Uuml;ldine" submit=no
	@groupinfo add caption="Lisa"

	@tableinfo messages index=id master_table=objects master_index=oid

	/@property view_toolbar type=toolbar store=no no_caption=1 form=showmsg
	/@caption Kirja vaatamise toolbar

	@property msg_headers type=text store=no form=showmsg no_caption=1
	@caption Kirja p&auml;ised

	@property msg_content type=text store=no form=showmsg no_caption=1
	@caption Kirja sisu

	@property msg_contener_title type=textbox field=meta method=serialize table=objects group=add
	@caption Konteineri pealkiri

	@property msg_contener_content type=textarea field=meta method=serialize table=objects group=add
	@caption Konteineri sisu

	@property msg_attachments type=text store=no form=showmsg no_caption=1
	@caption Manused

	@forminfo showmsg onload=load_remote_message
	@forminfo showheaders onload=load_remote_message

	@reltype ATTACHMENT value=1 clid=CL_FILE
	@caption Manus

	@reltype MAIL_ADDRESS value=2 clid=CL_ML_MEMBER
	@caption Meiliaadress

	@reltype RELTYPE_REGISTER_DATA value=3 clid=CL_REGISTER_DATA
	@caption Registri andmed

	@reltype CUSTOMER value=3 clid=CL_CRM_COMPANY
	@caption Klient

	@reltype PROJECT value=4 clid=CL_PROJECT
	@caption Projekt

	@reltype TO_MAIL_ADDRESS value=4 clid=CL_ML_MEMBER
	@caption Meiliaadress (kellele)

*/

class crm_offer_sent extends mail_message
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer_sent",
			"clid" => CL_CRM_OFFER_SENT
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_offer_sent" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_offer_sent` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "aw_offer":
						$this->db_add_col($table, array(
							"name" => $field,
							"type" => "int"
						));
						$r = true;
						break;
				}
			}
		}
		elseif ("messages" === $table)
		{
			$i = new mail_message();
			$r = $i->do_db_upgrade($table, $field, $query, $error);
		}

		return $r;
	}
}

?>
