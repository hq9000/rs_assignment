<?php


namespace Roadsurfer\Util;


use Symfony\Component\Form\FormInterface;

class FormErrorPresenterUtil
{
    public static function presentErrors(FormInterface $form): array
    {
        $errors = $form->getErrors(deep: true, flatten: true);

        $res    = [];
        foreach ($errors as $error) {
            $path = $error->getCause()->getPropertyPath();
            if (!$path || 'data' == $path) {
                $path = "root";
            }
            $res[] = $path . ':' . $error->getMessage();
        }

        return $res;
    }
}