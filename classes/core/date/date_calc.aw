<?php

////////////////////////////////////////////////////////////
// DEPRECATED. use date_calc class instead!
////////////////////////////////////////////////////////////


function get_date_range($args = array()) { return date_calc::get_date_range($args); }
function convert_wday($daycode) { return date_calc::convert_wday($daycode); }
function get_day_diff($time1,$time2) { return date_calc::get_day_diff($time1,$time2); }
function get_week_start($timestamp = null) { return date_calc::get_week_start($timestamp); }
function get_month_start() { return date_calc::get_month_start(); }
function get_day_start($tm = NULL) { return date_calc::get_day_start($tm); }
function get_year_start($tm = NULL) { return date_calc::get_year_start($tm); }
function timespans_overlap($a_from, $a_to, $b_from, $b_to) { return date_calc::timespans_overlap($a_from, $a_to, $b_from, $b_to); }
