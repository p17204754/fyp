<?php
/**
 * Created by PhpStorm.
 * User: p17204754
 * Date: 06/11/2019
 * Time: 14:41
 */

namespace booking;

class DatabaseWrapper
{
    private $database_connection_settings;
    private $database_handle;
    private $sql_queries;
    private $fetchedItem;
    private $errors;

    public function __construct()
    {
        $this->database_connection_settings = null;
        $this->database_handle = null;
        $this->sql_queries = null;
        $this->fetchedItem = null;
        $this->errors = [];
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }


    public function setDatabaseConnectionSettings($database_connection_settings){
        $this->database_connection_settings= $database_connection_settings;
    }


    public function makeDatabaseConnection(){
        $pdo = false;
        $error = '';

        $database_settings = $this->database_connection_settings;
        $host_name = $database_settings['rdbms']. ':host=' . $database_settings['host'];
        $port_number = ';port=' . '3306';
        $user_database = ';dbname=' . $database_settings['db_name'];
        $host_details = $host_name . $port_number . $user_database;
        $user_name = $database_settings['user_name'];
        $user_password = $database_settings['user_password'];
        $pd_attributes = $database_settings['options'];

        try{
            $pdo_handle = new \PDO($host_details, $user_name, $user_password, $pd_attributes);
            $this->database_handle = $pdo_handle;
        }
        catch (\PDOException $exception){
            trigger_error('error when connecting to database');
            $error = 'error when connecting to the database';
        }
        return $error;
    }


    public function setSqlQueries($sql_queries){
        $this->sql_queries = $sql_queries;
    }


    public function unsetStringVar($session_key){}


    public function getSessionValues($session_key){
        $var_exists = false;
        $query_string = $this->sql_queries->checkStringVar();

        $query_parameters= [
            ':id' => session_id(),
            ':var_name' => $session_key
        ];

        $this->safeQuery($query_string, $query_parameters);

        if($this->countRows() > 0){
            $var_exists = true;
        }
        return $var_exists;
    }


    public function setSessionValues($session_key, $session_value){
        if($this->getSessionValues($session_key) == true){
            $this->storeSessionValues($session_key, $session_value);
        }
        else{
            $this->createSessionValues($session_key, $session_value);
        }

        return($this->errors);
    }


    private function createSessionValues($session_key, $session_value)
    {
        $query_string = $this->sql_queries->createSessionValues();

        $query_parameters = [
            ':id' => session_id(),
            ':var_name' => $session_key,
            ':var_value' => $session_value
        ];

        $this->safeQuery($query_string, $query_parameters);
    }


    private function storeSessionValues($session_key, $session_value)
    {
        $query_string = $this->sql_queries->setSessionValues();

        $query_parameters = [
            ':id' => session_id(),
            ':var_name' => $session_key,
            ':var_value' => $session_value
        ];

        $this->safeQuery($query_string, $query_parameters);
    }


    private function safeQuery($query_string, $params = null)
    {
        $this->errors['db_error'] = false;
        $query_parameters = $params;

        try
        {
            $temp = array();

            $this->fetchedItem = $this->database_handle->prepare($query_string);

            // bind the parameters
            if (sizeof($query_parameters) > 0)
            {
                foreach ($query_parameters as $param_key => $param_value)
                {
                    $temp[$param_key] = $param_value;
                    $this->fetchedItem->bindParam($param_key, $temp[$param_key], \PDO::PARAM_STR);
                }
            }
            // execute the query
            $execute_result = $this->fetchedItem->execute();
            $this->errors['execute-OK'] = $execute_result;
        }
        catch (PDOException $exception_object)
        {
            $error_message  = 'PDO Exception caught. ';
            $error_message .= 'Error with the database access.' . "\n";
            $error_message .= 'SQL query: ' . $query_string . "\n";
            $error_message .= 'Error: ' . var_dump($this->fetchedItem->errorInfo(), true) . "\n";
            // NB would usually output to file for sysadmin attention
            $this->errors['db_error'] = true;
            $this->errors['sql_error'] = $error_message;
        }
        return $this->errors['db_error'];
    }


    public function countRows()
    {
        $num_rows = $this->fetchedItem->rowCount();
        return $num_rows;
    }


    public function safeFetchRow()
    {
        $record_set = $this->fetchedItem->fetch(PDO::FETCH_NUM);
        return $record_set;
    }


    public function safeFetchArray()
    {
        $row = $this->fetchedItem->fetch(PDO::FETCH_ASSOC);
        $this->fetchedItem->closeCursor();
        return $row;
    }


    public function lastInsertedID()
    {
        $sql_query = 'SELECT LAST_INSERT_ID()';

        $this->safeQuery($sql_query);
        $last_inserted_id = $this->safeFetchArray();
        $last_inserted_id = $last_inserted_id['LAST_INSERT_ID()'];
        return $last_inserted_id;
    }


}