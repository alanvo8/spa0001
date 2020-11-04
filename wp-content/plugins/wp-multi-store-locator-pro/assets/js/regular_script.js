var store_locator_map;
var markers = [];
var road_direction = 0;

//Bind events to the page
jQuery(document).ready(function (jQuery) {
    jQuery(document).on('change','.regular-filter ul.map_radius input[type="radio"]',function(){
       var me = jQuery(this);
       var radius = me.val();
       if (me.is(":checked")) {
        jQuery('#store_locator_search_form').find('input[name="store_locatore_search_radius"]').val(radius);
        jQuery('#store_locatore_search_btn').trigger('click');
       }
    });
    jQuery(document).on('click','.regular-filter ul.map_categories input[type="checkbox"]',function(){
       var me = jQuery(this);
         var term = me.val();
         for (i = 0; i < markers.length; i++)
         {   
             if(markers[i].category==term){
                 if (me.is(":checked")) {
                         markers[i].setVisible(true);
                         jQuery(document).find(".rel_cat_"+term).fadeIn();
                     } else {
                         markers[i].setVisible(false);    
                        jQuery(document).find(".rel_cat_"+term).fadeOut();
                     }
             }
         }
    });
    jQuery(document).on('click','#regularMap .regular-filter span.dashicons',function(){
       var me = jQuery(this);
      // jQuery('#regularMap .regular-filter ul.map_dropdown_filter').fadeOut();
       me.closest('.regular-filter').find('ul.map_dropdown_filter').fadeToggle();
    });
    /*
    jQuery(document).on('click','#regular_map_header_content .category-filter i.fa',function(){
       var me = jQuery(this);
       alert('hha');
         me.closest('#regular_map_header_content').find('ul.map_categories').fadeToggle();
    });*/
    jQuery(document).on('click','.close-start-direction',function(){
       var me = jQuery(this);
         jQuery(document).find('#directionsPanel').html('');
    });
    jQuery(document).on('click','.current-location-zoom #zoom-out',function(){
       var me = jQuery(this);
         zoomLevel = store_locator_map.getZoom();
         store_locator_map.setZoom(zoomLevel-1);
    });
    jQuery(document).on('click','.current-location-zoom #zoom-in',function(){
       var me = jQuery(this);
        zoomLevel = store_locator_map.getZoom();
        store_locator_map.setZoom(zoomLevel+1);
    });
     jQuery(document).on('click','.regular_map_filters .direction-filter',function(){
       var master = jQuery(this);
         jQuery(document).find('.map-listings .direction-toggle-addresses').slideToggle();
    });
     function convertMileKm(numberunit,unit){
       var temp= numberunit.split(" ");
       var num = temp[0].replace (/,/g, "");
       //var unit = temp[1];
       var converted='';
       if(unit=='Km')
       {
           num=num * 1.60934;
           converted=num.toFixed(2)+' Km';
       }
       else if(unit=='mile')
       {
            num=num / 1.60934;
            converted=num.toFixed(2)+' mile';
           
       }
        return converted;
    }
    jQuery(document).on('click','.mile-km-btns-switcher input',function(){
       var master = jQuery(this);
        if(jQuery(this).closest('.btn-km-mile').hasClass('active')){
            
        }
        else{
            jQuery(document).find('.mile-km-btns-switcher .btn-km-mile').each(function (i){
                jQuery(this).removeClass('active');
            });
           master.closest('.btn-km-mile').addClass('active');
           var unit=master.val();
           jQuery(document).find('.col-right.right-sidebar .store-locator-item .wpsl-distance').each(function (i){
               var me = jQuery(this);
              var details= me.html();
             // console.log(details);
               var converted=convertMileKm(details,unit);
               me.html(converted);
           });
        }
    });
    
    //bind select2 effect
    jQuery('#store_locator_category, #store_locator_tag').select2();

    //bind autocomplete
    var input = document.getElementById('store_locatore_search_input');
    var autocomplete = new google.maps.places.Autocomplete(input);
    
    
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        /*
       var place = autocomplete.getPlace();
          if (!place.geometry) {
            window.alert(placeholder_location + place.name );
            return;
          }
          if (place.geometry) {
            
            document.getElementById('store_locatore_search_lat').value = place.geometry.location.lat();
            document.getElementById('store_locatore_search_lng').value = place.geometry.location.lng();
           
          } 
          */
          
         // wpmsl_update_map(input);
        var current_location = jQuery('#store_locatore_search_input').val();
        var radius = jQuery('#store_locatore_search_radius').val();
         var map_id = jQuery(document).find("#wp_multi_store_locator_map_id").val();
        wpmsl_update_map(current_location,radius,map_id);
       //jQuery('#store_locatore_search_btn').trigger('click');
    });
    
    // get my location handling
    jQuery('#store_locatore_get_btn').on('click', function () {
        

        // Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                var geocoder = new google.maps.Geocoder;
                geocoder.geocode({
                    'location': pos
                }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            jQuery('#store_locatore_search_input').val(results[0].formatted_address);
                            jQuery('#store_locatore_search_lat').val(pos.lat);
                            jQuery('#store_locatore_search_lng').val(pos.lng);

                        } else {
                            jQuery('#store_locatore_search_input').attr('placeholder', "Couldn't be detected ...");
                        }
                    } else {
                        jQuery('#store_locatore_search_input').attr('placeholder', "Couldn't be detected ...");
                    }
                    button.val(old_text);
                    button.attr('disabled', false);
                });
            }, function () {
                jQuery('#store_locatore_search_input').attr('placeholder', "Couldn't be detected ...");
                // handleLocationError(true, infoWindow, map.getCenter());
                button.val(old_text);
                button.attr('disabled', false);
            });
        } else {
            jQuery('#store_locatore_search_input').attr('placeholder', "Couldn't be detected ...");
            // Browser doesn't support Geolocation
            // handleLocationError(false, infoWindow, map.getCenter());
            button.val(old_text);
            button.attr('disabled', false);
        }
    });

    // ajax search

    jQuery('#store_locatore_search_btn').on('click', function (e) {
        e.stopPropagation();
       // console.log('click');
      // var markers = [];
        if (jQuery('#store_locatore_search_input').val().length > 0) {
            // do something
            jQuery(".load-img").css("display", "block");
            jQuery(".overlay-store").css("display", "block");
        }

         var address = jQuery(document).find("#store_locatore_search_input").val(); 
        var addresslat = jQuery(document).find("#store_locatore_search_lat").val();
        var addresslng = jQuery(document).find("#store_locatore_search_lng").val();
        var map_id = jQuery(document).find("#wp_multi_store_locator_map_id").val();
            
        
        if(!address){
            jQuery('#store_locatore_search_input').css('box-shadow','0px 0px 4px red');
            return ;
        }

       
        jQuery('#store_locatore_search_results').html('');
        jQuery('#map-container').show();

        jQuery( ".store-locator-item-container" ).remove();
        jQuery.ajax({
            url: ajax_url,
            data: jQuery('#store_locator_search_form').serialize() + '&action=make_search_request_maps_regular' + '&lat=' + addresslat + '&lng=' + addresslng+'&map_id='+map_id,
            type: 'post',
            success: function (html) {
                
                jQuery('#store_locatore_search_results').html(html);

                var hh = jQuery( ".store-locator-item-container" ).detach();

                //hh.appendTo( ".col-left" );
                hh.appendTo( ".map-listings" );

                jQuery(".load-img").css("display", "none");
                jQuery(".overlay-store").css("display", "none");
               

                var body_width = jQuery('body').width();
                if(store_locator_map_options.mapscrollsearch){
                    jQuery("html, body").animate({scrollTop: jQuery('.col-left').offset().top - 120}, 2000);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus); alert("Error: " + errorThrown);
                jQuery(".load-img").css("display", "none");
                jQuery(".overlay-store").css("display", "none");
            }
        });

    });
    // set zoom to specific lat long
    jQuery(document).on('click','.wpsl-zoom-to', function (e) {
        e.stopPropagation();
        //console.log('zoom');
        var latlng = jQuery(this).data('direction').split(',');
        //console.log(latlng);
        var latt = parseFloat(latlng[0]);
        var lngg = parseFloat(latlng[1]);
        store_locator_map.setCenter({lat:latt, lng:lngg});
          store_locator_map.setZoom(15);
        //alert(jQuery(this).data('id'));
    });
     // ajax category filter ends
	jQuery('#store_locator_search_form').submit(function () {
		 return false;
	});
	
    // array to cache forms
    var storeGfForms = [];
    // before form shown
    jQuery(document).on('show.bs.modal', '.store_locator_gf_form', function() {
        var formContent = jQuery(this).find('.modal-body').html();
        if(storeGfForms[jQuery(this).attr('id')] === undefined ) {
            // caching form
            storeGfForms[jQuery(this).attr('id')] = formContent;
        }
        else {
            // display cached version of form
            jQuery('.modal-body', jQuery(this)).html(storeGfForms[jQuery(this).attr('id')]);
        }
    });
    // after form hidden
    jQuery(document).on('hidden.bs.modal', '.store_locator_gf_form', function() {
        // clear form to display caching version next time
        jQuery('.modal-body', jQuery(this)).html('');
    });

    jQuery(document).on('submit', '.gform_wrapper form', function(e) {
        var $this = jQuery(this);
        var modal = jQuery(this).closest('.modal');
        jQuery(window).one('scroll', function(){
            modal.animate({
                scrollTop: $this.offset().top
            });
        });
    });

    jQuery( "#search-location" ).keypress(function(e) {
        var search_location = jQuery(this).val();
        var search_radius = jQuery('#wpmsl-search-radius').val();
        if(e.keyCode == 13) {
            jQuery('#wpmsl-search-radius').focus();
            //wpmsl_update_map(search_location,search_radius);
        }
    });
});


function wpmsl_update_map(location_postition,search_radius,map_id) {

  
    location_postition = location_postition.replace('+','');
    location_postition = location_postition.replace('+','');
    location_postition = location_postition.replace('+','');
    jQuery('#search-location').val(location_postition);
    jQuery('img.load-img').show();
    jQuery('.col-right.right-sidebar').css('opacity','0.5');

    var geocoder = new google.maps.Geocoder();
    var address = location_postition;
    var latitude = ''; var longitude = '';
    if( jQuery(document).find("#wp_multi_store_locator_map_id").val()){
    var map_id = jQuery(document).find("#wp_multi_store_locator_map_id").val();
    }    
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            latitude = results[0].geometry.location.lat();
            longitude = results[0].geometry.location.lng();
            document.getElementById('store_locatore_search_lat').value = latitude;
            document.getElementById('store_locatore_search_lng').value = longitude;
            jQuery.ajax({
                url: ajax_url,
                data: 'store_locatore_search_input='+location_postition+'&store_locatore_search_lat='+latitude+'&store_locatore_search_lng='+longitude+'&store_locatore_search_radius='+search_radius+'&store_locator_category=' + '&action=make_search_request_maps_regular' + '&lat=' + latitude + '&lng=' + longitude+'&map_id='+map_id,
                type: 'post',
                success: function (html) {
                    //console.log(html);
                    jQuery('#store_locatore_search_results').html(html);
                    var hh = jQuery( ".store-locator-item-container" ).detach();
                   // hh.appendTo( ".col-left" );
                    hh.appendTo( ".map-listings" );
                    jQuery('.store-locator-item-container:nth-child(2)').remove();
                    jQuery(".load-img").css("display", "none");
                    jQuery(".overlay-store").css("display", "none");
                    jQuery('.col-right.right-sidebar').css('opacity','1');
                    var body_width = jQuery('body').width();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log('ERROR');
                    console.log(errorThrown);
                    console.log(textStatus);
                    var default_location = getCookie('default_location');

                    jQuery(".load-img").css("display", "none");
                    jQuery(".overlay-store").css("display", "none");
                }
            });

        }
    });
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function computeTotalDistance(result) {
    var total = 0;
    var myroute = result.routes[0];
    for (var i = 0; i < myroute.legs.length; i++) {
      total += myroute.legs[i].distance.value;
    }
    total = total / 1000;
    document.getElementById('total').innerHTML = total + ' km';
}
function calculateAndDisplayRouteNew(directionsService, directionsDisplay,map) {
    var start = document.getElementById('routeStart').value;
    var end = document.getElementById('routeEnd').value;
    var markerArray=[];
    var stepDisplay = new google.maps.InfoWindow;
    if(start!=='' && end!==''){
        road_direction=1;
        var map = new google.maps.Map(document.getElementById('store_locatore_search_map'), {
          zoom: 7,
        });
        var directionsDisplay = new google.maps.DirectionsRenderer({
            map: map,
            panel: document.getElementById('directionsPanel')
        });
        var directionsService = new google.maps.DirectionsService;
        directionsDisplay.addListener('directions_changed', function() {
          computeTotalDistance(directionsDisplay.getDirections());
        });
        jQuery(".load-img").show();
        jQuery(".overlay-store").show();
        directionsService.route({
          origin: start,
          destination: end,
          travelMode: 'WALKING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
            showSteps(response, markerArray, stepDisplay, map);
            directionsDisplay.setOptions({
                //suppressMarkers: true,
            });
            var $target = jQuery('.store-locator-item-container');
            var scrTop = Math.abs($target.offset().top-80);
            $target.animate({
              scrollTop: scrTop
            }, 400);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
        jQuery(".load-img").hide();
        jQuery(".overlay-store").hide();
    }else{
        jQuery(".load-img").hide();
        jQuery(".overlay-store").hide();
        alert('One of the direction field is empty');
    }
}
jQuery(document).on('click','.direction-toggle-addresses #get-directions',function(e){
    calculateAndDisplayRouteNew();
    jQuery("html, body").animate({scrollTop: jQuery('#store-locator-id').offset().top -120 }, 300);
});
jQuery(document).on('click','.store-direction',function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    var item = jQuery(this);
    var address=item.closest('.store-locator-item').find('.wpsl-address').html()+' '+item.closest('.store-locator-item').find('.wpsl-city').html();
    jQuery(document).find('#routeEnd').val(address);
    jQuery(document).find('#routeStart').val(jQuery(document).find('#store_locatore_direction_Addr').val());
    jQuery(".load-img").show();
    jQuery(".overlay-store").show();
    jQuery(document).find('#get-directions').trigger('click');
});
function store_locator_map_initialize(locations) {
    //console.log('init');
    //console.log(locations);
    var styledMapType = new google.maps.StyledMapType(
        JSON.parse(map_style(store_locator_map_options.map_style)),
        {name: 'Styled Map'});

    if ( store_locator_map_options.custom_style ) {
        var styledMapType = new google.maps.StyledMapType(
            JSON.parse(store_locator_map_options.custom_style),
            {name: 'Styled Map'});
    }

    if(store_locator_map_options.scroll == true){
        var scrolbyzoom = true;
    } else {
        var scrolbyzoom = false;
    }

    store_locator_map = new google.maps.Map(document.getElementById('store_locatore_search_map'), {
        zoom: 5,
        center: new google.maps.LatLng(41.923, 12.513),
        mapTypeControl: Number( store_locator_map_options.mapTypeControl ),
        // scrollwheel: Number( store_locator_map_options.scroll ),
        scrollwheel: scrolbyzoom,
        streetViewControl: Number( store_locator_map_options.streetViewControl ),
        gestureHandling: 'cooperative',
        mapTypeId: google.maps.MapTypeId[ store_locator_map_options.type.toUpperCase() ]
    });



    store_locator_map.mapTypes.set('styled_map', styledMapType);
    store_locator_map.setMapTypeId('styled_map');

    var bounds = new google.maps.LatLngBounds();
    markers= [];
     var infowindow = new google.maps.InfoWindow();

    // user location display
    var marker_one = store_locator_map_options.marker1;
    var marker_two = store_locator_map_options.marker2;

    if(store_locator_map_options.marker1_custom != '') {
        marker_one = store_locator_map_options.marker1_custom;
    }

    if(store_locator_map_options.marker2_custom != '') {
        marker_two = store_locator_map_options.marker2_custom;
    }
    //console.log(locations);
    var marker1 = new google.maps.Marker({
        position: new google.maps.LatLng(locations['center']['lat'], locations['center']['lng']),
        map: store_locator_map,
        animation: google.maps.Animation.DROP,
        icon: marker_one,
       // type : 'cat-17'
    });
    //extend the bounds to include each marker's position
    bounds.extend(marker1.position);
    google.maps.event.addListener(marker1, 'click', function () {
        infowindow.setContent(document.getElementById("store_locatore_search_input").value);
        infowindow.open(store_locator_map, marker1);
    });
    //var markers = [];

    //console.log(locations['locations']);

    for (i = 0; i < locations['locations'].length; i++) {
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations['locations'][i]['lat'], locations['locations'][i]['lng']),
            map: store_locator_map,
            animation: google.maps.Animation.DROP,
            icon: ((locations['locations'][i]['catimage'])) ? locations['locations'][i]['catimage'] : marker_two
            // icon: icon_set,
        });
        if(locations['terms'] && locations['terms'][i])
        marker.category = locations['terms'][i];
        markers.push(marker);
        //console.log(marker);
        //extend the bounds to include each marker's position
        bounds.extend(marker.position);

        jQuery('.data-direction-infowindow-'+locations['locations'][i]['lat'].replace('.', '')+'-'+locations['locations'][i]['lng'].replace('.', '')).val(locations['locations'][i]['infowindow']);

        google.maps.event.addListener(marker, 'click', (function (marker, i) {

            var latitude = parseFloat( locations['locations'][i]['lat'] );
            var longitude = parseFloat( locations['locations'][i]['lng'] );

                    return function () {
                    infowindow.setContent(locations['locations'][i]['infowindow']);
                    infowindow.open(store_locator_map, marker);
                }
        })(marker, i));
       // google.maps.event.addListener(marker, 'click', (function(marker, i) { 
       //        alert("I am marker " + i); 
       // })(marker, i));
    }

    // Set Zoom to 10 when no markers are exist
    if(markers.length < 1) {
        setTimeout(function() {
            store_locator_map.setZoom(10);
        },50);
    }

    store_locator_map.fitBounds(bounds);
    if(store_locator_map_options.mapcluster){
    var markerCluster = new MarkerClusterer(store_locator_map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
    }
    var marker_bouncing = 0;
    jQuery('.store-locator-item').on('click',function() {
        var item=jQuery(this);
        jQuery(".store-locator-item").removeClass("active-item");
        item.addClass('active-item');
        if(road_direction == 1) {
            //location.reload();
          // /*
            var list_id = jQuery(this).attr('id');
            var address = jQuery('#store_locatore_search_input').val();
            var radius = jQuery('#store_locatore_search_radius').val();
            var map_id = jQuery(document).find("#wp_multi_store_locator_map_id").val();
            wpmsl_update_map(address,radius,map_id);
            setTimeout(function() {
               jQuery('#'+list_id).click();
            },2000);
            road_direction = 0;
           // */
        }

        var marker_boune = jQuery(this).index()-3;

        markers[marker_boune].setAnimation(google.maps.Animation.BOUNCE);
        marker_bouncing = marker_boune + 1;

        setTimeout(function() {
            markers[marker_boune].setAnimation(null);
        },2400);
    
        var address=item.find('.wpsl-address').html();
        jQuery(document).find('#routeEnd').val(address);
        google.maps.event.trigger(markers[marker_boune], 'click');
        store_locator_map.panTo(markers[marker_boune].getPosition());
        //jQuery('.wpsl-choose-location-btn').css('background-color','#9e9e9e');
        //jQuery(this).find('.wpsl-choose-location-btn').css('background-color','#FF5A1A');
       // jQuery(".store-search-fields").slideUp();
        jQuery("html, body").animate({scrollTop: jQuery('#store-locator-id').offset().top -120 }, 300);
    });
    
    
    setTimeout(function() {
    var startDirection= document.getElementById('routeStart');
    var autocompletestartDirection = new google.maps.places.Autocomplete(startDirection);
    google.maps.event.addListener(autocompletestartDirection, 'place_changed', function () {
        
    });
    var endDirection= document.getElementById('routeEnd');
    var autocompletendDirection = new google.maps.places.Autocomplete(endDirection);
    google.maps.event.addListener(autocompletendDirection, 'place_changed', function () {
        
    });
   
    },1000);
}

// Bouning Marker on Hover
jQuery(document).ready(function() {
    jQuery(".search-options-btn").click(function(){
        jQuery(".store-search-fields").slideToggle();
    });
});

function map_style(map_style) {
    if(map_style == 1) {
        return '[]';
    } else if(map_style == 2) {
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#f5f5f5"      }    ]  },  {    "elementType": "labels.icon",    "stylers": [      {        "visibility": "off"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#616161"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#f5f5f5"      }    ]  },  {    "featureType": "administrative.land_parcel",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#bdbdbd"      }    ]  },  {    "featureType": "poi",    "elementType": "geometry",    "stylers": [      {        "color": "#eeeeee"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry",    "stylers": [      {        "color": "#e5e5e5"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9e9e9e"      }    ]  },  {    "featureType": "road",    "elementType": "geometry",    "stylers": [      {        "color": "#ffffff"      }    ]  },  {    "featureType": "road.arterial",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#dadada"      }    ]  },  {    "featureType": "road.highway",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#616161"      }    ]  },  {    "featureType": "road.local",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9e9e9e"      }    ]  },  {    "featureType": "transit.line",    "elementType": "geometry",    "stylers": [      {        "color": "#e5e5e5"      }    ]  },  {    "featureType": "transit.station",    "elementType": "geometry",    "stylers": [      {        "color": "#eeeeee"      }    ]  },  {    "featureType": "water",    "elementType": "geometry",    "stylers": [      {        "color": "#c9c9c9"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9e9e9e"      }    ]  }]';
    } else if (map_style == 3){
        return '[        {            "elementType": "geometry",            "stylers": [            {                "color": "#ebe3cd"            }        ]        },        {            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#523735"            }        ]        },        {            "elementType": "labels.text.stroke",            "stylers": [            {                "color": "#f5f1e6"            }        ]        },        {            "featureType": "administrative",            "elementType": "geometry.stroke",            "stylers": [            {                "color": "#c9b2a6"            }        ]        },        {            "featureType": "administrative.land_parcel",            "elementType": "geometry.stroke",            "stylers": [            {                "color": "#dcd2be"            }        ]        },        {            "featureType": "administrative.land_parcel",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#ae9e90"            }        ]        },        {            "featureType": "landscape.natural",            "elementType": "geometry",            "stylers": [            {                "color": "#dfd2ae"            }        ]        },        {            "featureType": "poi",            "elementType": "geometry",            "stylers": [            {                "color": "#dfd2ae"            }        ]        },        {            "featureType": "poi",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#93817c"            }        ]        },        {            "featureType": "poi.park",            "elementType": "geometry.fill",            "stylers": [            {                "color": "#a5b076"            }        ]        },        {            "featureType": "poi.park",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#447530"            }        ]        },        {            "featureType": "road",            "elementType": "geometry",            "stylers": [            {                "color": "#f5f1e6"            }        ]        },        {            "featureType": "road.arterial",            "elementType": "geometry",            "stylers": [            {                "color": "#fdfcf8"            }        ]        },        {            "featureType": "road.highway",            "elementType": "geometry",            "stylers": [            {                "color": "#f8c967"            }        ]        },        {            "featureType": "road.highway",            "elementType": "geometry.stroke",            "stylers": [            {                "color": "#e9bc62"            }        ]        },        {            "featureType": "road.highway.controlled_access",            "elementType": "geometry",            "stylers": [            {                "color": "#e98d58"            }        ]        },        {            "featureType": "road.highway.controlled_access",            "elementType": "geometry.stroke",            "stylers": [            {                "color": "#db8555"            }        ]        },        {            "featureType": "road.local",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#806b63"            }        ]        },        {            "featureType": "transit.line",            "elementType": "geometry",            "stylers": [            {                "color": "#dfd2ae"            }        ]        },        {            "featureType": "transit.line",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#8f7d77"            }        ]        },        {            "featureType": "transit.line",            "elementType": "labels.text.stroke",            "stylers": [            {                "color": "#ebe3cd"            }        ]        },        {            "featureType": "transit.station",            "elementType": "geometry",            "stylers": [            {                "color": "#dfd2ae"            }        ]        },        {            "featureType": "water",            "elementType": "geometry.fill",            "stylers": [            {                "color": "#b9d3c2"            }        ]        },        {            "featureType": "water",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#92998d"            }        ]        }    ]';
    } else if( map_style == 4 ){
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#212121"      }    ]  },  {    "elementType": "labels.icon",    "stylers": [      {        "visibility": "off"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#212121"      }    ]  },  {    "featureType": "administrative",    "elementType": "geometry",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "administrative.country",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9e9e9e"      }    ]  },  {    "featureType": "administrative.land_parcel",    "stylers": [      {        "visibility": "off"      }    ]  },  {    "featureType": "administrative.locality",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#bdbdbd"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry",    "stylers": [      {        "color": "#181818"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#616161"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1b1b1b"      }    ]  },  {    "featureType": "road",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#2c2c2c"      }    ]  },  {    "featureType": "road",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#8a8a8a"      }    ]  },  {    "featureType": "road.arterial",    "elementType": "geometry",    "stylers": [      {        "color": "#373737"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#3c3c3c"      }    ]  },  {    "featureType": "road.highway.controlled_access",    "elementType": "geometry",    "stylers": [      {        "color": "#4e4e4e"      }    ]  },  {    "featureType": "road.local",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#616161"      }    ]  },  {    "featureType": "transit",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "water",    "elementType": "geometry",    "stylers": [      {        "color": "#000000"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#3d3d3d"      }    ]  }]';
    } else if( map_style == 5 ){
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#242f3e"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#746855"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#242f3e"      }    ]  },  {    "featureType": "administrative.locality",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#d59563"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#d59563"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry",    "stylers": [      {        "color": "#263c3f"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#6b9a76"      }    ]  },  {    "featureType": "road",    "elementType": "geometry",    "stylers": [      {        "color": "#38414e"      }    ]  },  {    "featureType": "road",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#212a37"      }    ]  },  {    "featureType": "road",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9ca5b3"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#746855"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#1f2835"      }    ]  },  {    "featureType": "road.highway",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#f3d19c"      }    ]  },  {    "featureType": "transit",    "elementType": "geometry",    "stylers": [      {        "color": "#2f3948"      }    ]  },  {    "featureType": "transit.station",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#d59563"      }    ]  },  {    "featureType": "water",    "elementType": "geometry",    "stylers": [      {        "color": "#17263c"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#515c6d"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#17263c"      }    ]  }]';
    } else if( map_style == 6 ){
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#1d2c4d"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#8ec3b9"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1a3646"      }    ]  },  {    "featureType": "administrative.country",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#4b6878"      }    ]  },  {    "featureType": "administrative.land_parcel",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#64779e"      }    ]  },  {    "featureType": "administrative.province",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#4b6878"      }    ]  },  {    "featureType": "landscape.man_made",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#334e87"      }    ]  },  {    "featureType": "landscape.natural",    "elementType": "geometry",    "stylers": [      {        "color": "#023e58"      }    ]  },  {    "featureType": "poi",    "elementType": "geometry",    "stylers": [      {        "color": "#283d6a"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#6f9ba5"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1d2c4d"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#023e58"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#3C7680"      }    ]  },  {    "featureType": "road",    "elementType": "geometry",    "stylers": [      {        "color": "#304a7d"      }    ]  },  {    "featureType": "road",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#98a5be"      }    ]  },  {    "featureType": "road",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1d2c4d"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#2c6675"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#255763"      }    ]  },  {    "featureType": "road.highway",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#b0d5ce"      }    ]  },  {    "featureType": "road.highway",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#023e58"      }    ]  },  {    "featureType": "transit",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#98a5be"      }    ]  },  {    "featureType": "transit",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1d2c4d"      }    ]  },  {    "featureType": "transit.line",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#283d6a"      }    ]  },  {    "featureType": "transit.station",    "elementType": "geometry",    "stylers": [      {        "color": "#3a4762"      }    ]  },  {    "featureType": "water",    "elementType": "geometry",    "stylers": [      {        "color": "#0e1626"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#4e6d70"      }    ]  }]';
    } else if( map_style == 7 ){
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#ebe3cd"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#523735"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#f5f1e6"      }    ]  },  {    "featureType": "administrative",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#c9b2a6"      }    ]  },  {    "featureType": "administrative.land_parcel",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#dcd2be"      }    ]  },  {    "featureType": "administrative.land_parcel",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#ae9e90"      }    ]  },  {    "featureType": "landscape.natural",    "elementType": "geometry",    "stylers": [      {        "color": "#fefdd3"      }    ]  },  {    "featureType": "poi",    "elementType": "geometry",    "stylers": [      {        "color": "#dfd2ae"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#93817c"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#a5b076"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#447530"      }    ]  },  {    "featureType": "road",    "elementType": "geometry",    "stylers": [      {        "color": "#f5f1e6"      }    ]  },  {    "featureType": "road.arterial",    "elementType": "geometry",    "stylers": [      {        "color": "#fdfcf8"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#f8c967"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#e9bc62"      }    ]  },  {    "featureType": "road.highway.controlled_access",    "elementType": "geometry",    "stylers": [      {        "color": "#e98d58"      }    ]  },  {    "featureType": "road.highway.controlled_access",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#db8555"      }    ]  },  {    "featureType": "road.local",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#806b63"      }    ]  },  {    "featureType": "transit.line",    "elementType": "geometry",    "stylers": [      {        "color": "#dfd2ae"      }    ]  },  {    "featureType": "transit.line",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#8f7d77"      }    ]  },  {    "featureType": "transit.line",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#ebe3cd"      }    ]  },  {    "featureType": "transit.station",    "elementType": "geometry",    "stylers": [      {        "color": "#dfd2ae"      }    ]  },  {    "featureType": "water",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#03526b"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#92998d"      }    ]  }]';
    } else if(map_style == '' || map_style > 7 || map_style < 1) {
        return '[]';
    }
}