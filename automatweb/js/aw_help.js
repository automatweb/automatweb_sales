jQuery.aw_prop_help =
{
	disable : function()
	{
		return this.each(
			function()
			{
				$(this).css("padding", "0");
				$(this).css("background", "none");
			}
		);
	},
	enable : function()
	{
		return this.each(
			function()
			{
				$(this).css("padding", "0 20px 0 6px");
				$(this).css("display", "inline");
				$(this).css("background", "url(/automatweb/images/aw06/icon_prop_help.gif) no-repeat right");
				
				content = $(this).next().text();
				
				html = '<div class="tooltip '+this.id+'"><div class="tooltip_header"><!-- --></div>'+
						'<div class="tooltip_content">'+
						'<p>'+content+'</p>'+
						'</div>'+
						'<div class="tooltip_footer"></div></div>';

				$(this).next().replaceWith(html);
				
				$(function() {
					$('.help').parent().tooltip({
						bodyHandler: function() { 
							c = $('span', this);
						    return $("."+c.attr("id")).html()
						},
						left : 5,
						top : 10
					});
				});
			}
		);			
	}
};

jQuery.fn.extend (
	{
		/**
		 * Enable tabs in textareas
		 * 
		 * @name EnableTabs
		 * @description Enable tabs in textareas
		 *
		 * @type jQuery
		 * @cat Plugins/Interface
		 * @author Stefan Petre
		 */
		EnablePropHelp : jQuery.aw_prop_help.enable,
		/**
		 * Disable tabs in textareas
		 * 
		 * @name DisableTabs
		 * @description Disable tabs in textareas
		 *
		 * @type jQuery
		 * @cat Plugins/Interface
		 * @author Stefan Petre
		 */
		DisablePropHelp : jQuery.aw_prop_help.disable
	}
);



