<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/12/18
 * Time: 12:04 PM
 */

namespace PapaLocal\Referral\Notification\Message;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;


/**
 * Class ReferralDisputeNotice
 * @package PapaLocal\Referral\Notification\Message
 */
class ReferralDisputeNotice extends AbstractNotification implements EmailStrategyInterface
{
    /**
     * @var array
     */
    private $templateArgs;

    /**
     * @var string
     */
    protected $title;

    /**
     * ReferralDisputeNotice constructor.
     * @param AssociateFeedItem $associateFeedItem
     */
    public function __construct(AssociateFeedItem $associateFeedItem)
    {
        $this->associateFeedItem = $associateFeedItem;
        $this->templateArgs = array();

        $this->title = sprintf('Dispute initiated.');
        $this->messageTemplate = 'A new referral dispute is waiting to be resolved.';
        $this->messageBodyArgs = array();
    }

    /**
     * @inheritdoc
     */
    protected function getConfiguredStrategies(): array
    {
        return [self::STRATEGY_APP, self::STRATEGY_EMAIL];
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return Ewebify::ADMIN_EMAIL;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return 'emails/referral/referralDisputeNotice.html.twig';
    }

    /**
     * @return array
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}