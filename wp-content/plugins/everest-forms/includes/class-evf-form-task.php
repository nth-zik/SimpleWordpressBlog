<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Process form data
 *
 * @package    EverestForms
 * @author     WPEverest
 * @since      1.0.0
 */
class EVF_Form_Task {

	/**
	 * Holds errors.
	 *
	 * @since      1.0.0
	 * @var array
	 */
	public $errors;

	/**
	 * Holds formatted fields.
	 *
	 * @since      1.0.0
	 * @var array
	 */
	public $form_fields;

	/**
	 * Holds the ID of a successful entry.
	 *
	 * @since      1.0.0
	 * @var int
	 */
	public $entry_id = 0;

	/**
	 * Primary class constructor.
	 *
	 * @since      1.0.0
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'listen_task' ) );
	}

	/**
	 * Listen to see if this is a return callback or a posted form entry.
	 *
	 * @since      1.0.0
	 */
	public function listen_task() {
		if ( ! empty( $_GET['everest_forms_return'] ) ) {
			$this->entry_confirmation_redirect( '', $_GET['everest_forms_return'] );
		}

		if ( ! empty( $_POST['everest_forms']['id'] ) ) {
			$this->do_task( stripslashes_deep( $_POST['everest_forms'] ) );
		}
	}

	/**
	 * Do task of form entry
	 *
	 * @since 1.0.0
	 * @param array $entry $_POST object.
	 */
	public function do_task( $entry ) {
		try {
			if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'everest-forms_process_submit' ) ) {
				evf_add_notice( __( 'We were unable to process your form, please try again.', 'everest-forms' ) );
				return;
			}

			$this->errors      = array();
			$this->form_fields = array();
			$form_id           = absint( $entry['id'] );
			$form              = EVF()->form->get( $form_id );

			// Validate form is real and active (published).
			if ( ! $form || 'publish' !== $form->post_status ) {
				evf_add_notice( __('Invalid form. Please check again.', 'everest-forms') ,'error');
				return;
			}

			// Formatted form data for hooks
			$form_data = apply_filters( 'everest_forms_process_before_form_data', evf_decode( $form->post_content ), $entry );

			// Pre-process/validate hooks and filter. Data is not validated or cleaned yet so use with caution.
			$entry = apply_filters( 'everest_forms_process_before_filter', $entry, $form_data );

			do_action( 'everest_forms_process_before', $entry, $form_data );
			do_action( "everest_forms_process_before_{$form_id}", $entry, $form_data );

			// Validate fields.
			foreach ( $form_data['form_fields'] as $field ) {
				$field_id     = $field['id'];
				$field_type   = $field['type'];
				$field_submit = isset( $entry['form_fields'][ $field_id ] ) ? $entry['form_fields'][ $field_id ] : '';
				do_action( "everest_forms_process_validate_{$field_type}", $field_id, $field_type, $field_submit, $form_data );
			}

			// Recaptcha Validation
			if( isset( $form_data['settings']['recaptcha_support'] ) && 1 == $form_data['settings']['recaptcha_support'] && empty( $_POST['g-recaptcha-response'] ) ){
				evf_add_notice( get_option('evf_recaptcha_validation', __('Invalid recaptcha code.', 'everest-forms') ),'error');
				update_option( 'evf_validation_error', 'yes');
			}

			if( get_option( 'evf_validation_error' ) === 'yes' ){
				delete_option( 'evf_validation_error' );
				return;
			}

			$errors = apply_filters( 'everest_forms_process_initial_errors', $this->errors, $form_data );

			if ( ! empty( $errors[ $form_id ] ) ) {
				if ( empty( $this->errors[ $form_id ]['header'] ) ) {
					$this->errors[ $form_id ]['header'] = __( 'Form has not been submitted, please see the errors below.', 'everest-forms' );
				}
			}

			$is_spam = false;

			// Only trigger the processing (saving/sending entries, etc) if the entry.
			// is not spam.
			if ( ! $is_spam ) {

				// Pass the form created date into the form data.
				$form_data['created'] = $form->post_date;

				// Format fields
				foreach ( (array) $form_data['form_fields'] as $field ) {

					$field_id     = $field['id'];
					$field_type   = $field['type'];
					$field_submit = isset( $entry['fields'][ $field_id ] ) ? $entry['fields'][ $field_id ] : '';

					do_action( "everest_forms_process_format_{$field_type}", $field_id, $field_submit, $form_data );
				}

				// This hook is for internal purposes and should not be leveraged.
				do_action( 'everest_forms_process_format_after', $form_data );

				// Process hooks/filter - this is where most addons should hook
				// because at this point we have completed all field validation and
				// formatted the data.
				$this->form_fields = apply_filters( 'everest_forms_process_filter', $this->form_fields, $entry, $form_data );

				do_action( 'everest_forms_process', $this->form_fields, $entry, $form_data );
				do_action( "everest_forms_process_{$form_id}", $this->form_fields, $entry, $form_data );

				$this->form_fields = apply_filters( 'everest_forms_process_after_filter', $this->form_fields, $entry, $form_data );

				// One last error check - don't proceed if there are any errors.
				if ( ! empty( $this->errors[ $form_id ] ) ) {
					if ( empty( $this->errors[ $form_id ]['header'] ) ) {
						$this->errors[ $form_id ]['header'] = __( 'Form has not been submitted, please see the errors below.', 'everest-forms' );
					}

					return;
				}

				// Success - add entry to database.
				$entry_id = $this->entry_save( $this->form_fields, $entry, $form_data['id'], $form_data );

				// Success - send email notification.
				$this->entry_email( $this->form_fields, $entry, $form_data, $entry_id, 'entry' );

				// Pass completed and formatted fields in POST.
				$_POST['everest-forms']['complete'] = $this->form_fields;

				// Pass entry ID in POST.
				$_POST['everest-forms']['entry_id'] = $entry_id;

				// Post-process hooks.
				do_action( 'everest_forms_process_complete', $this->form_fields, $entry, $form_data, $entry_id );
				do_action( "everest_forms_process_complete_{$form_id}", $this->form_fields, $entry, $form_data, $entry_id );
			}
		} catch ( Exception $e ) {
			evf_add_notice( $e->getMessage(), 'error' );
		}

		evf_add_notice( isset( $form_data['settings']['successful_form_submission_message'] ) ? $form_data['settings']['successful_form_submission_message'] : __( 'Thanks for contacting us! We will be in touch with you shortly.', 'everest-forms' ), 'success' );

		$this->entry_confirmation_redirect( $form_data );
	}

	/**
	 * Validate the form return hash.
	 *
	 * @since      1.0.0
	 *
	 * @param string $hash
	 *
	 * @return mixed false for invalid or form id
	 */
	public function validate_return_hash( $hash = '' ) {

		$query_args = base64_decode( $hash );
		parse_str( $query_args );

		// Verify hash matches.
		if ( wp_hash( $form_id . ',' . $entry_id ) !== $hash ) {
			return false;
		}

		// Get lead and verify it is attached to the form we received with it.
		$entry = EVF()->entry->get( $entry_id );

		if ( $form_id != $entry->form_id ) {
			return false;
		}

		return $form_id;
	}

	/**
	 * Redirects user to a page or URL specified in the form confirmation settings.
	 *
	 * @since      1.0.0
	 *
	 * @param array|string $form_data
	 * @param string       $hash
	 */
	public function entry_confirmation_redirect( $form_data = '', $hash = '' ) {

		$_POST = array(); //clear fields after successful form submission

		if ( ! empty( $hash ) ) {

			$form_id = $this->validate_return_hash( $hash );

			if ( ! $form_id ) {
				return;
			}

			// Get form
			$form_data = EVF()->form->get( $form_id, array(
				'content_only' => true,
			) );
		}
		$settings = $form_data['settings'];
		if( isset( $settings['redirect_to'] ) && '1' == $settings['redirect_to'] ) {
			?>
				<script>
					var redirect = '<?php echo get_permalink( $settings['custom_page'] ); ?>';
				window.setTimeout(function () {
			        window.location.href = redirect;
			    }, 3000)
				</script>
			<?php
		}
		else if ( isset( $settings['redirect_to'] ) && '2' == $settings['redirect_to']){
			?><script>
				window.setTimeout(function () {
			        window.location.href = '<?php echo $settings['external_url'];?>';
			    }, 3000)
				</script>
			<?php
		}

		// Redirect if needed, to either a page or URL, after form processing.
		if ( ! empty( $form_data['settings']['confirmation_type'] ) && 'message' !== $form_data['settings']['confirmation_type'] ) {

			if ( 'redirect' === $form_data['settings']['confirmation_type'] ) {
				$url = apply_filters( 'everest_forms_process_smart_tags', $form_data['settings']['confirmation_redirect'], $form_data, $this->form_fields, $this->entry_id );
			}

			if ( 'page' === $form_data['settings']['confirmation_type'] ) {
				$url = get_permalink( (int) $form_data['settings']['confirmation_page'] );
			}
		}

		if ( ! empty( $form_data['id'] ) ) {
			$form_id = $form_data['id'];
		} else {
			return;
		}
		if ( ! empty( $url ) ) {
			$url = apply_filters( 'everest_forms_process_redirect_url', $url, $form_id, $this->form_fields );
			wp_redirect( esc_url_raw( $url ) );
			do_action( 'everest_forms_process_redirect', $form_id );
			do_action( "everest_forms_process_redirect_{$form_id}", $form_id );
			exit;
		}
	}

	/**
	 * Sends entry email notifications.
	 *
	 * @since      1.0.0
	 *
	 * @param array  $fields
	 * @param array  $entry
	 * @param array  $form_data
	 * @param array  $entry_id
	 * @param string $context
	 */
	public function entry_email( $fields, $entry, $form_data, $entry_id, $context = '' ) {

		// Provide the opportunity to override via a filter
		if ( ! apply_filters( 'everest_forms_entry_email', true, $fields, $entry, $form_data ) ) {
			return;
		}

		$fields = apply_filters( 'everest_forms_entry_email_data', $fields, $entry, $form_data );

		$email_notifications = isset( $form_data['settings']['email'] ) ? $form_data['settings']['email'] : array();

			if ( empty( $email_notifications['evf_to_email'] ) ) {
				return;
			}

			$form_fields = isset( $form_data['form_fields'] ) ? $form_data['form_fields'] : array();

			$data_html = '';

			foreach ( $form_fields as $field_key => $field ) {

				$field_type = isset( $field['type'] ) ? $field['type'] : '';

				$label = isset( $field['label'] ) ? $field['label'] : '';

				$field_value = isset( $entry['form_fields'][ $field_key ] ) ? $entry['form_fields'][ $field_key ] : '';

				if( $field_type == 'email' ){
					$user_email = $field_value;
				}
				if ( ! empty( $field_type ) ) {

					$process_field_email = apply_filters( 'everest_forms_entry_email_' . $field_type, true );

					if ( $process_field_email ) {

						if ( is_array( $field_value ) ) {
							$field_value = implode( ',', $field_value );
						}
						$data_html .= $label . ' : ' . $field_value . PHP_EOL;
					}
				}
			}

			$email = array();

			$email['evf_email_subject']        = ! empty( $email_notifications['evf_email_subject'] ) ? $email_notifications['evf_email_subject'] : sprintf( _x( 'New %s Entry', 'Form name', 'everest-forms' ), $form_data['settings']['form_title'] );
			$email['evf_from_email'] = ! empty( $email_notifications['evf_from_email'] ) ? $email_notifications['evf_from_email'] : evf_sender_address();
			$email['evf_from_name']    = ! empty( $email_notifications['evf_from_name'] ) ? $email_notifications['evf_from_name'] : evf_sender_name();
			$email['evf_to_email']        = ! empty( $email_notifications['evf_to_email'] ) ? $email_notifications['evf_to_email'] : false;
			$email['evf_email_header']        = ! empty( $email_notifications['evf_email_header'] ) ? $email_notifications['evf_email_header'] : '';

			if ( ! empty( $email_notifications['evf_email_message'] ) ) {
				if ( trim( '{all_fields}' ) == $email_notifications['evf_email_message'] ){
					$email['evf_email_message'] = $data_html;
				}
				else {
					$email['evf_email_message'] = $email_notifications['evf_from_email'];
				}
			}

			// Create new email.
			$emails = new EVF_Emails;
			$emails->__set( 'form_data', $form_data );
			$emails->__set( 'fields', $fields );
			$emails->__set( 'entry_id', $this->entry_id );
			$emails->__set( 'from_name', $email['evf_from_name'] );
			$emails->__set( 'from_address', $email['evf_from_email'] );
			$emails->__set( 'headers', $email['evf_email_header'] );
			$emails->__set( 'reply_to', isset( $user_email ) ? $user_email : $email['evf_from_email'] );

			// Go.
			$status = $emails->send( trim( $email['evf_to_email'] ), $email['evf_email_subject'], $email['evf_email_message'] );

			$_POST['evf_success'] = isset( $status ) ? true : false;

			return $status;

	}


	/**
	 * Saves entry to database.
	 *
	 * @since      1.0.0
	 *
	 * @param array        $fields
	 * @param array        $entry
	 * @param int          $form_id
	 * @param array|string $form_data
	 *
	 * @return int
	 */
	public function entry_save( $fields, $entry, $form_id, $form_data = '' ) {
		global $wpdb;

		if ( isset( $form_data['settings']['disabled_entries'] ) && '1' === $form_data['settings']['disabled_entries'] ) {
			return;
		}

		$browser = evf_get_browser();

		do_action( 'everest_forms_process_entry_save', $fields, $entry, $form_id, $form_data );

		$entry_data = array(
			'form_id'         => $form_id,
			'user_id'         => get_current_user_id(),
			'user_device'     => $browser['name'] . '/' . $browser['platform'],
			'user_ip_address' => evf_get_ip_address(),
			'status'          => 'publish',
			'referer'         => $_SERVER['HTTP_REFERER'],
			'date_created'    => current_time( 'mysql' )
		);

		if ( ! $entry_data['form_id'] ) {
			return new WP_Error( 'no-form-id', __( 'No form ID was found.', 'everest-forms' ) );
		}

		$success = $wpdb->insert( $wpdb->prefix . 'evf_entries', $entry_data );

		if ( is_wp_error( $success ) || ! $success ) {
			return new WP_Error( 'could-not-create', __( 'Could not create an entry', 'everest-forms' ) );
		}

		$entry_id = $wpdb->insert_id;

		$form_fields = isset( $form_data['form_fields'] ) ? $form_data['form_fields'] : array();

		foreach ( $form_fields as $field_key => $field ) {

			$meta_key  = isset( $field['meta-key'] ) ? $field['meta-key'] : '';

			$field_value = isset( $entry['form_fields'][ $field_key ] ) ? $entry['form_fields'][ $field_key ] : '';

			if ( is_array( $field_value ) ) {
				$field_value = serialize( $field_value );
			}

			$entry_metadata = array(
				'entry_id'   => $entry_id,
				'meta_key'   => $meta_key,
				'meta_value' => $field_value,
			);
			$wpdb->insert( $wpdb->prefix . 'evf_entrymeta', $entry_metadata );
		}

		do_action( 'everest_forms_complete_entry_save', $fields, $entry, $form_id, $form_data);
	}
}
