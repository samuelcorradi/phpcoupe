<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 29/12/14
 * Time: 21:35
 */

namespace Coupe\View;

class Template extends \Coupe\View
{

    const DEFAULT_TEMPLATE = "default.php";

    /**
     * Armazena o caminho do arquivo que
     * será exibido ao usuário.
     * @access public
     * @var string Caminho do arquivo.
     */
    protected $template = self::DEFAULT_TEMPLATE;

    /**
     * Define o caminho para a template.
     *
     * @param $tpl
     * @return \Coupe\View\Template
     */
    public function setTemplate($tpl)
    {

        $this->template = $tpl;

        return $this;

    }

    /**
     * Define o caminho para a template.
     *
     * @return string
     */
    public function getTemplate()
    {

        return $this->template;

    }

    /**
     * Informa se a View realmente existe.
     * Se o arquivo existe retorna seu caminho,
     * caso contrário retorna FALSO.
     * @access public
     * @return bool Verdadeiro caso o arquivo exista.
     * @see is_file()
     */
    public function exists()
    {

        return is_file($this->template);

    }

    /**
     * Renderiza um arquivo substituindo
     * as variáveis pelos valores de
     * Coupe_View::$data;
     * @access public
     * @return string Conteúdo do buffer de saída.
     * @see ob_start()
     * @see ob_get_clean()
     */
    public function render()
    {

        if( ! is_file($this->template) )
        {
            return FALSE;
        }

        extract((array)$this->data, EXTR_PREFIX_SAME, "wddx");

        ob_start();

        require $this->template;

        return ob_get_clean();

    }

    /*
    * Informa o nível hierárquico de
    * uma View (se é pai, filho, neto, etc.).
    * Por exemplo, essa função é utilizada
    * pela 'title' para saber o tipo de tag
    * 'header' a ser usado.
    *
    public function level()
    {

        $count = substr_count($this->_viewpath, DIRECTORY_SEPARATOR) + 1;

        return $count;

    }

    /**
    * Pega o caminho para a View pai.
    *
    public function parent()
    {

        $level = $this->level();

        if( $level>1 )
        {

            $rpos = strrpos($this->_viewpath, DIRECTORY_SEPARATOR);

            $parent = substr($this->_viewpath, 0, $rpos);

            return $parent;

        }

    }
    */

}