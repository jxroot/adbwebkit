// Shell
$("#run-shell").click(function () {
  $("#result-shell").css("display", "none");
  $("#loading").css("display", "block");
  var command = $("#command-shell").val();
  $.post("process/request.php", { shell: command }, function (data) {
    $("#loading").css("display", "none");
    $("#result-shell").text(data);
    $("#result-shell").css("display", "block");
    VanillaToasts.create({
      title: "!OK",
      text: `Command ${command} Run Successfully`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
