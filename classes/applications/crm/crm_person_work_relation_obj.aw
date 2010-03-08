<?php
class crm_person_work_relation_obj extends _int_object
{
	/** sets mail address to work relation
		@attrib api=1 params=pos
		@param mail required type=string
	**/
	public function set_mail($mail)
	{
		$o = new object();
		$o->set_parent($this->id());
		$o->set_class_id(CL_ML_MEMBER);
		$o->set_name($mail);
		$o->set_prop("mail" , $mail);
		$o->save();

		$conns = $this->connections_from(array("type" => "RELTYPE_EMAIL"));
		foreach($conns as $conn)
		{
			$conn->delete();
		}
		$this->connect(array("to" =>$o->id(), "type" => "RELTYPE_EMAIL"));
		return $o->id();
	}

	/** sets phone to work relation
		@attrib api=1 params=pos
		@param phone required type=string
	**/
	public function set_phone($phone)
	{
		$o = new object();
		$o->set_parent($this->id());
		$o->set_class_id(CL_CRM_PHONE);
		$o->set_name($phone);
		$o->save();

		$conns = $this->connections_from(array("type" => "RELTYPE_PHONE"));
		foreach($conns as $conn)
		{
			$conn->delete();
		}
		//mis kuradi jama see on - m6nikord ei saa just tehtud objekti id'd k2tte
		if($o->id())
		{
			$this->connect(array("to" =>$o->id(), "type" => "RELTYPE_PHONE"));
		}
		return $o->id();
	}

	/** finishes current work relation
		@attrib api=1
	**/
	public function finish()
	{
		$this->set_prop("end" , time());
		$this->save();
	}
}
?>
