/**
 * @author Cube
 */
var map;

var markers = {};

var geocoder;

var info_window = '';

function draw_map(mapOptions){
	geocoder = new google.maps.Geocoder();
	var DefaultOptions = {
		zoom: 14,
		panControl: false,
		zoomControl: false,
		scaleControl: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var Options = json_merge(DefaultOptions, mapOptions.set); 
	map = new google.maps.Map(document.getElementById(mapOptions.id), Options); 
}
/**
 * @param markerOptions
 * {
 *   id:id,
 *   set:{
 * 	   map:map,
 *     title:title,
 *     content
 *     position:{lt:lt, lg:lg},
 *   }
 * }
 * @param displayOptions
 * {
 *   setCenter:true,
 *   display:true
 * }
 */
function draw_marker(markerOptions, displayOptions){
	var displayOptionsDefault = {setCenter:true, display:true};
	displayOptions = json_merge(displayOptionsDefault, displayOptions);
	var res = {};
	var result;
	var marker_options = {
		map: map, 
	}
	if(markerOptions.set.iconUrl != null){
		marker_options.icon = markerOptions.set.iconUrl;
	}
	if(markerOptions.set.shadowUrl != null){
		marker_options.shadow = markerOptions.set.shadowUrl;
	}
	if(markerOptions.set.title != ''){
		marker_options.title = markerOptions.set.title
	}
	if(markerOptions.set.position != null){
		res.lt_lg = new google.maps.LatLng(markerOptions.set.position.lt, markerOptions.set.position.lg);
		_draw_marker();
	}else{
	    geocoder.geocode( { 'address': markerOptions.set.address }, function(results, status) {
			if(status == google.maps.GeocoderStatus.OK){
				res.lt_lg = results[0].geometry.location;
				_draw_marker();
			}else{
				res.marker = false;
			}
			marker_options.position = res.lt_lg;
		});
	}
	markers[markerOptions.id] = res;
	//子方法
	function _draw_marker(){
		marker_options.position = res.lt_lg;
		if(displayOptions.setCenter !== false){
			map.setCenter(res.lt_lg);
		}
		if(markerOptions.set.pano){
			/* 第一次加载加载pano，优化速度 */
			listener = google.maps.event.addListener(map, 'tilesloaded', function(){
				draw_pano_layer(true);
				google.maps.event.removeListener(listener);
			});
		}
		if(displayOptions.display === false){
			return;
		}
		res.marker = new google.maps.Marker(marker_options);
		if(markerOptions.set.content != ''){
			res.infowindow = new google.maps.InfoWindow({
				content: markerOptions.set.content,
			});
			google.maps.event.addListener(res.marker, 'click', function() {
//				if(info_window != ''){
//					info_window.close(map, res.marker);
//				}
//				res.infowindow.open(map, res.marker);
//				info_window = res.infowindow;
				openInfoWindow(markerOptions.id);
			});
		}
	}
}


function clean_marker(id){
	markers[id].marker.setMap(null);
}


//pano
var pano_marker = {};

var listener;

function draw_pano(auto)
{
	$.each(pano_marker, function(k, v){
		v.setMap(map);
	});
	if(auto){
		listener = google.maps.event.addListener(map, 'tilesloaded', function(){
			draw_pano_layer();
		});
	}
}

function addmarker_none(json){
	$.each(json.photos, function(k, v){
		if(!pano_marker[v.photo_id]){
			v.zindex = k;
			pano_marker[v.photo_id] = setMarkers(v, false);
		}
	});
	$('.img_box').fancybox({
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
}

function addmarker(json){
	$.each(json.photos, function(k, v){
		if(!pano_marker[v.photo_id]){
			v.zindex = k;
			pano_marker[v.photo_id] = setMarkers(v, true);
		}
	});
	$('.img_box').fancybox({
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
}

function remove_pano_marker()
{
	$.each(pano_marker, function(k, v){
		v.setMap(null);
	});
	google.maps.event.removeListener(listener);
}
function draw_pano_layer(is_hide){
//	google.maps.event.addListener(map, 'tilesloaded', function(){
		var bounds = map.getBounds();
		var southWest = bounds.getSouthWest();
		var northEast = bounds.getNorthEast();
		var img_options = {
			order: "popularity",
			set: "public",
			from: "0",
			to: "20",
			minx: southWest.lng(),
			miny: southWest.lat(),
			maxx: northEast.lng(),
			maxy: northEast.lat(),
			size: "medium",
			mapfilter: true,
			callback: 'addmarker',
		};
		if(is_hide){
			img_options.callback = 'addmarker_none';
		}
		var url = "http://www.panoramio.com/map/get_panoramas.php?";
		$.each(img_options, function(k, v){
			url += k+'='+v+'&';
		});
		
		//var script = $('<script><\/script>');
		//script
		//	.attr('src', url);
		//$('head').append(script);
		
		var script = document.createElement('script');
		script.setAttribute('src', url);
		script.setAttribute('id', 'jsonScript');
		script.setAttribute('type', 'text/javascript');
		document.documentElement.firstChild.appendChild(script);
//	});
}

function setMarkers(panoItem, is_display) {
	// Add markers to the map
	var m_icon_url = panoImg(panoItem.photo_id, 'mini_square');
	var m_icon = new google.maps.MarkerImage(
		//img url
		m_icon_url,
		// This marker is 20 pixels wide by 32 pixels tall.
		null, //  new google.maps.Size(20, 20),
		// The origin for this image is 0,0.
		null, //new google.maps.Point(0,0),
		// The anchor for this image is the base of the flagpole at 0,32.
		null, //new google.maps.Point(0, 32),
		// 缩放
		new google.maps.Size(20, 20)
	);
	//var shadow = new google.maps.MarkerImage(
		//'images/beachflag_shadow.png',
		// The shadow image is larger in the horizontal dimension
		// while the position and offset are the same as for the main image.
		//new google.maps.Size(37, 32),
		//new google.maps.Point(0,0),
		//new google.maps.Point(0, 32)
	//);
	// Shapes define the clickable region of the icon.
	// The type defines an HTML <area> element 'poly' which
	// traces out a polygon as a series of X,Y points. The final
	// coordinate closes the poly by connecting to the first
	// coordinate.
//	var shape = {
//		coord: [1, 1, 1, 20, 18, 20, 18 , 1],
//		type: 'poly'
//	};
	var img = '<a href="#key'+ panoItem.photo_id +'" id="img'+ panoItem.photo_id +'" class="img_box"></a>';
	var content = 
   		"<div id='key"+ panoItem.photo_id +"'>" +
	    	"<p><a href='http://www.panoramio.com/' target='_blank'>" +
	    	"<img src='http://www.panoramio.com/img/logo-small.gif' border='0' width='119px' height='25px' alt='Panoramio logo' /><\/a></p>" +
	    	"<a id='photo_infowin' target='_blank' href='" + panoItem.photo_url + "'>" +
	    	"<img border='0' width='" + panoItem.width + "' height='" + panoItem.height + "' src='" + panoItem.photo_file_url + "'/><\/a>" + //src='" + panoImg(panoItem.photo_id, 'original') + "'
	    	"<div style='overflow: hidden; width: 240px;'>" +
	    	"<p><a target='_blank' class='photo_title' href='" + panoItem.photo_url +
	    	"'><strong>" + panoItem.photo_title + "<\/strong><\/a></p>" +
	    	"<p>Posted by <a target='_blank' href='" + panoItem.owner_url + "'>" +
	    	panoItem.owner_name + "<\/a></p><\/div>" +
		"<\/div>";
	var m_icon_cache = '<img src="' + m_icon_url + '" />';
	if($('#map_pic_area').length == 0){
		var img_box = '<div id="map_pic_area" style="display:none;"></div>';
		$('body').append(img_box);
	}
	if(!is_display){
		$('#map_pic_area').append(img + content + m_icon_cache);
 	}else{
 		$('#map_pic_area').append(img + content);
 	}
	
    var myLatLng = new google.maps.LatLng(panoItem.latitude, panoItem.longitude);
 	var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
		//shadow: shadow,
        icon: m_icon,
//		shape: shape,
        title: panoItem.photo_title,
        zIndex: panoItem.zindex,
    });
 	if(!is_display){
 		marker.setMap(null);
 	}
 	var infowindow = new google.maps.InfoWindow({
		content: content,
	});
 	google.maps.event.addListener(marker, 'click', function() {
		$('#img' + panoItem.photo_id).click();
	});
 	return marker;
}

function setMarkerCenter(id){
	map.setCenter(markers[id].lt_lg);
	openInfoWindow(id);
}


function openInfoWindow(id){
	if(info_window != ''){
		info_window.close(map, markers[id].marker);
	}
	markers[id].infowindow.open(map, markers[id].marker);
	info_window = markers[id].infowindow;
}


function panoImg(photoId, imgType) {
	return 'http://www.panoramio.com/photos/' + imgType + '/' + photoId + '.jpg';
}

function json_merge(arr1, arr2)
{
	if(arr2 == null){
		return arr1;
	}
	$.each(arr1, function(k, v){
		if(arr2[k] != null){
			arr1[k] = arr2[k];
		}
	});
	$.each(arr2, function(k, v){
		arr1[k] = arr2[k];
	});
	return arr1;
}

function show_arr(arr)
{
	$.each(arr, function(k, v){
		alert(k + '  ' + v);
	});
}
/**
 * google map 图钉icon
 * @param count
 * @returns {String}
 */
function googleMapIcon(count)
{
	return 'http://www.google.com/mapfiles/marker' + String.fromCharCode(65 + count) + '.png';
}

/**
 * 自定义图标icon
 * @param category
 * @returns {String}
 */
function get_map_icon(category)
{
	return '/application/template/default/images/icons/' + category + '.png';
}


function googleMapIconShadow()
{
	return 'http://maps.google.com/mapfiles/shadow50.png';
}

