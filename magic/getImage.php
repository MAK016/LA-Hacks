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

//Set the time zone
date_default_timezone_set('America/Los_Angeles');

$_POST['locationLong'] = -118.4352035;
$_POST['locationLat'] = 34.0661969;

$db = connect();
$ImageResults = GetNearbyImageRows($_POST['locationLong'], $_POST['locationLat'], $db);

$JSONResult = array();

$ImagesJSONResult = array();

$JSONResult['MaxLat'] = -9999;
$JSONResult['MinLat'] = 9999;
$JSONResult['MaxLong'] = -9999;
$JSONResult['MinLong'] = 9999;

$maxPriority = -9001;

foreach($ImageResults as $ImageResult){
    $CurrentImage = array();

    //var_dump($ImageResult);

    //GD Downsize and then base64 encode
    $imageContents = imagecreatefromjpeg($ImageResult["ImagePath"]);
    $imageWidth = imagesx($imageContents);
    $imageHeight = imagesy($imageContents);
    $resizedImage = imagecreatetruecolor($imageWidth/2, $imageHeight/2);
    imagecopyresampled($resizedImage, $imageContents, 0,0,0,0,$imageWidth/2,$imageHeight/2,$imageWidth, $imageHeight);
    ob_start ();
    imagejpeg ($resizedImage);
    $image_data = ob_get_contents ();
    ob_end_clean ();
    $imageBase64Encoded = base64_encode ($image_data);
    $CurrentImage['Base64Image'] = $imageBase64Encoded;

    $CurrentImage['Width'] = floor($imageWidth/2);
    $CurrentImage['Height'] = floor($imageHeight/2);

    /*
    //Get the base64 encoding of the image
    $imageContents = file_get_contents($ImageResult["ImagePath"]);
    $imageBase64Encoded = base64_encode($imageContents);
    $CurrentImage['Base64Image'] = $imageBase64Encoded;
    */

    $CurrentImage['Priority'] = $ImageResult['Priority'];
    $CurrentImage['UploadTime'] = strtotime($ImageResult['UploadTime']);
    $CurrentImage['Latitude'] = $ImageResult['Latitude'];
    $CurrentImage['Longitude'] = $ImageResult['Longitude'];

    if($CurrentImage['Latitude']>$JSONResult['MaxLat']){
        $JSONResult['MaxLat'] = $CurrentImage['Latitude'];
    }
    if($CurrentImage['Latitude']<$JSONResult['MinLat']){
        $JSONResult['MinLat'] = $CurrentImage['Latitude'];
    }

    if($CurrentImage['Longitude']>$JSONResult['MaxLong']){
        $JSONResult['MaxLong'] = $CurrentImage['Longitude'];
    }
    if($CurrentImage['Longitude']<$JSONResult['MinLong']){
        $JSONResult['MinLong'] = $CurrentImage['Longitude'];
    }


    //Get the highest priority score for the image set
    if($CurrentImage['Priority']>$maxPriority){
        $maxPriority = $CurrentImage['Priority'];
    }

    $ImagesJSONResult[] = $CurrentImage;
}

$JSONResult['MaxPriority'] = $maxPriority;

$JSONResult['OriginLongitude'] = $_POST['locationLong'];
$JSONResult['OriginLatitude'] = $_POST['locationLat'];

$JSONResult['Images'] = $ImagesJSONResult;


echo json_encode($JSONResult);

function GetNearbyImageRows($longStart, $latStart, PDO $db) {
    try
    {
        $query = $db->prepare("SELECT ImagePath, Priority, UploadTime, Latitude, Longitude, (
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