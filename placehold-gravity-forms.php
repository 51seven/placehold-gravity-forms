<?php
/**
 * Plugin Name: Placehold Gravity Forms
 * Plugin URI: https://github.com/51seven/placehold-gravity-forms
 * Description: Adds a placeholder to inputs Gravity Forms.
 * Version: 1.0
 * Author: 51seven GmbH, Timo Maemecke
 * Author URI: http://51seven.de
 * License: MIT
 */

// With a bit code from http://www.wpbeginner.com/wp-tutorials/how-to-add-placeholder-text-in-gravity-forms/
add_action("gform_field_standard_settings", "placehold_gform_field_standard_settings", 10, 2);


function placehold_gform_field_standard_settings($position, $form_id) {

	if($position == 25) {
		echo '<li class="admin_label_setting field_setting" style="display: list-item; ">';
		echo '<label for="field_placeholder">Placeholder'
			.' <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_field_label" title="<h6>Placeholder</h6>This text will be displayed as the HTML5 Placeholder of the input field."><i class="fa fa-question-circle"></i></a>'
			.' <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_field_label_html" title="<h6>Placeholder</h6>This text will be displayed as the HTML5 Placeholder of the input field." style="display: none;"><i class="fa fa-question-circle"></i></a>'
			.'</label>';
		echo '<input type="text" id="field_placeholder" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty(\'placeholder\', this.value);">';
		echo '</li>';
	}
}

/* Now we execute some javascript technicalitites for the field to load correctly */

add_action("gform_editor_js", "placehold_gform_editor_js");

function placehold_gform_editor_js() {

	echo '<script>'
		.'jQuery(document).bind("gform_load_field_settings", function(event, field, form) {'
		.'jQuery("#field_placeholder").val(field["placeholder"]);'
		.'});'
		.'</script>';

}

add_filter("gform_field_content", "placehold_gform_field_content", 10, 5);

function placehold_gform_field_content($content, $field, $value, $lead_id, $form_id) {
	if(isset($field['placeholder'])) {
		$dom = new DOMDocument();
	    @$dom->loadHTML($content);
	    $x = new DOMXPath($dom);

	    foreach($x->query("//input") as $node)
	    {   
	        $node->setAttribute("placeholder", $field['placeholder']);
	    }
	    $content = $dom->saveHtml();
	}

	return $content;
}