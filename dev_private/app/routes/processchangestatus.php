<?php

/**
 * Created by PhpStorm.
 * User: arron
 * Date: 19/12/2019
 * Time: 21:27
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logged_user = checkUserRole($app);
$app->post('/processalldeliveries/processchangestatus', function (Request $request, Response $response) use ($app) {



    $tainted_parameters = $request->getParsedBody();
    $session_id = session_id();
    $clean_parameters = cleanBookingDetailsToSave($app, $tainted_parameters);

    $save_result = saveNewBookingDetails($app, $tainted_parameters['booking_id'], $clean_parameters);
    $store_result = [];

   // var_dump($clean_parameters['sanitised_status']);

    return $this->view->render($response,
        'alldeliveries_edit_status_result.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "Save result",
            'cleaned_status' => $clean_parameters['sanitised_status'],
            'save_result' => $save_result['outcome'],
            'action6' => LANDING_PAGE . '/homepage',
        ]);
})->setName('processchangestatus');

function cleanBookingDetailsToSave($app, array $tainted_parameters){
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');

    $tainted_status = $tainted_parameters['edited_status'];

    $cleaned_parameters['sanitised_status'] = $validator->sanitiseString($tainted_status);

    return $cleaned_parameters;
}

function saveNewBookingDetails($app, $booking_id, array $cleaned_parameters): array
{
    $storage_result = [];
    $store_result = [];
    if(!in_array(false, $cleaned_parameters)){
        try{
            $database_connection_settings = $app->getContainer()->get('doctrine_settings');
            $database_queries = $app->getContainer()->get('sqlQueries');
            $database_connection =  DriverManager::getConnection($database_connection_settings);
            $queryBuilder = $database_connection->createQueryBuilder();
            $storage_result['outcome'] = $database_queries::updateEditedStatus($queryBuilder, $booking_id, $cleaned_parameters);
            if($storage_result['outcome'] == 1 )
            {
                $store_result['outcome'] = 'Booking amendments saved successfully!';
            }
            else
            {
                $store_result['outcome'] = 'There was a problem saving edited values. Please try again';
            }
        }catch (exception $e){
            $store_result = 'An error occurred please try again' .$e->getCode();
        }
    }else {
        if ($cleaned_parameters['sanitised_status'] == false) {
            $store_result['outcome'] = 'Please enter a valid status';
        } else {
            $store_result['outcome'] = 'Something went wrong. PLease try again';
        }
    }
    return $store_result;
}