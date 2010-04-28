<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/protocols/file/ocsp.aw,v 1.3 2008/01/31 13:55:21 kristo Exp $
// ocsp.aw - OCSP p&auml;ring 
/*
@classinfo  maintainer=tarvo
*/

class ocsp extends class_base
{
	const AW_CLID = 1207;

	function ocsp()
	{
		$this->init(array(
			"clid" => CL_OCSP
		));
	}

	/**
		@attrib params=pos api=1
		@param cert optional type=string
			user certificate in PEM format
		@param issuer_dn optional type=string
			issuer domain name 

		@comment
			checks wheather the given certificate is valid or not
	 	@returns
			 0 - OCSP certificate status unknown
			 1 - OCSP certificate status valid
			 2 - OCSP internal error
			 4 - Some error in script
	**/
	function OCSP_check($cert = false,  $issuer_dn = false)
	{
		$cert = $cert?$cert:$_SERVER["SSL_CLIENT_CERT"];
		$issuer_dn = $issuer_dn?$issuer_dn:$_SERVER["SSL_CLIENT_I_DN_CN"];
		$user_good = 0;
		
		if(!aw_ini_get("id_config.use_ocsp"))
		{
			return 1;
		}

		// Saving user certificate file to OCSP temp folder
		$tmp_f = fopen($tmp_f_name = tempnam(aw_ini_get("ocsp.temp_dir"),'ocsp_check'),'w');
		fwrite($tmp_f, $cert);
		fclose($tmp_f);
		
		
		$ca_cert_file = aw_ini_get("ocsp.".str_replace(" ", "-", strtolower($issuer_dn))."-"."ca_cert_file");
		$ocsp_server_cert_file = aw_ini_get("ocsp.".str_replace(" ", "-", strtolower($issuer_dn))."-"."ocsp_server_cert_file");
		$ocsp_server_url = aw_ini_get("ocsp.".str_replace(" ", "-", strtolower($issuer_dn))."-"."ocsp_server_url");


		if (aw_ini_get("id_config.use_ocsp") && isset($ca_cert_file) && isset($ocsp_server_cert_file) && isset($ocsp_server_url))
		{

			// Making OCSP request using OpenSSL ocsp command
			$command = aw_ini_get("server.open_ssl_bin").' ocsp -issuer '.$ca_cert_file.' -cert '.$tmp_f_name.' -url '.$ocsp_server_url.' -VAfile '.$ocsp_server_cert_file;

			$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
				1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
				2 => array("pipe", "w") // stderr is a pipe that the child will write to
			);

			$process = proc_open($command, $descriptorspec, $pipes);

			if (is_resource($process))
			{
				fclose($pipes[0]);


				// Getting errors from stderr
				$errorstr = "";
				while ($line = fgets($pipes[2]))
				{
					$errorstr .= $line;
				}

				if ($errorstr!="")
				{
					$user_good = 4;
				}
				else
				{
					// Parsing OpenSSL command stdout
					while ($line = fgets($pipes[1]))
					{
						if (strstr($line,'good'))
						{
							$user_good = 1;
						}
						else if(strstr($line,'internalerror (2)'))
						{
							$user_good = 2;
						}
					}
					fclose($pipes[1]);
				}

				proc_close($process);
			}
		}
		return $user_good;
	}

}
?>
