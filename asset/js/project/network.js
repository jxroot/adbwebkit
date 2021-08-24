// Netstat

$("#netstat").click(function () {
  $("#result-network").css("display", "none");
  $("#result-network-loading").css("display", "block");

  $.post("process/request.php", { netstat: true }, function (data) {
    $("#result-network").val(data);
    $("#result-network").css("display", "block");
    $("#result-network-loading").css("display", "none");

    VanillaToasts.create({
      title: "!OK",
      text: "Netstat",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});

// ifconfig

$("#ifconfig").click(function () {
  $("#result-network").css("display", "none");
  $("#result-network-loading").css("display", "block");

  $.post("process/request.php", { ifconfig: true }, function (data) {
    $("#result-network").val(data);
    $("#result-network").css("display", "block");
    $("#result-network-loading").css("display", "none");

    VanillaToasts.create({
      title: "!OK",
      text: "ifconfig",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});

// ip addr

$("#ipaddr").click(function () {
  $("#result-network").css("display", "none");
  $("#result-network-loading").css("display", "block");

  $.post("process/request.php", { ipaddr: true }, function (data) {
    $("#result-network").val(data);
    $("#result-network").css("display", "block");
    $("#result-network-loading").css("display", "none");

    VanillaToasts.create({
      title: "!OK",
      text: "ipaddr",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});

// dumpwifi
$("#dumpwifi").click(function () {
  $("#result-network").css("display", "none");
  $("#result-network-loading").css("display", "block");

  $.post("process/request.php", { dumpwifi: true }, function (data) {
    $("#result-network").val(data);
    $("#result-network").css("display", "block");
    $("#result-network-loading").css("display", "none");

    VanillaToasts.create({
      title: "!OK",
      text: "dumpwifi",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
