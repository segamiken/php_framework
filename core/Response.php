<?php
//リクエストに対するレスポンス。最終的にユーザーへ返すレスポンスの情報を管理するクラス
//HTTPヘッダとHTMLなどのコンテンツを返すのが主な役割

class Response {
    
    //contentプロパティにはHTMLなどの実際にクライアントに返す内容を格納する
    protected $content;
    protected $status_code = 200;
    //HTTPのステータスコードを格納
    protected $status_text = 'OK';
    //HTTPヘッダを格納
    protected $http_headers = array();


    //各プロパティに設定された値を元にレスポンスの送信を行う
    public function send() {
        header('HTTP/1.1' . $this->status_code . '' . $this->status_text);

        foreach ($this->http_headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->content;
    }


    //$contentを設定する
    public function setContent($content) {
        $this->content = $content;
    }

    //$status_codeを設定する
    public function setStatusCode($status_code, $status_text = '') {
        $this->status_code = $status_code;
        $this->status_text = $status_text;
    }

    //ヘッダの名前をキーに、ヘッダの内容を値にして連想配列形式で格納する
    public function setHttpHeader($name, $value) {
        $this->http_headers[$name] = $value;
    }
}