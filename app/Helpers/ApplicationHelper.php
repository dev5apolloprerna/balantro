<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class ApplicationHelper
{
    public static function documentStatusClasses($status)
    {
        switch ($status) {
            case 'verified':
                return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            case 'rejected':
                return '!bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200';
            default: // uploaded
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        }
    }

    public static function requiredLabelTag($name, $text, $options = [])
    {
        $textWithStar = $text . ' <span class="!text-danger-500">*</span>';
        return '<label for="' . e($name) . '" ' . self::attributes($options) . '>' . $textWithStar . '</label>';
    }

    public static function getReceiverIdForClient($messages)
    {
        if ($messages->isNotEmpty()) {
            return $messages->first()->receiver_id == Auth::id() 
                ? $messages->first()->sender_id 
                : $messages->first()->receiver_id;
        }
        
        return Auth::user()->dataEntryOperators()->first()->id;
    }

    private static function attributes($options)
    {
        $html = [];
        foreach ($options as $key => $value) {
            $html[] = $key . '="' . e($value) . '"';
        }
        return implode(' ', $html);
    }
}