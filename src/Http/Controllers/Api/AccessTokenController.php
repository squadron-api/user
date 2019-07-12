<?php

namespace Squadron\User\Http\Controllers\Api;

use Illuminate\Http\Response;
use Laravel\Passport\Http\Controllers\AccessTokenController as ATC;
use Psr\Http\Message\ServerRequestInterface;
use Squadron\User\Events\TokenCreated;
use Zend\Diactoros\Response as Psr7Response;

class AccessTokenController extends ATC
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param ServerRequestInterface $request
     *
     * @return Response
     */
    public function issueToken(ServerRequestInterface $request): Response
    {
        return $this->withErrorHandling(function () use ($request) {
            $response = $this->convertResponse(
                $this->server->respondToAccessTokenRequest($request, new Psr7Response)
            );

            $responseData = json_decode($response->getContent(), true);

            if (! isset($responseData['error']))
            {
                $userModel = config('squadron.user.models.user');
                $username = $request->getParsedBody()['username'];

                $user = method_exists($userModel, 'findForPassport')
                            ? (new $userModel)->findForPassport($username)
                            : (new $userModel)->where('email', $username)->first();

                $tokenCreatedEvent = new TokenCreated($user, $responseData);
                event($tokenCreatedEvent);

                $response->setContent($tokenCreatedEvent->tokenData);
            }

            return $response;
        });
    }
}
