<?php

namespace Nornas;

/**
 *
 * Classe responsável pelo armazenamento de funções bastante utilizadas
 * durante a escrita do código, para evitar código repetitivo, priorizando o
 * re-uso de código e facilitando a manutenção destas funções.
 *
 * Certas vezes, elaboramos uma função que tem uma determinada finalidade para
 * um projeto em andamento. Futuramente nos vemos na necessidade de utilizar
 * esta função novamente, mas se não à temos armazenada em algum local, somos
 * obrigados à refazê-la, gerando re-trabalho e perda de tempo.
 *
 * Como solução, permitimos o uso desta classe para tal finalidade. Criando
 * funções, que aqui serão armazenadas e mantidas como métodos, e somente
 * chamá-las quando necessário.
 *
 * Um exemplo de criação de um método para ser usado posteriormente, não neces-
 * sita de muitos passos, sendo necessário somente a criação de um método com
 * o acesso público e estático, da seguinte forma:
 *
 *      public static function nome_da_funcao($parametros)
 *      {
 *          // ... Bloco de código da função
 *      }
 *
 * Em caso de persistência de dúvidas, visite um exemplo funcional desta classe
 * no endereço abaixo.
 *
 * @example http://www.thomerson.com.br/nornas/examples/common.php Demonstração
 * da criação de funções de re-uso para o projeto.
 *
 * @since 1.0.0 Primeira vez que esta classe foi adicionada.
 *
 * @author Thomerson Roncally Araújo Teixeira <thomersonroncally@outlook.com>
 * @copyright 2014-2015 Thomerson Roncally Araújo Teixeira
 */

class Common
{
    /**
     * 
     * @method void __construct() Método construtor da classe, setado como privado para evitar instanciação da classe, o que indica que todos os métodos serão chamados estáticamente.
     * @access private
     */

    private function __construct(){}
    
    /**
     *
     * @method bool isEmpty(mixed $data) Verifica se uma variável é vazia.
     */

    public static function isEmpty($data)
    {
        if (!is_array($data)) {
            switch ($data) {
                case '0':
                    return false;
            }
        }
        
        if (count($data) == 0) {
            return true;
        }

        return false;
    }

    /**
     *
     * @method void redir(string $url) Redireciona o cliente para uma determinada URL.
     */
    
    public static function redir($url)
    {
        header("location: " . SITE_URL . $url);
        
        exit;
    }

    /**
     *
     * @method string sanitize(string $str) Sanitariza uma "string" substituindo alguns caracteres e excluindo outros.
     */

    public static function sanitize($str)
    {
        $str = strtolower($str);
        $str = preg_replace("/[ÁÀÂÃÄ|áàâãä]/ui", "a", $str);
        $str = preg_replace("/[ÉÈÊË|éèêë]/ui", "e", $str);
        $str = preg_replace("/[ÍÌÎÏ|íìîï]/ui", "i", $str);
        $str = preg_replace("/[ÓÒÔÕÖ|óòôõö]/ui", "o", $str);
        $str = preg_replace("/[ÚÙÛÜ|úùûü]/ui", "u", $str);
        $str = preg_replace("/[Ç|ç]/ui", "c", $str);
        $str = preg_replace("/[!?@#$%&*()\[\] \/\;:\.,{}]/ui", "-", $str);
        $str = preg_replace("/-+/ui", "-", $str);
        $str = preg_replace("/^[-]/ui", "", $str);
        $str = preg_replace("/[-]$/ui", "", $str);

        return $str;
    }

    /**
     *
     * @method bool|string format(string $type, string $var) Formata "strings", substituindo hífens por espaços em branco.
     */

    public static function format($type, $var = null)
    {
        if (self::isEmpty($var)) {
            return false;
        }

        switch ($type) {
            case 'str': {
                $var = str_replace("-", " ", $var);
                $var = ucfirst($var);
                return $var;
            }
            case 'localeToDate': {
                $date = explode("/", $var);
                $newDate = $date[2] . "-" . $date[1] . "-" . $date[0];
                return $newDate; 
            }
            case 'dateToLocale': {
                $date = explode("-", $var);
                $newDate = $date[2] . "/" . $date[1] . "/" . $date[0];
                return $newDate; 
            }
            case 'datetimeToLocale': {
                $datetimeEx = explode(" ", $var);
                $date = explode("-", $datetimeEx[0]);
                $time = $datetimeEx[1];
                $newDatetime = $date[2] . "/" . $date[1] . "/" . $date[0] . " às " . $time;
                return $newDatetime; 
            }
            case 'datetimeToLocaleDate': {
                $datetimeEx = explode(" ", $var);
                $date = explode("-", $datetimeEx[0]);
                $newDate = $date[2] . "/" . $date[1] . "/" . $date[0];
                return $newDate; 
            }
            case 'value': {
                $var = str_replace(".", "", $var);
                $var = str_replace(",", ".", $var);
                return $var;
            }
            default:
                break;
        }
    }

    public static function removeMask($string)
    {
        return preg_replace("/[^0-9]/i", "", $string);
    }

    public static function menuSide ($userImg, $userName, $userOffice, $userType, $userPermissions, $module = "", $action = "")
    {
        $menuSide = array(
            "header" => array(
                "user-img" => STATIC_URL . "images/" . $userImg,
                "user-name" => $userName,
                "user-office" => $userOffice
            )
        );

        if ($module == "index") {
            if ($action == "main") {
                $dashboard = array(
                    "dashboard%active" => ADM_URL
                );
            } else {
                $dashboard = array(
                    "dashboard" => ADM_URL
                );
            }
        } else {
            $dashboard = array(
                "dashboard" => ADM_URL
            );
        }

        array_push($menuSide, $dashboard);

        $system = array(
            "Sistema" => array(
                "class-icon" => ICON_SYSTEM,
                "scd-level" => array()
            )
        );


        if ($userType <= 2) {
            $schools = array();

            if ($module == "school") {
                $system["Sistema"]["scd-level%in"] = $system["Sistema"]["scd-level"];
                unset($system["Sistema"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("school/create", $userPermissions)) {
                            $schools["Escolas%active"]["Novo%active"] = ADM_URL . "escola/novo";
                        }

                        if (in_array("school/list/full", $userPermissions) || 
                            in_array("school/list/partial", $userPermissions)) {
                            $schools["Escolas%active"]["Listar"] = ADM_URL . "escola/listar";
                        }
                        break;
                    }
                    case 'edit':{
                        if (in_array("school/create", $userPermissions)) {
                            $schools["Escolas%active"]["Novo"] = ADM_URL . "escola/novo";
                        }

                        if (in_array("school/list/full", $userPermissions) || 
                            in_array("school/list/partial", $userPermissions)) {
                            $schools["Escolas%active"]["Listar"] = ADM_URL . "escola/listar";
                        }
                        break;
                    }
                    case 'list':{
                        if (in_array("school/create", $userPermissions)) {
                            $schools["Escolas%active"]["Novo"] = ADM_URL . "escola/novo";
                        }

                        if (in_array("school/list/full", $userPermissions) || 
                            in_array("school/list/partial", $userPermissions)) {
                            $schools["Escolas%active"]["Listar%active"] = ADM_URL . "escola/listar";
                        }
                        break;
                    }
                }

                array_push($system["Sistema"]["scd-level%in"], $schools);
            } else {
                if (in_array("school/create", $userPermissions)) {
                    $schools["Escolas"]["Novo"] = ADM_URL . "escola/novo";
                }

                if (in_array("school/list/full", $userPermissions) || 
                    in_array("school/list/partial", $userPermissions)) {
                    $schools["Escolas"]["Listar"] = ADM_URL . "escola/listar";
                }

                array_push($system["Sistema"]["scd-level"], $schools);
            }
        }

        if ($userType <= 2) {
            $services = array(
                "Serviços" => array()
            );

            if ($module == "service") {
                $system["Sistema"]["scd-level%in"] = $system["Sistema"]["scd-level"];
                unset($system["Sistema"]["scd-level"]);

                switch ($action) {
                    case 'new':{       
                        if (in_array("service/create", $userPermissions)) {
                            $services["Serviços%active"]["Novo%active"] = ADM_URL . "servico/novo";
                        }

                        if (in_array("service/list/full", $userPermissions) || 
                            in_array("service/list/partial", $userPermissions)) {
                            $services["Serviços%active"]["Listar"] = ADM_URL . "servico/listar";
                        }

                        break;
                    }
                    case 'edit':{       
                        if (in_array("service/create", $userPermissions)) {
                            $services["Serviços%active"]["Novo"] = ADM_URL . "servico/novo";
                        }

                        if (in_array("service/list/full", $userPermissions) || 
                            in_array("service/list/partial", $userPermissions)) {
                            $services["Serviços%active"]["Listar"] = ADM_URL . "servico/listar";
                        }

                        break;
                    }
                    case 'list':{       
                        if (in_array("service/create", $userPermissions)) {
                            $services["Serviços%active"]["Novo"] = ADM_URL . "servico/novo";
                        }

                        if (in_array("service/list/full", $userPermissions) || 
                            in_array("service/list/partial", $userPermissions)) {
                            $services["Serviços%active"]["Listar%active"] = ADM_URL . "servico/listar";
                        }

                        break;
                    }
                }

                array_push($system["Sistema"]["scd-level%in"], $services);
            } else {
                if (in_array("service/create", $userPermissions)) {
                    $services["Serviços"]["Novo"] = ADM_URL . "servico/novo";
                }

                if (in_array("service/list/full", $userPermissions) || 
                    in_array("service/list/partial", $userPermissions)) {
                    $services["Serviços"]["Listar"] = ADM_URL . "servico/listar";
                }

                if (array_key_exists("scd-level%in", $system["Sistema"])) {
                    array_push($system["Sistema"]["scd-level%in"], $services);
                } else {
                    array_push($system["Sistema"]["scd-level"], $services);
                }
            }
        }

        $workOrders = array();

        if ($module == "workorder") {
            $system["Sistema"]["scd-level%in"] = $system["Sistema"]["scd-level"];
            unset($system["Sistema"]["scd-level"]);

            switch ($action) {
                case 'new':{
                    if (in_array("workorder/create", $userPermissions)) {
                        $workOrders["O.S.%active"]["Novo%active"] = ADM_URL . "ordem-de-servico/novo";
                    }

                    if (in_array("workorder/list/full", $userPermissions) || 
                        in_array("workorder/list/partial", $userPermissions)) {
                        $workOrders["O.S.%active"]["Listar"] = ADM_URL . "ordem-de-servico/listar";
                    }

                    break;
                }
                case 'edit':{
                    if (in_array("workorder/create", $userPermissions)) {
                        $workOrders["O.S.%active"]["Novo"] = ADM_URL . "ordem-de-servico/novo";
                    }

                    if (in_array("workorder/list/full", $userPermissions) || 
                        in_array("workorder/list/partial", $userPermissions)) {
                        $workOrders["O.S.%active"]["Listar"] = ADM_URL . "ordem-de-servico/listar";
                    }

                    break;
                }
                case 'list':{
                    if (in_array("workorder/create", $userPermissions)) {
                        $workOrders["O.S.%active"]["Novo"] = ADM_URL . "ordem-de-servico/novo";
                    }

                    if (in_array("workorder/list/full", $userPermissions) || 
                        in_array("workorder/list/partial", $userPermissions)) {
                        $workOrders["O.S.%active"]["Listar%active"] = ADM_URL . "ordem-de-servico/listar";
                    }

                    break;
                }
            }

            array_push($system["Sistema"]["scd-level%in"], $workOrders);
        } else {
            if (in_array("workorder/create", $userPermissions)) {
                $workOrders["O.S."]["Novo"] = ADM_URL . "ordem-de-servico/novo";
            }

            if (in_array("workorder/list/full", $userPermissions) || 
                in_array("workorder/list/partial", $userPermissions)) {
                $workOrders["O.S."]["Listar"] = ADM_URL . "ordem-de-servico/listar";
            }

            if (array_key_exists("scd-level%in", $system["Sistema"])) {
                array_push($system["Sistema"]["scd-level%in"], $workOrders);
            } else {
                array_push($system["Sistema"]["scd-level"], $workOrders);
            }
        }

        if (($userType <= "2")) {
            if ($module == "occurrence") {
                $system["Sistema"]["scd-level%in"] = $system["Sistema"]["scd-level"];
                unset($system["Sistema"]["scd-level"]);

                $system["Sistema"]["scd-level%in"]["Ocorrências%active"] = ADM_URL . "ocorrencia/consultar";
            } else {
                if (array_key_exists("scd-level%in", $system["Sistema"])) {
                    $system["Sistema"]["scd-level%in"]["Ocorrências"] = ADM_URL . "ocorrencia/consultar";
                } else {
                    $system["Sistema"]["scd-level"]["Ocorrências"] = ADM_URL . "ocorrencia/consultar";
                }
            }   
        }

        if ($userType <= "2") {
            $persons = array();

            if ($module == "person") {
                $system["Sistema"]["scd-level%in"] = $system["Sistema"]["scd-level"];
                unset($system["Sistema"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("person/create", $userPermissions)) {
                            $persons["Pessoas%active"]["Novo%active"] = ADM_URL . "pessoa/novo";
                        }

                        if (in_array("person/list/full", $userPermissions) || 
                            in_array("person/list/partial", $userPermissions)) {
                            $persons["Pessoas%active"]["Listar"] = ADM_URL . "pessoa/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("person/create", $userPermissions)) {
                            $persons["Pessoas%active"]["Novo"] = ADM_URL . "pessoa/novo";
                        }

                        if (in_array("person/list/full", $userPermissions) || 
                            in_array("person/list/partial", $userPermissions)) {
                            $persons["Pessoas%active"]["Listar"] = ADM_URL . "pessoa/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("person/create", $userPermissions)) {
                            $persons["Pessoas%active"]["Novo"] = ADM_URL . "pessoa/novo";
                        }

                        if (in_array("person/list/full", $userPermissions) || 
                            in_array("person/list/partial", $userPermissions)) {
                            $persons["Pessoas%active"]["Listar%active"] = ADM_URL . "pessoa/listar";
                        }

                        break;
                    }
                }

                array_push($system["Sistema"]["scd-level%in"], $persons);
            } else {
                if (in_array("person/create", $userPermissions)) {
                    $persons["Pessoas"]["Novo"] = ADM_URL . "pessoa/novo";
                }

                if (in_array("person/list/full", $userPermissions) || 
                    in_array("person/list/partial", $userPermissions)) {
                    $persons["Pessoas"]["Listar"] = ADM_URL . "pessoa/listar";
                }

                if (array_key_exists("scd-level%in", $system["Sistema"])) {
                    array_push($system["Sistema"]["scd-level%in"], $persons);
                } else {
                    array_push($system["Sistema"]["scd-level"], $persons);
                }
            }
        }

        if ($userType == "1") {
            $userActivities = array();

            if ($module == "useractivity") {
                $system["Sistema"]["scd-level%in"] = $system["Sistema"]["scd-level"];
                unset($system["Sistema"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("useractivity/create", $userPermissions)) {
                            $userActivities["Atividades de usuário%active"]["Novo%active"] = ADM_URL . "atividades-de-usuario/novo";
                        }

                        if (in_array("useractivity/list/full", $userPermissions) || 
                            in_array("useractivity/list/partial", $userPermissions)) {
                            $userActivities["Atividades de usuário%active"]["Listar"] = ADM_URL . "atividades-de-usuario/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("useractivity/create", $userPermissions)) {
                            $userActivities["Atividades de usuário%active"]["Novo"] = ADM_URL . "atividades-de-usuario/novo";
                        }

                        if (in_array("useractivity/list/full", $userPermissions) || 
                            in_array("useractivity/list/partial", $userPermissions)) {
                            $userActivities["Atividades de usuário%active"]["Listar"] = ADM_URL . "atividades-de-usuario/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("useractivity/create", $userPermissions)) {
                            $userActivities["Atividades de usuário%active"]["Novo"] = ADM_URL . "atividades-de-usuario/novo";
                        }

                        if (in_array("useractivity/list/full", $userPermissions) || 
                            in_array("useractivity/list/partial", $userPermissions)) {
                            $userActivities["Atividades de usuário%active"]["Listar%active"] = ADM_URL . "atividades-de-usuario/listar";
                        }

                        break;
                    }
                }
                array_push($system["Sistema"]["scd-level%in"], $userActivities);
            } else {
                if (in_array("useractivity/create", $userPermissions)) {
                    $userActivities["Atividades de usuário"]["Novo"] = ADM_URL . "atividades-de-usuario/novo";
                }

                if (in_array("useractivity/list/full", $userPermissions) || 
                    in_array("useractivity/list/partial", $userPermissions)) {
                    $userActivities["Atividades de usuário"]["Listar"] = ADM_URL . "atividades-de-usuario/listar";
                }

                if (array_key_exists("scd-level%in", $system["Sistema"])) {
                    array_push($system["Sistema"]["scd-level%in"], $userActivities);
                } else {
                    array_push($system["Sistema"]["scd-level"], $userActivities);
                }
            }
        }

        if ($userType == "1") {
            $userTypes = array();

            if ($module == "usertype") {
                $system["Sistema"]["scd-level%in"] = $system["Sistema"]["scd-level"];
                unset($system["Sistema"]["scd-level"]);

                switch ($action) {
                    case 'new': {
                        if (in_array("usertype/create", $userPermissions)) {
                            $userTypes["Tipos de usuário%active"]["Novo%active"] = ADM_URL . "tipos-de-usuario/novo";
                        }

                        if (in_array("usertype/list/full", $userPermissions) || 
                            in_array("usertype/list/partial", $userPermissions)) {
                            $userTypes["Tipos de usuário%active"]["Listar"] = ADM_URL . "tipos-de-usuario/listar";
                        }
                        
                        break;
                    }
                    case 'edit': {
                        if (in_array("usertype/create", $userPermissions)) {
                            $userTypes["Tipos de usuário%active"]["Novo"] = ADM_URL . "tipos-de-usuario/novo";
                        }

                        if (in_array("usertype/list/full", $userPermissions) || 
                            in_array("usertype/list/partial", $userPermissions)) {
                            $userTypes["Tipos de usuário%active"]["Listar"] = ADM_URL . "tipos-de-usuario/listar";
                        }
                        
                        break;
                    }
                    case 'list': {
                        if (in_array("usertype/create", $userPermissions)) {
                            $userTypes["Tipos de usuário%active"]["Novo"] = ADM_URL . "tipos-de-usuario/novo";
                        }

                        if (in_array("usertype/list/full", $userPermissions) || 
                            in_array("usertype/list/partial", $userPermissions)) {
                            $userTypes["Tipos de usuário%active"]["Listar%active"] = ADM_URL . "tipos-de-usuario/listar";
                        }

                        break;
                    }
                }

                array_push($system["Sistema"]["scd-level%in"], $userTypes);
            } else {
                if (in_array("usertype/create", $userPermissions)) {
                    $userTypes["Tipos de usuário"]["Novo"] = ADM_URL . "tipos-de-usuario/novo";
                }

                if (in_array("usertype/list/full", $userPermissions) || 
                    in_array("usertype/list/partial", $userPermissions)) {
                    $userTypes["Tipos de usuário"]["Listar"] = ADM_URL . "tipos-de-usuario/listar";
                }

                if (array_key_exists("scd-level%in", $system["Sistema"])) {
                    array_push($system["Sistema"]["scd-level%in"], $userTypes);
                } else {
                    array_push($system["Sistema"]["scd-level"], $userTypes);
                }
            }
        }

        if ($userType <= "2") {
            $users = array();

            if ($module == "user") {
                $system["Sistema"]["scd-level%in"] = $system["Sistema"]["scd-level"];
                unset($system["Sistema"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("user/create", $userPermissions)) {
                            $users["Usuários%active"]["Novo%active"] = ADM_URL . "usuario/novo";
                        }

                        if (in_array("user/list/full", $userPermissions) || 
                            in_array("user/list/partial", $userPermissions)) {
                            $users["Usuários%active"]["Listar"] = ADM_URL . "usuario/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("user/create", $userPermissions)) {
                            $users["Usuários%active"]["Novo"] = ADM_URL . "usuario/novo";
                        }

                        if (in_array("user/list/full", $userPermissions) || 
                            in_array("user/list/partial", $userPermissions)) {
                            $users["Usuários%active"]["Listar"] = ADM_URL . "usuario/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("user/create", $userPermissions)) {
                            $users["Usuários%active"]["Novo"] = ADM_URL . "usuario/novo";
                        }

                        if (in_array("user/list/full", $userPermissions) || 
                            in_array("user/list/partial", $userPermissions)) {
                            $users["Usuários%active"]["Listar%active"] = ADM_URL . "usuario/listar";
                        }

                        break;
                    }
                }

                array_push($system["Sistema"]["scd-level%in"], $users);
            } else {
                if (in_array("user/create", $userPermissions)) {
                    $users["Usuários"]["Novo"] = ADM_URL . "usuario/novo";
                }

                if (in_array("user/list/full", $userPermissions) || 
                    in_array("user/list/partial", $userPermissions)) {
                    $users["Usuários"]["Listar"] = ADM_URL . "usuario/listar";
                }

                if (array_key_exists("scd-level%in", $system["Sistema"])) {
                    array_push($system["Sistema"]["scd-level%in"], $users);
                } else {
                    array_push($system["Sistema"]["scd-level"], $users);
                }
            }
        }

        array_push($menuSide, $system);

        $financial = array(
            "Financeiro" => array(
                "class-icon" => ICON_FINANCIAL,
                "scd-level" => array()
            )
        );

        if ($userType == "1") {
            $payments = array();

            if ($module == "payment") {
                $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                unset($financial["Financeiro"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("payment/create", $userPermissions)) {
                            $payments["Formas de pagamento%active"]["Novo%active"] = ADM_URL . "forma-de-pagamento/novo";
                        }

                        if (in_array("payment/list/full", $userPermissions) || 
                            in_array("payment/list/partial", $userPermissions)) {
                            $payments["Formas de pagamento%active"]["Listar"] = ADM_URL . "forma-de-pagamento/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("payment/create", $userPermissions)) {
                            $payments["Formas de pagamento%active"]["Novo"] = ADM_URL . "forma-de-pagamento/novo";
                        }

                        if (in_array("payment/list/full", $userPermissions) || 
                            in_array("payment/list/partial", $userPermissions)) {
                            $payments["Formas de pagamento%active"]["Listar"] = ADM_URL . "forma-de-pagamento/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("payment/create", $userPermissions)) {
                            $payments["Formas de pagamento%active"]["Novo"] = ADM_URL . "forma-de-pagamento/novo";
                        }

                        if (in_array("payment/list/full", $userPermissions) || 
                            in_array("payment/list/partial", $userPermissions)) {
                            $payments["Formas de pagamento%active"]["Listar%active"] = ADM_URL . "forma-de-pagamento/listar";
                        }

                        break;
                    }
                }

                array_push($financial["Financeiro"]["scd-level%in"], $payments);
            } else {
                if (in_array("payment/create", $userPermissions)) {
                    $payments["Formas de pagamento"]["Novo"] = ADM_URL . "forma-de-pagamento/novo";
                }

                if (in_array("payment/list/full", $userPermissions) || 
                    in_array("payment/list/partial", $userPermissions)) {
                    $payments["Formas de pagamento"]["Listar"] = ADM_URL . "forma-de-pagamento/listar";
                }

                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    array_push($financial["Financeiro"]["scd-level%in"], $payments);
                } else {
                    array_push($financial["Financeiro"]["scd-level"], $payments);
                }
            }
        }

        if ($userType <= "2") {
            $accounts = array();

            if ($module == "account") {
                $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                unset($financial["Financeiro"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("account/create", $userPermissions)) {
                            $accounts["Contas%active"]["Novo%active"] = ADM_URL . "conta/novo";
                        }

                        if (in_array("account/list/full", $userPermissions) || 
                            in_array("account/list/partial", $userPermissions)) {
                            $accounts["Contas%active"]["Listar"] = ADM_URL . "conta/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("account/create", $userPermissions)) {
                            $accounts["Contas%active"]["Novo"] = ADM_URL . "conta/novo";
                        }

                        if (in_array("account/list/full", $userPermissions) || 
                            in_array("account/list/partial", $userPermissions)) {
                            $accounts["Contas%active"]["Listar"] = ADM_URL . "conta/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("account/create", $userPermissions)) {
                            $accounts["Contas%active"]["Novo"] = ADM_URL . "conta/novo";
                        }

                        if (in_array("account/list/full", $userPermissions) || 
                            in_array("account/list/partial", $userPermissions)) {
                            $accounts["Contas%active"]["Listar%active"] = ADM_URL . "conta/listar";
                        }

                        break;
                    }
                }

                array_push($financial["Financeiro"]["scd-level%in"], $accounts);
            } else {
                if (in_array("account/create", $userPermissions)) {
                    $accounts["Contas"]["Novo"] = ADM_URL . "conta/novo";
                }

                if (in_array("account/list/full", $userPermissions) || 
                    in_array("account/list/partial", $userPermissions)) {
                    $accounts["Contas"]["Listar"] = ADM_URL . "conta/listar";
                }

                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    array_push($financial["Financeiro"]["scd-level%in"], $accounts);
                } else {
                    array_push($financial["Financeiro"]["scd-level"], $accounts);
                }
            }
        }

        if ($userType == "1") {
            $transactionTypes = array();

            if ($module == "transactiontype") {
                $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                unset($financial["Financeiro"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("transactiontype/create", $userPermissions)) {
                            $transactionTypes["Tipos de transações%active"]["Novo%active"] = ADM_URL . "tipo-de-transacao/novo";
                        }

                        if (in_array("transactiontype/list/full", $userPermissions) || 
                            in_array("transactiontype/list/partial", $userPermissions)) {
                            $transactionTypes["Tipos de transações%active"]["Listar"] = ADM_URL . "tipo-de-transacao/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("transactiontype/create", $userPermissions)) {
                            $transactionTypes["Tipos de transações%active"]["Novo"] = ADM_URL . "tipo-de-transacao/novo";
                        }

                        if (in_array("transactiontype/list/full", $userPermissions) || 
                            in_array("transactiontype/list/partial", $userPermissions)) {
                            $transactionTypes["Tipos de transações%active"]["Listar"] = ADM_URL . "tipo-de-transacao/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("transactiontype/create", $userPermissions)) {
                            $transactionTypes["Tipos de transações%active"]["Novo"] = ADM_URL . "tipo-de-transacao/novo";
                        }

                        if (in_array("transactiontype/list/full", $userPermissions) || 
                            in_array("transactiontype/list/partial", $userPermissions)) {
                            $transactionTypes["Tipos de transações%active"]["Listar%active"] = ADM_URL . "tipo-de-transacao/listar";
                        }

                        break;
                    }
                }

                array_push($financial["Financeiro"]["scd-level%in"], $transactionTypes);
            } else {
                if (in_array("transactiontype/create", $userPermissions)) {
                    $transactionTypes["Tipos de transações"]["Novo"] = ADM_URL . "tipo-de-transacao/novo";
                }

                if (in_array("transactiontype/list/full", $userPermissions) || 
                    in_array("transactiontype/list/partial", $userPermissions)) {
                    $transactionTypes["Tipos de transações"]["Listar"] = ADM_URL . "tipo-de-transacao/listar";
                }

                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    array_push($financial["Financeiro"]["scd-level%in"], $transactionTypes);
                } else {
                    array_push($financial["Financeiro"]["scd-level"], $transactionTypes);
                }
            }
        }

        if ($userType == "1") {
            $transactionCategories = array();

            if ($module == "transactioncategory") {
                $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                unset($financial["Financeiro"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("transactioncategory/create", $userPermissions)) {
                            $transactionCategories["Categorias de transações%active"]["Novo%active"] = ADM_URL . "categoria-de-transacao/novo";
                        }

                        if (in_array("transactioncategory/list/full", $userPermissions) || 
                            in_array("transactioncategory/list/partial", $userPermissions)) {
                            $transactionCategories["Categorias de transações%active"]["Listar"] = ADM_URL . "categoria-de-transacao/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("transactioncategory/create", $userPermissions)) {
                            $transactionCategories["Categorias de transações%active"]["Novo"] = ADM_URL . "categoria-de-transacao/novo";
                        }

                        if (in_array("transactioncategory/list/full", $userPermissions) || 
                            in_array("transactioncategory/list/partial", $userPermissions)) {
                            $transactionCategories["Categorias de transações%active"]["Listar"] = ADM_URL . "categoria-de-transacao/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("transactioncategory/create", $userPermissions)) {
                            $transactionCategories["Categorias de transações%active"]["Novo"] = ADM_URL . "categoria-de-transacao/novo";
                        }

                        if (in_array("transactioncategory/list/full", $userPermissions) || 
                            in_array("transactioncategory/list/partial", $userPermissions)) {
                            $transactionCategories["Categorias de transações%active"]["Listar%active"] = ADM_URL . "categoria-de-transacao/listar";
                        }

                        break;
                    }
                }

                array_push($financial["Financeiro"]["scd-level%in"], $transactionCategories);
            } else {
                if (in_array("transactioncategory/create", $userPermissions)) {
                    $transactionCategories["Categorias de transações"]["Novo"] = ADM_URL . "categoria-de-transacao/novo";
                }

                if (in_array("transactioncategory/list/full", $userPermissions) || 
                    in_array("transactioncategory/list/partial", $userPermissions)) {
                    $transactionCategories["Categorias de transações"]["Listar"] = ADM_URL . "categoria-de-transacao/listar";
                }

                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    array_push($financial["Financeiro"]["scd-level%in"], $transactionCategories);
                } else {
                    array_push($financial["Financeiro"]["scd-level"], $transactionCategories);
                }
            }
        }

        if ($userType <= "2") {
            $transactionSubcategories = array();

            if ($module == "transactionsubcategory") {
                $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                unset($financial["Financeiro"]["scd-level"]);

                switch ($action) {
                    case 'new':{
                        if (in_array("transactionsubcategory/create", $userPermissions)) {
                            $transactionSubcategories["Subcategorias de transações%active"]["Novo%active"] = ADM_URL . "subcategoria-de-transacao/novo";
                        }

                        if (in_array("transactionsubcategory/list/full", $userPermissions) || 
                            in_array("transactionsubcategory/list/partial", $userPermissions)) {
                            $transactionSubcategories["Subcategorias de transações%active"]["Listar"] = ADM_URL . "subcategoria-de-transacao/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("transactionsubcategory/create", $userPermissions)) {
                            $transactionSubcategories["Subcategorias de transações%active"]["Novo"] = ADM_URL . "subcategoria-de-transacao/novo";
                        }

                        if (in_array("transactionsubcategory/list/full", $userPermissions) || 
                            in_array("transactionsubcategory/list/partial", $userPermissions)) {
                            $transactionSubcategories["Subcategorias de transações%active"]["Listar"] = ADM_URL . "subcategoria-de-transacao/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("transactionsubcategory/create", $userPermissions)) {
                            $transactionSubcategories["Subcategorias de transações%active"]["Novo"] = ADM_URL . "subcategoria-de-transacao/novo";
                        }

                        if (in_array("transactionsubcategory/list/full", $userPermissions) || 
                            in_array("transactionsubcategory/list/partial", $userPermissions)) {
                            $transactionSubcategories["Subcategorias de transações%active"]["Listar%active"] = ADM_URL . "subcategoria-de-transacao/listar";
                        }

                        break;
                    }
                }

                array_push($financial["Financeiro"]["scd-level%in"], $transactionSubcategories);
            } else {
                if (in_array("transactionsubcategory/create", $userPermissions)) {
                    $transactionSubcategories["Subcategorias de transações"]["Novo"] = ADM_URL . "subcategoria-de-transacao/novo";
                }

                if (in_array("transactionsubcategory/list/full", $userPermissions) || 
                    in_array("transactionsubcategory/list/partial", $userPermissions)) {
                    $transactionSubcategories["Subcategorias de transações"]["Listar"] = ADM_URL . "subcategoria-de-transacao/listar";
                }

                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    array_push($financial["Financeiro"]["scd-level%in"], $transactionSubcategories);
                } else {
                    array_push($financial["Financeiro"]["scd-level"], $transactionSubcategories);
                }
            }
        }

        if (($userType <= "2") || ($userType == "4")) {
            $transactions = array();

            if ($module == "transaction") {
                $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                unset($financial["Financeiro"]["scd-level"]);
                
                switch ($action) {
                    case 'new':{
                        if (in_array("transaction/create", $userPermissions)) {
                            $transactions["Transações%active"]["Novo%active"] = ADM_URL . "transacao/novo";
                        }

                        if (in_array("transaction/list/full", $userPermissions) || 
                            in_array("transaction/list/partial", $userPermissions)) {
                            $transactions["Transações%active"]["Listar"] = ADM_URL . "transacao/listar";
                        }

                        break;
                    }
                    case 'edit':{
                        if (in_array("transaction/create", $userPermissions)) {
                            $transactions["Transações%active"]["Novo"] = ADM_URL . "transacao/novo";
                        }

                        if (in_array("transaction/list/full", $userPermissions) || 
                            in_array("transaction/list/partial", $userPermissions)) {
                            $transactions["Transações%active"]["Listar"] = ADM_URL . "transacao/listar";
                        }

                        break;
                    }
                    case 'list':{
                        if (in_array("transaction/create", $userPermissions)) {
                            $transactions["Transações%active"]["Novo"] = ADM_URL . "transacao/novo";
                        }

                        if (in_array("transaction/list/full", $userPermissions) || 
                            in_array("transaction/list/partial", $userPermissions)) {
                            $transactions["Transações%active"]["Listar%active"] = ADM_URL . "transacao/listar";
                        }

                        break;
                    }
                }

                array_push($financial["Financeiro"]["scd-level%in"], $transactions);
            } else {
                if (in_array("transaction/create", $userPermissions)) {
                    $transactions["Transações"]["Novo"] = ADM_URL . "transacao/novo";
                }

                if (in_array("transaction/list/full", $userPermissions) || 
                    in_array("transaction/list/partial", $userPermissions)) {
                    $transactions["Transações"]["Listar"] = ADM_URL . "transacao/listar";
                }

                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    array_push($financial["Financeiro"]["scd-level%in"], $transactions);
                } else {
                    array_push($financial["Financeiro"]["scd-level"], $transactions);
                }
            }
        }

        if (($userType <= "2") || ($userType == "4")) {
            if ($module == "cashflow") {
                $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                unset($financial["Financeiro"]["scd-level"]);

                $financial["Financeiro"]["scd-level%in"]["Fluxo de caixa%active"] = ADM_URL . "fluxo-de-caixa";
            } else {
                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    $financial["Financeiro"]["scd-level%in"]["Fluxo de caixa"] = ADM_URL . "fluxo-de-caixa";
                } else {
                    $financial["Financeiro"]["scd-level"]["Fluxo de caixa"] = ADM_URL . "fluxo-de-caixa";
                }
            }
        }

        if ($userType <= "2") {
            if ($module == "report") {
                $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                unset($financial["Financeiro"]["scd-level"]);

                $financial["Financeiro"]["scd-level%in"]["Relatórios%active"] = ADM_URL . "relatorio";
            } else {
                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    $financial["Financeiro"]["scd-level%in"]["Relatórios"] = ADM_URL . "relatorio";
                } else {
                    $financial["Financeiro"]["scd-level"]["Relatórios"] = ADM_URL . "relatorio";
                }
            }
        }

        if ($userType <= "2") {
            if ($module == "index") {
                if ($action == "import") {
                    $financial["Financeiro"]["scd-level%in"] = $financial["Financeiro"]["scd-level"];
                    unset($financial["Financeiro"]["scd-level"]);

                    $financial["Financeiro"]["scd-level%in"]["Importar%active"] = ADM_URL . "importar";
                } else {
                    if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                        $financial["Financeiro"]["scd-level%in"]["Importar"] = ADM_URL . "importar";
                    } else {
                        $financial["Financeiro"]["scd-level"]["Importar"] = ADM_URL . "importar";
                    }
                }
            } else {
                if (array_key_exists("scd-level%in", $financial["Financeiro"])) {
                    $financial["Financeiro"]["scd-level%in"]["Importar"] = ADM_URL . "importar";
                } else {
                    $financial["Financeiro"]["scd-level"]["Importar"] = ADM_URL . "importar";
                }
            }
        }

        if ($userType !== "3") {
            array_push($menuSide, $financial);
        }

        return $menuSide;
    }

    /*public static function newThumb($w, $h, $orgSrc, $dstSrc)
    {
        $image_measure = 80;
        $icon_measure = 20;

        $icon = imagecreatefrompng($orgSrc);
        $image = imagecreatetruecolor($image_measure, $image_measure);

        $gray = imagecolorallocate($image, 106, 105, 113);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagecolortransparent($image, $black);

        $rec_dimensions = self::resize(
            $image_measure, 
            $image_measure, 
            $w, 
            $h
        );
        
        $rec_coords = self::centralize(
            $image_measure, 
            $image_measure, 
            $rec_dimensions['width'], 
            $rec_dimensions['height']
        );

        imagefilledrectangle(
            $image, 
            $rec_coords['x'], 
            $rec_coords['y'], 
            $rec_coords['x'] + $rec_dimensions['width'], 
            $rec_coords['y'] + $rec_dimensions['height'], 
            $gray
        );

        $iconSize = getimagesize($orgSrc);
        $icon_dimensions = self::resize(
            $icon_measure, 
            $icon_measure, 
            $iconSize[1], 
            $iconSize[0]
        );
        
        self::centralize(
            $icon_measure, 
            $icon_measure, 
            $icon_dimensions['width'], 
            $icon_dimensions['height']
        );

        $icon_coords = self::centralize(
            $image_measure, 
            $image_measure, 
            $icon_measure, 
            $icon_measure
        );

        imagecopyresampled(
            $image, 
            $icon, 
            $icon_coords['x'], 
            $icon_coords['y'], 
            0, 
            0, 
            $icon_measure, 
            $icon_measure, 
            $iconSize[1], 
            $iconSize[0]
        );

        imagepng($image, $dstSrc, 0);
        imagedestroy($image);
    }
    
    protected static function resize($dst_width, $dst_height, $src_width, $src_height)
    {
        if ($src_width >= $src_height) {
            $prop = ($dst_width / $src_width);
    	} else {
                $prop = ($dst_height / $src_height);
    	}
            
    	$src_width = $prop * $src_width;
    	$src_height = $prop * $src_height;
            
    	return array("width" => $src_width, "height" => $src_height);
    }

    public static function resize($dst, $x, $y, $h, $w, $width = 150, $height = 150)
    {
        $size = getimagesize($dst);

        $thumb = imagecreatetruecolor($width, $height);

        $src = imagecreatefromjpeg($dst);

        imagecopyresampled($thumb, $src, 0, 0, $x, $y, $width, $height, $w, $h);

        imagejpeg($thumb, $dst);

        imagedestroy($thumb);
    }
    
    protected static function centralize($dst_width, $dst_height, $src_width, $src_height)
    {
        $x = ($dst_width / 2) - ($src_width / 2);
    	$y = ($dst_height / 2) - ($src_height / 2);
            
    	return array('x' => $x, 'y' => $y);
    }*/

    /**
     *
     * @method bool|array upload(array $file, string $dst, bool $returnName) Faz upload de uma imagem para o servidor.
     */

    public static function upload($file, $dst, $returnName = true)
    {
        $fileEx = explode(".", $file["name"]);
        $ext 	= array_pop($fileEx);
	
        $types = array(
            "image/jpg",
            "image/gif",
            "image/png",
            "image/jpeg",
            "image/x-png",
            "image/p-jpeg"
        );

        if (!empty($file["error"])) {
            switch ($file["error"]) {
                case "UPLOAD_ERR_INI_SIZE":
                    return false;
                case "UPLOAD_ERR_FORM_SIZE":
                    return false;
                case "UPLOAD_ERR_PARTIAL":
                    return false;
                case "UPLOAD_ERR_NO_TMP_DIR":
                    return false;
                case "UPLOAD_ERR_CANT_WRITE":
                    return false;
                case "UPLOAD_ERR_EXTENSION":
                    return false;
                default:
                    return false;
            }
        } elseif (!is_uploaded_file($file["tmp_name"])) {
            return array("msg" => "Possível ataque de upload de arquivo.");
        } elseif (!in_array($file["type"], $types)) {
            return array("msg" => "Tipo de arquivo não suportado.");
        } elseif (filesize($file["tmp_name"]) > MAX_UPLOAD_SIZE * 1024) {
            return array("msg" => "O arquivo excedeu o tamanho máximo permitido.");
        } elseif (!getimagesize($file["tmp_name"])) {
            return array("msg" => "O arquivo upado não é uma imagem.");
        } elseif (!preg_match("/^[jpg|png|jpeg|gif]{3,4}$/", $ext)) {
            return array("msg" => "A extensão do arquivo não é permitida.");
        } else {
            $filename = uniqid(time()) . "." . $ext;

            if (move_uploaded_file($file["tmp_name"], $dst . $filename)) {
                if ($returnName) {
                    return $filename;
                } else {
                    return true;
                }
            } else{
                return array("msg" => "Houve um erro ao fazer upload do arquivo, por favor, tente novamente.");
            }
        }
    }

    /**
     *
     * @method void newFile(string $path, string $html, string $css) Cria um arquivo com código HTML + CSS.
     */
    
    public static function newFile($path, $html = "", $css = "")
    {
        $code = "<style>"
              . $css
              . "</style>"
              . $html;
        
        $file = fopen($path, "w");
        fwrite($file, $code);
        fclose($file);
    }

    public static function getKeyFromUrlYoutube($url)
    {
        $itens = parse_url ($url);
        parse_str($itens['query'], $params);

        return $params["v"];
    }

    public static function validateCPF($cpf) {
        if(empty($cpf)) {
            return false;
        }

        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) != 11) {
            return false;
        } else if (
            $cpf == '00000000000' || $cpf == '11111111111' || 
            $cpf == '22222222222' || $cpf == '33333333333' || 
            $cpf == '44444444444' || $cpf == '55555555555' || 
            $cpf == '66666666666' || $cpf == '77777777777' || 
            $cpf == '88888888888' || $cpf == '99999999999'
        ) {
            return false;
        } else {
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;
                
                if ($cpf{$c} != $d) {
                    return false;
                }
            }
     
            return true;
        }
    }
}
