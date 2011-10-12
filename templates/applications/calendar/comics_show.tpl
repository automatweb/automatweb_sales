<div class="r">
/ {VAR:sm_n}
<div class="r_n">Nr. {VAR:num}</div>
</div>
<center>
<div id="k">
<!-- koomiks --><a href='{VAR:comment_url}'><img border="0" src="{VAR:image}" alt=""></a><!-- /koomiks --><br />
{VAR:content}
</div>
</center>
<div class="kp">{VAR:start1}</div>

<!-- SUB: HAS_COMMENTS -->
<table border=0 width="100%" bgcolor="#CCCCCC"><tr><td>
<span style="font-size: 12px; color: #FFFFFF">Kommentaarid:</span> <br>

<!-- SUB: COMMENT -->
<hr>
<span style="font-size: 12px; color: #FFFFFF">{VAR:c_title}</span><br>
<span style="font-size: 12px; color: #FFFFFF">{VAR:c_commtext}</span><br>

<!-- END SUB: COMMENT -->

<form action='orb{VAR:ext}' method="POST">
	<table border="0" bgcolor="#CCCCCC"> 
		<tr>
			<td><span style="font-size: 12px; color: #FFFFFF">Pealkiri:</span></td>
			<td><input type="text" name="title"></td>
		</tr>
		<tr>
			<td><span style="font-size: 12px; color: #FFFFFF">Sisu:</span></td>
			<td><textarea name="ct"></textarea></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="L&auml;busta"></td>
		</tr>
	</table>
	{VAR:reforb}
</form>
</td></tr></table>
<!-- END SUB: HAS_COMMENTS -->
