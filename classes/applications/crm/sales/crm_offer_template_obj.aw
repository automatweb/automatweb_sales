<?php

class crm_offer_template_obj extends crm_offer_obj
{
	const CLID = 1765;

	/**	Creates a new CL_CRM_OFFER_TEMPLATE object from the given template and returns the newly created object
		@attrib api=1 params=pos
		@param parent required
			Parent the newly created offer will be saved under.
		@returns CL_CRM_OFFER_TEMPLATE
		@errors Throws awex_crm_offer_template if given template is not saved
	**/
	public function create_offer_from_template($parent)
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer_template("Offer template must be saved before a new offer can be created from it!");
		}

		$offer = $this->duplicate($parent, crm_offer_obj::CLID);
		$offer->set_prop("template", $this->id());
		$offer->save();

		return $offer;
	}
}


/** Generic crm_offer_template exception **/
class awex_crm_offer_template extends awex_crm_offer {}
