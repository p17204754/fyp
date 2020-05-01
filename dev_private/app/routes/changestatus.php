<?php

/**
 * Created by PhpStorm.
 * User: arron
 * Date: 19/12/2019
 * Time: 19:35
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;


//$logged_user = checkUserRole($app);
$app->get('/processalldeliveries/changestatus', function (Request $request, Response $response) use ($app) {

    $route = $request->getAttribute('route');
    $booking_id = $request->getQueryParams('booking_id');

    $session_id = session_id();
    $tainted_details = getBookingDetails($app, $booking_id);
    $cleaned_details = cleanBookingDetailsToEdit($tainted_details['items'], $app);


    $loginAction = checkLogIn($app);


    return $this->view->render($response,
        'alldeliveries_edit_status.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'POST',
            'getmethod'=>'get',
            'action3' => ADMIN,
            'action4' => LANDING_PAGE . '/book',
            'action5' => DOWNLOAD,
            'action6' => LANDING_PAGE . '/homepage',
            'action7' => LANDING_PAGE . '/viewdeliveries',
            'filterAction' => 'processfilterdeliveries',
            'allAction' => 'processalldeliveries',
            'action2' => $loginAction['action'],
            'actionButtonText2' => $loginAction['text'],
            'logInValue' => $loginAction['value'],
            'method2' => $loginAction['method'],

            'action' => 'processchangestatus',
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "Edit user",
            'booking_id' => $booking_id['booking_id'],
            'status' => $cleaned_details['cleaned_status'],
            'booking_exists' => $cleaned_details['booking_exists']
        ]);

})->setName('changestatus');

function getBookingDetails($app, $booking_id)
{
    $id = $booking_id['booking_id'];

    $database_connection_settings = $app->getContainer()->get('doctrine_settings');
    $database_queries = $app->getContainer()->get('sqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings);


    $queryBuilder = $database_connection->createQueryBuilder();
    try{
        $fetch_result['items'] = $database_queries:: getBookingDataToEdit($queryBuilder, $id);
        $fetch_result['message'] = 'Booking data was successfully fetched';
    }catch (exception $e){
        $fetch_result['items'] = [];
        $fetch_result['message'] = 'An error occurred ' . $e->getMessage();
    }

    return $fetch_result;
}


function cleanBookingDetailsToEdit($tainted_details, $app)
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');


    if (!empty($tainted_details)) {
        $tainted_status = $tainted_details[0]['status'];

        $cleaned_parameters['booking_exists'] = true;
        $cleaned_parameters['message'] = 'Please enter the new status';
        $cleaned_parameters['cleaned_status'] = $validator->sanitiseString($tainted_status);
    } else {
        $cleaned_parameters['booking_exists'] = false;
        $cleaned_parameters['message'] = 'booking does not exist';
        $cleaned_parameters['cleaned_status'] = '';
    }
    return $cleaned_parameters;
}



