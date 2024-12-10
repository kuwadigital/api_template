<?php

namespace App\Enum;

/**
 * Enum to define allowed actions for permissions.
 */
enum PermissionAction: string
{
    case CREATE = 'create';
    case READ = 'read';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case PATCH = 'patch';
}