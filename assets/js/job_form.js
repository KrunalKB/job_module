// Store jobform data to custom post type
(function ($) {
  $("#searchtable").hide();
  function job_form() {
    var formdata = new FormData(reg_form);
    formdata.append("action", "jobform_hook");
    formdata.append("nonce", myVar.nonce);

    $.ajax({
      url: myVar.ajax_url,
      type: "POST",
      contentType: false,
      processData: false,
      data: formdata,
      success: function (response) {
        alert("Submitted successfully");
      },
    });
  }
  $(".job_registerbtn").on("click", function (e) {
    e.preventDefault();
    job_form();
  });
})(jQuery);

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
