<?php


namespace Roadsurfer\Form;


use Roadsurfer\DependencyInjection\EntityManagerAware;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\OrderEquipmentCounter;
use Roadsurfer\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            EntityType::class,
            [
                'class'    => EquipmentType::class,
                'choices'  => $this->getEntityManager()->getRepository(EquipmentType::class)->findAll(),
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
        /** @var Station[] $allEquipmentTypes */
        $allEquipmentTypes = $this->getEntityManager()->getRepository(EquipmentType::class)->findAll();

        $res = [];
        foreach ($allEquipmentTypes as $type) {
            $res[$type->getName()] = $type->getId();
        }

        return $res;
    }

}