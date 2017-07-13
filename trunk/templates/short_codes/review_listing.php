<div class="reviews_container">
  <div class="sep_double_border_darker"></div>
  <div class="review_heading_container">
    <div class="review_summary">
      <h1><a href="<?php echo $result->clinic_profile_url;?>"><?php echo $result->clinic_name;?></a></h1>
      <table align="left" class="table_no_padding table_no_border">
        <?php if(!empty($result->clinic_phone)) {?>
          <tr>
            <td>
              Phone:
            </td>
            <td>
              <?php echo $result->clinic_phone;?>
            </td>
          </tr>
        <?php }?>
        <tr>
          <td>
            Address:
          </td>
          <td>
            <?php echo nl2br($result->clinic_formatted_address);?>
          </td>
        </td>
      </table>
    </div>
    <?php if($result->total_approved_reviews > 0) {?>
      <div class="review_count_summary">
        <table class="table_no_border">
          <tbody>
            <tr>
              <td>
                <div class="rateit" data-rateit-value="<?php echo $result->average_rating;?>" data-rateit-ispreset="true" data-rateit-readonly="true" title="<?php echo $average_rating;?>/5"></div>
              </td>

              <td class="total_review">(<?php echo $result->total_approved_reviews;?>)</td>
            </tr>

            <tr>
              <td colspan="2"><span><?php echo $result->average_rating;?></span> out of 5 stars</td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php }?>
    <div class="clearfix"></div>
  </div>

  <div id="listing_reviews">
    <?php if(sizeof($reviews) > 0) {?>
      <?php foreach($reviews as $review) {?>
        <div class="clinic_review_container">
          <div class="review_rating_title_container">
            <div class="review_rating">
              <div class="rateit" data-rateit-value="<?php echo $review->rating;?>" data-rateit-ispreset="true" data-rateit-readonly="true" title="<?php echo $review->rating;?>/5"></div>
            </div>
            <div class="review_title" style="font-weight: bold">
              <?php echo $review->title;?>
            </div>

            <div class="review_meta">
              <?php if(!empty($review->author)) {?>
                By: <?php if(!empty($review->reviewer_social_profile_url)) {?>
                  <a href="<?php echo $review->reviewer_social_profile_url; ?>" target="_blank">
                    <img class="review_soical_user_avatar" src="<?php echo $review->reviewer_social_profile_image_url; ?>" border="0" />
                  </a>&nbsp;<?php }?><span><?php echo $review->author;?><?php if(!empty($review->review_verification_title)){?>&nbsp;(<?php echo $review->review_verification_title;?>)<?php }?></span><?php }?> &nbsp; on <?php echo $review->created_date;?>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="review_comment">
            <?php echo $review->comment;?>
          </div>
        </div>
      <?php }?>
    <?php } else {?>
      No reviews yet.
    <?php } ?>
  </div>
  <div class="sep_double_border_darker"></div>
  <div class="review_heading_container">
    <h1>Write a review</h1>
    <div class="clearfix"></div>
  </div>
  <div id="clinic_review_form_container" clinic_name="<?php echo $result->clinic_name;?>">
    <form accept-charset="UTF-8" action="<?php echo admin_url('admin-ajax.php') . '?action=post_dp_review';?>" class="simple_form new_review" id="clinic_review_form" method="post" name="clinic_review_form">
      <div class="input review_rating_field_container">
        <label id="rating_label">Please select rating</label>
        <div id="clinic_rating"></div>
        <input class="string optional review_rating_field validate[required,custom[number],min[.5],max[5]]" id="review_rating_score" name="review[rating_score]" size="50" style="display: none;" type="text" value="0.0">
      </div>
      <div class="temp_hidden_fields">
        <div class="input hidden">
          <input class="hidden" id="review_review_request_patient_lead_id"  name="review[review_request_patient_lead_id]" type="hidden">
        </div>

        <div class="input string optional">
          <label class="string optional" for="review_title">Review Title
          *</label><input class="string optional validate[required]" id="review_title" name="review[title]" size="50" type="text">
        </div>
      </div>
      <div class="input text optional">
        <label class="text optional" for="review_comment">Comment *</label>
        <textarea class="text optional validate[required]" cols="40" id="review_comment" name="review[comment]" rows="5"></textarea>
      </div>
      <div class="temp_hidden_fields">
        <div class="input string optional">
          <label class="string optional" for="review_author_name">Your Name
          *</label><input class="string optional validate[required]" id="review_author_name" name="review[author_name]" size="50" type="text">
        </div>
      </div>
      <div class="temp_hidden_fields">
        <div class="input email optional field_with_hint">
          <label class="email optional" for="review_email">Email *</label>
          <input class="string email optional validate[required,custom[email]]" id="review_email" name="review[email]" size="50" type="email">
          <span class="hint">Your email will not be shown on the website, it is used to send you a validation email to verify you are human (and have feelings =D ).</span>
        </div>
      </div>
      <div class="temp_hidden_fields">
        <div class="input tel optional">
          <label class="tel optional" for="review_phone">Phone</label><input class="string tel optional" id="review_phone" name="review[phone]" size="50" type="tel">
        </div>
      </div>
      <div class="temp_hidden_fields" id="clinic_review_recaptha"></div>
      <div class="temp_hidden_fields">
        <div class="input boolean optional">
          <table align="left">
            <tr>
              <td>
                <input checked="checked" class="boolean optional" id="review_show_contact" name="review[show_contact]" type="checkbox" value="1">
              </td>
              <td>
                <label class="boolean optional" for="review_show_contact">Show my contact to dental clinic</label>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="temp_hidden_fields">
        <div class="input boolean optional">
          <table align="left">
            <tr>
              <td>
                <input class="boolean optional" id="review_anonymous" name="review[anonymous]"  type="checkbox" value="1">
              </td>
              <td>
                <label class="boolean optional" for="review_anonymous">Make review anonymous</label>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="temp_hidden_fields">
        <input class="button" id="review_submit_btn" name="commit" type="submit" value="Submit">
      </div><input id="open_review_popup" type="button" value="Submit">
      <div id="review_before_submit"></div>
    </form>
  </div>
  <div class="clearfix"></div>
</div>

<small class="powered_text">Patient Reviews powered by <a href="http://dentistfind.com">DentistFind.com</a></small>