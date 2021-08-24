<?php
session_start();
$session = $_SESSION['session'];
if (isset($_SESSION['ip']) and isset($_SESSION['port']) and !empty($_SESSION['ip']) and !empty($_SESSION['port']))
{
    $ip = $_SESSION['ip'];
    $port = $_SESSION['port'];
    $verb = "$ip:$port";
}
else
{
    $label = $_SESSION['label'];
    $verb = $label;
}

// var_dump($_GET);
$grantall = $_GET['grantall'];
$overwrite = $_GET['overwrite'];
$downgrade = $_GET['downgrade'];
$installpath = $_GET['installpath'];
if (isset($_FILES['file']['name']))
{

    /* Getting file name */
    $filename = "." . $_FILES['file']['name'];

    /* Location */
    $location = "../users/$session/apk/" . $filename;
    $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
    $imageFileType = strtolower($imageFileType);

    /* Valid extensions */
    $valid_extensions = array(
        "apk"
    );

    /* Check file extension */
    if (in_array(strtolower($imageFileType) , $valid_extensions))
    {
        /* Upload file */
        move_uploaded_file($_FILES['file']['tmp_name'], $location);
        shell_exec("adb -s $verb push $location /sdcard/Download");
        if (isset($grantall) and $grantall == "true" and !empty($grantall))
        {
            $grantall = "-g";
        }
        else
        {
            $grantall = "";

        }
        if (isset($overwrite) and $overwrite == "true" and !empty($overwrite))
        {
            $overwrite = "-r";
        }
        else
        {
            $overwrite = "";

        }
        if (isset($downgrade) and $downgrade == "true" and !empty($downgrade))
        {
            $downgrade = "-d";
        }
        else
        {
            $downgrade = "";

        }
        if (isset($installpath) and $installpath == "true" and !empty($installpath))
        {
            $installpath = "-s";
        }
        else
        {
            $installpath = "-f";

        }

        $response = shell_exec("adb -s $verb shell pm install $grantall $overwrite $downgrade $installpath /sdcard/Download/$filename");
        shell_exec("adb -s $verb shell rm -rf /sdcard/Download/$filename");

        if (strpos($response, "Success") !== false)
        {
            echo "Success";
        }
        // echo $grantall;
        

        
    }

}