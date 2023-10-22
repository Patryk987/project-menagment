<?php

class TestMail
{
    function __construct()
    {

        // $main_page = [
        //     "name" => "Powiadomienia",
        //     "link" => "send_mail",
        //     "function" => [$this, "send_mail"],
        //     "permission" => [0],
        //     "status" => true,
        //     "icon" => basename(__DIR__) . "/assets/img/icon.svg"
        // ];
        // ModuleManager\Pages::set_modules($main_page);

        $api = [
            "link" => 'send_mail',
            "function" => [$this, 'send_mail'],
            "http_methods" => "GET",
            "permission" => [0]
        ];

        \ModuleManager\Pages::set_endpoint($api);
    }

    public function send_mail($input)
    {
        $data = [
            "content" => "Witaj,
                <br><br>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                dolore magna
                aliqua.
                <br><br>
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat.
                <br><br>
                Dziękujemy",
            "title" => "Witamy w Aplikacji!"
        ];
        $x = new \ModuleManager\MailSender("web@foxstudio.eu", "TEST", $data);
        $send = $x->send_mail();

        return $send;

    }

    public static function send_welcome_mail($user_email)
    {
        $data = [
            "content" => "Witaj,
                <br><br>
                Dziękujemy za zarejestrowanie się w aplikacji!
                <br><br>
                Dziękujemy",
            "title" => "Witamy w Aplikacji"
        ];
        $x = new \ModuleManager\MailSender($user_email, "Witaj!", $data);
        $send = $x->send_mail();

        return $send;

    }

}

new TestMail;