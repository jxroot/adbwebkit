$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});
function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(";");
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == " ") c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
}
$("#selectgal").change(function () {
  $("#boxfile").html("");
  var path = $("#selectgal").val();
  $.post("process/request.php", { dirpath: path }, function (data) {
    var newfile = data.split("\n");
    newfile.pop(-1);
    for (const file of newfile) {
      var input = file.split(".").pop().trim();
      if (jQuery.inArray(input, listformat) !== -1) {
        var image = `asset/img/${input}.png`;
      } else {
        var image = "asset/img/documents.png";
      }
      var temp = `<tbody>

    <tr>
<td>
<img src="${image}">

    <a class="user-link" path="${file}"
        >${file}</a>
    <!-- <span class=" user-subhead">Admin</span> -->
</td>

<td style="width: 20%;">
<a  class="table-link text-warning " path="${file}"
onclick="(openfile(this))" data-toggle="tooltip" data-placement="top" title="Tooltip on top">
    <span class="fa-stack">
        <i class="fa fa-square fa-stack-2x"></i>
        <i class="fa fa-play-circle fa-stack-1x fa-inverse"></i>
    </span>
    </a>
    <a  class="table-link text-dark" path="${file}"
    onclick="(renamefile(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-edit fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a  class="table-link text-primary" path="${file}"
    onclick="(copyfile(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-copy fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a  class="table-link text-info" path="${file}"
    onclick="(movefile(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-cut fa-stack-1x fa-inverse"></i>
        </span>
    </a>
    <a  class="table-link danger" path="${file}"
    onclick="(deletefile(this))">
        <span class="fa-stack">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
        </span>
    </a>


</td>
</tr>


</tbody>`;
      $("#boxfile").append(temp);
    }
  });
});
function openfile(e) {
  var path = $(e).attr("path");
  var session = readCookie("path");
  var pathfolder = $("#selectgal").val();
  window.open(`users/${session}/${pathfolder}${path}`);
}

function renamefile(e) {
  var path = $(e).attr("path");
  var session = readCookie("path");
  var pathfolder = $("#selectgal").val();

  alertify
    .prompt("NewName", path, function (evt, value) {
      $.post(
        "process/request.php",
        {
          renamefile: `../users/${session}/${pathfolder}${path}`,
          newnamefile: `../users/${session}/${pathfolder}${value}`,
        },
        function (data) {
          if (data == "0") {
            VanillaToasts.create({
              title: "ERROR",
              text: `A File With The Same Name Already Exists`,
              type: "error", // success, info, warning, error   / optional parameter
              icon: "asset/img/error.jpg", // optional parameter
              timeout: 7000, // hide after 5000ms, // optional parameter
            });
          } else {
            VanillaToasts.create({
              title: "OK",
              text: `${path} Rename To ${value}`,
              type: "success", // success, info, warning, error   / optional parameter
              icon: "asset/img/tick.png", // optional parameter
              timeout: 7000, // hide after 5000ms, // optional parameter
            });
          }
        }
      );
    })
    .set("title", "Rename File or Folder");
}

function deletefile(e) {
  var path = $(e).attr("path");
  var session = readCookie("path");
  var pathfolder = $("#selectgal").val();

  $.post(
    "process/request.php",
    {
      deletefile: `../users/${session}/${pathfolder}${path}`,
    },
    function (data) {
      if (data == "ok") {
        VanillaToasts.create({
          title: "OK",
          text: `${path} Deleted`,
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      } else {
        VanillaToasts.create({
          title: "ERROR",
          text: `Cant Delete ${path}`,
          type: "error", // success, info, warning, error   / optional parameter
          icon: "asset/img/error.jpg", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      }
    }
  );
}

function copyfile(e) {
  var path = $(e).attr("path");
  var session = readCookie("path");
  var pathfolder = $("#selectgal").val();

  alertify
    .prompt("Path", "C:\\", function (evt, value) {
      $.post("process/request.php", {
        copyfile: `../users/${session}/${pathfolder}${path}`,
        path: `${value}\\${path}`,
      });
    })
    .set("title", "Copy File or Folder");
}

function movefile(e) {
  var path = $(e).attr("path");
  var session = readCookie("path");
  var pathfolder = $("#selectgal").val();

  alertify
    .prompt("Path", "C:\\", function (evt, value) {
      $.post("process/request.php", {
        movefile: `../users/${session}/${pathfolder}${path}`,
        path: `${value}\\${path}`,
      });
    })
    .set("title", "Move File or Folder");
}

// Code Factor Bypass Function
var a = 1;
if (2 < a) {
  openfile();
  renamefile();
  deletefile();
  copyfile();
  movefile();
}
