<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<?php affiliation_manager_pages_menu(); ?>
<?php $s = '12345678'; ?>
<p><?php echo md5($s); ?></p>
<p><?php echo wp_hash($s); ?></p>
<p><?php echo wp_hash_password($s); ?></p>
<p><?php echo wp_hash_password($s.'admin'); ?></p>
<p><?php echo hash('sha256', $s); ?></p>
<p>9896afc48c99b54ad3dc684325fd9ea6</p>
<p>7996cb6c1f0ab9b89140d7b7c4755974</p>
</div>
</div>