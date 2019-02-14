<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/3/18
 * Time: 1:07 PM
 */


namespace PapaLocal\Core\Security;


use PapaLocal\Core\Security\ValueObject\EmailSalt;
use PapaLocal\Core\Service\ServiceInterface;
use PapaLocal\Core\ValueObject\EmailAddressInterface;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Data\Repository\Person\PersonContactRepository;
use PapaLocal\Entity\Exception\Data\NotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class SecureLinkGenerator
 *
 * Provides generation for url links that can then be sent out publicly, and be traced back to the person they were intended for.
 *
 * This service class does not guarantee that the link will be sent to it's intended recipient.
 *
 * @package PapaLocal\Core\Security
 */
class SecureLinkGenerator implements ServiceInterface
{
	/**
	 * @var SerializerInterface
	 */
	private $serializer;

    /**
     * @var GuidGeneratorInterface
     */
	private $guidGenerator;

	/**
	 * @var EmailSaltRepository
	 */
	private $saltRepository;

	/**
	 * @var PersonContactRepository
	 */
	private $personContactRepository;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * @var Cryptographer
	 */
	private $cryptographer;

    /**
     * SecureLinkGenerator constructor.
     *
     * @param GuidGeneratorInterface                       $guidGenerator
     * @param \PapaLocal\Core\Security\EmailSaltRepository $saltRepository
     * @param SerializerInterface                          $serializer
     * @param RouterInterface                              $router
     * @param Cryptographer                                $cryptographer
     */
	public function __construct(
        GuidGeneratorInterface $guidGenerator,
        EmailSaltRepository $saltRepository,
        SerializerInterface $serializer,
        RouterInterface $router,
        Cryptographer $cryptographer
    )
    {
        $this->guidGenerator           = $guidGenerator;
        $this->saltRepository          = $saltRepository;
        $this->serializer              = $serializer;
        $this->router                  = $router;
        $this->cryptographer           = $cryptographer;
    }

	/**
	 * Creates an encrypted url (incl https prefix) that can be traced back to the recipient's email address.
     *
	 * @param EmailAddressInterface     $recipientEmailAddress   the emailAddress of the intended recipient
	 * @param EmailSaltPurpose          $purpose                 the intended use of the link
	 * @param string                    $urlName                 the end-pont that the link should navigate to
     * @param \DateInterval             $expirationPolicy        the time interval from creation that the salt should expire after
	 * @param array                     $linkParams              any additional named parameters to include in the link (ordering not guaranteed)
     * Each $linkParam is appended to the end of the url ( / separated).
	 *
	 * @return mixed|string
	 * @throws \InvalidArgumentException
	 */
	public function generateSecureLink(EmailAddressInterface $recipientEmailAddress,
	                                   EmailSaltPurpose $purpose,
                                       string $urlName,
	                                   \DateInterval $expirationPolicy,
	                                   array $linkParams = array())
	{
		// create a unique hash to embed in the invitee's email link
		$salt = $this->cryptographer->createSalt();
		$hash = $this->cryptographer->createHash($salt);

        // create a salt VO
        $guid = $this->guidGenerator->generate();
        $emailSalt = $this->serializer->denormalize(array(
		    'id' => array('value' => $guid->value()),
			'emailAddress' => array(
			    'emailAddress' => $recipientEmailAddress->getEmailAddress(),
			    'type' => array('value' => EmailAddressType::PERSONAL()->getValue())
            ),
			'hash' => $hash,
			'purpose' => array('value' => $purpose->getValue()),
            'expirationPolicy' => $expirationPolicy->format('P%yY%mM%dDT%hH%iM%sS')
		), EmailSalt::class, 'array');

		// save the new salt
        // the reason db write was inside this class, was because the
        // url generator needed the id based on sys design.
        // this is no longer the case and can be refactored.
        // TODO: Remove db write operation. Consider service.
        if (in_array($purpose->getValue(),
            [
                EmailSaltPurpose::PURPOSE_FORGOT_PASS()->getValue(),
                EmailSaltPurpose::PURPOSE_RESET_PASS()->getValue(),
            ])) {
                try {
                    $existingSalt = $this->saltRepository->findBy(array(
                        'emailAddress' => $recipientEmailAddress->getEmailAddress(),
                        'purpose' => $purpose->getValue()
                    ));

                    $this->saltRepository->deleteSalt($existingSalt->getId());
                } catch (NotFoundException $exception) {

                }
        }

		$this->saltRepository->save($emailSalt);

		$data = array_merge(array(
			'emailAddress' => $recipientEmailAddress->getEmailAddress(),
			'key' => $salt
        ), $linkParams);

        // generate url
		$url = $this->getUrl($urlName, $data, UrlGeneratorInterface::ABSOLUTE_URL);

		return $url;
	}

    /**
     * Get a string url with https:// prefix.
     *
     * @param string $urlName
     * @param array  $data
     * @param int $refType      defaults to UrlGeneratorInterface::ABSOLUTE_URL
     *
     * @return string
     */
    public function getUrl(string $urlName, array $data = array(), int $refType = UrlGeneratorInterface::ABSOLUTE_URL): string
    {
        $url = preg_replace('/^http:/', 'https:', $this->router->generate($urlName, $data, $refType));

        return $url;
	}
}