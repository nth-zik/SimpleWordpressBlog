<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Date field.
 *
 * @package    EverestForms
 * @author     WPEverest
 * @since      1.0.0
 */
class EVF_Field_Date extends EVF_Form_Fields {

	/**
	 * Primary class constructor.
	 *
	 * @since      1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = __( 'Date', 'everest-forms' );
		$this->type  = 'date';
		$this->icon  = 'evf-icon evf-icon-date-time';
		$this->order = 7;
		$this->group = 'advanced';
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since      1.0.0
	 *
	 * @param array $field
	 */
	public function field_options( $field ) {

		// -------------------------------------------------------------------//
		// Basic field options.
		// -------------------------------------------------------------------//

  		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'basic-options', $field, $args );

		// Label.
		$this->field_option( 'label', $field );

		// Meta.
		$this->field_option( 'meta', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'basic-options', $field, $args );

		// --------------------------------------------------------------------//
		// Advanced field options.
		// --------------------------------------------------------------------//

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'advanced-options', $field, $args );

		// Hide label.
		$this->field_option( 'label_hide', $field );


		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'advanced-options', $field, $args );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since      1.0.0
	 *
	 * @param array $field
	 */
	public function field_preview( $field ) {

		// Define data.
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		echo '<input type="date" placeholder="' . $placeholder . '" class="primary-input" disabled>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since      1.0.0
	 *
	 * @param array $field
	 * @param array $deprecated
	 * @param array $form_data
	 */
	public function field_display( $field, $deprecated, $form_data ) {

 		// Define data.
		$primary = $field['properties']['inputs']['primary'];

		$class = array_merge( array( 'date-picker-field' ), $primary['class'] );

		// Primary field.
		printf( '<input type="text" %s %s>',
			evf_html_attributes( $primary['id'], $class, $primary['data'], $primary['attr'] ),
			$primary['required']
		);
	}
}

new EVF_Field_Date;
