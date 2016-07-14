<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PHPMailer - GMail SMTP test</title>
</head>
<body>
<?php
$array = json_decode(filter_input(INPUT_POST, 'jsonize'));
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Europe/Budapest');

//require '../PHPMailerAutoload.php';
require './PHPMailerAutoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer();

//Custome name
$customerName = $array[1][0]->customername;

//Recognize the template language
switch ($array[1][0]->lang) {
    case 'hun':
        $lang = 'hun';
        break;
    case 'cze':
        $lang = 'cze';
        break;
    case 'sky':
        $lang = 'sky';
        break;
    case 'rom':
        $lang = 'rom';
        break;
    case 'hrv':
        $lang = 'hrv';
        break;
    default :
        $lang = 'hun';
}

//Open proper templatees
//$template=file_get_contents("./templates/" . $lang . "_subjtemplate");
//$template_text = file_get_contents("./templates/" . $lang . "_template");
$template_text = $array[1][0]->message;

//Replace tokens in template
switch ($array[1][0]->status) {
    case 'start':
        $status = 'megkezdtük';
	$subj_status = 'megkezdése';
        break;
    case 'end':
        $status = 'befejeztük';
	$subj_status = 'befejezése';
        break;
}


switch ($array[1][0]->products) {
    case 'sz7':
            $product = 'Szerviz 7';
            $company = '3 Sz-s Kft.';
            $sender = 'zk@3szs.hu';
            $mail->setFrom($sender, $company);
            $mail->addReplyTo($sender, $company);
        break;
    case 'm':
            $product = 'Modupro';
            $company = 'Indas Kft.';
            $sender = 'info@indas.hu';
            $mail->setFrom($sender, $company);
            $mail->addReplyTo($sender, $company);
        break;
    case 'mu':
            $product = 'Modupro ULTIMATE';
            $company = 'Indas Kft.';
            $sender = 'info@indas.hu';
            $mail->setFrom($sender, $company);
            $mail->addReplyTo($sender, $company);
        break;
}
switch ($array[1][0]->activity) {
    case 'repair':
            $activity = 'hibajavítás';
        break;
    case 'support':
            $activity = 'segítségnyújtás';
        break;
}

$template = str_replace(
        array(
            '[date]',
            '[company]',
            '[status]',
            '[activity]'),
        array(
            $array[1][0]->datetime,
            $company,
            $status,
            $activity),
        $template_text);
//subject
$subject = $customerName . ' - ' . $product . ' program ' . $activity . ' ' . $subj_status;

/*$target_file = fopen('./contents.html', 'w');
fputs($target_file, $template);
fclose($target_file);*/

//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = 'mail.intra';
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 25;
//Set the encryption system to use - ssl (deprecated) or tls
//$mail->SMTPSecure = '';
//Whether to use SMTP authentication
$mail->SMTPAuth = false;
//Username to use for SMTP authentication - use full email address for gmail
//$mail->Username = "";
//Password to use for SMTP authentication
//$mail->Password = "";
/*//Set who the message is to be sent from
Korábban már be lett állítva $mail->setFrom("a@b.hu, ceg");
//Set an alternative reply-to address
$mail->addReplyTo($sender, $company);
//Set who the message is to be sent to
//$mail->addAddress('oze.peter@3szs.hu');
Másolatot kap
if(isset($_POST['cc'])){
$mail->addCC(filter_input(INPUT_POST, 'cc'));
}
//Rejtett másolatot kap
if(isset($_POST['bcc'])){
$mail->addBCC(filter_input(INPUT_POST, 'bcc'));
}*/

for ($i = 0; $i <= (sizeof($array[0])-1); $i++) {
    
    switch ($array[0][$i]->type) {
        case 'to':
                $mail->addAddress($array[0][$i]->address);
            break;
        case 'cc':
                $mail->addCC($array[0][$i]->address);
            break;
        case 'bcc':
                $mail->addBCC($array[0][$i]->address);
            break;
    }
}
//Set the subject line
$mail->Subject = $subject;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
$mail->msgHTML($template);
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.gif');

//send the message, check for errors
if (!$mail->send()) {
    echo "Nem sikerült elküldeni a levelet: " . $mail->ErrorInfo;
} else {
    echo "Üzenet elküldve!";
}


?>
</body>
</html>
