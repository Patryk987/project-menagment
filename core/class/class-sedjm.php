<?php

namespace ModuleManager;



/**
 * Secure Encrypted Data JSON Mapping
 */
class SEDJM
{
    protected $connect;
    protected $database;

    private $where = [];
    private $having = [];
    private $join = [];
    private $order_by = [];
    private $group_by = [];
    private $limit = [];

    public $last_query;

    public function __construct($connect, $database)
    {

        $this->connect = $connect;
        $this->database = $database;

    }

    private function convert_data($data, $field, $table, $type = 'encrypt', $key = NULL)
    {

        $prepare_data = '';

        // TODO: sprawdzić dlaczego nie działa warunek poprawności
        // if(array_key_exists($field, $this->database[$table])) {

        $info_about_column = $this->database[$table][$field];

        if ($info_about_column['encrypt'] != false) {

            if ($type == 'encrypt') {

                switch ($info_about_column['encrypt_type']) {
                    case 'simple':
                        $data = htmlspecialchars($data, ENT_QUOTES);
                        $data = addslashes($data);
                        $prepare_data = EncryptData::encrypt($data, $key);
                        break;

                    case 'password':
                        $prepare_data = EncryptData::password_hash($data);
                        break;

                    default:
                        $data = !empty($data) ? htmlspecialchars($data, ENT_QUOTES) : "";
                        $data = !empty($data) ? addslashes($data) : "";
                        $prepare_data = EncryptData::encrypt($data, $key);
                        break;
                }

            } else if ($type == 'decrypt') {

                switch ($info_about_column['encrypt_type']) {

                    case 'simple':
                        $data = htmlspecialchars($data, ENT_QUOTES);
                        $data = addslashes($data);
                        $prepare_data = EncryptData::decrypt($data, $key);
                        break;
                    case 'password':
                        $prepare_data = $data;
                        break;
                    default:
                        if (!empty($data)) {

                            $data = htmlspecialchars($data, ENT_QUOTES);
                            $data = addslashes($data);
                            $prepare_data = EncryptData::decrypt($data, $key);
                        }
                        break;

                }

            }


        } else {
            if (!empty($data)) {

                $data = htmlspecialchars($data, ENT_QUOTES);
                $data = addslashes($data);
                $prepare_data = $data;

            } else {

                $prepare_data = $data;

            }
        }

        // }

        return $prepare_data;
    }

    // Setery

    public function set_where($column, $value, $parameter, $connector = "AND")
    {
        $this->where[] = [
            "column" => $column,
            "value" => $value,
            "parameter" => $parameter,
            "connector" => $connector
        ];
    }

    public function set_having($column, $value, $parameter, $connector = "AND")
    {
        // TODO: Dodać funkcje
        $this->having[] = [
            "column" => $column,
            "value" => $value,
            "parameter" => $parameter,
            "connector" => $connector
        ];
    }

    public function set_join($join_type, $table_from, $table_to)
    {
        $this->join[] = [
            "join_type" => $join_type,
            "from_table" => $table_from['table'],
            "from_column" => $table_from['column'],
            "to_table" => $table_to['table'],
            "to_column" => $table_to['column']
        ];
    }

    public function set_order_by($by, $order = "ASC")
    {
        $this->order_by[] = [
            "order" => $order,
            "by" => $by
        ];
    }

    public function set_group_by($data)
    {
        $this->group_by[] = [
            'column' => $data
        ];
    }

    public function set_limit($data)
    {
        $this->limit = $data;
    }

    // Clear

    public function clear_where()
    {
        $this->where = [];
    }

    public function clear_having()
    {
        $this->having = [];
    }

    public function clear_join()
    {
        $this->join = [];
    }

    public function clear_order_by()
    {
        $this->order_by = [];
    }

    public function clear_group_by()
    {
        $this->group_by = [];
    }

    public function clear_limit()
    {
        $this->limit = '';
    }

    public function clear_all(): void
    {
        $this->where = [];
        $this->having = [];
        $this->join = [];
        $this->order_by = [];
        $this->group_by = [];
        $this->limit = '';
    }

    // funkcje "przygotowujące"

    private function prepare_where($table)
    {

        $where_to_return = '';

        if (!empty($this->where)) {

            $where = " WHERE ";

            foreach ($this->where as $key => $value) {

                $parameter = $value['parameter'];
                $column = $value['column'];
                $element = $this->convert_data($value['value'], $column, $table, 'encrypt');

                if ($key !== array_key_first($this->where)) {
                    $where .= " " . $value['connector'];
                }

                $where .= " " . $table . "." . $column . " " . $parameter . " '" . $element . "'";
            }

            $where_to_return = $where;
        }

        return $where_to_return;

    }

    private function prepare_having($table)
    {

        $having_to_return = '';

        if (!empty($this->having)) {

            $having = " HAVING ";

            foreach ($this->having as $key => $value) {

                $parameter = $value['parameter'];
                $column = $value['column'];
                $element = $this->convert_data($value['value'], $column, $table, 'encrypt');

                if ($key !== array_key_first($this->having)) {
                    $having .= " " . $value['connector'];
                }

                $having .= " " . $column . " " . $parameter . " '" . $element . "'";
            }

            $having_to_return = $having;
        }

        return $having_to_return;

    }

    private function prepare_join($table)
    {

        $join_to_return = '';

        if (!empty($this->join)) {

            foreach ($this->join as $key => $value) {
                $join_to_return .= " " . $value['join_type'] . " JOIN " . $value['from_table'] . " ON " . $value['from_table'] . "." . $value['from_column'] . "=" . $value['to_table'] . "." . $value['to_column'];
            }

        }

        return $join_to_return;

    }

    private function prepare_order_by()
    {

        $order_by_to_return = '';

        $random = false;

        if (!empty($this->order_by)) {
            foreach ($this->order_by as $key => $value) {

                if ($key === array_key_first($this->order_by)) {
                    $order_by_to_return .= " ORDER BY ";
                }

                if ($value['by'] == "RANDOM") {

                    $random = true;

                }

                $order_by_to_return .= $value['by'];

                if ($key !== array_key_last($this->order_by)) {
                    $order_by_to_return .= ", ";
                } else {
                    $order_by_to_return .= " " . $value['order'];
                }
            }
        }

        if ($random == true) {
            $order_by_to_return = " ORDER BY RAND()";
        }

        return $order_by_to_return;

    }

    private function prepare_group_by()
    {

        $group_by_to_return = '';

        if (!empty($this->group_by)) {
            $group_by_to_return .= " GROUP BY ";
            foreach ($this->group_by as $key => $value) {

                $group_by_to_return .= $value['column'];

                if ($key !== array_key_last($this->group_by)) {
                    $group_by_to_return .= ", ";
                }

            }
        }

        return $group_by_to_return;

    }

    private function prepare_limit()
    {

        $limit_to_return = '';

        if (!empty($this->limit)) {

            $limit_to_return = ' LIMIT ' . $this->limit;

        }

        return $limit_to_return;

    }

    private function prepare_data_select($data, $table)
    {

        $data_to_return = '';

        foreach ($data as $key => $value) {
            if (gettype($value) == 'array') {
                $data_to_return .= $value['table'] . "." . $value['column'] . " as " . $value['alias'];
            } else {
                $data_to_return .= $table . "." . $value . " as " . $value;

            }
            // if (gettype($key) == 'string') {

            //     $data_to_return .= $key . "." . $value . " as " . $value;
            // } else {

            //     $data_to_return .= $table . "." . $value . " as " . $value;
            // }

            if ($key !== array_key_last($data)) {
                $data_to_return .= ', ';
            } else {
                $data_to_return .= ' ';
            }
        }

        return $data_to_return;

    }

    public function get($data, $table)
    {

        $prepare_data = $this->prepare_data_select($data, $table);
        $prepare_join = $this->prepare_join($table);
        $prepare_where = $this->prepare_where($table);
        $prepare_group_by = $this->prepare_group_by();
        $prepare_having = $this->prepare_having($table);
        $prepare_order_by = $this->prepare_order_by();
        $prepare_limit = $this->prepare_limit();

        $select = "SELECT " . $prepare_data . " FROM " . $table;
        $query = $select
            . $prepare_join
            . $prepare_where
            . $prepare_group_by
            . $prepare_having
            . $prepare_order_by
            . $prepare_limit;

        $this->last_query = $query;
        // Wykonywanie zapytania
        $query_result = $this->connect->query($query);

        $data_table = [];
        $field = mysqli_fetch_fields($query_result);

        foreach ($field as $value) {
            $data_table[$value->name] = $value->table;
        }

        $return_table = [];
        while ($row = $query_result->fetch_assoc()) {
            $r_d = [];
            foreach ($data as $value) {
                if (gettype($value) == 'array') {

                    $alias = $value['alias'];
                    $column = $value['column'];
                    $table = $value['table'];

                    $r_d[$alias] = $this->convert_data($row[$alias], $column, $data_table[$alias], 'decrypt');

                    try {
                        if (!empty($value['function'])) {
                            $r_d[$alias] = call_user_func([$value['function'][0], $value['function'][1]], $r_d[$alias]);
                        }
                    } catch (\Throwable $th) {
                        $details = [
                            "message" => $th->getMessage(),
                            "code" => $th->getCode(),
                            "file" => $th->getFile(),
                            "line" => $th->getLine()
                        ];
                        \ModuleManager\Main::set_error('SEDJM helper function', 'ERROR', $details);
                    }

                } else {
                    $r_d[$value] = $this->convert_data($row[$value], $value, $data_table[$value], 'decrypt');

                }
            }

            $return_table[] = $r_d;

        }

        return $return_table;

    }

    public function delete($table)
    {

        $prepare_where = $this->prepare_where($table);
        $query = "DELETE FROM " . $table . " " . $prepare_where;

        $return_data = [];

        $this->last_query = $query;

        if ($this->connect->query($query)) {
            $return_data = ['status' => true];
        } else {
            $return_data = ['status' => false];
        }

        return $return_data;

    }

    public function update($data, $table)
    {

        $prepare_where = $this->prepare_where($table);
        $query = "UPDATE $table SET ";

        foreach ($data as $key => $value) {

            $query .= $key . ' = \'' . $this->convert_data($value, $key, $table, 'encrypt') . '\' ';

            if ($key !== array_key_last($data)) {
                $query .= ',';
            }

        }

        $query .= " " . $prepare_where;
        $return_data = [];

        $this->last_query = $query;

        if ($this->connect->query($query)) {
            $return_data = ['status' => true];
        } else {
            $return_data = ['status' => false];
        }

        return $return_data;
    }

    public function insert($data, $table)
    {
        $column_list = '(';
        $value_list = '(';
        foreach ($data as $key => $value) {

            $column_list .= $key;
            $encrypt_data = $this->convert_data($value, $key, $table, 'encrypt');
            $value_list .= '\'' . $encrypt_data . '\'';
            // echo ("Klucz: " . $key . ", Wartość: " . $value . ", Wartość zaszyfrowana: " . $encrypt_data);
            // echo "<hr>";
            if ($key !== array_key_last($data)) {
                $column_list .= ',';
                $value_list .= ',';
            } else {
                $column_list .= ')';
                $value_list .= ')';
            }

        }

        $insert = 'INSERT INTO ' . $table . $column_list . " VALUES " . $value_list;

        $this->last_query = $insert;

        $return_data = [];

        if ($this->connect->query($insert)) {
            $last_id = $this->connect->insert_id;
            $return_data = ["status" => true, "id" => $last_id];
        } else {
            $return_data = ["status" => false];
        }

        return $return_data;

    }

    // opcjonalne funkcje 

    public function refactoring_database()
    {

    }

    public function generate_database(bool $save = false)
    {
        // $database

        // var_dump($this->database);
        $querys = [];
        $foreign_key = [];

        foreach ($this->database as $table_name => $table_column) {
            $columns = [];
            $pk_f = '';

            foreach ($table_column as $column_name => $column_value) {

                // $type = "";
                $type_switch = strtolower($column_value['type']);
                switch ($type_switch) {
                    case 'varchar':
                        $type = " VARCHAR(" . $column_value['length'] . ")";
                        break;
                    case 'int':
                        $type = " INT(" . $column_value['length'] . ")";
                        break;

                    default:
                        $type = " " . strtoupper($column_value['type']);
                        if (!empty($column_value['length']))
                            $type .= "(" . $column_value['length'] . ")";
                        break;
                }

                if (!$column_value['NULL']) {
                    $null = " NOT NULL";
                } else {
                    $null = "";
                }

                $unique = "";
                if (!empty($column_value['PRIMARY_KEY'])) {
                    if ($column_value['PRIMARY_KEY'] == true) {
                        $pk = " AUTO_INCREMENT";
                        $pk_f = "PRIMARY KEY (" . $column_name . ")";
                    } else {
                        $pk = "";
                    }
                } else {
                    $pk = "";

                    if (!empty($column_value['unique'])) {
                        if ($column_value['unique'] == true) {
                            $unique = " UNIQUE";
                        } else {
                            $unique = "";
                        }
                    } else {
                        $pk = "";
                    }
                }


                if (isset($column_value['foreign']) && $column_value["type"] != "varchar" && !empty($column_value['foreign'])) {

                    foreach ($column_value['foreign'] as $foreign_column => $foreign_column_value) {
                        $foreign_key[] = "ALTER TABLE " . $foreign_column . " ADD FOREIGN KEY (" . $foreign_column_value['column'] . ") REFERENCES " . $table_name . "(" . $column_name . ");";
                    }
                }

                $columns[] = "$column_name$type$null$unique$pk";
            }

            if (!empty($pk_f)) {

                $columns[] = $pk_f;
            }

            $prepare_column = '';
            foreach ($columns as $column) {
                $prepare_column .= $column;
                if (next($columns) == true)
                    $prepare_column .= ",";
            }

            $querys[] = "CREATE TABLE IF NOT EXISTS $table_name ($prepare_column);";
        }

        if ($save) {
            foreach ($querys as $value) {
                $this->connect->query($value);
            }
        }

        foreach ($foreign_key as $foreign_key) {
            $querys[] = $foreign_key;
        }

        return $querys;
    }

    public function __destruct()
    {

    }

}