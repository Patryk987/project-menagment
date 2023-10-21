<?php

namespace ModuleManager\Forms;


class Forms
{

    protected $data = [];
    protected $table, $id_field;

    public $form_html;

    public function __construct($id_field = null)
    {

        if (!empty($_SESSION['CSRF_token']) && $_SERVER["REQUEST_METHOD"] == "POST") {

            if ($_SESSION['CSRF_token'] != $_POST['CSRF_token']) {
                \ModuleManager\Main::set_error('CSRF attack detected', 'ERROR');
                die("Redirect failed. Please click on this link: <a href='/'>home</a>");
            }

        } else {
            $_SESSION['CSRF_token'] = uniqid("form");
        }

        $this->id_field = $id_field;

    }

    private function select($values)
    {
        $html = "<label>";
        $html .= "<p>" . $values['name'] . "</p>";
        $html .= '<select name="' . $values['key'] . '" id="" style="color: black !important">';

        foreach ($values['options'] as $key => $value) {
            if ($values['value'] == $key) {
                $html .= "<option value='$key' selected>$value</option>";
            } else {
                $html .= "<option value='$key'>$value</option>";
            }
        }

        $html .= '</select>';
        $html .= "</label>";

        return $html;
    }
    private function input($values)
    {

        if (!empty($_POST[$values['key']])) {
            $value = htmlspecialchars($_POST[$values['key']]);
        } else {
            $value = "";
        }

        $html = "<label>";
        $html .= "<p>" . $values['name'] . "</p>";
        $html .= "<input type='" . $values['type'] . "' placeholder='" . $values['name'] . "' name='" . $values['key'] . "'  value='" . (!empty($_POST[$values['key']]) ? $value : $values['value']) . "'>";
        $html .= "</label>";

        return $html;
    }
    private function textarea($values)
    {

        if (!empty($_POST[$values['key']])) {
            $value = htmlspecialchars($_POST[$values['key']]);
        } else {
            $value = "";
        }

        $html = "<label>";
        $html .= "<p>" . $values['name'] . "</p>";
        $html .= "<textarea name='" . $values['key'] . "'>" . (!empty($_POST[$values['key']]) ? $value : $values['value']) . "</textarea>";
        $html .= "</label>";

        return $html;
    }
    private function checkbox($values)
    {
        $html = "<label>";
        $html .= "<table>";
        $html .= "<tr>";
        $html .= "<td style='width: 10px'>";
        $html .= "<input type='checkbox' name='" . $values['key'] . "'   value='" . ($values['value']) . "'>";
        $html .= "</td>";
        $html .= "<td>" . $values['description'] . "</td>";
        $html .= "</tr>";
        $html .= "</table>";
        $html .= "</label>";

        return $html;
    }
    private function checkbox_list($values)
    {
        $html = "<div class='checkbox-list'>";

        $html .= "<p>" . $values['name'] . "</p>";

        foreach ($values['options'] as $key => $value) {
            // if ($values['value'] == $key) {
            //     $html .= "<option value='$key' selected>$value</option>";
            // } else {
            //     $html .= "<option value='$key'>$value</option>";
            // }
            $html .= "<label>";
            $html .= "<table>";
            $html .= "<tr>";
            $html .= "<td style='width: 10px'>";
            $html .= "<input type='checkbox' name='" . $values['key'] . "'  value='" . $key . "'>";
            $html .= "</td>";
            $html .= "<td>" . $value . "</td>";
            $html .= "</tr>";
            $html .= "</table>";
            $html .= "</label>";
        }


        $html .= "</div>";

        return $html;
    }

    private function multi($values)
    {
        $html = "<div class='multi-list'>";


        $html .= "<h3>" . $values['name'] . "</h3>";

        $html .= "<div class='object_group'>";




        $count = 1;
        if (!empty($_POST[$values['key']])) {
            if (count($_POST[$values['key']]) > $count)
                $count = count($_POST[$values['key']]);
        }

        for ($i = 0; $i < $count; $i++) {
            $html .= "<div class='object'>";

            foreach ($values["field"] as $field) {

                $key = $field['key'];
                $field['key'] = $values['key'] . '[' . $i . ']' . '[' . $key . ']';

                if (!empty($_POST[$values['key']][$i][$key])) {

                    $field['value'] = $_POST[$values['key']][$i][$key];
                }

                $html .= "<label>";
                $html .= $this->get_type($field['type'], $field);
                $html .= "</label>";
            }
            $html .= "<div class='object_header' style='margin-top: 10px'>";
            $html .= "<button type='button' class='remove_field'>usuń</button>";
            $html .= "</div>";
            $html .= "</div>";
        }

        $html .= "</div>";

        $html .= "<button type='button' class='add_field'>Dodaj</button>";

        $html .= "</div>";

        return $html;
    }

    public function set_data($data)
    {
        $this->data[] = [
            "key" => !empty($data['key']) ? $data['key'] : "",
            "name" => !empty($data['name']) ? $data['name'] : "",
            "type" => !empty($data['type']) ? $data['type'] : "",
            "value" => !empty($data['value']) ? $data['value'] : "",
            "options" => !empty($data['options']) ? $data['options'] : [],
            "description" => !empty($data['description']) ? $data['description'] : "",
            "field" => !empty($data['field']) ? $data['field'] : []
        ];
    }

    public function get_form(string $name, string $submit_button_name, array $errors = [])
    {
        $html = "<div class='form'>";
        $html .= "<h2>" . $name . "</h2>";
        if (!empty($errors)) {
            $html .= "<div class='error'>";
            foreach ($errors as $key => $value) {
                $html .= "<p>" . $value . "</p>";
            }
            $html .= "</div>";
        }

        $html .= "<form method='post' enctype='multipart/form-data'>";
        $html .= "<input type='hidden' value='true' name='active'>";
        $html .= "<input type='hidden' value='" . $_SESSION['CSRF_token'] . "' name='CSRF_token'>";

        foreach ($this->data as $values) {

            $html .= $this->get_type($values['type'], $values);

        }

        $html .= "<input type='submit' value='" . $submit_button_name . "'>";

        $html .= "</form>";

        $html .= "</div>";

        return $html;
    }

    private function get_type($type, $values)
    {
        $field = "";
        switch ($type) {
            case 'textarea':
                $field = $this->textarea($values);
                break;
            case 'select':
                $field = $this->select($values);
                break;
            case 'checkbox':
                $field = $this->checkbox($values);
                break;
            case 'multi':
                $field = $this->multi($values);
                break;
            case 'checkbox_list':
                $field = $this->checkbox_list($values);
                break;
            default:
                $field = $this->input($values);
                break;
        }

        return $field;
    }
}