<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 07/01/15
 * Time: 12:39
 */

namespace Habilis\Html\Form\Field;

class Phone extends \Habilis\Html\Form\Field
{

    /**
     * Tag.
     * @access public
     * @var string
     */
    protected $_tag = "input";

    /**
     * Atributos da tag armazenados
     * em forma de array associativo.
     * @access public
     * @var array
     */
    public $_attr = array('type'=>'phone');

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {

        $this->_attr['value'] = (string)$value;

    }

    /**
     * @param mixed $value
     */
    public function getValue()
    {

        return $this->getAttr('value');

    }

}