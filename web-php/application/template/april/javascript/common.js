/**
 * 
 */
$(document).ready(function() {
	$("#query,#query2").autocomplete("/ajax/relate/", {
		minchars: 1,
		max: 9,
		delay: 0,
		mustmatch: true,
		matchcontains: false,
		scrollheight: 220,
		selectFirst: false,
		cacheLength:1,
//		width: 260,
//		scroll: true,
		formatitem: function(data, i, total) {
			if(data[1]=="a"){
				return '<strong>'+data[0]+'</strong>';
			}
			return data[0];
		}
	});
});