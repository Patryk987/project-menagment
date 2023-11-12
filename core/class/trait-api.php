<?php

namespace ModuleManager;

trait API
{

    private static array $endpoints = [];

    /**
     * Register new endpoint
     * @param string $name 
     * @param string $function
     * @param int $access_permission 
     */
    public static function set_endpoint($data)
    {

        $method = $data["http_methods"];
        $link = $data["link"];

        static::$endpoints[$method][$link] = [
            "name" => $link,
            "function" => $data["function"],
            "method" => $method,
            "access_permission" => $data["permission"]
        ];

    }

    private function check_method(): string
    {

        $method = $_SERVER['REQUEST_METHOD'];
        return $method;

    }

    private function http_response_code($code = NULL)
    {

        if ($code !== NULL) {

            switch ($code) {
                case 100:
                    $text = 'Continue';
                    break;
                case 101:
                    $text = 'Switching Protocols';
                    break;
                case 200:
                    $text = 'OK';
                    break;
                case 201:
                    $text = 'Created';
                    break;
                case 202:
                    $text = 'Accepted';
                    break;
                case 203:
                    $text = 'Non-Authoritative Information';
                    break;
                case 204:
                    $text = 'No Content';
                    break;
                case 205:
                    $text = 'Reset Content';
                    break;
                case 206:
                    $text = 'Partial Content';
                    break;
                case 300:
                    $text = 'Multiple Choices';
                    break;
                case 301:
                    $text = 'Moved Permanently';
                    break;
                case 302:
                    $text = 'Moved Temporarily';
                    break;
                case 303:
                    $text = 'See Other';
                    break;
                case 304:
                    $text = 'Not Modified';
                    break;
                case 305:
                    $text = 'Use Proxy';
                    break;
                case 400:
                    $text = 'Bad Request';
                    break;
                case 401:
                    $text = 'Unauthorized';
                    break;
                case 402:
                    $text = 'Payment Required';
                    break;
                case 403:
                    $text = 'Forbidden';
                    break;
                case 404:
                    $text = 'Not Found';
                    break;
                case 405:
                    $text = 'Method Not Allowed';
                    break;
                case 406:
                    $text = 'Not Acceptable';
                    break;
                case 407:
                    $text = 'Proxy Authentication Required';
                    break;
                case 408:
                    $text = 'Request Time-out';
                    break;
                case 409:
                    $text = 'Conflict';
                    break;
                case 410:
                    $text = 'Gone';
                    break;
                case 411:
                    $text = 'Length Required';
                    break;
                case 412:
                    $text = 'Precondition Failed';
                    break;
                case 413:
                    $text = 'Request Entity Too Large';
                    break;
                case 414:
                    $text = 'Request-URI Too Large';
                    break;
                case 415:
                    $text = 'Unsupported Media Type';
                    break;
                case 500:
                    $text = 'Internal Server Error';
                    break;
                case 501:
                    $text = 'Not Implemented';
                    break;
                case 502:
                    $text = 'Bad Gateway';
                    break;
                case 503:
                    $text = 'Service Unavailable';
                    break;
                case 504:
                    $text = 'Gateway Time-out';
                    break;
                case 505:
                    $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }

    private function inputData(): array
    {
        $json = file_get_contents('php://input');

        if (!empty($json)) {
            $json = json_decode($json, true);
        } else {
            $json = [];
        }

        if (!empty($_GET)) {
            $get = $_GET;
        } else {
            $get = [];
        }

        if (!empty($_POST)) {
            $post = $_POST;
        } else {
            $post = [];
        }

        if (!empty($_FILES)) {
            $file = $_FILES;
        } else {
            $file = [];
        }

        // Token
        $token_data = Main::$token;
        $payload = (array) $token_data['payload'];

        $input = array_merge($json, $get);
        $input = array_merge($input, $post);
        $input = array_merge($input, $payload);
        $input = array_merge($input, $file);

        if (!empty($input)) {
            return $input;
        } else {
            return [];
        }

    }

    public function get_endpoint($name): void
    {

        if (empty(getallheaders()['api_key']) || getallheaders()['api_key'] != $_ENV['API_SECRET']) {


            $output = new \Models\ApiModel(\ApiStatus::ERROR, [], ['Unauthorized user']);
            $output->set_code(401);

            \ModuleManager\Main::set_error(
                'Unauthorized client',
                'ERROR',
                [
                    $_SERVER['REMOTE_ADDR'],
                    $_SERVER['HTTP_USER_AGENT']
                ]
            );

            $this->return_data($output);

            exit();

        }

        $method = $this->check_method();

        if (empty(static::$endpoints[$method][$name])) {

            $output = new \Models\ApiModel(\ApiStatus::ERROR, [], ['Not Found']);
            $output->set_code(404);

            $this->return_data($output);

            exit();

        }

        $token_data = Main::$token;
        $api_permission = !empty($token_data['payload']->permission) ? in_array($token_data['payload']->permission, static::$endpoints[$method][$name]['access_permission']) : false;

        if ($api_permission || in_array(0, static::$endpoints[$method][$name]['access_permission'])) {

            try {

                $output = call_user_func(static::$endpoints[$method][$name]["function"], $this->inputData());
                $output->set_code(200);

                $this->return_data($output);

            } catch (\Throwable $th) {

                if (Main::get_debug_status()) {

                    $error = [
                        "message" => $th->getMessage(),
                        "code" => $th->getCode(),
                        "file" => $th->getFile(),
                        "line" => $th->getLine(),
                    ];

                } else {
                    $error = [
                        "message" => "Internal Server Error"
                    ];
                }

                Main::set_error('API', 'ERROR', $error);

                $output = new \Models\ApiModel(\ApiStatus::ERROR, [], $error);
                $output->set_code(500);

                $this->return_data($output);

            }

        } else {

            $output = new \Models\ApiModel(\ApiStatus::ERROR, [], ["Unauthorized"]);
            $output->set_code(401);

            $this->return_data($output);

        }

    }

    private function api($last_sub_page): void
    {

        $this->get_endpoint($last_sub_page);
        exit();

    }

    private function return_data(\Models\ApiModel $data): void
    {

        header("Content-type: application/json; charset=utf-8");
        header('Access-Control-Allow-Origin: *');

        $this->http_response_code($data->get_code());

        $output = [];
        $output['code'] = $data->get_code();
        $output["status"] = $data->get_status()->get_bool_status();
        $output["message"] = $data->get_message();
        $output['error'] = $data->get_error();

        echo json_encode($output);

        exit();
    }


}