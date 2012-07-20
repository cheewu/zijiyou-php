/**
 * 
 */
$(document).ready(function() {
  $("#query,#query2").autocomplete("/ajax/relate/", {
    minchars : 1,
    max : 9,
    delay : 0,
    mustmatch : true,
    matchcontains : false,
    scrollheight : 220,
    selectFirst : false,
    cacheLength : 1,
    // width: 260,
    // scroll: true,
    formatitem : function(data, i, total) {
      if (data[1] == "a") {
        return '<strong>' + data[0] + '</strong>';
      }
      return data[0];
    }
  });
});

// for D3js
// Returns a flattened hierarchy containing all leaf nodes under the root.
function classes(root) {
  var classes = [];

  function recurse(name, node) {
    if (node.children)
      node.children.forEach(function(child) {
        recurse(node.name, child);
      });
    else
      classes.push({
        packageName : name,
        className : node.name,
        value : node.size
      });
  }

  recurse(null, root);
  return {
    children : classes
  };
}

function init_bubble(id, region_id) {
  var r = $(id).width(), 
      height = $(id).height(), 
      width = $(id).width(), 
      format = d3.format(",d"), 
      fill = d3.scale.categoryZJY();
  var bubble = d3.layout.pack().sort(null).size([width, height]);
  var vis = d3.select(id)
              .append("svg")
              .attr("width", width)
              .attr("height",height)
              .attr("class", "bubble");
  d3.json("/ajax/region/" + region_id + ".json", function(json) {
    var node = vis.selectAll("g.node")
                  .data(bubble.nodes(classes(json))
                              .filter(function(d) { return !d.children;}))
                  .enter()
                  .append("g")
                  .attr("class", "node")
                  .attr("transform", function(d) { 
                                        // (d.x * 1.3 - 320 * 0.3)
                                        return "translate(" + (d.x * 1.2 - 320 * 0.2) + "," + d.y + ")";
                  }) 
                  .on('click', function(d) {
                                  location.assign("/article/" + region_id + "/?keyword=" + d.className);
                  });
    // node.append("title")
    // .text(function(d) { return d.className + ": " + d.value; });
    node.append("title").text(function(d) {
      return d.className;
    });
    node.append("circle").attr("r", function(d) {
      return d.r * 0.98;
    }).style("fill", function(d) {
      return fill(d.packageName);
    });
    node.append("text")
        .attr("text-anchor", "middle")
        .attr("dy", ".3em")
        .attr("class", "bubble-text").text(function(d) {
                            return d.className.substring(0, d.r / 3);
    });
    // node.append("a")
    // //.attr("text-anchor", "middle")
    // .attr("href", "/detail")
    // .attr("target", "_blank")
    // //.attr("class", "bubble-text").append("text")
    // .attr("text-anchor", "middle")
    // .append("text")
    // .attr("dy", ".3em")
    // .attr("class", "bubble-text")
    // .text(function(d) { return d.className.substring(0, d.r / 3); });
  });
}