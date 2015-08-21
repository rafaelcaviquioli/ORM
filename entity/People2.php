<?php
use ORM;

class People2 extends ORM {

    protected $name;
    protected $email;
    protected $birthDate;
    protected $stature;

    function __construct($id = NULL){
        parent::__construct('People2', 'people', 'id');

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @result People2
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
     * @result People2
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
     * @result People2
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
     * @result People2
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
        return new People2($codigo);
    }

}
