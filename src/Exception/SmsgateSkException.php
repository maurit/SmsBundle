<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Exception;


class SmsgateSkException
	extends \Exception
{
	public function __construct(string $text)
	{
		parent::__construct($text);
	}
}
