<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/4/19
 */


namespace PapaLocal\Notification\Form;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class MarkReadForm.
 *
 * Used to request that a notification be marked as 'read' by a user.
 *
 * @package PapaLocal\Notification\Form
 */
class MarkReadForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Description cannot be blank."
     *     )
     */
    private $notificationGuid;

    /**
     * MarkReadForm constructor.
     *
     * @param string $notificationGuid
     */
    public function __construct(string $notificationGuid = '')
    {
        $this->notificationGuid = $notificationGuid;
    }

    /**
     * @return mixed
     */
    public function getNotificationGuid()
    {
        return $this->notificationGuid;
    }
}