<?php

interface OPanda_AWeberOAuthAdapter {

    public function request($method, $uri, $data = array());
    public function getRequestToken($callbackUrl=false);

}


?>
