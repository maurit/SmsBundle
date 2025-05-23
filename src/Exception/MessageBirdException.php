<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Exception;


class MessageBirdException
	extends \Exception
{
	public function __construct(int $code, string $description, ?string $parameter = null)
	{
		parent::__construct(sprintf('%u: %s. Parameter: %s.', $code, $description, $parameter ?? ''), $code);
	}
}
