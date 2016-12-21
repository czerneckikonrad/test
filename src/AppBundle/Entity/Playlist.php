<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AcmeAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Playlist
 *
 * @ORM\Table(name="playlist", uniqueConstraints={@ORM\UniqueConstraint(name="playlistId", columns={"playlistId"})})
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"playlistid"},
 *     message="Playlista o podanym ID już istnieje!"
 * )
 */
class Playlist
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
     * @var string
     * @AcmeAssert\ContainsYoutubePlaylist
     * @ORM\Column(name="playlistId", type="string", length=255, nullable=true)
     */
    private $playlistid;

    /**
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="playlistTitle", type="text", length=65535, nullable=true)
     */
    private $playlisttitle;

    /**
     * @var string
     *
     * @ORM\Column(name="playlistDescription", type="text", length=65535, nullable=true)
     */
    private $playlistdescription;



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
     * Set playlistid
     *
     * @param string $playlistid
     *
     * @return Playlist
     */
    public function setPlaylistid($playlistid)
    {
        $this->playlistid = $playlistid;

        return $this;
    }

    /**
     * Get playlistid
     *
     * @return string
     */
    public function getPlaylistid()
    {
        return $this->playlistid;
    }

    /**
     * Set playlisttitle
     *
     * @param string $playlisttitle
     *
     * @return Playlist
     */
    public function setPlaylisttitle($playlisttitle)
    {
        $this->playlisttitle = $playlisttitle;

        return $this;
    }

    /**
     * Get playlisttitle
     *
     * @return string
     */
    public function getPlaylisttitle()
    {
        return $this->playlisttitle;
    }

    /**
     * Set playlistdescription
     *
     * @param string $playlistdescription
     *
     * @return Playlist
     */
    public function setPlaylistdescription($playlistdescription)
    {
        $this->playlistdescription = $playlistdescription;

        return $this;
    }

    /**
     * Get playlistdescription
     *
     * @return string
     */
    public function getPlaylistdescription()
    {
        return $this->playlistdescription;
    }
}
