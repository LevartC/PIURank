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