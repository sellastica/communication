<?php
namespace Sellastica\Communication;

interface IResponse
{
	/**
	 * @return int
	 */
	function getStatusCode(): int;

	/**
	 * @return null|string
	 */
	function getDescription(): ?string;

	/**
	 * @return bool
	 */
	function isSuccessfull(): bool;

	/**
	 * @return bool
	 */
	function hasErrors(): bool;

	/**
	 * @return array
	 */
	function getErrors(): array;

	/**
	 * @param string $error
	 * @return
	 */
	function addError(string $error);

	/**
	 * @return string
	 */
	function __toString(): string;
}