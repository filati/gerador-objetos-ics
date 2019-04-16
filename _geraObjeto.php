<?php

// Objeto de Layout
require("../../includes/ics.php");

$pag = new Pagina(null, null, false);

include_once ('geradorDeObjetos.class.php');

if (!isset($_POST['dominio']) || empty($_POST['dominio'])) {
    die('Domínio não informado');
}
if (!isset($_POST['objeto']) || empty($_POST['objeto'])) {
    die('Objeto não informado');
}

$gerador = new geradorDeObjetos($_POST['dominio'], $_POST['objeto'], $_POST['table']);

echo "Objetos criados:<br><br>\n";
$tipos = $gerador->obterTipos();
foreach ($tipos as $tipo) {
    $codigo = $gerador->obterCodigoPorTipo($tipo);
    echo $gerador->fabricarObjetoVazio($tipo, $codigo, $_POST['mostrar_objetos']);
}

echo '<hr>';
echo "Config Provider (Edite e insira as linhas dos `invokables` e  `factories` manualmente): <br><br>";

echo $gerador->criarTrechoConfigProvider();
?>

