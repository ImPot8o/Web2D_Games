'use strict';

(function(){
	var sizes = [72, 60, 42, 44, 34, 26];
	for ( var px of sizes )
	{
		var div = document.createElement('div');
		div.style.width      = px + 'px';
		div.style.height     = px + 'px';
		div.style.background = '#800';
		div.style.margin     = '1em';
		div.innerHTML        = px + 'px';
		document.body.appendChild(div);
	}

	var sizes = [30, 20, 10];
	for ( var mm of sizes )
	{
		var div = document.createElement('div');
		div.style.width      = mm + 'mm';
		div.style.height     = mm + 'mm';
		div.style.background = '#800';
		div.style.margin     = '1em';
		div.innerHTML        = mm + 'mm';
		document.body.appendChild(div);
	}
})();
