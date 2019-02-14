<?php
/**
 * Created by Joseph Daigle.
 * Date: 2/3/19
 * Time: 11:59 AM
 */


namespace PapaLocal\ReferralAgreement\Notification;


use PapaLocal\Notification\AbstractNotification;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;


/**
 * AgreementListUpdated.
 *
 * @package PapaLocal\ReferralAgreement\Notification
 */
class AgreementListUpdated extends AbstractNotification
{
    /**
     * @var string
     */
    private $recipient;

    /**
     * AgreementListUpdated constructor.
     *
     * @param string $recipient
     */
    public function __construct(string $recipient, string $agreementName, string $listType, IncludeExcludeList $list)
    {
        $this->recipient = $recipient;

        $this->title = 'Agreement ' . ucfirst($listType) . 's Changed';
        $this->messageTemplate = 'The referral agreement named %s has been changed. The %s list was updated to the following: <br />%s';

        $includes = [];
        foreach ($list->getIncludes() as $include) {
            $item = call_user_func([$include, 'get' . ucfirst($listType)]);
            array_push($includes, $item);
        }

        $listAsString = '<h4>Includes</h4>';
        $listAsString .= implode("<br />", $includes);

        $excludes = [];
        foreach($list->getExcludes() as $exclude) {
            $item = call_user_func([$exclude, 'get' . ucfirst($listType)]);
            array_push($excludes, $item);
        }

        $listAsString .= '<h4>Excludes</h4>';
        $listAsString .= implode("<br />", $excludes);

        $this->messageBodyArgs = [$agreementName, $listType, $listAsString];

        $this->recipient = $recipient;
    }


    /**
     * @inheritDoc
     */
    protected function getConfiguredStrategies(): array
    {
        return [self::STRATEGY_APP];
    }

}