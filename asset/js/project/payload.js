$("#payload-convert").click(function () {
  $("#payload-output").text("");
  $("#payload-output").css("display", "inline");
  $("#payload-convert").css("width", " 49%");
  $("#payload-save").css("display", "inline");
  $("#payload-execute").css("display", "inline");
  $("#payload-copy").css("display", "inline");
  var listcommand = {
    menu: "input keyevent 82",
    resetwmsize: "wm size reset",
    resetdensity: "wm density reset ",
    home: "input keyevent 3",
    back: "input keyevent 4",
    a: "input keyevent 29",
    b: "input keyevent 30",
    c: "input keyevent 31",
    d: "input keyevent 32",
    e: "input keyevent 33",
    f: "input keyevent 34",
    g: "input keyevent 35",
    h: "input keyevent 36",
    i: "input keyevent 37",
    j: "input keyevent 38",
    k: "input keyevent 39",
    l: "input keyevent 40",
    m: "input keyevent 41",
    n: "input keyevent 42",
    o: "input keyevent 43",
    p: "input keyevent 44",
    q: "input keyevent 45",
    r: "input keyevent 46",
    s: "input keyevent 47",
    t: "input keyevent 48",
    u: "input keyevent 49",
    v: "input keyevent 50",
    w: "input keyevent 51",
    x: "input keyevent 52",
    y: "input keyevent 53",
    z: "input keyevent 54",
    0: "input keyevent 7",
    1: "input keyevent 8",
    2: "input keyevent 9",
    3: "input keyevent 10",
    4: "input keyevent 11",
    5: "input keyevent 12",
    6: "input keyevent 13",
    7: "input keyevent 14",
    8: "input keyevent 15",
    9: "input keyevent 16",
    tab: "input keyevent 61",
    space: "input keyevent 62",
    enter: "input keyevent 66",
    delete: "input keyevent 67",
    browsers: "input keyevent 64",
    callpad: "input keyevent 5",
    volumeup: "input keyevent 24",
    volumedown: "input keyevent 25",
    scrollup: "input keyevent 92",
    scrolldown: "input keyevent 93",
    "*": "input keyevent 17",
    ".": "input keyevent 56",
    "#": "input keyevent 18",
    "-": "input keyevent 69",
    "=": "input keyevent 70",
    "+": "input keyevent 81",
    ",": "input keyevent 55",
    ";": "input keyevent 74",
    "@": "input keyevent 77",
    "|": "input keyevent 71",
    "/": "input keyevent 76",
    "\\": "input keyevent 73",
    up: "input keyevent 19",
    down: "input keyevent 20",
    left: "input keyevent 21",
    right: "input keyevent 22",
    play: "input keyevent 85",
    pause: "input keyevent 86",
    next: "input keyevent 87",
    previous: "input keyevent 88",
    restart: "reboot",
    search: "input keyevent 84",
    shutdown: "reboot -p",
    brightnessup: "input keyevent 221",
    brightnessdown: "input keyevent 220",
    screen: "input keyevent 26",
    list: "am start --user 0 -a android.intent.action.MAIN",
  };
  var input = $("#payload-input").val();
  var lines = input.replace(/\r\n/g, "\n").split("\n");
  for (const iterator of lines) {
    var checkdata = iterator.trim();
    if (checkdata == "clear") {
      var val = prompt("ClearCache", "Enter App Package Name ");

      $("#payload-output").append(` pm clear   ${val};`);
    }

    if (checkdata == "wmsize") {
      var val = prompt("Chenge wm size", "200");

      $("#payload-output").append(` wm size  ${val};`);
    }

    if (checkdata == "densitysize") {
      var val = prompt("Chenge Density Size", "800x600");

      $("#payload-output").append(` wm densit ${val};`);
    }

    if (checkdata == "launch") {
      var val = prompt("Activity Packagename", "com.android.settings");
      var val1 = prompt("Activity", "com.android.settings.Settings");
      $("#payload-output").append(
        "  am start --user 0 -a android.intent.action.MAIN -n " +
          val +
          "/" +
          val1 +
          " ;"
      );
    }

    if (checkdata == "call") {
      var val = prompt("Call", "+98903728123");

      $("#payload-output").append(
        ` am start -a android.intent.action.CALL -d tel: ${val};`
      );
    }

    if (checkdata == "sleep") {
      var val = prompt("Time In Secound", "1");

      $("#payload-output").append(` sleep ${val};`);
    }
    if (checkdata == "run") {
      var val = prompt("Run App", "Enter App Package Name ");

      $("#payload-output").append(` monkey -p ${val}  -v 1 ;`);
    }
    if (checkdata == "text") {
      var val = prompt("String", "etc");
      $("#payload-output").append(` input text  ${val};`);
    }
    $("#payload-output").append(`${listcommand[checkdata]};`);
    var text = $("#payload-output").text();
    text = text.replace("undefined;", "");
    $("#payload-output").text(text);
  }
});
$("#payload-copy").click(function () {
  var copyText = document.getElementById("payload-output");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  document.execCommand("copy");
  VanillaToasts.create({
    title: "OK",
    text: "Copy Code Successfully",
    type: "info", // success, info, warning, error   / optional parameter
    icon: "asset/img/tick.png", // optional parameter
    timeout: 7000, // hide after 5000ms, // optional parameter
  });
});

$("#payload-help").click(function () {
  var dlgContentHTML = $("#dlghelp").html();
  $("#dlgInfo-list").html();
  alertify.confirm(dlgContentHTML).set("title", "Help").set("resizable", true);
});

$("#payload-execute").click(function () {
  var payload = $("#payload-output").val();
  var code = encodeURI(payload);
  //   var code = payload.replace("+", "");
  $.post("process/request.php", { payload: code }, function () {});
});
