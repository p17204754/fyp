<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;

$app->get('/report', function(Request $request, Response $response) use($app)
{

    $session_id = session_id();
    $loginAction = checkLogIn($app);
    $result = getReportDetails($app);
    //var_dump($result);

    return $this->view->render($response,
        'report.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'POST',
            'action3' => ADMIN,
            'action4' => LANDING_PAGE . '/book',
            'action5' => DOWNLOAD,
            'action6' => LANDING_PAGE . '/homepage',
            'print_page'=> LANDING_PAGE . '/report',
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "Home",
            'bookingAction' => "processbooking",
            'action2' => $loginAction['action'],
            'action7' => LANDING_PAGE . '/viewdeliveries',
            'actionButtonText2' => $loginAction['text'],
            'logInValue' => $loginAction['value'],
            'method2' => $loginAction['method'],
            'data_result' => $result['value'],
            'error_result' => $result['error'],
        ]);
});

function getReportDetails($app) {

    $result = [];

    $database_connection_settings = $app->getContainer()->get('doctrine_settings');
    $database_queries = $app->getContainer()->get('sqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings);

    $queryBuilder = $database_connection->createQueryBuilder();
    try {
        $result['value'] = $database_queries::RetrieveBookingDetails($queryBuilder);
        $result['error'] = '';
    } catch (exception $e) {
        echo 'An error occurred:' . $e->getMessage();
    }
    return $result;
}
