Date/DateTime control for [Nette Framework](http://nette.org)
=============================================================

[![Latest Stable Version](https://poser.pugx.org/nella/forms-datetime/v/stable.png)](https://packagist.org/packages/nella/forms-datetime) | [![Build Status](https://travis-ci.org/nella/forms-datetime.png?branch=master)](https://travis-ci.org/nella/forms-datetime)

Installation
------------

```
composer require nella/forms-datetime
```

Usage
------

```php

$form = new \Nette\Forms\Form;
$form->addComponent(new \Nella\Forms\Controls\DateInput('Date'), 'date');
$form->addComponent(new \Nella\Forms\Controls\DateTimeInput('DateTime'), 'datetime');

// or

\Nella\Forms\Controls\DateInput::register();
$form->addDate('date', 'Date', 'Y-m-d');

\Nella\Forms\Controls\DateTimeInput::register();
$form->addDateTime('datetime', 'DateTime', 'Y-m-d', 'G:i');

```

Manual rendering
----------------

```smarty
{form myForm}
	{label date /}
	{input date}

	{label datetime /}
    {input datetime:date}
    {input datetime:time}
{/form}
```

License
-------
Date/DateTime control for Nette Framework is licensed under the MIT License - see the LICENSE file for details
