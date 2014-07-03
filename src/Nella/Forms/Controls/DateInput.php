<?php
/**
 * This file is part of the Nella Project (http://nella-project.org).
 *
 * Copyright (c) Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.md that was distributed with this source code.
 */

namespace Nella\Forms\Controls;

use Nette\Forms\Container;
use Nette\Forms\Rules;

/**
 * Date input form control
 *
 * @author        Patrik Votoček
 *
 * @property string $value
 */
class DateInput extends \Nette\Forms\Controls\BaseControl
{
	/** @var bool */
	private static $registered = false;

	/** @var string */
	private $format;

	/**
	 * @param string
	 * @param string|NULL
	 */
	public function __construct($format = 'Y-m-d', $label = NULL)
	{
		parent::__construct($label);
		$this->format = $format;

		$this->getControlPrototype()->data('nella-date-format', $format);
	}

	/**
	 * @param \DateTimeInterface|NULL
	 * @return \DateInput
	 */
	public function setValue($value = NULL)
	{
		if ($value === NULL) {
			return parent::setValue(NULL);
		} elseif (!$value instanceof \DateTimeInterface) {
			throw new \Nette\InvalidArgumentException('Value must be DateTimeInterface or NULL');
		}

		return parent::setValue($value->format($this->format));
	}

	/**
	 * @return \DateTimeImmutable|NULL
	 */
	public function getValue()
	{
		$value = $this->getWorkingValue();

		if ($value === FALSE || $value === NULL) {
			return NULL;
		}

		return $value->setTime(0, 0, 0);
	}

	/**
	 * @return mixed
	 */
	public function getRawValue()
	{
		return parent::getValue();
	}

	public function loadHttpData()
	{
		parent::setValue($this->getHttpData(\Nette\Forms\Form::DATA_TEXT));
	}

	/**
	 * @return \DateTimeImmutable|FALSE|NULL
	 */
	private function getWorkingValue()
	{
		if (empty($this->getRawValue())) {
			return NULL;
		}

		return \DateTimeImmutable::createFromFormat($this->format, $this->getRawValue());
	}

	/**
	 * @return \Nette\Utils\Html
	 */
	public function getControl()
	{
		$control = parent::getControl();
		$control->value($this->getRawValue());
		$control->type('text');
		return $control;
	}

	/**
	 * @return boolean
	 */
	public function isFilled()
	{
		$value = $this->getRawValue();
		return $value !== NULL && $value !== array() && $value !== '';
	}

	/**
	 * @param DateInput
	 * @return bool
	 */
	public function validateDate(DateInput $control)
	{
		return $this->isDisabled() || !$this->isFilled() || $this->getWorkingValue() !== FALSE;
	}

	public static function register()
	{
		if (static::$registered) {
			throw new \Nette\InvalidStateException('DateInput control already registered.');
		}

		static::$registered = true;

		$class = get_called_class();
		$callback = function (Container $_this, $name, $label = NULL, $format = 'Y-m-d') use ($class) {
			$control = new $class($format, $label);
			$_this->addComponent($control, $name);
			return $control;
		};

		Container::extensionMethod('addDate', $callback);
	}
}
