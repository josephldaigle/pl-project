<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/6/17
 * Time: 11:11 AM
 */

namespace Test\Unit\Entity\Validation;

use PapaLocal\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NoteValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: create
     |-----------------------------------
    */

    public function testValidateReturnsNoErrorOnSuccess()
    {
        //set up fixtures
        $note = (new Note())
            ->setNote('This is a dope note');

        //exercise SUT
        $errors = $this->validator->validate($note, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

}