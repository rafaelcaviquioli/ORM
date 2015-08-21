<?php

/**
 * Description of Entidade
 * A Classe Entidade ira fixar alguns metodos padroes que todas as entidades devem conter.
 * @author Rafael
 */

use ConnectionPDO;

abstract class ORM {
    /*
     * Indicador da chave primaria de cada classe herdada.
     */

    protected $primaryKey;
    /*
     * Nome  do objeto que est� herdando a classe Entidade.
     */
    protected $nomeEntidade;

    /*
     * Tabela de armazenamento da entidade.
     */
    protected $tabelaEntidade;

    /*
     * Lista de atributos que participar�o da persist�ncia no banco de dados
     */
    protected $atributosPersistencia;
    //Cria exce��o padr�o para objeto nula
    static $ExceptionEntidadeNula;

    public function __construct($nomeEntidade, $tabelaEntidade, $primaryKey) {
        //construtor da classe ConexaoRoot
        $this->nomeEntidade = $nomeEntidade;
        $this->setTabelaEntidade($tabelaEntidade);

        $this->primaryKey = $primaryKey;

        //Cria exce��o padr�o para objeto nula
        self:$ExceptionEntidadeNula = new \Exception("Erro!, a objeto " . $this->nomeEntidade . " n�o foi carregada.");
    }


    private function insert() {
        try {
            $this->beforeInsert();
        } catch (\Exception $exIgnore) {
            
        }
        $primaryKey = $this->primaryKey;
        //Verifica se a objeto n�o est� criada.
        if (!$this->isLoad()) {

            $campos = implode(', ', $this->atributosPersistencia);

            foreach ($this->atributosPersistencia as $atributo) {
                $valuesDoisPonto[] = ":$atributo ";

                if (is_null($this->$atributo)) {
                    $this->$atributo = "";
                }
            }

            $valuesDoisPonto = implode(", ", $valuesDoisPonto);

            $sql = "INSERT INTO " . $this->tabelaEntidade . " ($campos) VALUES ($valuesDoisPonto)";
            $conexao = ConnectionPDO::getConnection();

            if ($stmt = $conexao->prepare($sql)) {
                $i = 1;
                foreach ($this->atributosPersistencia as $atributo) {
                    $valores[":" . $atributo] = $this->$atributo;
                }
                $stmt->execute($valores);

                //Postgresql
                //$this->$primaryKey = $conexao->lastInsertId($this->tabelaEntidade . "_" . $this->primaryKey . "_seq");
                //Mysql
                $this->$primaryKey = $conexao->lastInsertId();
                unset($conexao);

                try {
                    //Chama onInsert (CallBack).
                    $this->onInsert();
                } catch (\Exception $e) {
                    
                }
                return true;
            } else {
                throw new \Exception("Erro ao criar objeto " . $this->nomeEntidade . " no banco de dados.");
            }
        } else {
            throw new \Exception("Erro ao inserir! A objeto " . $this->nomeEntidade . " ja est� criada.");
        }
    }

    public function save() {
        $primaryKey = $this->primaryKey;

        if ($this->validationSave()) {
            //Verifica se a objeto est� criada.
            if ($this->isLoad()) {
                //Vali do objeto antes de salvar.
                //Prepara SET da SQL
                $set = NULL;
                $valuesArray = array();

                foreach ($this->atributosPersistencia as $atributo) {
                    $set .= $atributo . " = :$atributo,";
                    if (is_null($this->$atributo)) {
                        $this->$atributo = "";
                    }
                }
                //remove virgula do fim da sql.
                $set = trim($set);
                $set = substr($set, 0, -1);
                $sql = "UPDATE " . $this->tabelaEntidade . " SET $set WHERE " . $this->primaryKey . " = '" . $this->$primaryKey . "'";

                $conexao = ConnectionPDO::getConnection();
                if ($stmt = $conexao->prepare($sql)) {
                    unset($conexao);
                    foreach ($this->atributosPersistencia as $atributo) {
                        $stmt->bindParam(":" . $atributo, $this->$atributo);
                    }

                    return $stmt->execute();
                } else {
                    throw new \Exception("Erro ao salvar a objeto " . $this->nomeEntidade);
                }
            } else {
                $this->insert();
            }
        } else {
            throw new \Exception("Erro ao salvar a objeto " . $this->nomeEntidade . ", falha na validação.");
        }
    }

    /* delete()
     * M�todo respons�vel por deletar o objeto do banco de dados.
     */

    public function delete() {
        $primaryKey = $this->primaryKey;
        //Verifica se a objeto est� criada.
        if ($this->isLoad()) {
            //Chama o m�todo de valida��o da exclus�o.
            if ($this->validationDelete()) {
                $conexao = ConnectionPDO::getConnection();
                if ($conexao->query("DELETE FROM " . $this->tabelaEntidade . " WHERE " . $this->primaryKey . " = '" . $this->$primaryKey . "'")) {
                    unset($conexao);
                    try {
                        $this->onDelete();
                    } catch (\Exception $e) {
                        
                    }
                    return true;
                } else {
                    throw new \Exception("Erro ao excluir objeto " . $this->nomeEntidade . " c�digo " . $this->$primaryKey . " do banco de dados.");
                }
            }
        } else {
            throw new \Exception("Erro ao excluir! A objeto " . $this->nomeEntidade . " n�o est� criada.");
        }
    }

    protected function load() {
        $primaryKey = $this->primaryKey;
        if (!is_null($this->$primaryKey)) {
            //Cria sql do select
            $select = implode(", ", $this->atributosPersistencia);
            $sql = "SELECT " . $this->primaryKey . ", $select FROM " . $this->tabelaEntidade . " WHERE " . $this->primaryKey . " = '" . $this->$primaryKey . "' LIMIT 1";

            $conexao = ConnectionPDO::getConnection();
            $st = $conexao->prepare($sql);
            unset($conexao);
            if ($st->execute()) {
                if ($st->rowCount()) {
                    $dados = $st->fetch();
                    //Seta valor da chave primaria
                    $this->$primaryKey = $dados[$this->primaryKey];

                    //Percorre todos os atributos e preenche o valor.
                    foreach ($this->atributosPersistencia as $atributo) {
                        $this->$atributo = $dados[$atributo];
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                throw new \Exception("Erro ao carregar a objeto " . $this->nomeEntidade . " do banco de dados.");
            }
        } else {
            throw new \Exception("Erro ao carregar objeto " . $this->nomeEntidade . ", o codigo nao foi setado.");
        }
    }

    /* getAll()
     * M�todo respons�vel por buscar todos os objetos deste tipo de objeto de acordo com os par�metros.
     */

    public function getAll($where = NULL, $order = NULL, $limit = array('20'), $group = NULL) {
        //verifica os par�metros passados.
        if (!is_array($order) AND ! is_null($order)) {
            throw new \Exception("O parametro order é inválido.");
        }
        if (!is_array($limit) AND ! is_null($limit) AND ( is_string($limit) AND $limit != '*')) {
            throw new \Exception("O parametro limit é inválido.");
        }
        if (!is_array($group) AND ! is_null($group)) {
            throw new \Exception("O parametro group é inválido.");
        }

        //Armazena atributos entre virtula.
        $atributosSql = implode(", ", $this->atributosPersistencia);

        //Inicializa variaveis.
        $whereSql = $orderSql = $limitSql = $groupSql = NULL;

        if (!is_null($where)) {
            $whereSql = "WHERE " . $where;
        }
        if (!is_null($order)) {
            $orderSql = "ORDER BY " . implode(", ", $order);
        }
        if (!is_null($group)) {
            $groupSql = "GROUP BY " . implode(", ", $group);
        }

        if (!is_null($limit) AND $limit != '*' AND ! empty($limit)) {
            $limitSql = "LIMIT " . implode(", ", $limit);
        }
        $sql = trim("SELECT $this->primaryKey, $atributosSql FROM " . $this->tabelaEntidade . " " . $whereSql . " " . $groupSql . " " . $orderSql . " " . $limitSql);
        $conexao = ConnectionPDO::getConnection();
        $rs = $conexao->query($sql);
        unset($conexao);

        if ($rs) {
            $listaEntidade = array();
            while ($dados = $rs->fetch()) {
                //Inicializa objeto herdado da entidade.
                $nomeEntidade = "" . $this->nomeEntidade;
                $entidade = new $nomeEntidade();

                //Inseri a primary key da pesquisa.
                $entidade->setPrimaryKey($dados[$this->primaryKey]);

                //Percorre todos os atributos  do objeto e nicializa preenchimento pelos metodos set.
                foreach ($this->atributosPersistencia as $atributo) {
                    $setAtribute = "set" . $atributo;
                    //Executa método set do atributo
                    $entidade->$setAtribute($dados[$atributo]);
                }

                $listaEntidade[] = $entidade;
            }
            return $listaEntidade;
        } else {
            throw new \Exception("Erro ao executar metodo getAll na objeto " . $this->nomeEntidade);
        }
    }

    public function countAll($where = NULL) {

        //Inicializa variaveis.
        $whereSql = NULL;

        if (!is_null($where)) {
            $whereSql = "WHERE " . $where;
        }
        $sql = trim("SELECT COUNT(*) total FROM " . $this->tabelaEntidade . " " . $whereSql);
        $conexao = ConnectionPDO::getConnection();
        $rs = $conexao->query($sql);
        unset($conexao);
        if ($rs) {
            $dados = $rs->fetch();
            return $dados['total'];
        } else {
            throw new \Exception("Erro ao executar metodo getAll na objeto " . $this->nomeEntidade);
        }
    }

    private function setTabelaEntidade($tabelaEntidade) {
        if (empty($tabelaEntidade)) {
            throw new \Exception("Erro! Tabela  do objeto " . $this->nomeEntidade . " nao setada.");
        } else {
            $this->tabelaEntidade = $tabelaEntidade;
        }
    }

    public function isLoad() {
        $primaryKey = $this->primaryKey;
        return !is_null($this->$primaryKey);
    }

    protected function persistAttribute($atribute) {
        $this->atributosPersistencia[] = $atribute;
    }

    public function getPrimaryKey() {
        $primaryKey = $this->primaryKey;
        return $this->$primaryKey;
    }

    public function setPrimaryKey($codigo) {
        $primaryKey = $this->primaryKey;
        $this->$primaryKey = $codigo;
    }

 protected function beforeSave()
    {
        return true;
    }

    protected function beforeInsert()
    {
        return true;
    }

    protected function beforeDelete()
    {
        return true;
    }
    protected function onSave()
    {
        return true;
    }
    protected function onInsert()
    {
        return true;
    }

    protected function onDelete()
    {
        return true;
    }

    public function validationDelete()
    {
        return true;
    }

    public function validationSave()
    {
        return true;
    }
    public function validationInsert()
    {
        return true;
    }

}
