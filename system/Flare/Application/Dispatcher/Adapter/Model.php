<?php

namespace Flare\Application\Dispatcher\Adapter;

use Flare\Application\Dispatcher\Adapter;
use Flare\Util\Collection;
use ReflectionClass;
use Flare\Security;
use Flare\Object;

/**
 * 
 * @author anthony
 * 
 */
class Model extends Adapter
{
    /**
     * 
     * @return boolean
     */
    public function dispatch()
    {
        $this->_controller->session->start();
        @list($class, $method) = explode('/', pack('H*', $this->_controller->uri->getSegment(2)), 2);
        if (!isset($class, $method)
            || $this->_controller->request->server('HTTP_'.self::FLARE_JS_HEADER) !== $this->_controller->session->get(self::FLARE_JS_HEADER))
        {
            show_response(404);
        }

        $method = trim(Security::removeInvisibleChars($method));
        $class = trim(Security::removeInvisibleChars($class));
        $module = $this->_controller->request->getModule();
        if (!file_exists($this->_controller->getAppDirectory()."modules/{$module}/models/{$class}.php")) {
            show_response(404);
        }

        $class = new ReflectionClass(ucwords($module)."\\Models\\".$class);
        $result = $class->getMethod($method)->invoke(null, $this->_controller->request->post('params', true));
        if ($result instanceof Collection) {
            $result = $result->toJSON();
        } elseif ($result instanceof Object) {
            $result = (string) $result;
        } elseif (is_array($result)) {
            $result = json_encode($result);
        }

        $this->_controller->response
            ->setContentType('application/json')
            ->setBody($result)
            ->send();
        
        return true;
    }
}