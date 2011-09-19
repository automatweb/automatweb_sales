<?php

class aw_console implements orb_public_interface
{
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	function show_console($arr)
	{
		echo <<<ENDHTML
<html>
<body>
<applet code="/automatweb/js/p2pchat/org/litesoft/p2pchat/P2PChatAWT" width="300" height="200">
</applet>
</body>
</html>
ENDHTML;
exit;
	}
}
