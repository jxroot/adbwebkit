$("#v-pills-info-tab").click(function () {
  $.post("process/request.php", { info: true }, function (data) {
    $("#box").html(data);
    $("#infoi").css("display", "block");
  });
  $.post("process/request.php", { infofull: true }, function (data) {
    $("#dlgInfo-list").html(data);
    $("#list-info").css("display", "block");
  });
});

$("#list-info").click(function () {
  var dlgContentHTML = $("#dlgInfo-list").html();
  $("#dlgInfo-list").html();
  alertify
    .confirm(dlgContentHTML)
    .set("onok")
    .set("title", "Information")
    .set("resizable", true);
});
