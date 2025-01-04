<?php

namespace Nornas;

/**
 *
 * Classe responsável pelo carregamento dos recursos base indispensáveis
 * para o funcionamento do sistema, através da adição de namespaces.
 *
 * Para carregar os arquivos base do sistema é necessário saber onde estes se
 * encontram primeiramente. 
 *
 * Para isto, os desenvolvedores devem incluir no arquivo de nome
 * "autoloader.php", que se encontra na raiz do projeto, todos os namespaces
 * que possam levar à recursos considerados indispensáveis para o funcionamento
 * do seu sistema, como mostrado no bloco de código abaixo.
 *
 *      <?php
 *          // Criação de nova instância da classe Autoloader.
 *          $autoloader = new \Nornas\Autoloader();
 *
 *          // Adição de um novo namespace ao sistema.
 *          $autoloader->addNamespace(
 *              "\\Namespace\\",
 *              "caminho\para\a\pasta\com\classes"
 *          );
 *      ?>
 *
 * Em caso de persistência de dúvidas, veja um exemplo funcional desta classe
 * no endereço abaixo.
 *
 * @example http://www.thomerson.com.br/nornas/examples/autoloader.php Demons-
 * tração da adição de namespaces.
 *
 * @since 1.0.0 Primera vez que esta classe foi introduzida.
 * 
 * @author Thomerson Roncally Araújo Teixeira <thomersonroncally@outlook.com>
 * @copyright 2014-2015 Thomerson Roncally Araújo Teixeira
 *
 */

class Autoloader
{
    /**
     *
     * @var array $namespaces Armazena todos os namespaces adicionados.
     * @access protected
     */

    protected $namespaces = array();

    /**
     *
     * @method void __construct() Registra e habilita o funcionamento da classe.
     */

    public function __construct()
    {
        spl_autoload_register(array($this, "loadClass"));
    }

    /**
     *
     * @method void addNamespace(string $prefix, string $base_dir, bool $prepend) Adiciona os namespaces para carregamento das classes necessárias para funcionamento do sistema.
     */
    
    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
        $prefix = trim($prefix, "\\") . '\\';
        
        $prefix_len = strlen($prefix);
        
        $base_dir = str_replace("/", DIRECTORY_SEPARATOR, $base_dir) . DIRECTORY_SEPARATOR;
        
        if (isset($this->namespaces[$prefix]) === false) {
            $this->namespaces[$prefix] = array();
        }
        
        if ($prepend) {
            array_unshift($this->namespaces[$prefix], $prefix_len, $base_dir);
        } else {
            array_push($this->namespaces[$prefix], $prefix_len, $base_dir);
        }
    }

    /**
     *
     * @method string loadClass(string $class) Valida o nome da classe à ser adicionada e em caso de sucesso, à inclúi ao projeto.
     */
    
    public function loadClass($class)
    {        
        foreach ($this->namespaces as $prefix => $attrs) {
            if (strstr($class, $prefix)) {
                $class = substr($class, $attrs[0]);
                
                $mapped_file = $this->loadMappedfile($prefix, $class);
                
                if ($mapped_file) {
                    return $mapped_file;
                }
            }
        }

        if (!empty($class)) {

            $path = SITE_PATH . "/vendor/tcpdf/tcpdf.php";

            if (file_exists($path)) {
                require_once($path);
                return true;
            }
        }
        
        self::error("A classe '{$class}' não foi encontrada.");
    }
    
    /**
     *
     * @method bool|string loadMappedFile(string $prefix, string $class) Verifica se a classe está localizada no caminho especificado pelo namespace informado.
     * @access protected
     */

    protected function loadMappedfile($prefix, $class)
    {
        if (isset($this->namespaces[$prefix]) === false) {
            return false;
        }
        
        $file = $this->namespaces[$prefix][1]
              . $class
              . '.php';
        
        if ($this->requireFile($file)) {
            return $file;
        }
        
        return false;
    }

    /**
     *
     * @method bool requireFile(string $file) Checa a existência do arquivo passado como parâmetro e tenta incluí-lo ao sistema.
     * @access protected
     */
    
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        
        return false;
    }
    
    /**
     *
     * @method void error(string $msg) Lança uma exceção com a mensagem de erro passada como parâmetro.
     * @access protected
     */

    protected function error($msg)
    {
        throw new \Exception($msg);
    }
}
