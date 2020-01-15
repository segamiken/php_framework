<?php
//データベースへの接続情報やDbRepositoryクラスを管理するクラス

class DbManager {
    //接続情報であるPDOクラスのインスタンスを配列で保持
    protected $connections = array();

    //データベースとの接続を行うメソッド
    //$nameは接続を特定するための名前
    public function connect ($name, $params) 
    {
        $params = array_merge(array (
            'dsn' => null,
            'user' => '',
            'password' => '',
            'options' => array(),
        ), $params);

        $con = new PDO(
            $params['dsn'],
            $params['user'],
            $params['password'],
            $params['options']
        );

        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connections[$name] = $con;
    }

    //$connectionsプロパティには接続情報であるPDOクラスのインスタンスを配列で保存する。
    public function getConnection($name = null) 
    {
        if (is_null($name)) {
            return current($this->connections);            
        }
        return $this->connections[$name];
    }

    //テーブル毎のRepositoryクラスと接続名の対応を格納
    protected $repository_connection_map = array();

    public function setRepositoryConnectionMap($repository_name, $name) 
    {
        $this->repository_connection_map[$repository_name] = $name;
    }

    //Repositoryクラスに対応する接続を取得しようとした際に、$repository_connection_mapに設定されているものはgetConnetctionメソッドに接続名を指定し、それ以外は最初に作成したものを取得する。
    public function getConnectionForRepository($repository_name) 
    {
        if (isset($this->repository_connection_map[$repository_name])) {
            $name = $this->repository_connection_map[$repository_name];
            $con = $this->getConnection();
        } else {
            $con = $this->getConnection();
        }
        return $con;
    }

    //Repositoryクラスの管理
    protected $repositories = array();

    public function get($repository_name) 
    {
        if(!isset($this->repositories[$repository_name])) {
            $repository_class = $repository_name . 'Repository';
            $con = $this->getConnectionForRepository($repository_name);

            $repository = new $repository_class($con);

            $this->repositories[$repository_name];
        }
    }

    //データベースとの接続を開放する処理
    public function __destruct()
    {
        foreach ($this->repositories as $repository) {
            unset($repository);
        }

        foreach ($this->connections as $con) {
            unset($con);
        }
    }
}