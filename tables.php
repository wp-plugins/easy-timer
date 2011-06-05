<?php $tables['affiliates'] = array(
'id' => array('type' => 'int', 'name' => __('ID', 'affiliation-manager'), 'width' => 5),
'login' => array('type' => 'text', 'name' => __('Login name', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the login name', 'affiliation-manager')),
'password' => array('type' => 'text'),
'first_name' => array('type' => 'text', 'name' => __('First name', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the first name', 'affiliation-manager')),
'last_name' => array('type' => 'text', 'name' => __('Last name', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the last name', 'affiliation-manager')),
'email_address' => array('type' => 'text', 'name' => __('Email address', 'affiliation-manager'), 'width' => 15, 'searchby' => __('the email address', 'affiliation-manager')),
'paypal_email_address' => array('type' => 'text', 'name' => __('PayPal email address', 'affiliation-manager'), 'width' => 15, 'searchby' => __('the PayPal email address', 'affiliation-manager')),
'website_name' => array('type' => 'text', 'name' => __('Website', 'affiliation-manager'), 'width' => 15, 'searchby' => __('the website name', 'affiliation-manager')),
'website_url' => array('type' => 'text', 'name' => __('Website URL', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the website URL', 'affiliation-manager')),
'address' => array('type' => 'text', 'name' => __('Address', 'affiliation-manager'), 'width' => 15, 'searchby' => __('the address', 'affiliation-manager')),
'postcode' => array('type' => 'text', 'name' => __('Postcode', 'affiliation-manager'), 'width' => 9, 'searchby' => __('the postcode', 'affiliation-manager')),
'town' => array('type' => 'text', 'name' => __('Town', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the town', 'affiliation-manager')),
'country' => array('type' => 'text', 'name' => __('Country', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the country', 'affiliation-manager')),
'phone_number' => array('type' => 'text', 'name' => __('Phone number', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the phone number', 'affiliation-manager')),
'ip_address' => array('type' => 'text', 'name' => __('IP address', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the IP address', 'affiliation-manager')),
'user_agent' => array('type' => 'text', 'name' => __('User agent', 'affiliation-manager'), 'width' => 24, 'searchby' => __('the user agent', 'affiliation-manager')),
'referring_url' => array('type' => 'text', 'name' => __('Referring URL', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the referring URL', 'affiliation-manager')),
'date' => array('type' => 'datetime', 'name' => __('Registration date', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the registration date', 'affiliation-manager')),
'date_utc' => array('type' => 'datetime', 'name' => __('Registration date (UTC)', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the registration date (UTC)', 'affiliation-manager')),
'referrer' => array('type' => 'text', 'name' => __('Referrer', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the referrer', 'affiliation-manager')),
'commission_amount' => array('type' => 'text', 'name' => __('Commission amount', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the commission amount', 'affiliation-manager')),
'commission_percentage' => array('type' => 'text', 'name' => __('Commission percentage', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the commission percentage', 'affiliation-manager')));

$tables['clicks'] = array(
'id' => array('type' => 'int', 'name' => __('ID', 'affiliation-manager'), 'width' => 5),
'referrer' => array('type' => 'text', 'name' => __('Referrer', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the referrer', 'affiliation-manager')),
'url' => array('type' => 'text', 'name' => __('URL', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the URL', 'affiliation-manager')),
'ip_address' => array('type' => 'text', 'name' => __('IP address', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the IP address', 'affiliation-manager')),
'user_agent' => array('type' => 'text', 'name' => __('User agent', 'affiliation-manager'), 'width' => 24, 'searchby' => __('the user agent', 'affiliation-manager')),
'referring_url' => array('type' => 'text', 'name' => __('Referring URL', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the referring URL', 'affiliation-manager')),
'date' => array('type' => 'datetime', 'name' => __('Date', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the date', 'affiliation-manager')),
'date_utc' => array('type' => 'datetime', 'name' => __('Date (UTC)', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the date (UTC)', 'affiliation-manager')));

$tables['commissions'] = array(
'id' => array('name' => __('ID', 'affiliation-manager'), 'width' => 5),
'referrer' => array('name' => __('Referrer', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the referrer', 'affiliation-manager')),
'commission_amount' => array('name' => __('Amount', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the amount', 'affiliation-manager')),
'commission_payment' => array('name' => __('Payment', 'affiliation-manager'), 'width' => 12),
'commission_status' => array('name' => __('Status', 'affiliation-manager'), 'width' => 12),
'commission_payment_date' => array('name' => __('Payment date', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the payment date', 'affiliation-manager')),
'commission_payment_date_utc' => array('name' => __('Payment date (UTC)', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the payment date (UTC)', 'affiliation-manager')),
'product_id' => array('name' => __('Product ID', 'affiliation-manager'), 'width' => 9, 'searchby' => __('the product ID', 'affiliation-manager')),
'quantity' => array('name' => __('Quantity', 'affiliation-manager'), 'width' => 9, 'searchby' => __('the quantity', 'affiliation-manager')),
'price' => array('name' => __('Global price', 'affiliation-manager'), 'width' => 9, 'searchby' => __('the global price', 'affiliation-manager')),
'tax' => array('name' => __('Tax', 'affiliation-manager'), 'width' => 9, 'searchby' => __('the tax', 'affiliation-manager')),
'tax_included_in_price' => array('name' => __('Tax included in price', 'affiliation-manager'), 'width' => 12),
'shipping_cost' => array('name' => __('Shipping cost', 'affiliation-manager'), 'width' => 9, 'searchby' => __('the shipping cost', 'affiliation-manager')),
'amount' => array('name' => __('Order amount', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the order amount', 'affiliation-manager')),
'payment_mode' => array('name' => __('Order\'s payment mode', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the order\'s payment mode', 'affiliation-manager')),
'transaction_number' => array('name' => __('Transaction number', 'affiliation-manager'), 'width' => 15, 'searchby' => __('the transaction number', 'affiliation-manager')),
'transaction_cost' => array('name' => __('Transaction cost', 'affiliation-manager'), 'width' => 9, 'searchby' => __('the transaction cost', 'affiliation-manager')),
'instructions' => array('name' => __('Instructions', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the instructions', 'affiliation-manager')),
'shipping_address' => array('name' => __('Shipping address', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the shipping address', 'affiliation-manager')),
'date' => array('name' => __('Date', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the date', 'affiliation-manager')),
'date_utc' => array('name' => __('Date (UTC)', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the date (UTC)', 'affiliation-manager')),
'status' => array('name' => __('Order status', 'affiliation-manager'), 'width' => 12),
'refund_date' => array('name' => __('Order\'s refund date', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the order\'s refund date', 'affiliation-manager')),
'refund_date_utc' => array('name' => __('Order\'s refund date (UTC)', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the order\'s refund date (UTC)', 'affiliation-manager')),
'first_name' => array('name' => __('Customer\'s first name', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the customer\'s first name', 'affiliation-manager')),
'last_name' => array('name' => __('Customer\'s last name', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the customer\'s last name', 'affiliation-manager')),
'email_address' => array('name' => __('Customer\'s email address', 'affiliation-manager'), 'width' => 15, 'searchby' => __('the customer\'s email address', 'affiliation-manager')),
'website_name' => array('name' => __('Customer\'s website', 'affiliation-manager'), 'width' => 15, 'searchby' => __('the customer\'s website name', 'affiliation-manager')),
'website_url' => array('name' => __('Customer\'s website URL', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the customer\'s website URL', 'affiliation-manager')),
'address' => array('name' => __('Customer\'s address', 'affiliation-manager'), 'width' => 15, 'searchby' => __('the customer\'s address', 'affiliation-manager')),
'postcode' => array('name' => __('Customer\'s postcode', 'affiliation-manager'), 'width' => 9, 'searchby' => __('the customer\'s postcode', 'affiliation-manager')),
'town' => array('name' => __('Customer\'s town', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the customer\'s town', 'affiliation-manager')),
'country' => array('name' => __('Customer\'s country', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the customer\'s country', 'affiliation-manager')),
'phone_number' => array('name' => __('Customer\'s phone number', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the customer\'s phone number', 'affiliation-manager')),
'ip_address' => array('name' => __('Customer\'s IP address', 'affiliation-manager'), 'width' => 12, 'searchby' => __('the customer\'s IP address', 'affiliation-manager')),
'user_agent' => array('name' => __('Customer\'s user agent', 'affiliation-manager'), 'width' => 24, 'searchby' => __('the customer\'s user agent', 'affiliation-manager')),
'referring_url' => array('name' => __('Customer\'s referring URL', 'affiliation-manager'), 'width' => 18, 'searchby' => __('the customer\'s referring URL', 'affiliation-manager')));