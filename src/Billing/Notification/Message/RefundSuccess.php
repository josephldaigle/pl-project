<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/21/19
 * Time: 7:56 PM
 */

namespace PapaLocal\Billing\Notification\Message;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;


/**
 * Class RefundSuccess
 * @package PapaLocal\Billing\Notification\Message
 */
class RefundSuccess extends AbstractNotification implements EmailStrategyInterface
{
    /**
     * @var string
     */
    private $recipient;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var array
     */
    private $templateArgs;

    /**
     * RefundSuccess constructor.
     * @param float $payoutAmount
     * @param string $recipient
     * @param array $templateArgs
     */
    public function __construct(float $payoutAmount, string $recipient, array $templateArgs)
    {
        // set the title and message
        $this->title = 'A refund was successfully processed.';
        $this->messageTemplate = 'Transaction of $%0.2f was successful.';

        // set message body args
        $this->messageBodyArgs = array($payoutAmount);

        // set email config settings
        $this->recipient = $recipient;
        $this->templateArgs = $templateArgs;
        $this->subject = sprintf('A transaction has been made to your %s account. Check out your new balance.', Ewebify::APP_NAME);
    }

    /**
     * @param AssociateFeedItem $associateFeedItem
     */
    public function setAssociateFeedItem(AssociateFeedItem $associateFeedItem)
    {
        $this->associateFeedItem = $associateFeedItem;
    }

    /**
     * @inheritdoc
     */
    protected function getConfiguredStrategies(): array
    {
        return array(self::STRATEGY_APP, self::STRATEGY_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @inheritdoc
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @inheritdoc
     */
    public function getTemplateName(): string
    {
        return 'emails/billing/refundSuccess.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}