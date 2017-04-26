<?php

/**
* A classe \Habilis\Log armazena informações de log.
*
* A classe registra em arquivos de textos eventos informando uma
* descrição do evento, com data/hora e IP de quem executou a tarefa.
*
* @version 0.1
* @package Habilis
* @subpackage Log
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/habilis/log
*/

namespace Habilis
{

	class Log
	{
	
		/**
		* Caminho para o arquivo de log.
		* @access public
		* @var string Caminho para arquivo de log.
		*/
		public $filepath;
	
		/**
		* Método construtor.
		*
		* @access public
		* @param string $filepath Caminho para o arquivo de log.
		* @return void
		*/
		public function __construct($filepath)
		{
		
			$this->filepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $filepath);
	
		}
	
		/**
		* Escreve a mensagem de log passada como
		* parametro em um arquivo. A mensagem
		* será sempre escrita na última linha
		* do arquivo. Toda entrada recebe também
		* a data em que foi gerada.
		*
		* @access public
		* @param string $msg Mensagem a ser armazenada em log.
		* @return bool Verdadeiro caso tenha gravado com exito ou falso.
		* @see \Habilis\Log::$filepath
		*/
		public function write($msg)
		{
		
			$handle = ( ! file_exists($this->filepath) ) ? @ fopen($this->filepath, 'w+') : @ fopen($this->filepath, 'a+');
		
			if ( $handle )
			{
			
				$datetime = date('d/m/Y H:i:s'); /* Pegamos a data e hora atual */
		
				// $remote_addr = getenv('REMOTE_ADDR'); /* Pegar o IP */
		
				$msg = "\n[{$datetime}] {$msg}"; // $msg = "\n[{$datetime}] [{$remote_addr}] {$msg}"; /* Mensagem que será armazenada '<dia hora> [<IP>] <mensagem>' */
		
				fwrite($handle, $msg, strlen($msg)); /* Escreve no arquivo */
		
				fclose($handle);
		
				return TRUE;
		
			}

			return FALSE;
		
		}
	
	}

}

?>
