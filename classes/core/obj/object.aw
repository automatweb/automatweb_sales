<?php

// god damn, this is a fucking great idea!
// how to get around the php copy-object problem.
// basically, make the object class contain only object id, store the real objects in a global hash
// and access them through that only, so object data is in memory only once, but there can be several oid pointers to it.
// the dummy object class just forwards all calls to the global table.
// voila - instant object cache!


// rules for datasources:
// if you create a new wrapper datasource, then it must not derive from anything, be in this folder
// the file name must be ds_[name], the class name must be _int_obj_ds_[name]
// the constructor must take one parameter - the contained ds instance
// it must implement all functions in ds_base, even if that just means passing everything to the contained ds
// this way we minimize the number of connections and duplicate data, therefore save memory
// if you create a new db-specific datasource (the last one in the chain), the file naming convention still applies,
// but it should derive from ds_base and call $this->init() from the constructor, that takes no parameters


/////////////
// TODO:
// - separate, confable, possibly shared folders for caches: acl, search, connection, objdata/properties
// - don't load properties if not asked for, implement this by swithing the object instance in the objects array
// - is object_list caching really worth it?
// - merge objdata/propdata caches?

class object
{
	const STAT_DELETED = 0;
	const STAT_NOTACTIVE = 1;
	const STAT_ACTIVE = 2;

	var $oid;	// the object this instance points to
	//TODO: scope?

	private static $instance_count = 0;
	private $instance_number;

	/** object class constructor
		@attrib api=1 params=pos

		@param param optional default=null type=mixed
			type: can be object id, object instance or alias

		@param constructor_args optional type=array default=array()
		 	Additional class specific constructor arguments

		@comment
			The object class represents a single object and has member functions via which you can modify or query all aspects of the object, also save it, copy it and delete it.
			the constructor can be called in four different ways:
				- without parameters: creates empty object
				- single integer: loads the object
				- single textual parameter: assumes the parameter is object alias, finds the object and loads it
				- object class instance: loads the same object from memory

			in addition to the constructor, there is an utility function obj() in the global scope, to lessen the need to type, that passes it's parameters to the constructor

		@errors:
			- if the user has no access to the object, awex_obj_acl is thrown
			- if no such object exists or it cannot be loaded, error is thrown

		@examples:
			$o = new object; // creates an empty object

			$o = obj(666);	  // loads object with id 666

			// finds object that has alias osakonnad/majandus and loads it
			$o = obj("osakonnad/majandus");
			$o = new object(array(
			   "name" => $name,
			   "parent" => $parent,
			   "class_id" => CL_FOO
			));
	**/
	function object($param = null, $constructor_args = array())
	{
		$this->instance_number = ++self::$instance_count;

		if (is_array($param))
		{
			$this->oid = object_loader::instance()->load_new_object($param, $constructor_args);
		}
		elseif (null !== $param)
		{
			$this->load($param, false, $constructor_args);
		}
		else
		{
			$this->oid = object_loader::instance()->load_new_object(array(), $constructor_args);
		}
	}

	function __call($method, $args)
	{
		$this->_check_lock_write();

		if (method_exists($GLOBALS["objects"][$this->oid], $method) && is_callable(array($GLOBALS["objects"][$this->oid], $method)))
		{
			return call_user_func_array(array($GLOBALS["objects"][$this->oid], $method), $args);
		}
		else
		if ($this->is_property($method))
		{
			$val = $this->prop($method);
			if (object_loader::can("", $val))
			{
				return obj($val);
			}
			return obj();
		}
		elseif(!method_exists($GLOBALS["objects"][$this->oid], $method))
		{
			throw new awex_obj_method("Call to undefined method '" . $method . "' on an instance of '" . get_class($GLOBALS["objects"][$this->oid]) . "' (id: " . $this->oid . ") with clid '" . $GLOBALS["objects"][$this->oid]->class_id() . "'.");
		}
		else
		{
			throw new awex_obj_method("Call to private or protected method '" . $method . "' on an instance of '" . get_class($GLOBALS["objects"][$this->oid]) . "' (id: " . $this->oid . ") with clid '" . $GLOBALS["objects"][$this->oid]->class_id() . "'.");
		}
	}

	/** loads an object
		@attrib api=1

		@param param required
			type: can be object id, object instance or alias

		@param force_reload optional type=bool default=false
		 	if TRUE, object will be reloaded from data source.

		@param constructor_args optional type=array default=array()
		 	Additional class specific constructor arguments

		@errors
			- If user has no access, acl error is thrown.
			- If no such object exists or object cannot be read, error is thrown.

		@returns
			id of the object loaded

		@xamples
			$o = new object();
			$o->load(666);
			$o->load("alias/alias2");
			$o->load(obj(666));
	**/
	function load($param, $force_reload = false, $constructor_args = array())
	{
		$this->_check_lock_read();

		$oid = object_loader::instance()->param_to_oid($param);
		if ($force_reload && is_oid($oid))
		{
			$GLOBALS["objects"][$oid] = null;
		}

		$this->oid = object_loader::instance()->load($oid, $constructor_args);

		if (!empty($GLOBALS["TRACE_OBJ"]))
		{
			echo "load object $param from <br>".dbg::sbt()." <br>";
		}

		return $this->oid;
	}

	/** Saves the currently loaded object or creates a new one if the currently loaded object is not yet saved and all the necessary properties are set; returns the id of the object created or saved.
		@attrib api=1

		@comment
			The properties that must be set in order for an object to be saved, are:
				- parent
				- class_id
			If these properties are not set when save() is called, then script execution will be aborted with an error message.

		@errors
			- If all necessary properties are not set that are needed to save or create the object, error is thrown.
			- If the user has no access to the object, error is thrown.

		@returns
			Id of the object that was saved or created.

		@examples
			$o = obj();
			$o->set_parent(555);
			$o->set_class_id(CL_FOO);
			$o->save();
	**/
	function save($check_state = false)
	{
		self::lock();
		$this->oid = object_loader::instance()->save($this->oid, $check_state);
		self::unlock();
		return $this->oid;
	}

	/** Saves the currently loaded object or creates a new one if the currently loaded object is not yet saved and all the necessary properties are set; returns the id of the object created or saved.
		@attrib api=1

		@comment
			Checks modified count from the database to make sure that it has not been modified in between.
			The properties that must be set in order for an object to be saved, are:
				- parent
				- class_id
			If these properties are not set when save() is called, then script execution will be aborted with an error message.

		@param state_id optional type=int
			defaults to the one in memory. The state id of the object to compare when saving.

		@errors
			- if modified count is different than then one given or in memory, exception awex_obj_modified_by_others is thrown
			- If all necessary properties are not set that are needed to save or create the object, error is thrown.
			- If the user has no access to the object, error is thrown.

		@returns
			Id of the object that was saved or created.

		@examples
			$o = obj();
			$o->set_parent(555);
			$o->set_class_id(CL_FOO);
			$o->save();
	**/
	function save_check_state($state_id = null)
	{
		self::lock();
		return $this->oid = object_loader::instance()->save($this->oid, true, $state_id);
		self::unlock();
		return $this->oid;
	}

	/** Returns the current state id for the object from memory.
		@attrib api=1

		@returns
			State id of the object
	**/
	function get_state_id()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_state_id();
	}

	/** creates a new object from the currently loaded object, if all needed properties are set,returns the id of the object created
		@attrib api=1

		@comment
			the properties that must be set in order for an object to be saved, are:
				- parent
			- class_id

			if these properties are not set when save_new() is called, then script execution will
			be aborted with an error message

		@errors
			throws awex_obj_parent if parent is not set
			throws awex_obj_class if class id is not set
			- if the user has no add access to the object's parent, error is thrown

		@returns
			id of the object that was created

		@examples
			$o = obj(666);
			$new_id = $o->save_new();
			// creates a copy of the object under the same folder,
			// containing all the same properties
	**/
	function save_new()
	{
		$this->_check_lock_write();
		try
		{
			return object_loader::instance()->save_new($this->oid);
		}
		catch (awex_obj_parent $e)
		{
			throw $e;
		}
		catch (awex_obj_class $e)
		{
			throw $e;
		}
	}

	/** sets whether the loaded object will be saved whenever a property is changed or only if the save() method is called. the default is off.
		@attrib api=1

		@param param required type=bool
			true: object is saved after each modification automatically. false: object must be saved manually

		@errors
			none

		@returns
			the previous state of the implicit save flag

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
			$o->set_name("foo"); // object is now also automatically saved
	**/
	function set_implicit_save($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_implicit_save($param);
	}

	/** returns the state of the implicit save flag.
		@attrib api=1

		@errors
			none

		@returns
			true or false, depending on the state of the implicit save flag for the current object

		@examples
			if (!$o->get_implicit_save())
			{
				$o->save();
			}
	**/
	function get_implicit_save()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_implicit_save();
	}

	/** returns the currently loaded object as an array
		@attrib api=1

		@errors
			none

		@returns
			array that has elements for all the object's properties and it's metadata

		@examples
			$o = obj(78);
			$arr = $o->arr();
	**/
	function arr()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->arr();
	}

	/** deletes the currently loaded object
		@attrib api=1

		@param full_delete optional type=bool
			- whether to really delete the object or just set the status to deleted, defaults to false

		@errors
			- if the current user has no access rights to the object, acl error is thrown
			- if no object is loaded, error is thrown

		@returns
			the id of the deleted object

		@examples
			$o = obj(89);
			$o->delete(); // now the object exists in the database, but it's status is deleted

			$o = obj();
			$o->set_parent(1);
			$o->set_class_id(CL_MENU);
			$o->save();

			$o->delete(true); // now the database contains no traces of the just-created object
	**/
	function delete($full_delete = false)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->delete($full_delete);
	}

	/** connects the object to another (creates an alias).
		@attrib api=1

		@param to required type=oid
			the object id or object to connect to, required.
			type: can be either integer, string, object instance or object list instance

		@param type optional
		 	the type of the relation, optional.
			type: integer or string

		@param relobj_id optional
			the id of the relation object to attach to the relation, optional
			type: id, alias or object instance reference

		@param data optional
			extra data to pass to the connection, optional.
			type: text

		@errors
			- if the user has no edit access to the current object or no view acess to the connected object, acl error is thrown
			- if the object to connect to cannot be loaded, error is thrown
			- if the parameter is not an array, error is thrown

		@comment
			If an existing object is loaded then the connection is created immediately, but if no object is loaded, the connection is created when save() is called on the object.

		@returns
			nothing

		@examples
			$o1 = obj(666);
			$o2 = obj(90);
			$o1->connect(array(
				"to" => $o2
			));

			$o3 = new object(999);
			$o4 = new object(111);
			$o3->connect(array(
				"to" => $o4->id(),
				"type" => "RELTYPE_FOO",
			));
	**/
	function connect($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->connect($param);
	}

	/** disconnects the object from another object (deletes alias)
		@attrib api=1

		@param from required type=oid
			the object to disconnect from, required.

		@param type optional type=reltype
			if set, only this type of connections are removed - can be string, or numeric or array of either

		@param errors optional type=bool
			if false no error is thrown when the connection does not exist, defaults to true

		@errors
			- if user has no edit acess to the current object or view object to the connected
			object, acl error is thrown
			- if no object is loaded, error is thrown
			- if the current object is not connected to the object that is specified, error is thrown

		@returns
			nothing

		@examples
			$o = obj(666);
			$o->connect(array("to" => 90));
			$o->disconnect(array("from" => 90));
	**/
	function disconnect($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->disconnect($param);
	}

	/** returns an array of connection objects that the current object has to other objects
		@attrib api=1

		@errors
			- if no object is loaded, error is thrown

		@param param optional type=array
			- array of filter parameters, optional
			possible array members, all of the filter members can be arrays of the given types as well:
				filter members:
					type - connection type, string or numeric
					class - connected object's class_id
					to - id of the connected object
					idx - index of the connection that is searched for
					to.[lang_id,flags,modified,modifiedby,name,class_id,jrk,status,parent] - filter by the connected object's fields

				non filter members:
					sort_by - the field to sort the results by, sorting as strings
					sort_by_num - the field to sort the results by, sorting as numbers
					sort_dir - "asc" or "desc" - defaults to asc, the order of the sorting

		@returns
			array of connection object instances, array index is the connection id

		@examples
			$o = obj(666);
			$conns = $o->connections_from();
			foreach($conns as $con)
			{
				$con->delete();
			}

			// all connections with type 'SOMETHING', sorted by connected objects jrk value (object's order property), direction as ascending
			$conns = $o->connections_from(array(
				'type' => 'RELTYPE_SOMETHING',
				'sort_by_num' => 'to.jrk',
				'sort_dir' => 'asc'
			));

	**/
	function connections_from($param = NULL)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->connections_from($param);
	}

	/** returns array of connection object instances, that other objects have to the current object
		@attrib api=1

		@errors
			- if no object is loaded, error is thrown

		@param param optional type=array
			- array of filter parameters, optional
			possible array members, all of the filter members can be arrays of the given types as well:
			filter members:
				type - connection type: numeric or can be string if from.class_id is also given
				class - connected object's class_id
				from - id of the connected object
				idx - index of the connection that is searched for
				from.[lang_id,flags,modified,modifiedby,name,class_id,jrk,status,parent] - filter by the connected object's fields

			non filter members:
				sort_by - the field to sort the results by, sorting as strings
				sort_by_num - the field to sort the results by, sorting as numbers
				sort_dir - "asc" or "desc" - defaults to asc, the order of the sorting

		@returns
			array of connection objects, array index is connection id

		@examples
			$o = obj(666);
			$conns = $o->connections_to();
			foreach($conns as $con)
			{
				$con->delete();
			}
	**/
	function connections_to($param = NULL)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->connections_to($param);
	}

	/** Returns the first connection of type specified by parameter.
		@attrib api=1

		@param type optional
			 connection type - string or integer

		@errors
			- if no object is loaded, error is thrown

		@returns
			connection object instance when connection of specified type exists, FALSE otherwise

		@comment
			If connection of that type doesn't exist, returns FALSE.
			If type not specified, returns first of all connections regardless of reltype.


		@examples
			$o = obj(666);
			if ($image_c = $o->get_first_conn_by_reltype('RELTYPE_IMAGE'))
			{
			    $image_id = $image_c->prop("to");
			}
			else
			{
				echo "No image found";
			}
	**/
	function get_first_conn_by_reltype($type = NULL)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_first_conn_by_reltype($type);
	}

	/** Finds the first connection of type specified by parameter and returns the target object where the connection points to.
		@attrib api=1

		@param type optional type=string
			- connection type - string or integer, defaults to NULL

		@comment
			If connection of that type doesn't exist, returns FALSE.
			If type not specified, gets first of all connections regardless of reltype.

		@errors
			- if no object is loaded, error is thrown

		@returns
			an object when connection of specified type exists, FALSE otherwise

		@examples
			$o = obj(666);
			if ($image_obj = $o->get_first_obj_by_reltype('RELTYPE_IMAGE'))
			{
			    $image_obj->set_name("my image");
			    $image_obj->save();
			}
			else
			{
				echo "No image found";
			}
	**/
	function get_first_obj_by_reltype($type = NULL)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_first_obj_by_reltype($type);
	}

	/** returns an array of object instances that are the current objects parent objects
		@attrib api=1

		@errors
			- if no object is loaded, error is thrown
			- if called statically and no parameter is passed, error is thrown
			- if called non-statically and parameter is non empty and not an array, error is thrown
			- if the object's path is cyclical, error is thrown

		@comment
			parameters, if called statically:
				- id of the object that the path is returned for
				type: integer, string or object instance

			parameters, if called on object:
				- to - id of the object to which the path is returned

		@returns
			array of object instances, including the current object, that make up the path,
			objects are returned starting from the tree root. array key is object id

		@examples
			$o = obj(66);
			$path = $o->path();
			$path = object::path(66);
			foreach($path as $obj)
			{
				echo $obj->name();
			}
	**/
	function path($param = NULL)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->path($param);
	}

	/** returns a string that contains the names of objects in the path with / 's between them
		@attrib api=1 params=name

		@param max_len optional type=int
			reverse max length of path (items are left out from the beginning)

		@param start_at optional type=int
			the object to start the path from, optional

		@param path_only optional type=bool
			if true, only the objects that are before the current object are in the returned path, not the object itself, optional, defaults to false

		@errors
			- if no object is loaded, error is thrown
			- if parameter is given and it is not an array, error is shown
			- if called statically and no parameter is passed, error is thrown
			- if the object's path is cyclical, error is thrown


		@returns
			string that contains the names of objects in the path with / 's between them

		@examples
			$o = obj(66);
			echo "object 66 path is :".$o->path_str();
	**/
	function path_str($param = NULL)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->path_str($param);
	}

	/** returns true if the given string is a property name for the currently loaded object
		@attrib api=1

		@param param required type=string
			 name of property to check

		@errors
			- if no object is loaded or class id not set, error is thrown
			- if parameter is not a string, error is thrown


		@returns
			true if the given string is a name of a property defined for the current object
			false, if no such property exists.

		@examples
			$o = obj();
			$o->set_class_id(CL_MENU);
			if (!$o->is_property("foobar"))
			{
				echo "works! <br>";
			}

			$o = get_some_document_object();
			if ($o->is_property("content"))
			{
				echo "document class has content property <br>";
			}
	**/
	function is_property($param)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->is_property($param);
	}

	/** returns the access the current user has to the current object
		@attrib api=1

		@param param required type=string
			- access name that is returned. values: (add/edit/admin/delete/view)

		@errors
			- error is thrown if no current object exists

		@returns
			boolean value - true, if the user has access, false, if not
	**/
	function can($param)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->can($param);
	}

	/** returns the object's parent
		@attrib api=1

		@errors
			none

		@returns
			id of the current object's parent object, NULL is returned if no object is loaded

		@examples
			$o = obj(66);
			$parent = $o->parent();
	**/
	function parent()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->parent();
	}

	/** sets the object's parent object
		@attrib api=1

		@param param required type=oid
			new parent object, type: id, text or object instance

		@errors
			- if parent is string and no object with that alias is found, error is thrown
			- if implicit save is on and user has no change access , acl error is thrown
			- if parent is empty, error is thrown


		@returns
			the previous parent id

		@examples
			$o = obj(56);
			$o->set_parent(67);
			$o->save();
	**/
	function set_parent($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_parent($param);
	}

	/** returns the object's name
		@attrib api=1

		@errors
			none

		@returns
			name of the object, NULL is returned if no object is loaded

		@examples
			$o = obj(666);
			echo "name = ".$o->name();
	**/
	function name()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->name();
	}

	/** sets the object's name
		@attrib api=1

		@param param required type=string
			the new object's name

		@errors
			none, if implicit save is off

		@returns
			the old name of the object

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
			$o->set_parent(89);
	**/
	function set_name($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_name($param);
	}

	/** returns the class id of the object
		@attrib api=1

		@errors
			none

		@returns int
			class id of the object, 0 is returned if no object is loaded

		@examples
			$o = obj(666);
			$clid = $o->class_id();
	**/
	function class_id()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->class_id();
	}

	/** sets the object's class id
		@attrib api=1

		@param param required type=int
			the new class id

		@errors
			- error is thrown if new class id is not known
			- error is thrown if implicit_save is on and the user has no write access
			throws awex_obj_class when class id already set

		@returns
			the object instance

		@examples
			$o = obj(666)->set_class_id(CL_FOO)->get_property_list();
	**/
	function set_class_id($param)
	{
		if (is_class_id($GLOBALS["objects"][$this->oid]->class_id()))
		{
			throw new awex_obj_class("Class id can be changed only once.");
		}

		$this->_check_lock_write();

		$objdata = $GLOBALS["objects"][$this->oid]->get_object_data();
		$objdata["class_id"] = $param;
		$objdata["oid"] = $this->oid; // because _int_object doesn't know its own id before save
		object_loader::instance()->load_new_object($objdata);
		$GLOBALS["objects"][$this->oid]->set_class_id($param);
		return $this;
	}

	/**	returns the class title of the object
		@attrib api=1

		@errors
			none

		@returns
			class title of the object, NULL is returned if no object is loaded

		@examples
			$o = obj(666);
			$class = $o->class_title();
			printf("'%s' is of the type '%s'!", $o->name(), $o->class_title());	// Prints 'Foo' is of the type 'Bar'!
	**/
	public function class_title()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->class_title();
	}

	/**	returns the class title of the given clid
		@attrib api=1

		@errors
			none

		@returns
			class title of the clid, NULL is returned if invalid clid is given

		@examples
			$clid = 145;
			$class = object::class_title($clid);
			printf("%u is clid for '%s'!", $clid, $o->class_title());	// Prints "145 is of the type 'Bar'!"
	**/
	public static function class_title_by_clid($clid)
	{
		try
		{
			$class_title = aw_ini_get(sprintf("classes.%u.name", $clid));
		}
		catch(awex_cfg_key $e)
		{
			$class_title = NULL;
		}
		return $class_title;
	}

	/** returns the status of the object
		@attrib api=1

		@errors
			none

		@returns
			status of the object, NULL is returned if no object is loaded, the possible return values are:
			STAT_DELETED, if the object is deleted
			STAT_NOTACTIVE, if the object is not active
			STAT_ACTIVE, if the object is active

		@examples
			$o = obj(666);
			$status = $o->status();
	**/
	function status()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->status();
	}

	/** sets the status of the object, if called with STAT_DELETED it is the same as deleting the object
		@attrib api=1

		@param param required type=int
			the new status, one of STAT_DELETED, STAT_NOTACTIVE, STAT_ACTIVE

		@errors
			- error is thrown if new status is not in the list of status codes

		@returns
			the old status of the object

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
			$o->set_status(STAT_ACTIVE);
	**/
	function set_status($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_status($param);
	}

	/** returns the language code of the object
		@attrib api=1

		@errors
			none

		@returns
			two-letter language code of the object (en / et / ..), NULL is returned if no object is loaded

		@examples
			$o = obj(666);
			$lc = $o->lang();
	**/
	function lang()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->lang();
	}

	/** returns the language id of the language of the object
		@attrib api=1

		@errors
			none

		@returns
			the language id of the language the object is in. the id is not the language's oid, but it's id in the languages table

		@examples
			if ($o->lang_id() == aw_global_get("lang_id"))
			{
				echo "object is under the current language!";
			}
	**/
	function lang_id()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->lang_id();
	}

	/** sets the language code of the object
		@attrib api=1

		@param param required type=string
			two-character language code to set

		@errors
			- error is thrown if new language is not defined in current system

		@returns
			the old language code of the object

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
			$o->set_lang("et")
	**/
	function set_lang($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_lang($param);
	}

	/** sets the language id of the object
		@attrib api=1

		@param param required type=int
			new language id

		@errors
			- error is thrown if new language id is not integer

		@returns
			the old language id of the object

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
			$o->set_lang_id(3); // vene keele id
	**/
	function set_lang_id($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_lang_id($param);
	}

	/** returns the comment of the object
		@attrib api=1

		@errors
			none

		@returns
			comment string of the object, NULL is returned if no object is loaded

		@examples
			$o = obj(666);
			$comm = $o->comment();
	**/
	function comment()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->comment();
	}

	/** sets the comment of the object
		@attrib api=1

		@param param required type=string
			the new comment text

		@errors
			none

		@returns
			the old comment of the object

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
			$o->set_comment("cool comment string");
	**/
	function set_comment($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_comment($param);
	}

	/** returns the order of the object
		@attrib api=1

		@errors
			none

		@returns
			order of the object, NULL is returned if no object is loaded

		@examples
			$o = obj(666);
			$val = $o->ord();
	**/
	function ord()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->ord();
	}

	/** sets the order of the object
		@attrib api=1

		@param param required type=int
			the new order

		@errors
			none

		@returns
			the old order of the object

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
			$o->set_order(18);
	**/
	function set_ord($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_ord($param);
	}

	/** returns the alias of the object
		@attrib api=1

		@errors
			none

		@returns
			alias of the object, NULL is returned if no object is loaded

		@examples
			$o = obj(666);
			$val = $o->alias();
	**/
	function alias()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->alias();
	}

	/** sets the alias of the object
		@attrib api=1

		@param param required type=string
			the new alias

		@errors
			- acl error is thrown if implicit save is on and user has no change access

		@returns
			the old alias of the object

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
	**/
	function set_alias($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_alias($param);
	}

	/** returns the id of the object
		@attrib api=1

		@errors
			none

		@returns
			id of the object, NULL if no object is loaded

		@examples
			$o = obj("alias");
			$id = $o->id();
	**/
	function id()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->id();
	}

	/** returns the user that created the current object.
		@attrib api=1

		@errors
			- error is thrown id the creator has no object

		@returns
			object instance of the user object that represents the user that created the current object, NULL if no current object exists

		@examples
			$o = obj(666);
			$creator = $o->createdby();
			echo "createdby ".$creator->name();
	**/
	function createdby()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->createdby();
	}

	/** returns the date that the object was created at
		@attrib api=1

		@errors
			none

		@eturns
			unix timestamp that the object was created at, NULL if no object is loaded

		@examples
			$o =& obj(89);
			echo "created - ".$this->time2date($o->created(), 2);
	**/
	function created()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->created();
	}

	/**	returns the user that was the last one to modify the object
		@attrib api=1

		@errors
			- error is thrown, if the last-modifier of the current object has no object

		@returns
			object instance of the user object that represents the user that last
			modified the current object, NULL if no current object exists

		@examples
			$o = obj(666);
			$mod = $o->modifiedby();
			echo "createdby ".$mod->name();
	**/
	function modifiedby()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->modifiedby();
	}

	/** returns the date that the object was last modified at
		@attrib api=1

		@errors
			none

		@parameters
			none

		@returns
			unix timestamp that the object was last modified at, NULLif no object is loaded

		@examples
			$o =& obj(89);
			echo "modified - ".$this->time2date($o->modified(), 2);
	**/
	function modified()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->modified();
	}

	/** returns the period id of the period that the object currently has
		@attrib api=1

		@errors
			none

		@returns
			id of the period that the current object has, 0 if no period is set, NULL if no object is currently loaded

		@examples
			$o =& obj(90);
			$per = $o->period();
	**/
	function period()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->period();
	}

	/** sets the period of the object
		@attrib api=1

		@param param required type=int
			the new period id

		@errors
			none

		@returns
			the old period of the object

		@examples
			$o = obj(666);
			$o->set_implicit_save(true);
			$o->set_period(18);
	**/
	function set_period($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_period($param);
	}

	/** returns true if the object is periodic
		@attrib api=1

		@errors
			none

		@returns
			boolean value, true if object is periodic, false if not, NULL is no object is loaded

		@examples
			$o =& obj(90);
			if ($o->is_periodic())
			{
				echo "object is periodic!";
			}
	**/
	function is_periodic()
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->is_periodic();
	}

	/** sets the periodic property of the object
		@attrib api=1

		@param param required type=bool
			boolean value that specifies if the object is periodic or not

		@errors
			none, if implicit save is off

		@returns
			the old setting of the periodic flag

		@examples
			$o =& obj(1);
			$o->set_periodic(true);
	**/
	function set_periodic($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_periodic($param);
	}

	/** returns the site id that the current object has
		@attrib api=1

		@errors
			none

		@returns
			the id of the site that the current object has, NULL if no object is loaded

		@examples
			$o = obj(89);
			$site_id = $o->site_id();
	**/
	function site_id()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->site_id();
	}

	/** sets the site id for the current object
		@attrib api=1

		@param param required type=int
			new site id

		@errors
			none

		@examples
			$o = obj(90);
			$o->set_site_id(90);
	**/
	function set_site_id($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_site_id($param);
	}

	/** returns true or false, depending if the object is a brother object or not
		@attrib api=1

		@errors
			none

		@returns
			boolean value, true if the object is a brother object, false if the object is the original
			object, NULL if no object is loaded

		@examples
			$o = obj(89);
			if ($o->is_brother())
			{
				$orig = $o->get_original();
			}
	**/
	function is_brother()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->is_brother();
	}

	/** Returns the OID of brother (with specified parent), if no brother is found returns false
		@attrib api=1 params=pos

		@param parent optional type=oid acl=view
			the OID of parent, to look the brother from.

		@errors
			none

		@returns
			the OID of brother (with specified parent), false if no brother is found, NULL if no object is loaded

		@examples
			$o = obj(89);
			$bro_oid = $o->has_brother(127);

	**/
	function has_brother($parent = NULL)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->has_brother($parent);
	}

	/** Returns all brother OIDs
		@attrib api=1
		@errors
			none
		@returns
			array, NULL if no object is loaded
		@examples
			$o = obj(89);
			$bro_oids = $o->brothers();
	**/
	function brothers()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->brothers();
	}

	/** returns the object that the current object is brother to, or the same object if it is the original
		@attrib api=1

		@errors
			none

		@returns
			object instance of the object that the current object is brother to. if the
			current object is not a brother, then the original object instance is returned.
			if no current object exists, NULL is returned

		@examples
			$o = obj(89);
			$o = obj(89);
			if ($o->is_brother())
			{
				$orig = $o->get_original();
				echo "orig_id = ".$orig->id()." <br>";
			}
	**/
	function get_original()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_original();
	}

	/** returns the subclass of the current object
		@attrib api=1

		@errors
			none

		@returns
			id of the subclass of the current object, NULL if no object is loaded

		@examples
			$o = obj(666);
			$sc = $o->subclass();
	**/
	function subclass()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->subclass();
	}

	/** sets the subclass of the current object
		@attrib api=1

		@param param required type=int
			subclass id

		@errors
			none, if implicit save is off

		@returns
			old subclass id

		@comment
			The subclass can be defined separately for each class and is just an opaque integer, it only has meaning fir each class separately

		@examples
			$o = obj(666);
			$o->set_subclass(SC_FOO);
	**/
	function set_subclass($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_subclass($param);
	}

	/** returns all of the flags that the object has set
		@attrib api=1

		@errors
			none

		@returns
			flags that the current object has, NULL if no object is loaded

		@examples
			$o = obj(45);
			$fl = $o->flags();
			if ($fl & FL_FISH)
			{
				echo "you have fish!";
			}
	**/
	function flags()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->flags();
	}

	/** sets all the flags that the current object has
		@attrib api=1

		@param param required type=int
			flags that contains all the flags

		@errors
			none, if implicit save is off

		@returns
			previous state of the flags

		@examples
			$o = obj(67);
			$fl = $o->flags();
			$fl |= FL_FOO;
			$fl |= FL_BAR;
			$o->set_flags($fl);
	**/
	function set_flags($param)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_flags($param);
	}

	/** returns the state of the flag that is the parameter
		@attrib api=1

		@param flag_id required

		@errors
			none

		@returns
			the state of the flag for the current object, NULL if no object is loaded

		@examples
			$o = obj(78);
			if ($o->flag(FL_KALA))
			{
				echo "you hafe fish!";
			}
	**/
	function flag($param)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->flag($param);
	}

	/** sets or clears the specified flag
		@attrib api=1

		@param flag required type=int
			flag id that is set

		@param val required type=bool
			flag value

		@errors
			none, if implicit save is off

		@returns
			the boolean old state of the flag

		@examples
			$o = obj(666);
			$o->set_flag(FL_FISH, true):
			$o->save();
	**/
	function set_flag($flag, $val)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_flag($flag, $val);
	}

	/** returns the value of the specified metadata field
		@attrib api=1

		@param param optional type=string
			key of the metadata element

		@errors
			none

		@returns
			value of the metadata element, NULL if no element by that name is found, NULL
			if no current object exists

		@examples
			$o =& obj(67);
			$val = $o->meta("kala");
	**/
	function meta($param = false)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->meta($param);
	}

	/** sets the value for the specified metadata element
		@attrib api=1

		@param param type=string|array
			key of the metadata element
			or array of whole meta content

		@param val type=any default=NULL
			 value of the metadata element
			 not applicable if $key is array, required if $key is a meta field name

		@errors
			none, if implicit save is off

		@returns
			the previous value for the metadata element

		@examples
			$o = obj(78);
			$o->set_meta("fish", "tunafish");
	**/
	function set_meta($param, $value = null)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_meta($param, $value);
	}

	/** returns the value for the specified property for the current object
		@attrib api=1

		@errors
			none

		@param param required type=string
			the name of the property whose value is to be returned.
			 the property name might also be a chain of property names, for instance
			  if the object is of type CL_TASK, then you could give the argument
			  customer.contact.linn.name and get the name of the city of the address of the customer that is in the customer property of the task object
			  if any of the properties is empty or contains a deleted object, null is returned

		@returns
			the value of the specified proerty, NULL if no such property exists,
			NULL if no current object is loaded

		@examples
			$o = obj(56);
			$val = $o->prop("fish");

			$o = obj(1405);
			echo $o->prop("customer.contact.linn.name");
	**/
	public function prop($param)
	{
		$this->_check_lock_read();
		$method = "awobj_get_{$param}";

		if (method_exists($GLOBALS["objects"][$this->oid], $method))
		{
			return $GLOBALS["objects"][$this->oid]->$method();
		}
		else
		{
			return $GLOBALS["objects"][$this->oid]->prop($param);
		}
	}

	/** Returns the value of drafted property, NULL on failure.
		@attrib api=1

		@errors none

		@param param required type=string
			The nam of the property whose draft is to be returned.

		@returns
			The drafted value of specified property. Drafts are kept for seperately for every user.
			Return NULL on failure.

		@exaples
			$o = obj(56);
			$drafted_foo = $o->draft("foo");
	**/
	function draft($param)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->draft($param);
	}

	/** returns the value for the specified property for the current object, suitable for displaying to the user directly in xml, html
		@attrib api=1

		@param param type=string
			the name of the property whose value is to be returned
	**/
	public function prop_xml($param)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->prop_xml($param);
	}

	/** returns the value for the specified property for the current object, suitable for displaying to the user
		@attrib api=1

		@param param required type=string
			the name of the property whose value is to be returned

		@errors
			none

		@returns
			the value of the specified proerty, NULL if no such property exists,
			NULL if no current object is loaded. If the property contains an oid
			and is of a supported type, then the oid will be resolved to the objects' name.
			Useful if you want to display property values to the user.

		@examples
			$o = obj(56);
			$val = $o->prop_str("employee");#/php#

			- Will echo the name of the crm_company that is selected from the
			relpicker property employee for the object 56.
	**/
	public function prop_str($param, $is_oid = NULL)
	{
		$this->_check_lock_read();
		$method = "awobj_str_{$param}";

		if (method_exists($GLOBALS["objects"][$this->oid], $method))
		{
			return $GLOBALS["objects"][$this->oid]->$method();
		}
		else
		{
			return $GLOBALS["objects"][$this->oid]->prop_str($param, $is_oid);
		}
	}

	/** returns an array of properties that are defined for the current object's class
		@attrib api=1

		@errors
			none

		@returns
			array of property id => array with property data pairs containing all properties for the current object's class

		@examples
			$o = obj();
			$o->set_class_id(CL_MENU);
			echo "menu class properties are:\n";
			foreach($o->get_property_list() as $pn => $pd)
			{
				echo "$pn => $pd[caption] \n";
			}
	**/
	function get_property_list()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_property_list();
	}

	/** returns an array of groups that are defined for the current object's class
		@attrib api=1

		@errors
			none

		@returns
			array of group id => array with group data pairs containing all groups for the current object's class

		@examples
			$o = obj();
			$o->set_class_id(CL_MENU);
			echo "menu class groups are:\n";
			foreach($o->get_group_list() as $gn => $gd)
			{
				echo "$gn => $gd[caption] \n";
			}
	**/
	function get_group_list()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_group_list();
	}

	/** returns an array containing the relation types for the current object's class
		@attrib api=1

		@errors
			none

		@returns
			array of relation info

	**/
	function get_relinfo()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_relinfo();
	}

	/** returns an array containing the db tables for the current object's class
		@attrib api=1

		@errors
			none

		@returns
			array of table info

	**/
	function get_tableinfo()
	{
		$this->_check_lock_read();
		if (!is_object($GLOBALS["objects"][$this->oid]))
		{
			return array();
		}
		if (!method_exists($GLOBALS["objects"][$this->oid], "get_tableinfo"))
		{
			return array();
		}
		return $GLOBALS["objects"][$this->oid]->get_tableinfo();
	}

	/** returns an array containing the classinfo for the current object's class
		@attrib api=1

		@errors
			none

		@returns
			array of classinfo

	**/
	function get_classinfo()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_classinfo();
	}

	/** sets the value of the specified property for the current object
		@attrib api=1

		@param key required type=string
			the name of the property whose value is to be set

		@param val required type=any
			the value of the property to set

		@errors
			none, if implicit save is off


		@returns
			the old value of the property

		@examples
			$o =& obj(56);
			$o->set_prop("fish", "tunafish");
	**/
	function set_prop($key, $value)
	{
		$this->_check_lock_write();
		$method = "awobj_set_{$key}";

		if (method_exists($GLOBALS["objects"][$this->oid], $method))
		{
			return $GLOBALS["objects"][$this->oid]->$method($value);
		}
		else
		{
			return $GLOBALS["objects"][$this->oid]->set_prop($key, $value);
		}
	}

	/** returns an array of all the properties and their values for the object
		@attrib api=1

		@errors
			- error is thrown if no object is loaded

		@returns
			array of properties, proprty names are keys, property values are values

		@examples
			$o = obj(6);
			$props = $o->properties();
			echo $props["name"];
	**/
	function properties()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->properties();
	}

	function fetch()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->fetch();
	}

	function last()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->last();
	}

	/** returns the oid of the object that the current object is brother to
		@attrib api=1

		@errors
			none

		@returns
			the oid of the object that the current object is brother to

		@examples
			$o =& obj(3);
			$bro_id = $o->brother_of();
	**/
	function brother_of()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->brother_of();
	}

	//DEPRECATED
	function instance() { $tmp = $GLOBALS["objects"][$this->oid]->instance(); return $tmp; }

	/** creates a brother to the current object
		@attrib api=1

		@param parent required type=int
			the parent of the brother

		@comment
			there can only be one brother per parent, so that when you try to create
			another under the same parent, the old one is returned instead

		@errors
			- if no object is loaded, error is thrown
			- if parent argument is not a valid oid, error is thrown

		@returns
			 the oid of the brother object

		@examples:
			$o = obj(34);
			$bro = obj($o->create_brother(666));
	**/
	function create_brother($parent)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->create_brother($parent);
	}

	/** checks if the current object has connections to other objects
		@attrib api=1

		@comment
			has all the same parameters, that connections_from has

		@returns
			true if there are any connections that match the given parameters
			false, if not.

		@examples

			if (!$o->is_connected_to(array("to" => 666, "type" => "RELTYPE_FOO")))
			{
				$o->connect(array(
					"to" => 666,
					"type" => "RELTYPE_FOO"
				));
			}
	**/
	function is_connected_to($param)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->is_connected_to($param);
	}

	/** sets acl for the current object
		@attrib api=1

		@param group required type=object
			The object of the user group for which to set acl

		@param acl required type=array
			An array of acls to set to the given group for this object

		@returns
			none

		@errors
			none

		@examples
			$o = obj(1);
			$o->acl_set($group, array("can_view" => 1, "can_edit" => 0, "can_delete" => 0));
	**/
	function acl_set($group, $acl)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->acl_set($group, $acl);
	}

	/** returns a list of all the current acl connections for this object
		@attrib api=1

		@returns
			array of group_id => array of acls
			for each acl relation that this object has

		@errors
			error is thrown if no current object is loaded

		@examples
			$o = obj(6);
			foreach($o->acl_get() as $gid => $acl)
			{
				echo "group $gid  has acls ".dbg::dump($acl);
			}
	**/
	function acl_get()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->acl_get();
	}

	/** removes an acl relation from the curtrent object
		@attrib api=1 params=pos

		@returns
			nothing

		@errors
			error is thrown if no current object is loaded

		@param g_oid required type=oid
			The oid of the group to remove the relation with

	**/
	function acl_del($g_oid)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->acl_del($g_oid);
	}

	/** Returns the object's data in xml format
		@attrib api=1 params=name

		@param copy_subobjects type=bool default=true
			If true, all subobjects are also in the xml

		@param copy_subfolders type=bool default=false
			If true, all subfolders are in the xml as well

		@param copy_subdocs type=bool default=false
			If true, all documnents under the current object are in the xml as well

		@param copy_rels type=bool default=true
			If true, connections for the object are copied, but not the objects they point to

		@param new_rels type=bool default=false
			If true, connections from the objects are copied, and the objects they point to, are also copied

		@param no_header type=bool default=false
			If true, returned xml string will not have xml header. Default false.

		@errors
			none

		@returns
			string containing xml that contains the object data

		@examples:
			$o = obj(1);
			$xml = $o->get_xml(array(
				"copy_subobjects" => true,
				"new_rels" => true
			));

			$new_obj = object::from_xml($xml, 6); // copies all objects and their relations from object 1 to object 6
	**/
	function get_xml(array $options = array())
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_xml($options);
	}

	/** creates objects from the xml string given
		@attrib api=1

		@param xml required type=string
			The xml data returned from get_xml

		@param parent required type=oid
			The object under which to create the new objects

		@errors
			none

		@returns
			The first new object that was created

		@examples
			$o = obj(1);
			$xml = $o->get_xml(array(
				"copy_subobjects" => true,
				"new_rels" => true
			));

			$new_obj = object::from_xml($xml, 6); // copies all objects and their relations from object 1 to object 6
	**/
	function from_xml($xml, $parent)
	{
		$this->_check_lock_write();
		return _int_object::from_xml($xml, $parent);
	}

	function set_create_new_version()
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_create_new_version();
	}

	function load_version($v)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->load_version($v);
	}

	function set_save_version($v)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_save_version($v);
	}

	function set_no_modify($arg)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->set_no_modify($arg);
	}

	function originalize()
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->originalize();
	}

	/** Returns TRUE if the object has been saved -- a call to self::save() has ever been made
		@attrib api=1 params=pos
		@returns boolean
	**/
	public function is_saved()
	{
		return $GLOBALS["objects"][$this->oid]->is_saved();
	}

	/** Returns TRUE if the object is of this class or has this class as one of its parents
		@attrib api=1 params=pos

		@param class_id required type=int
			One of CL_... constants

		@errors
			none

		@returns boolean
	**/
	public function is_a($class_id)
	{
		return $GLOBALS["objects"][$this->oid]->is_a($class_id);
	}

	/** Returns TRUE if the object class implements given interface
		@attrib api=1 params=pos
		@param name type=string
			Interface name to check
		@errors
			none
		@returns boolean
	**/
	public function implements_interface ($name)
	{
		return $GLOBALS["objects"][$this->oid]->implements_interface($name);
	}

	/** Returns TRUE if given method is defined in this object class
		@attrib api=1 params=pos
		@param name type=string
			Method name
		@errors
			none
		@returns boolean
	**/
	public function has_method ($name)
	{
		return $GLOBALS["objects"][$this->oid]->has_method($name);
	}

	/** Return the translation of the property
		@attrib name=trans_get_val api=1 params=pos

		@param prop type=string
			The name of the property the translation is asked for

		@param lang_id type=boolean default=AW_REQUEST_CT_LANG_ID
			The language id the translation is asked for, default is current language id

		@param ignore_status type=boolean default=FALSE
			If true, the status of the translation is ignored.

		@errors
			none

		@returns
			The translation of the property.

		@examples

	**/
	function trans_get_val($prop, $lang_id = AW_REQUEST_CT_LANG_ID, $ignore_status = false)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->trans_get_val($prop, $lang_id, $ignore_status);
	}

	function trans_get_val_str($prop)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->trans_get_val_str($prop);
	}

	function prop_is_translated($prop)
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->prop_is_translated($prop);
	}

	public function trans_set_val($prop, $value, $lang_id = AW_REQUEST_CT_LANG_ID)
	{
		$this->_check_lock_write();
		return $GLOBALS["objects"][$this->oid]->trans_set_val($prop, $value, $lang_id);
	}

	public function set_translation_status($lang_id, $active)
	{
		$this->_check_lock_write();
		$GLOBALS["objects"][$this->oid]->set_translation_status($lang_id, $active);
	}

	public function get_translated_langs()
	{
		$this->_check_lock_read();
		return $GLOBALS["objects"][$this->oid]->get_translated_langs();
	}

	public function __toString()
	{
		return "AutomatWeb " . ($this->class_id() ? aw_ini_get("classes.".$this->class_id().".def") : "new") . " object '".(string)$this->oid . "'";
	}

	public function __get($prop)
	{
		$this->_check_lock_read();
		$ref = $GLOBALS["objects"][$this->oid];
		if (method_exists($ref, $prop))
		{
			return $ref->$prop();
		}
		return $ref->prop($prop);
	}

	public function __set($prop, $value)
	{
		$this->_check_lock_write();
		$ref = $GLOBALS["objects"][$this->oid];
		$meth = "set_".$prop;
		if (method_exists($ref, $meth))
		{
			return $ref->$meth($value);
		}
		return $ref->set_prop($prop, $value);
	}

	public function __isset($prop)
	{
		$this->_check_lock_read();
		$ref = $GLOBALS["objects"][$this->oid];
		return $ref->is_property($prop);
	}

	public function __unset($prop)
	{
		$this->__set($prop, null);
	}

	public function lock($type = aw_locker::LOCK_WRITE, $scope = aw_locker::SCOPE_PROCESS, $wait_type = aw_locker::WAIT_BLOCK, $release_time = 0)
	{
		// lock object by id inter-process
		aw_locker::lock("object", $this->oid, $type, $scope, $wait_type, $release_time);
	}

	public function unlock()
	{
		aw_locker::unlock("object", $this->oid);
	}

	public function is_locked()
	{
		return aw_locker::is_locked("object", $this->oid);
	}

	public function __destruct()
	{
	}

	private function _check_lock_read()
	{
		aw_locker::is_locked("object", $this->oid, aw_locker::LOCK_FULL);
	}

	private function _check_lock_write()
	{
		aw_locker::is_locked("object", $this->oid, aw_locker::LOCK_WRITE);
	}
}


/** Generic AW object exception **/
class awex_obj extends aw_exception
{
	//!!! siia, et automaatselt laetaks obj id ja meetod, milles ex tehti. viimane throw?
	public $awobj_id;
}

/** Requested method not defined for current object **/
class awex_obj_method extends awex_obj {}

/** No access rights to perform requested operation **/
class awex_obj_acl extends awex_obj
{
	protected $message = "No access rights to perform requested operation";
}

/** Object doesn't exist **/
class awex_obj_na extends awex_obj
{
	protected $message = "Object doesn't exist";
}

/** Object is deleted **/
class awex_obj_deleted extends awex_obj
{
	protected $message = "Object is deleted";
}

/** Generic property exception **/
class awex_obj_prop extends awex_obj {}

/** Generic object method parameter exception **/
class awex_obj_param extends awex_obj {}

/** Parent-not-set exception **/
class awex_obj_parent extends awex_obj {}

/** Property is read-only **/
class awex_obj_readonly extends awex_obj_prop {}

/** Generic type mismatch condition. A method argument or something else is not of expected type **/
class awex_obj_type extends awex_obj
{
	public $argument_name;
}

/** Generic class mismatch error condition. Contained internal object class is not what expected or tried to set class id on an object with clid already set **/
class awex_obj_class extends awex_obj {}

/** When in exclusive save and the object has been modified, this is thrown **/
class awex_obj_modified_by_others extends awex_obj {}

/** Indicates wrong object state for given operation **/
class awex_obj_state extends awex_obj
{
	protected $message = "Object state inappropriate for requested operation";
}

/** Indicates that given operation can't be executed on an unsaved object **/
class awex_obj_state_new extends awex_obj_state
{
	protected $message = "Operation can't be executed on a new object";
}

/** Indicates that data store integrity is compromised. **/
class awex_obj_data_integrity extends awex_obj {}

/** Indicates that objects which must have a specific count are in excess or deficiency **/
class awex_obj_invalid_count extends awex_obj_data_integrity {}

/** Indicates an attempt to violate object system architecture **/
class awex_obj_system extends awex_obj {}
