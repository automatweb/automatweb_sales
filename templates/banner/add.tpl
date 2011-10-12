<form method="POST" action="reforb{VAR:ext}" name='b88' enctype="multipart/form-data">
<table border="0" cellspacing="1" cellpadding="2" bgcolor="#CCCCCC">
<tr>
	<td class="fcaption2" colspan=2>{VAR:image}</td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_NAME}:</td>
	<td class="fform"><input type="text" name="name" size="40" value='{VAR:name}'></td>
</tr>
<tr>
	<td class="fcaption2" valign="top">{VAR:LC_BANNER_COMMENT}:</td>
	<td class="fform"><textarea name="comment" rows=5 cols=50>{VAR:comment}</textarea></td>
</tr>
<tr>
	<td class="fcaption2" colspan=2>{VAR:LC_BANNER_WHERE}:</td>
</tr>
<tr>
	<td colspan=2 class="fform"><select name='parent'>{VAR:parent}</select></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_POSITION}:</td>
	<td class="fform"><select multiple name='grp[]'>{VAR:grp}</select></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_CLIENT}:</td>
	<td class="fform"><select multiple name='buyer[]'>{VAR:buyer}</select></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_URL_WHERE_HEADED:</td>
	<td class="fform"><input type="text" name="url" value="{VAR:url}"></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_ACTIVE}:</td>
	<td class="fform"><input type="checkbox" name="act" value="1" {VAR:act}></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_PROBAB_OF_LOOKS}:</td>
	<td class="fform"><input type="text" name="probability" size=2 value="{VAR:probability}">%&nbsp;&nbsp;({VAR:LC_BANNER_NOTE.</td>
</tr>
<!-- SUB: CHANGE -->
<tr>
	<td class="fcaption2" colspan=2><a href='{VAR:periods}'>{VAR:LC_BANNER_CHOOSE_ACTIVITY_PERIOD}</a></td>
</tr>
<!-- END SUB: CHANGE -->
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_HOW_MUCH_SHOW_BANNER}:</td>
	<td class="fform"><input type='text' name='max_views' value='{VAR:max_views}' size=10></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_HOW_MANY_CLICKS_ON_BANNER}:</td>
	<td class="fform"><input type='text' name='max_clicks' value='{VAR:max_clicks}' size=10></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_HOW_MANY_TO_ONE_USER}:</td>
	<td class="fform"><input type='text' name='max_views_user' value='{VAR:max_views_user}' size=10></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_HOW_MANY_CLIKS_USER_MAKES}:</td>
	<td class="fform"><input type='text' name='max_clicks_user' value='{VAR:max_clicks_user}' size=10></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_PROFILES}:</td>
	<td class="fform"><select multiple name='profiles[]'>{VAR:profiles}</select></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_PICTURE}:</td>
	<td class="fform"><input type="hidden" name="MAX_FILE_SIZE" value="1000000"><input type="file" name="fail"></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_PICTURE_URL}:</td>
	<td class="fform"><input type="text" name="b_url" value="{VAR:b_url}"></td>
</tr>
<tr>
	<td class="fcaption2">Flash:</td>
	<td class="fform"><input type="file" name="flash"></td>
</tr>
<tr>
	<td class="fcaption2">HTML:</td>
	<td class="fform"><textarea name="html">{VAR:html}</textarea></td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_SHOWS}:</td>
	<td class="fform">{VAR:views}</td>
</tr>
<tr>
	<td class="fcaption2">{VAR:LC_BANNER_CLICKS}:</td>
	<td class="fform">{VAR:clics}</td>
</tr>
<tr>
	<td class="fcaption2">Click-through ratio:</td>
	<td class="fform">{VAR:ctr}%</td>
</tr>
<tr>
	<td class="fcaption2" colspan=2><a href='{VAR:stats}'>{VAR:LC_BANNER_CLICKS}</a></td>
</tr>
<tr>
	<td class="fform" align="center" colspan="2">
		<input type="submit" value="Salvesta">
		{VAR:reforb}
	</td>
</tr>
</table>
</form>
