<?php

namespace Maurit\Bundle\SmsBundle\Tests\Command;


use Maurit\Bundle\SmsBundle\Command\TestDeliveryCommand;
use Maurit\Bundle\SmsBundle\Service\ProviderManager;
use Maurit\Bundle\SmsBundle\Tests\Fixture\Provider\ProviderFixture;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;


class TestDeliveryCommandTest
	extends TestCase
{
	public function testExecute(): void
	{
		$app = new Application;
		$pm = new ProviderManager;
		$providerName = 'test-provider';

		$pm->addProvider($providerName, ProviderFixture::getProvider());
		$app->add(new TestDeliveryCommand($pm));

		$command = $app->find('maurit:sms:delivery:test');
		$commandTester = new CommandTester($command);
		$commandTester->execute([
			'command' => $command->getName(),
			'provider-name' => $providerName,
			'phone-number' => '+123456789',
			'message' => 'Hello World!',
		]);

		$this->assertRegExp('/[OK]/', $commandTester->getDisplay());
	}
}
