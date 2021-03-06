<?php

namespace TotalFlex\View\Formatter;
use TotalFlex\View\Formatter\ViewFormatterAbstract;
use TotalFlex\View\Formatter\ViewFormatterInterface;

class Html extends ViewFormatterAbstract implements ViewFormatterInterface {

	/**
	 * @var array The parsing queue
	 */
	// private $_queue = array ( );

	/**
	 * template content to generate output
	 * @var array
	 */
	// ISSO SERÁ ABANDONADO! O TEMPLATE SER´A DE RESPONSABILIDADE DO PR´OPRIO FIELD
	public static $defaultTemplateCollection = array (

		// 'start'   => "",
		// 'label'   => "\t<label for=\"__id__\">__label__</label><br>\n" ,
		// 'message' => "<div class=\"msg msg-__type__\">__message__</div>" ,
		// 'end'     => "" ,

		'form'    => array (
			'start' => "<form action=\"__action__\" method=\"__method__\" enctype=\"__enctype__\" >\n" ,
			'end'   => "</form>\n" ,
		),

	);

	protected $_templateCollection = array ( );

	/**
	 * @inheritdoc
	 */
	public function __construct ( Array $userTemplateCollection = array ( ) ) {
		$this->_templateCollection = array_merge ( Html::$defaultTemplateCollection , $userTemplateCollection );
	}

	/**
	 * @inheritdoc
	 * @param $View TotalFlex\View object with table configuration
	 * @param $context just one context, this method does not accept many context in a single call
	 */
	public function generate ( \TotalFlex\View $View , $context ) {

		if ( !in_array ( $context , array ( \TotalFlex\TotalFlex::CtxNone , \TotalFlex\TotalFlex::CtxCreate , \TotalFlex\TotalFlex::CtxRead , \TotalFlex\TotalFlex::CtxUpdate , \TotalFlex\TotalFlex::CtxDelete ) ) ) throw new \Exception ( "Please generate one context at a time" );

		$form = $this->_templateCollection['form']['start'];
		$form = str_replace ( "__method__"  , $View->getForm()->getMethod()  , $form ) ;
		$form = str_replace ( "__action__"  , $View->getForm()->getAction()  , $form ) ;
		$form = str_replace ( "__enctype__" , $View->getForm()->getEnctype() , $form ) ;

		// $form .= $this->generateField( \TotalFlex\Field\Hidden::getInstance ( "context" , null )->setValue($context) );
		// $form .= "<input type=\"hidden\" name=\"TFFields[".$View->getName()."][context]\" value=\"$context\" id=\"totalflex-context\" />\n";
		// $form .= "<input type=\"hidden\" name=\"TFFields[".$View->getName()."][view]\" value=\"".$View->getName()."\" id=\"totalflex-view\" />\n";

		foreach ($View->getFields() as $Field) {
			if (!$Field->isInContext($context)) continue;
			$form .= $Field->toHtml ( $context );
		}

		if ( $context === \TotalFlex\TotalFlex::CtxUpdate ) {
			// precisa adicionar o primary key
			// precisa também de um campo de proteção
			$primaryKeyFieldList = $View->getPrimaryKeyFields();
			$hashSource = "";
			foreach ( $primaryKeyFieldList as $pkField ) {
				$hashSource = $pkField->getColumn()."=".$pkField->getValue();
				$form .= "<input type=\"hidden\" name=\"TFFields[".$View->getName()."][$context][fields][".$pkField->getColumn()."]\" value=\"".$pkField->getValue()."\" />\n" ;
				// $form .= $this->generateField ( $pkField , $context );
			}
			$hash = sha1 ( md5 ( $hashSource ) . \TotalFlex\TotalFlex::SECURITY_SALT );
			$form .= "<input type=\"hidden\" name=\"TFFields[".$View->getName()."][$context][validation_hash]\" value=\"$hash\" />\n" ;

		}

		$form .= $this->_templateCollection['form']['end'];

		return $form;

	}




	/**
	 * @inheritdoc
	 */
	// public function addField ( TotalFlex\Field\Field $Field ) {
	// // public function addField($id, $label, $type, $attributes = array ( )) {
	// 		// $field->getColumn(),
	// 		// 	$field->getLabel(),
	// 		// 	$field->getType()

	// 	$this->_queue[] = ['field', $Field->getColumn() , $Field->getLabel() , $Field->getType() , $Field->getAttibutes() ];
	// }

	/**
	 * @inheritdoc
	 */
	// public function addMessage($message, $type) {
	// 	$this->_queue[] = ['message', $type];
	// }

	/**
	 * Generate field HTML.
	 *
	 * @param string $id The field ID
	 * @param string $label Visual label to the field
	 * @param string $type HTML Input Type
	 * @param string $value Pre-filled value.
	 * @return string The field HTML
	 */
	// protected function generateField ( \TotalFlex\Field\Field $Field , $context ) {

		// $output     = $this->_templateCollection['start'];

		// if (!empty($Field->getLabel())) {
		// 	$out = str_replace ( '__id__'    , $Field->getColumn() , $this->_templateCollection['label'] );
		// 	$out = str_replace ( '__label__' , $Field->getLabel() , $out );
		// 	$output .= $out;
		// }

		// $fieldTemplate = ( $Field->getTemplate () === null ) ? $this->_templateCollection['input'][$Field->getType()] : $Field->getTemplate () ;

		// $attributeList = $Field->getAttributes ();
		// $attributes = "";
		// foreach ( $attributeList as $attrKey => $attrValue ) $attributes .= " $attrKey=\"$attrValue\" " ;

		// $fieldTemplate = preg_replace ( '/^([^<]*<\w+)(\s*)(.*)/' , "$1 ".$attributes." $3" , $fieldTemplate);

		// $out = str_replace ( '__type__'  , $Field->getType ()   , $fieldTemplate );
		// $out = str_replace ( '__id__'    , "tf-field-".$Field->getColumn () , $out );
		// $out = str_replace ( '__name__'  , "TFFields[".$Field->getView()->getName()."][$context][fields][".$Field->getPostKey ()."]" , $out );
		// $out = str_replace ( '__value__' , $Field->getValue ()  , $out );
		// $output .= $out ;

		// $output .= $this->_templateCollection['end'];

		// return $output;

	// }

	/**
	 * Generate button HTML.
	 *
	 * @param string $id The field ID
	 * @param string $label Visual label to the field
	 * @param string $type HTML Input Type
	 * @param string $value Pre-filled value.
	 * @return string The field HTML
	 */
	// protected function generateButton ( \TotalFlex\Button $Button , $context ) {
	// 	return "$Button" ;
	// }

	/**
	 * Generate message HTML
	 *
	 * @param string $message The message
	 * @param int $type Message type.
	 * @return string The message HTML
	 */
	protected function _generateMessage($message, $type) {
		$output = str_replace ( 'type' , $type , $this->_templateCollection['message'] );
		$output = str_replace ( 'message' , $message , $output );
		return $output ;
	}

}
