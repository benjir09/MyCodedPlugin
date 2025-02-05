var $ = jQuery.noConflict();
$(document).ready(function () {
  $(window).load(function () {
    $("#program-product").select2({
      placeholder: "Select an option",
    });

    // $(".msh1 .msh-stepno").on("click", function () {
    //   $(".msh-stepno").removeClass("activeheader");
    //   $(".msh1 .msh-stepno").addClass("activeheader");

    //   $(".msform-step").removeClass("activestep");
    //   $(".msform-step1").addClass("activestep");
    //   $("#msnext").html("NEXT");
    //   $("#msprev").prop("disabled", true);
    // });

    // $(".msh2 .msh-stepno").on("click", function () {
    //   $(".msh-stepno").removeClass("activeheader");
    //   $(".msh2 .msh-stepno").addClass("activeheader");

    //   $(".msform-step").removeClass("activestep");
    //   $(".msform-step2").addClass("activestep");
    //   $("#msnext").html("NEXT");
    //   $(".msbutton").removeAttr("disabled");
    //   get_product_details();
    // });

    // $(".msh3 .msh-stepno").on("click", function () {
    //   $(".msh-stepno").removeClass("activeheader");
    //   $(".msh3 .msh-stepno").addClass("activeheader");

    //   $(".msform-step").removeClass("activestep");
    //   $(".msform-step3").addClass("activestep");
    //   $("#msnext").html("NEXT");
    //   $(".msbutton").removeAttr("disabled");
    // });

    // $(".msh4 .msh-stepno").on("click", function () {
    //   $(".msh-stepno").removeClass("activeheader");
    //   $(".msh4 .msh-stepno").addClass("activeheader");

    //   $(".msform-step").removeClass("activestep");
    //   $(".msform-step4").addClass("activestep");
    //   $("#msnext").html("Pay");
    //   $(".msbutton").removeAttr("disabled");
    // });

    $('.resumewrap input[type="file"]').change(function (e) {
      $(".uploadedfile").empty();
      var filename = e.target.files[0].name;
      $(".uploadedfile").append(filename);
    });

    if ($("input[type='radio'][name='program_category']").is(":checked")) {
      $(".msh-stepno").removeClass("activeheader");
      $(".msh2 .msh-stepno").addClass("activeheader");

      $(".msform-step").removeClass("activestep");
      $(".msform-step2").addClass("activestep");
      $("#msnext").html("NEXT");
      $(".msbutton").removeAttr("disabled");
    }

    $("input[type=radio][name=program_category]").change(function () {
      let cat_id = this.value;
      let selected_program = $(".selectproduct .ajaxloading");

      $("#msnext").prop("disabled", true);

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: ajax_object.ajax_url,
        data: {
          action: "get_product_list",
          cid: cat_id,
        },
        beforeSend: function () {
          selected_program.show();
        },
        success: function (response) {
          selected_program.hide();
          if (!response || response.error) return;
          if (response.status == "ok") {
            $("#program-product").html(response.data);
          } else {
            alert("Response else");
          }
        },
      });
    });

    $("select[name=program_product]").change(function () {
      let prod_id = this.value;
      if (prod_id) {
        $("#msnext").removeAttr("disabled");
      }
    });
  });
});

function stepchange(e, nextprev) {
  e.preventDefault();
  var activestep = $(".msh-stepno.activeheader").parent().prop("className");
  if (nextprev == "msnext") {
    if (activestep == "msh1") {
      $(".msh-stepno").removeClass("activeheader");
      $(".msh2 .msh-stepno").addClass("activeheader");

      $(".msform-step").removeClass("activestep");
      $(".msform-step2").addClass("activestep");

      $(".msbutton").removeAttr("disabled");
      get_product_details();
    } else if (activestep == "msh2") {
      step2validation();
    } else if (activestep == "msh3") {
      step3validation();
    } else if (activestep == "msh4") {
      final_validation();
    }
  } else if (nextprev == "msprev") {
    if (activestep == "msh4") {
      $(".msh-stepno").removeClass("activeheader");
      $(".msh3 .msh-stepno").addClass("activeheader");

      $(".msform-step").removeClass("activestep");
      $(".msform-step3").addClass("activestep");
      $("#msnext").html("NEXT");
      $("#msnext").removeClass("final-validation");
    } else if (activestep == "msh3") {
      $(".msh-stepno").removeClass("activeheader");
      $(".msh2 .msh-stepno").addClass("activeheader");

      $(".msform-step").removeClass("activestep");
      $(".msform-step2").addClass("activestep");
    } else if (activestep == "msh2") {
      $(".msh-stepno").removeClass("activeheader");
      $(".msh1 .msh-stepno").addClass("activeheader");

      $(".msform-step").removeClass("activestep");
      $(".msform-step1").addClass("activestep");
      $("#msprev").prop("disabled", true);
    }
  }
}

function get_product_details() {
  let selected_product = $("#program-product").select2("data");
  let product_id = selected_product[0].id;

  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: ajax_object.ajax_url,
    data: {
      action: "get_product_meta",
      pid: product_id,
    },
    success: function (response) {
      //selected_program.hide();
      if (!response || response.error) return;
      if (response.status == "ok") {
        $(".program-details-top-wrap").html(response.table);
        console.log(response.type);
      } else {
      }
    },
  });
}

function step2validation() {
  let expected_salary = $("input[name=expected_salary]").val();
  let last_salary = $("input[name=last_salary]").val();
  let previous_company = $("input[name=previous_company]").val();
  let experience = $("input[name=experience]").val();
  let how_soon_join = $("input[name=how_soon_join]").val();

  if (expected_salary) $("input[name=expected_salary]").removeClass("required");
  else $("input[name=expected_salary]").addClass("required");

  if (last_salary) $("input[name=last_salary]").removeClass("required");
  else $("input[name=last_salary]").addClass("required");

  if (previous_company)
    $("input[name=previous_company]").removeClass("required");
  else $("input[name=previous_company]").addClass("required");

  if (experience) $("input[name=experience]").removeClass("required");
  else $("input[name=experience]").addClass("required");

  if (how_soon_join) $("input[name=how_soon_join]").removeClass("required");
  else $("input[name=how_soon_join]").addClass("required");

  if (
    expected_salary &&
    last_salary &&
    previous_company &&
    experience &&
    how_soon_join
  ) {
    $(".msh-stepno").removeClass("activeheader");
    $(".msh3 .msh-stepno").addClass("activeheader");

    $(".msform-step").removeClass("activestep");
    $(".msform-step3").addClass("activestep");
  }
}

function step3validation() {
  let first_name = $("input[name=first_name]").val();
  let last_name = $("input[name=last_name]").val();
  let date_of_birth = $("input[name=date_of_birth]").val();
  let gender = $("input[name=gender]").val();
  let nationality = $("input[name=nationality]").val();
  let marital_status = $("input[name=marital_status]").val();
  let email_address = $("input[name=email_address]").val();

  if (email_validation(email_address) == false) {
    email_address = "";
  }

  let phone_number = $("input[name=phone_number]").val();
  let address = $("input[name=address]").val();
  let city = $("input[name=city]").val();
  let zip_code = $("input[name=zip_code]").val();
  let country = $("input[name=nationality]").val();

  if (first_name) $("input[name=first_name]").removeClass("required");
  else $("input[name=first_name]").addClass("required");

  if (last_name) $("input[name=last_name]").removeClass("required");
  else $("input[name=last_name]").addClass("required");

  if (date_of_birth) $("input[name=date_of_birth]").removeClass("required");
  else $("input[name=date_of_birth]").addClass("required");

  if (gender) $("input[name=gender]").removeClass("required");
  else $("input[name=gender]").addClass("required");

  if (nationality) $("input[name=nationality]").removeClass("required");
  else $("input[name=nationality]").addClass("required");

  if (marital_status) $("input[name=marital_status]").removeClass("required");
  else $("input[name=marital_status]").addClass("required");

  if (email_address) $("input[name=email_address]").removeClass("required");
  else $("input[name=email_address]").addClass("required");

  if (phone_number) $("input[name=phone_number]").removeClass("required");
  else $("input[name=phone_number]").addClass("required");

  if (address) $("input[name=address]").removeClass("required");
  else $("input[name=address]").addClass("required");

  if (city) $("input[name=city]").removeClass("required");
  else $("input[name=city]").addClass("required");

  if (zip_code) $("input[name=zip_code]").removeClass("required");
  else $("input[name=zip_code]").addClass("required");

  if (country) $("input[name=country]").removeClass("required");
  else $("input[name=country]").addClass("required");

  if (
    first_name &&
    last_name &&
    date_of_birth &&
    gender &&
    nationality &&
    marital_status &&
    email_address &&
    phone_number &&
    address &&
    city &&
    zip_code &&
    country
  ) {
    $(".msh-stepno").removeClass("activeheader");
    $(".msh4 .msh-stepno").addClass("activeheader");

    $(".msform-step").removeClass("activestep");
    $(".msform-step4").addClass("activestep");
    $("#msnext").html("Pay");
    $("#msnext").addClass("final-validation");

    let project = $(
      "input[type='radio'][name='program_category']:checked"
    ).attr("alt");
    $("#project-final").html(project);

    let selected_product = $("#program-product").select2("data");
    let product_id = selected_product[0].id;

    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: ajax_object.ajax_url,
      data: {
        action: "step4_product_details",
        pid: product_id,
      },
      success: function (response) {
        if (!response || response.error) return;
        if (response.status == "ok") {
          $(".pdc-total").html(response.data);
          $("#pdc-total-td").html(response.data);
        } else {
          alert("Response else");
        }
      },
    });
  }
}

function final_validation() {
  var formData = new FormData(document.getElementById("msform"));

  formData.append("resume", $("input[name=resume]")[0].files[0]);
  formData.append(
    "payment_name",
    $("input[type='radio'][name='payment_method']:checked").attr("alt")
  );
  formData.append("action", "final_submit");

  let category_id = $("input[type=radio][name=program_category]:checked").val();
  let product_id = $("#program-product").select2("data")[0].id;
  let expected_salary = $("input[name=expected_salary]").val();
  let last_salary = $("input[name=last_salary]").val();
  let previous_company = $("input[name=previous_company]").val();
  let experience = $("input[name=experience]").val();
  let how_soon_join = $("input[name=how_soon_join]").val();
  let first_name = $("input[name=first_name]").val();
  let last_name = $("input[name=last_name]").val();
  let date_of_birth = $("input[name=date_of_birth]").val();
  let gender = $("input[name=gender]").val();
  let nationality = $("input[name=nationality]").val();
  let marital_status = $("input[name=marital_status]").val();
  let email_address = $("input[name=email_address]").val();
  let phone_number = $("input[name=phone_number]").val();
  let address = $("input[name=address]").val();
  let city = $("input[name=city]").val();
  let zip_code = $("input[name=zip_code]").val();
  let country = $("input[name=nationality]").val();
  let payment_type = $("input[type=radio][name=payment_method]:checked").val();

  if (
    category_id &&
    product_id &&
    expected_salary &&
    last_salary &&
    previous_company &&
    experience &&
    how_soon_join &&
    first_name &&
    last_name &&
    date_of_birth &&
    gender &&
    nationality &&
    marital_status &&
    email_address &&
    phone_number &&
    address &&
    city &&
    zip_code &&
    country &&
    payment_type
  ) {
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: ajax_object.ajax_url,
      processData: false,
      contentType: false,
      data: formData,
      success: function (response) {
        if (!response || response.error) return;
        if (response.status == "ok") {
          alert("Order Created");
          console.log(response.data);
        } else {
          alert("Response else");
        }
      },
    });
  }
}

function email_validation(email) {
  var regex =
    /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if (!regex.test(email)) {
    return false;
  } else {
    return true;
  }
}
