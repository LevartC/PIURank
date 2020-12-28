<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PHPMailer_Lib
{
    public function load() {
        require_once __DIR__."/PHPMailer/PHPMailerAutoload.php";
        $tmp = new PHPMailer(true);
        return $tmp;
    }
}
?>