<?php

namespace Nornas;

/**
 *
 * Classe responsável pela estrutura de um Controller, sendo este responsável
 * pela comunicação entre o Model e a View.
 * 
 * Em uma arquitetura de software MVC (Model-View-Controller), o elemento prin-
 * cipal para o funcionamento da mesma, é o Controller, que desempenha o papel
 * de comunicação entre os outros dois componentes desta arquitetura, Model e
 * View.
 *
 * Esta classe deve ser obrigatoriamente extendida para uso, acrescentando mé-
 * todos e/ou propriedades às classes filhas, sem gerar qualquer alteração nes-
 * ta.
 *
 * Deve-se levar em conta que um controller executa a comunicação entre uma ou
 * mais Views e um ou mais Models, não sendo necessário a sua replicação a não
 * ser que se mostre realmente necessário este ato.
 *
 * Para extensão desta classe, deve-se tomar como exemplo este bloco de código
 * abaixo:
 *
 *      <?php
 *          // Inclusão da classe Controller pelo namespace.
 *          use \Nornas\Controller as Controller;
 *          
 *          // Classe filha da classe Controller
 *          class CalculadoraController extends Controller {
 *              public function __construct() {
 *                  parent::__construct();
 *              }
 *              
 *              // Método adicionado na criação da classe filha.
 *              public function soma($primeiroNumero, $segundoNumero) {
 *                  // ... Bloco de código da função soma
 *              }
 *          }
 *      ?>
 * 
 * Em caso de persistência de dúvidas, visite um exemplo funcional desta classe
 * no endereço abaixo:
 *
 * @example http://www.thomerson.com.br/nornas/examples/controller.php demons-
 * tração da extensão da classe Controller para uso em seus projetos.
 *
 * @since 1.0.0 Primeira vez que esta classe foi adicionada.
 *
 * @author Thomerson Roncally Araújo Teixeira <thomersonroncally@outlook.com>
 * @copyright 2014-2015 Thomerson Roncally Araújo Teixeira
 */

class Controller
{
    /**
     *
     * @var undefined $session
     * @access protected
     */
    protected $session;
    
    /**
     *
     * @method void __construct() Método construtor da classe.
     */

    public function __construct()
    {
        Session::init();
    }
    
    /**
     *
     * @method void loadView(string $view_path, string $view_file, array|null data) Carrega uma view.
     */

    public function loadView($view_path, $view_file, $data = null)
    {
        if (is_array($data) && count($data) > 0) {
            extract($data, EXTR_PREFIX_SAME, 'data');
        }
        
        $view = $view_path . "/" . $view_file . ".php";

        if (!file_exists($view)) {
            self::error("Houve um erro. A view '{$view}' não pode ser encontrada.");
        }
        
        require_once $view;
    }

    /**
     *
     * @method void loadModel(string $name, string $as) Carrega um model.
     */
    
    public function loadModel($name, $as = "")
    {
        $this->$name = new $name();
        if ($as !== "") {
            $this->$as = &$this->$name;
        }
    }

    /**
     *
     * @method void error(string $msg) Lança uma exceção com a mensagem passada como parâmetro.
     * @access protected
     */
    
    protected function error($msg)
    {
        throw new \Exception($msg);
    }
}
