<?php

namespace App\Controller\Admin;

use App\Classes\MailerHandler;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\Mailer\MailerInterface;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $crudUrlGenerator;
    private $mailer;

    public function __construct(EntityManagerInterface $em, CrudUrlGenerator $crudUrlGenerator, MailerInterface $mailer)
    {
        $this->entityManager = $em;
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->mailer = $mailer;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updateStatementPreparation = Action::new('updateStatementPreparation', 'Preparation en cours', 'fas fa-box-open')->linkToCrudAction('updateStatementPreparation');
        $updateStatementDelivery = Action::new('updateStatementDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateStatementDelivery');
        return $actions
            ->add('index', 'detail')
            ->add('detail', $updateStatementPreparation)
            ->add('detail', $updateStatementDelivery);
    }

    public function updateStatementPreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();

        $order->setState(2);
        $this->entityManager->flush();

        $options = [
            'username' => $order->getUser()->getFullName(),
            'reference' => $order->getReference()
        ];

        $mail = new MailerHandler;
        $mail->sendWithTemplate($this->mailer, $order->getUser()->getEmail(), 'Commande n°'. $order->getReference() .' en cours de préparation', 'email_template/order_preparation.html.twig', $options);

        $this->addFlash('notice', '<span style="color:green;"><strong>La commande n°'. $order->getReference() .' est bien <u>en cours de preparation</u></strong></span>');

        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl()
        ;
        return $this->redirect($url);
    }

    public function updateStatementDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();

        $order->setState(3);
        $this->entityManager->flush();

        $transporteurName = $order->getCarrierName();
        
        switch ($transporteurName) {
            case "Colissio" :
                $transporteur = 'lien-suivi-collissimo';
                break;
            case "Chronoposte" :
                $transporteur = 'lien-suivi-chronopost';
                break;
        }

        $options = [
            'username' => $order->getUser()->getFullName(),
            'reference' => $order->getReference(),
            'carrier_reference' => 'xxxxx-xxxx-xx',
            'site_transporteur' => $transporteur,
            'transporteur' => $order->getCarrierName()
        ];

        $mail = new MailerHandler;
        $mail->sendWithTemplate($this->mailer, $order->getUser()->getEmail(), 'Commande n°'. $order->getReference() ." en cours d'expedition", 'email_template/order_delivery.html.twig', $options);

        $this->addFlash('notice', '<span style="color:green;"><strong>La commande n°'. $order->getReference() .' est bien <u>en cours de livraison</u></strong></span>');

        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl()
        ;
        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Date'),
            TextField::new('user.getFullName', 'Nom'),
            TextEditorField::new('delivery', 'adresse de livraison')->hideOnIndex(),
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payé' => 1,
                'En cours de préparation' => 2,
                'En cours de livraison' => 3,
            ]),
            ArrayField::new('orderDetails', 'produits')->hideOnIndex()
        ];
    }
}
