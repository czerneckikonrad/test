<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlaylistVideo
 *
 * @ORM\Table(name="playlist_video", indexes={@ORM\Index(name="video_id", columns={"video_id"}), @ORM\Index(name="playlist_id", columns={"playlist_id"})})
 * @ORM\Entity
 */
class PlaylistVideo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="position_in_playlist", type="integer", nullable=true)
     */
    private $positionInPlaylist;

    /**
     * @var \AppBundle\Entity\Videos
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Videos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="video_id", referencedColumnName="id", unique=true)
     * })
     */
    private $video;

    /**
     * @var \AppBundle\Entity\Playlist
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Playlist")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="playlist_id", referencedColumnName="id", unique=true)
     * })
     */
    private $playlist;



    /**
     * Set id
     *
     * @param integer $id
     *
     * @return PlaylistVideo
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return PlaylistVideo
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set positionInPlaylist
     *
     * @param integer $positionInPlaylist
     *
     * @return PlaylistVideo
     */
    public function setPositionInPlaylist($positionInPlaylist)
    {
        $this->positionInPlaylist = $positionInPlaylist;

        return $this;
    }

    /**
     * Get positionInPlaylist
     *
     * @return integer
     */
    public function getPositionInPlaylist()
    {
        return $this->positionInPlaylist;
    }

    /**
     * Set video
     *
     * @param \AppBundle\Entity\Videos $video
     *
     * @return PlaylistVideo
     */
    public function setVideo(\AppBundle\Entity\Videos $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return \AppBundle\Entity\Videos
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set playlist
     *
     * @param \AppBundle\Entity\Playlist $playlist
     *
     * @return PlaylistVideo
     */
    public function setPlaylist(\AppBundle\Entity\Playlist $playlist = null)
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * Get playlist
     *
     * @return \AppBundle\Entity\Playlist
     */
    public function getPlaylist()
    {
        return $this->playlist;
    }

    public function setVideoToPlaylist($manager,\AppBundle\Entity\Videos $video,\AppBundle\Entity\Playlist $playlist,$dateAdded = null,$position = null) {
        if($dateAdded == null) { $dateAdded = new \DateTime("now"); }
        $playlistVideo = new PlaylistVideo();
        $playlistVideo->setVideo($video);
        $playlistVideo->setPlaylist($playlist);
        $playlistVideo->setDateAdded($dateAdded);
        $playlistVideo->setPositionInPlaylist($position);
        $manager->persist($playlistVideo);
        $manager->flush();
    }
}
