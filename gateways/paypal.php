<?php include_once '../../../../wp-load.php';
global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';

if ($_GET['action'] == 'purchase') {
if ($_GET['sale_winner'] == 'affiliate') {
if (strstr($_GET['referrer'], '@')) { $_GET['paypal_email_address'] = $_GET['referrer']; }
else { $_GET['paypal_email_address'] = affiliate_data('paypal_email_address'); } }
else { $_GET['paypal_email_address'] = product_data('paypal_email_address'); } ?>
<!DOCTYPE html>
<html>
<head>
<title><?php _e('Redirection to', 'commerce-manager'); ?> PayPal</title>
<meta charset="utf-8" />
</head>
<body style="text-align: center;" onload="document.forms['paypal2'].submit();">
<h2><?php _e('You will be redirected to PayPal.', 'commerce-manager'); ?></h2>
<form method="post" id="paypal" action="https://www.<?php echo ($_GET['sandbox_enabled'] == 'yes' ? 'sandbox.' : ''); ?>paypal.com/cgi-bin/webscr">
<div>
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="no_note" value="0" />
<input type="hidden" name="lc" value="<?php echo strtoupper(substr(WPLANG, 0, 2)); ?>" />
<input type="hidden" name="notify_url" value="<?php echo COMMERCE_MANAGER_URL.'gateways/paypal.php'; ?>" />
<input type="hidden" name="business" value="<?php echo $_GET['paypal_email_address']; ?>" />
<input type="hidden" name="amount" value="<?php echo $_GET['net_price']; ?>" />
<input type="hidden" name="currency_code" value="<?php echo $_GET['currency_code']; ?>" />
<input type="hidden" name="no_shipping" value="<?php echo ($_GET['shipping_address_required'] == 'yes' ? 0 : 1); ?>" />
<input type="hidden" name="item_number" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="item_name" value="<?php echo $_GET['name']; ?>" />
<input type="hidden" name="return" value="<?php echo $_GET['order_confirmation_url']; ?>" />
<input type="hidden" name="quantity" value="<?php echo $_GET['quantity']; ?>" />
<input type="hidden" name="shipping" value="<?php echo $_GET['shipping_cost']; ?>" />
<input type="hidden" name="tax" value="<?php echo $_GET['tax']; ?>" />
<input type="hidden" name="image_url" value="<?php echo $_GET['thumbnail_url']; ?>" />
<input type="hidden" name="cancel_return" value="<?php echo $_GET['url']; ?>" />
<input type="hidden" name="custom" value="referrer=<?php echo $_GET['referrer']; ?>/code=<?php echo $_GET['code']; ?>" />
</div>
<p><?php _e('If you\'re not redirected within 5 seconds,', 'commerce-manager'); ?> <input type="submit" value="<?php _e('click here', 'commerce-manager'); ?>" /></p>
</form>
</body>
</html>
<?php } ?>