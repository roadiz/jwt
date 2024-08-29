<?php

declare(strict_types=1);

namespace RZ\Roadiz\JWT\Validation\Constraint;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class UserInfoEndpoint implements Constraint
{
    public function __construct(
        private readonly string $userInfoEndpoint,
        private readonly HttpClientInterface $client,
    ) {
    }

    public function assert(Token $token): void
    {
        try {
            $response = $this->client->request('GET', $this->userInfoEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token->toString(),
                ],
            ]);
            // Trigger lazy request
            $response->getContent();
        } catch (ExceptionInterface $e) {
            throw new ConstraintViolation(
                'Userinfo cannot be fetch from Identity provider'
            );
        }
    }
}
