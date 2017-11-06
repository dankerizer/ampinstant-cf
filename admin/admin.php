<?php
/**
 * ampinstant-cf Project
 * @package ampinstant-cf
 * User: dankerizer
 * Date: 06/11/2017 / 10.39
 */

class AMPICF_Admin {

	static $params;

	public function __construct() {
		add_filter(
			'plugin_action_links_ampinstant-cf/ampinstant-cf.php', array(
				$this,
				'link_setting'
			)
		);
		//add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'menu_page' ) );
		add_action( 'admin_init', array( $this, 'simpan_parameters' ) );
	}

	public function menu_page() {
		add_options_page( __( 'AMP Instant CF Settings', 'ampinstant-cf' ), __( 'AMPInstant CF', 'ampinstant-cf' ), 'manage_options', 'ampinstant-cf-settings', array(
			$this,
			'create_admin_page'
		) );
	}

	public function init() {
		/*Register post type*/
		load_plugin_textdomain( 'ampinstant-cf' );
		//$this->load_plugin_textdomain();
	}

	public static function create_admin_page() {
		?>
        <div class="wrap">
            <h2><?php echo __( 'AMP Instant Contact Form Setting', 'ampinstant-cf' ); ?></h2>
        </div>

        <form action="" method="post">
		<?php ent2ncr( self::set_nonce() ) ?>
		<?php




		$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'content';
		$tabs    = array( 'content' => 'Email Content Setting','support'=>'Support' );
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='?page=ampinstant-cf-settings&tab=$tab'>$name</a>";

		}
		echo '</h2>';
		$admin_email = get_option( 'admin_email' );
		$def_subject    = 'Pesan baru dari ' . get_option( 'blogname' );
		$before_message = 'Terimakasih sudah mengirimkan kontak kepada kami, berikut ini adalah isi pesan anda : ';
		$after_message  = 'Kami akan segera membalas email anda dengan segera. <br/> Hormat Kami <br/><strong>Admin ' . get_option( 'blogname' ) . '</strong>';
		//echo self::get_field( 'email_recipient','' );
		?>
        <div style="">
        <table class="form-table">

        <tbody>
		<?php
		switch ( $current ):
            case 'content':
				?>
                <tr>
                    <th>Data Penerima Email</th>
                    <td>
                        <p class="help">Isi dengan email valid agar anda menerima kontak dengan baik</p>
                    </td>
                </tr>
                <tr valign="middle">
                    <th><label for="<?php echo self::set_field( 'email_recipient' ); ?>">Email Penerima</label></th>
                    <td scope="row">
                        <input type="email" class="text"
                               value="<?php echo self::get_field( 'email_recipient', $admin_email ) ?>"
                               name="<?php echo self::set_field( 'email_recipient' ); ?>"
                               id="<?php echo self::set_field( 'email_recipient' ); ?>"
                               size="60"
                               placeholder="email">
                    </td>
                </tr>

                <tr>
                    <th><label for="<?php echo self::set_field( 'email_subject' ); ?>">Subjek Email</label></th>
                    <td>
                        <input type="text" class="text"
                               value="<?php echo self::get_field( 'email_subject', $def_subject ) ?>"
                               name="<?php echo self::set_field( 'email_subject' ); ?>"
                               id="<?php echo self::set_field( 'email_subject' ); ?>"
                               size="60"
                               placeholder="Subjek Email">
                    </td>
                </tr>

                <tr>
                    <th>Data Untuk Pengirim</th>
                    <td><p class="help">Data Untuk Pengirim email (user)</p></td>
                </tr>
                <tr>
                    <th><label for="<?php echo self::set_field( 'email_subject_user' ); ?>">Subjek Email</label></th>
                    <td>
                        <input type="text" class="text"
                               value="<?php echo self::get_field( 'email_subject_user', 'Isi Pesan Anda di '.get_option( 'blogname' ) ) ?>"
                               name="<?php echo self::set_field( 'email_subject_user' ); ?>"
                               id="<?php echo self::set_field( 'email_subject_user' ); ?>"
                               size="60"
                               placeholder="Subjek Email">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="">Sebelum isi Pesan</label></th>
                    <td><textarea name="<?php echo self::set_field( 'before_message' ); ?>"
                                  id="<?php echo self::set_field( 'before_message' ); ?>" cols="62"
                                  rows="3"><?php echo self::get_field( 'before_message', $before_message ) ?></textarea>
                        <p class="description">Akan di tampilkan ke user sebelum isi pesan.</p>
                    </td>

                </tr>
                <tr valign="top">
                    <th scope="row"><label for="">Sebelum Setelah Pesan</label></th>
                    <td><textarea name="<?php echo self::set_field( 'after_message' ); ?>"
                                  id="<?php echo self::set_field( 'after_message' ); ?>" cols="62"
                                  rows="3"><?php echo self::get_field( 'after_message', $after_message ) ?></textarea>
                        <p class="description">Akan di tampilkan ke user Setelah isi pesan.</p>
                    </td>

                </tr>
                <tr>
                    <th>Frontend</th>
                    <td><p class="description">Tampilan Form</p></td>
                <tr>
                    <th><label for="<?php echo self::set_field( 'submit_text' ) ?>">Tombol Submit</label></th>
                    <td>
                        <input type="text"
                               name="<?php echo self::set_field( 'submit_text' ); ?>"
                               value="<?php echo self::get_field( 'submit_text','Kirim' ) ?>"
                               id="<?php echo self::set_field( 'submit_text' ) ?>"
                               size="60"
                               placeholder="Tombol Submit" >
                    </td>
                </tr>
                <tr>
                    <th><label for="<?php echo self::set_field( 'success_message' ); ?>">Pesan Saat Berhasil</label></th>

                    <td><textarea name="<?php echo self::set_field( 'success_message' ); ?>"
                                  id="<?php echo self::set_field( 'success_message' ); ?>" cols="62"
                                  placeholder="Sebelum Form"
                                  rows="3"><?php echo self::get_field( 'success_message', '' ) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="<?php echo self::set_field( 'error_message' ); ?>">Pesan saat gagal/Error</label></th>

                    <td><textarea name="<?php echo self::set_field( 'error_message' ); ?>"
                                  id="<?php echo self::set_field( 'error_message' ); ?>" cols="62"
                                  placeholder="Setelah Form"
                                  rows="3"><?php echo self::get_field( 'error_message', '' ) ?></textarea>
                    </td>
                </tr>
                </tr>
                <?php
                break;
//            case 'mailer':
//                ?>
<!--                <tr>-->
<!--                    <th><label for="--><?php //echo self::set_field( 'mailer' ); ?><!--">Gunakan Mailer dari : </label></th>-->
<!--                    <td>-->
<!--                        <select name="--><?php //echo self::set_field( 'mailer' ); ?><!--" id="--><?php //echo self::set_field( 'mailer' ); ?><!--">-->
<!--                            --><?php
//                                $mailers = array('wp_mail' => 'WordPress Mailer','php_mailer'=>'PHP Mail','mailgun'=>'Mailgun','sendgrid_v3'=>'Sendgrid V3','smtp'=>'Custom SMTP');
//                                    foreach ($mailers as $key => $mailer){
//                                        $selected = selected(self::get_field( 'mailer' ),$key);
//                                        echo '<option value="'.$key.'" '.$selected.'>'.$mailer.'</option>';
//                                    }
//                            ?>
<!--                        </select>-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <th><label for="mailgun">Mailgun Setting</label></th>-->
<!--                    <td>-->
<!--                        <input type="text"-->
<!--                               name="--><?php //echo self::set_field( 'mailgun_domain_name' ); ?><!--"-->
<!--                               value="--><?php //echo self::get_field( 'mailgun_domain_name' ) ?><!--"-->
<!--                               placeholder="mailgun domain name" id="mailgun">-->
<!--                        <input type="text" placeholder="mailgun API Key"-->
<!--                               name="--><?php //echo self::set_field( 'mailgun_apikey' ); ?><!--"-->
<!--                               value="--><?php //echo self::get_field( 'mailgun_apikey' ) ?><!--"-->
<!--                        >-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <th><label for="--><?php //echo self::set_field( 'sengrid_apikey' ); ?><!--">Sendgrid API Key</label></th>-->
<!--                    <td>-->
<!--                        <input type="text"-->
<!--                               name="--><?php //echo self::set_field( 'sengrid_apikey' ); ?><!--"-->
<!--                               value="--><?php //echo self::get_field( 'sengrid_apikey' ) ?><!--"-->
<!--                               id="--><?php //echo self::set_field( 'sengrid_apikey' ); ?><!--"-->
<!--                               size="60"-->
<!--                               placeholder="Sendgrid API Key">-->
<!--                    </td>-->
<!--                </tr>-->
<!---->
<!--                <tr>-->
<!--                    <th><label for="smtp">Custom SMTP</label></th>-->
<!--                    <td>-->
<!--                        <input type="text"-->
<!--                               name="--><?php //echo self::set_field( 'smtp_host' ); ?><!--"-->
<!--                               value="--><?php //echo self::get_field( 'smtp_host' ) ?><!--"-->
<!--                               placeholder="SMTP Host" id="smtp">-->
<!--                        Port-->
<!--                        <input type="text"-->
<!--                               name="--><?php //echo self::set_field( 'smtp_port' ); ?><!--"-->
<!--                               value="--><?php //echo self::get_field( 'smtp_port' ) ?><!--"-->
<!--                               size="3"-->
<!--                               placeholder="Port" id="port">-->
<!--                        <br/>-->
<!---->
<!--                        <input type="text"-->
<!--                               name="--><?php //echo self::set_field( 'smtp_user_name' ); ?><!--"-->
<!--                               value="--><?php //echo self::get_field( 'smtp_user_name' ) ?><!--"-->
<!--                               placeholder="SMTP Username" >-->
<!---->
<!--                        <input type="password"-->
<!--                               name="--><?php //echo self::set_field( 'smtp_password' ); ?><!--"-->
<!--                               value="--><?php //echo self::get_field( 'smtp_password' ) ?><!--"-->
<!--                               placeholder="SMTP Password" >-->
<!--                    </td>-->
<!--                </tr>-->
<!--	            --><?php
//	            break;
			case 'support':
				?>
                <tr>
                    <th>Cara Install</th>
                    <td>Tambahkan shortcode <code>[ampinstant-contact-form]</code> di page yang akan anda gunakan untuk contact form</td>
                </tr>

                <tr>
                    <th>Masalah Masalah</th>
                    <td>Berikut ini adalah solusinya</td>
                </tr>
                <tr>
                    <th>Email tidak terkirim</th>
                    <td>
                        <ul>
                            <li>Coba Cek spam folder</li>
                            <li>Gunakan Plugin <a href="https://wordpress.org/plugins/wp-mail-smtp/" target="_blank">WP Mail SMTP by WPForms</a> </li>


                        </ul>

                    </td>
                </tr>
	            <?php
	            break;
			    endswitch;
				?>
                </tbody>
                </table>
                </div>
				<?php submit_button( 'Save Your Setting!', 'primary save-notif-custom' ); ?>
                </form>

				<?php
			}

	protected static function set_field( $field, $multi = false ) {
		if ( $field ) {
			if ( $multi ) {
				return 'wpinstant_cf_options[' . $field . '][]';
			} else {
				return 'wpinstant_cf_options[' . $field . ']';
			}
		} else {
			return '';
		}
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

	protected static function set_nonce() {
		return wp_nonce_field( 'ampinstant_cf_save_paramter_settings', '_ampinstant_cf_ajax_nonce' );
	}
	static function link_setting() {

	}

	function simpan_parameters() {
	   // var_dump($_POST);
		if ( isset( $_POST['wpinstant_cf_options'] ) && wp_verify_nonce( $_POST['_ampinstant_cf_ajax_nonce'], 'ampinstant_cf_save_paramter_settings' ) ) {
			update_option( 'wpinstant_cf_options', $_POST['wpinstant_cf_options'] );
			$class = 'notice-success is-dismissible';
			$message = __( 'Settingan Anda Sudah di simpan', 'ampinstant-cf' );
			//echo printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );

			echo '<div id="message" class="notice notice-success is-dismissible">';
			echo "<p>$message</p>";
			echo '</div>';
		}

	}
}

new AMPICF_Admin();