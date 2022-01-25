<?php


namespace App\Services;


use Twig\Environment;

class FormBuilderService
{
    public function buildFormFields(array $filters, Environment $twig): array
    {
        $result = [];
        foreach ($filters as $filter => $type) {
            $data = [ 'field' => $filter ];
            switch ($type) {
                case 'datetime':
                    $result[] = $twig->render('datetimeFilter.twig', $data);
                    break;
                case 'int':
                    $result[] = $twig->render('intFilter.twig', $data);
                    break;
                case 'varchar':
                    $result[] = $twig->render('varcharFilter.twig', $data);
                    break;
            }
        }

        return $result;
    }

    public function buildHiddenInput(string $field, string $value, Environment $twig): string
    {
        return $twig->render('hiddenInputFilter.twig', [ 'field' => $field, 'value' => $value ]);
    }
}