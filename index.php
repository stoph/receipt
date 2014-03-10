<?php

require 'vendor/autoload.php';

$files_folder = getcwd().'/files/';

$file = $files_folder.$_GET['file'];
if (file_exists($file) && is_readable($file)) {

	$filename = basename($file);
	$ext = strtolower(substr(strrchr($filename,"."),1));

	switch( $ext ) {
		case "pdf":
			$mime = "application/pdf";
			break;
		case "zip":
			$mime = "application/zip";
			break;
		case "doc":
			$mime = "application/msword";
			break;
		case "xls":
			$mime = "application/vnd.ms-excel";
			break;
		case "ppt":
			$mime = "application/vnd.ms-powerpoint";
			break;
		case "gif":
			$mime = "image/gif";
			break;
		case "png":
			$mime = "image/png";
			break;
		case "jpeg":
		case "jpg":
			$mime = "image/jpg";
			break;
		case "mp3":
			$mime = "audio/mpeg";
			break;
		case "wav":
			$mime = "audio/x-wav";
			break;
		case "mpeg":
		case "mpg":
		case "mpe":
			$mime = "video/mpeg";
			break;
		case "mov":
			$mime = "video/quicktime";
			break;
		case "avi":
			$mime = "video/x-msvideo";
			break;
		default:
			$mime = FALSE;
	}

	if ($mime) {
		header('Content-type: '.$mime);
		header('Content-length: '.filesize($file));
		$fh = @fopen($file, 'rb');
		if ($fh) {
			fpassthru($fh);
			send_confirmation($file);
			exit;
		}
	} else {
		header("HTTP/1.0 404 Not Found");
		exit;
	}
} else {
	header("HTTP/1.0 404 Not Found");
	exit;
}

function send_confirmation($file) {

	$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

	$mailer = Swift_Mailer::newInstance($transport);

	// Create the message
	$message = Swift_Message::newInstance()
		->setSubject('File viewed')
		->setFrom(array('noreply@stoph.me' => 'Delivery Confirmation'))
		->setTo(array('christoph.khouri@gmail.com'))
		->setBody("A file was just viewed - $file")
		->addPart("A file was just viewed - <b>$file</b>", 'text/html')
	;


	$result = $mailer->send($message, $failures);
}