<?php
/**
 * Test: Nella\Forms\DateTime\DateInput
 * @testCase
 *
 * This file is part of the Nella Project (http://nella-project.org).
 *
 * Copyright (c) Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.md that was distributed with this source code.
 */

namespace Nella\Forms\DateTime;

use DateTime;
use DateTimeImmutable;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

class DateInputTest extends \Tester\TestCase
{

	/**
	 * @return array[]|array
	 */
	public function dataValidDateValues()
	{
		return array(
			array(NULL, NULL),
			array(new DateTimeImmutable('1978-01-23 00:00:00'), new DateTimeImmutable('1978-01-23 00:00:00')),
			array(new DateTime('1978-01-23 00:00:00'), new DateTimeImmutable('1978-01-23 00:00:00')),
		);
	}

	/**
	 * @return array[]|array
	 */
	public function dataValidDates()
	{
		return array(
			array(NULL, NULL),
			array('', NULL),
			array('1978-01-23', new DateTimeImmutable('1978-01-23 00:00:00')),
		);
	}

	/**
	 * @return array[]|array
	 */
	public function dataInvalidDates()
	{
		return array(
			array(FALSE),
			array('1978/01/23'),
			array(254358000),
		);
	}

	/**
	 * @dataProvider dataValidDateValues
	 *
	 * @param DateTimeImmutable|NULL
	 * @param DateTimeImmutable|NULL
	 */
	public function testValidDates($input, $expected)
	{
		$control = new DateInput;

		$control->setValue($input);

		Assert::equal($expected, $control->getValue());
	}

	/**
	 * @dataProvider dataInvalidDates
	 * @throws \Nette\InvalidArgumentException
	 *
	 * @param string
	 */
	public function testInvalidDates($input)
	{
		$control = new DateInput;
		$control->setValue($input);
	}

	public function testHtml()
	{
		$form = new \Nette\Forms\Form;
		$control = new DateInput;
		$form->addComponent($control, 'date');
		$control->setValue(new DateTimeImmutable('1978-01-23 00:00:00'));

		$dq = \Tester\DomQuery::fromHtml((string) $control->getControl());

		Assert::true($dq->has("input[value='1978-01-23']"));
	}

	public function testLoadHttpDataEmpty()
	{
		$control = $this->createControl();

		Assert::false($control->isFilled());
		Assert::null($control->getValue());
	}

	/**
	 * @dataProvider dataValidDates
	 *
	 * @param mixed
	 * @param DateTimeImmutable|NULL
	 */
	public function testLoadHttpDataValid($input, $expected)
	{
		$control = $this->createControl(array(
			'date' => $input,
		));

		Assert::equal($expected, $control->getValue());
	}

	public function testLoadHttpDataInvalid()
	{
		$control = $this->createControl(array(
			'date' => 'test',
		));

		$control->addRule([$control, 'validateDate'], 'test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(array('test'), $control->getErrors());
	}

	public function testLoadHttpDataInvalidDate()
	{
		$control = $this->createControl(array(
			'date' => '2015-02-31',
		));

		$control->addRule([$control, 'validateDate'], 'test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(array('test'), $control->getErrors());
	}

	public function testAttribute()
	{
		$control = $this->createControl();

		$control->setAttribute('readonly', 'readonly');

		Assert::equal('readonly', $control->getControl()->readonly);
	}

	public function testDisabled()
	{
		$control = $this->createControl();

		Assert::false($control->getControl()->disabled);

		$control->setDisabled();

		Assert::true($control->getControl()->disabled);
	}

	/**
	 * @return array[]|array
	 */
	public function dataNonStrictDates()
	{
		$data = $this->dataValidDates();
		$data[] = array('1978 - 01 - 23', new DateTimeImmutable('1978-01-23 00:00:00'));
		return $data;
	}

	/**
	 * @dataProvider dataNonStrictDates
	 *
	 * @param mixed
	 * @param DateTimeImmutable|NULL
	 */
	public function testNonStrictLoadHttpDataValid($input, $expected)
	{
		$control = $this->createControl(array(
			'date' => $input,
		));
		$control->disableStrict();

		Assert::equal($expected, $control->getValue());
	}

	public function testLoadHttpDataValidStrictDate()
	{
		$control = $this->createControl(array(
			'date' => '1978-01-23',
		), true);

		$control->addRule([$control, 'validateDate'], 'test');

		Assert::true($control->isFilled());
		Assert::equal(new DateTimeImmutable('1978-01-23 00:00:00'), $control->getValue());

		$control->validate();

		Assert::false($control->hasErrors());
	}

	public function testLoadHttpDataInvalidStrictDate()
	{
		$control = $this->createControl(array(
			'date' => '2015 - 02 - 31',
		), true);

		$control->addRule([$control, 'validateDate'], 'test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(array('test'), $control->getErrors());
	}

	public function testInvalidRequired()
	{
		$control = $this->createControl(array(
			'date' => '2012-02-31',
		), true);

		$control->setRequired('test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(array('test'), $control->getErrors());
	}

	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function testRequiredInvalidMessage()
	{
		$control = $this->createControl();

		$control->setRequired();
	}

	/**
	 * @throws \Nette\InvalidStateException
	 */
	public function testRegistrationMultiple()
	{
		DateInput::register();
		DateInput::register();
	}

	public function testRegistration()
	{
		DateInput::register();

		$form = new \Nette\Forms\Form;
		$control = $form->addDate('test', 'Test');
		Assert::type('Nella\Forms\DateTime\DateInput', $control);
		Assert::equal('test', $control->getName());
		Assert::equal('Test', $control->caption);
		Assert::same($form, $control->getForm());
	}

	private function createControl($data = array(), $strict = false)
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_FILES = array();
		$_POST = $data;

		$form = new \Nette\Forms\Form;
		$control = new DateInput;
		if ($strict) {
			$control->enableStrict();
		} else {
			$control->disableStrict();
		}
		$form->addComponent($control, 'date');

		return $control;
	}

}

id(new DateInputTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
