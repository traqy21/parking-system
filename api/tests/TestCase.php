<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    //for debugging
    public function debug($response, $flag = false) {
        $string = $response->baseResponse->getContent();
        if ($flag) {
            $string = preg_replace('/\s+/', ' ', trim($response->response->getContent()));
        }

        dd($string);
    }

    public function decode($response) {
        return json_decode($response->baseResponse->getContent());
    }
}
