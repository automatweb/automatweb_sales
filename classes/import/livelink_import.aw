<?php
// $Header: /home/cvs/automatweb_dev/classes/import/livelink_import.aw,v 1.29 2008/07/15 12:30:18 markop Exp $
// livelink_import.aw - Import livelingist

/*
	@default table=objects
	@default group=general
	@default field=meta
	@default method=serialize

	@property rootnode type=textbox size=40 maxlength=40
	@caption Juurika ID (eralda komadega)
	
	@property exception_node type=textbox size=40 maxlength=40
	@caption Erandite ID (eralda komadega)


	@property outdir type=textbox 
	@caption Kataloog, kuhu failid kirjutada

	@property fileprefix type=textbox
	@caption Prefiks tabelisse kirjutatavatele failinimedele

	@property message type=text editonly=1
	@caption Objekti staatus

	@property invoke type=text editonly=1
	@caption K&auml;ivita

	@default group=auth
	@property ll_username type=textbox size=40
	@caption Livelink kasutaja
	
	@property ll_password type=password size=40
	@caption Livelink parool

	@property ll_http_auth type=checkbox ch_value=1
	@caption HTTP autoriseerimisel samad andmed

	@groupinfo auth caption="Autoriseerimine"

	@classinfo syslog_type=ST_LIVELINK_IMPORT relationmgr=yes maintainer=kristo

	@reltype LOGFILE value=1 clid=CL_FILE
	@caption Logifail

*/

class livelink_import extends class_base
{
	function livelink_import()
	{
		$this->init(array(
			'clid' => CL_LIVELINK_IMPORT
		));
	}

	// if import progress needs to be redirected to a file, then it can be done in this method
	function log_progress($msg)
	{
		$tm = date("Y-m-d H:i:s");
		print $tm . "\t" . $msg . "<br>";
		$this->logdata .= $tm . "\t" . $msg . "\n";
	}

	function get_property($args)
	{
		$data = &$args["prop"];
		switch($data["name"])
		{
			case "outdir":
				if ($args["new"])
				{
					$data["value"] = aw_ini_get("site_basedir") . "/public";
				};
				break;
			case "invoke":
				list($err,) = $this->_chk_outdir($args["obj_inst"]->prop("outdir"));
				if (!$err)
				{
					$data["value"] = html::href(array(
						"url" => $this->mk_my_orb("invoke",array("id" => $args["obj_inst"]->id())),
						"caption" => $data["caption"],
					));
				}
				
				break;

			case "message":
				list($error,$data["value"]) = $this->_chk_outdir($args["obj_inst"]->prop("outdir"));
				break;

		}
		return $retval;
	}

	function _chk_outdir($_dir)
	{
		$error = true;
		$msg = "";
		if (!file_exists($_dir))
		{
			$msg = "Sellist kataloogi pole";
		}
		else
		if (!is_dir($_dir))
		{
			$msg = "See pole kataloog";
		}
		else
		if (!is_writable($_dir))
		{
			$msg = "V&auml;ljundkataloog pole kirjutatav!";
		}
		else
		{
			$msg = "OK";
			$error = false;
		};
		return array($error,$msg);
	}

	function _open_logfile($arr)
	{
		$o = new object($arr["id"]);
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_LOGFILE",
		));

		if (sizeof($conns) == 0)
		{
			$this->log_file_parent = $o->parent();
		}
		else
		{
			$first = reset($conns);
			$this->log_file_id = $first->prop("to");
		};
		$this->logdata = "";
		#var_dump($this->log_file_parent);
		#var_dump($this->log_file_id);
	}

	function _close_logfile($id)
	{
		$t = get_instance(CL_FILE);
		aw_disable_acl();
		$file_id = $t->create_file_from_string(array(
			"parent" => $this->log_file_parent,
			"id" => $this->log_file_id,
			"content" => $this->logdata,
			"name" => "Livelink_import_" . date("Y-m-d") . ".txt",
		));
		if (empty($this->log_file_id))
		{
			$o = new object($id);
			$o->connect(array(
				"to" => $file_id,
				"reltype" => "RELTYPE_LOGFILE",
			));
		};
		aw_restore_acl();
		//print "closing logfile<br>";
	}

	function _finish_import($arr)
	{
		if ($arr["success"])
		{
			$this->log_progress("Livelink import succeeded");
		}
		else
		{
			$this->log_progress("Livelink import failed!");
		};
		$this->_close_logfile($arr["id"]);
	}

	/**
		@attrib name=invoke params=name default="0" nologin=1
		@param id required type=int
		@returns
		@comment
	**/
	function invoke($args = array())
	{
if (aw_global_get("uid") == "")
{
die("ajutiselt suletud");
}
		$this->put_file(array(
			"file" => "/export/aw/automatweb/new/files/livelink_import_".date("d.m.Y H:i:s").".invoked",
			"content" => aw_global_get("uid")
		));
		$this->_open_logfile(array(
			"id" => $args["id"],
		));
		$obj = new object($args["id"]);
		$outdir = $obj->prop("outdir");
		list($err,$msg) = $this->_chk_outdir($outdir);
		if ($err)
		{
			$this->log_progress($msg);
			$this->_finish_import(array(
				"success" => false,
				"id" => $args["id"],
			));
			die();
		};

		print "<pre>";

		$this->icons = array(
			"pdf" => "apppdf.gif",
			"txt" => "apptext.gif",
			"rtf" => "apptext.gif",
			"doc" => "appword.gif",
			"htm" => "appiexpl.gif",
			"html" => "appiexpl.gif",
			"xls" => "appexel.gif",
			"csv" => "appexel.gif",
		);

		$rootnodes = explode(",",$obj->prop("rootnode"));

		$this->exceptions = explode(",",$obj->prop("exception_node"));

		foreach($rootnodes as $rootnode)
		{
			$this->import_livelink_structure(array(
				"outdir" => $outdir,
				"rootnode" => (int)$rootnode,
				"fileprefix" => $obj->prop("fileprefix"),
				"ll_username" => $obj->prop("ll_username"),
				"ll_password" => $obj->prop("ll_password"),
				"ll_http_auth" => $obj->prop("ll_http_auth"),
			));
		};

		$this->_finish_import(array(
			"success" => true,
			"id" => $args["id"],
		));
		print "</pre>";
	}

	function import_livelink_structure($args = array())
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		$this->tmpdir = aw_ini_get("server.tmpdir");
		$this->outdir = $args["outdir"];
		$this->rootnode = $args["rootnode"];
		$this->fileprefix = $args["fileprefix"];

		$this->ll_username = $args["ll_username"];
		$this->ll_password = $args["ll_password"];

		$this->http_username = $this->http_password = "";

		if (!empty($args["ll_http_auth"]))
		{
			$this->http_username = $args["ll_username"];
			$this->http_password = $args["ll_password"];
		};

		$this->docs_to_retrieve = array();
		$this->file_id_list = array();
                $this->need2update = array();

		// first, we create a list of _all_ files inside the table
		// then .. after we have done our processing, we should have 
		// a list of stuff that we can actually delete

		ob_implicit_flush(1);

                $this->log_progress("going to fetch structure");
                $outf = $this->fetch_structure();
                $this->log_progress("done!");

                # parse the structure
                $xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
                xml_set_object($xml_parser,&$this);
                xml_set_element_handler($xml_parser,"_xml_start_element","_xml_end_element");
                $xml_data = join("",file($outf));
		//die("<pre>".htmlentities(join("",file($outf))));
                $xml_data = str_replace("\r","",$xml_data);

                if (!xml_parse($xml_parser,$xml_data))
                {
			$xs = get_instance("xml/xml_parser");
			$xs->bitch_and_die(&$xml_parser,&$xml_data);
                };
                
		foreach($this->docs_to_retrieve as $node_id)
                {
                        $this->fetch_node($node_id);
                };
		if (sizeof($this->file_id_list) > 0)
		{
			$flist = join(",",$this->file_id_list);
			$this->log_progress("and now I'm going to delete these files");
			$q = "SELECT filename FROM livelink_files WHERE id IN ($flist)";
			$this->db_query($q);
			while($row = $this->db_next())
			{
				$outfile = $this->outdir . "/" . basename($row["filename"]);		
				$this->log_progress("deleting $outfile");
				unlink($outfile);
			};

			$q = "DELETE FROM livelink_files WHERE id IN ($flist)";
			echo "delete $q <br>";
			$this->db_query($q);
		};

		# clean up too after ourselves
		unlink($outf);

        }

	function _xml_start_element($parser,$name,$attribs)
	{
		// 0 on tavaline folder
		// 136 on compound document -- duke
		if ($name == "llnode" && isset($attribs["objtype"]) && ($attribs["objtype"] == 0 || $attribs["objtype"] == 136))
		{
			$name = $attribs["name"];
			$realname = preg_replace("/^\d+?\.\s/","",$name);
			$description = isset($attribs["description"]) ? $attribs["description"] : "";
			$id = $attribs["id"];
			$parent = $attribs["parentid"];
			$modified = $this->mystrtotime($attribs["modified"]);
			$rootnode = $this->rootnode;


			$old = $this->db_fetch_row("SELECT * FROM livelink_folders WHERE id = '$id'");
		
			// we always scan the contents of folders (files) for changes
			$this->need2update[] = $id;

			if (in_array($id,$this->exceptions))
			{
				//$this->need2update[] = $id;
			}
			else
			if (empty($old))
			{
				# so it must be new
				$this->quote($name);
				$this->quote($realname);
				$this->quote($description);
				$icon = ($attribs["objtype"] == 136) ? "compound_doc.gif" : "folder.gif";
				$iconurl = sprintf("<img src='/img/%s' alt='' title='' />",$icon);
				$this->quote($iconurl);
				$this->log_progress("creating $name\n");
				$q = "INSERT INTO livelink_folders  (id,name,realname,description,parent,modified,rootnode,icon)
					VALUES ('$id','$name','$realname','$description','$parent','$modified','$rootnode','$iconurl')";
				$this->log_progress($q);
				//$this->need2update[] = $id;
				$this->db_query($q);
			}
			else
			if ($modified > $old["modified"])
			{
				# update existing one
				$this->log_progress("renewing $name");
				$this->quote($name);
				$this->quote($description);
				$this->quote($realname);
			
				$q = "SELECT id FROM livelink_files WHERE parent = '$id' AND rootnode = '$rootnode'";
				echo "q = $q <br>";
				$this->db_query($q);
				while($row = $this->db_next())
				{
					$this->file_id_list[$row["id"]] = $row["id"];
				};

				$q = "UPDATE livelink_folders SET
					name = '$name',description = '$description',parent = '$parent',
					realname = '$realname',
					modified = '$modified',	
					rootnode = '$rootnode'
					WHERE id = '$id'";
				//$this->need2update[] = $id;

				$this->log_progress($q);
				$this->db_query($q);
			}
			/*
			else
			{
				//print "not touching $name, since it has not been modified\n";
			};
			*/
                }
		if ($name == "llnode" && isset($attribs["objtype"]) && ($attribs["objtype"] > 0 || $attribs["objtype"] != 136))
		{
			// only retrieve the docs, if the parent has been modified
			// siin me koostame siis nimekirja docid-dest, mida oleks vaja uuendada
			if (in_array($attribs["parentid"],$this->need2update))
			{
				$this->docs_to_retrieve[] = $attribs["id"];
				if (isset($this->file_id_list[$attribs["id"]]))
				{
					unset($this->file_id_list[$attribs["id"]]);
				};
			};
		}
	}

	function _xml_end_element($parser,$name)
        {
                //print "name $name ends<br />";
        }

	function fetch_structure()
        {
		$outfile = tempnam($this->tmpdir,"aw-");
		$rootnode = $this->rootnode;
		$ll_username = $this->ll_username;
		$ll_password = $this->ll_password;
		$http_auth_str = "";
		if (!empty($this->http_username))
		{
			$http_auth_str = $this->http_username . ":" . $this->http_password . "@";
		};
		// 'https://${http_auth_str}dok.ut.ee/livelink/livelink?func=LL.login&username=${ll_username}&password=${ll_password}' 
		$cmdline = "wget --no-check-certificate -O $outfile 'https://${http_auth_str}dok.ut.ee/livelink/livelink?func=LL.login&username=${ll_username}&password=${ll_password}' 'https://${http_auth_str}dok.ut.ee/livelink/livelink?func=ll&objId=${rootnode}&objAction=XMLExport&scope=sub&versioninfo=current&schema' --no-check-certificate 2>&1";
		$this->log_progress("executing $cmdline");
		passthru($cmdline,$retval);
		//var_dump($retval);
		$this->log_progress("got structure, parsing");
		// check whether opening succeeded?
		$fc = join("",file($outfile));
		// reap the bloody header
		$real_xml = substr($fc,strpos($fc,"<?xml"));
		$fh = fopen($outfile,"w");
		fwrite($fh,$real_xml);
		fclose($fh);
		return $outfile;
	}

	function fetch_node($node_id)
	{
		$outfile = tempnam($this->tmpdir,"aw-");	
		$ll_username = $this->ll_username;
		$ll_password = $this->ll_password;
		$http_auth_str = "";
		if (!empty($this->http_username))
		{
			$http_auth_str = $this->http_username . ":" . $this->http_password . "@";
		};
		$cmdline = "wget --no-check-certificate -O $outfile 'https://${http_auth_str}dok.ut.ee/livelink/livelink?func=LL.login&username=${ll_username}&password=${ll_password}'  'https://${http_auth_str}dok.ut.ee/livelink/livelink?func=ll&objId=${node_id}&objAction=XMLExport&scope=sub&versioninfo=current&schema&content=base64'";
		$this->log_progress("executing $cmdline");
		passthru($cmdline);
		// check whether opening succeeded?
		$fc = join("",file($outfile));
		// reap the bloody header
		$real_xml = substr($fc,strpos($fc,"<?xml"));
		$fh = fopen($outfile,"w");
		fwrite($fh,$real_xml);
		fclose($fh);
		sleep(3);

		$this->log_progress("entering parser");
		
		$this->parse_file($outfile);
		unlink($outfile);
        }

	function parse_file($fname)
	{
		$infile = $fname;
		$xml_data = join("",file($infile));
		#$xml_data = str_replace("\r","",$xml_data);
		/*
		print "<pre>";
		print htmlspecialchars($xml_data);
		print "</pre>";
		*/
		$this->catch_content = false;
		$this->content = "";
		$this->filename = "";

		$xml_parser = xml_parser_create();
		xml_set_object($xml_parser,&$this);
		xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
		xml_set_element_handler($xml_parser,"_xml_file_start_element","_xml_file_end_element");
		xml_set_character_data_handler($xml_parser,"_xml_file_cdata_handler");
		if (!xml_parse($xml_parser,$xml_data))
		{
			$xs = get_instance("xml/xml_parser");
			$xs->bitch_and_die(&$xml_parser,&$xml_data);
		};

		# parse the structure
		if ($this->filename)
		{
			$this->_existing_files[] = $this->filename;
			#$name = $this->name;
			$name = $this->desc;
			$realname = preg_replace("/^\d+?\.\s/","",$name);
			$id = $this->id;
			$parent = $this->parentid;
			$modified = $this->modified;
			$filename = $this->fileprefix . $this->filename;
			$old = $this->db_fetch_row("SELECT parent,modified FROM livelink_files WHERE id = '$id'");
			$iconurl = "";
			if ($this->icons[$this->fext])
			{
				$iconurl = sprintf("<img src='/img/%s' alt='%s' title='%s' />",$this->icons[$this->fext],$this->fext,$this->fext);
				$this->quote($iconurl);
			};
			$rootnode = $this->rootnode;
			if (in_array($parent,$this->exceptions))
			{
				$write_result = $this->write_outfile();
			}
			else
			if (empty($old))
			{
				$this->log_progress("creating file $filename");
				$write_result = $this->write_outfile();
				if ($write_result)
				{
					$this->quote($name);
					$this->quote($filename);
					$this->quote($realname);
					// wah, wah
					$q = "INSERT INTO livelink_files (id,parent,name,realname,filename,modified,icon,rootnode)
					VALUES('$id','$parent','$name','$realname','$filename','$modified','$iconurl','$rootnode')";
					$this->db_query($q);
				};
			}
			else
			//if ($modified > $old["modified"])
			if (($modified > $old["modified"]) || ($parent != $old["parent"]) || strlen(base64_decode(trim($this->content))) != filesize($this->outdir . "/" . $this->filename))
			{
				$this->log_progress("updating file $filename");
				// wah, wah
				$write_result = $this->write_outfile();
				if ($write_result)
				{
					$this->quote($name);
					$this->quote($filename);
					$this->quote($realname);
					$q = "UPDATE livelink_files SET
					parent = '$parent',name = '$name',realname = '$realname',filename = '$filename',
					modified = '$modified', rootnode = '$rootnode',
					icon = '$iconurl'
					WHERE id = '$id'";
					$this->db_query($q);	
					$this->log_progress($q);
				};
			}
			else
			{
				$this->log_progress("$modified <= " . $old["modified"]);
				$this->log_progress("not touching $filename, it has not been modified");
			};
		};
	}

	function write_outfile()
	{
		$outfile = $this->outdir . "/" . $this->filename;
		$this->log_progress("writing $outfile");
		error_reporting(E_ALL ^ E_NOTICE);
		$fh = fopen($outfile,"w");
		if ($fh)
		{
			fwrite($fh,base64_decode(trim($this->content)));
			fclose($fh);
			$rv = true;
		}
		else
		{
			$this->log_progress("Cannot open $outfile for writing, skipping. Check permissions");
			$rv = false;
		};
		return $rv;
	}

        function _xml_file_start_element($parser,$name,$attribs)
        {
                if (($name == "content") && ($attribs["type"] == "base64"))
                {
                        $this->catch_content = true;
                };

                if (($name == "version") && isset($attribs["filename"]))
                {
			#print "version node properties:<br />";
                        $this->filename = $this->id . "-" . $attribs["filename"];
			$fext  = array_pop(explode('.', $attribs["filename"]));	
			$this->fext = $fext;
			$this->filename = $this->id . "." . $fext;
                        $this->name = ($attribs["name"]) ? $attribs["name"] : $attribs["filename"];
			$this->alg = $attribs["modifydate"];
                        $this->modified = $this->mystrtotime($attribs["modifydate"]);
                };

                if (($name == "llnode") && isset($attribs["parentid"]))
                {
                        $this->parentid = $attribs["parentid"];
                        #$this->desc = $attribs["description"];
			$this->log_progress("setting name to $attribs[name]");
			$this->desc = $attribs["name"];
			$this->log_progress("llnode node properties:");
			$this->log_progress(print_r($attribs,true));
                        $this->id = $attribs["id"];
                };
        }

	function _xml_file_end_element($parser,$name)
	{
		if ($this->catch_content)
		{
			$this->catch_content = false;
		};
	}

	function _xml_file_cdata_handler($parser,$data)
	{
		if ($this->catch_content)
		{
			$this->content .= $data;
		};
	}

	// creates a usable timestamp from YYYY-MM-DDTHH:MM:SS
	function mystrtotime($orig)
	{
		$p = unpack("a4year/c1e/a2mon/c1e/a2day/c1e/a2hour/c1e/a2min/c1e/a2sec",$orig);
		return mktime($p["hour"],$p["min"],$p["sec"],$p["mon"],$p["day"],$p["year"]);
	}

}
?>
