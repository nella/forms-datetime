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

use DateTimeImmutable;
use Nette\Forms\Container;
use Nette\Forms\Form;
use Nette\Forms\Rules;

/**
 * Date time input form control
 *
 * @author        Patrik Votoček
 *
 * @property string $value
 */
class DateTimeInput extends \Nette\Forms\Controls\BaseControl
{
	const FORMAT_PATTERN = '%s %s';

	const NAME_DATE = 'date';
	const NAME_TIME = 'time';

	/** @var bool */
	private static $registered = false;

	/** @var string */
	private $dateFormat;

	/** @var string */
	private $timeFormat;

	/** @var string */
	private $date;

	/** @var string */
	private $time;

	/**
	 * @param string
	 * @param string
	 * @param string|NULL
	 */
	public function __construct($dateFormat = 'Y-m-d', $timeFormat = 'G:i', $label = NULL)
	{
		parent::__construct($label);
		$this->dateFormat = $dateFormat;
		$this->timeFormat = $timeFormat;
	}

	/**
	 * @param \DateTimeInterface|NULL
	 * @return \DateInput
	 */
	public function setValue($value = NULL)
	{
		if ($value === NULL) {
			$this->date = NULL;
			$this->time = NULL;
			return $this;
		} elseif (!$value instanceof \DateTimeInterface) {
			throw new \Nette\InvalidArgumentException('Value must be DateTimeInterface or NULL');
		}

		$this->date = $value->format($this->dateFormat);
		$this->time = $value->format($this->timeFormat);

		return $this;
	}

	/**
	 * @return \DateTimeImmutable|NULL
	 */
	public function getValue()
	{
		if (empty($this->date) || empty($this->time)) {
			return NULL;
		}

		$value = $this->getWorkingValue();

		if ($value === FALSE) {
			return NULL;
		}

		return $value;
	}

	public function getWorkingValue()
	{
		return DateTimeImmutable::createFromFormat(
			sprintf(static::FORMAT_PATTERN, $this->dateFormat, $this->timeFormat),
			sprintf(static::FORMAT_PATTERN, $this->date, $this->time)
		);
	}

	/**
	 * @return boolean
	 */
	public function isFilled()
	{
		return !empty($this->date) && !empty($this->time);
	}

	public function loadHttpData()
	{
		$this->date = $this->getHttpData(Form::DATA_LINE, '[' . static::NAME_DATE . ']');
		$this->time = $this->getHttpData(Form::DATA_LINE, '[' . static::NAME_TIME . ']');
	}

	public function getControl()
	{
		return $this->getControlPart(static::NAME_DATE) . $this->getControlPart(static::NAME_TIME);
	}

	public function getControlPart($key)
	{
		$name = $this->getHtmlName();

		if ($key === static::NAME_DATE) {
			$control = \Nette\Utils\Html::el('input')->name($name . '[' . static::NAME_DATE . ']');
			$control->data('nella-date-format', $this->dateFormat);
			$control->value($this->date);
			$control->type('text');

			if ($this->disabled) {
				$control->disabled($this->disabled);
			}

			return $control;
		} elseif ($key === static::NAME_TIME) {
			$control = \Nette\Utils\Html::el('input')->name($name . '[' . static::NAME_TIME . ']');
			$control->data('nella-time-format', $this->timeFormat);
			$control->value($this->time);
			$control->type('text');

			if ($this->disabled) {
				$control->disabled($this->disabled);
			}

			return $control;
		}

		throw new \Nette\InvalidArgumentException('Part ' . $key . ' does not exist');
	}

	public function getLabelPart()
	{
		return NULL;
	}

	/**
	 * @param DateTimeInput
	 * @return bool
	 */
	public function validateDateTime(DateTimeInput $dateTimeInput)
	{
		return $this->isDisabled() || !$this->isFilled() || $this->getWorkingValue() !== FALSE;
	}

	public static function register()
	{
		if (static::$registered) {
			throw new \Nette\InvalidStateException('DateTimeInput control already registered.');
		}

		static::$registered = true;

		$class = get_called_class();
		$callback = function (Container $_this, $name, $label = NULL, $dateFormat = 'Y-m-d', $timeFormat = 'G:i') use ($class) {
			$control = new $class($dateFormat, $timeFormat, $label);
			$_this->addComponent($control, $name);
			return $control;
		};

		Container::extensionMethod('addDateTime', $callback);
	}
}
