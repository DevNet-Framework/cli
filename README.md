# DevNet Cli
This dependency is a part of **DevNet Framework**, a command-line Interface tool, that used for creating and running DevNet applications.

## Requirements
- PHP 7.4 or higher version from [php.net](https://www.php.net/)
- Composer the dependency manager from [getcomposer.org](https://getcomposer.org/)
- Git the distributed version control system from [git-scm.com](https://git-scm.com/)

## Installation
> **Note :** This is an early release, not recommended for production use.

First we need to config composer global minimum-stability to development, because DevNet is still in development

so in the terminal type the following command:

```bash
composer global config minimum-stability dev
```

To install DevNet framework globally, run the following command in the terminal:

```bash
composer global require devnet/cli
```
>**Note:** For Linux users, do not forget to add composer **bin** into the System Environment Variables, like the following line:
`echo 'export PATH="$PATH:$HOME/.config/composer/vendor/bin"' >> ~/.bashrc`

## Getting Startted
In your terminal run the following command:

```bash
devnet new console --project YourProjectName
```

This will create a simple console application that output a "Hello World" message in your terminal.

```php
<?php

namespace Application;

use DevNet\System\Console;

class Program
{
   public static function main(array $args = [])
   {
      Console::writeLine("Hello World!");
   }
}
```

To run your application, run the following command in your terminal:

```bash
devnet run
```

##### Output

```bash
Hello World!
```

 For more help on how to use **DevNet Command Line**, run the following option in your terminal:

```bash
devnet --help
```
>**Note :** Full documentation on how to use **DevNet Framework** is available at [devnet-framework.github.io](https://devnet-framework.github.io)

That's it! Enjoy coding and build cool things :)
