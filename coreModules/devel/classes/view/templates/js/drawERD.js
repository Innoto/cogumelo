
function drawERD(  svgDiv,  graphData, cola) {


   var width = 800,
      height =  20 * graphData.nodes.length + 25*graphData.links.length ;
console.log(d3)
    var color = d3.scale.category20();

    var cola = cola.d3adaptor()
        .linkDistance(70)
        .handleDisconnected(true)
        .size([width, height]);

    var svg = d3.select(svgDiv).append("svg")
        .attr("width", width)
        .attr("height", height);

    graph = graphData;

    // packing respects node width and height
    graph.nodes.forEach(function (v) { v.width = 15, v.height = 15 })

    cola
        .nodes(graph.nodes)
        .links(graph.links)
        .start(30, 30, 30); // need to obtain an initial layout for the node packing to work with by specifying 30 iterations here

    var link = svg.selectAll(".link")
        .data(graph.links)
      .enter().append("line")
        .attr("class", "link")
        .style("stroke-width", 2);

    var node = svg.selectAll(".node")
        .data(graph.nodes)
      .enter().append("circle")
        .attr("class", "node")
        .attr("r", function (d) { return 1.5 * d.elements})
        .style("fill", function (d) { return d.color})
        .call(cola.drag);

    node.append("title")
        .text(function (d) { return d.name; });

    cola.on("tick", function () {
        link.attr("x1", function (d) { return d.source.x; })
            .attr("y1", function (d) { return d.source.y; })
            .attr("x2", function (d) { return d.target.x; })
            .attr("y2", function (d) { return d.target.y; });

        node.attr("cx", function (d) { return d.x; })
            .attr("cy", function (d) { return d.y; });
    });

}
