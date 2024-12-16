<?php

namespace App\Enum;

/**
 * Enum to define allowed actions for permissions.
 */
enum PermissionAction: string
{
    case CREATE = 'CREATE';
    case READ = 'READ';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
}