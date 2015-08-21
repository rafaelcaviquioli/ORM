<?php

require_once("vendor/autoload.php");

try {
    /**
     * Inserir um novo registro no banco de dados
     */
    $people = new \People();
    $people
            ->setName("Rafael Caviquioli")
            ->setEmail("rafaelcitj@gmail.com")
            ->setBirthDate("2015-01-01")
            ->setStature("1.90")
            ->save();

    /**
     * Obtem o id do registro no banco de dados.
     */
    $id = $people->getId();

    /**
     * Alterando um registro no banco de dados
     */
    $people = new \People($id);
    $people
            ->setName("Rafael Pereira")
            ->save();

    /*
     * FORMA RESUZIADA:
     * Alterando um registro no banco de dados
     */
    \People::newInstance($id)->setName("Rafael Pereira")->save();


    /**
     * Deletando um registro do banco de dados
     */
    $people = new \People($id);
    $people->delete();

    /*
     * FORMA RESUZIADA:
     * Alterando um registro no banco de dados
     */
    \People::newInstance($id)->delete();
    
    
    /*
     * Obdendo vários registros do banco de dados
     */
    $people = new \People();
    $where = "name LIKE '%Rafael%' OR email LIKE '%gmail%'";
    $order = array('id DESC', 'name ASC');
    $limit = array(1000, 0);
    //$group = array('birthDate');
    $group = null;
    
    //Resultado: array(Class People), Uma array com várias instâncias da classe People.
    $peoples = $people->getAll($where, $order, $limit, $group);
    
    
    /*
     * FORMA RESUZIADA:
     * Obdendo vários registros do banco de dados
     */
    
    $peoples = \People::newInstance()->getAll("name LIKE '%Rafael%' OR email LIKE '%gmail%'");
    
    foreach ($peoples as $people) {
        echo $people->getName() . "\n";
    }
    
    
    /*
     * Obtendo a quantidade de registros existentes no banco de dados.
     * Esse método utiliza menos esforço do banco de dados pois não obtem os dados do registro, apenas faz a contagem deles diretamente no banco de dados.
     * O resultado é um número inteiro.
     */
     $total = \People::newInstance()->countAll("name LIKE '%Rafael%' OR email LIKE '%gmail%'");

    
} catch (Exception $ex) {
    echo $ex->getMessage();
}

