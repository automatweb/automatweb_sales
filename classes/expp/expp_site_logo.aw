<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_site_logo.aw,v 1.6 2008/02/17 21:13:01 kristo Exp $
// expp_site_logo.aw - Expp site_logo 
/*

@classinfo syslog_type=ST_EXPP_SITE_LOGO relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_site_logo extends class_base implements main_subtemplate_handler
{

	var $ch;

	function expp_site_logo() 	{
		$this->init(array(
			"tpldir" => "automatweb/menuedit",
			"clid" => CL_EXPP_SITE_LOGO
		));
//		$this->ch = get_instance("cache");
	}

	function show($arr) {
		$retHTML = '';
		if( isset( $GLOBALS['expp_logo'] ) && !empty($GLOBALS['expp_logo'])) {
/*
			$retHTML = $this->ch->file_get( $GLOBALS['expp_site'] );
			if( empty( $retHTML )) return $retHTML;
			$this->read_template("main.tpl");
			$this->vars( array(
				'LOGO' => $retHTML,
			));
*/
			$ol = new object_list(array(
				"class_id" => CL_EXPP_JOURNAL_MANAGEMENT,
				"code" => $GLOBALS['expp_logo'],
			));
			
			if ($ol->count() > 0)
			{
				$o = $ol->begin();
				$image_obj = $o->get_first_obj_by_reltype("RELTYPE_DESIGN_IMAGE");
				if (empty($image_obj))
				{
					// organisations_logo_id should be accessible directly from
					// management objects metadata
					$organisation_logo_id = $o->meta("organisation_logo_id");
					if ($this->can("view", $organisation_logo_id))
					{
						$image_obj = new object($organisation_logo_id);
					}
				
				/*
					$org_obj = $o->get_first_obj_by_reltype("RELTYPE_ORGANISATION");
					if (!empty($org_obj))
					{
						$image_obj = $org_obj->get_first_obj_by_reltype("RELTYPE_LOGO_IMAGE");
					}
				*/
				}
				if (!empty($image_obj))
				{
					$image_inst = get_instance(CL_IMAGE);
					$image_tag = $image_inst->make_img_tag_wl($image_obj->id());
					$this->read_template("main.tpl");
					$this->vars(array(
						"LOGO" => $image_tag,
					));
					$retHTML = $this->parse( 'VAIKE_LOGO' );

				}
				
			}
			
		}
		return $retHTML;
	}

	function on_get_subtemplate_content($arr) {
		$arr["inst"]->vars(array(
			"VAIKE_LOGO" => $this->show( $arr ),
		));
	}
	
	function register( $in ) {
		$GLOBALS['expp_logo'] = $in;
	}
}
?>
