<?php

if (empty($_SERVER["SSL_CLIENT_I_DN_CN"]))
{
	exit("ID kaarti ei leitud");
}

require_once "const.aw";
