<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\Vote\Models;

class Ip extends \Ilch\Model
{
    /**
     * The pollId of the Vote.
     *
     * @var int
     */
    protected $pollId;

    /**
     * The ip of the Vote.
     *
     * @var string
     */
    protected $ip;

    /**
     * Gets the pollId of the Vote.
     *
     * @return int
     */
    public function getPollId()
    {
        return $this->pollId;
    }

    /**
     * Sets the pollId of the Vote.
     *
     * @param int $pollId
     *
     * @return $this
     */
    public function setPollId($pollId)
    {
        $this->pollId = (int)$pollId;

        return $this;
    }

    /**
     * Gets the ip of the Vote.
     *
     * @return string
     */
    public function getIP()
    {
        return $this->ip;
    }

    /**
     * Sets the ip of the Vote.
     *
     * @param string $ip
     *
     * @return $this
     */
    public function setIP($ip)
    {
        $this->ip = (string)$ip;

        return $this;
    }
}