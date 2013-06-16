<?php


include('ssl.inc.php');
include('timezone.inc.php');

create_graph("mhsav-hour.png", "-1h", "Hourly");
create_graph("mhsav-day.png", "-1d", "Daily");
create_graph("mhsav-week.png", "-1w", "Weekly");
create_graph("mhsav-month.png", "-1m", "Monthly");
create_graph("mhsav-year.png", "-1y", "Yearly");

function create_graph($output, $start, $title) {
  $RRDPATH = '/opt/minepeon/var/rrd/';
  $options = array(
    "--slope-mode",
    "--start", $start,
    "--title=$title",
    "--vertical-label=Hash per second",
    "--lower=0",
    "DEF:hashrate=" . $RRDPATH . "hashrate.rrd:hashrate:AVERAGE",
    "CDEF:realspeed=hashrate,1000,*",
    "LINE2:realspeed#FF0000"
  );

  $ret = rrd_graph("/opt/minepeon/http/rrd/" . $output, $options);
  if (! $ret) {
    echo "<b>Graph error: </b>".rrd_error()."\n";
  }
}
?>
