<?php

final class QuickForm
{
	protected $inputData = array(); // usually $postData
	protected $fieldData = array(); // stores field-description array
	
	protected $errors = array(); // validation errors (field_name => error string)
	protected $customErrors = array(); // validation errors (strings)
	protected $alerts = false; // selectors for DOM elements to witch is added 'alertERROR' css class
	private $logasID = false; // data stored after validation (WARNING: this data is filtered, it does not correspond to inputData values)
	

	
	//
	// CONSTRUCTOR & CONSTRUCTION HELPERS
	//
	
	function __construct(array $inputData, array $field_array, $validate = true, $logasID = false)
	{
		// log data if enabled
		$this->logasID = $logasID;
		if($this->logasID) {
			Cogumelo::addLog('INPUT: '.print_r($inputData , true) , 'QF_'.$this->logasID);
		}

		$this->type_errors['STRING'] = T_('Debe introducir texto en el campo [field_name]');
		$this->type_errors['FILE'] = T_('El campo [field_name] debe contener un nombre de archivo válido');
		$this->type_errors['URL'] = T_('El campo [field_name] debe contener una URL válida (La URL debe empezar por "http://")');
		$this->type_errors['INT'] = T_('El campo [field_name] debe contener un número entero');
		$this->type_errors['INT[]'] = T_('El valor \'[field_value]\' del campo [field_name] no es un número entero');
		$this->type_errors['FLOAT'] = T_('El campo [field_name] debe contener un número');	
		$this->type_errors['STRICT_DECIMAL'] = T_('El campo [field_name] solo puede contener números o una coma para separar decimales');	
		$this->type_errors['DATE'] = T_('El campo [field_name] debe contener una fecha válida');
		$this->type_errors['FILELIFT'] = T_('En el campo [field_name] debe haber un archivo válido');
		$this->type_errors['EMAIL'] = T_('El campo [field_name] debe contener una dirección de email válida');
		$this->type_errors['PREGMATCH'] = T_('El campo [field_name] debe coincidir con la expresión \'[option_value]\'');
		$this->type_errors['DNI'] = T_('El campo [field_name] debe ser un DNI válido');
		$this->type_errors['NIF'] = T_('El campo [field_name] debe ser un NIF válido');

	
		$this->field_options['required'] = T_('El campo [field_name] es obligatorio');
		$this->field_options['maxlength'] = T_('El campo [field_name] debe tener como máximo [option_value] caracteres');
		$this->field_options['minlength'] = T_('El campo [field_name] debe tener como mínimo [option_value] caracteres');
		$this->field_options['max'] = T_('El valor \'[field_value]\' del campo [field_name] no puede ser mayor a [option_value]');
		$this->field_options['min'] = T_('El valor \'[field_value]\' del campo [field_name] no puede ser menor a [option_value]');
		$this->field_options['maxcount'] = T_('El campo [field_name] no puede tener asignados más de [option_value] elemento(s)');
		$this->field_options['mincount'] = T_('El campo [field_name] no puede tener asignados menos de [option_value] elemento(s)');
		$this->field_options['format'] = T_('El campo [field_name] debe contener una fecha válida con el formato "[option_value]"');
		$this->field_options['profile'] = '';
		$this->field_options['category'] = '';
		$this->field_options['regexp'] = '';
		
		
		
		$this->inputData = $inputData;

		$this->parseFields( $field_array, $this->fieldData );

		if($validate)
			$this->Validate();
	}
	
	
	// recursive parse & store fields 
	// @param slot (where to store field data) by reference
	// @param reference stores key of parent's array (infinite recursion)
	private function parseFields($field_array, &$slot, $reference = null)
	{
		foreach($field_array as $name => $definition)
		{
			// infinite recursion
			if( is_array($definition) )
			{
				$slot[$name] = $reference = array();
				$reference[$name] = null;
				$this->parseFields( $definition, $slot[$name], $reference );
				$reference = null;
			}
			else
				$this->storeField( $name, $definition, $slot, $reference );
		}
	}
	
	private function storeField($name, $definition, &$slot, $reference)
	{	
		// explode field options
		$opts = explode(' ', $definition);
		
		// store field name & type
		$field = array(
			'name' => $name,
			'type' => $opts[0],
			'reference' => $reference,
			'value' => null
		);
		
		// iteract over options except type ($opts[0])
		array_splice($opts, 0, 1);
		foreach($opts as $opt)
		{
			// read and store option
			if ( $newoption = $this->readOption($opt) )
				$field['options'][$newoption->name] = $newoption->value;
		}
		if( !isset($field['options']) )
			$field['options'] = array();
		
		// store field as stdClass object
		if( !isset($slot[$name]) || is_null($slot[$name]) )
			$slot[$name] = (object) $field;
		else
			$slot[][$name] = (object) $field;
	}
	
	private function readOption($string)
	{
		foreach( array_keys($this->field_options) as $name)
		{
			$regexp = "#{$name}=(.+)#";
			if( preg_match("#{$name}=(.+)#", $string, $matches) )
				return (object) array(
					'name' => $name,
					'value' => $matches[1]
				);
		}
		return false;
	}
	
	//
	// VALIDATION
	//
	
	protected function ValidateField(stdClass $field_obj, &$validData)
	{
		$field_value = $field_obj->value;
		$valid_value = false;
			
		// field is required ?
		if( isset($field_obj->options['required']) && (bool) $field_obj->options['required'] )
			if( is_null($field_value) || $field_value == '' ):
				$this->setOptionError('required', $field_obj);
				goto StoreValue;
			endif;
			
		// validate by type
		switch($field_obj->type):
		
			case 'STRING':
				// check maxlength
				if( isset($field_obj->options['maxlength']) && is_numeric($max = $field_obj->options['maxlength']) )
					if( strlen($field_value) > (int) $max )
						$this->setOptionError('maxlength', $field_obj);
						
				// check minlength
				if( isset($field_obj->options['minlength']) && is_numeric($min = $field_obj->options['minlength']) )
					if( strlen($field_value) < (int) $min )
						$this->setOptionError('minlength', $field_obj);
						
				$valid_value = $field_value;
				break;
				
			case 'FILE':
				if( strlen($field_value) > 255 )
					$this->setOptionError('FILE', $field_obj);
				
				$valid_value = $field_value;
				break;
			
			case 'FILELIFT':
				if( !$field_obj->options['profile'] || !$field_obj->options['category']  )
					Cogumelo::Error(__CLASS__." cannot validate '{$field_obj->name}' as FILELIFT field if profile or category are not defined");
				
				Cogumelo::LoadModule('FileLift');
				FileLift::Load('FileLiftStorer');
				
				if(  !( $profile = FileLift::SecurityCheck($field_obj->options['profile']) )  )
					Cogumelo::Error(__CLASS__." cannot validate '{$field_obj->name}' as FILELIFT field because profile '{$field_obj->options['profile']}' is not valid");
				
					
				$storer = new FileLiftStorer($profile);
				
				if($field_value)
				{
					$valid_value = $storer->Find(array(
						'status' => FILELIFT_STATUS_UPLOADED,
						'category' => $field_obj->options['category'],
						'filename' => $field_value
					));
					if( !file_exists($valid_value) )
						$this->setOptionError('FILELIFT', $field_obj);
				}
				break;
		
				
			case 'URL':
				//$regexp = '#^http://[a-z0-9][a-z0-9-]{1,}?\.?[a-z0-9-]*\.?[a-z0-9]{3}?.[a-z]{2,}(/[a-z0-9-])?/?$#i';
				$regexp = '#^https?://([a-z0-9][a-z0-9-]*\.)+[a-z]{2,}(/[a-z0-9-%_])*#i';
				if( !preg_match( $regexp, $field_value ) )
					$this->setOptionError('URL', $field_obj);
				
				$valid_value = $field_value;
				break;
				
			case 'INT':
				if( !is_numeric($field_value) && !( is_null($field_value) || $field_value == '' ) ) 
					$this->setOptionError('INT', $field_obj);
					
				// check max
				if( isset($field_obj->options['max']) && is_numeric($max = $field_obj->options['max']) )
					if( $field_value > (int) $max )
						$this->setOptionError('max', $field_obj);
						
				// check min
				if( isset($field_obj->options['min']) && is_numeric($min = $field_obj->options['min']) )
					if( $field_value < (int) $min )
						$this->setOptionError('min', $field_obj);
				
				$valid_value = (int) $field_value;
				break;

			case 'DNI':
				//remove all non standard chars
				$form = strtoupper($field_value);

				if( $field_value == '' ) {
					$valid_value = '';
				}
				elseif( preg_match('/^[TXYZ]?[0-9]+[A-Z]$/i', $form) ) //DNI : non sabemos por qué está o dixito T contemplado co mesmo tratamento que a X, pero está así dende o principio
				{

					$last_digit = strtr( substr($form, -9, 1), "TXYZ", "0012"); //DNI : non sabemos por qué está o dixito T contemplado co mesmo tratamento que a X, pero está así dende o principio

					$form = $last_digit . substr($form, -8, 8);

					//check the letter of DNI or NIE
				 	$control = substr($form, 0, 8) % 23;


					if (substr($form, 8, 1) == $control || strtoupper( substr($form, 8, 1) ) == substr('TRWAGMYFPDXBNJZSQVHLCKE', $control, 1) ) {
						$valid_value = $field_value;
					}
					else{
						$this->setOptionError('DNI', $field_obj);
					}
				}
				else{
					$this->setOptionError('DNI', $field_obj);
				}	
				break;		
						
				
			//	SEN PROBAR!!!!
			case 'CIF':
				//remove all non standard chars
				$form = $field_value;
				$form = preg_replace( '/[^0-9A-Z]/i', '', $form);
				if (preg_match('/[A-HJ-NP-SUVW][0-9]{7}[A-J0-9]/i', $form)) //CIF
				{
					//summ all even digits
					$sum = 0;
					for ($i=2; $i<strlen($form)-1; $i+=2)
					{
						$sum += substr($form, $i, 1);
					}
					// x2 all odd position digits and sum all of them
					for ($i=1; $i<strlen($form)-1; $i+=2)
					{
						$t = substr($form, $i, 1) * 2;
						//Agregate the multiply result to the sum of the digits
						//$sum += ($t>9)?($t-9):$t;
						$sum += ($t>9) ? 1+($t%10) : $t;
					}
						
					//Rest to 10 the last digit of the sum
					$control = 10 - ($sum % 10);
						
					//the control can be a numbber or letter
					if ( substr($form, 8, 1) == $control ||	strtoupper(substr($form, 8, 1)) == substr('JABCDEFGHI', $control, 1 ))
						$valid_value[] = $field_value;
					}
				else{
					$this->setOptionError('CIF', $field_obj);
				}
				break;
		
			case 'INT[]':
				if( (bool) $field_obj->options['required'] )
				if( !is_array($field_value)
					|| ( count($field_value) == 1 && $field_value[0] === '' ) 
				):
					$this->setOptionError('required', $field_obj);
					goto StoreValue;
				endif;
				// a not-required field may be null
				// prevent fatal errors breaking on this case
				if( is_null($field_value) ) break;
				
				foreach($field_value as $position => $value)
				{
					$field_obj->value = $value;
					
					if( !is_numeric($value) ):
						$this->setOptionError('INT[]', $field_obj);
						continue;
					endif;
					
					// check max
					if( isset($field_obj->options['max']) && is_numeric($max = $field_obj->options['max']) )
						if( $value > (int) $max )
							$this->setOptionError('max', $field_obj);
					
					// check min
					if( isset($field_obj->options['min']) && is_numeric($min = $field_obj->options['min']) )
						if( $value < (int) $min )
							$this->setOptionError('min', $field_obj);
						
					$valid_value[] = (int) $value;
				}
				// check maxcount
				if( isset($field_obj->options['maxcount']) && is_numeric($max = $field_obj->options['maxcount']) )
				{
					if( count($value) > (int) $max )
						$this->setOptionError('maxcount', $field_obj);
				}
				// check mincount
				if( isset($field_obj->options['mincount']) && is_numeric($min = $field_obj->options['mincount']) )
					if( count($value) < (int) $min )
						$this->setOptionError('mincount', $field_obj);
				break;
			case 'FLOAT':
				if( !is_numeric($field_value) && !( is_null($field_value) || $field_value == '' ) ) 
					$this->setOptionError('FLOAT', $field_obj);
					
				// check max
				if( isset($field_obj->options['max']) && is_numeric($max = $field_obj->options['max']) )
					if( $field_value > (int) $max )
						$this->setOptionError('max', $field_obj);
						
				// check min
				if( isset($field_obj->options['min']) && is_numeric($min = $field_obj->options['min']) )
					if( $field_value < (int) $min )
						$this->setOptionError('min', $field_obj);
				
				$valid_value = (float) $field_value;
				break;

			case 'STRICT_DECIMAL':
				$regexp = '/^-?\d+(,\d+)?$/';
				if(!preg_match($regexp, $field_value) && $field_value !== "")
					$this->setOptionError('STRICT_DECIMAL', $field_obj);
				// check max
				if( isset($field_obj->options['max']) && is_numeric($max = $field_obj->options['max']) )
					if( $field_value > (int) $max )
						$this->setOptionError('max', $field_obj);
						
				// check min
				if( isset($field_obj->options['min']) && is_numeric($min = $field_obj->options['min']) )
					if( $field_value < (int) $min )
						$this->setOptionError('min', $field_obj);
				
				$valid_value = $field_value;
				break;

			case 'DATE':
				switch($field_obj->options['format'])
				{
					case 'DD-MM-YYYY':
						list($day, $month, $year) = explode('-', $field_value);
						break;
					default:
						Cogumelo::Error(__CLASS__." does not suport '{$field_obj->options['format']}' as date format");
				}
				$error1 = ( !is_numeric($month) || !is_numeric($day) || !is_numeric($year) );
				$error2 = ( !checkdate( (int)$month, (int)$day, (int)$year) );
				
				if( $error1 || $error2 )
					$this->setOptionError('format', $field_obj);
				
				$valid_value = (object) array(
					'day' => $day,
					'month' => $month,
					'year' => $year
				);
				break;	
				
			case 'EMAIL':
				$regexp = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*(\+[\._a-zA-Z0-9-]+)?@[a-z0-9-]+(\.[a-z0-9-]+)*\.[a-z]{2,}$/';
				if( (!preg_match($regexp, $field_value)&& $field_value !== "") || strlen($field_value) > 255 ) //should be max 255 chars
					$this->setOptionError('EMAIL', $field_obj);
				
				$valid_value = $field_value;
				break;
				
			case 'PREGMATCH':
				if( !$field_obj->options['regexp'] )
					Cogumelo::Error(__CLASS__." cannot validate '{$field_obj->name}' as PREGMATCH field if regexp is not defined as option");
					
				if( !preg_match($field_obj->options['regexp'], $field_value) > 0 )
					$this->setOptionError('PREGMATCH', $field_obj);
					
				$valid_value = $field_value;
				break;
			
			default:
				Cogumelo::Error(__CLASS__." cannot validate '{$field_obj->type}' as field type");

		endswitch;
		
		// store valid value
		StoreValue:
				$validData[$field_obj->name] = $valid_value;
	}
	
	protected function ValidateGroup($name, $field_obj_data, $inputData, &$validData)
	{
		$inputData = ( is_null($inputData))? array() : $inputData;
		foreach( $inputData as $key => $value)
		{
			foreach( $field_obj_data as $field_obj )
				$field_obj->reference[$name] = $key;
			
			$validData[$key] = array();
			$this->ValidateWalk($field_obj_data, $value, $validData[$key] );
		}
	}
	
	protected function ValidateWalk(array $fieldData, $inputData, &$validData)
	{
		foreach($fieldData as $key => $field_obj)
		{
			if( is_array($field_obj) )
			{
				$validData[$key] = array();
				$this->validateGroup($key, $field_obj, isset($inputData[$key])? $inputData[$key] : null, $validData[$key]);
			}
			else
			{
				$field_obj->value = isset($inputData[$field_obj->name])? $inputData[$field_obj->name] : null;
				$this->ValidateField( clone $field_obj, $validData);
			}
		}
	}
	
	public function Validate()
	{
		$this->ValidateWalk( $this->fieldData, $this->inputData, $this->validData );
	}
	
	//
	// ERROR HANDLING
	//
	
	
	private function setOptionError($option_name, stdClass $field)
	{
		// construct field's name based on reference
		if( !is_null($field->reference) )
		{
			$field_name = '';
			foreach($field->reference as $name => $key)
				$field_name .= ($field_name)? "[$name][$key]" : "{$name}[$key]";
			$field->name = "{$field_name}[{$field->name}]";
		}
		
		$tag_replace = array(
			'[field_name]' => "'{$field->name}'",
			'[field_value]' => $field->value
		);
		if( isset($field->options[$option_name]) )
			$tag_replace += array('[option_value]' => $field->options[$option_name]); 
		
		// set default alert & optional custom alert
		$this->setAlert( "*[name={$field->name}]" );
		
		if( isset($field->options['alert']) )
			$this->setAlert( $field->options['alert'] );
		
		$errors = array_merge($this->type_errors, $this->field_options);
			
		return $this->errors[$field->name] = str_replace(
			array_keys($tag_replace),
			array_values($tag_replace),
			$errors[$option_name]
		);
	}
	
	public function setError($error_string, $alert_selector = false)
	{
		if( $alert_selector ) $this->setAlert($alert_selector);
		return $this->customErrors[] = $error_string;
	}
	
	public function setAlert($selector)
	{
		if( is_array($selector) )
			foreach( $selector as $s )
				$this->setAlert($s);
			
		if( !is_string($selector) )
			return false;
		
		return $this->alerts .= ( $this->alerts == '' )? $selector : ', '.$selector;
	}
	
	public function getErrors()
	{
		return $this->errors;
	}
	
	public function hasErrors()
	{
		return ( count($this->errors) > 0 || count($this->customErrors) > 0 );
	}
	
	//
	// DATA DUMP
	//
	
	public function dieJSON(array $customData = array())
	{

		$outputdata = json_encode( array(
			'errors' => $this->errors,
			'success' => !$this->hasErrors(),
			'custom' => $this->customErrors,
			'alerts' => $this->alerts,
			'data' => $customData
		) );

		// Log output JSON
		if($this->logasID) {
			Cogumelo::addLog('OUTPUT: '.print_r($outputdata , true) , 'QF_'.$this->logasID);
		}

		header("Content-Type: application/json");
		die( $outputdata );
	}
	
	// @param $stdClass (bool): set to false for a pure array return. The hash is converted to stdClass object by default
	public function dumpData($stdClass = true)
	{
		return ($stdClass)? (object) $this->validData : $this->validData;
	}
	
	public function getFieldData()
	{
		return (object) $this->fieldData;
	}
}
