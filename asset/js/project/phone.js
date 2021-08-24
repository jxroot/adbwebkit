// Restart Device
$("#restartdevice").click(function () {
  $.post("process/request.php", { restartdevice: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: `Restart Device Successfully`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
// ShutDown Device
$("#shutdowndevice").click(function () {
  $.post("process/request.php", { shutdowndevice: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: `ShutDown Device Successfully`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
// ScreenToggle
$("#screentoggle").click(function () {
  $.post("process/request.php", { screentoggle: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: `Screen On or Off Successfully`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});

// Brightness Up
$("#brightup").click(function () {
  $.post("process/request.php", { brightup: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: `Brightness Up Successfully`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
// Brightness Down
$("#brightdown").click(function () {
  $.post("process/request.php", { brightdown: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: `Brightness Down Successfully`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});

// Set Wmsize
$("#setwmsize").click(function () {
  alertify
    .prompt("Resolution", "1280x720", function (evt, value) {
      $.post("process/request.php", { setwmsize: value }, function () {
        VanillaToasts.create({
          title: "!OK",
          text: `Set Wmsize ${value} Successfully`,
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      });
    })
    .set("title", "Set WmSize");
});

// Reset Wmsize
$("#resetwmsize").click(function () {
  $.post("process/request.php", { resetwmsize: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: `Reset Wmsize Successfully`,
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});

// Set Density
$("#setdensity").click(function () {
  alertify
    .prompt("Resolution >=72", "1280", function (evt, value) {
      $.post("process/request.php", { setdensity: value }, function () {
        VanillaToasts.create({
          title: "!OK",
          text: `Set Density ${value} Successfully`,
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      });
    })
    .set("title", "Set Density");
});

// Reset Density
$("#resetdensity").click(function () {
  $.post("process/request.php", { resetdensity: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: "Reset Density Successfully",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
// Volume Up
$("#volumeup").click(function () {
  $.post("process/request.php", { volumeup: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: "Volume Up Successfully",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
// Volume Down
$("#volumedown").click(function () {
  $.post("process/request.php", { volumedown: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: "Volume Down Successfully",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});
// Set Battery Level
$("#setbattery").click(function () {
  alertify
    .prompt("Persent", "200", function (evt, value) {
      $.post("process/request.php", { setbattery: value }, function () {
        VanillaToasts.create({
          title: "!OK",
          text: "Battery Level Chenge Successfully",
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      });
    })
    .set("title", "Set Battery Level");
});
// List Contacts
// function contacts() {
//   $.post("process/request.php", { contactlist: true }, function (data) {
//     var rawdata = data.split("\n");
//     for (const iterator of rawdata) {
//       var newname = iterator.replace("display_name=", "");
//       var newnam1 = newname.replace("data1=", "");
//       // var newnam2 = newnam1.replace("newnam1", "Row:*");
//       console.log(searchArray(newnam1, "Row: *"));
//       // console.log(newnam1);
//       //  773 امیرحسین صادق علی

//       var temp = `<tbody>

//   <tr>
// <td>
// <img src="">

//   <a class="user-link" path="${newnam1}"
//       >${newnam1}</a>
//   <!-- <span class=" user-subhead">Admin</span> -->
// </td>

// <td style="width: 20%;">
// <a  class="table-link text-warning " path="${file}"
// onclick="(openfile(this))" data-toggle="tooltip" data-placement="top" title="Tooltip on top">
//   <span class="fa-stack">
//       <i class="fa fa-square fa-stack-2x"></i>
//       <i class="fa fa-play-circle fa-stack-1x fa-inverse"></i>
//   </span>
//   </a>
//   <a  class="table-link text-dark" path="${file}"
//   onclick="(renamefile(this))">
//       <span class="fa-stack">
//           <i class="fa fa-square fa-stack-2x"></i>
//           <i class="fa fa-edit fa-stack-1x fa-inverse"></i>
//       </span>
//   </a>
//   <a  class="table-link text-primary" path="${file}"
//   onclick="(copyfile(this))">
//       <span class="fa-stack">
//           <i class="fa fa-square fa-stack-2x"></i>
//           <i class="fa fa-copy fa-stack-1x fa-inverse"></i>
//       </span>
//   </a>
//   <a  class="table-link text-info" path="${file}"
//   onclick="(movefile(this))">
//       <span class="fa-stack">
//           <i class="fa fa-square fa-stack-2x"></i>
//           <i class="fa fa-cut fa-stack-1x fa-inverse"></i>
//       </span>
//   </a>
//   <a  class="table-link danger" path="${file}"
//   onclick="(deletefile(this))">
//       <span class="fa-stack">
//           <i class="fa fa-square fa-stack-2x"></i>
//           <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
//       </span>
//   </a>

// </td>
// </tr>

// </tbody>`;
//       $("#contactlist").append(temp);
//     }
//   });
// }
// Hide Developer Options
$("#hidedev").click(function () {
  $.post("process/request.php", { hidedev: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: "Developer Options Hidden Successfully",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});

// Delete All Contacts
$("#delallcontacts").click(function () {
  $.post("process/request.php", { delallcontacts: true }, function () {
    VanillaToasts.create({
      title: "!OK",
      text: "Delete All Contacts",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
  });
});

// Get Clipboard
$("#getclip").click(function () {
  $.post("process/request.php", { getclip: true }, function (data) {
    VanillaToasts.create({
      title: "!OK",
      text: "Get Clipboard",
      type: "success", // success, info, warning, error   / optional parameter
      icon: "asset/img/tick.png", // optional parameter
      timeout: 7000, // hide after 5000ms, // optional parameter
    });
    alert(data);
  });
});

// Set Clipboard
$("#setclip").click(function () {
  alertify
    .prompt("Data", "test", function (evt, value) {
      $.post("process/request.php", { setclip: value }, function () {
        VanillaToasts.create({
          title: "!OK",
          text: `Cllipboard Set ${value}`,
          type: "success", // success, info, warning, error   / optional parameter
          icon: "asset/img/tick.png", // optional parameter
          timeout: 7000, // hide after 5000ms, // optional parameter
        });
      });
    })
    .set("title", "Set Clipboard");
});
