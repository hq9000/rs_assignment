<?php


namespace Roadsurfer\Form;


use Roadsurfer\DependencyInjection\EntityManagerAware;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    use EntityManagerAware;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'startStation',
            EntityType::class,
            [
                'class'   => Station::class,
                'choices' => $this->getEntityManager()->getRepository(Station::class)->findAll(),
            ]
        );
        $builder->add(
            'endStation',
            EntityType::class,
            [
                'class'   => Station::class,
                'choices' => $this->getEntityManager()->getRepository(Station::class)->findAll(),
            ]
        );
        $builder->add(
            'startDayCode'
        );
        $builder->add(
            'endDayCode'
        );

        $builder->add(
            'orderEquipmentCounters',
            CollectionType::class,
            [
                'entry_type'   => OrderEquipmentCounterType::class,
                'allow_add'    => true,
                'allow_delete' => true,
            ]
        );

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'      => Order::class,
                'csrf_protection' => false,
            ]
        );
        parent::configureOptions($resolver);
    }

    private function generateStationChoices(): array
    {
        /** @var Station[] $allStations */
        $allStations = $this->getEntityManager()->getRepository(Station::class)->findAll();

        $res = [];
        foreach ($allStations as $station) {
            $res[$station->getName()] = $station->getId();
        }

        return $res;
    }

}