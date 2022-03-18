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

// amount must be in cents | $1 = 100 cents

$payments_packages = array();

$payments_packages[] = array(
    "id" => 0,
    "amount" => 100,
    "credits" => 30,
    "name" => "30 credits");

$payments_packages[] = array(
    "id" => 1,
    "amount" => 200,
    "credits" => 70,
    "name" => "70 credits");

$payments_packages[] = array(
    "id" => 2,
    "amount" => 300,
    "credits" => 120,
    "name" => "120 credits");