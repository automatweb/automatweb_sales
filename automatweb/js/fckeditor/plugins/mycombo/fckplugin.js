var AWObjectsCommand=function(){};
AWObjectsCommand.Name='mycombo';
AWObjectsCommand.prototype.Execute=function(){}
AWObjectsCommand.GetState=function() { return FCK_TRISTATE_OFF; }
AWObjectsCommand.Execute=function() {
	alert ("F");
}
FCKCommands.RegisterCommand('mycombo', AWObjectsCommand ); 
var oawimageuploadItem = new FCKToolbarButton('mycombo', "mycombo");
oawimageuploadItem.IconPath = FCKPlugins.Items['mycombo'].Path + 'image.gif' ;

FCKToolbarItems.RegisterItem( 'mycombo', oawimageuploadItem ) ;