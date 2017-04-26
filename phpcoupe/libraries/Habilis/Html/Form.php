<?php 

namespace Habilis\HTML;

class Form extends \Habilis\HTML
{

	/**
	* A tag sempre será form.
	* @access public
	* @var string
	*/
	protected $_tag = 'form';
	
	/**
	* Armazena os atributos do formulário.
	* Por padrão, o atributo method é POST.
	* @access public
	* @var array
	*/
	protected $_attr = array('method'=>'POST');

	/**
	* Endereço de submissão do formulário.
	* @access public
	* @var string
	*/
	public $action;

	/**
	* O formulário irá enviar um arquivo?
	* @access public
	* @var bool
	*/
	public $send_file = FALSE;
	
	/**
	* Adiciona dois pontos ao final
	* do label dos campos ('Nome' se torna 'Nome:').
	* @access public
	* @var bool
	*/
	public $twopoint_in_label = TRUE;
	
	/**
	* Colocar os campos e seus labels
	* englobados dentro de uma DIV?
	* (<div><label><input /></label></div>).
	* @access public
	* @var bool
	*/
	public $colapse_div = TRUE;
	
	/**
	* A tag label engloba a tag do campo
	* (<label><input /></label>).
	* @access public
	* @var bool
	*/
	public $colapse_label = FALSE;
	
	/**
	* Adiciona o tipo do campo como prefixo
	* do seu ID ('#name' se torna '#input_name').
	* @access public
	* @var bool
	*/
	public $type_in_id = TRUE;
	
	/**
	* Adiciona a tag '<br />' ao final do label
	* para quebrar a linha (<label></label><br /><input />).
	* @access public
	* @var bool
	*/
	public $break_label = TRUE;

	/**
	 * Armazena objetos que são do tipo
	 * \Habilis\HTML\Form\Field.
	 *
	 * @var array
	 */
	protected $_fieldlist = array();
	
	/**
	* Sobrescreve o método construtor impedindo
	* que se passe uma nova tag como parâmetro.
	* A tag do objeto \Habilis\Form deve ser
	* sempre 'form'.
	* OBS: Mais isso não impede que a tag seja
	* alterada simplesmente mudando o valor da
	* propriedade pública \Habilis\Form::$tag.
	* @access public
	*/
	public function __construct($action) // (\Habilis\Url $url=NULL)
	{

		$this->setAction($action);

	}

	public function setAction($a)
	{

		$this->action = (string)$a;

	}
	
	/**
	* Pega o HTML do formulário.
	* @access public
	* @return string Código HTML do formulário.
	*/
	public function __toString()
	{

		if( $this->action )
		{
			$this->_attr['action'] = $this->action;
		}
		
		if( $this->send_file )
		{
			$this->_attr['enctype'] = "multipart/form-data";
		}
		
		return parent::__toString();

	}

	public function group($legend=NULL)
	{

		$grp = $this->children("fieldset");

		if($legend)
		{
			$grp->children("legend")->setText($legend);
		}

		return $grp;

	}
	
	/**
	* Fábrica para criação campos (objetos
	* do tipo \Habilis\HTML\Form\Field).
	*
	* @access public
	* @param string $type [optional] Tipo do campo (input|select|textarea|password|file|submit|reset).
	* @param array $attr [optional] Atributos adicionados ao HTML do campo.
	* @param string $description [optional] Descrição para criação de labels.
	* @return object Objeto de campo criado.
	*/
	public function & field($id, $type=NULL, Array $attr=NULL, $description=NULL, \Habilis\HTML $view=NULL)
	{

		switch($type)
		{

			case "select" :

				$f = new \Habilis\HTML\Form\Field\Select($id);

				break;

			case "textarea" :

				$f = new \Habilis\HTML\Form\Field\Textarea($id);

				break;

			case "file" :

				$f = new \Habilis\HTML\Form\Field\File($id);

				break;

			case "text" :

				$f = new \Habilis\HTML\Form\Field\Text($id);

				break;

			case "phone" :

				$f = new \Habilis\HTML\Form\Field\Phone($id);

				break;

			default :

				$f = new \Habilis\HTML\Form\Field($id);

				$f->setTag($type);

				break;

		}

		
		if( $attr )
		{
			$f->setAttr($attr);
		}

		$f->description = $description;

		$f->twopoint_in_label = $this->twopoint_in_label;

		$f->colapse_div = $this->colapse_div;

		$f->colapse_label = $this->colapse_label;

		$f->type_in_id = $this->type_in_id;

		$f->break_label = $this->break_label;

		/* Adiciona na tag certa para view. */

		if ( ! $view ) $view = $this;

		$view->setText($f);

		$this->_fieldlist[ $f->getId() ] = $f;
		
		return $f;
		
	}

	/**
	 * Atalho para criação de campo do tipo 'textarea'.
	 *
	 * @access public
	 * @param string $name [optional] Nome do campo.
	 * @param mixed $value [optional] Texto do campo. Valor passado será convertido para o tipo string.
	 * @param string $descripction [optional] Descrição do campo. Será usado em label.
	 * @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	 * @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	 * @return object Objeto de campo criado.
	 * @see \Habilis\HTML::setValue
	 * @see \Habilis\HTML\Form::field
	 */
	public function & textarea($name=NULL, $value=NULL, $description=NULL, $id=NULL, Array $class=NULL)
	{

		$textarea = $this->field($id, 'textarea', array('id'=>$id, 'class'=>$class), $description);

		$textarea->setValue($value);

		return $textarea;

	}

	/**
	* Atalho para criação de campo do tipo input.
	*
	* @access public
	* @param string $name [optional] Nome do campo.
	* @param string $type [optional] Tipo com campo input.
	* @param mixed $value [optional] Valor do campo. Valor passado será convertido para o tipo string.
	* @param string $descripction [optional] Descrição do campo. Será usado em label.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @param array $attr [optional] Array associativo com demais atributos da tag HTML.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML\Form::field
	*/
	public function & input($name=NULL, $type='text', $value=NULL, $description=NULL, $id=NULL, Array $class=array(), Array $attr=array())
	{
		
		return $this->field($id, 'input', array_merge(array('value'=>$value, 'id'=>$id, 'class'=>$class, 'type'=>$type), $attr), $description);

	}

	/**
	 * Atalho para criação de campo do tipo 'text'.
	 *
	 * @access public
	 * @param string $name [optional] Nome do campo.
	 * @param mixed $value [optional] Texto do campo. Valor passado será convertido para o tipo string.
	 * @param string $descripction [optional] Descrição do campo. Será usado em label.
	 * @param int $maxlength [optional] Quantidade máxima de caracteres.
	 * @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	 * @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	 * @return object Objeto de campo criado.
	 * @see \Habilis\HTML\Form::input
	 */
	public function & text($id, $v=NULL, $desc=NULL, $maxlen=NULL, \Habilis\HTML $view=NULL)
	{

		return $this->field($id, 'text', array('value'=>$v, 'type'=>'text', 'maxlength'=>$maxlen), $desc, $view);

	}

	/**
	 * Atalho para criação de campo do tipo 'text'.
	 *
	 * @access public
	 * @param string $name [optional] Nome do campo.
	 * @param mixed $value [optional] Texto do campo. Valor passado será convertido para o tipo string.
	 * @param string $descripction [optional] Descrição do campo. Será usado em label.
	 * @param int $maxlength [optional] Quantidade máxima de caracteres.
	 * @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	 * @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	 * @return object Objeto de campo criado.
	 * @see \Habilis\HTML\Form::input
	 */
	public function & phone($id, $v=NULL, $desc=NULL, $maxlen=NULL, \Habilis\HTML $view=NULL)
	{

		return $this->field($id, 'phone', array('value'=>$v, 'type'=>'tel', 'maxlength'=>$maxlen), $desc, $view);

	}

	/**
	* Atalho para criação de campo do tipo 'hidden'.
	*
	* @access public
	* @param string $name Nome do campo.
	* @param mixed $value [optional] Texto do campo. Valor passado será convertido para o tipo string.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML\Form::input
	*/
	public function & hidden($name, $value=NULL, $id=NULL, Array $class=NULL)
	{

		return $this->input($name, $type='hidden', $value, NULL, $id, $class);

	}

	/**
	* Atalho para criação de campo do tipo 'password'.
	*
	* @access public
	* @param string $name [optional] Nome do campo.
	* @param string $descripction [optional] Descrição do campo. Será usado em label.
	* @param int $maxlength [optional] Quantidade máxima de caracteres.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML\Form::input
	*/
	public function & password($name=NULL, $description=NULL, $maxlenght=NULL, $id=NULL, Array $class=NULL)
	{

		return $this->input($name, $type='password', NULL, $description, $id, $class, array('maxlenght'=>$maxlenght));

	}

	/**
	* Atalho para criação de campo 'input' do tipo 'file'.
	*
	* @access public
	* @param string $name [optional] Nome do campo.
	* @param string $descripction [optional] Descrição do campo. Será usado em label.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML\Form::input
	*/
	public function & file($id, $v=NULL, $desc=NULL, \Habilis\HTML $view=NULL)
	{

		$this->send_file = TRUE;

		return $this->field($id, 'file', array('value'=>$v, 'type'=>'file'), $desc, $view);

	}

	/**
	 * Atalho para criação de campo 'input' do tipo 'submit'.
	 *
	 * @access public
	 * @param mixed $value [optional] Rótulo do botão de submissão. Valor passado será converido em string.
	 * @param string $id [optional] Nome do campo.
	 * @return object Objeto de campo criado.
	 * @see \Habilis\HTML\Form::input
	 */
	public function & submit($value=NULL, $id=NULL)
	{

		return $this->input($id, $type='submit', $value);

	}

	/**
	* Atalho para criação de campo 'input' do tipo 'reset'.
	*
	* @access public
	* @param mixed $value [optional] Rótulo do botão de submissão. Valor passado será convertido em string.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML\Form::input
	*/
	public function & reset($value=NULL)
	{

		return $this->input(NULL, $type='reset', $value);

	}

	public function & date($id=NULL, $desc=NULL, $v=NULL)
	{

		return $this->input($id, $type='date', $v, $desc);

	}

	public function & email($id=NULL, $desc=NULL, $v=NULL)
	{

		return $this->input($id, $type='email', $v, $desc);

	}


	/**
	* Atalho para criação de campo de seleção com o
	* parametro 'multiple' definido, permitindo a
	* seleção de um ou mais valores.
	*
	* @access public
	* @param string $name [optional] Nome do campo.
	* @param string $option [optional] Array associativo com as opções da seleção. Chave do array indica o valor da opção. Valor do array indica texto da opção.
	* @param string $descripction [optional] Descrição do campo. Será usado em label.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML\Form::select
	*/
	public function & multiselect($name=NULL, Array $option=NULL, $description=NULL, $id=NULL, Array $class=NULL)
	{

		return $this->select($name, $option, $description, $id, $class, TRUE);

	}

	/**
	* Atalho para criação de campo de seleção.
	*
	* @access public
	* @param string $name [optional] Nome do campo.
	* @param string $option [optional] Array associativo com as opções da seleção. Chave do array indica o valor da opção. Valor do array indica texto da opção.
	* @param string $descripction [optional] Descrição do campo. Será usado em label.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @param bool $multiple [optional] Campo de seleção permite selecionar mais de um valor? Falso é o padrão.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML::node
	* @see \Habilis\HTML::setAttr
	* @see \Habilis\HTML\Form::field
	*/
	public function & select($name=NULL, Array $option=array(), $description=NULL, $id=NULL, Array $class=NULL, $multiple=FALSE)
	{
		
		$select = $this->field($id, 'select', array('id'=>$id, 'class'=>$class), $description);

		foreach ($option as $value => $desc)
		{
			$select->setOption($value, $desc);
		}
		
		return $select;

	}

	/**
	 * Adiciona um conjunto de checkboxes.
	 *
	 * @param $id
	 * @param array $options
	 * @param array $checked
	 */
	public function checkboxGroup($id, Array $options=array(), Array $checked=NULL)
	{

		foreach($options as $value => $desc)
		{

			$checked = ( in_array($value, (array)$checked) ) ? TRUE : FALSE;

			$this->checkbox($id, $value, $desc, $checked);

		}

	}

	/**
	* Atalho para criação de campo de marcação tipo 'checkbox'.
	*
	* @access public
	* @param string $name [optional] Nome do campo.
	* @param mixed $value [optional] Valor do campo. Valor passado será convertido para o tipo string.
	* @param string $text [optional] Texto descritivo para a marcação.
	* @param bool $checked [optional] Adicionar a propriedade 'checked', deixando o campo marcado? Padrão é falso.
	* @param bool $disabled [optional] Adicionar a propriedade 'disabled', deixando o campo desabilitado? Padrão é falso.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML\Form::input
	*/
	public function & checkbox($name=NULL, $value=NULL, $text='', $checked=FALSE, $disabled=FALSE)
	{
		
		$input = & $this->input($name, 'checkbox', $value, NULL);
		
		$input->colapse_div = FALSE;

		if( $checked )
		{
			$input->setAttr('checked', 'checked');
		}
		
		if( $disabled )
		{
			$input->setAttr('disabled', 'disabled');
		}
		
		$this->setText($text);
		
		$this->children('br');
		
		return $input;

	}

	/**
	* Atalho para criação de campo de marcação tipo 'radio'.
	*
	* @access public
	* @param string $name [optional] Nome do campo.
	* @param mixed $value [optional] Valor do campo. Valor passado será convertido para o tipo string.
	* @param string $text [optional] Texto descritivo para a marcação.
	* @param bool $checked [optional] Adicionar a propriedade 'checked', deixando o campo marcado? Padrão é falso.
	* @param bool $disabled [optional] Adicionar a propriedade 'disabled', deixando o campo desabilitado? Padrão é falso.
	* @param string $id [optional] Identificado do campo. Atributo 'id' da tag.
	* @param array $class [optional] Lista de classes. Adicionados ao atributo 'class' da tag.
	* @return object Objeto de campo criado.
	* @see \Habilis\HTML\Form::input
	*/
	public function & radio($name=NULL, $value=NULL, $text='', $checked=FALSE, $disabled=FALSE, $id=NULL, Array $class=NULL)
	{

		$input = & $this->input($name, 'radio', $value, NULL, $id, $class);

		$input->colapse_div = FALSE;

		if( $checked )
		{
			$input->setAttr('checked', 'checked');
		}

		if( $disabled )
		{
			$input->setAttr('disabled', 'disabled');
		}

		$this->setText($text);

		$this->children('br');

		return $input;
		
	}
	
	/**
	* Preenche os campos do formulário com 
	* valores do array associativo passado.
	* Nesse array associativo, a chave representa
	* a propriedade 'name' do objeto de campo. Já o valor,
	* será atribuido ao objeto.
	*
	* @access public
	* @param array $data Array associativo indicado campos e valores.
	* @param object $html [optional] Recebe objeto do tipo HTML. Usado apenas para recursividade.
	* @return bool Verdairo indicado a execução do método.
	* @see \Habilis\HTML::setValue
	*/
	public function fill(Array $data, \Habilis\HTML $html=NULL)
	{
		
		/*
		* Se não passou um objeto, pega
		* o próprio formulário.
		*/
		if( ! $html )
		{
			$html = $this;	
		}
		
		/*
		* Vai pegando os filhos do objeto passado
		*/
		foreach ( $html->_fieldlist as $children )
		{

			/*
			* Se o objeto for de qualquer tag (tirando
			* as que estao dentro do array) e se tiver
			* um filho, faz a recursão.
			*/
			if( is_object($children) && is_a($children,'\Habilis\HTML') )
			{
				if ( ! in_array($children->getTag(), array('input', 'select', 'textarea', 'option')) && ! empty($children->value) )
				{
					$this->fill($data, $children);
				}
				elseif( isset($data[ $children->getId() ]) )
				{
					$children->setValue($data[ $children->getId() ]);
				}
			}
		}

		return TRUE;

	}

	/**
	* Percorre os nós do formulário procurando por
	* objetos do tipo campo. Caso seja encontrado
	* um campo, pega o valor do array associativo que
	* tenha identificador igual ao atributo name do 
	* campo e passa esse valor para o método
	* \Habilis\HTML\Form\Field::hasError que verifica,
	* através de uma expressão regular definida no
	* objeto do campo se o valor do array é válido ou não.
	* Bom para validar valores sobmetidos para $_POST
	* ou $_GET. Caso haja algum erro, o 'name' do campo
	* é retornado permitindo tratamento. Caso não haja
	* retorna falso.
	*
	* @access public
	* @param array $data Array associativo onde chave indica a propriedade 'name' do campo.
	* @return mixed Nome do campo caso haja erro ou falso.
	* @see \Habilis\HTML::getAttr
	* @see \Habilis\HTML\Form\Field::hasError
	*/
	public function hasError(Array $data)
	{
		foreach ( $this->value as $node ) /* Vai pegando os filhos do objeto passado. */
		{
			if( is_object($node) && is_a($node, '\Habilis\HTML\Form\Field') )
			{
				if( $node->hasError(@ $data[ $node->getId() ]) )
				{
					return $node->getId();
				}
			}
		}
	}
	
}