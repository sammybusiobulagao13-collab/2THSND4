<?php


function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.";
}

function validateRequired(string $value, string $label): ?string
{
    return trim($value) === '' ? "$label is required." : null;
}
function validateUsername(string $value): ?string
{
    $value = trim($value);
    if (strlen($value) < 6) {
        return "Username must be at least 6 characters long.";
    }
    return null;
}

function validatePassword(string $value): ?string
{
    $value = trim($value);
    
    if (strlen($value) < 6) {
        return "Password must be at least 6 characters long.";
    }
    
    if (!preg_match('/[A-Z]/', $value)) {
        return "Password must contain at least 1 uppercase letter.";
    }

    if (!preg_match('/[a-z]/', $value)) {
        return "Password must contain at least 1 lowercase letter.";
    }
    
    if (!preg_match('/[0-9]/', $value)) {
        return "Password must contain at least 1 number.";
    }
    return null;
}

function validateConfirmPassword(string $password, string $confirmPassword): ?string
{
    return $password === $confirmPassword ? null : "Passwords do not match!";
}
?>