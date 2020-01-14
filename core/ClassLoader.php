<?php
//クラスを定義したファイルを自動的に読み込むためのオートロードを管理するクラス

class ClassLoader {
    protected $dirs;

    //PHPにオートローダクラスを登録する処理
    public function register() {
        //オートロードの登録。コールバック関数としてloadClassメソッドを呼ぶ。
        spl_autoload_register(array($this, 'loadClass'));
    }

    //読み込み対象のディレクトリを登録できる。引数にはオートロードの対象とするディレクトリへのフルパスを指定する。
    public function registerDir($dir) {
        $this->dirs[] = $dir;
    }

    //オートロードが実行された際にクラスファイルを読み込む処理
    public function loadClass($class) {
        foreach ($this->dirs as $dir) {
            $file = $dir . '/' . $class . '.php';
            if (is_readable($file)) {
                require $file;

                return;
            }
        }
    }
}