<?php
namespace BaseComponents\base\exception; 

class FmException extends \BaseComponents\base\Exception{
    public function getName(){
        return 'FM';
    }
}