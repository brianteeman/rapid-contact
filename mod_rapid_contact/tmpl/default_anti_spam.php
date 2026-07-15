<?php
/*------------------------------------------------------------------------
# mod_rapid_contact - Rapid Contact
# ------------------------------------------------------------------------
# author    Christopher Mavros - Mavxr.com
# copyright Copyright (C) 2008 Mavxr.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://mavxr.com
# Technical Support:  Forum - https://mavxr.com/support/forum
-------------------------------------------------------------------------*/

// no direct access
\defined( '_JEXEC' ) or die( 'Restricted access' );

use \Joomla\CMS\Factory;
use \Joomla\CMS\Captcha\Captcha;

$rpCaptchaPlugin = Factory::getApplication()->get('captcha');

?>
<div class="control-group">
  <?php if ($params->get('enable_anti_spam', '1') == '2') { ?>
    <?php if ($rpCaptchaPlugin != '0') {
      print Captcha::getInstance($rpCaptchaPlugin)->display('rp_recaptcha', 'rp_recaptcha', 'g-recaptcha');
    ?>
      <input type="hidden" name="<?php print $form_id; ?>_hasCaptcha" id="<?php print $form_id; ?>_hasCaptcha" value="true"/>
    <?php } ?>
  <?php } else if ($params->get('enable_anti_spam', '1') == '3') { // Honeypot: hidden from humans (off-screen) but present in the markup for bots. ?>
    <div class="rp_hp_wrap" aria-hidden="true">
      <label for="<?php print $form_id; ?>_hp"><?php print $honeypotLabel; ?></label>
      <input type="text" name="rp_hp" id="<?php print $form_id; ?>_hp" value="" tabindex="-1" autocomplete="off"/>
    </div>
  <?php } else if ($params->get('enable_anti_spam', '1') == '1') { // Label as Placeholder option is intentionally overlooked. ?>
    <label for="<?php print $form_id; ?>_as_answer"><?php print $params->get('anti_spam_q', 'How many eyes has a typical person?'); ?></label>
    <input class="rapid_contact form-control inputbox" type="text" name="rp_anti_spam_answer" id="<?php print $form_id; ?>_as_answer" size="<?php print $params->get('email_width', '15'); ?>" value="<?php print htmlspecialchars($CORRECT_ANTISPAM_ANSWER, ENT_QUOTES, 'UTF-8'); ?>"/>
  <?php } ?>
</div>