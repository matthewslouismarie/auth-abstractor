<?php

declare(strict_types=1);

namespace Tests\LM;

use Firehed\U2F\Registration;
use LM\Authentifier\Factory\U2fRegistrationFactory;
use PHPUnit\Framework\TestCase;

class U2fRegistrationFactoryTest extends TestCase
{
    public function testFactory()
    {
        $regFactory = new U2fRegistrationFactory();
        $firehedReg = (new Registration())
            ->setCounter(0)
            ->setAttestationCertificate(base64_decode('MIICSjCCATKgAwIBAgIEEkpy/jANBgkqhkiG9w0BAQsFADAuMSwwKgYDVQQDEyNZdWJpY28gVTJGIFJvb3QgQ0EgU2VyaWFsIDQ1NzIwMDYzMTAgFw0xNDA4MDEwMDAwMDBaGA8yMDUwMDkwNDAwMDAwMFowLDEqMCgGA1UEAwwhWXViaWNvIFUyRiBFRSBTZXJpYWwgMjQ5NDE0OTcyMTU4MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEPYsbvS/L9ghuEHRxYBRoSEFTwcbTtLaKXoVebkB1fuIrzYmIvzvv183yHLC/XXoVDYRK/pgQPGxmB9n6rih8AqM7MDkwIgYJKwYBBAGCxAoCBBUxLjMuNi4xLjQuMS40MTQ4Mi4xLjEwEwYLKwYBBAGC5RwCAQEEBAMCBSAwDQYJKoZIhvcNAQELBQADggEBAKFPHuoAdva4R2oQor5y5g0CcbtGWy37/Hwb0S01GYmRcDJjHXldCX+jCiajJWNOhXIbwtAahjA/a8B15ZlzGeEiFIsElu7I0fT5TPQRDeYmwolEPR8PW7sjnKE+gdHVqp31r442EmR1v8I68GKDFXJSdi/2iHm88O9XjVXWf5UbTzK2PIrqWw+Zxn19gUp/9ab1Lfg+iUo6XZyLguf4vI2vTIAXX/iXL9p5Mz7EZdgG6syUjxurIgRalVWKSMICJtrAA9QfvJ4F6iimu14QpJ3gYKCk9qJnajTWjEq+jGGHQ1W5An6CjKngZLAC1i6NjPB0SSF1PTXjyHxdV3lFPnc=', true))
            ->setPublicKey(base64_decode('BAcdB+X8+hq8MulBDtyfknw+bJsjyrK74dGuVg2hx6gSjFg3rHrhcH6J92r6qCBRYogNo04eeSV5XwwquVGFpFI=', true))
            ->setKeyHandle(base64_decode('PeTDOgdeJiftM3YOMVzr4lBEdMoR+wRdYARe8eWnuSB9V8VeD1wjcRkhbOadiZBSh/J/7XrQN4h31PjOaK+JwA==', true))
        ;
        $u2fReg = $regFactory->fromFirehed($firehedReg);
        $this->assertEquals(
            $firehedReg,
            $regFactory->toFirehed($u2fReg)
        );
    }
}
