<?php
namespace Sellastica\Communication;

class Response implements IResponse
{
	const IGNORED = -1,
		SKIPPED = 0,
		UPDATED = 200,
		CREATED = 201,
		REMOVED = 204,
		BAD_REQUEST = 400,
		INVALID_CREDENTIALS = 401,
		NOT_FOUND = 404,
		UNPROCESSABLE_ENTITY = 422,
		INTERNAL_SERVER_ERROR = 500;

	/** @var int */
	private $statusCode = 200;
	/** @var string|null */
	private $description;
	/** @var array */
	private $errors = [];
	/** @var \DateTime Response creation time */
	private $timestamp;


	/**
	 * @param int $statusCode
	 */
	public function __construct(int $statusCode = 200)
	{
		$this->statusCode = $statusCode;
		$this->timestamp = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	/**
	 * @param int $statusCode
	 * @return $this
	 */
	public function setStatusCode(int $statusCode)
	{
		$this->statusCode = $statusCode;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getTimestamp(): \DateTime
	{
		return $this->timestamp;
	}

	/**
	 * @return bool
	 */
	public function isSuccessfull(): bool
	{
		return $this->statusCode < 400 && !$this->hasErrors();
	}

	/**
	 * @return null|string
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param null|string $description
	 */
	public function setDescription(?string $description)
	{
		$this->description = $description;
	}

	/**
	 * @return bool
	 */
	public function hasErrors(): bool
	{
		return !empty($this->errors);
	}

	/**
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}

	/**
	 * @return string
	 */
	public function getErrorsAsString(): string
	{
		return implode("\n", $this->errors);
	}

	/**
	 * @param string $error
	 */
	public function addError(string $error)
	{
		$this->errors[] = $error;
	}

	/**
	 * @return string|null
	 */
	public function getFirstError(): ?string
	{
		return $this->errors[0] ?? null;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return implode("\n", $this->errors);
	}

	/**
	 * @param string|null $error
	 * @param int $code
	 * @return IResponse|mixed
	 */
	public static function error(
		string $error = null,
		int $code = 400
	): IResponse
	{
		$response = new static($code);
		if (isset($error)) {
			$response->addError($error);
		}

		return $response;
	}

	/**
	 * @return Response
	 */
	public static function ignored(): Response
	{
		return new static(self::IGNORED);
	}

	/**
	 * @param string|null $description
	 * @return Response
	 */
	public static function skipped(string $description = null): Response
	{
		$response = new static(self::SKIPPED);
		$response->setDescription($description);
		return $response;
	}

	/**
	 * @param int $code
	 * @return static
	 */
	public static function success(int $code = 200): Response
	{
		return new static($code);
	}

	/**
	 * @return static
	 */
	public static function modified(): Response
	{
		return new static(200);
	}

	/**
	 * @return static
	 */
	public static function created(): Response
	{
		return new static(201);
	}

	/**
	 * @return Response
	 */
	public static function removed(): Response
	{
		return new static(self::REMOVED);
	}

	/**
	 * @param string|null $error
	 * @return IResponse
	 */
	public static function badRequest(string $error = null): IResponse
	{
		return static::error($error, self::BAD_REQUEST);
	}

	/**
	 * @param string|null $error
	 * @return IResponse
	 */
	public static function notFound(string $error = null): IResponse
	{
		return static::error($error, self::NOT_FOUND);
	}

	/**
	 * @param string|null $error
	 * @return IResponse
	 */
	public static function unprocessableEntity(string $error = null): IResponse
	{
		return static::error($error, self::UNPROCESSABLE_ENTITY);
	}

	/**
	 * @param string|null $error
	 * @return IResponse
	 */
	public static function internalServerError(string $error = null): IResponse
	{
		return static::error($error, self::INTERNAL_SERVER_ERROR);
	}

	/**
	 * @param \Exception $exception
	 * @return static
	 */
	public static function fromException(\Exception $exception)
	{
		return static::error($exception->getMessage(), $exception->getCode());
	}

	/**
	 * @param IResponse $response
	 * @return static|IResponse
	 */
	public static function fromResponse(IResponse $response): IResponse
	{
		$newResponse = new static($response->getStatusCode());
		foreach ($response->getErrors() as $error) {
			$newResponse->addError($error);
		}

		$newResponse->setDescription($response->getDescription());
		return $newResponse;
	}
}