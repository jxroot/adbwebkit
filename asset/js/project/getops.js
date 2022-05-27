$(document).ready(function () {
    var url = window.location.hash
    var repl = url.replace("#", "")
    $("img").attr("src", "../" + repl)
    $('img').click(function (e) {
        var offset = $(this).offset();
        var X = Math.round(e.pageX - offset.left);
        var Y = Math.round(e.pageY - offset.top);
        alert('X: ' + X + ', Y: ' +Y);
    });
    $('img').bind("contextmenu", function () {
        return false;
    });

    $('img').mousedown(function (e) {
        if (e.which == 3) {
            var offset = $(this).offset();
            var X = Math.round(e.pageX - offset.left);
            var Y = Math.round(e.pageY - offset.top);
        
            $.post("../process/request.php", { click: true,x:X,y:Y }, function () {
  
              });
        }
    });

});
