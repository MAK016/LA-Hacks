<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/12/14
 * Time: 5:05 AM
 */

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



    <button id="mainImageUploadButton" type="button" class="btn btn-primary btn-lg btn-block">
        Upload Image
        <input id="mainImageUpload" capture="camera" type="file" name="pic" accept="image/*">
    </button>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery-1.10.2.min.js"><\/script>')</script>
    <script src="js/capture.js"></script>



    </body>
</html>