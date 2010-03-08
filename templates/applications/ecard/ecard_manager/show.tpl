<h3>{VAR:name}</h3>

<!-- SUB: dirlist_item -->
<a href="{VAR:dirurl}">{VAR:dirname}</a> 
<!-- END SUB: dirlist_item -->

<!-- SUB: images_list -->
{VAR:list}
<!-- END SUB: images_list -->

<!-- SUB: ecard_input -->
<br>
{VAR:card}
<br>
<table>
{VAR:form}

<!-- SUB: form_item -->
<tr><td>{VAR:caption}</td><td>
<!-- SUB: form_item_error -->
 <span class='error'>{VAR:errormsg}</span><br>
<!-- END SUB: form_item_error -->
{VAR:error}{VAR:element}</td></tr>
<!-- END SUB: form_item -->
</table>
<br>
<b>{VAR:message}</b>
<!-- END SUB: ecard_input -->

<!-- SUB: card_1 -->
<img src="{VAR:imgurl}"><br>{VAR:imgtext}<br>- <i>{VAR:from}</i>
<!-- END SUB: card_1 -->

<!-- SUB: card_2 -->
<table>
<tr><td><img src="{VAR:imgurl}"></td><td>{VAR:imgtext}</tr>
</table><br>- <i>{VAR:from}</i>
<!-- END SUB: card_2 -->
