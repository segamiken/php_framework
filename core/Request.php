<?php
//ユーザーがリクエストした際のHTTPメソッドやURLなどの情報を管理するクラス

class Request 
{
    //HTTPメソッドがPOSTかどうかを判定する
    public function isPost() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }
        return false;
    }

    //GET変数から値を取得するメソッド
    public function getGet($name, $default = null) 
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return $default;
    }

    //POST変数から値を取得するメソッド
    public function getPost($name, $default = null) 
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return $default;
    }

    //サーバーのホスト名を取得するメソッド。このホスト名はリダイレクトを行う場合などに利用する
    //リクエストヘッダに含まれていない場合もあるので、その場合はApache側に設定されたホスト名が格納されているSERVER_NAMEの値を返す。
    public function getHost() 
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        return $_SERVER['SERVER_NAME'];
    }

    //HTTPSでアクセスされたかの判定
    public function isSsl() 
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }
        return false;
    }

    //リクエストされたURLの情報を取得。URLのホスト部分以降の値。
    public function getRequestUrl() 
    {
        return $_SERVER['REQUEST_URL'];
    }

    //
    public function getBaseUrl() 
    {
        $script_name = $_SERVER['SCRIPT_NAME'];

        $request_uri = $this->getRequestUrl();

        //strpos関数は第一引数に指定した文字列中から第二引数にしていた文字列が最初に出現する位置をしらべる関数
        if (0 === strpos($request_uri, $script_name)) {
            return $script_name;
        } else if (0 === strpos($request_uri, dirname($script_name))) {
            return rtrim(dirname($script_name), '/');
        }

        return '';
    }


    public function getPathInfo() 
    {
        $base_url = $this->getBaseUrl();
        $request_uri = $this->getRequestUrl();
        
        //REQUEST_URLに含まれるGETパラメータを取り除く処理
        if (false !== ($pos = strpos($request_uri, '?'))) {
            //substr関数は、第一引数で指定した文字列のうち、第二引数で指定した位置から第三引数で指定した文字数文取得する関数。
            $request_uri = substr($request_uri, 0, $pos);
        }

        $path_info = (string)substr($request_uri, strlen($base_url));

        return $path_info;
    }
}