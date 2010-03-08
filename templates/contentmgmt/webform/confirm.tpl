<h1>Kinnitus</h1>
<!-- 
This is an example template file of the webforms confirmation page. 
-->
<p>
{VAR:userdate1_caption}: {VAR:userdate1_day}.{VAR:userdate1_month}.{VAR:userdate1_year}
</p>
<p>
{VAR:user1_caption}: {VAR:user1} 
</p>
<p>
{VAR:uservar1_caption}: {VAR:uservar1} 
</p>
<form method="post" action="{VAR:baseurl}/orb.aw">

{VAR:reforb}
{VAR:confirmed_button} {VAR:not_confirmed_button}
</form>
