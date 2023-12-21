<?php

namespace ModuleManager;

class Table
{
    protected $data;
    protected $key;
    protected $table;
    protected $row_per_page;
    protected $actual_page;
    protected $id;
    protected $more_filter = [];
    protected $action = [];
    protected $mass_action = [];
    protected $actual_link;
    protected $toggle = false;
    protected $additional = [];
    protected $filter_list = [];
    protected $sort_list = [];
    public $buttons = "";

    private array $converter = [];

    public function __construct($row_per_page = 25, $actual_page = 1)
    {
        $this->actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->row_per_page = $row_per_page;
        if ($actual_page > 0) {

            $this->actual_page = $actual_page - 1;
        } else {
            $this->actual_page = 0;
        }
    }

    // Setters
    public function set_id($id)
    {
        $this->id = $id;
    }

    public function set_converter($key, $function)
    {
        $this->converter[$key] = $function;
    }

    public function set_toggle($toggle)
    {
        $this->toggle = $toggle;
    }

    public function add_mass_action($klucz, $name)
    {
        $this->mass_action[$klucz] = $name;
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

    public function set_additional(array $additional)
    {
        $this->additional[] = $additional;
    }

    public function add_button($link, $icon, $label = '')
    {
        $this->more_filter[] = [
            "link" => $link,
            "icon" => $icon,
            "label" => $label
        ];
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

    // Create table
    public function generate_table($data, $key = null)
    {
        if (!empty($_GET['page'])) {
            $this->set_page($_GET['page']);
        }

        $this->data = $data;
        $this->key = $key;

        $table = "<div class='pole_filtry'>";
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
        $table .= $this->mass_action();
        $table .= "</form>";
        $table .= "</div>";

        return $table;
    }

    // Private

    private function arrow($value)
    {
        $arrow = "
            <div class='arrows'>
                <div class='up " . (!empty($_GET['sort']) && $value == $_GET['sort'] && $_GET['order'] == "ASC" ? "active" : "not_active") . "'>
                    " . file_get_contents(__DIR__ . "/../../panel-template/img/up-arrow.svg") . "   
                </div>
                <div class='down " . (!empty($_GET['sort']) && $value == $_GET['sort'] && $_GET['order'] == "DESC" ? "active" : "not_active") . "'>
                    " . file_get_contents(__DIR__ . "/../../panel-template/img/down-arrow.svg") . "                      
                </div>
            </div>
        ";

        return $arrow;
    }

    private function toggle_row($data = []): string
    {
        $body = "</tr>";
        $body .= "<tr class='explode hide'>";
        $body .= "<td colspan='" . count($data) + 2 . "'>";
        $body .= "<div class='boxes'>";

        foreach ($this->additional as $value) {

            // foreach ($data as $key => $value) {

            //     if (in_array($key, $value['key'])) {
            //         $body .= "<p>" . $key_k . ": " . "<span class='value'>" . $value . " </span></p>";
            //     }

            // }

            $body .= "<div class='content'>";
            $body .= "<p class='header'>" . $value['header'] . "</p>";
            $body .= "<div>";

            if (isset($value['key'])) {

                foreach ($data[$value['key']] as $data_key => $data_value) {

                    if (array_key_exists($data_key, $value['field']))
                        $body .= "<p><b>" . $value['field'][$data_key] . "</b>: " . $data_value . "<p>";

                }

            } else {

                foreach ($data as $data_key => $data_value) {

                    if (array_key_exists($data_key, $value['field']))
                        $body .= "<p><b>" . $value['field'][$data_key] . "</b>: " . $data_value . "<p>";

                }
            }

            $body .= "</div>";
            $body .= "</div>";

        }

        $body .= "</div>";
        $body .= "</td>";
        $body .= "</tr>";

        return $body;

    }

    private function create_link($key, $value, $first): string
    {
        $output = "";
        if ($first) {
            $output = "?";
        } else {
            $output = "&";
        }

        $output .= $key . "=" . $value;

        return $output;

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

    // Protected

    protected function create_header()
    {

        // Order 
        $order = "ASC";
        if (!empty($_GET['order'])) {
            if ($_GET['order'] == "ASC")
                $order = "DESC";
        }


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

        if ($this->toggle) {
            $header .= "<th class='check_field'>";
            $header .= "";
            $header .= "</th>";
        }

        if (!empty($this->key)) {

            foreach ($this->key as $key => $value) {

                $link = "?sort=" . $value[0] . "&order=" . $order;


                foreach ($_GET as $get_key => $get_value) {

                    if ($get_key != 'sort' && $get_key != 'order') {
                        $link .= $this->create_link($get_key, $get_value, false);
                    }
                }
                $header .= "<th>";
                $header .= "<a href='" . $this->actual_link . $link . "'>";
                $header .= $key;
                $header .= $this->arrow($value[0]);
                $header .= "</a>";
                $header .= "</th>";
            }

        } else {


            foreach ($this->data[0] as $key => $value) {

                $link = "?sort=" . $value[0] . "&order=" . $order;
                foreach ($_GET as $key => $value) {

                    if ($key != 'sort' && $key != 'order') {
                        $link .= $this->create_link($key, $value, false);
                    }
                }

                $header .= "<th>";
                $header .= "<a href='" . $this->actual_link . $link . "'>";
                $header .= $key;
                $header .= $this->arrow($value[0]);
                $header .= "</a>";
                $header .= "</th>";
            }

        }
        $header .= "</tr>";
        $header .= "</thead>";

        return $header;
    }

    protected function sort(array &$rows, array $sorts)
    {

        $tmp = [];

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

        array_multisort($tmp, $rows);

        return $rows;

    }

    protected function create_content()
    {
        $first_row = $this->actual_page * $this->row_per_page;
        array_splice($this->data, 0, $first_row);
        array_splice($this->data, $this->row_per_page);

        $this->actual_link = explode("?", $this->actual_link)[0];

        $order = "ASC";
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        if (isset($_GET['sort'])) {
            \Helper::sort_array($this->data, $_GET['sort'], $order);
        }

        $body = "<tbody>";


        if (!empty($this->key)) {

            foreach ($this->data as $data) {

                $data_id = "";

                if (!empty($this->id))
                    $data_id = "data-id='" . $data[$this->id] . "'";

                $body .= "<tr class='standard' " . $data_id . ">";

                if (!empty($this->id) && !empty($this->mass_action)) {

                    $body .= "<td class='check_field'>";
                    $body .= "
                        <label class='checkbox_container'>
                            <input name='id[]' type='checkbox' value='" . $data[$this->id] . "' class='id'>
                            <span class='checkmark'></span>
                        </label>
                    ";


                }
                $body .= "</td>";

                if ($this->toggle) {
                    $body .= "<td class='exploder'>";
                    $body .= file_get_contents(__DIR__ . "/../template/img/down-arrow.svg");
                    $body .= "</td>";
                }

                if (count($this->action) > 0) {
                    $body .= "<td class='icon_field'>";
                    if (!empty($this->id) && !empty($this->action)) {
                        foreach ($this->action as $action) {
                            $body .= "<a href='" . $this->actual_link . "/" . $action['link'] . "?id=" . $data[$this->id] . "&type=" . $action['icon'] . "' label=''>";
                            switch ($action['icon']) {
                                case 'edit':
                                    $body .= file_get_contents(__DIR__ . "/../../panel-template/img/edit.svg");
                                    break;
                                case 'show':
                                    $body .= file_get_contents(__DIR__ . "/../../panel-template/img/show.svg");
                                    break;
                                case 'delete':
                                    $body .= file_get_contents(__DIR__ . "/../../panel-template/img/delete.svg");
                                    break;
                                default:
                                    $body .= file_get_contents(__DIR__ . "/../../core/interface/img/edit.svg");
                                    break;
                            }
                            $body .= "</a>";
                        }
                    }

                    $body .= "</td>";
                }

                foreach ($this->key as $key_k => $value_k) {
                    $body .= "<td>";

                    foreach ($data as $key => $value) {

                        try {
                            if (!empty($this->converter[$key])) {
                                $value = call_user_func($this->converter[$key], $value);
                            }
                        } catch (\Throwable $th) {
                            $details = [
                                "message" => $th->getMessage(),
                                "code" => $th->getCode(),
                                "file" => $th->getFile(),
                                "line" => $th->getLine()
                            ];
                            \ModuleManager\Main::set_error('Include module', 'WARING', $details);
                        }


                        if (in_array($key, $value_k)) {
                            $body .= "<span class='value'>" . $value . " </span>";
                        }

                    }

                    $body .= "</td>";
                }

                $body .= $this->toggle_row($data);


            }

        } else {

            foreach ($this->data as $data) {

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

    protected function page_navigation()
    {

        if (isset($_GET['sort'])) {
            $page = $this->actual_link . "?sort=" . $_GET['sort'] . "&page=";
        } else {
            $page = $this->actual_link . "?page=";
        }

        $page = $this->actual_link;
        $first = true;

        if (isset($_GET['sort'])) {
            $page .= $this->create_link("sort", $_GET['sort'], $first);
            $first = false;
        }

        if (isset($_GET['order'])) {
            $page .= $this->create_link("order", $_GET['order'], $first);
            $first = false;
        }

        if (isset($_GET['filter'])) {
            $page .= $this->create_link("filter", $_GET['filter'], $first);
            $first = false;
        }

        if ($first) {
            $first = false;
            $page .= "?page=";
        } else {
            $page .= "&page=";
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

    protected function filter()
    {

        $this->actual_link = explode("?", $this->actual_link)[0];

        // $button = "<div class='table_filtr' cellspacing='0' cellpadding='0'>";
        $button = "";
        foreach ($this->more_filter as $filtr) {
            $button .= "<a class='button' href='" . $this->actual_link . $filtr['link'] . "'>" . $filtr['label'] . "</a>";
        }
        // $button .= "</div>";
        return $button;
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
}