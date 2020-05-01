<?php
/**
 * Created by PhpStorm.
 * User: p17204754
 * Date: 06/11/2019
 * Time: 14:48
 */


// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(
        $container['settings']['view']['template_path'],
        $container['settings']['view']['twig'],
        [
            'debug' => true // This line should enable debug mode
        ]
    );

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$container['flash'] = function(){
  return new \Slim\Flash\booking();
};

$container['xml_parser'] = function ($container){
  $parser = new \booking\XmlParser();
  return $parser;
};

$container['validator'] = function ($container){
    $validator = new \booking\Validator();
    return $validator;
};

$container['sessionModel'] = function ($container){
  $model = new \booking\SessionsModel();
  return $model;
};

$container['sessionWrapper'] = function ($container){
    $session_wrapper = new \booking\SessionWrapper();
    return $session_wrapper;
};


$container['databaseWrapper'] = function ($container){
    $database_wrapper = new \booking\DatabaseWrapper();
    return $database_wrapper;
};

$container['bcryptWrapper'] = function ($container){
    $bcrypt_wrapper = new \booking\BcryptWrapper();
    return $bcrypt_wrapper;
};

$container['sqlQueries'] = function () {
    $sql_queries = new \booking\SQLQueries();
    return $sql_queries;
};


