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

use Sk\SmartId\Exception\SmartIdException;

require_once __DIR__ . '/Exception/UserNotFoundException.php';

$app->error( function( Exception $e ) use ( $app )
{
  $app['session']->clear();

  $error = array(
      'status' => 'ERROR',
      'error'  => 'USER_NOT_FOUND',
      'data'   => new stdClass()
  );

  if ( $e instanceof UserNotFoundException )
  {
    return $app->json( $error );
  }
  elseif ( $e instanceof SmartIdException )
  {
    $error['data'] = $e->getTrace();
    $error['error'] = get_class( $e );
    $error['code'] = $e->getCode();
    return $app->json( $error );
  }

  return $app->json( array(
      'message'    => 'Undetermined error!',
      'stacktrace' => $e->getTrace()
  ) );
} );
