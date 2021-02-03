<?php


namespace Dpool\Gatedcontent\Event;


use Dpool\Gatedcontent\Domain\Model\UserData;

final class ProcessUserDataEvent
{
    /** @var UserData */
    private $userData;

    public function __construct(UserData $userData)
    {
        $this->userData = $userData;
    }

    /**
     * @return UserData
     */
    public function getUserData(): UserData
    {
        return $this->userData;
    }
}
