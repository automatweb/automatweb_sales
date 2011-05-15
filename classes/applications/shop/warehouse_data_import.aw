<?php
class warehouse_data_import extends run_in_background 
{

	var $logname = '/tmp/taket_prod_imp_log.log';

	var $data = array();

	var $sdb_handler = null;

	var $debug = true;

	function warehouse_data_import()
	{
	}

	// init tasks for process 
	function bg_run_init($o)
	{
		// This is probably a temporary thing here as well, until i figure out how it would be nice to get the warehouse object here

		$this->bg_checkpoint_steps = 1000 ;
		$this->bg_log_steps = 50;

		$this->write_log('see tausta asi l2ks nyyd k2ima');

		// this is for otto for now, no background and other crap
		// just try to get some products imported 
		$file = $this->get_xml_data($o);

		$this->load_data($file);

		// I need the warehouse object to be accessible in warehouse_products_import
		$wh = $this->get_warehouse($o);
		if ($wh_conf = $wh->prop('conf'))
		{
			$conf_obj = new object($wh_conf);
			$this->packets_folder = $conf_obj->prop('pkt_fld');
			$this->products_folder = $conf_obj->prop('prod_fld');
			$this->categories_folder = $conf_obj->prop('prod_cat_fld');
		}
		

		/*
		if ($this->start_new_import() === true)
		{
			$this->set_status_element('status', 'started');
			$this->set_status_element('start_time', time());

			$xml_file_path = $this->get_xml_data($o);

			$this->divide_to_pieces($xml_file_path);
		}
		else
		{
			$file = $this->get_status_element('current_file');
			if (empty($file))
			{
				$files = $this->get_chunk_files($this->get_import_start_time());
				foreach ($files as $f)
				{
					if (strpos('DONE', $f) === false)
					{
						$file = $f;
						$this->set_status_element('current_file', $file);
					}
				}
			}
			$this->load_data($file);
		}
		*/
	}

	// for continuing the 
	function bg_run_continue($o)
	{
		$this->write_log('bg_run_continue');
		// here i have to resume the state
	}
	
	// to save the checkpoint 
	function bg_checkpoint($o)
	{
		$this->write_log('bg_checkpoint');
		// save state
	}

	// what needs to be done in a step
	function bg_run_step($o)
	{
		$item = current($this->data);

		if ($item !== false)
		{
			$this->process_item($item);

			// advance the internal pointer of the array by one
			next($this->data);

			return BG_OK;
		}
		else
		{
			return BG_DONE;
		}

	}

	// for clean-up tasks
	function bg_run_finish($o)
	{
		$this->write_log('bg_run_finish');
		
		// any clean-up which is needed
	}

	function get_datasource($o)
	{
		$ds = $o->prop('data_source');
		return new $ds();
	}

	function start_new_import()
	{
		if ($this->get_import_status() == 'started')
		{
			return false;
		}
		return true;
	}

	function get_status_filename()
	{
		$fn = aw_ini_get("site_basedir")."/files/warehouse_import";
		if (!is_dir($fn))
		{
			mkdir($fn);
			chmod($fn, 0777);
		}
		$file = $fn.'/'.get_class($this).'.stat';
		if (file_exists($file))
		{
			chmod($file, 0660);
		}
		return $file;
	}

	
	public function get_import_status()
	{
		return (string)$this->get_status_element('status');
	}

	public function get_import_start_time()
	{
		return (int)$this->get_status_element('start_time');
	}

	function set_status_element($element, $value)
	{
		$fn = $this->get_status_filename();
		if (file_exists($fn))
		{
			$xml = new SimpleXMLElement($fn, null, true);
		}
		else
		{
			$xml = new SimpleXMLElement('<stat />');
		}
		$xml->$element = $value;
		$xml->asXML($fn);
	}

	function get_status_element($element)
	{
		$fn = $this->get_status_filename();
		if (file_exists($fn))
		{
			$xml = new SimpleXMLElement($fn, null, true);
			return $xml->$element;
		}
		return false;
	}

	function clear_status()
	{
		$fn = $this->get_status_filename();
		$xml = new SimpleXMLElement('<stat />');
		$xml->asXML($fn);
	}

	function write_xml_file($content)
	{
		$this->chunk_counter += 1000;
		$ts = $this->get_status_element('start_time');
		$fn = aw_ini_get('site_basedir').'/files/warehouse_import/products/chunk_'.$ts.'_'.$this->chunk_counter.'.xml';
		$content->asXML($fn);
	}

	function get_chunk_files($ts = 0)
	{
		$folder = $this->get_data_folder();
		if (!empty($ts))
		{
			$fn = $folder.'/chunk_'.(int)$ts.'_*.xml';
		}
		else
		{
			$fn = $folder.'/chunk_*.xml';
		}
		return glob($fn);
	}

	// returns the data folder
	function get_data_folder()
	{
		$fn = aw_ini_get('site_basedir').'/files/warehouse_import/data';
		if (file_exists($fn) === false)
		{
			// create the directories recursively if they don't exist
			mkdir($fn, 0777, true);
		}
		return $fn;
	}

	function fill_queue($arr)
	{

		// I think this implementation needs to come from a class inherited from this one: ie. from warehouse_products_import.aw class
		
	/*
		$db = $this->get_data_folder().'/warehouse_products_queue.sdb';

		echo "Filling the queue ... <br />\n";
		try 
		{
			$dbh = new PDO('sqlite:'.$db);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// if table don't exist, create it
			$sql = "CREATE TABLE IF NOT EXISTS products_queue ( status TEXT, timestamp default CURRENT_TIMESTAMP, data BLOB)";
			$dbh->query($sql);

			$insert_statement = $dbh->prepare("INSERT INTO products_queue (status, timestamp, data) VALUES (:status, :timestamp, :data)");

			$dbh = null;
		}
		catch (PDOException $e)
		{
			echo $e->getMessage();
		}
	*/
	}

	function init_queue()
	{
		$db = $this->get_data_folder().'/warehouse_products_queue.sdb';

		try 
		{
			$dbh = new PDO('sqlite:'.$db);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// if table don't exist, create it
			$sql = "CREATE TABLE IF NOT EXISTS products_queue ( status TEXT, timestamp default CURRENT_TIMESTAMP, data BLOB )";
			$dbh->query($sql);

			$this->sdb_handler = $dbh;

			$this->insert_statement = $dbh->prepare("INSERT INTO products_queue (status, timestamp, data) VALUES (:status, :timestamp, :data)");
		}
		catch (PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}
	}

	/**
		data(
			'status' => 
			'timestamp' =>
			'data' => 
		);
	**/
	function add_queue_item($data)
	{
		$this->insert_statement->execute($data);
		$this->insert_statement->closeCursor();
	}

	function close_queue()
	{
		$this->sdb_handler = null;
	}

	function get_warehouse($o)
	{
		return $o->get_first_obj_by_reltype(10); // 10 - RELTYPE_WAREHOUSE
	//	return $o->get_first_obj_by_reltype('RELTYPE_WAREHOUSE');
	}

	function write_log($str)
	{
		file_put_contents($this->logname, $str."\n", FILE_APPEND);
		echo $str."<br />\n";
	}
}
?>
