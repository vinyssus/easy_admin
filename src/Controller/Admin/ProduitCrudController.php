<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class ProduitCrudController extends AbstractCrudController
{

    public const ACTION_DUPLICATE = 'duplicate';
    public const PRODUITS_BASE_PATH = 'upload/images/produits';
    public const PRODUITS_UPLOAD_DIR = 'public/upload/images/produits';

    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureActions(Actions $actions): Actions
    {
      $duplicate = Action::new(self::ACTION_DUPLICATE)
      ->linkToCrudAction('duplicateProduit')
      ->setCssClass('btn btn-info');
      return $actions
        ->add(Crud::PAGE_EDIT,$duplicate)
        ->reorder(Crud::PAGE_EDIT,[self::ACTION_DUPLICATE,Action::SAVE_AND_RETURN]);
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextEditorField::new('description'),
            MoneyField::new('price')->setCurrency('EUR'),
            AssociationField::new('category')->setQueryBuilder(function (QueryBuilder $queryBuilder){
                $queryBuilder->where('entity.active = true');
            }),
            ImageField::new('image')
            ->setBasePath(self::PRODUITS_BASE_PATH)
            ->setUploadDir(self::PRODUITS_UPLOAD_DIR)
            ->setSortable(false),
            BooleanField::new('active'),
            DateTimeField::new('updateAt')->hideOnForm(),
            DateTimeField::new('createAt')->hideOnForm(),
        ];
    }

    // public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    // {
    //     if (!$entityInstance instanceof Produit) return;
    //     $entityInstance->setCreateAt(new \DateTimeImmutable);
    //     parent::persistEntity($entityManager,$entityInstance);
    // }

    // public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    // {
    //     if (!$entityInstance instanceof Produit) return;
    //     $entityInstance->setUpdateAt(new \DateTimeImmutable);
    //     parent::persistEntity($entityManager,$entityInstance);
    // }

    public function duplicateProduit(EntityManagerInterface $entityManager, AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
      /** @var Produit $produit */
      $produit = $context->getEntity()->getInstance();
      
    $duplicateProduit = clone $produit;

    parent::persistEntity($entityManager,$duplicateProduit);

    $url = $adminUrlGenerator->setController(self::class)
    ->setAction(Action::DETAIL)
    ->setEntityId($duplicateProduit->getId())
    ->generateUrl();
    return $this->redirect($url);
    }
}
