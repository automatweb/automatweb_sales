<div style="white-space: nowrap;">
<!-- SUB: prev -->
<a href="{VAR:link}" title="{VAR:caption}" style="font-size: 1.3em; font-weight: bold; text-decoration: none;">&lt;&lt;</a>
<!-- END SUB: prev -->
<select {VAR:class} name="ft_page" onChange="window.location='{VAR:pageurl}ft_page='+this.options[this.selectedIndex].value">
<!-- SUB: page -->
<option value="{VAR:ft_page}">{VAR:text}</option>
<!-- END SUB: page -->

<!-- SUB: sel_page -->
<option value="{VAR:ft_page}" selected>{VAR:text}</option>
<!-- END SUB: sel_page -->
</select>
<!-- SUB: next -->
<a href="{VAR:link}" title="{VAR:caption}" style="font-size: 1.3em; font-weight: bold; text-decoration: none;">&gt;&gt;</a>
<!-- END SUB: next -->
</div>
