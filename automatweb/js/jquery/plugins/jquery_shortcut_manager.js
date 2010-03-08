(function (jQuery){
	this.version = '(0.1)';
	
	this.get_action = function(arr) {
		$.getScript("/automatweb/orb.aw?class=shortcut&action=get_action&id="+arr.oid+"&parent="+arr.parent);
	};
	jQuery.shortcut_manager = this;
	return jQuery;    
})(jQuery);