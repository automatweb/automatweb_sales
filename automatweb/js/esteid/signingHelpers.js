
var certListLocal;

function getErrorString(code, message)
{
	return '[' + LC_ErrorCode + ': ' + code + '; ' + LC_Error + ': ' + message + ']';
}

function loadPlugin(){

	document.getElementById('error').innerHTML = '';

	try
	{
		loadSigningPlugin(pluginLanguage);
		var plugin =  new IdCardPluginHandler(pluginLanguage);
	}
	catch (ex)
	{
		if (ex instanceof IdCardException) {
			document.getElementById('error').innerHTML = getErrorString(ex.returnCode, ex.message);
		} else {
			document.getElementById('error').innerHTML = ex.message != undefined ? ex.message : ex;
		}
	}
}

function getCert() {

	document.getElementById('error').innerHTML = '';

	try {
		var selectedCertificate = new IdCardPluginHandler(pluginLanguage).getCertificate();
		document.getElementById('certHex').value = selectedCertificate.cert;
		document.getElementById('certId').value = selectedCertificate.id;
	} catch(ex) {
		if (ex instanceof IdCardException) {
			document.getElementById('error').innerHTML = getErrorString(ex.returnCode, ex.message);
		} else {
			document.getElementById('error').innerHTML = ex.message != undefined ? ex.message : ex;
		}
	}
}

function sign() {

	document.getElementById('error').innerHTML = '';

	try {
		var signature = new IdCardPluginHandler(pluginLanguage).sign(document.getElementById('certId').value, document.getElementById('hashHex').value);
		document.getElementById('signatureHex').value = signature;
	} catch(ex) {
		if (ex instanceof IdCardException) {
			document.getElementById('error').innerHTML = getErrorString(ex.returnCode, ex.message);
		} else {
			document.getElementById('error').innerHTML = ex.message != undefined ? ex.message : ex;
		}
	}
}
