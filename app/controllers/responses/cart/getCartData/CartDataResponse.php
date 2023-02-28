<?php

require_once __DIR__ . '/../../BaseResponse.php';
require_once __DIR__ . '/../CartResponse.php';
require_once __DIR__ . '/../ClientBaseResponse.php';
require_once __DIR__ . '/../getCartData/items/ProductsResponse.php';
require_once __DIR__ . '/../getCartData/items/PackageResponse.php';
require_once __DIR__ . '/../getCartData/debts/ClientActivityResponse.php';
require_once __DIR__ . '/../getCartData/meetings/ServiceResponse.php';
require_once __DIR__ . '/../getCartData/meetings/ServiceCoachesResponse.php';
require_once __DIR__ . '/../getCartData/meetings/ServiceDiaryResponse.php';
require_once __DIR__ . '/../../../../../office/Classes/Client.php';

class CartDataResponse extends BaseResponse implements CartResponse
{
    public $vatAmount;
    public $lessons;

    /**
     * @return bool
     */
    public function hasLessons(): bool
    {
        return $this->lessons;
    }

    /**
     * @param bool $lessons
     */
    public function setHasLessons(bool $lessons): void
    {
        $this->lessons = $lessons;
    }
    public $businessType;
    public $client;
    /** @var ClientActivityResponse[] $debts */
    public $debts;
    /** @var ProductsResponse[] $products */
    public $products;
    /** @var PackageResponse[] $packages */
    public $packages;
    /** @var ServiceResponse[] $services */
    public $services;

    public $openOrderId = 0;
    public $openOrderRefund = false;


    /** @var ServiceCoachesResponse[] $coaches */
    //todo-cart-ks-ask if here
    public $coaches;

    /** @var ServiceDiariesResponse[] $diaries */
    //todo-cart-ks-ask if here
    public $diaries;

    private $diariesIds = [];
    private $coachesId = [];

    /**
     * @return array
     */
    public function getDiariesIds(): array
    {
        return $this->diariesIds ?? [];
    }

    /**
     * @return array
     */
    public function getCoachesIds(): array
    {
        return $this->coachesId ?? [];
    }

    /**
     * @param ClientActivities $ClientActivity
     */
    public function addDebt(ClientActivities $ClientActivity): void
    {
        $this->debts[] = new ClientActivityResponse($ClientActivity);
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param ItemAndItemCat $ItemAndItemCat
     */
    public function addProduct(ItemAndItemCat $ItemAndItemCat): void
    {
        (int)$itemCatId = $ItemAndItemCat->itemCat_id ?? 0;
        if(!array_key_exists($itemCatId, $this->products)) {
            $this->products[$itemCatId] = new ProductsResponse($ItemAndItemCat);
        }
        $this->products[$itemCatId]->addItem($ItemAndItemCat);
    }

    /**
     * @param ItemAndItemCat $ItemAndItemCat
     */
    public function addPackage(ItemAndItemCat $ItemAndItemCat): void
    {
        $this->packages[] = new PackageResponse($ItemAndItemCat);
    }

    /**
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param MeetingTemplateClassType $MeetingTemplateClassType
     */
    public function addService(MeetingTemplateClassType $MeetingTemplateClassType): void
    {
        if(!array_key_exists($MeetingTemplateClassType->meetingCategory_id, $this->services)) {
            $this->services[$MeetingTemplateClassType->meetingCategory_id] = new ServiceResponse($MeetingTemplateClassType);
        }
        $this->services[$MeetingTemplateClassType->meetingCategory_id]->addItem($MeetingTemplateClassType, $this->getDiariesIds(), $this->getCoachesIds());
    }

    /**
     * @param Users $Coach
     */
    public function addCoach(Users $Coach): void
    {
        $this->coaches[] = new ServiceCoachesResponse($Coach);
        $this->coachesId[] = (int)$Coach->id;
    }

    /**
     * @param Section $Diary
     */
    public function addDiary(Section $Diary): void
    {
        $this->diaries[] = new ServiceDiaryResponse($Diary);
        isset($Diary->sectionsId) ? $this->diariesIds[] = (int)$Diary->sectionsId : null;
    }


    /**
     * CartDataResponse constructor.
     * @param int $vatAmount
     * @param bool $hasLessons
     * @param array|null $client
     */
    public function __construct(int $vatAmount = 0, bool $hasLessons = true , $client = null)
    {
        $this->lessons = $hasLessons;
        $this->vatAmount = $vatAmount;
        $this->client = $client;
        $this->products = [];
        $this->services = [];
        $this->diaries = [];
        $this->coaches = [];
    }


    /**
     * @return int
     */
    public function getVatAmount(): int
    {
        return $this->vatAmount;
    }

    /**
     * @param int $vatAmount
     */
    public function setVatAmount(int $vatAmount): void
    {
        $this->vatAmount = $vatAmount;
    }

    /**
     * @param int $openOrderId
     * @param bool $openOrderRefund
     */
    public function setOpenOrderId(int $openOrderId, bool $openOrderRefund = false): void
    {
        $this->openOrderId = $openOrderId;
        $this->openOrderRefund = $openOrderRefund;
    }

    /**
     * @param int $businessType
     */
    public function setBusinessType(int $businessType): void
    {
        $this->businessType = $businessType;
    }

    /**
     * @return array
     */
    public function getClient(): array
    {
        return $this->client;
    }

    /**
     * @param Client $Client
     */
    public function setClient(Client $Client): void
    {
        $this->client = new ClientBaseResponse($Client);
    }


    /**
     * @param bool $onlyClient
     * @return bool
     */
    public function getData($onlyClient = false): bool
    {
        $response = $this->returnData();
        if (!empty($this->client)) {
            $response['client'] = $this->client->getData();
            $response['debts'] = $this->debts;
            $response['openOrderId'] = $this->openOrderId;
            $response['openOrderRefund'] = $this->openOrderRefund;
        }

        if (!$onlyClient) {
            $response['products'] = array_values($this->products);
            $response['packages'] = $this->packages;
            $response['services'] = array_values($this->services);
            $response['coaches'] = $this->coaches;
            $response['diaries'] = $this->diaries;
        } elseif (!isset($response['client'])) {
            $response['client'] = null;
        }

        if (isset($this->vatAmount)) {
            $response['vatAmount'] = $this->vatAmount;
        }
        if (isset($this->lessons)) {
            $response['lessons'] = $this->lessons;
        }
        if (isset($this->businessType)) {
            $response['businessType'] = $this->businessType;
        }

        echo json_encode($response);
        return true;
    }


    /**
     * @param string $message
     * @param int $status
     * @return bool
     */
    public function returnError(string $message = '', int $status = 400): bool
    {
        $this->setError($message, $status);
        return $this->getData();
    }
}
