<?php

/**
* PHP Coupé
*
* Desenvolvimento PHP rápido e reaproveitável
*
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/2.5/br/ [(CC) by-nd]
* @link http://www.samuelcorradi.net/phpcoupe
*/

/**
* Classe \Coupe\Model
*
* A classe pai para os modelos de dados.
*
* @version 0.1
* @package Coupe
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/coupe/model
*/

namespace Coupe;

abstract class Model
{

    /**
	* Retorna uma única instância (Singleton)
	* da classe solicitada.
	*
	* @static
	* @return object Objeto da classe utilizada
	* @see self::getClass()
	*/
    public static function & getInstance()
	{

		static $inst = array();

		$class = self::getClass();

		if( ! isset($inst[ $class ]) ) 
		{
			$inst[ $class ] = new $class();
		}
	
		return $inst[ $class ];

	}
	
	/**
	* Antes do PHP 5.3, era mais complicado
	* pegar o nome da classe de forma estática.
	*
	* @access public
	* @return string Nome da classe atual.
	* @static
	* @see debug_backtrace
	* @see get_called_class
	* @see get_class
	*/
	public static function getClass()
	{
		
		if( ! function_exists('get_called_class') )
		{
			
			$traces = debug_backtrace();
			
			foreach ($traces as $trace)
			{
				if ( isset($trace['object']) )
				{
					if ( is_object($trace['object']) )
					{
						return get_class($trace['object']);
					}
				}
			}
			
		}
		else
		{
			return get_called_class();
		}
			
	}

	/**
	* Último id modificado ou inserido.
	* @access public
	* @var mixed
	*/
    public $id;

	/**
	* Dados ainda não persistidos
	* (dados em memória).
	* @access public
	* @var array Lista de registros.
	*/
	public $data;

	/**
	* Formato de data usado nas
	* inserções.
	* @access public
	* @var string Formato da data (ex.: d/M/Y).
	*/
	public $dateformat;

	/**
	* Filtro automático para tudo
	* que envolver esse modulo.
	* @access public
	* @var array Dicionário com opções de filtro.
	*/
	public $filter;

	/**
	* Lista dos campos que devem
	* ser resgatados pelas consultas.
	* @access public
	* @var array Lista com nome dos campos.
	*/
	public $fields;

	/**
	* Recursão usada em buscas
	* realizadas com o método getAll.
	* @access public
	* @var int Número de recursões.
	*/
	public $recursion = 0;

	/**
	* Armazena a estrutura de dados do modelo
	* (pode ser alimentada manualmente).
	*
	* Campo com o nome seguido de '_id' e tiver
	* o mesmo nome do stage é entendido como PK.
	* Campos com o nome seguido de '_id' e NÃO
	* tiverem o mesmo nome do stage é FK de
	* outro stage
	*
	* Cada campo recebe propriedades que
	* influenciaram na sua criação dependendo
	* do tipo de modelo.
	*
	* As propriedades de um campo do schema
	* podem ser:
	*
	* primary : Se esse campo é uma chave primária (teóricamente a união de 'unique' com 'notnull'). Recebe TRUE, FALSE, ou um array com o nome dos modelos para onde essa chave se propaga.
	* type : Tipo do campo (char, varchar, numeric, name, address). Existem alguns pseudo tipos que na verdade são a implementação de tipos reais de acordo com propósitos especificos (name, address, cpf, etc).
	* size : Tamanho do campo (1, 2, 35, etc.)
	* unique : Se o valor desse campo deve ser único (TRUE ou FALSE)
	* sequence : Se a insersão dos valores desse campo deve ser sequencial. Recebe como parametro o passo da sequencia (1, 2, etc).
	* notnull : Se esse campo pode ou não ser vazio (TRUE ou FALSE).
	* foreign : Se esse campo tem valores vindo de outro modelo de dados (TRUE ou FALSE). Internamente remove '_id' do nome do campo para descobrir qual é o outro modelo.
	* default : Definição do valor padrão desse campo caso não tenha sido setado um valor.
	* validate : Local para uma expressão regular no formato PHP que será usado para validar os valores setados para o campo.
	* description : Descrição para o campo (opcional).
	* zerofill : Em campos numéricos, preenche as casas a esquerda com 0 (AINDA NÃO IMPLEMENTADO).
	* positive : Em campos numéricos, grava apenas numeros positivos (AINDA NAO IMPLEMENTADO).
	*
	* Exemplo de um schema:
	*
	* $schema = array(
	* 'client_id' => array('type'=>'tipo', 'size'=>'tamanho', 'unique'=>TRUE, 'sequencial'=>1, 'notnull'=>TRUE, 'primary'=>TRUE, 'foreign'=>TRUE, 'default'=>'Valor padrão', 'zerofill'=>TRUE, 'positive'=>TRUE),
	* 'nome'      => array(),
	* 'table2_id' => array(),
	* 'table3_id' => array(),
	* 'cpf'       => array(),
	* );
	* @access public
	* @var array
	*/
	public $schema; 

	/**
	* Caso esteja definido indica que
	* a validação de campos deve ser
	* feita a cada salvamento ou
	* alteração de dados.
	* @access public
	* @var bool
	*/
	public $validate = FALSE;

	/**
	* Se quiser alterar o nome do
	* modelo. Caso não seja definido,
	* o nome usado é o mesmo nome da 
	* classe sem o sufixo 'Model'.
	* Ex.: Caso o nome da classe
	* seja ClienteModel a propriedade
	* $name será Client.  
	* @access public
	* @var string
	*/
	public $name;

	/**
	* Armazena o nome do local de
	* armazenamento. Usa o nome do
	* modelo em minúsculo para
	* definir o nome do stage.
	* Pode ser definido manualmente
	* mas isso não é recomendado.
	* @access public
	* @see \Coupe\Model::$name
	* @var string Nome do stage de armazenamento.
	*/
	public $stagename;

	/**
	* Todo modelo deve ter uma chave
	* primária. Essa propriedade armazena
	* o nome do campo que é a chave
	* primária do modelo. Usa o nome do
	* modelo acompanhado do sufixo '_id'
	* para definir o campo. Mas pode ser
	* definido manualmente (apesar) de
	* não ser recomendado.
	* @access public
	* @var string Nome do campo que é chave primária.
    */
    public $primary;

	/**
	* Lista de metadados que define tipos
	* de campos. Dessa forma, um campo do
	* modelo pode em sua propriedade 'type'
	* um tipo aqui criado e ele incorporará
	* as propriedades do tipo.
	* @access public
	* @var array Lista de pseudo tipos.
	*/
	protected $_patterns = array(
	'default'=>array('type'=>'int', 'size'=>5, 'notnull'=>TRUE, 'default'=>''),
	'name'=>array('type'=>'character', 'size'=>55),
	'created_on'=>array('type'=>'datetime'),
	'modify_on'=>array('type'=>'datetime'),
	'deleted_on'=>array('type'=>'datetime')
	);

	/**
	* Classe construtora protegida.
	*
	* @access protected
	* @see get_class()
	* @see preg_match()
	*/
	protected function __construct()
	{

		if ( preg_match("/(([^\\\^\_.]*)\_?([^\\\.]*))Model$/", get_class($this), $match) ) // if ( preg_match("/^([a-zA-Z]+_)?([a-zA-Z\_]+)Model$/", get_class($this), $match) && count($match)>0 )
		{
		
			if ( ! $this->name )
			{
				$this->name = $match[1];
			}
		
			if ( ! $this->stagename )
			{
				$this->stagename = strtolower($this->name); /* Nome do modelo é o prefixo + nome do modelo em minúsculo. */
			}
		
			if ( ! $this->primary )
			{
				$this->primary = strtolower(($match[3]) ? $match[3] : $match[2]) . '_id';
			}
		
			if ( empty($this->schema) )
			{
			
				$this->schema = $this->loadFromStage();
			
				if( ! $this->schema )
				{
					trigger_error('Unable to load a schema for the model: ' . $this->name, E_USER_ERROR);
				}
			
			}
		
		}
	
	}

	/**
	* O acesso privado ao método __clone()
	* previne que o objeto seja duplicado
	* preservando a característica de
	* Singleton da classe.
	*
	* @access private
	* @final
	*/
	private final function __clone() { trigger_error('Clone is not allowed.', E_USER_ERROR); }

	/**
	* Método mágico retorna valor
	* armazenado em \Coupe\Model::$data
	* se o nome da propriedade estiver
	* definida como coluna no esquema.
	*
	* @access public
	* @param string $key Nome da propriedade.
	* @see array_key_exists()
	* @see \Coupe\Model::$data
	* @see \Coupe\Model::$schema
	* @return mixed Valor da propriedade se estiver definida ou NULL.
	*/
	public function __get($key)
	{
	
        if( isset($this->schema[ $key ]) && array_key_exists($key, $this->data) )
        {
        	return $this->data[ $key ];
		}
	
		return NULL;
	
	}

	/**
	* Método mágico permite manipular
	* os dados do modelo como atributos
	* do objeto. Somente se a propriedade
	* tiver o mesmo nome de um campo
	* definido no schema que o dados
	* será armazenado em \Coupe\Model::$data.
	*
	* @access public
	* @param string $key Nome da propriedade ou coluna no modelo.
	* @param mixed $value [optional] Valor a ser atributo.
	* @see array_key_exists()
	* @see \Coupe\Model::$data
	* @see \Coupe\Model::$schema
	* @return bool Verdadeiro caso a propriedade tenha sido atribuida.
	*/
	public function __set($key, $value=NULL)
	{
	
		if ( array_key_exists($this->schema[ $key ]) ) /* Se esquetiver no esquema, adiciona em data. */
		{
			return $this->data[ $key ] = $value;
		}
	
		return $this->{$key} = $value;
	
	}

	/**
	* Método mágico que avalia o
	* método chamado.
	* Se o método não existir na classe,
	* e tiver 'findBy_' como prefixo,
	* pega o que vem depois para usar
	* com método \Coupe\Model::findBy().
	*
	* @access public
	* @param string $key Nome da propriedade ou coluna no modelo.
	* @param mixed $value Valor a ser atributo.
	* @see preg_match()
	* @see \Coupe\Model::findBy()
	* @return array Resultado da consulta em forma de array.
	*/
    public function __call($method, $params)
	{
		if( preg_match("~findBy_(.*)~", $method, $field) )
		{
			return $this->findBy($field[1], $params[0]);
		}
    }

	/**
	* Apaga \Coupe\Model::$data e
	* seta como NULL \Coupe\Model::$last_id
	* criando a possibilidade de criar
	* um novo registro no modelo.
	*
	* @access public
	* @return bool Verdadeiro indicado sucesso na operação.
	*/
	public function create()
	{
	
		$this->id = NULL;

        $this->data = NULL;

		return TRUE;
	
	}

	/**
	* Pega o primeiro registro da consulta
	* ordenada pelo campo indicado. Caso o
	* campo não seja indicado, será
	* retornado o primeiro registro ornedado
	* pelo campo que é chave primária.
	*
	* @access public
	* @param string $field [optional] Nome do campo que ordenará a consulta.
	* @param int $recursion Recursão utilizada na busca.
	* @see \Coupe\Model::getAll()
	* @return array Resultado da consulta em forma de array.
	*/
	public function first($field=NULL, $recursion=NULL)
	{
	
		if ( ! $field )
		{
			$field = $this->primary;
		}
	
		return $this->getAll(NULL, $order=array($field=>'asc'), $recursion, 1);

	}

	/**
	* Pega o último registro da consulta ordenada
	* por um campo. Caso o campo não seja indicado,
	* será retornado o último registro ornedado
	* pelo campo que é chave primária.
	*
	* @access public
	* @param string $field [optional] Nome do campo que ordenará a consulta.
	* @param int $recursion Recursão utilizada na busca.
	* @see \Coupe\Model::getAll()
	* @return array Resultado da consulta em forma de array.
	*/
	public function last($field=NULL, $recursion=NULL)
	{
	
		if ( ! $field )
		{
			$field = $this->primary;
		}
		
		return $this->getAll(NULL, $order=array($field=>'desc'), $recursion, 1);

	}

	/**
	* Remove todas os registros em que o campo
	* primário possui o valor igual ao informado.
	*
	* @access public
	* @param mixed $id Valor usado no filtro de remoção dos dados.
	* @see self::getInstance()
	* @see \Coupe\Model::$primary
	* @see \Coupe\Model::$schema
	* @see \Coupe\Model::deleteAll()
	* @return bool Verdadeiro casos os dados sejam removidos.
	*/
	public function delete($id)
	{
	
		if( is_array($this->schema[ $this->primary ]['primary']) )
		{
			foreach( $this->schema[ $this->primary ]['primary'] as $modelname )
			{
				$classname = "\\{$othermodel[1]}Model";
			
				$model = $classname::getInstance();
			
				$model->deleteAll(array($this->primary=>$id));
			}
		}
	
		return $this->deleteAll(array($this->primary=>$id));
	
	}

	// APARENTEMENTE OK
	/**
	* Salva os dados não persistidos
	* no stage de armazenamento.
	* Faz uso dos métodos
	* \Coupe\Model::update() e
	* \Coupe\Model::insert().
	* Os dados a serem salvos devem
	* ser passados por um array
	* associativo onde as associassões
	* serão verificadas com campos
	* definidos no schema do modelo.
	* Se o campo existir no esquema,
	* coloca esse valor em um array
	* final que será salvo.
	* Se determinada campo tiver
	* como valor um array, será
	* considerado que esse array
	* contém dados de
	* outro modelo para ser salvo.
	* O nome do modelo é encontrado
	* pelo nome do campo, menos o
	* sufixo '_id', então os dados
	* do array são primeiros salvos
	* no outro modelo, para então
	* sua chave ser salva no modelo
	* principal.
	*
	* Formato de dados salvos é:
	* array(
	* 'modulo_id'     => 1,
	* 'campo'         => 'String',
	* 'campo2'        => 7,
	* 'outromodel_id' => array(
	*    'outromodel_id' => 1,
	*    'campo'         => 'String'
	*    )
	* )
	*
	* @access public
	* @param array $data [optional] Dados a serem salvos.
	* @see \Coupe\Model::update()
	* @see \Coupe\Model::insert()
	* @return mixed Falso caso haja algum problema ao salvar os dados ou o valor da chave primária.
	*
	*/
	public function save(Array $data=NULL)
	{
	
		if ( empty($data) )
		{
			$data = $this->data; /* Se não foi informado os dados, pega os dados armazenado no modelo */
		}
	
		foreach ( (array)$data as $key => $value )
		{
		
			if( isset($this->schema[ $key ]) )
			{
			
				if ( preg_match("~(.*)_id~", $key, $othermodel) && is_array( $value ) && $this->schema[ $key ]['foreign'] )
				{
				
					$classname = "\\{$othermodel[1]}Model";

					$model = & $classname::getInstance();
				
					$id = $model->saveAll($value);
				
					$to_save[ $key ] = ( is_numeric($value[ $key ]) && $id ) ? $id : $value[ $key ];
			
				}
				else
				{
					$to_save[ $key ] = $value;
				}
			
			}
		
		}
	
		if( ! empty($to_save) ) /* Continua somente se há dados para serem salvos */
		{
		
			$to_save = $this->useDefault($to_save); /* Adiciona valores padrões para campos vazios */
		
			if ( $this->validate && $this->beforeValidate() ) /* Se a validação está ativa, então excuta o metodo validate() que carrega o helper Validate */
			{
			
				$error = $this->validate($to_save);
			
				if ( $error ) /* Se retornar um valor verdadeiro é por que deu erro */
				{
					die($error); /* Mata exibindo o nome do campo e o valor */
				}
			
			}
		
			if ( $this->beforeSave() ) /* Só salva se o callback retornar TRUE */
			{
			
				if ( isset($to_save[ $this->primary ]) && $to_save[ $this->primary ]!='' && $this->exists($to_save[ $this->primary ]) ) // if ( isset($to_save[$this->name.'_id']) && $this->exists($to_save[$this->name.'_id']) ) // se o id foi indica e o id jah existe no stage, dah update
				{
				
					$this->update($to_save, array($this->primary=>$to_save[ $this->primary ]));
				
					$last_id = $to_save[ $this->primary ];
			
				}
				else /* Se o id não existe no stage, dá insert */
				{
				
					$this->insert($to_save);
				
					$last_id = ( ! isset($to_save[ $this->primary ]) ) ? $this->Db->lastResult()->lastId() : $to_save[ $this->primary ];
		
				}
			
				if ( $last_id!==FALSE ) /* Se não deu erro ao salvar, executa o metodo para pós salvamento */
				{
					$this->afterSave();
				}
				else /* Se deu algum erro ao salvar (e conseguiu chegar até aqui) dá um erro fatal */
				{
					return FALSE; // trigger_error('Failed to sava data', E_USER_ERROR);
				}
			
				$this->read($last_id);
			
				$this->id = $last_id; // $this->_affected_rows = $this->Db->affected_rows(); // nao é possivel colocar a camada de banco de dados aqui
			
				return $last_id; /* Retorna o id da última operação de salvamento */
		
			}
		
		}
	
	}

	/**
	* Salva uma lista de dados. Basicamente
	* faz o mesmo que \Coupe\Model::save()
	* porém, ao invés de receber apenas um
	* registro de dados para ser salvo,
	* recebe vários armazenados em uma
	* lista. Depois vai percorrendo esse
	* array e salvando cada posição usando
	* \Coupe\Model::save().
	* Formato de dados salvos é:
	* array(
	* 	array(
	* 	'model_id' => 1,
	* 	'campo'    => 'String',
	* 	'campo2'   => 7
	* 	),
	* 	array(
	* 	'model_id' => 2,
	* 	'campo'    => 'String',
	* 	'campo2'   => 9
	* 	),
	* )
	*
	* @access public
	* @param array $data Dados a serem salvos.
	* @see \Coupe\Model::save()
	* @return mixed Falso caso haja algum problema ao salvar os dados ou o valor da chave primária.
	*/
    public function saveAll(Array $data)
    {

        if( isset($data[0]) && is_array($data[0]) )
        {
            foreach($data as $row)
            {
                $this->save($row);
            }
		}
        else
        {
            return $this->save($data);
        }

        return TRUE;

    }

	/**
	* Remove todas as ocorrências do
	* stage ou apenas as indicadas
	* por um filtro.
	*
	* @access public
	* @param array $filter [optional] Filtro para remoção dos dados.
	* @return bool Verdadeiro casos os dados sejam removidos.
	* @see \Coupe\Model::_deleteAll()
	* @see \Coupe\Model::afterDelete()
	* @see \Coupe\Model::beforeDelete()
	*/
	public function deleteAll(Array $filter=NULL)
	{

		if( $this->beforeDelete() && $this->_deleteAll($filter) )
		{

			$this->afterDelete();

			return TRUE;

		}

	}

	/**
	* Método para remover de um vetor
	* todas posições que não conferem
	* com o esquema.
	*
	* @access public
	* @param array $data Registro a ser sanetizado.
	* @return array Referencia do array passado no parâmetro.
	*/
	public function sanetizeData(Array & $data)
	{
	
		foreach ( $data as $key => $value )
		{
		
			if( ! array_key_exists($this->schema[ $key ]) )
			{
				unset($data[ $key ]);
			}
		
		}
	
		return $data;
	
	}

	/**
	* Função que recebe um array de dados
	* e, consultando o esquema do modelo,
	* o método descobre quais campos não
	* tem seus valores definidos no array a
	* ser salvo. As colunas que não foram
	* informadas assumem então o valor
	* padrão definido em suas metas
	* informações no esquema do modelo.
	*
	* @access public
	* @param array $data Registro a ser salvo.
	* @return array Registros com os valores padrões definidos nos campos auxentes.
	*/
	public function useDefault(Array $data)
	{	
	
		foreach( $this->schema as $key => $value )
		{

			if( array_key_exists($data[ $key ]) && $data[ $key ]==='' ) /* Se não foi setado o campo do schema em $data, ou estiver fazio... */ // if( ! isset($data[ $key ]) || (is_string($data[ $key ]) && $data[ $key ]=='') )
			{
				if ( isset($value['default']) && ! empty($value['default']) )
				{
					$data[ $key ] = $value['default'];
				}
				elseif ( isset($value['type']) && in_array($value['type'], array('datetime', 'date', 'time') ) ) // se nao tem um valor padrao, mas seu tipo é datetime, date, ou time, grava a data corrente usando o formato de data do modelo
				{
					$data[ $key ] = date($this->dateformat); /* Gera a data atual usando o formato padrão. */
				}
				else /* Se não tem nada, remove de uma vez... */
				{
					unset($data[ $key ]);
				}
			}
		}
	
		return $data;
	
	}

	/**
	* No esquema do modelo, os campos podem
	* ter a propriedade 'validate' definida
	* com um regex usado para validação
	* antes de salvar os dados.
	* Esse método, por tanto, recebe um
	* registro a ser salvo e percorre cada
	* coluna validando o valor definido de
	* acordo com sua propriedade 'validate'
	* (caso ela tenha sido configurada).
	*
	* @access public
	* @param array $data Registro a ser salvo.
	* @return array Registros com os valores padrões definidos nos campos auxentes.
	*/
	public function validate(Array $data)
	{
		foreach ($data as $key => $value)
		{
			if( isset($this->schema[$key]['validate']) && ! preg_match($this->schema[$key]['validate'], $value) )
			{
				return $key;
			}
		}
	}

	/**
	* Pega a lista de modelos e campos
	* para os quais a(s) chave(s)
	* primárias do modelo atual
	* propaga(m) evidenciando a relação
	* entre o modelo atual e outros
	* modelos que compõem a base de dados.
	*
	* @access public
	* @final
	* @see \Coupe\Model::$schema
	* @return array Lista modelos para onde a(s) chave(s) primária(s) do modelo atual propaga(m).
	*/
	final public function pkto()
	{
	
		$schema = array();
	
		foreach ($this->schema as $fieldname => $params)
		{
			if ( isset($params['primary']) && is_array($params['primary']) )
			{
				foreach($params['primary'] as $goesTo)
				{
					$schema[ ucfirst($goesTo) ][] = $fieldname;
				}
			}
		}
	
		return $schema;
	
	}

	/**
	* Pega a lista de modelos e campos
	* os quais recebem valores de
	* outro modelo como chave
	* estrangeira evidenciando a relação
	* de dependencia do modelo atual com
	* outros modelos que compõem a base
	* de dados.
	*
	* @access public
	* @final
	* @see \Coupe\Model::$schema
	* @return array Lista modelos que fazem vinculo com o modelo atual.
	*/
	final public function fkof()
	{
	
		$schema = array();
	
		foreach ($this->schema as $fieldname => $params)
		{
			if ( isset($params['foreign']) && is_string($params['foreign']) )
			{
				$schema[ ucfirst($params['foreign']) ][] = $fieldname;
			}
		}
	
		return $schema;
	
	}

	/**
	* Pega a lista de dependências e
	* propagação do modelo. Dessa
	* forma pode-se encontrar todos
	* os modelo (e os campos) que o
	* modelo atual tem relações.
	*
	* @access public
	* @final
	* @see \Coupe\Model::fkof()
	* @see \Coupe\Model::pkto()
	* @return array Lista de modelos que tem relação de alguma forma com o modelo atual.
	*/
	final public function relations()
	{

		return $this->pkto() + $this->fkof();

	}

	/**
	* Gera um mapa hierárquico da
	* relação entre modelos
	* baseando-se em um índice recursivo.
	*
	* @access public
	* @final
	* @param int $recursion [optional] Índice recursivo.
	* @return array Lista de modelos que tem relação com o modelo atual até atingir o nivel de recursividade indicado.
	* @see self::getInstance()
	* @see \Coupe\Model::$name
	* @see \Coupe\Model::$recursion
	* @see \Coupe\Model::hierarchy()
	* @see \Coupe\Model::relations()
	*/
	final public function hierarchy($recursion=-1)
	{

		if ($recursion<0)
		{
			$recursion = $this->recursion; /* Se a recursão não foi informada, pega a propriedade do modelo. */
		}
	
		$hierarchy[ $this->name ] = array();
	
		if ( $recursion>0 )
		{
		
			$recursion--;
		
			$relations = $this->relations();
		
			foreach($relations as $modelname=>$fields)
			{
			
				$classname = $modelname . 'Model';
			
				$model = $classname::getInstance();
			
				$sub = $model->hierarchy($recursion);
			
				$hierarchy[ $this->name ] = array_merge($hierarchy[ $this->name ], $sub);
			
			}
		
		}
	
		return $hierarchy;
	
	}

	/* NOVA TENTATIVA
	protected function _parse_filter(Array $filter)
	{
	
		if( ! isset($filter[ $this->name ]) )
		{
			$filter[ $this->name ] = array();
		}
	
		$relations = $this->relations();
	
		foreach( $relations as $modelname => $fields)
		{
		
			$fields = array_flip($fields);
		
			if( isset($filter[ $modelname ]) )
			{
			
				$intersect = array_intersect_key($filter[ $modelname ], $fields);
			
				if( $intersect )
				{
					$filter[ $this->name ] = array_merge($intersect, $filter[ $this->name ]);
				}
			
			}
		
		}
	
		return $filter;
	
	}
	*/

	/**
	* Faz consulta a base da dados.
	*
	* @param array $filter [optional] Filtro para a consulta.
	* @param array $order [optional] Ordem dos resultados da busca.
	* @param array $fields [optional] Campos que devem ser retornados.
	* @param int $pagesize [optional] Quantidade de dados retornados.
	* @return mixed Array se encontrar registros ou nulo.
	* @see \Coupe\Model::_get()
	*/
	public function get(Array $filter=NULL, Array $order=NULL, Array $fields=NULL, $pagesize=NULL)
	{

		if( $this->beforeFind() ) /* Se o callback retornar verdadeiro... */
		{
	
			$fields = array_merge((array)$fields, (array)$this->fields);
		
			$filter = array_merge((array)$filter, (array)$this->filter);

			$result = $this->_get($filter, $order, $fields, $pagesize);

			$this->afterFind();
		
			return $result;

		}

	}

	/**
	* Parseia um filtro de forma
	* simplificada transformando-o
	* em um array multidimensional.
	*
	* @final
	* @param array $filter [optional] Filtro simplificado para consulta.
	* @return mixed Filtro em forma multidimenssional.
	*/
	final public function parseFilter(Array $filter=NULL)
	{
	
		$parsed = array($this->name => array());
	
		foreach((array)$filter as $k => $v)
		{
			if( is_string($k) && preg_match('/^(.*?)\.?([^\..*]*)$/', $k, $matches) )
			{
			
				if( ! $matches[1] )
				{
					$matches[1] = $this->name;
				}
			
				$parsed[ $matches[1] ][ $matches[2] ] = $v;
			
			}
		}

		return $parsed;

	}

	/**
	* Transforma um filtro
	* multidimensional em um
	* array de forma simplificada.
	*
	* @final
	* @param array $filter [optional] Filtro multidimensional para consulta.
	* @return mixed Filtro em forma multidimenssional.
	*/
	final public function unparseFilter(Array $filter=NULL)
	{
	
		$unparsed = array();
	
		foreach((array)$filter as $model_name => $fields)
		{
			foreach($fields as $field_name => $value)
			{
				$unparsed[ $model_name . '.' . $field_name ] = $value;
			}
		}
	
		return $unparsed;
	
	}
	
	/*
	public function getAll_backup(Array $filter=NULL, Array $order=NULL, $recursion=-1, $pagesize=NULL)
	{
	
		if ($recursion<0)
		{
			$recursion = $this->recursion;
		}
	
		/*
		* Pega um array associativo onde a chave
		* e o nome de um modelo para o qual esse
		* tem vinculo, e os valores são os campos
		* usados nessa associacao
		*
		$relations = $this->relations();

		$parsed_filter = $this->parseFilter($filter);

		$result = $this->get($parsed_filter[ $this->name ], $order, NULL, $pagesize);
	
		$recursive_filter = array();
	
		$final = array(); /* Array que armazena o resultado final. *
	
		foreach((array)$result as $index => $row) /* Para cada resultado encontrado... *
		{
	
			$final[ $index ][ $this->name ][0] = $row; /* Coloca o resultado em sua posição final *
	
			if( $recursion>0 && $relations )
			{
		
				foreach($relations as $modelname => $used_fields)
				{

					$intersect = array_intersect_key($row, array_flip($used_fields));

					if( ! empty($intersect) )
					{
						$recursive_filter[ $modelname ][ $index ] = $intersect;
					}
			
				}
		
			}
	
		}
	
		if( ! empty($recursive_filter) )
		{
		
			$recursion--;
		
			foreach($recursive_filter as $modelname => $rows)
			{

				$classname = "\\{$modelname}Model";
			
				$model = $classname::getInstance();
				
				if( $model ) /* Coloquei para barrar a propagação se buscar um  modelo na hierarquia que não tenha classe declarada. *
				{
				
					foreach($rows as $index => $sub_filter)
					{

						$sub_final = array();	

						$sub_filter = array_merge($this->unparseFilter(array($modelname=>$sub_filter)), $this->unparseFilter($parsed_filter));

						$sub_result = $model->getAll_backup($sub_filter, NULL, $recursion, NULL);

						if( empty($sub_result) )
						{
							$final[ $index ][ $modelname ] = array();
						}
						else
						{
							
							foreach($sub_result as $sub_index => $sub_row)
							{
								foreach($sub_row as $sub_modelname=>$temp)
								{
									foreach($temp as $kk=>$vv)
									{
										$sub_final[ ucfirst($sub_modelname) ][] = $vv;
									}	
								}
							}
						
							$final[ $index ] = array_merge($sub_final, $final[ $index ]);
						
						}
					
					}
				
				}
			
			}
		
		}
	
		return $final;
	
	}
	*/

	/**
	* Busca resultados onde o valor
	* da chave primária for igual
	* ao valor passado.
	*
	* @access public
	* @param mixed $value Valor que será usado como filtro.
	* @return array Resultado da consulta em forma de array.
	*/
	public function find($value)
	{

		return $this->findBy($this->primary, $value);
	
	}

	/**
	* Busca registros onde o campo
	* indicado possui o valor
	* informado.
	*
	* @access public
	* @param string $field Campo que será usado no filtro.
	* @param mixed $value Valor que será usado no filtro.
	* @return array Resultado da consulta em forma de array ou nulo.
	*/
	public function findBy($field, $value)
	{

		if ( isset($this->schema[ $field ]) )
		{
			return $this->getAll(array($field=>$value));
		}
	
	}

	/**
	* Faz consulta a base da dados e retorna
	* o resultado incluindo as relações
	* com outros modelos usando um número de
	* recursões.
	*
	* @param array $filter [optional] Filtro para a consulta.
	* @param array $order [optional] Ordem dos resultados da busca.
	* @param array $fields [optional] Campos que devem ser retornados.
	* @param int $pagesize [optional] Quantidade de dados retornados.
	* @return mixed Array multidimensional ou nulo caso não haja registros.
	* @see self::getInstance()
	* @see \Coupe\Model::unparseFilter()
	*/
	public function getAll(Array $filter=NULL, Array $order=NULL, $recursion=-1, $pagesize=NULL)
	{
	
		if ($recursion<0)
		{
			$recursion = $this->recursion;
		}
	
		/*
		* Pega um array associativo onde a chave
		* e o nome de um modelo para o qual esse
		* tem vinculo, e os valores são os campos
		* usados nessa associacao
		*/
		$relations = $this->relations();

		$parsed_filter = $this->parseFilter($filter);

		$result = $this->get($parsed_filter[ $this->name ], $order, NULL, $pagesize);

		$recursive_filter = array();
	
		$final = array(); /* Array que armazena o resultado final. */
	
		foreach( (array)$result as $index => $row ) /* Para cada resultado encontrado... */
		{
	
			$final_temp= array($this->name=>array($row)); /* Coloca o resultado em sua posição final */
	
			if( $recursion>0 && $relations )
			{
		
				foreach($relations as $modelname => $used_fields)
				{
					
					$classname = "\\{$modelname}Model";

					$intersect = array_intersect_key($row, array_flip($used_fields));

					$model = $classname::getInstance();
					
					if( $model ) /* Coloquei para barrar a propagação se buscar um  modelo na hierarquia que não tenha classe declarada. */
					{
						
						$unparsed = $this->unparseFilter(array($modelname=>$intersect));
						
						// $passed_filter = $this->unparseFilter($parsed_filter);
						
						$sub_filter = array_merge($unparsed, (array)$filter); //array_merge($unparsed, $passed_filter);
						
						foreach((array)$filter as $k => $v) // foreach($passed_filter as $k => $v)
						{
							if(isset($unparsed[ $k ]))
							{
								$sub_filter[ $k ] = array_merge((array)$unparsed[ $k ], (array)$v);
							}
						}
						
						
						$sub_result = $model->getAll($sub_filter, NULL, $recursion - 1, NULL);
						
						if( empty($sub_result) )
						{
							
							if( isset($parsed_filter[ $modelname ]) )
							{
								$final_temp = NULL; break;
							}
							else
							{
								$final_temp[ $modelname ] = array();
							}
							
						}
						else
						{
							
							$sub_final = array();
							
							foreach($sub_result as $sub_index => $sub_row)
							{
								foreach($sub_row as $sub_modelname => $temp)
								{
									foreach($temp as $kk => $vv)
									{
										$sub_final[ ucfirst($sub_modelname) ][] = $vv;
									}	
								}
							}
						
							$final_temp = array_merge($sub_final, $final_temp);
							
						}
						
					}

				}
				
			}
			
			if( $final_temp )
			{
				$final[ $index ] = $final_temp;
			}

		}

		return $final;

	}
	

	/**
	* Faz consulta a base da dados e retorna
	* o resultado incluindo as relações
	* com outros modelos, baseando-se no número
	* de recursões, na forma semelhante a uma lista.
	*
	* @param array $filter [optional] Filtro para a consulta.
	* @param array $order [optional] Ordem dos resultados da busca.
	* @param array $fields [optional] Campos que devem ser retornados.
	* @param int $pagesize [optional] Quantidade de dados retornados.
	* @return mixed Array multidimensional ou nulo caso não haja registros.
	* @see self::getInstance()
	*/
	
	public function getList(Array $filter=NULL, Array $order=NULL, $recursion=-1, $pagesize=NULL) // public function select(Array $fields, Array $filter=array(), Array $order=array(), Array $group=array(), $pagesize=NULL, $pagenumber=NULL, $instruction=TRUE, $recursive=FALSE)
	{
	
		if ($recursion<0)
		{
			$recursion = $this->recursion; /* Se a recursão nao foi informada pega a propriedade do modelo. */	
		}
	
		$result = $this->get($filter, $order, $pagesize); /* Faz a pesquisa no modelo atual. */

		foreach( (array)$result as $index => $rows) /* Para cada registro retornado... */
		{
		
			if( $recursion>0 )
			{
			
				foreach($this->fkof() as $modelname => $subfields)
				{

					$filter = array_intersect_key($rows, array_flip($subfields));

					$classname = "\\{$modelname}Model";

					$model = $classname::getInstance();

					$subresult = $model->getList($filter, NULL, $recursion - 1, NULL);

					$result[ $index ][ $model->primary ] = $subresult;

				}
			
			}
		
		}
	
		return $result;
	
	}

	/**
	* Verifica se um campo possui o valor
	* indicado. Caso o segundo parâmetro não
	* seja informado, a função usará o nome
	* do campo indicado como chave primária,
	* verificando se um id existe no stage.
	*
	* @param mixed $value Valor usado no filtro de busca.
	* @param string $field [optional] Nome do campo usado no filtro.
	* @return bool Verdadeiro caso ocorrências tenham sido encontradas.
	*/
	public function exists($value, $field=NULL)
	{
	
		if ( ! $field )
		{
			$field = $this->primary;
		}
	
		$result = $this->get(array($field=>$value), NULL, NULL, 1);
	
		if ( ! empty($result) )
		{
			return TRUE;
		}
	
		return FALSE;
	
	}

	/* Callbacks */

	/**
	* Callback executado antes de salvar
	* os dados. Caso essa função retorne
	* falso, os dados não serão salvos.
	* Bom para fazer todo tipo de verificação
	* ou validação de dados antes de salvar.
	*
	* @access protected
	* @return bool
	* @see \Coupe\Model::save()
	*/
	protected function beforeSave() { return TRUE; }

	/**
	* Callback executado depois de
	* salvar os dados. Bom para envio
	* automático de mensagens.
	*
	* @access protected
	* @return bool
	* @see \Coupe\Model::save()
	*/
	protected function afterSave() { return TRUE; }

	/**
	* Função executada antes de validar
	* os dados a serem salvos.
	*
	* @access protected
	* @return bool
	* @see \Coupe\Model::save()
	*/
	protected function beforeValidate() { return TRUE; }

	/**
	* Função executada antes de buscar
	* dados.
	*
	* @access protected
	* @return bool
	* @see \Coupe\Model::get()
	*/
	protected function beforeFind() { return TRUE; }

	/**
	* Função executada depois de buscar
	* dados.
	*
	* @access protected
	* @return bool
	* @see \Coupe\Model::get()
	*/
	protected function afterFind() { return TRUE; }

	/**
	* Função executada antes de deletar.
	*
	* @access protected
	* @return bool
	* @see \Coupe\Model::deleteAll()
	*/
	protected function beforeDelete() { return TRUE; }

	/**
	* Função executada depois de deletar.
	*
	* @access protected
	* @return bool
	* @see \Coupe\Model::deleteAll()
	*/
	protected function afterDelete() { return TRUE; }

	/* Abstratas */

	/**
	* Esse método público é responsável
	* por carregar o esquema do modelo
	* diretamente do local de
	* armazenamento.
	* Deve ser especializada de acordo
	* com o tipo de armazenamento (MySQL,
	* Flatfile, etc.).
	*
	* @abstract
	* @access public
	* @return array Array associativo no padrão e com as definições do esquema do modelo.
	*/
	abstract public function loadFromStage();

	/**
	* Lê um resultado especifico
	* do modelo de dados e
	* alimenta a variável data
	* para o registro poder então
	* ser manipulado.
	*
	* @abstract
	* @access public
	* @param mixed $value Valor no campo de chave primária usado como filtro.
	* @param int $recursion [optional] Recursão usada na busca dos dados.
	* @return bool Verdadeiro caso tenha sido encontrado dados.
	*/
	abstract public function read($value, $recursion=0);

	/**
	* Pega a lista de valores únicos
	* do campo indicado. Pode-se
	* passar um filtro para restringir
	* a consulta. Se o campo não for
	* passado, usa o campo definido
	* como chave primária.
	*
	* @abstract
	* @access public
	* @param string $field [optional] Campo que terá os valores únicos retornados. Se o campo não for passado, usar o campo primário.
	* @param array $filter [optional] Filtro para retringir a consulta.
	* @return array Array com valores únicos do campo indicado.
	*/
	abstract public function getFieldValues($field=NULL, Array $filter=NULL);

	/**
	* Insere dados no local de armazenamento.
	* Os dados devem ser passado na forma de
	* um array associativo onde o indice é
	* o identificador da coluna.
	*
	* @abstract
	* @access public
	* @param array $data Array associativo onde chave indica o campo.
	* @return int Valor da chave primária do registro inserido.
	*/
	abstract public function insert(Array $data);

	/**
	* Insere os dados não persistidos
	* no stage de armazenamento.
	*
	* @abstract
	* @access public
	* @param array $data Array associativo onde chave indica o campo.
	* @param array $filter [optional] Filtro que define os registros a serem atualizados.
	* @return int Quantidade de linhas atualizadas.
	*/
	abstract public function update(Array $data, Array $filter=NULL);

	/**
	* Cria o local de armazenamento
	* (caso ele já não exista) de
	* acordo com o tipo de modelo
	* e seu schema.
	* Caso o modelo de dados acesse
	* dados de umbSGBD relacional,
	* esse método deverá criar a
	* tabela. Caso utilize um arquivo
	* como fonte de dados (XML, CSV,
	* etc.), deverá criar o arquivo
	* que armazena a informações, etc.
	*
	* @abstract
	* @access public
	* @return bool Verdadeiro caso o local de armazenamento tenha sido criado.
	*/
	abstract public function createStage();

	/**
	* Método principal para recuperação
	* de dados. Deve ser protegido para
	* não ser acessado diretamente,
	* mas somente por outros métodos
	* do próprio modelo ou classes
	* filhas.
	* Esse método deve receber um
	* filtro que será usado para
	* definir quais dados deve ser
	* recuperados, um array que definirá
	* a ordem dos registros, outro array
	* com a lista de campos que deverão
	* ser retornados, e um último
	* parâmetro que informa a quantidade
	* de dados a serem retornados.
	*
	* @abstract
	* @access protected
	* @param array $filter [optional] Filtro para a consulta.
	* @param array $order [optional] Ordem dos resultados da busca.
	* @param array $fields [optional] Campos que devem ser retornados.
	* @param int $pagesize [optional] Quantidade de dados retornados.
	* @return mixed Array se encontrar registros ou nulo.
	*/
	abstract protected function _get(Array $filter=NULL, Array $order=NULL, Array $fields=NULL, $pagesize=NULL);


	/**
	* Remove todas as ocorrências do
	* stage ou apenas as indicadas
	* por um filtro.
	*
	* @abstract
	* @access protected
	* @param array $filter [optional] Filtro para remoção dos dados.
	* @return bool Verdadeiro casos os dados sejam removidos.
	*/
	abstract protected function _deleteAll(Array $filter=NULL);

}