<?php
/**
 * Created by PhpStorm.
 * User: Konrad
 * Date: 20.11.2016
 * Time: 11:39
 */

namespace AppBundle\Controller\admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Config\Definition\Exception\Exception;

class VideoController extends Controller {

    public function indexAction() {
        $video = $this->getDoctrine()->getRepository("AppBundle:Videos");
        $video->findAll();
        var_dump($video);
        return new Response("test");
    }
}