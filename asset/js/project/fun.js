// Send Messages
$("#sendmessage").click(function () {
  var dlgContentHTML = $("#dlgsms").html();
  $("#dlgsms").html("");
  alertify
    .confirm(dlgContentHTML)
    .set("onok", function () {
      var number = $("#number").val();
      var message = $("#message").val();

      $.post(
        "process/request.php",
        {
          number: number,
          message: message,
        },
        function () {
          VanillaToasts.create({
            title: "!OK",
            text: `Send SMS To ${number}`,
            type: "success", // success, info, warning, error   / optional parameter
            icon: "asset/img/tick.png", // optional parameter
            timeout: 7000, // hide after 5000ms, // optional parameter
          });
        }
      );
    })
    .set("title", "Send SMS");
});

// Open Link
$("#openlink").click(function () {
  alertify
    .prompt("URL", "https://google.com", function (evt, value) {
      $.post("process/request.php", { openlink: value }, function () {
        VanillaToasts.create({
          title: "!OK",
          text: `Link Open ${value}`,
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      });
    })
    .set("title", "Open Link");
});

// Call
$("#call").click(function () {
  alertify
    .prompt("Number", "+989027356271", function (evt, value) {
      $.post("process/request.php", { call: value }, function () {
        VanillaToasts.create({
          title: "!OK",
          text: `Call To ${value}`,
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      });
    })
    .set("title", "Call");
});

// Play Music
$("#playmusic").click(function () {
  $.post("process/request.php", { playmusic: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: `Play Music`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
