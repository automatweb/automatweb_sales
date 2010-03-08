<!-- SUB: selallscript -->
<script type="text/javascript">
var chk_status = 1;

function selall(element)
{
        element = element + '[';
        len = document.changeform.elements.length;
        for (i=0; i < len; i++)
        {
                if (document.changeform.elements[i].name.indexOf(element) != -1)
                {
                        document.changeform.elements[i].checked = chk_status;
                        //window.status = "" + i + " / " + len;
                }
        }
        chk_status = chk_status ? 0 : 1;
}
</script>
<!-- END SUB: selallscript -->

<!-- SUB: hilight_script -->
<script type="text/javascript">
var sel_row_style = '{VAR:sel_row_style}';
function hilight(el,tgt)
{
        tgtel = document.getElementById(tgt);
        if (el.checked)
        {
                tgtel.setAttribute('oldclass',tgtel.className);
                tgtel.className = sel_row_style;
        }
        else
        {
                tgtel.className = tgtel.getAttribute('oldclass');
        };
}
</script>
<!-- END SUB: hilight_script -->

<!-- SUB: hover_script -->
<script type="text/javascript">
$("table#{VAR:table_id} tr.awmenuedittablerow").hover(
	function (){ $(this).children("td").addClass("{VAR:hover_row_style}"); },
	function (){ $(this).children("td").removeClass("{VAR:hover_row_style}");}
);
</script>
<!-- END SUB: hover_script -->
