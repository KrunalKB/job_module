(function ($) {
  function contractor_reg() {
     var username   = $("#username").val();
     var email      = $("#email").val();
     var fname      = $("#fname").val();
     var lname      = $("#lname").val();
     var password   = $("#password").val();
     var buss_name  = $("#buss_name").val();
     var buss_phone = $("#buss_phone").val();
    $.ajax({
      url : myVar.ajax_url,
      type: "POST",
      data: {
        action    : "contractor_hook",
        nonce     : myVar.nonce,
        username  : username,
        email     : email,
        fname     : fname,
        lname     : lname,
        password  : password,
        buss_name : buss_name,
        buss_phone: buss_phone
      },
      success: function (response) {
        alert("Registration successfull");
        // alert(response);
      },
    });
  }
  $(".co_registerbtn").on("click", function (e) {
    e.preventDefault();
    contractor_reg();
  });
})(jQuery);
