$.rtrim = function (str, chr) {
	var rgxtrim = (!chr) ? new RegExp('\\s+$') : new RegExp(chr+'+$');
	return str.replace(rgxtrim, '');
};

$.ltrim = function (str, chr) {
	var rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^'+chr+'+');
	return str.replace(rgxtrim, '');
};