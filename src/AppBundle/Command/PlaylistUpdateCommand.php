<?php
/**
 * Created by PhpStorm.
 * User: Konrad
 * Date: 21.12.2016
 * Time: 18:56
 */

namespace AppBundle\Command;

use AppBundle\Entity\PlaylistVideo;
use AppBundle\Entity\Videos;
use AppBundle\YoutubeApi\YoutubeApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlaylistUpdateCommand extends ContainerAwareCommand {
    protected function configure()
    {
        $this
            ->setName("app:playlist-update")
            ->setDescription("Aktualizuje playlisty")
            ->setHelp("Pozwala na zaktualizowanie playlist");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $playlists = $this->getContainer()->get('doctrine')->getRepository("AppBundle:Playlist")->findAll();
        $vprepo = $this->getContainer()->get('doctrine')->getRepository("AppBundle:PlaylistVideo");

        foreach($playlists as $playlist) {
            $youtubeApiVideo = new YoutubeApi($playlist->getPlaylistid());
            $videos = $youtubeApiVideo->getAllVideosByPlaylistId();

            foreach ($videos as $row) {
                /* @var Videos $videoFromDb */
                $videoFromDb = $this->getContainer()->get('doctrine')->getRepository("AppBundle:Videos")->findOneByVideoid(YoutubeApi::getVideoId($row));
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

        $output->writeln('Aktualizowanie playlist.');
    }
}