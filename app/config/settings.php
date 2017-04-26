<?php

/**
* Relatório de erros
*
* Relatorios de erros apresentados pelo PHP durante a
* execucao do script. Utilize E_ALL durante o
* desenvolvimento e coloque 0 quando o projeto
* for para producao. Ninguem precisa receber
* informacoes adicionais sobre seu programa.
* Veja mais em:
* http://br.php.net/manual/en/function.error-reporting.php
*
*/

error_reporting(E_ALL);

/**
* Mapas de rotas
*
* Configurações que serão utilizadas pela classe Mappers.
* Essas configurações são os mapas de rotas para
* determinadas áreas do projeto. Rotas são como
* endereços fictícios que apontam para um endereço real,
* funcionando como um dicionário de URL.
*
*/

$config['mapper'] = array(
'/'=>'/modeltest' /* O caminho '/' NÃO deve ser removido. Ele indica o caminho inicial do site. */
);

/**
* Prefixos de URL
*
* Essa opção permite configurar prefixos de URL.
* Quando adicionados na URL, um prefixo também é
* adicionado ao nome da action do controller ou ao
* inicio do arquivo de conteudo, permitindo uma forma
* fácil de customizar áreas distintas de um
* controller.
* Áreas administrativas são um bom uso para prefixos.
* Por exemplo: se configurar um prefixo chamado
* 'admin' toda vez que a URL com o prefixo for usada
* 'http://www.meusite.com.br/admin/cliente/deletar'
* ao invés de se chamar a action 'deletar', será
* chamada uma action 'admin_deletar'
*
*/

$config['url_prefix'] = array('admin');

/**
* Autoload
*
* Essa configuração permite o carregamento automático
* de classes e helpers logo no inicio da execução do
* projeto, deixando-os carregados em memória prontos
* para serem utilizados.
* Para isso, basta definir nas posições do array
* autoload os nomes dos objetos, observando a chave
* associativa que indica o tipo do objeto a ser
* carregado.
* Os tipos podem ser helper, controller, config,
* dictionary. Exemplo:
*
* $autoload['autoload']= array(
* 'helper'=>array('meu_helper', 'helper_xpto')
* );
*
*/

$config['autoload'] = array(
	'lib'=>array('\Coupe\Model\Mysql.php'),
	'helper'=>array('utils', 'resources'),
	'controller'=>array('App')
);

/**
* Idioma padrão do projeto
*
* Defina aqui o idioma principal de seu projeto. As
* mensagens de erro, avisos do sistema (nao o
* conteudo do projeto) e das classes serao exibidos
* de acordo com a linguagem definida.
* pt | en | fr | sp | jp | ch
*
*/

$config['idiom_default'] = 'pt';

/**
* Nome da sessão
*
* Sempre que o aplicativo for armazenar dados em
* sessao (como carrinho de compras), qual o nome da
* sessao que armazenarah os dados?
*
*/

$config['session_name'] = 'Mysession';

/**
* Chave de criptografia
*
* Sempre que seu aplicativo for utilizar criptografia
* de strings, qual serah a palavra chave para
* codificar/decodificar?
*
*/

$config['encryption_key'] = 'Mykey';

/**
* Ativar Log
*
* Configurado como TRUE, registra todos eventos
* ocorridos em um arquivo de log diario. Os arquivos
* de log ficam dentro da pasta '%application%/log',
* onde '%' significa que o caminho dependerah do
* caminho configurado para o diretorio 'application/'
* (dentro do arquivo index.php).
*
*/

$config['active_log'] = TRUE;

/**
* Ativa debug do framework
*
* Ativando o debug, o PHP Coupe te mostra varias informacoes
* sobre os objetos (modulos, classes) carregados pelo seu
* projeto, assim como o tempo de execucao de querys
* executadas pela classe 'Coupe_Database', assim como o
* tempo total de execucao de cada tela/pagina do seu projeto.
* AVISO: Lembre-se de desabilitar o cache ao usar o debug.
*
*/

$config['active_debug'] = FALSE;

/**
* Formato de data
*
* Indique o formato de data que serah utilizado no
* projeto. Assim, as validacoes de data feitas pela
* classes Coupe utilizarao esse formato para dizer
* se a data estah correta.
* Por exemplo, caso utilize o metodo 'verify()' da
* classe 'Coupe Form'] = a verificacao do formato de
* data serah usado como base no formato aqui
* indicado.
* Importante: Utilize formatos de data do PHP.
*
*/

$config['date_format'] = 'dd/mm/YYYY';

/**
* Spaceless
*
* Uma forma de acelerar o carregamento de sua pagina
* eh removendo o excesso de espacos em branco.
* Para remover o excesso de espacos em branco da
* pagina gerada pelo PHP Coupe coloque essa opcao
* como TRUE. Veja o codigo fonte da sua pagina
* para ver o resultado.
*
*/

$config['spaceless'] = FALSE;

/**
* Configura templates manualmente
*
* Nessa configuração é possível forçar o Template
* que determinado documento irá usar para que não
* seja um Template que tenha o seu nome, ou o
* Template padrão. Como chave do Array coloque o ID
* do documento e como valor o ID do Template.
*
* Exemplo:
* Config::set('template_force', array(
* 'home'=>'template_home'
* 'contato'=>'template_xpto'
* ));
*
*/

$config['template_force'] = array(
);

/**
* Template padrão a ser usado sempre 
* um template solicitado não for
* encontrado.
*/
$config['template_default'] = 'default';

/**
* Resolver marcas
*
* Coloque essa opcao como TRUE se quiser que as
* marcacoes de bloco [##], modulos [!!] ou [[]], e
* de variaveis {{}} sejam resolvidas.
* Nao eh o obrigatorio o uso de marcacoes. Eh
* possivel invocar codigos PHP nativos ao inves
* de usar essas marcacoes. Porem, as marcacoes
* deixam o codigo HTML mais limpo e facil de ser
* manipulado por designers. Alem de permitir que
* certos trechos de codigo nao sejam salvos pelo
* cache caso esse esteja ativo (veja sobre isso no
* arquivo de configuracao do Cache).
*
*/

$config['resolve_marks'] = FALSE;

/**
* Auto completar URL
*
* Caso seja definida como TRUE, essa opcao ativa a
* funcao de auto completar URLs.
* Isso significa que se seu usuario digitar um
* endereco errado ou incompleto, o PHP Coupe
* tentarah encontrar um documento valido no qual seu
* endereco combine com a parte digitada.
* Por exemplo: se um usuario digitar
* 'http://www.meusite.com.br/pagina' e esse endereco
* nao existir, mas existir a pagina
* 'http://www.meusite.com.br/pagina_inicial'] = entao
* essa outra pagina valida carregada automaticamente.
* Esse recurso se torna util nos casos em que o
* usuario nao se lembrar do endereco completo de
* uma pagina.
*
*/

$config['url_complete'] = TRUE;

/**
* Ativa cache
*
* Quando definida como TRUE essa opção permite salvar
* os resultados das paginas geradas em cache. Dessa
* forma, códigos PHP e, consequentemente, consultas
* a bancos de dados terão seus resultados salvos
* (evitando o re-processamento a cada nova visita).
*
*/

$config['cache_active'] = TRUE;

/**
* Tempo do cache
*
* Configure o numero de DIAS que seu cache terá
* validade. Caso o arquivo de cache seja mais
* velho que o tempo definido aqui, ele será
* automaticamente re-escrito.
*
*/

$config['cache_time'] = 2;

/**
* Defina dentro do array os plugins a serem
* carregados pelo framework.
*
*/
$config['plugins'] = array(
	'auth'
);

/**
* Código do cadastro no interface
*
* Caso tenha dados armazenados no Component, coloque
* aqui seu codigo de usuario. Assim, todo modulo
* que trabalhe com o Component saberah quais dados
* baixar.
*
*/

$config['interface_code'] = '';

?>
