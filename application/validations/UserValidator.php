<?php

class UserValidator
{
    public static function validateCreate($data)
    {
        $errors = [];

        if (!isset($data['name']) || trim($data['name']) === '') {
            $errors['name'] = 'Name is required';
        }

        if (!isset($data['email']) || trim($data['email']) === '') {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        return $errors;
    }
}
