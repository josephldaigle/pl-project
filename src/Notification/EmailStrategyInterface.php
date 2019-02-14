<?php
/**
 * Created by Joseph Daigle.
 * Date: 5/10/18
 * Time: 12:02 PM
 */


namespace PapaLocal\Notification;


/**
 * Interface EmailStrategyInterface.
 *
 * Describe a Notification that is sent by email.
 *
 * @package PapaLocal\Notification
 */
interface EmailStrategyInterface
{
	/**
	 * Get the email address of the intended recipient.
	 * @return string
	 */
	public function getRecipient(): string;

	/**
	 * Get the subject of th email.
	 * @return string
	 */
	public function getSubject(): string;

	/**
	 * Get the name of the Twig template used to generate the email.
	 * @return string
	 */
	public function getTemplateName(): string;

	/**
	 * Fetch the email's template arguments.
	 *
	 * @return array
	 */
	public function getTemplateArgs(): array;
}