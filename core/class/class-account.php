<?php

namespace ModuleManager;

/**
 * 
 */
class Accounts
{
    use DataValidation;
    private SEDJM $sedjm;
    public function __construct($sedjm)
    {

        $this->sedjm = $sedjm;

    }

    public function init_endpoint()
    {

        Pages::set_endpoint([
            "link" => 'create_account',
            "function" => [$this, 'create_account'],
            "http_methods" => 'POST',
            "permission" => [0]
        ]);

        Pages::set_endpoint([
            "link" => 'login_to_account',
            "function" => [$this, 'login_to_account'],
            "http_methods" => 'POST',
            "permission" => [0]
        ]);

        Pages::set_endpoint([
            "link" => 'password_reset',
            "function" => [$this, 'password_reset'],
            "http_methods" => 'POST',
            "permission" => [0]
        ]);

        Pages::set_endpoint([
            "link" => 'user_password_reset',
            "function" => [$this, 'password_reset_login_user'],
            "http_methods" => 'POST',
            "permission" => [11, 2, 3, 11]
        ]);

        Pages::set_endpoint([
            "link" => 'delete_user_account',
            "function" => [$this, 'delete_user_account'],
            "http_methods" => 'DELETE',
            "permission" => [11, 2, 3]
        ]);

    }

    /**
     * Login history
     */
    private function set_new_login($user_id, $status): void
    {

        $table = 'LoginHistory';
        $data = [
            "user_id" => $user_id,
            "login_time" => time(),
            "ip" => $_SERVER['REMOTE_ADDR'],
            "status" => \ApiStatus::CORRECT->get_bool_status()
        ];
        $this->sedjm->insert($data, $table);

    }

    /**
     * Save JWT token to database 
     * @param int $user_id
     * @param int $expire_time
     * @param array $token - token status and id 
     */
    private function save_token(int $user_id, int $expire_time): array
    {

        $table = 'Tokens';
        $data = [
            "user_id" => $user_id,
            "expire_time" => $expire_time,
            "status" => true
        ];
        $token = $this->sedjm->insert($data, $table);

        return $token;
    }

    /**
     * Register new user
     * @param array $input
     * @return array \Models\ApiModel
     */
    public function create_account($input): \Models\ApiModel
    {

        $input_nick = !empty($input['nick']) ? trim($input['nick']) : "";
        $input_password = !empty($input['password']) ? trim($input['password']) : "";
        $input_repeat_password = !empty($input['repeat_password']) ? trim($input['repeat_password']) : "";
        $input_email = !empty($input['email']) ? trim($input['email']) : "";
        $input_phone_number = !empty($input['phone_number']) ? trim($input['phone_number']) : "";
        $permission = !empty($input['permission']) ? trim($input['permission']) : 1;


        $errors = false;
        $error_details = [];

        $check_nick = $this->is_valid_nick($input_nick);

        if (!$check_nick['status']) {
            $error_details['nick_error'] = $check_nick['errors'];
            $errors = true;
        } else {
            $error_details['nick_error'] = [];
        }

        $check_password = $this->is_password_valid($input_password);

        if (!$check_password['status']) {
            $error_details['password_error'] = $check_password['errors'];
            $errors = true;
        } else {
            $error_details['password_error'] = [];
        }

        $check_password_integrity = $this->password_are_identical($input_password, $input_repeat_password);

        if (!$check_password_integrity['status']) {
            $error_details['password_error'] = array_merge($error_details['password_error'], $check_password_integrity['errors']);
            $errors = true;
        } else {
            $error_details['password_error'] = array_merge($error_details['password_error'], $check_password_integrity['errors']);
        }

        $check_mail = $this->is_valid_email($input_email);

        if (!$check_mail['status']) {
            $error_details['email_error'] = $check_mail['errors'];
            $errors = true;
        } else {
            $error_details['email_error'] = [];
        }

        if (!empty($input_phone_number)) {

            $check_password = $this->is_valid_phone_number($input_phone_number);

            if (!$check_password['status']) {
                $error_details['telephone_error'] = $check_password['errors'];
                $errors = true;
            } else {
                $error_details['telephone_error'] = [];
            }

        } else {
            $error_details['telephone_error'] = [];
        }

        $return = new \Models\ApiModel(\ApiStatus::ERROR);

        if (!$errors) {

            if (!$check_password['status']) {
                $find_error = true;
            }

            $table = 'Users';
            $data = [
                "nick" => $input_nick,
                "password" => $input_password,
                "email" => $input_email,
                "phone_number" => $input_phone_number,
                "permission" => $permission,
                "status" => 1
            ];

            $registration_state = $this->sedjm->insert($data, $table);

            if ($registration_state["status"]) {

                try {
                    \TestMail::send_welcome_mail($input_email);
                } catch (\Throwable $th) {
                    $details = [
                        "message" => $th->getMessage(),
                        "code" => $th->getCode(),
                        "file" => $th->getFile(),
                        "line" => $th->getLine()
                    ];
                    \ModuleManager\Main::set_error('Send e-mail', 'ERROR', $details);
                }

                foreach ($input['additional'] as $key => $value) {

                    $additional_table = 'UserData';
                    $additional_data = [
                        "user_id" => $registration_state["id"],
                        "field_key" => $key,
                        "value" => $value
                    ];

                    $additional_data = $this->sedjm->insert($additional_data, $additional_table);

                }

            }

            // $return["status"] = $registration_state["status"];
            $return->set_status(\ApiStatus::from($registration_state["status"]));
            // $return["data"]["id"] = $registration_state["id"];
            $return->set_message(["id" => $registration_state["id"]]);
            // $return["errors"] = null;

        } else {

            // $return["status"] = false;
            $return->set_status(\ApiStatus::ERROR);
            // $return["data"]["id"] = null;
            $return->set_error($error_details);
            // $return["errors"] = $error_details;

        }

        return $return;

    }

    /**
     * Get user data
     * @param array $input
     * @return array $output
     */
    public function get_account_data($input): array
    {

        $user_id = $input['user_id'];

        $table = "Users";
        $additionalTable = "UserData";

        $this->sedjm->set_where("user_id", $input['user_id'], "=");
        $get_data = $this->sedjm->get(["nick", "email", "phone_number"], $table);
        $additional_data = $this->sedjm->get(["field_key", "value"], $additionalTable);

        $additional = [];
        foreach ($additional_data as $key => $value) {

            $additional[$value['field_key']] = $value['value'];

        }

        $output = [
            "status" => true,
            "data" => $get_data[0],
            "additional_data" => $additional
        ];

        return $output;

    }

    /**
     * Update user data
     * @param array $input
     * @return array $output
     */
    public function update_user_data($input)
    {
        $this->sedjm->clear_all();

        // TODO: Walidacja, automatyczne wykrywanie dodatkowych pul
        $table = "Users";

        $data = [];


        if (!empty($input["nick"]))
            $data["nick"] = $input["nick"];

        if (!empty($input["email"]))
            $data["email"] = $input["email"];

        if (!empty($input["phone_number"]))
            $data["phone_number"] = $input["phone_number"];

        $this->sedjm->set_where("user_id", $input['user_id'], "=");
        $update_data = $this->sedjm->update($data, $table);

        // Dodatkowe dane
        if (!empty($input['additional'])) {

            foreach ($input['additional'] as $key => $value) {

                $this->update_additional_data($key, $value, $input['user_id']);

            }

        }


        return $update_data;

    }

    /**
     * Update user data
     * @param string $key, $value, $user_id
     * @param string $key
     * @param string $user_id
     * @return void
     */
    private function update_additional_data($key, $value, $user_id)
    {
        $additional_table = "UserData";
        $this->sedjm->clear_all();

        $data_additional = [
            "field_key" => $key,
            "value" => $value,
            "user_id" => $user_id
        ];

        $this->sedjm->set_where("user_id", $user_id, "=");
        $this->sedjm->set_where("field_key", $key, "=");
        $field_content = $this->sedjm->get(["user_id"], $additional_table);

        if (count($field_content)) {
            $this->sedjm->update($data_additional, $additional_table);
        } else {
            $this->sedjm->insert($data_additional, $additional_table);
        }

        $this->sedjm->clear_all();

    }


    /**
     * Login to account
     * @param array $input [$nick, $password]
     * @return array [$status bool, $token string, $error string]
     */
    public function login_to_account($input): \Models\ApiModel
    {

        $errors = [];
        $status = \ApiStatus::ERROR;
        $token = null;
        $user_id = null;

        $nick = trim($input['nick']);
        $password = trim($input['password']);

        if (
            !empty($nick)
            && !empty($password)
        ) {

            $table = 'Users';
            $this->sedjm->clear_where();
            $this->sedjm->set_where('email', $nick, '=', 'OR');
            $this->sedjm->set_where('nick', $nick, '=', 'OR');
            $this->sedjm->set_where('status', 1, '=');
            $nick_in_db = $this->sedjm->get(['user_id', 'nick', 'password', 'permission'], $table);

            if (count($nick_in_db) > 0) {

                $user_id = $nick_in_db[0]['user_id'];
                $hash = $nick_in_db[0]['password'];

                $password_is_correct = EncryptData::password_hash_verify($password, $hash);
                if ($password_is_correct) {

                    $expire_time = strtotime("+30 day");

                    $token_id = $this->save_token($nick_in_db[0]['user_id'], $expire_time);

                    $payload = [
                        "user_id" => $nick_in_db[0]['user_id'],
                        "nick" => $nick_in_db[0]['nick'],
                        "permission" => $nick_in_db[0]['permission'],
                        "token_id" => $token_id['id'],
                        "create_time" => time(),
                        "expire_time" => $expire_time
                    ];

                    $token = \ModuleManager\Main::$jwt->get_token($payload);
                    $nick = $nick_in_db[0]['nick'];

                    $status = \ApiStatus::CORRECT;

                } else {
                    $errors['password_is_incorrect'] = true;
                }

            } else {

                $errors['nick_not_exist'] = true;

            }

        } else {
            $errors['incomplete_input_data'] = true;
        }

        if (!empty($user_id)) {
            $this->set_new_login($user_id, $status);
        }


        $return = new \Models\ApiModel($status, ["token" => $token, "nick" => $nick], $errors);

        return $return;


    }

    /**
     * Check jwt token
     * @param array $input [token]
     * @return array [$status bool]
     */
    public function check_user_token($input): array
    {

        $token = $input["token"];

        $valid = \ModuleManager\Main::$jwt->check_token($token);

        return ["status" => $valid['status'], "data" => $valid['payload']];

    }

    /**
     * logout user
     */
    public function logout($token_id): array
    {

        $table = 'Tokens';
        $data = [
            "status" => 0
        ];
        $this->sedjm->clear_all();
        $this->sedjm->set_where("token_id", $token_id, "=");
        $token = $this->sedjm->update($data, $table);

        LocalStorage::remove_data("token", "session");
        LocalStorage::remove_data("token", "cookie");

        return ["status" => $token['status']];

    }

    /**
     * Reset user password sending mail
     */
    public function password_reset($input): \Models\ApiModel
    {
        $email = $input['email'];

        $output = new \Models\ApiModel(\ApiStatus::ERROR);

        $table = 'Users';
        $this->sedjm->clear_where();
        $this->sedjm->set_where('nick', $email, '=', 'OR');
        $this->sedjm->set_where('email', $email, '=', 'OR');
        $user_data = $this->sedjm->get(['user_id', 'nick', 'email', 'permission'], $table);

        if (count($user_data) > 0) {
            // $output['status'] = true;
            $output->set_status(\ApiStatus::CORRECT);
        } else {
            // $output['error'] = "user_doesnt_exist";
            $output->set_error(["User doesn't exist"]);
            // $output['status'] = false;
        }
        // TODO: Reset password by mail
        // MailSender::send_mail();

        // return $output;
        return $output;
    }

    /**
     * password reset if user know password
     */
    public function password_reset_login_user($input): \Models\ApiModel
    {

        $errors = [];
        $incorrect_data = false;
        $status = \ApiStatus::ERROR;
        $user_id = null;
        $table = 'Users';

        $user_id = !empty($input['user_id']) ? trim($input['user_id']) : "";
        $password = !empty($input['password']) ? trim($input['password']) : "";
        $new_password = !empty($input['new_password']) ? trim($input['new_password']) : "";
        $repeat_password = !empty($input['r_new_password']) ? trim($input['r_new_password']) : "";

        if (
            empty($nick)
            && empty($password)
            && empty($new_password)
            && empty($repeat_password)
        ) {
            $incorrect_data = true;
            $errors[] = 'incomplete_input_data';
        }


        $check_password_integrity = $this->password_are_identical($new_password, $repeat_password);

        if (!$check_password_integrity['status']) {
            $errors[] = 'password_is_not_identical';
            $incorrect_data = true;
        }


        $check_password = $this->is_password_valid($new_password);

        if (!$check_password['status']) {
            $error_details['password_error'] = $check_password['errors'];
            $incorrect_data = true;
        }


        $this->sedjm->clear_where();
        $this->sedjm->set_where('user_id', $user_id, '=');
        $nick_in_db = $this->sedjm->get(['user_id', 'nick', 'password', 'permission'], $table);

        if (count($nick_in_db) <= 0) {
            $errors[] = 'account_is_not_exist';
            $incorrect_data = true;
        }

        if (!$incorrect_data) {

            $user_id = $nick_in_db[0]['user_id'];
            $hash = $nick_in_db[0]['password'];

            $password_is_correct = EncryptData::password_hash_verify($password, $hash);
            if ($password_is_correct) {

                $this->sedjm->clear_all();
                $this->sedjm->set_where('user_id', $user_id, '=');
                $update_password = $this->sedjm->update(["password" => $new_password], $table);

                if ($update_password["status"]) {
                    $status = \ApiStatus::CORRECT;
                } else {
                    $status = \ApiStatus::ERROR;
                }

            } else {
                $status = \ApiStatus::ERROR;
                $errors[] = 'password_is_incorrect';
            }

        }

        // return ["status" => $status, "errors" => $errors];

        $output = new \Models\ApiModel($status, [], $errors);
        return $output;

    }

    /**
     * Delete account
     */
    public function delete_user_account($input): \Models\ApiModel
    {

        $errors = [];
        $incorrect_data = false;
        $status = \ApiStatus::ERROR;
        $user_id = null;
        $table = 'Users';

        $user_id = !empty($input['user_id']) ? trim($input['user_id']) : "";
        $password = !empty($input['password']) ? trim($input['password']) : "";
        $accept = !empty($input['accept']) ? $input['accept'] : false;

        if (
            empty($nick)
            && empty($password)
        ) {
            $incorrect_data = true;
            $errors[] = 'incomplete_input_data';
        }


        $this->sedjm->clear_where();
        $this->sedjm->set_where('user_id', $user_id, '=');
        $nick_in_db = $this->sedjm->get(['user_id', 'nick', 'password', 'permission'], $table);

        if (count($nick_in_db) <= 0) {
            $errors[] = 'account_is_not_exist';
            $incorrect_data = true;
        }

        if (!$incorrect_data && ($accept === true || $accept === "true")) {

            $user_id = $nick_in_db[0]['user_id'];
            $hash = $nick_in_db[0]['password'];

            $password_is_correct = EncryptData::password_hash_verify($password, $hash);
            if ($password_is_correct) {

                $this->sedjm->clear_all();
                $this->sedjm->set_where('user_id', $user_id, '=');
                $update_password = $this->sedjm->update(["status" => 3], $table);

                if ($update_password["status"]) {
                    $status = \ApiStatus::CORRECT;
                } else {
                    $status = \ApiStatus::ERROR;
                }

            } else {
                $status = \ApiStatus::ERROR;
                $errors[] = 'Password is incorrect';
            }

        }

        if ($accept === false || $accept === "false") {
            $errors[] = 'User is not accept terms';
        }

        // return ["status" => $status, "errors" => $errors, "accept" => $accept];
        $output = new \Models\ApiModel($status, ["accept" => $accept], $errors);
        return $output;

    }



}