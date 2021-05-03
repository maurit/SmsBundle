<?php

namespace Maurit\Bundle\SmsBundle\Provider;


use Maurit\Bundle\SmsBundle\Sms\SmsInterface;


interface ProviderInterface
{
	public function send(SmsInterface $sms);
	public function balance(): float;
	public function check($id): string;
}
