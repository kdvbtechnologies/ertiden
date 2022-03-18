<?php

/*!
 * https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2021 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!defined("APP_SIGNATURE")) {

    header("Location: /");
    exit;
}

if (auth::isSession()) {

    header("Location: /account/wall");
    exit;
}

if (isset($_SESSION['oauth'])) {

    unset($_SESSION['oauth']);
    unset($_SESSION['oauth_id']);
    unset($_SESSION['oauth_name']);
    unset($_SESSION['oauth_email']);
    unset($_SESSION['oauth_link']);

    header("Location: /signup");
    exit;
}

header("Location: /");