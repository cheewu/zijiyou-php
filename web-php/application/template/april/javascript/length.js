/**
 * @author cube
 */
/** *************** 距离测量 ***************** */
// 距离标记数组
var polyline;
var polylinesArray = [];
var lenArray = [];

function HomeControl(controlDiv, name, style) {
  // Set CSS styles for the DIV containing the control
  // Setting padding to 5 px will offset the control
  // from the edge of the map
  controlDiv.style.padding = '5px';

  // Set CSS for the control border

  var controlUI = document.createElement('div');
  this.controlUI = controlUI;
  controlUI.style.backgroundColor = 'white';
  controlUI.style.borderStyle = 'solid';
  controlUI.style.borderWidth = '2px';
  controlUI.style.cursor = 'pointer';
  controlUI.style.textAlign = 'center';
  controlUI.title = '单击以测距';
  controlDiv.appendChild(controlUI);
  // Set CSS for the control interior

  var controlText = document.createElement('div');
  this.Text = controlText;
  controlText.style.fontFamily = 'Arial,sans-serif';
  controlText.style.fontSize = '12px';
  controlText.style.paddingLeft = '4px';
  controlText.style.paddingRight = '4px';
  controlText.innerHTML = name;
  controlUI.appendChild(controlText);
}
function drawOverlay(box) {
  // 路线数组
  var flightPlanCoordinates = [];
  // 将坐标压入路线数组
  if (lenArray) {
    for (i in lenArray) {
      flightPlanCoordinates.push(lenArray[i].getPosition());
    }
  }
  // 路径选项
  var myOptions = {
    path : flightPlanCoordinates,
    map : map,
    strokeColor : "#FF0000",
    strokeOpacity : 1.0,
    strokeWeight : 2
  };
  polyline = new google.maps.Polyline(myOptions);
  // 清除原有折线路径
  if (polylinesArray) {
    for (i in polylinesArray) {
      polylinesArray[i].setMap(null);
    }
    polylinesArray = [];
  }
  polyline.setMap(map);
  // 格式化距离
  var res = polyline.getLength();
  if (res > 1000) {
    res = (res / 1000).toFixed(3) + 'km';
  } else {
    res = res + 'm'
  }
  box.Text.innerHTML = res;
  polylinesArray.push(polyline);
}

google.maps.LatLng.prototype.distanceFrom = function(latlng) {
  var lat = [this.lat(), latlng.lat()]
  var lng = [this.lng(), latlng.lng()] // var R = 6371; // km (change this
                                       // constant to get miles)
  var R = 6378137; // In meters
  var dLat = (lat[1] - lat[0]) * Math.PI / 180;
  var dLng = (lng[1] - lng[0]) * Math.PI / 180;
  var a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
      + Math.cos(lat[0] * Math.PI / 180) * Math.cos(lat[1] * Math.PI / 180)
      * Math.sin(dLng / 2) * Math.sin(dLng / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  var d = R * c;
  return Math.round(d);
}

google.maps.Marker.prototype.distanceFrom = function(marker) {
  return this.getPosition().distanceFrom(marker.getPosition());
}

google.maps.Polyline.prototype.getLength = function() {
  var d = 0;
  var path = this.getPath();
  var latlng;
  for ( var i = 0; i < path.getLength() - 1; i++) {
    latlng = [path.getAt(i), path.getAt(i + 1)];
    d += latlng[0].distanceFrom(latlng[1]);
  }
  return d;
}

// 添加新标记
function add_Marker(location, box) {
  if (lenArray.length == 0) {
    var icon = '/application/template/default/images/icons/dd-start.png';
  } else {
    if (lenArray.length >= 2) {
      marker.setMap(null);
    }
    var icon = '/application/template/default/images/icons/dd-end.png';
  }
  // 标记选项
  var myOptions = {
    position : location,
    draggable : true,
    map : map,
    icon : icon
  };
  marker = new google.maps.Marker(myOptions);
  // 双击
  google.maps.event.addListener(marker, 'click', function() {
    lenArray.push(marker);
    drawOverlay(box);
  });

  // 拖动结束事件
  google.maps.event.addListener(marker, 'dragend', function() {
    lenArray.push(marker);
    drawOverlay(box);
  });

  lenArray.push(marker);
  drawOverlay(box);
}
// 删除所有叠加物
function deleteOverlays(box) {
  if (lenArray) {
    for (i in lenArray) {
      lenArray[i].setMap(null);
    }
    lenArray.length = 0;
  }

  // 清除原有折线路径
  if (polylinesArray) {
    for (i in polylinesArray) {
      polylinesArray[i].setMap(null);
    }
    polylinesArray = [];
  }
  box.Text.innerHTML = "0m";
}
