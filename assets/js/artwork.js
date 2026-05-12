(function () {
	'use strict';

	// Reference room: the scene depicts a 240 cm-tall wall.
	// The wall occupies 72 % of the container height.
	var WALL_REF_HEIGHT_CM = 240;
	var WALL_HEIGHT_RATIO  = 0.72;
	var PERSON_HEIGHT_CM   = 175;
	var SCALE_BAR_CM       = 100;

	function scaleRoom( el ) {
		var containerH = el.offsetHeight;
		var containerW = el.offsetWidth;
		var wallH      = containerH * WALL_HEIGHT_RATIO;
		var scale      = wallH / WALL_REF_HEIGHT_CM; // px per cm

		var artWcm = parseFloat( el.dataset.artworkW ) || 0;
		var artHcm = parseFloat( el.dataset.artworkH ) || 0;

		var displayW, displayH;

		if ( artWcm > 0 && artHcm > 0 ) {
			displayW = artWcm * scale;
			displayH = artHcm * scale;
		} else {
			// No real-world dimensions: derive ratio from the loaded image.
			var img   = el.querySelector( '.artwork-room__img' );
			var ratio = ( img && img.naturalWidth && img.naturalHeight )
				? img.naturalWidth / img.naturalHeight
				: 0.75;
			displayH = wallH * 0.55;
			displayW = displayH * ratio;
		}

		// Clamp so the artwork never overflows the room.
		var maxW   = containerW * 0.72;
		var maxH   = wallH * 0.78;
		var factor = Math.min(
			displayW > maxW ? maxW / displayW : 1,
			displayH > maxH ? maxH / displayH : 1
		);
		displayW *= factor;
		displayH *= factor;

		// Ensure a visible minimum.
		var minW = containerW * 0.12;
		if ( displayW < minW ) {
			var grow = minW / displayW;
			displayW *= grow;
			displayH *= grow;
		}

		var personH   = Math.min( PERSON_HEIGHT_CM * scale, wallH * 0.92 );
		var scaleBarW = SCALE_BAR_CM * scale;

		el.style.setProperty( '--artwork-display-w', displayW.toFixed( 1 ) + 'px' );
		el.style.setProperty( '--artwork-display-h', displayH.toFixed( 1 ) + 'px' );
		el.style.setProperty( '--person-display-h',  personH.toFixed( 1 )  + 'px' );
		el.style.setProperty( '--scale-bar-w',        scaleBarW.toFixed( 1 ) + 'px' );

		el.classList.add( 'is-scaled' );
	}

	function initRooms() {
		var rooms = document.querySelectorAll( '.artwork-room' );
		if ( ! rooms.length ) { return; }

		rooms.forEach( function ( room ) {
			var img = room.querySelector( '.artwork-room__img' );
			if ( img && ! img.complete ) {
				img.addEventListener( 'load', function () { scaleRoom( room ); } );
			}
			scaleRoom( room );
		} );

		var resizeTimer;
		window.addEventListener( 'resize', function () {
			clearTimeout( resizeTimer );
			resizeTimer = setTimeout( function () {
				rooms.forEach( scaleRoom );
			}, 120 );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initRooms );
	} else {
		initRooms();
	}
}());
