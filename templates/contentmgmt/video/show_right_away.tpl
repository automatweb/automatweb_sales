<!--
<object width="{VAR:width}" height="{VAR:height}" 
classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" 
id="mediaplayer1">
<param name="Filename" value="{VAR:mpeg_fn}">
<param name="AutoStart" value="True">
<param name="ShowControls" value="True">
<param name="ShowStatusBar" value="False">
<param name="ShowDisplay" value="False">
<param name="AutoRewind" value="True">
<embed width="320" height="290" src="{VAR:mpeg_url}"
filename="{VAR:mpeg_fn}" autostart="True" 
showcontrols="True" showstatusbar="False" 
showdisplay="False" autorewind="True">
</embed> 
</object>-->
<OBJECT id='mediaPlayer' width="{VAR:width}" height="{VAR:height}" 
      classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95' 
      codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701'
      standby='Loading Microsoft Windows Media Player components...' type='application/x-oleobject'>
      <param name='fileName' value="{VAR:mpeg_url}">
      <param name='animationatStart' value='true'>
      <param name='transparentatStart' value='true'>
      <param name='autoStart' value="true">
      <param name='showControls' value="false">
      <param name='loop' value="true">
      <EMBED type='application/x-mplayer2'
        pluginspage='http://microsoft.com/windows/mediaplayer/en/download/'
        id='mediaPlayer' name='mediaPlayer' displaysize='4' autosize='-1' 
        bgcolor='darkblue' showcontrols="false" showtracker='-1' 
        showdisplay='0' showstatusbar='-1' videoborder3d='-1' width="{VAR:width}" height="{VAR:height}"
        src="{VAR:mpeg_url}" autostart="true" designtimesp='5311' loop="true">
      </EMBED>
      </OBJECT>
