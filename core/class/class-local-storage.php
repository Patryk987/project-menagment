<?php

namespace ModuleManager;

if (!session_id()) {
    session_start();
}

trait Cookies
{
    // protected static string $expire_time = 30; // two day

    /**
     * Set new cookie
     * @param string $key 
     * @param string $value 
     * @return bool $status 
     */
    private static function set_cookies(string $key, string $value): bool
    {
        if (setcookie($key, $value, time() + (86400 * 30), '/', '', true, true)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get cookie
     * @param string $key 
     * @return string $value 
     */
    private static function get_cookies(string $key): string
    {

        if (!isset($_COOKIE[$key])) {
            return "";
        } else {
            return $_COOKIE[$key];
        }

    }

    /**
     * remove cookie
     * @param string $key 
     */
    private static function remove_cookies(string $key): bool
    {

        if (isset($_COOKIE[$key])) {
            setcookie($key, "", time() - 3600, "/");
            return true;
        } else {
            return false;
        }

    }
}
trait SqLite
{

}
trait Session
{

    /**
     * Set new Session
     * @param string $key 
     * @param string $value 
     * @return bool $status 
     */
    private static function set_session(string $key, string $value): bool
    {

        if ($_SESSION[$key] = $value) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get session
     * @param string $key 
     * @return string $value 
     */
    private static function get_session(string $key): string
    {

        if (!isset($_SESSION[$key])) {
            return "";
        } else {
            return $_SESSION[$key];
        }

    }

    /**
     * remove session
     * @param string $key 
     */
    private static function remove_session(string $key): bool
    {

        if (isset($_SESSION[$key])) {
            $_SESSION[$key] = "";
            unset($_SESSION[$key]);
            return true;
        } else {
            return false;
        }

    }

}

class LocalStorage
{

    use Cookies, SqLite, Session;

    public static function set_data(string $key, string $value, string $type, bool $encrypt = false)
    {
        if ($encrypt) {
            $value = EncryptData::encrypt($value);
        }

        switch ($type) {
            case 'cookie':
                return static::set_cookies($key, $value);
            case 'sqlite':
                return false;
            case 'session':
                return static::set_session($key, $value);
            default:
                return false;
        }

    }

    public static function get_data(string $key, string $type, bool $encrypt = false)
    {

        switch ($type) {
            case 'cookie':
                $cookie = static::get_cookies($key);

                if ($encrypt) {

                    if (!empty($cookie))
                        $cookie = EncryptData::decrypt($cookie);
                    else
                        $cookie = "";

                }

                return $cookie;
            case 'sqlite':
                return false;
            case 'session':
                $session = static::get_session($key);
                if ($encrypt) {

                    if (!empty($session))
                        $session = EncryptData::decrypt($session);
                    else
                        $session = "";

                }

                return $session;
            default:
                return false;
        }

    }

    public static function remove_data(string $key, string $type)
    {

        switch ($type) {
            case 'cookie':
                return static::remove_cookies($key);
            case 'sqlite':
                return false;
            case 'session':
                return static::remove_session($key);
            default:
                return false;
        }

    }

}