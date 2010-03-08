<style type="text/css" title="Vcl Gantt Chart Default">
.VclGanttLink
{
	text-decoration: none;
}

.VclGanttColumn a, .VclGanttSubdivision a
{
	z-index: 1000;
	position: relative;
}

td.VclGanttColumn
{
	padding-top: 0px;
	padding-bottom: 0px;
	white-space: nowrap;
}

.VclGanttColumn a:hover, .VclGanttSubdivision a:hover
{
	z-index: 1001;
	background-color: white;
}

td.VclGanttHeader
{
	border-left: 1px solid white;
	padding-bottom: 2px;
	padding-left: 3px;
	white-space: nowrap;
}

a.VclGanttHeader
{
	white-space: nowrap;
}

.VclGanttColumn a span, .VclGanttSubdivision a span
{
	white-space: normal;
	display: none;
	color: black;
	text-decoration: none;
	top: 2em;
	width: 180px;
	padding: 2px;
	border: 1px solid black;
	background-color: white;
}

.VclGanttColumn a:hover span, .VclGanttSubdivision a:hover span
{
	white-space: normal;
	position: absolute;
	display: inline;
}

.VclGanttRowName
{
	background-color: #EEEEEE;
	font-family : Verdana, Arial, Helvetica, Geneva, sans-serif;
	font-size: {VAR:row_text_height}px;
	white-space: nowrap;
	padding: 1px 3px 1px 6px;
	border-bottom: 1px solid;
	border-right: none;
	border-top: none;
}

.VclGanttRowName a.VclGanttLink
{
	font-size: {VAR:row_text_height}px;
	text-decoration: none;
}

.VclGanttColumn img, .VclGanttSubdivision img
{
	position: relative;
	border: none;
	height: {VAR:row_height}px;
	margin: 0px;
}

div#VclGanttChartBox{VAR:chart_id}
{
	width: {VAR:chart_width}px;
	overflow: visible;
}

.VclGanttColumn, .VclGanttSubdivision
{
	border: none;
	border-bottom: 1px solid ;
	border-color: #CCC;
	font-family: Arial, Verdana, sans-serif;
	font-size: 11px;
	color: #000000;
	color: #0018AF;
	text-decoration: none;
}

.VclGanttColumn
{
	border-left: 1px solid #CCCCCC;
}

.VclGanttSubdivision
{
	border-left: 1px solid #EEEEEE;
	white-space: nowrap;
}

.VclGanttTimespan
{
	font-size: 9px;
	font-weight: normal;
	border-left: 1px solid white;
}

img.VclGanttStartBar
{
	white-space: nowrap;
	border-left: 1px solid #DF0D12;
	margin-right: -1px;
}
</style>
