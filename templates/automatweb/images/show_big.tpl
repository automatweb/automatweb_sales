<!-- SUB: minimal -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="page-enter" content="blendtrans(duration=1.0)" />
		<meta http-equiv="page-exit" content="blendtrans(duration=1.0)" />
		<title></title>
		<script type="text/javascript" language="javascript">
		<!--
			var iw = 0;
			var ih = 0;
			function preload() {
				var newImg = new Image();
				newImg.src = '{VAR:big_url}';
				iw = newImg.width;
				ih = newImg.height;
				function rs(w,h) {
					var iw1 = w;
					var ih1 = h;
					window.resizeTo(iw1,ih1);
				}
				
				var hd = 0;
				var wd = 0;
				if (window.outerHeight)
				{
					var oh = window.outerHeight;
					var wih = window.innerHeight;
					hd = oh - wih;
					
				}
				else
				{
					hd = 80;
					wd = 20;
					self.moveTo(0,0);
				}
				rs(iw+wd,ih+hd);
			}
			-->
		</script>
		<style type="text/css"> 
			.button { background: #f0f0f0; float: right;  padding: 3px; border: 1px solid #000}
			.button a { color: #000; text-decoration: none; font-family: verdana,arial,tahoma,times,serif; font-size: 10px;}
		</style>
	</head>
	<body onload="preload()" style="height:100%; text-align: center; vertical-align: middle; margin: 0px">
		<table border="0" cellspacing="0" cellpadding="0" style="height: 100%"><tr><td style="height: 100%; vertical-align: middle">
			<a title="{VAR:LC_CLOSE}" href='javascript:window.close()'><img alt="{VAR:LC_CLOSE}" title="{VAR:LC_CLOSE}" border="0" src='{VAR:big_url}' /></a>
		</td></tr></table>
		<div style="float: right; height: 23px; width: 150px; bottom: 5px; right: 5px; top: auto; left: auto; position: absolute; text-align: right">

<!-- SUB: NEXT_LINK -->
<div class="button" style="margin-left: 5px; -moz-border-radius-bottomleft: 7px; -moz-border-radius-topright: 7px; -moz-border-radius-topleft: 7px; -moz-border-radius-bottomright: 7px"><a href='{VAR:next_url}'>{VAR:LC_NEXT}</a></div>
<!-- END SUB: NEXT_LINK -->
<!-- SUB: PREV_LINK -->
<div class="button" style="-moz-border-radius-bottomleft: 7px; -moz-border-radius-topleft: 7px; -moz-border-radius-bottomright: 7px; -moz-border-radius-topright: 7px"><a href='{VAR:prev_url}'>{VAR:LC_PREV}</a></div>
<!-- END SUB: PREV_LINK -->

		</div>
	</body>
</html>
<!-- END SUB: minimal -->
<!-- SUB: with_comments -->
<html>
<body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0"><a alt="{VAR:LC_CLOSE}" title="{VAR:LC_CLOSE}" href='javascript:window.close()' style="float: left;"><img alt="{VAR:LC_CLOSE}" title="{VAR:LC_CLOSE}" border="0" src='{VAR:big_url}'></a>{VAR:comments}</body>
</html>
<!-- END SUB: with_comments -->

