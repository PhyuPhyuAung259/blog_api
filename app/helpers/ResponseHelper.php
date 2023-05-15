<?php

namespace App\helpers;
class ResponseHelper{
    public static function success($data=[], $message="success"){

        return response()->json(
            [
                "data"=>$data,
                "message"=>$message
            ],
            200
        );
    }
    public static function fail($message){
        return response()->jason(
            [
                "message"=>$message
            ],
            422
            );
    }
}
