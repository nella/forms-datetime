<?php
/**
 * Test: Nella\Forms\DateTime\DateTimeInput
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

class DateTimeInputTest extends \Tester\TestCase
{

	/**
	 * @return array[]|array
	 */
	public function dataValidDateValues()
	{
		return [
			[NULL, NULL],
			[new DateTimeImmutable('1978-01-23 00:00:00'), new DateTimeImmutable('1978-01-23 00:00:00')],
			[new DateTime('1978-01-23 00:00:00'), new DateTimeImmutable('1978-01-23 00:00:00')],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataValidDateTimes()
	{
		return [
			[NULL, NULL, NULL],
			[NULL, '', NULL],
			['', NULL, NULL],
			['', '', NULL],
			['1978-01-23', '12:00', new DateTimeImmutable('1978-01-23 12:00:00')],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataInvalidDates()
	{
		return [
			[FALSE],
			['1978/01/23'],
			[254358000],
		];
	}

	/**
	 * @dataProvider dataValidDateValues
	 *
	 * @param DateTimeImmutable|NULL $input
	 * @param DateTimeImmutable|NULL $expected
	 */
	public function testValidDateTimes($input, $expected)
	{
		$control = new DateTimeInput;

		$control->setValue($input);

		Assert::equal($expected, $control->getValue());
	}

	/**
	 * @dataProvider dataInvalidDates
	 * @throws \Nette\InvalidArgumentException
	 *
	 * @param string $input
	 */
	public function testInvalidDateTimes($input)
	{
		$control = new DateTimeInput;
		$control->setValue($input);
	}

	public function testHtml()
	{
		$form = new \Nette\Forms\Form;
		$control = new DateTimeInput;
		$form->addComponent($control, 'datetime');
		$control->setValue(new DateTimeImmutable('1978-01-23 12:00:00'));

		$dq = \Tester\DomQuery::fromHtml((string) $control->getControl());

		Assert::true($dq->has("input[value='1978-01-23']"));
		Assert::true($dq->has("input[value='12:00']"));
	}

	public function testLoadHttpDataEmpty()
	{
		$control = $this->createControl();

		Assert::false($control->isFilled());
		Assert::null($control->getValue());
	}

	/**
	 * @dataProvider dataValidDateTimes
	 *
	 * @param mixed $date
	 * @param mixed $time
	 * @param DateTimeImmutable|NULL $expected
	 */
	public function testLoadHttpDataValid($date, $time, $expected)
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => $date,
				'time' => $time,
			],
		]);

		Assert::equal($expected, $control->getValue());
	}

	public function testLoadHttpDataInvalid()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => 'test',
				'time' => 'test',
			],
		]);

		$control->addRule([$control, 'validateDateTime'], 'test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(['test'], $control->getErrors());
	}

	public function testLoadHttpDataInvalidDate()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '2015-02-31',
				'time' => '11:59',
			],
		]);

		$control->addRule([$control, 'validateDateTime'], 'test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(['test'], $control->getErrors());
	}

	public function testLoadHttpDataInvalidTime()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '2015-02-28',
				'time' => '11:61',
			],
		]);

		$control->addRule([$control, 'validateDateTime'], 'test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(['test'], $control->getErrors());
	}

	public function testAttribute()
	{
		$control = $this->createControl();

		$control->setAttribute('readonly', 'readonly');

		Assert::equal('readonly', $control->getControlPart(DateTimeInput::NAME_DATE)->readonly);
		Assert::equal('readonly', $control->getControlPart(DateTimeInput::NAME_TIME)->readonly);
	}

	public function testRemovingAttribute()
	{
		$control = $this->createControl();

		$control->setAttribute('readonly', 'readonly');

		Assert::equal('readonly', $control->getControlPart(DateTimeInput::NAME_DATE)->readonly);
		Assert::equal('readonly', $control->getControlPart(DateTimeInput::NAME_TIME)->readonly);

		$control->setAttribute('readonly', NULL);

		Assert::false(isset($control->getControlPart(DateTimeInput::NAME_DATE)->readonly));
		Assert::false(isset($control->getControlPart(DateTimeInput::NAME_TIME)->readonly));
	}

	public function testPartWithoutPart()
	{
		$control = $this->createControl();

		Assert::equal($control->getControl(), $control->getControlPart());
	}

	public function testRemovingNotSetAttribute()
	{
		$control = $this->createControl();

		$control->setAttribute('readonly', NULL);

		Assert::false(isset($control->getControlPart(DateTimeInput::NAME_DATE)->readonly));
		Assert::false(isset($control->getControlPart(DateTimeInput::NAME_TIME)->readonly));
	}

	public function testDateAttribute()
	{
		$control = $this->createControl();

		$control->setDateAttribute('readonly', 'readonly');

		Assert::equal('readonly', $control->getControlPart(DateTimeInput::NAME_DATE)->readonly);
		Assert::false(isset($control->getControlPart(DateTimeInput::NAME_TIME)->readonly));
	}

	public function testTimeAttribute()
	{
		$control = $this->createControl();

		$control->setTimeAttribute('readonly', 'readonly');

		Assert::equal('readonly', $control->getControlPart(DateTimeInput::NAME_TIME)->readonly);
		Assert::false(isset($control->getControlPart(DateTimeInput::NAME_DATE)->readonly));
	}

	public function testDisabled()
	{
		$control = $this->createControl();

		Assert::false($control->getControlPart(DateTimeInput::NAME_DATE)->disabled);
		Assert::false($control->getControlPart(DateTimeInput::NAME_TIME)->disabled);

		$control->setDisabled();

		Assert::true($control->getControlPart(DateTimeInput::NAME_DATE)->disabled);
		Assert::true($control->getControlPart(DateTimeInput::NAME_TIME)->disabled);
	}

	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function testInvalidControlPart()
	{
		$control = $this->createControl();

		$control->getControlPart('test');
	}

	public function testLabelPart()
	{
		$control = $this->createControl();

		Assert::null($control->getLabelPart());
	}

	public function testFilledDate()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '1978-01-23',
			],
		]);

		$control->addRule([$control, 'validateDateTime'], 'test');

		$control->validate();

		Assert::true($control->isFilled());
		Assert::true($control->hasErrors());
	}

	public function testFilledTime()
	{
		$control = $this->createControl([
			'datetime' => [
				'time' => '12:00',
			],
		]);

		$control->addRule([$control, 'validateDateTime'], 'test');

		$control->validate();

		Assert::true($control->isFilled());
		Assert::true($control->hasErrors());
	}

	public function testFilledBoth()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '1978-01-23',
				'time' => '12:00',
			],
		]);

		$control->addRule([$control, 'validateDateTime'], 'test');

		$control->validate();

		Assert::true($control->isFilled());
		Assert::false($control->hasErrors());
	}

	public function testNotFilled()
	{
		$control = $this->createControl();

		$control->addRule([$control, 'validateDateTime'], 'test');

		$control->validate();

		Assert::false($control->isFilled());
		Assert::false($control->hasErrors());
	}

	public function testShortHourSanitizer()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '1978-01-23',
				'time' => '00:00',
			],
		]);

		$control->addRule([$control, 'validateDateTime'], 'test');

		$control->validate();

		Assert::false($control->hasErrors());
	}

	public function testShortHourSanitizerDisabled()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '1978-01-23',
				'time' => '00:00',
			],
		]);

		$control->disableShortHourSanitizer();
		$control->loadHttpData(); // this must be called

		$control->addRule([$control, 'validateDateTime'], 'test');

		$control->validate();

		Assert::true($control->hasErrors());
	}

	/**
	 * @return array[]|array
	 */
	public function dataNonStrictValidDateTimes()
	{
		$data = $this->dataValidDateTimes();
		$data[] = ['1978 - 01 - 23', '12:00', new DateTimeImmutable('1978-01-23 12:00:00')];
		$data[] = ['1978-01-23', '12 : 00', new DateTimeImmutable('1978-01-23 12:00:00')];
		$data[] = ['1978 - 01 - 23', '12 : 00', new DateTimeImmutable('1978-01-23 12:00:00')];
		return $data;
	}

	/**
	 * @dataProvider dataNonStrictValidDateTimes
	 *
	 * @param mixed $date
	 * @param mixed $time
	 * @param DateTimeImmutable|NULL $expected
	 */
	public function testNonStrictLoadHttpDataValid($date, $time, $expected)
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => $date,
				'time' => $time,
			],
		]);

		Assert::equal($expected, $control->getValue());
	}

	public function testLoadHttpDataValidStrictDateTime()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '1978-01-23',
				'time' => '12:00',
			],
		]);
		$control->enableStrict();

		$control->addRule([$control, 'validateDateTime'], 'test');

		Assert::true($control->isFilled());
		Assert::equal(new DateTimeImmutable('1978-01-23 12:00:00'), $control->getValue());

		$control->validate();

		Assert::false($control->hasErrors());
	}

	public function testLoadHttpDataInvalidStrictDate()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '2015 - 02 - 31',
				'time' => '12:00',
			],
		], TRUE);

		$control->addRule([$control, 'validateDateTime'], 'test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(['test'], $control->getErrors());
	}

	public function testLoadHttpDataInvalidStrictTime()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '2015-02-31',
				'time' => '12 : 00',
			],
		], TRUE);

		$control->addRule([$control, 'validateDateTime'], 'test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(['test'], $control->getErrors());
	}

	public function testDefaultTime()
	{
		$control = $this->createControl();
		$control->setDefaultTime(new \DateTimeImmutable('2015-04-20 12:00:00'));

		$dq = \Tester\DomQuery::fromHtml((string) $control->getControlPart(DateTimeInput::NAME_TIME));

		Assert::true($dq->has("input[value='12:00']"));
	}

	public function testInvalidRequired()
	{
		$control = $this->createControl([
			'datetime' => [
				'date' => '2012-02-31',
				'time' => '25:61',
			],
		], TRUE);

		$control->setRequired('test');

		Assert::true($control->isFilled());
		Assert::null($control->getValue());

		$control->validate();

		Assert::true($control->hasErrors());
		Assert::equal(['test'], $control->getErrors());
	}

	/**
	 * @throws \Nette\InvalidArgumentException
	 */
	public function testRequiredInvalidMessage()
	{
		$control = $this->createControl();

		$control->setRequired([]);
	}

	public function testRequired()
	{
		$control = $this->createControl();

		$control->setRequired(TRUE);
		Assert::true($control->isRequired());
	}

	public function testOptional()
	{
		$control = $this->createControl();

		$control->setRequired(FALSE);
		Assert::false($control->isRequired());
	}

	/**
	 * @throws \Nette\InvalidStateException
	 */
	public function testRegistrationMultiple()
	{
		DateTimeInput::register();
		DateTimeInput::register();
	}

	public function testRegistration()
	{
		DateTimeInput::register();

		$form = new \Nette\Forms\Form;
		$control = $form->addDateTime('test', 'Test');
		Assert::type('Nella\Forms\DateTime\DateTimeInput', $control);
		Assert::equal('test', $control->getName());
		Assert::equal('Test', $control->caption);
		Assert::same($form, $control->getForm());
	}

	private function createControl($data = [], $strict = FALSE)
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_FILES = [];
		$_POST = $data;

		$form = new \Nette\Forms\Form;
		$control = new DateTimeInput;
		if ($strict) {
			$control->enableStrict();
		} else {
			$control->disableStrict();
		}
		$form->addComponent($control, 'datetime');

		return $control;
	}

}

id(new DateTimeInputTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
