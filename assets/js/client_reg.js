(function ($) {
  function client_reg() {
     var username = $("#username").val();
     var email    = $("#email").val();
     var fname    = $("#fname").val();
     var lname    = $("#lname").val();
     var password = $("#password").val();
     var url = $("#url").val();
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
        password: password,
        url : url
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

// (function($){
//   $("#username").on("click",function(){
//     $.ajax({
//     url: myVar.ajax_url,
//     type: "GET",
//     data:{
//       action : "client_verify",
//       // nonce : myVar.nonce,
//     },
//     success: function(response){
//       console.log(response);
//     }
//   });
//   })
    
// })(jQuery);
