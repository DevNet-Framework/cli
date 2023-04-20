# DevNet Cli
This dependency is a part of **DevNet Framework**, a command-line Interface tool, for managing DevNet projects.

## Requirements
- [DevNet Core](https://github.com/DevNet-Framework/core/) version 1.0
- [Composer](https://getcomposer.org/) version 2.0 or higher

## Installation
To install DevNet CLI in your project, run the following command in the terminal:
```
composer require devnet/cli
```
>**Note:** This is the minimal installation of DevNet Framework that allows you to create a console app only, but you can add other DevNet packages later, like devnet/web package to be able to create a web app.

You can install DevNet CLI globally in your system, by run the following command in the terminal:
```
composer global require devnet/cli
```

>**Note:** In order to run DevNet CLI globally in linux os, you need to add composer **bin** into the system environment variables.  
>`echo 'export PATH="$PATH:$HOME/.config/composer/vendor/bin"' >> ~/.bashrc`

## Usage
To show help on how to use **DevNet Command Line**, run the following option in your terminal:

```
devnet --help
```

## Documentation
Full documentation on how to use **DevNet Framework** is available at [devnet-framework.github.io](https://devnet-framework.github.io)

## License
This library is licensed under the MIT license. See [License File](https://github.com/DevNet-Framework/cli/blob/master/LICENSE) in the root folder for more information.
