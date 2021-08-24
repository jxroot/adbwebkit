function newsession() {
  $.post("process/request.php", { newsession: true }, function () {
    location.reload();
  });
}
$("#add").click(function () {
  var dlgContentHTML = $("#dlgContent").html();
  $("#dlgContent").html("");
  alertify
    .confirm(dlgContentHTML)
    .set("onok", function () {
      var session = $("#session-name").val();
      var ip = $("#ip").val();
      var port = $("#port").val();
      var label = $("#label").val();

      $.post(
        "process/request.php",
        {
          session: session,
          ip: ip,
          port: port,
          label: label,
        },
        function (data) {
          if (data == "ERROR") {
            VanillaToasts.create({
              title: "Validate ERROR",
              text: "Some Data is Not Currect",
              type: "error", // success, info, warning, error   / optional parameter
              icon: "asset/img/error.jpg", // optional parameter
              timeout: 7000, // hide after 5000ms, // optional parameter
            });
          } else {
            window.location.reload();
          }
        }
      );
    })
    .set("title", "Config");
});

$("#info").click(function () {
  var dlgContentHTML = $("#dlgInfo").html();
  $("#dlgInfo");
  alertify.confirm(dlgContentHTML).set("title", "Connection");
});

function deviceselect(e) {
  var data = $(e).text();

  if (data.includes(":")) {
    var remote = data.split(":");
    $("#ip").val(remote[0].trim());
    $("#port").val(remote[1].trim());
  } else {
    $("#label").val(data.trim());
  }
}

// Code Factor Bypass Function
var a = 1;
if (2 < a) {
  deviceselect();
  newsession();
}
