<?php

/*

this message will get posted whenever an alias is about to be deleted
the message will get the connection object as the "connection" parameter
EMIT_MESSAGE(MSG_STORAGE_ALIAS_DELETE)

this message will get posted whenever an alias is about to be deleted
the message parameter will be the class id of the "from" object
the message will get the connection object as the "connection" parameter
EMIT_MESSAGE(MSG_STORAGE_ALIAS_DELETE_FROM)

this message will get posted whenever an alias is about to be deleted
the message parameter will be the class id of the "to" object
the message will get the connection object as the "connection" parameter
EMIT_MESSAGE(MSG_STORAGE_ALIAS_DELETE_TO)

this message will get posted after a new alias is created
the message will get the connection object as the "connection" parameter
EMIT_MESSAGE(MSG_STORAGE_ALIAS_ADD)

this message will get posted after a new alias is created
the message will have the class id of the object for the "to" end as the message parameter
the message will get the connection object as the "connection" parameter
EMIT_MESSAGE(MSG_STORAGE_ALIAS_ADD_TO)

this message will get posted after a new alias is created
the message will have the class id of the object for the "from" end as the message parameter
the message will get the connection object as the "connection" parameter
EMIT_MESSAGE(MSG_STORAGE_ALIAS_ADD_FROM)

*/

class connection
{
	private $conn;		// int connection data

	/**	inits the connection
		@attrib api=1

		@param id optional
			- connection id, optional:
			  type: integer or array
			  array indexes are:
			   - from - start object of the connection (required)
			   - to - end object of the connection (required)
			   - reltype - relation type of the connection, can not be string (optional)
			   - data - string to save with connection (optional)
			   - idx - index of the connection (optional)

		@comment
			connection class - with this you can manage connections (aliases)
			normally, you do not create these objects yourself, instead you get them from the object class, from the connections() method, except
			if you want to search for connections, then you create an empty connection instance and use the find() method on it.

		@errors
			- if the parameter is not integer or array, error is thrown
			- if the connection does not exist in the database, error is thrown

		@returns
			none

		@examples
			// load connection with id 56
			$conn = new connection(56);

			// find all connections from object 66
			$c = new connection();
			$results = $c->find(array("from" => 66));

			// create a new connection
			$c = new connection(array(
				"from" => 67,
				"to" => 45,
				"reltype" => 34343
			));
			$c->save();
	**/
	function connection($id = NULL)
	{
		if ($id !== NULL)
		{
			if (!(is_numeric($id) || is_array($id)))
			{
				error::raise(array(
					"id" => "ERR_CONNECTION",
					"msg" => sprintf(t("connection::constructor(%s): parameter must be numeric or array!"), $id)
				));
				return;
			}

			$this->load($id);
		}
	}

	/** loads the connection
		@attrib api=1

		@param param required
			- connection id or connection data, required:
			  type: integer or array
			  array indexes are:
			   - from - start object of the connection (required)
			   - to - end object of the connection (required)
			   - reltype - relation type of the connection, can not be string (optional)
			   - data - string to save with connection (optional)
			   - idx - index of the connection (optional)

		@errors
			- if the parameter is not integer or array, error is thrown

		@returns
			none

		@examples
			// creates a new connection with the parameters given
			// to load()
			$conn = new connection();
			$conn->load(array("from" => 12, "to" => 34));
			$conn->save();

			// loads connection with id 78
			$conn = new connection();
			$conn->load(78);
	**/
	function load($param)
	{
		if (is_array($param))
		{
			$this->conn = $param;
		}
		else
		if (!is_numeric($param))
		{
			error::raise(array(
				"id" => "ERR_CONNECTION",
				"msg" => t("connection::load(): parameter must be either array (connection data) or integer (connection id)!")
			));
			return;
		}
		else
		{
			$this->_int_load($param);
		}
	}


	/** finds connections that match the given filter
		@attrib api=1

		@param param required type=array
			- array of connection parameters by what to search connections
			  possible array indexes are (all optional and can be arrays):
			    - from - start object of the connection
			    - to - end object of the connection
			    - type - relation type of the connection, can be string, if from.class_id is given
			    - relobj_id - relation object id
			    - idx - index of the connection
			    - [from|to].lang_id - lang_id of the from or to object of the connection
			    - [from|to].flags - flags of the from or to object of the connection
			    - [from|to].modified - modified of the from or to object of the connection
			    - [from|to].modifiedby - modifiedby of the from or to object of the connection
			    - [from|to].name - name of the from or to object of the connection
			    - [from|to].class_id - class_id of the from or to object of the connection
			    - [from|to].jrk - order of the from or to object of the connection
			    - [from|to].status - status of the from or to object of the connection
			    - [from|to].parent - parent of the from or to object of the connection

		@errors
			- if the parameter is not an array, error is thrown

		@returns
			array of arrays containing connection data that match the filter

		@examples
			$conn = new connection();
			$results = $conn->find(array(
					"from" => 90,
					"type" => RELTYPE_FOO
			));
			foreach($results as $result)
			{
				echo "from = $result[from] , to = $retult[to] <br>";
			}
	**/
	public static function find($param)
	{
		if (isset($GLOBALS["OBJ_TRACE"]) && $GLOBALS["OBJ_TRACE"])
		{
			echo "connection::find(".join(",", map2('%s => %s', $param)).") <br>";
		}
		if (!is_array($param))
		{
			error::raise(array(
				"id" => "ERR_PARAM",
				"msg" => t("connection::find(): parameter must be an array of filter parameters!")
			));
			return;
		}

		if (isset($param["type"]))
		{
			if (!is_numeric($param["type"]) && !is_array($param["type"]) && substr($param["type"], 0, 7) == "RELTYPE" && is_class_id($param["from.class_id"]))
			{
				// it is "RELTYPE_FOO"
				// resolve it to numeric
				if (!isset($GLOBALS["relinfo"][$param["from.class_id"]]) || !is_array($GLOBALS["relinfo"][$param["from.class_id"]]))
				{
					// load class def
					$i = new _int_object;
					$i->_int_load_properties($param["from.class_id"]);
				}

				if (!$GLOBALS["relinfo"][$param["from.class_id"]][$param["type"]]["value"])
				{
					$param["type"] = -1; // won't match anything
				}
				else
				{
					$param["type"] = $GLOBALS["relinfo"][$param["from.class_id"]][$param["type"]]["value"];
				}
			}
		}

		if (isset($param["type"]) && is_class_id(ifset($param, "from.class_id")))
		{
			$param["type"] = object_loader::instance()->resolve_reltype($param["type"], $param["from.class_id"]);
		}

		$retval =  object_loader::ds()->find_connections($param);

		if (isset($param["sort_by"]) && $param["sort_by"] != "")
		{
			uasort($retval, create_function('$a,$b', 'return strcasecmp($a["'.$param["sort_by"].'"], $["'.$param["sort_by"].'"]);'));
		}
		if (isset($param["sort_by_num"]) && $param["sort_by_num"] != "")
		{
			uasort($retval, create_function('$a,$b', 'return ($a["'.$param["sort_by_num"].'"] == $b["'.$param["sort_by_num"].'"] ? 0 : ($a["'.$param["sort_by_num"].'"] > $b["'.$param["sort_by_num"].'"] ? 1 : -1 ));'));
		}
		if(isset($param['sort_dir']) && $param['sort_dir'] == 'desc')
		{
			$retval = array_reverse($retval);
		}

		$rv = array();
		foreach($retval as $k => $v)
		{
			if (object_loader::can("", $v["to"]) and object_loader::can("", $v["from"]))
			{
				$rv[$k] = $v;
			}
		}
		return $rv;
	}

	/** alters the connection parameters that are specified. the connection is automatically saved, you do not need to call save() explicitly
		@attrib api=1

		@param param required
			- array of connection parameters to change, possible indexes are:
				  from - object id
				  to - object id
				  reltype - connection type
				  relobj_id - connection object id
				  data - connection data
				  cached - if the connection is cached

		@errors
			- if the user does not have view access to the connected objects, acl error is thrown

		@returns
			none

		@examples
			$conn = new connection(90);
			$conn->change(array(
				"from" => 90,
				"reltype" => RELTYPE_FOO
			));
	**/
	function change($param)
	{
		if (!is_array($param))
		{
			error::raise(array(
				"id" => ERR_ARG,
				"msg" => sprintf(t("connection::change(%s): parameter must be an array!"), $param)
			));
			return;
		}

		if (!is_array($this->conn))
		{
			$this->conn = array();
		}

		foreach($param as $k => $v)
		{
			$this->conn[$k] = $v;
		}
		$this->_int_save();
	}

	/** removes the current connection
		@attrib api=1

		@errors
			- if the user does not have view access to the connected objects, acl error is returned
			- if no current connection exists, error is thrown

		@returns
			the id of the connection removed

		@examples
			$conn = new connection(90);
			$conn->delete();
	**/
	function delete()
	{
		if (!$this->conn["id"])
		{
			error::raise(array(
				"id" => ERR_CONNECTION,
				"msg" => t("connection::delete(): no current connection to delete!")
			));
			return;
		}

		$param = array(
			"connection" => $this
		);

		post_message(
			"MSG_STORAGE_ALIAS_DELETE",
			$param
		);

		post_message_with_param(
			"MSG_STORAGE_ALIAS_DELETE_FROM",
			$this->conn["from.class_id"],
			$param
		);

		post_message_with_param(
			"MSG_STORAGE_ALIAS_DELETE_TO",
			$this->conn["to.class_id"],
			$param
		);

		// need to read both from and to site_ids to know if cache needs to be propagated
		$from_objdata = object_loader::ds()->get_objdata($this->prop("from"));
		object_loader::instance()->handle_cache_update($from_objdata["oid"], $from_objdata["site_id"], "delete_connection");

		$to_objdata = object_loader::ds()->get_objdata($this->prop("to"));
		object_loader::instance()->handle_cache_update($to_objdata["oid"], $to_objdata["site_id"], "delete_connection");

		object_loader::ds()->delete_connection($this->conn["id"]);
		return $this->conn["id"];
	}

	/** returns the id of the connection
		@attrib api=1

		@errors
			none

		@returns
			the id of the connection

		@examples
			$conn = new connection(90);
			$id = $conn->id();#/php#
	**/
	function id()
	{
		return $this->conn["id"];
	}

	/** returns the specified propery of the connection
		@attrib api=1

		@param key optional
			- property name to return, defaults to null and returns all properties in that case
			property names are:
				- id
				- from
				- to
				- type (class id of connected object)
				- data
				- idx
				- cached
				- relobj_id
				- pri
				- reltype
				- to.* ( object fields of connected object - name,class_id,parent,modified,modifiedby,flags,status,lang_id,jrk)
				- from.* (object fields of originating object - name,class_id,parent,modified,modifiedby,flags,status,lang_id,jrk)

		@errors
			error is thrown, if no current connection exists

		@returns
			value of the requested property for the current connection

		@examples
			$conn = new connection(90);
			$to = $conn->prop("to");
			$to_name = $conn->prop("to.name");
			echo "connection points to oid: $to name: $to_name";
	**/
	function prop($key = NULL)
	{
		if ($key === NULL)
		{
			return $this->conn;
		}
		return $this->conn[$key];
	}

	/** changes connection type to link and vice versa
		@attrib api=1

		@param b_set required
			- boolean - true changes connection to link and false back to normal

		@errors
			none

		@returns
			none

		@examples
			$o = new object(396520);
			foreach($o->connections_from() as $c)
			{
				$c -> alias_to_link(true);
			}
	**/
	function alias_to_link( $b_set)
	{
		$o_from = new object($this->prop("from"));
		$a_aliaslinks = $o_from->meta("aliaslinks");

		if ($b_set==true)
		{
			$a_aliaslinks[$this->prop("to")] = 1;
		} else
		{
			unset($a_aliaslinks[$this->prop("to")]);
		}
		$o_from->set_meta("aliaslinks", $a_aliaslinks);
		$o_from->save();
	}

	/** returns the object that the connection points to
		@attrib api=1

		@errors
			none

		@returns
			object instance of the object that the connection points to

		@examples
			$conn = new connection(90);
			$to = $conn->to();
			echo "name = ".$to->name();
	**/
	function to()
	{
		if (!$this->conn["id"])
		{
			return NULL;
		}
		return obj($this->conn["to"]);
	}

	/** returns the object that the connection starts from
		@attrib api=1

		@errors
			none

		@returns
			object instance of the object that the connection starts from

		@examples
			$conn = new connection(90);
			$from = $conn->from();
			echo "name = ".$from->name();
	**/
	function from()
	{
		if (!$this->conn["id"])
		{
			return NULL;
		}
		return obj($this->conn["from"]);
	}

	/** saves the current connection - this should be used to save the connection after it has been initialized via an array by the constructor or load()
		@attrib api=1

		@errors
			error is thrown, if the from and to properties are not set
			error is thrown if the current user has no view access for both endpoints

		@returns
			none

		@examples
			$conn = new connection(array(
				"from" => 100,
				"to" => 200
			));
			$conn->save();
	**/
	function save()
	{
		$this->_int_save();
	}

	////////////////////////////
	// private functions

	function _int_load($id)
	{
		$this->conn = object_loader::ds()->read_connection($id);
		if ($this->conn === false)
		{
			error::raise(array(
				"id" => ERR_CONNECTION,
				"msg" => sprintf(t("connection::load(%s): no connection with id %s!"), $id, $id)
			));
			return;
		}

		// now, check acl - both ends must be visible for the connection to be shown
		if (!(object_loader::can("", $this->conn["from"]) || object_loader::can("", $this->conn["to"])))
		{
			error::raise(array(
				"id" => ERR_ACL,
				"msg" => sprintf(t("connection::load(%s): no view access for this connection!"), $id)
			));
			return;
		}
	}

	function _int_save()
	{
		if (!$this->conn["from"] || !$this->conn["to"])
		{
			error::raise(array(
				"id" => ERR_CONNECTION,
				"msg" => t("connection::save(): connection must have both ends defined!")
			));
			return;
		}

		// now, check acl - both ends must be visible for the connection to be changed
		if (!object_loader::can("", $this->conn["from"]) or !object_loader::can("", $this->conn["to"]))
		{
			error::raise(array(
				"id" => ERR_ACL,
				"msg" => sprintf(t("connection::load(%s): no view access for this connection!"), $id)
			));
			return;
		}

		// check if there already exists a connection between these ends with this reltype
		$ext_conns = $this->find(array(
			"from" => $this->conn["from"],
			"to" => $this->conn["to"],
			"type" => $this->conn["reltype"],
		));
		if(count($ext_conns) > 0)
		{
			$ext_conn = reset($ext_conns);
			// Problem occurred when I tried to change a connection and there already existed a connection with those ends and reltype.
			// So I'll just delete the connection I'm trying to change.
			if(!empty($this->conn["id"]) && $this->conn["id"] != $ext_conn["id"])
			{
				$this->delete();
			}
			// Load the existing connection if it's not loaded already
			if(empty($this->conn["id"]) || $this->conn["id"] != $ext_conn["id"])
			{
				$this->_int_load($ext_conn["id"]);
			}
		}

		// check if this is a new connection
		$new = false;
		if (empty($this->conn["id"]))
		{
			$new = true;

			// now, if it is, then check if a relobj_id was passed
			if (empty($this->conn["relobj_id"]))
			{
				// if it wasn't, then create the relobj
				$to = obj($this->conn["to"]);

				// only create connection objects, IF
				if ($this->conn["reltype"] == RELTYPE_ACL || $to->class_id() == CL_CALENDAR_VIEW || $to->class_id() == CL_ML_LIST)
				{
					$from = obj($this->conn["from"]);
					$o = obj();
					$o->set_parent($from->parent());
					$o->set_class_id(CL_RELATION);
					$o->set_status(STAT_ACTIVE);
					$o->set_subclass($to->class_id());
					$this->conn["relobj_id"] = $o->save();
				}
			}
		}

		// now that everything is ok, save the damn thing
		$this->conn["id"] = object_loader::ds()->save_connection($this->conn);

		// load all connection parameters
		$this->_int_load($this->conn["id"]);

		// need to read both from and to site_ids to know if cache needs to be propagated
		if (isset($GLOBALS["objects"][$this->prop("from")]))
		{
			$from_site_id = $GLOBALS["objects"][$this->prop("from")]->site_id();
		}
		else
		{
			$from_objdata = object_loader::ds()->get_objdata($this->prop("from"));
			$from_site_id = $from_objdata["site_id"];
		}

		object_loader::instance()->handle_cache_update($this->prop("from"), $from_site_id, "save_connection");

		if (isset($GLOBALS["objects"][$this->prop("to")]))
		{
			$to_site_id = $GLOBALS["objects"][$this->prop("to")]->site_id();
		}
		else
		{
			$to_objdata = object_loader::ds()->get_objdata($this->prop("to"));
			$to_site_id = $to_objdata["site_id"];
		}

		object_loader::instance()->handle_cache_update($this->prop("to"), $to_site_id, "save_connection");

		if ($new)
		{
			$param = array(
				"connection" => $this
			);

			post_message(
				"MSG_STORAGE_ALIAS_ADD",
				$param
			);

			post_message_with_param(
				"MSG_STORAGE_ALIAS_ADD_TO",
				$this->conn["to.class_id"],
				$param
			);

			post_message_with_param(
				"MSG_STORAGE_ALIAS_ADD_FROM",
				$this->conn["from.class_id"],
				$param
			);

		}
	}
}
