<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/16/19
 * Time: 4:18 PM
 */

namespace PapaLocal\Billing\Notification\Message;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;


/**
 * Class PayoutSuccess
 * @package PapaLocal\Billing\Notification\Message
 */
class PayoutSuccess extends AbstractNotification implements EmailStrategyInterface
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
     * PayoutSuccess constructor.
     * @param float $payoutAmount
     * @param float $availableBalance
     * @param string $recipient
     * @param array $templateArgs
     */
    public function __construct(float $payoutAmount, float $availableBalance, string $recipient, array $templateArgs)
    {
        // set the title and message
        $this->title = 'A payout was successfully processed.';
        $this->messageTemplate = 'Payout of $%0.2f was added to your account. Your account balance is $%0.2f.';

        // set message body args
        $this->messageBodyArgs = array($payoutAmount, $availableBalance);

        // set email config settings
        $this->recipient = $recipient;
        $this->templateArgs = $templateArgs;
        $this->subject = sprintf('A payout has been made to your %s account. Check out your new balance.', Ewebify::APP_NAME);
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
        return 'emails/billing/payoutSuccess.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}