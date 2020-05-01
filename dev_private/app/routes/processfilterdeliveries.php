<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;

$app->post('/processfilterdeliveries', function(Request $request, Response $response) use($app)
{
    $session_id = session_id();
    $tainted_parameters = $request->getParsedBody();
    $loginAction = checkLogIn($app);
    $clean_parameters = cleanSearchTerm($app, $tainted_parameters);
    $filterID = filterID($app, $clean_parameters);
    return $this->view->render($response,
        'filterdeliveries_result.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'POST',
            'methodGet' => 'get',
            'changeAction' => '/viewdeliveries/changestatus',
            'action3' => ADMIN,
            'action4' => LANDING_PAGE . '/book',
            'action5' => DOWNLOAD,
            'action6' => LANDING_PAGE . '/homepage',
            'action7' => LANDING_PAGE . '/viewdeliveries',
            'page_heading_2' => "Stored deliveries",
            'action2' => $loginAction['action'],
            'actionButtonText2' => $loginAction['text'],
            'searchAction' => 'processfilterdeliveries',
            'logInValue' => $loginAction['value'],
            'method2' => $loginAction['method'],
            'stored_deliveries' => $filterID['items'],


            'cleaned_search_term' => $clean_parameters['sanitised_search_term'],
        ]);

});

function cleanSearchTerm($app, $tainted_parameters){
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');
    $tainted_search_term= $tainted_parameters['search_term'];
    $cleaned_parameters['sanitised_search_term'] = $validator->sanitiseString($tainted_search_term);
    return $cleaned_parameters;
}


function filterID($app, array $cleaned_parameters):array {

    try{
        $database_connection_settings = $app->getContainer()->get('doctrine_settings');
        $database_queries = $app->getContainer()->get('sqlQueries');
        $database_connection = DriverManager::getConnection($database_connection_settings);


        $queryBuilder = $database_connection->createQueryBuilder();
        $fetch_result['items'] = $database_queries::filterID($queryBuilder, $cleaned_parameters);
        $fetch_result['error'] = '';

        if(sizeof($fetch_result['items'] ) == 0){
            $fetch_result['error'] = 'No messages were found';
        }
    }
    catch (exception $e){
        $fetch_result['items'] = [];
        $fetch_result['error'] = 'An error occurred ' . $e->getMessage();
    }
    return $fetch_result;
}