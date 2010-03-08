// Register the related command.
FCKCommands.RegisterCommand( 'AWRelationManager', new FCKDialogCommand( 'AWRelationmanager', FCKLang.AWRelationmanagerDlgTitle, FCKPlugins.Items['awrelationmgr'].Path + 'temp.html', 800, 460 ) ) ;

// Create the "Plaholder" toolbar button.
var oAWRelationmanagerItem = new FCKToolbarButton( 'AWRelationManager', FCKLang.AWRelationmanagerBtn ) ;
oAWRelationmanagerItem.IconPath = '/automatweb/images/icons/connectionmanager.gif' ;

FCKToolbarItems.RegisterItem( 'AWRelationManager', oAWRelationmanagerItem ) ;

