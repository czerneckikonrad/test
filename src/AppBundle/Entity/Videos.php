<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Videos
 *
 * @ORM\Table(name="videos", uniqueConstraints={@ORM\UniqueConstraint(name="videoId", columns={"videoId"})})
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"videoid"},
 *     message="Video o podanym Youtube ID już istnieje!"
 * )
 */
class Videos
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
     *
     * @ORM\Column(name="videoId", type="string", length=255, nullable=false)
     */
    private $videoid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime", nullable=false)
     */
    private $dateadded = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer", nullable=false)
     */
    private $views = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="votesUp", type="integer", nullable=false)
     */
    private $votesup = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="votesDown", type="integer", nullable=false)
     */
    private $votesdown = '0';

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set videoid
     *
     * @param string $videoid
     *
     * @return Videos
     */
    public function setVideoid($videoid)
    {
        $this->videoid = $videoid;

        return $this;
    }

    /**
     * Get videoid
     *
     * @return string
     */
    public function getVideoid()
    {
        return $this->videoid;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Videos
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return utf8_decode($this->title);
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Videos
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return utf8_decode($this->description);
    }

    /**
     * Set dateadded
     *
     * @param \DateTime $dateadded
     *
     * @return Videos
     */
    public function setDateadded($dateadded)
    {
        $this->dateadded = $dateadded;

        return $this;
    }

    /**
     * Get dateadded
     *
     * @return \DateTime
     */
    public function getDateadded()
    {
        return $this->dateadded;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return Videos
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set votesup
     *
     * @param integer $votesup
     *
     * @return Videos
     */
    public function setVotesup($votesup)
    {
        $this->votesup = $votesup;

        return $this;
    }

    /**
     * Get votesup
     *
     * @return integer
     */
    public function getVotesup()
    {
        return $this->votesup;
    }

    /**
     * Set votesdown
     *
     * @param integer $votesdown
     *
     * @return Videos
     */
    public function setVotesdown($votesdown)
    {
        $this->votesdown = $votesdown;

        return $this;
    }

    /**
     * Get votesdown
     *
     * @return integer
     */
    public function getVotesdown()
    {
        return $this->votesdown;
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
}
