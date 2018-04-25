<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Mocker;

use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Implementation\U2fRegistration;
use LM\AuthAbstractor\Model\IU2fRegistration;
use LM\Common\Model\ArrayObject;

class U2fMocker
{
    private $u2fRegistrations;

    public function __construct(IApplicationConfiguration $appConfig)
    {
        $items = json_decode(file_get_contents($appConfig->getLibdir().'/u2f_registrations.json'));
        $this->u2fRegistrations = $this->createFromArray($items, new ArrayObject([], IU2fRegistration::class));
    }

    /**
     * @todo $items is mutated!
     */
    public function createFromArray(array $items, ArrayObject $list): ArrayObject
    {
        if (0 !== count($items)) {
            $item = array_pop($items);
            return $this->createFromArray($items, $list->add(
                new U2fRegistration(
                    $item->attestationCertificate,
                    $item->counter,
                    $item->keyHandle,
                    $item->publicKey
                )
            ));
        } else {
            return $list;
        }
    }

    public function get(int $id): IU2fRegistration
    {
        return $this->u2fRegistrations->get($id);
    }
}
