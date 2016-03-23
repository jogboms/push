<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Components;

class MailException extends \Exception {}
class Mail
{
  private $mailer, $salt, $simulate, $wordWrap = 50;
  public  $debug = false, $debugType = 'html', $params;

  private $init = null;
  public static function instance(){
    return is_null(static::$init) ? static::$init = new self : static::$init;
  }
  /**
  * constructor inits mailer object
  */
  function __construct(Array $params = [], $debug = 0, $debugType = 'html'){
    if(count($params) !== 4)
      throw new MailException('new Mail($params): <br /> $params should be in this format |<br /> array(\''.implode(['fromName','fromMail','replyName','replyMail'], '\' => [value], \'').'\' => [value])');

    include_once(LIBRARY.DS.'phpmailer'.DS.'class.phpmailer.php');

    $config = \Push\Application::init()->config('mail');
    $this->params = $params;
    $this->debug = $debug;
    $this->debugType = $debugType;
    $this->mailer = new \PHPMailer();
    $this->mailer->isSMTP();
    $this->mailer->Host = $config->SMTPhost;
    $this->mailer->Username = $config->SMTPusername;
    $this->mailer->Password = $config->SMTPpassword;
    $this->mailer->Port = $config->SMTPport;
    $this->mailer->SMTPSecure = $config->SMTPsecure;
    $this->mailer->SMTPAuth = $config->SMTPauth;
    $this->mailer->WordWrap = $this->wordWrap;
    return $this;
  }

  /**
  * function to send mail
  * @param array receiver, contains name and email of receiver
  * @param mailbody mail content
  * @param name  name of receiver
  */
  function send($receiver, $mailerTitle = 'New Mail', $mailerBody = '', $isHTML=false){
    // alert(func_get_args());
    $this->mailer->SMTPDebug = $this->debug;
    $this->mailer->Debugoutput = $this->debugType;
    $this->mailer->setFrom($this->params['fromMail'], $this->params['fromName']);
    $this->mailer->addReplyTo($this->params['replyMail'], $this->params['replyName']);
    $this->mailer->Subject = $mailerTitle;
    $this->mailer->addAddress($receiver[1], $receiver[0]);
    if($isHTML===true){
      $this->mailer->isHTML(true);
      $this->mailer->Body = $mailerBody;
      $this->mailer->msgHTML($mailerBody);
      $this->mailer->AltBody = strip_tags(br2nl($mailerBody));
    }
    else $this->mailer->Body = $mailerBody;

    return $this->simulate ? true : $this->mailer->Send();
  }

  /**
  * function to simulate successful mail delivery
  *
  * eg $mailObject->simulate()->send([$params])
  * @return object
  */
  function simulate(){
    $this->simulate = true;
    return $this;
  }
  /**
  * function to return mail Error
  * @return object
  */
  function wordWrap($wordWrap){
    $this->mailer->WordWrap = $wordWrap;
    return $this;
  }
  /**
  * function to return mail Error
  * @return string
  */
  function error(){
    return $this->mailer->ErrorInfo;
  }
  /**
  * function to clear current mail list
  * @return void
  */
  function clear(){
    return $this->mailer->clearAddresses();
  }
}
