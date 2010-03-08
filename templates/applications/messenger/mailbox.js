// TODO
// message flags - answered. kirjale vastamine peab selle püsti panema
// loading flag is nice to have
// refresh linki on vaja, mis päringu kohe minema saadab.
// adding folders

function mailbox() {
	// need asjad siin saab ära asendada, kui seda skripti käivitatakse läbi AW
	this.server = '{VAR:server}';
	this.msgurl = '{VAR:message}';
	this.mbox = 'INBOX';

	this.first = 1;

	// do not touch anything after this line
	this.tbl = document.getElementById('mailbox');
	this.folders = document.getElementById('folders');
	this.msg_page = 0;
	this.max_rows = 50;

	this.fldarr = {};

	// there will be 2 threads, one for reading messages, the other for folders.
	this.msgtimer = null;
	this.foldertimer = null;

}

// insert a new message to the top
mailbox.prototype.insertmessage = function(msgobj, bottom) {
	var row_id = 'msgrow' + msgobj.id;
	// do not readd the message, if it is already there,
	// this ensures that any checkboxes checked by user will remain intact
	if (document.getElementById(row_id)) {
		return false;
	}

	var lastRow = this.tbl.rows.length;

	// 0 adds to the very top, but our table has a header and we add below that
	var pos = 0;
	if (bottom) {
		pos = lastRow;
	}
	var row = this.tbl.insertRow(pos);

	row.setAttribute('id',row_id);
	row.setAttribute('msgid',msgobj.id);

	var mstyle;

	if (0 == msgobj.seen) {
		mstyle = 'newmsg';
	} else {
		mstyle = 'readmsg';
	}
	
	row.setAttribute('class',mstyle);
		// tundub, et pean siin ka innerHTMLi kasutama, muidu on see värk lihtsalt rõvedalt aeglane

	//row.setAttribute('onClick','alert("kala");');

	// asi mida ma tahaks teha .. oleks kuidagi abstraheerida seda tabeli veergude
	// süsteemi, nii et ma saaks oma suva järgi asju paika panna. Messengeris ma seda
	// kasutama ei hakka, aga mujal võib küll tarvis minna. It's just nice to have.
	// and doesn't add too much overhead

	var cell = row.insertCell(row.cells.length);

	// no ja kuidas kuradi moodi ma vastatud flagi peale panen, s.t. see ju tähendab
	// uue data lisamist celli ja see ei ole üldse lihtne
	var x = document.createElement('input');
	x.setAttribute('type', 'checkbox');
	x.setAttribute('name', 'msgmark[]');
	x.setAttribute('value', msgobj.id);
	x.setAttribute('onClick', "mb.hilight(this,'" + row_id + "')");
	cell.appendChild(x);
	
	var cell = row.insertCell(row.cells.length);
	var textNode = document.createTextNode(msgobj.from);
	cell.appendChild(textNode);

	var cell = row.insertCell(row.cells.length);
	var textNode = document.createTextNode(1 == msgobj.answered ? '»' : '');
	cell.appendChild(textNode);
  
	var cell = row.insertCell(row.cells.length);
	var msglink = document.createElement('a');
	msglink.setAttribute('href', 'javascript:mb.openmsg(\'' + this.mbox + '\',' + msgobj.id + ');');

	var textNode = document.createTextNode(msgobj.subject);
	//cell.appendChild(textNode);
	msglink.appendChild(textNode);
	cell.appendChild(msglink)
	
	var cell = row.insertCell(row.cells.length);
	var textNode = document.createTextNode(1 == msgobj.attach ? '' : '');
	cell.appendChild(textNode);

	/*
	var cell = row.insertCell(row.cells.length);
	var textNode = document.createTextNode(msgobj.size);
	cell.appendChild(textNode);
	*/
	
	var cell = row.insertCell(row.cells.length);
	var textNode = document.createTextNode(msgobj.date);
	cell.appendChild(textNode);
}

mailbox.prototype.getmsgids = function() {
	var rv = "";
	var rows = this.tbl.rows;
	for (var i = 0; i < rows.length; i++) {
		rv = rv + rows[i].getAttribute('msgid') + ',';
	}
	return rv;
}

// delete the last row in the table
mailbox.prototype.deletelast = function() {
  	var lastRow = this.tbl.rows.length;
	if (lastRow > 2) {
		this.tbl.deleteRow(lastRow - 1);
	}
}

mailbox.prototype.removerow = function(arg1) {
	var el_id = 'msgrow' + arg1;
	var elref = document.getElementById(el_id);
	if (elref) {
		this.tbl.deleteRow(elref.rowIndex);
	}
}

mailbox.prototype.updatemessage = function(packet) {
	// msgid - seen - answered
	// kui seen on pea, siis tuleb newmsg klass maha võtta
	// kui seen on maas, siis tuleb newmsg klass peale panna
	// .. answered asja kohta ma veel ei tea, mis ma sellega peale hakkan. Fuck küll
	//alert('flagging message ' + packet['msgid']);
	var el_id = 'msgrow' + packet['msgid'];
	var elref = document.getElementById(el_id);
	if (elref) {
		if (1 == packet['seen']) {
			this.removestyle(el_id,'newmsg');
			this.addstyle(el_id,'readmsg');
		} else {
			this.removestyle(el_id,'readmsg');
			this.addstyle(el_id,'newmsg');
		}
	}
}

mailbox.prototype.query_server = function() {
	var mx = new sens_o_matic(this.server,'mb.process_result','POST');
	res = {};
	res['msgids'] = this.getmsgids();
	res['msgpage'] = this.msg_page;
	res['mailbox'] = this.mbox;
	mx.hail(res);

	// alright .. that query server thingie .. first I want to get a folder list .. and I have that
	// then .. I need to start another tread for accessing the full folder list
	this.msgtimer = window.setTimeout("mb.query_server()", 60000);
}

mailbox.prototype.query_folders = function() {
	var mx = new sens_o_matic(this.server,'mb.process_folders','POST');
	res = {};
	res['action'] = 'folders';
	mx.hail(res);

	this.foldertimer = window.setTimeout("mb.query_folders()", 60000);
}

mailbox.prototype.load_page = function(page) {
	window.clearTimeout(this.msgtimer);
	this.msg_page = page;
	this.query_server();
}

mailbox.prototype.process_folders = function(res) {
	if (this.first) {		
		document.getElementById('loading').style.display = 'none';
	}
	eval('packets = ' + res);
	for(var i = 0; i < packets.length; i++) {
		var packet = packets[i];
		if (packet['type'] == 'mbox') {
			// actually, I'm not so sure about updating the pager
			if (packet['name'] == this.mbox) this.updatepager(packet);
			this.updatefolder(packet);
		}
	}

	var tmp = '';
	for (var i in this.fldarr) {
		tmp = tmp + '<option value="move_' + this.fldarr[i]['name'] + '">' + this.fldarr[i]['caption'] + '</option>';
	}
	document.getElementById('optfolders').innerHTML = tmp;
}

mailbox.prototype.process_result = function(res) {
	
	eval('packets = ' + res);

	if (this.first) {		
		document.getElementById('loading').style.display = 'none';
	}

	for(var i = 0; i < packets.length; i++) {
		var packet = packets[i];
		if (packet['type'] == 'msgdel') {
			this.removerow(packet['msgid']);
		} else if (packet['type'] == 'mbox') {
			if (packet['name'] == this.mbox) this.updatepager(packet);
			// now add the thing to folders table
			this.updatefolder(packet);
		} else if (packet['type'] == 'mflag') {
			this.updatemessage(packet);
		} else if (packet['type'] == 'error') {
			document.getElementById('error').innerHTML = packet['txt'];
		} else {
			// seda appendi ei pea isegi siin vaatama
			this.insertmessage(packet,packet['append']);
		}
	}
}

// see asi siin kirjutab inffi folderite tabelisse
mailbox.prototype.updatefolder  = function(packet) {
	var el = this.folders;
	var row_id = 'folder' + packet['name'];
	var row;

	var existing = document.getElementById(row_id);
	if (existing) {
		row = existing;
	} else {
		row = el.insertRow(el.rows.length);
		row.setAttribute('id',row_id);
		row.setAttribute('folderid',packet['name']);
	}

	this.fldarr[packet['name']] = packet;

	// ja see kala on, et korraga tohib valitud olla ainult 1 folder.
	// nii nagu mul praegu asjad on, ei lähe see kohe teps mitte


	// ja attachi juures on vaja näidata klambrit ja paremat attachi avastamise moodust
	// on ka tarvis

	// ja vastatud flag peab peab kuidagi teistmoodi kuvatama, mitte nii nagu praegu

	// foldereid tuleb hoida eraldi arrays, sest mul on neid vaja ka 
	// move to funktsionaalsuse implementeerimisel. See on siis järgmine uutest featuuridest
	// mida mul tarvis.

	// noh ja muidugi .. uue folderi lisamise võimalus poleks ka üldse mitte paha.

	// ja kirjade valimine vastavalt staatusele. Ja noh, siit omakorda järeldub, et mul
	// on tarvis arrayd kirjade info hoidmiseks. Ja mingi lahe setter oleks lahe, et kui ma
	// message flage muudan, siis see teab juba ise, et kus ja mida muuta .. et mina ei 
	// peaks seda kruvima kusagilt.
	url = 'javascript:mb.openfolder(\'' + packet['name'] + '\');';
	var txt = '<td><a href="' + url + '">' + packet['caption'] + '</a>';
	var classes = '';
	if (packet['name'] == this.mbox) {
		classes = ' openfolder';
	}

	if (packet['unread'] > 0) {
		classes = classes + ' unreadfolder';
		txt = txt + ' (' + packet['unread'] + ')';
	};
	row.setAttribute('class',classes);
	row.innerHTML = txt + '</td>';
}

mailbox.prototype.updatepager = function(packet) {

	if (this.first) {
		document.getElementById('mheader').innerHTML = this.drawnavigator(1);
		document.getElementById('mheader').className = 'navi';
		document.getElementById('mfooter').innerHTML = this.drawnavigator(2);
		document.getElementById('mfooter').className = 'navi';
		this.first = 0;
	}


	el = document.getElementById('pager1');
	el2 = document.getElementById('pager2');
	el.innerHTML = '';
	el2.innerHTML = '';
	//var txt = packet['total'] + ' / ' + packet['unread'];
	// now I don't need to draw all the different pages, because that makes no sense whatsoever
	// I just need to create prev/next links
	var total = packet['total'];
	var msgfrom = this.msg_page * this.max_rows + 1;
	var msgto = this.msg_page * this.max_rows + this.max_rows;

	var lastmsg = msgto;

	if (lastmsg > total) {
		lastmsg = total;
	}

	var txt = '';
	if (this.msg_page > 0) {
		if (this.msg_page > 1) {
			txt = txt + '<a href=\'javascript:mb.load_page(0)\'>« Newest</a> ';
		}
		txt = txt + '<a href=\'javascript:mb.load_page(' + (this.msg_page-1) + ')\'>< Newer</a> ';
	}
	
	txt = txt + '<strong>' + msgfrom + ' - ' + lastmsg + ' of ' + packet['total'] + '</strong>';

	var total_pages = Math.ceil(packet['total'] / this.max_rows - 1);

	if (msgto < total) {
		txt = txt + ' <a href=\'javascript:mb.load_page(' + (this.msg_page+1) + ')\'>Older ></a>';
		if (this.msg_page + 1 < total_pages) {
			txt = txt + ' <a href=\'javascript:mb.load_page(' + (total_pages) + ')\'>Oldest »</a> ';
		}
	}
	el.innerHTML = txt;
	el2.innerHTML = txt;

};


mailbox.prototype.delete_selected = function() {
	var res = {};
	var count = 0;

	var sel = this.getselected();
	for (var i = 0; i < sel.length; i++) {
		res['msgmark['+sel[i]+']'] = 1;
		count++;
	}

	if (count > 0) {
		// okei .. possible actions that I want to do with selected messages:
		// 2. move elsewhere
		res['mailbox'] = this.mbox;
		res['action'] = 'delete';

		var responder = new sens_o_matic(this.server,'mb.query_server','POST');
		responder.hail(res);
	}
}

mailbox.prototype.move_selected = function(target) {
	var res = {};
	var count = 0;

	var sel = this.getselected();
	for (var i = 0; i < sel.length; i++) {
		res['msgmark['+sel[i]+']'] = 1;
		count++;
	}

	if (count > 0) {
		// okei .. possible actions that I want to do with selected messages:
		// 2. move elsewhere
		res['mailbox'] = this.mbox;
		res['action'] = 'move';
		res['moveto'] = target;

		var responder = new sens_o_matic(this.server,'mb.query_server','POST');
		//var responder = new sens_o_matic(this.server,'','POST');
		responder.hail(res);
	}
}

mailbox.prototype.getselected = function() {
	var form = document.getElementById('marker');
	var els = form.elements;
	var res = [];

	// I have no idea why but going through this backwards works better than the other way around
	for (var i = els.length - 1; i > 0; i--) {
		// so how do I access the row id now? bloody hell
		if (els[i].name.indexOf('msgmark') != -1 && els[i].checked) {
			//res[els[i].name] = els[i].value;
			val = els[i].value;
			res[res.length] = val;
			//res['msgmark['+val+']'] = 1;
			//mb.removerow(val);
			//count++;
		}
	}
	return res;
	
}

// this does all the message flagging
mailbox.prototype.flag = function(flag) {
	var form = document.getElementById('marker');
	var els = form.elements;
	var res = {};
	var count = 0;
	var val;

	//for (var i = 0; i < els.length; i++)

	var sel = this.getselected();

	for (var i = 0; i < sel.length; i++) {
		res['msgmark['+sel[i]+']'] = 1;
		count++;
	}

	if (count > 0) {
		res['mailbox'] = this.mbox;
		res['action'] = 'flags';
		res['msgids'] = this.getmsgids();
		res['flag'] = flag;

		var responder = new sens_o_matic(this.server,'mb.process_result','POST');
		responder.hail(res);
	}


}

// problems:

// kirja avanud aken saadab serverisse tagasi paketi, mis märgib selle kirja loetuks
// sama vastamisel

// siis järgmise updatega saan ma põhivaates need kirjad ära muuta.

// then i need a way to mark messages as important .. the message header can probably used for that
// oh yeah .. groovy

// and I want to add folders

mailbox.prototype.hilight = function(el,tgt) {
	if (el.checked) {
		this.addstyle(tgt,'selmsg');
	} else {
		this.removestyle(tgt,'selmsg');
	};
}

mailbox.prototype.addstyle = function(el,style) {
	var el = document.getElementById(el);
	var css = el.className;
        var pos = css.indexOf(style);
        // add this thing only if it is not already there
        if (pos == -1) {
                el.className = css + ' ' + style;
        }
}

mailbox.prototype.removestyle = function(el,style) {
	el = document.getElementById(el);
        el.className = el.className.replace(new RegExp(style),'');
}

mailbox.prototype.openmsg = function(mailbox,msgid) {
	var arg = 'msgid=' + mailbox + '*' + msgid;
	window.open(this.msgurl + arg,'msg','toolbar=0,location=0,menubar=0,width=500,height=500,scrollbars=1');
}

mailbox.prototype.openfolder = function(folder) {
	// vbla see clearTimeout peaks olema mujal?
	window.clearTimeout(this.msgtimer);
	this.removestyle("folder"+this.mbox,"openfolder");
	this.mbox = folder;
	this.msg_page = 0;
	this.query_server();
}

mailbox.prototype.drawnavigator = function(arg1) {
	/*
	accessing this.value from onClick gives an empty value, although this.nodeName works and has the correct result
	*/
	var txt = '<span style="float: left; font-size: 80%; font-family: Arial,sans-serif;">';
	txt = txt + '<select id="ops" onChange="mb.processaction()"><option value=0>Actions...</option><option value="mark_del">Delete</option><option value="mark_read">Mark as read</option><option value="mark_unread">Mark as unread</option><optgroup id="optfolders" label="Move to"></optgroup>';
	txt = txt + '</select>';
	txt = txt + '</span>';
	txt = txt + '<span id="pager' + arg1 + '" style="font-size: 80%; float: right; margin-right: 5px;"></span>';
	return txt;
}

mailbox.prototype.processaction = function() {
	var el = document.getElementById('ops');
	var sel = el.options[el.selectedIndex].value;
	var op = sel.substr(0,5)
	if (op == 'mark_') {
		var act = sel.substr(5);
		if (act == 'del') {
			this.delete_selected();
		} else if (act == 'read') {
			this.flag('read');
		} else if (act == 'unread') {
			this.flag('unread');
		};
	} else if (op == 'move_') {
		// okey .. I can fire off the request to move messages, but what do I do then?
		// I need to update my screen and I have no idea how do do that. Hm, just like delete?
		var target = sel.substr(5);
		this.move_selected(target);
	}
	el.selectedIndex = 0;
}
