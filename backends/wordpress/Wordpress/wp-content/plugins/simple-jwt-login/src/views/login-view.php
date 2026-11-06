<?php
use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;
?>
<h3><?php

    echo __('Allow Auto-Login', 'simple-jwt-login') ;?></h3>
<div class="form-group">
	<input type="radio" id="allow_autologin_no" name="allow_autologin" class="form-control"
	       value="0"
        <?php echo( $jwtSettings->getAllowAutologin() === false ? 'checked' : '' ); ?> />
	<label for="allow_autologin_no"><?php  echo __('No', 'simple-jwt-login') ;?></label>

	<input type="radio" id="allow_autologin_yes" name="allow_autologin" class="form-control"
	       value="1" <?php echo( $jwtSettings->getAllowAutologin() === true ? 'checked' : '' ); ?> />
	<label for="allow_autologin_yes"><?php  echo __('Yes', 'simple-jwt-login') ;?></label>
	<br />
	<small class="url-example-title"><?php  echo __('URL Example', 'simple-jwt-login') ;?>:</small>
	<code class="generated-code">
		<span class="method">GET:</span>
        <span class="code">
            <?php
            $sampleUrlParams = array(
                    'jwt' => __('JWT', 'simple-jwt-login')
            );
            if( $jwtSettings->getRequireLoginAuthKey()){
                $sampleUrlParams[$jwtSettings->getAuthCodeKey()] =__('AUTH_KEY_VALUE', 'simple-jwt-login');
            }
            echo $jwtSettings->generateExampleLink('autologin', $sampleUrlParams);
            ?>
        </span>
        <span class="copy-button">
            <button class="btn btn-secondary btn-xs">
                <?php  echo __('Copy', 'simple-jwt-login') ;?>
            </button>
        </span>
	</code>
</div>
<hr/>
<h3><?php  echo __('Auto-Login Requires Auth Code', 'simple-jwt-login') ;?></h3>
<div class="form-group">
    <input type="radio" id="require_login_auth_no" name="require_login_auth" class="form-control"
           value="0"
        <?php echo $jwtSettings->getRequireLoginAuthKey() === false ? 'checked' : '' ?>
    />
    <label for="require_login_auth_no">
        <?php  echo __('No', 'simple-jwt-login') ;?>
    </label>
    <input type="radio" id="require_login_auth_yes" name="require_login_auth" class="form-control"
           value="1"
		<?php echo $jwtSettings->getRequireLoginAuthKey() === true? 'checked' : '' ?>
    />
    <label for="require_login_auth_yes">
        <?php  echo __('Yes', 'simple-jwt-login') ;?>
    </label>
</div>
<hr />

<div class="row">
    <div class="col-md-12">
        <h3><?php  echo __('JWT Login Settings', 'simple-jwt-login') ;?></h3>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <label for="jwt_login_by"><?php  echo __('Action', 'simple-jwt-login') ;?></label>
        <select name="jwt_login_by" class="form-control" id="jwt_login_by">
            <option value="0"
				<?php
				echo $jwtSettings->getJWTLoginBy() === SimpleJWTLoginSettings::JWT_LOGIN_BY_EMAIL
					? 'selected'
					: ''
				?>
            ><?php  echo __('Log in by Email', 'simple-jwt-login') ;?></option>
            <option value="1"
				<?php
				echo $jwtSettings->getJWTLoginBy() === SimpleJWTLoginSettings::JWT_LOGIN_BY_WORDPRESS_USER_ID
					? 'selected'
					: ''
				?>
            ><?php  echo __('Log in by WordPress User ID', 'simple-jwt-login') ;?></option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="jwt_login_by_paramter"><?php  echo __('JWT parameter key(key name where the option is saved)', 'simple-jwt-login') ;?></label>

        <input type="text" name="jwt_login_by_parameter" class="form-control"
               id="jwt_login_by_paramter"
               value="<?php echo $jwtSettings->getJwtLoginByParameter(); ?>"
               placeholder="<?php  echo __('JWT Parameter here. Example: email', 'simple-jwt-login') ;?>"
        />
        <br />
        <p>
			<?php  echo __('You can use `.` (dot) as a separator for sub-array values.', 'simple-jwt-login') ;?>
            <br/>
			<?php  echo __('Example: Use `user.id` for getting key `id` from array `user`', 'simple-jwt-login') ;?>
        </p>
    </div>
</div>
<hr />

<h3><?php  echo __('Redirect after Auto-Login', 'simple-jwt-login') ;?></h3>
<div class="form-group">
	<input type="radio" id="redirect_dashboard" name="redirect" class="form-control"
	       value="<?php echo SimpleJWTLoginSettings::REDIRECT_DASHBOARD; ?>"
		<?php echo( $jwtSettings->getRedirect() === SimpleJWTLoginSettings::REDIRECT_DASHBOARD ? 'checked' : '' ); ?>
	/>
	<label for="redirect_dashboard"><?php  echo __('Dashboard', 'simple-jwt-login') ;?></label>
	<br />
	<input type="radio" id="redirect_homepage" name="redirect" class="form-control"
	       value="<?php echo SimpleJWTLoginSettings::REDIRECT_HOMEPAGE; ?>"
		<?php echo( $jwtSettings->getRedirect() === SimpleJWTLoginSettings::REDIRECT_HOMEPAGE ? 'checked' : '' ); ?>
	/>
	<label for="redirect_homepage"><?php  echo __('Homepage', 'simple-jwt-login') ;?></label>
	<br />
	<input type="radio" id="redirect_custom" name="redirect" class="form-control"
	       value="<?php echo SimpleJWTLoginSettings::REDIRECT_CUSTOM; ?>"
		<?php echo( $jwtSettings->getRedirect() === SimpleJWTLoginSettings::REDIRECT_CUSTOM ? 'checked' : '' ); ?>
	/>
	<label for="redirect_custom"><?php  echo __('Custom', 'simple-jwt-login') ;?></label>
	<br />
	<input type="text" id="redirect_url" name="redirect_url" class="form-control"
	       placeholder="<?php  echo __('Example', 'simple-jwt-login') ;?>: https://www.your-site.com/sample-page"
	       value="<?php echo $jwtSettings->getCustomRedirectURL() ?>"
	       style="<?php echo ( $jwtSettings->getRedirect() === SimpleJWTLoginSettings::REDIRECT_CUSTOM
		       ? ''
		       : 'display:none;'
	       ); ?>"
	/>
</div>
<hr />

<h3><?php  echo __('Allow Auto-Login only from the following IP addresses', 'simple-jwt-login') ;?>:</h3>
<div class="form-group">
    <input type="text" id="login_ip" name="login_ip" class="form-control"
           value="<?php echo $jwtSettings->getAllowedLoginIps(); ?>" placeholder="<?php  echo __('Enter IP here', 'simple-jwt-login') ;?>"/>
    <small>
        <?php  echo __("If you want to add more IP's, separate them by comma", 'simple-jwt-login') ;?>.
        <br />
        <?php  echo __('Leave blank to allow all IP addresses', 'simple-jwt-login') ;?>.
    </small>
</div>
<hr/>