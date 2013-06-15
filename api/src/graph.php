<?php

require('../../api.php');


//This API will force the system to make an updatded graph
if (isset($_GET["type"])){
if (isset($_GET["API_KEY"])){

if ($_GET["type"] == "hour"){
?>
<img src="rrd/mhsav-hour.png" alt="mhsav.png" />
<?php
}
if ($_GET["type"] == "day"){
?>
<img src="rrd/mhsav-day.png" alt="mhsav.png" />
<?php
}
if ($_GET["type"] == "week"){
?>
<img src="rrd/mhsav-week.png" alt="mhsav.png" />
<?php
}
if ($_GET["type"] == "month"){
?>
<img src="rrd/mhsav-month.png" alt="mhsav.png" />
<?php
}
if ($_GET["type"] == "year"){
?>
<img src="rrd/mhsav-year.png" alt="mhsav.png" />
<?php
}

}else{
echo "API KEY ERROR!";
}
}

?>