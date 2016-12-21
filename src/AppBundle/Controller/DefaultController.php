<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PlaylistVideo;
use AppBundle\Entity\Videos;
use AppBundle\YoutubeApi\YoutubeApi;
use AppBundle\YoutubeApi\YoutubeApiConf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{

    /**
     * @Route("/video/{id}", name="getVideoById")
     */

    public function getVideoById($id) {
        $video = $this->getDoctrine()
            ->getRepository("AppBundle:Videos")
            ->findOneById($id);

        if (!$video) {
            throw $this->createNotFoundException(
                'No video found for id '.$video
            );
        }

        return $this->render("AppBundle:site:video.html.twig", array(
            "video" => $video
        ));
    }

    /**
     * @Route("/", name="home")
     */

    public function homeAction() {
        return new Response("test");
    }

    /**
     * @Route("/playlista/{id}", name="getVideoFromPlaylist")
     */
    public function getVideoFromPlaylist($id) {
        $playlist = $this->getDoctrine()->getRepository("AppBundle:Playlist")->find($id);
        if (!$playlist) {
            throw $this->createNotFoundException(
                'No playlist found for id '.$id
            );
        }

        /* @var PlaylistVideo $videoFromPlaylist */
        $videoFromPlaylist = $this->getDoctrine()->getRepository("AppBundle:PlaylistVideo")->findBy(array("playlist" => $id),array("positionInPlaylist" => "ASC"),100);

        return $this->render("AppBundle:site:base.html.twig", array(
            "videos" => $videoFromPlaylist,
            "playlist" => $playlist
        ));
    }

    
}
