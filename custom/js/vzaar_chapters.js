function playVzaarVideo() {
	playVzaarVideoAt(0);
}

function playVzaarVideoAt( seconds ) {
	vzPlayer.play2();
	vzPlayer.seekTo( seconds );
	vzPlayer.focus();
}
