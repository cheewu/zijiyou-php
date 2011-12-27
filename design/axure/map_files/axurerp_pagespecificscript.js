
var PageName = 'map';
var PageId = 'pa1f67fa88a38408cb6c4f21cea482c92'
var PageUrl = 'map.html'
document.title = 'map';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

var $OnLoadVariable = '';

var $CSUM;

var hasQuery = false;
var query = window.location.hash.substring(1);
if (query.length > 0) hasQuery = true;
var vars = query.split("&");
for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0].length > 0) eval("$" + pair[0] + " = decodeURIComponent(pair[1]);");
} 

if (hasQuery && $CSUM != 1) {
alert('Prototype Warning: The variable values were too long to pass to this page.\nIf you are using IE, using Firefox will support more data.');
}

function GetQuerystring() {
    return '#OnLoadVariable=' + encodeURIComponent($OnLoadVariable) + '&CSUM=1';
}

function PopulateVariables(value) {
  value = value.replace(/\[\[OnLoadVariable\]\]/g, $OnLoadVariable);
  value = value.replace(/\[\[PageName\]\]/g, PageName);
  return value;
}

function OnLoad(e) {

}

var u10 = document.getElementById('u10');

var u5 = document.getElementById('u5');
gv_vAlignTable['u5'] = 'top';
var u0 = document.getElementById('u0');

var u13 = document.getElementById('u13');

var u3 = document.getElementById('u3');
gv_vAlignTable['u3'] = 'top';
var u9 = document.getElementById('u9');
gv_vAlignTable['u9'] = 'top';
var u6 = document.getElementById('u6');
gv_vAlignTable['u6'] = 'top';
var u1 = document.getElementById('u1');

var u14 = document.getElementById('u14');
gv_vAlignTable['u14'] = 'center';
var u12 = document.getElementById('u12');
gv_vAlignTable['u12'] = 'center';
var u4 = document.getElementById('u4');
gv_vAlignTable['u4'] = 'top';
var u11 = document.getElementById('u11');

var u15 = document.getElementById('u15');

var u7 = document.getElementById('u7');

var u2 = document.getElementById('u2');
gv_vAlignTable['u2'] = 'top';
var u8 = document.getElementById('u8');
gv_vAlignTable['u8'] = 'center';
if (window.OnLoad) OnLoad();
