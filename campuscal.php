<?php
if(!isset($_SERVER['PHP_AUTH_USER'])) {
	header('WWW-Authenticate: Basic realm="CAMPUS Login"');
	header('HTTP/1.0 401 Unauthorized');
	exit;
}
$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];
try {
	$cookie = tempnam(null, 'campus');
	if(!is_writable($cookie)) {
		throw new Exception('cookie nicht schreibbar in Verzeichnis: ' . dirname($cookie));
	}
	$ch = curl_init();
	// login:
	curl_setopt($ch, CURLOPT_URL, 'https://www.campus.rwth-aachen.de/office/views/campus/redirect.asp');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	$loginData = array(
		'u' => $user,
		'p' => $pass
	);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
	curl_exec($ch);
	// get ics:
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	$params = array(
		'startdt' => date('d.m.Y', strtotime('-1 month')),
		'enddt' => date('d.m.Y', strtotime('+8 months'))
	);
	curl_setopt($ch, CURLOPT_URL, 'https://www.campus.rwth-aachen.de/office/views/calendar/iCalExport.asp?' . http_build_query($params));
	$ics = curl_exec($ch);
	curl_close($ch);
	unlink($cookie);
	/*if(!strpos($ics, 'BEGIN:VCALENDAR')) {
		header('WWW-Authenticate: Basic realm="CAMPUS Login"');
		header('HTTP/1.0 401 Unauthorized');
		exit;
	}*/
	header('Content-Type: text/calendar; charset=UTF-8');
	die($ics);
} catch(Exception $e) {
	die('Error: ' . $e->getMessage());
}
