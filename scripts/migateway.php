#!/usr/bin/php5
<?php

/**
 * XiaoMi Mi Home Gateway zabbix agent plugin
 * author: @icez
 * reference: https://www.gitbook.com/book/louiszl/lumi-gateway-local-api
*/

if (empty($_SERVER['argv'][1]) || empty($_SERVER['argv'][2]) || empty($_SERVER['argv'][3])) {
	die("Usage: ".$_SERVER['argv'][0]." <gwip> <gwport> <cmd> [<otherparam>]\n");
}
$srvip = $_SERVER['argv'][1];
$srvport = $_SERVER['argv'][2] + 0;
$cmd = $_SERVER['argv'][3];

$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

function sendudp($msg) {
	global $sock, $srvip, $srvport;
	socket_sendto($sock, $msg, strlen($msg), 0, $srvip, $srvport);
	socket_recvfrom($sock, $buf, 65535, 0, $srvip, $srvport);
	return $buf;
}
switch ($cmd) {
case "discovery":
	$msg = "{\"cmd\":\"get_id_list\"}";
	$result = sendudp($msg);
	$resp = json_decode($result, true);
	if ($resp === false) die("invalid response: $result\n");
	$subdevices = json_decode($resp['data'], true);
	$subdevices[] = $resp['sid'];
	$result = array();
	$result['data'] = [];
	foreach($subdevices as $subdevice) {
		$msg = '{"cmd":"read","sid":"'.$subdevice.'"}';
		$res = json_decode(sendudp($msg), true);
		if ($res === false) continue;
		$result['data'][] = [
			'{#SID}' => $subdevice,
			'{#MODEL}' => $res['model']
		];
	}
	echo json_encode($result);
break;
case "dump":
	if (empty($_SERVER['argv'][4])) die("invalid parameter for dump\n");
	$msg = '{"cmd":"read","sid":"'.$_SERVER['argv'][4].'"}';
	$res = json_decode(sendudp($msg), true);
	if (empty($res['data'])) die('invalid response');
	$data = json_decode($res['data'], true);
	print_r($res);
	print_r($data);
break;
case "read":
	if (empty($_SERVER['argv'][4]) || empty($_SERVER['argv'][5])) die("invalid parameter for read\n");
	$key = $_SERVER['argv'][5];
	$msg = '{"cmd":"read","sid":"'.$_SERVER['argv'][4].'"}';
	$res = json_decode(sendudp($msg), true);
	if (empty($res['data'])) die('invalid response');
	$data = json_decode($res['data'], true);
	if (!isset($data[$key])) die("invalid key.\n");
	echo $data[$key];
break;
default:
	echo "Unknown command: $cmd\n";
break;
}
