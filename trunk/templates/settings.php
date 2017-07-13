<div class="wrap">
    <h2>WP Plugin Template</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('dentistfind_profile_review-group'); ?>
        <?php @do_settings_fields('dentistfind_profile_review-group'); ?>

        <?php do_settings_sections('dentistfind_profile_review'); ?>

        <?php @submit_button(); ?>
    </form>
</div>