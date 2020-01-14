<?php
//セッション情報を管理するクラス

class Session
{
    protected static $sessionStarted = false;
    protected static $sessionIdRegenerated = false;

    //セッションの自動スタート
    //複数回実行されないように静的プロパティを用いてチャックしている
    public function __construct()
    {
        if(!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function  get($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return $default;
    }

    public function remove($name) 
    {
        unset($_SESSION[$name]);
    }

    public function clear()
    {
        $_SESSION = array();
    }

    //セッションIDを新しく発行するためのsession_regenerate_idを実行
    //複数回呼び出されないようにチェックしている
    public function regenerate($destroy = true)
    {
        if(!self::$sessionIdRegenerated) {
            session_regenerate_id($destroy);

            self::$sessionIdRegenerated = true;
        }
    }

    //ログイン状態の制御
    public function setAuthenticated($bool)
    {
        $this->set('_authenticated', (bool)$bool);

        $this->regenerate();
    }

    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}