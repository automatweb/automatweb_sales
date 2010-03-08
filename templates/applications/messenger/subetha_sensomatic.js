function commchannel() {
	var c = false;
	if (typeof XMLHttpRequest != 'undefined') {
		c = new XMLHttpRequest();
	} else {
		try {
			c = new ActiveXObject("Msxml2.XMLHTTP");
		} 
		catch (e) {
			try {
				c = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
				c = false;
			}
		}
	}
	return c;

}

function sens_o_matic(url, callback, req_type) {
	if (!req_type) {
		req_type = "GET";
	}
	this.request_type = req_type;
	if (url.indexOf('?') < 0) {
		url = url + '?';
	}
	this.url = url;
	this.callback = callback;
}

sens_o_matic.prototype.hail = function(params) {
	// if something is not finished yet, then abort it
	if (channel && channel.readyState != 0) {
		channel.abort();
	}
	
	var channel = new commchannel();
	var callback = this.callback;
	if (!channel) {
		return;
	}
	
	var post_data = null;
	var req_params = "";
	var sep = "";

	for (var name in params) {
		req_params = req_params + '&' + name + '=' + encodeURIComponent(params[name]);
	}

	var url = this.url;
	if (this.request_type == "GET") {
		url = url + req_params;
	}

	channel.open(this.request_type, url, true);
	if (this.request_type == "POST") {
		post_data = req_params;
		channel.setRequestHeader("Method", "POST " + url + " HTTP/1.1");
		channel.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	}

	channel.onreadystatechange = function() {
		if (channel.readyState != 4) {
			return;
		}
		if (callback) {
			var evalStr = callback + "(channel.responseText)"; 
			eval(evalStr);
		} else {
			alert(channel.responseText);
		}
	}
	channel.send(post_data);
	delete channel;
}

