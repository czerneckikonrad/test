<?php
/**
 * Created by PhpStorm.
 * User: Konrad
 * Date: 02.10.2016
 * Time: 13:53
 */

namespace AppBundle\YoutubeApi;

use Symfony\Component\Config\Definition\Exception\Exception;

class YoutubeApi {
    public $apiKey;
    private $playlistId;

    public function __construct($playlistId) {
        $this->apiKey = YoutubeApiConf::apikey;
        $this->playlistId = $playlistId;
    }

    public function getPlaylistId() {
        return $this->playlistId;
    }

    public function setPlaylistId($playlistId) {
        $this->playlistId = $playlistId;
    }

    public function checkPlaylistIsset() {
        $json_url="https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&playlistId=".$this->playlistId."&key=".$this->apiKey."&maxResults=5";
        $json = @file_get_contents($json_url);
        if($json === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Zwraca tablice z wynikami
     *
     * @return array
     */
    public function getAllVideosByPlaylistId() {
        $json_url="https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&playlistId=".$this->playlistId."&key=".$this->apiKey."&maxResults=5";

        $json = @file_get_contents($json_url);
        if ($json === false) {
            throw new Exception("Nie znaleziono playlisty w API Youtube (".$this->getPlaylistId().") [Błąd - 404]");
        }

        $result = json_decode($json);
        $video = $result->items;

        while(@$result->nextPageToken) {
            $json_url="https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&playlistId=".$this->playlistId."&key=".$this->apiKey."&maxResults=5&pageToken=".$result->nextPageToken;
            $json = file_get_contents($json_url);
            $result = json_decode($json);
            $video = array_merge_recursive($video,$result->items);
        }

        return $video;
    }

    public static function getTitle($row) {
        $str = utf8_encode($row->snippet->title);
        return $str;
    }

    public static function getDesc($row) {
        $str = utf8_encode($row->snippet->description);
        return $str;
    }

    public static function getVideoId($row) {
        return $row->snippet->resourceId->videoId;
    }

    public static function getPosition($row) {
        $position = $row->snippet->position + 1;
        return $position;
    }
}