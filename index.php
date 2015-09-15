<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Phalcon\DI\FactoryDefault,
      Phalcon\Mvc\Micro,
      Phalcon\Http\Response,
      Phalcon\Http\Request,
      Phalcon\Mvc\View\Simple,
      Phalcon\Mvc\View\Engine\Volt;

$di = new FactoryDefault();

$di["response"] = function () {
	return new Response();
};

$di["request"] = function() {
	return new Request();
};

$config = array(
	'viewsDir' => 'app/public/temps/'
);

$di['view'] = function () {
    $view = new Simple();
   	$view->setViewsDir('app/public/temps/');
    $view->registerEngines(array(
		'.volt' => function ($view, $di) {
            $volt = new Volt($view, $di);
            $volt->setOptions(array(
                'compiledPath' => 'app/public/temps/cache/',
                'compiledSeparator' => '_',
                'compileAlways' => true
            ));
            return $volt;
        },
    ));
    return $view;
};

$app = new Micro();

$app->setDI($di);

$routes = json_decode(file_get_contents("routes.json"));


foreach($routes as $route):

	$app->get($route->url, function () use ($app, $route, $config) {
		$temp = explode($config['viewsDir'],$route->templateUri);
		echo $app->view->render('index.volt', array('partials' => $temp[1]));
	});

endforeach;

$debug = new \Phalcon\Debug();
$debug->listen();

//try {

    $app->handle();

//} catch (\Exception $e) {

    //echo $e->getMessage();

//}
