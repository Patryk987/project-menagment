<?php

namespace ModuleManager;

/**
 * 
 */

class MailSender
{

    private $to;
    private $subject;
    private $data;

    public function __construct($to, $subject, $data = [])
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->data = $data;
    }

    public function send_mail()
    {
        $message = static::get_mail_format();

        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';

        // Additional headers
        $headers[] = 'From: <web@foxstudio.eu>';

        if (mail($this->to, $this->subject, $message, implode("\r\n", $headers))) {
            return ["status" => true];
        } else {
            return ["status" => false];

        }
    }

    private function get_mail_format(): string
    {

        $link = './panel-template/html/mail.html';
        $mail = file_get_contents($link, true);
        $pattern = '/(\{{)(.*)(\}})/i';

        $mail = preg_replace_callback($pattern, [$this, 'load_data'], $mail);


        return $mail;

    }

    private function load_data($value)
    {
        $this->data['subject'] = $this->subject;
        $this->data['main_link'] = \ModuleManager\Config::get_config()['link'];
        $value = $value[2];

        if (!empty($this->data[$value])) {
            return $this->data[$value];

        } else {

            return "";
        }

    }


}