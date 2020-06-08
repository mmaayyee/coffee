<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/26
 * Time: 下午7:40
 */
namespace common\helpers;

class CurlRequest
{
    public static function CallApi($method, $url, $data, $perpage = 10, $page = 0)
    {
        //$url = "http://localhost:82/slimdemo/RESTAPI/" . $api;
        $curl = curl_init($url.'?per-page='.$perpage.'&page='.$page);

        /*$headers = array();
        //$headers[] = 'Accept: application/xml';
        $headers[] = 'Content-Type: x-www-form-urlencoded';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);*/

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        switch ($method) {
            case "GET":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        $response = curl_exec($curl);
        $data = json_decode($response,true);
        return $data;
        /* Check for 404 (file not found). */
        /*$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // Check the HTTP Status code
        switch ($httpCode) {
            case 200:
                $error_status = "200: Success";
                return ($data);
                break;
            case 404:
                $error_status = "404: API Not found";
                break;
            case 500:
                $error_status = "500: servers replied with an error.";
                break;
            case 502:
                $error_status = "502: servers may be down or being upgraded. Hopefully they'll be OK soon!";
                break;
            case 503:
                $error_status = "503: service unavailable. Hopefully they'll be OK soon!";
                break;
            default:
                $error_status = "Undocumented error: " . $httpCode . " : " . curl_error($curl);
                break;
        }*/
        curl_close($curl);
        //echo $error_status;
        //die;
    }
}