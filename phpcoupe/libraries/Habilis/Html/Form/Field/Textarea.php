<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 07/01/15
 * Time: 12:43
 */

namespace Habilis\Html\Form\Field;


class Textarea extends \Habilis\Html\Form\Field
{

    /**
     * Tag.
     * @access public
     * @var string
     */
    protected $_tag = "textarea";

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {

        parent::setText(array((string)$value));

    }

    /**
     * @param mixed $value
     */
    public function getValue()
    {

        return implode('', parent::getText());

    }

}