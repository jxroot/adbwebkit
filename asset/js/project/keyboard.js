$("#v-pills-keyboard-tab").click(function () {
  VanillaToasts.create({
    title: "Info",
    text: "Keyboard Enabled",
    type: "success", // success, info, warning, error   / optional parameter
    icon: "asset/img/tick.png", // optional parameter
    timeout: 7000, // hide after 5000ms, // optional parameter
  });

  $(document).keydown(function (e) {
    e.preventDefault();

    var code = e.key;

    $.post(
      "process/request.php",
      {
        keyboard: true,
        key: code,
      },
      function (res) {
        $("#result-keyboard").text(res);
      }
    );
  });
});
