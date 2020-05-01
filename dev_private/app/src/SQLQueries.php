<?php
/**
 * Created by PhpStorm.
 * User: p17204754
 * Date: 06/11/2019
 * Time: 14:43
 */
namespace booking;

use DateTime;

class SQLQueries
{
    public function __construct()
    {
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public static function filterID($queryBuilder,  array $clean_parameters)
    {
        $searchTerm = $clean_parameters['sanitised_search_term'];

        $queryBuilder = $queryBuilder->select('booking_id', 'forename', 'surname', 'addressline1',
        'addressline2', 'addressline3', 'postcode','deliverydate', 'description','status')
        ->from('booking')
        ->where('booking_id = :searchTerm')
        ->setParameter('searchTerm', $searchTerm);
    $retrieved_messages = $queryBuilder->execute()->fetchAll();
    return $retrieved_messages;
    }

    public static function getTopTown($queryBuilder)
    {

        $queryBuilder = $queryBuilder->select('addressline3', 'count(addressline3) AS value')
            ->from('booking')
            ->groupBy('addressline3')
            ->orderBy('value', 'DESC')
            ->setMaxResults(1);
        $retrieved_messages = $queryBuilder->execute()->fetchAll();
        return $retrieved_messages;
    }

    public static function filterTown($queryBuilder,  array $clean_parameters)
    {
        $searchTerm = $clean_parameters['sanitised_search_term'];

        $queryBuilder = $queryBuilder->select('booking_id', 'forename', 'surname', 'addressline1',
            'addressline2', 'addressline3', 'postcode','deliverydate', 'description','status')
            ->from('booking')
            ->where('addressline3= :searchTerm')
            ->setParameter('searchTerm', $searchTerm);
        $retrieved_messages = $queryBuilder->execute()->fetchAll();
        return $retrieved_messages;
    }


    public static function getDeliveries($queryBuilder)
    {
        $queryBuilder = $queryBuilder->select('booking_id', 'forename', 'surname', 'addressline1',
            'addressline2', 'addressline3', 'postcode','deliverydate', 'description','status')
            ->from('booking')
            ->orderBy('booking_id', 'DESC');
        return $queryBuilder->execute()->fetchAll();
    }

    public static function getNewDeliveries($queryBuilder)
    {
        $date = new \DateTime("d-m-Y");
        $date->modify("-1 month");

        $queryBuilder = $queryBuilder->select('booking_id', 'forename', 'surname', 'addressline1',
            'addressline2', 'addressline3', 'postcode','deliverydate', 'description','status')
            ->from('booking')
            ->where('deliverydate >= :date')
            ->setParameter('date', $date);
        return $queryBuilder->execute()->fetchAll();
    }
    public static function getOldDeliveries($queryBuilder)
    {
        $date = new \DateTime("d-m-Y");
        $date->modify("-60 days");

        $queryBuilder = $queryBuilder->select('booking_id', 'forename', 'surname', 'addressline1',
            'addressline2', 'addressline3', 'postcode','deliverydate', 'description','status')
            ->from('booking')
            ->where('deliverydate >= :date')
            ->setParameter('date', $date);
        return $queryBuilder->execute()->fetchAll();
    }


    /**
     * Function to store user data in user_info table
     * @param $queryBuilder
     * @param array $clean_parameters
     * @param string $hashed_password
     * @return array
     */
    public static function StoreUserData($queryBuilder, array $clean_parameters,  $hashed_password)
    {
        $store_result = [];
        $username = $clean_parameters['sanitised_username'];
        $email = $clean_parameters['sanitised_email'];

        $queryBuilder = $queryBuilder->insert('user_info')
            ->values([
                'user_name' => ':name',
                'email' => ':email',
                'password' => ':password',
            ])
            ->setParameters([
                ':name' => $username,
                ':email' => $email,
                ':password' => $hashed_password
            ]);

        $store_result['outcome'] = $queryBuilder->execute();
        $store_result['sql_query'] = $queryBuilder->getSQL();

        return $store_result;
    }

    public static function StoreBooking($queryBuilder, array $clean_parameters)
    {
        $store_result = [];
        $forename = $clean_parameters['sanitised_forename'];
        $surname = $clean_parameters['sanitised_surname'];
        $addressline1 = $clean_parameters['sanitised_addressline1'];
        $addressline2 = $clean_parameters['sanitised_addressline2'];
        $addressline3 = $clean_parameters['sanitised_addressline3'];
        $postcode = $clean_parameters['sanitised_postcode'];
        $deliverydate = $clean_parameters['sanitised_deliverydate'];
        $description = $clean_parameters['sanitised_description'];
        $status = $clean_parameters['sanitised_status'];
        $email = $clean_parameters['sanitised_email'];
        $queryBuilder = $queryBuilder->insert('booking')
            ->values([
                'forename' => ':forename',
                'surname' => ':surname',
                'addressline1' => ':addressline1',
                'addressline2' => ':addressline2',
                'addressline3' => ':addressline3',
                'postcode' => ':postcode',
                'deliverydate' =>':deliverydate',
                'description' => ':description',
                'status' => ':status',
                'email' => ':email'

            ])
            ->setParameters([
                ':forename' => $forename,
                ':surname' => $surname,
                ':addressline1' => $addressline1,
                ':addressline2' => $addressline2,
                ':addressline3' => $addressline3,
                ':postcode' => $postcode,
                ':deliverydate' => $deliverydate,
                ':description' => $description,
                ':status' => $status,
                ':email' => $email
            ]);

        $store_result['outcome'] = $queryBuilder->execute();
        $store_result['sql_query'] = $queryBuilder->getSQL();

        return $store_result;
    }

    /**
     * Function to get user data using the user_name entered by the user
     * data found in the user_info table
     * @param $queryBuilder
     * @param array $cleaned_parameters
     * @return mixed
     */
    public static function RetrieveUserData($queryBuilder, array $cleaned_parameters)
    {
        $username = $cleaned_parameters['sanitised_username'];

        $queryBuilder = $queryBuilder->select('user_id','user_name', 'password')
            ->from('user_info')
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('user_name', '?')
            ))
            ->setParameter(0, $username);
        $retrieved_Items = $queryBuilder->execute()->fetchAll();
        $sql_query_string = '';
        return $retrieved_Items;
    }

    /**
     * Function to check whether user data already exists in the user_info table
     * @param $queryBuilder
     * @param array $cleaned_parameters
     * @return mixed
     */

    public static function CheckUserData($queryBuilder, array $cleaned_parameters)
    {
        $username = $cleaned_parameters['sanitised_username'];
        $email = $cleaned_parameters['sanitised_email'];

        $queryBuilder = $queryBuilder->select('user_id','email','user_name', 'password')
            ->from('user_info')
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('user_name', '?'),
                $queryBuilder->expr()->eq('email', '?')
            ))
            ->setParameters([0 => $username, 1 => $email]);
        $retrieved_Items = $queryBuilder->execute()->fetchAll();
        $sql_query_string = '';
        return $retrieved_Items;
    }

    /**
     * Function to retrieve all users
     * Uses on the admin interface
     * @param $queryBuilder
     * @return mixed
     */
    public static function RetrieveAllUsers($queryBuilder){
        $queryBuilder = $queryBuilder->select('user_id, user_name, email, role, lastLoggedIn, lastModified')
            ->from('user_info');
        $retrieved_Items = $queryBuilder->execute()->fetchAll();

        return $retrieved_Items;
    }
    public static function RetrieveAllProducts($queryBuilder){
        $queryBuilder = $queryBuilder->select('product_id, description')
            ->from('products');
        $retrieved_Items = $queryBuilder->execute()->fetchAll();

        return $retrieved_Items;
    }

    public static function RetrieveBookingDetails($queryBuilder) {
        $queryBuilder = $queryBuilder
            ->select('booking_id, forename, surname, addressline1, addressline2, addressline3, postcode', 'deliverydate', 'description', 'status')
            ->from('booking')
            ->orderBy('booking_id', 'DESC')
           ->setMaxResults(1);
        $retrieved_Items = $queryBuilder->execute()->fetchAll();

        return $retrieved_Items;
    }


    /**
     * Function to get user data based on the user ID given
     * used on the edit user data page. Access through admin interface
     * @param $queryBuilder
     * @param $user_id
     * @return mixed
     */
    public static function getUserDataToEdit($queryBuilder, $user_id){
        $id = $user_id;
        $queryBuilder = $queryBuilder->select('user_id, user_name, email, role')
            ->from('user_info')
            ->where($queryBuilder->expr()->eq('user_id', '?'))
            ->setParameter(0, $id);
        $retrieved_Items = $queryBuilder->execute()->fetchAll();

        return $retrieved_Items;
    }
    public static function getBookingDataToEdit($queryBuilder, $booking_id){
        $id = $booking_id;
        $queryBuilder = $queryBuilder->select('booking_id, status')
            ->from('booking')
            ->where($queryBuilder->expr()->eq('booking_id', '?'))
            ->setParameter(0, $id);
        $retrieved_Items = $queryBuilder->execute()->fetchAll();

        return $retrieved_Items;
    }

    /**
     * Retrieve the user role from the user_info page
     * used to check what role the user has to give authorization
     * @param $queryBuilder
     * @param $user_id
     * @return mixed
     */
    public static function RetrieveUserRole($queryBuilder,  $user_id){
        $id = $user_id;
        $queryBuilder = $queryBuilder->select('user_id, role')
            ->from('user_info')
            ->where($queryBuilder->expr()->eq('user_id', '?'))
            ->setParameter(0, $id);
        $retrieved_Items = $queryBuilder->execute()->fetchAll();

        return $retrieved_Items;
    }

    /**
     * Sets the data and time the user was last logged in
     * used when users log in
     * @param $queryBuilder
     * @param $user_id
     * @param $date
     * @return mixed
     */
    public static function setUserLastLogIn($queryBuilder, $user_id, $date){
        $queryBuilder = $queryBuilder->update('user_info')
            ->set('lastLoggedIn', '?')
            ->where($queryBuilder->expr()->eq('user_id', '?'))
            ->setParameters([0 => $date,1 => $user_id]);

        $update_outcome = $queryBuilder->execute();
        return $update_outcome;
    }

    /**
     * Update user data with the values entered by the admin on the edit user page
     * @param $queryBuilder
     * @param $user_id
     * @param $cleaned_parameters
     * @return mixed
     */
    public static function updateEditedUser($queryBuilder, $user_id, $cleaned_parameters){
        $id = $user_id;
        $username = $cleaned_parameters['sanitised_username'];
        $email = $cleaned_parameters['sanitised_email'];
        $role = $cleaned_parameters['sanitised_role'];
        $queryBuilder = $queryBuilder->update('user_info')
            ->set('user_name', '?')
            ->set('email', '?')
            ->set('role', '?')
            ->where($queryBuilder->expr()->eq('user_id', '?'))
            ->setParameters([0 => $username, 1 => $email, 2 => $role, 3 => $id]);
        $update_outcome = $queryBuilder->execute();

        return $update_outcome;
    }

    public static function updateEditedStatus($queryBuilder, $booking_id, $cleaned_parameters){
        $id = $booking_id;
        $status = $cleaned_parameters['sanitised_status'];

        $queryBuilder = $queryBuilder->update('booking')
            ->set('status', '?')
            ->where($queryBuilder->expr()->eq('booking_id', '?'))
            ->setParameters([0 => $status, 1 => $id]);
        $update_outcome = $queryBuilder->execute();

        return $update_outcome;
    }

    /**
     * Deletes user based on the selected user ID in the admin interface
     * @param $queryBuilder
     * @param $user_id
     * @return mixed
     */
    public static function deleteUser($queryBuilder, $user_id)
    {
        $id = $user_id;
        $queryBuilder = $queryBuilder->delete('user_info')
            ->where($queryBuilder->expr()->eq('user_id', '?'))
            ->setParameter(0, $id);
        $deletion_outcome = $queryBuilder->execute();

        return $deletion_outcome;
    }
}