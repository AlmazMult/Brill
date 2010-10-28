<?php
/*
 * Model
 *
 * Класс родитель для вех моделей
 */

/*
 * save()
 * getCols($aCols)
 * getFields()
 * getObject($pk)
 * getObjects($field)
 * delete($pk)
 *
 * update()
 * add()
 *
 *
 */


abstract class Model {
    protected $tbl_name;
    private $values = null;
    protected $fields = array();

    final function  __construct() {

    }

    /**
     * Заполняет один объект значениями, полученным по первому полу
     * used for only unique fields
     */
    public function getObject($valPk) {
        // подумать, как от этого избавиться
        $valPk = addslashes($valPk);
        $values = DBExt::getOneRow($this->tbl_name, $this->fields[0], $valPk);
        if ($values) {
            //заполнение полей значениями
            foreach ($this->fields as $fld) {
                $this->values[$fld] = $values[$fld];
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * Model::getObjects($class, $field, $val)
     * Возвращает массив объектов
     * used for only unique fields
     */
    public static function getObjects($class, $field, $val) {
        // подумать, как от этого избавиться
        //$class
        $val = addslashes($val);
        $values = DBExt::getRows($this->tbl_name, $field, $val);
        if (empty($values)) return false;
        $this->values = array();
        $this->values[$fld] = $values[$fld];
        return true;
    }

    /**
     * Получает значения по первому полю
     * used for only unique fields
     */
    function getPkObject($val) {

        // подумать, как от этого избавиться
    
        $values = DBExt::getByField($this->tbl_name, $this->fields[0], $val, $this->fields);

        if (empty($values)) return false;
        $this->values = array();
        //заполнение полей значениями
        foreach ($this->fields as $fld) {
            $this->values[$fld] = $values[$fld];
        }
        return true;
    }

    /**
     * Получить массив объектов
     */
    function getArrayObjects() {
        return DBExt::select($this->tbl_name);
    }

    /**
     * Вставляет данные в базу
     */
    function add () {
        return  DBExt::insert($this->tbl_name, $this->values);

    }
    function  __get($field) {
        if (array_key_exists($field, $this->values)) {
             return $this->values[$field];
        } else {
            Log::warning('Не возможно получить свойство. / ' . get_class($this) .'->' . $field . ' - не определено');
        }

    }

    function  __set($field, $value) {
        if (in_array($field, $this->fields)) {
            //экранируем все от греха по дельше
            $this->values[$field] = addslashes($value);
        } else {
            Log::warning('Не возможно задать свойство. / ' . get_class($this) .'->' . $field . ' - не определено');
        }

    }
    /**
     *
     * @param string $field обновить только конкретное поле
     */

    //сделать какойто идентификатор id каждому объекту модели
    function save($field = null) {
        if ($field) {
            $value = $this->__get($field);
            $result = DBExt::update($this->tbl_name, array($field => $value), 'id', $this->id);
        } else {
            $result = DBExt::update($this->tbl_name, $this->values, 'id', $this->id);
        }

    }
    function update () {}

    function delete() {}

    public function getValues() {
        return $this->values;
    }

    public function getFields() {
        return $this->fields;
    }
}