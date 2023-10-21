<?php

namespace ModuleManager;

trait DataValidation
{
    /**
     * Check password strange
     * @param string $password
     * @return array [$status, $errors]
     */
    public function is_password_valid($password): array
    {

        $status = true;
        $errors = [];

        $min_length = 8;

        if (strlen($password) < $min_length) {
            $status = false;
            $errors[] = 'password_is_to_short';
        }

        if (!preg_match('/\d/', $password)) {
            $status = false;
            $errors[] = 'password_has_no_number';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $status = false;
            $errors[] = 'password_does_not_contain_capital_letters';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $status = false;
            $errors[] = 'password_does_not_contain_lower_case_letters';
        }

        if (!preg_match('/[^\w\s]/', $password)) {
            $status = false;
            $errors[] = 'password_has_no_special_characters';
        }

        return ["status" => $status, "errors" => $errors];

    }

    /**
     * Check email correct
     * @param string $email
     * @return array [$status, $errors]
     */
    public function is_valid_email($email)
    {
        $status = true;
        $errors = [];

        if (empty($email)) {
            $status = false;
            $errors[] = 'email_is_empty';
        }

        // Sprawdzenie, czy adres e-mail ma poprawny format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $status = false;
            $errors[] = 'email_is_in_the_wrong_format';
        }

        // Sprawdzenie, czy adres e-mail ma domenę zarejestrowaną w DNS
        $domain = explode('@', $email);

        if (!empty($domain[1])) {

            if (!checkdnsrr($domain[1], 'MX')) {
                $status = false;
                $errors[] = 'domain_is_not_registered';
            }

        } else {
            $status = false;
            $errors[] = 'domain_is_not_registered';
        }




        if ($this->is_email_already_exist($email)) {
            $status = false;
            $errors[] = 'email_already_exists_in_the_database';
        }

        return ["status" => $status, "errors" => $errors];
    }

    public function is_email_already_exist($email): bool
    {
        $table = 'Users';
        $this->sedjm->clear_where();
        $this->sedjm->set_where('email', $email, '=');
        $email_in_db = $this->sedjm->get(['email'], $table);

        if (count($email_in_db) > 0) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Check email correct
     * @param string $phone_number
     * @return array [$status, $errors]
     */
    public function is_valid_phone_number($phone_number)
    {
        $status = true;
        $errors = [];

        $phone_number = preg_replace('/[^0-9]/', '', $phone_number);

        if (strlen($phone_number) < 9 || strlen($phone_number) > 15) {
            $status = false;
            $errors[] = 'phone_number_is_of_the_wrong_length';
        }

        return ["status" => $status, "errors" => $errors];
    }

    /**
     * Check nick correct
     * @param string $nick
     * @return array [$status, $errors]
     */
    public function is_valid_nick($nick)
    {
        $status = true;
        $errors = [];

        $min_length = 4;

        if (strlen($nick) < $min_length) {
            $status = false;
            $errors[] = 'nick_is_too_short';
        }

        $table = 'Users';

        $this->sedjm->clear_where();
        $this->sedjm->set_where('nick', $nick, '=');

        $nick_in_db = $this->sedjm->get(['nick'], $table);

        if (count($nick_in_db) > 0) {
            $status = false;
            $errors[] = 'nick_already_exists_in_the_database';
        }


        return ["status" => $status, "errors" => $errors];
    }

    /**
     * Check that the passwords are identical
     * @param string $password
     * @param string $repeated_password
     * @return array [$status, $errors]
     */
    public function password_are_identical($password, $repeated_password): array
    {

        $errors = [];

        if ($password === $repeated_password) {

            $status = true;

        } else {

            $errors[] = 'passwords_are_not_identical';
            $status = false;

        }

        return ["status" => $status, "errors" => $errors];


    }
}