<?php
use ORM;
use entity\ClassFilho;
use entity\ClassFilho2;

class People extends ORM {

    protected $id;
    protected $name;
    protected $email;
    protected $birthDate;
    protected $stature;

    function __construct($id = NULL){
        parent::__construct('People', 'people', 'id');

        $this->persistAttribute('id');
        $this->persistAttribute('name');
        $this->persistAttribute('email');
        $this->persistAttribute('birthDate');
        $this->persistAttribute('stature');
            
        if (!is_null($id)) {
            $this->id = $id;
            $this->load();
        }
    }
            
    /**
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @result People
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    
    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @result People
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    /**
     * @return String
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @result People
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
    
    /**
     * @return String
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @result People
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }
    
    /**
     * @return String
     */
    public function getStature()
    {
        return $this->stature;
    }

    /**
     * @result People
     */
    public function setStature($stature)
    {
        $this->stature = $stature;

        return $this;
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

    static function newInstance($codigo = NULL){
        return new People($codigo);
    }

        public function getClassFilho($update = false, $where = NULL, $order = NULL, $limit = NULL, $group = NULL) {
            if (isset($this->ClassFilho) AND ! $update AND is_null($where)) {
                return $this->ClassFilho;
            } else {
                $entidade = new ClassFilho();
                if(!is_null($where)){
                    $sqlWhere = ' AND ' . $where;
                }else{
                    $sqlWhere = NULL;
                }
                return $this->ClassFilho = $entidade->getAll("indiceAssociativo = '" . $this->id . "' $sqlWhere", $order, $limit, $group);
            }
        }
        public function getClassFilho2($update = false, $where = NULL, $order = NULL, $limit = NULL, $group = NULL) {
            if (isset($this->ClassFilho2) AND ! $update AND is_null($where)) {
                return $this->ClassFilho2;
            } else {
                $entidade = new ClassFilho2();
                if(!is_null($where)){
                    $sqlWhere = ' AND ' . $where;
                }else{
                    $sqlWhere = NULL;
                }
                return $this->ClassFilho2 = $entidade->getAll("indiceAssociativo2 = '" . $this->id . "' $sqlWhere", $order, $limit, $group);
            }
        }
}
