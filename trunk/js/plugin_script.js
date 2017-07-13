jQuery(document).ready(function($) {
    // Inside of this function, $() will work as an alias for jQuery()
    // and other libraries also using $ will not be accessible under this shortcut
    $('div.rateit').rateit();

    if ($('#clinic_rating').length > 0) {
      $('#clinic_rating').rateit({
        max: 5,
        step: .5,
        backingfld: '#review_rating_score',
        resetable: false
      });
      $("#clinic_rating").live('rated', function(event, value) {
        if (parseFloat(value) <= 3) {
          $('#review_show_contact').parent().addClass('highlight');
        } else {
          $('#review_show_contact').parent().removeClass('highlight');
        }
      });
    }
    if($('#clinic_review_form').length > 0)
      $("#clinic_review_form").validationEngine()

    $('#open_review_popup').live('click', function() {
      var review_form_container_parent;
      review_form_container_parent = $('#clinic_review_form_container').parent();
      $('#clinic_review_form_container').dialog({
        autoOpen: true,
        modal: true,
        draggable: false,
        resizable: false,
        title: "Review for " + ($('#clinic_review_form_container').attr('clinic_name')),
        width: 541,
        dialogClass: "clinic_review_form_popup green_titled_popup",
        position: {
          my: "top",
          at: "top",
          of: window
        },
        open: function() {
          $('#open_review_popup').hide();
          $('#rating_label').html("Rating");
          $(this).find('.temp_hidden_fields').each(function() {
            $(this).removeClass('temp_hidden_fields').addClass('popup_visible_fields');
            //new RecaptchaOntheFly('clinic_review_recaptha');
          });
          $("#clinic_review_form").validationEngine('hide');
        },
        close: function() {
          var actualOriginalDialogDiv;
          $('#open_review_popup').show();
          $("#review_before_submit").html('');
          $('#rating_label').html("Please select rating");
          $(this).find('.popup_visible_fields').each(function() {
            $(this).removeClass('popup_visible_fields').addClass('temp_hidden_fields');
          });
          actualOriginalDialogDiv = $(this).dialog("widget").find('#clinic_review_form_container').clone();
          actualOriginalDialogDiv.attr('class', '').attr('style', '');
          actualOriginalDialogDiv.appendTo(review_form_container_parent);
          $(this).dialog("destroy").remove();
          $("#clinic_review_form").validationEngine('attach');
          $("#clinic_review_form").validationEngine('hide');
          $('#clinic_rating').html('');
          $('#clinic_rating').rateit({
            max: 5,
            step: .5,
            backingfld: '#review_rating_score',
            resetable: false
          });
          if ($('#reviewed_clinic_success').length > 0 && $('#reviewed_clinic_success').attr('review_success') === 'yes') {
            $('#reviewed_clinic_success').dialog({
              width: 540,
              modal: true,
              title: 'Thanks!',
              closeOnEscape: false,
              dialogClass: "review_clinic_success_social green_titled_popup",
              buttons: {
                'Ok': function() {
                  $(this).dialog('close');
                }
              }
            });
          }
        }
      });
    });


    $('#clinic_review_form').live('submit', function(e) {
      var $this, data;
      e.preventDefault();
      $this = $(this);
      data = $this.serializeArray();
      $("#review_before_submit").html('Submitting.... please wait');
      $.ajax({
        url: $this.attr('action'),
        type: "post",
        dataType: "json",
        data: data,
        success: function(response) {
          if (response.status === 'ok') {
            $("#review_before_submit").html('');
            $this.trigger('reset');
            $this.closest('#clinic_review_form_container.ui-dialog-content').css({
              'display': 'none'
            });
            $this.closest('#clinic_review_form_container.ui-dialog-content').after('<div id="review_success_msg">Please check your email to verify your review.</div>');
            $("#clinic_review_form_container").dialog({
              title: "Thank you for your review!"
            });
            if ($('#reviewed_clinic_success').length > 0) {
              $('#reviewed_clinic_success').attr('review_success', 'yes');
            }
          } else {
            $("#review_before_submit").html(response.errors);
          }
        }
      });
    });


});