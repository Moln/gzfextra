<?php

namespace Gzfextra\Form;

use Zend\Filter\FilterInterface;
use Zend\Filter\FilterPluginManager;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterAwareTrait;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;


/**
 * Class Form
 *
 * @package Gzfextra\Form
 * @author  Xiemaomao
 * @version $Id$
 */
class Form implements InputFilterAwareInterface
{
    use InputFilterAwareTrait;

    private $isValid;

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new InputFilter();
        }
        return $this->inputFilter;
    }

    public function setData($data)
    {
        return $this->getInputFilter()->setData($data);
    }

    public function setInputs($inputs)
    {
        foreach ($inputs as $input) {
            $this->getInputFilter()->add($input);
        }
    }

    public function isValid()
    {
        if ($this->isValid !== null) {
            return $this->isValid;
        }

        return $this->isValid = $this->getInputFilter()->isValid();

        /*
        $this->inputs;
        $isValid = true;
        $vm = $this->getValidatorManager();
        $fm = $this->getFilterManager();
        foreach ($this->inputs as $input) {
            $name  = $input['name'];
            $value = isset($this->data[$name]) ? $this->data[$name] : null;

            if (isset($input['filters'])) {
                $filters = $input['filters'];

                foreach ($filters as $filter) {
                    if (is_array($filter)) {
                        $filter = $fm->get(
                            $filter['name'],
                            isset($filter['options']) ? $filter['options'] : []
                        );
                    }
                    if (!$filter instanceof FilterInterface) {
                        throw new \RuntimeException('Error validator: ' . $name);
                    }

                    $value = $filter->filter($value);
                }
                $this->data[$name] = $value;
            }

            if (!empty($input['required'])) {
                $input['validators'][] = ['name' => 'NotEmpty'];
            }

            if (isset($input['validators'])) {
                $validators = $input['validators'];

                foreach ($validators as $key => $options) {
                    if (is_array($options) && strtolower($options['name']) == 'notempty') {
                        unset($validators[$key]);
                        $validator = $vm->get($options['name']);
                        if (!$validator->isValid($value, $this->data)) {
                            $isValid = false;
                            $this->messages[$name] = $validator->getMessages();

                            if (isset($options['continue_if_empty']) && !$options['continue_if_empty']) {
                                continue 2;
                            }
                        }
                    }
                }

                foreach ($validators as $options) {
                    if (is_array($options)) {
                        $validator = $vm->get($options['name'], isset($options['options']) ? $options['options'] : []);
                    } else if ($options instanceof ValidatorInterface) {
                        $validator = $options;
                    } else {
                        throw new \RuntimeException('Error validator: ' . $name);
                    }

                    if (!$validator->isValid($value, $this->data)) {
                        $isValid = false;
                        $this->messages[$name] = $validator->getMessages();
                    }

                    if (!$isValid && is_array($options) && isset($options['break_on_failure']) && $options['break_on_failure']) {
                        break;
                    }
                }
            }
        }

        return $isValid;
        */
    }

    public function getData()
    {
        return $this->getInputFilter()->getValues();
    }

    public function getMessages()
    {
        return $this->getInputFilter()->getMessages();
    }

    /**
     * protected static function getValidatorManager()
     * {
     * if (!self::$validatorManager) {
     * self::$validatorManager = new ValidatorPluginManager;
     * }
     *
     * return self::$validatorManager;
     * }
     *
     * protected static function getFilterManager()
     * {
     * if (!self::$filterManager) {
     * self::$filterManager = new FilterPluginManager;
     * }
     *
     * return self::$filterManager;
     * }
     * */
}