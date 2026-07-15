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
use \Joomla\CMS\Helper\ModuleHelper;
use \Joomla\CMS\HTML\HTMLHelper;

$document = Factory::getApplication()->getDocument();
$document->addStyleDeclaration('
  .rapid_contact .form-control { max-width: 95%; margin-bottom: 8px; }
  .rapid_contact .g-recaptcha { margin-bottom: 10px; max-width: 95%; }
  .rapid_contact .rp-vh { position:absolute; width:1px; height:1px; margin:-1px; padding:0; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
  .rapid_contact .rp_hp_wrap { position:absolute !important; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden; }
');
if ($params->get('addcss', '') != '') {
  $document->addStyleDeclaration($params->get('addcss', ''));
}
$document->addScriptDeclaration('
  function rp_checkCaptcha(form_id) {
    result = true;
    if (document.getElementById(form_id+"_hasCaptcha")) {
      if ((typeof grecaptcha !== "undefined") && (document.querySelectorAll(".g-recaptcha").length == 1)) { // We only know how to deal with Google ReCaptcha, and only one of it in JS
        if (grecaptcha.getResponse().length == 0) {
          alert("'.$params->get('please_complete_captcha_text', 'Please complete the Captcha').'");
          result = false;
        }
      }
    }
    return result;
  }
');

$form_id = 'rp_'.random_int(1,999999);
?>
<div class="rapid_contact <?php print $params->get('moduleclass_sfx', ''); ?>">
  <form <?php print $url; ?> id="<?php print $form_id; ?>" method="post" onSubmit="return rp_checkCaptcha('<?php print $form_id; ?>');">

    <?php if ($params->get('pre_text', '') != '') {
      print '<div class="rapid_contact intro_text">'.$params->get('pre_text', '').'</div>';
    } ?>

    <?php if ($myError != '') { print '<div class="rapid_contact_error" role="alert">'.$myError.'</div>'; } ?>

    <div class="rapid_contact_form" id="rapid_contact_form_<?php print $form_id; ?>">
      <?php require ModuleHelper::getLayoutPath('mod_rapid_contact', 'default_form'); ?>
    </div>
    <input type="hidden" name="rp_instance" value="<?php print (int) $module->id; ?>"/>
    <?php print HTMLHelper::_('form.token'); ?>
  </form>
</div>