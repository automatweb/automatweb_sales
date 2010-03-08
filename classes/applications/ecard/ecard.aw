<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/ecard/ecard.aw,v 1.4 2007/12/06 14:33:28 kristo Exp $
// ecard.aw - E-kaart 
// Sort of for internal use. Go see ecard_manager
/*

@classinfo syslog_type=ST_ECARD relationmgr=yes no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@property comment type=textarea field=comment
@caption Tekst kaardil

@default method=serialize
@default field=meta

@property from_name type=textbox
@caption Saatja nimi

@property from_mail type=textbox
@caption Saatja aadress

@property to_name type=textbox
@caption Saaja nimi

@property to_mail type=textbox
@caption Saaja aadress

@property senddate type=date_select
@caption Saatmise kuupäev

@groupinfo image caption="Pilt"
@default group=image

	@property image type=releditor reltype=RELTYPE_IMAGE mode=form rel_id=first props=file
	@caption Pilt
	
@groupinfo conf caption="Seaded"
@default group=conf

	@property position type=chooser orient=vertical
	@caption Paigutus lehel

	@property hash type=text
	@caption Kood kaardi nägemiseks

	@property spy type=checkbox field=flags method=bitmask ch_value=16
	@caption Vaatamisel saada teade


@reltype IMAGE value=1 clid=CL_IMAGE
@caption Piltide kataloog

*/

class ecard extends class_base
{
	function ecard()
	{
		$this->init(array(
			"clid" => CL_ECARD
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case 'position':
				$prop["options"] = array(
					1 => t("Tekst pildi all"),
					2 => t("Tekst pildi kõrval"),
				);
			break;
		};
		return $retval;
	}
}
?>
