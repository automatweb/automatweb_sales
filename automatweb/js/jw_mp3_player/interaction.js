// some variables to save
var currentPosition;
var currentVolume;
var currentItem;

// These functions are caught by the feeder object of the player.
function loadFile(obj) { thisMovie("mpl").loadFile(obj); };


// This is a javascript handler for the player and is always needed.
function thisMovie(movieName) {
    if(navigator.appName.indexOf("Microsoft") != -1) {
		return window[movieName];
	} else {
		return document[movieName];
	}
};