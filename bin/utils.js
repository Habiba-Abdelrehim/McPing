function httpPost(URL, data, callback)
{
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST", URL, true); // false for synchronous request
	xmlHttp.onreadystatechange = function() {
		
		if (this.readyState == 4 && this.status == 200) {
			if (callback) callback(xmlHttp.responseText);
		}
		if (this.readyState == 4 && this.status == 400) {
			window.location.href = "../bin/error400.html";
		}
		if (this.readyState == 4 && this.status == 401) {
			window.location.href = "../bin/error401.html";
		}
		if (this.readyState == 4 && this.status == 500) {
			window.location.href = "../bin/error500.html";
		}
	};
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send(data);

}
