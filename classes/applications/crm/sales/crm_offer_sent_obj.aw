<?php

class crm_offer_sent_obj extends _int_object
{
	/**
		@attrib api=1
	**/
	public function get_confirmation_url()
	{
		return $this->instance()->mk_my_orb("confirm", array("id" => $this->prop("offer")), CL_CRM_OFFER);
	}
}

?>