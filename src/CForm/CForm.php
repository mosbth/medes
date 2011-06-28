<?php
/**
 * Utility class to easy creating and handling of forms.
 *
 */
class CForm {

	/**
	 * Members
	 */
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
		$_SESSION['form-feedback-'] = serialize($this->feedback);
	}
	
	
	/**
	 * Clear feedback.
	 */
	public function ClearFeedback() {
		$this->feedback = array();
		//unset($_SESSION['form-feedback-' . $this->secret]);
		unset($_SESSION['form-feedback-']);
	}
	
	
	/**
	 * Add output as feedback to user.
	 */
	public function StoreInSession() {
		;
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
		if(isset($_SESSION['form-feedback-'])) {
			$this->feedback = array_merge($this->feedback, unserialize($_SESSION['form-feedback-']));
			unset($_SESSION['form-feedback-']);
		} 
		
		foreach($this->actions as $val) {
			if(isset($val['name']) && isset($_POST[$val['name']]) && isset($val['callback'])) {
				if($this->secretMatch) {
					call_user_func($val['callback']);
					return true;
				}
				else {
					$this->AddFeedback(array('class'=>'error', 'message'=>'Secret message does not match.'));
					return false;				
				}
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
		foreach($elements as $val) {
			$defaultId = "form-input-" . $i++;
			$id 		= isset($val['id']) ? "{$val['id']}" : $defaultId;
			$label	= isset($val['label']) ? ($val['label'] . (isset($val['mandatory']) && $val['mandatory'] ? "<span class='form-element-mandatory'> *</span>" : null)) : null;
			$class 	= isset($val['class']) ? " class='{$val['class']}'" : null;
			$name 	= isset($val['name']) ? " name='{$val['name']}'" : $defaultId;
			$script = isset($val['script']) ? " script='{$val['script']}'" : null;
			$onChange = isset($val['onChange']) ? " onChange='{$val['onChange']}'" : null;

			if(isset($val['type']) && $val['type'] == 'textarea') {
				$value 	= isset($val['value']) ? $val['value'] : null;
				$html .= "<p><label for='$id'>$label</label><br><textarea id='$id'{$class}{$name}{$script}{$onChange}>{$value}</textarea></p>\n";			
			} 
			else if(isset($val['type']) && $val['type'] == 'select') {
				$options = null;
				foreach($val['options'] as $opt) {
					$selected = isset($val['value']) && $opt['value'] == $val['value'] ? " selected=selected" : null ;
					$options .= "<option value='{$opt['value']}'{$selected}>{$opt['option']}</option>";
				}
				$html .= "<p><label for='$id'>$label</label><br><select id='$id'{$class}{$name}{$script}{$onChange}>{$options}</select></p>\n";			
			} 
			else {
				$type 	= isset($val['type']) ? " type='{$val['type']}'" : null;
				$value 	= isset($val['value']) ? " value='{$val['value']}'" : null;
				$html .= "<p><label for='$id'>$label</label><br><input id='$id'{$type}{$class}{$name}{$value}{$script}{$onChange} /></p>\n";			
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
			$value 	= isset($val['value']) ? " value='{$val['value']}'" : null;
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
