<?php

class Auth
{

	/**
	 * Tratamento de dados
	 * de credenciais como
	 * senhas.
	 */
	const SHA256 = "sha256";
	
	/**
	 * Local para persistência
	 * dos dados de quem autenticou.
	 * Pode ser uma sessão, banco
	 * de dados ou sistema de cache
	 * como Memcached.
	 * O storage possui uma interface
	 * e o tipo de interface vai
	 * depender da implementação.
	 *
	 * @see Habilis\Auth\Storage
	 */
	public $storage;
	
	/**
	 * Amazena a lista dos campos que,
	 * uma vez retornado por
	 * Habilis\Auth\Adapter como
	 * uma consulta válida, será
	 * salvo por Habilis\Auth\Sotage.
	 * Basicamente são os campos
	 * que terão seus valores
	 * salvos na sessão armazenada
	 * no storage para consulta
	 * durante a aplicação.
	 * Campos comuns para identidade
	 * são nome, e-mail, perfil de
	 * usuário, etc.
	 */
	public $identity = array();

	/**
	 * Crendenciais são um conjunto
	 * chave/valor que indica os
	 * dados que deverão ser usados
	 * para autenticar um usuário.
	 * Se os dados do usuário estão
	 * em um SGBD, os dados da
	 * credencial será o filtro
	 * usado para consulta.
	 *
	 * @var array
	 */
	public $credentials = array();

	/**
	 * Indica o tratamento que dados
	 * de credencial devem sofre.
	 * Isso é usado pois dados de
	 * credenciais não são
	 * @var string
	 */
	public $credential_tratament = self::SHA256;
	
	/**
	 * Construtor recebe obrigatoriamente
	 * objeto que fará a persistência
	 * dos dados de quem logou. Essa
	 * persistencia então será usada
	 * para dizer se o usuário está
	 * logado ou não.
	 *
	 * @param \Habilis\Auth\Storage $storage
	 */
	public function __construct(\Habilis\Auth\Storage & $storage)
	{

		$this->storage = $storage;

	}
	
	/**
	 * Retorna o objeto usado para
	 * armazenar os dados obtidos
	 * durante a autenticação.
	 *
	 * @return \Habilis\Auth\Storage
	 */
	public function getStorage()
	{
		
		return $this->storage;
		
	}

	/**
	 * Define quais campos onde estão
	 * os dados que serão usados
	 * pelo Habilis\Auth\Storage como
	 * identificador de quem faz a
	 * autenticação.
	 * Basicamente definir uma
	 * identidade é definir os campos
	 * que são salvos pelo storage.
	 * Campos definidos como identidade
	 * geralmente são o nome, email,
	 * perfil, etc.
	 * Se for passado um valor para
	 * a identidade, o campo será usado
	 * também como credencial na
	 * consulta que será feita por
	 * Habilis\Auth\Adapter.
	 * Exemplo de uso:
	 * $auth = new Auth();
	 * $auth->setIdentity('User.name');
	 * $auth->setIdentity('User.login', $_POST['login']);
	 * $auth->setCredential('User.password', $_POST['pass']);
	 *
	 */
	public function setIdentity($name, $value=NULL)
	{

		if( $value )
		{
			$this->setCredential($name, $value);
		}

		$this->identity[] = $name;

	}
	
	/**
	 * Define uma característica que
	 * deverá ser observada ao realizar
	 * a autenticação com o usuário.
	 * As credenciais quase sempre são
	 * o nome do usuário e sua senha.
	 * Mas pode ser qualquer outra
	 * informação que seja relevante
	 * para identificar o usuário.
	 * Os dados definidos na credencial
	 * quase sempre são os valores
	 * obitidos através do formulário
	 * de login. Exemplo de uso:
	 * $auth = new Auth();
	 * $auth->setIdentity('User.email', $_POST['login']);
	 * $auth->setIdentity('Client_client.service_id', $_POST['group']);
	 * $auth->setCredential('User.password', $_POST['pass']);
	 *
	 * @param $name string Nome da propriedade.
	 * @param $value string Valor da propriedade.
	 */
	public function setCredential($name, $value)
	{

		$this->credentials[ $name ] = $value;

	}

	/**
	 * Define o tratamento a ser usado
	 * em credenciais.
	 * Somente os tipos de tratamentos
	 * definidos na classe são suportados.
	 *
	 * @param $tratament
	 * @throws Exception
	 */
	public function setCredentialTratamet($tratament)
	{
		if( in_array($tratament, array(self::SHA256)) )
		{
			$this->credential_tratament = $tratament;
		}
		else
		{
			throw new \Exception("Invalid treatment type.");
		}
	}

	/**
	 * Método para realizar a autenticação
	 * de acordo com as credenciais
	 * definidas.
	 * Uma vez realizada a autencicação
	 * pelo adapter, os dados de identidade
	 * são então salvos no storage que
	 * será usado como sessão.
	 *
	 * @param \Habilis\Auth\Adapter $adapter
	 * @return bool
	 */
	public function authenticate(\Habilis\Auth\Adapter & $adapter)
	{

		$result = $adapter->authenticate($this->credentials);

		/*
		 * Se o conjunto das credenciais
		 * resultou em uma consulta válida,
		 * foi autênticado.
		 */
		if($result)
		{
			
			$identity = array_intersect_key(array_fill_keys($this->identity, NULL), $this->credentials);
			
			$identity['authentication_time'] = mktime();
			
			$this->storage->setData($identity);
			
			return TRUE;
			
		}

	}
	
}

?>
