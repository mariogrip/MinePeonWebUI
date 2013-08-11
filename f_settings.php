<?php
/*
f_settings syncs settings in different files and always returns new state
returns settings and logs last settings change in ['time']
*/
header('Content-type: application/json');
$minerUserConfig='miner.conf';
$settingsConfig='minepeon.conf';

// Set new password
if (!empty($_REQUEST['pass'])) {
	$pass=json_decode($_REQUEST['pass']);
	if (strlen($pass) > 0) {
		$file = '/opt/minepeon/etc/uipassword';

		file_put_contents($file,'minepeon:' . crypt($pass));
		$r['info'][]=array('type' => 'success', 'text' => 'Password saved');
	}
}

// Manage settings
elseif (!empty($_REQUEST['settings'])) {
	$newdata   = json_decode($_REQUEST['settings'], true);
	$r['data'] = json_decode(@file_get_contents('/opt/minepeon/etc/'.$settingsConfig), true);

	// Sync current with new settings
	if(!empty($newdata)&&is_array($newdata)){
		foreach ($newdata as $key => $value) {
			$r['data'][$key]=$value;
		}
		// Write back to file
		file_put_contents('/opt/minepeon/etc/'.$settingsConfig, json_encode($r['data'], JSON_PRETTY_PRINT));
		$r['info'][]=array('type' => 'success', 'text' => 'Settings saved');
	}
	// Load current settings
	else{
		$r['info'][]=array('type' => 'success', 'text' => 'Settings loaded');
	}

	$r['data']['time'] = time();
}

// Manage pools
elseif (!empty($_REQUEST['pools'])) {
	$newdata   = json_decode($_REQUEST['pools'], true);
	$r['data'] = json_decode(@file_get_contents('/opt/minepeon/etc/'.$minerUserConfig), true);

	// Overwrite current with new pools
	if(!empty($newdata)&&is_array($newdata)){
		unset($r['data']['pools']);
		$r['data']['pools']=$newdata;
		// Write back to file
		file_put_contents('/opt/minepeon/etc/'.$minerUserConfig, json_encode($r['data'], JSON_PRETTY_PRINT));
		file_put_contents('/opt/minepeon/etc/miner.user.conf',   json_encode($r['data'], JSON_PRETTY_PRINT));
		$r['info'][]=array('type' => 'success', 'text' => 'Pools config saved');
		$r['info'][]=minerRestart();
	}
	// Restart miner
	elseif(!empty($newdata)&&$newdata=='restart'){
		$r['info'][]=minerRestart();
	}
	// Load current settings
	else{
		$r['info'][]=array('type' => 'success', 'text' => 'Pools config loaded');
	}
}

// Manage miner.user.conf
elseif (!empty($_REQUEST['options'])) {
	$newdatatemp = json_decode($_REQUEST['options'], true);

	// Overwrite current with new config
	if(!empty($newdata)&&is_array($newdata)){

		// Angular => cgminer (objects => strings)
		foreach ($newdatatemp as $value) {
			$newdata[$value['key']]=$value['value'];
		}

		file_put_contents('/opt/minepeon/etc/'.$minerUserConfig, json_encode($newdata, JSON_PRETTY_PRINT));
		$r['data']=$newdata;
		$r['info'][]=array('type' => 'success', 'text' => 'Miner config saved');
		$r['info'][]=minerRestart();
	}
	// Load current settings
	else{
		$olddata = json_decode(@file_get_contents('/opt/minepeon/etc/'.$minerUserConfig), true);

		if(!empty($olddata)&&is_array($olddata)){
			$r['data']=$olddata;
			$r['info'][]=array('type' => 'success', 'text' => 'Miner config loaded');
		}
		else{
			$r['info'][]=array('type' => 'danger', 'text' => 'Failed to load miner config');
		}
	}

	// cgminer => Angular (strings => objects)
	if(!empty($r['data'])&&is_array($r['data'])){
		$temp=$r['data']; unset($r['data']); $i=0;
		foreach ($temp as $key => $value) {
			$r['data'][$i]['key']=$key;
			$r['data'][$i++]['value']=$value;
		}
	}
}

// Set system timezone to what is stored in settings 
elseif (!empty($_REQUEST['timezone'])) {
	$timezone = json_decode(file_get_contents('/opt/minepeon/etc/'.$settingsConfig), true)['userTimezone'];
	ini_set( 'date.timezone', $timezone );
	putenv('TZ=' . $timezone);
	date_default_timezone_set($timezone);
	$r['info'][]=array('type' => 'danger', 'text' => 'Timezone updated');
}

echo json_encode($r);

function minerRestart(){
	// Setup socket
	$client = @stream_socket_client('tcp://127.0.0.1:4028', $errno, $errorMessage);

	// Socket failed
	if (empty($client)) {
		return array('type' => 'danger', 'text' => 'Could not restart cgminer because: '.$errno.' '.$errorMessage);
	}

	// Socket success
	fwrite($client, json_encode(array('command'=>'restart')));
	fclose($client);
	return array('type' => 'info', 'text' => 'Cgminer restarting');
}
?>