<?php
namespace BaseComponents\base;

use BaseComponents\customLog\CustomLogger;
use Yii;
use yii\base\InlineAction;
use yii\base\InvalidRouteException;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class BaseActiveController extends ActiveController
{

    private $sucessStatusCodes = [
        200,
        201,
        202,
        203,
        204,
        205,
        206,
    ]; //restful 成功状态码

    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (isset($_GET["callback"])) {
            Yii::$app->response->format = Response::FORMAT_JSONP;
        }
    }

    public function run($route, $params = [])
    {
        $pos = strpos($route, '/');
        if ($pos === false) {
            return $this->runAction($route, $params);
        } elseif ($pos > 0) {
            return $this->module->runAction($route, $params);
        } else {
            return Yii::$app->runAction(ltrim($route, '/'), $params);
        }
    }

    public function runAction($id, $params = [])
    {
        $bodyParams = Yii::$app->request->getBodyParams();
        $params = ArrayHelper::merge($params, $bodyParams);
        try {
            $action = $this->createAction($id);
            if ($action === null) {
                throw new InvalidRouteException('Unable to resolve the request: ' . $this->getUniqueId() . '/' . $id);
            }

            Yii::trace("Route to run: " . $action->getUniqueId(), __METHOD__);

            if (Yii::$app->requestedAction === null) {
                Yii::$app->requestedAction = $action;
            }

            $oldAction    = $this->action;
            $this->action = $action;

            $modules   = [];
            $runAction = true;

            foreach ($this->getModules() as $module) {
                if ($module->beforeAction($action)) {
                    array_unshift($modules, $module);
                } else {
                    $runAction = false;
                    break;
                }
            }
            $result = null;
            if ($runAction && $this->beforeAction($action)) {
                $result = $action->runWithParams($params);
                $result = $this->afterAction($action, $result);
                foreach ($modules as $module) {
                    $result = $module->afterAction($action, $result);
                }
            }
            $this->action = $oldAction;
            $statusCode   = Yii::$app->response->getStatusCode();
            if (!in_array($statusCode, $this->sucessStatusCodes)) {
                $errorMsg = [];
                foreach ($result as $msg) {
                    $errorMsg[] = "{$msg['message']}";
                }
                return $this->error(implode($errorMsg, '; '), $statusCode, $result);
            }
            return $this->success($result);
        } catch (\yii\web\HttpException $e) {
            $code = 400; // 解决 Yii HttpException code 默认为0的问题
            $this->renderJSON($this->error($e->getMessage(), $code));
        } catch (\Exception $e) {
            $code = $e->getCode();
            $this->renderJSON($this->error($e->getMessage(), $code));
        }
    }

    public function createAction($id)
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }

        $actionMap = $this->actions();
        if (isset($actionMap[$id])) {
            return Yii::createObject($actionMap[$id], [
                $id,
                $this,
            ]);
        } elseif (preg_match('/^[\w\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = 'api' . str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));
            if (method_exists($this, $methodName)) {
                $method = new \ReflectionMethod($this, $methodName);
                if ($method->isPublic() && $method->getName() === $methodName) {
                    return new InlineAction($id, $this, $methodName);
                }
            }
        }
        return null;
    }

    public function bindActionParams($action, $params)
    {
        if ($action instanceof InlineAction) {
            $method = new \ReflectionMethod($this, $action->actionMethod);
        } else {
            $method = new \ReflectionMethod($action, 'run');
        }

        $args         = [];
        $missing      = [];
        $actionParams = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = $actionParams[$name] = (array) $params[$name];
                } elseif (!is_array($params[$name])) {
                    $args[] = $actionParams[$name] = $params[$name];
                } else {
                    $args[] = $actionParams[$name] = (array) $params[$name];
                   /* throw new BadRequestHttpException(Yii::t('yii', 'Invalid data received for parameter "{param}".', [
                        'param' => $name,
                    ]));*/
                }
                unset($params[$name]);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $actionParams[$name] = $param->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new \Exception('缺少必要参数 : ' . implode(', ', $missing), 400);
        }
        set_error_handler(array(
            &$this,
            'apiErrorHandler',
        ));
        return $args;
    }

    public function apiErrorHandler($errorNumber, $errorString, $errorFile, $errorLine)
    {
        if (!(error_reporting() & $errorNumber)) {
            return;
        }
        $messages   = array();
        $messages[] = 'ERROR_NO:' . $errorNumber . PHP_EOL;
        $messages[] = 'ERROR_STR:' . $errorString . PHP_EOL;
        $messages[] = 'ERROR_LINE:' . $errorLine . PHP_EOL;
        $messages[] = 'ERROR_FILE:' . $errorFile . PHP_EOL;
        $messages[] = 'ENVIRONMENT:' . PHP_VERSION . ' (' . PHP_OS . ')';
        Yii::error(CustomLogger::formatMessage('系统500错误', '', $messages), "apiErrorHandler");
        if (YII_DEBUG) {
            throw new \Exception(implode(',', $messages), 500);
        }
        throw new \Exception('error', 500);
    }

    public function renderJSON($data)
    {
        $response = new Response();
        if (isset($_GET["callback"])) {
            $response->format = Response::FORMAT_JSONP;
        } else {
            $response->format = Response::FORMAT_JSON;
        }
        $response->data = $data;
        $response->send();
        Yii::$app->end();
    }

    protected function error($msg = '', $code = -1, $data = array())
    {
        $result       = new \stdClass();
        $result->code = $code;
        $result->data = $data;
        $result->msg  = $msg;
        return $result;
    }

    protected function success($data = array(), $msg = 'success')
    {
        $result       = new \stdClass();
        $result->code = 0;
        $result->data = $data;
        $result->msg  = $msg;
        return $result;
    }
}
