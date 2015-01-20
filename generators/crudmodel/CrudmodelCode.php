<?php

class CrudmodelCode extends CCodeModel
{

    /**
     *
     * @var string The model class with path alias
     */
    public $model;

    /**
     *
     * @var string The desired controller class
     */
    public $controller;
    public $baseControllerClass = 'Controller';

    /**
     * @var bool If the form intented to be inline form
     */
    public $inline = false;

    /**
     *
     * @var string  The model class without path alias
     */
    private $_modelClass;

    /**
     *
     * @var CDbTableSchema The table that model based on
     */
    private $_table;
    // @todo ModelClass
    public $connectionId = 'db';
    public $tablePrefix;
    public $tableName;

    /**
     *
     * @var string Todo: it is like the $_modelClass above.
     */
    public $modelClass;
    public $modelPath = 'application.models';
    public $baseClass = 'CActiveRecord';
    public $buildRelations = true;

    /**
     * @var array list of candidate relation code. The array are indexed by AR class names and relation names.
     * Each element represents the code of the one relation in one AR class.
     */
    protected $relations;

    /**
     *
     * @var array List of GiiniusBuilder models that include all current model fields
     */
    public $builder = array();

    //todo

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('model, controller', 'filter', 'filter' => 'trim'),
            array('model, controller, baseControllerClass', 'required'),
            array('model', 'match', 'pattern' => '/^\w+[\w+\\.]*$/', 'message' => '{attribute} should only contain word characters and dots.'),
            array('controller', 'match', 'pattern' => '/^\w+[\w+\\/]*$/', 'message' => '{attribute} should only contain word characters and slashes.'),
            array('baseControllerClass', 'match', 'pattern' => '/^[a-zA-Z_]\w*$/', 'message' => '{attribute} should only contain word characters.'),
            array('baseControllerClass', 'validateReservedWord', 'skipOnError' => true),
            array('model', 'validateModel'),
            array('baseControllerClass', 'sticky'),
            array('builder', 'safe'),
            //modelCode
            array('tablePrefix, baseClass, modelClass, modelPath', 'filter', 'filter' => 'trim'),
            array('connectionId, modelPath, baseClass', 'required'),
            array('tablePrefix, modelPath', 'match', 'pattern' => '/^(\w+[\w\.]*|\*?|\w+\.\*)$/', 'message' => '{attribute} should only contain word characters, dots, and an optional ending asterisk.'),
            //array('tableName', 'validateTableName', 'skipOnError' => true),
            array('tablePrefix, modelClass, baseClass', 'match', 'pattern' => '/^[a-zA-Z_]\w*$/', 'message' => '{attribute} should only contain word characters.'),
            array('modelPath', 'validateModelPath', 'skipOnError' => true),
            array('baseClass, modelClass', 'validateReservedWord', 'skipOnError' => true),
            array('baseClass', 'validateBaseClass', 'skipOnError' => true),
            array('connectionId, tablePrefix, modelPath, baseClass, buildRelations', 'sticky'),
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'model' => 'Model Class',
            'controller' => 'Controller ID',
            'baseControllerClass' => 'Base Controller Class',
            //modelCode
            'tablePrefix' => 'Table Prefix',
            'tableName' => 'Table Name',
            'modelPath' => 'Model Path',
            'modelClass' => 'Model Class',
            'baseClass' => 'Base Class',
            'buildRelations' => 'Build Relations',
            'connectionId' => 'Database Connection',
        ));
    }

    public function requiredTemplates()
    {
        return array(
            'controller.php',
            'model.php',
        );
    }

    public function init()
    {
        if (Yii::app()->{$this->connectionId} === null)
            throw new CHttpException(500, 'An active "' . $this->connectionId . '" connection is required to run this generator.');
        $this->tablePrefix = Yii::app()->{$this->connectionId}->tablePrefix;
        parent::init();
    }

    public function successMessage()
    {
        $link = CHtml::link('try it now', Yii::app()->createUrl($this->controller), array('target' => '_blank'));
        return "The controller has been generated successfully. You may $link.";
    }

    public function validateModel($attribute, $params)
    {
        if ($this->hasErrors('model'))
            return;
        $class = @Yii::import($this->model, true);
        if (!is_string($class) || !$this->classExists($class))
            $this->addError('model', "Class '{$this->model}' does not exist or has syntax error.");
        else if (!is_subclass_of($class, 'CActiveRecord'))
            $this->addError('model', "'{$this->model}' must extend from CActiveRecord.");
        else {
            $table = CActiveRecord::model($class)->tableSchema;
            if ($table->primaryKey === null)
                $this->addError('model', "Table '{$table->name}' does not have a primary key.");
            else if (is_array($table->primaryKey))
                $this->addError('model', "Table '{$table->name}' has a composite primary key which is not supported by crud generator.");
            else {
                $this->_modelClass = $class;
                $this->_table = $table;
            }
        }
    }

    public function prepare()
    {
        $this->files = array();
        $templatePath = $this->templatePath;
        $controllerTemplateFile = $templatePath . DIRECTORY_SEPARATOR . 'controller.php';

        $this->files[] = new CCodeFile(
                $this->controllerFile, $this->render($controllerTemplateFile)
        );

        $files = scandir($templatePath);
        foreach ($files as $file) {
            if (is_file($templatePath . '/' . $file) && CFileHelper::getExtension($file) === 'php' && $file !== 'controller.php' && $file !== 'model.php') {
                $rendered = $this->render($templatePath . '/' . $file);
                $this->files[] = new CCodeFile(
                        $this->viewPath . DIRECTORY_SEPARATOR . $file, $rendered
                );
            }
        }
        //modelCode
        $this->relations = $this->generateRelations();


        $tableName = $this->_table->name;
        $className = $this->_modelClass;
        $params = array(
            'tableName' => $tableName,
            'modelClass' => $className,
            'columns' => $this->_table->columns,
            'labels' => $this->generateLabels($this->_table),
            'rules' => $this->generateRules($this->_table),
            'relations' => isset($this->relations[$className]) ? $this->relations[$className] : array(),
            'connectionId' => $this->connectionId,
        );
        $modelPath=strpos($this->model, '.')>0 ? $this->model: $this->modelPath;
        $this->files[] = new CCodeFile(
                Yii::getPathOfAlias($modelPath . '/' . $this->_modelClass) . '.php', $this->render($templatePath . '/model.php', $params)
        );
    }

//model template
//    public function prepare()
//    {
//        //if we have alredy the table name we dont need it
//        if (($pos = strrpos($this->tableName, '.')) !== false) {
//            $schema = substr($this->tableName, 0, $pos);
//            $tableName = substr($this->tableName, $pos + 1);
//        }
//        else {
//            $schema = '';
//            $tableName = $this->tableName;
//        }
//        //build model for multiple tables
//        if ($tableName[strlen($tableName) - 1] === '*') {
//            $tables = Yii::app()->{$this->connectionId}->schema->getTables($schema);
//            if ($this->tablePrefix != '') {
//                foreach ($tables as $i => $table) {
//                    if (strpos($table->name, $this->tablePrefix) !== 0)
//                        unset($tables[$i]);
//                }
//            }
//        }
//        else
//            $tables = array($this->getTableSchema($this->tableName));
//
//        $this->files = array();
//        $templatePath = $this->templatePath;
//        $this->relations = $this->generateRelations();
//
//        foreach ($tables as $table) {
//            $tableName = $this->removePrefix($table->name);
//            $className = $this->generateClassName($table->name);
//            $params = array(
//                'tableName' => $schema === '' ? $tableName : $schema . '.' . $tableName,
//                'modelClass' => $className,
//                'columns' => $table->columns,
//                'labels' => $this->generateLabels($table),
//                'rules' => $this->generateRules($table),
//                'relations' => isset($this->relations[$className]) ? $this->relations[$className] : array(),
//                'connectionId' => $this->connectionId,
//            );
//            $this->files[] = new CCodeFile(
//                    Yii::getPathOfAlias($this->modelPath) . '/' . $className . '.php', $this->render($templatePath . '/model.php', $params)
//            );
//        }
//    }

    public function getModelClass()
    {
        return $this->_modelClass;
    }

    public function getControllerClass()
    {
        if (($pos = strrpos($this->controller, '/')) !== false)
            return ucfirst(substr($this->controller, $pos + 1)) . 'Controller';
        else
            return ucfirst($this->controller) . 'Controller';
    }

    public function getModule()
    {
        if (($pos = strpos($this->controller, '/')) !== false) {
            $id = substr($this->controller, 0, $pos);
            if (($module = Yii::app()->getModule($id)) !== null)
                return $module;
        }
        return Yii::app();
    }

    public function getControllerID()
    {
        if ($this->getModule() !== Yii::app())
            $id = substr($this->controller, strpos($this->controller, '/') + 1);
        else
            $id = $this->controller;
        if (($pos = strrpos($id, '/')) !== false)
            $id[$pos + 1] = strtolower($id[$pos + 1]);
        else
            $id[0] = strtolower($id[0]);
        return $id;
    }

    public function getUniqueControllerID()
    {
        $id = $this->controller;
        if (($pos = strrpos($id, '/')) !== false)
            $id[$pos + 1] = strtolower($id[$pos + 1]);
        else
            $id[0] = strtolower($id[0]);
        return $id;
    }

    public function getControllerFile()
    {
        $module = $this->getModule();
        $id = $this->getControllerID();
        if (($pos = strrpos($id, '/')) !== false)
            $id[$pos + 1] = strtoupper($id[$pos + 1]);
        else
            $id[0] = strtoupper($id[0]);
        return $module->getControllerPath() . '/' . $id . 'Controller.php';
    }

    public function getViewPath()
    {
        return $this->getModule()->getViewPath() . '/' . $this->getControllerID();
    }

    public function getTableSchema()
    {
        return $this->_table;
    }

    /**
     *
     * @param string $modelClass
     * @param CDbColumnSchema $column
     * @return string
     */
    public function generateInputLabel($modelClass, $column)
    {
        return "CHtml::activeLabelEx(\$model,'{$column->name}')";
    }

    /**
     *
     * @param string $modelClass
     * @param CDbColumnSchema $column
     * @return string
     */
    public function generateInputField($modelClass, $column)
    {
        if ($column->type === 'boolean')
            return "CHtml::activeCheckBox(\$model,'{$column->name}')";
        else if (stripos($column->dbType, 'text') !== false)
            return "CHtml::activeTextArea(\$model,'{$column->name}',array('rows'=>6, 'cols'=>50))";
        else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name))
                $inputField = 'activePasswordField';
            else
                $inputField = 'activeTextField';

            if ($column->type !== 'string' || $column->size === null)
                return "CHtml::{$inputField}(\$model,'{$column->name}')";
            else {
                if (($size = $maxLength = $column->size) > 60)
                    $size = 60;
                return "CHtml::{$inputField}(\$model,'{$column->name}',array('size'=>$size,'maxlength'=>$maxLength))";
            }
        }
    }

    /**
     *
     * @param string $modelClass
     * @param CDbColumnSchema $column
     * @return string
     */
    public function generateActiveLabel($modelClass, $column)
    {
        $inline = $this->inline ? 'sr-only' : '';
        return "\$form->labelEx(\$model,'{$column->name}', array('class'=>'$inline'))";
    }

    /**
     *
     * @param string $modelClass
     * @param CDbColumnSchema $column
     * @return string
     */
    public function generateActiveField($modelClass, $column)
    {

        if (!$this->builder) {
            if ($column->type === 'boolean')
                return "\$form->checkBox(\$model,'{$column->name}')";
            else if (stripos($column->dbType, 'text') !== false)
                return "\$form->textArea(\$model,'{$column->name}',array('rows'=>6, 'cols'=>50, 'class'=>'form-control'))";
            else {
                if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name))
                    $inputField = 'passwordField';
                else
                    $inputField = 'textField';

                if ($column->type !== 'string' || $column->size === null)
                    return "\$form->{$inputField}(\$model,'{$column->name}')";
                else {
                    if (($size = $maxLength = $column->size) > 60)
                        $size = 60;
                    return "\$form->{$inputField}(\$model,'{$column->name}',array('size'=>$size,'maxlength'=>$maxLength, 'class'=>'form-control'))";
                }
            }
        }
        else {
            $maxLength = $column->size;
            $return = '';
            $builder = GiiniusBuilder::model()->findByAttributes(array('model' => $this->model, 'attribute' => $column->name));

                switch ($builder->field_type) {
                    case 'text':
                        return "\$form->textField(\$model, '{$column->name}', array('maxlength' => $maxLength, 'class' => 'form-control {$builder->css}'))";
                        break;
                    case 'checkbox':
                        return "\$form->checkBox(\$model, '{$column->name}', array('class' => {$builder->css}))";
                        break;
                    case 'dropdown':
                        return "\$form->dropDownList(\$model, '{$column->name}', \$model->{$column->name}Data, array('class' => 'form-control {$builder->css}'))";
                }

        }
    }

    public function guessNameColumn($columns)
    {
        foreach ($columns as $column) {
            if (!strcasecmp($column->name, 'name'))
                return $column->name;
        }
        foreach ($columns as $column) {
            if (!strcasecmp($column->name, 'title'))
                return $column->name;
        }
        foreach ($columns as $column) {
            if ($column->isPrimaryKey)
                return $column->name;
        }
        return 'id';
    }

    /**
     * @todo ModelCode
     */
    public function validateTableName($attribute, $params)
    {
        $invalidTables = array();
        $invalidColumns = array();

        if ($this->tableName[strlen($this->tableName) - 1] === '*') {
            if (($pos = strrpos($this->tableName, '.')) !== false)
                $schema = substr($this->tableName, 0, $pos);
            else
                $schema = '';

            $this->modelClass = '';
            $tables = Yii::app()->{$this->connectionId}->schema->getTables($schema);
            foreach ($tables as $table) {
                if ($this->tablePrefix == '' || strpos($table->name, $this->tablePrefix) === 0) {
                    if (in_array(strtolower($table->name), self::$keywords))
                        $invalidTables[] = $table->name;
                    if (($invalidColumn = $this->checkColumns($table)) !== null)
                        $invalidColumns[] = $invalidColumn;
                }
            }
        }
        else {
            if (($table = $this->getTableSchema($this->tableName)) === null)
                $this->addError('tableName', "Table '{$this->tableName}' does not exist.");
            if ($this->modelClass === '')
                $this->addError('modelClass', 'Model Class cannot be blank.');

            if (!$this->hasErrors($attribute) && ($invalidColumn = $this->checkColumns($table)) !== null)
                $invalidColumns[] = $invalidColumn;
        }

        if ($invalidTables != array())
            $this->addError('tableName', 'Model class cannot take a reserved PHP keyword! Table name: ' . implode(', ', $invalidTables) . ".");
        if ($invalidColumns != array())
            $this->addError('tableName', 'Column names that does not follow PHP variable naming convention: ' . implode(', ', $invalidColumns) . ".");
    }

    /*
     * Check that all database field names conform to PHP variable naming rules
     * For example mysql allows field name like "2011aa", but PHP does not allow variable like "$model->2011aa"
     * @param CDbTableSchema $table the table schema object
     * @return string the invalid table column name. Null if no error.
     */

    public function checkColumns($table)
    {
        foreach ($table->columns as $column) {
            if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $column->name))
                return $table->name . '.' . $column->name;
        }
    }

    public function validateModelPath($attribute, $params)
    {
        if (Yii::getPathOfAlias($this->modelPath) === false)
            $this->addError('modelPath', 'Model Path must be a valid path alias.');
    }

    public function validateBaseClass($attribute, $params)
    {
        $class = @Yii::import($this->baseClass, true);
        if (!is_string($class) || !$this->classExists($class))
            $this->addError('baseClass', "Class '{$this->baseClass}' does not exist or has syntax error.");
        else if ($class !== 'CActiveRecord' && !is_subclass_of($class, 'CActiveRecord'))
            $this->addError('baseClass', "'{$this->model}' must extend from CActiveRecord.");
    }

    public function generateLabels($table)
    {
        $labels = array();
        foreach ($table->columns as $column) {
            $label = ucwords(trim(strtolower(str_replace(array('-', '_'), ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $column->name)))));
            $label = preg_replace('/\s+/', ' ', $label);
            if (strcasecmp(substr($label, -3), ' id') === 0)
                $label = substr($label, 0, -3);
            if ($label === 'Id')
                $label = 'ID';
            $labels[$column->name] = $label;
        }
        return $labels;
    }

    public function generateRules($table)
    {
        $rules = array();
        $required = array();
        $integers = array();
        $numerical = array();
        $length = array();
        $safe = array();
        foreach ($table->columns as $column) {
            if ($column->autoIncrement)
                continue;
            $r = !$column->allowNull && $column->defaultValue === null;
            if ($r)
                $required[] = $column->name;
            if ($column->type === 'integer')
                $integers[] = $column->name;
            else if ($column->type === 'double')
                $numerical[] = $column->name;
            else if ($column->type === 'string' && $column->size > 0)
                $length[$column->size][] = $column->name;
            else if (!$column->isPrimaryKey && !$r)
                $safe[] = $column->name;
        }
        if ($required !== array())
            $rules[] = "array('" . implode(', ', $required) . "', 'required')";
        if ($integers !== array())
            $rules[] = "array('" . implode(', ', $integers) . "', 'numerical', 'integerOnly'=>true)";
        if ($numerical !== array())
            $rules[] = "array('" . implode(', ', $numerical) . "', 'numerical')";
        if ($length !== array()) {
            foreach ($length as $len => $cols)
                $rules[] = "array('" . implode(', ', $cols) . "', 'length', 'max'=>$len)";
        }
        if ($safe !== array())
            $rules[] = "array('" . implode(', ', $safe) . "', 'safe')";

        return $rules;
    }

    public function getRelations($className)
    {
        return isset($this->relations[$className]) ? $this->relations[$className] : array();
    }

    protected function removePrefix($tableName, $addBrackets = true)
    {
        if ($addBrackets && Yii::app()->{$this->connectionId}->tablePrefix == '')
            return $tableName;
        $prefix = $this->tablePrefix != '' ? $this->tablePrefix : Yii::app()->{$this->connectionId}->tablePrefix;
        if ($prefix != '') {
            if ($addBrackets && Yii::app()->{$this->connectionId}->tablePrefix != '') {
                $prefix = Yii::app()->{$this->connectionId}->tablePrefix;
                $lb = '{{';
                $rb = '}}';
            }
            else
                $lb = $rb = '';
            if (($pos = strrpos($tableName, '.')) !== false) {
                $schema = substr($tableName, 0, $pos);
                $name = substr($tableName, $pos + 1);
                if (strpos($name, $prefix) === 0)
                    return $schema . '.' . $lb . substr($name, strlen($prefix)) . $rb;
            }
            else if (strpos($tableName, $prefix) === 0)
                return $lb . substr($tableName, strlen($prefix)) . $rb;
        }
        return $tableName;
    }

    protected function generateRelations()
    {
        if (!$this->buildRelations)
            return array();
        $relations = array();
        foreach (Yii::app()->{$this->connectionId}->schema->getTables() as $table) {
            if ($this->tablePrefix != '' && strpos($table->name, $this->tablePrefix) !== 0)
                continue;
            $tableName = $table->name;

            if ($this->isRelationTable($table)) {
                $pks = $table->primaryKey;
                $fks = $table->foreignKeys;

                $table0 = $fks[$pks[0]][0];
                $table1 = $fks[$pks[1]][0];
                $className0 = $this->generateClassName($table0);
                $className1 = $this->generateClassName($table1);

                $unprefixedTableName = $this->removePrefix($tableName);

                $relationName = $this->generateRelationName($table0, $table1, true);
                $relations[$className0][$relationName] = "array(self::MANY_MANY, '$className1', '$unprefixedTableName($pks[0], $pks[1])')";

                $relationName = $this->generateRelationName($table1, $table0, true);
                $relations[$className1][$relationName] = "array(self::MANY_MANY, '$className0', '$unprefixedTableName($pks[1], $pks[0])')";
            }
            else {
                $className = $this->generateClassName($tableName);
                foreach ($table->foreignKeys as $fkName => $fkEntry) {
                    // Put table and key name in variables for easier reading
                    $refTable = $fkEntry[0]; // Table name that current fk references to
                    $refKey = $fkEntry[1];   // Key in that table being referenced
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $relationName = $this->generateRelationName($tableName, $fkName, false);
                    $relations[$className][$relationName] = "array(self::BELONGS_TO, '$refClassName', '$fkName')";

                    // Add relation for the referenced table
                    $relationType = $table->primaryKey === $fkName ? 'HAS_ONE' : 'HAS_MANY';
                    $relationName = $this->generateRelationName($refTable, $this->removePrefix($tableName, false), $relationType === 'HAS_MANY');
                    $i = 1;
                    $rawName = $relationName;
                    while (isset($relations[$refClassName][$relationName]))
                        $relationName = $rawName . ($i++);
                    $relations[$refClassName][$relationName] = "array(self::$relationType, '$className', '$fkName')";
                }
            }
        }
        return $relations;
    }

    /**
     * Checks if the given table is a "many to many" pivot table.
     * Their PK has 2 fields, and both of those fields are also FK to other separate tables.
     * @param CDbTableSchema table to inspect
     * @return boolean true if table matches description of helpter table.
     */
    protected function isRelationTable($table)
    {
        $pk = $table->primaryKey;
        return (count($pk) === 2 // we want 2 columns
                && isset($table->foreignKeys[$pk[0]]) // pk column 1 is also a foreign key
                && isset($table->foreignKeys[$pk[1]]) // pk column 2 is also a foriegn key
                && $table->foreignKeys[$pk[0]][0] !== $table->foreignKeys[$pk[1]][0]); // and the foreign keys point different tables
    }

    protected function generateClassName($tableName)
    {
        if ($this->tableName === $tableName || ($pos = strrpos($this->tableName, '.')) !== false && substr($this->tableName, $pos + 1) === $tableName)
            return $this->modelClass;

        $tableName = $this->removePrefix($tableName, false);
        $className = '';
        foreach (explode('_', $tableName) as $name) {
            if ($name !== '')
                $className.=ucfirst($name);
        }
        return $className;
    }

    /**
     * Generate a name for use as a relation name (inside relations() function in a model).
     * @param string the name of the table to hold the relation
     * @param string the foreign key name
     * @param boolean whether the relation would contain multiple objects
     * @return string the relation name
     */
    protected function generateRelationName($tableName, $fkName, $multiple)
    {
        if (strcasecmp(substr($fkName, -2), 'id') === 0 && strcasecmp($fkName, 'id'))
            $relationName = rtrim(substr($fkName, 0, -2), '_');
        else
            $relationName = $fkName;
        $relationName[0] = strtolower($relationName);

        if ($multiple)
            $relationName = $this->pluralize($relationName);

        $names = preg_split('/_+/', $relationName, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($names))
            return $relationName;  // unlikely
        for ($name = $names[0], $i = 1; $i < count($names); ++$i)
            $name.=ucfirst($names[$i]);

        $rawName = $name;
        $table = Yii::app()->{$this->connectionId}->schema->getTable($tableName);
        $i = 0;
        while (isset($table->columns[$name]))
            $name = $rawName . ($i++);

        return $name;
    }

    /**
     * @return array List of DB connections ready to be displayed in dropdown
     */
    public function getConnectionList()
    {
        $list = array();
        foreach (Yii::app()->getComponents(false) as $name => $component) {
            if ($this->isDbConnection($name, $component)) {
                $connectionString = is_object($component) ? $component->connectionString : $component['connectionString'];
                $list[$name] = $name . ' (' . $connectionString . ')';
            }
        }
        return $list;
    }

    public function generateDropdownOptions($model, $attribute)
    {
        $builder = GiiniusBuilder::model()->findByAttributes(array(
            'model' => $model,
            'attribute' => $attribute,
        ));
        if (!$builder)
            return 'gfdgdfgdgdfgdg';
        $arr = preg_split('/\n/', $builder->options);
        $s = '';
        foreach ($arr as $opt) {
            $s.="$opt => $opt,\n";
        }
        return <<<DAT
  \n\n public function get{$attribute}Data()
   {
        return array(
            $s
           );
   }
DAT;
    }

    public function save()
    {
//        $ret = true;
//        if (isset($_POST['GiiniusBuilder'])) {
//            foreach ($_POST['GiiniusBuilder'] as $i => $post) {
//                $model = new GiiniusBuilder;
//                $model->attributes = $post;
//                if ($model->save()) {
//
//                }
//            }
//        }
        return parent::save();
    }

    /**
     * @param string $name component name
     * @param mixed $component component config or component object
     * @return bool if component is DB connection
     */
    private function isDbConnection($name, $component)
    {
        if (is_array($component)) {
            if (isset($component['class']) && $component['class'] == 'CDbConnection')
                return true;
            else
                $component = Yii::app()->getComponent($name);
        }

        return $component instanceof CDbConnection;
    }

}