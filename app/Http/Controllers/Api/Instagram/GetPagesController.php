<?php

namespace App\Http\Controllers\Api\Instagram;

use Facebook\Exceptions\FacebookSDKException;
use App\Http\Controllers\Controller;

class GetPagesController extends Controller
{
    public function index()
    {
        try {
            $pagesEndPoint = config('instagram.endPoint') . 'me/accounts';
            $pagesParams = [
                'access_token' => config('instagram.accessToken')
            ];

            $pagesEndPoint .= '?' . http_build_query($pagesParams);

            //CURL Initialization
            $cu = curl_init($pagesEndPoint);
            curl_setopt($cu, CURLOPT_URL, $pagesEndPoint);
            curl_setopt($cu, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);

            //CURL CALL
            $response = curl_exec($cu);
            curl_close($cu);

            $responseArr = json_decode($response, true);
        } catch (FacebookSDKException $e) {
            dd($$e->message);
        }

        return view('get-pages', ['responseArr' => $responseArr['data'][0]]);
    }
}
