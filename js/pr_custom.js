function check_password(pw_input, pw2_input, pw_label) {
    pw_input = $(pw_input);
    pw2_input = $(pw2_input);
    pw_label = $(pw_label);
    var pw_chk = false;
    if (pw_input.val() && pw2_input.val()) {
        if (pw_input.val().length >= 6) {
            if (pw_input.val() == pw2_input.val()) {
                pw_label.html("패스워드가 일치합니다.");
                pw_label.attr("style", "color:rgba(28, 200, 138, 0.9)");
                pw_label.removeAttr("display");
                pw2_input.removeClass("is-invalid");
                pw2_input.addClass("is-valid");
                pw_chk = true;
            } else {
                pw_label.html("패스워드가 일치하지 않습니다.");
                pw_label.attr("style", "color:#e74a3b");
                pw_label.removeAttr("display");
                pw2_input.removeClass("is-valid");
                pw2_input.addClass("is-invalid");
                pw_chk = false;
            }
        } else {
            pw_label.html("패스워드는 6자 이상이어야 합니다.");
            pw_label.attr("style", "color:#e74a3b");
            pw_label.removeAttr("display");
            pw2_input.removeClass("is-valid");
            pw2_input.addClass("is-invalid");
            pw_chk = false;
        }
    } else {
        pw_chk = false;
    }
    return pw_chk;
}

function resetOrientation(srcBase64, srcOrientation, callback) {
    var img = new Image();

    img.onload = function() {
      var width = img.width,
          height = img.height,
          canvas = document.createElement('canvas'),
          ctx = canvas.getContext("2d");

      // set proper canvas dimensions before transform & export
      if (4 < srcOrientation && srcOrientation < 9) {
        canvas.width = height;
        canvas.height = width;
      } else {
        canvas.width = width;
        canvas.height = height;
      }

      // transform context before drawing image
      switch (srcOrientation) {
        case 2: ctx.transform(-1, 0, 0, 1, width, 0); break;
        case 3: ctx.transform(-1, 0, 0, -1, width, height); break;
        case 4: ctx.transform(1, 0, 0, -1, 0, height); break;
        case 5: ctx.transform(0, 1, 1, 0, 0, 0); break;
        case 6: ctx.transform(0, 1, -1, 0, height, 0); break;
        case 7: ctx.transform(0, -1, -1, 0, height, width); break;
        case 8: ctx.transform(0, -1, 1, 0, 0, width); break;
        default: break;
      }

      // draw image
      ctx.drawImage(img, 0, 0);

      // export base64
      callback(canvas.toDataURL());
    };

    img.src = srcBase64;
};