jQuery(document).ready( function($) {

	$('select#robohash_bot').change( function() {
		img = $(this).siblings('img');

		url = img.attr('src').replace( /%3Fset%3D(set[1-3]|any)%26/g, '%3Fset%3D' +$(this).val() + '%26' );
 		img.attr('src', $('#spinner').val() );
 		img.attr('src', url );

 		input = $(this).siblings('input[name="avatar_default"]');
		url2 = input.val().replace( /\?set=(set[1-3]|any)&/g, '?set=' +$(this).val() + '&' );
		input.val( url2 );
	});

	$('select#robohash_bg').change( function() {
		img = $(this).siblings('img');

		url = img.attr('src').replace( /%26bgset%3D(bg[1-3]|any|)%26/g, '%26bgset%3D' +$(this).val() + '%26' );
 		img.attr('src', $('#spinner').val() );
 		img.attr('src', url );
 		
 		input = $(this).siblings('input[name="avatar_default"]');
		url2 = input.val().replace( /bgset=(bg[1-3]|any|)/g, 'bgset=' +$(this).val() );
		input.val( url2 );
	});

});