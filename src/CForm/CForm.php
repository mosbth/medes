<?php
/**
 * Utility class to easy creating and handling of forms.
 *
 */
class CForm {

	/**
	 * Members
	 */
	const sessionName = 'mds-form';
	public $id;					// Form id, default none
	public $name;				// Form name
	public $action;			// Form action, default none which is current url
	public $method;			// Form method, default post
	
	public $fieldsets;	// All fieldsets
	public $elements;		// All elements in the form
	public $actions;		// All actions/buttons in the form
	public $feedback;		// Feedback to user
	public $secret;			// hidden field with md5 hash to avoid bots using form submission, stored in session.
	public $secretMatch;	// Form was submitted and secret match.
	
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id 				= null;
		$this->name				= null;
		$this->action 		= null;
		$this->method 		= 'post';
		$this->fieldsets 	= array();
		$this->elements 	= array();
		$this->actions 		= array();
		$this->feedback		= array();
		$this->secret			= md5(microtime());
		$this->secretMatch = false;
	}


	/**
	 * Destructor
	 */
	public function __destruct() {}


	/**
	 * Add output as feedback to user.
	 */
	public function AddFeedback($feedback) {
		$this->feedback[] = $feedback;
		//$_SESSION['form-feedback-' . $this->secret] = serialize($this->feedback);
		$_SESSION[self::sessionName] = serialize($this->feedback);
	}
	
	
	/**
	 * Add feedback as success message.
	 */
	public function AddFeedbackSuccess($feedback) {
		$this->AddFeedback(array('class'=>'success', 'message'=>$feedback));
	}
	
	
	/**
	 * Add feedback as notice message.
	 */
	public function AddFeedbackNotice($feedback) {
		$this->AddFeedback(array('class'=>'notice', 'message'=>$feedback));
	}
	
	
	/**
	 * Add feedback as alert message.
	 */
	public function AddFeedbackAlert($feedback) {
		$this->AddFeedback(array('class'=>'alert', 'message'=>$feedback));
	}
	
	
	/**
	 * Add feedback as error message.
	 */
	public function AddFeedbackError($feedback) {
		$this->AddFeedback(array('class'=>'error', 'message'=>$feedback));
	}
	
	
	/**
	 * Clear feedback.
	 */
	public function ClearFeedback() {
		$this->feedback = array();
		//unset($_SESSION['form-feedback-' . $this->secret]);
		unset($_SESSION[self::sessionName]);
	}
	
	
	/**
	 * Add output as feedback to user.
	 */
	/*public function StoreInSession() {
		;
	}
	*/
	
	/**
	 * Set a value of a form element.
	 * @param string $key The key of the element.
	 * @param string $value The value to set.
	 */
	public function SetValue($key, $value) {
		if(isset($this->elements[$key])) {
			$this->elements[$key]['value'] = $value;
		} else {
			throw new Exception(t('Key does not exist in form.'));
		}
	}
	
	
	/**
	 * Get a value of a form element. Use instead of POST to get validated values.
	 * @param string $key The key of the element.
	 * @param string $value The value to set.
	 * @returns mixed the string or null if not set.
	 */
	public function GetValue($key) {
		if(isset($this->elements[$key])) {
			if(isset($this->elements[$key]['value'])) {
				return $this->elements[$key]['value'];
			} else {
				return null;
			}
		} else {
			throw new Exception(t('Key does not exist in form.'));
		}
	}


	/**
	 * Check if a checkbox is checked (perhaps valid for more types).
	 * @param string $key The key of the element.
	 * @param string $optionkey The key of the option to check.
	 * @returns boolean.
	 */
	public function IsOptionChecked($key, $optionkey) {
		if(isset($this->elements[$key])) {
			if(isset($this->elements[$key]['value'])) {
				if(is_array($this->elements[$key]['value'])) {
				  return in_array($optionkey, $this->elements[$key]['value']);
				} else {
				  echo "single?";
				  return $this->elements[$key]['value'] === $optionkey;
				}
			} else {
				return null;
			}
		} else {
			throw new Exception(t('Key does not exist in form.'));
		}
	}
	
	
	/**
	 * Check if the form was submitted, and validates and then call the callback.
	 *
	 * @return boolean returns on failure, else calls callback, if returning from callback, return true.
	 */
	public function CheckDoSubmitted() {
		// Fill form from session
/*
		if(isset($_POST['secret']) && isset($_SESSION['form-secret-' . $_POST['secret']])) {
			$this->secret = $_POST['secret'];
			$this->secretMatch = true;
			unset($_SESSION['form-secret-' . $this->secret]);

			// Check if feedback is set
			if(isset($_SESSION['form-feedback-' . $this->secret])) {
				$this->feedback = array_merge($this->feedback, unserialize($_SESSION['form-feedback-' . $this->secret]));
				unset($_SESSION['form-feedback-' . $this->secret]);
			} 
		}
*/
		$this->secretMatch = true;
		// Check if feedback is set
		if(isset($_SESSION[self::sessionName])) {
			//$this->feedback = array_merge($this->feedback, unserialize($_SESSION[self::sessionName]));
			$this->feedback = unserialize($_SESSION[self::sessionName]);
			unset($_SESSION[self::sessionName]);
		} 
		
		foreach($this->elements as $val) {
			if(isset($val['name']) && isset($_POST[$val['name']])) {
				if(isset($val['validate'])) {
					// Do validation of incoming value, santize, filter, make sure its correct
					$this->elements[$val['name']]['value'] = $_POST[$val['name']];
				} else {
				  // Get values from post array
					$this->elements[$val['name']]['value'] = $_POST[$val['name']];
				}
				if(empty($val['value']) && isset($val['mandatory'])) {
					// Should not submit form, resend instead and display error message.
				}
			} /* elseif(isset($val['name']) && isset($val['default'])) {
				// Has default values, use them
				$this->elements[$val['name']]['value'] = $val['default'];				
			} must be called before callback to enable defaults */
		}

		foreach($this->actions as $val) {
			if(isset($val['name']) && isset($_POST[$val['name']])) {
				if(!$this->secretMatch) {
					$this->AddFeedback(array('class'=>'error', 'message'=>'Secret message does not match.'));
					return false;				
				}
        if(isset($val['callback'])) {
          if(isset($val['callback-args'])) {
  					call_user_func($val['callback'], $this, $val['callback-args']);
  				} else {
  					call_user_func($val['callback'], $this);
  				}
				}
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * Get the form as HTML.
	 *
	 * @return string The form as HTML. 
	 */
	public function GetHTML() {	
		//$_SESSION['form-secret-' . $this->secret] = $this->secret;
		$id 		= isset($this->id) ? " id='{$this->id}'" : null;
		$class 	= isset($this->class) ? " class='{$this->class}'" : null;
		$name 	= isset($this->name) ? " name='{$this->name}'" : null;
		$action = isset($this->action) ? " action='{$this->action}'" : null;
		$method = " method='{$this->method}'";
		$elements = $this->GetHTMLForElements($this->elements);
		$actions 	= $this->GetHTMLForActions($this->actions);
		$feedback	= $this->GetHTMLForFeedback($this->feedback);
		$this->ClearFeedback();
		$html = <<< EOD
\n<form{$id}{$class}{$name}{$action}{$method}>
<input type='hidden' name='secret' value='{$this->secret}'/>
<fieldset>
{$feedback}
{$elements}
{$actions}
</fieldset>
</form>
EOD;
		return $html;
	}


	/**
	 * Get HTML for all elements within a fieldset (not yet supported).
	 *
	 * @return string The HTML for the elements. 
	 */
	public function GetHTMLForElements($elements) {
		$html = null;
		$i = 0;
		foreach($elements as $key => $val) {
			$defaultId = "form-input-" . $i++;
			$id 		= isset($val['id']) ? "{$val['id']}" : $defaultId;
			$label	= isset($val['label']) ? ($val['label'] . (isset($val['mandatory']) && $val['mandatory'] ? "<span class='form-element-mandatory'> *</span>" : null)) : null;
			$class 	= isset($val['class']) ? " class='{$val['class']}'" : null;
			$onlyname = isset($val['name']) ? $val['name'] : $key;
			$name 	= " name='{$onlyname}'";
			$script = isset($val['script']) ? " script='{$val['script']}'" : null;
			$onChange = isset($val['onChange']) ? " onChange='{$val['onChange']}'" : null;
			$autofocus = isset($val['autofocus']) && $val['autofocus'] ? " autofocus='autofocus'" : null;

			if(isset($val['type']) && $val['type'] == 'textarea') {
				$value 	= isset($val['value']) ? $val['value'] : null;
				$html .= "<p><label for='$id'>$label</label><br><textarea id='$id'{$class}{$name}{$script}{$onChange}{$autofocus}>{$value}</textarea></p>\n";			
			} 
			else if(isset($val['type']) && $val['type'] == 'hidden') {
				$value 	= isset($val['value']) ? " value='{$val['value']}'" : null;
				$html .= "<input type='hidden' id='$id'{$class}{$name}{$script}{$onChange}{$autofocus}{$value}/>\n";			
			} 
			else if(isset($val['type']) && $val['type'] == 'select') {
				$options = null;
				foreach($val['options'] as $optkey => $optval) {
					$selected = isset($val['value']) && $optkey == $val['value'] ? " selected=selected" : null;
					$value = (is_array($optval)) ? $optval['label'] : $optval;
					$title = (is_array($optval)) ? " title='{$optval['description']}'" : null;
					$options .= " <option{$title} value='{$optkey}'{$selected}>{$value}</option>\n";
				}
				$html .= "<p>\n<label for='$id'>$label</label><br>\n<select id='$id'{$class}{$name}{$script}{$onChange}{$autofocus}>\n{$options}</select>\n</p>\n\n";			
			} 
			else if(isset($val['type']) && $val['type'] == 'checkbox') {
				$options = null;
				$name = substr($name, 0, strlen($name)-1) . "[]'";
				foreach($val['options'] as $optkey => $optval) {
					$checked = $this->IsOptionChecked($onlyname, $optkey) ? " checked" : null;
					$options .= " <input id='$id' type='checkbox'{$class}{$name}{$script}{$onChange} value='{$optkey}'{$checked}/>{$optval}<br/>\n\n";
				}
				$html .= "<p>\n<label for='$id'>$label</label><br>\n{$options}</p>\n";			
			} 
			else {
				$type 	= isset($val['type']) ? " type='{$val['type']}'" : null;
				$value 	= isset($val['value']) ? " value='{$val['value']}'" : null;
				$html .= "<p><label for='$id'>$label</label><br><input id='$id'{$type}{$class}{$name}{$value}{$script}{$onChange}{$autofocus} /></p>\n";			
			}
		}
		return $html;
	}
	

	/**
	 * Get HTML for all actions, or buttons that define the form actions.
	 *
	 * @return string The HTML for the actions. 
	 */
	public function GetHTMLForActions($actions) {
		$html = null;
		foreach($actions as $val) {
			$id 		= isset($val['id']) ? " id='{$val['id']}'" : null;
			$type 	= isset($val['type']) ? " type='{$val['type']}'" : null;
			$class 	= isset($val['class']) ? " class='{$val['class']}'" : null;
			$name 	= isset($val['name']) ? " name='{$val['name']}'" : null;
			$value 	= isset($val['value']) ? " value='" . t($val['value']) . "'" : null;
			$disabled = isset($val['disabled']) && $val['disabled'] == true ? " disabled='disabled'" : null;
			$html .= "<input{$id}{$type}{$class}{$name}{$value}{$disabled} />\n";
		}
		return $html;
	}


	/**
	 * Get HTML for feedback of form submission.
	 *
	 * @return string The HTML for the feedback. 
	 */
	public function GetHTMLForFeedback($feedback) {
		$html = null;
		foreach($feedback as $val) {
			$html .= "<p><output class='{$val['class']}'>{$val['message']}</output></p>\n";
		}
		return $html;
	}


} // End of class
