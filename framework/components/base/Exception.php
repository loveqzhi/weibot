<?php
namespace BaseComponents\base;

class Exception extends \yii\base\Exception
{

    public function __construct($errorCode = 0, $message = null, \Exception $previous = null)
    {
        $code = $errorCode;
        if (is_array($errorCode)){
            $code = $errorCode[0];
            if ($message == null) {
                $message = $errorCode[1];
            }
        }
        parent::__construct($message, $code, $previous);
    }

    public function getName()
    {
        return 'app error';
    }
}
