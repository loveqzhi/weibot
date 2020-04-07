<?php
namespace BaseComponents\base\exception; 

class ApiErrorException extends \BaseComponents\base\Exception{
    public function getName(){
        return 'ApiError';
    }
}