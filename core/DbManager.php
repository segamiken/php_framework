<?php

class DbManager {
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

    protected $repository_connection_map = array();

    public function setRepositoryConnectionMap($repository_name, $name) 
    {
        $this->repository_connection_map[$repository_name] = $name;
    }

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