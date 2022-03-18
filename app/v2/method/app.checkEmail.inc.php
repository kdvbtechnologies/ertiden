<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, https://ifsoft.co.uk, https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!defined("APP_SIGNATURE")) {

    header("Location: /");
    exit;
}

if (!empty($_POST)) {

    $email = isset($_POST['email']) ? $_POST['email'] : '';

    $email = helper::clearText($email);
    $email = helper::escapeText($email);

    $result = array("error" => true);

    if (!$helper->isEmailExists($email)) {

        $result = array("error" => false);
    }

    echo json_encode($result);
    exit;
}
