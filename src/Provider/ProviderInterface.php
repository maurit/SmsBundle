<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Provider;

use Maurit\Bundle\SmsBundle\Sms\SmsInterface;


interface ProviderInterface
{
	public function send(SmsInterface $sms): string|int;
	public function balance(): float;
	public function check($id): string;
}
