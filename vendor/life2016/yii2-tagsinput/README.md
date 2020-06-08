## Install

Via Composer

```bash
$ composer require life2016/yii2-tagsinput
```

or add

```
"life2016/yii2-tagsinput": "*"
```

to the require section of your `composer.json` file.


## Usage

On your view file.

```php

<?php
use wbraganca\tagsinput\TagsinputWidget;
?>

<?= $form->field($model, 'tags')->widget(TagsinputWidget::classname(), [
    'clientOptions' => [
        'trimValue' => true,
        'allowDuplicates' => false
    ]
]) ?>

```

For more options, visit: http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
