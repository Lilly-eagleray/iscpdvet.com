<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

/**
 * Fires to output the email header.
 *
 * @hooked WC_Emails::email_header()
 *
 * @since 3.7.0
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo $email_improvements_enabled ? '<div class="email-introduction">' : ''; ?>

<!-- CUSTOMIZATION: Using the custom, Hebrew header from the old template -->
<h1> ברוך הבא למרכז ללימודי המשך וטרינריים CPD-Vet </h1>

<p><?php esc_html_e( 'תודה על יצירת חשבונך.', 'woocommerce' ); ?></p>
<p><?php printf( esc_html__( 'שם המשתמש שלך הוא: %s', 'woocommerce' ), '<b>' . esc_html( $user_login ) . '</b>' ); ?></p>

<?php
// Logic for New Email Improvements (WooCommerce 10.0.0)
if ( $email_improvements_enabled ) :
?>
	<div class="hr hr-top"></div>

	<?php if ( $password_generated && $set_password_url ) : ?>
		<?php // If the password has not been set by the user during the sign up process, send them a link to set a new password. ?>
		<p><a href="<?php echo esc_attr( $set_password_url ); ?>"><?php esc_html_e( 'לחץ כאן כדי לקבוע את הסיסמה שלך.', 'woocommerce' ); ?></a></p>
	<?php endif; ?>

	<div class="hr hr-bottom"></div>
	<p><?php esc_html_e( 'באפשרותך לגשת לאזור החשבון שלך כדי לצפות בהזמנות, לשנות את הסיסמה ועוד:', 'woocommerce' ); ?></p>
	<p><a href="<?php echo esc_attr( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'לאזור האישי שלי', 'woocommerce' ); ?></a></p>

<?php
// Logic for Classic Email Templates
else :
?>
	<?php if ( $password_generated && $set_password_url ) : ?>
		<?php // If the password has not been set by the user during the sign up process, send them a link to set a new password. ?>
		<p><a href="<?php echo esc_attr( $set_password_url ); ?>"><?php esc_html_e( 'לחץ כאן כדי לקבוע את הסיסמה שלך.', 'woocommerce' ); ?></a></p>
	<?php elseif ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated && ! empty( $user_pass ) ) : ?>
		<p><?php printf( esc_html__( 'הסיסמה שלך נוצרה באופן אוטומטי: %s', 'woocommerce' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>
	<?php endif; ?>

	<p><?php printf( esc_html__( 'באפשרותך לגשת לאזור החשבון שלך כדי לצפות בהזמנות ולשנות את הסיסמה שלך כאן: %s.', 'woocommerce' ), make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

<?php endif; ?>
<?php echo $email_improvements_enabled ? '</div>' : ''; ?>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="email-additional-content email-additional-content-aligned">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

/**
 * Fires to output the email footer.
 *
 * @hooked WC_Emails::email_footer()
 *
 * @since 3.7.0
 */
do_action( 'woocommerce_email_footer', $email );
