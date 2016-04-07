# CodeIgniter Model
CodeIgniter 3 Model extension, working with maltyxx Origami package (see https://github.com/maltyxx/origami)

## Requirements

- PHP 5.4.x (Composer requirement)
- CodeIgniter 3.0.x

## Step 1 : Installation (by Composer)
#### Option 1 : Run composer
```shell
composer require gccloud/parser
```
#### Option 2 : or edit /composer.json
```json
{
    "require":
    {
        "gccloud/model": "1.1.*"
    }
}
```
#### And then run composer update
```shell
composer update
```

### Step 2 : Add it to CodeIgniter
Create core file in `/application/core/MY_Model.php`.
```php
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/third_party/model/MY_Model.php');
```
