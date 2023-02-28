<?php

require_once __DIR__ . '/../../../../office/Classes/Client.php';
require_once __DIR__ . '/../../../helpers/ImageHelper.php';

class ClientBaseResponse
{
    public $id;
    public $name;
    public $fname;
    public $lname;
    public $phone;
    public $img;
    public $isRandomClient;

    /**
     * ClientBaseResponse constructor.
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        $this->id = (int)$Client->id;
        $this->name = $Client->CompanyName;
        $this->fname = $Client->FirstName;
        $this->lname = $Client->LastName;
        $this->phone = $Client->ContactMobile;
        $this->isRandomClient = (int)$Client->isRandomClient;

        if(!empty($Client->ProfileImage)) {
            $this->img = ImageHelper::getImageWithAppPrefix($Client->ProfileImage);
        } else {
            $userImage = $Client->getAvatarFromUser();
            if ($userImage) {
                $this->img = $userImage;
            } else if (!empty($Client->FirstName)) {
                $this->img = 'https://ui-avatars.com/api/?length=1&background=f3f3f4&color=000&font-size=0.5&name=' . $Client->FirstName;
            }
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    public function getIsRandomClient()
    {
        return $this->isRandomClient;
    }

    public function setIsRandomClient(int $isIt): void
    {
        $this->isRandomClient = $isIt;
    }

    public function getImg()
    {
        return $this->img;
    }

    public function setImg($img): void
    {
        $this->img = $img;
    }
}
