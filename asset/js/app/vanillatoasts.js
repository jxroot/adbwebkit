(function (root, factory) {
  try {
    // commonjs
    if (typeof exports === "object") {
      module.exports = factory();
      // global
    } else {
      root.VanillaToasts = factory();
    }
  } catch (error) {
    console.log(
      "Isomorphic compatibility is not supported at this time for VanillaToasts."
    );
  }
})(this, function () {
  // We need DOM to be ready
  if (document.readyState === "complete") {
    init();
  } else {
    window.addEventListener("DOMContentLoaded", init);
  }

  // Create VanillaToasts object
  VanillaToasts = {
    // In case toast creation is attempted before dom has finished loading!
    create: function () {
      console.error(
        [
          "DOM has not finished loading.",
          "\tInvoke create method when DOMs readyState is complete",
        ].join("\n")
      );
    },
    //function to manually set timeout after create
    setTimeout: function () {
      console.error(
        [
          "DOM has not finished loading.",
          "\tInvoke create method when DOMs readyState is complete",
        ].join("\n")
      );
    },
    toasts: {}, //store toasts to modify later
  };
  var autoincrement = 0;

  // Initialize library
  function init() {
    // Toast container
    var container = document.createElement("div");
    container.id = "vanillatoasts-container";
    document.body.appendChild(container);

    // @Override
    // Replace create method when DOM has finished loading
    VanillaToasts.create = function (options) {
      var toast = document.createElement("div");
      toast.id = ++autoincrement;
      toast.id = "toast-" + toast.id;
      toast.className = "vanillatoasts-toast";

      // title
      if (options.title) {
        var h4 = document.createElement("h4");
        h4.className = "vanillatoasts-title";
        h4.innerHTML = options.title;
        toast.appendChild(h4);
      }

      // text
      if (options.text) {
        var p = document.createElement("p");
        p.className = "vanillatoasts-text";
        p.innerHTML = options.text;
        toast.appendChild(p);
      }

      // icon
      if (options.icon) {
        var img = document.createElement("img");
        img.src = options.icon;
        img.className = "vanillatoasts-icon";
        toast.appendChild(img);
      }

      // position
      var position = options.positionClass;
      switch (position) {
        case "topLeft":
          container.classList.add("toasts-top-left");
          break;
        case "bottomLeft":
          container.classList.add("toasts-bottom-left");
          break;
        case "bottomRight":
          container.classList.add("toasts-bottom-right");
          break;
        case "topRight":
          container.classList.add("toasts-top-right");
          break;
        case "topCenter":
          container.classList.add("toasts-top-center");
          break;
        case "bottomCenter":
          container.classList.add("toasts-bottom-center");
          break;
        default:
          container.classList.add("toasts-top-right");
          break;
      }

      // click callback
      if (typeof options.callback === "function") {
        toast.addEventListener("click", options.callback);
      }

      // toast api
      toast.hide = function () {
        toast.className += " vanillatoasts-fadeOut";
        toast.addEventListener("animationend", removeToast, false);
      };

      // autohide
      if (options.timeout) {
        setTimeout(toast.hide, options.timeout);
      }

      if (options.type) {
        toast.className += " vanillatoasts-" + options.type;
      }

      toast.addEventListener("click", toast.hide);

      function removeToast() {
        document.getElementById("vanillatoasts-container").removeChild(toast);
        delete VanillaToasts.toasts[toast.id]; //remove toast from object
      }

      document.getElementById("vanillatoasts-container").appendChild(toast);

      //add toast to object so its easily gettable by its id
      VanillaToasts.toasts[toast.id] = toast;

      return toast;
    };

    /*
    custom function to manually initiate timeout of
    the toast.  Useful if toast is created as persistant
    because we don't want it to start to timeout until
    we tell it to
    */
    VanillaToasts.setTimeout = function (toastid, val) {
      if (VanillaToasts.toasts[toastid]) {
        setTimeout(VanillaToasts.toasts[toastid].hide, val);
      }
    };
  }

  return VanillaToasts;
});
