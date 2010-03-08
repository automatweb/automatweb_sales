{VAR:menubar}
<style>
	.reptexttitle {
		font-family: Tahoma,Arial,Helvetica,sans-serif;
		font-size: 12px;
		font-weight: bold;
	}
	.reptext {
		font-family: Tahoma,Arial,Helvetica,sans-serif;
		font-size: 11px;
	}
	
	.repform {
		font-family: Tahoma,Arial,Helvetica,sans-serif;
		font-size: 11px;
	};
</style>
<script language="javascript">
var sel_day = 1;
var sel_week = 1;
var sel_mon = 1;
var sel_year = 1;

function init_regions()
{
	check_week_state(99);
	check_mon_state(99)
	toggle_year();
	check_day_state(99);
}

function toggle_day1(state,chact)
{
	document.repeater.dayskip.disabled = state;
	if (chact == true)
	{
		sel_day = 1;
	};
}

function toggle_day2(state,chact)
{
	with(document.repeater)
	{
		for (i = 0; i < elements.length; i++)
		{
			if (elements[i].name.indexOf("wday") != -1)
			{
				elements[i].disabled = state;
			};
		};
	};
	if (chact == true)
	{
		sel_day = 2;
	};
}

function toggle_day3(state,chact)
{
	document.repeater.monpwhen2.disabled = state;
	if (chact == true)
	{
		sel_day = 3;
	};
}


function check_day_state(id)
{
	var i;
	if (id == 0)
	{
		toggle_day1(false,true);
		toggle_day2(true,true);
		toggle_day3(true,true);
	};

	if (id == 1)
	{
		toggle_day1(true,true);
		toggle_day2(false,true);
		toggle_day3(true,true);
	};

	if (id == 2)
	{
		toggle_day1(true,true);
		toggle_day2(true,true);
		toggle_day3(false,true);
	};

	if (id == 99)
	{
		if (document.repeater.region1.checked)
		{
			state = false;
			for (i = 0; i <= 2; i++)
			{
				document.repeater.day[i].disabled = false;
				j = i + 1;
				if (sel_day == j)
				{
					eval("toggle_day" + j + "(false,false)");
				}
				else
				{
					eval("toggle_day" + j + "(true,false)");
				};
			};
		}
		else
		{
			state = true;
			for (i = 0; i <= 3; i++)
			{
				j = i + 1;
				document.repeater.day[i].disabled = true;
				eval("toggle_day" + j + "(true,false)");
			}
		};
	};

}

function toggle_week1(state,chact)
{
	document.repeater.weekskip.disabled = state;
	if (chact == true)
	{
		sel_week = 1;
	};
}

function toggle_week2(state,chact)
{
	with(document.repeater)
	{
		for (i = 0; i < elements.length; i++)
		{
			if (elements[i].name.indexOf("mweek") != -1)
			{
				elements[i].disabled = state;
			};
		};
	};
	if (chact == true)
	{
		sel_week = 2;
	};
}

function check_week_state(id)
{
	var i;
	if (id == 0)
	{
		toggle_week1(false,true);
		toggle_week2(true,true);
	};

	if (id == 1)
	{
		toggle_week1(true,true);
		toggle_week2(false,true);
	};
	
	if (id == 99)
	{
		if (document.repeater.region2.checked)
		{
			state = false;
			for (i = 0; i <= 1; i++)
			{
				document.repeater.week[i].disabled = false;
				j = i + 1;
				if (sel_week == j)
				{
					eval("toggle_week" + j + "(false,false)");
				}
				else
				{
					eval("toggle_week" + j + "(true,false)");
				};
			};
		}
		else
		{
			state = true;
			for (i = 0; i <= 1; i++)
			{
				j = i + 1;
				document.repeater.week[i].disabled = true;
				eval("toggle_week" + j + "(true,false)");
			}
		};
	};
};

function toggle_mon1(state,chact)
{
	document.repeater.monthskip.disabled = state;
	if (chact == true)
	{
		sel_mon = 1;
	};
}

function toggle_mon2(state,chact)
{
	document.repeater.months.disabled = state;
	if (chact == true)
	{
		sel_mon = 2;
	};
}

function check_mon_state(id)
{
	var i;
	if (id == 0)
	{
		toggle_mon1(false,true);
		toggle_mon2(true,true);
	};

	if (id == 1)
	{
		toggle_mon1(true,true);
		toggle_mon2(false,true);
	};
	if (id == 99)
	{
		if (document.repeater.region3.checked)
		{
			state = false;
			for (i = 0; i <= 1; i++)
			{
				document.repeater.month[i].disabled = false;
				j = i + 1;
				if (sel_mon == j)
				{
					eval("toggle_mon" + j + "(false,false)");
				}
				else
				{
					eval("toggle_mon" + j + "(true,false)");
				};
			};
		}
		else
		{
			state = true;
			for (i = 0; i <= 1; i++)
			{
				j = i + 1;
				document.repeater.month[i].disabled = true;
				eval("toggle_mon" + j + "(true,false)");
			}
		};


	};
};

function toggle_year()
{
	if (document.repeater.region4.checked)
	{
		document.repeater.yearskip.disabled = false;
	}
	else
	{
		document.repeater.yearskip.disabled = true;
	};
}
	

</script>
<table border="0" cellspacing="0" cellpadding="1" width="100%">
<form method="POST" name="repeater">
<!-- days -->
<tr>
<td align="center" rowspan="3" width="15" valign="top"><input type="checkbox" onClick="check_day_state(99)" name="region1" value="1" class="repform" {VAR:region1}></td>
<td align="center" width="15"><input type="radio" onClick="check_day_state(0)" name="day" value="1" class="repform" {VAR:day1}></td>
<td class="reptext">
	{VAR:LC_PLANNER_EVERY} <input type="text" name="dayskip" class="repform" value="{VAR:dayskip}" size="2" maxlength="2"> {VAR:LC_PLANNER_AFTER_DAY}
</td>
</tr>
<tr>
<td align="center" width="15"><input type="radio" onClick="check_day_state(1)" name="day" value="2" {VAR:day2}></td>
<td class="reptext">
	{VAR:LC_PLANNER_EV_WEEK_THOSE_DAYS}:
	<input type="checkbox" name="wday[1]" value="1" {VAR:wday1}>E |
	<input type="checkbox" name="wday[2]" value="2" {VAR:wday2>T |
	<input type="checkbox" name="wday[3]" value="3" {VAR:wday3}>K |
	<input type="checkbox" name="wday[4]" value="4" {VAR:wday4>N |
	<input type="checkbox" name="wday[5]" value="5" {VAR:wday5}>R |
	<input type="checkbox" name="wday[6]" value="6" {VAR:wday6}>L |
	<input type="checkbox" name="wday[7]" value="7" {VAR:wday7}>P 
</td>
</tr>
<tr>
<td align="center" width="15"><input type="radio" onClick="check_day_state(2)" name="day" value="3" {VAR:day3}></td>
<td class="reptext">
	{VAR:LC_PLANNER_EV_MONTH_THOSE_DAYS} (nt 9,19,29) <input type="text" size="20" class="repform" name="monpwhen2" value="{VAR:mp2}">
</td>
</tr>
<!-- days end -->
<tr bgcolor="#EEEEEE">
<td colspan="3">
<hr size=1 width="100%">
</td>
</tr>
<!-- weeks -->
<tr bgcolor="#EEEEEE">
<td align="center" rowspan="2" width="15" valign="top"><input type="checkbox" onClick="check_week_state(99)" name="region2" value="1" class="repform" {VAR:region2}></td>

<td align="center" width="15"><input type="radio" onClick="check_week_state(0)" name="week" value="1" class="repform" {VAR:week1}></td>
<td class="reptext">
	{VAR:LC_PLANNER_EVERY} <input type="text" name="weekskip" value="{VAR:weekskip}" size="2" class="repform" maxlength="2"> {VAR:LC_PLANNER_AFTER_WEEK}
</td>
</tr>
<tr bgcolor="#EEEEEE">
<td align="center" width="15"><input type="radio" onClick="check_week_state(1)" name="week" value="2" {VAR:week2}></td>
<td class="reptext">
	{VAR:LC_PLANNER_EV_MONTH_THOSE_WEEKS}:
	<input type="checkbox" name="mweek" value="1" {VAR:monpwhen1}>1 |
	<input type="checkbox" name="mweek" value="2" {VAR:monpwhen2}>2 |
	<input type="checkbox" name="mweek" value="2" {VAR:monpwhen3}>3 |
	<input type="checkbox" name="mweek" value="4" {VAR:monpwhen4}>4 |
	<input type="checkbox" name="mweek" value="5" {VAR:monpwhen5}>5 |
	<input type="checkbox" name="mweek" value="last" {VAR:monpwhen6}>viimasel
</td>
</tr>
<!-- weeks end -->
<tr bgcolor="#EEEEEE">
<td colspan="3">
<hr size=1 width="100%">
</td>
</tr>
<!-- months -->
<tr>
<td align="center" rowspan="2" width="15" valign="top"><input type="checkbox" onClick="check_mon_state(99)" name="region3" value="1" class="repform" {VAR:region3}></td>
<td align="center" width="15"><input type="radio" onClick="check_mon_state(0)" name="month" class="repform" value="1" {VAR:month1}></td>
<td class="reptext">
	{VAR:LC_PLANNER_EVERY}<input type="text" value="{VAR:monthskip}" name="monthskip" size="2" class="repform" maxlength="2"> {VAR:LC_PLANNER_AFTER_MONTH}
</td>
</tr>
<tr bgcolor="#EEEEEE">
<td align="center" width="15"><input type="radio" onClick="check_mon_state(1)" name="month" class="repform" value="2" {VAR:month2}></td>
<td class="reptext">
	{VAR:LC_PLANNER_EV_YEAR_THOSE_MONTHS} <input type="text" size="20" class="repform" name="yearpwhen" value="{VAR:yearpwhen}">
</td>
</tr>
<!-- months end -->
<tr bgcolor="#EEEEEE">
<td colspan="3">
<hr size=1 width="100%">
</td>
</tr>
<!-- years -->
<tr bgcolor="#EEEEEE">
<td align="center" width="15" valign="top"><input type="checkbox" name="region4" value="1" onClick="toggle_year()" class="repform" {VAR:region4}></td>
<td align="center" width="15">&nbsp;</td>
<td class="reptext">
	{VAR:LC_PLANNER_EVERY} <input type="text" value="{VAR:yearskip}" size="2" name="yearskip" class="repform" maxlength="2"> {VAR:LC_PLANNER_AFTER_YEAR}
</td>
</tr>
<!-- end years -->
<tr bgcolor="#EEEEEE">
<td colspan="3">
<hr size=1 width="100%">
</td>
</tr>
<!-- repeat types -->
<tr>
<td colspan="3" valign="top" class="reptext">
<input type="radio" name="rep" value="1" checked>{VAR:LC_PLANNER_REPEAT_UNTIL_SAID} (forever)<br>
<input type="radio" name="rep" value="2">{VAR:LC_PLANNER_RESERVE}<input type="text" class="repform" value="6" name="repeats" size="2"> {VAR:LC_PLANNER_ORDER_TIME}<br>
<input type="radio" name="rep" value="3">{VAR:LC_PLANNER_REPEAT_UNTIL}(dd/mm/yyyy)<input type="text" size="2" class="repform" name="repend[day]">/<input type="text" size="2" class="repform" name="repend[mon]">/<input type="text" size="4" class="repform" name="repend[year]"><br>
</td>
</tr>
<!-- repeat types end -->
<tr bgcolor="#EEEEEE">
<td class="reptext" align="center" colspan="3">
<input type="submit" value="{VAR:LC_PLANNER_SAVE}">
{VAR:reforb}
</td>
</tr>
</form>
</table>

<!-- initialize regions -->
<script language="javascript">
init_regions();
</script>
