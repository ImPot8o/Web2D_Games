'use strict';

(function(){

	var CANVAS = document.getElementById('canvas');
	var VIDEO  = document.getElementById('video');
	var opt = {
		audio : false,
		video : {
			frameRate      : 5,
			cursor         : 'always',
			displaySurface : 'monitor',
		},
	};
	var is_capture = false;
	var timer;

	document.getElementById('screencast').addEventListener('click', function(){

		if ( is_capture )
		{
			VIDEO.srcObject.getTracks().forEach(function(t){
				t.stop();
			});
			VIDEO.srcObject = null;

			clearInterval(timer);
			is_capture = ! is_capture;
		}
		else
		{
			navigator.mediaDevices.getDisplayMedia(opt).then(function(stream){
				VIDEO.srcObject = stream;
				VIDEO.play();
			}).catch(function(error){
				console.log('getDisplayMedia', error);
			});

			timer = setInterval(function(){
				CANVAS.width  = VIDEO.videoWidth;
				CANVAS.height = VIDEO.videoHeight;
				CANVAS.getContext('2d').drawImage(VIDEO, 0, 0, CANVAS.width, CANVAS.height);
			}, 1000);
			is_capture = ! is_capture;
		}
	});

})();
