<?php
use SimpleJWTLogin\Libraries\JWT;
use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;
?>

<div class="row">
    <div class="col-md-12">
        <h3><?php echo __('Route Namespace','simple-jwt-login');?></h3>
        <div class="form-group">
            <input type="text" name="route_namespace" value="<?php echo $jwtSettings->getRouteNamespace();?>"
                   class="form-control"
                   placeholder="<?php echo __('Default route namespace', 'simple-jwt-login');?>"
            />
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <h3><?php

            echo __('JWT Decryption Key', 'simple-jwt-login') ;?></h3>
        <div class="info"><?php  echo __('JWT decryption signature', 'simple-jwt-login') ;?></div>
        <div class="form-group">
            <input type="password" name="decryption_key" class="form-control"
                   id="decryption_key"
                   value="<?php echo $jwtSettings->getDecryptionKey(); ?>"
                   placeholder="<?php  echo __('JWT decryption key here', 'simple-jwt-login') ;?>"
            />
            <a href="javascript:void(0)" onclick="showDecryptionKey()">
                <?php echo __('Toggle decryption key', 'simple-jwt-login');?>
            </a>
        </div>
    </div>
    <div class="col-md-6">
        <h3> <?php  echo __('JWT Decrypt Algorithm', 'simple-jwt-login') ;?></h3>
        <div class="info"><?php echo __('The algorithm that should be used to verify JWT signature.', 'simple-jwt-login');?></div>
        <div class="form-group">
            <select name="jwt_algorithm" class="form-control">
                <?php
                foreach ( JWT::$supported_algs as $alg => $arr ) {
                    $selected = $jwtSettings->getJWTDecryptAlgorithm() === $alg
                        ? 'selected'
                        : '';
                    echo "<option value=\"" . $alg . "\" " . $selected . ">" . $alg . "</option>\n";
                }
                ?>
            </select>

        </div>
    </div>
</div>