<?php
/**
 * The plugin bootstrap file
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 * @link              http://www.dankedev.com
 * @since             1.0.0
 * @package           ampinstant-cf
 * @wordpress-plugin
 * Plugin Name:       AMP Instant Contact Form
 * Plugin URI:        http://www.dankedev.com
 * Description:       Special For AMPInstant Members
 * Version:           1.0.0
 * Author:            Hadie Danker
 * Author URI:        http://www.dankedev.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:      ampinstant-cf
 * Domain Path:       /languages
 */
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class AMPInstantCF {
	static $params;
	var $Name;
	var $Email;
	var $ConfirmEmail;
	var $Message;
	var $EmailToSender = true;
	var $ErrorMessage;
	var $Errors;
	var $PostID;

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		//add_action( 'admin_notices', array( $this, 'pesan' ) );
		add_shortcode( 'ampfc-contact-form', array( $this, 'form' ) );
		add_shortcode( 'ampinstant-contact-form', array( $this, 'form' ) );
		$this->Errors = array();
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['ampfc'] ) ) {
			$ampcf       = $_POST['ampfc'];
			$this->Name  = filter_var( $ampcf['name'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
			$this->Email = sanitize_email($ampcf['email']);
			$this->Message = sanitize_email($ampcf['message']);
			//$this->EmailToSender = isset( $ampcf['email-sender'] );
			if ( isset( $ampcf['confirm_email'] ) ) {
				$this->ConfirmEmail = sanitize_email($ampcf['confirm_email']);
			}
			$this->Message = filter_var( $ampcf['message'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
			if ( isset( $_POST['post-id'] ) ) {
				$this->PostID = $_POST['post-id'];
			}
			//unset( $_POST['ampfc'] );
		}


	}

	public  function form() {
		global $post;
		$permalink = get_the_permalink( $post->ID );
		$permalink = str_replace( 'http:', '', $permalink );


		if ( isset( $_POST['ampfc'] ) && wp_verify_nonce( $_POST['_ampinstant_cf_ajax_contact_nonce'], 'ampinstant_cf_send_message' ) ) {
		    //var_dump($_POST);
			$this->sendMessage();
			echo '<p>'.$this->get_field('success_message').'</p>';
        }else{
			echo '<p>'.$this->get_field('error_message').'</p>';
        }
		?>

        <form action-xhr="/components/amp-form/submit-form-input-text-xhr" class="form-inline" method="post"
              target="_top">
            <input type="hidden" name="post-id" value="<?php echo $post->ID; ?>">
			<?php ent2ncr( self::set_nonce() ) ?>

            <fieldset>
                <div class="form-group">
                    <label for="ampcf_name"><?php _e( 'Name', 'ampinstant-cf' ); ?></label>
                    <input type="text" class="form-input" name="ampfc[name]" id="ampcf_name" value="<?php echo $this->Name;?>"
                           placeholder="<?php _e( 'Name', 'ampinstant-cf' ); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ampcf_email"><?php _e( 'Email', 'ampinstant-cf' ); ?></label>
                    <input type="email" class="form-input" name="ampfc[email]" id="ampcf_email" value="<?php echo $this->Email;?>"
                           placeholder="<?php _e( 'Email Address', 'ampinstant-cf' ); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ampcf_confirm_email"><?php _e( 'Confirm Email', 'ampinstant-cf' ); ?></label>
                    <input type="email" class="form-input" name="ampfc[confirm_email]" id="ampcf_confirm_email" value="<?php echo $this->ConfirmEmail;?>"
                           placeholder="<?php _e( 'Confirm Email Address', 'ampinstant-cf' ); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ampcf_message"><?php _e( 'Message', 'ampinstant-cf' ); ?></label>
                    <textarea class="form-input" name="ampfc[message]" id="ampcf_message"
                              placeholder="<?php _e( 'Your Message', 'ampinstant-cf' ); ?>" rows="10"
                              required><?php echo $this->Message;?></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" id="ampcf_SubmitButton" class="btn btn-default"
                           value="<?php _e( 'Send Message', 'ampinstant-cf' ); ?>"/>

                </div>

            </fieldset>
        </form>
		<?php
	}

	public function sendMessage() {

		// var_dump($_POST);
		if ( isset( $_POST['ampfc'] ) && wp_verify_nonce( $_POST['_ampinstant_cf_ajax_contact_nonce'], 'ampinstant_cf_send_message' ) ) {
			//var_dump($_POST);


		}
		$filters            = new ampinstantcf_Filter();
		$filters->fromEmail = $this->Email;
		$filters->fromName  = $this->Name;
		$receipent = $this->get_field('email_recipient',get_option( 'admin_email' ));
		$subject    = $this->get_field('email_subject');
		//add filters
		$filters->add( 'wp_mail_from' );
		$filters->add( 'wp_mail_from_name' );
		//headers
		$header = "Reply-To: " . $this->Email . "";
		//$header[] = "From:{$this->Name} {$this->Email}";


		//message
		$message    = '';
		$message = __( 'From: ', 'ampinstant-cf' ) . $this->Name . "\n\n";
		$message .= __( 'Email: ', 'ampinstant-cf' ) . $this->Email . "\n\n";
		$message .= __( 'Page URL: ', 'ampinstant-cf' ) . get_permalink( $this->PostID ) . "\n\n";
		$message .= __( 'Message:', 'ampinstant-cf' ) . "\n\n" . $this->Message;
		//$mailer  = $options['mailer'];
		$result = false;

		$result = wp_mail($receipent,$subject,$message,$header,'');
		//remove filters (play nice)

		$filters->remove( 'wp_mail_from' );
		$filters->remove( 'wp_mail_from_name' );

		//send copy to user
		$message_before = $this->get_field('before_message');
		$after_message = $this->get_field('after_message');
		$subject_message = $this->get_field('email_subject_user');

		if ( $this->EmailToSender ) {
			$filters->fromName = get_bloginfo( 'name' );
			//			$filters->add( 'wp_mail_from' );
			//			$filters->add( 'wp_mail_from_name' );
			$heading = "Reply-To: " . $receipent . "";
			//$heading[] = "From:" . get_bloginfo( 'name' ) . " {$receipent}";
			$pesan = '';
			$pesan .= "$message_before \n\n";
		//	$pesan .= __( "Here is a copy of your message :", "ampinstant-cf" ) . "\n\n";
			$pesan .= $this->Message . "\n\n\n\n";
			$pesan .= "$after_message \n\n";
			//$result = false;
			//add filters
			$filters->add( 'wp_mail_from' );
			$filters->add( 'wp_mail_from_name' );


			$result = wp_mail( $this->Email, $subject_message, stripslashes( $pesan ), $heading, '' );
			//remove filters (play nice)
			$filters->remove( 'wp_mail_from' );
			$filters->remove( 'wp_mail_from_name' );
		}

		return $result;
		//unset( $_POST['ampfc'] );
	}



	public static function get_field( $field, $default = '' ) {
		if ( self::$params ) {
			$params = self::$params;
		} else {
			$params       = get_option( 'wpinstant_cf_options', array() );
			self::$params = $params;

		}
		if ( isset( $params[ $field ] ) && $field ) {
			return $params[ $field ];
		} else {
			return $default;
		}
	}

	public function IsValid() {
		$this->Errors = array();
		if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
			return false;
		}
		if ( strlen( $this->Email ) == 0 ) {
			$this->Errors['email'] = __( 'Please give your email address.', 'ampinstant-cf' );
		}
		if ( strlen( $this->ConfirmEmail ) == 0 ) {
			$this->Errors['confirm_email'] = __( 'Please confirm your email address.', 'ampinstant-cf' );
		}
		//name
		if ( strlen( $this->Name ) == 0 ) {
			$this->Errors['name'] = __( 'Please give your name.', 'ampinstant-cf' );
		}
		if ( strlen( $this->Message ) == 0 ) {
			$this->Errors['message'] = __( 'Please enter a message.', 'ampinstant-cf' );
		}
		//email invalid address
		if ( strlen( $this->Email ) > 0 && ! filter_var( $this->Email, FILTER_VALIDATE_EMAIL ) ) {
			$this->Errors['email'] = __( 'Please enter a valid email address.', 'ampinstant-cf' );
		}

		return count( $this->Errors ) == 0;
	}

	protected static function set_nonce() {
		return wp_nonce_field( 'ampinstant_cf_send_message', '_ampinstant_cf_ajax_contact_nonce' );
	}

	public function install() {
		global $wp_version;
		if ( version_compare( $wp_version, "2.9", "<" ) ) {
			deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
			wp_die( "This plugin requires WordPress version 2.9 or higher." );
		}
		$parameters = array();
		$json       = json_encode( $parameters );
		if ( ! get_option( 'wpinstant_cf_options', '' ) ) {
			update_option( 'wpinstant_cf_options', json_decode( $json, true ) );
		}
	}

	public function uninstall() {
		delete_option( 'wpinstant_cf_options' );
	}
}

new AMPInstantCF();
include dirname( __FILE__ ) . '/admin/admin.php';
include dirname( __FILE__ ) . '/admin/ampinstant-cf-filter.php';
