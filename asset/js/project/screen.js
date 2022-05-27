// Recored Screen
$("#recoredscreen").click(function () {
  $("#screenshot-out").css("display", "none");
  $("#recoredscreen-out").css("display", "none");

  alertify
    .prompt("Time in Secound Max 180", "10", function (evt, value) {
      $.post("process/request.php", { recoredscreen: value }, function (data) {
        $("#recoredscreen-out").attr("src", data);
        $("#loading-screen").css("display", "none");
        $("#recoredscreen-out").css("display", "block");
      });
      $("#loading-screen").css("display", "block");
    })
    .set("title", "RecoredScreen");
});

// Screenshot
$("#screenshot").click(function () {
  $("#recoredscreen-out").css("display", "none");
  $.post("process/request.php", { screenshot: true }, function (data) {
    $("#screenshot-out").attr("src", data);
    $("#screenshot-out").css("display", "block");
  });
});

$("#setposimg").click(function () {
  var url = $("#screenshot-out").attr("src");
  window.open(`pages/getpos.php#${url}`, "", "width=500,height=300");

})

