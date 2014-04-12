<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/12/14
 * Time: 10:46 AM
 */

require 'db.php';

if(!isset($_POST['locationLong']) || !isset($_POST['locationLat']) ){
    //dieJSON(-1, 'Incomplete location information.');
}
if($_POST['locationLong'] == 0 || $_POST['locationLat'] == 0 ){
    //dieJSON(-1, 'Invalid location information');
}

$_POST['locationLong'] = -118.4352035;
$_POST['locationLat'] = 34.0661969;

$db = connect();
$ImageResults = GetNearbyImageRows($_POST['locationLong'], $_POST['locationLat'], $db);

foreach($ImageResults as $ImageResult){
    var_dump($ImageResult);
    echo '<br>';
}


echo "A!";

function GetNearbyImageRows($longStart, $latStart, PDO $db) {
    try
    {
        $query = $db->prepare("SELECT Latitude, Longitude, (
            POW(69.1 * (Latitude - ?), 2) +
            POW(69.1 * (? - Longitude) * COS(Latitude / 57.3), 2)) AS distance
            FROM Images HAVING distance < 4 ORDER BY distance;");

        $query->bindValue(1, $latStart);
        $query->bindValue(2, $longStart);
        $query->execute();

        return $query->fetchAll();
    } catch(PDOException $e)
    {
        print "Error on ".__FUNCTION__."!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}