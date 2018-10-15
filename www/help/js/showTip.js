function showTip(tipTxt) {
	var div = document.createElement('div');
	div.innerHTML = '<div class="deploy_ctype_tip"><p>' + tipTxt + '</p></div>';
	var tipNode = div.firstChild;
	$("body").after(tipNode);
	setTimeout(function () {
	    $(tipNode).remove();
	}, 3000);
}