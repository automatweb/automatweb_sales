<!-- 
bill vars : "id" , "number" , "currency" , "date" , "deadline" , "sum"
delivery_note vars : "id" , "number" , "currency" , "date" , "delivery" , "sum"

-->


{VAR:name}<br>


<table width="630" border="0" cellspacing="0" cellpadding="0">
<!-- SUB: ROW -->
  <tr>
    <td  align="left" valign="bottom"><a href={VAR:url}>{VAR:id}</a></td>
    <td  align="right" valign="middle">{VAR:number}</td>
    <td > {VAR:date}</td>
    <td > {VAR:sum}</td>
    <td > {VAR:currency}</td>
  </tr>
<!-- END SUB: ROW -->
</table>











