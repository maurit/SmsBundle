<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Sms;


class Sms
	implements SmsInterface
{
	/** @var string */
	private $phoneNumber;
	/** @var string */
	private $message;
	/** @var \DateTime */
	private $dateTime;


	public function __construct(string $phoneNumber, string $message, \DateTime $dateTime = null)
	{
		$this->setPhoneNumber($phoneNumber);
		$this->setMessage($message);
		$this->setDateTime(($dateTime) ?? new \DateTime);
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function setMessage(string $message): self
	{
		$this->message = $message;

		return $this;
	}

	public function getPhoneNumber(): string
	{
		return $this->phoneNumber;
	}

	public function setPhoneNumber(string $phoneNumber): self
	{
		$this->phoneNumber = $phoneNumber;

		return $this;
	}

	public function getDateTime(): \DateTime
	{
		return $this->dateTime;
	}

	public function setDateTime(\DateTime $dateTime): self
	{
		$this->dateTime = $dateTime;

		return $this;
	}
}
