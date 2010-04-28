<?php

namespace automatweb;


/*
@classinfo maintainer=voldemar
*/

class quickmessagebox_obj extends _int_object
{
	const AW_CLID = 816;

	// approved_senders property value options
	const APPROVED_SENDERS_ANYONE = 1; // all messages are received
	const APPROVED_SENDERS_CONTACTS = 2; // only messages from users in contact list are received
	const APPROVED_SENDERS_NONE = 3; // no messages are received

	// addressees property options
	const ADDRESSEES_EVERYONE = 1; // can send to anyone
	const ADDRESSEES_CONTACTS = 2; // can send only to users in contact list

	// message statuses
	const STATUS_READ = 1;
	const STATUS_UNREAD = 2;

	const COUNTER_FILE = "/files/qmsg_msg_counter.aw"; // users' messages counter data file relative location

	private $counter_file; // contains counter data file absolute path at runtime

	public function __construct($param)
	{
		$this->counter_file = self::get_counter_file();
		parent::__construct($param);
	}

	private static function get_counter_file()
	{
		return aw_ini_get("site_basedir") . self::COUNTER_FILE;
	}

	/**
	@attrib api=1
	@returns object_list
		Read messages.
	**/
	public function get_read_msgs()
	{
		$ol = new object_list($this->connections_from(array("type" => "RELTYPE_READ_MESSAGE")));
		return $ol;
	}

	/**
	Reads new messages.
	@attrib api=1
	@returns object_list
		Unread messages.
	**/
	public function read_new_msgs()
	{
		$unread = $this->connections_from(array("type" => "RELTYPE_UNREAD_MESSAGE"));
		$ol = new object_list();

		if (count($unread))
		{
			$ol = new object_list($unread);
			$this->disconnect(array(
				"from" => $ol->ids(),
				"type" => "RELTYPE_UNREAD_MESSAGE"
			));
			$this->connect(array(
				"to" => $ol->ids(),
				"type" => "RELTYPE_READ_MESSAGE"
			));
			$this->update_counter(- $ol->count());
		}

		return $ol;
	}


	/**
	@attrib api=1 params=pos
	@param limit optional type=int default=0
		Messages per 'page'.
	@param page optional type=int default=1
		'Page' number
	@returns object_list
		Messages sent by this box's owner.
	**/
	public function get_sent_msgs($limit = 0, $page = 1)
	{
		if (!is_int($limit) or !is_int($page) or $page < 1 or $limit < 0)
		{
			throw new awex_qmsg_param("Invalid page ('" . $page . "') or limit ('" . $limit . "') parameter");
		}

		$limit = (0 === $limit) ? null : (($page * $limit) . "," . $limit);
		$ol = new object_list(array(
			"class_id" => CL_QUICKMESSAGE,
			"limit" => $limit,
			"lang_id" => array(),
			"box" => $this->id()
		));
		return $ol;
	}

	/**
	@attrib api=1
	@param msg required type=cl_quickmessage
		Message object
	@returns void
	@comment
		Posts a message to this box.
	@errors
		Throws awex_qmsg_unwanted_msg when poster is not approved.
	**/
	public function post_msg(object $msg)
	{
		$approved_senders = (int) $this->prop("approved_senders");

		if (!$approved_senders)
		{
			$approved_senders = constant("self::" . aw_ini_get("quickmessaging.approved_senders"));
		}

		if (
			(self::APPROVED_SENDERS_CONTACTS === $approved_senders and !in_array($msg->prop("from"), $this->prop("contactlist"))) or
			self::APPROVED_SENDERS_NONE === $approved_senders and
			obj($msg->prop("from"))->meta("usertype") != "superuser"
		)
		{
			throw new awex_qmsg_unwanted_msg("Message poster is not among this user's approved senders.");
		}

		$this->connect(array(
			"to" => $msg->id(),
			"type" => "RELTYPE_UNREAD_MESSAGE"
		));
		$this->update_counter(1);

		// make msg visible to recipient
		$owner = $this->get_first_obj_by_reltype("RELTYPE_OWNER");
		$group = new object($owner->get_default_group());
		$msg->acl_set($group, array(
			"can_add" => 0,
			"can_edit" => 0,
			"can_admin" => 0,
			"can_delete" => 0,
			"can_view" => 1
		));
	}

	/**
	@attrib api=1
	@returns array
	@comment
		Options for selecting value for approved_senders setting.
	**/
	public static function get_approved_senders_options()
	{
		$options = array(
			self::APPROVED_SENDERS_ANYONE => t("anyone"),
			self::APPROVED_SENDERS_CONTACTS => t("users in my contact list"),
			self::APPROVED_SENDERS_NONE => t("no-one")
		);
		array_unshift($options, sprintf(t("system default (currently '%s')"), $options[constant("self::" . aw_ini_get("quickmessaging.approved_senders"))]));
		return $options;
	}

	/**
	@attrib api=1
	@returns array
	@comment
		Options for selecting value for show_addressees setting.
	**/
	public static function get_addressees_options()
	{
		$options = array(
			self::ADDRESSEES_EVERYONE => t("all users"),
			self::ADDRESSEES_CONTACTS => t("users in my contact list"),
		);
		array_unshift($options, sprintf(t("system default (currently '%s')"), $options[constant("self::" . aw_ini_get("quickmessaging.show_addressees"))]));
		return $options;
	}

	/**
	@attrib api=1
	@param user required type=cl_user
		User object
	@returns cl_quickmessagebox object
		User's messagebox.
	@errors
		throws awex_qmsg_no_box if user has no messagebox.
		throws awex_qmsg_cfg if user has more than one messagebox.
		throws awex_qmsg_acl if quickmessagebox is defined but current user has no permissions to access it
	@comment
		If no messagebox found for user and ini setting quickmessaging.auto_create_box is set to true, creates one.
		Options are set to default values found in current ini configuration.
	**/
	public static function get_msgbox_for_user(object $user, $create = false)
	{
		$filter = array(
			"class_id" => CL_QUICKMESSAGEBOX,
			"site_id" => array(),
			"lang_id" => array(),
			"CL_QUICKMESSAGEBOX.RELTYPE_OWNER" => $user->id(),
			"status" => new obj_predicate_not(object::STAT_DELETED)
		);
		$ol = new object_list($filter);
		$ol_count = $ol->count();

		aw_disable_acl(); // because could be a call when another user is logged in
		$ol_check = new object_list($filter);
		$ol_check = $ol_check->count();
		aw_restore_acl();

		if (1 === $ol_check and 0 === $ol_count)
		{
			throw new awex_qmsg_acl("No access permissions for user's quickmessagebox.");
		}
		elseif (1 === $ol_count)
		{
			$box = $ol->begin();
		}
		elseif (0 === $ol_count)
		{
			if (aw_ini_get("quickmessaging.auto_create_box") || $create)
			{
				$approved_senders = constant("self::" . aw_ini_get("quickmessaging.approved_senders"));
				$show_addressees = constant("self::" . aw_ini_get("quickmessaging.show_addressees"));
				$box = new object();
				$box->set_class_id(CL_QUICKMESSAGEBOX);
				$box->set_parent($user->id());
				$box->set_name(sprintf(t("Quickmessagebox of %s"), $user->name()));
				$box->set_prop("owner", $user->id());
				$box->set_prop("approved_senders", $approved_senders);
				$box->set_prop("show_addressees", $show_addressees);
				aw_disable_acl(); // because trying to save under another user object
				$box->save();
				aw_restore_acl();
				$box->connect(array(
					"to" => $user->id(),
					"type" => "RELTYPE_OWNER"
				));
				$group = new object($user->get_default_group());
				$box->acl_set($group, array(
					"can_add" => 1,
					"can_edit" => 1,
					"can_admin" => 0,
					"can_delete" => 1,
					"can_view" => 1
				));
			}
			else
			{
				throw new awex_qmsg_no_box("User has no messagebox configured.");
			}
		}
		else
		{
			throw new awex_qmsg_cfg("Messagebox configuration error. User has more than one messagebox.");
		}

		return $box;
	}

	/**
	@attrib api=1 params=pos
	@param msgs required type=array
		Array of message object instances or id-s
	@returns void
	@comment
		Deletes messages from this box.
	@errors
		throws awex_qmsg_param when
		- msgs parameter is empty
		- msgs contains a msg that doesn't belong to this msgbox
		throws awex_qmsg when
		- some messages couldn't be deleted for unspecified reasons
	**/
	public function delete_msgs($msgs)
	{
		if (empty($msgs) or !is_array($msgs))
		{
			throw new awex_qmsg_param("No messages to delete.");
		}

		// load messages
		$delete_q = array();
		foreach ($msgs as $msg)
		{
			if (!is_a($msg, "object"))
			{
				if (is_oid($msg))
				{
					$msg = new object($msg);
				}
				else
				{
					throw new awex_qmsg_param("Invalid message id '" . $msg . "'.");
				}
			}

			$delete_q[$msg->id()] = $msg;
		}

		// get connections to messages to be deleted.
		$c = new connection();
		$c = $c->find(array(
			"from" => $this->id(),
			"to" => array_keys($delete_q)
		));

		// get count of unread messages among those to be deleted
		$unread = array();
		$connected_msgs = array();
		foreach ($c as $connection)
		{
			if ("6" === $connection["reltype"]) // RELTYPE_UNREAD_MESSAGE
			{
				$unread[] = $connection["to"];
			}

			$connected_msgs[] = $connection["to"];
		}

		// check if connected to this box. delete.
		$failed = array();
		foreach ($delete_q as $msg)
		{
			try
			{
				if (in_array($msg->id(), $connected_msgs))
				{
					$this->disconnect(array(
						"from" => $msg->id(),
						"type" => array("RELTYPE_UNREAD_MESSAGE", "RELTYPE_READ_MESSAGE")
					));
				}
				elseif ($msg->prop("box") === $this->id())
				{
					$msg->delete();
				}
				else
				{
					$e = new awex_qmsg_box("Can't delete messages not belonging to this messagebox.");
					$e->qmsg_affected_msgs = array($msg->id() => "wrong box");
					$failed[$msg->id()] = $e;
				}
			}
			catch (\Exception $e)
			{
				$failed[$msg->id()] = $e;
			}
		}

		// subtract deleted from new message count
		$unread_deleted = count(array_diff($unread, array_keys($failed)));
		if ($unread_deleted)
		{
			$this->update_counter(- $unread_deleted);
		}

		if (count($failed))
		{
			$e = new awex_qmsg("Some messages couldn't be deleted.");
			$e->qmsg_affected_msgs = $failed;
			throw $e;
		}
	}

	/**
	@attrib api=1 params=pos
	@param msgbox_id required type=int
		Messagebox object id
	@returns int
		Number of new messages in specified box
	@errors
		throws awex_qmsg_counter on various counter reading errors
	**/
	public static function get_new_msgs_count($msgbox_id)
	{
		if (!is_oid($msgbox_id))
		{
			throw new awex_qmsg_param("Invalid messagebox id.");
		}

		$counter_file = self::get_counter_file();
		$fp = @fopen($counter_file, "r");

		if (flock($fp, LOCK_SH))
		{
			// get current counter
			$counter_data = @fread($fp, @filesize($counter_file));

			if (false === $counter_data)
			{
				throw new awex_qmsg_counter("New message counter couldn't be updated. Couldn't read counter file.", 1);
			}

			$counter_data = unserialize($counter_data);

			if (false === $counter_data)
			{
				throw new awex_qmsg_counter("New message counter couldn't be updated. Invalid counter data.", 2);
			}

			return isset($counter_data[$msgbox_id]) ? $counter_data[$msgbox_id] : 0;
		}
		else
		{
			throw new awex_qmsg_counter("New message counter couldn't be updated. Failed to acquire shared lock.", 6);
		}

		fclose($fp);
	}

	// $count - int, counter value changed by that amount
	private function update_counter($count)
	{
		// update unread msgs counter
		$fp = @fopen($this->counter_file, "a+");
		$ret = rewind($fp);

		if ($ret and flock($fp, LOCK_EX))
		{
			// get current counter
			$size = filesize($this->counter_file);

			if ($size)
			{
				$counter_data = @fread($fp, $size);

				if (false === $counter_data)
				{
					throw new awex_qmsg_counter("New message counter couldn't be updated. Couldn't read counter file.", 1);
				}

				$counter_data = unserialize($counter_data);

				if (false === $counter_data)
				{
					throw new awex_qmsg_counter("New message counter couldn't be updated. Invalid counter data.", 2);
				}
			}
			else
			{
				$counter_data = array();
			}

			// update counter
			if (!isset($counter_data[$this->id()]))
			{
				$counter_data[$this->id()] = 0;
			}

			$counter = $counter_data[$this->id()] + $count;

			if ($counter < 0)
			{
				throw new awex_qmsg_counter("New message counter couldn't be updated. Negative new count.", 5);
			}

			$counter_data[$this->id()] = $counter;
			$ret = ftruncate($fp, 0);

			if (false === $ret)
			{
				throw new awex_qmsg_counter("New message counter couldn't be updated. Couldn't reset file.", 8);
			}

			$ret = @fwrite($fp, serialize($counter_data));

			if (false === $ret)
			{
				throw new awex_qmsg_counter("New message counter couldn't be updated. Couldn't write counter file.", 3);
			}
		}
		else
		{
			throw new awex_qmsg_counter("New message counter couldn't be updated. Failed to acquire lock.", 4);
		}

		fclose($fp);
	}

	// finds / creates a user's messagebox and redirects to it
	function redir_userbox($url)
	{
		$o = self::get_msgbox_for_user(obj(aw_global_get("uid_oid")), true);
		return $this->mk_my_orb("change", array("group" => "message_inbox", "id" => $o->id(), "return_url" => $url), "quickmessagebox");
	}
}

/* Generic quickmessaging error condition indicator */
class awex_qmsg extends awex_obj
{
	public $qmsg_affected_msgs = array(); // array of quickmessage object id-s as index and exception or errorstring as element
}

/* Indicates invalid method parameter value */
class awex_qmsg_param extends awex_qmsg {}

/* Indicates that recipient user has in some way configured to not receive messages from that sender */
class awex_qmsg_unwanted_msg extends awex_qmsg {}

/* Generic messagebox error condition indicator */
class awex_qmsg_box extends awex_qmsg {}

/* Indicates that user has no quickmessagebox defined */
class awex_qmsg_no_box extends awex_qmsg_box {}

/* Indicates missing access permissions */
class awex_qmsg_acl extends awex_qmsg_box {}

/* Configuration errors */
class awex_qmsg_cfg extends awex_qmsg_box {}

/* Indicates unexpected message counter behaviours */
class awex_qmsg_counter extends awex_qmsg_box {}


?>
