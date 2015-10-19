## RC ORM for Mysql##

[1. Exemplo de uso na pratica](https://github.com/rafaelcaviquioli/ORM/blob/master/USE_EXAMPLE.md)
[2. Gerando entidades](https://github.com/rafaelcaviquioli/ORM/blob/master/GENERATE_ENTITY_EXAMPLE.md)

Inserir um novo registro no banco de dados
------------------------------------------
    $people = new \People();
    $people
            ->setName("Rafael Caviquioli")
            ->setEmail("rafaelcitj@gmail.com")
            ->setBirthDate("2015-01-01")
            ->setStature("1.90")
            ->save();

    $id = $people->getId();
Alterando um registro no banco de dados
---------------------------------------
    $people = new \People($id);
    $people
            ->setName("Rafael Pereira")
            ->save();

    /* Forma simplificada */
    \People::newInstance($id)->setName("Rafael Pereira")->save();

Deletando um registro do banco de dados
---------------------------------------
    $people = new \People($id);
    $people->delete();

    /* Forma simplificada */
    \People::newInstance($id)->delete();
    
Obdendo vários registros do banco de dados
------------------------------------------
    $people = new \People();
    $where = "name LIKE '%Rafael%' OR email LIKE '%gmail%'";
    $order = array('id DESC', 'name ASC');
    $limit = array(1000, 0);
    $group = null;
    
    $peoples = $people->getAll($where, $order, $limit, $group);
    //Resultado: array(Class People), Uma array com várias instâncias da classe People.
   
    /* Forma simplificada */
    $peoples = \People::newInstance()->getAll("name LIKE '%Rafael%' OR email LIKE '%gmail%'");
    
    foreach ($peoples as $people) {
        echo $people->getName() . "\n";
    }

Obtendo a quantidade de registros existentes no banco de dados.
---------------------------------------------------------------
    /*
	 * Esse método utiliza menos esforço do banco de dados pois não 
	 * obtem os dados do registro, apenas faz a contagem deles
	 * diretamente no banco de dados.
	 * O resultado é um número inteiro.
	*/
	$total = \People::newInstance()->countAll("name LIKE '%Rafael%' OR email LIKE '%gmail%'");
