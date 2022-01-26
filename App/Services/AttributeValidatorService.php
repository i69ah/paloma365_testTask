<?php


namespace App\Services;


class AttributeValidatorService
{
    public function validatePostAttributes(array $filtersAndTypes, array $postData): bool
    {
        $result = true;
        foreach ($filtersAndTypes as $filter => $typeAndCommentRaw) {
            $typeAndComment = $typeAndCommentRaw[0];
            if ('datetime' === $typeAndComment['type']) {
                $result &= isset($postData[$filter]['begin'], $postData[$filter]['end']);
            } else {
                $result &= isset($postData[$filter]);
            }
        }

        return $result;
    }

    public function validateDateFormat(array $filtersAndTypes, array $postData): bool
    {
        foreach ($filtersAndTypes as $filter => $typeAndCommentRaw) {
            $typeAndComment = $typeAndCommentRaw[0];
            if ('datetime' === $typeAndComment['type']) {
                $dateBegin = \DateTimeImmutable::createFromFormat('d.m.Y H:i',$postData[$filter]['begin']);
                $dateEnd = \DateTimeImmutable::createFromFormat('d.m.Y H:i', $postData[$filter]['end']);

                return $dateBegin instanceof \DateTimeInterface && $dateEnd instanceof \DateTimeInterface;
            }
        }

        return true;
    }
}