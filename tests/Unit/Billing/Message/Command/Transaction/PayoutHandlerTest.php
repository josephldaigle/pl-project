<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/18/19
 * Time: 2:51 PM
 */

namespace Test\Unit\Entity\Billing\Message\Command\Transaction;


use PapaLocal\Billing\Data\MessageFactory;
use PapaLocal\Billing\Data\Query\FindByUserGuid;
use PapaLocal\Billing\Data\TransactionRepository;
use PapaLocal\Billing\Form\WithdrawFunds;
use PapaLocal\Billing\Message\Command\Transaction\Payout;
use PapaLocal\Billing\Message\Command\Transaction\PayoutHandler;
use PapaLocal\Billing\Notification\NotificationFactory;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Notification\Notifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class PayoutHandlerTest
 * @package Test\Unit\Entity\Billing\Message\Command\Transaction
 */
class PayoutHandlerTest extends TestCase
{
    public function testPayoutHandlerIsSuccess()
    {
        $this->markTestIncomplete();

        $guid = 'f649d85a-dfe7-46a4-a984-52490129a427';

        $guidFactory = $this->createMock(GuidGeneratorInterface::class);
        $transactionRepository = $this->createMock(TransactionRepository::class);

        $userGuidMock = $this->createMock(Guid::class);
        $userGuidMock->expects($this->once())
            ->method('value')
            ->willReturn($guid);

        $queryMock = $this->createMock(FindByUserGuid::class);

        dump($guid);

        $messageFactory = $this->createMock(MessageFactory::class);
        $messageFactory->expects($this->once())
            ->method('newFindByUserGuid')
            ->with($guid)
            ->willReturn($queryMock);

        $mysqlBus = $this->createMock(MessageBusInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $notificationFactory = $this->createMock(NotificationFactory::class);
        $notifier = $this->createMock(Notifier::class);

        $WithdrawFundsFormMock = $this->createMock(WithdrawFunds::class);
        $WithdrawFundsFormMock->expects($this->once())
            ->method('getUserGuid')
            ->willReturn($userGuidMock);

        $commandMock = $this->createMock(Payout::class);
        $commandMock->expects($this->once())
            ->method('getForm')
            ->willReturn($WithdrawFundsFormMock);

        // Exercise SUT
        $handler = new PayoutHandler(
            $guidFactory,
            $transactionRepository,
            $messageFactory,
            $mysqlBus,
            $serializer,
            $notificationFactory,
            $notifier
        );
        $handler->__invoke($commandMock);
    }
}