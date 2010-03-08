<?php
/*
@classinfo  maintainer=kristo
*/
class localetest extends testrunner
{
	function localetest()
	{
		$this->init("");
	}

	function setup()
	{
		$this->lc_inst = @get_instance("core/locale/lt/date");
	}

	/** Does the date locale class exists for this language?
	**/
	function test_class_exists($arr)
	{
		return $this->assert_test(is_object($this->lc_inst),"Pole sellist klassi");
	}

	/** Does the date class have a get_lc_date method?
	**/
	function test_lc_date_exists($arr)
	{
		return $this->assert_test(method_exists($this->lc_inst,"get_lc_date"),"Pole sellist meetodit");
	}
	
	/** Does the date class have a get_lc_weekday method?
	**/
	function test_lc_weekday_exists($arr)
	{
		return $this->assert_test(method_exists($this->lc_inst,"get_lc_weekday"),"Pole sellist meetodit");
	}
	
	/** Does the date class have the get_lc_month method?
	**/
	function test_lc_month_exists($arr)
	{
		return $this->assert_test(method_exists($this->lc_inst,"get_lc_month"),"Pole sellist meetodit");
	}

};
?>
