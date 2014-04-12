<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/12/14
 * Time: 10:50 AM
 */


$Connection = 'localhost';
$Database = 'mikeshi_LAHacks';
$Username = 'mikeshi_root';
$Password = 'q1234!@#$';

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

function dieJSON($code, $msg){
    $Return['code'] = $code;
    $Return['msg'] = $msg;
    die(json_encode($Return));
}