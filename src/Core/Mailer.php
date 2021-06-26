<?php /** @noinspection PhpUnused */

namespace Miqu\Core;

use Miqu\Core\Interfaces\IContainer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer as Manager;

class Mailer
{
    /**
     * @var Manager
     */
    private $mailer;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        /** @var IContainer $container */
        global $container;
        $this->mailer = $container->Resolve( Manager::class );
        if ( env('mailing.is_smtp') )
        {
            $this->mailer->isSMTP();
            $this->mailer->Host       = env( 'mailing.smtp.host' );
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = env( 'mailing.smtp.user' );
            $this->mailer->Password   = env( 'mailing.smtp.password' );
            $this->mailer->Port       = env( 'mailing.smtp.port' );
            $this->mailer->SMTPSecure = Manager::ENCRYPTION_SMTPS;
            $this->mailer->setFrom( $this->mailer->Username );
        }
    }

    /**
     * For add attachment
     * @param string $location
     * @param string|null $name
     * @return Mailer
     * @throws Exception
     */
    public function addAttachment( string $location, string $name = null ) : self
    {
        $this->mailer->addAttachment( $location, is_null( $name ) ? '' : $name );
        return $this;
    }

    /**
     * For add a new BCC
     * @param string $email
     * @return Mailer
     * @throws Exception
     */
    public function addBcc( string $email ) : self
    {
        $this->mailer->addBCC( $email );
        return $this;
    }

    /**
     * For add a new cc
     * @param string $email
     * @return Mailer
     * @throws Exception
     */
    public function addCc( string $email ) : self
    {
        $this->mailer->addCC( $email );
        return $this;
    }

    /**
     * For add receiver
     * @param string $email
     * @return Mailer
     * @throws Exception
     */
    public function addReceiver( string $email ) : self
    {
        $this->mailer->addAddress( $email );
        return $this;
    }

    /**
     * Send the mail.
     * @return boolean.
     * @throws Exception
     */
    public function send() : bool
    {
        $results = $this->mailer->send();

        if ( ! $results )
            logger()->warning( 'failed to send an email: ' . $this->mailer->ErrorInfo );

        $this->clear();
        return $results;
    }

    public function isSMTP() : void
    {
        $this->mailer->isSMTP();
    }

    /**
     * For the formatted content (HTML) of the mail.
     * @param string $content
     * @param string|null $alt_body
     * @return Mailer
     */
    public function setContentHTML( string $content, string $alt_body = null ) : self
    {
        $this->mailer->isHTML( true );
        $this->mailer->Body = $content;
        
        if( $alt_body )
        {
            $this->mailer->AltBody = $alt_body;
        }

        return $this;
    }

    /**
     * For setting the plain content of the mail.
     * @param string $content
     * @return Mailer
     */
    public function setContentPlain( string $content ) : self
    {
        $this->mailer->isHTML( false );
        $this->mailer->Body = $content;
        return $this;
    }

    /**
     * For set reply_to in mail.
     * @param string $email The subject of the mail.
     * @return Mailer
     * @throws Exception
     */
    public function setReplyTo( string $email ) : self
    {
        $this->mailer->addReplyTo( $email );
        return $this;
    }

    /**
     * For set sender in mail.
     * @param string $email
     * @return Mailer
     * @throws Exception
     */
    public function setSender( string $email ) : self
    {
        $this->mailer->setFrom( $email );
        return $this;
    }

    /**
     * For set subject in mail.
     * @param string $subject The subject of the mail.
     * @return Mailer
     */
    public function setSubject( string $subject ) : self
    {
        $this->mailer->Subject = $subject;
        return $this;
    }   

    /**
     * Clear all the information
     * @return void
     */
    public function clear() : void
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearAllRecipients();
        $this->mailer->clearAttachments();
        $this->mailer->clearBCCs();
        $this->mailer->clearCCs();
        $this->mailer->clearReplyTos();
    }     
}