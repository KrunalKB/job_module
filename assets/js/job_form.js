// custom method for file size

$.validator.addMethod(
  "file_size",
  function (value, element) {
    const limit = 2 * 1024 * 1024;
    const size = element.files[0].size;
    if (size > limit) {
      return false;
    }
    return true;
  },
  "File size should be less than 2 mb "
);

//custom method for file type

$.validator.addMethod(
  "file_type",
  function (value, element) {
    const type = element.files[0].type;
    if (type != "image/jpg" && type != "image/jpeg" && type != "image/png") {
      $("#imgpre").hide();
      return false;
    }
    return true;
  },
  "File type must be jpg,jpeg,png only"
);

// Validation
$(document).ready(function () {
  $(".loader").hide();
  $("#searchtable").hide();
  $("#regfrm").validate({
    rules: {
      client: {
        required: true,
      },
      contractor: {
        required: true,
      },
      jobname: {
        required: true,
      },
      jobdesc: {
        required: true,
      },
      image: {
        required: true,
        file_size: true,
        file_type: true,
      },
      price: {
        required: true,
      },
    },
    messages: {
      client: {
        required: "Please enter client name",
      },
      contractor: {
        required: "Please enter client name",
      },
      jobname: {
        required: "Please enter job name",
      },
      jobdesc: {
        required: "Please enter job description",
      },
      image: {
        required: "Please select the image",
      },
      price: {
        required: "Please enter price",
      },
    },

    submitHandler: function (form) {
      $(".job_registerbtn").css({
        "background-color": "#cccccc",
        color: "#808080",
      });
      $(".loader").show();
      var formdata = new FormData(form);
      formdata.append("action", "jobform_hook");
      formdata.append("nonce", myVar.nonce);
      $.ajax({
        url: myVar.ajax_url,
        type: "POST",
        contentType: false,
        processData: false,
        data: formdata,
        success: function (response) {
          $(".job_registerbtn").css({
            "background-color": "#0170b9",
            color: "#fff",
          });
          $(".loader").hide();
          $(".msg").html("Job submitted successfully");
        },
      });
    },
  });
});

// (function ($) {
//   $("#searchtable").hide();
//   function job_form() {
//     var formdata = new FormData(reg_form);
//     formdata.append("action", "jobform_hook");
//     formdata.append("nonce", myVar.nonce);
//     $.ajax({
//       url: myVar.ajax_url,
//       type: "POST",
//       contentType: false,
//       processData: false,
//       data: formdata,
//       success: function (response) {
//         alert("Submitted successfully");
//       },
//     });
//   }
//   $(".job_registerbtn").on("click", function (e) {
//     e.preventDefault();
//     job_form();
//   });
//   // });
// })(jQuery);

// Getting client name
(function ($) {
  $("#client").keyup(function (e) {
    e.preventDefault;
    var search = $(this).val();
    if (search != "") {
      $.ajax({
        url: myVar.ajax_url,
        type: "POST",
        data: {
          action: "search_client_hook",
          search: search,
        },
        success: function (response) {
          $("#searchtable").fadeIn("fast").html(response);
          console.log(response);
        },
      });
    } else {
      $("#searchtable").fadeOut();
    }
  });
  $(document).on("click", "#searchtable li", function () {
    $("#client").val($(this).text());
    $("#searchtable").fadeOut("fast");
  });
})(jQuery);

// Getting contractor name
(function ($) {
  $("#contractor").keyup(function (e) {
    e.preventDefault;
    var search = $(this).val();
    if (search != "") {
      $.ajax({
        url: myVar.ajax_url,
        type: "POST",
        data: {
          action: "search_contractor_hook",
          search: search,
        },
        success: function (response) {
          $("#searchtable").fadeIn("fast").html(response);
          console.log(response);
        },
      });
    } else {
      $("#searchtable").fadeOut();
    }
  });
  $(document).on("click", "#searchtable li", function () {
    $("#contractor").val($(this).text());
    $("#searchtable").fadeOut("fast");
  });
})(jQuery);
