<?php

namespace Users\Controller;

class UsersController
{


    private $data;

    public function init_page()
    {

        $main_page = [
            "name" => "Edit profile",
            "link" => "edit_your_account",
            "function" => [$this, "edit_your_account"],
            "permission" => [1, 11],
            "status" => true,
            "icon" => basename(__DIR__) . "/assets/img/icon.svg",
            "position" => 98,
            "belongs_to_project" => false
        ];
        \ModuleManager\Pages::set_modules($main_page);

    }

    public function edit_your_account()
    {
        global $main;
        $user_id = \ModuleManager\Main::$token['payload']->user_id;
        $account = new \ModuleManager\Accounts($main->sedjm);

        $form = new \ModuleManager\Forms\Forms();
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $data = [
                "user_id" => $user_id,
                "nick" => $_POST["nick"],
                "email" => $_POST["email"],
                "phone_number" => $_POST["phone_number"],
                "additional" => [
                    "name" => $_POST["name"],
                    "surname" => $_POST["surname"]
                ]
            ];

            $results = $account->update_user_data($data);
            var_dump($results);
        }

        $user_data = $account->get_account_data(["user_id" => $user_id]);

        $user_additional_data = $user_data['additional_data'];
        $user_data = $user_data['data'];

        $form->set_data([
            "key" => "nick",
            "name" => "Nick",
            "type" => "text",
            "value" => !empty($user_data["nick"]) ? $user_data["nick"] : ""
        ]);

        $form->set_data([
            "key" => "email",
            "name" => "Adres e-mail",
            "type" => "email",
            "value" => !empty($user_data["email"]) ? $user_data["email"] : ""
        ]);

        $form->set_data([
            "key" => "phone_number",
            "name" => "Numer telefonu",
            "type" => "telephone",
            "value" => !empty($user_data["phone_number"]) ? $user_data["phone_number"] : ""
        ]);

        $form->set_data([
            "key" => "name",
            "name" => "Imię",
            "type" => "text",
            "value" => !empty($user_additional_data["name"]) ? $user_additional_data["name"] : ""
        ]);

        $form->set_data([
            "key" => "surname",
            "name" => "Nazwisko",
            "type" => "text",
            "value" => !empty($user_additional_data["surname"]) ? $user_additional_data["surname"] : ""
        ]);

        return $form->get_form("Edit profile", "update", $errors);
    }

}
