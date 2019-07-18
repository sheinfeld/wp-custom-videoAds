# wp-custom-videoAds
WP plugin that allows users to enter custom video advertising by embed url or their custom html5 player

To enable in the theme, add this BEFORE your wp_footer(); tag.

<?php
 if(is_single())
   ceu_get_html(get_the_ID());
else
  ceu_get_html(null);
?>
