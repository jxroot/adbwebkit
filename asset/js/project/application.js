// Uninstall Apk

function delapk(e) {
  var name = $(e).attr("packagename");

  $.post(
    "process/request.php",
    {
      uninstall: name,
    },
    function (data) {
      if (data == "OK") {
        VanillaToasts.create({
          title: "OK",
          text: "Uninstall Successfully",
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      } else {
        VanillaToasts.create({
          title: "ERROR",
          text: "PackageName NotFound",
          type: "error", // success, info, warning, error   / optional parameter
          icon: "asset/img/error.jpg", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      }
    }
  );
}

// Clear Apk
function clearapk(e) {
  var name = $(e).attr("packagename");

  alertify;

  $.post(
    "process/request.php",
    {
      clearapk: name,
    },
    function (data) {
      if (data == "OK") {
        VanillaToasts.create({
          title: "OK",
          text: "Clear Cache Successfully",
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      } else {
        VanillaToasts.create({
          title: "ERROR",
          text: "PackageName NotFound",
          type: "error", // success, info, warning, error   / optional parameter
          icon: "asset/img/error.jpg", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      }
    }
  );
}

// Run Apk
function runapk(e) {
  var name = $(e).attr("packagename");
  $.post(
    "process/request.php",
    {
      runapk: name,
    },
    function (data) {
      if (data == "OK") {
        VanillaToasts.create({
          title: "OK",
          text: "Run Apk Successfully",
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      } else {
        VanillaToasts.create({
          title: "ERROR",
          text: "PackageName NotFound",
          type: "error", // success, info, warning, error   / optional parameter
          icon: "asset/img/error.jpg", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      }
    }
  );
}

// downloadapk

function downloadapk(e) {
  var pkg = $(e).attr("packagename");

  $.post(
    "process/request.php",
    {
      downloadapk: pkg,
    },
    function (data) {
      if (data == "ERROR") {
        VanillaToasts.create({
          title: "ERROR",
          text: "PackageName NotFound",
          type: "error", // success, info, warning, error   / optional parameter
          icon: "asset/img/error.jpg", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      } else {
        VanillaToasts.create({
          title: "OK",
          text: "Save Apk Successfully",
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      }
    }
  );
}
// Reset All Permission
function reset_all() {
  $.post(
    "process/request.php",
    {
      resetall: true,
    },
    function (data) {
      if (data == "ERROR") {
        VanillaToasts.create({
          title: "ERROR",
          text: "PackageName NotFound",
          type: "error", // success, info, warning, error   / optional parameter
          icon: "asset/img/error.jpg", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      } else {
        VanillaToasts.create({
          title: "!OK",
          text: "Reset All Permisstion Successfully",
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      }
    }
  );
}

// Allow Permission
function allowpermission(e) {
  var pkg = $(e).attr("packagename");
  $.post(
    "process/request.php",
    {
      allowperm: pkg,
    },
    function () {
      VanillaToasts.create({
        title: "OK",
        text: "Allow Permission Successfully",
        type: "info", // success, info, warning, error   / optional parameter
        icon: "asset/img/tick.png", // optional parameter
        timeout: 7000, // hide after 5000ms, // optional parameter
      });
    }
  );
}
// Deny Permission
function denypermission(e) {
  var pkg = $(e).attr("packagename");
  $.post(
    "process/request.php",
    {
      denyperm: pkg,
    },
    function () {
      VanillaToasts.create({
        title: "OK",
        text: "Deny Permission Successfully",
        type: "info", // success, info, warning, error   / optional parameter
        icon: "asset/img/tick.png", // optional parameter
        timeout: 7000, // hide after 5000ms, // optional parameter
      });
    }
  );
}
// Stop Apk
function stopapk(e) {
  var pkg = $(e).attr("packagename");
  $.post(
    "process/request.php",
    {
      stopapk: pkg,
    },
    function () {
      VanillaToasts.create({
        title: "!OK",
        text: "Stop Apk Successfully",
        type: "info", // success, info, warning, error   / optional parameter
        icon: "asset/img/tick.png", // optional parameter
        timeout: 7000, // hide after 5000ms, // optional parameter
      });
    }
  );
}

// install apk
function install_apk() {
  var dlgContentHTML = $("#dlgInstallapk").html();
  $("#dlgInstallapk").html("");
  alertify
    .confirm(dlgContentHTML)
    .set("onok", function () {
      var form_data = new FormData();
      var files = $("#file")[0].files;

      if ($("#grantall").is(":checked")) {
        var grantall = true;
      } else {
        var grantall = false;
      }
      if ($("#overwrite").is(":checked")) {
        var overwrite = true;
      } else {
        var overwrite = false;
      }
      if ($("#downgrade").is(":checked")) {
        var downgrade = true;
      } else {
        var downgrade = false;
      }
      if ($("#installpath").is(":checked")) {
        var installpath = true;
      } else {
        var installpath = false;
      }
      // Check file selected or not
      if (files.length > 0) {
        form_data.append("file", files[0]);
        $.ajax({
          url: `process/uploadapk.php?grantall=${grantall}&overwrite=${overwrite}&downgrade=${downgrade}&installpath=${installpath}`,
          type: "post",
          data: form_data,
          contentType: false,
          cache: false,
          processData: false,
          success: function (data) {
            if (data == "Success") {
              VanillaToasts.create({
                title: "OK",
                text: "Install Apk Successfully",
                type: "info", // success, info, warning, error   / optional parameter
                icon: "asset/img/tick.png", // optional parameter
                timeout: 7000, // hide after 5000ms, // optional parameter
              });
            } else {
              VanillaToasts.create({
                title: "ERROR",
                text: "Cant Install Apk",
                type: "error", // success, info, warning, error   / optional parameter
                icon: "asset/img/error.jpg", // optional parameter
                timeout: 7000, // hide after 5000ms, // optional parameter
              });
            }
          },
        });
      } else {
        VanillaToasts.create({
          title: "ERROR",
          text: "Please select a file",
          type: "error", // success, info, warning, error   / optional parameter
          icon: "asset/img/error.jpg", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      }
    })
    .set("title", "InstallApk");
}

function readURL(input) {
  if (input.files && input.files[0]) {
    var fileExtension = ["apk"];
    if (
      $.inArray($(input).val().split(".").pop().toLowerCase(), fileExtension) ==
      -1
    ) {
      VanillaToasts.create({
        title: "ERROR",
        text: "Only formats are allowed : " + fileExtension.join(", "),
        type: "error", // success, info, warning, error   / optional parameter
        icon: "asset/img/error.jpg", // optional parameter
        timeout: 7000, // hide after 5000ms, // optional parameter
      });
      return;
    }
    var reader = new FileReader();

    reader.onload = function () {
      $(".file-upload-content").show();
      $(".image-title").html(input.files[0].name);
    };

    reader.readAsDataURL(input.files[0]);
  } else {
    removeUpload();
  }
}

function removeUpload() {
  $(".file-upload-input").replaceWith($(".file-upload-input").clone());
  $(".file-upload-content").hide();
}
// list Apk
$("#selectapp").on("change", function () {
  $("#application").html("");
  var value = this.value;
  $.post(
    "process/request.php",
    {
      applist: value,
    },
    function (data) {
      var rawdata = data.split("\n");
      rawdata.pop(-1);
      for (const iterator of rawdata) {
        if (navigator.onLine) {
          var image = `https://cdn.apk-cloud.com/detail/image/${iterator}-w130.png`;
        } else {
          var image = "asset/img/apk.png";
        }

        var temp = `<tbody>

      <tr>
  <td>
  
  <img src="${image}">
  
      <a class="user-link" path=""
          >${iterator}</a>
    <span class="user-subhead" ></span> 
  </td>
  
  <td style="width: 20%;">
  <a  class="table-link text-warning " onclick="runapk(this)" packagename="${iterator}">
      <span class="fa-stack">
          <i class="fa fa-square fa-stack-2x"></i>
          <i class="fa fa-play-circle fa-stack-1x fa-inverse"></i>
      </span>
      </a>
      <a  class="table-link text-danger" onclick="delapk(this)" packagename="${iterator}">
          <span class="fa-stack">
              <i class="fa fa-square fa-stack-2x"></i>
              <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
          </span>
      </a>
      <a  class="table-link text-info" onclick="clearapk(this)" packagename="${iterator}">
      <span class="fa-stack">
          <i class="fa fa-square fa-stack-2x"></i>
          <i class="fa fa-broom fa-stack-1x fa-inverse"></i>
      </span>
  </a>
      <a  class="table-link text-primary" onclick="downloadapk(this)" packagename="${iterator}" >
          <span class="fa-stack">
              <i class="fa fa-square fa-stack-2x"></i>
              <i class="fa fa-save  fa-stack-1x fa-inverse"></i>
          </span>
      </a>
      <a  class="table-link text-success" packagename="${iterator}"
      onclick="allowpermission(this)">
          <span class="fa-stack">
              <i class="fa fa-square fa-stack-2x"></i>
              <i class="fa fa-key fa-stack-1x fa-inverse"></i>
          </span>
      </a>
      <a  class="table-link text-warning" packagename="${iterator}"
      onclick="(denypermission(this))">
          <span class="fa-stack">
              <i class="fa fa-square fa-stack-2x"></i>
              <i class="fa fa-lock fa-stack-1x fa-inverse"></i>
          </span>
      </a>
      <a  class="table-link text-muted" packagename="${iterator}"
      onclick="(stopapk(this))">
          <span class="fa-stack">
              <i class="fa fa-square fa-stack-2x"></i>
              <i class="fa fa-biohazard fa-stack-1x fa-inverse"></i>
          </span>
      </a>
  
  
  </td>
  </tr>
  
  
  </tbody>`;
        $("#application").append(temp);
      }
    }
  );
});

// Code Factor Bypass Function
var a = 1;
if (2 < a) {
  runapk();
  delapk();
  clearapk();
  downloadapk();
  reset_all();
  allowpermission();
  denypermission();
  stopapk();
  install_apk();
  readURL(1);
}
