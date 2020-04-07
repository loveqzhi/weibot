<?php
namespace BaseComponents\base\exception; 

class ApiStoreException extends \BaseComponents\base\Exception{
    public function getName(){
        return 'Apistore';
    }
}