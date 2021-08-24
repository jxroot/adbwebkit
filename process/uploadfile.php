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
$path = $_GET['path'];
if (isset($_FILES['uploadfile']['name']))
{
    /* Getting file name */
    $filename = $_FILES['uploadfile']['name'];
    /* Location */
    $location = "../users/$session/fileupload/" . $filename;

    move_uploaded_file($_FILES['uploadfile']['tmp_name'], $location);

    shell_exec("adb -s $verb push $location \"$path/$filename\"");
    echo $path;
}