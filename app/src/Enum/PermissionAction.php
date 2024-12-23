<?php

namespace App\Enum;

/**
 * Enum to define allowed actions for permissions.
 */
enum PermissionAction: string
{
    case GET_COLLECTION = 'GET_COLLECTION';
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
}