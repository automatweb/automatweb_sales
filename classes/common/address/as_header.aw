<?php

### address system settings
define ("ADDRESS_SYSTEM", 1);
define ("AS_NEWLINE", "<br />");
define ("ADDRESS_STREET_TYPE", "street"); # used in many places. also in autocomplete javascript -- caution when changing.
define ("ADDRESS_COUNTRY_TYPE", "country"); # used in many places. also in autocomplete javascript -- caution when changing.
define ("ADDRESS_DBG_FLAG", "address_dbg");

/** Address system generic error **/
class awex_as extends awex_obj {}

/** Administrative structure related error **/
class awex_as_admin_structure extends awex_as {}
