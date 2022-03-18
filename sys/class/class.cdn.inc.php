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

require_once '../sys/addons/aws/autoload.php';

use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class cdn extends db_connect
{
    private $ftp_url = "";
    private $ftp_server = "";
    private $ftp_user_name = "";
    private $ftp_user_pass = "";
    private $cdn_server = "";
    private $conn_id = false;

    public function __construct($dbo)
    {
        if (strlen($this->ftp_server) > 0) {

            $this->conn_id = @ftp_connect($this->ftp_server);
        }

        parent::__construct($dbo);
    }

    public function upload($file, $remote_file)
    {
        $remote_file = $this->cdn_server.$remote_file;

        if ($this->conn_id) {

            if (@ftp_login($this->conn_id, $this->ftp_user_name, $this->ftp_user_pass)) {

                // upload a file
                if (@ftp_put($this->conn_id, $remote_file, $file, FTP_BINARY)) {

                    return true;

                } else {

                    return false;
                }
            }
        }
    }

    public function uploadGalleryImage($imgFilename)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "",
            "fileUrl" => ""
        );

        $settings = new settings($this->db);
        $config = $settings->get();
        unset($settings);

        if ($config['S3_AMAZON']['intValue'] == 1) {

            $result = $this->s3_upload($config, "gallery.".$_SERVER['SERVER_NAME'], $imgFilename);
        }

        if ($result['error'] || strlen($result['fileUrl']) == 0) {

            rename($imgFilename, GALLERY_PATH.basename($imgFilename));

            $result['error'] = false;
            $result['fileUrl'] = APP_URL."/".GALLERY_PATH.basename($imgFilename);
            $result['error_description'] = "Default upload";
        }

        @unlink($imgFilename);

        return $result;
    }

    public function uploadPhoto($imgFilename)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "",
            "fileUrl" => ""
        );

        $settings = new settings($this->db);
        $config = $settings->get();
        unset($settings);

        if ($config['S3_AMAZON']['intValue'] == 1) {

            $result = $this->s3_upload($config, "photo.".$_SERVER['SERVER_NAME'], $imgFilename);
        }

        if ($result['error'] || strlen($result['fileUrl']) == 0) {

            rename($imgFilename, PHOTO_PATH.basename($imgFilename));

            $result['error'] = false;
            $result['fileUrl'] = APP_URL."/".PHOTO_PATH.basename($imgFilename);
            $result['error_description'] = "Default upload";
        }

        return $result;
    }

    public function uploadCover($imgFilename)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "",
            "fileUrl" => ""
        );

        $settings = new settings($this->db);
        $config = $settings->get();
        unset($settings);

        if ($config['S3_AMAZON']['intValue'] == 1) {

            $result = $this->s3_upload($config, "cover.".$_SERVER['SERVER_NAME'], $imgFilename);
        }

        if ($result['error'] || strlen($result['fileUrl']) == 0) {

            rename($imgFilename, COVER_PATH.basename($imgFilename));

            $result['error'] = false;
            $result['fileUrl'] = APP_URL."/".COVER_PATH.basename($imgFilename);
            $result['error_description'] = "Default upload";
        }

        return $result;
    }

    public function uploadPostImg($imgFilename)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "",
            "fileUrl" => ""
        );

        $settings = new settings($this->db);
        $config = $settings->get();
        unset($settings);

        if ($config['S3_AMAZON']['intValue'] == 1) {

            $result = $this->s3_upload($config, "post.".$_SERVER['SERVER_NAME'], $imgFilename);
        }

        if ($result['error'] || strlen($result['fileUrl']) == 0) {

            rename($imgFilename, POST_PHOTO_PATH.basename($imgFilename));

            $result['error'] = false;
            $result['fileUrl'] = APP_URL."/".POST_PHOTO_PATH.basename($imgFilename);
            $result['error_description'] = "Default upload";
        }

        @unlink($imgFilename);

        return $result;
    }

    public function uploadChatImg($imgFilename)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "",
            "fileUrl" => ""
        );

        $settings = new settings($this->db);
        $config = $settings->get();
        unset($settings);

        if ($config['S3_AMAZON']['intValue'] == 1) {

            $result = $this->s3_upload($config, "chat.".$_SERVER['SERVER_NAME'], $imgFilename);
        }

        if ($result['error'] || strlen($result['fileUrl']) == 0) {

            rename($imgFilename, CHAT_IMAGE_PATH.basename($imgFilename));

            $result['error'] = false;
            $result['fileUrl'] = APP_URL."/".CHAT_IMAGE_PATH.basename($imgFilename);
            $result['error_description'] = "Default upload";
        }

        return $result;
    }

    public function uploadVideoImg($imgFilename)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "",
            "fileUrl" => ""
        );

        $settings = new settings($this->db);
        $config = $settings->get();
        unset($settings);

        if ($config['S3_AMAZON']['intValue'] == 1) {

            $result = $this->s3_upload($config, "thumbnail.".$_SERVER['SERVER_NAME'], $imgFilename);
        }

        if ($result['error'] || strlen($result['fileUrl']) == 0) {

            rename($imgFilename, VIDEO_IMAGE_PATH.basename($imgFilename));

            $result['error'] = false;
            $result['fileUrl'] = APP_URL."/".VIDEO_IMAGE_PATH.basename($imgFilename);
            $result['error_description'] = "Default upload";
        }

        @unlink($imgFilename);

        return $result;
    }

    public function uploadVideo($imgFilename)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "",
            "fileUrl" => ""
        );

        $settings = new settings($this->db);
        $config = $settings->get();
        unset($settings);

        if ($config['S3_AMAZON']['intValue'] == 1) {

            $result = $this->s3_upload($config, "video.".$_SERVER['SERVER_NAME'], $imgFilename);
        }

        if ($result['error'] || strlen($result['fileUrl']) == 0) {

            rename($imgFilename, VIDEO_PATH.basename($imgFilename));

            $result['error'] = false;
            $result['fileUrl'] = APP_URL."/".VIDEO_PATH.basename($imgFilename);
            $result['error_description'] = "Default upload";
        }

        @unlink($imgFilename);

        return $result;
    }

    public function uploadMarketImg($imgFilename)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "",
            "fileUrl" => ""
        );

        $settings = new settings($this->db);
        $config = $settings->get();
        unset($settings);

        if ($config['S3_AMAZON']['intValue'] == 1) {

            $result = $this->s3_upload($config, "market.".$_SERVER['SERVER_NAME'], $imgFilename);
        }

        if ($result['error'] || strlen($result['fileUrl']) == 0) {

            rename($imgFilename, "market/".basename($imgFilename));

            $result['error'] = false;
            $result['fileUrl'] = APP_URL."/market/".basename($imgFilename);
            $result['error_description'] = "Default upload";
        }

        return $result;
    }

    private function s3_upload($settings, $bucket, $filename): array
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_SUCCESS,
            "error_description" => "s3 upload",
            "fileUrl" => ""
        );

        try {

            $s3 = new S3Client([

                'region' => $settings['S3_REGION']['textValue'],
                'version' => 'latest',
                'credentials' => [
                    'key'    => $settings['S3_KEY']['textValue'],
                    'secret' => $settings['S3_SECRET']['textValue']
                ]
            ]);

            try {

                // Test if bucket exists

                $response = $s3->headBucket(array('Bucket' => $bucket));

            } catch (Aws\S3\Exception\S3Exception $e) {

                // Create bucket

                try {

                    $response = $s3->createBucket(['Bucket' => $bucket]);

                    $result['error_description'] = 'The bucket\'s location is: ' . $response['Location'] . '. ' . 'The bucket\'s effective URI is: ' . $response['@metadata']['effectiveUri'];

                } catch (AwsException $e) {

                    $result['error_description'] = 'Error: ' . $e->getAwsErrorMessage();
                }
            }

            // Upload

            try {

                $response = $s3->putObject([

                    'Bucket' => $bucket,
                    'Key'    => basename($filename),
                    'Body'   => fopen($filename, 'r'),
                    'ACL'    => 'public-read'
                ]);

                $result['error'] = false;
                $result['error_description'] = "s3 upload";
                $result['fileUrl'] = $response['@metadata']['effectiveUri'];

            } catch (Aws\S3\Exception\S3Exception $e) {

                $result['error_description'] = "There was an error uploading the file.";
            }

        } catch (S3Exception $e) {

            $result['error_description'] = "unable connect to aws";
        }

        return $result;
    }
}
