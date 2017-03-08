<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    /***************************** Send Mail ************************************/

    /**
     * This example shows sending a message using a local sendmail binary.
     */

    require 'PHPMailer-master/PHPMailerAutoload.php';

    $message = $_POST['message'];
    $to = $_POST['to'];
    
    if(isset($message,$to) && $message != '' && $to !=''){
        
        // subject
        $subject = 'Jira Admin -'.date(' d M Y');
        
        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        
        // Mail it
        if (!mail($to, $subject, $message, $headers)) {
            echo "Mailer Error: " ;
        } else {
            echo "Message sent!";
            #unlink($fileName);
        }
        exit;
        //Create a new PHPMailer instance
        /*$mail = new PHPMailer;
        // Set PHPMailer to use the sendmail transport
        $mail->isSMTP();                                      // Set mailer to use SMTP

        $mail->SMTPDebug = 4;

        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup server
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'tt.683.2015@gmail.com';                   // SMTP username
        $mail->Password = 'Techtreeit@123';               // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
        $mail->Port = 587;     

        //Set who the message is to be sent from
        $mail->setFrom('tt.683.2015@gmail.com', 'Jira Admin');
        //Set an alternative reply-to address
        $mail->addReplyTo('replyto@jiraadmin.com', 'Jira Admin');

        //Set who the message is to be sent to
        $mail->addAddress('raghvendra.yadav@techtreeit.com', 'Raghvendra Yadav');
        $tos = explode(',', $to);
        foreach ($tos as $to) {
            $mail->addCC($to);
        }
        $mail->addCC('pritpal.s@techtreeit.com');
        $mail->addCC('murali.kg@techtreeit.com');
        //Set the subject line
        $mail->Subject = 'Jira Admin -'.date(' d M Y');
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($message);
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        //$mail->addAttachment($fileName);

        $mail->isHTML(true); 
        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
            #unlink($fileName);
        }*/
    }