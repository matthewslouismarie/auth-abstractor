<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Mocker;

use Firehed\U2F\RegisterRequest;
use Firehed\U2F\SignRequest;
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
            $list[$item['id']]['u2fRegistration'] = new U2fRegistration(
                $item['u2fRegistration']['attestationCertificate'],
                $item['u2fRegistration']['counter'],
                $item['u2fRegistration']['keyHandle'],
                $item['u2fRegistration']['publicKey']
            );
            if (isset($item['registerRequest'])) {
                $list[$item['id']]['registerRequest'] = (new RegisterRequest())
                    ->setAppId($item['registerRequest']['appId'])
                    ->setChallenge($item['registerRequest']['challenge'])
                ;
            }
            if (isset($item['registerResponse'])) {
                $list[$item['id']]['registerResponseStr'] = json_encode($item['registerResponse']);
            }
            if (isset($item['u2fAuthentications'])) {
                $list[$item['id']]['u2fAuthentications'] = [];
                foreach ($item['u2fAuthentications'] as $u2fAuth) {
                    $signRequests = array_map(
                        function ($object) {
                            return (new SignRequest())
                                ->setAppId($object['appId'])
                                ->setChallenge($object['challenge'])
                                ->setKeyHandle(base64_decode($object['keyHandle'], true))
                            ;
                        },
                        $u2fAuth['signRequests']
                    );
                    $list[$item['id']]['u2fAuthentications'][] = [
                        'signRequests' => $signRequests,
                        'signResponse' => $u2fAuth['signResponse'],
                    ];
                }
            }

            return $this->createFromArray($items, $list);
        } else {
            return $list;
        }
    }

    /**
     * @api
     * @param int $id The index of the U2F registration.
     * @return mixed[] An array.
     */
    public function get(int $id): array
    {
        return $this->u2fRegistrations[$id];
    }

    /**
     * @api
     * @return array An array of U2F registrations and related data.
     */
    public function getU2fRegistrations(): array
    {
        return $this->u2fRegistrations;
    }

    /**
     * @api
     * @return IU2fRegistration[] An array of U2F registrations only.
     */
    public function getU2fRegistrationsOnly(): array
    {
        return array_map(
            function ($item) {
                return $item['u2fRegistration'];
            },
            $this->u2fRegistrations
        );
    }
}
