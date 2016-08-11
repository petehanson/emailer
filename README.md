# Installation

You can include this library in your project, via composer:

"uarsoftware/emailer": "@dev"


# Environment Configuration For The Transport

The application requires some basic configuration. There are some sample env files that you can reference and use. They
are located in samples/env/

At a minimum, two environment variables need to be defined:

```
$_ENV['emailer_driver'] = "smtp|sendmail";  
$_ENV['emailer_message_location'] = "path/to/json/config/files";
```

emailer_driver handles the driver setting for the Swiftmailer transport. You define "smtp" or "sendmail" to define
which transport and ultimately config approach is used.  Each config can take in different parameters for through
then $_ENV variable to config how that transport works. More on this below.

emailer_message_location handles the location of where the JSON config files for each email are located. Ideally, an
absolute path is given to the location. You can pass in relative paths as well, which could vary, depending on how
the working directory is set. Ultimately, file_get_contents is used to read the config files. Any mechanisms that
that function uses to find file references would work in this setting.

The following sections outline the environment variables supported by each config driver:

SMTP:

The following are the supported SMTP specific environment variables.

```
$_ENV['emailer_smtp_host']  
$_ENV['emailer_smtp_port']  
$_ENV['emailer_smtp_username']  
$_ENV['emailer_smtp_password']  
$_ENV['emailer_smtp_encryption']
```

These are the same parameters that can be set on a Swift_SmtpTransport object.

Sendmail:

The following are the supported Sendmail specific environment variables.

$_ENV['emailer_sendmail_binary']

This is the same parameter that can be set on a Swift_SendmailTransport object.


# Example Usage

See the examples in the examples/ folder.

Here's a quick example for sending through MailCatcher:

```
require_once("../vendor/autoload.php");  
use \UAR\Emailer\Factory as EmailerFactory;

$_ENV['emailer_driver'] = 'smtp';  
$_ENV['emailer_smtp_host'] = 'localhost';  
$_ENV['emailer_smtp_post'] = 1025;  
$_ENV['emailer_message_location'] = __DIR__;  // use the current folder that this example file is in  
  
try {  
    $message = EmailerFactory::message("example1");
    $result = EmailerFactory::send($message);
} catch (Exception $e) {
    var_dump($e);
}
```

# JSON Message Config Samples

See the files located at samples/emails/


