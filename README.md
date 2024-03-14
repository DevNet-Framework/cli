# DevNet Cli
This dependency is a part of **DevNet Framework**, a command-line Interface tool, for managing DevNet projects.

## Requirements
- [PHP](https://www.php.net/) version 8.1 or higher
- [Composer](https://getcomposer.org/) version 2.0 or higher

>**Note:** If you are using composer on a Linux operating system, please make sure that you have added the path "composer/vendor/bin" to the system environment variables in the ".bashrc" file as follows:  
>`echo 'export PATH="$PATH:$HOME/.config/composer/vendor/bin"' >> ~/.bashrc`

## Installation
It's recommended that you install DevNet CLI globally in your system, by run the following command in the terminal:
```
composer global require devnet/cli
```

## Usage
To show help on how to use **DevNet Command Line**, run the following option in your terminal:

```
devnet --help
```

## Documentation
Full documentation on how to use **DevNet Framework** is available at [devnet-framework.github.io](https://devnet-framework.github.io)

## License
This library is licensed under the MIT license. See [License File](https://github.com/DevNet-Framework/cli/blob/master/LICENSE) in the root folder for more information.
