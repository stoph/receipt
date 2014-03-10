<?php

require 'vendor/autoload.php';

$file = $_GET['file'];
$full_file = getcwd().'/'.$file;
$ext = strtolower(substr(strrchr($full_file,"."),1));

if (file_exists($full_file) && is_readable($full_file)) {

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
			$type = 'image';
			break;
		case "png":
			$mime = "image/png";
			$type = 'image';
			break;
		case "jpeg":
		case "jpg":
			$mime = "image/jpg";
			$type = 'image';
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
			$type = NULL;
	}

	if ($mime) {
		header('Content-type: '.$mime);
		header('Content-length: '.filesize($full_file));
		$fh = @fopen($full_file, 'rb');
		if ($fh) {
			fpassthru($fh);
			send_confirmation($file, $type);
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

function send_confirmation($file, $type) {

	if (isset($_GET['no_receipt'])) {
		return;
	}
	$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

	$mailer = Swift_Mailer::newInstance($transport);

	// Create the message
	switch ($type) {
		case 'image':
			$link = "<img src='http://domain.com/$file?no_receipt'>";
			break;
		default:
			$link = $file;
	}

	$message = Swift_Message::newInstance()
		->setSubject("File viewed - $file")
		->setFrom(array("you@email.com" => "Delivery Receipt"))
		->setTo(array("you@email.com"))
		->setBody("A file was just viewed - $file")
		->addPart("A file was just viewed <br><br> $link", "text/html")
	;

	$result = $mailer->send($message, $failures);
}