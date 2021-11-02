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
```
composer global config minimum-stability dev
```

Then force composer to prefer the stable version if that possible, like so:
```
composer global config prefer-stable true
```

To install DevNet Cli globally, run the following command in the terminal:
```
composer global require devnet/cli
```

This will run a minimal installation of DevNet Framework that allows you to create a console app only, but you can add other DevNet packages later, like devnet/web package to be able to create a web app.

>**Note:** For Linux users, do not forget to add composer **bin** into the System Environment Variables, like the following line:  
>`echo 'export PATH="$PATH:$HOME/.config/composer/vendor/bin"' >> ~/.bashrc`

## Usage
To show help on how to use **DevNet Command Line**, run the following option in your terminal:

```
devnet --help
```

##### Output
```
DevNet command-line interface v1.0.0
Usage: devnet [options]

Options:
   --help      Show command line help.
   --version   Show DevNet Cli version.
   --path      Show DevNet runtime path.

Usage: devnet [command] [arguments] [options]

commands:
   new   Create a new project
   run   Run the DevNet applicaton
   add   Add a template code file to the project

Run 'devnet [command] --help' for more information on a command.
```

The Help shows a list of responsible commands that you can use with DevNet Cli, and for more details about a command that you want to use you can run it with the option `--help` like in the following example:

```
devnet new --help
```

##### Output
```
Usage: devnet new [template] [options] [arguments]

Options:
  --help     Displays help for this command.
  --project  Location to place the generated project.

templates:
  console    Console Applicatinon project
```

The help shows a usage of the `new` command followed by the name of the project template that you want to create, and followed by options and arguments, and shows a list of options that you can use with the command and a list of templates that you can choose from to create your project.

### Add more Templates
By default DevNet Cli include **console** template only, and in order to create a **web** project like **web api** and **mvc** project, you have to install DevNet addons templates  

To add empty web template to your system, run the following command in your terminal:
```
composer global require devnet/web
```

To add mvc web template to your system, run the following command in your terminal:
```
composer global require devnet/mvc
```

Now when we check again the list of the available templates, we find that we can create more than just a console project.

```
devnet new --help
```

##### Output
```
Usage: devnet new [template] [arguments] [options]

Options:
  --help     Displays help for this command.
  --project  Location to place the generated project.

templates:
  console    Console Application
  mvc        DevNet Web Application (Model-View-Controller)
  web        DevNet Web Application (Empty)
```

### Create new Project
This example shows how to create a console application project, so in your terminal run the following command:

```
devnet new console --project YourProjectName
```

This will create a Program class that outputs a "Hello World" message in your terminal.

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

### Run Your Application
To run your application, go to your project folder and run the following command in your terminal:

```
devnet run
```

##### Output

```
Hello World!
```

>**Note :** Full documentation on how to use **DevNet Framework** is available at [devnet-framework.github.io](https://devnet-framework.github.io)

That's it! Enjoy coding and build cool things :)
