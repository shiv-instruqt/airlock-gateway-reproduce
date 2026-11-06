<?php

use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;

wp_enqueue_style( 'simple-jwt-login-bootstrap', plugins_url( '../../vendor/bootstrap/bootstrap.min.css', __FILE__ ) );
wp_enqueue_style( 'simple-jwt-login-style', plugins_url( '../../css/style.css', __FILE__ ) );

wp_enqueue_script( 'simple-jwt-login-scripts', plugins_url( '../../js/scripts.js', __FILE__ ), array( 'jquery' ) );
wp_enqueue_script( 'simple-jwt-bootstrap-min', plugins_url( '../../vendor/bootstrap/bootstrap.min.js', __FILE__ ) );

$jwtSettings = new SimpleJWTLoginSettings(new \SimpleJWTLogin\Modules\WordPressData());
try {
	$saved = $jwtSettings->watchForUpdates( $_POST );
	if($saved){
		?>
        <div class="updated notice my-acf-notice is-dismissible" >
            <p><?php  echo __('Settings successfully saved', 'simple-jwt-login') ;?>.</p>
        </div>
		<?php
    }
}catch ( \Exception $e){
    ?>
    <div class="notice error my-acf-notice is-dismissible" >
        <p><?php echo $e->getMessage(); ?></p>
    </div>
    <?php
}
?>

<div id="jwt-login-plugin" class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>
                <?php  echo __('Simple JWT Login Settings', 'simple-jwt-login') ;?>
            </h1>
        </div>
    </div>
    <form method="post">
        <div class="row">
            <div class="col-md-12">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item  nav-link active" id="nav-general-tab" data-toggle="tab" href="#nav-general"
                           role="tab" aria-controls="nav-general" aria-selected="true">
                            <?php  echo __('General', 'simple-jwt-login') ;?>
                        </a>
                        <a class="nav-item nav-link" id="nav-auth-codes-tab" data-toggle="tab" href="#nav-auth-codes"
                           role="tab" aria-controls="nav-auth-codes" aria-selected="false">
                            <?php  echo __('Auth Codes', 'simple-jwt-login') ;?>
                        </a>
                        <a class="nav-item nav-link" id="nav-login-tab" data-toggle="tab" href="#nav-login" role="tab"
                           aria-controls="nav-login" aria-selected="false">
                            <?php  echo __('Login Config', 'simple-jwt-login') ;?>
                        </a>
                        <a class="nav-item nav-link" id="nav-register-tab" data-toggle="tab" href="#nav-register"
                           role="tab" aria-controls="nav-register" aria-selected="false">
                            <?php  echo __('Register Config', 'simple-jwt-login') ;?>
                        </a>
                        <a class="nav-item nav-link" id="nav-register-tab" data-toggle="tab" href="#nav-delete"
                           role="tab" aria-controls="nav-register" aria-selected="false">
		                    <?php  echo __('Delete User Config', 'simple-jwt-login') ;?>
                        </a>
                    </div>
                </nav>

                <div class="tab-content" id="tabs-container">

                    <!-- general -->
                    <div class="tab-pane fade show active" id="nav-general" role="tabpanel"
                         aria-labelledby="nav-general-tab">
                      <?php require_once('general-view.php'); ?>
                    </div>

                    <!-- auth-codes -->
                    <div class="tab-pane fade" id="nav-auth-codes" role="tabpanel" aria-labelledby="nav-auth-codes-tab">
                       <?php require_once('auth-codes-view.php'); ?>
                    </div>

                    <!-- login -->
                    <div class="tab-pane fade" id="nav-login" role="tabpanel" aria-labelledby="nav-login-tab">
                        <?php require_once('login-view.php'); ?>
                    </div>

                    <!-- register -->
                    <div class="tab-pane fade" id="nav-register" role="tabpanel" aria-labelledby="nav-register-tab">
                        <?php require_once('register-view.php'); ?>
                    </div>

                    <!-- delete -->
                    <div class="tab-pane fade" id="nav-delete" role="tabpanel" aria-labelledby="nav-register-tab">
		                <?php require_once('delete-view.php'); ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
				<?php submit_button(); ?>
            </div>
        </div>
    </form>
</div>

<div id="code_line" style="display:none">
    <div class="form-group auth_row">
        <input
                type="text"
                name="auth_codes[]"
                class="form-control"
                placeholder="<?php  echo __('Add Authentication Key here', 'simple-jwt-login') ;?>..."
        />
        <a href="javascript:void(0)" onclick="jwt_login_remove_auth_line(jQuery(this));">
            <?php  echo __('delete', 'simple-jwt-login') ;?>
        </a>
    </div>
</div>
