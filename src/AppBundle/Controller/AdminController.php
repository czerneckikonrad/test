<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Playlist;
use AppBundle\Entity\PlaylistVideo;
use AppBundle\Entity\Videos;
use AppBundle\Form\PlaylistType;
use AppBundle\Form\VideoEditType;
use AppBundle\Form\VideoType;
use AppBundle\YoutubeApi\YoutubeApi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{

    /**
     * @Route("/panel", name="panel")
     */
    public function panelAction(Request $request)
    {

        return $this->render('AppBundle:admin:base.html.twig');

    }

    /**
     * @Route("/panel/playlist/{id}", name="playlistEdit")
     */
    public function editPlaylistAction(Playlist $playlist = null,Request $request)
    {
        if (!$playlist) {
            throw $this->createNotFoundException('Nie znaleziono playlisty');
        }

        $form = $this->createForm(PlaylistType::class,$playlist);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($playlist);
            $em->flush();
            $this->addFlash('success', 'Playlista została zaktualizowana.');
            return $this->redirect($request->getUri());
        }

        return $this->render('AppBundle:admin/playlist:edit.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/panel/playlist", name="playlist")
     */
    public function addPlaylistAction(Request $request)
    {
       $allPlaylist = $this->getDoctrine()->getRepository("AppBundle:Playlist")->findAll();

        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class,$playlist);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($playlist);
            $em->flush();
            $this->addFlash('success', 'Playlista została dodana.');
            return $this->redirect($request->getUri());
        }

        return $this->render('AppBundle:admin:addplaylist.html.twig', array(
            'form' => $form->createView(),
            'playlists' => $allPlaylist
        ));

    }

    /**
     * @Route("/panel/video/new", name="videoNew")
     */
    public function videoAction(Request $request)
    {
        $video = new Videos();
        $form = $this->createForm(VideoType::class,$video);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $video->setDateadded(new \DateTime("now"));
            $em->persist($video);
            $em->flush();
            foreach($form->get("playlist")->getData() as $plst) {
                $playlsit_video = new PlaylistVideo();
                $playlsit_video->setPlaylist($plst);
                $playlsit_video->setVideo($video);
                $playlsit_video->setDateAdded(new \DateTime("now"));
                $em->persist($playlsit_video);
                $em->flush();
            }
            $this->addFlash('success', 'Video zostało dodane.');
            return $this->redirect($request->getUri());
        }

        return $this->render('AppBundle:admin/video:add.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/panel/video/edit/{id}", name="videoEdit")
     */
    public function videoEditAction($id, Request $request)
    {
        $video = $this->getDoctrine()->getRepository("AppBundle:Videos")->find($id);

        if(!$video) {
            throw $this->createNotFoundException("Video o podanym ID nie istnieje");
        }

        $playlsit_video = $this->getDoctrine()->getRepository("AppBundle:PlaylistVideo")->findByVideo($video);

        $currentPlaylist = array();
        foreach($playlsit_video as $pls) {
            $currentPlaylist[] = $pls->getPlaylist()->getId();//zbieram aktualne playlisty przypisane do video
        }

        $form = $this->createForm(VideoEditType::class,$video);
        foreach($form->get('playlist')->all() as $playlist) {
            if(in_array((int)$playlist->getConfig()->getName(),$currentPlaylist)) {
                $playlist->setData(true);//zaznaczam checkboxy z aktualnie przypisanymi playlistami zanim zostanie wygenerowany form
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $video->setDateadded(new \DateTime("now"));
            $em->persist($video);
            $em->flush();
            $checkedPlaylist = array();
            foreach($form->get("playlist")->getData() as $plst) {
                $checkedPlaylist[] = $plst->getId();//zbieram aktualnie zaznaczone playlisty po wyslaniu forma
            }

            foreach($currentPlaylist as $beforePlaylist) {
                if(!in_array($beforePlaylist,$checkedPlaylist)) {
                    $removePlaylistVideo = $this->getDoctrine()->getRepository("AppBundle:PlaylistVideo")->findBy(array(
                        'video' => $video,
                        'playlist'=> $beforePlaylist
                    ));

                    foreach ($removePlaylistVideo as $removePlaylistVideo) {
                        $em->remove($removePlaylistVideo);//usuwam playlisty ktore zostaly odznaczone
                    }
                    $em->flush();
                }
            }
            foreach($form->get("playlist")->getData() as $plst) {
                $getplaylist = $this->getDoctrine()->getRepository("AppBundle:PlaylistVideo")->findBy(array(
                   'video' => $video,
                    'playlist'=> $plst
                ));
                if($getplaylist) { continue; }//juz jest ta playlista -> pomijam aby nie dodal sie duplikat
                $playlsit_video = new PlaylistVideo();
                $playlsit_video->setPlaylist($plst);
                $playlsit_video->setVideo($video);
                $playlsit_video->setDateAdded(new \DateTime("now"));
                $em->persist($playlsit_video);//dodaje nowe playlisty do video
                $em->flush();
            }
            $this->addFlash('success', 'Video zostało zaktualizowane.');
            return $this->redirect($request->getUri());
        }

        return $this->render('AppBundle:admin/video:edit.html.twig', array(
            'form' => $form->createView(),
            'video' => $video
        ));

    }

    /**
     * @Route("/panel/playlist/delete/{id}", name="deletePlaylist")
     * @ParamConverter("playlist", class="AppBundle:Playlist")
     */
    public function deletePlaylistAction(Playlist $playlist,Request $request)
    {
        if (!$playlist) {
            throw $this->createNotFoundException('Nie znaleziono playlisty');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($playlist);
        $em->flush();
        $this->addFlash("success","Playlista została poprawnie usunięta");
        return $this->redirect($this->generateUrl('playlist'));
    }

    /**
     * @Route("/panel/video/delete/{id}", name="deleteVideo")
     * @ParamConverter("videos", class="AppBundle:Videos")
     */
    public function deleteVideotAction(Videos $videos,Request $request)
    {
        if (!$videos) {
            throw $this->createNotFoundException('Video o podanym ID nie istnieje');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($videos);
        $em->flush();
        $this->addFlash("success", 'Video zostało poprawnie usunięte');
        return $this->redirect($this->generateUrl('panel'));
    }

    /**
     * @Route("/panel/vfp", name="downloadVideoFromPlaylist")
     */
    public function getAllVideoAction(Request $request) {

        $form = $this->createFormBuilder()
            ->add('playlist', EntityType::class, array(
                'class' => 'AppBundle:Playlist',
                'label' => 'Wybierz playlistę',
                'choice_label' => function ($playlist) {
                    $name = $playlist->getPlaylisttitle()." [".$playlist->getId()."]"." [".$playlist->getPlaylistid()."]";
                    return $name;
                }
            ))
            ->add('Aktualizuj', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();

                $playlist_id = $form->getData()['playlist'];
                $playlists = $this->getDoctrine()->getRepository("AppBundle:Playlist")->find($playlist_id);
                $vprepo = $this->getDoctrine()->getRepository("AppBundle:PlaylistVideo");

                $youtubeApiVideo = new YoutubeApi($playlists->getPlaylistid());
                $videos = $youtubeApiVideo->getAllVideosByPlaylistId();

                $videoCount = count($videos);
                $newVideo = 0;
                $updateVideo = 0;
                $addVideo = 0;

                foreach ($videos as $row) {
                    /* @var Videos $videoFromDb */
                    $videoFromDb = $this->getDoctrine()->getRepository("AppBundle:Videos")->findOneByVideoid(YoutubeApi::getVideoId($row));
                    $addVideoToPlaylist = new PlaylistVideo();

                    if (!empty($videoFromDb)) {//jest video
                        /* @var PlaylistVideo $videoInPlaylist */
                        $videoInPlaylist = $vprepo->findOneBy(array("playlist" => $playlist_id, "video" => $videoFromDb->getId()));
                        if (empty($videoInPlaylist)) {//nie ma video w playliscie
                            $videoToPlaylist = new PlaylistVideo();
                            $videoToPlaylist->setVideoToPlaylist($em, $videoFromDb, $playlists, null, YoutubeApi::getPosition($row));
                            $addVideo++;
                        } else {//jest video w playliscie. Aktualizuje pozycje i date dodania
                            $videoInPlaylist->setPositionInPlaylist(YoutubeApi::getPosition($row));
                            $videoInPlaylist->setDateAdded(new \DateTime("now"));
                            $em->persist($videoInPlaylist);
                            $em->flush();
                            $updateVideo++;
                        }
                    } else {//dodaje video, i dodaje je do playlisty
                        $video = new Videos();
                        $video->setVideoId(YoutubeApi::getVideoId($row));
                        $video->setDescription(YoutubeApi::getDesc($row));
                        $video->setTitle(YoutubeApi::getTitle($row));
                        $video->setDateadded(new \DateTime("now"));
                        $addVideoToPlaylist->setPositionInPlaylist(YoutubeApi::getPosition($row));
                        $addVideoToPlaylist->setPlaylist($playlists);
                        $addVideoToPlaylist->setVideo($video);
                        $addVideoToPlaylist->setDateAdded(new \DateTime("now"));
                        $em->persist($video);
                        $em->persist($addVideoToPlaylist);
                        $em->flush();
                        $newVideo++;
                    }

                }
            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirect($request->getUri());
            }

            $this->addFlash('success',"Ilość filmów w playliście: $videoCount,Nowych filmów: $newVideo,Zaktualizowano pozycji: $updateVideo,Przypisano do playlisty: $addVideo");
            return $this->redirect($request->getUri());
        }

        return $this->render('AppBundle:admin:videofromyoutubeapi.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("panel/playlist-update")
     */
    public function playlistUpdate() {
        $em = $this->getDoctrine()->getManager();
        $playlists = $this->getDoctrine()->getRepository("AppBundle:Playlist")->findAll();
        $vprepo = $this->getDoctrine()->getRepository("AppBundle:PlaylistVideo");

        foreach($playlists as $playlist) {
            $youtubeApiVideo = new YoutubeApi($playlist->getPlaylistid());
            $videos = $youtubeApiVideo->getAllVideosByPlaylistId();

            foreach ($videos as $row) {
                /* @var Videos $videoFromDb */
                $videoFromDb = $this->getDoctrine()->getRepository("AppBundle:Videos")->findOneByVideoid(YoutubeApi::getVideoId($row));
                $addVideoToPlaylist = new PlaylistVideo();

                if (!empty($videoFromDb)) {//jest video
                    /* @var PlaylistVideo $videoInPlaylist */
                    $videoInPlaylist = $vprepo->findOneBy(array("playlist" => $playlist->getId(), "video" => $videoFromDb->getId()));
                    if (empty($videoInPlaylist)) {//nie ma video w playliscie
                        $videoToPlaylist = new PlaylistVideo();
                        $videoToPlaylist->setVideoToPlaylist($em, $videoFromDb, $playlist, null, YoutubeApi::getPosition($row));
                    } else {//jest video w playliscie. Aktualizuje pozycje i date dodania
                        $videoInPlaylist->setPositionInPlaylist(YoutubeApi::getPosition($row));
                        $videoInPlaylist->setDateAdded(new \DateTime("now"));
                        $em->persist($videoInPlaylist);
                        $em->flush();
                    }
                } else {//dodaje video, i dodaje je do playlisty
                    $video = new Videos();
                    $video->setVideoId(YoutubeApi::getVideoId($row));
                    $video->setDescription(YoutubeApi::getDesc($row));
                    $video->setTitle(YoutubeApi::getTitle($row));
                    $video->setDateadded(new \DateTime("now"));
                    $addVideoToPlaylist->setPositionInPlaylist(YoutubeApi::getPosition($row));
                    $addVideoToPlaylist->setPlaylist($playlist);
                    $addVideoToPlaylist->setVideo($video);
                    $addVideoToPlaylist->setDateAdded(new \DateTime("now"));
                    $em->persist($video);
                    $em->persist($addVideoToPlaylist);
                    $em->flush();
                }

            }
        }

        return new Response("ok");
    }

    /**
     * @Route("panel/test")
     */
    public function testAction() {
        $playlist = new \AppBundle\Playlist\Playlist($this->container);
        $playlist->update();
        return new Response("test");
    }
}
