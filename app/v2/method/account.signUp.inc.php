<?php

/*!
 * https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2022 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!defined("APP_SIGNATURE")) {

    header("Location: /");
    exit;
}

require_once '../sys/addons/recaptcha/autoload.php';

if (!empty($_POST)) {

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;

    $hash = isset($_POST['hash']) ? $_POST['hash'] : '';

    $appType = isset($_POST['appType']) ? $_POST['appType'] : 0; // 0 = APP_TYPE_UNKNOWN
    $fcm_regId = isset($_POST['fcm_regId']) ? $_POST['fcm_regId'] : '';
    $lang = isset($_POST['lang']) ? $_POST['lang'] : '';

    $oauth_id = isset($_POST['oauth_id']) ? $_POST['oauth_id'] : '';
    $oauth_type = isset($_POST['oauth_type']) ? $_POST['oauth_type'] : 0;

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $referrer = isset($_POST['referrer']) ? $_POST['referrer'] : 0;

    $photoUrl = isset($_POST['photo']) ? $_POST['photo'] : '';
    $u_gender = isset($_POST['gender']) ? $_POST['gender'] : 0;
    $u_age = isset($_POST['age']) ? $_POST['age'] : 18;

    $language = isset($_POST['language']) ? $_POST['language'] : '';

    $recaptcha_token = isset($_POST['recaptcha_token']) ? $_POST['recaptcha_token'] : '';
    $recaptcha_key = isset($_POST['recaptcha_key']) ? $_POST['recaptcha_key'] : '';

    $clientId = helper::clearInt($clientId);

    $referrer = helper::clearInt($referrer);

    $username = helper::clearText($username);
    $fullname = helper::clearText($fullname);
    $password = helper::clearText($password);
    $email = helper::clearText($email);
    $language = helper::clearText($language);

    $username = helper::escapeText($username);
    $fullname = helper::escapeText($fullname);
    $password = helper::escapeText($password);
    $email = helper::escapeText($email);
    $language = helper::escapeText($language);

    $oauth_id = helper::escapeText($oauth_id);
    $oauth_id = helper::clearText($oauth_id);

    $oauth_type = helper::clearInt($oauth_type);

    $u_age = helper::clearInt($u_age);
    $u_gender = helper::clearInt($u_gender);
    $photoUrl = helper::clearText($photoUrl);
    $photoUrl = helper::escapeText($photoUrl);

    $appType = helper::clearInt($appType);

    $fcm_regId = helper::clearText($fcm_regId);
    $fcm_regId = helper::escapeText($fcm_regId);

    $lang = helper::clearText($lang);
    $lang = helper::escapeText($lang);

    if ($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
    }

    if (APP_USE_CLIENT_SECRET) {

        if ($hash !== md5(md5($username).CLIENT_SECRET)) {

            api::printError(ERROR_CLIENT_SECRET, "Error hash.");
        }
    }

    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    //

    $settings = new settings($dbo);

    if ($settings->getIntValue("RECAPTCHA_SIGNUP_APP") == 1) {

        $recaptcha = new \ReCaptcha\ReCaptcha($recaptcha_key);
        $resp = $recaptcha->verify($recaptcha_token, $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess()){

            api::printError(ERROR_RECAPTCHA, "Error reCaptcha.");
        }
    }

    unset($settings);

    //

    $account = new account($dbo);
    $result = $account->signup($username, $fullname, $password, $email, $language);
    unset($account);

    if (!$result['error']) {

        $account = new account($dbo);
        $account->setState(ACCOUNT_STATE_ENABLED);
        $account->setLastActive();
        $result = $account->signin($username, $password);
        unset($account);

        if ($result['error'] === false) {

            $auth = new auth($dbo);
            $result = $auth->create($result['accountId'], $clientId, $appType, $fcm_regId, $lang);

            if (!$result['error']) {

                $account = new account($dbo, $result['accountId']);

                $account->setSex($u_gender);
                $account->setAge($u_age);

                // refsys

                if ($referrer != 0) {

                    $ref = new refsys($dbo);
                    $ref->setRequestFrom($account->getId());
                    $ref->setBonus(BONUS_REFERRAL);
                    $ref->setReferrer($referrer);

                    unset($ref);
                }

                if (strlen($photoUrl) != 0) {

                    $photos = array(
                        "error" => false,
                        "originPhotoUrl" => $photoUrl,
                        "normalPhotoUrl" => $photoUrl,
                        "bigPhotoUrl" => $photoUrl,
                        "lowPhotoUrl" => $photoUrl
                    );

                    $account->setPhoto($photos);

                    unset($photos);
                }

                //

                if (strlen($oauth_id) != 0) {

                    $helper = new helper($dbo);

                    switch ($oauth_type) {

                        case OAUTH_TYPE_FACEBOOK: {

                            if ($helper->getUserIdByFacebook($oauth_id) == 0) {

                                $account->setFacebookId($oauth_id);
                            }

                            break;
                        }

                        case OAUTH_TYPE_GOOGLE: {

                            if ($helper->getUserIdByGoogle($oauth_id) == 0) {

                                $account->setGoogleFirebaseId($oauth_id);
                            }

                            break;
                        }

                        default: {

                            break;
                        }
                    }
                }

                //

                $result['account'] = array();

                array_push($result['account'], $account->get());
            }
        }
    }

    echo json_encode($result);
    exit;
}
