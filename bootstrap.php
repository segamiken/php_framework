<?php

require 'core/ClassLoader.php';

$loader = new ClassLoader();

//coreとmodelsディレクトリをオートロードの対象として登録する。
$loader->registerDir(dirname(__FILE__).'/core');
$loader->registerDir(dirname(__FILE__).'/models');
$loader->register();

