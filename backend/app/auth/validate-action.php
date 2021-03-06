<?php
/**
 *     This file is part of Smart-ID PHP Demo.
 *
 *     Smart-ID PHP Demo is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     Smart-ID PHP Demo is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with Smart-ID PHP Demo.  If not, see <https://www.gnu.org/licenses/>.
 */

use Sk\SmartId\Api\AuthenticationResponseValidator;

$app->post( 'validate', function() use ( $app, $client )
{
  /** @var \Sk\SmartId\Api\Data\AuthenticationHash $authenticationHash */
  $authenticationHash = $app['session']->get( 'authenticationHash' );
  $sessionId = $app['session']->get( 'sessionId' );

  $authenticationResponse = $client->authentication()
      ->createSessionStatusFetcher()
      ->withSessionId( $sessionId )
      ->withAuthenticationHash( $authenticationHash )
      ->getAuthenticationResponse();

  if ($authenticationResponse->isRunningState())
  {
    $response = array(
        'data' => array(
            'isRequestingValidation' => true
        )
    );
  }
  else
  {
      $authenticationResponseValidator = new AuthenticationResponseValidator();
      $authenticationResult = $authenticationResponseValidator->validate( $authenticationResponse );
      $isValidResult = $authenticationResult->isValid();
      $authenticationIdentity = $authenticationResult->getAuthenticationIdentity();
      $app['session']->set( 'user', $authenticationIdentity );

      $response = array(
          'data' => ($isValidResult) ? array(
              'isSignedIn'   => true,
              'firstName'    => $authenticationIdentity->getGivenName(),
              'lastName'     => $authenticationIdentity->getSurName(),
              'personalCode' => $authenticationIdentity->getIdentityCode()
          ) :
          array(
              'isSignedIn'  => false,
              'errors' => $authenticationResult->getErrors(),
              )
          );
  }


  return $app->json(array_merge($response, array( 'status' => (($isValidResult) ? 'SUCCESS' : 'ERROR')) ), $isValidResult ? 200 : 401);
} );
