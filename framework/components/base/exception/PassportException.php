<?php
namespace BaseComponents\base\exception; 

class PassportException extends \BaseComponents\base\Exception{
    public function getName(){
        return 'Passport';
    }
}