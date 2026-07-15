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
use \Joomla\CMS\Captcha\Captcha;
use \Joomla\CMS\Mail\MailHelper;
use \Joomla\CMS\Session\Session;
use \Joomla\CMS\Uri\Uri;

$app = Factory::getApplication();
$input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
$session = method_exists($app, 'getSession') ? $app->getSession() : Factory::getSession();

$recipient = $params->get('email_recipient', 'email@email.com');
$wrongantispamanswer = $params->get('wrong_antispam', 'Wrong anti-spam answer');
$invalidTokenError = $params->get('invalid_token_error', 'Your session has expired. Please try submitting the form again.');
$honeypotLabel = $params->get('honeypot_label', 'Leave this field empty');
$error_text_color = $params->get('error_text_color', '#FF0000');
$url = $params->get('fixed_url', false) ? 'action="' . $params->get('fixed_url_address', '') . '"' : '';

// Each module instance only processes its own submissions.
$instanceId = (int) $module->id;

$myError = '';
$CORRECT_ANTISPAM_ANSWER = '';
$CORRECT_EMAIL = '';
$CORRECT_SUBJECT = '';
$CORRECT_MESSAGE = '';
$email_class = '';

if ($input->post->exists('rp_email') && $input->post->getInt('rp_instance', 0) === $instanceId) {
  $CORRECT_SUBJECT = $input->post->get('rp_subject', '', 'string');
  $CORRECT_MESSAGE = $input->post->get('rp_message', '', 'string');
  // Cross-Site Request Forgery protection.
  if (!$input->post->getInt(Session::getFormToken(), 0)) {
    $myError = '<span style="color: ' . $error_text_color . ';">' . $invalidTokenError . '</span>';
  }
  // check anti-spam
  if ($params->get('enable_anti_spam', '1') == '1') {
    if (strtolower($input->post->get('rp_anti_spam_answer', '', 'string')) != strtolower((string) $params->get('anti_spam_a', '2'))) {
      $myError = '<span style="color: ' . $error_text_color . ';">' . $wrongantispamanswer . '</span>';
    }
    else {
      $CORRECT_ANTISPAM_ANSWER = $input->post->get('rp_anti_spam_answer', '', 'string');
    }
  }
  else if ($params->get('enable_anti_spam', '1') == '2') {
    if ($app->get('captcha') != '0') {
      $captcha = Captcha::getInstance($app->get('captcha'));
      try {
        if (!$captcha->checkAnswer($input->get('rp_recaptcha', null, 'string'))) {
          $myError = '<span style="color: ' . $error_text_color . ';">' . $wrongantispamanswer . '</span>';
        }
      }
      catch(RuntimeException $e) {
        $myError = '<span style="color: ' . $error_text_color . ';">' . $wrongantispamanswer . '</span>';
      }
    }
  }
  else if ($params->get('enable_anti_spam', '1') == '3') {
    // Honeypot: real visitors never see or fill this field.
    // A filled honeypot is silently discarded, so bots cannot tell they were caught.
    if ($input->post->get('rp_hp', '', 'string') !== '') {
      $session->set('rp_thanks', $instanceId);
      $app->redirect(Uri::getInstance()->toString(), 303);
    }
  }
  // check email
  $posted_email = $input->post->get('rp_email', '', 'string');
  if ($posted_email === '') {
    $myError = '<span style="color: ' . $error_text_color . ';">' . $params->get('no_email', 'Please write your email') . '</span>';
    $email_class = ' has-error';
  }
  else if (!MailHelper::isEmailAddress($posted_email)) {
    $myError = '<span style="color: ' . $error_text_color . ';">' . $params->get('invalid_email', 'Please write a valid email') . '</span>';
    $email_class = ' has-error';
  }
  else {
    $CORRECT_EMAIL = $posted_email;
  }

  if ($myError == '') {
    $mailSender = Factory::getMailer();
    $mailSender->addRecipient($recipient);

    $from_email = ($params->get('from_email', 'rapid_contact@yoursite.com') == 'rapid_contact@yoursite.com') ? $app->get('mailfrom') : $params->get('from_email', 'rapid_contact@yoursite.com');

    $mailSender->setSender(array($from_email, $params->get('from_name', 'Rapid Contact')));
    $mailSender->addReplyTo($posted_email, $posted_email);

    $mailSender->setSubject($CORRECT_SUBJECT);

    ob_start();
    require ModuleHelper::getLayoutPath('mod_rapid_contact', 'default_message_body');
    $myMessage = ob_get_clean();
    $mailSender->setBody($myMessage);

    try {
      if ($mailSender->Send() !== true) {
        print '<span style="color: ' . $error_text_color . ';">' . $params->get('error_text', 'Your message could not be sent. Please try again.') . '</span>';
        return true;
      }
      else {
        // Post/Redirect/Get: a refresh will not re-send the message.
        // The thank-you state travels in the session, keeping the URL clean.
        $session->set('rp_thanks', $instanceId);
        $app->redirect(Uri::getInstance()->toString(), 303);
      }
    }
    catch(\Throwable $e) {
      print '<span style="color: ' . $error_text_color . ';">' . $params->get('error_text', 'Your message could not be sent. Please try again.') . '</span>';
      print '<br/><span style="color: ' . $error_text_color . ';">' . $e->getMessage() . '</span>';
    }

  }
} // end if posted

// Post/Redirect/Get: display the thank-you message after a successful submission.
if ((int) $session->get('rp_thanks', 0) === $instanceId) {
  $session->set('rp_thanks', null);
  require ModuleHelper::getLayoutPath('mod_rapid_contact', 'default_thank_you');
  return true;
}

// check recipient
if ($recipient === "email@email.com") {
  print '<span style="color: ' . $error_text_color . ';">Your form recipient is email@email.com. Please change that in the Rapid Contact module options.</span>';
  return true;
}

require ModuleHelper::getLayoutPath('mod_rapid_contact');
