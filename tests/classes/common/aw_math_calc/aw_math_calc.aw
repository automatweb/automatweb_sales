<?php

class object_data_list_test extends UnitTestCase
{
	function object_data_list_test($name)
	{
		 $this->UnitTestCase($name);
	}

	/**
	echo aw_math_calc::string2float("$35,234.43")."\n";
	echo "35234.43 - 1\n\n";
	**/
	function test_string2float_dollar_mark_thousand_separator()
	{
		$this->assertTrue(aw_math_calc::string2float("$35,234.43") == (float) "35234.43");
	}

	/**	
	echo aw_math_calc::string2float("35,234.43")."\n";
	echo "35234.43 - 2\n\n";
	**/
	function test_string2float_1()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35,2,3,4.43")."\n";
	echo "35234.43 - 2b\n\n";
	**/
	function test_string2float_2()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35234.43")."\n";
	echo "35234.43 - 3\n\n";
	**/
	function test_string2float_3()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35234,43")."\n";
	echo "35234.43 - 4\n\n";
	**/
	function test_string2float_4()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35 234.43")."\n";
	echo "35234.43 - 5\n\n";
	**/
	function test_string2float_5()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35.234,43")."\n";
	echo "35234.43 - 6\n\n";
	**/
	function test_string2float_6()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35.23.4,43")."\n";
	echo "35234.43 - 6b\n\n";
	**/
	function test_string2float_7()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35,234,43")."\n";
	echo "35.234 - 7\n\n";
	**/
	function test_string2float_8()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35.234.43")."\n";
	echo "35.234 - 8\n\n";
	**/
	function test_string2float_9()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35.234, 43.4")."\n";
	echo "35.234 - 9\n\n";
	**/
	function test_string2float_10()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35asdf234,43asdf")."\n";
	echo "35234.43 - 10\n\n";
	**/
	function test_string2float_11()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35asdf234.43asdf")."\n";
	echo "35234.43 - 11\n\n";
	**/
	function test_string2float_12()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("adf35234.43asdf")."\n";
	echo "35234.43 - 12\n\n";
	**/
	function test_string2float_13()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("adf35234,43asdf")."\n";
	echo "35234.43 - 13\n\n";
	**/
	function test_string2float_14()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("adf35234,43afcc.ad")."\n";
	echo "35234.43 - 14\n\n";
	**/
	function test_string2float_15()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("a,df35234,43afcc.ad")."\n";
	echo "35234.43 - 15\n\n";
	**/
	function test_string2float_16()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("a,d,f35234,43af,,,cc.ad")."\n";
	echo "35234.43 - 16\n\n";
	**/
	function test_string2float_17()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("a,d,f35234.43af,,,cc.ad")."\n";
	echo "35234.43 - 17\n\n";
	**/
	function test_string2float_18()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("ac,df35234.43afcc.a,d")."\n";
	echo "35234.43 - 18\n\n";
	**/
	function test_string2float_19()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("35234")."\n";
	echo "35234 - 19\n\n";
	**/
	function test_string2float_20()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float(".45")."\n";
	echo "0.45 - 20\n\n";
	**/
	function test_string2float_21()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float(",45")."\n";
	echo "0.45 - 21\n\n";
	**/
	function test_string2float_22()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("'',45")."\n";
	echo "0.45 - 22\n\n";
	**/
	function test_string2float_23()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float(".45'")."\n";
	echo "0.45 - 23\n\n";
	**/
	function test_string2float_24()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("45,'")."\n";
	echo "45 - 1\n\n";
	**/
	function test_string2float_25()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}

	/**	
	echo aw_math_calc::string2float("45.'")."\n";
	echo "45 - 1\n\n";
	**/
	function test_string2float_26()
	{
		return;
		//$this->assertTrue(aw_math_calc::string2float("$35,234.43") == float("35234.43"));
	}
}