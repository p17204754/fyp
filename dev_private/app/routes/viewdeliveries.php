<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/viewdeliveries', function (Request $request, Response $response) use ($app) {

    $session_id = session_id();
    $loginAction = checkLogIn($app);


    return $this->view->render($response,
        'viewdeliveries_form.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'get',
            'postmethod' => 'post',
            'action3' => ADMIN,
            'action4' => LANDING_PAGE . '/book',
            'action5' => DOWNLOAD,
            'action6' => LANDING_PAGE . '/homepage',
            'action7' => LANDING_PAGE . '/viewdeliveries',
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "Home",
            'filterAction' => 'processfilterdeliveries',
            'allAction' => 'processalldeliveries',
            'action2' => $loginAction['action'],
            'actionButtonText2' => $loginAction['text'],
            'logInValue' => $loginAction['value'],
            'method2' => $loginAction['method'],
        ]);
});
