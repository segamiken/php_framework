<?php
//ユーザがアクセスしてきたURL(path_info)をRequestクラスから受け取り、どのコントローラを呼び出すかを決定するクラス

class Router {

    protected $routes;

    public function __construct($definitions){
        $this->routes = $this->compileRoutes($definitions);
    }

    //受け取ったルーティング定義配列中の動的パラメータ指定を正規表現で扱える形式に変換するメソッド
    public function compileRoutes($definitions) {
        $routes = array();

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (0 === strpos($token, ':')) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $token[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    //変換済みのルーティング定義配列とPATH_INFOのマッチングを行いルーティングパラセイリングの特定を行う
    public function resolve($path_info) {
        if ('/' !== substr($path_info, 0, 1)) {
            $path_info = '/' . $path_info;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#' , $path_info, $matches)) {
                $params = array_merge($params, $matches);

                return $params;
            }
        }

        return false;
    }

}