<?php
/**
 * Created by PhpStorm.
 * User: Konrad
 * Date: 07.11.2016
 * Time: 18:32
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsYoutubePlaylist extends Constraint {
    public $message = 'Playlista o podanym ID nie istnieje';
}