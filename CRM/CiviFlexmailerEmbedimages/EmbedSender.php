<?php

/*
 *
 * Nearly a 100% copy&paste from
 * https://github.com/civicrm/org.civicrm.flexmailer/blob/master/src/Listener/DefaultSender.php
 * except the function insert around line 49 to implement message composition with embedded images
 *
 */

class CRM_CiviFlexmailerEmbedimages_EmbedSender extends \Civi\FlexMailer\Listener\BaseListener
{
    const BULK_MAIL_INSERT_COUNT = 10;

    public function onSend(Civi\FlexMailer\Event\SendBatchEvent $e) {
      static $smtpConnectionErrors = 0;

      if (!$this->isActive()) {
        return;
      }

      $e->stopPropagation();

      $job = $e->getJob();
      $mailing = $e->getMailing();
      $job_date = \CRM_Utils_Date::isoToMysql($job->scheduled_date);
      $mailer = \Civi::service('pear_mail');

      $targetParams = $deliveredParams = array();
      $count = 0;
      $retryBatch = FALSE;

      foreach ($e->getTasks() as $key => $task) {
        /** @var \Civi\FlexMailer\FlexMailerTask $task */
        /** @var \Mail_mime $message */
        if (!$task->hasContent()) {
          continue;
        }

        $message = \Civi\FlexMailer\MailParams::convertMailParamsToMime($task->getMailParams());

        if (empty($message)) {
          // lets keep the message in the queue
          // most likely a permissions related issue with smarty templates
          // or a bad contact id? CRM-9833
          continue;
        }
        /* insert start */
        $message = CRM_CiviFlexmailerEmbedimages_EmbedHTMLImages::doEmbed( $message );
        /* insert end */

        // disable error reporting on real mailings (but leave error reporting for tests), CRM-5744
        if ($job_date) {
          $errorScope = \CRM_Core_TemporaryErrorScope::ignoreException();
        }

        $headers = $message->headers();
        $result = $mailer->send($headers['To'], $message->headers(), $message->get());

        if ($job_date) {
          unset($errorScope);
        }

        if (is_a($result, 'PEAR_Error')) {
          /** @var \PEAR_Error $result */
          // CRM-9191
          $message = $result->getMessage();
          if ($this->isTemporaryError($result->getMessage())) {
            // lets log this message and code
            $code = $result->getCode();
            \CRM_Core_Error::debug_log_message("SMTP Socket Error or failed to set sender error. Message: $message, Code: $code");

            // these are socket write errors which most likely means smtp connection errors
            // lets skip them and reconnect.
            $smtpConnectionErrors++;
            if ($smtpConnectionErrors <= 5) {
              $mailer->disconnect();
              $retryBatch = TRUE;
              continue;
            }

            // seems like we have too many of them in a row, we should
            // write stuff to disk and abort the cron job
            $job->writeToDB($deliveredParams, $targetParams, $mailing, $job_date);

            \CRM_Core_Error::debug_log_message("Too many SMTP Socket Errors. Exiting");
            \CRM_Utils_System::civiExit();
          }
          else {
            $this->recordBounce($job, $task, $result->getMessage());
          }
        }
        else {
          // Register the delivery event.
          $deliveredParams[] = $task->getEventQueueId();
          $targetParams[] = $task->getContactId();

          $count++;
          if ($count % self::BULK_MAIL_INSERT_COUNT == 0) {
            $job->writeToDB($deliveredParams, $targetParams, $mailing, $job_date);
            $count = 0;

            // hack to stop mailing job at run time, CRM-4246.
            // to avoid making too many DB calls for this rare case
            // lets do it when we snapshot
            $status = \CRM_Core_DAO::getFieldValue(
              'CRM_Mailing_DAO_MailingJob',
              $job->id,
              'status',
              'id',
              TRUE
            );

            if ($status != 'Running') {
              $e->setCompleted(FALSE);
              return;
            }
          }
        }

        unset($result);

        // seems like a successful delivery or bounce, lets decrement error count
        // only if we have smtp connection errors
        if ($smtpConnectionErrors > 0) {
          $smtpConnectionErrors--;
        }

        // If we have enabled the Throttle option, this is the time to enforce it.
        $mailThrottleTime = \CRM_Core_Config::singleton()->mailThrottleTime;
        if (!empty($mailThrottleTime)) {
          usleep((int) $mailThrottleTime);
        }
      }

      $completed = $job->writeToDB(
        $deliveredParams,
        $targetParams,
        $mailing,
        $job_date
      );
      if ($retryBatch) {
        $completed = FALSE;
      }
      $e->setCompleted($completed);
    }

    /**
     * Determine if an SMTP error is temporary or permanent.
     *
     * @param string $message
     *   PEAR error message.
     * @return bool
     *   TRUE - Temporary/retriable error
     *   FALSE - Permanent/non-retriable error
     */
    protected function isTemporaryError($message) {
      // SMTP response code is buried in the message.
      $code = preg_match('/ \(code: (.+), response: /', $message, $matches) ? $matches[1] : '';

      if (strpos($message, 'Failed to write to socket') !== FALSE) {
        return TRUE;
      }

      // Register 5xx SMTP response code (permanent failure) as bounce.
      if (isset($code{0}) && $code{0} === '5') {
        return FALSE;
      }

      if (strpos($message, 'Failed to set sender') !== FALSE) {
        return TRUE;
      }

      if (strpos($message, 'Failed to add recipient') !== FALSE) {
        return TRUE;
      }

      if (strpos($message, 'Failed to send data') !== FALSE) {
        return TRUE;
      }

      return FALSE;
    }

    /**
     * @param \CRM_Mailing_BAO_MailingJob $job
     * @param \Civi\FlexMailer\FlexMailerTask $task
     * @param string $errorMessage
     */
    protected function recordBounce($job, $task, $errorMessage) {
      $params = array(
        'event_queue_id' => $task->getEventQueueId(),
        'job_id' => $job->id,
        'hash' => $task->getHash(),
      );
      $params = array_merge($params,
        \CRM_Mailing_BAO_BouncePattern::match($errorMessage)
      );
      \CRM_Mailing_Event_BAO_Bounce::create($params);
    }

}

class CRM_CiviFlexmailerEmbedimages_EmbedHTMLImages {

  const TRACKER_PARTS = [
    'civicrm/extern/open.php',
    'civicrm/mailing/open'
  ];

  // compile a list of images in the HTML, replace in HTML with aliases,
  // return an array of image-alias => image-URL
  private static function scanHTMLforImages( &$html_body, $only_local = false ) {
    $result = [];
    // supress warnings
    libxml_use_internal_errors( true );
    try {
      // convert HTML mail into DOM object
      $html_DOM = \DOMDocument::loadHTML( $html_body, LIBXML_BIGLINES );
      // simply return on error
      if ( false === $html_DOM )
          return $result;
      // setup URL parts of our upload directory
      $uploaddir = CRM_Utils_System::baseURL();
      $uploaddir_parts = parse_url( $uploaddir );
      // search img tags
      $images = $html_DOM->getElementsByTagName( 'img' );
      foreach ( $images as $image ) {
        // get src attribute
        if ( $image->hasAttribute( 'src' ) ) {
          $img_src = $image->getAttribute( 'src' );
          // create image URL parts
          $img_src_parts = parse_url( $img_src );
          // if the query string is URL encoded, decode it to make the tracker path findable
          if ( array_key_exists('query', $img_src_parts)) {
            if ( false !== stripos( $img_src_parts[ 'query' ], "%2F" ) ) {
              $img_src_parts[ 'query' ] = urldecode( $img_src_parts[ 'query' ] );
            }
          } else {
            $img_src_parts[ 'query' ] = '';
          }
          if ( ( !$only_local ) || ( $img_src_parts[ 'host' ] == $uploaddir_parts[ 'host' ] ) ) {
            // ignore tracker pixels
            $is_tracker = false;
            foreach( self::TRACKER_PARTS as $tracker_part )
            {
              if (
                ( $img_src_parts[ 'host' ] == $uploaddir_parts[ 'host' ] ) && (
                  ( false !== strpos( $img_src_parts[ 'path' ], $tracker_part ) ) ||
                  ( false !== strpos( $img_src_parts[ 'query' ], $tracker_part ) )
                )
              ) {
                $is_tracker = true;
                break;
              }
            }
            if ( !$is_tracker ) {
              // alias is the md5 of the full URL
              $img_file_alias = md5( $img_src );
              // replace
              $image->setAttribute( 'src', $img_file_alias );
              // push into result array
              if ( !array_key_exists( $img_file_alias, $result ) ) {
                $result[ $img_file_alias ] = $img_src;
              }
            }
          }
        }
      }
      // convert DOM back to HTML string
      $html_body = $html_DOM->saveHTML();
    }
    catch ( \Exception $e )
    {
      \CRM_Core_Error::debug_log_message( "Failed to scan HTML e-mail images. Exiting" );
      // todo: error message to frontend
    }
    libxml_clear_errors();
    return $result;
  }

  private static function delTree( $dir, $removeSelf = false ) {
    if (!file_exists( $dir )) {
      return false;
    }
    $files = array_diff( scandir( $dir ), array( '.', '..' ) );
    foreach ( $files as $file ) {
      ( is_dir( "$dir" . DIRECTORY_SEPARATOR . "$file" ) ) ? self::delTree( "$dir" . DIRECTORY_SEPARATOR . "$file", true ) : unlink( "$dir" . DIRECTORY_SEPARATOR . "$file" );
    }
    if ( $removeSelf ) {
      return rmdir( $dir );
    } else {
      return true;
    }
  }

  private static function getCacheRootDir() {
    return CRM_Core_Config::singleton()->uploadDir . 'flexmailer_embed';
  }

  public static function flushCache() {
    self::delTree( self::getCacheRootDir(), false );
  }

  public static function doEmbed( $message ) {
    $embedImages = CRM_Core_BAO_Setting::getItem( 'Advanced E-Mail settings', 'civi_flexmailer_embedimages' );
    $onlyLocal = CRM_Core_BAO_Setting::getItem( 'Advanced E-Mail settings', 'civi_flexmailer_embedimages_local' );

    if ( $embedImages ) {
      $html_body = $message->getHTMLBody();
      $image_array = self::scanHTMLforImages( $html_body, $onlyLocal );
      if ( !empty( $image_array ) ) {
        $message->setHTMLBody( $html_body );
        // every week a new cache directory
        $CacheRootDir = self::getCacheRootDir();
        $CacheDir = $CacheRootDir . DIRECTORY_SEPARATOR . date( 'W' );
        if ( !file_exists( $CacheDir ) ) {
          // clean old cache dirs, create new
          self::delTree( $CacheRootDir, false );
          if ( !mkdir( $CacheDir, 0755, true ) ) {
            \CRM_Core_Error::debug_log_message( "Failed to create cache directory $CacheDir - exiting" );
            \CRM_Utils_System::civiExit();
            return;
          }
        }
        foreach ( $image_array as $img_file_alias => $img_src ) {
          $img_cache_filename = $CacheDir . DIRECTORY_SEPARATOR . 'fx_mb_' . $img_file_alias;
          // look if image is cached
          if ( !file_exists( $img_cache_filename ) ) {
            // cache file
            if ( false === file_put_contents( $img_cache_filename, fopen( $img_src, 'r') ) ) {
              \CRM_Core_Error::debug_log_message( "Failed to cache image file $img_cache_filename - exiting" );
              \CRM_Utils_System::civiExit();
              return;
            }
          }
          $img_mime = mime_content_type( $img_cache_filename );
          $message->addHTMLImage( $img_cache_filename, $img_mime, $img_file_alias );
        }
      }
    }
    return $message;
  }

}

?>
