<?php
require_once("../vendor/autoload.php");

######################################################################################################
################Gerando entidade ORM setando os atributos manualmente.###########
/**
 * 
 */
/* Nome da entidade */
$entityName = "People2";

/* Chave primária da entidade */
$entityPrimaryKey = "id";

/* Tabela da entidade no banco de dados*/
$entityTable = "people";

/* 
 * Array $entityAttributes
 * A array de atributos (campos da tabela) só deve ser preenchida caso você opte por não gerar a entidade buscando do banco de dados.
 * Essa array só deve ser preenchida para geração de entidade sem tabela definida.
 */
$entityAttributes = array('name', 'email', 'birthDate', 'stature');

/* 
 * Array $entitiesAssociated
 * Utilize o padrão abaixo para indicar que essa tabela terá tabelas filhas associadas a ela.
 * 
 * $entitiesAssociated = array(array('ClassFilho', 'indiceAssociativo', 'entity/'), array('ClassFilho2', 'indiceAssociativo2', 'entity/'))
 */
$entitiesAssociated = null;

/*
 * Diretório de destino da classe que será gerada.
 * $dirTarget = __DIR__ . "/../entity/";
 */
$dirTarget = __DIR__ . "/../entity/";

$generateEntity = new \EntityGenerator($entityName, $entityPrimaryKey, $entityTable, $entityAttributes, $entitiesAssociated);
$generateEntity->gerar($dirTarget);
######################################################################################################