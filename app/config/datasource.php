<?php

/**
| Conexões armazenadas para banco de dados
|
| As configuracoes abaixo serao utilizadas para
| efetuar conexoes a bancos de dados através de
| classe Loader::database().
| Para criar novos dados, e utiliza-los para conexao,
| basta adicionar novas posicoes ao array
| $config['datasource'], onde a chave associativa
| será a referencia a ser informada ao carregar
| uma nova conexao ao banco de dados usando o metodo
| Loader::database(). Exemplo:
| 
| $config['datasource'] = array(
| 'default' => array(
| 	'type'=>'',
| 	'host'=>'',
| 	'port'=>'',
| 	'user'=>'',
| 	'password'=>'',
| 	'name'=>'',
| 	'prefix'=>'',
| 	'description'=>'')
| );
|
*/

$config['datasource'] = array(
'default' => array(
	'drive'=>'mysql',
	'host'=>'127.0.0.1',
	'port'=>'3306',
	'user'=>'root',
	'password'=>'123',
	'name'=>'phpcoupe',
	'prefix'=>'',
	'description'=>'')
);
