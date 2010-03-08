<?php
// $Header: /home/cvs/automatweb_dev/classes/core/dbg/cookiemonster.aw,v 1.3 2008/01/31 13:53:03 kristo Exp $
// a class to aid in debugging - you can set cookies in your browser with this

/*
@classinfo  maintainer=kristo

@default group=general
@default form=monster

@property cookietable type=table
@caption Olemasolevad cookied

@property new_name type=textbox 
@caption Uue cookie nimi

@property new_value type=textbox 
@caption Uue cookie väärtus

@forminfo monster onsubmit=munch

*/

class cookiemonster extends class_base
{
	function cookiemonster()
	{
		$this->init();
	}

	/** generates a list of cookies in the user's browser 
		
		@attrib name=list params=name default="1"
		
	**/
	function gen_list($arr)
	{
		$arr["form"] = "monster";
		return $this->change($arr);
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		switch($data["name"])
		{
			case "cookietable":
				$t = &$data["vcl_inst"];

				$t->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
					"sortable" => 1,
				));

				$t->define_field(array(
					"name" => "value",
					"caption" => t("Väärtus"),
					"sortable" => 1,
				));

				$t->define_chooser(array(
					"name" => "del",
					"field" => "name",
				));

				foreach($_COOKIE as $k => $v)
				{
					$t->define_data(array(
						"name" => $k,
						"value" => html::textbox(array(
							"name" => "val[$k]",
							"value" => $v,
						)),
					));


				};
				break;
		};
		return PROP_OK;
	}

	/** saves changes 
		
		@attrib name=munch params=name 

	**/
	function munch($arr)
	{
		extract($arr);

		$domain = substr($this->cfg["baseurl"],strlen("http://"));
		if (is_array($del))
		{
			foreach($del as $nm => $ddd)
			{
				setcookie($nm,"",time(),"/",$domain,0);
			}
		}

		if (is_array($val))
		{
			foreach($val as $nm => $vl)
			{
				if ($_COOKIE[$nm] != $vl)
				{
					setcookie($nm,$vl,time()+3600*24*100,"/",$domain,0);
				}
			}
		}

		if ($new_name != "" && $new_value != "")
		{
			setcookie($new_name,$new_value,time()+3600*24*100,"/",$domain,0);
		}

		return $this->mk_my_orb("list");
	}

	function session_show()
	{
		foreach($_SESSION as $k => $v)
		{
			echo "$k => $v <br>";
		}
		die();
	}

	function session_show_arr()
	{
		echo dbg::dump($_SESSION);
		die();
	}
}
?>
