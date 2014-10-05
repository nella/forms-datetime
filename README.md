Date/DateTime control for [Nette Framework](http://nette.org)
=============================================================

[![Build Status](https://travis-ci.org/nella/forms-datetime.svg?branch=nette)](https://travis-ci.org/nella/forms-datetime)
[![Latest Stable Version](https://poser.pugx.org/nella/forms-datetime/version.png)](https://packagist.org/packages/nella/forms-datetime)
[![Composer Downloads](https://poser.pugx.org/nella/forms-datetime/d/total.png)](https://packagist.org/packages/nella/forms-datetime)
[![Dependency Status](https://www.versioneye.com/user/projects/53a201e883add7719f000004/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53a201e883add7719f000004)

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

// Optional date[time] validation
$form['date']
	->addCondition(\Nette\Application\UI\Form::FILLED)
		->addRule([$form['date'], 'validateDate'], 'Date is invalid');

$form['datetime']
	->addCondition(\Nette\Application\UI\Form::FILLED)
		->addRule([$form['datetime'], 'validateDateTime'], 'Date time is invalid');

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
