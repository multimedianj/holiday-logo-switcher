<div class="wrap">
    <span class="alignright">
        <a target="_blank" href="<?php _e( 'https://wpinclusion.com/plugins/logo-switcher', 'holiday-logo-switcher' ); ?>">
            <img src="<?php echo $this->plugin_dir_url; ?>/images/logo.png" alt="Holiday Logo Switcher" style="height:75px; width:180px;">
        </a>
    </span>
    <h1><?php _e( 'Holiday Logo Switcher', 'holiday-logo-switcher' ); ?></h1>
    <p>
        <?php _e( 'Manage your logos to display at the desired holidays.', 'holiday-logo-switcher' ); ?>
    </p>
    <form method="post" action="options.php">
        <?php
        settings_fields( 'ls_logo_switcher' );
        do_settings_sections( 'ls_logo_switcher' );
        submit_button();
        ?>
    </form>
</div>