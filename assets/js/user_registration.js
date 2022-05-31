$.validator.addMethod(
  "alphabet",
  function (value, element) {
    return this.optional(element) || /^[a-z]+$/i.test(value);
  },
  "Only alphabets are allowed"
);

$(document).ready(function () {
  $(".loader").hide();
  $("#regfrm").validate({
    rules: {
      username: {
        required: true,
      },
      email: {
        required: true,
      },
      fname: {
        required: true,
        alphabet: true,
      },
      lname: {
        required: true,
        alphabet: true,
      },
      password: {
        required: true,
      },
      buss_name: {
        required: true,
      },
      buss_phone: {
        required: true,
      },
    },
    messages: {
      username: {
        required: "Please enter username",
      },
      email: {
        required: "Please enter email",
      },
      fname: {
        required: "Please enter first name",
      },
      lname: {
        required: "Please enter last name",
      },
      password: {
        required: "Please enter password",
      },
      buss_name: {
        required: "Please enter bussiness name",
      },
      buss_phone: {
        required: "Please enter bussiness number",
      },
    },

    submitHandler: function (form) {
      $(".registerbtn").css({
        "background-color": "#cccccc",
        color: "#808080",
      });
      $(".loader").show();
      var form_data = new FormData(form);
      form_data.append("action", "user_hook");
      form_data.append("nonce", myLink.nonce);
      $.ajax({
        url: myLink.ajax_link,
        type: "POST",
        contentType: false,
        processData: false,
        data: form_data,
        success: function (response) {
          $(".registerbtn").css({
            "background-color": "#0170b9",
            color: "#fff",
          });
          $(".loader").hide();
          $(".msg").html(response);
        },
      });
    },
  });
});
