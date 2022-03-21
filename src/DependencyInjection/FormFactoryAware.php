<?php


namespace Roadsurfer\DependencyInjection;


use Symfony\Component\Form\FormFactoryInterface;

trait FormFactoryAware
{
    private FormFactoryInterface $formFactory;

    /**
     * @return FormFactoryInterface
     */
    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

}