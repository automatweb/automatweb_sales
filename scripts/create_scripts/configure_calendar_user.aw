<?php
// a script that will create an user interface and and allows doing other very nice things
// ok .. I want things thing to be able to run directly from the AW shell interface, so
// I do not want to create a class or something else

// why? because then I can run the script from cmdline and execute it from other system
// management scripts. Thats called integration baby!


// Imagine running /sbin/add_user and getting a configured user and calendar in AW as well

// doing this with propertys allows for maximal reuse of existing code
/*
@default form=configure
@default group=general

@property parent_folder type=relpicker reltype=RELTYPE_CONFIG_OPTION
@caption Vali kataloog

*/

// 1. create a calendar
// 2. create a folder for events
// 3. connect calendar to folder (event_folder)
// 4. connect calendar to user  (calendar_ownership)

// profit! but from where to I get the user object oid? Or other arguments for that matter?

//$use_parent = 124966;

// so I need a way to access the script configuration, eh?

// and I need the user object id!

// and I need a way to figure out the user_obj. FUCK!

if (!is_oid($parent_folder) || !is_oid($user_oid))
{
	print "cannot run the script at this time, prerequisites are not met";
}
else
{
	//print "pf = $parent_folder\n";
	//print "uo = $user_oid\n";

	$user_obj = new object($user_oid);
	$use_name = $user_obj->name();

	$event_folder = new object();
	$event_folder->set_class_id(CL_MENU);
	$event_folder->set_status(STAT_ACTIVE);
	$event_folder->set_parent($parent_folder);
	$event_folder->set_name("$use_name kalendri sündmuste kataloog");
	$event_folder->save();

	$cal = new object();
	$cal->set_class_id(CL_PLANNER);
	$cal->set_status(STAT_ACTIVE);
	$cal->set_parent($parent_folder);
	$cal->set_name("$use_name kalender");
	$cal->save();


	$cal->connect(array(
		"to" => $event_folder,
		"reltype" => "RELTYPE_EVENT_FOLDER",
	));

	$cal->connect(array(
		"to" => $user_obj,
		"reltype" => "RELTYPE_CALENDAR_OWNERSHIP",
	));

	// now I also need to connect this thing to a an user and from where pray tell to I get
	// the information about the user I need to connect to?

	// it is clear, that it will enter through the message, but how does this script KNOW it?

	$cal->set_prop("event_folder",$event_folder->id());
	$cal->save();

	//print "all done<br>";
};

?>
