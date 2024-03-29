<?php

require_once("./core/class/class-forms.php");

function login_to_panel($nick, $password, $key_password)
{
    global $main;
    $data = [
        "nick" => htmlspecialchars($nick),
        "password" => htmlspecialchars($password),
        "key_password" => htmlspecialchars($key_password)
    ];

    $login_status = $main->accounts->login_to_account($data);
    if ($login_status->get_status() == \ApiStatus::CORRECT) {

        try {
            ModuleManager\LocalStorage::set_data("token", $login_status->get_message()["token"], 'session', true);
            ModuleManager\LocalStorage::set_data("token", $login_status->get_message()["token"], 'cookie', true);
        } catch (\Throwable $th) {
            $details = [
                "message" => $th->getMessage(),
                "code" => $th->getCode(),
                "file" => $th->getFile(),
                "line" => $th->getLine()
            ];
            \ModuleManager\Main::set_error('Token retrieval error', 'ERROR', $details);
        }

        header("Location: /panel");
    } else {
        // $errors = $login_status['errors'];
    }

    return $login_status;
}

function login_form()
{
    global $main;
    $form = new ModuleManager\Forms\Forms();

    $errors = [];

    if (!empty($_POST['active'])) {

        $login = login_to_panel($_POST["nick"], $_POST["password"], $_POST['key_password']);
        foreach ($login->get_error() as $key => $value) {
            switch ($key) {
                case 'password_is_incorrect':
                    $errors[] = "Podano niepoprawne hasło";
                    break;
                case 'nick_not_exist':
                    $errors[] = "Podany nick jest niepoprawny";
                    break;
                default:
                    # code...
                    break;
            }
        }
    }

    $form->set_data([
        "key" => "nick",
        "name" => "Adres e-mail",
        "type" => "text",
        "id" => "nick"
    ]);

    $form->set_data([
        "key" => "password",
        "name" => "Wpisz hasło",
        "type" => "password",
        "id" => "password"
    ]);

    $form->set_data([
        "key" => "key_password",
        "name" => "Hasło klucza",
        "type" => "password",
        "id" => "key_password"
    ]);

    return $form->get_form("Zaloguj się", "Zaloguj się", $errors);
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "login_form",
        "function" => "login_form"
    ]
);

function delete_account_form()
{
    global $main;
    $form = new ModuleManager\Forms\Forms();

    $errors = [];

    if (!empty($_POST['active'])) {

    }

    $form->set_data([
        "key" => "nick",
        "name" => "Adres e-mail",
        "type" => "text",
        "id" => "nick"
    ]);

    $form->set_data([
        "key" => "password",
        "name" => "Wpisz hasło",
        "type" => "password",
        "id" => "password"
    ]);

    $form->set_data([
        "key" => "checkbox",
        "name" => "Na pewno chcesz usunąć konto? (proces będzie nieodwracalny)",
        "description" => "Na pewno chcesz usunąć konto? (proces będzie nieodwracalny)",
        "type" => "checkbox"
    ]);

    return $form->get_form("Usuń konto", "Usuń", $errors);
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "delete_account_form",
        "function" => "delete_account_form"
    ]
);

function register_to_panel($nick, $email, $phone, $password, $repeat_password, $access, $name, $surname, $key_password)
{
    global $main;

    $data = [
        "nick" => $nick,
        "password" => $password,
        "repeat_password" => $repeat_password,
        "encrypt_password" => $key_password,
        "email" => $email,
        "phone_number" => $phone,
        "additional" => [
            "name" => $name,
            "surname" => $surname
        ]
    ];

    $register_status = $main->accounts->create_account($data);

    if ($register_status->get_status() == \ApiStatus::CORRECT) {
        $login_status = login_to_panel($nick, $password, $key_password);
    }
    return $register_status;
}

function registration_form()
{
    $errors = [];
    $form = new ModuleManager\Forms\Forms();

    if (!empty($_POST['active'])) {
        if (

            !empty($_POST['nick'])
            && !empty($_POST['email'])
            && !empty($_POST['phone'])
            && !empty($_POST['password'])
            && !empty($_POST['repeat_password'])
            && !empty($_POST['name'])
            && !empty($_POST['surname'])
            && !empty($_POST['encrypt_data_password'])
        ) {

            $login = register_to_panel(
                $_POST['nick'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['password'],
                $_POST['repeat_password'],
                $_POST['access'],
                $_POST['name'],
                $_POST['surname'],
                $_POST['encrypt_data_password']
            );

            foreach ($login->get_error() as $key => $value) {
                $errors[] = $key;
                // switch ($key) {
                //     case 'password_is_incorrect':
                //         $errors[] = "Podano niepoprawne hasło";
                //         break;
                //     case 'nick_not_exist':
                //         $errors[] = "Podany nick jest niepoprawny";
                //         break;
                //     default:
                //         # code...
                //         break;
                // }
            }
        } else {
            $errors[] = "Uzupełnij wszystkie dane";
        }

    }

    $form->set_data([
        "key" => "nick",
        "name" => "Nick",
        "type" => "input"
    ]);

    $form->set_data([
        "key" => "email",
        "name" => "E-mail",
        "type" => "email"
    ]);

    $form->set_data([
        "key" => "phone",
        "name" => "phone",
        "type" => "telephone"
    ]);

    $form->set_data([
        "key" => "name",
        "name" => "Your name",
        "type" => "text"
    ]);

    $form->set_data([
        "key" => "surname",
        "name" => "Your surname",
        "type" => "text"
    ]);

    $form->set_data([
        "key" => "password",
        "name" => "account Password",
        "type" => "password"
    ]);

    $form->set_data([
        "key" => "repeat_password",
        "name" => "Repet account password",
        "type" => "password"
    ]);

    $form->set_data([
        "key" => "encrypt_data_password",
        "name" => "Hasło klucza",
        "type" => "password"
    ]);

    $form->set_data([
        "key" => "access",
        "name" => "access box",
        "type" => "checkbox",
        "description" => "Akceptuje politykę prywatności"
    ]);

    return $form->get_form("Registration", "Registration", $errors);
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "registration_form",
        "function" => "registration_form"
    ]
);

function module_content()
{
    global $main;

    return $main->pages::$module;

}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "module_content",
        "function" => "module_content"
    ]
);


function module_list()
{
    global $main;

    if (\ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {

        $module_list = $main->pages->get_modules_list(true);

        $toggle_icon = file_get_contents(__DIR__ . "/../../panel-template/img/toggle_icon.svg");
        $html = "<ul>";
        foreach ($module_list as $value) {
            if ($value['show']) {

                if ($main->page_name == $value['link'])
                    $active = "active";
                else
                    $active = "";

                if (!empty($value['icon'])) {
                    $icon = file_get_contents(__DIR__ . "/../../modules/" . $value['icon']);
                } else {
                    $icon = "";
                }

                $child = "";
                if (!empty($value['child'])) {

                    $visible_child = false;

                    foreach ($value['child'] as $key => $child_value) {
                        if ($child_value['show'] == true)
                            $visible_child = true;
                    }

                    if ($visible_child) {
                        $child .= "<ul>";
                        foreach ($value['child'] as $key => $child_value) {
                            if ($main->page_name == $child_value['link']) {
                                $sub_active = "active";
                                $active = "active";
                            } else {
                                $sub_active = "";
                            }

                            if (!empty($child_value['show']) || $child_value['show'] == true) {

                                $child .= "<li class='" . $sub_active . "'>";
                                $child .= "<a href='/" . ModuleManager\Config::get_config()["pages"]->project . "/" . \ModuleManager\Pages::$project->get_project_id() . "/" . $value['link'] . "/" . $child_value['link'] . "'>";
                                $child .= "<span class='icon'></span>";
                                $child .= "<span>" . $child_value['name'] . "</span>";

                                $child .= "</a>";
                                $child .= "</li>";

                            }

                        }
                        $child .= "</ul>";
                    }
                }

                $html .= "<li class='" . $active . "'>";
                $html .= "<div class='parent'>";
                $html .= "<a href='/" . ModuleManager\Config::get_config()["pages"]->project . "/" . \ModuleManager\Pages::$project->get_project_id() . "/" . $value['link'] . "'>";
                $html .= "<span class='border'></span>";
                $html .= "<span class='icon'>" . $icon . "</span>";
                $html .= "<span class='name'>" . $value['name'] . "</span>";
                $html .= "</a>";
                $html .= "<span class='toggle'>";
                if (!empty($child))
                    $html .= $toggle_icon;
                $html .= "</span>";
                $html .= "</div>";
                $html .= "<div class='toggle_nav'>";

                if (!empty($child)) {
                    $html .= $child;
                }

                $html .= "</div>";
                $html .= "</li>";
            }
        }
        $html .= "</ul>";

        return $html;

    }
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "module_list",
        "function" => "module_list"
    ]
);

function page_name()
{
    global $main;
    $module_list = $main->pages->get_modules_list();

    foreach ($module_list as $value) {

        if ($main->page_name == $value['link']) {
            return $value['name'];
        }

        if (!empty($value['child'])) {

            foreach ($value['child'] as $child_value) {
                if ($main->page_name == $child_value['link']) {
                    return $child_value['name'];
                }
            }

        }
    }
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "page_name",
        "function" => "page_name"
    ]
);

function page_description()
{
    return ModuleManager\Config::get_config()["description"];
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "page_description",
        "function" => "page_description"
    ]
);

function main_patch()
{
    return ModuleManager\Config::get_config()['link'];
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "main_patch",
        "function" => "main_patch"
    ]
);

function logout()
{
    $icon = file_get_contents(__DIR__ . "/../../panel-template/img/tabler_logout.svg");
    return "<a href='/" . ModuleManager\Config::get_config()["pages"]->panel . "/logout' aria-label='Logout'>
     Wyloguj <span>" . $icon . "</span>
    </a>";
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "logout",
        "function" => "logout"
    ]
);

function module_patch()
{
    global $main;

    $icon = file_get_contents(__DIR__ . "/../../panel-template/img/icon-right.svg");
    $module_list = $main->pages->get_modules_list();
    $actual = "";

    $tabs = [];
    foreach ($module_list as $value) {

        $tabs[] = "<a href='/" . ModuleManager\Config::get_config()["pages"]->panel . "/" . $value['link'] . "'>" . $value['name'] . "</a>";

        if ($main->page_name == $value['link']) {
            $actual = $icon . "<a href='/" . ModuleManager\Config::get_config()["pages"]->panel . "/" . $value['link'] . "'>" . $value['name'] . "</a>";
            break;
        } else {

            if (!empty($value['child'])) {
                foreach ($value['child'] as $key => $child_value) {
                    if ($main->page_name == $child_value['link']) {

                        $actual = $icon . "<a href='/" . ModuleManager\Config::get_config()["pages"]->panel . "/" . $value['link'] . "'>" . $value['name'] . "</a>" . $icon . "<a href='/" . ModuleManager\Config::get_config()["pages"]->panel . "/" . $value['link'] . "/" . $child_value['link'] . "'>" . $child_value['name'] . "</a>";

                        break;
                    }
                }
            }

        }

    }


    return "<a href='/" . ModuleManager\Config::get_config()["pages"]->panel . "/'>Home</a>" . $actual;
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "module_patch",
        "function" => "module_patch"
    ]
);

function profile()
{
    return "<div class='profile'></div>";
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "profile",
        "function" => "profile"
    ]
);

function show_warning()
{
    $output = "";

    foreach (\ModuleManager\Main::$warning_list as $key => $value) {
        $output .= "<div>";
        $output .= "<p><b>" . $value["name"] . "</b></p>";
        if (!empty($value["details"])) {
            $output .= "<p>" . $value["details"] . "</p>";
        }
        $output .= "</div>";
    }

    return $output;
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "info_box",
        "function" => "show_warning"
    ]
);

function error_list()
{
    return "<div class='errors'></div>";
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "error_list",
        "function" => "error_list"
    ]
);

function insert_svg($path)
{
    return file_get_contents(__DIR__ . "/../../" . $path[0]);
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "insert_svg",
        "function" => "insert_svg"
    ]
);

function nonce_id()
{
    return \NONCE;
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "nonce",
        "function" => "nonce_id"
    ]
);

function inject_javascript()
{
    return \InjectJavaScript::inject_script();
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "inject_javascript",
        "function" => "inject_javascript"
    ]
);

function inject_style()
{
    return \InjectStyles::inject_style();
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "inject_style",
        "function" => "inject_style"
    ]
);

function put_token()
{
    $token = \ModuleManager\LocalStorage::get_data("token", 'session', true);
    if (empty($token)) {
        $token = \ModuleManager\LocalStorage::get_data("token", 'cookie', true);
        \ModuleManager\LocalStorage::set_data("token", $token, 'session', true);
    }

    return $token;
}

ModuleManager\DataBinder::set_binder(
    [
        "key" => "token",
        "function" => "put_token"
    ]
);

function project_list()
{
    global $main;

    $project = new ProjectsRepository;
    $projects_id = $project->get_user_projects(\ModuleManager\Main::$token['payload']->user_id);

    $output = "";

    foreach ($projects_id as $project_id) {

        try {
            $project_data = $project->get_by_id($project_id);
            if (!empty($project) && $project_data[0]['status'] == \ProjectStatus::ACTIVE->value) {

                $img_src = '/' . $project_data[0]['photo_url'] . '" alt="' . $project_data[0]['name'] . '';
                $link = '/' . ModuleManager\Config::get_config()["pages"]->project . "/" . $project_data[0]['project_id'];
                if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {
                    $active = $project_data[0]['project_id'] == ModuleManager\Pages::$project->get_project_id() ? "active" : "";
                } else {
                    $active = "";
                }
                $output .= '
                <a href="' . $link . '">
                    <div class="project_box ' . $active . '">
                        <img src="' . $img_src . '"/>
                    </div>
                </a>
                ';

            }
        } catch (\Throwable $th) {
            var_dump($th->__toString());
        }

    }

    return $output;
}

\ModuleManager\DataBinder::set_binder(
    [
        "key" => "project_list",
        "function" => "project_list"
    ]
);

function put_base_link()
{
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    return $link;
}

\ModuleManager\DataBinder::set_binder(
    [
        "key" => "base_link",
        "function" => "put_base_link"
    ]
);

function temp_key()
{
    return $_SESSION['tmp_key'];
}

\ModuleManager\DataBinder::set_binder(
    [
        "key" => "temp_key",
        "function" => "temp_key"
    ]
);

function project_patch()
{
    return ModuleManager\Pages::$project->get_name();
}

\ModuleManager\DataBinder::set_binder(
    [
        "key" => "project_patch",
        "function" => "project_patch"
    ]
);