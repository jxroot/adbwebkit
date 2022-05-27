<?php
require_once "function.php";
session_start();

if (!isset($_SESSION['ip']) and !isset($_SESSION['port']) and !isset($_SESSION['session']) and !isset($_SESSION['label']))
{
    if (!empty($_POST['session']) and !empty($_POST['ip']) and !empty($_POST['port']) and filter_var($_POST['ip'], FILTER_VALIDATE_IP) and $_POST['port'] <= 65535 and !$_POST['port'] == 0 and is_numeric($_POST['port']) or isset($_POST['label']) and !empty($_POST['label']))
    {
        $data = $_POST;

        $_SESSION['session'] = $data['session'];
        if (isset($_POST['label']) and !empty($_POST['label']))
        {
            $_SESSION['label'] = $data['label'];
        }
        else
        {
            $_SESSION['ip'] = $data['ip'];
            $_SESSION['port'] = $data['port'];
            $verb = $data['ip'] . ':' . $data['port'];
            shell_exec("adb tcpip" . $data['port']);
            shell_exec("adb connect $verb");
        }

        $pathname = $data['session'];
        mkdir("../users");
        $path = "../users/$pathname";
        mkdir($path);
        mkdir($path . "/download");
        mkdir($path . "/videorecorder");
        mkdir($path . "/screenshot");
        mkdir($path . "/fileupload");
        mkdir($path . "/download/apk");
        mkdir($path . "/download/jpeg");
        mkdir($path . "/download/jpg");
        mkdir($path . "/download/m4a");
        mkdir($path . "/download/mp3");
        mkdir($path . "/download/mp4");
        mkdir($path . "/download/other");
        mkdir($path . "/download/pdf");
        mkdir($path . "/download/png");
        mkdir($path . "/download/txt");
        mkdir($path . "/download/wav");
        mkdir($path . "/download/folder");
        mkdir($path . "/download/newfile");
        mkdir($path . "/download/xml");
        mkdir($path . "/download/rc");
        mkdir($path . "/download/sh");
        mkdir($path . "/download/py");
        mkdir($path . "/download/php");
        mkdir($path . "/download/c");
        mkdir($path . "/download/cpp");
        mkdir($path . "/download/bin");
        mkdir($path . "/download/prop");
        mkdir($path . "/download/ogg");
        mkdir($path . "/download/zip");
        mkdir($path . "/download/rar");
        mkdir($path . "/download/mkv");
        mkdir($path . "/download/log");
        mkdir($path . "/download/avi");
        mkdir($path . "/download/conf");
        mkdir($path . "/download/html");
        mkdir($path . "/download/so");
        mkdir($path . "/download/vcf");
        mkdir($path . "/download/ps1");
        mkdir($path . "/download/exe");
        mkdir($path . "/download/gif");

        mkdir($path . "/apk");

    }
    else
    {
        echo "ERROR";
    }

}
else
{
    $list = ["android.permission.CAMERA", "android.permission.WAKE_LOCK", "android.permission.WRITE_EXTERNAL_STORAGE", "android
    android.permission.INTERNET", "android.permission.ACCESS_NETWORK_STATE", "android.permission.READ_SMS", "android.permission.SEND_SMS", "android.permission.RECEIVE_BOOT_COMPLETED", "android.permission.READ_PHONE_STATE", "android.permission.READ_EXTERNAL_STORAGE", "android.permission.READ_CALL_LOG", "android.permission.RECORD_AUDIO", "android.permission.MODIFY_AUDIO_SETTINGS", "android.permission.ACCESS_FINE_LOCATION", "android.permission.READ_CONTACTS"];
    $listformat = ["txt", "xml", "sh", "bin", "prop", "sh", "cpp", "c", "png", "jpg", "jpeg", "rc", "mp4", "mp3", "ogg", "wav", "log", "avi", "mkv", "rar", "apk", "zip", "conf", "py", "php", "html", "so", "m4a", "pdf", "vcf", "ps1", "exe", "gif"];
    $listcode = ["a" => "KEYCODE_A", "b" => "KEYCODE_B", "c" => "KEYCODE_C", "d" => "KEYCODE_D", "e" => "KEYCODE_E", "f" => "KEYCODE_F", "g" => "KEYCODE_G", "h" => "KEYCODE_H", "i" => "KEYCODE_I", "j" => "KEYCODE_J", "k" => "KEYCODE_K", "l" => "KEYCODE_L", "m" => "KEYCODE_M", "n" => "KEYCODE_N", "o" => "KEYCODE_O", "p" => "KEYCODE_P", "q" => "KEYCODE_Q", "r" => "KEYCODE_R", "s" => "KEYCODE_S", "t" => "KEYCODE_T", "u" => "KEYCODE_U", "v" => "KEYCODE_V", "w" => "KEYCODE_W", "x" => "KEYCODE_X", "y" => "KEYCODE_Y", "0" => "KEYCODE_0", "1" => "KEYCODE_1", "2" => "KEYCODE_2", "3" => "KEYCODE_3", "4" => "KEYCODE_4", "5" => "KEYCODE_5", "6" => "KEYCODE_6", "7" => "KEYCODE_7", "8" => "KEYCODE_8", "9" => "KEYCODE_9", "Enter" => "KEYCODE_66", "@" => "KEYCODE_AT", "\\" => "KEYCODE_BACKSLASH", ";" => "KEYCODE_SEMICOLON", "/" => "KEYCODE_SLASH", "*" => "KEYCODE_STAR", "#" => "KEYCODE_POUND", "ArrowUp" => "KEYCODE_DPAD_UP", "ArrowDown" => "KEYCODE_DPAD_DOWN", "ArrowLeft" => "KEYCODE_DPAD_LEFT", "ArrowRight" => "KEYCODE_DPAD_RIGHT", "Backspace" => "KEYCODE_DEL", "-" => "KEYCODE_MINUS", "Tab" => "KEYCODE_TAB", "," => "KEYCODE_COMMA", "Alt" => "KEYCODE_ALT_LEFT", "Shift" => "KEYCODE_SHIFT_LEFT", "." => "KEYCODE_PERIOD", "`" => "KEYCODE_GRAVE", "=" => "KEYCODE_EQUALS", "+" => "KEYCODE_PLUS", "PageUp" => "KEYCODE_PAGE_UP", "PageDown" => "KEYCODE_PAGE_DOWN", "Home" => "KEYCODE_MOVE_HOME", "End" => "KEYCODE_MOVE_END", "Escape" => "4", "F12" => "KEYCODE_SPACE",

    ];
    $session = $_SESSION['session'];

    if (isset($_SESSION['label']))
    {
        $label = $_SESSION['label'];

        $verb = $label;
    }
    else
    {
        $ip = $_SESSION['ip'];
        $port = $_SESSION['port'];

        $verb = "$ip:$port";

    }

    $cookie_name = "path";
    $cookie_value = $session;
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30) , "/"); // 86400 = 1 day
    
}
// Shell
if (isset($_POST['shell']) and !empty($_POST['shell']))
{
    $command = $_POST['shell'];
    $res = shell_exec("adb -s $verb shell $command");
    $result = str_replace("^", "", $res);
    echo $result;
}
// Uninstall Apk
if (isset($_POST['uninstall']) and !empty($_POST['uninstall']))
{
    $command = $_POST['uninstall'];
    $res = shell_exec("adb -s $verb uninstall $command");
    if (strpos($res, "Success") !== false)
    {
        echo "OK";
    }

}
// Clear Apk
if (isset($_POST['clearapk']) and !empty($_POST['clearapk']))
{
    $command = $_POST['clearapk'];
    $res = shell_exec("adb -s $verb shell pm clear $command");
    if (strpos($res, "Success") !== false)
    {
        echo "OK";
    }

}
// Run Apk
if (isset($_POST['runapk']) and !empty($_POST['runapk']))
{

    $command = str_replace("\n", "", $_POST['runapk']);
    $res = shell_exec("adb -s $verb shell monkey -p $command -v 1 ");
    if (strpos($res, "finished") !== false)
    {
        echo "OK";
    }

}

// list Apk
if (isset($_POST['applist']) and !empty($_POST['applist']))
{
    $appmode = $_POST['applist'];
    $result = shell_exec("adb -s $verb shell $appmode");
    $replace = str_replace("package:", "", $result);
    echo $replace;

}
// downloadapk
if (isset($_POST['downloadapk']) and !empty($_POST['downloadapk']))
{
    $pkg = $_POST['downloadapk'];
    $getpath = shell_exec("adb -s $verb shell pm path $pkg");
    $clear = trim(str_replace("package:", "", $getpath));
    $result = shell_exec("adb -s $verb pull $clear ../users/$session/download/apk/$pkg.apk");

    echo $result;
    if (strpos($result, "error") !== false)
    {
        echo "ERROR";
    }
}

if (isset($_POST['stopapk']) and !empty($_POST['stopapk']))
{

    $pkg = $_POST['stopapk'];
    $result = shell_exec("adb -s $verb shell am force-stop $pkg");

}
// Reset All Permission
if (isset($_POST['resetall']))
{
    $result = shell_exec("adb -s $verb shell pm reset-permissions");
    if (strpos($result, "error") !== false)
    {
        echo "ERROR";
    }
}
// Screenshot
if (isset($_POST['screenshot']))
{
    $namefs = rand();
    shell_exec("adb -s $verb shell screencap  /sdcard/Download/.$namefs.png");
    $path = realpath("../users/$session/screenshot");
    shell_exec("adb -s $verb pull /sdcard/Download/.$namefs.png  $path");
    shell_exec("adb -s $verb shell rm -rf /sdcard/Download/.$namefs.png");
    echo "users/$session/screenshot/.$namefs.png";

}
// Recored Screen
if (isset($_POST['recoredscreen']) and !empty($_POST['recoredscreen']) and is_numeric($_POST['recoredscreen']))
{
    $time = trim($_POST['recoredscreen']);
    $newnf = rand();

    shell_exec("adb -s $verb  shell \"screenrecord --time-limit=$time /sdcard/Download/.$newnf.mp4\"");
    $path = realpath("../users/$session/videorecorder");
    shell_exec("adb -s $verb  pull /sdcard/Download/.$newnf.mp4  $path");
    shell_exec("adb -s $verb  shell rm -rf /sdcard/Download/.$newnf.mp4");
    echo "users/$session/videorecorder/.$newnf.mp4";

}
// Get Full Info
if (isset($_POST['infofull']))
{
    $result = shell_exec("adb -s $verb shell getprop");
    $list = explode("\n", $result);
    foreach ($list as $data)
    {
        $level1 = str_replace("[", "", $data);
        $level2 = str_replace("]", "", $level1);
        $level3 = str_replace(":", "   ---->   ", $level2);
        echo "<pre><b>$level3</b></pre>";

    }

}
// Get Information
if (isset($_POST['info']))
{
    $root = shell_exec("adb -s $verb shell su");
    if (strpos($root, "not") !== false)
    {
        $checkroot = "True";
    }
    else
    {
        $checkroot = "False";

    }
    $androidversion = shell_exec("adb -s $verb shell getprop ro.build.version.release");
    $brand = shell_exec("adb -s $verb shell getprop ro.product.brand");
    $model = shell_exec("adb -s $verb shell getprop ro.product.model");
    $board = shell_exec("adb -s $verb shell getprop ro.product.board");
    $serial = shell_exec("adb -s $verb shell getprop ro.serialno");
    $sdk = shell_exec("adb -s $verb shell getprop ro.build.version.sdk");
    $battery = shell_exec("adb -s $verb shell \"dumpsys battery | grep level\"");
    $batterylevel = str_replace("level:", "", $battery);
    $hostname = shell_exec("adb -s $verb shell getprop net.hostname");
    $dns1 = shell_exec("adb -s $verb shell getprop net.dns1");
    $dns2 = shell_exec("adb -s $verb shell getprop net.dns2");
    $zone = shell_exec("adb -s $verb shell getprop persist.sys.timezone");
    $arch = shell_exec("adb -s $verb shell getprop ro.product.cpu.abilist");
    $up = trim(preg_replace('!(\S+).*!','\1',shell_exec("adb -s $verb shell cat /proc/uptime")));
    $uptime = [(int) ($up/86400), (int) (($up%86400) / 3600), (int) (($up % 3600) / 60), (int) ($up % 60)];
    $mac = shell_exec("adb -s $verb shell \"ip addr show wlan0  | grep 'link/ether '| cut -d' ' -f6\"");
    echo "<b>Root : </b><b class='colorful'>$checkroot</b><br>";
    echo "<b>Uptime : </b><b class='colorful'>$uptime[0] Days $uptime[1] Hours $uptime[2] Minutes $uptime[3] Secound   </b><br>";
    echo "<b>Architect : </b><b class='colorful'>$arch </b><br>";
    echo "<b>Brand : </b><b class='colorful'>$brand </b><br>";
    echo "<b>Model : </b><b class='colorful'>$model</b><br>";
    echo "<b>Board :</b><b class='colorful'> $board</b><br>";
    echo "<b>Android Version : </b><b class='colorful'>$androidversion</b><br>";
    echo "<b>Serilal : </b><b class='colorful'>$serial </b><br>";
    echo "<b>SDK : </b><b class='colorful'>$sdk</b><br>";
    echo "<b>BatteryLevel : </b><b class='colorful'>$batterylevel </b><br>";
    echo "<b>TimeZone : </b><b class='colorful'>$zone </b><br>";
    echo "<b>Hostname : </b><b class='colorful'>$hostname </b><br>";
    echo "<b>DNS1 : </b><b class='colorful'>$dns1 </b><br>";
    echo "<b>DNS2 : </b><b class='colorful'>$dns2 </b><br>";
    echo "<b>MAC wlan0 : </b><b class='colorful'>$mac</b><br>";

}
if (isset($_POST['payload']) and !empty($_POST['payload']))
{
    $code = $_POST['payload'];
    $result = urldecode($code);
    shell_exec("adb -s $verb shell $result");

}
// Allow Permission
if (isset($_POST['allowperm']) and !empty($_POST['allowperm']))
{
    $pkg = $_POST['allowperm'];
    foreach ($list as $data)
    {
        $result = shell_exec("adb -s $verb shell pm grant $pkg $data");
    }
}
// Deny Permission
if (isset($_POST['denyperm']) and !empty($_POST['denyperm']))
{
    $pkg = $_POST['denyperm'];
    foreach ($list as $data)
    {
        $result = shell_exec("adb -s $verb shell pm revoke $pkg $data");
    }
}

if (isset($_POST['folderlist']))
{

    $result = shell_exec("adb -s $verb shell ls -d */");
    // $result=shell_exec("adb -s $verb shell ls -R /sdcard");
    // $_SESSION['folder']=$result;
    echo $result;
}

if (isset($_POST['filelist']))
{

    $result = shell_exec("adb -s $verb shell find / -maxdepth 1 -not -type d");
    echo $result;
}

if (isset($_POST['filelistsub']) and !empty($_POST['filelistsub']))
{
    $path = $_POST['filelistsub'];
    $result = shell_exec("adb -s $verb shell find $path/ -maxdepth 1 -not -type d");
    echo $result;
}

if (isset($_POST['folderlistsub']) and !empty($_POST['folderlistsub']))
{
    $path = $_POST['folderlistsub'];
    $result = shell_exec("adb -s $verb shell find $path/ -maxdepth 1 -type d");
    echo $result;
}

if (isset($_POST['deletefolder']) and !empty($_POST['deletefolder']))
{
    $path = $_POST['deletefolder'];
    $result = shell_exec("adb -s $verb shell rm -rf $path");
}

if (isset($_POST['downloadfolder']) and !empty($_POST['downloadfolder']))
{
    $path = $_POST['downloadfolder'];
    $result = shell_exec("adb -s $verb pull $path/ ../users/$session/download/folder");
}
if (isset($_POST['infofolder']) and !empty($_POST['infofolder']))
{
    $path = $_POST['infofolder'];
    $result = shell_exec("adb -s $verb shell ls -lahR $path");
    echo $result;
}
if (isset($_POST['newfolder']) and !empty($_POST['newfolder']))
{
    $path = $_POST['newfolder'];
    $result = shell_exec("adb -s $verb shell mkdir $path");
}

// need path apk
if (isset($_POST['renamefolder']) and !empty($_POST['renamefolder']) and isset($_POST['newnamefolder']) and !empty($_POST['newnamefolder']))
{
    $path = $_POST['renamefolder'];
    $newnamefolder = $_POST['newnamefolder'];

    $result = shell_exec("adb -s $verb shell mv $path $newnamefolder");
    echo $path . "<br>" . $newnamefolder;
}

if (isset($_POST['new_file_name']) and !empty($_POST['new_file_name']) and isset($_POST['new_file_data']))
{
    $filename = $_POST['new_file_name'];
    $path = $_POST['path'];

    $filedata = $_POST['new_file_data'];

    file_put_contents("../users/$session/download/newfile/$filename", "$filedata");
    shell_exec("adb -s $verb push ../users/$session/download/newfile/$filename $path");
}

if (isset($_POST['downloadfile']) and !empty($_POST['downloadfile']))
{
    $path = $_POST['downloadfile'];
    $break = explode(".", $path);
    $ext = end($break);
    if (in_array($ext, $listformat))
    {
        $pathsave = $ext;
    }
    else
    {
        $pathsave = "other";
    }
    $result = shell_exec("adb -s $verb pull $path ../users/$session/download/$pathsave");

}

if (isset($_POST['dirpath']) and !empty($_POST['dirpath']))
{
    $path = $_POST['dirpath'];
    $mydir = "../users/$session/$path";

    $myfiles = array_diff(scandir($mydir) , array(
        '.',
        '..'
    ));

    foreach ($myfiles as $files)
    {
        echo $files . "\n";

    }
}

if (isset($_POST['renamefile']) and !empty($_POST['renamefile']) and isset($_POST['newnamefile']) and !empty($_POST['newnamefile']))
{
    $old_name = $_POST['renamefile'];
    $new_name = $_POST['newnamefile'];
    if (file_exists($new_name))
    {
        echo "0";
    }
    else
    {
        if (rename($old_name, $new_name))
        {
            echo "1";
        }

    };

}

if (isset($_POST['deletefile']) and !empty($_POST['deletefile']))
{
    $path = $_POST['deletefile'];
    $res = recursiveDelete($path);
    echo "ok";
}

if (isset($_POST['copyfile']) and !empty($_POST['copyfile']) and isset($_POST['path']) and !empty($_POST['path']))
{
    $path = $_POST['copyfile'];
    $rawpath = $_POST['path'];
    $copypath = str_replace('"', "", $rawpath);
    if (is_dir($path))
    {

        recurse_copy($path, $copypath);
    }
    else
    {
        copy($path, $copypath);
    }
}

if (isset($_POST['movefile']) and !empty($_POST['movefile']) and isset($_POST['path']) and !empty($_POST['path']))
{
    $path = $_POST['movefile'];
    $rawpath = $_POST['path'];
    $copypath = str_replace('"', "", $rawpath);
    if (is_dir($path))
    {

        recurse_copy($path, $copypath);

    }
    else
    {
        copy($path, $copypath);
    }
    recursiveDelete($path);
}

if (isset($_POST['path']) and !empty($_POST['path']) and isset($_POST['uploadpath']) and !empty($_POST['uploadpath']))
{
    $path = $_POST['path'];
    $uploadpath = $_POST['uploadpath'];
    $remove = str_replace('"', "", $uploadpath);
    $temp = explode("\\", $remove);
    $raw = end($temp);
    shell_exec("adb -s $verb push $remove \"$path/$raw\"");

}
if (isset($_POST['newsession']))
{
    session_destroy();

}

if (isset($_POST['copyall']) and !empty($_POST['copyall']))
{
    $raw = $_POST['copyall'];
    $format = str_replace(".", "", $raw);
    $break = explode(":", $format);
    $type = $break[0];
    if ($type == "fast")
    {
        shell_exec("adb -s $verb  shell mkdir sdcard/.$break[1]");
        shell_exec("adb -s $verb  shell  \"find sdcard/ -type f -name *.$break[1] -exec cp {} sdcard/.$break[1] \;\"");
        shell_exec("adb -s $verb pull /sdcard/.$break[1] ../users/$session/download/folder");
        shell_exec("adb -s $verb shell rm -rf /sdcard/.$break[1]");
    }
    else
    {

        $listfile = shell_exec("adb -s $verb  shell  \"find sdcard/ -type f -name *.$format\" ");
        $break = explode("\n", $listfile);
        foreach ($break as $files)
        {
            if (in_array($format, $listformat))
            {
                $format;
            }
            else
            {
                $format = "other";
            }
            shell_exec("adb -s $verb pull \"$files\" ../users/$session/download/$format");
        }

    }

}
if (isset($_POST['moveall']) and !empty($_POST['moveall']))
{
    $raw = $_POST['moveall'];
    $format = str_replace(".", "", $raw);
    $break = explode(":", $format);
    $type = $break[0];
    if ($type == "fast")
    {
        shell_exec("adb -s $verb  shell mkdir sdcard/.$break[1]");
        shell_exec("adb -s $verb  shell  \"find sdcard/ -type f -name *.$break[1] -exec mv {} sdcard/.$break[1] \;\"");
        shell_exec("adb -s $verb pull \"/sdcard/.$break[1]\" ../users/$session/download/folder");
        shell_exec("adb -s $verb shell rm -rf /sdcard/.$break[1]");
    }
    else
    {
        $listfile = shell_exec("adb -s $verb  shell  find sdcard/ -type f -name *.$format ");
        $break = explode("\n", $listfile);
        foreach ($break as $files)
        {
            if (in_array($format, $listformat))
            {
                $format;
            }
            else
            {
                $format = "other";
            }
            shell_exec("adb -s $verb pull \"$files\" ../users/$session/download/$format");
            shell_exec("adb -s $verb shell rm -rf \"$files\"");
        }
    }
}
if (isset($_POST['delall']) and !empty($_POST['delall']))
{
    $raw = $_POST['delall'];
    $format = str_replace(".", "", $raw);
    shell_exec("adb -s $verb  shell find sdcard/ -type f -name *.$format -exec rm -f {} \;");

}
// Keybord Tab
if (isset($_POST['keyboard']) and !empty($_POST['key']) and isset($_POST['key']))
{
    $key = $_POST['key'];
    $run = $listcode[$key];
    shell_exec("adb -s $verb  shell input keyevent $run");
    echo $key;

}
// Click Input
if (isset($_POST['click']) and !empty($_POST['x']) and isset($_POST['x']) and !empty($_POST['y']) and isset($_POST['y']))
{
    $x =$_POST['x'];
    $y =$_POST['y'];
    shell_exec("adb -s $verb  shell input tap $x $y");

}
// Restart Device
if (isset($_POST['restartdevice']))
{
    shell_exec("adb -s $verb reboot");

}
// ShutDown Device
if (isset($_POST['shutdowndevice']))
{
    shell_exec("adb -s $verb reboot -p");
}
// ScreenToggle
if (isset($_POST['screentoggle']))
{

    shell_exec("adb -s $verb shell input keyevent 26");

}
// Brightness Up
if (isset($_POST['brightup']))
{

    shell_exec("adb -s $verb shell input  keyevent 221");

}
// Brightness Down
if (isset($_POST['brightdown']))
{

    shell_exec("adb -s $verb shell input  keyevent 220");

}
// Set Wmsize
if (isset($_POST['setwmsize']) and !empty($_POST['setwmsize']))
{
    $size = $_POST['setwmsize'];
    shell_exec("adb -s $verb shell wm size $size");

}
// Reset Wmsize
if (isset($_POST['resetwmsize']))
{
    shell_exec("adb -s $verb shell wm size reset");

}
// Set Density
if (isset($_POST['setdensity']) and !empty($_POST['setdensity']))
{
    $size = $_POST['setdensity'];
    shell_exec("adb -s $verb shell wm density $size");

}
// Reset Density
if (isset($_POST['resetdensity']))
{
    shell_exec("adb -s $verb shell wm density reset");

}
// Volume Up
if (isset($_POST['volumeup']))
{
    shell_exec("adb -s $verb shell input keyevent 24");

}
// Volume Down
if (isset($_POST['volumedown']))
{
    shell_exec("adb -s $verb shell input keyevent 25");

}
// Set Battery Level
if (isset($_POST['setbattery']) and !empty($_POST['setbattery']))
{
    $size = $_POST['setbattery'];
    shell_exec("adb -s $verb shell dumpsys battery set status $size");
}
// Hide Developer Options
if (isset($_POST['hidedev']))
{
    shell_exec("adb -s $verb shell pm clear com.android.settings");

}
// List Contacts
if (isset($_POST['contactlist']))
{
    $res = shell_exec("adb -s $verb shell \"content query --uri content://com.android.contacts/data --projection data1 | uniq -u \"");
    echo $res;
}
// Delete All Contacts
if (isset($_POST['delallcontacts']))
{
    shell_exec("adb -s $verb shell pm clear com.android.providers.contacts");

}
// Get Clipboard
if (isset($_POST['getclip']))
{
    $res = shell_exec("adb -s $verb shell \"am broadcast -a clipper.get\"");
    $result = str_replace('Broadcasting: Intent { act=clipper.get }', "", $res);
    $result1 = str_replace('Broadcast completed: result=-1, data=', "", $result);
    $result2 = str_replace('"', "", $result1);

    echo $result2;
}
// Set Clipboard
if (isset($_POST['setclip']) and !empty($_POST['setclip']))
{
    $text = $_POST['setclip'];
    shell_exec("adb -s $verb shell am broadcast -a clipper.set -e text '$text'");

}

// Netstat
if (isset($_POST['netstat']))
{

    $res = shell_exec("adb -s $verb shell netstat");
    echo $res;
}
// iconfig
if (isset($_POST['ifconfig']))
{

    $res = shell_exec("adb -s $verb shell ifconfig");
    echo $res;
}

// ipaddr
if (isset($_POST['ipaddr']))
{

    $res = shell_exec("adb -s $verb shell ip addr");
    echo $res;
}

// DumpWifi
if (isset($_POST['dumpwifi']))
{

    $res = shell_exec("adb -s $verb shell  dumpsys wifi");
    echo $res;
}

// Open Link
if (isset($_POST['openlink']) and !empty($_POST['openlink']))
{
    
   $url=$_POST['openlink'];
   shell_exec("adb -s $verb shell am start -a android.intent.action.VIEW -d '$url'");

}
// Call
if (isset($_POST['call']) and !empty($_POST['call']))
{
    
   $number=$_POST['call'];
   shell_exec("adb -s $verb shell am start -a android.intent.action.CALL -d tel:'$number'");

}
// Play Music
if (isset($_POST['playmusic']))
{

    shell_exec("adb -s $verb shell input  keyevent 85");
  
}
// Send SMS
if (isset($_POST['number']) and !empty($_POST['number']) and isset($_POST['message']) and !empty($_POST['message']))
{
    
   $number=$_POST['number'];
   $message=urldecode($_POST['message']);
   shell_exec("adb -s $verb shell am start -a android.intent.action.SENDTO -d sms:$number   --es  sms_body '$message'");
}
