<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;
use PHPMailer\PHPMailer\PHPMailer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$app->post('/processbooking', function(Request $request, Response $response) use ($app)
{

    $tainted_parameters = $request->getParsedBody();
    $session_id = session_id();
    $clean_parameters = cleanBookingDetails($app, $tainted_parameters);
    $store_result = [];
    $booking_result = storeNewBooking($app, $clean_parameters);
    $email = sendMail($app, $clean_parameters);


    return $this->view->render($response,
        'booking_result.html.twig',
        [
            'css_path' => CSS_PATH,
            'home_page' => LANDING_PAGE . '/homepage',
            'print_page' => LANDING_PAGE . '/report',
            'action3' => ADMIN,
            'action5' => DOWNLOAD,
            'action7' => LANDING_PAGE . '/viewdeliveries',
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,

            'cleaned_forename' => $clean_parameters['sanitised_forename'],
            'cleaned_surname' => $clean_parameters['sanitised_surname'],
            'cleaned_addressline1' => $clean_parameters['sanitised_addressline1'],
            'cleaned_addressline2' => $clean_parameters['sanitised_addressline2'],
            'cleaned_addressline3' => $clean_parameters['sanitised_addressline3'],
            'cleaned_postcode' => $clean_parameters['sanitised_postcode'],
            'cleaned_deliverydate' => $clean_parameters['sanitised_deliverydate'],
            'cleaned_description' => $clean_parameters['sanitised_description'],
            'cleaned_status' => $clean_parameters['sanitised_status'],
            'cleaned_email' => $clean_parameters['sanitised_email'],


            'welcome_message' => $booking_result['welcomemsg'],
            'store_result' => $booking_result['outcome'],
            'email_result' => $email,
        ]);
});


function cleanbookingDetails($app, array $tainted_parameters){
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');

    $tainted_forename= $tainted_parameters['forename'];
    $tainted_surname = $tainted_parameters['surname'];
    $tainted_addressline1 = $tainted_parameters['addressline1'];
    $tainted_addressline2 = $tainted_parameters['addressline2'];
    $tainted_addressline3 = $tainted_parameters['addressline3'];
    $tainted_postcode = $tainted_parameters['postcode'];
    $tainted_deliverydate = $tainted_parameters['deliverydate'];
    $tainted_description = $tainted_parameters['description'];
    $tainted_status = $tainted_parameters['status'];
    $tainted_email = $tainted_parameters['email'];

    $cleaned_parameters['sanitised_forename'] = $validator->sanitiseString($tainted_forename);
    $cleaned_parameters['sanitised_surname'] = $validator->sanitiseString($tainted_surname);
    $cleaned_parameters['sanitised_addressline1'] = $validator->sanitiseString($tainted_addressline1);
    $cleaned_parameters['sanitised_addressline2'] = $validator->sanitiseString($tainted_addressline2);
    $cleaned_parameters['sanitised_addressline3'] = $validator->sanitiseString($tainted_addressline3);
    $cleaned_parameters['sanitised_postcode'] = $validator->sanitiseString($tainted_postcode);
    $cleaned_parameters['sanitised_deliverydate'] = $validator->sanitiseString($tainted_deliverydate);
    $cleaned_parameters['sanitised_description'] = $validator->sanitiseString($tainted_description);
    $cleaned_parameters['sanitised_status'] = $validator->sanitiseString($tainted_status);
    $cleaned_parameters['sanitised_email'] = $validator->sanitiseEmail($tainted_email);

    return $cleaned_parameters;
}

function checkBookingDetails($app, array $cleaned_parameters)
{
    $result = 0;

        if($cleaned_parameters['sanitised_forename'] == "")
        {
            $result = 1;
        }
        else if($cleaned_parameters['sanitised_surname'] == "")
        {
            $result = 2;
        }
        else if($cleaned_parameters['sanitised_addressline1'] == "")
        {
            $result = 3;
        }
        else if($cleaned_parameters['sanitised_addressline2'] == "")
        {
            $result = 4;
        }
        else if($cleaned_parameters['sanitised_addressline3'] == "")
        {
            $result = 5;
        }
        else if($cleaned_parameters['sanitised_postcode'] == "")
        {
            $result = 6;
        }
        else if($cleaned_parameters['sanitised_deliverydate'] == "")
        {
            $result = 9;
        }
        else if($cleaned_parameters['sanitised_description'] == "")
        {
            $result = 7;
        }
        else if($cleaned_parameters['sanitised_status'] == "")
        {
            $result = 8;
        }
        else if($cleaned_parameters['sanitised_email'] == "")
        {
            $result = 9;
        }

    return $result;
}




function storeNewBooking($app, array $cleaned_parameters): array
{
    $storage_result = [];
    $store_result = [];
    $check_details = checkBookingDetails($app, $cleaned_parameters);

    if($check_details === 0) {

        $database_connection_settings = $app->getContainer()->get('doctrine_settings');
        $database_queries = $app->getContainer()->get('sqlQueries');
        $database_connection = DriverManager::getConnection($database_connection_settings);

        $queryBuilder = $database_connection->createQueryBuilder();
        try {
            $storage_result = $database_queries::StoreBooking($queryBuilder, $cleaned_parameters);
        } catch (exception $e) {
            echo 'An error occurred:' . $e->getMessage();
        }

        if ($storage_result['outcome'] == 1) {
            $store_result['outcome'] = 'Your order was successfully booked';
            $store_result['welcomemsg'] = ' thank you for booking';
        } else {
            $store_result['outcome'] = 'There was a problem creating your booking. Please try again';
            $store_result['welcomemsg'] = '';
        }
    }

    else if($check_details === 1){
        $store_result['outcome'] = 'please enter a forename';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 2){
        $store_result['outcome'] = 'please enter a surname';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 3){
        $store_result['outcome'] = 'please enter addressline1';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 4){
        $store_result['outcome'] = 'please enter a addressline2';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 5){
        $store_result['outcome'] = 'please enter addressline3';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 6){
        $store_result['outcome'] = 'please enter a postcode';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 7){
        $store_result['outcome'] = 'please enter a description';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 8){
        $store_result['outcome'] = 'please enter a status';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 9){
        $store_result['outcome'] = 'please enter an email';
        $store_result['welcomemsg'] = '';
    }
    else if($check_details === 10){
        $store_result['outcome'] = 'please enter a delivery date';
        $store_result['welcomemsg'] = '';
    }

        return $store_result;
}

function sendMail($app, array $cleaned_parameters)
{
    $mail = new PHPMailer;
    $mail->isSMTP();

    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->Host='smtp.gmail.com';
    $mail->Username = 'fyptestemail123@gmail.com';
    $mail->Password = 'TestTest123';

    $mail->setFrom('fyptestemail123@gmail.com');
    $mail->addAddress($cleaned_parameters['sanitised_email']);

    $mail->isHTML(true);
    $mail->Subject = 'Booking conformation';
    $mail->Body = 'Greetings from Sapcote Garden Center. This email is to let you know that your order is being processed.';
    $mail->Send();

    if (!$mail->send()) {
        $return_result = "Error sending message" . $mail->ErrorInfo;
    } else {
        $return_result = "Message sent!";
    }
    return $return_result;
}
