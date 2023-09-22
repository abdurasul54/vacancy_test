<?php

namespace App\Controller\Admin; 

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
// use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

     public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('email', 'email'),
            Field::new('username', 'имя пользователя'),
            Field::new('gender', 'пол'),
            Field::new('password', 'парль'),
            ChoiceField::new('gender', 'пол')->setChoices([
                'мужской' => '1',
                'женский' => '2',
            ]),
        ];

    }
    
}
