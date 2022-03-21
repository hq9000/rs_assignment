<?php


namespace Roadsurfer\Form;


use Roadsurfer\DependencyInjection\EntityManagerAware;
use Roadsurfer\Entity\OrderEquipmentCounter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderEquipmentCounterType extends AbstractType
{
    use EntityManagerAware;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'equipmentType',
            ChoiceType::class,
            [
                'choices'  => $this->getEquipmentTypeChoices(),
                'required' => true,
            ]
        );

        $builder->add(
            'count',
            IntegerType::class,
            [
                'required' => true,
            ]
        );

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => OrderEquipmentCounter::class,
            ]
        );
        parent::configureOptions($resolver);
    }

    private function getEquipmentTypeChoices(): array
    {
    }

}