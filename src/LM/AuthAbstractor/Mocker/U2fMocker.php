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
        $items = json_decode(file_get_contents($appConfig->getLibdir().'/u2f_registrations.json'), true);
        $this->u2fRegistrations = $this->createFromArray($items);
    }

    /**
     * @internal
     * @todo $items is mutated!
     * @todo Use ArrayObject instead.
     */
    public function createFromArray(array $items, array $list = []): array
    {
        if (0 !== count($items)) {
            $item = array_pop($items);
            $list[$item['id']] = new U2fRegistration(
                $item['u2fRegistration']['attestationCertificate'],
                $item['u2fRegistration']['counter'],
                $item['u2fRegistration']['keyHandle'],
                $item['u2fRegistration']['publicKey']
            );

            return $this->createFromArray($items, $list);
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

    /**
     * @api
     * @return IU2fRegistration[] An array of U2F registrations.
     */
    public function getU2fRegistrations(): array
    {
        return $this->u2fRegistrations;
    }
}
