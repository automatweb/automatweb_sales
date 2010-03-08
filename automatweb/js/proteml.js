function aw_proteml(n,d,f)
{
	var e = (n + "@" + d);
	if (!f) { f = e; }
		document.write( '<a '+'hr'+'ef="mailto:' + e + '">' + f + '</'+'a>');
}
