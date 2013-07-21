<?php
/**
 * plugin name: Seamless Donation's Code manipulation
 * author: Mahibul Hasan Sohag
 * author url: http://sohag07hasan.elance.com
 */

define("SDCODEMANIPLATION_FILE", __FILE__);
define("SDCODEMANIPLATION_DIR", dirname(__FILE__));
define("SDCODEMANIPLATION_ULR", plugins_url('/', __FILE__));

include SDCODEMANIPLATION_DIR . '/classes/db.php';

include SDCODEMANIPLATION_DIR . '/classes/code-manipulation.php';
SeamlessDonationCodeManipulation::init();
