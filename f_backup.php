<?php
/*
- default: Get a list of all backups and their content
- backup: Copy list of items to backupFolder
- export: Zip a folder and serve as download
*/

// Default backup folder
$backupFolder= '/opt/minepeon/etc/backup/';
$baseFolder  = '/opt/minepeon/';

include('backup.inc.php');

// Zip a folder and serve as download
if (!empty($_REQUEST['export'])) {
	$zipFolder='/opt/minepeon/etc/';
	$zipData='backup/'.$_REQUEST['export'];
	$zipName='minepeon-'.$_REQUEST['export'].'.zip';

	serve_zip($zipFolder,$zipName,$zipData);
	exit;
}

header('Content-type: application/json');

// Copy list of items to backupFolder
if (!empty($_REQUEST['backup'])) {
	$items = json_decode($_REQUEST['backup'], true);

	if(!empty($items)&&is_array($items)&&!empty($_REQUEST['name'])){
		$r['info'][]=array('type' => 'success', 'text' => 'Backup saved');
		foreach ($items as $key => $value) {
			if($value['selected']){
				$r['data'][$key]=secure_copy($baseFolder.'/'.$value['name'],$backupFolder.'/'.$_REQUEST['name'].'/'.$value['name']);
			}
		}
	}
	else{
		$r['info'][]=array('type' => 'success', 'text' => 'Backup not saved');
	}
}

// Get a list of all backups and their content
else{
	// Scan backup folder and remove . & ..
	$items = @scandir($backupFolder);
	if(!empty($items)){
		array_shift($items);
		array_shift($items);
	}
	rsort($items);

	// Scan subfolders, in the future this should also return the files
	foreach ($items as $key => $value) {
		$r['data'][$key]['dir']=$value;
		$r['data'][$key]['items']=@scandir($backupFolder.'/'.$value);
		if(!empty($r['data'][$key]['items'])){
			array_shift($r['data'][$key]['items']);
			array_shift($r['data'][$key]['items']);
		}
	}
}

echo json_encode($r);
?>