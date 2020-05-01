<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;

$app->get('/homepage', function(Request $request, Response $response) use($app)
{
    $session_id = session_id();
    $loginAction = checkLogIn($app);

    $getAll = getAll($app);
    $getNumberOfDeliveries = count($getAll['items']);

    $getNew = getNewDeliveries($app);
    $getNumberofNew = count($getNew);

    $getOld = getOldDeliveries($app);
    $getNumberOld = count($getOld);

    $getTop = getTopTown($app);



    return $this->view->render($response,
        'homepage.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'get',
            'action3' => ADMIN,
            'action4' => LANDING_PAGE . '/book',
            'action5' => DOWNLOAD,
            'action6' => LANDING_PAGE . '/homepage',
            'action7' => LANDING_PAGE . '/viewdeliveries',
            'action2' => $loginAction['action'],
            'actionButtonText2' => $loginAction['text'],
            'logInValue' => $loginAction['value'],
            'method2' => $loginAction['method'],
            'total' => $getNumberOfDeliveries,
            'monthTotal' => $getNumberofNew,
            'twoMonthTotal' => $getNumberOld,
            'topTown' => $getTop['items'][0]['addressline3'],

        ]);
});

function getNewDeliveries($app): array {
    try {
        $database_connection_settings = $app->getContainer()->get('doctrine_settings');
        $database_queries = $app->getContainer()->get('sqlQueries');
        $database_connection = DriverManager::getConnection($database_connection_settings);


        $queryBuilder = $database_connection->createQueryBuilder();
        $fetch_result['items'] = $database_queries::getNewDeliveries($queryBuilder);
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
function getOldDeliveries($app): array {
    try {
        $database_connection_settings = $app->getContainer()->get('doctrine_settings');
        $database_queries = $app->getContainer()->get('sqlQueries');
        $database_connection = DriverManager::getConnection($database_connection_settings);


        $queryBuilder = $database_connection->createQueryBuilder();
        $fetch_result['items'] = $database_queries::getOldDeliveries($queryBuilder);
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
function getTopTown($app): array {
    try {
        $database_connection_settings = $app->getContainer()->get('doctrine_settings');
        $database_queries = $app->getContainer()->get('sqlQueries');
        $database_connection = DriverManager::getConnection($database_connection_settings);


        $queryBuilder = $database_connection->createQueryBuilder();
        $fetch_result['items'] = $database_queries::getTopTown($queryBuilder);
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



