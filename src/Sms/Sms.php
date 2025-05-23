<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Sms;


class Sms
	implements SmsInterface
{
	private string $phoneNumber;
	private string $message;
	private \DateTime $dateTime;
	private string $sender;


	public function __construct(string $phoneNumber, string $message, ?\DateTime $dateTime = null, ?string $sender = '')
	{
		$this->setPhoneNumber($phoneNumber);
		$this->setMessage($message);
		$this->setDateTime(($dateTime) ?? new \DateTime);
		$this->setSender($sender ?? '');
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

	public function getSender(): string
	{
		return $this->sender;
	}

	public function setSender(string $sender): self
	{
		$this->sender = $sender;
		return $this;
	}
}
