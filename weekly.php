<?php
////////////////////////////////////////////////////////////////
// 7 DAY TEMPERATURE AND HUMIDITY LOGGING LINE CHART
// Andrew Taylor 2016 
//

////////////////////////////////////////////////////////////////
// Library Imports 
//
include("pChart/class/pData.class.php");
include("pChart/class/pDraw.class.php");
include("pChart/class/pImage.class.php");

////////////////////////////////////////////////////////////////
// Variables 
//
/* Chart Data Variable*/
$MyData= new pData(); 
/* DB filename Variable*/
$dbname = "/media/databases/temphumidata.db";
/* Get 7 days ago timestamp */
$timestamp = strtotime('now') - 168*60*60;
/* Format timestamp for DB */
$weekago = date("Y-m-d H:i:s",$timestamp);
/* DB results */
$timestamp = $temperature = $humidity = ""; 

////////////////////////////////////////////////////////////////
// Get information from SQLITE3 Database 
//
/* Get connection to statement */
$db = new sqlite3($dbname);
/* Prepared Statement creation */
$statement = $db->prepare('SELECT * FROM weather WHERE timestamp > :id;');
/* Bind Value to prepared statement */
$statement->bindValue(':id',$weekago);
/* Results array */
$results = $statement->execute();
/* While loop to extract results array for charting */
while ($row = $results->fetchArray()) 
{
	$timestamp[]   = $row["timestamp"];
	$temperature[] = $row["temperature"];
	$humidity[]    = $row["humidity"];
}
/* Close DB Connection */
$db->close();

////////////////////////////////////////////////////////////////
// Organise Data for Graphing 
//
/* Load date time data */
$MyData->addPoints($timestamp,"Timestamp");
/* Load Temperature data */
$MyData->addPoints($temperature,"Temperature");
/* Load Humidity data */
$MyData->addPoints($humidity,"Humidity");
/* Set X-Axis */
$MyData->setAbscissa("Timestamp");
/* Set X-Axis name */
$MyData->setXAxisName("Time");
/* Setup Series Y-Axis 0 */
$MyData->setSerieOnAxis("Temperature",0);
/* Setup Series Y-Axis 1 */
$MyData->setSerieOnAxis("Humidity",1);
/* Setup Series Y-Axis 1 Location */
$MyData->setAxisPosition(1,AXIS_POSITION_RIGHT);
/* Set Y-Axis Series 0 name */
$MyData->setAxisName(0,"Temperature");
/* Set Y-Axis Series 1 name */
$MyData->setAxisName(1,"Humidity");
/* Set Y-Axis Series 0 Units */
$MyData->setAxisUnit(0,"°C");
/* Set Y-Axis Series 1 Units */
$MyData->setAxisUnit(1,"%");
////////////////////////////////////////////////////////////////
// Maximum Values
//
/* Get maximum Temperature Value */
$temp = $MyData->getMax("Temperature");
/* Get maximum Temperature Value */
$T_Maximum = number_format($temp,1);
/* Get maximum Humidity Value */
$temp = $MyData->getMax("Humidity");
/* Format Max to 1 decimal place */
$H_Maximum = number_format($temp,1);

////////////////////////////////////////////////////////////////
// Average Values
//
/* get average Temperature value */
$temp = $MyData->getSerieAverage("Temperature");
/* format average to 3 decimal places */
$T_Average = number_format($temp,1);
/* get average Humidity value */
$temp = $MyData->getSerieAverage("Humidity");
/* format average to 1 decimal places */
$H_Average = number_format($temp,1);

////////////////////////////////////////////////////////////////
// Standard Deviation Values
//
/* get Standard Deviation Temperature Value */
$temp = $MyData->getStandardDeviation("Temperature");
/* Format SD to 3 decimal places */
$T_StandardDeviation = number_format($temp,1);
/* get Standard Deviation Humidity Value */
$temp = $MyData->getStandardDeviation("Humidity");
/* Format SD to 1 decimal places */
$H_StandardDeviation = number_format($temp,1);

////////////////////////////////////////////////////////////////
// Minimum Values
//
/* Get minimum Temperature value */
$temp = $MyData->getMin("Temperature");
/* Get minimum Temperature value */
$T_Minimum = number_format($temp,1);
/* Get minimum Humidity value */
$temp = $MyData->getMin("Humidity");
/* Format Min to 1 decimal place */
$H_Minimum = number_format($temp,1);

////////////////////////////////////////////////////////////////
// Plotting Line Colours
//
/* Set Temperature Line Colour */
$MyData->setPalette("Temperature",array("R"=>0,"G"=>0,"B"=>255));
/* Set Humidity Line Colour */
$MyData->setPalette("Humidity",array("R"=>255,"G"=>0,"B"=>0));

////////////////////////////////////////////////////////////////
// Prepare and create Graph 
//
/* Set graph area */
$myPicture = new pImage(1080,460,$MyData);
/* Turn an AA */
$myPicture->Antialias = TRUE;
/* Rectangle Border */
$myPicture->drawRectangle(0,0,1079,459,array("R"=>0,"G"=>0,"B"=>0));

////////////////////////////////////////////////////////////////
// Headings 
//
/* Font Setup */
$myPicture->setFontProperties(array("FontName"=>"pChart/fonts/Forgotte.ttf","FontSize"=>11));
/* Heading Time Frame */
$myPicture->drawText(540,35,"7 Days",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
/* Heading Temperature */
$myPicture->drawText(200,35,"TEMPERATURE",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE,"B"=>255));
/* Heading Humidity */
$myPicture->drawText(880,35,"Humidity",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE,"R"=>255));

////////////////////////////////////////////////////////////////
// Maximum, Minimum, Standard Deviation and Average 
//
/* Font Setup */
$myPicture->setFontProperties(array("FontName"=>"pChart/fonts/pf_arma_five.ttf","FontSize"=>8));
/* Maximum Temperature Value */
$myPicture->drawText(100,425,"Maximum:- $T_Maximum °C",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMLEFT,"B"=>255));
/* Average Temperature Value */
$myPicture->drawText(260,425,"Average:- $T_Average °C",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMLEFT,"B"=>255));
/* Standard Temperature Deviation Value */
$myPicture->drawText(260,445,"SD:- $T_StandardDeviation °C",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMLEFT,"B"=>255));
/* Minimum Temperature Value */
$myPicture->drawText(100,445,"Minimum:- $T_Minimum °C",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMLEFT,"B"=>255));
/* Maximum Humidity Value */
$myPicture->drawText(540,425,"Maximum:- $H_Maximum %",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>255));
/* Average Humidity Value */
$myPicture->drawText(700,425,"Average:- $H_Average %",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>255));
/* Standard Humidity Deviation Value */
$myPicture->drawText(700,445,"SD:- $H_StandardDeviation %",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>255));
/* Minimum Humidity Value */
$myPicture->drawText(540,445,"Minimum:- $H_Minimum %",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>255));
/* Image creation time */
$myPicture->drawText(1075,455,date("Y/m/d H:i:s"),array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMRIGHT));

////////////////////////////////////////////////////////////////
// Chart area and Scale 
//
/* Line Chart Area */
$myPicture->setGraphArea(100,40,980,400);
/* Scaling Setup */
$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
/* Draw Scale */
$myPicture->drawScale($scaleSettings);

////////////////////////////////////////////////////////////////
// Average and Standard Deviation Lines
//
/* Temperature Average Line */
$myPicture->drawThreshold($T_Average,array("AxisID"=>0,"WriteCaption"=>FALSE,"Caption"=>"Temp Ave","R"=>0,"G"=>0,"B"=>255,));
/* Temperature Standard Deviation Upper */
$myPicture->drawThreshold($T_Average + $T_StandardDeviation,array("AxisID"=>0,"WriteCaption"=>FALSE,"Caption"=>"Temp SD","R"=>0,"G"=>0,"B"=>255,));
/* Temperature Standard Deviation Lower */
$myPicture->drawThreshold($T_Average - $T_StandardDeviation,array("AxisID"=>0,"WriteCaption"=>FALSE,"Caption"=>"Temp SD","R"=>0,"G"=>0,"B"=>255,));
/* Humidity Average Line */
$myPicture->drawThreshold($H_Average,array("AxisID"=>1,"WriteCaption"=>FALSE,"Caption"=>"Pres Ave","R"=>255,"G"=>0,"B"=>0,));
/* Humidity Standard Deviation Upper */
$myPicture->drawThreshold($H_Average + $H_StandardDeviation,array("AxisID"=>1,"WriteCaption"=>FALSE,"Caption"=>"Pres SD","R"=>255,"G"=>0,"B"=>0,));
/* Humidity Standard Deviation Lower */
$myPicture->drawThreshold($H_Average - $H_StandardDeviation,array("AxisID"=>1,"WriteCaption"=>FALSE,"Caption"=>"Pres SD","R"=>255,"G"=>0,"B"=>0,));

////////////////////////////////////////////////////////////////
// Draw the Line Chart
//
/* Draw Line Chart */
$myPicture->drawLineChart();
/* Render the picture and send to browser */
$myPicture->Stroke();
?>
