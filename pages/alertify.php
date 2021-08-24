<?php 
if(!isset($_SESSION['session']) and empty($_SESSION['session']) ){
echo '<div class="fab " id="add"> + </div>';
}
else{

echo '<div class="fab " id="info"> ? </div>';    
}
?>
<div style="display:none;">
    <div id="dlgContent">
        <p> SESSION NAME : </p>
        <input class="ajs-input" id="session-name" type="text" value="test" />

        <p> IP : </p>
        <input class="ajs-input" id="ip" type="text" value="" />
        <p> PORT : </p>
        <input class="ajs-input" id="port" type="text" value="" />
        <br>
        <p> Label For USB: </p>
        <input class="ajs-input" id="label" type="text" value="" /><br>
        <?php
        echo "<h4>Device List :</h4>";
        echo "<hr>";
        $result=shell_exec("adb devices");
 
        $level1=str_replace("List of devices attached","",$result);
        $level2=str_replace("device","",$level1);
        $level3=str_replace("host","",$level2);
        $level4=explode("\n",$level3);
        foreach($level4 as $data){
        echo "<b onclick='deviceselect(this)' class='text-info' style='cursor:pointer;'>$data </b><br>";
    }

?>
    </div>



    <div style="display:none;">
        <div id="dlgInfo">
            <?php
            $session=$_SESSION['session'];
             if(isset($_SESSION['ip']) and !empty($_SESSION['ip']) and isset($_SESSION['port']) and !empty($_SESSION['port'])){
                $mode="Remote";
            $ip=$_SESSION['ip'];
            $port=$_SESSION['port'];
            echo "<h3>Mode $mode</h3>";

            echo "<p id='ipadderss'>IP ADDRESS : $ip</p>";
            echo "<p id='portnumber'>PORT NUMBER : $port</p>";
             
            }else{
                $mode="USB";
                $label=$_SESSION['label'];
            echo "<h3>Mode $mode</h3>";

               echo "<p>LabelName : $label</p>";

            }
            
            echo "<p id='session'>SESSION NAME : $session</p><br>";
            
            ?>

            <button onclick="newsession()" style="border: none;
color: cornflowerblue;
border-radius: 14px;">NewSession</button>

        </div>

    </div>
    <div style="display:none;">
        <div id="dlgInfo-list">

        </div>

    </div>






    <div style="display:none;">
        <div id="dlgInstallapk">
            <div class="file-upload">
                <button class="file-upload-btn" type="button" onclick="$('.file-upload-input').trigger( 'click' )">Add
                    Apk</button>


                <input class="file-upload-input" type='file' onchange="readURL(this);" accept=".apk" id="file" />

                <div class="file-upload-content">
                    <br>
                    <!-- <img class="file-upload-image" src="#" alt="your image" /> -->
                    <div class="image-title-wrap">
                        <button type="button" onclick="removeUpload()" class="remove-image">Remove <span
                                class="image-title">Uploaded Image</span></button>
                        <br>
                        <br>

                        <div class="mb-4">
                            <span class="text-muted small">install apps path</span>
                            <label class="custom-control material-switch">
                                <span class="material-switch-control-description">Internal</span>
                                <input type="checkbox" class="material-switch-control-input" id="installpath"
                                    value="true">
                                <span class="material-switch-control-indicator"></span>
                                <span class="material-switch-control-description">Sdcard</span>

                                <div class="mb-4">
                                    <span class="text-muted small">Reinstall existing apps</span>
                                    <label class="custom-control material-switch">
                                        <span class="material-switch-control-description">Off</span>
                                        <input type="checkbox" class="material-switch-control-input" id="overwrite"
                                            value="true">
                                        <span class="material-switch-control-indicator"></span>
                                        <span class="material-switch-control-description">On</span>

                                    </label>
                                    <div class="mb-4">
                                        <span class="text-muted small">Allow version code downgrade</span>
                                        <label class="custom-control material-switch">
                                            <span class="material-switch-control-description">Off</span>
                                            <input type="checkbox" class="material-switch-control-input" id="downgrade"
                                                value="true">
                                            <span class="material-switch-control-indicator"></span>
                                            <span class="material-switch-control-description">On</span>
                                        </label>

                                        <div class="mb-4">
                                            <span class="text-muted small">Grant all permissions</span>
                                            <label class="custom-control material-switch">
                                                <span class="material-switch-control-description">Off</span>
                                                <input type="checkbox" class="material-switch-control-input"
                                                    id="grantall" value="true">
                                                <span class="material-switch-control-indicator"></span>
                                                <span class="material-switch-control-description">On</span>
                                            </label>
                                        </div>
                                    </div>

                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div style="display:none;">
            <div id="dlghelp">
                <h3 class="text-center">Keyboard</h3>
                <hr>
                <b>Allow Char : a-z | 0-9 | Enter |@/;#*-.=`+</b><br>
                <b>ArrowKey : up | down | left | right</b><br>
                <b>Scroll : scrollup | scrolldown</b><br>
                <b>PhoneKey : back | home</b><br>
                <b>KeyPad : enter | delete | tab | space</b><br>
                <b>Input : text | menu</b><br>
                <b>Device : resetwmsize | wmsize | densiysize | resetdensity | screen | shutdown | restart</b><br>
                <b>Control : brightnessup | brightnessdown | volumeup | volumedown</b><br>
                <b>App : run | install | launch | list | call | callpad | search | browsers</b><br>
                <b>Music : play | pause | next | previous</b><br>
                <b>Useful : sleep </b><br>



            </div>

        </div>




        <div style="display:none;">
            <div id="dlginfofolder">
                <pre id="resultinfofolder"></pre>

            </div>

        </div>
        <div style="display:none;">
            <div id="dlgnewfile">
                <pre>Name : </pre>
                <input id="name-file"><br><br>
                <pre>data : </pre>

                <textarea id="data-file" cols="30" rows="10"></textarea>

            </div>

        </div>



        <div style="display:none;">
            <div id="dlgsms">
                <pre>Number : </pre>
                <input id="number"><br><br>
                <pre>Message : </pre>

                <textarea id="message" cols="30" rows="10"></textarea>

            </div>

        </div>




        <div style="display:none;">
            <div id="dlgeditfile">

                <pre>data : </pre>

                <div id="edit-file-data">
                </div>

            </div>

        </div>