<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;



$app->get('/book', function(Request $request, Response $response) use($app)
{

    $session_id = session_id();
    $loginAction = checkLogIn($app);
    $products = getProducts($app);

    return $this->view->render($response,
        'booking.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'POST',
            'action3' => ADMIN,
            'action4' => LANDING_PAGE . '/book',
            'action5' => DOWNLOAD,
            'action6' => LANDING_PAGE . '/homepage',
            'action7' => LANDING_PAGE . '/viewdeliveries',
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "Home",
            'bookingAction' => "processbooking",
            'action2' => $loginAction['action'],
            'actionButtonText2' => $loginAction['text'],
            'logInValue' => $loginAction['value'],
            'method2' => $loginAction['method'],
            'products'=>$products['products'],
        ]);

});

function getProducts($app)
{
    $database_connection_settings = $app->getContainer()->get('doctrine_settings');
    $database_queries = $app->getContainer()->get('sqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings);


    $queryBuilder = $database_connection->createQueryBuilder();
    try{
        $fetch_result['products'] = $database_queries::RetrieveAllProducts($queryBuilder);
        $fetch_result['message'] = 'products fetched successfully';
    }catch(exception $e){
        $fetch_result['products'] = [];
        $fetch_result['message'] = 'An error occurred: ' . $e->getMessage();
    }


    //var_dump($fetch_result);
    return $fetch_result;
}





