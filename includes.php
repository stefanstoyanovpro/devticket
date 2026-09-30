<?php

function isValidUsername(string $username): bool {
    return strlen(trim($username)) >= 3;
}

function isValidPassword(string $password): bool {
    return strlen($password) >= 6;
}

function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

function calculatePriority(string $impact, string $urgency): string {
    $matrix = [
        'low'    => ['low' => 'low',    'medium' => 'low',    'high' => 'medium'],
        'medium' => ['low' => 'low',    'medium' => 'medium', 'high' => 'high'],
        'high'   => ['low' => 'medium', 'medium' => 'high',   'high' => 'critical'],
    ];
    return $matrix[$impact][$urgency] ?? 'medium';
}
