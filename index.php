<?php
require("config.php");



?>


<!DOCTYPE html>

<html>
<head>

                        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
                        <link rel="stylesheet" href="styles/2col.css"/>
                        <link rel="stylesheet" href="styles/2col-pro.css"/>
                        <script src="scripts/libs/modernizr.2.6.2.min.js"></script>
</head>


<body>
        <div class="two columns">
                                        <h2>Power Consumption </h2>
					<div id="controls">Date: <input id="fromdate" value="<?php echo date("d/m/Y");?>"> <input type="submit" value="Go" onclick="javascript: update_graph()"></div>
<br><br>
                                        <div id="graph"></div>
					<div id="test"></div>
                                </div>
                        </div>
                        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
                        <script src="scripts/libs/jquery.flot.js"></script>



<script>
                                $(document).ready(function() {

					update_graph();
});

function update_graph(){
				var sel_fromdate = $("#fromdate").val();
				var sel_todate = $("#todate").val();


					var request = $.ajax({
						type: "POST",
					url: "generate.php",
					cache: false,
					data: {"fromdate" : sel_fromdate},
					datatype: "html"
					});


					request.done(function(html){
					//alert(html);
		

					var consumption = jQuery.parseJSON(html);

					//alert(consumption);

					//var axis_date=new Date();
					var graph_data=new Array();
					var counter=0;
					for (var i=0;i<consumption.length;i=i+1) { //todo: increase resolution
						graph_data[counter] = new Array();
						if(i<10) {
//							axis_date=new Date(parseDate('12/01/2013 0'+i+':00:00 UTC'));//'2012-11-27 '+i+':00:00 UTC'));
							axis_date=new Date(parseDate(sel_fromdate+' 0'+i+':00:00 UTC'));//'2012-11-27 '+i+':00:00 UTC'));
						} else {
							axis_date=new Date(parseDate(sel_fromdate+' '+i+':00:00 UTC'));//'2012-11-27 '+i+':00:00 UTC'));
						}	
						graph_data[counter]=[parseInt(axis_date.getTime(),10),consumption[i]];
						counter++;
						//graph_data[counter][1]=consumption[i];
						//counter++;
					}
					//$('#canvas_id').empty();
					//alert("here");
					//$("#test").html("hi");
					//alert(graph_data);
                                        function showkwh(v,axis) { return v.toFixed(axis.tickDecimals)+"kWh"; }
                                        function doPlot(position) {
                                                $.plot($("#graph"),
                                                        [ { data: graph_data, color: '#1275c2', label: 'Power Used', lines: { show: true }, points: { show: false }, yaxis: 1 } ],
                                                        { xaxes: [ { mode: 'time', timeformat: '%h',

                                                                //min: (new Date("<?php print(date("Y/m/d 00:00:00")); ?>")).getTime(),

								min: (new Date(parseDate(sel_fromdate+' 00:00:00 UTC')).getTime()),
                                                              //max: (new Date("<?php print(date("Y/m/d 23:59:59")); ?>")).getTime(),
									max: (new Date(parseDate(sel_fromdate+' 23:59:59 UTC')).getTime()),
                                                                ticks: 24, tickLength: 1, minTickSize: [1, 'hour'], reserveSpace: true } ],
                                                          yaxes: [ { min: 0, alignTicksWithAxis: null, position: 'left', tickFormatter: showkwh } ],
                                                          legend: { show: false },
                                                          grid: { hoverable: true, clickable: true, autoHighlight: true }
                                                        }
                                                );
                                        }


                                function showTooltip(x, y, contents) {
                                        $('<div id="tooltip">' + contents + '</div>').css( {
                                            position: 'absolute',
                            display: 'none',
                    top: y + 5,
                    left: x + 5,
                    border: '1px solid #fdd',
                    padding: '2px',
                    'background-color': '#fee',
                    opacity: 0.80
                }).appendTo("body").fadeIn(200);
    }
var previousPoint = null;
$("#graph").bind("plothover", function (event, pos, item) {
if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);

                    showTooltip(item.pageX, item.pageY,item.series.label + " of " + x + " = " + y);
                    }
                else {

                                $("#tooltip").remove();
                previousPoint = null;
 }
        }
});
                                        doPlot("right");

					});
					      };
function parseDate(date) {
    var m = /^(\d\d)\/(\d\d)\/(\d{4}) (\d\d):(\d\d):(\d\d) UTC$/.exec(date);
    var tzOffset = new Date(+m[3], +m[2] - 1, +m[1], +m[4], +m[5], +m[6]).getTimezoneOffset();

    return new Date(+m[3], +m[2] - 1, +m[1], +m[4], +m[5] - tzOffset, +m[6]);
}

                        </script>



</body>
</html>
