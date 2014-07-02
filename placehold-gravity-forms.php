<?php
/**
 * Plugin Name: Placehold Gravity Forms
 * Plugin URI: https://github.com/51seven/placehold-gravity-forms
 * Description: Adds a placeholder to inputs Gravity Forms.
 * Version: 1.2
 * Author: 51seven GmbH, Timo Maemecke
 * Author URI: http://51seven.de
 * License: MIT
 */

/**
 * Adds the placeholder input to the field of Gravity Forms.
 * HTML Code is the same as in every input of a Gravity field,
 * it get's echo'ed on position 25 (right after the name of the field).
 *
 * Attribution goes to http://www.wpbeginner.com/wp-tutorials/how-to-add-placeholder-text-in-gravity-forms/
 *
 * Hook: gform_field_standard_settings
 */
function placehold_gform_field_standard_settings($position, $form_id) {
	if($position == 25) {
		$output = '<li class="admin_label_setting field_setting" style="display: list-item; ">';
		$output .= '<label for="field_placeholder">Placeholder'
			.' <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_field_label" title="<h6>Placeholder</h6>This text will be displayed as the HTML5 Placeholder of the input field."><i class="fa fa-question-circle"></i></a>'
			.' <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_field_label_html" title="<h6>Placeholder</h6>This text will be displayed as the HTML5 Placeholder of the input field." style="display: none;"><i class="fa fa-question-circle"></i></a>'
			.'</label>';
		$output .= '<input type="text" id="field_placeholder" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty(\'placeholder\', this.value);">';
		$output .= '</li>';
		echo $output;
	}
}

/**
 * Sets the saved content of the placeholder to the value of the field with jQuery.
 *
 * Attribution goes to http://www.wpbeginner.com/wp-tutorials/how-to-add-placeholder-text-in-gravity-forms/
 *
 * Hook: gform_editor_js
 */
function placehold_gform_editor_js() {
	$output = '<script>'
		.'jQuery(document).bind("gform_load_field_settings", function(event, field, form) {'
		.'jQuery("#field_placeholder").val(field["placeholder"]);'
		.'});'
		.'</script>';
	echo $output;
}


/**
 * Adds the placeholder attribute with the saved content to the input.
 * This will happen without jQuery, due the fact that all JavaScript related
 * things should be included right before the ending body tag, not in the
 * middle of a document.
 * You don't need to include jQuery in your head. You don't need to add jQuery anywhere.
 *
 * It parses the HTML with a DOMDocument Object and adds the placeholder to every input element
 */
function placehold_gform_field_content($content, $field, $value, $lead_id, $form_id) {
	if(isset($field['placeholder']) && !empty($field['placeholder'])) {
		$dom = new DOMDocument();
		$utf8content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
	    @$dom->loadHTML($utf8content);
	    $path = new DOMXPath($dom);

	    foreach($path->query("//input") as $node) {
	        $node->setAttribute("placeholder", $field['placeholder']);
	    }
	    foreach($path->query("//textarea") as $node) {
	        $node->setAttribute("placeholder", $field['placeholder']);
	    }
	    $content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $dom->saveHtml());
	}

	return $content;
}


/**
 * Calling all Hooks
 */
add_action("gform_field_standard_settings", "placehold_gform_field_standard_settings", 10, 2);
add_action("gform_editor_js", "placehold_gform_editor_js");
add_filter("gform_field_content", "placehold_gform_field_content", 10, 5);