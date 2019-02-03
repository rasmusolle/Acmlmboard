function toggleAll(cls, enable) {
	var elems = document.getElementsByClassName(cls);
	for (var i = 0; i < elems.length; i++)
		elems[i].disabled = !enable;
}

function ajaxGet(url, callback) {
	var x = new XMLHttpRequest();
	x.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            callback(this.responseText);
        }
    };
	x.open('GET', url);
	x.send(null);
}