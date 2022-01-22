<?php


namespace App\Controllers;

/**
 * Class Controller
 * Родительский класс всех контроллеров
 *
 * @package App\Controllers
 */
abstract class Controller
{
    /**
     * @param string $action
     * @param array $arguments
     */
    public function __call(string $action, array $arguments)
    {
        $action = 'action' . ucfirst($action);
        $array = get_class_methods($this);
        if (!in_array($action, $array)) {
            $action = 'actionIndex';
        }

        $this->$action(...$arguments);
    }
}
