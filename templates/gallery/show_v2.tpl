<script language="JavaScript">
<!--
function MM_swapImgRestore() { //v2.0
  if (document.MM_swapImgData != null)
    for (var i=0; i<(document.MM_swapImgData.length-1); i+=2)
      document.MM_swapImgData[i].src = document.MM_swapImgData[i+1];
}

function MM_preloadImages() { //v2.0
  if (document.images) {
    var imgFiles = MM_preloadImages.arguments;
    if (document.preloadArray==null) document.preloadArray = new Array();
    var i = document.preloadArray.length;
    with (document) for (var j=0; j<imgFiles.length; j++) if (imgFiles[j].charAt(0)!="#"){
      preloadArray[i] = new Image;
      preloadArray[i++].src = imgFiles[j];
  } }
}

function MM_swapImage() { //v2.0
  var i,j=0,objStr,obj,swapArray=new Array,oldArray=document.MM_swapImgData;
  for (i=0; i < (MM_swapImage.arguments.length-2); i+=3) {
    objStr = MM_swapImage.arguments[(navigator.appName == 'Netscape')?i:i+1];
    if ((objStr.indexOf('document.layers[')==0 && document.layers==null) ||
        (objStr.indexOf('document.all[')   ==0 && document.all   ==null))
      objStr = 'document'+objStr.substring(objStr.lastIndexOf('.'),objStr.length);
    obj = eval(objStr);
    if (obj != null) {
      swapArray[j++] = obj;
      swapArray[j++] = (oldArray==null || oldArray[j-1]!=obj)?obj.src:oldArray[j];
      obj.src = MM_swapImage.arguments[i+2];
  } }
  document.MM_swapImgData = swapArray; //used for restore
}

function MM_openBrWindow(width,height,file)
{
	var features = "toolbar=no,location=0,directories=0,status=0, "+
	"menubar=0,scrollbars=0,left=2,top=107,resizable=1,width=100,height=100";
	if (navigator.family == 'nn4')
	{
		popUpWin = window.open(file,"remote",features);
		popUpWin.focus()
		//popUpWin.moveTo(2,107)
		popUpWin.resizeTo(width,height);
	}
	else
	{ 
		popUpWin = window.open(file,"remote",features);
		popUpWin.focus()
		//popUpWin.moveTo(2,107)
		popUpWin.resizeTo(width+8,height+28);
	}

}



//-->
</script>

<table width="100%" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td BGCOLOR="#FCF3DC" colspan="{VAR:num_cols}">
			<table width="100%" border="0" cellpadding="2" cellspacing="0">
				<tr>
					<td BGCOLOR="#FCF3DC" class="textpealkiri">{VAR:name}</td>
					<td align="right" class="textmiddle">
						<!-- SUB: PAGESEL_BACK -->
						<a href="{VAR:link}"><</a>
						<!-- END SUB: PAGESEL_BACK -->
					
						<!-- SUB: SEL_PAGE -->
						 <b>{VAR:page_num}</b> 
						<!-- END SUB: SEL_PAGE -->
	
						<!-- SUB: PAGE -->
						<a href="{VAR:link}"><b>{VAR:page_num}</b></a> 
						<!-- END SUB: PAGE -->


						<!-- SUB: PAGE_SEP -->
						|
						<!-- END SUB: PAGE_SEP -->

						<!-- SUB: PAGESEL_FWD -->
            			<a href="{VAR:link}">></a>
						<!-- END SUB: PAGESEL_FWD -->
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" colspan="{VAR:num_cols}">
			<table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr>
					<td class="textmiddle">
					<!-- SUB: RATE_OBJ -->
						<a href="{VAR:link}">{VAR:name}</a> 
					<!-- END SUB: RATE_OBJ -->

					<!-- SUB: RATE_OBJ_SEP -->
					|
					<!-- END SUB: RATE_OBJ_SEP -->
					</td>
				</tr>
			</table>
		</td>
	</tr>
	{VAR:layout}
	<tr>
		<td bgcolor="#FFFFFF" colspan="{VAR:num_cols}">
			<table width="100%" border="0" cellpadding="2" cellspacing="0">
				<tr>
					<td class="aa_weekday">&nbsp; </td>
					<td align="right" class="textmiddle">{VAR:PAGESEL_BACK} {VAR:PAGE} {VAR:PAGESEL_FWD}</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
