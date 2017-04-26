<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 08/01/15
 * Time: 01:21
 */

namespace Habilis\Html\Form\Field;


class Select extends \Habilis\Html\Form\Field
{

    /**
     * Tag.
     * @access public
     * @var string
     */
    protected $_tag = "select";

    /**
     * Informa se é possível selecionar
     * mais de uma única opção.
     *
     * @var bool
     */
    public $multi = FALSE;

    /**
     * Lista das opções possíveis.
     * Lista deve ser um array associativo
     * com a chave da opção e um valor que
     * será usado como a descrição da chave.
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Lista das opções selecionadas.
     * Array armazena a lista de chaves
     * das seleções.
     *
     * @var array
     */
    protected $_selected = array();

    /**
     * Define o conjunto de opções possíveis
     * para o select.
     *
     * @param $v
     * @param $desc
     * @return $this
     */
    public function setOption($v, $desc)
    {

        $this->_options[ $v ] = $desc;

        return $this;

    }

    /**
     * Retorna o array de opções definidos
     * para o select.
     *
     * @return array
     */
    public function getOption()
    {

        return $this->_options;

    }

    /**
     * Remove todas opções do select.
     *
     * @param $v
     * @param $desc
     * @return $this
     */
    public function clearOption()
    {

        $this->_options = array();

        return $this;

    }

    public function clearValue()
    {

        $this->_selected = array();

        return $this;

    }

    public function setValue($v)
    {

        $this->_selected[] = $v;

        return $this;

    }

    public function getValue()
    {

        return $this->_selected;

    }

    public function __toString()
    {

        if( $this->multi )
        {
            $this->setAttr('multiple', 'multiple');
        }

        foreach($this->_options as $v => $desc)
        {

            $opt = $this->children('option', array('value'=>$v));

            $opt->setText($desc);

            if( in_array($v, $this->_selected) )
            {
                $opt->setAttr('selected', 'selected');
            }

            /*
            * Se não for multiplo, termina as
            * atribuições de valores.
            */
            if( ! $this->multi )
            {
                // break;
            }

        }

        return parent::__toString();

    }

}