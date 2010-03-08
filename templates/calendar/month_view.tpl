<table id="calendar_month" width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
  <!-- SUB: HEADER -->
    <!-- SUB: HEADER_CELL -->
    <th class="caption">{VAR:dayname}</th>
    <!-- END SUB: HEADER_CELL -->
  <!-- END SUB: HEADER -->
  </tr>
  <!-- SUB: WEEK -->
  <tr>
    <!-- SUB: DAY -->
    <td class="day" valign="top" width="20%">
	<div class="daynr"><a href="{VAR:daylink}">{VAR:daynum}</a></div>
	<div class="event">{VAR:EVENT}</div>
    </td>
    <!-- END SUB: DAY -->
    <!-- SUB: TODAY -->
    <td class="today" valign="top" width="20%">
	<div class="daynr"><a href="{VAR:daylink}">{VAR:daynum}</a></div>
	<div class="event">{VAR:EVENT}</div>
    </td>
    <!-- END SUB: TODAY -->
  </tr>
  <!-- END SUB: WEEK -->
</table>