<?php
/**
 * Created by PhpStorm.
 * User: Vector
 * Date: 12/03/2019
 * Time: 12:08
 */
?>
<form method="post" action="_geraObjeto.php">
    <label>Domínio:</label><br>
    <input type="text" name="dominio" placeholder="Redespacho" /><br>
    <br>
    <label>Objeto:</label><br>
    <input type="text" name="objeto" placeholder="CorreiosCartaoPortagem" /><br>
    <br>
    <label>Tabela do Db para gerar os atributos da Entidade (opcional):</label><br>
    <input type="text" name="table" /><br>
    <br>
    <br>
    <label>Mostrar Objetos?</label><br>
    <select name="mostrar_objetos">
        <option value="0" selected>NÃO</option>
        <option value="1">SIM</option>
    </select>
    <br>
    <br>
    <input type="submit" value="Gerar"/>
</form>