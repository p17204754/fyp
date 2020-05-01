<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;

$app->get('/processalldeliveries', function (Request $request, Response $response) use ($app) {
    $session_id = session_id();
    $loginAction = checkLogIn($app);
    $getAll = getAll($app);
    return $this->view->render($response,
        'alldeliveries_result.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'get',
            'action3' => ADMIN,
            'action4' => LANDING_PAGE . '/book',
            'action5' => DOWNLOAD,
            'action6' => LANDING_PAGE . '/homepage',
            'action7' => LANDING_PAGE . '/viewdeliveries',
            'action8' => LANDING_PAGE . '/changeStatus',
            'page_heading_2' => "Stored deliveries",
            'action2' => $loginAction['action'],
            'actionButtonText2' => $loginAction['text'],
            'searchAction' => 'processallrdeliveries',
            'logInValue' => $loginAction['value'],
            'method2' => $loginAction['method'],
            'stored_deliveries' => $getAll['items'],

            'action' => 'processalldeliveries/changestatus',

        ]);

});


function getAll($app): array
{

    try {
        $database_connection_settings = $app->getContainer()->get('doctrine_settings');
        $database_queries = $app->getContainer()->get('sqlQueries');
        $database_connection = DriverManager::getConnection($database_connection_settings);


        $queryBuilder = $database_connection->createQueryBuilder();
        $fetch_result['items'] = $database_queries::getDeliveries($queryBuilder);
        $fetch_result['error'] = '';

        if (sizeof($fetch_result['items']) == 0) {
            $fetch_result['error'] = 'No messages were found';
        }
    } catch (exception $e) {
        $fetch_result['items'] = [];
        $fetch_result['error'] = 'An error occurred ' . $e->getMessage();
    }
    return $fetch_result;
}

function getBookingId($app){
    $session_wrapper = $app->getContainer()->get('sessionWrapper');
    $session_model = $app->getContainer()->get('sessionModel');
    $session_model->setSessionWrapper($session_wrapper);

    $booking_id = $session_model->getStoredValues('booking_id');
    return $booking_id;
}