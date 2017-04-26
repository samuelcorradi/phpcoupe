<?php

namespace Habilis\HTML\Form;

class Field extends \Habilis\HTML
{

	protected $_id;
	
	/**
	* Adiciona dois pontos ao final
	* do label ('Nome' se torna 'Nome:').
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
	* Adiciona o tipo do campo como
	* prefixo do seu ID
	* ('#id' se torna '#input_id').
	* @access public
	* @var bool
	*/
	public $type_in_id = TRUE;
	
	/**
	* Adiciona a tag '<br />' ao
	* final do label para quebrar
	* a linha (<label></label><br /><input />).
	* @access public
	* @var bool
	*/
	public $break_label = TRUE;
	
	/**
	* Descrição do campo. Usado como label
	* quando o campo for impresso em HTML.
	* @access public
	* @var string
	*/
	public $description;
	
	/**
	* Recebe um regex que servirá
	* para validar os valores postados
	* pelo formulário.
	* @access public
	* @var string
	*/
	public $validate = NULL;
	
	/**
	* O campo permite valores nulos?
	* Se definido como verdadeiro, sempre que
	* for verificar por erros, e o valor for vazio,
	* indica erro.
	* @access public
	* @var bool
	*/
	public $notnull = FALSE;

	/**
	 * Construtor recebe o valor do campo.
	 *
	 * @param string $v
	 */
	public function __construct($id, $v=NULL)
	{

		$this->_id = $id;

		$this->setValue($v);

	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function setId($id)
	{

		$this->_id = $id;

		return $this;

		// return $this->setAttr("name", $id);

	}

	/**
	 * @return mixed
	 * @throws \Habilis\InvalidArgumentException
	 */
	public function getId()
	{

		return $this->_id;

	}
	
	/**
	* Gera o HTML do campo.
	* @access public
	* @return string Código HTML do campo.
	*/
	public function __toString()
	{

		$this->setAttr("name", $this->_id);

		if ( $this->type_in_id && isset($this->_attr['id']) )
		{
			if ( ! preg_match('/^' . $this->_tag . '\_.*/', $this->_attr['id']) )
			{
				$this->_attr['id'] = $this->_tag . '_' . $this->_attr['id'];
			}
		}
		
		$html = parent::__toString();
		
		if ( $this->description )
		{

			$label = new \Habilis\HTML('label');
			
			$label->setText($this->description);
			
			if ( $this->twopoint_in_label )
			{
				$label->setText(':');
			}
		
			if ( $this->break_label )
			{
				$label->children('br');
			}
			
			/*
			* Se tiver a variavel error setada,
			* coloca o label com a classe o
			* que permite alterar a aparencia.
			*/
			if( $this->hasError() ) 
			{
				$label->setAttr('class', array('error'));	
			}
			
			if( $this->colapse_label )
			{
				
				$label->setText($html);
				
				$html = $label->__toString();
				
			}
			else
			{
				
				$newhtml = new \Habilis\HTML();
				
				$newhtml->setText(array($label, $html));
				
				$html = $newhtml->__toString();
				
			}
			
		}
		
		if ( $this->colapse_div )
		{
			
			$div = new \Habilis\HTML('div');
			
			$div->setText($html);
			
			return $div->__toString();
			
		}
		
		return $html;
		
	}
	
	/**
	* Preenche o campo em tempo de execução com o valor
	* passado. Caso o campo seja do tipo Radio, Checkbox
	* ou Select, o valor é preenchido simplesmente marcado
	* o campo como selecionado (ao invés de atribuir o
	* valor as propriedades do campo). Geralmente essa
	* função é usada para preencher o campo com os valores
	* recebidos de alguma fonte de dados (POST, banco de dados, etc.)
	* @param $value mixed Valor a ser adicionado ao campo em tempo de execução. Selects, podem receber um array.
	*/
	public function setValue($value)
	{

		if( $this->_tag=='input' )
		{
			
			if( in_array($this->getAttr('type'), array('radio', 'checkbox')) )
			{
				if( $this->getAttr('value')==(string)$value )
				{
					$this->setAttr('checked', 'checked');
				}
			}
			else
			{
				
				$this->_attr['value'] = (string)$value;
			
			}
			
		}
		elseif( $this->_tag=='select' )
		{
		
			if( ! is_array($value) )
			{
				
				$value = array((string)$value);
			
			}
			
			foreach($this->value as $val)
			{
				
				/*
				* Se o nó for um objeto do tipo \Habilis\HTML
				* tiver o atributo 'value', e ele estiver
				* no meio do array de valores coloca como
				* selecionado.
				*/
				if( is_object($val) && is_a($val, '\Habilis\HTML') )
				{
					
					if( in_array($val->getAttr('value'), $value) )
					{
						
						$val->setAttr('selected', 'selected');

						/*
						* Se não for multiplo, termina as
						* atribuições de valores.
						*/
						if( ! $this->getAttr('multiple') )
						{
							break;
						}
						
					}
					/*
					* Senão tive na lista de valores, remove
					* o atributo selected caso ele esteja
					* definido.
					*/
					else
					{
						
						unset($val->_attr['selected']);
					
					}

				}
				
			}
			
		}
		elseif( $this->_tag=='textarea' )
		{
			
			parent::setText(array((string)$value));
		
		}
		
		return $this;
		
	}
	
	/**
	* Pega o valor do campo.
	*/
	public function getValue()
	{
		
		if( $this->_tag=='input' )
		{

			return $this->getAttr('value');

		}
		elseif( $this->_tag=='select' )
		{
			
			$values = array();
			
			foreach($this->value as $val)
			{

				if( is_object($val) && is_a($val, 'Habilis\HTML') && $val->getAttr('selected') && ! is_null($val->getAttr('value')) )
				{

					$values[] = $val->getAttr('value');
					
					if( ! $this->getAttr('multiple') )
					{
						break;
					}
					
				}
				
			}
			
			if( ! empty($values) )
			{
				return $values;
			}
			
		}
		elseif( $this->_tag=='textarea' )
		{

			return implode('', parent::getText());
		
		}
		
	}
	
	/**
	* Verifica se o campo possui um erro usando o regex.
	*/
	public function hasError($value=NULL)
	{
		
		/*
		* Se um valor não foi informado mas tem
		* um valor padrão definido, usa-o.
		*/
		if( empty($value) )
		{
			$value = $this->getValue();
		}
		
		/**
		* Se o valor for vazio e notnull estiver como
		* verdadeiro, há um erro por não aceita ficar
		* sem valor.
		*/
		if( $this->notnull && empty($value) )
		{
			return TRUE;
		}

		if( ! $this->validate )
		{
			return FALSE;
		}

		if( ! is_array($value) )
		{
			$value = array($value);
		}

		foreach($value as $each)
		{
			if ( ! preg_match($this->validate, (string)$value) )
			{
				return TRUE;
			}
		}

	}

}
