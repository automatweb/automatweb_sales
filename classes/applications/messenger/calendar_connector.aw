<?php
// the purpose of this class is to provide form definition for creating events from
// incoming messages
/*
@classinfo maintainer=tarvo
@default group=general
@default form=connector

@property class_id type=select 
@caption Klass

@property start type=datetime_select
@caption Algus

@property title type=textbox size=70
@caption Pealkiri

@property content type=textarea cols=70 rows=15
@caption Sisu

@property main_calendar type=select 
@caption Põhikalender

@property calendars type=chooser orient=vertical multiple=1
@caption Teised kalendrid

@property projects type=chooser orient=vertical multiple=1 
@caption Projektid


@property sbt type=submit
@caption Loo sündmus

@forminfo connector onsubmit=process_message

*/
class calendar_connector extends class_base
{

	function calendar_connector()
	{
		$this->init();
		//$this->orb_class = get_class($this);
	}
};
?>
