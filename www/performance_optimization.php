<?php 
include 'common.inc';
require_once('optimization_detail.inc.php');
$page_keywords = array('Optimization','Webpagetest','Website Speed Test','Page Speed');
$page_description = "Website performance optimization recommendations$testLabel.";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>WebPagetest Optimization Check Results<?php echo $testLabel; ?></title>
        <?php $gaTemplate = 'Optimization Check'; include ('head.inc'); ?>
        <style type="text/css">
            td.nowrap {white-space:nowrap;}
            th.nowrap {white-space:nowrap;}
            tr.blank {height:2ex;}
			.indented1 {padding-left: 40pt;}
			.indented2 {padding-left: 80pt;}
            h1
            {
                font-size: larger;
            }
            
            #opt
            {
                margin-bottom: 2em;
            }
            #opt_table
            {
                border: 1px solid black;
                border-collapse: collapse;
            }
            #opt_table th
            {
                padding: 5px;
                border: 1px solid black;
                font-weight: normal;
            }
            #opt_table td
            {
                padding: 5px;
                border: 1px solid black;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="page">
            <?php
            $tab = 'Test Result';
            $subtab = 'Performance Review';
            include 'header.inc';
            $requests = getRequests($id, $testPath, $run, $cached, $secure, $haveLocations, false, false, true);
            ?>
            <hr>
            <h1 style="text-align:center; font-size:2.8em">
              	<?php 
 				if($cached){
					echo "Repeat View";
				} else {
					echo "First View";
				}      
               	?>
            </h1>
            <hr>
            <br>
            <?php
                echo '<div id="grades">';
                echo '<table class="pretty" align="center" border="none">';
                require_once('page_data.inc');
                $pageDataArray = loadPageRunData($testPath, $run, $cached, array('allEvents' => true));
                foreach($pageDataArray as $eventName => $pageData) {
                    $grades = GetGrades($pageData, $test, $id, $run);
                    if (count($grades)) {
                        $smaller = '';
                        if (count($grades) > 6)
                            $smaller = ' smaller';
                        echo "<tr class=\"grades$smaller\">";
                        echo "<td style=\"border:none;\"><h3>$eventName</h3></td>";
                        $optlink = FRIENDLY_URLS ? "/result/$id/$gradeRun/performance_optimization/" : "performance_optimization.php?test=$id&run=$gradeRun";
                        foreach ($grades as $check => &$grade)
                            echo "<td class=\"$check\" style=\"border:none;\"><a href=\"$optlink#{$check}_".getEventNameId($eventName)."\"><h2 class=\"{$grade['class']}\">{$grade['grade']}</h2></a>{$grade['description']}</td>";
                        echo '</tr>';
                    }
                }
                echo '</table>';
                echo '</div>';
            ?>
            <hr>
            <br>
            <div style="text-align:center;">
            	<?php foreach(array_keys($requests) as $eventName)
            	{ ?>
                	<a name="checklist<?php echo $eventName?>"></a>
                	<h1>Full Optimization Checklist - <?php echo $eventName; ?></h1>
                	<?php                	
	                    echo '<img alt="Optimization Checklist" id="image'.$eventName.'" src="';
	                    echo "/optimizationChecklist.php?test=$id&run=$run&cached=$cached&eventName=$eventName";
                    echo '">';
	                ?>                
                <br/><br/><br/>
                <?php     
            	}
                ?>
            </div>

		    <br>
            <?php include('./ads/optimization_middle.inc'); ?>
		    <br>
			
			<a name="details"></a>
            <h2>Details (for all Events):</h2>
            <?php
                require 'optimization.inc';

                require_once('object_detail.inc');
                $secure = false;
                $haveLocations = false;
                $requestsArray = getRequests($id, $testPath, $run, $cached, $secure, $haveLocations, false, false, true);
                foreach($pageDataArray as $eventName => $pageData) {
                    echo "<h3>Event $eventName</h3>";
                    $requests = $requestsArray[$eventName];
                    dumpOptimizationReport($pageData, $requests, $id, $run, $cached, $test);
                }
                echo '<p></p><br>';
                include('./ads/optimization_bottom.inc');
                echo '<br>';
                dumpOptimizationGlossary($settings);
            ?>
            
            <?php include('footer.inc'); ?>
        </div>
    </body>
</html>
