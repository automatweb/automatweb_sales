<?php

class orders_form_obj extends _int_object
{
	const CLID = 841;


	public function send_confirm_mail($order)
	{
		$mail_data = array(
			"subject"  => $this->prop("mail_subject"),
			"from_name" => $this->prop("mail_from"),
			"from_address" => $this->prop("mail_from_address"),
			"template" => "orders_form_confirm_mail.tpl"
		);
		$this->oc = obj($this->prop("order_center"));
		$this->oc->send_confirm_mail($order, $mail_data);
	}

}

?>
