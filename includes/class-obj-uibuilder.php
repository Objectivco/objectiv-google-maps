<?php
// Prevent direct access
defined( 'WPINC' ) || header( 'HTTP/1.1 403' ) & exit;

class Obj_Gmaps_UIBuilder {
	private $name_id_prefix = null;
	
	function __construct( $name_id_prefix='' ) {
		$this->name_id_prefix = $name_id_prefix;
	}
	
	public function get_name_id( $field_name ) {
		return $this->name_id_prefix.'_'.$field_name;
	}
	
	/*
	 * Function name: selectbox
	 * Purpose: Builds and echos html for a selectbox.
	 * Agruements:
	 *	-$field_name: Value of name attribute for form submission. 
	 * 		Doubles as the field's id.
	 *	-$options: An array of options to populate the selectbox with. 
	 * 		Formatted as value => text.
	 *	-$selected: Value of option to be selected by default.'
	 *  	If multiple is true, this can be an array of values.
	 *	-$default_option: Text to be displayed in the default option. 
	 * 		Blank or false for no default option.
	 *  -$multiple: Boolean value indicates that multiple options can be selected.
	 */
	public function selectbox( $field_name, $options, $class=false, $selected='', 
						$default_option='Select an option', 
						$use_prefix=true, $multiple=false, $disabled=false ) {
		
		$field_name_id = $field_name;
		if( $use_prefix )
			$field_name_id = $this->get_name_id($field_name);
		$field_name = $field_name_id;
		$field_id = $field_name_id;
		if( $multiple )
			$field_name .= '[]';
		
		$attrs = '';
		if( $multiple )
			$attrs[] .= 'multiple="multiple"';
		if( $disabled )
			$attrs[] .= 'disabled="disabled"';
		if( $class )
			$attrs[] .= 'class="'.$class.'"';
		$attrs = implode(' ', $attrs);
		
		if( !is_array($selected) )
			$selected = array($selected);
		
		$output = '<select name="'.$field_name.'" id="'.$field_id.'"'.
			$attrs.'>'."\n";
		if( !empty($default_option) )
			$output .= '<option value="">'.$default_option.'</option>'."\n";
		foreach( $options as $value => $text ) {
			$is_selected = (!empty($selected) && in_array($value, $selected)) ? 
				' selected="selected"' : '';
			$output .= '<option value="'.$value.'"'.$is_selected.'>'.$text.'</option>';
		}
		$output .= '</select>'."\n";
		
		echo $output;
	}
	
	/*
	 * Function name: selectbox_posts
	 * Purpose: Builds and echos html for a selectbox to select a post id.
	 * Agruements:
	 *	-$field_name: Value of name attribute for form submission. 
	 * 		Doubles as the field's id.
	 *	-$posts: An array of WP Post objects to populate selectbox options.
	 *	-$selected: Value of option to be selected by default.
	 *	-$default_option: Text to be displayed in the default option. 
	 * 		Blank or false for no default option.
	 */
	public function selectbox_posts( $field_name, $posts, $class='', $selected='', 
						$default_option='Select an option',
						$use_prefix=true, $multiple=false ) {
		//Do nothing if no posts were provided
		if( empty($posts) || !is_array($posts) )
			return;
		
		//Format post array into select options
		$options = array();
		foreach( $posts as $post_obj ) {
			$options[$post_obj->ID] = $post_obj->post_title;
		}
		
		//Output selectbox
		$this->selectbox( $field_name, $options, $selected, 
			$default_option, $use_prefix, $multiple, $class);
	}
	
	/*
	 * Function name: selectbox_terms
	 * Purpose: Builds and echos html for a selectbox to select a term id
	 * Agruements:
	 *	-$field_name: Value of name attribute for form submission. 
	 * 		Doubles as the field's id.
	 *	-$terms: An array of WP Term objects to populate selectbox options.
	 *	-$selected: Value of option to be selected by default.
	 *	-$default_option: Text to be displayed in the default option. 
	 * 		Blank or false for no default option.
	 */
	public function selectbox_terms( $field_name, $terms, $class='', $selected='', 
						$default_option='Select an option', 
						$use_prefix=true, $multiple=false ) {
		//Do nothing if no posts were provided
		if( empty($terms) || !is_array($terms) )
			return;
		
		//Format post array into select options
		$options = array();
		foreach( $terms as $term_obj ) {
			$options[$term_obj->term_id] = $term_obj->name;
		}
		
		//Output selectbox
		$this->selectbox( $field_name, $options, $selected, 
			$default_option, $use_prefix, $multiple, $class);
	}
	
	/*
	 * Function name: date
	 * Purpose: Builds and echos html for a date selector.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Initial value of the date field.
	 */
	public function date($field_name, $value='', $class='' ) {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<input type="date" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'" class="'.$class.'" />';
		
		echo $output;
	}

	/*
	 * Function name: time
	 * Purpose: Builds and echos html for a time selector.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Initial value of the date field.
	 */
	public function time($field_name, $value='', $class='' ) {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<input type="time" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'" pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:MM" class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: textbox
	 * Purpose: Builds and echos html for a textbox.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Initial value of the text field.
	 */
	public function textbox($field_name, $value='', $class='' ) {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<input type="text" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'" class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: url
	 * Purpose: Builds and echos html for a url field.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Initial value of the url field.
	 */
	public function url($field_name, $value='', $class='' ) {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<input type="url" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'" class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: email
	 * Purpose: Builds and echos html for an email field.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Initial value of the email field.
	 */
	public function email($field_name, $value='', $class='' ) {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<input type="email" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'" class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: tel
	 * Purpose: Builds and echos html for a tel field.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Initial value of the tel field.
	 */
	public function tel($field_name, $value='', $class='', $attrs = '' ) {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<input type="tel" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'" class="'.$class.'" '.$attrs.' />';
		
		echo $output;
	}

	/*
	 * Function name: hidden
	 * Purpose: Builds and echos html for a hidden field.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Initial value of the hidden field.
	 */
	public function hidden($field_name, $value='', $class='' ) {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<input type="hidden" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'" class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: checkbox
	 * Purpose: Builds and echos html for a checkbox.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Value of the checkbox.
	 *  -$checked: Whether the checkbox is checked by default.
	 */
	public function checkbox($field_name, $value='', $class='', $checked=false ) {
		$field_name_id = $this->get_name_id($field_name);
		
		if( $checked ) $checked = ' checked="checked"';
		else $checked = '';
		
		$output = '<input type="checkbox" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'"'.$checked.' class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: number
	 * Purpose: Builds and echos html for a number field.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission. 
	 * 		Doubles as the field's id.
	 *	-$value: Initial value of the number field. 
	 * 		Must be a $step value inside the $min to $max range.
	 *	-$step: Number to increment by starting with $min.
	 *	-$min: Minimum allowed numeric value, inclusive.
	 *	-$max: Maximum allowed numeric value, inclusive.
	 */
	public function number($field_name, $value=0, $class='', $step=1, $min='', $max='') {
		//Validate and correct arguements
		if(empty($value))
			$value = 0;
		if(empty($step))
			$step = 1;
		
		//Ensure numbers were passed into the arguements
		if(!empty($value))
			$value = (float) $value;
		if(!empty($step))
			$step = (float) $step;
		if(!empty($min))
			$min = (float) $min;
		if(!empty($max))
			$max = (float) $max;
		
		//Correct initial value of field
		if(!empty($min) && $value < $min)
			$value = $min;
		elseif(!empty($max) && $value > $max)
			$value = $max;
		elseif(!empty($min) && ($value - $min) % $step != 0) {
			//Round down to nearest step
			$num_steps = floor(($value - $min) / $step);
			$value = $min + ($step * $num_steps);
		}
		
		$field_name_id = $this->get_name_id($field_name);
		
		$output = '<input type="number" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'step="'.$step.'" min="'.$min.'" max="'.$max.'" value="'.$value.'" 
					class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: file
	 * Purpose: Builds and echos html for a file field.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$accept: Comma separated string or array of valid file extensions or mime types
	 *  -$max-size: Maximum file size in bytes. For JS form validation.
	 *	-$class: Value of class attribute.
	 *	-$ajax_url: URL to be used by AJAX scripts for uploading the file.
	 */
	public function file($field_name, $accept='', $max_size='', $class='', $ajax_url='') {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<input type="file" name="'.$field_name_id.'" id="'.$field_name_id.'"';
		if( !empty($accept) ) {
			if( is_array($accept) ) 
				$accept = implode(',', $accept);
			$output .= ' accept="'.$accept.'"';
		}
		if( !empty($max_size) )
			$output .= ' data-max-size="'.$max_size.'"';
		if( !empty($ajax_url) )
			$output .= ' data-ajax-url="'.$ajax_url.'"';
		$output .= ' class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: textarea
	 * Purpose: Builds and echos html for a textarea.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Initial value of the textarea.
	 */
	public function textarea($field_name, $value='', $class='') {
		$field_name_id = $this->get_name_id($field_name);
	
		$output = '<textarea name="'.$field_name_id.'" id="'.$field_name_id.'" class="'.$class.'">'.
					$value.'</textarea>';
		
		echo $output;
	}
	
	/*
	 * Function name: submit
	 * Purpose: Builds and echos html for a submit button.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission.
	 *	-$value: Value of the submit button.
	 */
	public function submit($field_name, $value='Submit', $class='') {
		$field_name_id = $this->get_name_id($field_name);
		
		$output = '<input type="submit" name="'.$field_name_id.'" id="'.$field_name_id.'" '.
					'value="'.$value.'" class="'.$class.'" />';
		
		echo $output;
	}
	
	/*
	 * Function name: tinymce_box
	 * Purpose: Builds and echos html for a TinyMCE WYSIWYG editor.
	 * Arguements:
	 *	-$field_name: Value of name attribute for form submission. 
	 * 		Doubles as the field's id.
	 *	-$value: String containing html to rendered by TinyMCE.
	 */
	public function tinymce_editor($field_name, $value='', $tinymce_settings=array()) {
		wp_editor($value, $this->get_name_id($field_name), $tinymce_settings);
	}
}