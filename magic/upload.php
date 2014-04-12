<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/12/14
 * Time: 6:53 AM
 */

session_start();

//TODO: Actually check if everything is set
if(!isset($_SESSION['UserID']) || !isset($_POST['locationLong'])){
    die('Invalid session or location!');
}

$Connection = 'localhost';
$Database = 'mikeshi_LAHacks';
$Username = 'mikeshi_root';
$Password = 'q1234!@#$';

$Directory = $_SERVER['DOCUMENT_ROOT'].'/Projects/LAHacks/images/';

$db = connect();
$UploadedImage = SaveImageToDrive();

UploadNewImage($_SESSION['UserID'], $UploadedImage, $_POST['locationLong'], $_POST['locationLat'], $_POST['locationAcc'], $db);

//Save to image directory and then return the file name.
function SaveImageToDrive(){
    global $Directory;
    $target_Path = $Directory;
    $target_Path = tempnam($target_Path,'')."jpg";
    move_uploaded_file($_FILES['mainImageUpload']['tmp_name'], $target_Path );

    $src_img = imagecreatefromjpeg($target_Path);
    image_fix_orientation($src_img, $target_Path);
    imagejpeg($src_img, $target_Path, 100);

    return $target_Path;
}

function image_fix_orientation(&$image, $filename) {
    $exif = exif_read_data($filename);

    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
    }
}

//Add new image entry into the DB
function UploadNewImage($User_idUser, $ImagePath, $Longitude, $Latitude, $Accuracy, PDO $db) {
    try
    {
        //$db = connect();
        $query = $db->prepare("INSERT INTO Images (Users_idUsers, ImagePath, Longitude, Latitude, Accuracy, UploadTime, Priority) VALUES (?, ?, ?, ? ,?, NOW(), 0)");

        $query->bindValue(1, $User_idUser);
        $query->bindValue(2, $ImagePath);
        $query->bindValue(3, $Longitude);
        $query->bindValue(4, $Latitude);
        $query->bindValue(5, $Accuracy);
        $query->execute();

        return $db->lastInsertId();
    } catch(PDOException $e)
    {
        print "Error on ".__FUNCTION__."!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function connect()
{
    try
    {
        global $Connection, $Database, $Username, $Password;
        $db = new PDO("mysql:host={$Connection};dbname={$Database}", $Username, $Password);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch(PDOException $e)
    {
        print "Error on ".__FUNCTION__."!: " . $e->getMessage() . "<br/>";
        $db = null;
        die();
    }
}