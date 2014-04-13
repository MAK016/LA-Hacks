<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/12/14
 * Time: 6:53 AM
 */

require 'db.php';

session_start();

//TODO: Actually check if everything is set
if(!isset($_SESSION['UserID']) || !isset($_POST['locationLong'])){
    dieJSON(-1, 'Incomplete form.');
}
if($_POST['locationLong'] == 0 || $_POST['locationLat'] == 0 ){
    dieJSON(-1, 'Location is not locked on.');
}

$Directory = $_SERVER['DOCUMENT_ROOT'].'/Projects/LAHacks/images/';
//$Directory = $_SERVER['DOCUMENT_ROOT'].'/images/';

$db = connect();
$UploadedImage = SaveImageToDrive();

UploadNewImage($_SESSION['UserID'], $UploadedImage, $_POST['locationLong'], $_POST['locationLat'], $_POST['locationAcc'], $db);

//Save to image directory and then return the file name.
function SaveImageToDrive(){
    global $Directory;

    $target_Path = $Directory;
    $temp_nam = tempnam($target_Path,'');
    unlink($temp_nam);
    $target_Path = $temp_nam.".jpg";
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
