<?php

require_once('settings.inc.php');
require_once('miner.inc.php');


// Check for settings to write and do it after all checks
$writeSettings=false;

// User settings
if (isset($_POST['userTimezone'])) {

  $settings['userTimezone'] = $_POST['userTimezone'];
  $writeSettings=true;

}
if (isset($_POST['userPassword'])) {

	if ($_POST['userPassword'] <> '') {
		$file = '/opt/minepeon/etc/uipassword';
		$content = 'minepeon:' . crypt($_POST['newpassword']);

		file_put_contents($file, $content);
	}
}

// Mining settings
if (isset($_POST['miningRecover'])) {

  $settings['miningRecover'] = $_POST['miningRecover']=="true";
  $writeSettings=true;

}
if (isset($_POST['miningExpDev'])) {

  $settings['miningExpDev'] = $_POST['miningExpDev'];
  $writeSettings=true;

}
if (isset($_POST['api_nkey'])) {
  $rand = rand(1, 100000000000);
  $key = sha1($rand);
  $settings['apikey'] = $key;
   $writeSettings=true;

}
if (isset($_POST['api_enable'])) {
  $rand = rand(1, 100000000000);
  $key = sha1($rand);
  $settings['apikey'] = $key;
   $writeSettings=true;

}
if (isset($_POST['api_disable'])) {
  $settings['apikey'] = "";
   $writeSettings=true;

}

if (isset($_POST['miningExpHash'])) {

  $settings['miningExpHash'] = $_POST['miningExpHash'];
  $writeSettings=true;

}

// Donation settings
if (isset($_POST['donateEnable']) and isset($_POST['donateAmount'])) {

  $settings['donateEnable'] = $_POST['donateEnable']=="true";
  $settings['donateAmount'] = $_POST['donateAmount'];

  // If one of both 0, make them both
  if ($_POST['donateEnable']=="false" || $_POST['donateAmount']<1) {
    $settings['donateEnable'] = false;
    $settings['donateAmount'] = 0;
  }
  $writeSettings=true;
  
}

// Alert settings
if (isset($_POST['alertEnable'])) {

  $settings['alertEnable'] = $_POST['alertEnable']=="true";
  $writeSettings=true;
  
}
if (isset($_POST['alertDevice'])) {

  $settings['alertDevice'] = $_POST['alertDevice'];
  $writeSettings=true;

}
if (isset($_POST['alertEmail'])) {

	$settings['alertEmail'] = $_POST['alertEmail'];
	$writeSettings=true;

}
if (isset($_POST['alertSmtp'])) {

  $settings['alertSmtp'] = $_POST['alertSmtp'];
  $writeSettings=true;

}

// Write settings
if ($writeSettings) {
  ksort($settings);
  writeSettings($settings);
}

function formatOffset($offset) {
	$hours = $offset / 3600;
	$remainder = $offset % 3600;
	$sign = $hours > 0 ? '+' : '-';
	$hour = (int) abs($hours);
	$minutes = (int) abs($remainder / 60);

	if ($hour == 0 AND $minutes == 0) {
		$sign = ' ';
	}
	return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');

}

$utc = new DateTimeZone('UTC');
$dt = new DateTime('now', $utc);

$tzselect = '<select id="userTimezone" name="userTimezone" class="form-control">';

foreach(DateTimeZone::listIdentifiers() as $tz) {
	$current_tz = new DateTimeZone($tz);
	$offset =  $current_tz->getOffset($dt);
	$transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
	$abbr = $transition[0]['abbr'];

	$tzselect = $tzselect . '<option ' .($settings['userTimezone']==$tz?"selected":""). ' value="' .$tz. '">' .$tz. ' [' .$abbr. ' '. formatOffset($offset). ']</option>';
}
$tzselect = $tzselect . '</select>';


include('head.php');
include('menu.php');
?>
<div class="container">
  <h2>Settings</h2>
  <form name="user" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>User</legend>
      <div class="form-group">
        <label for="userTimezone" class="control-label col-lg-3">Timezone</label>
        <div class="col-lg-9">
          <?php echo $tzselect ?>
          <p class="help-block">MinePeon thinks it is now <?php echo date('D, d M Y H:i:s T') ?></p>
        </div>
      </div>
      <div class="form-group">
        <label for="userPassword" class="control-label col-lg-3">New Password</label>
        <div class="col-lg-9">
          <input type="password" placeholder="New password" id="userPassword" name="userPassword" class="form-control">
          <p class="help-block">An empty password will not be saved.</p>
          <button type="submit" class="btn btn-default">Submit</button>
        </div>
      </div>
    </fieldset>
  </form>

  <form name="mining" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Mining</legend>
      <div class="form-group">
        <label for="miningRecover" class="control-label col-lg-3">Miner process</label>
        <div class="col-lg-9">
          <div class="checkbox">
            <input type='hidden' value='false' name='miningRecover'>
            <label>
              <input type="checkbox" <?php echo $settings['miningRecover']?"checked":""; ?> value="true" id="miningRecover" name="miningRecover"> Automatically attempt recovery
            </label>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label for="miningExpDev" class="control-label col-lg-3">Expected Devices</label>
        <div class="col-lg-9">
          <input type="number" value="<?php echo $settings['miningExpDev'] ?>" id="miningExpDev" name="miningExpDev" class="form-control">
          <p class="help-block">
            If the count of active devices falls below this value, an alert will be sent.
          </p>
        </div>
      </div>
      <div class="form-group">
        <label for="miningExpHash" class="control-label col-lg-3">Expected Hashrate</label>
        <div class="col-lg-9">
          <div class="input-group">
            <input type="number" value="<?php echo $settings['miningExpHash'] ?>" id="miningExpHash" name="miningExpHash" class="form-control">
            <span class="input-group-addon">MH/s</span>
          </div>
          <p class="help-block">
            If the hashrate falls below half this value for more than a minute, an alert will be sent.<br/>
            After 3 minutes cgminer will be restarted.
          </p>
        </div>
      </div>
      <div class="form-group">
        <label for="donateAmount" class="control-label col-lg-3">Donation</label>
        <div class="col-lg-9">
          <div class="checkbox">
            <input type='hidden' value='false' name='donateEnable'>
            <label>
              <input type="checkbox" <?php echo $settings['donateEnable']?"checked":""; ?> value="true" id="donateEnable" name="donateEnable"> Enable donation
            </label>
          </div>
          <div class="donate-enabled <?php echo $settings['donateEnable']?"":"collapse"; ?>">
            <div class="input-group">
              <input type="number" value="<?php echo $settings['donateAmount'] ?>" placeholder="Donation minutes" id="donateAmount" name="donateAmount" class="form-control">
              <span class="input-group-addon">minutes per day</span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Submit</button>
        </div>
      </div>
    </fieldset>
  </form>

  <form name="alerts" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Alerts</legend>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <div class="checkbox">
            <input type='hidden' value='false' name='alertEnable'>
            <label>
              <input type="checkbox" <?php echo $settings['alertEnable']?"checked":""; ?> value="true" id="alertEnable" name="alertEnable"> Enable e-mail alerts
            </label>
          </div>
        </div>
      </div>
      <div class="form-group alert-enabled <?php echo $settings['alertEnable']?"":"collapse"; ?>">
        <label for="alertDevice" class="control-label col-lg-3">Device Name</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $settings['alertDevice'] ?>" id="alertDevice" name="alertDevice" class="form-control" placeholder="MinePeon">
        </div>
      </div>
      <div class="form-group alert-enabled <?php echo $settings['alertEnable']?"":"collapse"; ?>">
        <label for="alertEmail" class="control-label col-lg-3">E-mail</label>
        <div class="col-lg-9">
          <input type="email" value="<?php echo $settings['alertEmail'] ?>" id="alertEmail" name="alertEmail" class="form-control" placeholder="example@example.com">
        </div>
      </div>
      <div class="form-group alert-enabled <?php echo $settings['alertEnable']?"":"collapse"; ?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Server</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $settings['alertSmtp'] ?>" id="alertSmtp" name="alertSmtp" class="form-control" placeholder="smtp.myisp.com">
          <p class="help-block">Please choose your own SMTP server.</p>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Submit</button>
        </div>
      </div>
    </fieldset>
  </form>


    <fieldset>
      <legend>API</legend>
 <div class="form-group">
<?php
if ($settings['apikey'] == ""){
?>
<div class="alert">
    <strong>Warning!</strong> API key is not <strong><a href="javascript:{}" onclick="document.getElementById('api_nkey').submit(); return false;" class="alert-link">Enabled</a></strong>
</div>
<?php
}
?>
        <label for="Api_Key" class="control-label col-lg-3">API Key</label>
     <div class="row">
  <div class="col-lg-6">


<form action="/settings.php" name="api_nkey" method="POST" id="api_nkey">
<input type="hidden" name="api_nkey">
</form>
<form action="/settings.php" name="api_enable" method="POST" id="api_enable">
<input type="hidden" name="api_enabe">
</form>
<form action="/settings.php" name="api_disable" method="POST" id="api_disable">
<input type="hidden" name="api_disable">
</form>

    <div class="input-group">
      <input value="<?php echo $settings['apikey'];?>" type="text" name="api_key" class="form-control">
      <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Options <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right">

          <li><a href="javascript:{}" name="api_nkey" onclick="document.getElementById('api_nkey').submit(); return false;">Create new Key</a></li>


        
<?php
if ($settings['apikey'] == ""){
?>
          <li><a href="javascript:{}" name="api_enable" onclick="document.getElementById('api_enable').submit(); return false;">Enable</a></li>
<?php
}else{
?>
<li><a href="javascript:{}" name="api_disable" onclick="document.getElementById('api_disable').submit(); return false;">Disable</a></li>

<?php
}
?>

        </ul>
       </div>
      </div>



    </div>
  </div>
</div>
    </fieldset>
  </form>

</div>
<?php
include('foot.php');
?>
