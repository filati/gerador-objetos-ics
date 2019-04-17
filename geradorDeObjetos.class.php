<?php

/**
 * Class geradorDeObjetos para sistema TEX/ICS;
 *
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Nox Works <www.noxworks.com.br>
 */
class geradorDeObjetos
{

    /** @var string */
    private $dominio;    //Nome do dominio a qual pertente o objeto

    /** @var string */
    private $varObjeto;  //Nome do Objeto para variaveis

    /** @var string */
    private $objeto;     // Nome do objeto;

    /** @var string */
    private $table;

    /** @var array */
    private $tipos = [
        'Collection',
        'Entity',
        'Mapper',
        'Service',
        'ServiceFactory',
        'ServiceInterface',
        'Repository',
        'RepositoryFactory',
        'RepositoryInterface'
    ];

    /**
     * geradorDeObjetos constructor.
     *
     * @param string $dominio
     * @param string $objeto
     */
    public function __construct($dominio, $objeto, $table)
    {
        $this->dominio = $dominio;
        $this->objeto = ucfirst($objeto);
        $this->varObjeto = strtolower(substr($objeto, 0, 1)) . substr($objeto, 1);
        $this->table = $table;
    }

    /**
     * Cria um objeto vazio.
     *
     * @param string $dominio
     * @param string $pasta
     * @param string $objeto
     * @param string $tipo
     * @param string $codigo
     * @param bool $mostrarObjetos
     * @return string
     */
    public function fabricarObjetoVazio($tipo, $codigo, $mostrarObjetos = false)
    {
        $objeto = $this->objeto;
        $pasta = $this->dominio . '/';

        switch (true) {
            case (strpos($tipo, 'Entity') !== false):
            case (strpos($tipo, 'Collection') !== false):
                $pasta .= 'Entity/';
                break;
            case (strpos($tipo, 'Repository') !== false):
                $pasta .= 'Repository/';
                break;
            case (strpos($tipo, 'Mapper') !== false):
            case (strpos($tipo, 'Service') !== false):
                $pasta .= 'Service/';
                break;
        }

        $codigo = str_replace(
            ['#dominio#', '#objeto#', '#varObjeto#'],
            [$this->dominio, $this->objeto, $this->varObjeto],
            $codigo
        );

        $conteudoEntity = $this->gerarAtributosEntidade();

        //Atributos da Enttity
        $codigo = str_replace('#conteudoEntity#', $conteudoEntity, $codigo);

        //Mappers
        $mappers = $this->gerarDeParaMapper();
        $codigo = str_replace('#mappers#', $mappers, $codigo);

        $retorno = "<label>{$pasta}{$objeto}{$tipo}.php</label><br>\n";
        if ($mostrarObjetos) {
            $retorno .= "\n
        <textarea style=\"width: 100%;height: 200px;\">
        {$codigo}\n
        </textarea><br><br>\n";
        }
        if (!file_exists($pasta)) {
            mkdir($pasta, 0777, true);
        }
        file_put_contents("{$pasta}{$objeto}{$tipo}.php", $codigo);

        return $retorno;
    }


    /**
     * Cria um trecho de código de como o `ConfigProvider` deve ficar.
     *
     * @param string $dominio
     * @param string $objeto
     * @return string
     */
    function criarTrechoConfigProvider()
    {
        $dominio = $this->dominio;
        $objeto = $this->objeto;

        $configProvider = "
    private function getServiceManagerDependecias()
    {
            ...
            
            'invokables' => [
                // service name => class name pairs
                // Entidades
                \TexICS\#dominio#\Entity\#objeto#Entity::class => \TexICS\#dominio#\Entity\#objeto#Entity::class,

                // Mappers
                \TexICS\#dominio#\Service\#objeto#Mapper::class => \TexICS\#dominio#\Service\#objeto#Mapper::class,
                
                // Collections
                \TexICS\#dominio#\Entity\#objeto#Collection::class => \TexICS\#dominio#\Entity\#objeto#Collection::class,
            ],
            'factories' => [
                // service name => factory pairs
                \TexICS\#dominio#\Service\#objeto#ServiceInterface::class => \TexICS\#dominio#\Service\#objeto#ServiceFactory::class,
                \TexICS\#dominio#\Repository\#objeto#RepositoryInterface::class => \TexICS\#dominio#\Repository\#objeto#RepositoryFactory::class,
            ],
            
            ...
    }
";
        $configProvider = str_replace(
            ['#dominio#', '#objeto#'],
            [$dominio, $objeto],
            $configProvider
        );

        return "<label>{$dominio}/ConfigProvider.php</label>\n
        <textarea style=\"width: 100%;height: 500px;\">
        {$configProvider}\n
        </textarea><br><br>\n";
    }

    /**
     *
     *
     * @return array
     */

    /**
     * Retorna codigo de um tipo determinado. (tipo = Entity, Factory, Collection, Mapper etc);
     *
     * @param string $tipo
     * @return string
     */
    public function obterCodigoPorTipo($tipo)
    {

        $dominio = $this->dominio;
        $objeto = $this->objeto;

        $codigos = [];

        $codigos['Entity'] = "<?php

namespace TexICS\#dominio#\Entity;

use TexICS\Common\Entity\DefaultEntityAbstract;

/**
 * Class #objeto#Entity
 *
 * @package TexICS\#dominio#\Entity
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
class #objeto#Entity extends DefaultEntityAbstract
{
#conteudoEntity#
}";

        $codigos['Collection'] = "<?php

namespace TexICS\#dominio#\Entity;


use TexICS\Common\Entity\DefaultCollectionAbstract;
use TexICS\Common\Entity\DefaultCollectionInterface;

/**
 * Class #objeto#Collection
 * 
 * @package TexICS\#dominio#\Entity
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
class #objeto#Collection extends DefaultCollectionAbstract implements DefaultCollectionInterface
{

    /**
     * @param #objeto#Entity \$#varObjeto#Entidade
     * @return #objeto#Collection
     */
    public function add(\$#varObjeto#Entidade)
    {
        if (!(\$#varObjeto#Entidade instanceof #objeto#Entity)) {
            throw new \InvalidArgumentException('Entidade inválida em ' . __METHOD__);
        }

        \$this->colecao[\"{\$#varObjeto#Entidade->getId()}\"] = \$#varObjeto#Entidade;
        return \$this;
    }

    /**
     * @param #objeto#Entity \$#varObjeto#Entidade
     */
    public function remove(\$#varObjeto#Entidade)
    {
        if (!(\$#varObjeto#Entidade instanceof #objeto#Entity)) {
            throw new \InvalidArgumentException('Entidade inválida em ' . __METHOD__);
        }

        unset(\$this->colecao[\"{\$#varObjeto#Entidade->getId()}\"]);
    }
}";

        $codigos['ServiceInterface'] = "<?php

namespace TexICS\#dominio#\Service;

/**
 * Interface #objeto#ServiceInterface
 * 
 * @package TexICS\#dominio#\Service
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
interface #objeto#ServiceInterface
{

}
";

        $codigos['Service'] = "<?php

namespace TexICS\#dominio#\Service;

use TexICS\#dominio#\Entity\#objeto#Collection;
use TexICS\#dominio#\Entity\#objeto#Entity;
use TexICS\#dominio#\Repository\#objeto#RepositoryInterface;

/**
 * Class #objeto#Service
 *
 * @package TexICS\#dominio#\Service
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
class #objeto#Service implements #objeto#ServiceInterface
{

    /**
     * #objeto#Service constructor.

     * @param #objeto#RepositoryInterface \$#varObjeto#Repositorio
     */
    public function __construct(
        #objeto#RepositoryInterface \$#varObjeto#Repositorio 
    ) {
        \$this->#varObjeto#Repositorio = \$#varObjeto#Repositorio;
    }

}";

        $codigos['ServiceFactory'] = "<?php

namespace TexICS\#dominio#\Service;

use Interop\Container\ContainerInterface;
use TexICS\#dominio#\Repository\#objeto#RepositoryInterface;

/**
 * Class #objeto#ServiceFactory
 *
 * @package TexICS\#dominio#\Service
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
class #objeto#ServiceFactory
{

    /**
     * @param ContainerInterface \$container
     * @return #objeto#Service
     */
    public function __invoke(ContainerInterface \$container)
    {

        /** @var #objeto#RepositoryInterface \$#varObjeto#Repositorio */
        \$#varObjeto#Repositorio = \$container->get(#objeto#RepositoryInterface::class);

        return new #objeto#Service(
            \$#varObjeto#Repositorio
        );
    }
}";

        $codigos['Mapper'] = "<?php

namespace TexICS\#dominio#\Service;

use TexICS\Common\Mapper\DefaultMapperAbstract;

/**
 * Class #objeto#Mapper
 * 
 * @package TexICS\#dominio#\Service
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
class #objeto#Mapper extends DefaultMapperAbstract
{

    /**
     * @var array
     * @code
     * [
     *      'chaveOriginal' => 'chaveMapeada',
     * ];
     * @endcode
     */
    protected \$mapa = #mappers#;
}";

        $codigos['RepositoryInterface'] = "<?php

namespace TexICS\#dominio#\Repository;

use TexICS\#dominio#\Entity\#objeto#Collection;
use TexICS\#dominio#\Entity\#objeto#Entity;

/**
 * Interface #objeto#RepositoryInterface
 *
 * @package TexICS\#dominio#\Repository
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
interface #objeto#RepositoryInterface
{

}";

        $codigos['RepositoryFactory'] = "<?php

namespace TexICS\#dominio#\Repository;

use Interop\Container\ContainerInterface;
use TexICS\Common\Service\DataBaseServiceInterface;
use TexICS\#dominio#\Entity\#objeto#Collection;
use TexICS\#dominio#\Entity\#objeto#Entity;
use TexICS\#dominio#\Service\#objeto#Mapper;

/**
 * Class #objeto#RepositoryFactory
 * 
 * @package TexICS\#dominio#\Repository
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
class #objeto#RepositoryFactory
{

    /**
     * @param ContainerInterface \$container
     * @return #objeto#Repository
     */
    public function __invoke(ContainerInterface \$container)
    {
        /** @var DataBaseServiceInterface \$dataBase */
        \$dataBase = \$container->get(DataBaseServiceInterface::class);

        /** @var  #objeto#Mapper \$#varObjeto#Mapeamento */
        \$#varObjeto#Mapeamento = \$container->get(#objeto#Mapper::class);

        /** @var  #objeto#Entity $#varObjeto#Entidade */
        \$#varObjeto#Entidade = \$container->get(#objeto#Entity::class);

        /** @var #objeto#Collection $#varObjeto#Colecao */
        \$#varObjeto#Colecao = \$container->get(#objeto#Collection::class);

        return new #objeto#Repository(
            \$dataBase,
            \$#varObjeto#Mapeamento,
            \$#varObjeto#Entidade,
            \$#varObjeto#Colecao
        );
    }
}";

        $codigos['Repository'] = "<?php

namespace TexICS\#dominio#\Repository;

use TexICS\Common\Service\DataBaseServiceInterface;
use TexICS\#dominio#\Entity\#objeto#Collection;
use TexICS\#dominio#\Entity\#objeto#Entity;
use TexICS\#dominio#\Service\#objeto#Mapper;

/**
 * Class #objeto#Repository

 * @package TexICS\#dominio#\Repository
 * @author Fabio Fila <fila@noxworks.com.br>
 * @copyright Total Express <www.totalexpress.com.br>
 */
class #objeto#Repository implements #objeto#RepositoryInterface
{

    /** @var DataBaseServiceInterface */
    private \$dataBase;

    /** @var #objeto#Mapper */
    private \$#varObjeto#Mapeamento;

    /** @var #objeto#Entity */
    private \$#varObjeto#Entidade;

    /** @var #objeto#Collection */
    private \$#varObjeto#Colecao;

    /**
     * #objeto#Repository constructor.
     *
     * @param DataBaseServiceInterface \$dataBase
     * @param #objeto#Mapper \$#varObjeto#Mapper
     * @param #objeto#Entity \$#varObjeto#Entidade
     * @param #objeto#Collection \$#varObjeto#Colecao
     */
    public function __construct(
        DataBaseServiceInterface \$dataBase,
        #objeto#Mapper \$#varObjeto#Mapper,
        #objeto#Entity \$#varObjeto#Entidade,
        #objeto#Collection \$#varObjeto#Colecao
    ) {
        \$this->dataBase = \$dataBase;
        \$this->#varObjeto#Mapeamento = \$#varObjeto#Mapper;
        \$this->#varObjeto#Entidade = \$#varObjeto#Entidade;
        \$this->#varObjeto#Colecao = \$#varObjeto#Colecao;
    }

}";
        return $codigos[$tipo];
    }

    public function obterTipos()
    {
        return $this->tipos;
    }

    public function gerarDeParaMapper()
    {
        if (empty($this->table)) {
            return '[]';
        }

        global $servicoContainer;

        /** @var \TexICS\Common\Service\DataBaseServiceInterface $dbCorrier */
        $dbCorrier = $servicoContainer->get(\TexICS\Common\Service\DataBaseServiceInterface::class);

        $dbRelat = $dbCorrier->getDbRelat();

        $res = $dbRelat->Execute('DESC ' . $this->table);

        $mapper = '';
        if ($dbRelat->NumRows($res) >= 1) {
            $row = $dbRelat->FetchAssoc($res);
            $mapper = "[\n";
            while ($row) {
                $nomeAtributo = ($row['Key'] == 'PRI') ? 'id' : $row['Field'];
                $posUnderline = strpos($nomeAtributo, '_');
                if ($posUnderline !== false) {
                    $parts = explode('_', $nomeAtributo);
                    $nomeAtributo = '';
                    foreach ($parts as $part) {
                        $nomeAtributo .= (empty($nomeAtributo)) ? $part : ucfirst($part);
                    }
                }

                $mapper .= "        '{$row['Field']}' => '{$nomeAtributo}',\n";

                $row = $dbRelat->FetchAssoc($res);
            }
            $mapper .= "    ];\n";
        }

        return $mapper;
    }

    /**
     * @param string $table
     * @return string
     */
    private function gerarAtributosEntidade()
    {

        if (empty($this->table)) {
            return;
        }

        global $servicoContainer;

        /** @var \TexICS\Common\Service\DataBaseServiceInterface $dbCorrier */
        $dbCorrier = $servicoContainer->get(\TexICS\Common\Service\DataBaseServiceInterface::class);

        $dbRelat = $dbCorrier->getDbRelat();

        $res = $dbRelat->Execute('DESC ' . $this->table);

        if ($dbRelat->NumRows($res) >= 1) {
            $row = $dbRelat->FetchAssoc($res);

            //Trata Tipo do campo
            $tipo = '';

            $codeAtributo = "    /** @var #tipo# */\n    private $#nomeAtributo#;";
            $codeGet = '    /**
     * @return #tipo#
     */
    public function get#nomeAtributoUcFirst#()
    {
        return $this->#nomeAtributo#;
    }';
            $codeSet = '    /**
     * @param #tipo# $#nomeAtributo#
     * @return #objeto#Entity
     */
    public function set#nomeAtributoUcFirst#($#nomeAtributo#)
    {
        $this->#nomeAtributo# = $#nomeAtributo#;
        return $this;
    }';

            $atributos = $gettersSetters = '';
            while ($row) {
                switch (true) {
                    case (strpos($row['Type'], 'int') !== false):
                        $tipo = 'integer';
                        break;
                    case (strpos($row['Type'], 'varchar') !== false):
                    case (strpos($row['Type'], 'date') !== false):
                    case (strpos($row['Type'], 'time') !== false):
                        $tipo = 'string';
                        break;
                }


                $nomeAtributo = ($row['Key'] == 'PRI') ? 'id' : $row['Field'];
                $posUnderline = strpos($nomeAtributo, '_');
                if ($posUnderline !== false) {
                    $parts = explode('_', $nomeAtributo);
                    $nomeAtributo = '';
                    foreach ($parts as $part) {
                        $nomeAtributo .= (empty($nomeAtributo)) ? $part : ucfirst($part);
                    }
                }
                $codeAtributoFormatado = str_replace(['#tipo#', '#nomeAtributo#'], [$tipo, $nomeAtributo],
                    $codeAtributo);
                $codeGetFormatado = str_replace(['#tipo#', '#nomeAtributo#', '#nomeAtributoUcFirst#'],
                    [$tipo, $nomeAtributo, ucfirst($nomeAtributo)], $codeGet);
                $codeSetFormatado = str_replace(['#tipo#', '#nomeAtributo#', '#nomeAtributoUcFirst#', '#objeto#'],
                    [$tipo, $nomeAtributo, ucfirst($nomeAtributo), $this->objeto], $codeSet);

                $atributos .= $codeAtributoFormatado . "\n\n";
                $gettersSetters .= $codeGetFormatado . "\n\n" . $codeSetFormatado . "\n\n";

                $row = $dbRelat->FetchAssoc($res);
            }
            return "{$atributos}{$gettersSetters}";
        }

        return '';
    }
}