
var $ = jQuery.noConflict();

function add_shortcode() {
  $("#cmodal-title").html("Add Widget");

  $('#crypto-type').val(null).trigger('change');
  $("input[name=crypto_width]").val('');
  $("input[name=crypto_height]").val('');
  $('#coin-type').val(null).trigger('change');
  $("input[name=crypto_txt_color]").val('');
  $("input[name=crypto_bg_color]").val('');
}

function copy_shortcode(copyshortcode) {
  let to_copy = $("#"+copyshortcode).text();
  navigator.clipboard.writeText(to_copy);
  alert('Copied');
}

function save_shortcode() {
  let crypto_type = $("#crypto-type").select2("data")[0].id;
  let crypto_width = $("input[name=crypto_width]").val();
  let crypto_height = $("input[name=crypto_height]").val();
  let coin_type = $("#coin-type").select2("data")[0].id;
  let crypto_txt_color = $("input[name=crypto_txt_color]").val();
  let crypto_bg_color = $("input[name=crypto_bg_color]").val();
  let save_nonce = $("input[name=name_of_your_nonce_field]").val();

  // console.log(crypto_type, crypto_width, crypto_height, coin_type, crypto_txt_color, crypto_bg_color);

  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: ajax_object.ajax_url,
    data: {
      action: "crypto_widget_save_crypto_data",
      crypto_type: crypto_type,
      crypto_width: crypto_width,
      crypto_height: crypto_height,
      coin_type: coin_type,
      crypto_txt_color: crypto_txt_color,
      crypto_bg_color: crypto_bg_color,
      save_nonce:save_nonce
    },
    // beforeSend: function () {
    //   selected_program.show();
    // },
    success: function (response) {
      if (!response || response.error) return;
      if (response.status == "ok") {
        location.reload();
      } else {
        $("#responsemessage").html("Data Not Verified");
        alert("Response else");
      }
    },
  });
}

function edit_shortcode(editshortcode) {
// get values & then open popup with values

  let static_id = $("#"+editshortcode+" #static_id").text();
  let origin_id = $("#"+editshortcode+" #origin_id").text();
  let crypto_type = $("#"+editshortcode+" #crypto_type").text();
  let crypto_coin_type = $("#"+editshortcode+" #crypto_coin_type").text();
  let crypto_txt_color = $("#"+editshortcode+" #crypto_txt_color").text();
  let crypto_bg_color = $("#"+editshortcode+" #crypto_bg_color").text();
  let crypto_width = $("#"+editshortcode+" #crypto_width").text();
  let crypto_height = $("#"+editshortcode+" #crypto_height").text();

  $("#submit-crypto").hide();
  $("#edit-crypto").show();

  $("#cmodal-title").html("Edit Widget: "+ static_id);
  $("select[name='crypto_type']").val(crypto_type).trigger("change");
  $("input[name='crypto_width']").val(crypto_width);
  $("input[name='crypto_height']").val(crypto_height);
  $("select[name='coin_type']").val(crypto_coin_type).trigger("change");
  $("input[name='crypto_txt_color']").val('#'+crypto_txt_color);
  $("input[name='crypto_bg_color']").val('#'+crypto_bg_color);

  $('#crypto-form').modal({
    //closeExisting: false
  });

  $("#edit-crypto").on("click", function (e) {
    e.preventDefault();
    
    let edited_origin_id = origin_id;
    let edited_crypto_type = $("select[name='crypto_type']").select2("data")[0].id;
    let edited_crypto_coin_type = $("select[name='coin_type']").select2("data")[0].id;
    let edited_crypto_txt_color = $("input[name='crypto_txt_color']").val();
    let edited_crypto_bg_color = $("input[name='crypto_bg_color']").val();
    let edited_crypto_width = $("input[name='crypto_width']").val();
    let edited_crypto_height = $("input[name='crypto_height']").val();
    let edit_nonce = $("input[name=name_of_your_nonce_field]").val();
    

    // console.log(edited_origin_id, edited_crypto_type, edited_crypto_coin_type, edited_crypto_txt_color, edited_crypto_bg_color, edited_crypto_width, edited_crypto_height);
    // alert(origin_id);

    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: ajax_object.ajax_url,
      data: {
        action: "crypto_widget_edit_crypto_data",
        edited_origin_id: edited_origin_id,
        edited_crypto_type: edited_crypto_type,
        edited_crypto_coin_type: edited_crypto_coin_type,
        edited_crypto_txt_color: edited_crypto_txt_color,
        edited_crypto_bg_color: edited_crypto_bg_color,
        edited_crypto_width: edited_crypto_width,
        edited_crypto_height: edited_crypto_height,
        edit_nonce:edit_nonce
      },
      // beforeSend: function () {
      //   selected_program.show();
      // },
      success: function (response) {
        if (!response || response.error) return;
        if (response.status == "ok") {
          location.reload();
        } else {
          $("#responsemessage").html("Data Not Verified");
          alert("Response else");
        }
      },
    });

  });

}

function delete_shortcode(deleteshortcode) {
  let to_delete = $("#"+deleteshortcode).text();
  let delete_nonce = $("input[name=name_of_your_nonce_field]").val();

  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: ajax_object.ajax_url,
    data: {
      action: "crypto_widget_delete_copyshortcode",
      to_delete: to_delete,
      delete_nonce:delete_nonce
    },
    success: function (response) {
      if (!response || response.error) return;
      if (response.status == "ok") {
        location.reload();
      } else {
        alert("Response else");
      }
    },
  });

}




$(document).ready(function () {
  $(window).load(function () {

    let table = new DataTable('#cryptotable');

    $(".selec2plug").select2({
      placeholder: "Select an option",
    });
    
    // $("#add_shortcode").on("click", function (e) {
    //   e.preventDefault();
    //   $(".popup").fadeIn(500);
    // });
    // $(".close").click(function() {
    //   $(".popup").fadeOut(500);
    // });

    // let totalshortcode = $("#totalshortcode").text();
    // let to_copy = [];
    // for(i=1; i<=totalshortcode; i++) {
    //   $("#copyshortcode" + i).click(function() {
    //     let to_copy = $("#shortcodedata").text();
    //     navigator.clipboard.writeText(to_copy);
    //     alert('copied');
    //   });
    // }
    

    // $("#deleteshortcode").click(function() {
    //   let origin_id = $("#origin_id").text();
    //   alert(origin_id);
    // });

   
    

    // $("#submit-crypto").on("click", function (e) {
    //   e.preventDefault();

    //   let crypto_type = $("#crypto-type").select2("data")[0].id;
    //   let crypto_width = $("input[name=crypto_width]").val();
    //   let crypto_height = $("input[name=crypto_height]").val();
    //   let coin_type = $("#coin-type").select2("data")[0].id;
    //   let crypto_txt_color = $("input[name=crypto_txt_color]").val();
    //   let crypto_bg_color = $("input[name=crypto_bg_color]").val();

    //   // console.log(crypto_type, crypto_width, crypto_height, coin_type, crypto_txt_color, crypto_bg_color);

    //   jQuery.ajax({
    //     type: "POST",
    //     dataType: "json",
    //     url: ajax_object.ajax_url,
    //     data: {
    //       action: "save_crypto_data",
    //       crypto_type: crypto_type,
    //       crypto_width: crypto_width,
    //       crypto_height: crypto_height,
    //       coin_type: coin_type,
    //       crypto_txt_color: crypto_txt_color,
    //       crypto_bg_color: crypto_bg_color
    //     },
    //     // beforeSend: function () {
    //     //   selected_program.show();
    //     // },
    //     success: function (response) {
    //       if (!response || response.error) return;
    //       if (response.status == "ok") {
    //         location.reload();
    //       } else {
    //         alert("Response else");
    //       }
    //     },
    //   });

    // });

  });
});
