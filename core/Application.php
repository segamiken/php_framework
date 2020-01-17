<?php
//全体の処理の流れの管理。RequestクラスやSessionクラスの初期化、コントローラを呼び出しアクションの実行などを行うクラス。

abstract class Application
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;
    protected $login_action = array();

    //３つのメソッドをまず実行
    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    //デバッグモードに応じてエラー表示処理を変更する
    protected function setDebugMode($debug)
    {   
        if($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    //クラスの初期化処理。Routerクラスはインスタンス作成時にルーティング定義配列を渡すようにしているので、ルーティング定義配列を返すregisterRoutesメソッドを呼び出している。
    protected function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db_manager = new DbManager();
        $this->router = new Router($this->registerRoutes());
    }

    protected function configure()
    {

    }

    //アプリケーションのルートディレクトリへのパスを返すメソッド。
    abstract public function getRootDir();

    abstract protected function registerRoutes();

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getDbManager()
    {
        return $this->db_manager;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    //Routerからコントローラを特定しレスポンスの送信を行う。
    //Routerクラスのresolveメソッドを呼び出してルーティングパラメータを取得し、コントローラとアクション名を特定する。
    public function run()
    {
    try {
        $params = $this->router->resolve($this->request->getPathInfo());
        if ($params === false) {
            //例外処理
            throw new HttpNotFoundException('No route found for' . $this->request->getPathInfo());
        }

        $controller = $params['controller'];
        $action = $params['action'];

        $this->runAction($controller, $action, $params);

        $this->response->send();
    
    } catch (HttpNotFoundException $e) {
        $this->render404Page($e);

    } catch (UnauthorizedActionException $e) {
        list($controller, $action) = $this->login_action;
        $this->runAction($controller, $action);
    }
    }

    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
        
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >
        <html lang="en">
        <head>
            <meta http-equiv="Content-Type content=text/html"; charset="UTF-8" />
            <title>404</title>
        </head>
        <body>
            {$message}
        </body>
        </html>
        
        EOF
        );
    }

    //実際にアクションを実行する.
    public function runAction($controller_name, $action, $params = array())
    {   //ucfirstは先頭を大文字にする関数
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);
        if ($controller === false) {
            //例外処理
            throw new HttpNotFoundException($controller_class . 'controller is not found.');
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    //Controllerの作成
    protected function findController($controller_class)
    {
        if (!class_exists($controller_class)){
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';

            if (!is_readable($controller_file)) {
                return false;
            } else {
                require_once $controller_file;

                if (!class_exists($controller_class)) {
                    return false;
                }
            }
        }
        return new $controller_class($this);
    }


}
