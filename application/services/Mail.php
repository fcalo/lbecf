<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mail
 *
 * @author fer
 */
class Service_Mail {
    //put your code here
    public static function sendMail($to, $subject,$body){
        //now lets send the validation token by email to confirm the user email
        $mail = new Zend_Mail ( );
        $footer='<br /><br />_______________________________<br />La Butaca Escarlata';
        $mail->setBodyHtml($body.$footer);
        $mail->setFrom ('noresponder@labutacaescarlata.com', 'labutacaescarlata.com' );
        $mail->addTo($to);
        $mail->setSubject($subject);
        $mail->send();
    }
}
?>
