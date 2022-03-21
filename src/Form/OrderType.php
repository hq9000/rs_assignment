<?php


namespace Roadsurfer\Form;


use Roadsurfer\DependencyInjection\EntityManagerAware;
use Roadsurfer\Entity\Order;
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
            ChoiceType::class,
            [
                'choices' => $this->generateStationChoices(),
                'required' => true
            ]
        );
        $builder->add(
            'endStation',
            ChoiceType::class,
            [
                'choices' => $this->generateStationChoices(),
                'required' => true
            ]
        );
        $builder->add(
            'startDayCode',
            ChoiceType::class,
            [
                'choices' => $this->generateStationChoices(),
                'required' => true
            ]
        );
        $builder->add(
            'endDayCode',
            ChoiceType::class,
            [
                'choices' => $this->generateStationChoices(),
                'required' => true
            ]
        );

        $builder->add(
            'orderEquipmentCounters',
            CollectionType::class,
            [
                'entry_type' => OrderEquipmentCounterType::class
            ]
        );

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class
        ]);
        parent::configureOptions($resolver);
    }

    private function generateStationChoices(): array
    {
    }

}