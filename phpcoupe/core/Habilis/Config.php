<?php

/**
* A classe \Habilis\Config gerência configurações de aplicação.
*
* Ela permite carregar arquivos que possuem a variável
* \Habilis\Config::$config definida e disponibiliza o
* conteúdo dessa variável para ser usado como
* configurações pré-definidas.
*
* @version 0.1
* @package Habilis
* @subpackage Configuration
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @since 04/05/2012
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/habilis/config
*/

namespace Habilis
{

	class Config
	{
	
		/**
		* Lista dos arquivos já carregados.
		* @var array Lista de nomes de arquivos carregados.
		* @access private
		*/
		private $__loaded = array();

		/**
		* Array que armazena as configurações carregadas.
		* @var array Configurações dos arquivos carregados.
		* @access private
		*/
		private $__config = array();

		/**
		* Retorna configurações como parâmetros do objeto.
		*
		* @access public
		* @see \Habilis\Config::get()
		* @param string $config Nome da configuração a ser carregada.
		* @return mixed Conteúdo da configuração ou Nulo caos não exista.
		*/
		public function __get($config)
		{
			return $this->get($config);
		}

		/**
		* Pega o item indicado.
		*
		* @access public
		* @see \Habilis\Config::$__config
		* @param string $config Nome da configuração a ser carregada.
		* @return mixed Conteúdo da configuração ou Nulo caos não exista.
		*/
		public function get($config)
		{
			if ( isset($this->__config[ $config ]) )
			{
				return $this->__config[ $config ];
			}
		}
	
		/**
		* Carrega as configurações contidas em um arquivo do tipo configuração.
		*
		* @access public
		* @see \Habilis\Config::$__config
		* @see \Habilis\Config::$__loaded
		* @param string $filepath Caminho para o arquivo com configurações.
		* @return mixed Verdadeiro se o arquivo foi carregado, Nulo se arquivo não existir ou Falso se aconteceu qualquer outro problema no carregamento.
		*/
		public function load($filepath)
		{
		
			/*
			* Verifica se o arquivo jah estah carregado.
			*/
			if ( in_array($filepath, $this->__loaded, TRUE) )
			{
				return TRUE;	
			}
		
			if ( ! is_file($filepath) )
			{
				return NULL;
			}
		
			if( ! is_readable($filepath) )
			{
				return FALSE;
			}
		
			include_once $filepath;
		
			/*
			* Verifica se há uma variável $lang no arquivo de dicionário carregado.
			*/
			if ( ! isset($config) )
			{
				return FALSE;
			}
		
			/*
			* Adiciona o conteúdo do vetor $lang carregado ao vetor de linguagens da classe.
			*/
			$this->__config = array_merge($this->__config, $config);
		
			$this->__loaded[] = $filepath;
		
			return TRUE;
		
		}
	
	}

}