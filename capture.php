<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/12/14
 * Time: 5:05 AM
 */
session_start();

$_SESSION['GoogleID'] = 'User1';
$_SESSION['UserID'] = 1;

var_dump($_SESSION);

?>


<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <script>

        </script>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/capture.css">

    </head>
    <body>

    <form action="magic/upload.php" enctype="multipart/form-data" method="POST">
        <button id="mainImageUploadButton" type="button" class="btn btn-primary btn-lg btn-block">
            Upload Image
            <input id="mainImageUpload" capture="camera" type="file" name="mainImageUpload" accept="image/*">
        </button>
        <input name="locationLong" id="locationLong" type="hidden">
        <input name="locationLat" id="locationLat" type="hidden">
        <input name="locationAcc" id="locationAcc" type="hidden">
        <button id="SubmitImageButton" type="submit" class="btn btn-primary btn-lg btn-block">
            Submit Image
        </button>

    </form>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery-1.10.2.min.js"><\/script>')</script>
    <script src="js/capture.js"></script>



    </body>
</html>