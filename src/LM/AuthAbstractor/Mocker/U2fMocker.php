<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Mocker;

use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Implementation\U2fRegistration;
use LM\AuthAbstractor\Model\IU2fRegistration;
use LM\Common\Model\ArrayObject;

/**
 * This class is used for unit testing U2F. It retrieves and instantiates
 * IU2FRegistration objects.
 *
 * @see \LM\AuthAbstractor\Model\IU2fRegistration
 */
class U2fMocker
{
    /**
     * @var IU2fRegistration[]
     */
    private $u2fRegistrations;

    /**
     * @param IApplicationConfiguration $appConfig The configuration of the
     * application.
     */
    public function __construct(IApplicationConfiguration $appConfig)
    {
        $items = json_decode(file_get_contents($appConfig->getLibdir().'/u2f_registrations.json'));
        $this->u2fRegistrations = $this->createFromArray($items, new ArrayObject([], IU2fRegistration::class));
    }

    /**
     * @internal
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

    /**
     * @api
     * @param int $id The index of the U2F registration.
     * @return IU2fRegistration A U2F registration.
     */
    public function get(int $id): IU2fRegistration
    {
        return $this->u2fRegistrations->get($id);
    }
}
