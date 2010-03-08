// reset form
jQuery.fn.reset = function()
{
	return this.each(function()
	{
		var type = this.type, tag = this.tagName.toLowerCase();
		if (tag == 'form')
		return jQuery(':input',this).reset();
		if (type == 'text' || type == 'password' || tag == 'textarea')
		{
            this.value = '';
		}
		else if (type == 'checkbox' || type == 'radio')
		{
			this.checked = false;
		}
		else if (tag == 'select')
		{
            //this.selectedIndex = -1;
		}
        });
};
