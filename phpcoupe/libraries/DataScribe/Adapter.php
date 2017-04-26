<?php

namespace DataScribe
{

	abstract class Adapter
	{
		
		/**
		* Armazena o ponteiro para uma resultado de query.
		* @var handler Ponteiro para resultado de busca.
		*/
		public $result;
		
		/**
		* Armazena o ponteiro de conexão.
		* @var handle Ponteiro para conexão.
		*/
		protected $_connection;
		
		/**
		* Configurações do adaptador
		* para conexão e execução.
		* @var array Configurações do conector.
		*/
		protected $_config;
		
		/**
		* Indica se uma transação foi iniciada.
		* @var bool Verdadeiro se uma trasação está ocorrendo.
		*/
	    protected $_transaction_started;

		/**
		* Método construtor recebe
		* um array com parâmetros de conexão.
		*
		* @access public
		* @return void
		* @see \DataScribe\Adapter::$_config
		*/
		public function __construct(Array $config)
		{
			
			$this->_config = $config;
			
		}
		
		/**
		* Retornar as configurações
		* do banco como propriedades do objeto.
		*/
		public function getConfig($key)
		{
			if( array_key_exists($key, $this->_config) )
			{
				return $this->_config[ $key ];
			}
		}
		
		/**
		* Pega o handler de conexão ao banco de dados.
		* @access public
		* @return handler Ponteiro para a conexão com o banco.
		*/
		public function getConnection()
		{
	
			return $this->_connection;
	
		}
		
		public function isConnected()
		{
			
			return $this->_connection == TRUE;
			
		}

		/**
		* Diz se já resultado.
		*/
		public function hasResult()
		{

			return is_resource($this->_result);

		}
		
		public function fetch($sql)
		{
			
			$this->query($sql);
			
			return $this->fetchAll();
			
		}

		/**
		* Retorna todas linhas do resultado com array.
		*/
		public function fetchAll()
		{
			
			if( $this->hasResult() )
			{
				
				$result = array();
			
				while( $row = $this->fetchRow() )
				{
					$result[] = $row;
				}
			
				return $result;
				
			}
			
			return NULL;
			
		}
	
		/* Método criados nas classes especializadas em determinado SGBD. */
		
		/**
		* Cria um tabela com o nome indicado.
		*/
		abstract public function createTableAs($tablename, \DataScribe\Select & $select);
		
		/**
		* Inicia uma transição.
		*/
		abstract public function begin();
		
		/**
		* Desfaz uma transição.
		*/
		abstract public function rollBack();
		
		/**
		* Comita a transição.
		*/
		abstract public function commit();
		
		/**
		* Retorna uma linha do resultado.
		*/
		abstract public function fetchRow();
		
		/**
		* Faz uma consulta ao banco de dados.
		*/
		abstract public function query($sql);
		
		/**
		* Lista as tabelas do banco onde se está conectado.
		*
		* @access public
		* @abstract
		* @return array Lista das tabelas.
		*/
		abstract public function listTables();

		/**
		* Versão do SGBD onde se está conectado.
		*
		* @access public
		* @abstract
		* @return string Versão do banco.
		*/
		abstract public function version();
		
		/**
		* Método de conexão específico de cada banco.
		*
		* @access protected
		* @abstract
		* @return bool Verdadeiro se conectou ou falso.
		*/
		abstract public function connect();
		
		/**
		* Método de desconexão específico de cada banco.
		*
		* @access protected
		* @abstract
		* @return bool Verdadeiro se desconectou ou falso.
		*/
		abstract public function disconnect();

	}

}