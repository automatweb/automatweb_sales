$(function() {
	var $tabs = $('ul.tabs2').tabs({
		add: function(ui) {
			var li = $(ui.tab).parents('li:eq(0)')[0];

			$('<em class="close" title="Uus tab"></em>').prependTo(li)
			.bind('click', function() {
			/*
			//console.log( $('li', $tabs).index(li) );
			$tabs.tabs('remove', $('li', $tabs).index(li));
			*/
		});
	}
	});
	
	$('ul.newtab a').bind('click', function() {
	    $('ul.tabs2').tabs('add', '#new', 'Close me');
	});
});