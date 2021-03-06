<?php

namespace TotalFlex\Field;

class Field {

	/**
	 * @var string Column name
	 */
	private $_column;

	/**
	 * @var string Field label
	 */
	private $_label;

    /**
     * @var string HTML Input type
     */
    private $_type;

	/**
	 * @var boolean Is a primary key
	 */
	private $_primaryKey;

	/**
	 * @var array[Context] The contexts this fields is allowed
	 */
	private $_contexts;

	/**
	 * @var array[IRule] Rules to this field
	 */
	private $_rules;

	/**
	 * @var TotalFlex\Table This field's table
	 */
	protected $_view;

	/**
	 * @var mixed this is the value of the field
	 */
	private $_value;

	/**
	 * @var string chave da variavel post
	 */
	private $_postKey;

	/**
	 * set it to true to not include it on create, update or select 
	 * @var boolean
	 */
	protected $_skipOnCreate = false ;
	protected $_skipOnUpdate = false ;
	protected $_skipOnSelect = false ;
	protected $_skipOnSetData = false ;
	// private $_skipOnSave   = false ;

	/**
	 * Default template for all Fields
	 * @var string
	 */
	protected static $defaultEncloseStart  = "";
	protected static $defaultEncloseEnd    = "";
	protected static $defaultTemplate      = "\t<input type=\"__type__\" name=\"__name__\" id=\"__id__\" value=\"__value__\"/><br>\n\n" ;
	protected static $defaultLabelTemplate = "\t<label for=\"__id__\">__label__</label><br>\n" ;

	/**
	 * specific template for this field
	 * this can't be 'private', it **must** be 'protected' so, subclasses can override it
	 * @var [type]
	 */
	protected $_encloseStart ;
	protected $_encloseEnd ;
	protected $_template ;
	protected $_labelTemplate ;

 	/**
	 * @todo #24
	 * additional attributes to be set on html element
	 * @var array
	 */
	private $_attributes = array ();

	/**
	 * @var mixed a default value to be set to this field when reset value
	 */
	private $_defaultValue = null ;

	private $_emptyValue = null ;


	public static function setDefaultEncloseStart ( $defaultEncloseStart ) {
		static::$defaultEncloseStart = $defaultEncloseStart ;
	}

	public static function setDefaultEncloseEnd ( $defaultEncloseEnd ) {
		static::$defaultEncloseEnd = $defaultEncloseEnd ;
	}

	public static function setDefaultTemplate ( $defaultTemplate ) {
		static::$defaultTemplate = $defaultTemplate ;
	}

	public static function setDefaultLabelTemplate ( $defaultLabelTemplate ) {
		static::$defaultLabelTemplate = $defaultLabelTemplate ;
	}

	public static function getDefaultTemplate ( ) {
		return static::$defaultTemplate ;
	}

	public function setMaxLength ( $maxLength ) {
		$this->setAttribute('maxlength',$maxLength) ;
		return $this ;
	}

	/**
	 * only child classes will have it's own getInstance method
	 */
	// public static function getInstance ( $column , $label ) {
	// 	return new self ( $column , $label );
	// }

	/**
	 * Constructs the field
	 *
	 * @param string $column Field column name
	 * @param string $label Field label
	 * @throws \InvalidArgumentException
	 */
	public function __construct ( $column , $label ) {

		if ( empty ( $this->_encloseStart  ) ) $this->_encloseStart  = static::$defaultEncloseStart ;
		if ( empty ( $this->_encloseEnd    ) ) $this->_encloseEnd    = static::$defaultEncloseEnd ;
		if ( empty ( $this->_template      ) ) $this->_template      = static::$defaultTemplate ;
		if ( empty ( $this->_labelTemplate ) ) $this->_labelTemplate = static::$defaultLabelTemplate ;

		$this
            ->setColumn($column)
            ->setLabel($label)
            ->setPostKey($column)
            ->setType('text')
            ->setPrimaryKey(false)
            ->setRules([])
        ;

	}

	/**
	 * process something we need to process before create
	 * @return [type] [description]
	 */
	public function processCreate ( ) {

	}

	/**
	 * process something we need to process before update
	 * @return [type] [description]
	 */
	public function processUpdate ( ) {


	}



	public function setEmptyValue ( $emptyValue ) {
		$this->_emptyValue = $emptyValue ;
		return $this;
	}

	public function getEmptyValue ( ) {
		return $this->_emptyValue ;
	}

	public function setAttribute ( $key , $value ) {
		$this->_attributes[$key] = $value ;
		return $this ;
	}

	public function getAttributes ( ) {
		return $this->_attributes ;
	}

	public function getAttribute ( $key ) {
		return $this->_attributes[$key];
	}

	public function removeAttribute ( $key ) {
		unset ( $this->_attributes[$key] ) ;
		return $this ;
	}

	public function setPostKey ( $column ) {
		if ( !empty ( $column ) ) {
			$this->_postKey = $column;
		} else {
			$this->_postKey = '';
		}
		return $this ;
	}

	public function getPostKey ( ) {
		return $this->_postKey ;
	}

	public function setView ( \TotalFlex\View $View ) {
		$this->_view = $View ;
	}

	public function getView ( ) {
		return $this->_view ;
	}

    /**
     * Check if this field should be included in specific context(s)
     *
     * @param int Context(s) to check.
     * @return boolean Field's configuration to be included or not
     */
    // public function applyToContext($context) {
    public function isInContext($context) {
        return (($context & $this->getContexts()) !== 0);
    }

    public function setTemplate ( $template ) {
    	$this->_template = $template;
    	return $this;
    }

    public function getTemplate ( ) {
    	return $this->_template ;
    }

    /**
     * Gets the Column name
     *
     * @return string Column name
     */
    public function getColumn() {
        return $this->_column;
    }

    /**
     * Sets the Column name
     *
     * @param string Column name
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setColumn($column) {
    	if (empty($column)) {
    		throw new \InvalidArgumentException('Column name cannot be empty.');
    	}

        $this->_column = $column;
        return $this;
    }

    public function resetValue ( ) {
    	$this->_value = $this->_defaultValue ;
    }

    /**
     * Gets the Field label
     *
     * @return string Field label
     */
    public function getLabel() {
        return $this->_label;
    }

    /**
     * Sets the Field label
     *
     * @param string Field label
     * @return self
     */
    public function setLabel($label) {
        $this->_label = $label;
        return $this;
    }

    /**
     * Gets the HTML Input Type
     *
     * @return string HTML Input Type
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Sets the HTML Input Type
     *
     * @param string HTML Input Type
     * @return self
     */
    public function setType($type) {
        $this->_type = $type;
        return $this;
    }

    /**
     * Check if this is a primary key
     *
     * @return boolean Is a primary key
     */
    public function isPrimaryKey() {
        return $this->_primaryKey;
    }

    /**
     * Sets if this is a primary key
     *
     * @param boolean Is a primary key
     * @return self
     */
    public function setPrimaryKey($primaryKey=true) {
        $this->_primaryKey = $primaryKey;
        return $this;
    }

    /**
     * Gets the contexts this fields is allowed
     *
     * @return int The contexts this fields is allowed
     */
    public function getContexts() {
        return $this->_contexts;
    }

    /**
     * Sets the contexts this fields is allowed
     *
     * @param int The contexts this fields is allowed. See TotalFlex::Ctx* constants.
     * @return self
     */
    public function setContexts($contexts) {
        $this->_contexts = $contexts;
        return $this;
    }

    /**
     * Gets the rules to this field
     *
     * @return array[IRule] Rules to this field
     */
    public function getRules() {
        return $this->_rules;
    }

    /**
     * Sets the rules to this field
     *
     * @param array[IRule] Rules to this field
     * @return self
     */
    public function setRules($rules) {
        $this->_rules = $rules;
        return $this;
    }

    public function getValue ( ) {
    	if ( $this->_value === "" ) return $this->_emptyValue ;
    	return $this->_value;
    }

    public function setValue ( $value ) {
    	$this->_value = $value;
    	return $this ;
    }

    public function toHtml ( $context ) {

		$output     = $this->_encloseStart;

		if (!empty($this->getLabel())) {
			$out = str_replace ( '__id__'    , $this->getColumn() , $this->_labelTemplate );
			$out = str_replace ( '__label__' , $this->getLabel() , $out );
			$output .= $out;
		}

		$attributeList = $this->getAttributes ();
		$attributes = "";
		foreach ( $attributeList as $attrKey => $attrValue ) $attributes .= " $attrKey=\"$attrValue\" " ;

		$fieldTemplate = preg_replace ( '/^([^<]*<\w+)(\s*)(.*)/' , "$1 ".$attributes." $3" , $this->getTemplate () );

		$out = str_replace ( '__type__'  , $this->getType ()   , $fieldTemplate );
		$out = str_replace ( '__id__'    , "tf-field-".$this->getColumn () , $out );
		$out = str_replace ( '__name__'  , "TFFields[".$this->getView()->getName()."][$context][fields][".$this->getPostKey ()."]" , $out );
		$out = str_replace ( '__value__' , $this->getValue ()  , $out );
		$output .= $out ;

		$output .= $this->_encloseEnd;

		return $output;

    }

    /**
     * @todo
     * @param  [type] $context [description]
     * @return [type]          [description]
     */
    public function toJson ( $context ) {
    }

    /**
     * Adds a rule to the ruleset of this field
     *
     * @param IRule $rule The rule
     * @return self
     */
    public function addRule($rule) {
        $this->_rules[] = $rule;
        return $this;
    }

    /**
     * Validates a value with this fields rules
     *
     * @param mixed $value Value to validate
     * @return boolean Value validity
     */
    public function validate ( $context ) {

        $value = $this->getValue();
        foreach ( $this->_rules as $rule ) {
			if ( !$rule->validate ( $value ) ) return false;
        }

        return true;
        
    }

    public function skipOnCreate ( ) {
    	return $this->_skipOnCreate ;
    }

    public function skipOnUpdate ( ) {
    	return $this->_skipOnUpdate ;
    }

    /** talvez o "save" e "create" sejam a mesma coisa e devam ser resumidos em apenas um método */
    public function skipOnSave ( ) {
    	return $this->_skipOnUpdate ;
    	// return $this->_skipOnSave ;
    }

    public function skipOnSelect ( ) {
    	return $this->_skipOnSelect ;
    }

    public function skipOnSetData ( ) {
    	return $this->_skipOnSetData ;
    }

    /**
     * Return it's table to more semantic usage of TotalFlex
     *
     * @return TotalFlex\Table This field's table
     * @throws \RuntimeException when it haven't a table
     */
    // public function then() {
    // 	if ($this->_table === null) {
    // 		throw new \RuntimeException('This field is not associated to a table.');
    // 	}

    // 	return $this->_table;
    // }

}
