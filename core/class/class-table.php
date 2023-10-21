<?php

namespace ModuleManager;

class Table
{
    protected $data, $key, $table, $row_per_page, $actual_page, $id, $more_filter = [], $action = [], $mass_action = [], $actual_link;

    public $buttons = "";

    protected $filter_list = [];
    protected $sort_list = [];

    public function __construct($row_per_page = 20, $actual_page = 1)
    {
        $this->actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->row_per_page = $row_per_page;
        if ($actual_page > 0) {

            $this->actual_page = $actual_page - 1;
        } else {
            $this->actual_page = 0;
        }
    }

    protected function create_header()
    {
        $header = "<thead>";
        $header .= "<tr>";
        if (!empty($this->id) && !empty($this->mass_action)) {

            $header .= "<th class='check_field'>";
            $header .= "
                <label class='checkbox_container check_all'>
                    <input type='checkbox' value='' class='check_all'>
                    <span class='checkmark'></span>
                </label>
            ";
            $header .= "</th>";

        }
        if (count($this->action) > 0) {
            $header .= "<th class='check_field'>";
            $header .= "";
            $header .= "</th>";
        }
        if (!empty($this->key)) {
            foreach ($this->key as $key => $value) {
                $header .= "<th>";
                $header .= $key;
                $header .= "</th>";
            }
        } else {
            foreach ($this->data[0] as $key => $value) {
                $header .= "<th>";
                $header .= $key;
                $header .= "</th>";
            }
        }
        $header .= "</tr>";
        $header .= "</thead>";

        return $header;
    }

    protected function sort(array &$rows, array $sorts)
    {

        // $args = [];
        //
        // foreach ($sorts as $field => $direction) {
        //     $col = array_column($rows, $field);
        //     $args[] = $col;
        //
        //     if ('asc' === $direction) {
        //         $args[] = SORT_ASC;
        //     } else {
        //         $args[] = SORT_DESC;
        //     }
        // }
        // $args[] = &$rows;
        // call_user_func_array("array_multisort", $args);

        $tmp = [];



        // foreach($rows as &$ma) {
        foreach ($sorts as $key => $value) {
            foreach ($rows as &$ma)
                $tmp[] = &$ma[$key];

            if (strtoupper($value) == 'ASC') {
                array_multisort($tmp, SORT_ASC, $rows);
            } else if (strtoupper($value) == 'DESC') {
                array_multisort($tmp, SORT_DESC, $rows);
            } else {
                array_multisort($tmp, $rows);
            }
        }
        // }

        // return $rows;
        array_multisort($tmp, $rows);

        return $rows;
    }

    protected function create_content()
    {
        $row = 0;
        $first_row = $this->actual_page * $this->row_per_page;
        $last_row = $first_row + $this->row_per_page;

        // $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->actual_link = explode("?", $this->actual_link)[0];

        $body = "<tbody>";
        if (!empty($this->key)) {
            if (!empty($_GET['sort'])) {
                $this->data = $this->sort($this->data, [
                    $_GET['sort'] => 'ASC'
                ]);
            }

            foreach ($this->data as $data) {
                $row++;
                if ($last_row < $row)
                    break;
                if ($first_row > $row)
                    continue;

                $body .= "<tr>";
                if (!empty($this->id) && !empty($this->mass_action)) {

                    $body .= "<td class='check_field'>";
                    $body .= "
                        <label class='checkbox_container'>
                            <input name='id[]' type='checkbox' value='" . $data[$this->id] . "' class='id'>
                            <span class='checkmark'></span>
                        </label>
                    ";
                    $body .= "</td>";

                }
                if (count($this->action) > 0) {
                    $body .= "<td class='icon_field'>";
                    if (!empty($this->id) && !empty($this->action)) {
                        foreach ($this->action as $action) {
                            $body .= "<a href='" . $this->actual_link . "/" . $action['link'] . "?id=" . $data[$this->id] . "&type=" . $action['icon'] . "' label=''>";
                            switch ($action['icon']) {
                                case 'edit':
                                    // $body .= "edit";
                                    $body .= file_get_contents("panel-template/img/edit.svg");
                                    break;
                                case 'show':
                                    // $body .= "show";
                                    $body .= file_get_contents("panel-template/img/show.svg");
                                    break;
                                case 'delete':
                                    // $body .= "delete";
                                    $body .= file_get_contents("panel-template/img/delete.svg");
                                    break;
                                default:
                                    // $body .= "edit";
                                    $body .= file_get_contents("core/interface/img/edit.svg");
                                    break;
                            }
                            $body .= "</a>";
                        }
                        // $body .= $data[$this->id];
                    }
                    // $body .= file_get_contents("core/interface/img/edit.svg");
                    $body .= "</td>";
                }
                foreach ($this->key as $key_k => $value_k) {
                    $body .= "<td>";

                    foreach ($data as $key => $value) {

                        if (in_array($key, $value_k)) {
                            $body .= "<span class='value'>" . $value . " </span>";
                        }

                    }

                    $body .= "</td>";
                }
                $body .= "</tr>";
            }

        } else {
            foreach ($this->data as $data) {
                $row++;
                if ($last_row < $row)
                    break;
                if ($first_row > $row)
                    continue;

                $body .= "<tr>";
                $body .= "<td>";
                $body .= "
                    <label class='checkbox_container'>
                        <input name='id[]' type='checkbox' value='" . $data[$this->id] . "' class='id'>
                        <span class='checkmark'></span>
                    </label>
                ";
                $body .= "</td>";
                $body .= "<td class='check_field'>";
                $body .= file_get_contents("core/interface/img/edit.svg");
                $body .= "</td>";
                if (true) {

                    // if(in_array(, $data))
                    foreach ($data as $key => $value) {
                        $body .= "<td>";
                        $body .= $value;
                        $body .= "</td>";
                    }
                } else {

                    foreach ($data as $key => $value) {
                        $body .= "<td>";
                        $body .= $value;
                        $body .= "</td>";
                    }
                }
                $body .= "</tr>";
            }
        }
        $body .= "</tbody>";

        return $body;
    }
    public function set_id($id)
    {
        $this->id = $id;
    }

    public function add_mass_action($klucz, $name)
    {
        $this->mass_action[$klucz] = $name;
    }

    protected function mass_action()
    {

        if (count($this->mass_action) > 0) {
            $html = '<div id="mass_action">';

            $html .= "<select name='action' class='admin_select'>";
            foreach ($this->mass_action as $key => $value) {
                $html .= "<option value='" . $key . "'>" . $value . "</option>";
            }
            $html .= "</select>";

            $html .= "<input type='submit' value='Wykonaj'>";

            $html .= '</div>';
            return $html;

        } else {
            return "";
        }
    }

    public function set_page($page)
    {
        if (!empty($page)) {
            if ($page > 0) {
                $this->actual_page = $page - 1;
            } else {
                $this->actual_page = 0;
            }
        }
    }

    public function set_action($link, $icon, $label = '')
    {
        $this->action[] = [
            "link" => $link,
            "icon" => $icon,
            "label" => $label
        ];
    }

    protected function page_navigation()
    {

        if (isset($_GET['sort'])) {
            $page = $this->actual_link . "?sort=" . $_GET['sort'] . "&page=";
        } else {
            $page = $this->actual_link . "?page=";

        }


        $last_page = ceil(count($this->data) / $this->row_per_page);
        $button = "<div class='page_navigation'>";
        if ($this->actual_page > 0) {
            $button .= "<a class='button_prev' href='" . $page . ($this->actual_page) . "'><span><</span></a>";
        }
        $button .= "<div class='page_number'><span>" . ($this->actual_page + 1) . " / " . ($last_page == 0 ? 1 : $last_page) . "</span></div>";
        if ($this->actual_page < $last_page - 1) {

            $button .= "<a class='button_next' href='" . $page . ($this->actual_page + 2) . "'><span>></span></a>";
        }
        $button .= "</div>";
        return $button;
    }

    public function add_button($link, $icon, $label = '')
    {
        $this->more_filter[] = [
            "link" => $link,
            "icon" => $icon,
            "label" => $label
        ];
    }

    protected function filter()
    {

        $this->actual_link = explode("?", $this->actual_link)[0];

        $button = "<div class='table_filtr' cellspacing='0' cellpadding='0'>";
        // $button .= "<a class='button' href='#' onclick='toggle_filtr()'>" . file_get_contents("./core/interface/img/filtr.svg") . "</a>";
        // $button .= "<a class='button' href='#' onclick='toggle_sort()'>" . file_get_contents("./core/interface/img/sort.svg") . "</a>";
        foreach ($this->more_filter as $filtr) {
            $button .= "<a class='button' href='" . $this->actual_link . $filtr['link'] . "'>" . $filtr['label'] . "</a>";
        }
        $button .= "</div>";
        return $button;
    }

    private function filter_icon()
    {
        $html = "<div class='button filtr_button'>";
        $html .= "<img src='/panel-template/img/filter_icon.svg' />";
        $html .= "<p>Filtruj</p>";
        $html .= "</div>";

        return $html;
    }

    private function sort_icon()
    {
        $html = "<div class='button sort_button'>";
        $html .= "<img src='/panel-template/img/sort_icon.svg' />";
        $html .= "<p>Sortuj</p>";
        $html .= "</div>";

        return $html;
    }

    public function action($action, $function)
    {

    }

    private function select($values)
    {
        $html = "<label>";
        $html .= '<select name="' . $values['key'] . '" id="" style="color: black !important">';
        $html .= "<option value=''>" . $values['name'] . "</option>";
        foreach ($values['options'] as $key => $value) {
            if (!empty($_GET[$values['key']]) && $_GET[$values['key']] == $key) {
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
        $html = "<label>";
        $html .= "<input type='" . $values['type'] . "' placeholder='" . $values['name'] . "' name='" . $values['key'] . "'  value='" . (!empty($_POST[$values['key']]) ? $_POST[$values['key']] : "") . "'>";
        $html .= "</label>";

        return $html;
    }

    protected function filter_inputs()
    {

        $html = "<div id='filter_inputs' class='toggle_box options_list'>";
        $html .= "<h3>Filtruj</h3>";

        $html .= "<form method='get'>";

        foreach ($this->filter_list as $key => $data) {

            if (!empty($_GET[$data["row_key"]])) {

                $value = "value='" . $_GET[$data["row_key"]] . "'";

            } else {

                $value = "";
            }

            // $html .= "<input type='" . $data['filed_type'] . "' placeholder='" . $data["placeholder"] . "' name='" . $data["row_key"] . "' " . $value . ">";

            switch ($data["filed_type"]) {
                case 'select':
                    $html .= $this->select([
                        "name" => $data["placeholder"],
                        "type" => $data["filed_type"],
                        "key" => $data["row_key"],
                        "options" => $data["options"]
                    ]);
                    break;
                case 'input':
                    $html .= $this->input([
                        "name" => $data["placeholder"],
                        "type" => $data["filed_type"],
                        "key" => $data["row_key"]
                    ]);
                    break;

                default:
                    $html .= $this->input([
                        "name" => $data["placeholder"],
                        "type" => $data["filed_type"],
                        "key" => $data["row_key"]
                    ]);
                    break;
            }

        }

        $html .= "<input type='submit' value='Filtruj'>";

        $html .= "</div>";

        return $html;
    }

    public function add_filter($placeholder, $key, $filed_type, $options = [])
    {
        $this->filter_list[] = [
            "placeholder" => $placeholder,
            "filed_type" => $filed_type,
            "row_key" => $key,
            "options" => $options
        ];
    }

    public function add_sort($placeholder, $key, $options = [])
    {
        $this->sort_list[] = [
            "placeholder" => $placeholder,
            "row_key" => $key,
            "options" => $options
        ];
    }

    protected function sort_inputs()
    {
        $html = "<div id='sort_inputs' class='toggle_box options_list'>";
        $html .= "<h3>Sortuj po:</h3>";

        $html .= "<form method='get'>";

        foreach ($this->sort_list as $key => $data) {

            $html .= $this->select([
                "name" => $data["placeholder"],
                "key" => $data["row_key"],
                "options" => $data["options"]
            ]);

            break;

        }

        $html .= "<input type='submit' value='Sortuj'>";

        $html .= "</div>";

        return $html;
    }

    public function generate_table($data, $key = null)
    {
        if (!empty($_GET['page'])) {
            $this->set_page($_GET['page']);
        }

        $this->data = $data;
        $this->key = $key;

        $table = "<div class='pole_filtry'>";
        // $table .= $this->filter();
        $table .= "<div class='buttons'>";
        $table .= $this->filter();
        if (count($this->filter_list) > 0) {
            $table .= $this->filter_icon();
        }
        if (count($this->sort_list) > 0) {
            $table .= $this->sort_icon();
        }
        $table .= "</div>";
        $table .= $this->page_navigation();
        $table .= "</div>";
        $table .= $this->filter_inputs();
        $table .= $this->sort_inputs();
        $table .= "<div class='table'>";
        $table .= "<form method='post'>";
        $table .= "<div class='cms_table'>";
        $table .= "<table>";
        $table .= $this->create_header();
        $table .= $this->create_content();
        $table .= "</table>";
        $table .= "</div>";
        $table .= "</table>";
        $table .= $this->mass_action();
        $table .= "</form>";

        return $table;
    }
}