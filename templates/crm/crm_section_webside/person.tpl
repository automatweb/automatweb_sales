<!-- SUB: SHOW_PHOTO -->
<div style="display: inline;padding: 5px 5px 5px 5px">{VAR:photo}</div>
<!-- END SUB: SHOW_PHOTO -->

<!-- SUB: header -->
<div style="display: inline;">
<table style="display: inline">
<tr><td valign="top">
<!-- END SUB: header -->

<!-- SUB: SHOW_NAME -->
<div class="text11b">{VAR:name}</div>

<!-- END SUB: SHOW_NAME -->

<!-- SUB: SHOW_RANK -->
<div class="text11">{VAR:rank}</div>
<br />
<!-- END SUB: SHOW_RANK -->

<!-- SUB: SHOW_PHONE -->
<div class="text11"><img src="{VAR:baseurl}/img/tel.gif">{VAR:phone}</div>
<!-- END SUB: SHOW_PHONE -->

<!-- SUB: SHOW_EMAIL -->
<div class="text11">
<img src="/img/mail.gif"><a href="mailto:{VAR:email}">{VAR:email}</a></div>
<!-- END SUB: SHOW_EMAIL -->

<!-- SUB: footer -->
</tr></td>
</table>
</div>
<!-- END SUB: footer -->
