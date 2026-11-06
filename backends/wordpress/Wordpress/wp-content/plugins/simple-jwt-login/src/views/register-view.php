<?php

use SimpleJWTLogin\Modules\RouteService;
use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;

?>
<h3><?php  echo __('Allow Register', 'simple-jwt-login') ;?></h3>
<div class="form-group">
	<input type="radio" id="allow_register_no" name="allow_register" class="form-control"
	       value="0"
           <?php echo( $jwtSettings->getAllowRegister() === false ? 'checked' : '' ); ?>
    />
	<label for="allow_register_no">
        <?php  echo __('No', 'simple-jwt-login') ;?>
    </label>

	<input type="radio" id="allow_register_yes" name="allow_register" class="form-control"
	       value="1" <?php echo( $jwtSettings->getAllowRegister() === true ? 'checked' : '' ); ?> />
	<label for="allow_register_yes">
        <?php  echo __('Yes', 'simple-jwt-login') ;?>
    </label>
	<br />
	<small class="url-example-title"><?php  echo __('URL Example', 'simple-jwt-login') ;?>:</small>
	<code class="generated-code">
        <span class="method">POST:</span>
        <span class="code">
            <?php
            $sampleUrlParams = array(
                'email'                        => __('NEW_USER_EMAIL', 'simple-jwt-login'),
                'password'                     => __('NEW_USER_PASSWORD', 'simple-jwt-login'),
            );
            if($jwtSettings->getRequireRegisterAuthKey()){
                $sampleUrlParams[$jwtSettings->getAuthCodeKey()] =  __('AUTH_KEY_VALUE', 'simple-jwt-login');
            }
            echo $jwtSettings->generateExampleLink( RouteService::USER_ROUTE, $sampleUrlParams);
            ?>
        </span>
        <span class="copy-button">
            <button class="btn btn-secondary btn-xs">
                <?php  echo __('Copy', 'simple-jwt-login') ;?>
            </button>
        </span>
	</code>
</div>
<h3><?php  echo __('Register Requires Auth Code', 'simple-jwt-login') ;?></h3>
<div class="form-group">
    <input type="radio" id="require_register_auth_no" name="require_register_auth" class="form-control"
           value="0"
		<?php echo $jwtSettings->getRequireRegisterAuthKey() === false ? 'checked' : '' ?>
    />
    <label for="require_register_auth_no">
        <?php  echo __('No', 'simple-jwt-login') ;?>
    </label>
    <input type="radio" id="require_register_auth_yes" name="require_register_auth" class="form-control"
           value="1"
		<?php echo $jwtSettings->getRequireRegisterAuthKey() === true? 'checked' : '' ?>
    />
    <label for="require_register_auth_yes">
        <?php  echo __('Yes', 'simple-jwt-login') ;?>
    </label>
    <div id="require_register_auth_alert" class="alert alert-warning" role="alert"
         style="<?php echo $jwtSettings->getRequireRegisterAuthKey() === true? 'display:none;' : '' ;?>">
        <?php  echo __(" Warning! It's not recommended to allow register without Auth Codes", 'simple-jwt-login') ;?>.
    </div>
</div>
<h3><?php  echo __('New User profile slug', 'simple-jwt-login') ;?></h3>
<small><?php  echo __('Example', 'simple-jwt-login') ;?>: `administrator`, `editor`, `author`, `contributor`, `subscriber`</small>
<a href="https://wordpress.org/support/article/roles-and-capabilities/" target="_blank">
    <?php  echo __('More details', 'simple-jwt-login') ;?>
</a>
<div class="form-group">
	<input type="text" name="new_user_profile" class="form-control"
	       value="<?php echo $jwtSettings->getNewUSerProfile(); ?>"
           placeholder="<?php  echo __('New user profile name', 'simple-jwt-login') ;?>"
    />
</div>
<hr />
<h3><?php  echo __('Allow Register only from the following IP addresses', 'simple-jwt-login') ;?>:</h3>
<div class="form-group">
    <input type="text" id="register_ip" name="register_ip" class="form-control"
           value="<?php echo $jwtSettings->getAllowedRegisterIps(); ?>" placeholder="<?php  echo __('Enter IP here', 'simple-jwt-login') ;?>"/>
    <small>
        <?php  echo __("If you want to add more IP's, separate them by comma", 'simple-jwt-login') ;?>.
        <br />
        <?php  echo __('Leave blank to allow all IP addresses', 'simple-jwt-login') ;?>.
    </small>
</div>
<hr/>

<h3><?php  echo __('Allow Register only for specific domains', 'simple-jwt-login') ;?>:</h3>
<div class="form-group">
    <input type="text" id="register_domain" name="register_domain" class="form-control"
           value="<?php echo $jwtSettings->getAllowedRegisterDomain(); ?>" placeholder="<?php  echo __('', 'simple-jwt-login') ;?>Email domain"/>
    <small>
        <?php  echo __('For example, if you want to allow registration only for users that use their gmail account, add `gmail.com`', 'simple-jwt-login') ;?>.
        <?php  echo __('For multiple domains, separate them by comma', 'simple-jwt-login') ;?>.
        <br />
        <?php  echo __('Leave blank to allow all domains', 'simple-jwt-login') ;?>.
    </small>
</div>