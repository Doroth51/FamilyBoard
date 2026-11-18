<?php

namespace App\Controller\Admin;

use App\Entity\Enfant;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EnfantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Enfant::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('periodeType'),
            TextField::new('evaluationMode'),
        ];
    }
}
