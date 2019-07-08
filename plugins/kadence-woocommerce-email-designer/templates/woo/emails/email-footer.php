<?php
/**
 * Email Footer
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-footer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.3.0
 */

/** 
 * EDIT NOTES FOR KADENCE WOOMAIL DESIGNER
 *
 * Add option tp Move footer out of template container so background can be fullwidth
 * Change width="600" to width 100% for tables
 * Add subtitle option.
 * Add Header image container
 * Add Order Style Class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$footer_placement = Kadence_Woomail_Customizer::opt( 'footer_background_placement' );
if ( empty( $footer_placement ) ) {
	$footer_placement = 'inside';
}

?>
															</div>
														</td>
													</tr>
												</table>
												<!-- End Content -->
											</td>
										</tr>
									</table>
									<!-- End Body -->
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<?php if( 'inside' == $footer_placement ) { 
										do_action('kadence_woomail_designer_email_footer'); 
									} ?>
								</td>
							</tr>
						</table> <!-- End template container -->
						<?php if( 'outside' == $footer_placement ) { 
							do_action('kadence_woomail_designer_email_footer'); 
						} ?>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>
