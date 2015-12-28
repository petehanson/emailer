# Design

This outlines the design a bit about how Emailer works.

The concept behind Emailer is that gives a convenient way to handle manage elements of system based emails that need to
go out. Emails are handled inside a configuration file, where subject, attachments, body messages, etc... are defined.

Along with providing a simple storage mechanism for aspects of an email, Emailer also provides the ability for tag
replacement in the subject and header. So if you're sending a series of emails, say to a list of recipients, you can
define a tag to represent the person's first name for example and then you could replace a placeholder tag for
first name with the actual name to generate a personalized email.

Environment Variables Needed:

General variables:
emailer_driver = smtp
emailer_message_location = ../_data/

SMTP variables:
emailer_smtp_host = localhost
emailer_smtp_port = 1025
emailer_smtp_username = user
emailer_smtp_password = pass
emailer_smtp_encryption = ssl

Sendmail variables:
emailer_sendmail_binary = /usr/bin/sendmail







An example of use for the public interface:


$emailerConfig = new \UAR\EmailerConfig();
$emailerConfig->host = 'localhost';
$emailerConfig->port = '1025';


$emailer = \UAR\EmailerFactory::smtpEmailer($emailerConfig);

$email = new \UAR\Email("path/to/email.config.json");
$email->replace("{{firstName}}",$firstName);
$email->replace("{{lastName}}",$lastName);

$emailer->send($email);




$messageFactory = new \UAR\MessageFactory("path/to/email.config.json");
$swiftMailerMessage = $messageFactory->newInstance()


or:

$message = \UAR\EmailFactory::message("message.title");
$message->replace("{{firstName}}",$firstName);
$message->replace("{{lastName}}",$lastName);
$result = \UAR\EmailFactory::send($message);


what's contained in message:





what's contained in send:



