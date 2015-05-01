<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail extends CI_Controller {

	public function send()
	{
		$to='refazul.refat@gmail.com';
		$subject='Activation';
		$name='Refazul Refat';
		$body='Hi, Refazul Refat';
		
		header("Access-Control-Allow-Origin: *");
		require_once('PHPMailerAutoload.php');
		$Mail = new PHPMailer();
		$Mail->IsSMTP(); 																		// Use SMTP
		$Mail->Host        = "mail.cotfield.com"; 											// Sets SMTP server
		//$Mail->SMTPDebug   = 2; 																// 2 to enable SMTP debug information
		$Mail->SMTPAuth    = TRUE; 																// Enable SMTP Authentication
		$Mail->SMTPSecure  = "tls"; 															// Secure Connection
		$Mail->Port        = 587; 																// set the SMTP port
		$Mail->Username    = 'admin@cotfield.com'; 											// SMTP account User
		$Mail->Password    = 'CodeIsTheLaw007'; 														// SMTP account Password
		$Mail->Priority    = 1; 																// Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
		$Mail->CharSet     = 'UTF-8';
		$Mail->Encoding    = '8bit';
		$Mail->Subject     = $subject;
		$Mail->ContentType = 'text/html; charset=utf-8\r\n';
		$Mail->AddReplyTo    ('noreply@muktovabna.net','Muktovabna Team');  
		$Mail->SetFrom       ('noreply@muktovabna.net','Muktovabna Team');
	
		$Mail->WordWrap    = 900; 																// RFC 2822 Compliant for Max 998 characters per line

		$Mail->AddAddress( $to , $name); 														// To, Name
		$Mail->isHTML( TRUE );
		$Mail->Body	       = $body;
		//$Mail->AltBody = $MessageTEXT;														// Who cares
		$Mail->Send();
		$Mail->SmtpClose();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */