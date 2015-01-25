<?php
/**
 * This file is part of the Nella Project (http://nella-project.org).
 *
 * Copyright (c) Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.md that was distributed with this source code.
 */

namespace Nella\Forms\DateTime;

use Nette\Forms\Container;

/**
 * Date input form control
 *
 * @author Patrik Votoček
 *
 * @property string $value
 */
class DateInput extends \Nette\Forms\Controls\BaseControl
{

	const DEFAULT_FORMAT = 'Y-m-d';

	/** @var bool */
	private static $registered = FALSE;

	/** @var string */
	private $format;

	/**
	 * @param string
	 * @param string|NULL
	 */
	public function __construct($format = self::DEFAULT_FORMAT, $label = NULL)
	{
		parent::__construct($label);
		$this->format = $format;

		$this->getControlPrototype()->data('nella-date-format', $format);
	}

	/**
	 * @param \DateTimeInterface|NULL
	 * @return \Nella\Forms\DateTime\DateInput
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
		if (!$this->isFilled()) {
			return NULL;
		}

		$datetime = \DateTimeImmutable::createFromFormat($this->format, $this->getRawValue());
		if ($datetime === FALSE || $datetime->format($this->format) !== $this->getRawValue()) {
			return NULL;
		}

		return $datetime->setTime(0, 0, 0);
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
	 * @return bool
	 */
	public function isFilled()
	{
		$value = $this->getRawValue();
		return $value !== NULL && $value !== array() && $value !== '';
	}

	/**
	 * @param \Nella\Forms\DateTime\DateInput
	 * @return bool
	 */
	public function validateDate(DateInput $control)
	{
		return $this->isDisabled() || !$this->isFilled() || $this->getValue() !== NULL;
	}

	public static function register()
	{
		if (static::$registered) {
			throw new \Nette\InvalidStateException('DateInput control already registered.');
		}

		static::$registered = TRUE;

		$class = get_called_class();
		$callback = function (Container $_this, $name, $label = NULL, $format = 'Y-m-d') use ($class) {
			$control = new $class($format, $label);
			$_this->addComponent($control, $name);
			return $control;
		};

		Container::extensionMethod('addDate', $callback);
	}

}
