<?php
/**
 * Created by PhpStorm.
 * User: Konrad
 * Date: 07.11.2016
 * Time: 18:35
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\YoutubeApi\YoutubeApi;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsYoutubePlaylistValidator extends ConstraintValidator{

    public function validate($value, Constraint $constraint)
    {
        if($value != NULL) {
            $playlist = new YoutubeApi($value);
            if (!$playlist->checkPlaylistIsset()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%string%', $value)
                    ->addViolation();
            }
        }

    }

}