<?php
/**
 *  Video Map helper for FITV Media Burn
 *
 *  @author Michael Cannon <mc@aihr.us>
 */
// add_shortcode( 'video_map', 'fitv_video_map' );
function fitv_video_map() {
	$url = get_stylesheet_directory_uri() . '/functions/user/custom/fitv/vm/';

	$head = <<<EOD
    <style type="text/css">
      #map-canvas { height: 100% }
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCHXh8xg9Cf1ZJfaK1HIN6Q6Whblp4Ex38&sensor=false&libraries=visualization,places&language=us"> <!-- &language=us -->
    </script>
    <script type="text/javascript" src="{$url}MediaBurnMapURLsData.js"></script>
    <script type="text/javascript" src="{$url}markerclusterer.js"></script>
    
    <script type="text/javascript">
    
    /****google map defined***/
    var geocoder;
    var map;
    var LatLngDatas = new Array;
    var markersDatas = new Array;
    var mapDataArrayLength = MapObjectArray.length;

    /****google map defined***/
  	
    function initialize() {
    	
      geocoder = new google.maps.Geocoder();
      var mapOptions = {
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }
      map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

      /**init the map center by geo**/
 	  map.setCenter(new google.maps.LatLng(42.02932699999999, -87.74344300000001));

 	  /***the zoom changed event,this is very import!!!
      google.maps.event.addListener(map, 'zoom_changed', function() {
          var zoomLevel = map.getZoom();
          //alert(zoomLevel);
          if(zoomLevel == 14){
              //alert('begin create map');
				createMarkerOnMap();
          }  
          if( zoomLevel < 14) {
              //clear the map marker
        		    for(var i=0; i < markersDatas.length; i++){
            		    var marker = markersDatas[i];
        		    	 if (null != marker) {
            		    	 marker.setMap(null);
        		    	 }
        		    }
          }
      }); ***/
 	  
    /************************************************/
    
  var input = /** @type {HTMLInputElement} */(document.getElementById('searchLocation'));
  var autocomplete = new google.maps.places.Autocomplete(input);

  autocomplete.bindTo('bounds', map);
    
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
       // infowindow.close();
        marker.setVisible(false);
        input.className = '';
        var place = autocomplete.getPlace();
        if (!place.geometry) {
          // Inform the user that the place was not found and return.
          input.className = 'notfound';
          return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
        } else {
          map.setCenter(place.geometry.location);
          map.setZoom(17);  // Why 17? Because it looks good.
        }
        marker.setIcon(/** @type {google.maps.Icon} */({
          url: place.icon,
          size: new google.maps.Size(71, 71),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(17, 34),
          scaledSize: new google.maps.Size(35, 35)
        }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        var address = '';
        if (place.address_components) {
          address = [
            (place.address_components[0] && place.address_components[0].short_name || ''),
            (place.address_components[1] && place.address_components[1].short_name || ''),
            (place.address_components[2] && place.address_components[2].short_name || '')
          ].join(' ');
        }

        // infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        // infowindow.open(map, marker);
      });
    /*******init the heatmap Data from mapdataArray   google.maps.visualization.WeightedLocation/google.maps.LatLng   **/
     var markers = [];
    for(var i=0;i<mapDataArrayLength;i++) {
    	  var mapObejct = MapObjectArray[i];
    	  var currentLatlng = mapObejct.googleLatLng.split(",");
    	  var latLng ;
    	  if( !isNaN(parseFloat(currentLatlng[0]))  && !isNaN(parseFloat(currentLatlng[1]))) {
    	  		// LatLngDatas.push(new google.maps.LatLng(parseFloat(currentLatlng[0]),parseFloat(currentLatlng[1])));
    	  		 latLng = new google.maps.LatLng(parseFloat(currentLatlng[0]),parseFloat(currentLatlng[1]))
    	  	     var marker = new google.maps.Marker({
    	  	          position: latLng,
    	  	          title:mapObejct.title,
    	  	          icon:'{$url}images/marker_1.png'
    	  	     });
    	  	   markers.push(marker);
    	  	 	
    	  	 createMarkerOnMapByClusterer(marker,mapObejct,latLng);
    	   } else {
				alert('The LatLng is incorrect  : ' + currentLatlng);
    	   }
        }  
    
    var markerCluster = new MarkerClusterer(map, markers);
    
    /*****
    var pointArray = new google.maps.MVCArray(LatLngDatas);
    /***heat map data  demo
    var heatmap = new google.maps.visualization.HeatmapLayer({
  	  data: pointArray,
  	dissipating:true,
  	  gradient:[
  	          'rgba(0, 255, 255, 0)',
  	        'rgba(0, 255, 255, 1)',
  	        'rgba(0, 191, 255, 1)',
  	        'rgba(0, 127, 255, 1)',
  	        'rgba(0, 63, 255, 1)',
  	        'rgba(0, 0, 255, 1)',
  	        'rgba(0, 0, 223, 1)',
  	        'rgba(0, 0, 191, 1)',
  	        'rgba(0, 0, 159, 1)',
  	        'rgba(0, 0, 127, 1)',
  	        'rgba(63, 0, 91, 1)',
  	        'rgba(127, 0, 63, 1)',
  	        'rgba(191, 0, 31, 1)',
  	        'rgba(255, 0, 0, 1)'
  	            
  	          ]
  	});  
  	heatmap.setMap(map);**/
    
    // Sets a listener on a radio button to change the filter type on Places
    // Autocomplete.
    function setupClickListener(id, types) {
      var radioButton = document.getElementById(id);
      google.maps.event.addDomListener(radioButton, 'click', function() {
        autocomplete.setTypes(types);
      });
    }

    setupClickListener('changetype-all', []);
    setupClickListener('changetype-establishment', ['establishment']);
    setupClickListener('changetype-geocode', ['geocode']);
    
    /***************************************************/
   
    }

    /***************************************
    	GOOGLE LIMIT   every ms/10 requets data 
    	Within a few seconds, if the error was received because your application sent too many requests per second.
    	Some time in the next 24 hours, if the error was received because your application sent too many requests per day.
    	 The time of day at which the daily quota for a service is reset varies between customers and for each API, and can change over time.
    ********************************/
   
    function createMarkerOnMap() {
     	for(var i=0;i<mapDataArrayLength;i++) {
    	  var mapObejct = MapObjectArray[i];
    	  buildMapLocationByObject(mapObejct);
        }  
    }

    /***
    *******************/
    function createMarkerOnMapByClusterer(marker,mapObejct,latLng){
    	google.maps.event.addListener(marker, 'click', function() {
            // infowindow.open(map,marker);
            /** w create a info window ***/
       	     var infowindow = new google.maps.InfoWindow({
       	  	    content:  '<div id="content"'+
       	  	    '<div id="siteNotice">'+
       	  	    '</div>'+
       	  	    '<h3 id="firstHeading" class="firstHeading">'+mapObejct.location+'</h1>'+
       	  	    '<div id="bodyContent">'+
       	  	    ''+mapObejct.date+''+
       	  	    '<p><a href="'+mapObejct.videoURL+'" target="_blank">'+
       	  	    ''+mapObejct.title+'<br><img src="'+mapObejct.imageURL+'"/></a></p>'+
       	  	    '<p id="streetViewDiv" style="width:300px;height:200px;">StreetView</p>'+
       	  	    '</div>'+
       	  	    '</div>'
       	  });
             /**info window open***/
       	  infowindow.open(map,marker);
       	  /**info window domready event and then build the street view***/
       	  google.maps.event.addListener(infowindow, 'domready', function() {
           	  /***google street view***/
	              var panoramaOptions = {
	            		    position: latLng,
	            		    pov: {
	            		      heading: 34,
	            		      pitch: 10
	            		    }
	            		  };
	         	  var panorama = new  google.maps.StreetViewPanorama(document.getElementById("streetViewDiv"),panoramaOptions);
	         	  map.setStreetView(panorama);
       	  });
         });
    }
	
    google.maps.event.addDomListener(window, 'load', initialize);
    
    /**
    	build map locaion 
    **/

    function  buildMapLocationByObject(mapObejct) {
    	 var currentLatlng = mapObejct.googleLatLng.split(",");
		     if( !isNaN(parseFloat(currentLatlng[0]))  && !isNaN(parseFloat(currentLatlng[1]))) {
		    	 var currentLat = new google.maps.LatLng(parseFloat(currentLatlng[0]),parseFloat(currentLatlng[1]));
		   	 	 // map.setCenter(currentLat);
		    	     var marker = new google.maps.Marker({
		              	 map: map,
		             	 position: currentLat,
		             	 title:mapObejct.title,
		             	 icon:'{$url}images/marker_1.png'
		         	  }); 
		    	     markersDatas.push(marker);
		              //bind the click event
		    	     createMarkerOnMapByClusterer(marker,mapObejct,currentLat); 	
				} else {
					 alert('The LatLng can not load.....' + currentLat);
			 }
   		}
    
    /***get the location  reload the map  autocomplete***/
    function googleMapSearch(){
     	var targetLocation = document.getElementById("searchLocation").value;
    	//循环我的数组，看里面是否有 如果没有就请求谷歌
    	var isExist = false ;
	    	 for(var i=0;i< mapDataArrayLength;i++) {
	    	  var mapObejct = MapObjectArray[i];
	    	     if(targetLocation.indexOf(mapObejct.location) >= 0) {
	    	    	 isExist = true;
	    	    	 var currentLatlng = mapObejct.googleLatLng.split(",");
		    		     if( !isNaN(parseFloat(currentLatlng[0]))  && !isNaN(parseFloat(currentLatlng[1]))) {
		    		    	 var currentLat = new google.maps.LatLng(parseFloat(currentLatlng[0]),parseFloat(currentLatlng[1]));
		    		    	 map.setCenter(currentLat);//设置当前位置
		    		    	 var marker = new google.maps.Marker({
		    	              	 map: map,
		    	             	 position:currentLat,
		    	             	 title:mapObejct.title
		    	             	
		    	         	  });
		    		    	 createMarkerOnMapByClusterer(marker,mapObejct,currentLat);
		    		    	 break;
		    		     }
	    	       }
	    	   }  
			//不存在的时候
    	   	if(!isExist) {
    	   		geocoder.geocode( { 'address': targetLocation}, function(results, status) {
    		        if (status == google.maps.GeocoderStatus.OK) {
    		         	  map.setCenter(results[0].geometry.location);
    		              var marker = new google.maps.Marker({
    		              	 map: map,
    		             	 position: results[0].geometry.location,
    		             	 title:targetLocation,
    		         	  });
    		        } else {
    		          	alert('Geocode was not successful for the following reason: ' + status);
    		        }
    	      });
    	   	}
        } 
    
    
  </script>
EOD;

	echo $head;

	$body = <<<EOD
      	<div align="center">
      		<input type="text" name="searchLocation" id="searchLocation" size="100">
      		<input type="button" value="Google" onclick="googleMapSearch()" >
      	</div>
        <div id="map-canvas"></div>
        <div id="lat"></div>
EOD;

	echo $body;
}

?>
