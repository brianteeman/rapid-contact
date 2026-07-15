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

use \Joomla\CMS\Helper\ModuleHelper;

$label_pos = $params->get('label_pos', '2');

// print anti-spam
if ($params->get('anti_spam_position', 0) == 0) {
  require ModuleHelper::getLayoutPath('mod_rapid_contact', 'default_anti_spam');
}

// print email input
print '<div class="control-group">';
$email_placeholder = ($label_pos == '2') ? ' placeholder="'.$params->get('email_label', 'email@site.com').'"' : '';
$email_label_class = ($label_pos == '2') ? ' class="rp-vh"' : '';
print '<label'.$email_label_class.' for="'.$form_id.'_email">'.$params->get('email_label', 'email@site.com').'</label>';
print '<input class="rapid_contact form-control inputbox ' . $email_class . '" type="email" name="rp_email" id="'.$form_id.'_email" size="'.$params->get('email_width', '15').'" value="'.htmlspecialchars($CORRECT_EMAIL, ENT_QUOTES, 'UTF-8').'" autocomplete="email" required '.$email_placeholder.'/>';
print '</div>';
// print subject input
print '<div class="control-group">';
$subject_placeholder = ($label_pos == '2') ? ' placeholder="'.$params->get('subject_label', 'Subject').'"' : '';
$subject_label_class = ($label_pos == '2') ? ' class="rp-vh"' : '';
print '<label'.$subject_label_class.' for="'.$form_id.'_subject">'.$params->get('subject_label', 'Subject').'</label>';
print '<input class="rapid_contact form-control inputbox" type="text" name="rp_subject" id="'.$form_id.'_subject" size="'.$params->get('subject_width', '15').'" value="'.htmlspecialchars($CORRECT_SUBJECT, ENT_QUOTES, 'UTF-8').'" '.$subject_placeholder.'/>';
print '</div>';
// print message input
print '<div class="control-group">';
$message_placeholder = ($label_pos == '2') ? ' placeholder="'.$params->get('message_label', 'Your Message').'"' : '';
$message_label_class = ($label_pos == '2') ? ' class="rp-vh"' : '';
print '<label'.$message_label_class.' for="'.$form_id.'_message">'.$params->get('message_label', 'Your Message').'</label>';
print '<textarea class="rapid_contact form-control textarea" name="rp_message" id="'.$form_id.'_message" cols="' . $params->get('message_width', '13') . '" rows="4" '.$message_placeholder.'>'.htmlspecialchars($CORRECT_MESSAGE, ENT_QUOTES, 'UTF-8').'</textarea>';
print '</div>';

//print anti-spam
if ($params->get('anti_spam_position', 0) == 1) {
  require ModuleHelper::getLayoutPath('mod_rapid_contact', 'default_anti_spam');
}

require ModuleHelper::getLayoutPath('mod_rapid_contact', 'default_form_button');