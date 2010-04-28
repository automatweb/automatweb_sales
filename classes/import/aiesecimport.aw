<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/import/aiesecimport.aw,v 1.6 2009/01/16 11:37:47 kristo Exp $
// aiesecimport.aw - Aiesec import 
/*

@classinfo syslog_type=ST_AIESECIMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@default table=objects
@default group=general

@property import_persons_status type=hidden  no_caption=1

@property importfolder type=relpicker reltype=RELTYPE_IMPORTFOLDER field=meta method=serialize
@caption Impordikaust

@property do_import type=checkbox store=no ch_value=1
@caption Impordi mind

@groupinfo preview caption="Preview"
@default group=preview

	@groupinfo preview_companies caption="Organisatsioonid" parent=preview
	@default group=preview_companies

		@property preview_companies_count type=text store=no
		@caption Andmebaasis organisatsioone
	
		@property preview_companies_table type=table store=no no_caption=1
		
	@groupinfo preview_persons caption="Isikud" parent=preview
	@default group=preview_persons

		@property preview_persons_count type=text store=no
		@caption Andmebaasis isikuid
	
		@property preview_persons_table type=table store=no no_caption=1
		
	@groupinfo preview_positions caption="Ametid" parent=preview
	@default group=preview_positions
	
		@property preview_positions_count_before type=text store=no
		@caption Esialgses db's ameteid kokku
		
		@property preview_positions_count_after type=text store=no
		@caption Unikaalseid (aw'sse imporditavaid)

		@property preview_positions_table type=table store=no no_caption=1

@groupinfo debug caption="Debug"
@default group=debug

	@property debug_persons_table type=table store=no no_caption=1
	
@reltype IMPORTFOLDER value=1 clid=CL_MENU
@caption Impordi kaust

*/ 

class aiesecimport extends class_base
{
	const AW_CLID = 1357;

	function aiesecimport()
	{
		$this->init(array(
			"tpldir" => "import/aiesecimport",
			"clid" => CL_AIESECIMPORT
		));
		$this->db_prefix = "aie_"; 
		$this->aCities = array("Jõgeva" => false,
										"Võru" => false, 
										"Pärnu" => false, 
										"Viljandi" => false, 
										"Haapsalu" => false, 
										"Kuressaare" => false, 
										"Põlva" => false, 
										"Valga" => false, 
										"Jõhvi" => false, 
										"Kohtla-Järve" => false, 
										"Sillamäe" => false, 
										"Tartu" => false, 
										"Tallinn" => false, 
										"Narva" => false, 
										"Rakvere" => false);
										
		$this->aCounties = array("Harju maakond" => false,
										"Hiiu maakond" => false, 
										"Ida-Viru maakond" => false, 
										"Jõgeva maakond" => false, 
										"Järva maakond" => false, 
										"Lääne maakond" => false, 
										"Lääne-Viru maakond" => false, 
										"Põlva maakond" => false, 
										"Pärnu maakond" => false, 
										"Rapla maakond" => false, 
										"Saare maakond" => false, 
										"Tartu maakond" => false, 
										"Valga maakond" => false, 
										"Viljandi maakond" => false, 
										"Võru maakond" => false);
										
		$this->aCountries = array("Eesti" => false);
		
		$this->aLocationRelations = array(
								"Jõgeva" => array("county" => "Jõgeva maakond", "country"=>"Eesti"),
								"Võru"  => array("county" => "Võru maakond", "country"=>"Eesti"),
								"Pärnu"  => array("county" => "Pärnu maakond", "country"=>"Eesti"),
								"Viljandi"  => array("county" => "Viljandi maakond", "country"=>"Eesti"),
								"Haapsalu"  => array("county" => "Lääne maakond", "country"=>"Eesti"),
								"Kuressaare"  => array("county" => "Saare maakond", "country"=>"Eesti"),
								"Põlva" =>  array("county" => "Põlva maakond", "country"=>"Eesti"),
								"Valga" =>  array("county" => "Valga maakond", "country"=>"Eesti"),
								"Jõhvi" =>  array("county" => "Ida-Viru maakond", "country"=>"Eesti"),
								"Kohtla-Järve"  => array("county" => "Ida-Viru maakond", "country"=>"Eesti"),
								"Sillamäe"  => array("county" => "Ida-Viru maakond", "country"=>"Eesti"),
								"Tartu"  => array("county" => "Tartu maakond", "country"=>"Eesti"),
								"Tallinn"  => array("county" => "Harju maakond", "country"=>"Eesti"),
								"Narva"  => array("county" => "Ida-Viru maakond", "country"=>"Eesti"),
								"Rakvere"  => array("county" => "Lääne-Viru maakond", "country"=>"Eesti"),
		);
		
		aw_set_exec_time(AW_LONG_PROCESS);
	}
	
	
	/**
	@attrib name=do_import_finish
	
	@param vars_ok required type=integer
	@param import_parent required type=integer
	
	@comment
		in order, it imports
		companies
		persons
		
	**/ 
	function do_import_finish($arr)
	{
		$this->set_parents($arr["import_parent"]);
		
		// just a check for myself cuz i have to make sure arr all ids are set in code..
		if ($arr["vars_ok"] != 1)
		{
			die ("var'id on paigas?");
		}
		
		$this->i_aiesec_in_estonia_id = 406423;
		
		$this->a_members_group["Aiesec alumni"]["rank_oid"] = 421101; 
		$this->a_members_group["Aiesec alumni"]["MC"] = 421100;
		$this->a_members_group["Aiesec alumni"]["MC Estonia"] = 421099;
		$this->a_members_group["Aiesec alumni"]["IG Pärnu"] = 421098;
		$this->a_members_group["Aiesec alumni"]["LC Universities of Tallinn"] = 421097;
		$this->a_members_group["Aiesec alumni"]["LC Tartu"] = 421096;
		$this->a_members_group["AIESECi liikmed"]["rank_oid"] = 421093; // rank id
		$this->a_members_group["AIESECi liikmed"]["MC"] = 421088;
		$this->a_members_group["AIESECi liikmed"]["MC Estonia"] = 421089;
		$this->a_members_group["AIESECi liikmed"]["IG Pärnu"] = 421090;
		$this->a_members_group["AIESECi liikmed"]["LC Universities of Tallinn"] = 421091;
		$this->a_members_group["AIESECi liikmed"]["LC Tartu"] = 421092;
		
		//$this->do_import_finish_get_members_and_alumni();
		$this->do_import_finish_fix_tasks();
		//$this->do_import_finish_fix_meetings();

		die("k6ik valmis");
	}
	
	function do_import_finish_get_members_and_alumni()
	{
		$q = "SELECT * FROM ".$this->db_prefix."members";
        $this->db_query($q);
		
		$k=$l=1;
		while ($w = $this->db_next() )
		{
			$ol = new object_list(array(
				"user1" => $w["i_id"],
				"class_id" => CL_CRM_PERSON,
			));
			if ($ol->count() == 1)
			{
				$i_person_oid = array_pop($ol->ids());
			}
			else
			{
				echo "Hoiatus. AW's ei ole isikut, kelle aie id on ".$w["i_id"]." Tõenäoliselt ei ole seda isikut ka aie baasis. Kontrollida võid <a href='http://db.aiesec.ee/db/inddetails.php?i_id=".$w["i_id"]."'>siit</a><br>\n";
			}
			
			if ($w["status"] == "member" && isset($i_person_oid))
			{
				$this->do_import_finish_set_member("AIESECi liikmed", $w["lc"], $i_person_oid, $w);
			}
			else if ($w["status"] == "alumni" && isset($i_person_oid))
			{
				$this->do_import_finish_set_member("Aiesec alumni", $w["lc"], $i_person_oid, $w);
			}
			
			if ($k%10==0)
			{
				echo "Liikmeid imporditud ".($l*10)."<br>\n";
				ob_flush();
				flush();
				$l++;
			}
			
			$k++;
		}
		$c = get_instance("cache");
		$c->full_flush();
		echo "kokku vaatasin l2bi "+($k-1) +" isikut";
		echo "cache puhas";
		ob_flush();
		flush();

	}
	
	function do_import_finish_set_member($s_type, $s_lc, $i_person_id, $w)
	{
		$s_lc = utf8_decode($s_lc);
		$i_since = $w["since"];
		$s_msn = trim($w["msn"]);
		
		$o_person = new object($i_person_id);
		
		$o_person->add_work_relation(array(
			"org" => $this->i_aiesec_in_estonia_id,
			"section" => $this->a_members_group[$s_type][$s_lc],
			"profession" => $this->a_members_group[$s_type]["rank_oid"],
		));
		
		if ($s_type == "AIESECi liikmed")
		{
			$s_workrelation_name = "Aiesec'i liige";
		}
		else
		{
			$s_workrelation_name = "Aiesec alumni";
		}
		
		if (strlen($i_since) == 4 )
		{
			$o_workrelation = new object(array(
				"name" => $s_workrelation_name,
				"parent" => $o_person->id(),
				"class_id" => CL_CRM_PERSON_WORK_RELATION,
			));
			$o_workrelation->set_class_id(CL_CRM_PERSON_WORK_RELATION);
			$o_workrelation->set_prop("start", mktime(0, 0, 0, 1, 1, $i_since));
			$o_workrelation->save();
			
			$o_workrelation ->connect(array(
				"to" => $this->i_aiesec_in_estonia_id,
				"type" => RELTYPE_ORG,
			));
			$o_workrelation->set_prop("org", $this->i_aiesec_in_estonia_id);
			
			$o_workrelation ->connect(array(
				"to" => $this->a_members_group[$s_type]["rank_oid"],
				"type" => RELTYPE_PROFESSION,
			));
			$o_workrelation->set_prop("profession", $this->a_members_group[$s_type]["rank_oid"]);
			$o_workrelation->save();
			
			$o_person -> connect(array(
				"to" => $o_workrelation->id(),
				"type" => RELTYPE_PREVIOUS_JOB,
			));
		}
		
		if ( strpos ( $s_msn, "@") > 0)
		{
			$o_person->set_prop("messenger", $s_msn);
			$o_person->save();
		}
		
		// and last but not least, new positions
		{
			$s_position_name = trim($w["pos"]);
			$ol_positions = new object_list(array(
					"name" => $s_position_name,
					"parent" => $this->positions_parent,
					"class_id" => CL_CRM_PROFESSION,
			));
			
			if ($ol_positions->count()==0)
			{
				$o_position = new object(array(
					"name" => $s_position_name,
					"parent" => $this->positions_parent,
					"class_id" => CL_CRM_PROFESSION,
				));
				$o_position ->save();
				
				$o_person -> connect(array(
					"to" => $o_position->id(),
					"type" => RELTYPE_RANK,
				));
				
				if ($o_person->prop("rank") == "")
				{
					$o_person->set_prop("rank", $o_position->id());
					$o_person->save();
				}
				
				echo "lisasin uue ameti ja seostasin selle ".$o_person->name()." isikuga<br>";
				ob_flush();
				flush();
			}
			else if ($ol_positions->count()==1)
			{
				$cons_ranks = $o_person->connections_from(array(
					"type" => RELTYPE_RANK,
				));
				
				$a_person_ranks = array();
				foreach($cons_ranks as $con)
				{
					$a_person_ranks[$con->conn["to.name"]] = $con->conn["to"];
				}
				
				if ( !array_key_exists( $s_position_name, $a_person_ranks))
				{
					$i_rank_id = $ol_positions->ids();	
					$o_person -> connect(array(
						"to" => $i_rank_id[0],
						"type" => RELTYPE_RANK,
					));
					
					if ($o_person->prop("rank") == "")
					{
						$o_person->set_prop("rank", $o_position->id());
						$o_person->save();
					}
					
					echo "amet oli juba olemas ja seostasin selle  ".$o_person->name()." isikuga<br>";
					ob_flush();
					flush();
				}
				
			}
			else if ($ol_positions->count()>1)
			{
				die("baasis esineb samanimelisi t88kohti - $s_position_name. l6petan t88 ");
			}
		}
		
	}
	
	function do_import_finish_fix_tasks()
	{
		$ol = new object_list(array(
				"parent" => $this->companies_parent,
				"class_id" => CL_CRM_COMPANY,
		));
		
		$k=$l=1;
		for ($o = $ol->begin(); !$ol->end(); $o =& $ol->next())
		{
			// move persons under tasks
				$cons_task = $o->connections_from(array(
					"type" => RELTYPE_TASK,
				));
				
				foreach($cons_task as $con)
				{
					$i_task_oid = $con->conn["to"];
					$o_task = new object($i_task_oid);
					
					// clients under tasks
					$cons_customers = $o_task->connections_from(array(
						"class" => CL_CRM_PERSON,
						"type" => RELTYPE_CUSTOMER,
					));
					
					// move persons to participants
					foreach($cons_customers as $cons_custom)
					{
						$i_customer_oid = $cons_custom->conn["to"];
						$o_person = new object($i_customer_oid);
						$o_person -> connect(array(
							"to" => $i_task_oid,
							"type" => RELTYPE_PERSON_TASK,
						));
						
						
						$cons_custom->delete();
						
						if ($k%50==0)
						{
							echo "Inimesi taskide all ümber tõstetud ".($l*50)."<br>\n";
							ob_flush();
							flush();
							$l++;
						}
						
						$k++;
						
					}
					
					
				}
		}
		echo "do_import_finish_fix_tasks ... done";
	}
	
	function do_import_finish_fix_meetings()
	{
		$ol = new object_list(array(
				"parent" => $this->companies_parent ,
				"class_id" => CL_CRM_COMPANY,
		));
		
		$k=$l=1;
		for ($o = $ol->begin(); !$ol->end(); $o =& $ol->next())
		{
				// move persons under meetings
				$cons_meetings = $o->connections_from(array(
					"type" => RELTYPE_KOHTUMINE,
				));
				
				foreach($cons_meetings as $con)
				{
				
					$i_meeting_oid = $con->conn["to"];
					$o_meeting = new object($i_meeting_oid);
					
					// clients under meetings
					$cons_customers = $o_meeting->connections_from(array(
						"class" => CL_CRM_PERSON,
						"type" => RELTYPE_CUSTOMER,
					));
					
					// move persons to participants
					foreach($cons_customers as $con_customer)
					{
						
						$i_customer_oid = $con_customer->conn["to"];
						$o_person = new object($i_customer_oid);
						
						$o_person -> connect(array(
							"to" => $i_meeting_oid,
							"type" => RELTYPE_PERSON_MEETING,
						));
						
						$con_customer->delete();
						
						if ($k%50==0)
						{
							echo "Inimesi miitingute all ümber tõstetud ".($l*50)."<br>\n";
							ob_flush();
							flush();
							$l++;
						}
						
						$k++;
					}
					
				}
		}
		$c = get_instance("cache");
		$c->full_flush();
		echo "cache puhas<br>";
		echo "do_import_finish_fix_meetings ... done<br>";
		ob_flush();
		flush();
	}
	
	/**
	@attrib name=do_import
	
	@param import_parent required type=integer
	
	@comment
		in order, it imports
		companies
		persons
		
	**/ 
	function do_import($arr)
	{
		aw_disable_acl();
		
		$this -> set_parents($arr["import_parent"]);
		
		$this->create_cities();
		$this->create_counties();
		$this->create_countries();
		
		$aPersons = $this->get_persons();
		$iPersonsCount = count($aPersons);
		
		$aCompanies = $this->get_companies();
		$iCompaniesCount = count($aCompanies);
		
		$aPositions = $this->get_positions();
		$iPositionsCount = count($aPositions);

		$aPhonecalls = $this->get_phonecalls();
		$iPhonecallsCount = count($aPhonecalls);

		$a_meetings = $this->get_meetings();
		$i_meetings_count = count($a_meetings);
		
		$a_contacts_other = $this->get_contacts_other();
		$i_contacts_other_count = count($a_contacts_other);
		
		$a_members = $this->get_aie_members();
		$i_members_count = count($a_members);

		if ($this->do_import_create_import_folders($arr["import_parent"]))
		{
			//$this->delete_objects($this->persons_parent);
			//$this->do_import_persons();
			//$this->delete_objects($this->companies_parent);
			//$this->do_import_companies();
			//$this->do_import_positions();
			//$this->do_import_person_company_connections();
			//$this->do_import_company_activities();
			
			//$this->remove_person_company_connections($list);
		}
		aw_restore_acl();


		
		echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Import status</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
	<script src="/js/ajaxgold.js"></script>
	
	<style>.hide {display: none;}</style>
	
	<script type="text/javascript">
	window.onload = function()
	{
		do_import();
	}
	
	function do_import()
	{
		iPersonsCount = '.$iPersonsCount.';
		iCompaniesCount = '.$iCompaniesCount.';
		iPositionsCount = '.$iPositionsCount.';
		iPhonecallsCount = '.$iPhonecallsCount.';
		i_contacts_other_count = '.$i_contacts_other_count.';
		i_meetings_count = '.$i_meetings_count.';
		i_members_count = '.$i_members_count.';
		
		
		//import_positions("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_positions&import_parent='.$arr["import_parent"].'");
		import_companies("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_companies&import_parent='.$arr["import_parent"].'");
		//import_persons("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_persons&import_parent='.$arr["import_parent"].'");
		//import_phonecalls("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_phonecalls&import_parent='.$arr["import_parent"].'");
		//import_meetings("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_meetings&import_parent='.$arr["import_parent"].'");
		//import_othercontacts("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_other_contact&import_parent='.$arr["import_parent"].'");
		//import_members_and_alumni("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_aiesec_members_and_alumni&import_parent='.$arr["import_parent"].'");
	}
	
	function import_positions(sUrl)
	{
		document.getElementById("import_positions").style.display = "block";
		getDataReturnText(sUrl, import_positions_callback);
	}
	
	function import_positions_callback(text)
	{
		eval(text);
		if (arr["sUrl"].length>0)
		{
			import_positions(arr["sUrl"]);
			el = document.getElementById("import_positions_percent");
			el.innerHTML = Math.round((arr["iImported"]/iPositionsCount)*100);

			el2 = document.getElementById("test");
			el2.innerHTML = "|"+text+"|"+"<br>"+el2.innerHTML;
		} else if (arr["sUrl"].length==0)
		{
			import_companies("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_companies&import_parent='.$arr["import_parent"].'");
		}
	}
	
	function import_companies(sUrl)
	{
		document.getElementById("import_companies").style.display = "block";
		getDataReturnText(sUrl, import_companies_callback);
	}
	
	function import_companies_callback(text)
	{
		eval(text);
		if (arr["sUrl"].length>0)
		{
			import_companies(arr["sUrl"]);
			el = document.getElementById("import_companies_percent");
			el.innerHTML = Math.round((arr["iImported"]/iCompaniesCount)*100);
		
			el2 = document.getElementById("test");
			el2.innerHTML = "|"+text+"|"+"<br>"+el2.innerHTML;
		} else if (arr["sUrl"].length==0)
		{
			import_persons("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_persons&import_parent='.$arr["import_parent"].'");
		}
	}
	
	function import_persons(sUrl)
	{
		document.getElementById("import_persons").style.display = "block";
		getDataReturnText(sUrl, import_persons_callback);
	}
	
	function import_persons_callback(text)
	{
		eval(text);
		if (arr["sUrl"].length>0)
		{
			import_persons(arr["sUrl"]);
			el = document.getElementById("import_persons_percent");
			el.innerHTML = Math.round((arr["iImported"]/iPersonsCount)*100);
		
			el2 = document.getElementById("test");
			el2.innerHTML = "|"+text+"|"+"<br>"+el2.innerHTML;
		} else if (arr["sUrl"].length==0)
		{
			import_phonecalls("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_phonecalls&import_parent='.$arr["import_parent"].'");
		}
	}
	
	function import_phonecalls(sUrl)
	{
		document.getElementById("import_phonecalls").style.display = "block";
		getDataReturnText(sUrl, import_phonecalls_callback);
	}
	
	function import_phonecalls_callback(text)
	{
		eval(text);
		if (arr["sUrl"].length>0)
		{
			import_phonecalls(arr["sUrl"]);
			el = document.getElementById("import_phonecalls_percent");
			el.innerHTML = Math.round((arr["iImported"]/iPhonecallsCount)*100);
			
			el2 = document.getElementById("test");
			el2.innerHTML = "|"+text+"|"+"<br>"+el2.innerHTML;
		}
		else if (arr["sUrl"].length==0)
		{
			import_meetings("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_meetings&import_parent='.$arr["import_parent"].'");
		}
	}
	
	function import_meetings(sUrl)
	{
		document.getElementById("import_meetings").style.display = "block";
		getDataReturnText(sUrl, import_meetings_callback);
	}
	
	function import_meetings_callback(text)
	{
		eval(text);
		if (arr["sUrl"].length>0)
		{
			import_meetings(arr["sUrl"]);
			el = document.getElementById("import_meetings_percent");
			el.innerHTML = Math.round((arr["iImported"]/i_meetings_count)*100);
			
			el2 = document.getElementById("test");
			el2.innerHTML = "|"+text+"|"+"<br>"+el2.innerHTML;
		}
		else if (arr["sUrl"].length==0)
		{
			import_othercontacts("http://hannes.dev.struktuur.ee/automatweb/orb.aw?class=aiesecimport&action=ajax_import_other_contact&import_parent='.$arr["import_parent"].'");
		}
	}

	
	function import_othercontacts(sUrl)
	{
		document.getElementById("import_othercontacts").style.display = "block";
		getDataReturnText(sUrl, import_othercontacts_callback);
	}
	
	function import_othercontacts_callback(text)
	{
		eval(text);
		if (arr["sUrl"].length>0)
		{
			import_othercontacts(arr["sUrl"]);
			el = document.getElementById("import_othercontacts_percent");
			el.innerHTML = Math.round((arr["iImported"]/i_contacts_other_count)*100);
			
			el2 = document.getElementById("test");
			el2.innerHTML = "|"+text+"|"+"<br>"+el2.innerHTML;
		}
	}
	
	function import_members_and_alumni(sUrl)
	{
		document.getElementById("import_members").style.display = "block";
		getDataReturnText(sUrl, import_members_and_alumni_callback);
	}
	
	function import_members_and_alumni_callback(text)
	{
		eval(text);
		if (arr["sUrl"].length>0)
		{
			import_members_and_alumni(arr["sUrl"]);
			el = document.getElementById("import_members_percent");
			el.innerHTML = Math.round((arr["iImported"]/i_members_count)*100);
			
			el2 = document.getElementById("test");
			el2.innerHTML = "|"+text+"|"+"<br>"+el2.innerHTML;
		}
	}
	
	
	
	
	</script> 
</head>

<body>

<div id="import_positions" class="hide">Loon ametite objekte <span id="import_positions_percent">0</span>%</div>
<div id="import_companies" class="hide">Loon organisatsioonide objekte <span id="import_companies_percent">0</span>%</div>
<div id="import_persons" class="hide">Loon isikute objekte <span id="import_persons_percent">0</span>%</div>
<div id="import_phonecalls" class="hide">Loon toimunud kontaktide objekte (telefonid) <span id="import_phonecalls_percent">0</span>%</div>
<div id="import_meetings" class="hide">Loon toimunud kontaktide objekte (miitingud) <span id="import_meetings_percent">0</span>%</div>
<div id="import_othercontacts" class="hide">Loon toimunud kontaktide objekte (ülejäänud) <span id="import_othercontacts_percent">0</span>%</div>
<div id="import_members" class="hide">Aisec members and alumni <span id="import_members_percent">0</span>%</div>



<div id="test"></div>

</body>
</html>
';
		die();
	}
	
	
	function do_import_company_activities()
	{
		$this->delete_objects($this->phonecalls_parent);
		
		// cache persons aie and theyr corresponding aw id's into array
		{
			$olPersons = new object_list(array(
					"parent" => $this->persons_parent,
					"class_id" => CL_CRM_PERSON,
			));
			$aPersons = array();
			for ($o = $olPersons->begin(); !$olPersons->end(); $o =& $olPersons->next())
			{
				$iOid = $o->id();
				$iAieId = $o->prop("user1");
				$aPersons[$iAieId] = array("oid" => $iOid);
			}
			unset($olPersons);
		}
		
		$list = new object_list(array(
			"parent" => $this->companies_parent,
			"class_id" => CL_CRM_COMPANY,
		));
		
		for ($o = $list->begin(); !$list->end(); $o =& $list->next())
		{
			$iCompanyId = $o->prop("userta1");
			$sQuery = "SELECT ".
							"a_id											AS contacterPersonId1, ".
							"2a_id											AS contacterPersonId2, ".
							"individualid 									AS contactedPersonId, ".
							"UNIX_TIMESTAMP(contactdate)		AS cdate, ".
							"contacttypeid, ".
							"longdesc, ".
							"reason ".
							"FROM ".$this->db_prefix."contacts ".
							"WHERE companyid = ".$iCompanyId;
			$this->db_query($sQuery);
			
			while ($line = $this->db_next() )
			{
				// import phonecalls
				if ($line["contacttypeid"]==1)
				{
					$oCall = new object(array(
							"name" => $this->get_first_words(utf8_decode($line["longdesc"]), 4),
							"parent" => $this->phonecalls_parent,
							"class_id" => CL_CRM_CALL
					));
					$oCall -> set_class_id(CL_CRM_CALL);
					$oCall -> set_prop("start1", $line["cdate"]);
					$oCall -> set_prop("end", $line["cdate"]);
					$oCall -> set_prop("is_done", 8);
					$oCall -> set_prop("content", utf8_decode($line["longdesc"]));
					$oCall -> save();
					$o -> connect(array(
								"to" => $oCall -> id(), 
								"type"=> RELTYPE_CALL,
							));
					$o -> save();
					
					if (strlen($line["contacterPersonId1"])>0)
					{
						$oPerson = new object($aPersons[$line["contacterPersonId1"]]["oid"]);
						$oPerson -> connect(array(
								"to" => $oCall->id(), 
								"type"=> RELTYPE_PERSON_CALL,
						));
					}

					if (strlen($line["contacterPersonId2"])>0)
					{
						$oPerson = new object($aPersons[$line["contacterPersonId2"]]["oid"]);
						$oPerson -> connect(array(
								"to" => $oCall->id(), 
								"type"=> RELTYPE_PERSON_CALL,
						));
					}
					
					if (strlen($line["contactedPersonId"])>0)
					{
						$oPerson = new object($aPersons[$line["contactedPersonId"]]["oid"]);
						$oPerson -> connect(array(
								"to" => $oCall->id(), 
								"type"=> RELTYPE_PERSON_CALL,
						));
					}
				}
				else if ($line["contacttypeid"]==2)
				{
					
				}
			}
		}
	}
	
	/**
	 *	This does every possible connections and objects concerning phonecalls
	 */
	function do_import_contacts_others_full($a_contacts, $i_contacts_count = false, $begin=false, $end=false )
	{
		if ($i_contacts_count <= $begin || $i_contacts_count < $end)
		{
			return false;
		}
		
		// cache persons aie and theyr corresponding aw id's into array
		{
			$olPersons = new object_list(array(
					"parent" => $this->persons_parent,
					"class_id" => CL_CRM_PERSON,
			));
			$aPersons = array();
			for ($o = $olPersons->begin(); !$olPersons->end(); $o =& $olPersons->next())
			{
				$iOid = $o->id();
				$iAieId = $o->prop("user1");
				$aPersons[$iAieId] = array("oid" => $iOid);
			}
			unset($olPersons);
		}
		
		// cache companies to array
		{
			$companies = new object_list(array(
					"parent" => $this->companies_parent,
					"class_id" => CL_CRM_COMPANY,
			));
			for ($o = $companies->begin(); !$companies->end(); $o =& $companies->next())
			{
				$iAieId = $o->prop("userta1");
				$aTmp = array(
						"oid" => $o->id(),
				);
				$aCompanies[$iAieId] = $aTmp;
			}
			unset($companies);
		}
		
		for ($i=$begin;$i<$end;$i++)
		{
			$iAieCompanyId = $a_contacts[$i]["companyid"];
			// there are entries in aie base that link to companies that do not exist
			if ($this->can("view",$aCompanies[$iAieCompanyId]["oid"]))
			{
				$oTask = new object(array(
							"name" => $this->get_first_words($a_contacts[$i]["longdesc"], 4),
							"parent" => $aCompanies[$iAieCompanyId]["oid"],
							"comment" => $a_contacts[$i]["reason"],
							"class_id" => CL_TASK
				));
				
				$oTask->set_class_id(CL_TASK);
				$oTask->set_prop("start1", $a_contacts[$i]["cdate"]);
				$oTask->set_prop("end", $a_contacts[$i]["cdate"]);
				$oTask->set_prop("deadline", $a_contacts[$i]["cdate"]);
				$oTask->set_prop("is_done", 8);
				$oTask->set_prop("content", $a_contacts[$i]["longdesc"]);
				$oTask->save();
				
				$oCompany = new object($aCompanies[$iAieCompanyId]["oid"]);
				$oCompany -> connect(array(
							"to" => $oTask->id(), 
							"type"=> RELTYPE_TASK,
				));
				$oCompany -> save();
				
				if (strlen($a_contacts[$i]["contacter1"])>0)
				{
					$iAieId = $a_contacts[$i]["contacter1"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oTask-> connect(array(
							"to" => $oPerson->id(),
							"type"=> RELTYPE_CUSTOMER,
					));
				}
					
				if (strlen($a_contacts[$i]["contacter2"])>0)
				{
					$iAieId = $a_contacts[$i]["contacter2"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oTask-> connect(array(
							"to" => $oPerson->id(),
							"type"=> RELTYPE_CUSTOMER,
					));
				
				}
				
				if (strlen($a_contacts[$i]["contacted"])>0)
				{
					$iAieId = $a_contacts[$i]["contacted"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oTask-> connect(array(
							"to" => $oPerson->id(),
							"type"=> RELTYPE_CUSTOMER,
					));
					
					$oTask -> connect(array(
								"to" => $aCompanies[$iAieCompanyId]["oid"],
								"type" => RELTYPE_CUSTOMER
					));
					$oTask->set_prop("customer", $aCompanies[$iAieCompanyId]["oid"]);
					$oTask->save();
				}
			}
		}

		return true;
	}
	
	/**
	 *	This does every possible connections and objects concerning meetings
	 */
	function do_import_meetings_full($a_meetings, $i_meetings_count = false, $begin=false, $end=false )
	{

		if ($i_meetings_count <= $begin || $i_meetings_count < $end)
		{
			return false;
		}
		
		// cache persons aie and theyr corresponding aw id's into array
		{
			$olPersons = new object_list(array(
					"parent" => $this->persons_parent,
					"class_id" => CL_CRM_PERSON,
			));
			$aPersons = array();
			for ($o = $olPersons->begin(); !$olPersons->end(); $o =& $olPersons->next())
			{
				$iOid = $o->id();
				$iAieId = $o->prop("user1");
				$aPersons[$iAieId] = array("oid" => $iOid);
			}
			unset($olPersons);
		}
		
		// cache companies to array
		{
			$companies = new object_list(array(
					"parent" => $this->companies_parent,
					"class_id" => CL_CRM_COMPANY,
			));
			for ($o = $companies->begin(); !$companies->end(); $o =& $companies->next())
			{
				$iAieId = $o->prop("userta1");
				$aTmp = array(
						"oid" => $o->id(),
				);
				$aCompanies[$iAieId] = $aTmp;
			}
			unset($companies);
		}
		
		for ($i=$begin;$i<$end;$i++)
		{
			$iAieCompanyId = $a_meetings[$i]["companyid"];
			
			// there are entries in aie base that link to companies that do not exist
			if ($this->can("view",$aCompanies[$iAieCompanyId]["oid"]))
			{
				$oMeeting = new object(array(
							"name" => $this->get_first_words($a_meetings[$i]["longdesc"], 4),
							"parent" => $iAieCompanyId,
							"comment" => $a_meetings[$i]["reason"],
							"class_id" => CL_CRM_MEETING
				));
				
				$oMeeting -> set_class_id(CL_CRM_MEETING);
				$oMeeting -> set_prop("start1", $a_meetings[$i]["cdate"]);
				$oMeeting -> set_prop("end", $a_meetings[$i]["cdate"]);
				$oMeeting -> set_prop("is_done", 8);
//				$oMeeting -> set_prop("summary", $a_meetings[$i]["longdesc"]);
				$oMeeting -> save();
				
				$oCompany = new object($aCompanies[$iAieCompanyId]["oid"]);
				$oCompany -> connect(array(
							"to" => $oMeeting -> id(), 
							"type"=> RELTYPE_KOHTUMINE,
				));
				
				$oCompany -> save();
				
				if (strlen($a_meetings[$i]["contacter1"])>0)
				{
					$iAieId = $a_meetings[$i]["contacter1"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oMeeting-> connect(array(
							"to" => $oPerson->id(),
							"type"=> RELTYPE_CUSTOMER,
					));
				}
					
				if (strlen($a_meetings[$i]["contacter2"])>0)
				{
					$iAieId = $a_meetings[$i]["contacter2"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oMeeting-> connect(array(
							"to" => $oPerson->id(),
							"type"=> RELTYPE_CUSTOMER,
					));
				
				}
				
				if (strlen($a_meetings[$i]["contacted"])>0)
				{
					$iAieId = $a_meetings[$i]["contacted"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oMeeting-> connect(array(
							"to" => $oPerson->id(),
							"type"=> RELTYPE_CUSTOMER,
					));
					
					$oMeeting -> connect(array(
								"to" => $aCompanies[$iAieCompanyId]["oid"],
								"type" => RELTYPE_CUSTOMER
					));
					$oMeeting->set_prop("customer", $aCompanies[$iAieCompanyId]["oid"]);
					$oMeeting->save();
				}
			}
		}

		return true;
	}
	
	
	/**
	 *	This does every possible connections and objects concerning phonecalls
	 */
	function do_import_phonecalls_full($aPhonecalls, $iPhonecallsCount = false, $begin=false, $end=false )
	{

		if ($iPhonecallsCount <= $begin || $iPhonecallsCount < $end)
		{
			return false;
		}
		
		// cache persons aie and theyr corresponding aw id's into array
		{
			$olPersons = new object_list(array(
					"parent" => $this->persons_parent,
					"class_id" => CL_CRM_PERSON,
			));
			$aPersons = array();
			for ($o = $olPersons->begin(); !$olPersons->end(); $o =& $olPersons->next())
			{
				$iOid = $o->id();
				$iAieId = $o->prop("user1");
				$aPersons[$iAieId] = array("oid" => $iOid);
			}
			unset($olPersons);
		}
		
		// cache companies to array
		{
			$companies = new object_list(array(
					"parent" => $this->companies_parent,
					"class_id" => CL_CRM_COMPANY,
			));
			for ($o = $companies->begin(); !$companies->end(); $o =& $companies->next())
			{
				$iAieId = $o->prop("userta1");
				$aTmp = array(
						"oid" => $o->id(),
				);
				$aCompanies[$iAieId] = $aTmp;
			}
			unset($companies);
		}
		
		for ($i=$begin;$i<$end;$i++)
		{
			$iAieCompanyId = $aPhonecalls[$i]["companyid"];
			
			// there are entries in aie base that link to companies that do not exist
			if ($this->can("view",$aCompanies[$iAieCompanyId]["oid"]))
			{
				$oCall = new object(array(
							"name" => $this->get_first_words($aPhonecalls[$i]["longdesc"], 4),
							"parent" => $iAieCompanyId,
							"comment" => $aPhonecalls[$i]["reason"],
							"class_id" => CL_CRM_CALL
				));
				
				$oCall -> set_class_id(CL_CRM_CALL);
				$oCall -> set_prop("start1", $aPhonecalls[$i]["cdate"]);
				$oCall -> set_prop("end", $aPhonecalls[$i]["cdate"]);
				$oCall -> set_prop("is_done", 8);
				$oCall -> set_prop("content", $aPhonecalls[$i]["longdesc"]);
				$oCall -> save();
				
				$oCompany = new object($aCompanies[$iAieCompanyId]["oid"]);
				$oCompany -> connect(array(
							"to" => $oCall -> id(), 
							"type"=> RELTYPE_CALL,
				));
				
				$oCompany -> save();
				
				if (strlen($aPhonecalls[$i]["contacter1"])>0)
				{
					$iAieId = $aPhonecalls[$i]["contacter1"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oPerson -> connect(array(
							"to" => $oCall->id(), 
							"type"=> RELTYPE_PERSON_CALL,
					));
				}
	
				if (strlen($aPhonecalls[$i]["contacter2"])>0)
				{
					$iAieId = $aPhonecalls[$i]["contacter2"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oPerson -> connect(array(
							"to" => $oCall->id(), 
							"type"=> RELTYPE_PERSON_CALL,
					));
				}
				
				if (strlen($aPhonecalls[$i]["contacted"])>0)
				{
					$iAieId = $aPhonecalls[$i]["contacted"];
					$oPerson = new object($aPersons[$iAieId]["oid"]);
					
					$oPerson -> connect(array(
							"to" => $oCall->id(), 
							"type"=> RELTYPE_PERSON_CALL,
					));
					
					$oCall -> connect(array(
								"to" => $aCompanies[$iAieCompanyId]["oid"],
								"type" => RELTYPE_CUSTOMER
					));
					$oCall->set_prop("customer", $aCompanies[$iAieCompanyId]["oid"]);
					$oCall->save();
				}
			}
		}

		return true;
	}

	function get_first_words($str, $count=1)
	{
		$sOutput = "";
		
		$iStrLen = strlen($str);
		$i=0;
		$done == false;
		$countWhitespaces = 0;
		while($countWhitespaces<$count && $i<$iStrLen && $actChar = substr ( $str, $i, 1 ))
		{
			if ( $actChar == " ")
			{
				$countWhitespaces++;
			}
			$sOutput.=$actChar;
			$i++;
		}
		return trim(strip_tags($sOutput))." ...";
	}
	
	/**
	 * This will look up all persons and companies objects which have original db's id's set.
	 * prop for those id's is companies->userta1 and persons->user1
	 *
	 */
	function do_import_person_company_connections()
	{
		$list = new object_list(array(
			"parent" => $this->persons_parent,
			"class_id" => CL_CRM_PERSON,
		));
		
		// cache positions into array
		{
			$positions = new object_list(array(
					"parent" => $this->positions_parent,
					"class_id" => CL_CRM_PROFESSION,
			));
			$aPositions = array();
			for ($o = $positions->begin(); !$positions->end(); $o =& $positions->next())
			{
				$iId = $o->id();
				$aPositions[$iId] = $o->name();
			}
			unset($positions);
		}
		
		// cache companies to array
		{
			$companies = new object_list(array(
					"parent" => $this->companies_parent,
					"class_id" => CL_CRM_COMPANY,
			));
			for ($o = $companies->begin(); !$companies->end(); $o =& $companies->next())
			{
				$iAieId = $o->prop("userta1");
				$aTmp = array(
						"name" =>$o->name(),
						"aw_id" => $o->id(),
				);
				$aCompanies[$iAieId] = $aTmp;
			}
			unset($companies);
		}
		
		// cache query "SELECT companyid, name as positionname,  individualid"
		{
			$this->db_query("SELECT companyid, name as positionname, individualid
			FROM ".$this->db_prefix."influence, ".$this->db_prefix."positions
			WHERE ".$this->db_prefix."influence.positionid = ".$this->db_prefix."positions.id
			ORDER BY inf_id ASC");
			$qSelect = array();
			while ($qSelect[] = $this->db_next() );
		}
		
		$list->foreach_cb(array(
			"func" => array(&$this, "ol_person_company_connections"),
			"param" => array(
				"list_positions" => $aPositions,
				"list_companies" => $aCompanies,
				"influence" => $qSelect
			),
			"save" => true,
        ));
	}
	
	
	function ol_person_company_connections(&$o, $arr)
	{
		$aPositions = $arr["list_positions"];
		$aCompanies = $arr["list_companies"];
		$qInfluence = $arr["influence"];
		
		if (strlen($o->prop("user1"))>0)
		{
			foreach($qInfluence as $w)
			{
				if ($o->prop("user1") == $w["individualid"])
				{
					if (array_key_exists($w["companyid"], $aCompanies))
					{
						$iAWCompanyId = $aCompanies[$w["companyid"]]["aw_id"];
						{
							$o->add_work_relation(array(
								"org" => $iAWCompanyId,
								"profession" =>array_search ( $w["positionname"], $aPositions),
							));
						}
						
						// RELTYPE_PREVIOUS_JOB connection
						/*
						{
							$oPWR = new object(array(
									"parent" => $this->workrelations_parent,
									"class_id" => CL_CRM_PERSON_WORK_RELATION
							));
							$oPWR->connect(array(
								"to" => $iAWCompanyId,
								"type" => RELTYPE_ORG,
							));
							$oPWR->connect(array(
								"to" => array_search ( $w["positionname"], $aPositions),
								"type" => RELTYPE_PROFESSION,
							));
							$oPWR->save();
							
							$o->connect(array(
								"to" => $oPWR->id(), 
								"type"=> RELTYPE_PREVIOUS_JOB,
							));
						}
						*/
					}
				}
				
			}
		}
	}
	
	function remove_person_company_connections()
	{
		$list = new object_list(array(
			"parent" => $this->persons_parent,
			"class_id" => CL_CRM_PERSON,
		));
		
		for ($o = $list->begin(); !$$list->end(); $o =& $list->next())
		{
			 $conns = $o->connections_from(array(
			 	"class" => CL_CRM_COMPANY,
			 ));
			 
			foreach($conns as $con)
			{
				$con->delete();
			}
		}
	}

	
	function _get_debug_persons_table($arr)
	{
		$table =& $arr["prop"]["vcl_inst"];
		
		$conn = new object_list($arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_IMPORTFOLDER"
		)));
		$import_parent = $conn->ids();
		$import_parent = $import_parent[0];
		
		$obj_list = new object_list(array(
			"name" => "organisatsioonid",
			"parent" =>$import_parent,
		));
		$comp_parent = $obj_list->ids();
		$comp_parent = $comp_parent[0];
		
		$list = new object_list(array(
				"parent" => $comp_parent,
				"class_id" => CL_CRM_COMPANY,
		));
		
		$table->table_from_ol($list, array("name", "userta1"), CL_CRM_COMPANY);
	}
	
	/**
	@attrib name=ajax_import_aiesec_members_and_alumni

	@param begin optional type=integer
	@param end optional type=integer
	@param import_parent required type=integer
	
	@comment
		begin and end are vars in for cycle
		begin is i, end is x in i<x
	**/ 
	function ajax_import_aiesec_members_and_alumni($arr)
	{
		aw_disable_acl();
		
		$this -> set_parents($arr["import_parent"]);
		
		$iStep = 30;
		if (!isset($arr["begin"]))
		{
			$arr["begin"] = 0;
		}
		
		if (!isset($arr["end"]))
		{
			$arr["end"] = $arr["begin"]+$iStep;
		}
		
		$a_members = $this->get_aie_members();
		$i_members_count = count($a_members);
		
		if ($i_members_count<$arr["end"])
		{
			$arr["end"] = $i_members_count;
		}
		
		if ($this->do_import_aiesec_members_and_alumni($a_members, $i_members_count, $arr["begin"], $arr["end"]))
		{
			$sUrl = $this->mk_my_orb("ajax_import_aiesec_members_and_alumni", array(
				"begin" =>$arr["end"],
				"end" => $arr["end"]+$iStep,
				"import_parent" => $arr["import_parent"],
			));
			$sOut = 'arr = {"sUrl":"'.$sUrl.'", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		else
		{
			$sOut = 'arr = {"sUrl":"", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		aw_restore_acl();
	}
	
	/**
	 *	This does every possible connections and objects concerning aiesec members and alumni
	 */
	function do_import_aiesec_members_and_alumni($a_members, $i_members_count = false, $begin=false, $end=false )
	{
		if ($i_members_count <= $begin || $i_members_count < $end)
		{
			return false;
		}
		
		// cache persons aie and theyr corresponding aw id's into array
		{
			$olPersons = new object_list(array(
					"parent" => $this->persons_parent,
					"class_id" => CL_CRM_PERSON,
			));
			$aPersons = array();
			for ($o = $olPersons->begin(); !$olPersons->end(); $o =& $olPersons->next())
			{
				$iOid = $o->id();
				$iAieId = $o->prop("user1");
				$aPersons[$iAieId] = array("oid" => $iOid);
			}
			unset($olPersons);
		}
		
		// cache companies to array
		{
			$companies = new object_list(array(
					"parent" => $this->companies_parent,
					"class_id" => CL_CRM_COMPANY,
			));
			for ($o = $companies->begin(); !$companies->end(); $o =& $companies->next())
			{
				$iAieId = $o->prop("userta1");
				$aTmp = array(
						"oid" => $o->id(),
				);
				$aCompanies[$iAieId] = $aTmp;
			}
			unset($companies);
		}
		
		$i_aisec_in_est_id = new object_list(array(
			"name" => "AIESEC in Estonia",
			"class_id" => CL_CRM_COMPANY
		));
		$i_aisec_in_est_id = $i_aisec_in_est_id->ids();
		$i_aisec_in_est_id = $i_aisec_in_est_id[0];
		
		for ($i=$begin;$i<$end;$i++)
		{
			// aiesec estonia id
			// todo
			
		}
	}
	
	/**
	@attrib name=ajax_import_other_contact

	@param begin optional type=integer
	@param end optional type=integer
	@param import_parent required type=integer
	
	@comment
		begin and end are vars in for cycle
		begin is i, end is x in i<x
	**/ 
	function ajax_import_other_contact($arr)
	{
		aw_disable_acl();
		
		$this -> set_parents($arr["import_parent"]);
		
		$iStep = 30;
		if (!isset($arr["begin"]))
		{
			$arr["begin"] = 0;
		}
		
		if (!isset($arr["end"]))
		{
			$arr["end"] = $arr["begin"]+$iStep;
		}
		
		$a_contacts = $this->get_contacts_other();
		$i_contacts_count = count($a_contacts);
		
		if ($i_contacts_count<$arr["end"])
		{
			$arr["end"] = $i_contacts_count;
		}
		
		if ($this->do_import_contacts_others_full($a_contacts, $i_contacts_count, $arr["begin"], $arr["end"]))
		{
			$sUrl = $this->mk_my_orb("ajax_import_other_contact", array(
				"begin" =>$arr["end"],
				"end" => $arr["end"]+$iStep,
				"import_parent" => $arr["import_parent"],
			));
			$sOut = 'arr = {"sUrl":"'.$sUrl.'", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		else
		{
			$sOut = 'arr = {"sUrl":"", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		aw_restore_acl();
	}
	
	
	/**
	@attrib name=ajax_import_positions

	@param begin optional type=integer
	@param end optional type=integer
	@param import_parent required type=integer
	
	@comment
		begin and end are vars in for cycle
		begin is i, end is x in i<x
	**/ 
	function ajax_import_positions($arr)
	{
		aw_disable_acl();
		
		$this -> set_parents($arr["import_parent"]);
		
		$iStep = 80;
		if (!isset($arr["begin"]))
		{
			$arr["begin"] = 0;
		}
		
		if (!isset($arr["end"]))
		{
			$arr["end"] = $arr["begin"]+$iStep;
		}
		
		{
			$iPositionsParent = new object_list(array(
				"name" => "ametid",
				"parent" =>$arr["import_parent"],
				"class_id" => CL_MENU,
			));
			$iPositionsParent = $iPositionsParent->ids();
			$iPositionsParent = $iPositionsParent[0];
		}
		
		if ($arr["begin"] === 0)
		{
			$ol = new object_list(array(
				"parent" => $iPositionsParent,
			));
			$ol->delete(true);
		}
		
		$aPositions = $this -> get_positions();
		$iPositionsCount = count($aPositions);

		if ($iPositionsCount<$arr["end"])
		{
			$arr["end"] = $iPositionsCount;
		}
		
		if ($this->do_import_positions_full($iPositionsParent, $aPositions, $iPositionsCount, $arr["begin"], $arr["end"]))
		{
			$sUrl = $this->mk_my_orb("ajax_import_positions", array(
				"begin" =>$arr["end"],
				"end" => $arr["end"]+$iStep,
				"import_parent" => $arr["import_parent"],
			));
			$sOut = 'arr = {"sUrl":"'.$sUrl.'", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		else
		{
			$sOut = 'arr = {"sUrl":"", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		aw_restore_acl();
	}
	
	
		/**
	@attrib name=ajax_import_companies

	@param begin optional type=integer
	@param end optional type=integer
	@param import_parent required type=integer
	
	@comment
		begin and end are vars in for cycle
		begin is i, end is x in i<x
	**/ 
	function ajax_import_companies($arr)
	{
		aw_disable_acl();
		
		$this -> set_parents($arr["import_parent"]);
		
		$this->get_cities();
		$this->get_counties();
		$this->get_countries();

		$iStep = 20;
		if (!isset($arr["begin"]))
		{
			$arr["begin"] = 0;
		}
		
		if (!isset($arr["end"]))
		{
			$arr["end"] = $arr["begin"]+$iStep;
		}
		
		// companies folder id
		{
			$iCompaniesParent = new object_list(array(
				"name" => "organisatsioonid",
				"parent" =>$arr["import_parent"],
				"class_id" => CL_MENU,
			));
			$iCompaniesParent = $iCompaniesParent->ids();
			$iCompaniesParent = $iCompaniesParent[0];
		}
		
		if ($arr["begin"] === 0)
		{
			$ol = new object_list(array(
				"parent" => $iCompaniesParent,
			));
			$ol->delete(true);
		}
		
		$aCompanies = $this -> get_companies();
		$iCompaniesCount = count($aCompanies);
		if ($iCompaniesCount<$arr["end"])
		{
			$arr["end"] = $iCompaniesCount;
		}
		
		if ($this->do_import_companies_full($iCompaniesParent, $aCompanies, $iCompaniesCount, $arr["begin"], $arr["end"]))
		
		{
			$sUrl = $this->mk_my_orb("ajax_import_companies", array(
				"begin" =>$arr["end"],
				"end" => $arr["end"]+$iStep,
				"import_parent" => $arr["import_parent"],
			));
			$sOut = 'arr = {"sUrl":"'.$sUrl.'", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		else
		{
			$sOut = 'arr = {"sUrl":"", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		aw_restore_acl();
	}
	
	/**
	@attrib name=ajax_import_meetings

	@param begin optional type=integer
	@param end optional type=integer
	@param import_parent required type=integer
	
	@comment
		begin and end are vars in for cycle
		begin is i, end is x in i<x
	**/ 
	function ajax_import_meetings($arr)
	{
		aw_disable_acl();
		
		$this -> set_parents($arr["import_parent"]);
		
		$iStep = 30;
		
		if (!isset($arr["begin"]))
		{
			$arr["begin"] = 0;
		}
		
		if (!isset($arr["end"]))
		{
			$arr["end"] = $arr["begin"]+$iStep;
		}
		
		$a_meetings = $this->get_meetings();
		$i_meetings_count = count($a_meetings);
		
		if ($a_meetings<$arr["end"])
		{
			$arr["end"] = $a_meetings;
		}
		
		if ($this->do_import_meetings_full($a_meetings, $i_meetings_count, $arr["begin"], $arr["end"]))
		{
			$sUrl = $this->mk_my_orb("ajax_import_meetings", array(
				"begin" =>$arr["end"],
				"end" => $arr["end"]+$iStep,
				"import_parent" => $arr["import_parent"],
			));
			$sOut = 'arr = {"sUrl":"'.$sUrl.'", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		else
		{
			$sOut = 'arr = {"sUrl":"", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		aw_restore_acl();
	}
	
	
	
		/**
	@attrib name=ajax_import_phonecalls

	@param begin optional type=integer
	@param end optional type=integer
	@param import_parent required type=integer
	
	@comment
		begin and end are vars in for cycle
		begin is i, end is x in i<x
	**/ 
	function ajax_import_phonecalls($arr)
	{
		aw_disable_acl();
		
		$this -> set_parents($arr["import_parent"]);
		
		$iStep = 30;
		if (!isset($arr["begin"]))
		{
			$arr["begin"] = 0;
		}
		
		if (!isset($arr["end"]))
		{
			$arr["end"] = $arr["begin"]+$iStep;
		}
		
		$aPhonecalls = $this->get_phonecalls();
		$iPhonecallsCount = count($aPhonecalls);
		
		if ($aPhonecalls<$arr["end"])
		{
			$arr["end"] = $aPhonecalls;
		}
		
		if ($this->do_import_phonecalls_full($aPhonecalls, $iPhonecallsCount, $arr["begin"], $arr["end"]))
		{
			$sUrl = $this->mk_my_orb("ajax_import_phonecalls", array(
				"begin" =>$arr["end"],
				"end" => $arr["end"]+$iStep,
				"import_parent" => $arr["import_parent"],
			));
			$sOut = 'arr = {"sUrl":"'.$sUrl.'", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		else
		{
			$sOut = 'arr = {"sUrl":"", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		aw_restore_acl();
	}
	
	/**
	@attrib name=ajax_import_persons

	@param begin optional type=integer
	@param step optional type=integer
	@param import_parent required type=integer
	
	@comment
		bla bla
	**/ 
	function ajax_import_persons($arr)
	{
		aw_disable_acl();
		
		$this -> set_parents($arr["import_parent"]);
		
		$iStep = 20;
		if (!isset($arr["begin"]))
		{
			$arr["begin"] = 0;
		}
		
		if (!isset($arr["end"]))
		{
			$arr["end"] = $arr["begin"]+$iStep;
		}
		
		// companies folder id
		{
			$iPersonsParent = new object_list(array(
				"name" => "isikud",
				"parent" =>$arr["import_parent"],
				"class_id" => CL_MENU,
			));
			$iPersonsParent = $iPersonsParent ->ids();
			$iPersonsParent = $iPersonsParent[0];
		}
		
		if ($arr["begin"] === 0)
		{
			$ol = new object_list(array(
				"parent" => $iPersonsParent,
			));
			$ol->delete(true);
		}
		
		$aPersons = $this -> get_persons();
		$iPersonsCount = count($aPersons);
		if ($iPersonsCount<$arr["end"])
		{
			$arr["end"] = $iPersonsCount;
		}

		if ($this->do_import_persons_full($iPersonsParent, $aPersons, $iPersonsCount, $arr["begin"], $arr["end"]))
		{
			$sUrl = $this->mk_my_orb("ajax_import_persons", array(
				"begin" =>$arr["end"],
				"end" => $arr["end"]+$iStep,
				"import_parent" => $arr["import_parent"],
			));
			$sOut = 'arr = {"sUrl":"'.$sUrl.'", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		else
		{
			$sOut = 'arr = {"sUrl":"", "iImported":"'.$arr["end"].'"}';
			die($sOut);
		}
		
		aw_restore_acl();
	}
	
	
	/**
	@attrib name=do_recursive_url_persons_import

	@param begin optional type=integer
	@param step optional type=integer
	@param import_parent required type=integer
	
	@comment
		bla bla
	**/ 
	function do_recursive_url_persons_import($arr)
	{
		// persons folder id
		{
			$obj_list = new object_list(array(
				"name" => "isikud",
				"parent" =>$arr["import_parent"],
				"class_id" => CL_MENU,
			));
			$persons_parent = $obj_list->ids();
			$persons_parent = $persons_parent[0];
		}
		
		if (!isset($arr["begin"]))
		{
			$arr["begin"] = 0;
		}
		if (!isset($arr["step"]))
		{
			$arr["step"] = 10;
		}

		
		// delete all persons if starting all over
		if ($arr["begin"] === 0)
		{
			$obj_list = new object_list(array(
				"parent" =>$persons_parent,
			));
			$obj_list -> delete(true);
		}

		$status .=  $arr["begin"]+$step. " isikut valmis<br>\n";
		
		if ($this->do_import_persons($persons_parent, $arr["begin"], $arr["step"]))
		{
			$url = $this->mk_my_orb("do_recursive_url_persons_import", array(
				"begin" =>$arr["begin"]+$arr["step"],
				"step" => $arr["step"],
				"import_parent" => $arr["import_parent"],
			));
		}
		else
		{
			die("import l2bi");
		}
	
		echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		   "http://www.w3.org/TR/html4/loose.dtd">
		
		<html>
		<head>
			<title>Import</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
			<script>
			window.onload = function()
			{
				 window.setTimeout("change_url()",500); 
			}
			
			function change_url()
			{
				location.href="'.$url.'";
			}
			</script>
		</head>
		
		<body>
		
		'.$status.' 
		
		
		</body>
		</html>';
		
		die();
	}
	

	
	
	/**
	 * gets choices for peoples work statuses
	 */
	function do_import_positions($import_parent)
	{
		$obj_list = new object_list(array(
			"name" => "ametid",
			"parent" =>$import_parent,
		));
		$positions_parent = $obj_list->ids();
		$positions_parent = $positions_parent[0];
		
		$this->delete_objects($positions_parent);
		
		$positions = $this->get_positions();
		$positions_count = count($positions);
		for ($i=0;$i<$positions_count;$i++)
		{
			$oPosition = new object(array(
					"name" => $positions[$i]["name"],
					"parent" => $positions_parent,
					"class_id" => CL_CRM_PROFESSION,
			));
			$oPosition->set_class_id(CL_CRM_PROFESSION);
			$oPosition->set_prop("comment", $positions[$i]["id"]);
			$oPosition->save();
		}
	}
	
		/**
	 * gets choices for peoples work statuses
	 */
	function do_import_positions_full($iPositionsParent, $aPositions, $iPositionsCount = false, $begin=false, $end=false )
	{
		if ($iPositionsCount <= $begin || $iPositionsCount < $end)
		{
			return false;
		}
		
		for ($i=$begin;$i<$end;$i++)
		{
			$oPosition = new object(array(
					"name" => $aPositions[$i]["name"],
					"parent" => $iPositionsParent,
					"class_id" => CL_CRM_PROFESSION,
			));
			$oPosition->set_class_id(CL_CRM_PROFESSION);
			$oPosition->set_prop("comment", $aPositions[$i]["id"]);
			$oPosition->save();
		}
		return true;
	}
	
	function set_parents($import_parent)
	{
		$this->import_parent = $import_parent;
		
		$obj_list = new object_list(array(
			"name" => "isikud",
			"parent" =>$this->import_parent,
		));
		$persons_parent = $obj_list->ids();
		$this->persons_parent = $persons_parent[0];
		
		$obj_list = new object_list(array(
			"name" => "ametid",
			"parent" =>$import_parent,
		));
		$positions_parent = $obj_list->ids();
		$this->positions_parent = $positions_parent[0];
		
		$obj_list = new object_list(array(
			"name" => "organisatsioonid",
			"parent" =>$import_parent,
		));
		$companies_parent = $obj_list->ids();
		$this->companies_parent = $companies_parent[0];

		$obj_list = new object_list(array(
			"name" => "töösuhted",
			"parent" =>$import_parent,
		));
		$workrelations_parent = $obj_list->ids();
		$this->workrelations_parent = $workrelations_parent[0];
		
		$obj_list = new object_list(array(
			"name" => "linnad",
			"parent" =>$import_parent,
		));
		$cities_parent = $obj_list->ids();
		$this->cities_parent = $cities_parent[0];
		
		$obj_list = new object_list(array(
			"name" => "maakonnad",
			"parent" =>$import_parent,
		));
		$counties_parent = $obj_list->ids();
		$this->counties_parent = $counties_parent[0];
		
		$obj_list = new object_list(array(
			"name" => "riigid",
			"parent" =>$import_parent,
		));
		$countries_parent = $obj_list->ids();
		$this->countries_parent = $countries_parent[0];
		
		$obj_list = new object_list(array(
			"name" => "kõned",
			"parent" =>$import_parent,
		));
		$phonecalls_parent = $obj_list->ids();
		$this->phonecalls_parent = $phonecalls_parent[0];
	}
	
	function _get_preview_positions_count_before($arr)
	{
		$q = "SELECT COUNT(*) as count FROM ".$this->db_prefix."positions";
		$this->db_query($q);
		if ($w = $this->db_next())
			$arr["prop"]["value"] = $w["count"];
	}

	
	function _get_preview_positions_count_after($arr)
	{
		$q = "SELECT DISTINCT id, name FROM ".$this->db_prefix."positions GROUP BY name";
		$this->db_query($q);
		if ($w = $this->db_next())
			$arr["prop"]["value"] = $this->num_rows();
	}
	
	function _get_preview_positions_table($arr)
	{
		$table =& $arr["prop"]["vcl_inst"];

		$table->define_field(array(
			"name" => "id",
			"caption" => t("Id"),
			"sortable" => 1,
			"align" => "left",
			"numeric" => 1
		));
		
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$positions = $this->get_positions();
		$i=0;
        while($positions[$i])
        {
			$table->define_data(array(
				"id" => $positions[$i]["id"],
				 "name" => $positions[$i]["name"],
			));
			$i++;
        }
		
		$table->define_pageselector(array(
				"type"=>"lb",
				"records_per_page"=>100,
				"position"=>"both",
		));
	}
	
	
	function get_aie_members()
	{
		$aOut = array();
		$q = "SELECT 
		a_id AS id,
		i_id,
		status,
		country,
		lc,
		msn,
		since,
		pos
		FROM ".$this->db_prefix."members
		ORDER BY id ASC";
		
        $this->db_query($q);
        while($w = $this->db_next())
        {
			$aOut[count($aOut)] = array(
				 "id" => $w["id"],
				 "i_id" => $w["i_id"],
				 "status" => $w["status"],
				 "country" => $w["country"],
				 "lc" => $w["lc"],
				 "pos" => $w["pos"],
				 "msn" => $w["msn"],
				 "since" => $w["since"],
				 "pos" => $w["pos"],
			);
        }
		return $aOut;
	}
	
	
	function get_positions()
	{
		$aOut = array();
		$q = "SELECT DISTINCT id, name FROM ".$this->db_prefix."positions GROUP BY name";
        $this->db_query($q);
        while($w = $this->db_next())
        {
			$aOut[count($aOut)] = array(
				 "id" => $w["id"],
				 "name" => trim(utf8_decode($w["name"]))
			);
        }
		return $aOut;
	}
	
	function do_import_companies($import_parent)
	{
		$companies = $this->get_companies();
		$companies_count = count($companies);
		for ($i=0;$i<10;$i++)
		{
			$o = new object(array(
	               "name" => $companies[$i]["name"],
				   "parent" => $this->companies_parent,
	               "class_id" => CL_CRM_COMPANY,
			));
			$o->set_status(STAT_ACTIVE);
			$o->set_class_id(CL_CRM_COMPANY);
			$o->set_prop("tegevuse_kirjeldus",  $companies[$i]["tegevuse_kirjeldus"]);
			$o->set_prop("userta1",  $companies[$i]["id"]);
			$o->save();
			
			$this->do_import_companies_connect_address($o, $companies[$i]["address"], $companies[$i]["postal_index"], $companies[$i]["city"]);
			$this->do_import_companies_connect_url($o, $companies[$i]["url"]);
			$this->do_import_companies_connect_email($o, $companies[$i]["email"]);
			$this->do_import_companies_connect_phone($o, $companies[$i]["workphone1"]);
			$this->do_import_companies_connect_phone($o, $companies[$i]["workphone2"]);
			$this->do_import_companies_connect_phone($o, $companies[$i]["mobile1"], "mobile");
			$this->do_import_companies_connect_phone($o, $companies[$i]["mobile2"], "mobile");
		}
	}
	
	/**
	 *	This does every possible connections and objects concerning companies
	 */
	function do_import_companies_full($iCompaniesParent, $aCompanies, $iCompaniesCount = false, $begin=false, $end=false )
	{
		if ($iCompaniesCount <= $begin || $iCompaniesCount < $end)
		{
			return false;
		}
		
		for ($i=$begin;$i<$end;$i++)
		{
			$o = new object(array(
	               "name" => $aCompanies[$i]["name"],
				   "parent" => $iCompaniesParent,
	               "class_id" => CL_CRM_COMPANY,
			));
			$o->set_status(STAT_ACTIVE);
			$o->set_class_id(CL_CRM_COMPANY);
			$o->set_prop("tegevuse_kirjeldus",  $aCompanies[$i]["tegevuse_kirjeldus"]);
			$o->set_prop("userta1",  $aCompanies[$i]["id"]);
			$o->save();
			
			$this->do_import_companies_connect_address($o, $aCompanies[$i]["address"], $aCompanies[$i]["postal_index"], $aCompanies[$i]["city"]);
			$this->do_import_companies_connect_other_addresses($o, $aCompanies[$i]["branches"], $aCompanies[$i]["city"]);
			$this->do_import_companies_connect_url($o, $aCompanies[$i]["url"]);
			$this->do_import_companies_connect_email($o, $aCompanies[$i]["email"]);
			$this->do_import_companies_connect_phone($o, $aCompanies[$i]["workphone1"]);
			$this->do_import_companies_connect_phone($o, $aCompanies[$i]["workphone2"]);
			$this->do_import_companies_connect_phone($o, $aCompanies[$i]["mobile1"], "mobile");
			$this->do_import_companies_connect_phone($o, $aCompanies[$i]["mobile2"], "mobile");
			
			{
				// connect persons
			}
		}
		return true;
	}
	
	function do_import_companies_connect_other_addresses($oCompany, $sBranches, $sCity)
	{
		$i_branches_count = count($sBranches);
		if ($i_branches_count>1)
		{
			for ($i=0;$i<$i_branches_count;$i++)
			{
				if ($sBranches[$i] != $sCity)
				{
					$oAddress = new object(array(
						"name" => $sBranches[$i],
						"parent" => $oCompany->id(),
						"class_id"=>CL_CRM_ADDRESS
					));
					$oAddress->set_status(STAT_ACTIVE);
					$oAddress->set_class_id(CL_CRM_ADDRESS);
					$oAddress->set_prop("aadress", $sBranches[$i]);
					
					if (array_key_exists ( $sBranches[$i], $this->aCities))
					{
						$oAddress->set_prop("linn", $this->aCities[$sBranches[$i]]);
						$sCountyName = $this->aLocationRelations[$sBranches[$i]]["county"];
						$oAddress->set_prop("maakond", $this->aCounties[$sCountyName]);
						$sCountryName = $this->aLocationRelations[$sBranches[$i]]["country"];
						$oAddress->set_prop("riik", $this->aCountries[$sCountryName]);
					}
					$oAddress->save();
					$oCompany->connect(array(
						"to" => $oAddress->id(),
						"type" => RELTYPE_ADDRESS
					));
					$oCompany->set_prop("contact", $oAddress->id());
					$oCompany->save();
				}
			}
		}
	}
	
	function do_import_companies_connect_phone($oCompany, $sNumber, $sType="work")
	{
		if (strlen($sNumber)>3)
		{
			$phone = new object(array(
					"name" => $sNumber,
					"parent" => $oCompany->id(),
					"class_id"=>CL_CRM_PHONE
			));
			$phone->set_status(STAT_ACTIVE);
			$phone -> set_class_id(CL_CRM_PHONE);
			$phone->set_prop(type, $sType);
			$phone->save();
			$oCompany->connect(array(
				"to" => $phone->id(),
				"type" => RELTYPE_PHONE
			));
			$oCompany->set_prop("phone_id", $phone->id());
			$oCompany->save();
		}
	}
	
	function do_import_companies_connect_email($oCompany, $sMail)
	{
		if (strlen($sMail)>3)
		{
			$email = new object(array(
					"name" => $sMail,
					"parent" => $oCompany->id(),
					"class_id"=>CL_ML_MEMBER
			));
			$email->set_status(STAT_ACTIVE);
			$email -> set_class_id(CL_ML_MEMBER);
			$email -> set_prop("mail", $sMail);
			$email->save();
			$oCompany->connect(array(
				"to" => $email->id(),
				"type" => RELTYPE_EMAIL
			));
			$oCompany->set_prop("email_id", $email->id());
			$oCompany->save();
		}
			
	}
	
	function do_import_companies_connect_url($oCompany, $sUrl)
	{
		if (strlen($sUrl)>3)
		{
			$oUrl = new object(array(
					"name" => $sUrl,
					"parent" => $oCompany->id(),
					"class_id"=>CL_EXTLINK
			));
			$oUrl->set_status(STAT_ACTIVE);
			$oUrl->set_class_id(CL_EXTLINK);
			$oUrl->set_prop("url", $sUrl);
			$oUrl->save();
			$oCompany->connect(array(
				"to" => $oUrl->id(),
				"type" => RELTYPE_URL
			));
			$oCompany->set_prop("url_id", $oUrl->id());
			$oCompany->save();
		}
	}
	
	function do_import_companies_connect_address($oCompany, $sAddress, $sPostalIndex="", $sCity="")
	{
		if (strlen($sAddress)>3)
		{
			$oAddress = new object(array(
					"name" => $sAddress,
					"parent" => $oCompany->id(),
					"class_id"=>CL_CRM_ADDRESS
			));
			$oAddress->set_status(STAT_ACTIVE);
			$oAddress->set_class_id(CL_CRM_ADDRESS);
			$oAddress->set_prop("aadress", $sAddress);
			if (strlen($sPostalIndex)>0)
			{
				$oAddress->set_prop("postiindeks", $sPostalIndex);
			}

			if (strlen($sCity)>0 &&  array_key_exists ( $sCity, $this->aCities))
			{
				$oAddress->set_prop("linn", $this->aCities[$sCity]);
				$sCountyName = $this->aLocationRelations[$sCity]["county"];
				$oAddress->set_prop("maakond", $this->aCounties[$sCountyName]);
				$sCountryName = $this->aLocationRelations[$sCity]["country"];
				$oAddress->set_prop("riik", $this->aCountries[$sCountryName]);
			}
			$oAddress->save();
			$oCompany->connect(array(
				"to" => $oAddress->id(),
				"type" => RELTYPE_ADDRESS
			));
			$oCompany->set_prop("contact", $oAddress->id());
			$oCompany->save();
		}
	}
	
	function create_cities()
	{
		$obj_list = new object_list(array(
			"parent" => $this->cities_parent,
			"class_id" => CL_CRM_CITY,
		));
		$obj_list->delete(true);
		
		foreach($this->aCities as $key=>$value)
		{
			$o = new object(array(
	               "name" => $key,
				   "parent" => $this->cities_parent,
	               "class_id" => CL_CRM_CITY,
			));
			$o->save();
			$this->aCities[$key] = $o->id();
		}
	}
	
	function get_cities()
	{
		foreach($this->aCities as $key=>$value)
		{
			$ol = new object_list(array(
	               "name" => $key,
				   "parent" => $this->cities_parent,
	               "class_id" => CL_CRM_CITY,
			));
			$id = $ol->ids();
			$id = $id[0];
			$this->aCities[$key] = $id;
		}
	}
	
	function create_counties()
	{
		$obj_list = new object_list(array(
			"parent" => $this->counties_parent,
			"class_id" => CL_CRM_COUNTY,
		));
		$obj_list->delete(true);
		
		foreach($this->aCounties as $key=>$value)
		{
			$o = new object(array(
	               "name" => $key,
				   "parent" => $this->counties_parent,
	               "class_id" => CL_CRM_COUNTY,
			));
			$o->save();
			$this->aCounties[$key] = $o->id();
		}
	}
	
	function get_counties()
	{
		foreach($this->aCounties as $key=>$value)
		{
			$ol = new object_list(array(
	               "name" => $key,
				   "parent" => $this->counties_parent,
	               "class_id" => CL_CRM_COUNTY,
			));
			$id = $ol->ids();
			$id = $id[0];
			$this->aCounties[$key] = $id;
		}
	}
	
	function create_countries()
	{
		$obj_list = new object_list(array(
			"parent" => $this->countries_parent,
			"class_id" => CL_CRM_COUNTRY,
		));
		$obj_list->delete(true);
		
		foreach($this->aCountries as $key=>$value)
		{
			$o = new object(array(
	               "name" => $key,
				   "parent" => $this->countries_parent,
	               "class_id" => CL_CRM_COUNTRY,
			));
			$o->save();
			$this->aCountries[$key] = $o->id();
		}
	}
	
	function get_countries()
	{
		foreach($this->aCountries as $key=>$value)
		{
			$ol = new object_list(array(
	               "name" => $key,
				   "parent" => $this->countries_parent,
	               "class_id" => CL_CRM_COUNTRY,
			));
			$id = $ol->ids();
			$id = $id[0];
			$this->aCountries[$key] = $id;
		}
	}
	
	function delete_objects($iParent)
	{
		$obj_list = new object_list(array(
			"parent" =>$iParent,
		));
		$obj_list->delete();
	}
	
	//do_import_persons_full($iPersonsParent, $aPersons, $iPersonsCount, $arr["begin"], $arr["end"]))
	function do_import_persons_full($iPersonsParent, $aPersons, $iPersonsCount, $begin, $end)
	{
		if ($iPersonsCount <= $begin || $iPersonsCount < $end)
		{
			return false;
		}
		
		for ($i=$begin;$i<$end;$i++)
		{
			$o = new object(array(
	               "name" => $aPersons[$i]["lastname"]." ".$aPersons[$i]["firstname"],
				   "parent" => $iPersonsParent,
	               "class_id" => CL_CRM_PERSON,
			));
			$o->set_status(STAT_ACTIVE);
			$o->set_class_id(CL_CRM_PERSON);
			$o->set_prop("firstname", $aPersons[$i]["firstname"]);
			$o->set_prop("lastname", $aPersons[$i]["lastname"]);
			$o->set_prop("user1", $aPersons[$i]["id"]);
			$o->save();

			$this->do_import_persons_connect_phone($o, array("number" =>$aPersons[$i]["workphone1"]));
			$this->do_import_persons_connect_phone($o, array("number" =>$aPersons[$i]["workphone2"]));
			$this->do_import_persons_connect_phone($o, array(
					"number" =>$aPersons[$i]["mobile1"],
					"type" => "mobile",
			));
			$this->do_import_persons_connect_phone($o, array(
					"number" =>$aPersons[$i]["mobile2"],
					"type" => "mobile",
			));
			$this->do_import_persons_connect_email($o, $aPersons[$i]["email"]);
			$this->do_import_persons_connect_url($o, $aPersons[$i]["url"]);

			$this->do_import_persons_workrelations($o);
			$this->do_import_persons_comments($o);
		}
		return true;
	}
	
	function do_import_persons_comments($oPerson)
	{
		$iPersonId = $oPerson->prop("user1");
		$sQuery = "SELECT id, i_id, notes, time, author 
						FROM ".$this->db_prefix."notes 
						WHERE i_id =$iPersonId
						";
		
		$this->db_query($sQuery);
		$sAllComments = "";
		while ($w = $this->db_next())
		{
			$sAllComments = utf8_decode($w["author"]) . " " . $w["time"] . "\n";
			$sAllComments .= utf8_decode($w["notes"]) . "\n\n";
		}
		if (strlen($sAllComments)>0)
		{
			$oPerson->set_prop("notes", $sAllComments);
			$oPerson->save();
		}
	}
	
	function do_import_persons_workrelations($oPerson)
	{
		// cache positions into array
		{
			$positions = new object_list(array(
					"parent" => $this->positions_parent,
					"class_id" => CL_CRM_PROFESSION,
			));
			$aPositions = array();
			for ($o = $positions->begin(); !$positions->end(); $o =& $positions->next())
			{
				$iId = $o->id();
				$aPositions[$iId] = $o->name();
			}
			unset($positions);
		}
	
		$iPersonAieId = $oPerson -> prop("user1");
		$sQuery = "SELECT DISTINCT companyid, ".$this -> db_prefix."positions.name AS positionname, individualid ".
						"FROM ".$this -> db_prefix."influence, ".$this -> db_prefix."positions, ".$this -> db_prefix."companies ".
						"WHERE ".$this -> db_prefix."influence.positionid = ".$this -> db_prefix."positions.id ".
						"AND ".$this -> db_prefix."companies.id = companyid ".
						"AND individualid =".$iPersonAieId.
						" ORDER BY inf_id ASC";

		$this->db_query($sQuery);
		
		while($w = $this->db_next())
        {
			$ol = new object_list(array(
					"userta1" =>  $w["companyid"],
					"class_id" => CL_CRM_COMPANY,
			));

			$iAWCompanyId = $ol -> ids();
			$iAWCompanyId = $iAWCompanyId[0];
			
			if ($iAWCompanyId>0)
			{
				$oPerson->add_work_relation(array(
					"org" => $iAWCompanyId,
					"profession" =>array_search ( $w["positionname"], $aPositions),
				));
			}
			else
			{
				die("firmat - ".$w["companyid"]." - ei ole ole olemas. mIks?");
			}
		}
		
	}
	
	function do_import_persons($parent=false, $from=false, $step=false)
	{
		if ($parent == false)
		{
			$parent = $this->persons_parent;
		}
		//$this->delete_objects($parent);
		$persons = $this->get_persons();
		$persons_count = count($persons);

		if ($from>$persons_count)
		{
			return false;
		}
		
		if (!isset($from) && !isset($step))
		{
			$k=0;
		}
		else
		{
			$k=$from;
			if ($persons_count>$from+$step)
			{
				$persons_count=$from+$step;
			}
		}
		for ($i=$k;$i<$persons_count;$i++)
		{
			$o = new object(array(
	               "name" => $persons[$i]["lastname"]." ".$persons[$i]["firstname"],
				   "parent" => $parent,
	               "class_id" => CL_CRM_PERSON,
			));
			$o->set_status(STAT_ACTIVE);
			$o->set_class_id(CL_CRM_PERSON);
			$o->set_prop("firstname", $persons[$i]["firstname"]);
			$o->set_prop("lastname", $persons[$i]["lastname"]);
			$o->set_prop("user1", $persons[$i]["id"]);
			$o->save();

			$this->do_import_persons_connect_phone($o, array("number" =>$persons[$i]["workphone1"]));
			$this->do_import_persons_connect_phone($o, array("number" =>$persons[$i]["workphone2"]));
			$this->do_import_persons_connect_phone($o, array(
					"number" =>$persons[$i]["mobile1"],
					"type" => "mobile",
			));
			$this->do_import_persons_connect_phone($o, array(
					"number" =>$persons[$i]["mobile2"],
					"type" => "mobile",
			));
			$this->do_import_persons_connect_email($o, $persons[$i]["email"]);
			$this->do_import_persons_connect_url($o, $persons[$i]["url"]);
		}
		return true;
	}
	
	
	function do_import_persons_connect_url($oPerson, $sUrl)
	{
		if (strlen($sUrl)>3)
		{
			$oUrl = new object(array(
					"name" => $sUrl,
					"parent" => $oPerson->id(),
					"class_id"=>CL_EXTLINK
			));
			$oUrl->set_status(STAT_ACTIVE);
			$oUrl->set_class_id(CL_EXTLINK);
			$oUrl->set_prop("url", $sUrl);
			$oUrl->save();
			$oPerson->connect(array(
				"to" => $oUrl->id(),
				"type" => RELTYPE_URL
			));
			$oPerson->set_prop("url", $oUrl->id());
			$oPerson->save();
		}
	}
	
	function do_import_persons_connect_email($oPerson, $sMail)
	{
		$ol = new object_list(array(
			"name" => $sMail,
			"class_id"=>CL_ML_MEMBER,
			"parent" => $oPerson->id(),
		));
		if ($ol->count()==0)
		{
			if (strlen($sMail)>3)
			{
				$email = new object(array(
						"name" => $sMail,
						"parent" => $oPerson->id(),
						"class_id"=>CL_ML_MEMBER
				));
				$email->set_status(STAT_ACTIVE);
				$email -> set_class_id(CL_ML_MEMBER);
				$email -> set_prop("mail", $sMail);
				$email->save();
				$oPerson->connect(array(
					"to" => $email->id(),
					"type" => RELTYPE_EMAIL
				));
				$oPerson->set_prop("email", $email->id());
				$oPerson->save();
			}
		}
	}
	
	function do_import_persons_connect_phone($oPerson, $arr )
	{
		$sNumber = $arr["number"];
		if ($arr["type"])
		{
			$sType=$arr["type"];
		}
		else
		{
			$sType="work";
		}
		if (strlen($sNumber)>3)
		{
			$phone = new object(array(
					"name" => $sNumber,
					"parent" => $oPerson->id(),
					"class_id"=>CL_CRM_PHONE
			));
			$phone->set_status(STAT_ACTIVE);
			$phone ->set_class_id(CL_CRM_PHONE);
			$phone->set_prop("type", $sType);
			$phone->save();
			$oPerson->connect(array(
				"to" => $phone->id(),
				"type" => RELTYPE_PHONE
			));
			$oPerson->set_prop("phone", $phone->id());
			$oPerson->save();
		}
	}
	
	function do_import_create_import_folders($import_parent)
	{
		//$list_todelete = new object_list(array(
		//		"parent" => $import_parent,
		//));
		//$list_todelete->delete(true);
		
		$folders= array ("isikud",
								"organisatsioonid", 
								"kohtumised", 
								"toimetused", 
								"kõned", 
								"ametid", 
								"töösuhted", 
								"linnad", 
								"maakonnad", 
								"riigid", 
								"kõned"
		);
		
		for ($i=0;$i<count($folders);$i++)
		{
			$oFolder = new object_list(array(
					"parent"=>$import_parent,
					"name"=>$folders[$i],
			));
			if ($oFolder->count()===0)
			{
				$o = new object(array(
	               "name" => $folders[$i],
	               "parent" => $import_parent,
	               "class_id" => CL_MENU
				));
				$o -> save();
			}
		}
		return true;
	}
	
	function _get_preview_companies_table($arr)
	{
		$table =& $arr["prop"]["vcl_inst"];

		$table->define_field(array(
			"name" => "id",
			"caption" => t("Id"),
			"sortable" => 1,
			"align" => "left",
			"numeric" => 1
		));
		
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "postal_index",
			"caption" => t("Posti i."),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "city",
			"caption" => t("Linn"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "url",
			"caption" => t("Url"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "email",
			"caption" => t("email"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "contact",
			"caption" => t("Kontakt"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "workphone1",
			"caption" => t("Töötelefon1"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "workphone2",
			"caption" => t("Töötelefon2"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "mobile1",
			"caption" => t("Mob1"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "mobile2",
			"caption" => t("Mob2"),
			"sortable" => 1,
			"align" => "left"
		));
		
		
		
		$table->define_field(array(
			"name" => "tegevuse_kirjeldus",
			"caption" => t("Tegevuse kirjeldus"),
			"sortable" => 1,
			"align" => "left"
		));
		
		
		$companies = $this->get_companies();
		$i=0;
        while($companies[$i])
        {
			$url =  $this->mk_my_orb("change", array(
													"group" =>"preview_persons",
													"id" => $arr["obj_inst"]->id(),
													"company_id" => $companies[$i]["id"],
					));

			$table->define_data(array(
				"id" => $companies[$i]["id"],
				"name" =>  html::href(array(
									"caption" => $companies[$i]["name"],
									"url" => $url,
				)),
				 "address" => $companies[$i]["address"],
				 "postal_index" => $companies[$i]["postal_index"],
 				 "city" => $companies[$i]["city"],
				 "url" => $companies[$i]["url"],
				 "email" => $companies[$i]["email"],
				 "contact" => $companies[$i]["contact"],
				 "workphone1" => $companies[$i]["workphone1"],
				 "workphone2" => $companies[$i]["workphone2"],
				 "mobile1" => $companies[$i]["mobile1"],
				 "mobile2" => $companies[$i]["mobile2"],
				 "tegevuse_kirjeldus" => $companies[$i]["tegevuse_kirjeldus"],
			));
			$i++;
        }
		
		$table->define_pageselector(array(
				"type"=>"lb",
				"records_per_page"=>100,
				"position"=>"both",
		));
	}
	
	
	function _get_preview_persons_table($arr)
	{
		$table =& $arr["prop"]["vcl_inst"];
		
		$table->define_field(array(
			"name" => "id",
			"caption" => t("Id"),
			"sortable" => 1,
			"align" => "left",
			 "numeric" => 1
		));
		
		$table->define_field(array(
			"name" => "firstname",
			"caption" => t("Eesnimi"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "lastname",
			"caption" => t("Perenimi"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "company_name",
			"caption" => t("Firma"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "email",
			"caption" => t("Email"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "url",
			"caption" => t("URL"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "contact",
			"caption" => t("Siit v6tame telefonid"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "workphone1",
			"caption" => t("Töötelefon1"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "workphone2",
			"caption" => t("Töötelefon2"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "mobile1",
			"caption" => t("Mob1"),
			"sortable" => 1,
			"align" => "left"
		));
		
		$table->define_field(array(
			"name" => "mobile2",
			"caption" => t("Mob2"),
			"sortable" => 1,
			"align" => "left"
		));
		
		if ($arr["request"]["company_id"])
		{
			$persons = $this->get_persons(array(
					"company_id"=>$arr["request"]["company_id"]
			));
		}
		else
		{
			$persons = $this->get_persons();
		}

		$i=0;
        while($persons[$i])
        {
			$table->define_data(array(
				 "id" => $persons[$i]["id"],
				 "firstname" => $persons[$i]["firstname"],
				 "lastname" => $persons[$i]["lastname"],
				 "company_name" => $persons[$i]["company_name"],
				 "email" => $persons[$i]["email"],
				 "url" => $persons[$i]["url"],
				 "contact" => $persons[$i]["contact"],
 				 "workphone1" => $persons[$i]["workphone1"],
  				 "workphone2" => $persons[$i]["workphone2"],
				 "mobile1" => $persons[$i]["mobile1"],
 				 "mobile2" => $persons[$i]["mobile2"],
			));
			$i++;
        }
		
		$table->define_pageselector(array(
				"type"=>"lb",
				"records_per_page"=>100,
				"position"=>"both",
		));
	}
	
	function get_contacts_other($aParams = array())
	{
		$aOut = array();
		
		$q = "SELECT ".
				"c_id											AS id, ".
				"a_id											AS contacter1, ".
				"2a_id											AS contacter2, ".
				"individualid 									AS contacted, ".
				"UNIX_TIMESTAMP(contactdate)		AS cdate, ".
				"contacttypeid, ".
				"longdesc, ".
				"reason, ".
				"companyid,".
				"contacttypeid ".
				"FROM ".$this->db_prefix."contacts ".
				"WHERE contacttypeid = 2 OR 
				contacttypeid = 4 OR  
				contacttypeid = 5 OR 
				contacttypeid = 6 OR 
				contacttypeid = 7 ".
				"ORDER BY a_id ASC";
		
	    $this->db_query($q);
        while($w = $this->db_next())
        {
				if ($w["contacter2"] == 0)
				{
					$w["contacter2"] = "";
				}
			
				$aOut[] = array(
					 "id" => $w["id"],
					 "contacter1" => $w["contacter1"],
					 "contacter2" => $w["contacter2"],
					 "contacted" => $w["contacted"],
					 "cdate" => $w["cdate"],
					"longdesc" => utf8_decode($w["longdesc"]),
					"reason" =>  utf8_decode($w["reason"]),
					"companyid" => $w["companyid"],
					"contacttypeid" => $w["contacttypeid"],
				);
        }

		return $aOut;
	}
	
	function get_meetings($aParams = array())
	{
		$aOut = array();
		
		$q = "SELECT ".
				"c_id											AS id, ".
				"a_id											AS contacter1, ".
				"2a_id											AS contacter2, ".
				"individualid 									AS contacted, ".
				"UNIX_TIMESTAMP(contactdate)		AS cdate, ".
				"contacttypeid, ".
				"longdesc, ".
				"reason, ".
				"companyid ".
				"FROM ".$this->db_prefix."contacts ".
				"WHERE contacttypeid = 3 ".
				"ORDER BY a_id ASC";
		
	    $this->db_query($q);
        while($w = $this->db_next())
        {
			if ($w["contacter2"] == 0)
			{
				$w["contacter2"] = "";
			}
			
			$aOut[] = array(
				 "id" => $w["id"],
				 "contacter1" => $w["contacter1"],
				 "contacter2" => $w["contacter2"],
				 "contacted" => $w["contacted"],
				 "cdate" => $w["cdate"],
				"longdesc" => utf8_decode($w["longdesc"]),
				"reason" =>  utf8_decode($w["reason"]),
				"companyid" => $w["companyid"],
			);
        }

		return $aOut;
	}
	
	
	function get_phonecalls($aParams = array())
	{
		$aOut = array();
		
		$q = "SELECT ".
				"c_id											AS id, ".
				"a_id											AS contacter1, ".
				"2a_id											AS contacter2, ".
				"individualid 									AS contacted, ".
				"UNIX_TIMESTAMP(contactdate)		AS cdate, ".
				"contacttypeid, ".
				"longdesc, ".
				"reason, ".
				"companyid ".
				"FROM ".$this->db_prefix."contacts ".
				"WHERE contacttypeid = 1 ".
				"ORDER BY a_id ASC";
		
	    $this->db_query($q);
        while($w = $this->db_next())
        {
				if ($w["contacter2"] == 0)
				{
					$w["contacter2"] = "";
				}
			
				$aOut[] = array(
					 "id" => $w["id"],
					 "contacter1" => $w["contacter1"],
					 "contacter2" => $w["contacter2"],
					 "contacted" => $w["contacted"],
					 "cdate" => $w["cdate"],
					"longdesc" => utf8_decode($w["longdesc"]),
					"reason" =>  utf8_decode($w["reason"]),
					"companyid" => $w["companyid"],
				);
        }

		return $aOut;
	}
	
	function get_companies()
	{
		$aOut = array();
		$q = "SELECT id, name, address, url, email, contact, operation AS tegevuse_kirjeldus FROM ".$this->db_prefix."companies ORDER BY id ASC";
        $this->db_query($q);
		while ($w = $this->db_next() ) {$qQompanies[] = $w;}
		
		foreach($qQompanies as $w)
        {
			$q = "SELECT 
			aie_districts.name AS district_name 
			FROM 
			aie_districts, 
			aie_districttocompany 
			WHERE 
			companyid=".$w["id"]."
			AND 
			aie_districts.id = districtid;";
			
			$this->db_query($q);
			$a_branches = array();
			 while($w2 = $this->db_next())
			 {
			 	$a_branches[] = utf8_decode($w2["district_name"]);
			 }
		
			$aadress = utf8_decode($w["address"]);
			preg_match ("/[0-9]{5}|\s[0-9]{2}\s+[0-9]{3}/U", $aadress, $postal_index);
			$postal_index[0] = str_replace (" ", "",  $postal_index[0]);
			
			$sCities = "";
			foreach ($this->aCities as $key => $value)
			{
				$sCities.=$key."|";
			}
			$sCities = trim($sCities, "|");
			
			$city = false;
			if (preg_match("/($sCities)\s*,|($sCities)$/i", $aadress, $mt)!=0)
			{
				$city = $mt[1] ? $mt[1] : $mt[2];
			}
			
			$contact = utf8_decode($w["contact"]);
			$phones = $this -> parse_phonenumbers($contact);
			
			$aOut[count($aOut)] = array(
				 "id" => $w["id"],
				 "name" => utf8_decode($w["name"]),
				 "address" => utf8_decode($w["address"]),
				 "postal_index" => $postal_index[0],
				 "city" => $city,
				 "url" => $this->fix_url(utf8_decode($w["url"])),
				 "email" => $this->fix_email(utf8_decode($w["email"])),
				 "contact" => $contact,
				 "workphone1" => $phones["workphones"][0],
				 "workphone2" => $phones["workphones"][1],
				 "mobile1" => $phones["mobiles"][0],
				 "mobile2" => $phones["mobiles"][1],
				 "tegevuse_kirjeldus" => utf8_decode($w["tegevuse_kirjeldus"]),
				 "branches" => $a_branches,
			);
        }
		return $aOut;
	}
	
	function get_persons($aParams = array())
	{
		$aOut = array();
		
		if ($aParams["company_id"])
		{
			$q = "SELECT DISTINCT ".
			"i_id AS id, ".
			$this->db_prefix."companies.id AS company_id, ".
			$this->db_prefix."companies.name AS company_name, ".
			"forename, ".
			"lastname, ".
			$this->db_prefix."individuals.email, ".
			$this->db_prefix."individuals.url, ".
			$this->db_prefix."individuals.contact ".
			"FROM ".
			$this->db_prefix."individuals, ".
			$this->db_prefix."companies, ".
			$this->db_prefix."contacts ".
			"WHERE ".
			$this->db_prefix."contacts.companyid = ".$this->db_prefix."companies.id ".
			"AND ".
			$this->db_prefix."contacts.individualid = ".$this->db_prefix."individuals.i_id ".
			"AND ".
			$this->db_prefix."companies.id=".$aParams["company_id"]."
			ORDER BY id ASC";
		}
		else
		{
			$q = "SELECT DISTINCT ".
			"i_id AS id, ".
			//$this->db_prefix."companies.id AS company_id, ".
			//$this->db_prefix."companies.name AS company_name, ".
			"forename, ".
			"lastname, ".
			$this->db_prefix."individuals.email, ".
			$this->db_prefix."individuals.url, ".
			$this->db_prefix."individuals.contact ".
			"FROM ".
			//$this->db_prefix."companies, ".
			//$this->db_prefix."contacts, ".
			$this->db_prefix."individuals 
			ORDER BY id ASC";
			//"WHERE ".
			//$this->db_prefix."contacts.companyid = aie_companies.id ".
			//"AND ".
			//$this->db_prefix."contacts.individualid = aie_individuals.i_id";
		};
		
	    $this->db_query($q);
        while($w = $this->db_next())
        {
			$telephones = utf8_decode($w["contact"]);
			
			$phones = $this -> parse_phonenumbers($telephones);
			
			$aOut[count($aOut)] = array(
				 "id" => intval($w["id"]),
				 "firstname" => utf8_decode($w["forename"]),
				 "lastname" => utf8_decode($w["lastname"]),
				 "company_id" => $w["company_id"],
				 "company_name" => utf8_decode($w["company_name"]),
 				 "email" => $this->fix_email(utf8_decode($w["email"])),
  				 "url" => $this->fix_url(utf8_decode($w["url"])),
				 "contact" => $telephones,
				 "workphone1" => $phones["workphones"][0],
  				 "workphone2" =>$phones["workphones"][1],
				 "mobile1" => $phones["mobiles"][0],
				 "mobile2" => $phones["mobiles"][1],
			);
        }

		return $aOut;
	}
	
	/**
	 *
	 *
	 */
	function parse_phonenumbers($sContact)
	{
		//$sContact = "+372 6060111, +372 50 15 126,";
		//$sContact = "+372 668 4774";
		//$sContact = "N/A";
		//$sContact = "(0) 6129130";
		//$sContact  = "501.6765 & 5018421";
		$sContact = trim($sContact, " ,");
		$is_set = false;
		
		if (strpos($sContact, "N/A")===0)
		{
			$aNumbers["workphones"][0] = "";
			return $aNumbers;
		}
		
		if (strpos($sContact, "/001"))
		{
			$aNumbers["workphones"][0] = $sContact;
			return $aNumbers;
		}
		
		$sContact = trim (str_replace(array(";", "-", "0/","/", ".","(0)", "&"), array(",", " ", "",",", " ", "", ","), $sContact));
		
		$aNumbers["workphones"] = array();
		$aNumbers["mobiles"] = array();
		
		//echo $sContact."<br>";
		
		if (preg_match ( "/[a-zA-Z]/", $sContact)==0)
		{
			$i = 0;
			while (strlen($sContact)>0 && $i<10)
			{
				//while (preg_match("/\+\s*372\s*5\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]?|5\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]?/", $sContact, $tmp  ))
				//while (preg_match("/^\+\s*372\s*5\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]?\s*[0-9]?[\s|$]|^5\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]\s*[0-9]?\s*[0-9]?[\s|$]/", $sContact, $tmp  ))
				while (preg_match("/^\+\s*372\s*0?\s*5\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]?\s*[0-9]?(\s*|$]|,)|^0?5\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]\s*[0-9]?\s*[0-9]?(\s*|$|,)/", $sContact, $tmp  ))
				{
					$sContact = trim(str_replace ($tmp[0], "", $sContact), " ,");
					$aNumbers["mobiles"][count($aNumbers["mobiles"])] = $this->fix_phonenr($tmp[0]);
					$is_set = true;
				}
			
				//while (preg_match("/\+\s*372\s*[012346789]\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]?|[12346789]\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]?/", $sContact, $tmp  ))
				while (preg_match("/^\+\s*372\s*[012346789]\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]?(\s*|$|,])|^0?[12346789]\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]{1}\s*[0-9]?(\s*|$]|,)/", $sContact, $tmp  ))
				{
					$sContact = trim(str_replace ($tmp[0], "", $sContact), " ,");
					$aNumbers["workphones"][count($aNumbers["workphones"])] = $this->fix_phonenr($tmp[0]);
					$is_set = true;
				}
				$i++;
			}
			
			//die (arr($aNumbers));
		}
		else
		{
			// if no match found then import all from contact field to workphone1
			$aNumbers["workphones"][0] = $sContact;
		}
			
		if ($is_set == false )
		{
			// if no match found then import all from contact field to workphone1
			$aNumbers["workphones"][0] = $sContact;
		}
		
		return $aNumbers;
	}
	
	function fix_phonenr($sNumber)
	{

		$sNumber = trim ($sNumber);

		if (strlen($sNumber)>0)
		{
			if (strpos ( $sNumber, "0") === 0 )
			{
				$sNumber = substr($sNumber, 1);
			}
			
			if (strpos ( $sNumber, "+") === false )
			{
				$sNumber = "+372" . $sNumber;
			}
			
			$sNumber = str_replace(array("(", ")", " "), array("", "", ""), $sNumber);
			$sNumber = substr($sNumber, 0, 4) . " " . substr($sNumber, 4, 1) . " " . substr($sNumber, 5, 3) . " " . substr($sNumber, 8) ;
			$sNumber = str_replace("+372", "(+372)", $sNumber );
		}
		return $sNumber;
	}
	
	function fix_email($sEmail)
	{
		if (strlen($sEmail)>3)
		{
			if (strpos($sEmail, "@")!==false)
			{
				return $sEmail;
			}
		}
	}
	
	function fix_url($sUrl)
	{
		if (strlen($sUrl)>3)
		{
			if (strpos($sUrl, "http://")===false && strpos($sUrl, "https://")===false && strpos($sUrl, "ftp://")===false)
			{
				return "http://".$sUrl;
			}
			else
			{
				return $sUrl;
			}
		}
	}

	function _get_preview_companies_count($arr)
	{
		$q = "SELECT COUNT(*) AS count FROM ".$this->db_prefix."companies;";
		$this->db_query($q);
        $w = $this->db_next();
		$arr["prop"]["value"] = $w["count"];
	}
	
	function _get_preview_persons_count($arr)
	{
		$q = "SELECT COUNT(*) AS count FROM ".$this->db_prefix."individuals;";
		$this->db_query($q);
        $w = $this->db_next();
		$arr["prop"]["value"] = $w["count"];
	}
	

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		
		switch($prop["name"])
		{
			case "do_import":
				if ($prop["value"] == 1)
				{
					$o = $arr["obj_inst"];
					header("Location: ".aw_ini_get("baseurl")."/automatweb/orb.aw?class=aiesecimport&action=do_import&import_parent=".$o->prop("importfolder"));
					die();
				}
			break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
