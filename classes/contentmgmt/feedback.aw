<?php
/*
@classinfo  maintainer=kristo
*/
class db_feedback extends aw_template 
{
	function db_feedback() 
	{
		$this->init("");
		$this->tekst[0] = "Nii nagu vaja, lihtne ja arusaadav.";
		$this->tekst[1] = "Arusaadav, kuid konarlik. Vajab toimetamist.";
		$this->tekst[2] = "Liiga keeruline või tehniline.";
		$this->tekst[3] = "Liiga pealiskaudne või mittemidagiütlev.";

		$this->kujundus[0] = "Laitmatu kujundus.";
		$this->kujundus[1] = "Liiga hall ja üksluine kujundus.";
		$this->kujundus[2] = "Liiga pealetükkiv kujundus.";

		$this->struktuur[0] = "Laitmatu menüüde loogika.";
		$this->struktuur[1] = "Menüüde loogika hästi taibatav.";
		$this->struktuur[2] = "Menüüde loogika kehvasti taibatav.";

		$this->tehnika[1] = "Sait on piisaval määral kiire.";
		$this->tehnika[2] = "Sait on liiga aeglane.";
		$this->tehnika[4] = "Olen saanud ühe veateate (ERROR).";
		$this->tehnika[8] = "Olen saanud rohkem kui ühe veateate.";

		$this->ala[0] = "Tehnika ja tootmine";
		$this->ala[1] = "Arvutid ja Internet";
		$this->ala[2] = "Müük ja turundus";
		$this->ala[3] = "Muu";
	}
	
	function add_feedback($data) 
	{
		$this->quote(&$data);
		extract($data);
		$ip = $_SERVER["REMOTE_ADDR"];
		$host = gethostbyaddr($ip);
		if ($more == "")
		{
			header("Location: ".aw_ini_get("baseurl")."/?class=document&action=feedback&section=$docid&e=1".($print ? "&print=".$print : ""));
			die();
		}
		$msg = "
Dokument: ".aw_ini_get("baseurl")."/?section=$docid\n
Pealkirjaga: $title\n
\n
Arvamust avaldas:\n
Nimi: $eesnimi $perenimi
$gender
E-post: $mail
IP: $ip/$host";
		if ($homepage)
		{
			$msg .= "Koduleht: $homepage\n";
		}
		if ($ala)
		{
			$msg .= "Tegevusala: " . $this->ala[$ala] . "\n";
		}

		if ($tekst)
		{
			$msg .= "Tekst: " . $this->tekst[$tekst] . "\n";
		}

		if ($kujundus)
		{
			$msg .= "Kujundus: " . $this->kujundus[$kujundus] . "\n";
		}
		if ($struktuur)
		{
			$msg .= "Struktuur: " . $this->struktuur[$struktuur] . "\n";
		}

		$tsum = 0;
		if (is_array($tehnika)) 
		{
			$msg .= "Tehnika: \n";
			while(list($k,$v) = each($tehnika)) 
			{
				$msg .= $this->tehnika[$v] . "\n";
				$tsum = $tsum + $v;
			};
		};

		$msg .= "\nTäpsustav tekst:\n" . $more . "\n";
		if ($wantsnews1) 
		{
			$msg .= "\nSoovib dokumendi teemaga seonduvat e-uudiskirja\n";
		};
		if ($wantsnews2) 
		{
			$msg .= "\nSoovib teadet e-postile, kui toimuvad suuremad muudatused Struktuur Meedia kodulehel.\n";
		};
		if ($wantsfeedback) 
		{
			$msg .= "\nSoovin oma arvamusele/ettepanekule personaalset tagasisidet\n";
		};
		$headers = "From: $eesnimi $perenimi <$mail>";
		$t = time();
		$q = "INSERT INTO feedback (docid,time,tekst,kujundus,
					struktuur,tehnika,ala, more, gender,
					eesnimi, perenimi, mail, homepage,
					wantsnews1, wantsnews2, wantsfeedback)
					VALUES('$docid','$t','$tekst','$kujundus','$struktuur',
						'$tsum','$ala','$more','$gender',
						'$eesnimi','$perenimi','$mail',
						'$homepage','$wantsnew1','$wantsnew2',
						'$wantsfeedback')";
		$this->db_query($q);
		$to = aw_ini_get("feedback.mail_to");
		if ($to == "")
		{
			$to = "content@struktuur.ee";
		}
		$tos = explode(",", $to);
		foreach($tos as $ml)
		{
			if ($ml != "")
			{
				send_mail($ml,"\"$title\"",$msg,"$headers");
			}
		}
	}
};

class feedback extends db_feedback
{
	function feedback()
	{
		$this->db_feedback();
	}
}
?>
