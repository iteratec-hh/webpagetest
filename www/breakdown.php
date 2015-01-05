<?php
include 'utils.inc';
include 'common.inc';
include 'breakdown.inc';
require_once('contentColors.inc');
include 'waterfall.inc';
require_once('page_data.inc');

$page_keywords = array('Content Breakdown','MIME Types','Webpagetest','Website Speed Test','Page Speed');
$page_description = "Website content breakdown by mime type$testLabel";

$extension = 'php';
if( FRIENDLY_URLS )
    $extension = 'png';

// walk through the requests and group them by mime type
$requests = getRequests($id, $testPath, $run, $cached, $secure, $haveLocations, false, false, true);
$requestsFv;
$breakdownFv = array();
foreach(array_keys($requests) as $eventName){
	$breakdownFv[$eventName] = getBreakdown($id, $testPath, $run, 0, $requestsFv, $eventName);
}
$breakdownRv = array();
$requestsRv;
if( (int)$test[test][fvonly] == 0 ){
	foreach(array_keys($requests) as $eventName){
    	$breakdownRv[$eventName] = getBreakdown($id, $testPath, $run, 1, $requestsRv, $eventName);
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>WebPagetest Content Breakdown<?php echo $testLabel; ?></title>
        <?php $gaTemplate = 'Content Breakdown'; include ('head.inc'); ?>
        <style type="text/css">
            td {
                text-align:center; 
                vertical-align:middle; 
                padding:1em;
            }

            div.bar {
                height:12px; 
                margin-top:auto; 
                margin-bottom:auto;
            }

            td.legend {
                white-space:nowrap; 
                text-align:left; 
                vertical-align:top; 
                padding:0;
            }
        </style>
    </head>
    <body>
        <div class="page">
            <?php
            $tab = 'Test Result';
            $subtab = 'Content Breakdown';
            include 'header.inc';
            foreach(array_keys($requests) as $eventName)
			{ ?>
				<br><hr><br>	            
            <table align="center">
                <tr>
                    <th colspan="2">
	                    <h2>Content breakdown by MIME type (First  View) - <?php echo $eventName; ?></h2>
                    </th>
                </tr>
                <tr>
                    <td>
	                        <div id="pieRequestsFv_div_<?php echo getEventNameID($eventName); ?>" style="width:450px; height:300px;"></div>
                    </td>
                    <td>
	                        <div id="pieBytesFv_div_<?php echo getEventNameID($eventName); ?>" style="width:450px; height:300px;"></div>
                    </td>
                </tr>
                <tr>
                    <td>
	                        <div id="tableRequestsFv_div_<?php echo getEventNameID($eventName); ?>" style="width: 100%;"></div>
                    </td>
                    <td>
	                        <div id="tableBytesFv_div_<?php echo getEventNameID($eventName); ?>" style="width: 100%;"></div>
                    </td>
                </tr>
            </table>
            <div style="text-align:center;">
	            <h3 name="connection<?php echo getEventNameID($eventName); ?>">Connection View (First View) - <?php echo $eventName; ?></h3>
	            <map name="connection_map<?php echo getEventNameID($eventName); ?>">
            <?php
	                $connection_rows = GetConnectionRows($requestsFv[$eventName], $summary);
                $options = array(
                    'id' => $id,
                    'path' => $testPath,
                    'run_id' => $run,
                    'is_cached' => $cached,
                    'use_cpu' => true,
                    'show_labels' => true,
                    'is_mime' => $mime
                    );
                $map = GetWaterfallMap($connection_rows, $url, $options, $pageData);
                foreach($map as $entry)
                {
                    if( $entry['request'] !== NULL )
                    {
                        $index = $entry['request'] + 1;
                        $title = $index . ': ' . $entry['url'];
                        echo '<area href="#request' . $index . '" alt="' . $title . '" title="' . $title . '" shape=RECT coords="' . $entry['left'] . ',' . $entry['top'] . ',' . $entry['right'] . ',' . $entry['bottom'] . '">' . "\n";
                    }
                    else
                        echo '<area href="#request" alt="' . $entry['url'] . '" title="' . $entry['url'] . '" shape=RECT coords="' . $entry['left'] . ',' . $entry['top'] . ',' . $entry['right'] . ',' . $entry['bottom'] . '">' . "\n";
                }
            ?>
            </map>
            <table border="1" cellpadding="2px" cellspacing="0" style="width:auto; font-size:11px; margin-left:auto; margin-right:auto;">
                <tr>
                    <td class="legend"><table><tr><td class="legend"><div class="bar" style="width:2px; background-color:#28BC00"></div></td><td class="legend">Start Render</td></tr></table></td>
                    <?php if((float)$test[$section][domElement] > 0.0) { ?>
                    <td class="legend"><table><tr><td class="legend"><div class="bar" style="width:2px; background-color:#F28300"></div></td><td class="legend">DOM Element</td></tr></table></td>
                    <?php } ?>
                    <td class="legend"><table><tr><td class="legend"><div class="bar" style="width:2px; background-color:#0000FF"></div></td><td class="legend">Document Complete</td></tr></table></td>
                </tr>
            </table>
            <br>
            <img class="progress" usemap="#connection_map" id="connectionView" src="<?php 
	            if($test['testinfo']['imageCaching']){
					$type = "connection";
					$file = generateViewImagePath($testPath, $eventName, $run, 0, $type);
	            	if(!file_exists($file)){
	            		createImageAndSave($id, $testPath, $test['testinfo'], $eventName, $run, 0, $data[$run][0], $type);
	            	}
	            	echo substr($file, 1);
	            } else {
	            	echo "/waterfall.php?type=connection&width=930&test=$id&run=$run&cached=0&mime=1&eventName=$eventName";
	            }?>">
            </div>
            <?php 
			}	
            if( count($breakdownRv) ) { 
				foreach(array_keys($requests) as $eventName)
				{ ?>
            <br><hr><br>
            <table align="center">
                <tr>
                    <th colspan="2">
	                    <h2>Content breakdown by MIME type (Repeat  View) - <?php echo $eventName; ?></h2>
                    </th>
                </tr>
                <tr>
                    <td>
	                        <div id="pieRequestsRv_div_<?php echo getEventNameID($eventName); ?>" style="width:450px; height:300px;"></div>
                    </td>
                    <td>
	                        <div id="pieBytesRv_div_<?php echo getEventNameID($eventName); ?>" style="width:450px; height:300px;"></div>
                    </td>
                </tr>
                <tr>
                    <td>
	                        <div id="tableRequestsRv_div_<?php echo getEventNameID($eventName); ?>" style="width: 100%;"></div>
                    </td>
                    <td>
	                        <div id="tableBytesRv_div_<?php echo getEventNameID($eventName); ?>" style="width: 100%;"></div>
                    </td>
                </tr>
            </table>
            <div style="text-align:center;">
	            <h3 name="connection_rv<?php echo getEventNameID($eventName); ?>">Connection View (Repeat View) - <?php echo $eventName; ?></h3>
	            <map name="connection_map_rv<?php echo getEventNameID($eventName); ?>">
            <?php
	                $connection_rows = GetConnectionRows($requestsRv[$eventName], $summary);
                $options = array(
                    'id' => $id,
                    'path' => $testPath,
                    'run_id' => $run,
                    'is_cached' => $cached,
                    'use_cpu' => true,
                    'show_labels' => true,
                    'is_mime' => $mime
                    );
                $map = GetWaterfallMap($connection_rows, $url, $options, $pageData);
                foreach($map as $entry)
                {
                    if( $entry['request'] !== NULL )
                    {
                        $index = $entry['request'] + 1;
                        $title = $index . ': ' . $entry['url'];
                        echo '<area href="#request' . $index . '" alt="' . $title . '" title="' . $title . '" shape=RECT coords="' . $entry['left'] . ',' . $entry['top'] . ',' . $entry['right'] . ',' . $entry['bottom'] . '">' . "\n";
                    }
                    else
                        echo '<area href="#request" alt="' . $entry['url'] . '" title="' . $entry['url'] . '" shape=RECT coords="' . $entry['left'] . ',' . $entry['top'] . ',' . $entry['right'] . ',' . $entry['bottom'] . '">' . "\n";
                }
            ?>
            </map>
            <table border="1" cellpadding="2px" cellspacing="0" style="width:auto; font-size:11px; margin-left:auto; margin-right:auto;">
                <tr>
                    <td class="legend"><table><tr><td class="legend"><div class="bar" style="width:2px; background-color:#28BC00"></div></td><td class="legend">Start Render</td></tr></table></td>
                    <?php if((float)$test[$section][domElement] > 0.0) { ?>
                    <td class="legend"><table><tr><td class="legend"><div class="bar" style="width:2px; background-color:#F28300"></div></td><td class="legend">DOM Element</td></tr></table></td>
                    <?php } ?>
                    <td class="legend"><table><tr><td class="legend"><div class="bar" style="width:2px; background-color:#0000FF"></div></td><td class="legend">Document Complete</td></tr></table></td>
                </tr>
            </table>
            <br>
            <img class="progress" usemap="#connection_map_rv" id="connectionViewRv" src="<?php 
				if($test['testinfo']['imageCaching']){
					$type = "connection";
					$file = generateViewImagePath($testPath, $eventName, $run, 1, $type);
	            	if(!file_exists($file)){
	            		createImageAndSave($id, $testPath, $test['testinfo'], $eventName, $run, 1, $data[$run][1], $type);
	            	}
	            	echo substr($file, 1);
	            } else {
	            	echo "/waterfall.php?type=connection&width=930&test=$id&run=$run&cached=1&mime=1&eventName=$eventName";
	            }?>">
            </div>
	            <?php 
				} 
			} ?>
        </div>
        
        <?php include('footer.inc'); ?>

        <!--Load the AJAX API-->
        <script type="text/javascript" src="<?php echo $GLOBALS['ptotocol']; ?>://www.google.com/jsapi"></script>
        <script type="text/javascript">
    
        // Load the Visualization API and the table package.
        google.load('visualization', '1', {'packages':['table', 'corechart']});
        google.setOnLoadCallback(drawTable);
        function drawTable() {
            <?php
            foreach(array_keys($requests) as $eventName)
            { ?>
            
            var dataFv = new google.visualization.DataTable();
            dataFv.addColumn('string', 'MIME Type');
            dataFv.addColumn('number', 'Requests');
            dataFv.addColumn('number', 'Bytes');
	            dataFv.addRows(<?php echo count($breakdownFv[$eventName]); ?>);
            var fvRequests = new google.visualization.DataTable();
            fvRequests.addColumn('string', 'Content Type');
            fvRequests.addColumn('number', 'Requests');
	            fvRequests.addRows(<?php echo count($breakdownFv[$eventName]); ?>);
            var fvColors = new Array();
            var fvBytes = new google.visualization.DataTable();
            fvBytes.addColumn('string', 'Content Type');
            fvBytes.addColumn('number', 'Bytes');
	            fvBytes.addRows(<?php echo count($breakdownFv[$eventName]); ?>);
            <?php
            $index = 0;
	            ksort($breakdownFv[$eventName]);
	            foreach($breakdownFv[$eventName] as $type => $data)
            {
                echo "dataFv.setValue($index, 0, '$type');\n";
                echo "dataFv.setValue($index, 1, {$data['requests']});\n";
                echo "dataFv.setValue($index, 2, {$data['bytes']});\n";
                echo "fvRequests.setValue($index, 0, '$type');\n";
                echo "fvRequests.setValue($index, 1, {$data['requests']});\n";
                echo "fvBytes.setValue($index, 0, '$type');\n";
                echo "fvBytes.setValue($index, 1, {$data['bytes']});\n";
                $color = rgb2html($data['color'][0], $data['color'][1], $data['color'][2]);
                echo "fvColors.push('$color');\n";
                $index++;
            }
            ?>
	
            var viewRequestsFv = new google.visualization.DataView(dataFv);
            viewRequestsFv.setColumns([0, 1]);
            
	            var tableRequestsFv = new google.visualization.Table(document.getElementById('tableRequestsFv_div_<?php echo getEventNameID($eventName); ?>'));
            tableRequestsFv.draw(viewRequestsFv, {showRowNumber: false, sortColumn: 1, sortAscending: false});
	
            var viewBytesFv = new google.visualization.DataView(dataFv);
            viewBytesFv.setColumns([0, 2]);
            
	            var tableBytesFv = new google.visualization.Table(document.getElementById('tableBytesFv_div_<?php echo getEventNameID($eventName); ?>'));
            tableBytesFv.draw(viewBytesFv, {showRowNumber: false, sortColumn: 1, sortAscending: false});
            
	            var pieRequestsFv = new google.visualization.PieChart(document.getElementById('pieRequestsFv_div_<?php echo getEventNameID($eventName); ?>'));
            google.visualization.events.addListener(pieRequestsFv, 'ready', function(){markUserTime('aft.Requests Pie');});
            pieRequestsFv.draw(fvRequests, {width: 450, height: 300, title: 'Requests', colors: fvColors});
	
	            var pieBytesFv = new google.visualization.PieChart(document.getElementById('pieBytesFv_div_<?php echo getEventNameID($eventName); ?>'));
            google.visualization.events.addListener(pieBytesFv, 'ready', function(){markUserTime('aft.Bytes Pie');});
            pieBytesFv.draw(fvBytes, {width: 450, height: 300, title: 'Bytes', colors: fvColors});
	        <?php 
			} 
			if( count($breakdownRv) ) { 
            	foreach(array_keys($requests) as $eventName)
            	{ ?>
                var dataRv = new google.visualization.DataTable();
                dataRv.addColumn('string', 'MIME Type');
                dataRv.addColumn('number', 'Requests');
                dataRv.addColumn('number', 'Bytes');
	                dataRv.addRows(<?php echo count($breakdownRv[$eventName]); ?>);
                var rvRequests = new google.visualization.DataTable();
                rvRequests.addColumn('string', 'Content Type');
                rvRequests.addColumn('number', 'Requests');
	                rvRequests.addRows(<?php echo count($breakdownRv[$eventName]); ?>);
                var rvColors = new Array();
                var rvBytes = new google.visualization.DataTable();
                rvBytes.addColumn('string', 'Content Type');
                rvBytes.addColumn('number', 'Bytes');
	                rvBytes.addRows(<?php echo count($breakdownRv[$eventName]); ?>);
                <?php
                $index = 0;
	                ksort($breakdownRv[$eventName]);
	                foreach($breakdownRv[$eventName] as $type => $data)
                {
                    echo "dataRv.setValue($index, 0, '$type');\n";
                    echo "dataRv.setValue($index, 1, {$data['requests']});\n";
                    echo "dataRv.setValue($index, 2, {$data['bytes']});\n";
                    echo "rvRequests.setValue($index, 0, '$type');\n";
                    echo "rvRequests.setValue($index, 1, {$data['requests']});\n";
                    echo "rvBytes.setValue($index, 0, '$type');\n";
                    echo "rvBytes.setValue($index, 1, {$data['bytes']});\n";
                    $color = rgb2html($data['color'][0], $data['color'][1], $data['color'][2]);
                    echo "rvColors.push('$color');\n";
                    $index++;
                }
                ?>
	
                var viewRequestsRv = new google.visualization.DataView(dataRv);
                viewRequestsRv.setColumns([0, 1]);
                
	                var tableRequestsRv = new google.visualization.Table(document.getElementById('tableRequestsRv_div_<?php echo getEventNameID($eventName); ?>'));
                tableRequestsRv.draw(viewRequestsRv, {showRowNumber: false, sortColumn: 1, sortAscending: false});
	
                var viewBytesRv = new google.visualization.DataView(dataRv);
                viewBytesRv.setColumns([0, 2]);
                
	                var tableBytesRv = new google.visualization.Table(document.getElementById('tableBytesRv_div_<?php echo getEventNameID($eventName); ?>'));
                tableBytesRv.draw(viewBytesRv, {showRowNumber: false, sortColumn: 1, sortAscending: false});
	
	                var pieRequestsRv = new google.visualization.PieChart(document.getElementById('pieRequestsRv_div_<?php echo getEventNameID($eventName); ?>'));
                pieRequestsRv.draw(rvRequests, {width: 450, height: 300, title: 'Requests', colors: rvColors});
	
	                var pieBytesRv = new google.visualization.PieChart(document.getElementById('pieBytesRv_div_<?php echo getEventNameID($eventName); ?>'));
                pieBytesRv.draw(rvBytes, {width: 450, height: 300, title: 'Bytes', colors: rvColors});
			<?	} 
			} ?>
        }
        </script>
    </body>
</html>

<?php
function rgb2html($r, $g=-1, $b=-1)
{
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return '#'.$color;
}
?>