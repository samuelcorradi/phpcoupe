<?php if ( ! defined('MANPATH') ) exit('No direct script access allowed');

// gera o menu para trocar o idioma do site
// o menu pode ser informado atraves de SELECT (post) ou uma LISTA (get)
// o menu tambem pode ser formatado atraves do parametro usando palavras chave
// para gerar uma lista, coloque %list%
// para gerar um formulario com select, use %action% %select% %buttom%
// recebe: ([$string=formatacao_opcional])
// retorna: STRING (HTML do menu)
if ( ! function_exists('idiom_menu') )
{
	function idiom_menu($html='')
	{
		
		$idiom_names = Config::item('IDIOM', Idiom::current()); // tenta pegar o nome dos idiomas

		$idiom_list = Config::get('IDIOM');

		// if( $html==false ) $html = "<form method=\"POST\" action=\"%action%\">\n%select%\n%buttom%\n</form>\n"; // se nao recebeu nenhuma formatacao, usa uma formatacao padrao
		if( $html==false ) $html = "<form method=\"POST\" action=\"#\">\n%select%\n%buttom%\n</form>\n"; // se nao recebeu nenhuma formatacao, usa uma formatacao padrao
		
		$x = 0;
		
		if ( strpos($html, '%select%')!==false )
		{

			$action = URI::make('/', array(), true); // o form serah submetido para o endereco da pagina inicial
			
			$select = "<select name=\"__idiom__\">\n"; // cria o formulario para se escolher o idioma
			
			while ( $code = key($idiom_list) )
			{
				
				$selected = '';
				
				if ( $code == Idiom::current() ) $selected = ' selected="selected"';
				
				$select .= '<option value="' . $code . '"' . $selected . '>' . $idiom_names[$x] . '</option>
';
				next($idiom_list);
				
				$x++;
				
			}
			
			$select .= "</select>";
			
			$buttom = '<input type="submit" value="OK" />';
			
			$html = str_replace('%action%', $action, $html);
			
			$html = str_replace('%select%', $select, $html);
			
			$html = str_replace('%buttom%', $buttom, $html);
			
		}
		elseif ( strpos($html, '%list%')!==false )
		{
		
			$list = '<ul>';
			
			reset($idiom_list);
			
			while ( $code = key($idiom_list) )
			{
				$link =  URI::make('', array('__idiom__'=>$code), true);
				
				$selected = '';
				
				if ( $code == Idiom::current() ) $selected = ' class="selected"';
				
				$list .= "<li{$selected}><a title=\"{$idiom_names[ $x ]}\" href=\"{$link}\">{$idiom_names[ $x ]}</a></li>";
				
				next($idiom_list);
				
				$x++;
				
			}
			
			$list .= '</ul>';
			
			$html = str_replace('%list%', $list, $html);
			
		}
		
		log_message('Menu de tradução carregado');
		
		return $html;
		
	}
}

?>
