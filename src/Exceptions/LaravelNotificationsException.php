<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Exceptions;

use JOOservices\Exceptions\Base\AbstractContextAwareLogicException;

/**
 * Root marker for programmer / domain-invariant failures in this package.
 */
abstract class LaravelNotificationsException extends AbstractContextAwareLogicException
{
}
