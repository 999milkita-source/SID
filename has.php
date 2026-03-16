<?php
/**
 * hash_password.php
 * Hash password menggunakan bcrypt ($2y$)
 */

// Password asli (biasanya dari form)
$password_plain = 'kades';

// COST bcrypt (10–12 aman untuk XAMPP/produksi ringan)
$options = [
    'cost' => 12,
];

// Hash password (bcrypt -> $2y$)
$hash = password_hash($password_plain, PASSWORD_BCRYPT, $options);

echo "Password asli: " . $password_plain . "<br>";
echo "Hash bcrypt: <br><code>" . $hash . "</code>";
