<?php
?>
<h3><?php  echo __('Authorization Codes', 'simple-jwt-login') ;?></h3>
<p class="text-justify">
    <?php  echo __('Add authorization codes for authentication to this WordPress', 'simple-jwt-login') ;?>.
    <br />
    <?php  echo __('One of this codes should be added in the request parameters for each API request', 'simple-jwt-login') ;?>.
    <br />
    <?php  echo __('For security reasons please use some random strings', 'simple-jwt-login') ;?>.
    <br />
	<small><?php  echo __('Example: THISISMySpeCiaLAUthCode', 'simple-jwt-login') ;?></small>
</p>
<br />
<h3><?php  echo __('Config', 'simple-jwt-login') ;?></h3>
<label for="auth_code_key"><b><?php  echo __('Auth Code URL Key', 'simple-jwt-login') ;?></b></label> :
<input
        name="auth_code_key"
        value="<?php echo $jwtSettings->getAuthCodeKey();?>"
        class="form-control"
        id="auth_code_key"
        placeholder="<?php  echo __('Auth Code Key', 'simple-jwt-login') ;?>"
/>
<hr/>

<h3><?php  echo __('Auth Codes', 'simple-jwt-login') ;?></h3>
<div id="auth_codes">
    <input type="button" class="btn btn-primary" value="<?php  echo __('Add Auth Code', 'simple-jwt-login') ;?> +" id="add_code"/>
    <br />
    <br />
    <?php
	foreach ( $jwtSettings->getAuthCodes() as $code ) {
		?>
		<div class="form-group auth_row">
			<input type="text" name="auth_codes[]" class="form-control"
			    value="<?php echo $code; ?>"
                placeholder="<?php  echo __('Authentication Key', 'simple-jwt-login') ;?>"
            />

			<a href="javascript:void(0)" onclick="jwt_login_remove_auth_line(jQuery(this));">
                <?php  echo __('delete', 'simple-jwt-login') ;?>
            </a>
		</div>
		<?php
	}
	?>
</div>