
# Icinga Web 2 Urlshipper module

This module extends Icinga Director to provide an Import Source that will scrape json content from a given url.

Inspired by [icingaweb2-module-fileshipper](https://github.com/Icinga/icingaweb2-module-fileshipper).

## Installation

Same as any other Icinga Web 2 module:

```
cd /usr/share/icingaweb2/modules
git clone https://github.com/thomseddon/icingaweb2-module-urlshipper.git urlshipper
```

The module can be enabled in the Icinga Director GUI or via the cli:

```
icingacli module enable urlshipper
```

You can now define your Import Source.

## Copyright

2018 Thom Seddon

## License

[MIT](https://github.com/thomseddon/icingaweb2-module-urlshipper/blob/master/LICENSE)
