(function ($) {
  function client_reg() {
     var username = $("#username").val();
     var email    = $("#email").val();
     var fname    = $("#fname").val();
     var lname    = $("#lname").val();
     var password = $("#password").val();
    $.ajax({
      url : myVar.ajax_url,
      type: "POST",
      data: {
        action  : "client_hook",
        nonce   : myVar.nonce,  
        username: username,
        email   : email,
        fname   : fname,
        lname   : lname,
        password: password
      },
      success: function (response) {
        alert("Registration successfull");
      },
    });
  }
  $(".cl_registerbtn").on("click", function (e) {
    e.preventDefault();
    client_reg();
  });
})(jQuery);
