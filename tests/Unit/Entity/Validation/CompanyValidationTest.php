<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 11/30/17
 * Time: 1:42 PM
 */

namespace Test\Unit\Entity\Validation;

use PapaLocal\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CompanyValidationTest.
 *
 * @package Test\Unit\Entity\Validation
 */
class CompanyValidationTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');
    }

    /*
     |-----------------------------------
     | DATA PROVIDERS
     |-----------------------------------
    */
    /**
     * Provide a list of dates that are expected to be valid.
     *
     * @return array
     */
    public function dateFoundedBlackListProvider()
    {
        return [
            ['1899', 'Date provided is out of range'],
            ['2200', 'Date provided is out of range'],
        ];
    }

    /**
     * Provides a list of dates that are expected to be valid.
     *
     * @return array
     */
    public function dateFoundedWhiteListProvider()
    {
        return [
            ['1900'],
            ['2199'],
        ];
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: create
     |-----------------------------------
    */

    public function testValidateReturnsNoErrorOnSuccessOnCreate()
    {
        //set up fixtures
        $company = (new Company())
            ->setName('Marvels LLC')
            ->setDateFounded('1996');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenIdIsNotBlankOnCreate()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setName('Marvels LLC')
            ->setDateFounded('1996');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Id must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenNameIsBlankOnCreate()
    {
        //set up fixtures
        $company = (new Company())
            ->setName('')
            ->setDateFounded('1996');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnCreate()
    {
        //set up fixtures
        $company = (new Company())
            ->setName('Marvels LLC')
            ->setDateFounded('1996')
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenTimeUpdatedIsNotBlankOnCreate()
    {
        $company = (new Company())
            ->setName('Marvels LLC')
            ->setDateFounded('1900')
            ->setTimeUpdated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time updated must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    /**
     * @dataProvider dateFoundedWhiteListProvider
     */
    public function testValidateReturnsNoErrorsWhenPasswordIsValidOnCreate($dateFounded)
    {
        //set up fixtures
        $company = (new Company())
            ->setName('Marvels LLC')
            ->setDateFounded($dateFounded);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('create'));

        //assertions
        $this->assertEmpty($errors);
    }

    public function testValidateReturnsCorrectErrorWhenStatusIsNotValidOnCreate()
    {
        //set up fixtures
        $company = (new Company())
            ->setName('Marvels LLC')
            ->setStatus('Invalid Status');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(),
            'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('Invalid value supplied for status field.', $errors->get(0)->getMessage(),
            'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: update
     |-----------------------------------
    */
    public function testValidateReturnsNoErrorOnSuccessOnUpdate()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setName('Marvels LLC')
            ->setDateFounded('1996');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('update'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsCorrectErrorWhenIdNotPresentOnUpdate()
    {
        //set up fixtures
        $company = (new Company())
            ->setName('Marvels LLC')
            ->setDateFounded('1996');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Id must be present.', $errors[0]->getMessage(),'expected message not found');
    }


    public function testValidateReturnsNoErrorWhenOnlyNameIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setName('Marvels LLC');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('update'));

        //assertions
        $this->assertEmpty($errors);

    }

    public function testValidateReturnsNoErrorWhenOnlyDateFoundedIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setDateFounded('1996');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('update'));

        //assertions

        $this->assertEmpty($errors);
    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnUpdate()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenTimeUpdatedIsNotBlankOnUpdate()
    {
        $company = (new Company())
            ->setId(3)
            ->setTimeUpdated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time updated must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    /**
     * @dataProvider dateFoundedWhiteListProvider
     */
    public function testValidateReturnsNoErrorsWhenDateFoundedIsValidOnUpdate($dateFounded)
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setDateFounded($dateFounded);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('update'));

        //assertions
        $this->assertEmpty($errors);
    }

    /**
     |--------------------------------------
     | VALIDATION GROUP: save_founded_date
     |--------------------------------------
    */
    public function testValidateReturnsCorrectErrorWhenDateFoundedIsBlankOnSaveFoundedDate()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_founded_date'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Date founded cannot be blank.', $errors[0]->getMessage(),
            'expected message not found');
    }

    /**
     * @dataProvider dateFoundedBlackListProvider
     */
    public function testValidateReturnsCorrectErrorWhenDateFoundedIsOutOfBoundsOnSaveFoundedDate($dateFounded, $errorMessage)
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setDateFounded($dateFounded);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_founded_date'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(), 'expected message not found');
    }

    /**
     * @dataProvider dateFoundedWhiteListProvider
     */
    public function testValidateReturnsNoErrorWhenDateFoundedIsValidOnSaveFoundedDate($dateFounded)
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setDateFounded($dateFounded);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_founded_date'));

        // make assertions
        $this->assertEmpty($errors);
    }

    /**
     |--------------------------------------
     | VALIDATION GROUP: save_about
     |--------------------------------------
    */
    /**
     * Provide a list of dates that are expected to be valid.
     *
     * @return array
     */
    public function aboutBlackListProvider()
    {
        return [
            ['too long', 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk'
                . 'lmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz'
                . 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopa',
                'Description (about) is too long.'],
            ['too short', 'Notenough', 'Description (about) is too short.'],
        ];
    }

    /**
     * Provides a list of dates that are expected to be valid.
     *
     * @return array
     */
    public function aboutWhiteListProvider()
    {
        return [
            ['This is an ideal description for a company. Not too long, and not too short.']
        ];
    }

    public function testValidateReturnsCorrectErrorsWhenAboutIsBlankOnSaveAbout()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_about'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Description (about) cannot be blank.', $errors[0]->getMessage(),
            'expected message not found');
    }

    /**
     * @dataProvider aboutBlackListProvider
     */
    public function testValidateReturnsCorrectErrorsWhenAboutIsNotValid($testCase, $about, $errorMessage)
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setAbout($about);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_about'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exist');
        $this->assertSame($errorMessage, $errors[0]->getMessage(), 'expected message not found');
    }

    /**
     * @dataProvider aboutWhiteListProvider
     */
    public function testValidateReturnsNoErrorWhenAboutIsValidOnSaveAbout($about)
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setAbout($about);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_about'));

        // make assertions
        $this->assertEmpty($errors);
    }

    /**
    |--------------------------------------
    | VALIDATION GROUP: save_website
    |--------------------------------------
     */
    /**
     * Provide a list of dates that are expected to be valid.
     *
     * @return array
     */
    public function websiteBlackListProvider()
    {
        return [
            ['too long', 'http://www.lmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk'
                . 'lmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz'
                . 'abcdefghijklmno.com',
                'Website cannot contain more than 200 characters.'],
            ['too short', 'http://www.a.d', 'The website provided is not in an acceptable format. Please include a suffix(.com, .org, .biz).'],
            ['missing www prefix', 'http://papalocal.com', 'The website provided is not in an acceptable format. Please include the www prefix.'],
            ['missing protocol', 'www.papalocal.com', 'The website provided is not in an acceptable format.'],
            ['missing domain extension', 'http://www.papalocal', 'The website provided is not in an acceptable format. Please include a suffix(.com, .org, .biz).']
        ];
    }

    /**
     * Provides a list of dates that are expected to be valid.
     *
     * @return array
     */
    public function websiteWhiteListProvider()
    {
        return [
            ['https', 'https://www.papalocal.com'],
            ['http', 'http://www.papalocal.com'],
            ['foreign domain extension', 'http://www.papalocal.se']
        ];
    }

    public function testValidateReturnsCorrectErrorsWhenWebsiteIsBlankOnSaveAbout()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_website'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Website cannot be blank.', $errors[0]->getMessage(),
            'expected message not found');
    }

    /**
     * @dataProvider websiteBlackListProvider
     */
    public function testValidateReturnsCorrectErrorsWhenWebsiteIsNotValid($testCase, $website, $errorMessage)
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setWebsite($website);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_website'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exist');
        $this->assertSame($errorMessage, $errors[0]->getMessage(), 'expected message not found');
    }

    /**
     * @dataProvider websiteWhiteListProvider
     */
    public function testValidateReturnsNoErrorWhenWebsiteIsValidOnSaveWebsite($testCase, $website)
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setWebsite($website);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_website'));

        // make assertions
        $this->assertEmpty($errors, 'unexpected validation errors exist');
    }

    /**
    |--------------------------------------
    | VALIDATION GROUP: save_website
    |--------------------------------------
    */

    public function testValidateReturnsCorrectErrorWhenStatusIsNotValidOnSaveStatus()
    {
        //set up fixtures
        $company = (new Company())
            ->setName('Marvels LLC')
            ->setStatus('Invalid Status');

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_status'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(),
            'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('Invalid value supplied for status field.', $errors->get(0)->getMessage(),
            'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    public function testValidateReturnsNoErrorWhenStatusIsValidOnSaveStatus()
    {
        //set up fixtures
        $company = (new Company())
            ->setId(3)
            ->setStatus(Company::STATUS_DEACTIVATED);

        //exercise SUT
        $errors = $this->validator->validate($company, null, array('save_status'));

        // make assertions
        $this->assertEmpty($errors, $errors->__toString());
    }
}