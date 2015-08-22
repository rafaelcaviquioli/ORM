<?php

class EntityGenerator {

    private $nome;
    private $tabela;
    private $atributos;
    private $primaryKey;
    private $filhos;
    private $classe;

    function __construct($nome, $primaryKey, $tabela, $atributos = NULL, $filhos = NULL) {
        $this->nome = $nome;
        $this->primaryKey = $primaryKey;
        $this->tabela = $tabela;

        /* array(array('ClassFilho', 'indiceAssociativo', 'entity/'), array('ClassFilho2', 'indiceAssociativo2', 'entity//')) */
        $this->filhos = $filhos;

        if (is_null($atributos) OR count($atributos) == 0) {
            $conexao = ConnectionPDO::getConnection();
            $rs = $conexao->query("SHOW COLUMNS FROM $tabela");
            if (!$rs) {
                unset($conexao);
                throw new Exception("Erro ao buscar campos da tabela $tabela.");
            }
            $atributos = array();
            while ($row = $rs->fetch()) {
                $atributos[] = $row['Field'];
            }
            unset($conexao);
        }
        $this->atributos = $atributos;
    }

    public function gerar($destino = NULL) {
        $nome = $this->nome;
        $primaryKey = $this->primaryKey;
        $tabela = $this->tabela;
        $atributos = $this->atributos;
        $this->classe .= "<?php
use ORM;";
        if (count($this->filhos)) {
            echo "\n";
            foreach ($this->filhos as $filho) {
                $this->classe .= "\nuse " . str_replace("/", "\\", $filho[2]) . $filho[0] . ";";
            }
        }
        $this->classe .= "

class $nome extends ORM {
";
        foreach ($atributos as $atributo) {
            $this->classe .= "\n    protected \$$atributo;";
        }

        $this->classe .= "

    function __construct(\$$primaryKey = NULL){
        parent::__construct('$nome', '$tabela', '$primaryKey');
";
        foreach ($atributos as $atributo) {
            $this->classe .= "\n        \$this->persistAttribute('$atributo');";
        }

        $this->classe .= "
            
        if (!is_null(\$$primaryKey)) {
            \$this->$primaryKey = \$$primaryKey;
            \$this->load();
        }
    }
            ";

        foreach ($atributos as $atributo) {
            $atributoMaiusculo = strtoupper($atributo[0]) . substr($atributo, 1);
            $this->classe .= "
    /**
     * @return String
     */
    public function get$atributoMaiusculo()
    {
        return \$this->$atributo;
    }

    /**
     * @result $nome
     */
    public function set$atributoMaiusculo(\$$atributo)
    {
        \$this->$atributo = \$$atributo;

        return \$this;
    }
    ";
        }
        $this->classe .= "
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

    static function newInstance(\$codigo = NULL){
        return new $nome(\$codigo);
    }
";
        if (count($this->filhos)) {
            foreach ($this->filhos as $filho) {
                $this->classe .= "
        public function get" . $filho[0] . "(\$update = false, \$where = NULL, \$order = NULL, \$limit = NULL, \$group = NULL) {
            if (isset(\$this->" . $filho[0] . ") AND ! \$update AND is_null(\$where)) {
                return \$this->" . $filho[0] . ";
            } else {
                \$entidade = new " . $filho[0] . "();
                if(!is_null(\$where)){
                    \$sqlWhere = ' AND ' . \$where;
                }else{
                    \$sqlWhere = NULL;
                }
                return \$this->" . $filho[0] . " = \$entidade->getAll(\"" . $filho[1] . " = '\" . \$this->$this->primaryKey . \"' \$sqlWhere\", \$order, \$limit, \$group);
            }
        }";
            }
        }
        $this->classe .= "
}
";
        if (is_null($destino) OR empty($destino)) {
            return $this->classe;
        } else {
            $arquivo = $destino . "/" . $nome . ".php";
            if (file_exists($arquivo)) {
                throw new Exception("Erro, o arquivo já existe: $arquivo");
            } else if (!file_put_contents($arquivo, $this->classe)) {
                throw new Exception("Erro ao escrever arquivo $arquivo");
            } else {
                return "Arquivo gerado com sucesso: $arquivo\n";
            }
        }
    }

}
