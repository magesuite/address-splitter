# CLI commands

This module uses viison/address-splitter (https://github.com/pickware/address-splitter) repository to split street into street and house number in customer address entities.

It allows to use two commands:
- `address:splitter:split`<br/>
Test command, can be used to check the address-splitter logic.<br/>
Example:<br/>
```bash
bin/magento address:splitter:split "test address 11"
```
will return
```bash
Array
(
    [0] => test address
    [1] => 11
)
```

- `address:extract:housenumber mode`<br/>
Command extract house number from street and updates street in customer address entries in the database.<br/>
<br/>
Mode:
- `preview` loads data from database, parses them and saves result into the CSV file
- `execute` loads data from db, parses them, saves result into the CSV file and updats data in the database
