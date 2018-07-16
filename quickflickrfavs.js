 var getFlickrApiKey = function() {
 	jQuery.ajax({
 		type: 'post',
 		url: custom_ajax_vars.ajaxurl,

 		data: {
 			action: 'flickr_api_key',
 			nonce_field: custom_ajax_vars.auth
 		},
 		success: function(data) {
 			console.log("flickr_api_key: " + data);
 			// use var data
			if (data){
 			window.FlickrApiKey = data;
 			getLicences( data );}else(jQuery( '#quickflickrfavs' ).prepend('<strong>No Flickr api key. If you are the blog owner please add one in Settings->QuickFlickrFavs</strong>'));
 		}
 	});

 }

 var getLicences = function( apikey ) {
 	var flickrLicenseUrl = "https://api.flickr.com/services/rest/?method=flickr.photos.licenses.getInfo&api_key=" + apikey + "&format=json&nojsoncallback=1";

 	jQuery.ajax({
 		type: 'get',
 		url: flickrLicenseUrl,
 		success: function(data) {
			console.log(data);
 			window.licences = data.licenses.license
 			showFlickr( apikey );
 		}
 	});
 }

 var showFlickr = function(apikey) {

 	user = jQuery( '#quickflickrfavs' ).attr( 'data-user' );
 	count = jQuery( '#quickflickrfavs' ).attr( 'data-count' );

 	var flickrSearchUrl = "https://api.flickr.com/services/rest/?method=flickr.favorites.getList&api_key=" + apikey + "&user_id=" + user + "&per_page=" + count + "&format=json&extras=o_dims,url_n,license,owner_name&nojsoncallback=1";
 	//console.log( flickrSearchUrl );
 	jQuery.ajax({
 		type: 'get',
 		url: flickrSearchUrl,
 		success: function(data) {
 			 console.log("search:"+ data );
 			photosObj = data.photos.photo;
 			console.log( photosObj );
 			for ( i = photosObj.length - 1; i >= 0; i-- ) {
 				//for ( var i = 0; i < photosObj.length; i++ ) {
 				var title = photosObj[i].title;
 				var heightN = photosObj[i].height_n;
 				var widthN = photosObj[i].width_n;
 				var imgurl = photosObj[i].url_n;
 				var owner = photosObj[i].ownername;
 				var license = flickrlicense(photosObj[i].license);
 				var pageurl = "https://www.flickr.com/photos/" + photosObj[i].owner + "/" + photosObj[i].id
 					//var htmldiv="<img src='"+imgurl+"'/>";
 					//jQuery( '#quickflickrfavs' ).append( htmldiv );

 				jQuery("<div>", {
 					'class': "flickrthumb",
 					css: {
 						"background": "url( '" + imgurl + "' )",
 						"background-size": "cover",
 						"float": "left"
 					}
 				}).prependTo( jQuery( '#quickflickrfavs' ) ).append( "<p><strong>" + title + "</strong> <a href='" + pageurl + "'>photo</a> by " + owner + "<br>" + license + "</p>" );

 			}
 		}
 	});


 }

 var flickrlicense = function(id) {
 	for (var i = 0; i < window.licences.length; i++) {
 		if (window.licences[i].id == id) {
 			return "<a href='" + window.licences[i].url + "'>" + window.licences[i].name + "</a>";
 			break;
 		}
 	}

 }


 jQuery( document ).ready( function() {

 	// Stuff to do as soon as the DOM is ready;
 	var flickerApiKey = getFlickrApiKey();



 }); //end ready
