<?php

declare(strict_types=1);

namespace Keystone\Core\Update\Signature;

use RuntimeException;

final class SignatureVerifier {


    public function __construct(
        private readonly PublicKeyRepository $keys
    ) {}

    public function verify(
        string $zipPath,
        string $signaturePath
    ): void {
        if (!file_exists($signaturePath)) {
            throw new SignatureException('Signature file missing');
        }

        $publicKey = $this->keys->get();

        $data = file_get_contents($zipPath);
        if ($data === false) {
            throw new RuntimeException('Unable to read zip file');
        }

        $signature = file_get_contents($signaturePath);
        
        if ($signature === false) {
            throw new RuntimeException('Unable to read signature file');
        }

        $ok = openssl_verify(
            $data,
            $signature,
            $publicKey,
            OPENSSL_ALGO_SHA256
        );

        if ($ok !== 1) {
            throw new RuntimeException('Signature verification failed');
        }

    }
}

?>